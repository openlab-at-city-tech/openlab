=== Media Cleaner: Clean your WordPress! ===
Contributors: TigrouMeow
Tags: clean, delete, file, files, images, image, media, library, upload, acf
Donate link: https://meowapps.com/donation/
Requires at least: 6.0
Tested up to: 6.3.1
Requires PHP: 7.4
Stable tag: 6.6.7
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

= 6.6.7 (2023/09/21) =
* Update: Enhanced the get_references_for_post_id function.
* Update: Code cleaning.
* Info: I am working hard on Media Cleaner. If you want to give me some love and motivation, write a simple and nice review [here](https://wordpress.org/support/plugin/media-cleaner/reviews/?rate=5#new-post). Thank you so much! 💖

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

= 6.5.2 (2022/12/26) =
* Add: Support for Global Gallery, Lana Downloads Manager, Powerpress, Connections Business Directory, Wonderful Plugin 3D Carousel.
* Update: Better support for Avada (Fusion Builder).
* Update: Live content is not restricted to Posts and Pages anymore (it can be used for any custom post type).

= 6.5.1 (2022/12/09) =
* Update: Better support for WPBakery and Elementor.

= 6.5.0 (2022/11/12) =
* Add: In the Edit Media page, you can now see the post/page where your media are used.
* Fix: Avoid a few errors in some specific cases.

= 6.4.9 (2022/10/24) =
* Fix: Improved (and fixed) the accuracy of the explanation displayed before the scan.

= 6.4.7 (2022/10/10) =
* Add: Fixed a potential issue while scanning the widgets.

= 6.4.6 (2022/09/27) =
* Add: Option to skip trash.
* Update: Better and faster handling of options (big refactor).
* Update: Tiny UI enhancements to make this a bit better.
* Add: Better support for ACF Blocks.

= 6.4.5 (2022/09/10) =
* Add: Support for Ultimate Responsive Image Slider.
* Add: Support for Presto Player.

= 6.4.4 (2022/09/01) =
= Fix: Avoid duplicate issues, even if a file is analyzed twice for some reason.
* Add: Auto-retry in the UI.

= 6.4.2 (2022/08/03) =
* Fix: Tiny UI issues in Safari.
* Add: Select a range of items by using shift.

= 6.4.0 (2022/06/30) =
* Add: Support for retry when an error occurs.
* Fix: There was an issue with the resolution of some filepaths.
* Add: Support of the source tag.
* Fix: The links and thumbnails were broken in the Dashboard Trash.

= 6.3.7 (2022/06/07) =
* Fix: Better support for filters for filesystem scan (and a little optimization as well).
* Fix: Better support for backgrounds/overlays in elementor.

= 6.3.6 (2022/05/17) =
* Fix: Support for Elementor background images.
* Fix: There were a few warnings related to undeclared arrays.
* Add: Support for Metabox.

= 6.3.4 (2022/04/29) =
* Fix: Issue with Elementor.
* Fix: Issue with JetEngine.
* Add: Support for ACF Photo Gallery.

= 6.3.2 (2022/03/22) =
* Fix: Better support for WebP.

= 6.3.1 (2022/03/15) =
* Add: Do not rely on the Media Trash anymore (MEDIA_TRASH)!
* Fix: Better support for WPBakery.
* Add: Support for Google Web Stories.
* Info: There is no need to add MEDIA_TRASH anymore :) You can remove it from your wp-config.php if you added it in there. 

= 6.3.0 (2022/02/20) =
* Fix: When a Media entry is removed from Media Library, also remove it from the Issues in the Cleaner Dashboard.
* Update: Consider SVGs as images.
* Update: Support for WordPress 5.9.

= 6.2.7 (2021/12/11) =
* Add: Support for Jet Engine and its metaboxes and fields.
* Add: Support for CM Business Directory.
* Add: Support for Sunshine Photo Cart.
* Add: Support for Woodmart Theme.
* Add: Support for HTML in Product Descriptions.

= 6.2.6 (2021/11/15) =
* Add: jp_img_sitemap to the ignored post types.
* Add: Works even when original size image filename contains resolution.
* Fix: Support for roles.
* Add: Support for Download Monitor.

= 6.2.5 (2021/10/19) =
* Fix: Avoid JS issues with a certain version of React used by WP.
* Fix: Avoid some notices and warnings.
* Add: Support fort wysizyg ACF field.

