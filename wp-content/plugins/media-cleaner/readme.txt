=== Media Cleaner: Clean your WordPress! ===
Contributors: TigrouMeow
Tags: clean, media, files, images, library
Donate link: https://www.patreon.com/meowapps
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 7.0.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Clean your WordPress! Eliminate unused and broken media files. For a faster, and better website.

== Description ==

Media Cleaner is a powerful plugin that helps you clean up your WordPress media library by deleting unused media entries and files, as well as fixing broken entries. With an internal trash feature, you can preview and confirm changes before permanently deleting anything. Plus, Media Cleaner uses smart analysis to ensure compatibility with specific plugins and themes. 

Use it alongside [Database Cleaner](https://wordpress.org/plugins/database-cleaner/) for the ultimate clean-up experience.

Media Cleaner is like a ninja assassin for your Media Library - it'll stealthily take out all the unnecessary media and broken entries that are cluttering up the place. Just make sure you have a **solid backup plan** in place before you let this bad boy loose. 

To learn more about compatibility, features, and the Pro version, check out the [tutorial](https://meowapps.com/media-cleaner/tutorial/) on the [official website](https://meowapps.com/media-cleaner/).

[youtube https://www.youtube.com/watch?v=qmDSgWZWnSw]

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

= 7.0.8 (2026/04/25) =
* Update: Renamed internal origin references for clarity.
* Update: Path descriptions now use 'thumbnails' instead of 'files' for clarity.
* Fix: Image URLs in the metabox are now only added when actually present.
* Update: Improved UI/UX for better user experience. We hope you'll love it! 🥰

= 7.0.7 (2026/04/15) =
* Add: New "References Buffer" option to split and flush reference processing to reduce memory errors on large datasets.
* Fix: Improved base64 handling to better cope with malformed edge cases and prevent related issues.
* Add: More detailed timeout debugging for reference extraction to make diagnosing performance issues easier.
* Fix: Removed base64 content from HTML before URL parsing to prevent regex timeouts on complex pages.
* Update: Replaced cached references with a unique hash-based system and limited media ID lookups to multilingual URLs for faster and cleaner reference handling.

= 7.0.6 (2026/03/27) =
* Fix: Hotfix to resolve a security issue that could allow SSRF attacks through embedded iframes.

= 7.0.5 (2026/03/09) =
* Fix: Ensure plugin checks run after all required options are initialized.
* Add: Introduce LayerSlider compatibility and update Enfold theme integration.
* Update: Improve origin handling so the plugin can work with sources that don't use numeric IDs.
* Add: Include more direct documentation links in error popups.
* Add: Provide ACF-specific filters for URLs and IDs to give developers more precise control over field handling.

= 7.0.4 (2026/02/25) =
* Fix: Improve how issue names are displayed.
* Add: Allow selecting specific thumbnail sizes (to avoid the unnecessary ones).
* Fix: Ensure media scans always use ID-based URL references (improving compatibility with WPML).
* Update: Improve compatibility with multisite and network site.
* Update: Apply better default settings on fresh installs.
* 🎵 Discuss with others about Media Cleaner on [the Discord](https://discord.gg/bHDGh38).
* 🌴 Keep us motivated with [a little review here](https://wordpress.org/support/plugin/media-cleaner/reviews/). Thank you!
* 🥰 If you want to help us, check our [Patreon](https://www.patreon.com/meowapps). Thank you!

= 7.0.3 (2026/01/27) =
* Add: Added full support for newer versions of the Oxygen builder.
* Fix: Updated the Divi integration so URLs with direct UR patterns are no longer incorrectly marked as safe.
* Update: Extended Divi 5 support by recognizing mp4 and webm keys for background videos and the new "linkUrl" key in Divi blocks.
* Update: Improved Elementor compatibility by having the parser also check page settings for relevant data.
* Update: Made excluded directories easier to customize by turning them into a configurable setting that developers can filter.

= 7.0.2 (2026/01/05) =
* Fix: Prevent warnings and errors caused by empty HTML output and broken shortcodes.
* Add: Support FooGallery album shortcodes for better gallery integration.
* Add: Display icons for reference tags including SAFE, CACHE, OG_THUMB, and GEN_THUMB.
* Update: Improve FooGallery handling by supporting non-HTML shortcode output and flagging cached media items.
* Update: Remove the transient cache for post types and include the post ID in reference data.
* Fix: Correct the JetEngine parser to ensure dynamic content is detected and rendered properly.

= 7.0.1 (2025/12/16) =
* Add: Introduced scan resume support for both Thumbnails and Duplicates scans.
* Fix: Prevented an error in the Metabox parser caused by treating text values like lists.
* Fix: Ensured the “Repair x Entries” notice only appears while repair mode is active.
* Add: Added a new “Optimize Thumbnails” feature.
* Add: Added support for Toolset.

= 7.0.0 (2025/12/03) =
* Fix: Prevent the plugin from loading on pages where it is not needed.
* Add: CSV export option for scan results to make it easier to review.
* Add: Support for pagination in FooGalleries so all gallery items are properly scanned.
* Add: Recursive scanning for ACF Blocks to ensure nested blocks are fully detected.
* Add: FooGallery parser support for block shortcodes.
* 🎵 Discuss with others about Media Cleaner on [the Discord](https://discord.gg/bHDGh38).
* 🌴 Keep us motivated with [a little review here](https://wordpress.org/support/plugin/media-cleaner/reviews/). Thank you!
* 🥰 If you want to help us, check our [Patreon](https://www.patreon.com/meowapps). Thank you!

= 6.9.8 (2025/11/13) =
* Update: Enhanced UI and UX.
* Fix: Fixed the Smart Slider 3 parser to load correctly and function as expected.
* Add: Parsed "href" and "src" attributes in Enfold HTML to accurately extract URLs.
* Refactor: Enhanced caching to store IDs and URLs in the DB.
* Add: Support for the "value" attribute in Divi 5 blocks to facilitate ID extraction.
* Update: Refined Divi parser to support both Divi 4 and Divi 5 simultaneously.
* Fix: Hotfix for handling null values with substr, avoiding deprecated functions.
* Fix: Fixed the Scan confirmation prompt to show only once.
* Fix: Restricted scope of the "substr" function to prevent deprecation issues.
* Update: Optimized database checks and CLI commands for faster performance.

= 6.9.7 (2025/09/30) =
* Add: Timeout mechanism to avoid a stalled scan.
* Add: New Duplicates scan type (beta).
* Update: Confirmation before restarting a scan to prevent accidental restarts.
* Update: Better support for Kadence Theme.
* Add: "Deleted At" column and filters.
* Update: Optimized the WooCommerce parser for better performance.
* Fix: Remove a few minor bugs.

= 6.9.5 (2025/08/27) =
* Add: A clear message guiding users on how to disable the pop-up.

= 6.9.4 (2025/08/16) =
* Fix: Added check for WP_Error in ACF after database calls to improve stability.
* Fix: Resuming process now properly continues from step 3.
* Add: Support for Meow Gallery.
* Add: Warning modal; Sorry guys, but it looks like we need to warn you better about the risks.
* Update: Header included for Filesystem + Content-Type for better handling.
* Comments: Clarified handling of WebP images for improved compatibility.

= 6.9.3 (2025/07/23) =
* Add: Enhanced the resume functionality.  
* Fix: Hotfix for errorCounts modal display to ensure proper visibility.  
* Update: Show incompatible plugins message only in the free version for clarity.  
* Update: Improved memory management by replacing in-memory references with direct processing and transients.  
* Update: Refreshed common libraries for better performance and stability.

= 6.9.2 (2025/07/01) =
* Add: Added support for Fluent Forms.
* Fix: Fixed NekoTable display issues.
* Update: Streamlined Divi parser for better image URL extraction and improved filesystem handling.
* Update: Enhanced thumbnail URL extraction and added new function to retrieve attachment sizes.

= 6.9.1 (2025/06/29) =
* Add: Support for Kadence Blocks.
* Add: Support for Salient Theme Elements.
* Add: Support for Houzez Theme.
* Add: Support for WooCommerce variation galleries.
* Update: Improved Enfold parser with better handling of nested shortcodes and enhanced ID/URL extraction.

= 6.9.0 (2025/05/11) =
* Update: Removed unused code for live content checks in CLI commands to improve plugin performance and maintainability.

= 6.8.9 (2025/05/04) =
* Fix: Ensure the "Disable Shortcode Analysis" setting is now respected in all content parsers.
* Add: Support for ACF 'file' fields now includes returning the file URL when using ID references.

= 6.8.8 (2025/05/01) =
* Add: Introduced SAFE support for URLs from srcset in WooCommerce galleries.
* Add: Added a function to extract thumbnail URLs from srcset for better detection.
* Fix: Prevented warning messages when URLs are empty.
* Fix: Corrected countdown end event behavior.
* Update: Enhanced the W3 parser with array_to_ids_or_urls and added recursive support.
* Add: Added a parser for W3 Total Cache to handle media attachments.
* Add: Introduced a force trash option for cases when trash isn’t emptied automatically.
* Update: Temporarily disabled live content scanning to prevent issues.
* Fix: Corrected a typo in iframe handling comments.
* Update: Improved shortcode scanning by retrieving all common attributes for IDs and URLs.
* Fix: Prevented recursion in ACF field scanning with flexible content.
* Update: Reworked the ACF Parser to fully support flexible content structures.
* Update: Added an index on mediaId in the references table to optimize step 4 performance.

= 6.8.7 (2025/03/12) =
* Update: Improved type handling and updated the Divi parser for better compatibility.
* Fix: Added a recursion limit to ACF field scanning functions to prevent infinite loops.

= 6.8.6 (2025/02/17) =
* Add: Added a SAFE tag to Foo Gallery full sizes to mark certain media as in use.
* Update: Moved SAFE tag logic from JavaScript to PHP for better efficiency.
* Fix: Corrected featured image thumbnails and improved SAFE tag detection.
* Update: Improved UI by replacing the timer component with a countdown version.
* Update: Added an error modal with auto-retry functionality for better user experience.
* Update: Reworked compatibility for Avada builder.

= 6.8.5 (2024/12/25) =
* Fix: HTML needs to be well-decoded before being sent to DOMDocument.
* Fix: Avoid an issue related to logos.
* Info: Merry Christmas and Happy New Year! 🎄🎉

= 6.8.4 (2024/12/06) =
* Add: Support for Spectra.
* Add: Support for Tutor LMS.
* Add: Support for Foo Gallery.
* Info: We are working hard on Media Cleaner. If you want to share some love, write a simple and nice review [here](https://wordpress.org/support/plugin/media-cleaner/reviews/?rate=5#new-post). Thank you so much! 💖

= 6.8.3 (2024/11/27) =
* Fix: MetaBox parser was not working properly.
* Fix: The free version of Media Cleaner was not properly displaying the incompatible plugins.

= 6.8.2 (2024/11/14) =
* Update: Enhanced the UI in many little ways.
* Update: Don't show the thumbnails for non-image files.
* Fix: Better parser for Avada.

= 6.8.1 (2024/11/04) =
* Add: Check for empty path in repair mode.
* Add: Introduction tutorial.

= 6.8.0 (2024/10/17) =
* Fix: Search in References (Cleaner Dashboard).

= 6.7.9 (2024/09/24) =
* Update: Internal improvements to avoid some errors with low-quality servers such as OVH, GoDaddy, etc.

= 6.7.8 (2024/08/01) =
* Fix: Elementor parser was not working properly.
* Add: Video Block support.

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
