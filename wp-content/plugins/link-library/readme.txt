=== Link Library ===
Contributors: jackdewey
Donate link: https://ylefebvre.home.blog/wordpress-plugins/link-library/
Tags: link, list, directory, page, library, AJAX, RSS, feeds, inline, search, paging, add, submit, import, batch, pop-up
Requires at least: 4.4
Tested up to: 5.6
Stable tag: trunk

The purpose of this plugin is to add the ability to output a list of link categories and a complete list of links with notes and descriptions.

== Description ==

This plugin is used to be able to create a page on your web site that will contain a list of all of the link categories that you have defined inside of the Links section of the Wordpress administration, along with all links defined in these categories. The user can select a sub-set of categories to be displayed or not displayed. Link Library also offers a mode where only one category is shown at a time, using AJAX or HTML Get queries to load other categories based on user input. It can display a search box and find results based on queries. It can also display a form to accept user submissions and allow the site administrator to moderate them before listing the new entries. Finally, it can generate an RSS feed for your link collection so that people can be aware of additions to your link library.

For links that carry RSS feed information, Link Library can display a preview of the latest feed items inline with the all links or in a separate preview window.

This plugin uses the filter method to add contents to the pages. It also contains a configuration page under the admin tools to be able to configure all outputs. This page allows for an unlimited number of different configurations to be created to display links on different pages of a Wordpress site.

For screenshots showing how to achieve these results, check out my [site](https://ylefebvre.home.blog/wordpress-plugins/link-library/link-library-faq/)

All pages are generated using different configurations all managed by Link Library. Link Library is compatible with the [My Link Order](http://wordpress.org/extend/plugins/my-link-order/) plugin to define category and link ordering.

* [Changelog](http://wordpress.org/extend/plugins/link-library/other_notes/)
* [Support Forum](http://wordpress.org/tags/link-library)

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

= 6.7.12 =
* Fix bug in generation of target field for web link

= 6.7.11 =
* Add support for lazy loading images

= 6.7.10 =
* Fix to avoid permalinks / rewrite issues on some installations

= 6.7.9 =
* Fixed issues with quotes in HTML fields for custom lists
* Fixed issue with custom text fields

= 6.7.8 =
* Fix for some users experiencing issues with AJAX mode. Extra line breaks were being output on some configurations
* Added custom text fields and custom list fields

= 6.7.7 =
* Reduced the transient time for form submissions to 5 seconds instead of 60
* Fixed warning in category display
* User submission message is now transmitted in transient expiring after 10 seconds

= 6.7.6 =
* Added new options to linkorderoverride parameter when calling [link-library] shortcode to reflect list of choices in interface
* Cleared some PHP warnings related to recent changes
* Fix for show not cat on startup in AJAX mode not working
* Modified link hits logic to disregard visits from administrators to links

= 6.7.5 =
* Fixed to support new option to allow categories to be used to refine search results when displayed in more configurations

= 6.7.4 =
* Fixed warning in admin when activatinguser voting
* Changed mechanism for user-submitted links to repost info if anything is missing or wrong. Now using transients.
* Fixed issues with escaped characters when users submit new links

= 6.7.3 =
* Fixed issues with user-submission form

= 6.7.2 =
* Added new option to allow categories to be used to refine search results when displayed
* Fixed issues with user-submission form

= 6.7.1 =
* Fixed issue with user vote custom label not staying when you upvote links

= 6.7 =
* Added update message for new version
* Removed Accessibe banner ads and moved Accessibe menu to bottom of list
* Fixed issue with New tag displayed before link name not working correctly if link name is not the first field or is in a table

= 6.6.12 =
* Fixed bug with image link not displayed at the right place with new dedicated page option

= 6.6.11 =
* Added two options to link image display. Can now link to dedicated page or not link to any page

= 6.6.10 =
* Added ability to set label of user vote button
* Fixed bugs with user voting system
* When activating user voting system, all existing links will be assigned an initial vote value of 0

= 6.6.9 =
* First implementation of user voting system. New block in Advanced page for User Votes. Ability to sort links by number of user votes. Open to user feedback
* Added new option to display link names only, not as link. Set under source for Link Name field.

= 6.6.8 =
* Added new section under General Options to add custom URL fields to Link Library. Fields can be enabled and named to appear in all relevant places (Link editor, advanced configuration table for links)
* Removed debug code from user submission code to avoid issues with headers submitted too early

= 6.6.7 =
* Revamped user submission section of library configuration to implement table approach
* Added code to fortify LL core
* Added stylesheet rules to make user-submission form look better on mobile
* Added option to delete media attachment or local site file assigned as link URL

= 6.6.6 =
* Added multi select lists in the Moderation screen to allow users to assign one or more tags or categories to user-submitted links, or make changes to the ones assigned by users
* Added ability to upload a file for the link to point to instead of specifying a URL
* Added new option to [link-library-addlink] shortcode to override default category in category list (addlinkdefaultcatoverride)

= 6.6.5 =
* Added button to open media library dialog for link URL selection. Allows you to upload a media item and easily link to it

= 6.6.4 =
* Fixed issue with broken link checker only reporting first error

= 6.6.3 =
* Renamed ll_write_log function to linklibrary_write_log function to avoid potential conflicts with other plugins.

= 6.6.2 =
* Improved broken link checked to identify redirections to new URLs

= 6.6.1 =
* Added new option field for user submission form to specify tooltips to be displayed when user hovers over the fields
* Fixed issue with export all links function not working if no tags are assigned
* Fixed issue with some form fields not being re-displayed is captcha is wrong
* Fixed issue with form validation not working with description field is set to required

= 6.6 =
* Fixed editor issue in WP 5.5

== Frequently Asked Questions ==

= Where can I find documentation for Link Library? =

Visit the [official documentation for Link Library](https://ylefebvre.home.blog/wordpress-plugins/link-library/link-library-faq/)

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