= 6.2.4 (2021/09/23) =
* Add: Option to consider Attached Images as In Use.
* Update: Sanitized output for admin.
* Update: Admin 3.6.

= 6.2.2 (2021/09/16) =
* Add: Option to consider Attached Images as In Use.
* Update: Sanitized output for admin.

= 6.2.1 (2021/09/11) =
* Fix: Images Only now includes PNG, GIF, ICO and BMP on top of the JPGs.
* Add: Support for ACF Repeater with Array of Images ID.
* Add: Support for ACF Blocks.
* Add: Support for Jet Engine.
* Add: Support for Social Warfare.
* Add: Support for WP Job Manager.
* Add: Support for wpDiscuz.
* Fix: Better support for Salient Theme.
* Update: More powerful CLI.

= 6.2.0 (2021/08/28) =
* Fix: Little UI glitches fixed.

= 6.1.9 (2021/07/05) =
* Add: Updated UI and libraries.
* Add: Support for Simple 3D Carousel.
* Update: Better codebase that will allow new features and enhancements (like Pause, Retry, etc).

= 6.1.8 (2021/06/14) =
* Add: Support SVG.
* Add: Support for Elfsight Slider.
* Add: Support for Nimble Builder.

= 6.1.7 (2021/04/16) =
* Add: Support for Siteground cache.
* Add: Support for Audio block.
* Add: Support for WebDirectory.
* Add: Avoid WordPress to automatically empty its trash.
* Fix: Alternative for those who don't have the MB module.

= 6.1.6 (2021/02/23) =
* Add: Support for Smart Slider.

= 6.1.5 (2021/02/13) =
* Add: Support for video tags (also used by the Gutenberg block).
* Fix: WebP should be checked even though the content is not being checked.
* Fix: Avoid scan to stop if a warning is sent by the server when using Live Scan.

= 6.1.3 (2021/01/13) =
* Add: Title of the Media Entry in the Cleaner Dashboard.
* Add: Link to Image directly from Dashboard for a quick check.
* Fix: Scan without content check was still checking for webp or retina parent files.

= 6.1.2 =
* Fix: Fixed an issue with WPML (and potentially, Polylang as well).
* Fix: Updated to Common Admin 3.3.

= 6.1.1 =
* Fix: UI is even more dynamic.

= 6.1.0 =
* Fix: WP CLI wasn't working with the new role system.

= 6.0.9 =
* Fix: Improved support for WebP.
* Add: Avoid overring the roles used by the plugin.
* Fix: Support for WPML and WPML Media.
* Fix: Support for Advanced Ads.

= 6.0.8 =
* Fix: Compatibility with Litespeed.
* Update: Support for WP 4.8.

= 6.0.7 =
* Update: Avoid too many refreshes of the statistics and the options in the Dashboard.
* Update: Better logs for the licenser.
* Add: Support for Brizy 2.0.

= 6.0.6 =
* Update: Support for nonces.
* Fix: Support for WooCommerce Downloads.
* Fix: Some settings could not be changed when Filesystem was selected in Dashboard.
* Add: Support for Justified Image Grid.
* Add: Support for Custom Logo in themes.
* Add: Support for Background Images in Avada (Fusion Builder).
* Update: Added the sourcemaps for debugging purposes.

= 6.0.4 =
* Fix: Works even if the Permalinks are disabled.
* Fix: Create the DB as soon as it is required.

= 6.0.3 =
* Fix: Avoid a notice with WP 5.5 (wp_make_content_images_responsive is deprecated).
* Fix: Retrieve the correct path for the Rest API on every kind of install.

= 6.0.2 =
* Update: Better and fresh new UI. The way it works was simplified while keeping the same features and giving more room for new ones. This is the biggest update to Media Cleaner ever :)
* Update: Create the DB for Cleaner automatically if needed.

= 6.0.1 =
* Update: Brings back errors management to the bulk actions (skip, skip all).
* Update: Prompt before emptying the trash.

= 6.0.0 =
* Update: Better and fresh new UI. The way it works was simplified while keeping the same features and giving more room for new ones. This is the biggest update to Media Cleaner ever :)
* Add: Support for a few more plugins and themes.

