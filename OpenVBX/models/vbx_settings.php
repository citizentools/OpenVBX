<?php
/**
 * "The contents of this file are subject to the Mozilla Public License
 *  Version 1.1 (the "License"); you may not use this file except in
 *  compliance with the License. You may obtain a copy of the License at
 *  http://www.mozilla.org/MPL/

 *  Software distributed under the License is distributed on an "AS IS"
 *  basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 *  License for the specific language governing rights and limitations
 *  under the License.

 *  The Original Code is OpenVBX, released June 15, 2010.

 *  The Initial Developer of the Original Code is Twilio Inc.
 *  Portions created by Twilio Inc. are Copyright (C) 2010.
 *  All Rights Reserved.

 * Contributor(s):
 **/

class VBX_SettingsException extends Exception {}

class VBX_Settings extends Model
{
	protected $settings_table = 'settings';
	protected $tenants_table = 'tenants';

	public $setting_options = array(
								'twilio_sid',
								'twilio_token',
								'twilio_endpoint',
								'from_email',
								'recording_host',
								'theme',
								'transcriptions',
								'voice',
								'voice_language',
								'numbers_country',
								'gravatars'
							);

	protected $settings_params = array(
								'name',
								'value',
								'tenant_id'
							);

	protected $tenants_params = array(
								'active',
								'name',
								'url_prefix',
								'type'
							);

	private $cache_key;

	const CACHE_TIME_SEC = 1;
	
	const CLIENT_TOKEN_TIMEOUT = 28800; // 8 hours
	
	const AUTH_TYPE_PARENT = 0;
	const AUTH_TYPE_FULL = 1; // @note currently not used, this is for future expansion
	const AUTH_TYPE_SUBACCOUNT = 2;
	const AUTH_TYPE_CONNECT = 3;

	function __construct()
	{
		parent::__construct();
		$this->cache_key = 'settings';
	}

	function get_all_tenants()
	{
		$ci =& get_instance();

		$tenants = $ci->db
			 ->from($this->tenants_table)
			 ->where('name !=', 'default')
			 ->get()->result();

		return $tenants;
	}

	function get_tenant($url_prefix)
	{
		$ci =& get_instance();

		$query = $ci->db
			 ->from($this->tenants_table)
			 ->where('url_prefix', strtolower($url_prefix))
			 ->get();

		if ($query) 
		{
			$tenant = $query->result();
			if(!empty($tenant[0]))
			{
				return $tenant[0];
			}
		}
		
		return false;
	}

	function get_tenant_by_name($name)
	{
		$ci =& get_instance();

		$tenant = $ci->db
			 ->from('tenants as i')
			 ->where('i.name', $name)
			 ->get()->result();

		if(!empty($tenant[0]))
			return $tenant[0];

		return false;
	}

	function get_tenant_by_id($id)
	{
		$ci =& get_instance();

		$tenant = $ci->db
			 ->from($this->tenants_table)
			 ->where('id', $id)
			 ->get()->result();

		if(!empty($tenant[0]))
			return $tenant[0];

		return false;
	}

	function tenant($name, $url_prefix, $local_prefix)
	{
		$ci =& get_instance();

		$tenant = $this->get_tenant($url_prefix);
		$errors = array();

		if(strlen($url_prefix) > 32)
		{
			$errors[] = "Tenant name exceeds 32 character limit";
		}

		if(preg_match('/[^0-9A-Za-z_-]/', $name) > 0)
		{
			$errors[] = "Tenant name contains invalid characters. ".
						"Allowed characters: alphanumeric, dashes, and underscores.";
		}

		if(!empty($errors))
		{
			throw new VBX_SettingsException(implode(',', $errors));
		}

		if($tenant === false)
		{
			$ci->db
				->set('name', $name)
				->set('url_prefix', $url_prefix)
				->set('local_prefix', $local_prefix)
				->insert($this->tenants_table);
			$tenant_id = $ci->db->insert_id();
			if(!$tenant_id)
			{
				throw new VBX_SettingsException('Tenant failed to create');
			}

			return $tenant_id;
		}

		throw new VBX_SettingsException('Tenant by this name or url already exists');
	}

