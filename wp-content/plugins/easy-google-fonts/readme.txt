=== Easy Google Fonts ===
Contributors: sunny_johal, amit_kayasth
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=28M8NWPHVQNYU
Tags: WordPress Google Fonts Plugin, Google Webfonts, Google Fonts WordPress, Typography, Webfonts, WordPress Webfonts, Fonts, WordPress Fonts, Theme Fonts, Theme Fonts Plugin
Requires PHP: 7.0.0
Requires at least: 5.8
Tested up to: 5.8
Stable tag: 2.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds google fonts to any theme without coding and integrates with the WordPress Customizer automatically for a realtime live preview.

== Description ==
> **Our new WordPress theme is almost ready!** Want to know when we launch? Awesome! [Visit our website](http://www.titaniumthemes.com) and enter your details and we will e-mail you as soon as we are ready :)

[View Plugin Demo Here](https://www.youtube.com/watch?v=Qk9z7S6J9Yo)

https://www.youtube.com/watch?v=Qk9z7S6J9Yo

[Follow us on twitter!](https://twitter.com/titaniumthemes)

If you have found this plugin useful please [donate here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=28M8NWPHVQNYU)

A simple and easy way to add custom google fonts to any WordPress theme without coding. This plugin integrates with the WordPress Customizer so you can preview google fonts on your site in realtime. It's compatible with any theme. 

It also allows you to create custom theme specific font controls in the admin settings area to control particular css selectors. Once created, these custom font controls are instantly available in the customizer no coding required!

= What does this plugin do? =

* This plugin allows you to **take full control of your theme's typography** in any WordPress theme (no coding required).
* It allows you to **choose from over 600+ google fonts** and font variants to insert into your website without coding.
* Allows you to **preview font changes on your website in realtime** using the WordPress Customizer.
* **Create Unlimited Custom Font Controls:** Create custom font controls in the admin area that are instantly available in the Customizer preview.
* Allows you to preview what your theme will look like with the new google fonts before you save any changes.
* Allows you to **create your own font controls and rules** in the admin area (no coding required).
* Allows you to easily change the look of your website with the click of a button.
* **Automatically enqueues all stylesheets for your chosen google fonts**.
* Allows you to add google fonts to your theme without editing the themes main stylesheet which allows you to update your theme without losing your custom google fonts.


= Plugin Features =

* **Live Customizer Preview:** Preview google fonts without refreshing the page in real time right in the WordPress Customizer.
* **Over 600+ Google Fonts** to choose from as well as a list of default system fonts.
* Works with any WordPress Theme. No coding required.
* Automatic Background Updates: Updates the google fonts list with the latest fonts automatically once.
* Translation Ready: MO and PO files are included.
* Seamless WordPress Integration: Uses the WordPress customizer for the live preview and has a white label admin area that looks like it is a part of WordPress.
* Custom WordPress Customizer Control: One of a kind control only available with this plugin.

= Who is this Plugin ideal for? =
* Anyone who is looking for an easy way to use google fonts in their theme without coding.
* Theme Authors: you can use this plugin to add custom google webfonts to your theme.
* Great for use on client projects or for use on existing websites.
* People that are happy with their theme but want an easy way to change the typography.
* Anyone with basic knowledge of CSS Selectors (in order to add custom font rules).


= Developer Features =
**Please note:** We are currently working on producing in-depth documentation for theme developers which will be available shortly. 

* **Cross Browser and Backwards Browser Compatible** (Tested in all major browsers).
* **Uses Action Hooks and Filters:** For advanced integration.
* **Seamless WordPress Integration:**  Live preview is integrated into the WordPress Customizer and the settings page follows core WordPress design guidelines.
* Uses the WordPress **Options API** to store and retrieve options.
* **Highly Secure:** Checks user permissions, uses nonces and the WordPress Security API.
* **Uses the REST API** for an enhanced admin experience.
* Strong Usability Testing.
* **Enhanced Performance:** Will only make a single request to google to fetch all fonts.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. From your WordPress Admin Dashboard go to: Plugins > Add New
1. Search for "Easy Google Fonts"
1. Locate the "Easy Google Fonts" plugin by Titanium Themes and click the Install Now button.
1. Activate the plugin labeled "Easy Google Fonts".
1. You're done!

== Screenshots ==

1. Before: The page before we have used the plugin to change the font.
2. After: The page after we have used the plugin to change the heading 1 font.
3. Live Customizer Font Control Preview: Choose from over 600+ Google Fonts and preview them instantly without refreshing the page.
4. Font Style Tab: Customize all styles of the font including the color, background, font size, line height etc.
5. Font Position Tab: Customize all position properties of the font including the margin, padding, border, display etc.
6. Create Screen: Create your own font controls for your theme.
7. Edit Screen: Manage any existing font controls for your theme.
8. Your custom control is instantly available for real time preview in the customizer. No coding required!
9. View and manage all of your custom controls in the admin area.
10. Plugin Settings: If you enter a valid google fonts api key then this plugin will update itself with the latest fonts automatically.

== Changelog ==
= 2.0.4 JS API =
* Added fallback for sanitization functions when no font key is selected.

= 2.0.3 Bugfix and Backwards Compatibilty =
* Bugfix: Tuples needed to be sorted in the google fonts url when the stylesheet url was being generated.
* Added complete support for the old tabs based api.

= 2.0.2 =
* Backwards Compatibilty: Added backwards compatibility for old theme integrations.

= 2.0.1 =
* Bugfix: Google stylesheet url requires font weights to be sorted in ascending order.

= 2.0.0 =
* Complete plugin codebase rewrite.
* Font controls/styles now support media queries.
* New Admin UI.
* New Customizer UI (now uses react js components).
* Live customizer preview has been rewritten to be more performant (by handling subsettings for each font control individually).
* Frontend query for font controls has been optimised for performance.
* Removed unnessary WordPress filters.

= 1.4.4 - WordPress 5.0 compatibility update =
* Updated webfonts.json with the latest google fonts.
* Last minor update before major rewrite.

= 1.4.3 - WordPress 4.8 compatibility update =
* Updated webfonts.json with the latest google fonts.
* Updated includes class-egf-frontend.php to only output styles if there is a selector.

= 1.4.2 - WordPress 4.7 compatibility update =
* Fixed issue where the font controls weren't saving in WordPress 4.7.
* Updated the font list.

= 1.4.1 - Urgent update =
* Fixed issue where the plugin wasn't taking effect for screen sizes under 700px

= 1.4.0 - Plugin update =
* Now automatically removes white spaces added to the api key on the settings page.
* Started rewriting part of the code for eventual media query support.

= 1.3.9 - Stylesheet Update =
* Updated the appearance in the customizer for the new changes in WordPress 3.4

= 1.3.7 - Important Update =
* Addressed potential security issue on the plugin admin page - Hardened security and escaped any attributes passed via the URL throughout the plugin.
* Now added support for arabic and telegu and devangari subsets.
* Updated JSON decode when parsing fonts for servers running an older version of PHP.

= 1.3.6 - Updated Stylesheet Enqueue =
* Addressed issue on certain servers where a 400 error was being returned when making a http request to google.

= 1.3.5 - Changed Stylesheet Enqueue =
* Removed esc_url_raw() as it was causing a 404 error when fetching the stylesheet from google.

= 1.3.4 - Customizer js API Update =
* Implemented font search to make it easier to browse google fonts (using the chosen js plugin).
* Implemented border controls.
* Implemented border radius controls.
* Completely rewritten the control javascript to utilise the new customizer js api (using backbone and underscore templates).
* Preview performance enhancement: Completely rewritten the preview javascript to utilise the new customizer js api.
* Performance update: Each font control now only registers one setting per control.
* Performance update: The functionality for each font control is lazy loaded to increase the customizer load speed.
* All settings for each font controls are handled by json objects (removed any hidden inputs and json2 library dependancy).

= 1.3.3 - WordPress 4.1+ Update =
* Added patch for WordPress 4.1
* The code is going to be rewritten in Backbone for future releases.

= 1.3.2 - Big Update =
* Now combining all of the font requests from google fonts in a single http request.
* Introducing Panels: A new customizer feature in WordPress 4.0 that makes it easier to manage alot of font controls. Our plugin creates a new panel called Typography. 
* Google fonts are now separated into the following categories to make them easier to navigate: Serif, Sans Serif, Display, Handwriting, Monospace.
* Now the plugin keeps font state when user switches page in customizer.
* Now there is a separate section for default and custom font controls.
* Improved the help tab in the admin area to help people obtain a google api key.
* Synchronised Force Styles between the Manage Font Controls and the Edit Font Controls Screen
* Fixed issue with the font weights not showing up for the customizer control.
* The plugin now checks and handles occurances where theme developers have removed all default controls.

= 1.3.1 =
* Changed order of style outputs
* Futher UI Improvements

= 1.3 =
* Critical Update: Made the plugin WordPress 3.9 compatible
* Performance tuned the plugin
* Removed recursion during runtime.
* Updated local font list

= 1.2.5 =
* Performance enhancement for older browsers implemented. Safe to upgrade.

= 1.2 =
* Big Update: Complete Rewrite of Plugin
* New controls: Background Color, Margin, Padding and Display.
* Added Subset support.
* Introduced a large amount of actions and filters for theme developers.
* Big performance enhancement. The old version used to register 32 settings per control, this is now down to 3.
* Lightning fast customizer loading times, under 1 second.
* Rewrote the plugin into classes and views to make it more managable for future development.
* Rewrote the plugin into classes.
* MO/PO language files now included.

= 1.1.1=
* 3.8 Admin integration

= 1.1 =
* Full google fonts transient integration.

= 1.0 =
* First plugin release.

== Upgrade Notice ==
Nothing to see here...

== Frequently Asked Questions ==
For a quick video demo please view the [youtube screencast](https://www.youtube.com/watch?v=Qk9z7S6J9Yo&t=36s).

== Credits and Donation ==

* [WordPress Components](https://developer.wordpress.org/block-editor/reference-guides/components/). (Used in the admin settings page)
* Otherwise, this plugin has been entirely written from scratch by Titanium Themes.

If you have found this plugin useful please [donate here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=28M8NWPHVQNYU)
