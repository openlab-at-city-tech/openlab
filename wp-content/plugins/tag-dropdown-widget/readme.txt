=== Taxonomy Dropdown Widget ===
Contributors: ethitter
Donate link: https://ethitter.com/donate/
Tags: tag, tags, taxonomy, sidebar, widget, widgets, dropdown, drop down
Requires at least: 2.8
Tested up to: 6.0
Stable tag: 2.3.3
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a dropdown list of non-hierarchical taxonomies as an alternative to the term (tag) cloud. Formerly known as Tag Dropdown Widget.

== Description ==

Creates dropdown lists of non-hierarchical taxonomies (such as `post tags`) as an alternative to term (tag) clouds. Multiple widgets can be used, each with its own set of options.

Numerous formatting options are provided, including maximum numbers of terms, term order, truncating of term names, and more.

Using the `taxonomy_dropdown_widget()` function, users can generate dropdowns for use outside of the included widget.

**Only use version 2.2 or higher with WordPress 4.2 and later releases.** WordPress 4.2 changed how taxonomy information is stored in the database, which directly impacts this plugin's include/exclude term functionality.

This plugin was formerly known as the `Tag Dropdown Widget`. It was completely rewritten for version 2.0.

**Follow and contribute to development on GitHub at https://github.com/ethitter/Taxonomy-Dropdown-Widget.**

== Installation ==

1. Upload taxonomy-dropdown-widget.php to /wp-content/plugins/.
2. Activate plugin through the WordPress Plugins menu.
3. Activate widget from the Appearance > Widgets menu in WordPress.
4. Set display options from the widget's administration panel.

== Frequently Asked Questions ==

= What happened to the Tag Dropdown Widget plugin? =

Since I first released this plugin in November 2009, WordPress introduced custom taxonomies and, as more-fully discussed below, saw a new widgets API overtake its predecessor. As part of the widgets-API-related rewrite, I expanded the plugin to support non-hierarchical custom taxonomies, which necessitated a new name for the plugin.

= Why did you rewrite the plugin? =

