=== Name Directory ===
Contributors: jeroenpeters1986, mpmarinov, mastababa
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://ko-fi.com/jeroenpeters
Tags: glossary, dictionary, index, directory, names
Requires at least: 3.0.1
Requires PHP: 5.3
Tested up to: 6.7
Stable tag: 1.29.1

Name directory (glossary) with many options like multiple directories, integrated search, non-latin characters, recaptcha, HTML editor and many more.

== Description ==

### Name Directory: Build your own glossary!

This plugin adds name/term directories (glossaries) to your WordPress installation.
The output on your website is like a glossary/index, including a search function.
This plugin supports **multiple directories** in one WordPress installation.
I recommend you to take a look at the screenshots, they illustrate more than words.

The Name Directory plugin was originally developed for [ParkietenVilla.nl](https://www.parkietenvilla.nl/namenlijst/) to show a directory of names to name your budgies.

#### Create multiple directories and customize them!

You can create multiple directories with this plugin.
Every directory can be embedded with a very simple shortcode which you can just copy-and-paste
in your own pages and posts. Every directory has a few configuration options that customize
the layout and functionality of the directory:

 - Show/Hide title
 - Show/Hide description
 - Enter the subject of the directory (i.e.: movies, birds, countries, names)
 - Show/Hide suggestion form
 - Show/Hide submitter name
 - Show/Hide search function (searches names/titles and description)
 - Show/Hide a horizontal rule between the entries
 - Show/Hide all entries when the user has not chosen an index-letter yet
 - Show/Hide the newest entries (and choose an amount of newest entries to show)
 - Choose the amount of columns to display
 - Whether to jump to the name directory when a visitor is using the search box (for onepage websites)
 - When you embed a directory, you can configure it to start with a letter of your choosing. E.g.: start on letter J.
 - You can limit the amount of words in the description (and display a "Read more" link which reveals the rest of the text

The administration view of this plugin has the familiar look and feel of (the rest of) the WordPress Administration panel.
I have done my best to enable some AJAX-features in the administration panel, so you can work efficiently while adding new entries.

Since v1.7 and v1.8, simple import and export functionality is also supported through .csv-files.

This plugin is also tested compatible with:
 * the popular [Members plugin](https://wordpress.org/plugins/members/) which makes role permissions easy
 * [Relevanssi plugin](https://wordpress.org/plugins/relevanssi/), the better search plugin

#### Try the plugin without installing it yourself.
Do you want to try Name Directory instantly? Thanks to TasteWP, you can start with an empty demo-site, just for you!
Also, it's free. 
[Click here to create the instant demo-website with Name Directory installed](https://demo.tastewp.com/name-directory).

#### Embed the directory in the WordPress default search engine
When you go to the General Settings of the plugin, you can enable WordPress search compatibility.
This will include the pages who have a matching entry of Name Directory in the search results

#### Language support
Do you want Name Directory to be available in your language?
Please help us translate!
You can translate directly by going to [https://translate.wordpress.org/projects/wp-plugins/name-directory](https://translate.wordpress.org/projects/wp-plugins/name-directory),
login with your WordPress account and click Select your language and click 'Contribute Translation'.

#### Support
If you like this plugin and want to support and/or thank me, [please buy me a coffee](https://ko-fi.com/jeroenpeters).

#### References
We are proud to be featured on:
 - [WordPress tutorials](https://wpglob.com/blog/)
 - [Kinsta: The best Directory plugins (no. 7)](https://kinsta.com/blog/wordpress-directory-plugins/#name-directory)
 - [WPBeginner: Best Directory plugins (no. 5)](https://www.wpbeginner.com/plugins/best-directory-plugins-for-wordpress/)
 - [QuadLayers: Best Directory Plugins (no. 3)](https://quadlayers.com/best-wordpress-glossary-plugins/)
 - [ThemeGrill: Business Directory Plugins (no. 6)](https://themegrill.com/blog/wordpress-business-directory-plugin/#6-name-directory)
 - [WP Wax: Best Directory plugins](https://wpwax.com/best-wordpress-directory-plugins/)

#### Thank you
Thank you to the few who have donated to me already, or bought a custom version of Name Directory.

Additional and a special thanks goes to [JetBrains, the creator of PhpStorm](https://www.jetbrains.com/?from=name-directory),
for providing me with a free open-source licence to their products. This helps me maintain this plugin!


== Installation ==

= Displaying a directory on your site =

1. Go to the Name Directory settings page
1. Hover over the directory you want to add to the page.
1. A few options should show now, like Delete, Manage and Shortcode (see screenshot https://ps.w.org/name-directory/assets/screenshot-2.png).
1. Click 'Shortcode', a little textbox will show now.
1. Copy-and-paste the content of the textbox into the page you want the plugin to show up.
1. Save and view the page to see the result.

= What does the shortcode look like? =

The shortcode for this Name Directory plugin is like this:
`[namedirectory dir=1]`

= Installing the plugin =
Installation is very easy. You can just download this plugin through the Plugin Finder in your WordPress Administration Panel.

If you download the zip-file, installation isn't that difficult either:

1. Unzip the file which results into a directory called `name-directory`
1. Upload that directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new name directory and add some names
1. Copy the shortcode and paste it into a post or page to show it on your website


== Frequently Asked Questions ==

= What does the shortcode look like? =

The shortcode for this Name Directory plugin is like this:
`[namedirectory dir=1]`

The `1` in this example is the internal ID of the directory, the rest of the shortcode should always look like this.

= I created a directory, how do I show it on my site? =

1. Go to the Name Directory settings page
1. Hover over the directory you want to add to the page.
1. A few options should show now, like Delete, Manage and Shortcode - see screenshot https://ps.w.org/name-directory/assets/screenshot-2.png
1. Click 'Shortcode', a little textbox will show now.
1. Copy-and-paste the content of the textbox into the page you want the plugin to show up.
1. Save and view the page to see the result.

= Can I customize the appearance / styling of the Name Directory? =

Yes you can, with CSS. If you know your way around CSS you might already know that you can style elements by their class name or HTML structure.
This plugin was written with styling / CSS in mind. Using the HTML inspector of your favourite browser you should be able to discover the classnames, but here are a few popular classes:

* `.name_directory_index`: Index links (the letters A-Z)
* `.name_directory_name_box > strong`: Name / Entry title
* `.name_directory_name_box > div`: Name / Entry descriptiong
* `.name_directory_total`: Total count of names / entries
* `.name_directory_index > form`: Search form
* `.name_directory_submit_bottom_link`: Link to submit form
* `.name_directory_active`: Currently active character on index
* `.name_directory_empty`: Indicates that there a no entries for character
* `.name_directory_character_header`: Indicates a new starting-letter header
* `#name_directory_search_input_box`: Search input box
* `#name_directory_search_input_button`: Search button

Also, I do offer help on styling / CSS if you are willing to make a small donation.
Please contact me at the [Support Forums](https://wordpress.org/support/plugin/name-directory) or [my website](https://jeroenpeters.dev/contact) to discuss this.

= Is there a bulk-add or csv import in this plugin? =

Since v1.7, yes there is! You can import a .csv-file into a directory.

1. Go to the Name Directory settings page
1. Hover over the directory you want to import names into.
1. A few options should show now, like Delete, Manage and Import - [see this screenshot](https://ps.w.org/name-directory/assets/screenshot-2.png) for an example
1. Click 'Import'
1. Select your .csv-file
1. Upload

You can add names, descriptions and submitter entries, just the first column (name) is required. Good to know: the first row is always ignored (they should be headers).
You can download an [example import file](http://ps.w.org/name-directory/assets/name-directory-import-example.csv) to take a look at the format.

Might your first try does not work, please use https://www.freefileconvert.com to convert your file into a valid CSV file.
Also, importing works best if you export the files with 'UTF-8' character set, ANSI does not always work.

If you need any help, contact me on the [Support Forums](https://wordpress.org/support/plugin/name-directory).

= Can I export my directory? =

Since v1.8, yes you can! This export is also compatible with the import-functionality offcourse. If you want to export, use the following steps:

1. Go to the Name Directory settings page
1. Hover over the directory you want to export.
1. A few options should show now, like Delete, Manage, Import and also Export - [see this screenshot](https://ps.w.org/name-directory/assets/screenshot-2.png) for an example
1. Click 'Export'
1. On the new page click the button and your .csv-file will be downloaded

= Are there demo's / examples to see the plugin in action? =

Yes, every now and then I come across an installed version of the plugin. Here is an incomplete list.

1. [Dutch Budgie website - How to name your bird](https://www.parkietenvilla.nl/namenlijst/)
1. [French website - Kitchen-terms](https://goutu.org/lexique-de-cuisine/)
1. [Dutch website - How to name your pet rat](http://ratten.nl/fun/namenlijst/)
1. [Dutch website Pieckbon - (participating enterpreneurs lists)](https://www.pieckbon.nl/deelnemers-pieckbon/)
1. [Dutch Pregnancy - Term List](https://allesoverzwanger.nl/woordenlijst/)
1. [Alabama Orthopaedic Society - Member list](https://aosdocs.com/find-an-orthopaedist/)
1. [Convertus - Paid Search Terms](https://www.convertus.com/search-glossary/)
1. [SBS ShopRI - Vendor List](http://sbsshopri.com/vendors/vendor-listing-2018/)
1. [Coin Collector Blog](http://coinsblog.ws/collectors-reference/numismatic-dictionary)
1. [Venlo's Waordeboek (Dutch Limburgian Dialect Dictionary)](https://veldekevenlo.nl/waordebook/)
1. [Dutch Winetasting Terms](https://www.winesessions.nl/proefterminologie/)
1. [Certficate Holders in Ghana](https://idmcghana.com/index.php/certificat-directory/)
1. [Preston County Commission](https://prestoncountywv.gov/directories/)
1. [Tuscany Cookie Class Terms](https://tuscany-cooking-class.com/italian-to-english-cooking-dictionary/)

If you see a dead link, would you please [let me know](https://wordpress.org/support/plugin/name-directory)?

= Can I try the plugin myself before I install it? =

Yes, [click here for in instant demo-website with Name Directory installed](https://demo.tastewp.com/name-directory)

= Can I use HTML in the name description? =

Yes, this is possible! Please do be careful to use valid HTML only though.

= Can I use a WYSIWYG editor to edit the name description? =

Yes, you can, since v1.14.1. You can enable this yourself when you go to the General Settings ([also see screenshot 7](https://ps.w.org/name-directory/assets/screenshot-7.png))

= Is it possible to be a Name Directory admin without being a Site Administrator? =

Since version 1.17 his is possible by also using the [Members plugin](https://wordpress.org/plugins/members/).
Name Directory registers a capability called `manage_name_directory` there.
Whenever you give a user this capability, they will see the Name Directory admin menu's and will be able to manage the Name Directories.

= Can I also display a random name? =

Yes, since v1.12 this is possible. You can use this shortcode for that:
`[namedirectory_random dir=1]`

The `1` in this example is the internal ID of the directory.

= Can I also display a single name? =

Yes, since v1.13 this is possible. You can use this shortcode for that:
`[namedirectory_single id=10]`

= How come some characters don't show, or show as questionmarks? =

If so, please check that your PHP version has _mb_string_ enabled. If that is the case, please check if your database is UTF-8 supported (utf8mb4_unicode_ci).

The `10` in this example is the internal ID of the name, you can find it in the last column when you view all names for a directory in the WordPress admin.

Also, if this does not work, there is a setting called "Show all letters on index". If you switch this off, it should work.

= How can I contact you? =

If you have questions about the plugin or if you have ideas to share, the best way to contact me is through the [Support Forums](https://wordpress.org/support/plugin/name-directory).
If you want me to do custom, paid work for you, you can get in touch by [contacting me on website](https://jeroenpeters.dev/contact).

= Do you also offer customizations? =

Yes, I do. For this I offer paid help. Please contact me at [my website](https://jeroenpeters.dev/contact) to discuss this.

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program.
The Patchstack team helps validate, triage and handle any security vulnerabilities.
[Report a security vulnerability.](https://patchstack.com/database/vdp/name-directory)


== Screenshots ==

1. The output of a name directory on a standard WordPress website. It's a full-featured name directory (search form, index links, descriptions and submit button)
2. Overview of all the name directories in the WordPress Administration screen for this plugin
3. List of all names in the selected directory and the 'Add name' form
4. Settings screen for a name directory
5. Where to find the Name Directory plugin settings page
6. Another example of the plugin on a website
7. Overview of the new General Settings page, which allows you to embed search results from Name Directory in your WordPress site search and since v1.14.1 also to enable the HTML editor


== Changelog ==

= 1.29.1 | August 14, 2024 =
 * Enhancement: Tested with WordPress 6.6
 * Enhancement: Tested up to WordPress 6.7
 * Bugfix: escape searchterm in the WordPress admin
 * Bugfix: add extra permission-check in ajax-status calls

= 1.29.0 | March 25, 2024 =
 * Enhancement: Improved sorting with diacritic characters
 * Enhancement: Tested up to WordPress 6.5
 * Enhancement: Clarify implications of the use of HTML

= 1.28.5 | November 5, 2023 =
 * Bugfix: PHP showed a notice when in debug mode about missing variable, thanks @jibbius
 * Bugfix: Only select letters from published names (so for example H isn't shown if there is one unpublished name Henk)
 * Bugfix: Don't send an e-mail to the admin twice
 * Bugfix: Display issue with a title in the admin
 * Bugfix: Derive the starting character from the cleaned string
 * Enhancement: Added some WCAG hints to the front-end
 * Enhancement: Optimized query

= 1.28.4 | October 29, 2023 =
 * Enhancement: Sent the mail to the directory-set e-mail

= 1.28.3 | October 27 (party, party), 2023 =
 * Bugfix: Bug with creating new directories

= 1.28.2 | October 27 (party, party), 2023 =
 * Bugfix: Bug with creating new directories

= 1.28.1 | October 27 (party, party), 2023 =
 * Bugfix: Bug with creating new directories

= 1.28.0 | October 22, 2023 =
 * Bugfix: (Finally!) fixed the issue that editing a names HTML doesn't break formatting (with quotes)
 * Feature: Choose whether you want to show the name count
 * Feature: Choose whether you want to show the instructions on the front-end
 * Feature: Set an e-mail address (per directory) which receives new-submission notifications
 * Enhancement: The directory settings screen: readability, now divided into sections, bit more spacey
 * Enhancement: Tested up to WordPress 6.3
 * Enhancement: Tested up to WordPress 6.4 Beta
 * Enhancement: Tested up to WordPress 6.5 Nightly
 * Enhancement: Reviced the public demo list

= 1.27.2 | January 20, 2023 =
 * Enhancement: Tested up to WordPress 6.2
 * Bugfix: Quick Import nonce check

= 1.27.1 | September 28, 2022 =
 * Enhancement: Also css-hide images on the 'Read more'-feature
 * Bugfix: Closed the div surrounding reCaptha
 * Bugfix: Trim the name when importing names, so the correct starting letter will be selected
 * Bugfix: Display the name correctly use entity_decode

= 1.27.0 | August 27, 2022 =
 * Feature: Choose whether an admin has to approve a submitted name first
 * Enhancement: Nicer error message on (quick) import

= 1.26.1 | July 19, 2022 =
 * Bugfix: After submitting a name, return to the page the visitor came from, without explicitly showing all the names, just use the directory's default behaviour

= 1.26.0 | July 16, 2022 =
 * Feature: when show_all_index_letters is Off, do an exact query (case sensitive) to make sure special characters are honoured as starting-letter

= 1.25.5 | July 15, 2022 =
 * Enhancement: The edit screen is now standalone
 * Security: Use wp-nonce on add name action
 * Security: Rely on the WordPress API and its help functions to assist with security
 * Security: User rights checking improved
 * Special thanks: Thank you Erwan Le Rousseau from WPScan.com for pointing out issues and the patience to check my fixes

= 1.25.4 | June 28, 2022 =
 * Enhancement: Some users have huge directories, because of that, there is a mode where the adding form is shown without the list of all the names. This is now the default behaviour if you click the "Add Names" link on the directory overview
 * Enhancement: Remove extra space in overview table for WordPress admin consistency
 * Security: Use wp-nonce on delete directory action
 * Security: Use wp-nonce on delete name action
 * Security: Use wp-nonce on edit name action
 * Security: Secured the import script with script-tag-stripper and wp-nonce

= 1.25.3 | May 8, 2022 =
 * Bugfix: Fixed XSS vulnerabilities reported to me by Donato Di Pasquale, thank you!

= 1.25.2 | April 16, 2022 =
 * Bugfix: The 'All'-link jumplocation did not work since v1.25.1
 * Bugfix: URL-component ordering

= 1.25.1 | April 7, 2022 =
 * Improvement: Made the anchor-links unique so multi-directory-pages are working OK when you click the index-links
 * Improvement: When there are multiple directories on one page, only show the correct submit form when it's requested
 * Improvement: When there are multiple directories on one page, only do the name-exists-check in the right directory

= 1.25.0 | April 5, 2022 =
 * Improvement: When there are multiple directories on one page, and you use search, jump to the right directory
 * Maintenance: WordPress 6.0 compatible

= 1.24.0 | February 1, 2022 =
 * Bugfix: Fixed duplicate hidden input field, thanks @pavelinnuendo for the tip!
 * Improvement: Speed up performance by adding database indexes
 * Maintenance: WordPress 5.9 compatible
 * Enhancement: Add TasteWP links to description where you can try Name Directory instantly

= 1.23.2 | September 16, 2021 =
 * Bugfix: Removed natural sorting for now as it's messing up the sorting for non-numeric characters

= 1.23.1 | August 6, 2021 =
 * Feature: Natural number sort
 * Enhancement: Tested with WordPress 5.9 alpha

= 1.23.0 | June 20, 2021 =
 * Feature: Compatible with Relevanssi
 * Feature: Search terms can be highlighted in the search results
 * Enhancement: Compatible with WordPress 5.8

= 1.22 | March 3, 2021 =
 * Bugfix: 'latest' option did not show any entries

= 1.21 | March 1st, 2021 =
 * Bugfix: the regex which splits the words (limit at 10, 25, etc) was not UTF-8 ready yet

= 1.20 | February 28, 2021 =
 * Feature: Optionally secure your submit-a-name form with reCAPTCHA (v2) from Google (Thanks for the idea Aung!)

= 1.19 | February 16, 2021 =
 * Feature: Show a heading when the next starting-letter will begin (show B after all the A-words, C after the B-words, etc)
 * Feature: In admin panel replace the 'Yes'/'No' link (which was a clickable ajax link) with a fancy toggle button (much more userfriendly)
 * Enhancement: Added a few extra help texts
 * Enhancement: Used a more narrow javascript selector for the import page
 * Enhancement: Made the CSS classes more consistent
 * Enhancement: Compatible with WordPress 5.7

= 1.18.1 | January 13, 2021 =
 * Bugfix: Fixed wrong usage of $wpdb->esc_like

= 1.18 | January 10, 2021 =
 * Bugfix: Implemented pull request from Jack Barker (http://jackbarker.com.au/) with search enhancement
 * Security: CVE-2021-20652: Fixed CSRF vulnerability, thank you Yuta!

= 1.17.4 | November 22, 2020 =
 * Enhancement: Just a small enhancement to see if translations are working

= 1.17.3 | November 19, 2020 =
 * Enhancement: Use the correct plural when there is result in searching
 * Enhancement: Compatible with the new WordPress 5.6

= 1.17.2 | October 22, 2020 =
 * Security: Added additional checks for the front-end submits
 * Enhancement: Edited some code to not show native PHP notices on dev-enabled sites
 * Enhancement: Did some initialization of values in the new-directory admin
 * Enhancement: Display an error message when a directory with a non-existing ID is called
 * Enhancement: Compatible with the new WordPress 5.6 beta
 * Enhancement: Compatible with PHP 8.0

= 1.17.1 | September 2, 2020 =
 * Bugfix: Capability race-condition

= 1.17 | September 2, 2020 =
 * Feature: Name Directory is now also available with capability 'manage_name_directory', compatible with the commonly used Members plugin
 * Bugfix: Better value-checking at import, so it doesn't wing any PHP notices for the sites which enabled them
 * Maintenance: WordPress 5.5 compatible

= 1.16.1 | July 3, 2020 =
 * Bugfix: Database constraints loosened

= 1.16 | April 18, 2020 =
 * Feature: You can now disable the duplicate protection in the General Options
 * Enhancement: Ability to use shortcodes in descriptions
 * Bugfix: Better use of the 'singular' term for name directories
 * Bugfix: Linebreaks are preserved when you use the visual editor

= 1.15.6 | March 15, 2020 =
 * Bugfix: Update DB to unicode at setup too
 * Bugfix: Translation of 'Show less' did not show up due to wrong classname
 * Enhancement: CSS Extra spacing on index characters

= 1.15.5 | March 7, 2020 =
 * Maintenance: WordPress 5.4 compatible

= 1.15.4 | January 7, 2020 | Happy New Year! =
 * Bugfix: Determining number of columns
 * Bugfix: Replaced 'ellips' with '...' to broaden compatibility for other themes/plugins
 * Maintenance: WordPress 5.3 styling

 = 1.15.3 | November 2019 =
  * Maintenance: WordPress 5.3.1 compatible

= 1.15.2 | October 13, 2019 =
 * Feature: Added an option to the import screen to empty a directory before importing (useful for people who use a spreadsheet for name management)
 * Enhancement: Added a special import-option the import screen which uses UTF8 import.
 * Maintenance: WordPress 5.3 compatible

= 1.15.1 | August 16, 2019 =
 * Bugfix: Added an exact match query to the "exact" search functionality

= 1.15 | August 14, 2019 =
 * Feature: You can now choose (per-directory) whether to search in the description
 * Enhancement: Added extra explaination / documentation on the subject of importing to the plugin
 * Enhancement: Refined the "exact" search functionality in the directories
 * Maintenance: Cleaned up the plugins CSS
 * Maintenance: Changed the way the database is installed and kept in sync on updating, way less code

= 1.14.2 | August 1, 2019 (my nephew's birthday) =
 * Improvement: Empty the edit form on Ajax submit and scroll to the top to see the success message

= 1.14.1 | August 1, 2019 (my nephew's birthday) =
 * Feature: You can now use a visual editor! You just have to enable this on the General Settings (the one of Name Directory) screen
 * Bugfix: Fixed small import bug with accent characters
 * Bugfix: htmlspecialchars sometimes issued a warning to the error_log

= 1.14 | July 17, 2019 =
 * Feature: Added Quick Import from menu, imports into a new directory
 * Enhancement: Translations/naming
 * Enhancement: When searching with (double) quotation marks, it will not perform a wildcard search. So searching for "media" will not return 'mediator'.
 * Bugfix: Directory sorting in the admin is now on most recent added named, but with empty directories on top

= 1.13.7 | June 2, 2019 =
 * Bugfix: SQL improvement for selecting and grouping

= 1.13.6 | June 1, 2019 =
 * Feature: sort name directory on most recent added name
 * Bugfix: also show empty directories

= 1.13.5 | May 28, 2019 =
 * Refactored some code
 * Javascript is handled cleaner (more WordPress-alike)
 * Bugfix: Fixed One-click status toggle (published/unpublished)

= 1.13.4 | May 28, 2019 =
 * Feature: sort name directory on most recent added name
 * Bugfix: remove debug output

= 1.13.3 | May 16, 2019 =
 * Bugfix: utf8_encode in import functionality, so non-latin characters are supported too

= 1.13.2 | April 18, 2019 =
 * mb_string enhancement, this is used on non-latin character sets.
 * Bugfix: Do not use mb_string functionality when it's not available (so it does not crash)
 * Feature: Test whether the mb_string extension in PHP is enabled and display a notice when it's not
 * Tested WordPress v5.2 (beta)

= 1.13.1 | February 28, 2019 (my birthday!) =
 * Bugfix: When exporting, rows were not properly separated
 * Bugfix: When exporting, HTML markup cannot support linebreaks

= 1.13 | February 20, 2019 =
 * Introduced `[namedirectory_single]`, to display a single name entry on the website. Useful for widgets
 * Bugfix: When exporting, HTML markup is now saved to the CSV
 * Added ID's in the admin (so you can actually use the new shortcode)
 * Created a render-helper for nameboxes to be reused by `[namedirectory]`, `[namedirectory_random]` and the new `[namedirectory_single]`
 * Finally using separate .js and .css for the admin!
 * Numurous small enhancements to the admin

= 1.12 | January 31, 2019 =
 * Added search in the backend -> Code kindly provided by @mpmarinov
 * Added shortcode for displaying a random name (`[namedirectory_random]`) from a given directory -> Code kindly provided by @mastababa

= 1.11.6 | January 21, 2019 =
 * Also added the 'jump to' functionality when clicking on index letters. Earlier, this was only done at the searchbox (Thanks Ana!)

= 1.11.5 | January 12, 2019 =
 * Better support for your own name term
 * Admin link to the add-form

= 1.11.4 | December 27, 2018 =
 * Fixes bug in WordPress site search hook when no preferences for site search were set

= 1.11.3 | December 27, 2018 =
 * Temporary disabled the search because of bug reports

= 1.11.2 | December 26, 2018 =
 * Fixes bug in WordPress site  search hook where all posts were included on no results

= 1.11.1 | December 26, 2018 =
 * Temporary disabled the search because of bug reports

= 1.11 | December 18, 2018 =
 * Added the ability to give your own term for 'names'. So it does not have to be a 'name' directory, you can customize it to be like 'movies'. It'll state "There are currently 10 movies in this directory", instead of "There are currently 10 names in this directory".
 * WordPress 5.0.1 compatible

= 1.10 | December 17, 2018 =
 * It's now possible to include Name Directory in your WordPress (site wide) search results. Offcourse, Name Directory still has it's own searchbox!
 * Added a new General Settings page which allows you to enable and tweak the search-behaviour. You can also choose to search in descriptions and whether it should be a wildcard search.
 * Added a screenshot of the new General Settings page
 * Updated screenshot #2 (Directory overview)
 * Updated Frequently Asked Questions

= 1.9.7 =
 * PHP 5.3 compatibility
 * WordPress 5.0 compatible

= 1.9.6 =
 * Cleaned up code to prevent errors in debug-mode

= 1.9.5 =
 * Startswith character can now be non-latin

= 1.9.4 =
 * Confirm-delete message did show an alert, but no text

= 1.9.3 =
 * Added a confirm-delete message when deleting a directory.

= 1.9.2 =
 * Sorted index characters when they were not shown by default: A-Z

= 1.9.1 =
 * Compatible with WordPress 4.9.1
 * Ability to limit the amount of words in the description, shown on the frontend of the website. You can manage this in the directory settings. Names with a description which exceeds the setting, are limited with a "Show more" link.
 * Optimized code, rewritten the directory options screen to prevent lots of duplicate code and make it easier for myself to maintain
 * Added some more descriptions to what the directory settings do
 * Updated screenshots

= 1.9.0 =
 * You can now use words that start with non-latin characters (like Chinese, Arabic, Nordic/Danish, etc)
 * Updated Readme, which includes the Description, Changelog and the Frequently Asked Questions
 * Updated php code to follow development guidelines

= 1.8.2 =
 * Compatible with WordPress 4.9 final
 * Update php code to follow development guidelines

= 1.8.1 =
 * Compatible with WordPress 4.9b3
 * Removed old translation file, since Name Directory uses the WordPress translation platform
 * Moved an admin `add_action` hook
 * Updated Export to work with Javascript, possible denying Internet Explorer 9 (and lower) users export
 * Fixed compatibility with AJAX Front-end plugins
 * WordPress moves all divs with class 'updated' to the top of the page, which isn't very handy for the Add-Name form, which is on the bottom of the page. Created a workaround, all success-messages will be displayed above the add-form again.

= 1.8 =
 * Added export function, export your name directory to .csv file. Offcourse, it's compatible with the upload function

= 1.7.15 =
 * WND-46: Created a new option the admin for jumping to the searchbox on the front-end, useful on long page or onepage website
 * Support bumped to WordPress 4.8.2

= 1.7.14 =
 * WND-45: Better multisite support

= 1.7.13 =
 * WND-44: Front-end submit form now requires at least a name

= 1.7.12 =
 * WND-42: Option to use latest was broken, treated as L

= 1.7.11 =
 * WND-41: Verified translation from translate.wordpress.org
 * WND-40: Fixed bug there selecting only names that start with numbers, everything was shown

= 1.7.10 | July 23, 2016 =
 * WND-39: Added `name_directory_active` class on index to indicate which character was activated
 * WND-39: Added `name_directory_empty` class on index to indicate there are no entries for a character
 * Started working with the Stable tag: https://wordpress.org/plugins/about/svn/#task-3
 * Removed translations so we can benefit from translate.wordpress.org :)
 * Do you want to help me translate this plugin in your own language? Let me know in the support forums! I will credit you!

= 1.7.9 =
 * WND-38: Database structure adjust

= 1.7.8 =
 * WND-35: Fixed CVS import
 * WND-36: Tested WP 4.4.2

= 1.7.7 =
 * WND-24: Fixed Possible XSS vulnerability

= 1.7.6 =
 * WordPress 4.3 compatible
 * Added Arabic translation (Thanks Ahmad from http://www.ams.ly)
 * Updated some PHP-code
 * Prevent possible function collisions
 
= 1.7.5 =
 * Updated Norwegian translation (Thanks Mikael!)
 * Some improvements for English language
 * Synced .pot and .po files

= 1.7.4 =
 * WND-25: Send e-mailnotification to WordPress admin when a new name is submitted
 * Generated new .pot file and synced all .po files

= 1.7.3 =
 * Ordering enhancements
 * Generated new .pot file and synced all .po files

= 1.7.2 =
 * WND-32: Show X latest (most recent) names
 * Updated Dutch Translation

= 1.7.1 =
 * Added Norwegian translation thanks to Mikael
 * WND-31: Search for searchterm in description (but only if show_description is enabled)
 * Moved common code to helpers, preparing for better code

= 1.7 =
 * WND-11: Import names and descriptions by csv-upload, find this option at the manage-screen
 * WND-24: Toggle published-status for name (easily show or hide names)
 * Name in WordPress settings menu is now "Name Directory" instead of "Name Directory Plugin"
 * Extended FAQ
 * Code improvements
 * Updated Dutch Translation

= 1.6.16 =
 * WND-26 & WND-28: Honour the Show Description setting in frontend

= 1.6.15 =
 * Added little spacers in the admin on the Manage names screen
 * Every name on the front-end got an anchor name

= 1.6.14 =
 * Added new translation file
 * Updated Dutch translation

= 1.6.13 =
 * WND-23: New option to only show letters on the index when there are entries, so A B D E when there is no entry with C
 * Fixed small legacy db-convert bug
 * Gave the admin panel for directory settings some space

= 1.6.12 =
 * Expanded FAQ
 * Updated documentation / edited screenshots
 * Updated information displayed at the WordPress Plugin Repository page

= 1.6.11 =
 * Search URL's didn't function properly
 * Search argument didn't work together (selected name and input filter)
 * function didn't work when WordPress was running without SEO tools
 * URL improvements (also tested with Yoast SEO plugin)

= 1.6.10 =
 * URLencoded the # sign, so entries starting with a number will show up

= 1.6.9 =
 * WND-21: Checked translation strings. Also edited two fussy strings in the Dutch translation
 * WND-22: Fixed wp-admin paths for WP Multisite users

= 1.6.8 =
 * WND-17: Added option which let's the user choose a default starting-character when displaying the name directory. For example: use [namedirectory dir="X" start_with="j"] to start with the letter J.
 * WordPress 4.0 compatibility
 * Added Icon to the installer gallery

= 1.6.7 =
 * Updated Russion Translation (Thanks to: Rig Kruger http://rodichi.org)

= 1.6.6 =
 * Fixed small display bug

= 1.6.5 =
 * Showed submitted name

= 1.6.4 =
 * Updated French translations
 * Fixed too-many-slashes issue

= 1.6.3 =
 * Updated Dutch translations
 * Fixed display bug. 
 * The All-link is hidden when you a visitor HAS to choose a letter from the index

= 1.6 =
* Added option 'Show all names by default', this can be disabled to hide all entries if a user hasn't chosen a letter from the index.

= 1.5.2 =
* Fixed bug in CREATE TABLE and backlink in form, thank you very much MerlIAV for the patch!

= 1.5.1 =
* Fixed bug that prevent saving searchform preference in admin

= 1.5 =
* Added search box on front-end (You can enable this in the name-directory settings)
* Added support for four-column layout
* Added Russion Translation (Translated by: Rig Kruger http://rodichi.org)

= 1.4.3 =
* Fixed bug which allowed non-published items to be shown

= 1.4.2 = 
* Fixed support for Chinese characters
* Added French Translation (Translated by: Patrick BARDET http://www.web-studio-creation.fr)

= 1.4.1 = 
* Fixed sorting issue at the frontend

= 1.4 = 
* WND-19: Added support for HTML in the name description

= 1.3 =
* Name lists can now have multiple columns at the frontend
* Added css in a separate file
* Added database upgrade module

= 1.2.1 =
* Plugin url's are now compatible with third party SEO modules

= 1.2 =
* Added support for submission form on the front-end
* Added possibility for admin to filter on published/unpublished names
* Rearranged directory overview for admin, overview now shows totals for published/unpublished

= 1.1 =
* Added double name detection

= 1.0 | November 8, 2013 =
* First major public release

= 0.5 =
* First version for private use