= 5.6.4 =
* Fix: Support for GeoDirectory.
* Fix: Support for Modula Gallery.
* Add: Better support for Fusion Builder (Avada).
* Fix: Could not detect in the HTML absolute URLs starting with 'wp-content' directly.
* Fix: Divi was not using the common file types.

= 5.6.3 =
* Fix: There was an issue with the "Ignore" feature which was not working in some cases.
* Add: Filter to allow developers to override the decisions of the plugin.
* Add: Auto-add MEDIA_TRASH.
* Fix: Fuzzier pattern matching for wording variety.

= 5.6.2 =
* Add: Always Skip/Retry feature.
* Add: "Images Only" for Media Library scan.
* Add: Support for Salient theme.

= 5.6.1 =
* Add: You can now sort the results by size and path. Little present for the week-end ;)

= 5.5.8 =
* Add: Support for Image Map Pro.
* Add: Support for Directories.
* Update: Code cleaning and a bit of refactoring.
* Update: Cleaner references table with null values when needed.
* Fix: Check if the filename exists in the trash for every new upload (and if yes, give it a different filename). 
* Fix: Avoid crash related to unserialization.
* Fix: Ignore some other plugins' files which are not supposed to be scanned.

= 5.5.7 =
* Update: UI improved in many ways, I hope you will love it more!
* Add: Filter by issue, and allow to delete those specific issues.
* Add: Support for the original image (-scaled) feature added in a recent version of WP.
* Add: Support for Custom Product Tabs.
* Add: Support for Support for FAT Portfolio.
* Update: Better support for translations.
* Update: Better support for Revolution Slider.
* Update: Added additional checks for DOM parser and check if the DOM module is loaded.
* Fix: 100% of the code was checked and a few tiny issues were fixed here and there.

= 5.5.4 =
* Update: Creates the DB tables automatically.
* Add: Support for Revolution Slider.
* Add: Support for WP Residence.
* Add: Support for Avia Framework.

= 5.5.3 =
* Add: Check the IDs of the standard galleries.
* Add: Support for the ACF groups.
* Add: Support for the ACF fields for taxonomies.

= 5.5.2 =
* Update: Better support for WPBakery.
* Fix: Issue with the URLs pointing at the plugin's tutorial page.
* Fix: Avoid the scan to be halted by error logging.
* Add: Basic support for WCFM MarketPlace.

= 5.5.1 =
* Update: Admin refreshed to 2.4.
* Fix: Support for ACF Aspect Ratio Crop, Tasty Pins, and more extensions.

= 5.4.9 =
* Fix: ACF File field wasn't being detected properly in some cases.
* Fix: Support for WPBakery Masonry Grid and probably for many more cases than just this one.
* Add: Ask for confirmation before deleting all the files at once.

= 5.4.8 =
* Fix: Widgets were not scanned.
* Add: Support for Divi modules.

= 5.4.6 =
* Add: Option to disable the analysis of shortcodes.

= 5.4.4 =
* Add: Support for Brizy Builder.
* Fix: Doesn't trigger the timeout check if WP-CLI is being used.
* Add: WP-CLI can now delete and trash media entries and files.

= 5.4.3 =
* Add: Support for Yoast SEO and its Facebook Image.
* Add: Support for Elementor and Oxygen Builder.
* Add: Support for ACF File Field.
* Update: Better support for WP CLI.
* Fix: Make sure the HTML is UTF8 encoded before analyzing it.
* Update: Removed affiliate links to BlogVault in the Readme as it seems to be against the WordPress guidelines.

= 5.4.0 =
* Add: Support for Uber, Easy Real Estate.
* Update: Admin CSS and texts.
* Fix: A rare but wrong call to the log() function was causing the plugin to fail.
* Update: Clean the options. Now, the Content option replaces Posts/Meta/Widgets (they were useless in a way).
* Add: Support for WP-CLI (have a look at the how-it-works.txt) in the Pro. Now, scanning can be 100x times faster.
* Add: Option Live Site in the Pro.

= 5.2.4 =
* Add: Lot of refactoring and optimizations.
* Add: Support for Theme X, ZipList, and better support for standard websites as well.
* Add: Yes/No dialog for Reset button.

= 5.2.3 =
* Add: Support for Recent Blog Posts.
* Add: Additional support for images used by the theme.

