=== Gravity Forms Directory ===
Tags: gravity forms, gravity form, forms, gravity, form, crm, directory, business, business directory, list, listings, sort, submissions, table, tables, member, contact, contacts, directorypress, business directory, directory plugin, wordpress directory, classifieds, captcha, cforms, contact, contact form, contact form 7, contact forms, CRM, email, enhanced wp contact form, feedback, form, forms, gravity, gravity form, gravity forms, secure form, simplemodal contact form, wp contact form, widget
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: trunk
Contributors: katzwebdesign
License: GPLv2 or later
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Gravity%20Forms%20Addons&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

Add directory capabilities and other functionality to the great Gravity Forms plugin.

== Description ==

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
* Easily access form data to use in your website with PHP functions - [Learn more on the plugin's website](http://www.seodenver.com/gravity-forms-addons/)

#### Other Gravity Forms Add-ons:

* <a href="http://wordpress.org/extend/plugins/gravity-forms-salesforce/">Gravity Forms Salesforce Add-on</a> - Integrate Gravity Forms with Salesforce.com
* <a href="http://wordpress.org/extend/plugins/gravity-forms-highrise/">Gravity Forms Highrise Add-on</a> - Integrate Gravity Forms with Highrise, a CRM
* <a href="http://wordpress.org/extend/plugins/gravity-forms-constant-contact/">Gravity Forms + Constant Contact</a> - If you use Constant Contact and Gravity Forms, this plugin is for you.
* <a href="http://wordpress.org/extend/plugins/gravity-forms-mad-mimi/">Gravity Forms Mad Mimi Add-on</a> - Integrate Mad Mimi, a great email marketing company, and Gravity Forms.
* <a href="http://wordpress.org/extend/plugins/gravity-forms-exacttarget/">Gravity Forms ExactTarget Add-on</a> - Integrate with ExactTarget, an enterprise-class email marketing service

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

= How do I select what columns I want to display in the Directory? =

1. Edit the Gravity Forms form you'd like to configure a Directory for
1. Click "Directory Columns" In the Form Editor toolbar (near the top-center of the page)
1. Drag & drop the fields in the order you'd like them to appear in the directory
	* Drag from the right ("Hidden Columns") side to the left ("Visible Columns") side.
1. Click the "Save" button
1. Voila!

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

== Changelog ==

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
* Fixed issue where entries appeared not to be approving properly in the admin. (<a href="http://www.seodenver.com/forums/topic/edits-to-entries-not-visible-in-directory-view/">Reported on the Support Forum</a>)

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
	* Fixed header code 404 when viewing entries in lightbox mode (Support topics <a href="http://www.seodenver.com/forums/topic/intermittent-404s-in-ie-when-clicking-entry-detail-firefox-ok/">here</a> and <a href="http://www.seodenver.com/forums/topic/lightbox-error/">here</a>)
	* Front-end editing of certain types of input types failed because `GFFormDisplay` class wasn't defined.
	* Fixed <a href="http://www.seodenver.com/forums/topic/intermittent-404s-in-ie-when-clicking-entry-detail-firefox-ok/">issue</a> where scripts were not always printing in the `<head>`
	* Fixed fatal error when outputting `date_created` field (<a href="http://wordpress.org/support/topic/649652">issue #649652</a>)

= 3.1.1 =
* Fixes issue where entries not showing on sort (<a href="http://www.seodenver.com/forums/topic/entries-not-showing-on-sort/">as reported in the plugin support forums</a>)

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
* HTML generation fix: `<liclass` now `<li class` (<a href="http://www.seodenver.com/gravity-forms-addons/#dsq-comment-header-193118389">thanks @lolawson</a>)
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
* Fixed issue where entries appeared not to be approving properly in the admin. (<a href="http://www.seodenver.com/forums/topic/edits-to-entries-not-visible-in-directory-view/">Reported on the Support Forum</a>)

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
* Fixes issue where entries not showing on sort (<a href="http://www.seodenver.com/forums/topic/entries-not-showing-on-sort/">as reported in the plugin support forums</a>) - NoteL this only affected users using versions of Gravity Forms older than 1.6.

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
* HTML generation fix: `<liclass` now `<li class` (<a href="http://www.seodenver.com/gravity-forms-addons/#dsq-comment-header-193118389">thanks @lolawson</a>)
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