=== Table of Contents Plus ===
Contributors: aioseo, smub, benjaminprojas
Tags: table of contents, indexes, toc, sitemap, cms
Requires at least: 3.2
Tested up to: 6.7
Stable tag: 2411.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.


== Description ==

A powerful yet user friendly plugin that automatically creates a context specific index or table of contents (TOC) for long pages (and custom post types).  More than just a table of contents plugin, this plugin can also output a sitemap listing pages and/or categories across your entire site.

Built from the ground up and with Wikipedia in mind, the table of contents by default appears before the first heading on a page.  This allows the author to insert lead-in content that may summarise or introduce the rest of the page.  It also uses a unique numbering scheme that doesn't get lost through CSS differences across themes.

This plugin is a great companion for content rich sites such as content management system oriented configurations.  That said, bloggers also have the same benefits when writing long structured articles.

Includes an administration options panel where you can customise settings like display position, define the minimum number of headings before an index is displayed, other appearance, and more.  For power users, expand the advanced options to further tweak its behaviour - eg: exclude undesired heading levels like h5 and h6 from being included; disable the output of the included CSS file; adjust the top offset and more.  Using shortcodes, you can override default behaviour such as special exclusions on a specific page or even to hide the table of contents altogether.

Prefer to include the index in the sidebar?  Go to Appearance > Widgets and drag the TOC+ to your desired sidebar and position.

Custom post types are supported, however, auto insertion works only when the_content() has been used by the custom post type.  Each post type will appear in the options panel, so enable the ones you want.