When I first wrote the Tag Dropdown Widget plugin (and it's sister Tag List Widget), WordPress was amidst a change in how widgets were managed. I decided to utilize the old widget methods to ensure the greatest compatibility at the time. In the nearly two years since I released version 1.0, the new widget system has been widely adopted, putting this plugin at a disadvantage. So, I rewrote the plugin to use the new widget API and added support for non-hierarchical taxonomies other than just post tags.

= I upgraded to version 2.0 and all of my widgets disappeared. What happened? =

As discussed above, WordPress' widget system has changed drastically since I first released this plugin. To facilitate multiple uses of the same widget while allowing each to maintain its own set of options, the manner for storing widget options changed. As a result, there is no practical way to transition a widget's options from version 1.7 to 2.0.

= If my theme does not support widgets, or I would like to include the dropdown outside of the sidebar, can I still use the plugin? =

Insert the function `<?php if( function_exists( 'taxonomy_dropdown_widget' ) ) echo taxonomy_dropdown_widget( $args, $id ); ?>` where the dropdown should appear, specifying `$args` as an array of arguments and, optionally, `$id` as a string uniquely identifying this dropdown.

* taxonomy - slug of taxonomy for dropdown. Defaults to `post_tag`.
* select_name - name of first (default) option in the dropdown. Defaults to `Select Tag`.
* max_name_length - integer representing maximum length of term name to display. Set to `0` to show full names. Defaults to `0`.
* cutoff - string indicating that a term name has been cutoff based on the `max_name_length` setting. Defaults to an ellipsis (`&hellip;`).
* limit - integer specifying maximum number of terms to retrieve. Set to `0` for no limit. Defaults to `0`.
* orderby - either `name` to order by term name or `count` to order by the number of posts associated with the given term. Defaults to `name`.
* order - either `ASC` for ascending order or `DESC` for descending order. Defaults to `ASC`.
* threshold - integer specifying the minimum number of posts to which a term must be assigned to be included in the dropdown. Set to `0` for now threshold. Defaults to `0`.
* incexc - `include` or `exclude` to either include or exclude the terms whose IDs are included in `incexc_ids`. By default, this restriction is not enabled.
* incexc_ids - comma-separated list of term IDs to either include or exclude based on the `incexc` setting.
* hide_empty - set to `false` to include in the dropdown any terms that haven't been assigned to any objects (i.e. unused tags). Defaults to `true`.
* post_counts - set to `true` to include post counts after term names. Defaults to `false`.

= Why are the makeTagDropdown(), TDW_direct(), and generateTagDropdown() functions deprecated? =

Version 2.0 represents a complete rewrite of the original Tag Dropdown Widget plugin. As part of the rewrite, all prior functions for generating tag dropdowns were deprecated, or marked as obsolete, because they are unable to access the full complement of features introduced in version 2.0. While the functions still exist, their capabilities are extremely limited and they should now be replaced with `taxonomy_dropdown_widget()`.

= Where do I obtain a term's ID for use with the inclusion or exclusion options? =

Term IDs can be obtained in a variety of ways. The easiest is to visit the taxonomy term editor (Post Tags, found under Posts, for example) and, while hovering over the term's name, looking at your browser's status bar. At the very end of the address shown in the status bar, the term ID will follow the text "tag_ID."

You can also obtain the term ID by clicking the edit link below any term's name in the Post Tags page. Next, look at your browser's address bar. At the very end of the address, the term ID will follow the text "tag_ID."

= I'd like more control over the tags shown in the dropdown. Is this possible? =

This plugin relies on WordPress' `get_terms()` function (http://codex.wordpress.org/Function_Reference/get_terms). To modify the arguments passed to this function, use the `taxonomy_dropdown_widget_options` filter to specify any of the arguments discussed in the Codex page for `get_terms()`.

To make targeting a specific filter reference possible should you use multiple instances of the dropdown (multiple widgets, use of the `taxonomy_dropdown_widget()` function, or some combination thereof), the filter provides a second argument, `$id`, that is either the numeric ID of the widget's instance or the string provided as the second argument to `taxonomy_dropdown_widget()`.

== Changelog ==

= 2.3.3 =
* Correct hook used to load plugin textdomain.

= 2.3.2 =
* Ready plugin for translation.

= 2.3.1 =
* PHP 7.3 compatibility

= 2.3 =
* Update for WordPress 4.3 by removing PHP4-style widget constructor usage (https://make.wordpress.org/core/2015/07/02/deprecating-php4-style-constructors-in-wordpress-4-3/).

= 2.2 =
* Update for WordPress 4.2 to handle term splitting in the plugin's include/exclude functionality. Details at https://make.wordpress.org/core/2015/02/16/taxonomy-term-splitting-in-4-2-a-developer-guide/.

= 2.1 =
* Introduce filters on dropdown and its components for greater customizability.
* Implement plugin as a singleton for proper reusability.
* Improve adherence to WordPress coding standards.
* Improve translatability of plugin.
* Generally clean up code for better readability and clarity.
* Eliminates all uses of `extract()` for clarity's sake.

= 2.0.3 =
* Correct problem in WordPress 3.3 and higher that resulted in an empty taxonomy dropdown.
* Remove all uses of PHP short tags.

= 2.0.2 =
* Allow empty title in widget options. If empty, the `taxonomy_dropdown_widget_title` filter isn't run.

= 2.0.1 =
* Fix fatal error in older WordPress versions resulting from PHP4 and PHP5 constructors existing in widget class.

= 2.0.0.2 =
* Fix bug in post count threshold that resulted in no terms being listed.

= 2.0.0.1 =
* Fix bug that appended cutoff indicators when unnecessary.

= 2.0 =
* Completely rewritten plugin to use WordPress' newer Widgets API.
* Drop support for WordPress 2.7 and earlier.
* Add support for all public, non-hierarchical custom taxonomies, in addition to Post Tags.
* Introduce new, more flexible function for manually generating dropdown menus.
* Introduce options requested by the community, such as control over the default dropdown item.
* Fixed persistent bugs in the include/exclude functionality.
* Widget admin is translation-ready.

= 1.7 =
* Replaced `TDW_direct()` and `makeTagDropdown()` with `generateTagDropdown()`.
* Recoded entire plugin to simplify and clean up overall functionality.
* Switched exclude functionality to use tag ids rather than tag slugs.
* Added numerous additional options to the widget panel based on user response, as detailed below.
* Added the ability to specify the indicator shown when a tag name is trimmed.
* Added the ability to limit the number of tags shown.
* Added the ability to specify the minimum number of posts a given tag must be associated with before it will show in the dropdown.
* Added options for specifying the order tags are displayed in.
* Added the ability to specify a list of tags to include in the dropdown, expanding on the existing ability to exclude certain tags.
* Added the option to display tags which aren't associated with any posts.
* Added the `TagDropdown_get_tags` filter to provide advanced users the ability to modify the arguments passed to WordPress' `get_tags` function. Using this filter, the trimming, trimming indicator, and count display settings are still obeyed.

= 1.6 =
* Add `TDW_direct()` function.
* Add count and exclusion options to new direct-implementation function (`TDW_direct()`).
* Corrects two XHTML validation errors.

= 1.5.2 =
* Unloads tag exclusion list upon deactivation.

= 1.5.1 =
* Moved plugin pages to ethitter.com.

= 1.5 =
* Added option to display number of posts within each tag.

= 1.4 =
* Added option to exclude tags based on comma-separated list of slugs.

= 1.3 =
* Rewrote certain widget elements for compatibility back to version 2.3.

= 1.2 =
* Added function to remove plugin settings when deactivated.

= 1.1 =
* Added the ability to trim tag names when calling the function directly.

== Upgrade Notice ==

= 2.3.3 =
* Corrects hook used to load plugin textdomain.

= 2.3.2 =
Readies plugin for translation.

= 2.3.1 =
Now compatible with PHP 7.3.

= 2.3 =
Updated for WordPress 4.3. Removed PHP4-style widget constructor usage (https://make.wordpress.org/core/2015/07/02/deprecating-php4-style-constructors-in-wordpress-4-3/).

= 2.2 =
Updated for WordPress 4.2. Only version 2.2 or higher should be used with WordPress 4.2 or higher, otherwise included/excluded terms may reappear in dropdowns. This is due to WordPress splitting shared terms, as detailed at https://make.wordpress.org/core/2015/02/16/taxonomy-term-splitting-in-4-2-a-developer-guide/.

= 2.1 =
While no major functional changes are included in this release, the plugin itself is better-written and users are encouraged to upgrade. A set of filters are now applied to the dropdown and its components, for greater customizability.

= 2.0.3 =
Corrects a problem in WordPress 3.3 and higher that resulted in an empty taxonomy dropdown. Also removes all uses of PHP short tags.

= 2.0.2 =
Allows empty title in widget options. If empty, the `taxonomy_dropdown_widget_title` filter isn't run.

= 2.0.1 =
Fixes a backwards-compatibility problem in the widget class that generated fatal errors in WordPress 3.0 and earlier.

= 2.0.0.2 =
Fixes a minor bug in the post count threshold setting.

= 2.0.0.1 =
Fixes minor bug that appended cutoff indicators when unnecessary.

= 2.0 =
The plugin was renamed, completely rewritten, and drops support for WordPress 2.7 and earlier. Upgrading will delete all of your existing widgets; see the FAQ for an explanation. Review the changelog and FAQ for more information.

= 1.7 =
This is a major revision to the Tag Dropdown Widget. Before upgrading, please be aware that both `TDW_direct()` and `makeTagDropdown()` are now deprecated functions. Additionally, tags can no longer be excluded based on slug. See changelog for full details.

= 1.6 =
Replaces `makeTagDropdown()` with `TDW_direct()` function, adds post count and exclusion options to direct-implementation function. `makeTagDropdown()` function retained for backwards compatibility. Corrects two XHTML validation errors.