	function update_tenant($tenant)
	{
		$ci =& get_instance();

		$errors = array();
		if(!(!empty($tenant)
			 && isset($tenant['id'])
			 && intval($tenant['id']) > 0
			 && $this->get_tenant_by_id($tenant['id']) !== false))
		{
			throw new VBX_SettingsException('Can not update tenant, malformed update request');
		}

		if(isset($tenant['url_prefix'])
		   && strlen($tenant['url_prefix']) > 32)
		{
			$errors[] = "Tenant name exceeds 32 character limit";
		}

		if(isset($tenant['name'])
		   && preg_match('/[^0-9A-Za-z_-]/', $name) > 0)
		{
			$errors[] = "Tenant name contains invalid characters. ".
						"Allowed characters: alphanumeric, dashes, and underscores.";
		}

		foreach($this->tenants_params as $param)
		{
			if(isset($tenant[$param]))
			{
				$ci->db
					->set($param, $tenant[$param]);
			}
		}

		return $ci->db
			->where('id', $tenant['id'])
			->update($this->tenants_table);
	}

	function add($name, $value, $tenant_id)
	{
		$ci =& get_instance();

		if($this->get_tenant_by_id($tenant_id) === false)
		{
			return false;
		}

		if($this->get($name, $tenant_id) !== false) {
			$ci->db
				->set('value', $value)
				->where('name', $name)
				->where('tenant_id', $tenant_id)
				->update($this->settings_table);
		} else {
			$ci->db
				->set('name', $name)
				->set('value', $value)
				->set('tenant_id', $tenant_id)
				->insert($this->settings_table);
		}

		if(function_exists('apc_delete')) {
			apc_delete($this->cache_key.$tenant_id.$name);
		}

		return $ci->db
			->insert_id();
	}

	function set($name, $value, $tenant_id)
	{
		$ci =& get_instance();

		if($this->get($name, $tenant_id) === false)
		{
			return false;
		}

		$ci->db
			->set('value', $value)
			->where('name', $name)
			->where('tenant_id', $tenant_id)
			->update($this->settings_table);

		if(function_exists('apc_delete')) {
			return apc_delete($this->cache_key.$tenant_id.$name);
		}

		return ($ci->db
				->affected_rows() > 0? true : false);
	}

	function get($name, $tenant_id)
	{
		if(function_exists('apc_fetch')) {
			$success = false;
			$cachekey = $this->cache_key.$tenant_id.$name;
			if(($data = apc_fetch($cachekey, $success)) && $success) 
			{
				$result = @unserialize($data);
				if(!empty($result[0]))
					return $result[0]->value;
			}
		}

		$ci =& get_instance();
		$query = $ci->db
			->select('value')
			->from($this->settings_table)
			->where(array(
				'name' => $name, 
				'tenant_id' => intval($tenant_id)
			))
			->get();
		
		if ($query) 
		{
			$result = $query->result();
			if(function_exists('apc_store')) {
				$success = apc_store($cachekey, serialize($result), self::CACHE_TIME_SEC);
			}

			if(!empty($result[0]))
			{
				return $result[0]->value;
			}
		}
		
		return false;
	}
	
	function delete($name, $tenant_id) {
		$ci =& get_instance();
		
		if($this->get($name, $tenant_id) === false)
		{
			return false;
		}
		
		$query = $ci->db
					->where(array(
						'name' => $name, 
						'tenant_id' => $tenant_id
					))
					->delete($this->settings_table);
		
		if (function_exists('apc_delete')) 
		{
			apc_delete($this->cache_key.$tenant_id.$name);
		}

		return ($ci->db->affected_rows() > 0 ? true : false);
	}

	function get_all_by_tenant_id($tenant_id)
	{
		$ci =& get_instance();

		$result = $ci->db
			->from($this->settings_table)
			->where('tenant_id', $tenant_id)
			->get()->result();

		return $result;
	}

}
