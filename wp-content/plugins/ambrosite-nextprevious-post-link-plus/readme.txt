=== Ambrosite Next/Previous Post Link Plus ===
Contributors: ambrosite
Donate link: http://www.ambrosite.com/plugins
Tags: adjacent, next, previous, post, link, links, sort, sorted, sortable, order, reordered, thumbnail, thumbnails, truncate, loop, format, author
Requires at least: 2.5
Tested up to: 3.3
Stable tag: trunk

Upgrades the next/previous post link functions to reorder or loop adjacent post navigation links, display post thumbnails, and customize link format.

== Description ==

**IMPORTANT: Make sure you are using the right plugin.**

* **Next/Previous Post Link Plus** is intended for use in **single post** templates.
* **Next/Previous Page Link Plus** is intended for use in **page** templates.

The two plugins have similar sounding, but different, function names. If you mistakenly install the wrong plugin, you will get a "call to undefined function" error. If you want to create next/previous links for your pages, please check out:
http://wordpress.org/extend/plugins/ambrosite-nextprevious-page-link-plus/

**IMPORTANT: This plugin is not compatible with PHP 4.** If you try to install it on a host running PHP 4, you will get a parse error. WordPress has officially ended support for PHP 4 as of version 3.2, so you should upgrade to PHP 5.2 now. For those who cannot upgrade, you can download the alternate PHP 4 compatible version of the plugin. The only difference with the PHP 4 version is that the *%category* variable will not work with custom taxonomies.
http://www.ambrosite.com/download/ambrosite-nextprevious-post-link-plus.php4.zip

This plugin creates two new template tags -- **next_post_link_plus** and **previous_post_link_plus** -- which are upgraded versions of the core WordPress next_post_link and previous_post_link template tags. The new tags include all of the functionality of the core tags, plus the following additional options:

* Sort the next/previous post links on columns other than post_date (e.g. alphabetically).
* Sort next/previous links on custom fields (both string and integer sorts are supported).
* Full WordPress 3.3 compatibility, including support for custom post types, custom taxonomies, and post formats.
* Loop around to the first post if there is no next post (and vice versa).
* Retrieve the first/last post, rather than the previous/next post (for First|Previous|Next|Last navigation links).
* Display post thumbnails alongside the links (WordPress 2.9 or higher).
* Truncate the link titles to any length, and display custom text in the tooltip.
* Display the title, date, author, category, and meta value of the next/previous links.
* Specify a custom date format for the %date variable.
* Restrict next/previous links to same category, taxonomy, format, author, custom field value, custom post ID list, or custom category list.
* Exclude categories, custom taxonomies, post formats, or individual post IDs.
* Three category exclusion methods for greater control over the navigation stream.
* Return multiple next/previous links (e.g. the next N links, in an HTML list).
* Return the ID, title, date, href attribute, or post object of the next/previous links, instead of echoing them to the screen.
* Return false if no next/previous link is found, so themes may conditionally display alternate text.
* Works with Post Types Order and other popular post reordering plugins.

Extensive documentation on configuring the plugin may be found here:
http://www.ambrosite.com/plugins/next-previous-post-link-plus-for-wordpress

== Installation ==

* Upload ambrosite-post-link-plus.php to the /wp-content/plugins/ directory.
* Activate the plugin through the Plugins menu in WordPress.
* Edit your template files, and replace the next_post_link and previous_post_link template tags with next_post_link_plus and previous_post_link_plus. Configure them using parameters as explained in the online documentation:
http://www.ambrosite.com/plugins/next-previous-post-link-plus-for-wordpress

== Frequently Asked Questions ==

* How exactly do I install this plugin? Which file needs to be edited, and where do I put the code?
* I am getting a parse error while attempting to install the plugin. Why?
* I am getting a "call to undefined function" error. Why?
* How can I get rid of the arrows on my next/previous links?
* I am confused about the difference between strong, differential, and weak exclusion. Which one should I use?
* I am using a custom field with a simple integer value to order my posts, but they're not sorting correctly. Why?
* I am seeing the number '1' printed next to my links. Why?
* What about compatibility with the Post Types Order plugin?
* What about compatibility with the WPML plugin?
* What about compatibility with the qTranslate plugin?
* Is there any way to use an image instead of link text?

Answers to these questions may be found here:
http://www.ambrosite.com/plugins/next-previous-post-link-plus-for-wordpress#faq

== Changelog ==

= 2.4 =
* Added 'in_cats' parameter.
* Added 'in_same_meta' parameter.
* Added 'return' parameter to specify what should be returned from the function.
* Added 'date_format' parameter for customizing the %date variable.
* Added %title variable to 'format' parameter.
* Added option to sort on custom fields as integers rather than strings.
* Primary sort column defaults to 'menu_order' if Post Types Order plugin is installed.
* Many documentation updates to address frequently asked questions.

= 2.3 =
* Added 'in_same_author' parameter.
* Added 'in_posts' parameter.
* Added 'post_type' parameter.
* Added option to specify a custom taxonomy with 'in_same_tax'.
* Added new format variable (%author).
* Added variables (%title, %date, %author) to custom tooltip text.
* Added option to suppress the tooltip completely.
* Renamed filters for compatibility with Post Types Order and other plugins that attempt to filter the output of get_adjacent_post.

= 2.2 =
* Added option to retrieve the first/last post links.
* Added support for post formats.
* Improved support for custom taxonomies.
* Added 'tooltip' parameter to specify custom tooltip text.
* Added %meta variable to the format parameter.
* Ground-up rewrite of the 'ex_cats' functionality, with three exclusion methods for greater control over the navigation stream.
* Added 'ex_posts' parameter to exclude individual post IDs.
* Added option to return the next/previous links as a PHP string.

= 2.1 =
* Added 'order_2nd' parameter for specifying a secondary sort column.
* Updated the documentation to address PHP 4 incompatibility problems.

= 2.0 =
* Full WordPress 3.0 compatibility, including custom post types.
* Rewrote the plugin using wp_parse_args to simplify the function calls.
* Added option to sort the next/previous links on custom fields.
* Added option to return multiple next/previous links.
* Fix for custom taxonomies.
* Added %category variable to the format parameter.
* Added 'before' and 'after' parameters.
* Added support for post thumbnail sizes.
* Added 'echo' parameter.

= 1.1 =
* Added truncate link title and loop options.
* Added support for post thumbnails.

= 1.0 =
* Initial version.