= 5.2.1 =
* Add: Support for My Calendar (thanks to Mike Meinz).
* Add: Support for iFrames (thanks to Mike Meinz).
* Update: Code cleaning, reorganization and optimization.

= 5.2.0 =
* Update: Many optimizations, modules and big sections of the code are now only loaded when really needed.
* Fix: Filenames with spaces weren't detected correctly and other.
* Fix: Make sure that the shortcodes are resolved.
* Add: Compatibility with more plugins (ACF Widgets, Attachments, Metaslider).

= 5.1.3 =
* Add: Support for WebP.
* Update: Avoid removing tables when plugin is only disabled.
* Fix: For some, the tables couldn't be reset.

= 5.1.2 =
* Update: Admin style update and common framework updated.
* Update: Compatibility with WordPress 5.1.

= 5.1.0 =
* Add: Filters for Filesystem scan. Please have a look at the tutorial (https://meowapps.com/media-cleaner/tutorial/), there is now a section about those filters.
* Fix: Query for metakey.
* Fix: Thumbnails matching.
* Update: Compatibility for WordPress 5 and Gutenberg.

= 5.0.1 =
* Update: Slight code cleaning.
* Update: Checkboxes are updated dynamically.

= 4.8.4 =
* Fix: Issue with ACF Repeater.
* Fix: Trash and Ignore features resulted in a weird behavior when used together.
* Add: Now can delete the results of a search.
* Update: Many UI improvements.

= 4.8.0 =
* Update: Many parts of the UI were rewritten for a better experience. Buttons have a nicer logic.
* Add: Enhanced error control. From now, when an error occurs during the scan, a popup will appear (asking to try again, or to skip the current item), and errors will be logged to the console.

= 4.6.3 =
* Add: Added an option to only scan the thumbnails and ignore the base files.
* Add: ACF Repeater support.
* Update: Improved the code and the performance. Scan is now done differently, using the DB.
* Fix: Debug logs weren't logging (and enhanced them a bit).

= 4.5.5 =
* Fix: Doesn't remove the Media entry if the files cannot be deleted.
* Update: Displays a warning if the log file cannot be created.

= 4.5.4 =
* Update: Streamlined the plugin, tutorial has also been rewritten.
* Update: Simplified the Settings. Removed the Gallery option, as it is part of the Posts or Post Meta.
* Update: Support for UTF8, Background CSS, and Shortcodes have been moved to the Free version, and are now always enabled. Easier for everyone.
* Add: Extra support for Page Builders is being added into the Pro version.

= 4.5.0 =
* Add: Support for WooCommerce Short Description.
* Add: Support for Divi Background.
* Add: Support for Custom Fields Pro (ACF gallery).
* Fix: Better support for CSS background.
* Fix: Avoid detected file to be re-added if already there.
* Update: Removed UTF-8 option (became useless).

= 4.4.7 =
* Fix: Divi Single Image wasn't always properly detected.
* Add: Option for CSS background.
* Update: Code cleaning, slighlty faster now.
* Info: This plugin is hard work, don't hesitate to review it :) Thank you.

= 4.4.6 =
* Update: Support for ACF (Image Field as Object, URL and ID).
* Info: This plugin is hard work, don't hesitate to review it :) Thank you.

= 4.4.4 =
* Update: Check DIVI Galleries and Single Images in Beaver Builder.
* Update: Support for files which aren't images and links (href's).

= 4.4.2 =
* Fix: Too many files were detected as used if WooCommerce was installed.

= 4.4.0 =
* Update: Meta Data analysis is now cached, so much faster.
* Update: URL detections became a bit more safer.
* Update: Detect the images used by the themes more than before.
* Fix: Images in widgets weren't detected in many cases.

= 4.2.5 =
* Update: Support for WP 4.9.
* Fix: Could not empty trash if Media was already removed.

= 4.2.3 =
* Fix: Meta search issue.
* Fix: SQL typo for WooCommerce detection.
* Fix: Avoid checking the empty arrays.

= 4.2.0 =
* Info: This is a MAJOR UPDATE both in term of optimization and detection.
* Add: Support for Fusion Builder (Avada).
* Add: Cache the results found in posts to analyze them much faster later.
* Add: Debugging log file (option).

= 4.1.0 =
* Add: Support for WooCommerce Gallery.
* Add: Support for Visual Composer (Single Image and Gallery).

