=== WP Google Plus Connect ===
Contributors: Messenlehner
Tags: google plus, google, googleplus, plugin, login, api, buddypress, registration, multisite, members, import, post, social, widget, badge, google direct connect
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.0.5.1
License: GPLv2 or later

Add Google+ Direct Connect Badge & allow your WordPress/BuddyPress users to register or login via their Google+ account & import their stream via the Google+ API.

== Description ==

Display a Google+ Direct Connect Badge on your site via widget or shortcode, allow your members to login/register via their Google+ account or sync your Google+ stream with your blog posts or BuddyPress activity via the Google+ API.

WordPress Google+ Connect allow your website members the ability to register and/or log in via their Google+ account utilizing the Google+ API. Set up a Google Plus Application and store the API credentials in the WordPress backend, when a user clicks on the "Login with Google+" button that gets added to the WP log in screen or anywhere via a short code and authenticates their Google Plus account a WordPress account will be created with their G+ information and they will automatically be logged in. Any existing WordPress users can also log in via Google+ and link their two accounts.

If BuddyPress is enabled a Google+ login button will appear on the sidebar login and the registration page. Google+ profile photos will also be imported in as BuddyPress avatars. Members can stream their Google+ activity into their BuddyPress activity if they choose via the Google+ options screen under the logged in BuddyPress members profile settings page. A cron job runs every 30 minutes to import any new Google plus activity from connected users.

Configure Google+ Direct Connect and help visitors find your Google+ page and add it to their circles from directly within a Google Search.

Configure your Google+ Badge and allow visitors to directly connect with and promote your brand on Google+. Visitors can also add your Google+ page to their circles directly from your website. Badges are easy to configure and can be placed on your website via a short code or a widget.

== Installation ==

Upload the wp-google-plus-connect plugin to your website, Activate it, then head to the "Google+ Connect" link added to the settings menu.

== Screenshots ==

1. Google+ Direct Connect Settings and Badge Configuration
2. Google+ API Configuration for setting up Application
3. Google+ Connect Button to allow WordPress users to login via their G+ account
4. Google+ Connect Button to allow BuddyPress members to login and register via their G+ account

== Changelog ==

= 1.0.5 =
* Fixed some errors.
* Added login button to API settings page to test authentication. 
* Don't display login button on front end unless API settings are saved and authenticated.
* Fixed issue with badge showing above content. 

= 1.0.4 =
* Added Language to Direct Connect & Badge Settings Admin Page.
* Added Widget Title to Google+ Badge Widget

= 1.0.3 =
* Added Direct Connect & Badge Settings Admin Page.
* Added Direct Connect Support: &lt;link href="{GoogleplusPageUrl}" rel="publisher" /&gt;
* Added Google+ Badge Short Code and Widget

= 1.0.2 = 
* BuddyPress profile settings page for Google+ members can toggle option to stream their Google+ activity into their BuddyPress activity
* Set up cron job to import Google+ connected users activity every 30 minutes. Only new activity not already imported gets imported.

= 1.0.1 = 
* BuddyPress Compatible

= 1.0 =
* Plugin Launch