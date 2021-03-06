# OpenVBX Change Log


## OpenVBX 1.1.1

- fixed an issue with validation of requests from Twilio
- fixed an issue with gathering devices during a group dial
- fixing issue with media uploading when a user's email contains a "+"
- silenced various notices
- added more system status output to Settings > About
- fixed possible tenant first run sending to Connect signup when not necessary


## OpenVBX 1.1

- integrate [Twilio Connect](http://www.twilio.com/docs/connect/) for Tenants
- adding voice & language preferences to site options to allow administrator to set site wide options for the voice type and language of voice
	- enable tenant administrators to edit their system settings
- adding transcription preference to site options to allow transcriptions to be turned off
- adding ability to sort users in a group
- updated Twilio Client UI slider to fully integrate dialing functionality in to slider dialog
	- dialog now saves state as user specific settings
	- dialog allows dialing using any configured device wether active or not
	- dialog shows a list of users in the system along with a quick dial button
	- dialog live updates when user/device/number data changes in the system
		- for user making the change, other users currently require logging out/in to refresh
- adding [Minify.php](http://code.google.com/p/minify/) for asset minification in lieu of pre-compiled assets
- enable the purchase of numbers by specific country
- display and enforce number capabilities. not all numbers are allowed to use sms
- updated API library to latest [Services Twilio library](https://github.com/twilio/twilio-php) & updated all calls to Twilio to use the new library
	- modify internal calls & standard applets to use new library for API calls & TwiML generation
- updated install, upgrade & welcome JS to abstract out the steps slider
- updated install routine to detect & use mysqli if available
- added native sequential="true" attribute to Dial actions
- hardened install/upgrade error handling JavaScript
- showing number names in Twilio numbers list
	- numbers are named in the Twilio Account portal
- added form convenience helpers to Twilio Helper
- added Upgrade instructions file
- added user settings table
	- added user `setting()` and `setting_set()` interfaces on VBX_User
	- added /account/settings ajax endpoint for setting preferences
- improved Twilio Client init & user online status handling when Client takes a while to init
- added deprecation notices to old api library items
- added new Twilio helper file with:
	- request validation
	- clean digits helper
	- url versioning helper
	- deprecation notice helper
- documentation content and formatting updates
- bumped system PHP requirement to 5.2+
	- PHP versions less than 5.2 are no longer supported
- better handle disabled tenants during upgrade
- added integration testing library based on [foostack](http://www.foostack.com/foostack/)
	- started integration tests for applets
- fixing database debug setting default. Now defaults to off
- added [Gravatar](http://gravatar.com) support in theme options
- users list is now ordered by user last name & administrators are identified in the list
- adding local config override option for `OpenVBX/config/config.php`
	- overrides are ignored via .gitignore
- fix for audio uploads 302 error
- fix bug in rest access tokens that could allow unauthorized access to twiml controller
	- each twilio client outbound call now has its own rest access token
- add more standards compliant CSS for base UI styling
- fixed bug where an error entering basic tenant info could create an incomplete tenant
- added Changelog file


## OpenVBX 1.0.5

- fixed issue with validation of `rest_access` tokens
- modified Twilio Client interface to request a new `rest_access` token for each call
- fixed iPhone install url helper
	
	
## OpenVBX 1.0.4

- changed cookie tracking in dial applet to use the built in CI session instead 
- fixed issue with browser phone when the client connection takes a while to become ready
- fixed online status indicator for new users
- added better messaging during install if server tests do not pass
- added progress indicator in submit button during upgrade
- fix double encoding bug with output sanitization
- fix to properly use `recording_host` if provided
- fix for OpenVBX running on nginx servers
- fix to properly persist session data on twiml delivery
- minor fixes, updates, and JS nicities


## OpenVBX 1.0.3

- Fixed infinite subtenant redirect


## OpenVBX 1.0.2

- Fixed bad behavior if flash is not Installed or if a Flash blocker is in use
- Fixed flow editor & possible flow failure if `date.default_timezone` is not defined in PHP for the timing Applet
- Fixed issue with handling normal numbers in Dial applet
- Allow dismissal of the Browser Phone slider in case of an error


## OpenVBX 1.0.1

- Handling browser not having flash or a using a flash-blocker


## OpenVBX 1.0

- Twilio Client Support
- Site now runs in an iFrame to provide persistent call support to Twilio Client calls
- jQuery 1.6.2 & jQuery UI 1.8.14
  - jQuery 1.6 made some important changes. More information about what changed with jQuery 1.6 can be found here: http://blog.jquery.com/2011/05/03/jquery-16-released/
  - upgraded Soundmanager2 plugin & now require Flash 9
- Added plugin page titles
- Added the ability to rename flows
- Fix incoming numbers query to properly pull all incoming numbers for an account
- Fixed bug in Dial applet that would cause an infinite dial loop
- Fixed bug in Menu applet that would cause it not to save and/or damage data
- Removed iPhone app banner
- Normalized `OpenVBX.home` & `OpenVBX.assets` in site wide JS to NOT have a trailing slash
- Fixed bug that prohibited the use of OpenVBX with database table prefixes
- Many bug fixes
- Updated repository layout to use Git Flow principles
  - learn more about git-flow here: https://github.com/nvie/gitflow


## OpenVBX 0.91

- fix case issue on timezone
- add 3rd party plugin notice to upgrade routine
- add trademark info to README file
- added GoDaddy specific install instructions
  

## OpenVBX 0.90

- Implemented OpenVBX 2010 Number Upgrader into standard upgrade system
- Added support for arbitrary php scripts in upgrade process.
- Added basic auth support for CGI based installations
- UX Integration of OpenVBX for iPhone into OpenVBX message and installation workflow
   - Added invitation support for OpenVBX iphone app
   - Added new external linking support with browser detection for opening details in iphone app
- New 2010 number provisioning for tollfree and domestic numbers
- Fix for Error when changing the phone number on an existing user
- Added restriction for OpenVBX iphone app to 0.90	
- Fix for user password reset in OpenVBX
- For for problem with URLs containing double slashes in url generator