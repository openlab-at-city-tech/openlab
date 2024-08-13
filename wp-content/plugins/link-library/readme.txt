=== Link Library ===
Contributors: jackdewey
Donate link: https://ylefebvre.github.io/wordpress-plugins/link-library/
Tags: link, list, directory, page, library
Requires at least: 4.4
Tested up to: 6.5.3
Stable tag: 7.7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The purpose of this plugin is to add the ability to output a list of link categories and a complete list of links with notes and descriptions.

== Description ==

This plugin is used to be able to create a page on your web site that will contain a list of all of the link categories that you have defined inside of the Links section of the Wordpress administration, along with all links defined in these categories. The user can select a sub-set of categories to be displayed or not displayed. Link Library also offers a mode where only one category is shown at a time, using AJAX or HTML Get queries to load other categories based on user input. It can display a search box and find results based on queries. It can also display a form to accept user submissions and allow the site administrator to moderate them before listing the new entries. Finally, it can generate an RSS feed for your link collection so that people can be aware of additions to your link library.

You can try it out in a temporary copy of WordPress [here](https://demo.tastewp.com/link-library).

For links that carry RSS feed information, Link Library can display a preview of the latest feed items inline with the all links or in a separate preview window.

This plugin uses the filter method to add contents to the pages. It also contains a configuration page under the admin tools to be able to configure all outputs. This page allows for an unlimited number of different configurations to be created to display links on different pages of a Wordpress site.

For screenshots showing how to achieve these results, check out my [site](https://github.com/ylefebvre/link-library/wiki)

All pages are generated using different configurations all managed by Link Library. Link Library is compatible with the [Simple Custom Post Order](https://en-ca.wordpress.org/plugins/simple-custom-post-order/) plugin to define category and link ordering.

* [Changelog](http://wordpress.org/extend/plugins/link-library/other_notes/)
* [Support Forum](https://wordpress.org/support/plugin/link-library/)

== Installation ==

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin

To get a basic Link Library list showing on one of your Wordpress pages:<br />
1. In the Wordpress Admin, create a new page and type the following text, where # should be replaced by the Settings Set number:<br />
   [link-library settings=#]

1. To add a list of categories to jump to a certain point in the list, add the following text to your page:<br />
   [link-library-cats settings=#]<br />

1. To add a search box to your Link Library list, add the following text to your page:<br />
   [link-library-search]

1. To add a form for users to be able to submit new links:<br />
   [link-library-addlink settings=#]

In addition to specifying a library, categories to be displayed can be specified using addition keywords. Read the FAQ for more information on this topic.

Further configuration is available under the Link Library Settings panel.

== Changelog ==

= 7.7.2 =
* Fixed potential security issue

= 7.7.1 =
* Added way to export all links to OPML format under Global Options

= 7.7 =
* Fixes for potential security issues

= 7.6.11 =
* Fix to avoid PHP warning

= 7.6.10 =
* Fixed to previous checkin to restore compabitility with older versions of WordPress

= 7.6.9 =
* Added check to see if current page is login page and exit plugin if it is

= 7.6.8 =
* Fix to retain comments in Stylesheet editor

= 7.6.7 =
* Fix for potential security issue
* Fix for large description field in link editor showing bad HTML
* Fix for taglistoverride not working when pagination is enabled

= 7.6.6 =
* Fixes to restore user link submission capabilities

= 7.6.5 =
* Link editor now automatically shows most used tag list

= 7.6.4 =
* Fixes for potential security vulnerabilities
* Admin notices now only displayed on Link Library pages
* Potential fix for issue with categorylistoverride introduced with last version

= 7.6.3 =
* Fix to allow categorylistoverride to work correctly when library is configured to show a single library at a time

= 7.6.2 =
* Added new function to only show link with specific text as part of their URL

= 7.6.1 =
* Fixes for potential security vulnerabilities

= 7.6 =
* Fixes for potential security vulnerabilities

= 7.5.13 =
* Fix for hierarchical list of categories in add link form

= 7.5.12 =
* Added missing parameters for iframe white listing

= 7.5.11 =
* Added iframe to white list of HTML tags for configuration options
* Enabled embed shortcodes inside of large description fields

= 7.5.10 =
* Added option to suppress linklist div from output

= 7.5.9 =
* Fixed admin stylesheet to avoid conflict with default editor

= 7.5.8 =
* Fix to allow WordPress embed shortcodes to work when used in full-page content

= 7.5.7 =
* Correction for PHP warnings when visiting admin pages

= 7.5.6 =
* Correction for PHP warnings around $excludetagoverride

= 7.5.5 =
* Correction to allow table tags in advanced configuration table

= 7.5.4 =
* Correction to allow HTML tags in Single Item Layout editor

= 7.5.3 =
* Link Library now respects categorylistoverride and excludecategoryoverride when displaying results with "Combine all results without categories" option activated

= 7.5.2 =
* Added query variable to show upgrade tools for legacy 6.0 users migrating to current versions of the plugin

= 7.5.1 =
* Renamed one of the column of category CSV export to match import template

= 7.5 =
* Add ability to import list of categories from a CSV file

= 7.4.19 =
* Translation update

= 7.4.18 =
* Restored plugin book section

= 7.4.17 =
* Added option to schedule automatic generation of missing thumbnails

= 7.4.16 =
* Added new configuration section under Global Options > Import/Export Links to be able to schedule automated import of links
* Added missing files for pop-dialog functionality introduced in 7.4.15
* Modified link search when displaying categories to search in content and not only link title
* Removed some leftover debug functions

= 7.4.15 =
* Added new mode for user link submission form to only display button and have form appear in pop-up dialog

= 7.4.14 =
* Added missing / characters when displaying list of categories in permalink mode and in breadcrumbs

= 7.4.13 =
* Added new option to specify page containing category list when using HTML Get + Permalinks switching method with breadcrumbs

= 7.4.12 =
* Update to allow for translation of text that was previously hard-coded
* Updated french translation

= 7.4.11 =
* Modified form validator script to allow for empty e-mail addresses in user link submission form if field set to Show and not Required

= 7.4.10 =
* Fix to allow non-admin users to be able to see edit links on Link Library visitor-facing pages

= 7.4.9 =
* Added new display mode for category list ([link-library-cats]) called Simple Divs

= 7.4.8 =
* Added option to [link-library-filters] shortcode to display apply button (showapplybutton)

= 7.4.7 =
* Fix for display of hierarchical categories in [link-library-cats] shortcode

= 7.4.6 =
* Relaxed HTMl tags parsing for custom field before and after content

= 7.4.5 =
* Further refinement of accepted HTML tags for advanced configuration table

= 7.4.4 =
* Fixes for warnings in usersubmission.php

= 7.4.3 =
* Further refinement of accepted HTML tags for advanced configuration table

= 7.4.2 =
* Implemented some accessibility functions in tag filter
* Fixed advanced table to accept HTML values once again

= 7.4.1 =
* Security fixes
* Added support for WPGraphQL

= 7.4 =
* Corrected PHP warning in render-link-library-addlink-sc.php

= 7.3.21 =
* Fix for AJAX category switching when using New editor on some themes
* Additional fixes for [link-library-cats] shortcode for sites with hierarchical categories

= 7.3.20 =
* Fixes for [link-library-cats] shortcode for sites with hierarchical categories

= 7.3.19 =
* Fixed additional french translations
* Changed colors of Submit and Reset buttons in stylesheet editor
* Added new option under search tab configuration to suppress output if no results are found

= 7.3.18 =
* Fixed issue introduced in 7.3.16

= 7.3.17 =
* Modified to allow excludecategoryoverride to work with sub-categories in [link-library-cat] shortcode

= 7.3.16 =
* Added parameter to [link-library-cats] shortcode called parent_cat_id. Should be set to empty "" if using categorylistoverride and only specifying sub-categories
* Corrected some errors in French translation

= 7.3.15 =
* Corrected error message when running empty cat link checker and none are found

= 7.3.14 =
* Added two new types of link checking tools: Check Secondary Links and Check image links

= 7.3.13 =
* Added new tool under Global Options > Import/Export to export list of categories with IDs

= 7.3.12 =
* Add general option to show excerpt section in link editor

= 7.3.11 =
* Fix to allow single quotes in empty search results message field

= 7.3.10 =
* Fix for error when displaying part of the Global Options section
* Updated promotional section in Global Options page with newest book published

= 7.3.9 =
* Additional improvements to link creation page display on mobile devices

= 7.3.8 =
* Brought back mechanism to help with large imports from pre-6.0 versions to current revisions.

= 7.3.7 =
* Improved display of link creation page on mobile devices

= 7.3.6 =
* Fix for item sorting with publication date

= 7.3.5 =
* Additional fixes for featured item sorting

= 7.3.4 =
* Fixed problem with quotes getting escaped in library-specific stylesheet editor

= 7.3.3 =
* Fixed problem with featured links not longer appearing ahead of other link when ordering by title and having specified articles to be ignored

= 7.3.2 =
* Fixed problems with new option to ignore specific articles introduced in version 7.3 beta 2

= 7.3.1 =
* Added extra field to Link Library widget to allow users to select category(ies) to be displayed

= 7.3 =
* Official 7.3 version containing all features from beta 1 to 4

= 7.3 Beta 4 =
* Added CSS rules for new visibility toggle buttons

= 7.3 Beta 3 =
* Added new option when displaying categories as visibility toggles to add show all and hide all buttons

= 7.3 Beta 2 =
* Added new global option to specify articles to be ignored when sorting links by title

= 7.3 Beta 1 =
* Added support to use post categories instead of Link Library categories
* Added support to use post tags instead of Link Library tags

= 7.2.9 =
* Fixed additional potential security issues

= 7.2.8 =
* Fixed potential security issues in plugin admin section

= 7.2.7 =
* Increased character limit for user form fields from 255 to 1024 characters

= 7.2.6 =
* Further fixes for hide donation and display of new feature box

= 7.2.5 =
* Fixes around Hide donation option and display of new feature box

= 7.2.4 =
* Added new option in search section to look for results in all categories even if library is configured to display from a subset of categories
* Fix block_categories editor warnings for deprecated filters

= 7.2.3 =
* Fixed issue with displaying library count for library other than #1

= 7.2.2 =
* Fix for list of categories in user-submission form when using hierarchical categories
* Fix for PHP warning when displaying link count

= 7.2.1 =
* Add support for WordPress mshots thumbnail generator

= 7.2.0 =
* Links can now be sorted by number of hits in admin

= 7.1.9 =
* Added new options to only display links that are updated/new and to specify for how many days links should be considered as updated/new

= 7.1.8 =
* Fix to only make clear: both div display after categories when rendering category list using block editor

= 7.1.7 = 
* Fix to make RSS feed only accessible when option to publish it is actually checked

= 7.1.6 =
* Fixed for RSS Feed Checking tool

= 7.1.5 =
* Added new RSS Feed checking tool under Link Checking Tools

= 7.1.4 =
* Remove current page number link from pagination

= 7.1.3 =
* Additional fix for updated tag
* Fix for pagination system

= 7.1.2 =
* Changed updated tag to use publication date to determine if it's displayed

= 7.1.1 =
* Fix in Link Library Broken Link Checker
* Fix for pagination links in HTML GET + Permalink mode

= 7.1.0 =
* Added new [rss-library] shortcode to display a combined RSS library feed from all links
* Enhanced bookmarket to grab selected text as new link description as well as get RSS feed link. If more than one link is found, asks the user which one to use.
* Improved breadcrumb generation code so that the last part of the breakcrumb does not have a link. Also fixed links for sub-categories.
* Fix for PHP installations that don't support short array syntax

= 7.0.8 =
* Add support for taglistoverride parameter in link-library-cats shortcode
* Add parameter to override tag list in Link Library Category block

= 7.0.7 =
* Fix to avoid masonry layout becoming a single column when first item is hidden
* Added a new option to Categories tab in Library Configuration to hide empty categories from category list

= 7.0.6 =
* Fixed [link-library-cats] shortcode to react to tag selection
* Fix for duplicate link checker where it was previous showing items from other post types with same name as links

= 7.0.5 =
* Improved broken link checker to report on the type of redirection that took place

= 7.0.4 =
* Added option to include links in main site RSS feed
* Added option in RSS feed configuration to select displaying the link updated date value or the publication date

= 7.0.3 =
* Corrected problem with duplicate detection when using bookmarklet

= 7.0.2 =
* Added labels in user-submission form for accessibility
* Removed unnecessary file

= 7.0.1 =
* Fix to avoid duplicate media library entries for link images
* Fixed issue with pagination when filtering by link tags
* Fix to show sub-categories in user-form when no top-level categories are selected
* Bump up copyright version
* Add missing styling for block editor warning box
* Fix issue where link tags not displayed if option to suppress empty output is selected

= 7.0 =
* New admin icon for Link Library using dashicons
* Fix for better match of duplicate links entered through bookmarklet
* Updated new feature pop-up

== Frequently Asked Questions ==

= Where can I find documentation for Link Library? =

Visit the [official documentation for Link Library](https://github.com/ylefebvre/link-library/wiki)

= Who are the translators behind Link Library? =

* French Translation courtesy of Luc Capronnier
* Danish Translation courtesy of [GeorgWP](http://wordpress.blogos.dk)
* Italian Translation courtesy of Gianni Diurno
* Serbian Translation courtesy of [Ogi Djuraskovic, firstsiteguide.com](http://firstsiteguide.com)

= Where do I find my category IDs to place in the "Categories to be Displayed" and "Categories to be Excluded" fields? =

The category IDs are numeric IDs. You can find them by going to the page to see and edit link categories, then placing your mouse over a category and seeing its numeric ID in the link that is associated with that name.

= How can I display different categories on different pages? =

If you want all of your link pages to have the same layout, create a single setting set, then specify the category to be displayed when you add the short code to each page. For example: [link-library categorylistoverride="28"]
If the different pages have different styles for different categories, then you should create distinct setting sets for each page and set the categories to be displayed in the "Categories to be Displayed" field in the admin panel.

= After assigning a Link Acknowledgement URL, why do links no longer get added to my database? =

When using this option, the short code [link-library-addlinkcustommsg] should be placed on the destination page.

= How can I override some of the options when using shortcodes in my pages =

To override the settings specified inside of the plugin settings page, the two commands can be called with options. Here is the syntax to call these options:

[link-library-cats categorylistoverride="28"]

Overrides the list of categories to be displayed in the category list

[link-library-cats excludecategoryoverride="28"]

Overrides the list of categories to be excluded in the category list

[link-library categorylistoverride="28"]

Overrides the list of categories to be displayed in the link list

[link-library excludecategoryoverride="28"]

Overrides the list of categories to be excluded in the link list

[link-library notesoverride=0]

Set to 0 or 1 to display or not display link notes

[link-library descoverride=0]

Set to 0 or 1 to display or not display link descriptions

[link-library rssoverride=0]

Set to 0 or 1 to display or not display rss information

[link-library tableoverride=0]

Set to 0 or 1 to display links in an unordered list or a table.

= Can Link Library be used as before by calling PHP functions? =

For legacy users of Link Library (pre-1.0), it is still possible to call the back-end functions of the plugin from PHP code to display the contents of your library directly from a page template.

The main differences are that the function names have been changed to reflect the plugin name. However, the parameters are compatible with the previous function, with a few additions having been made. Also, it is important to note that the function does not output the Link Library content by themselves as they did. You now need to print the return value of these functions, which can be simply done with the echo command. Finally, it is possible to call these PHP functions with a single argument ('AdminSettings1', 'AdminSettings2', 'AdminSettings3', 'AdminSettings4' or 'AdminSettings5') so that the settings defined in the Admin section are used.

Here would be the installation procedure:

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin
1. Use the following functions in a [new template](http://codex.wordpress.org/Pages#Page_Templates) and select this template for your page that should display your Link Library.

`&lt;?php echo $my_link_library_plugin->LinkLibraryCategories('name', 1, 100, 3, 1, 0, '', '', '', false, '', ''); ?&gt;<br />
`&lt;br /&gt;<br />
&lt;?php echo $my_link_library_plugin->LinkLibrary('name', 1, 1, 1, 1, 0, 0, '', 0, 0, 1, 1, '&lt;td>', '&lt;/td&gt;', 1, '', '&lt;tr&gt;', '&lt;/tr&gt;', '&lt;td&gt;', '&lt;/td&gt;', 1, '&lt;td&gt;', '&lt;/td&gt;', 1, "Application", "Description", "Similar to", 1, '', '', '', false, 'linklistcatname', false, 0, null, null, null, false, false, false, false, '', ''); ?&gt;

== Screenshots ==

1. The Settings Panel used to configure the output of Link Library
2. A sample output page, displaying a list of categories and the links for all categories in a table form.
2. A second sample output showing a list of links with RSS feed icons and RSS preview link.
