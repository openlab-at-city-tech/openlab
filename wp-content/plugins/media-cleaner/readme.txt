=== Media Cleaner ===
Contributors: TigrouMeow
Tags: clean, delete, file, files, images, image, media, library, upload, acf, gutenberg
Donate link: https://commerce.coinbase.com/checkout/d047546a-77a8-41c8-9ea9-4a950f61832f
Requires at least: 4.8
Tested up to: 5.3
Requires PHP: 7.0
Stable tag: 5.6.3

Clean your WordPress from unused or broken media and files. It has its own trash system and recovery features. Please read the description.

== Description ==

Media Cleaner cleans your Media Library from the media entries (and files) which aren't used in your website, as well as broken entries. An internal trash allows you to make sure everything works properly before deleting the media entries (and files) permanently. It uses a smart analysis powered by many implementations for specific plugins and themes.

**Before using this plugin, make sure you have a proper backup of your install. This step is not optional; it is required. You cannot trust any tools modifying or deleting your files.**

For compatibility, Pro version and important information, please read this until the end. Last but not least, it is necessary to read the [tutorial](https://meowapps.com/media-cleaner-tutorial/). It is available on the official website, here: [Media Cleaner](https://meowapps.com/plugin/media-cleaner/). 

=== VERY IMPORTANT ===

This tool is a knife. Do not use it if you don't have any backup, or if you don't understand what it does. This plugin does its best to help you. Learn how to use it and you will get awesome results. 

=== DASHBOARD ===

The files detected as not used will be listed in a specific dashboard. It will be up to you to delete them. Then, they will be moved to a trash internal to the plugin. After more testing, you can retrieve those files from the trash, or you can delete them permanently.

=== COMPATIBILITY ===

It works with any kind of media entries, including their retina and/or WebP versions. It is tested on modern WordPress installs (Gutenberg included) as well as older ones, with various themes and by a community of thousands of users. It does support **WooCommerce**. As it requires meticulous a lot of work and testing, **the Pro version might be required if you are using complex plugins to handle the content of your website**. I am constantly increasing the compatibility.

=== PRO VERSION ===

The Pro version adds Filesystem Analysis, extra support for complex plugins, Live Site option and WP-CLI support. 

With the Filesystem Analysis, [Media Cleaner Pro](https://meowapps.com/plugin/media-cleaner/) scans your physical /uploads directory, and match it against the Media Library. It also has extra support for complex plugins, such as:

- ACF (+ ACF Widgets)
- Divi Builder
- Fusion Builder (Avada)
- WPBakery Page Builder
- Visual Composer
- Elementor
- Beaver Builder
- Brizy Builder
- Oxygen Builder
- Slider Revolution
- Avia Framework
- And more (Theme X, ZipList Recipe, WP Residence, WCFM MarketPlace, UberMenu...)

The Live Site option will analyze the online version of your website, which might enhance the accuracy of the Cleaner in edge-cases.

Last but not least, [Media Cleaner Pro](https://meowapps.com/plugin/media-cleaner/) has support for WP-CLI. If you have direct access (SSH) to your server, you will be able to run the plugin at a much higher speed or have it ran automatically, if you like.

=== AGAIN, BE CAREFUL ===

Better to be safe than sorry. This plugin deletes files! Therefore, backup is not only important, it is **necessary**. Don't use this plugin if you are not ready. I can't help you if you damaged your install.

=== SPECIAL THANKS ===

- Mike Meinz, an amazing developer from the US who made a thorough debugging of the whole process, load lot of corrections and optimizations, and added support for more plugins.
- Satoshi Soma, an excellent Japanese developer who helped me when I needed it the most.

== Installation ==

1. Upload `media-file-cleaner-pro` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go in the Settings -> Media Cleaner and check the appropriate options
3. Go in Media -> Media Cleaner

== Screenshots ==

1. Media -> Media Cleaner

== Changelog ==

= 5.6.3 =
* Fix: There was an issue with the "Ignore" feature which was not working in some cases.
* Add: Filter to allow developers to override the decisions of the plugin.
* Add: Auto-add MEDIA_TRASH.
* Fix: Fuzzier pattern matching for wording variety.
* Info: This plugin is a lot of work. Please help me by giving it a nice review, [here](https://wordpress.org/support/plugin/media-cleaner/reviews/?rate=5#new-post).

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
* Add: Filters for Filesystem scan. Please have a look at the tutorial (https://meowapps.com/media-cleaner-tutorial/), there is now a section about those filters.
* Fix: Query for metakey.
* Fix: Thumbnails matching.
* Update: Compatibility for WordPress 5 and Gutenberg.

= 5.0.1 =
* Update: Slight code cleaning.
* Update: Checkboxes are updated dynamically.
* Info: Media Cleaner is better than ever by going through so many improvements and optimizations this year. The plugin has also been perfectly stable for a few weeks, so I have decided to change its version number. Please help this plugin survive by giving me a nice review, here: https://wordpress.org/support/plugin/meow-lightbox/reviews/?rate=5#new-post. Thank you :)

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
* Info: This is a MAJOR UPDATE both in term of optimization and detection. Keep my motivation up and give a good review to the plugin here: https://wordpress.org/support/plugin/media-cleaner/reviews/?rate=5#new-post. That helps me a lot.
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
* Info: Thanks to syntax53 for those improvements via GitHub (https://github.com/tigroumeow/media-file-cleaner/pull/3). Please review Media Cleaner if you like it. The plugin needs reviews to live. Thank you :) (https://wordpress.org/support/view/plugin-reviews/media-file-cleaner)

= 2.4.0 =
* Fix: Cross site scripting vulnerability fixes.
* Change: Many enhancements and fixes made by Matt (http://www.twistedtek.net/). Please thanks him :)
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
