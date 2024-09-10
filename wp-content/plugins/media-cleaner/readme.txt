=== Media Cleaner: Clean your WordPress! ===
Contributors: TigrouMeow
Tags: clean, media, files, images, library
Donate link: https://www.patreon.com/meowapps
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 6.7.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Clean your WordPress! Eliminate unused and broken media files. For a faster, and better website.

== Description ==

Media Cleaner is a powerful plugin that helps you clean up your WordPress media library by deleting unused media entries and files, as well as fixing broken entries. With an internal trash feature, you can preview and confirm changes before permanently deleting anything. Plus, Media Cleaner uses smart analysis to ensure compatibility with specific plugins and themes. 

Use it alongside [Database Cleaner](https://wordpress.org/plugins/database-cleaner/) for the ultimate clean-up experience.

Media Cleaner is like a ninja assassin for your Media Library - it'll stealthily take out all the unnecessary media and broken entries that are cluttering up the place. Just make sure you have a **solid backup plan** in place before you let this bad boy loose. 

To learn more about compatibility, features, and the Pro version, check out the [tutorial](https://meowapps.com/media-cleaner/tutorial/) on the [official website](https://meowapps.com/media-cleaner/).

=== COMPATIBILITY ===

This plugin is compatible with all media types, including retina and WebP versions. It has been tested on a wide range of WordPress versions, including the latest version with Gutenberg, as well as on various themes with a large community of users. It also supports WooCommerce. For users with more complex plugins for handling website content, the Pro version may be necessary for optimal compatibility. We are constantly working to increase compatibility with other plugins.

=== PRO VERSION ===

[Media Cleaner Pro](https://meowapps.com/media-cleaner/) adds extra features to the free version of Media Cleaner:

* Filesystem Analysis: Scans your physical /uploads directory and matches it against the Media Library.
* Extra support for complex plugins, such as ACF, Metabox, Divi Builder, Fusion Builder (Avada), WPBakery Page Builder, Visual Composer, Elementor, Beaver Builder, Brizy Builder, Oxygen Builder, Slider Revolution, Justified Image Grid, Avia Framework, and many more!
* Live Site Scan: Analyzes the online version of your website, potentially improving accuracy in some cases.
* WP-CLI support: Allows you to run the plugin at a higher speed or automatically with direct server access (via SSH).

== Installation ==

1. Upload the plugin to WordPress.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Meow Apps -> Cleaner in the sidebar and check the appropriate options.
4. Go to Media -> Cleaner.

== Screenshots ==

1. Media -> Media Cleaner

== Changelog ==

= 6.7.8 (2024/08/01) =
* Fix: Elementor parser was not working properly.
* Add: Video Block support.
* Info: We are working hard on Media Cleaner. If you want to share some love, write a simple and nice review [here](https://wordpress.org/support/plugin/media-cleaner/reviews/?rate=5#new-post). Thank you so much! 💖

= 6.7.7 (2024/06/28) =
* Fix: Warnings with ACF.
* Fix: Base folder for the Filesystem Scan.
* Update: Cleaned the UI a bit more.

= 6.7.6 (2024/06/05) =
* Update: Better References section.
* Add: Support for Bricks Builder.
* Update: Refreshed the UI, updated to the latest common librairies.

= 6.7.5 (2024/05/24) =
* Fix: Logging system.

= 6.7.4 (2024/04/27) =
* Update: Updated description for OB cleaning to enhance clarity.
* Add: Support for Breakdance Builder, extending compatibility.
* Update: Added shortcode checking for Oxygen Builder.
* Fix: Updated readme file to comply with the latest WordPress guidelines.

= 6.7.3 (2024/03/01) =
* Update: Better translations.
* Update: Safer logs system.

= 6.7.2 (2024/02/02) =
* Add: "Create Batch" feature for Filesystem scans, streamlining the scanning process.
* Add: "Delete Permanently" option in trash tab for targeted item management.
* Add: Tooltip for repair mode to enhance user understanding and interaction.
* Fix: Option to disable OB Cleaning.
* Fix: Corrected dashboard media link functionality for subdirectories.
* Fix: Resolved issues with backslash replacement on multiple occurrences for more accurate processing.

= 6.7.0 (2024/01/13) =
* Add: New 'check-live' argument for WP-CLI.
* Add: Import and Export of the settings.

= 6.6.9 (2023/12/05) =
* Add: Expert Mode for advanced users.

= 6.6.8 (2023/11/18) =
* Add: Support for ACF File Field based on IDs.
* Add: Repair Mode for Filesystem Scan (use this carefully, still in beta).
* Update: Much better "References" section in the Dashboard, with additional filters.
* Update: Various additional enhancements, maybe you'll notice! 😊
* Add: Support for Academy LMS.

= 6.6.7 (2023/09/21) =
* Update: Enhanced the get_references_for_post_id function.
* Update: Code cleaning.

= 6.6.6 (2023/09/14) =
* Add: The get_reference_for_media_id and get_references_for_post_id functions are now accessible through the global $wpmc_core variable. Those functions will return where a specific media entry is used, or which  media entries are used in a specific post. 

= 6.6.5 (2023/07/25) =
* Update: Better checkboxes.
* Update: Link to the posts in the References section.
* Add: Support for Mailpoet.

= 6.6.4 (2023/05/30) =
* Update: Improved the UI and its elements.

= 6.6.3 (2023/04/09) =
* Add: New filter to see the found references. This will improve a lot.
* Fix: Tiny fixes, retrieved the main dashboard, and lighter bundles.
* Info: The new version of WordPress (6.2) came with what is seemingly a bug with the $wpdb->prepare. There are workaround, and I fixed an issue I was aware of. If you find any others, please kindly report it [here](https://wordpress.org/support/plugin/media-cleaner/).

= 6.6.0 (2023/02/21) =
= Fix: Avoid certain errors related to ACF and fields which were created with former versions.

= 6.5.8 (2023/02/09) =
* Update: Slightly cleaner UI (and it will get better and better).
* Update: Better support for Avada.

= 6.5.7 (2023/02/01) =
* Fix: Little issue with the nekoFetch.
* Add: Timer on the Scan button.

= 6.5.6 (2023/01/30) =
* Update: Optimization and better handling of Divi.

= 6.5.5 (2023/01/09) =
* Add: Support for Uncode Theme.
* Add: Reset settings button.
* Update: Smaller package, better performance.
