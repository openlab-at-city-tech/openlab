=== Gravity Forms Directory ===
Tags: gravity forms, gravity form, forms, gravity, form, directory, business, business directory, directorypress, classifieds, cforms, formidable, gravityview
Requires at least: 3.3
Tested up to: 4.6
Stable tag: 3.8.1
Contributors: katzwebdesign, katzwebservices
License: GPLv2 or later
Donate link: https://gravityview.co

Add directory capabilities and other functionality to the great Gravity Forms plugin.

== Description ==

> #### [GravityView](https://gravityview.co/?utm_source=wordpress&utm_medium=readme&utm_campaign=readme) is the best way to display Gravity Forms entries
>
> We set out to make a better Directory plugin, and we did it: GravityView is a brand-new plugin that takes displaying your form entries to the next level. It is easier, more powerful and more customizable than the Directory plugin. If you like the Directory plugin, you'll *love* GravityView.
> 
> [Check out GravityView](https://gravityview.co/?utm_source=wordpress&utm_medium=readme&utm_campaign=readme) or [try a demo](http://demo.gravityview.co?utm_source=wordpress&utm_medium=readme&utm_campaign=readme) today!

### Turn Gravity Forms into a Directory plugin

Gravity Forms is already the easiest form plugin...now, the Gravity Forms Directory & Addons plugin turns Gravity Forms into a great directory.

[youtube http://www.youtube.com/watch?v=PMI7Jb-RP2I]

* Completely shortcode based, using the `[directory]` shortcode
* Includes built-in __searching__
* Allow logged-in users to edit their directory listings
* Choose to show entries to only the users who created them
* Sort by column
* Easily re-organize the columns inside Gravity Forms
* Has an option to <strong>show only approved listings</strong> with an easy approval process
* Show or hide any column
* Display directory & entries as a table (default), list (`<ul>`), or definition list (`<dl>`)
* Directory features pagination
* Define custom styles inside the shortcode
* Includes lightbox support for uploaded images
* Option to __view single entries__ in their own page or in a lightbox

####Insert a totally configurable table using the editor

There are tons of configurable options for how you want the directory to display.

###Improve Gravity Forms Functionality and Usability

* Expand the Add Fields boxes to view all the boxes at once.
* Edit form entries directly from the Entries page (saving two clicks)
* Easily access form data to use in your website with PHP functions - [Learn more on the plugin's website](https://katz.co/gravity-forms-addons/)

#### Have an idea or issue with this Gravity Forms add-on plugin?

* [Leave suggestions, comments, and/or bug reports](http://wordpress.org/support/plugin/gravity-forms-addons)

== Screenshots ==

1. Approving directory entries is very easy
2. When using the Form Editor, your form fields will have a Directory tab for easily modifying your display options.
3. This plugin adds an Edit link to Gravity Form entries
4. Insert a directory
5. How the Gravity Forms widget appears on the widgets page
6. The Gravity Forms Addons settings page, found in the Forms > Directory & Addons menu link
7. How the Gravity Forms 'Add Fields' boxes look after plugin is activated

== Frequently Asked Questions ==

= To integrate a form with Directory: =
1. Go to the post or page where you would like to add the directory.
1. Click the "Add Directory" button above the content area.
1. Choose a form from the drop-down menu and configure settings as you would like them.
1. Click "Insert Directory". A "shortcode" should appear in the content editor that looks similar to <code style="font-size:1em;">[directory form="#"]</code>
1. Save the post or page

= Configuring Fields &amp; Columns =

When editing a form, click on a field to expand the field. Next, click the "Directory" tab. There, you will find options to:

* Choose whether you would like the field to be a link to the Single Entry View;
* Hide the field in Directory View; and
* Hide the field in Single Entry View
* Enable using the field as an advanced search filter

= How do I select what columns I want to display in the Directory? =

1. Edit the Gravity Forms form you'd like to configure a Directory for
1. Click "Directory Columns" In the Form Editor toolbar (near the top-center of the page)
1. Drag & drop the fields in the order you'd like them to appear in the directory
	* Drag from the right ("Hidden Columns") side to the left ("Visible Columns") side.
1. Click the "Save" button
1. Voila!

= How do Directory Search filters work? =
If the field is a text field, a text search input will be added that will search only this field. Otherwise, the field choices will be used to populate a dropdown menu search input.

Example: if the "Vehicle Make" field has choices "Ford", "Chevy", and "Toyota", the search dropdown options will have those items as choices in a dropdown search field. If "Ford" is selected and the search form is submitted, only entries with the Vehicle Make of "Ford" will be shown.

To enable a field as a search filter, view "How do I add a field as a Directory Search filter?" below.

= How do I add a field as a Directory Search filter? =

1. Edit the Gravity Forms form you'd like to configure a Directory for
1. Click the bar on the top of the field to expand the field
1. Click the "Directory" tab
1. Check the box that says "Use this field as a search filter"
1. Click "Update Form" to save the form

= How can I translate the plugin? =

The plugin is fully translatable. [Go here to contribute a translation](https://www.transifex.com/projects/p/gravity-forms-directory/) and we will include it in the next update!

= How do I turn off lightbox grouping? =
Add the following to your theme's `functions.php` file:

`
add_filter('kws_gf_directory_lightbox_settings_rel', 'turn_off_directory_lightbox_grouping');

function turn_off_directory_lightbox_grouping() {
	return 'nofollow';
}
`

= How do I hide entries from logged-out users? =
Turn on the `limituser` setting, then add the following to your theme's `functions.php` file:

`add_filter('kws_gf_treat_not_logged_in_as_user', '__return_false');`

= How do I sort by a column? =
The `sort` attribute allows you to sort by an ID. To find the field ID, On the Gravity Forms ìEdit Formsî page, hover over the form and click the link called "IDs" that appears.

If you want to sort by last name, you find the last name id (`1.6` for example). Then, you add `sort="1.6"` to your `[directory]` shortcode.

Your shortcode could then look something like `[directory form="1" sort="1.6"]`

= I want the URL to be different than `/entry/` - can I do that? =
You can! Add the following to your theme's `functions.php` file:

`
add_filter('kws_gf_directory_endpoint', 'different_directory_endpoint');

function different_directory_endpoint($endpoint) {
		return 'example'; // Use your preferred text here. Note: punctuation may screw things up.
}
`

= How do I change who created an Entry? =
You will see a drop-down input titled "Change Entry Creator" in the Edit Entry "Info" box when you are editing an entry on your site. Change the user in the dropdown, then click the Update button to commit the changes.

* Only users with entry entry editing capability will be able to see the drop-down and edit the entry (the gravityforms_edit_entries capability)
* Select a new user from the drop-down, update the entry, and the entry creator will be updated.
* A note will be added to the entry with the following information:
	* Who changed the entry creator
	* When the change took place
	* Who the previous entry creator was

= How do I add a date filter? =
To add a filter by date, you add either a `start_date` or `end_date` parameter--or both--in `YYYY-MM-DD` format. Here's an example:

`[directory form="14" start_date="1984-10-22" end_date="2011-09-07"]`

= How do I find a field ID? =
On the Gravity Forms "Edit Forms" page, hover over the form and click the link called "IDs" that appears.

= What's the license? =
This plugin is released under a GPL license.

= Form submissions are showing as duplicates. =
This is a known issue. If the submission page has both a form in the content and the same form on the sidebar widget, the entry will be submitted twice. We're working on a fix.

= How do I remove referrer information from emails? =
Add the following to your theme's `functions.php` file:

<code>remove_filter('gform_pre_submission_filter','gf_yst_store_referrer');</code>

= How do I use the filters? =
If you want to modify the output of the plugin, you can do so by adding code to your active __theme's `functions.php` file__. For more information, check out the <a href="http://codex.wordpress.org/Function_Reference/add_filter" rel="nofollow">add_filter() WordPress Codex page</a>

<h3>Plugin filters</h3>

- `kws_gf_directory_output`, `kws_gf_directory_output_'.$form_id` - Modify output for all directories or just a single directory, by ID
- `kws_gf_directory_detail`, `kws_gf_directory_detail_'.$lead_id` - Modify output for single entries
- `kws_gf_directory_value`, `kws_gf_directory_value_'.$input_type`, `kws_gf_directory_value_'.$field_id` - Modify output for fields in general, or based on type (`text`, `date`, `textarea`, etc...), or based on field id.
- `kws_gf_directory_th`, `kws_gf_directory_th_'.$field_id`, `kws_gf_directory_th_'.sanitize_title($label)` - Modify the `<th>` names en masse, by field ID, or by field name (lowercase like a slug)
- `kws_gf_directory_lead_image`, `kws_gf_directory_lead_image_icon`, `kws_gf_directory_lead_image_image`, `kws_gf_directory_lead_image_'.$lead_id`
- And many more - search for `apply_filters` and `do_action` in the `gravity-forms-addons.php` file
<pre>
// This replaces "John" in a first name field with "Jack"
add_filter('kws_gf_directory_value_text', 'john_to_jack');
function john_to_jack($content) {
	return str_replace('John', 'Jack', $content);
}

// This replaces the "Email" table column header with "asdsad"
add_filter('kws_gf_directory_th', 'email_to_asdsad');
function email_to_asdsad($content) {
	return str_replace('Email', 'asdsad', $content);
}

// This replaces "Displaying 1-20" with "asdsad 1 - 20"
add_filter('kws_gf_directory_output', 'displaying_to_asdasd');
function displaying_to_asdasd($content) {
	return str_replace('Displaying', 'asdsad', $content);
}

// This replaces images with the Google icon.
// You can modify all sorts of things using the $img array in this filter.
add_filter('kws_gf_directory_lead_image', 'kws_gf_directory_lead_image_edit');
function kws_gf_directory_lead_image_edit($img = array()) {
	// $img = array('src' => $src, 'size' => $size, 'title' => $title, 'caption' => $caption, 'description' => $description, 'url' => $url, 'code' => "<img src='$src' {$size[3]} />");
        $img['code'] = '<img src="http://www.google.com/images/logo.gif" alt="Replaced!" />';
	return $img;
}
</pre>

= I can't see the fields in the Add Fields box! =
The code is meant to expand all the field boxes so you don't need to click them open and closed all the time. This works normally in Safari and Chrome (read: good browsers :-P). For some other browsers, it breaks the whole page.

To fix this issue, add this to your theme's `functions.php` file:

<code>add_filter('kws_gf_display_all_fields', create_function('$content', 'return "";') );</code>

= I don't want the values to be formatted =

Since 3.6.3, the Directory plugin displays formatted values (like currency). To disable that, add the code below to your theme's `functions.php` file:

`add_filter('kws_gf_directory_format_value', '__return_false' );`


== Changelog ==

= 3.8.1 on August 17, 2016 =
* Fixed: Compatibility with Gravity Forms 2.0 Entries screen
    - The "Directory Columns" menu returns to the toolbar
    - Show the "Directory Columns" link when displaying the default form in the admin
    - The "Approved" checkbox now does not break entries layout
* Fixed: PHP warnings shown when configuring directory columns

= 3.8 on December 10, 2015 =
* Fixed: Gravity Forms 1.9.15 Entries screen conflict
* Fixed: Post Category display included category ID
* Fixed: Display of full Name, full Address, Checkbox, and Radio fields
* Tested with WordPress 4.4

= 3.7.2 and 3.7.3 on May 29 =
* Fixed: Security issue with `add_query_arg()` function. **Please update!** [Learn about the issue](https://make.wordpress.org/plugins/2015/04/20/fixing-add_query_arg-and-remove_query_arg-usage/)
* Fixed: Conflict with the Directory tab in the Form Editor when using Gravity Forms 1.9+
* Updated: Colorbox and Tablesorter scripts

= 3.7 and 3.7.1 on December 17 = 
* Fixed: Add `load_plugin_textdomain()` for translations
* Fixed: Restored single entry links
* Fixed: Displaying IP address, other default entry values
* Fixed: HTML links are no longer displayed as text
* Fixed: Restored respecting settings for image upload formatting
* Fixed: Lightboxes no longer work for non-image files
	- Added `kws_gf_directory_image_extensions` filter to modify what image formats to support
* Added: Improved support for multiple file uploads
* Modified: Added support for future Business Hours field
* Modified: Refactored code; created `render_image_link()` method to render images

= 3.6.3 (on October 3, 2014) =
* Modified: Values now using Gravity Forms formatting by default. Most field types won't change, but some will look different, including pricing fields
* Modified: Removed `wpautop` and `fulltext` parameters - full text, styled using paragraphs is always used
* Fixed: "Too Few Arguments" PHP warning [reported here](https://wordpress.org/support/topic/php-error-107)
* Fixed: Error on activating the plugin, [reported here](https://wordpress.org/support/topic/fatal-error-wp-get-current-user-capabilitiesphp)

= 3.6.2 (on September 25, 2014) =
* Added: Translation files - the plugin's now fully translation-ready. [Go here to contribute a translation](https://www.transifex.com/projects/p/gravity-forms-directory/)
* Fixed: Solved PHP warnings ([see here](https://wordpress.org/support/topic/php-warning-38))
* Fixed: Made IDs lightbox fit inside Gravity Forms' defined height/width settings on Forms page
* Fixed: Sanitize usernames in Change Entry Creator
* Fixed: Don't load lead approval scripts on single entry screen
* Modified: Improved security for editing entries by improving nonce generation
* Modified: Rewrote Change Entry Creator as class
* Modified: Include the Change Entry Creator in core plugin
* Tweak: Switched to dashicon for insert button icon

= 3.6.1.1 & 3.6.1.2 (April 10th, 2014) =
* Fixed: Post Image wasn't showing on the single entry view.
* Fixed: When updating an entry on the frontend, allow the update of conditional hidden fields with cascade conditions

= 3.6.1 (March 31, 2014) =
* Fixed: When updating an entry on the frontend, allow the update of conditional hidden fields (if visible)
* Fixed: Add Directory button (to introduce directory shortcode)
* Fixed: Colorbox examples' paths
* Fixed: Approve toggle icons for entries shown by default (no form selected)

= 3.6 (March 27, 2014) =
* Updated Colorbox js plugin (v 1.5.5)
	* Fixed: jquery.colorbox-min.js path
* Updated Tablesorter plugin
	- Now Tablesorter supports more themes using the `kws_gf_tablesorter_theme` filter:
		- `black-ice`, `blue`, `bootstrap`, `bootstrap_2`, `dark`, `default`, `dropbox`, `green`, `grey`, `ice`, `jui`
		- [See themes here](http://mottie.github.io/tablesorter/docs/themes.html)
* Fixed: When displaying the post link allow `nofollow` and `target` configuration
* Fixed: Added support for non-standard locations of plugin directories
* Modified: Removed the default `fixed` table class, this was conflicting with several WordPress themes' CSS

= 3.5.4.5 (March 21, 2014) =
* Fixed: View entry issue when website has too many users caused by entry creator change select box. If more than 300 users, show only administrators.
* Fixed: Undefined variable notice (line 2606)
* Added filter to convert the post_title field in a link to the post itself

= 3.5.4.3 (March 10, 2014) =
* IMPORTANT SECURITY UPDATE - security hole patched. __Update as soon as possible.__ (Thanks, BMoskovits)
* Fixed: Lightbox entry view now allows `wp-content` to be a different name
* Fixed: Static PHP messages on settings page
* Fixed: Back to Directory link now works with Javascript disabled
* Fixed: Back to Directory link now displays properly on lightbox entry view. Note: it will link to the originating entry, not a blog archive page if the directory is embedded in a blog post.

= 3.5.4.2 (March 10, 2014) =
* Fix broken path to Change Lead Creator plugin

= 3.5.4.1 (March 10, 2014) =
* Small fix on showing the edit entry link for own user entries
* Renamed filename from `change-lead-creator.php` to `gravity-forms-lead-creator.php` as WordPress activates the first file with plugin info encountered in the directory (ordered by name) - this way, the main file `gravity-forms-addons.php` will appear first.

= 3.5.4 (January 23, 2014) =
* Separated Change Entry Creator functionality into a separate, packaged plugin. This will allow you to enable or disable the functionality as you would a plugin.
* Added a new filter (`kws_gf_entry_creator_users`) for the Change Entry Creator plugin. This allows you to define what users appear in the dropdown (as an array of `WP_User` objects). If no users are specified, all users are shown.

= 3.5.3 (January 13, 2014) =
The fixes in this update were submitted by [Dylan Bartlett](http://www.dylanbarlett.com). Thanks, Dylan!

* Check for 'page' request var instead of suppressing error when not set.
* Add filters & actions to `gf_edit_forms` only when editing a specific form
* Fixed: Use correct path to enqueue Colorbox JS
* Fixed: JS syntax in Search function

= 3.5.2 (December 30, 2013) =
* Fixed: Fatal error for users using < Gravity Forms 1.8

= 3.5 & 3.5.1 (December 29, 2013) =
* Added: __Advanced search filters!__ Filter results based on fields of the form. For more information, read the item in the FAQ tab: "How do Directory Search filters work?"
* Added: Make fields visibile based on whether an user is logged-in and has certain capabilities ("Only visible to logged in users with [Any] role." setting in the Directory tab)
* Added: Supports Single Entry links when Previewing posts and pages that have an embedded directory
* Fixed: Use `sort_field_number` instead of `sort_field` for the `get_leads()` method
* Fixed: Replaced `WP_PLUGIN_URL` with `plugins_url()` to prevent mixed content warnings when editing forms over HTTPS (thanks, [dbarlett](https://github.com/dbarlett))
* Fixed: Now respects shortcode `entryback` link setting (previously, only the global setting was respected)
* Updated: Better icon on Edit Form view for Gravity Forms 1.8
* Updated: Removed `remove_admin_only()` method and replaced with `remove_hidden_fields()`

= 3.4.4 (December 9, 2013) =
* Fixed: Entry approval error [ticket](http://wordpress.org/support/topic/approval-not-working-1)
* Fixed: Images in the directory now open using lightbox again
* Fixed: Show Form IDs functionality
* Fixed: Broken link to SimpleModal script on Forms page ([ticket](http://wordpress.org/support/topic/js-error-6))
* Fixed: Compatibility with Gravity Forms 1.8's new icon set
* Updated: Now uses latest Colorbox script

= 3.4.3 (October 31, 2013) =
* Fixed: PHP 5.4 warnings
* Fixed: Editing Lists field type
* Fixed: Directory Columns window now displays properly
* Fixed: File Upload display in single entry
* Fixed: `Notice: Trying to get property of non-object[...]on line 4051` error
* Known issue: Compatibility with editing entries in forms with Quiz and Poll types

= 3.4.2 (October 11, 2013) =
* Fixed compatibility with WordPress 3.6+
	- Directory tab restored to form editor
	- Converted jQuery `live()` to `on()`
* Updated Colorbox library

= 3.4.1 (February 26, 2013) =
* Fixed: Issue where entries would be hidden if both "Show only entries that have been Approved" and "Smart Approval" aren't checked
* Fixed: Insert Directory button image path fixed
* Fixed: PHP warning
* Modified: Single Entry view now uses `<th>` instead of `<td>` for headings
* Removed: The removed checkbox for Yoast Widget in settings

= 3.4 (February 21, 2013) =
* Added: __Finally__: A Directory Columns interface! Read the FAQ "How do I select what columns I want to display?" to set up.
	- Includes field summary option: instead of each individual checkbox or field value, you can choose to diplay the whole shebang. You can now have a column for "Address" and "Name" instead of "First Name" and "Last Name"!
* Added: When leads are approved or disapproved, a note is added to the lead with who took the action and when.
* Added: new setting `entrydetailtitle`, which allows you to easily overwrite the Entry View table heading
* Added: Support for "List" input types
* Added: Ability to change who created an Entry.
* Added `kws_gf_directory_lead_being_edited` and `kws_gf_directory_form_being_edited` filters to allow users to modify what fields should be shown for editing.
* Added: `kws_gf_date_format` filter for Directory date format
* Added: `kws_gf_directory_tick` filter for changing the check mark in the directory
* Modified: Improved "Add Directory" button to match WordPress 3.5.
* Modified: Removed a few options to simplify the plugin (`icon`, `showrowids`)
* Modified: "Un-approve" is now proper English: Disapprove
* Modified: "Entry Links" Lightbox view now has back links
* Modified: Updated Colorbox to latest version
* Fixed: Approve checkbox works again.
* Fixed: Added support for the User Registration Add-on's password field.
* Fixed: Entry Date field displays properly
* Fixed: Expand All Menus works properly again
* Fixed: Issue in Entries view where approval "tick" images would be too agressive and take over a couple of columns
* Fixed: Many admin issues where things were broken.
* Removed: Redundant widget file
* Removed: No longer uses Gravity Forms CSS file on directory view

= 3.3.1 =
* Fixed: Fixed issue where datepicker functionality may not exist.
* Improved: Plugin now uses WordPress jQueryUI datepicker script, instead of Gravity Forms'.

= 3.3 =
* Next up: improved management of directory column order and visibility!
* Fixed: Pagination doesn't work when embedding forms in a page and using permalinks
* Fixed: Back links would always link to the homepage when permalinks are turned off
* Fixed: Issue with removing certain fields in the edit screen
* Fixed: Searches on pages without permalinks enabled now won't go to the home page
* Fixed: Messed up datepicker fields when working with other plugins using date pickers (<a href="http://wordpress.org/support/topic/plugin-gravity-forms-directory-date-picker-conflict">support topic</a>)
* Fixed: Messed up menu links when navigating from a single entry view
* Fixed & Improved: Added support for order details in entry view
* Fixed: Incorrect instructions in the Add Directory form. "Allow administrators to edit entries they created." should have been "Allow administrators to edit all entries."
* Fixed: Issue with full text not showing up in the Directory, even when `fulltext` was enabled
* Fixed: `compact` now properly implemented. This is to better inform content filters.
* Fixed: Editing an entry in a lightbox now works properly
* Improved: Added ids to the directory `<th>`, <a href="http://wordpress.org/support/topic/plugin-gravity-forms-directory-set-column-width">as requested</a>
* Improved: Cleaned up some code
* Improved: Lead detail editing

= 3.2.2 =
* For sites that have "pretty permalinks" turned off
	* Fixed issue with "Back to Directory" links not working
	* Fixed search
* Fixed bug where "Hide This Field in Directory View" wasn't working properly
* Added a check so that both Thickbox and Colorbox don't open entry if both scripts are loaded

= 3.2.1 =
* Fixed Colorbox not loading properly in certain cases (<a href="Issue http://wordpress.org/support/topic/656033" rel="nofollow">Issue #656033</a>)
* Fixed issue where entries appeared not to be approving properly in the admin.

= 3.2 =
* Added `limituser` option - a new option to show only entries users have created. You can also hide entries from not-logged-in users (see FAQ).
* Updated lightbox to use <a href="http://jacklmoore.com/colorbox/">Colorbox</a>, vastly superior lightbox to Thickbox.
	- Now uses `lightboxsettings` shortcode attribute (but is backward compatible with `entrylightbox` and `lightbox` settings)
	- Added `kws_gf_directory_colorbox_settings` filter to allow you to modify the settings
	- Groups images, websites, and entries separately by default. Use `kws_gf_directory_lightbox_settings_rel` filter to modify (see FAQ)
	- Choose from multiple styles
* Added `list` input type support
* Fixed bugs/issues
	* Fixed 404 errors in Single Entry View after de-activating then re-activating plugin. Now properly generates rewrite rules.
	* Fixed header code 404 when viewing entries in lightbox mode
	* Front-end editing of certain types of input types failed because `GFFormDisplay` class wasn't defined.
	* Fixed issue where scripts were not always printing in the `<head>`
	* Fixed fatal error when outputting `date_created` field (<a href="http://wordpress.org/support/topic/649652">issue #649652</a>)

= 3.1.1 =
* Fixes issue where entries not showing on sort

= 3.1 =
* Added much-requested option for front-end User editing of entries. Must be enabled (off by default).
* Added option for front-end Administrator editing of entries (except for approval status). Must be enabled (off by default).
* Fixed issue where multiple-word searches were being converted into one word.
* Removed `?row=#` for the back-link to the directory. There was no need for it to get the lead ID.
* Added actions and filters for the new editing capabilities. Check out the code if you a) know what this means, and b) want to see. Search for `apply_filters` and `do_action`.

= 3.0.3 =
Sorry for the many updates in one day, but I can only fix many bugs as they get reported.

* Fixed "close thickbox" button image path for IIS (Windows) servers by using `site_url()` instead of `get_bloginfo()`
* Fixed potential incorrect form ID in the link generation to single entries
* Improved `start_date` and `end_date` shortcode generation
* Fixed `Warning: require_once(directory.php): failed to open stream: No such file or directory` warning when using lightbox to view single entries.
* Fixed non-javascript links to sort by column

= 3.0.2 =
* Fixed "This form does not have any entries yet." issue - the filtering code was not compatible with Gravity Forms 1.5, only 1.6 beta. This has been resolved.

= 3.0.1 =
This release should fix some major issues users were having with 3.0. Sorry for the problems.

* Fixed issue where Directory Fields buttons weren't being rendered (the JavaScript hadn't been loaded)
* Fixed issue with support for <a href="http://wordpress.org/extend/plugins/members/" rel="nofollow">Members plugin</a>
* Added improved support for filter by date
	- Added `start_date` and `end_date` settings to Insert Directory form with datepicker
	- Now allows for sorting using the query string (for example, adding `?start_date=YYYY-MM-DD` to the directory URL)
* Removed bulk update Approve and Disapprove options when form not approval-enabled
* Fixed display of Directory & Addons menu - now showing on all admin pages.

= 3.0 =
* Completely revamped the admin approval process! Now approving an entry is as easy as checking a box in the Entries view.
	- Supports bulk approve and disapprove
* Added "Directory Fields" in the Form Editor
	- "Approved" field: Add this to your form to have a pre-configured admin-only checkbox.
	- "Entry Link" field: Use this text as a link to the single entry view
* Added "Directory" tab to fields in the Form Editor
	- Use Field As Link to Single Entry
	- Text for Link to Single Entry
		* Use field values from entry
		* Use the Field Label as link text
		* Use custom link text.
	- Hide Field in Directory View
	- Hide Field in Single Entry View
* Added a how-to video and improved instructions on settings page
* Improved how settings work & some new settings
	* Added "Smart Approval" - Automatically convert directory into Approved-only mode when an Approved field is detected
	* Added configuration for default directory settings on the Directory & Addons settings page
	* Added `jstable` setting to enable javascript sorting using the Tablesorter script. Includes `kws_gf_directory_tablesorter_options` filter to modify Tablesorter settings.
	* Updated `page_size` setting: setting a page size of 0 now shows all entries.
	* Added credit link setting for directories
* Fixed bugs & issues
	* Fixed search and entry counts for Approved-only directories
	* Improved internationalization support
* Structural & display improvements
	* Added proper enqueuing of scripts and styles with `enqueue_files` function.
	* Hides search and page count when there are no results
	* Restructured plugin to use the `GFDirectory` class.
	* Added a host of new actions and filters to allow for inserting custom content throughout the directory
	* Added support for custom endpoints (instead of `entries`...see FAQ for more information)
* And much, much more!

Note: This update has only been tested with WordPress 3.2 and Gravity Forms 1.5.2.8 and Gravity Forms 1.6 beta.

= 2.5.2 =
* Fixed broken image for lightbox close button (<a href="http://wordpress.org/support/topic/570042" rel="nofollow">issue #570042</a>)
* Fixed definition list (DL) display mode: each entry in directory view is now wrapped with a `dl`; single-entry view entries are now wrapped with single `dl`
* HTML generation fix: `<liclass` now `<li class` (thanks @lolawson)
* Improved JavaScript table sorting function (thanks to <a href="http://wordpress.org/support/topic/565544" rel="nofollow">feedback from heavymark</a>)
* Added option to use links to sort tables instead of JavaScript (`jssearch`, under Formatting Options)

= 2.5.1 =
* Added alternating `class` of even and odd for rows

= 2.5 =
* Improved directory shortcode insertion by checking values against defaults; now inserts into code only non-default items (the default shortcode is now 20 characters instead of 815!)
* Added formatting options for directory & entries: display as table (default), list (`<ul>`), or definition list (`<dl>`)
* Added `kws_gf_directory_defaults` filter to update plugin defaults.
* Added address formatting using `appendaddress` setting. This will add a column to the output with a combined, formatted address. Use new `hideaddresspieces` setting to turn off the individual address pieces. Instead of having Street, City, State, ZIP, now there's one column "Address"
* Added `truncatelink` option (explained below)
* Added URL formatting filters to modify how links are truncated so you can choose to display the anchor text exactly as you want (the URL itself won't change). The link text `http://example.example.choicehotels.com/hotel/tx173` becomes `choicehotels.com`, but will still link to the full URL.
	- Don't show http(s): `kws_gf_directory_anchor_text_striphttp`
	- Strip www: `kws_gf_directory_anchor_text_stripwww`
	- Show root only, not the linked to page (`example.com/inner-page/` becomes `example.com`): `kws_gf_directory_anchor_text_rootonly`
	- Strip all subdomains, including www: `kws_gf_directory_anchor_text_nosubdomain`
	- Hide "query strings" (`example.com?search=example&action=search` becomes `example.com`): `kws_gf_directory_anchor_text_noquerystring`
* Submit a form using the keyboard, not just clicking the button
* Added filter to change directory pagination settings (results page links): `kws_gf_results_pagination`
* Fixed issue with malformed pagination link URLs
* Improved "Expand All Menus" checkbox layout
* Discovered an issue: pagination on approved-only entries doesn't work well. To compensate, you could set your page size to a large number that contains all the entries. This likely will not be fixed soon.

= 2.4.4 =
* Added administration menu for Gravity Forms Addons, allowing you to turn off un-used or un-desired functionality. Access settings either using Forms > Addons link or Forms > Settings > Addons.
	* Choose to turn off referrer information, directory functionality, the Addons widget, and Gravity Forms backend modifications

= 2.4.3 =
* Should fix issue with Approved checkbox not working in some cases where Admin-Only is enabled. Please report if still having issues.

= 2.4.2 =
* Fixed display of textarea entry data for short content (<a href="http://wordpress.org/support/topic/504755" rel="nofollow">thanks, Tina</a>)

= 2.4.1 =
* Included entry-details.php file, required for lightbox viewing
* Fixed issue with single-entry lightbox view - no longer shows admin-only columns if admin-only setting is turned off.
* Fixed Multi-blog single entry view, canonical link and chortling generation

= 2.4 =
* __Added single-entry viewing capability__
	- View single entry details on either a separate page or in a lightbox
	- Entries in separate page have their own permalink (http://example.com/directory/entry/[form#]/[entry#]/)
	- Add entry detail links by having Entry ID column added to directory
* Fixed footer column filters


== Upgrade Notice ==

= 3.8.1 on August 17, 2016 =
* Fixed: Compatibility with Gravity Forms 2.0 Entries screen
    - The "Directory Columns" menu returns to the toolbar
    - Show the "Directory Columns" link when displaying the default form in the admin
    - The "Approved" checkbox now does not break entries layout
* Fixed: PHP warnings shown when configuring directory columns

= 3.8 on December 10, 2015 =
* Fixed: Gravity Forms 1.9.15 Entries screen conflict
* Fixed: Post Category display included category ID
* Fixed: Display of full Name, full Address, Checkbox, and Radio fields
* Tested with WordPress 4.4

= 3.7 and 3.7.1 on December 17 = 
* Fixed: Add `load_plugin_textdomain()` for translations
* Fixed: Restored single entry links
* Fixed: Displaying IP address, other default entry values
* Fixed: HTML links are no longer displayed as text
* Fixed: Restored respecting settings for image upload formatting
* Fixed: Lightboxes no longer work for non-image files
	- Added `kws_gf_directory_image_extensions` filter to modify what image formats to support
* Added: Improved support for multiple file uploads
* Modified: Added support for future Business Hours field
* Modified: Refactored code; created `render_image_link()` method to render images

= 3.6.3 (on October 3, 2014) =
* Modified: Values now using Gravity Forms formatting by default. Most field types won't change, but some will look different, including pricing fields
* Modified: Removed `wpautop` and `fulltext` parameters - full text, styled using paragraphs is always used
* Fixed: "Too Few Arguments" PHP warning [reported here](https://wordpress.org/support/topic/php-error-107)
* Fixed: Error on activating the plugin, [reported here](https://wordpress.org/support/topic/fatal-error-wp-get-current-user-capabilitiesphp)

= 3.6.2 (September 25, 2014) =
* Added: Translation files - to contribute a translation, please go here.
* Fixed: Solved PHP warnings ([see here](https://wordpress.org/support/topic/php-warning-38))
* Fixed: Made IDs lightbox fit inside Gravity Forms' defined height/width settings on Forms page
* Fixed: Sanitize usernames in Change Entry Creator
* Fixed: Don't load lead approval scripts on single entry screen
* Modified: Improved security for editing entries by improving nonce generation
* Modified: Rewrote Change Entry Creator as class
* Modified: Include the Change Entry Creator in core plugin
* Tweak: Switched to dashicon for insert button icon

= 3.6.1.1 & 3.6.1.2 (April 10th, 2014) =
* Fixed: Post Image wasn't showing on the single entry view.
* Fixed: When updating an entry on the frontend, allow the update of conditional hidden fields with cascade conditions

= 3.6.1 (March 31, 2014) =
* Fixed: When updating an entry on the frontend, allow the update of conditional hidden fields (if visible)
* Fixed: Add Directory button (to introduce directory shortcode)
* Fixed: Colorbox examples' paths
* Fixed: Approve toggle icons for entries shown by default (no form selected)

= 3.6 (March 27, 2014) =
* Updated Colorbox js plugin (v 1.5.5)
	* Fixed: jquery.colorbox-min.js path
* Updated Tablesorter plugin
	- Now Tablesorter supports more themes using the `kws_gf_tablesorter_theme` filter:
		- `black-ice`, `blue`, `bootstrap`, `bootstrap_2`, `dark`, `default`, `dropbox`, `green`, `grey`, `ice`, `jui`
		- [See themes here](http://mottie.github.io/tablesorter/docs/themes.html)
* Fixed: When displaying the post link allow `nofollow` and `target` configuration
* Fixed: Added support for non-standard locations of plugin directories
* Modified: Removed the default `fixed` table class, this was conflicting with several WordPress themes' CSS

= 3.5.4.5 (March 21, 2014) =
* Fixed: View entry issue when website has too many users caused by entry creator change select box. If more than 300 users, show only administrators.
* Fixed: Undefined variable notice (line 2606)
* Added filter to convert the post_title field in a link to the post itself

= 3.5.4.3 (March 10, 2014) =
* IMPORTANT SECURITY UPDATE - security hole patched. __Update as soon as possible.__
* Fixed: Lightbox entry view now allows `wp-content` to be a different name
* Fixed: Static PHP messages on settings page
* Fixed: Back to Directory link now works with Javascript disabled
* Fixed: Back to Directory link now displays properly on lightbox entry view. Note: it will link to the originating entry, not a blog archive page if the directory is embedded in a blog post.

= 3.5.4.2 (March 10, 2014) =
* Fix broken path to Change Lead Creator plugin

= 3.5.4.1 (March 10, 2014) =
* Small fix on showing the edit entry link for own user entries
* Renamed filename from `change-lead-creator.php` to `gravity-forms-lead-creator.php` as WordPress activates the first file with plugin info encountered in the directory (ordered by name) - this way, the main file `gravity-forms-addons.php` will appear first.

= 3.5.4 (January 23, 2014) =
* Separated Change Entry Creator functionality into a separate, packaged plugin. This will allow you to enable or disable the functionality as you would a plugin.
* Added a new filter (`kws_gf_entry_creator_users`) for the Change Entry Creator plugin. This allows you to define what users appear in the dropdown (as an array of `WP_User` objects). If no users are specified, all users are shown.

= 3.5.3 (January 13, 2014) =
The fixes in this update were submitted by [Dylan Bartlett](http://www.dylanbarlett.com). Thanks, Dylan!

* Check for 'page' request var instead of suppressing error when not set.
* Add filters & actions to `gf_edit_forms` only when editing a specific form
* Fixed: Use correct path to enqueue Colorbox JS
* Fixed: JS syntax in Search function

= 3.5.2 (December 30, 2013) =
* Fixed: Fatal error for users using < Gravity Forms 1.8

= 3.5 & 3.5.1 (December 29, 2013) =
* Added: __Advanced search filters!__ Filter results based on fields of the form. For more information, read the item in the FAQ tab: "How do Directory Search filters work?"
* Added: Make fields visibile based on whether an user is logged-in and has certain capabilities ("Only visible to logged in users with [Any] role." setting in the Directory tab)
* Added: Supports Single Entry links when Previewing posts and pages that have an embedded directory
* Fixed: Use `sort_field_number` instead of `sort_field` for the `get_leads()` method
* Fixed: Replaced `WP_PLUGIN_URL` with `plugins_url()` to prevent mixed content warnings when editing forms over HTTPS (thanks, [dbarlett](https://github.com/dbarlett))
* Fixed: Now respects shortcode `entryback` link setting (previously, only the global setting was respected)
* Updated: Better icon on Edit Form view for Gravity Forms 1.8
* Updated: Removed `remove_admin_only()` method and replaced with `remove_hidden_fields()`

= 3.4.4 (December 9, 2013) =
* Fixed: Entry approval error [ticket](http://wordpress.org/support/topic/approval-not-working-1)
* Fixed: Images in the directory now open using lightbox again
* Fixed: Show Form IDs functionality
* Fixed: Broken link to SimpleModal script on Forms page ([ticket](http://wordpress.org/support/topic/js-error-6))
* Fixed: Compatibility with Gravity Forms 1.8's new icon set
* Updated: Now uses latest Colorbox script

= 3.4.3 (October 31, 2013) =
* Fixed: PHP 5.4 warnings
* Fixed: Editing Lists field type
* Fixed: Directory Columns window now displays properly
* Fixed: File Upload display in single entry
* Fixed: `Notice: Trying to get property of non-object[...]on line 4051` error
* Known issue: Compatibility with editing entries in forms with Quiz and Poll types

= 3.4.2 (October 11, 2013) =
* Fixed compatibility with WordPress 3.6+
	- Directory tab restored to form editor
	- Converted jQuery `live()` to `on()`
* Updated Colorbox library

= 3.4.1 (February 26, 2013) =
* Fixed: Issue where entries would be hidden if both "Show only entries that have been Approved" and "Smart Approval" aren't checked
* Fixed: Insert Directory button image path fixed
* Fixed: PHP warning
* Modified: Single Entry view now uses `<th>` instead of `<td>` for headings

= 3.4 (February 21, 2013) =
* A major update with big fixes and additions. Read the changelog for more information.

= 3.3.1 =
* Fixed: Fixed issue where datepicker functionality may not exist.
* Improved: Plugin now uses WordPress jQueryUI datepicker script, instead of Gravity Forms'.

= 3.3 =
* Next up: improved management of directory column order and visibility!
* Fixed: Pagination doesn't work when embedding forms in a page and using permalinks
* Fixed: Back links would always link to the homepage when permalinks are turned off
* Fixed: Issue with removing certain fields in the edit screen
* Fixed: Searches on pages without permalinks enabled now won't go to the home page
* Fixed: Messed up datepicker fields when working with other plugins using date pickers (<a href="http://wordpress.org/support/topic/plugin-gravity-forms-directory-date-picker-conflict">support topic</a>)
* Fixed: Messed up menu links when navigating from a single entry view
* Fixed & Improved: Added support for order details in entry view
* Fixed: Incorrect instructions in the Add Directory form. "Allow administrators to edit entries they created." should have been "Allow administrators to edit all entries."
* Fixed: Issue with full text not showing up in the Directory, even when `fulltext` was enabled
* Fixed: `compact` now properly implemented. This is to better inform content filters.
* Fixed: Editing an entry in a lightbox now works properly
* Improved: Added ids to the directory `<th>`, <a href="http://wordpress.org/support/topic/plugin-gravity-forms-directory-set-column-width">as requested</a>
* Improved: Cleaned up some code
* Improved: Lead detail editing

= 3.2.2 =
* For sites that have "pretty permalinks" turned off
	* Fixed issue with "Back to Directory" links not working
	* Fixed search
* Fixed bug where "Hide This Field in Directory View" wasn't working properly

= 3.2.1 =
* Fixed Colorbox not loading properly in certain cases (<a href="Issue http://wordpress.org/support/topic/656033" rel="nofollow">Issue #656033</a>)
* Fixed issue where entries appeared not to be approving properly in the admin.

= 3.2 =
* Added `limituser` option - a new option to show only entries users have created. You can also hide entries from not-logged-in users (see FAQ).
* Updated lightbox to use <a href="http://jacklmoore.com/colorbox/">Colorbox</a>, vastly superior lightbox to Thickbox.
	- Now uses `lightboxsettings` shortcode attribute (but is backward compatible with `entrylightbox` and `lightbox` settings)
	- Added `kws_gf_directory_colorbox_settings` filter to allow you to modify the settings
	- Groups images, websites, and entries separately by default. Use `kws_gf_directory_lightbox_settings_rel` filter to modify (see FAQ)
	- Choose from multiple styles
* Fixed issue where lightbox scripts weren't outputting
* Fixed issue where front-end editing of certain types of input types failed because `GFFormDisplay` class wasn't defined.
* Fixed 404 errors in Single Entry View after de-activating then re-activating plugin. Now properly generates rewrite rules.
* Fixed header code 404 when viewing entries in lightbox mode

= 3.1.1 =
* Fixes issue where entries not showing on sort - Note: this only affected users using versions of Gravity Forms older than 1.6.

= 3.1 =
* Added much-requested option for front-end User editing of entries. Must be enabled (off by default).
* Added option for front-end Administrator editing of entries (except for approval status). Must be enabled (off by default).
* Fixed issue where multiple-word searches were being converted into one word.
* Shortened changelog to only show versions after 2.4

= 3.0.3 =
* Fixed "close thickbox" button image path for IIS (Windows) servers by using `site_url()` instead of `get_bloginfo()`
* Fixed potential incorrect form ID in the link generation to single entries
* Improved `start_date` and `end_date` shortcode generation
* Fixed `Warning: require_once(directory.php): failed to open stream: No such file or directory` warning when using lightbox to view single entries.
* Fixed non-javascript links to sort by column

= 3.0.2 =
* Fixed "This form does not have any entries yet." issue - the filtering code was not compatible with Gravity Forms 1.5, only 1.6 beta. This has been resolved.

= 3.0.1 =
* Fixed issue where Directory Fields buttons weren't being rendered (the JavaScript hadn't been loaded)
* Fixed issue with support for <a href="http://wordpress.org/extend/plugins/members/" rel="nofollow">Members plugin</a>
* Added improved support for filter by date
	- Added `start_date` and `end_date` settings to Insert Directory form with datepicker
	- Now allows for sorting using the query string (for example, adding `?start_date=YYYY-MM-DD` to the directory URL)
* Removed bulk update Approve and Disapprove options when form not approval-enabled
* Fixed display of Directory & Addons menu - now showing on all admin pages.

= 3.0 =
* Completely revamped the admin approval process! Now approving an entry is as easy as checking a box in the Entries view.
	- Supports bulk approve and un-approve
* Added "Directory Fields" in the Form Editor
	- "Approved" field: Add this to your form to have a pre-configured admin-only checkbox.
	- "Entry Link" field: Use this text as a link to the single entry view
* Added "Directory" tab to fields in the Form Editor
	- Use Field As Link to Single Entry
	- Text for Link to Single Entry
		* Use field values from entry
		* Use the Field Label as link text
		* Use custom link text.
	- Hide Field in Directory View
	- Hide Field in Single Entry View
* Added a how-to video and improved instructions on settings page
* Improved how settings work & some new settings
	* Added "Smart Approval" - Automatically convert directory into Approved-only mode when an Approved field is detected
	* Added configuration for default directory settings on the Directory & Addons settings page
	* Added `jstable` setting to enable javascript sorting using the <a href="http://tablesorter.com/docs/" rel="nofollow">Tablesorter</a> script. Includes `kws_gf_directory_tablesorter_options` filter to modify Tablesorter settings.
	* Updated `page_size` setting: setting a page size of 0 now shows all entries.
	* Added credit link setting for directories
* Fixed bugs & issues
	* Fixed search and entry counts for Approved-only directories
	* Improved internationalization support
* Structural & display improvements
	* Added proper enqueuing of scripts and styles with `enqueue_files` function.
	* Hides search and page count when there are no results
	* Restructured plugin to use the `GFDirectory` class.
	* Added a host of new actions and filters to allow for inserting custom content throughout the directory
	* Added support for custom endpoints (instead of `entries`...see FAQ for more information)
* And much, much more!

Note: This update has only been tested with WordPress 3.2 and Gravity Forms 1.5.2.8 and Gravity Forms 1.6 beta.

= 2.5.2 =
* Fixed broken image for lightbox close button (<a href="http://wordpress.org/support/topic/570042" rel="nofollow">issue #570042</a>)
* Fixed definition list (DL) display mode: each entry in directory view is now wrapped with a `dl`; single-entry view entries are now wrapped with single `dl`
* HTML generation fix: `<liclass` now `<li class` (thanks @lolawson)
* Improved JavaScript table sorting function (thanks to <a href="http://wordpress.org/support/topic/565544" rel="nofollow">feedback from heavymark</a>)
* Added option to use links to sort tables instead of JavaScript (`jssearch`, under Formatting Options)

= 2.5.1 =
* Added alternating `class` of even and odd for rows

= 2.5 =
* Improved directory shortcode insertion by checking values against defaults; now inserts into code only non-default items (the default shortcode is now 20 characters instead of 815!)
* Added formatting options for directory & entries: display as table (default), list (`<ul>`), or definition list (`<dl>`)
* Added `kws_gf_directory_defaults` filter to update plugin defaults.
* Added address formatting using `appendaddress` setting. This will add a column to the output with a combined, formatted address. Use new `hideaddresspieces` setting to turn off the individual address pieces. Instead of having Street, City, State, ZIP, now there's one column "Address"
* Added `truncatelink` option (explained below)
* Added URL formatting filters to modify how links are truncated so you can choose to display the anchor text exactly as you want (the URL itself won't change). The link text `http://example.example.choicehotels.com/hotel/tx173` becomes `choicehotels.com`, but will still link to the full URL.
	- Don't show http(s): `kws_gf_directory_anchor_text_striphttp`
	- Strip www: `kws_gf_directory_anchor_text_stripwww`
	- Show root only, not the linked to page (`example.com/inner-page/` becomes `example.com`): `kws_gf_directory_anchor_text_rootonly`
	- Strip all subdomains, including www: `kws_gf_directory_anchor_text_nosubdomain`
	- Hide "query strings" (`example.com?search=example&action=search` becomes `example.com`): `kws_gf_directory_anchor_text_noquerystring`
* Submit a form using the keyboard, not just clicking the button
* Added filter to change directory pagination settings (results page links): `kws_gf_results_pagination`
* Fixed issue with malformed pagination link URLs
* Improved "Expand All Menus" checkbox layout
* Discovered an issue: pagination on approved-only entries doesn't work well. To compensate, you could set your page size to a large number that contains all the entries. This likely will not be fixed soon.

= 2.4.4 =
* Added administration menu for Gravity Forms Addons, allowing you to turn off un-used or un-desired functionality.

= 2.4.3 =
* Should fix issue with Approved checkbox not working in some cases where Admin-Only is enabled. Please report if still having issues.

= 2.4.2 =
* Fixed display of textarea entry data for short content (<a href="http://wordpress.org/support/topic/504755" rel="nofollow">thanks, Tina</a>)

= 2.4.1 =
* Included entry-details.php file, required for lightbox viewing
* Fixed issue with single-entry lightbox view - no longer shows admin-only columns if admin-only setting is turned off.
* Fixed Multi-blog single entry view, canonical link and chortling generation

= 2.4 =
* Added single-entry viewing capability
	- View single entry details on either a separate page or in a lightbox
	- Entries in separate page have their own permalink (http://example.com/directory/entry/[form#]/[entry#]/)
	- Add entry detail links by having Entry ID column added to directory
* Fixed footer column filters


== Installation ==

1. Upload this plugin to your blog and Activate it
2. Set it up following instructions below:

### To integrate a form with Directory: ###
1. Go to the post or page where you would like to add the directory.
1. Click the "Add Directory" button above the content area.
1. Choose a form from the drop-down menu and configure settings as you would like them.
1. Click "Insert Directory". A "shortcode" should appear in the content editor that looks similar to <code style="font-size:1em;">[directory form="#"]</code>
1. Save the post or page

### How do I select what columns I want to display in the Directory? ###

1. Edit the Gravity Forms form you'd like to configure a Directory for
1. Click "Directory Columns" In the Form Editor toolbar (near the top-center of the page)
1. Drag & drop the fields in the order you'd like them to appear in the directory
	* Drag from the right ("Hidden Columns") side to the left ("Visible Columns") side.
1. Click the "Save" button
1. Voila!

### More Configuring of Fields & Columns ###

When editing a form, click on a field to expand the field. Next, click the "Directory" tab. There, you will find options to:

* Choose whether you would like the field to be a link to the Single Entry View;
* Hide the field in Directory View; and
* Hide the field in Single Entry View
