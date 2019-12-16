=== UF Health Require Image Alt Tags ===
Contributors: ufhealth, ChrisWiegman
Donate link: http://giving.ufhealth.org/ways-to-give/give-now/
Tags: a11y, alt tag, accessibility
Requires at least: 4.2
Requires PHP: 5.6
Tested up to: 4.9.8
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Forces users to add an ALT tag when adding images to WordPress posts and more.

== License ==
Released under the terms of the GNU General Public License.

== Description ==

Forces users to add an ALT tag when adding images to WordPress posts and more.

= Screenshots =

= Features =

* Multisite compatible
* Handle single or multiple image uploads

== Frequently Asked Questions ==

= Can I change the disclaimer copy shown in the warning box? =
* Yes. Use the `ufhealth_alt_tag_disclaimer` filter to edit the copy.

== Installation ==

1. Backup your WordPress database, config file, and .htaccess file
2. Upload the zip file to the `/wp-content/plugins/` directory
3. Unzip
4. Activate the plugin through the 'Plugins' menu in WordPress
5. All images will now require alt text. There are no settings to worry about. To disable the feature simply disable the plugin.

== Changelog ==

= 1.2 =
* Updated plugin for newer coding standards, easier docker development and more.

= 1.1.5 =
* Minor fixes for WordPress 4.9 compatibility

= 1.1.4 =
* Fixed bugs leading to false positives or a stuck modal in certain situations.

= 1.1.3 =
* Add ufhealth_alt_tag_disclaimer filter to edit copy

= 1.1.2 =
* Check for image as media type for standard insertion box to allow other file times to be added.

= 1.1.1 =
* Better catch of edge cases in the image upload process.

= 1.1 =
* Make all test i18n compatible
* Add "Alt Text" column to media table to easily find missing alt tags.

= 1.0 =
* Initial Release