Collaborate, participate, fork this plugin on [Github](https://github.com/zedzedzed/table-of-contents-plus/).


== Screenshots ==

1. An example of the table of contents, positioned at the top, right aligned, and a width of 275px
2. An example of the sitemap_pages shortcode
3. An example of the sitemap_posts shortcode
4. The options panel found in Settings > TOC+
5. Some advanced options
6. The sitemap tab


== Installation ==

The normal plugin install process applies, that is search for `table of contents plus` from your plugin screen or via the manual method:

1. Upload the `table-of-contents-plus` folder into your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

That's it!  The table of contents will appear on pages with at least four or more headings.

You can change the default settings and more under Settings > TOC+


== Shortcodes ==
The plugin was designed to be as seamless and painfree as possible and did not require you to insert a shortcode for operation.  However, using the shortcode allows you to fully control the position of the table of contents within your page.  The following shortcodes are available with this plugin.

When attributes are left out for the shortcodes below, they will fallback to the settings you defined under Settings > TOC+.  The following are detailed in the help tab.

= [toc] =
Lets you generate the table of contents at the preferred position.  Useful for sites that only require a TOC on a small handful of pages.  Supports the following attributes:

* "label": text, title of the table of contents
* "no_label": true/false, shows or hides the title
* "wrapping": text, either "left" or "right"
* "heading_levels": numbers, this lets you select the heading levels you want included in the table of contents.  Separate multiple levels with a comma.  Example: include headings 3, 4 and 5 but exclude the others with `heading_levels="3,4,5"`
* "class": text, enter CSS classes to be added to the container. Separate multiple classes with a space.
* "start": number, show when this number of headings are present in the content.

= [no_toc] =
Allows you to disable the table of contents for the current post, page, or custom post type.

= [sitemap] =
Produces a listing of all pages and categories for your site. You can use this on any post, page or even in a text widget.  Note that this will not include an index of posts so use sitemap_posts if you need this listing.

= [sitemap_pages] =
Lets you print out a listing of only pages. The following attributes are accepted:

* "heading": number between 1 and 6, defines which html heading to use
* "label": text, title of the list
* "no_label": true/false, shows or hides the list heading
* "exclude": IDs of the pages or categories you wish to exclude
* "exclude_tree": ID of the page or category you wish to exclude including its all descendants
* "child_of": "current" or page ID of the parent page. Defaults to 0 which includes all pages.

= [sitemap_categories] =
Same as `[sitemap_pages]` but for categories.

= [sitemap_posts] =
This lets you print out an index of all published posts on your site.  By default, posts are listed in alphabetical order grouped by their first letters.  The following attributes are accepted:

* "order": text, either ASC or DESC
* "orderby": text, popular options include "title", "date", "ID", and "rand". See [WP_Query](https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters) for a list.
* "separate": true/false (defaults to true), does not separate the lists by first letter when set to false.

Use the following CSS classes to customise the appearance of your listing:

* toc_sitemap_posts_section
* toc_sitemap_posts_letter
* toc_sitemap_posts_list


== Credits ==
This plugin was created and maintained for many years by conjur3r. We are grateful for all the hard work he put in and we are excited to continue to build on that!


== Changelog ==
= 2411.1 =
* Released: 21 November 2024
* Security hardening reported by WPScan

= 2411 =
* Released: 14 November 2024
* Security hardening reported by Patchstack
* Plugin updates for compatibility with Plugin Check

= 2408 =
* Released: 14 August 2024
* Redo XSS issue reported by wpscan

= 2406 =
* Released: 16 June 2024
* Revert 'Do not output CSS/JS on pages not eligible' introduced in 2402 as it broke some clever edge cases
* Fixed XSS issue requiring editor or higher privileges for show/hide text (thanks to wpscan)

= 2402.1 =
* Released: 22 February 2024
* Fixed check for shortcode use (#164)

= 2402 =
* Released: 21 February 2024
* Added option to generate TOC in REST requests, disabled by default (props ballpumpe)
* Composer improvements (props mohjak)
* Do not output CSS/JS on pages not eligible
* Update POT translation file
* Update links to help

= 2311 =
* Released: 7 November 2023
* Bump tested WordPress version to 6.4
* Do not trigger on REST requests (props steffenster)
* Update include mechanism in init.php (props maciejmackowiak)

= 2309 =
* Released: 19 September 2023
* Bump tested version to 6.3.1
* Added `child_of` property to sitemap_pages shortcode (props flagsoft). This lets you output a listing of child pages for a set parent or "current". Property is optional and defaults to all pages. 
* Fixed XSS possibility handling nonce while saving options (thanks to Patchstack)
* Updated WordPress Coding Standard to 3.0

= 2302 =
* Released: 9 February 2023
* Added `toc_brackets` CSS class to square brackets around show/hide text
* Added a filter to the `toc_extract_headings` function (props Raymond Radet)
* Refactor using WordPress-Extra coding standard

= 2212 =
* Released: 16 December 2022
* Bump tested version to 6.1.1
* When using the TOC+ widget, execute shortcodes beforehand (props endcoreCL)
* When using the TOC+ widget, abort early when no post for edge cases (props jonas-hoebenreich)
* Add start property to toc shortcode to override the minimum number of headings needed to display (props woutervanvliet)
* Add no_numbers property to toc shortcode to disable leading heading indexes (props TedAvery)
* Fixed XSS vulnerability in toc shortcode, class property (thanks to wpscan)
* Fixed XSS vulnerabilities in sitemap_pages and sitemap_categories shortcodes, label property

= 2106 =
* Released: 23 June 2021
* Add compatibility with Rank Math SEO
* Bump tested WordPress version to 5.7
* Add PHP coding style
* Adhere to majority of coding tips

= 2002 =
* Released: 9 February 2020
* Fixed encoding when using %PAGE_TITLE% or %PAGE_NAME%
* Bump tested WordPress version to 5.3
* Removed all local translations as you can find more up to date ones at translate.wordpress.org
* Removed translators links from readme

= 1601 =
* Released: 5 January 2016
* Bump tested WordPress version to 4.4
* Add 'enable' and 'disable' API functions so a developer can better control the execution.
* Add Brazilian Portuguese translation thanks to Blog de Niterói
* Add Spanish translation thanks to David Saiz
* TOC+ widget now adheres to a blank title if none provided. Thanks to [Dirk](http://dublue.com/plugins/toc/comment-page-11/#comment-5140) for the cue.
* Updated jQuery Smooth Scroll 1.5.5 to 1.6.0
* Updated text domain to better support translation packs.

= 1509 =
* Released: 4 September 2015
* Added Hebrew translation thanks to Ahrale
* Added Japaense translation thanks to シカマル
* Added Greek translation thanks to Dimitrios Kaisaris
* Updated jQuery Smooth Scroll 1.4.10 to 1.5.5
* Supply both minified and unminified CSS and JS files, use minified versions.
* Convert accented characters to ASCII in anchors.
* Bump tested WordPress version to 4.3
* Fixed: PHP notice introduced in WP 4.3
* Fixed: javascript error with $.browser testing for Internet Explorer 7.
* Plugin has moved to [GitHub](https://github.com/zedzedzed/table-of-contents-plus/) for better collaboration.
* Help needed: preg_match_all failing with bad UTF8 characters producing no TOC. If you can help, please participate in this [issue](https://github.com/zedzedzed/table-of-contents-plus/issues/105).

= 1507 =
* Released: 5 July 2015
* Added Danish translation courtesy of Cupunu
* Simplified the translation duty by moving the help material to the plugin's website.
* Updated translation file.

= 1505 =
* Released: 2 May 2015
* Huge thanks to Jason for an updated Simplified Chinese translation.
* Added collapse property to the toc shortcode.  When set to true, this will hide the table of contents when it loads.  Example usage: [toc collapse="true"]
* Added label_show and label_hide properties to the toc shortcode.  This lets you change the "show" and "hide" link text when using the shortcode.
* Bump tested WordPress version to 4.2.1.

= 1408 =
* Released: 1 August 2014
* Added a human German translation courtesy Ben
* Added "class" attribute to the TOC shortcode to allow for custom CSS classes to be added to the container.  Thanks to Joe for [suggesting it](http://dublue.com/plugins/toc/comment-page-7/#comment-2803)

= 1407 =
* Released: 5 July 2014
* Added Ukrainian translation courtesy Michael Yunat
* Added French translation courtesy Jean-Michel Duriez
* Empty headings are now ignored, as suggested by [archon810](http://wordpress.org/support/topic/patch-ignore-empty-tags)
* Removed German translation, may have been machine translated, [ref](http://wordpress.org/support/topic/excluding-headlines-special-characters)
* Fixed: Special chars in TOC+ > Settings > Exclude Headings no longer get mangled on save.  Thanks to N-Z for [reporting it](http://wordpress.org/support/topic/excluding-headlines-special-characters).

= 1404 =
* Released: 18 April 2014
* Bump WordPress support to 3.9
* Fixed: Strip HTML tags from post titles for sitemap_posts so those items do not appear under a < heading. Thanks to [Rose](http://dublue.com/plugins/toc/comment-page-6/#comment-2311) for reporting it.
* Fictitious: This release was powered by three blind mice.

= 1402 =
* Released: 19 February 2014
* Added German translation courtesy Cord Peter
* Modify toc_get_index API function to also reset minimum number of headings to 0.
* Removing the TOC+ widget from the sidebar no longer requires you to uncheck the 'Show the table of contents only in the sidebar' option. It will be reset on removal.
* Delay count of headings until disqualified have been removed. Thanks to [Simone di Saintjust](http://dublue.com/plugins/toc/comment-page-6/#comment-2190) for raising it.
* Using the TOC+ widget, you can now limit the display to selected post types. Thanks to [Pete Markovic](http://dublue.com/plugins/toc/comment-page-6/#comment-2248) for the idea.
* Updated translation file (extra options added).

= 1311 =
* Released: 10 November 2013
* Added third parameter to toc_get_index API function to enable eligibility check (eg apply minimum heading check, is post type enabled, etc). This has been switched off by default and only affects those using the API. Thanks [Jonon](http://dublue.com/plugins/toc/comment-page-5/#comment-1943) for your comment.
* Added Dutch translation courtesy Renee
* Apply bullet option to TOC+ widget, thanks to [Thomas Pani for the patch](http://dublue.com/plugins/toc/comment-page-5/#comment-2040).

= 1308 =
* Released: 5 August 2013
* Fix javascript issue with minimum jQuery version check (broke smooth scrolling using WordPress 3.6).
* Replaced Slovak translation with a human translated version courtesy Boris Gereg.
* Remove <!--TOC--> signature from source when using the shortcode but not allowed to print (eg on homepage).
* Add "separate" attribute for sitemap_posts shortcode to not split by letter, thanks [DavidMjps](http://wordpress.org/support/topic/exclude-alphabetical-headings-on-sitemap) for the suggestion.

= 1303.1 =
* Released: 22 March 2013
* New: added Polish translation, curtesy Jakub
* Fixed: an issue in 1303 that ignored headings with the opening tag on the first line and the heading text on a new line.  Thanks to [richardsng](http://wordpress.org/support/topic/unable-to-display-the-full-toc) for the quick discovery.

== Upgrade Notice ==

Update folder with the latest files.  All previous options will be saved.