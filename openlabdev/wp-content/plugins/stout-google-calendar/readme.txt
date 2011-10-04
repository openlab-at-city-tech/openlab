=== Plugin Name ===
Contributors: stoutdesign
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8Y6HL2PMLPQXA
Tags: google, calendar, custom, colors, embed, widget, admin, plugin, sidebar
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.2.3

Stout Google Calendar allows you to add and customize the colors and options of embedded Google Calendars directly within the WordPress admin.

== Description ==

The Stout Google Calendar plugin allows you to easily add and customize embedded Google Calendars to your Wordpress site. You can even change color settings that are not normally modifiable, allowing you to seamlessly integrate Google Calendars into the look of your site. No more ugly Google Calendars!

Features include:

*   Customize the color scheme of embedded Google Calendars
*   Options from the Google Embeddable Calendar Helper can be modified directly within the WordPress Admin
*   Save multiple calendars, each with it's own color scheme, size and display settings
*   Display calendars in a Widget, Pages or Posts via shortcode or in templates
*   Live preview of all changes to a calendar as you make each change
*   Easy color picker or directly input hexadecimal color values


== Installation ==

1. Download the plugin zip file `stout-google-calendar.zip`.
1. Unzip and upload the `stout-google-calendar` folder to the `/wp-content/plugins/` directory. Or, you may go to 'Plugins > Add New' in the WordPress menu. 'Upload' the `stout-google-calendar.zip` file or 'Search' for "Stout Google Calendar".
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to 'Settings > Stout Google Calendar' in the WordPress menu.
1. Grab the embed code from your Google Calendar [(Instructions Here)](http://www.google.com/support/calendar/bin/answer.py?hl=en&answer=41207)
1. Paste the embed code into the "Google Calendar iframe embed code:" textarea under 'Add a New Calendar'
1. Have fun editing the colors and options right in WordPress. HINT: After pasting in embed code, "Tab" or "Click" out of the textarea. A "Preview Calendar" link will appear, click on it. Leave the calendar open while you edit your colors/options and watch the calendar evolve before your eyes! 

**Requirements**:

*   A Google Calendar (actually, the embed code for a Google Calendar)
*   A browser with JavaScript enabled (at least for the admin)
*   PHP 4.3.0 or later
*   Support for one of the following JSON libraries:
 *   [PECL JSON extension](http://pecl.php.net/package/json) (built in to PHP 5.2.0 and higher) *or*
 *   [PEAR::Services_JSON](http://pear.php.net/package/Services_JSON) package
*   Support for one of the following http methods:
 *  	[cURL extension](http://www.php.net/manual/en/curl.installation.php) *or*
 *   [allow_url_fopen](http://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen) configuration option plus OpenSSL support
*   If you have met the above server requirements and you continue to have problems, you can find more [documentation here](http://www.unitz.com/u-notez/2009/04/color-customization-for-embedded-google-calendars)


== Frequently Asked Questions ==

= Why the Stout Google Calendar plugin? =

Basically, we really dislike the default colors of embedded Google Calendars - at least how they look on 99.9% of the sites we develop. We went in search of a workaround and found the great [gcalendar-wrapper.php](http://www.unitz.com/u-notez/2009/04/color-customization-for-embedded-google-calendars/) script by Chris Dornfeld. After giving it a test run we thought it would be really cool to have a WordPress plugin that allowed us to easily manage calendars with customized color schemes from within WordPress. We modified the gcalendar-wrapper script, whipped up some code, added a dash of AJAXy goodness to the admin page and, voila!, the Stout Google Calendar plugin was created.

= How do I know what is changed by each color choice? =

Instead of writing a book trying to explain what each color choice changes in each calendar view (Month/Week/Agenda) we decided it would be easier to give you instant feedback when you change a color or option. The best way to do this is add your embed code in the textarea and the click on the "Preview Calendar" link (you may have to tab out of the textarea or click on something else on the page for the link to appear.) Once you see your calendar preview, move it out of the way of the color pickers/options - **Don't close the preview**. While the preview is open, start modifying your calendar colors/options. You'll see the calendar change before your eyes. If the calendar is too big and you can't see the colors/options then you should temporarily change the width & height to about 300, relaunch the preview and continue on. When you are happy with your calendar - **Be sure to click on the 'Add Calendar' or 'Update Calendar' button**.

= My calendar looks fine in the admin but asks my site visitors to login, why? =

Your calendar must be shared with everyone for any site visitor to see. [Learn how to share your Google Calendar](http://www.google.com/support/calendar/bin/answer.py?answer=37083).

= Can multiple Google calendars be displayed at once? =

Yes. Please look at this forum post which describes [how to display multiple calendars at once](http://wordpress.org/support/topic/plugin-stout-google-calendar-display-muliple-calendars-in-one).

= Can multiple calendars be created? =

Yes, you can save multiple calendars each with its own colors, size and options. 

= Is there a widget? =

Yes, there is a widget which allows you to easily embed a calendar within a widget. You simply select the calendar from your saved calendar list and choose whether or not to display the calendar name above it. You may have to tweak the size of your calendar for it to look good within the widget area.

= Can I add the calendar to a Post/Page? =

Yes, use the shortcode `[stout_gc id=YOUR_CALENDAR_ID]` in a Post/Page or you can use `<?php echo stout_gc(YOUR_CALENDAR_ID); ?>` in your templates. After you save your calendar, the exact shortcode and template tag will be displayed under the calendar name.

= How do I change the color of calendar events as they appear within my calendar? =

You can't, well, at least not through the Stout Google Calendar plugin. Google has a set of colors available for its calendars. You will need to go to your [Google Calendar and make the change](http://www.google.com/support/calendar/bin/answer.py?answer=37227)

= It doesn't work for me. What's up? =

Well, there may be a few things going on. There are some server requirements which must be met. You can check your [phpinfo](http://www.php.net/phpinfo#function.phpinfo.examples) screen to see if your server meets the requirements below:

*   PHP 4.3.0 or later
*   Support for one of the following JSON libraries:
 *   [PECL JSON extension](http://pecl.php.net/package/json) (built in to PHP 5.2.0 and higher) *or*
 *   [PEAR::Services_JSON](http://pear.php.net/package/Services_JSON) package
*   Support for one of the following http methods:
 *  	[cURL extension](http://www.php.net/manual/en/curl.installation.php) *or*
 *   [allow_url_fopen](http://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen) configuration option plus OpenSSL support
*   If you have met the above server requirements and you continue to have problems, you can find more [documentation here](http://www.unitz.com/u-notez/2009/04/color-customization-for-embedded-google-calendars).

= Who is responsible for this plugin? =

In short, Matt McKenny at Stout Design for the [Stout Google Calendar](http://blog.stoutdesign.com/stout-google-calendar-custom-colors) WordPress plugin (admin interface, widget, shortcode, etc), Chris Dornfeld for the [gcalendar-wrapper.php script](http://www.unitz.com/u-notez/2009/04/color-customization-for-embedded-google-calendars) that modifies the Google Calendar CSS, and Stefan Petre for the [jQuery Color Picker](http://www.eyecon.ro/colorpicker/) used in the admin.

== Screenshots ==

1. "Add a New Calendar" view. No preview button available. 
2. Google Calendar embed code added. Notice the "Preview Calendar" that appears after embed code is present.
3. Calendar Preview "floats" on top of settings
4. Calendar Preview moved to see full view of color picker
5. Preview of customized calendar in "Month" view
6. Preview of customized calendar in "Agenda" view
7. Widget for Stout Google Calendar

== Changelog ==
= 1.2.3 - 2011-02-23 =
*		Bugfix: Fix typo - remove wpurl from get string

= 1.2.2 - 2011-02-23 =
*		Bugfix: Use hosted calendar images (Fixes problem with wpurl embedded in get request for gcalendar-wrapper.php)
*		Added a few notes at bottom of plugin settings page
*		Added error messages for WP_Http

= 1.2.1 - 2011-02-23 =
*		Bugfix: Include 'wp-includes/pluggable.php' to deal with: Fatal error: Call to undefined function wp_get_current_user()

= 1.2.0 - 2011-02-22 =
*		Use WP_Http class to retrieve calendar from Google. Replaces previous cURL/fopen logic embedded in gcalendar-wrapper.php script.
*		Added internationalization and French translation. Thanks to Bastien Durand for setting this up and translating to French. 
*		No longer updating plugin via the register_activation_hook() function. 

= 1.1.01 - 2011-02-05 =
*		Bug Fix: Fixed issue of calendar not displaying when percentage specified for bubble width

= 1.1.0 - 2011-02-02 =
*		Added Bubble Width option; Allows ability to set event detail "popup" width (in month view)
*   Properly enqueue CSS and scripts to only load while on Stout Google Calendar settings page; fixes issue with SGC styles conflicting with other admin CSS including PageLines PlatformPro 

= 1.0.9 - 2011-01-26 =
*		Strip slashes from the calendar name admin settings for Stout Google Calendar 

= 1.0.8 - 2011-01-26 =
*   Set event "bubble"/popup overlays to 80% calendar width. Prevents details from being clipped if calendar width is under 400px in month view.
*		Strip slashes from the calendar name in the widget admin and widget display on pages 

= 1.0.7 - 2010-12-01 =
*   Fixed incorrect path to JSON.php file

= 1.0.6 - 2010-11-29 =
*   Added "language" option to set language for calendar to be displayed in
*   Fixed issue with a second window in Firefox being brought in front of the active Firefox window every time a form submitted or link clicked in the admin (removed colorpicker's eye.js, utils.js and layout.js). 
*   Fixed error caused by undefined $msg variable and issue when trying to match border. Thanks [Jeff Lambert](http://webphysiology.com/)
*   Corrected misspelled name for Chris Dornfeld in credits - sorry Chris!

= 1.0.5 - 2010-11-15 =
*   Fixed typo in widget code to use `$wpdb->prefix` correctly
*   Updated screenshots

= 1.0.4 - 2010-11-15 =
*   Updated widget code to use `$wpdb->prefix` as corrected in 1.0.2

= 1.0.3 - 2010-11-15 =
*   Updated `gcalendar-wrapper.php` script with latest version (version 2010-11-15) from [Chris Dornfeld](http://www.unitz.com/u-notez/2009/04/color-customization-for-embedded-google-calendars/) which fixes issue with calendar(s) loading. Now requires PHP OpenSSL

= 1.0.2 - 2010-11-15 =
*   Fixed issue with not being able to save to database because database table name was hardcoded instead of using `$wpdb->prefix`

= 1.0.1 - 2010-11-02 =
*   Fixed issue with iframe transparency in IE
*   Fixed issue with button position in admin
*   Cleaned up code formatting a bit
*   Initial release to WordPress Plugin repository

= 1.0 - 2010-10-29 =
Initial Release


== Upgrade Notice ==
= 1.2.3 - 2011-02-23 =
*		Bugfix: Fix typo - removewp url from get string

= 1.2.2 - 2011-02-23 =
*		Bugfix: Use hosted calendar images (Fixes problem with wpurl embedded in get request for gcalendar-wrapper.php)
*		Added a few notes at bottom of plugin settings page
*		Added error messages for WP_Http

= 1.2.1 - 2011-02-23 =
*		Bugfix: Include 'wp-includes/pluggable.php' to deal with: Fatal error: Call to undefined function wp_get_current_user()

= 1.2.0 - 2011-02-22 =
*		Use WP_Http class to retrieve calendar from Google. Replaces previous cURL/fopen logic embedded in gcalendar-wrapper.php script.
*		Added internationalization and French translation. Thanks to Bastien Durand for setting this up and translating to French. 
*		No longer updating plugin via the register_activation_hook() function. 

= 1.1.01 - 2011-02-05 =
*		Bug Fix: Fixed issue of calendar not displaying when percentage specified for bubble width

= 1.1.0 - 2011-02-02 =
*		Added Bubble Width option; Allows ability to set event detail "popup" width (in month view)
*   Properly enqueue CSS and scripts to only load while on Stout Google Calendar settings page; fixes issue with SGC styles conflicting with other admin CSS including PageLines PlatformPro 

= 1.0.9 - 2011-01-26 =
Fixes issue of slashes appearing in the calendar name in admin settings for Stout Google Calendar 

= 1.0.8 - 2011-01-26 =
Upgrade fixes issue of slashes appearing in calendar name in widgets.

= 1.0.7 - 2010-12-01 =
Upgrade to fix issue if getting error messages because JSON.php file cannot be included.

= 1.0.6 - 2010-11-15 =
Upgrade to fix issue with inactive browser window being brought in front of active window while in admin. 

= 1.0.5 - 2010-11-15 =
Upgrade to fix issue with widget and custom named WordPress Database Table prefix (v 1.0.4 incorrect)

= 1.0.4 - 2010-11-15 =
Upgrade to fix issue with widget and custom named WordPress Database Table prefix

= 1.0.3 - 2010-11-15 =
Upgrade required to ensure calendars load. `gcalendar-wrapper.php` script updated as result of change by Google. Requires PHP OpenSSL.

= 1.0.2 - 2010-11-15 =
Required upgrade in order for the Stout Google Calendar to work with custom named WordPress Database Table prefix.

= 1.0.1 - 2010-11-02 =
Minor bug fixes

= 1.0 - 2010-10-29 =
No known issues (initial release)