= 4.0.7 =
* Update: Bulk analyze/prepare galleries, avoid the first request to time out.
* Add: Many option to make the processing faster or slower depending on the server.
* Fix: Handle server timeout.
* Add: Pause button and Retry button.

= 4.0.4 =
* Update: Safest default values.

= 4.0.2 =
* Add: Information about how a certain media is used (Edit Media screen).
* Fix: Check / Create DB process.
* Fix: Plugin was not working well with themes using Background/Header.
* Update: A bit of cleaning.

= 4.0.0 =
* Update: Core was re-organized and cleaned. Ready for nice updates.

= 3.7.0 =
* Fix: Little issue when inserting the serial key for the first time.
* Update: Compliance with the WordPress.org rules, new licensing system.
* Update: Moved assets.
* Info: There will be an important warning showing up during this update. It is an important annoucement.

= 3.6.4 =
* Fix: Plugin was not working properly with broken Media metadata. It now handles it properly.

= 3.6.2 =
* Fix: When over 1 GO, was displaying a lower size value.
* Fix: Counting wasn't exact with a Filesystem scan.
* Info: Please read the previous changelog as it didn't appear in WP for some reason.
* Add: Check Posts also look for the Media ID in the classes (more secure).

= 3.6.0 =
* Add: Now the Media can be recovered! You can remove your Media through the plugin, make sure they are not in use (by testing your website thoroughly) and later delete them definitely from the trash. I think you will find it awesome.
* Update: Nicer internal icons rather than the old images for the UI.
* Update: Faster and safer for post_content checks.
* Update: This is a big one. The plugin is more clear about what it does. You need to choose either to scan the Media or the Filesystem, and also against what exactly. There has also been a few fixes and it will work on more big installs. If it fails, you can remove a few scanning options, and I will continue to work on making it perfect to support huge installs with all the options on.

= 3.2.8 =
* Update: Show a better edit media screen.
* Update: Will show the same number of items as in the Media Library (before it was fixed to 15 items per page).
* Fix: Was displaying warning if the number of items per page in the Media page is not set.

= 3.2.0 =
* Fix: HTML adapted to WP 4.5.1.
* Fix: Doesn't break if there is an error on the server-side. Display an alert and continue.
* Update: Can select more than one file for non-Pro.
* Fix: Issue with PHP 7.

= 3.0.0 =
* Add: Option for resolving shortcode during analysis.
* Update: French translation. Big thanks to Guillaume (and also for all his testing!).
* Info: New name, fresh start. This plugin changed completely since it very first release :)

= 2.5.0 =
* Add: Delete the unused directories.
* Add: Doesn't break when there are too many files in the system.
* Add: Pro version with better support.
* Update: Improved detection of unused files.
* Fix: UTF8 filenames skipped by default but can be scanned through an option.
* Fix: Really many fixes :)
* Info: Contact me if you have been using the plugin for a long time and love it.

= 2.4.2 =
* Add: Inclusion of gallery post format images.
* Fix: Better gallery URL matching.

= 2.4.0 =
* Fix: Cross site scripting vulnerability fixes.
* Info: Please perform a "Reset" in the plugin dashboard after installing this new version.

= 2.2.6 =
* Fix: Scan for multisite.
* Change: options are now all enabled by default.
* Fix: DB issue avoided trashed files from being deleted permanently.

= 2.0.2 =
* Works with WP 4.
* Gallery support.
* Fix: IGNORE function was... ignored by the scanning process.

= 1.9.0 =
* Add: thumbnails.
* Add: IGNORE function.
* Change: cosmetic changes.
* Add: now detects the custom header and custom background.
* Change: the CSS was updated to fit the new Admin theme.

= 1.7.0 =
* Change: the MEDIA files are now going to the trash but the MEDIA reference in the DB is still removed permanently.
* Stable release.
* Change: Readme.txt.

= 1.4.0 =
* Add: check the meta properties.
* Add: check the 'featured image' properties.
* Fix: keep the trash information when a new scan is started.
* Fix: remove the DB on uninstall, not on desactivate.

= 1.2.2 =
* Add: progress %.
* Fix: issues with apostrophes in filenames.
* Change: UI cleaning.

= 1.2.0 =
* Add: options (scan files / scan media).
* Fix: mkdir issues.
* Change: operations are buffered by 5 (faster).

= 0.1.0 =
* First release.
