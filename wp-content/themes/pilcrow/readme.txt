=== Pilcrow ===

Pilcrow's 6 different layouts, with multiple sidebar configurations, four default color schemes, custom header images (using featured images in posts and pages), and a customizable background, make personalizing your blog a snap.

== Changelog ==

= 1.5.1 - Jul 11 2013 =
* Sets post thumbnail size after adding theme support.
* Removed flex-height option.

= 1.5 - Jul 08 2013 =
* Moved away from using deprecated functions and improve compliance with .org theme review guidelines.
* Removed RSS Links Widget support as it's not available in core.
* Made adjustments to post format media sizing for 3.6 compatibility.
* Updated license.
* Enqueues scripts and styles via callback.
* Added post-formats tag to style.css.
* Uses a filter to modify the output of wp_title().
* Added support for post formats that match our guidelines.
* Added post format name/link to formats archive above each post.
* Added appropriate styling/style tweaks for post formats and post format labels where necessary.
* Updated comments in Jetpack compat files to point to live documentation on jetpack.me.

= 1.4 - Nov 05 2012 =
* Updated screenshot for HiDPI support.
* Fix @package and @subpackage information.
* Add styling for HTML5 email inputs.
* Remove loading of $locale.php.
* Add Jetpack compatibility file.
* PNG image compression.
* Use correct action hook to load theme options CSS.
* Make sure attribute escaping occurs after printing.
* Fix issue with gallery image captions being off-center.
* Show the full post_content in archive templates.

= 1.3 - Jan 06 2012 =
* Move styles to wp_enqueue_scripts hook.
* Enable is_multi_author() support.
* Remove trailing spaces in CSS files.
* Add generic action-hooks to header and sidebar.
* Use correct action hook to load theme options CSS.
* Move theme options files to /inc.

= 1.2 - Oct 05 2011 =
* Set svn:eol-style on JS and TXT files.
* Clean up calendar widget styles.
* the_post should always be called in the loop.
* TEMPLATEPATH to get_template_directory().
* Fix missing text domains.

= 1.1 - Aug 01 2011 =
* Better handling for color scheme.
* Avoid running update_option on init from front-end.
* Miscellaneous layout and style fixes.
* Add additional screenshot and remove unused image.
* Correct typo preventing single-column pages from being full-width.
* Updated print styles for Firefox.
* Add languages folder with POT.

= 1.0 - Dec 06 2010 =
* Original upload.