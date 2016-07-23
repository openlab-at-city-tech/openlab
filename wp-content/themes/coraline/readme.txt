== Changelog ==

= 1.5.1 - June 19 2015 =
* Escaped all necessary inputs, URLs, etc.

= 1.5 - November 27, 2014 =
* Add support for the Eventbrite API plugin.
* Numerous bug fixes.
* Improved i18n text strings.

= 1.4.1 - May 29 2013 =
* Resolved merge conflict.

= 1.4 - May 29 2013 =
* Made adjustments to post format media sizing for 3.6 compatibility.
* Updated license.
* Minor style adjustments in preparation for 3.6 compatability.
* Addws flexible custom header support and flexible-header tag.
* Allows the get_posts() results in loop.php to be cached.
* Uses get_posts() in loop.php instead of get_children() to get image attachments.
* Enqueues scripts and styles via callback.
* Use a filter to modify the output of wp_title().
* Edited editor-style.css to use body selector instead of *, which prevented inline styles from being properly displayed in the visual editor.
* Ensures title is only displayed if it exists on single view.
* Tweaked to red color scheme post format color link.
* Changed color of post format link so it works with all color schemes.
* Ensured Aside and Gallery post formats, which got special treatment in Coraline, display the proper post format heading.
* Ensured the "Standard" post format does not get Aside, Image, Video, Quote, or Link formatting if get_post_format() happens to be set to Standard.
* Added a new section to the loop for formatted posts to support more post formats.
* Move entry meta in single view if viewing a formatted post.
* Added link to post format types, including pre-existing Aside and Gallery post formats.
* Updated comments in Jetpack compat files to point to live documentation on jetpack.me.

= 1.3 - Nov 5 2012 =
* Updated screenshot for HiDPI support.
* Fix @package and @subpackage information.
* Use correct action hook to load theme options CSS.
* Add color scheme value to body_class output.
* Styles: 'container' needs to contain floats; better menu selectors; fix incorrect background image reference in dark.css; fix overly general .attachment img selectors.
* Fix gettext functions that passed variables.
* Add styling for HTML5 email inputs.
* Make sure attribute escaping occurs after printing.
* PNG and JPG image compression.
* Remove loading of $locale.php.
* Add Jetpack compatibility file.
* Use a named image size to display queried image in image.php template.
* Improve gallery styles so they work better multiple column sizes.

= 1.2 - Jan 6 2012 =
* Move styles to wp_enqueue_scripts hook.
* Fix PHP deprecated notices.
* Add is_multi_author() support.
* Add new color schemes: pink, blue, purple, red, and brown color scheme.
* Add full-width layout as a layout option in the theme options page.
* Enable max-width CSS rule on container to allow the layout elements to align properly in full-width layout.
* Add generic action-hooks to header and sidebar.

= 1.1 - Oct 5 2011 =
* Fix translatable attribute values that were not using esc_attr_e().
* Fix category highlighting in the menu.
* Set svn:eol-style on TXT files.
* Fix get_the_author() escaping.
* Set wider content_width for full-width template and image template.
* Hide RTL byline instead of off-screen positioning to prevent horizontal scrollbars.
* Tag updates for style.css.
* Make sure wp_print_styles does not run for wp-admin.
* Avoid running update_option on init from front-end.
* Add sanitize_title to category lookup to avoid clash with special characters.
* Show category chooser in Theme Options to sites that set a category in the past, but remove for newer blogs.
* Enable post formats for asides and galleries.
* Misc layout and markup fixes; trim trailing whitespace.
