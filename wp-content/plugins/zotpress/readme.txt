=== Zotpress ===
Contributors: kseaborn
Plugin Name: Zotpress
Plugin URI: http://katieseaborn.com/plugins/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5HQ8FXAXS9MUQ
Tags: zotero, zotpress, citation manager, citations, citation, cite, citing, bibliography, bibliographies, reference, referencing, references, reference list, reference manager, academic, academic blogging, academia, scholar, scholarly, scholarly blogging, cv, curriculum vitae, resume, publish, publication, publications
Author URI: http://katieseaborn.com/
Author: Katie Seaborn
Requires at least: 3.5
Tested up to: 6.5.2
Stable tag: 7.3.10
License: Apache2.0

Zotpress displays your Zotero citations on WordPress.

== Description ==

[Zotpress](http://katieseaborn.com/plugins/ "Zotpress for WordPress") brings publication broadcasting and scholarly blogging to WordPress through [Zotero](http://zotero.org/ "Zotero"), a free, cross-platform reference manager.

= Features =
* Displays your personal and group Zotero items through in-text citations, bibliographies, and searchable libraries
* Supports thumbnail images through WordPress's Media Library and Open Library
* Supports selective CSS styling via IDs and classes
* Provides a range of additional features, such as allowing visitors to download citations
* And more!

Compatible with Firefox, Safari, Chrome, and IE9. Made with jQuery, jQuery UI, jQuery doTimeout, Live Query, OAuth, and [Open Library](https://openlibrary.org/ "Open Library").

Special thanks to Joe Alberts for substantial contributions to the code, comprehensive testing, and design ideation. Thanks also to contributors Christopher Cheung, Jason S., Chris Wentzloff, Karljürgen Feuerherm (@feuerherm), Mark Dingemanse (@codeispoetry), Jörg Mechnich (jmechnich@github), Tomas Risberg, and @ericcorbett2 for their code contributions, testing, and guidance. Finally, my sincere gratitude goes out to all who have donated in support of this plugin.

Please note that this plugin is on semi-hiatus, with updates expected about 1-3 times a year.

= Requirements =
jQuery included in your theme (Zotpress will do this for you if it isn't already included), and an HTTP request method supported by WordPress enabled on your server: cURL, fopen with Streams (PHP 5), or fsockopen. In your server config file, X-Frame-Options should be set to SAMEORIGIN. Optional: OAuth enabled on your server.

== Installation ==

1. Install from the `Plugins` page in your WordPress admin panel. Or, download and extract the zip, then manually upload the folder `zotpress` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the `Zotpress` page through the admin menu.
1. Add the Zotero API information for each Zotero account you'd like to use.
1. Use the Zotpress Shortcode option in the Gutenberg editor to serach for items and build a shortcode. Or, look up the keys of items, collections, and tags on the `Browse` page. Or, use the Zotpress Reference Meta Box to generate shortcodes.
1. Place the shortcodes in your blog post or page, or enable the Zotpress sidebar widget. Below are a few examples to get you started. Look at the `Help` page for more examples and the full details about all use options and shortcodes.

= Shortcode =
You can display your Zotero citations in a number of ways. To display a list of five citations from the collection with the key "ZKDTKM3X", use this shortcode:

> [zotpress collection="ZKDTKM3X" limit="5"]

You can also use in-text citations as follows:

> [zotpressInText item="{U9Z5JTKC,36-45}"]

> [zotpressInTextBib]

The first shortcode will display an in-text citation for an item with the key "U9Z5JTKC", which will look something like this: (Seaborn, 2011, p. 36). The [zotpressInTextBib] will auto-generate a bibliography for all [zotpressInText] shortcodes on the post/page.

Check out the "Help" page on your installation of Zotpress for more information and a full listing of parameters for all shortcodes.

== Frequently Asked Questions ==

The F.A.Q. can be found on the "Help" page of every Zotpress installation. If you have a question that isn't answered there, feel free to post a message in the [forums](https://wordpress.org/support/plugin/zotpress "Zotpress forums on Wordpress.com").

== Screenshots ==

1. Display items from individual or group Zotero libraries on your WordPress website. Special characters are supported.
2. Search for items and generate shortcodes for bibliographies and in-text citations using the "Zotpress Reference" meta box on edit screens.
3. Find the shortcode builder in the Gutenberg toolbar. Search for items and create bibliographies and in-text citations.
4. Give your citation an image using WordPress's Media Library or Open Library.
5. Share your library through your website. Allow visitors to browse by collection or tag.
6. Allow visitors to search your library by item metadata or tag, or both.

== Changelog ==

= 7.3.10 =

* Fixed shortcode security issues.
* Refined shortcode attribute processing code.
* Fixed request limit bug.
* Various code refinements.

= 7.3.9 = 

* Fixed security issue.
* Fixed search bar bugs.
* Added direct access prevention (thanks to jmechnich@github).
* Added fix for countable issue (thanks to Tomas Risberg and @ericcorbett2).

= 7.3.8 =

* Fixed database security issues.

= 7.3.7 =

* Fixed numeric in-text sorting bug.
* Fixed Zotpress_change_timeout() bug.
* Fixed in-text notes bug.
* Added support for in-text non-contiguous page numbers.

= 7.3.6 =

* Fixed duplicates bug.
* Fixed cache issue.
* Refined search bar.

= 7.3.5.1 =

* Fixed $tempAuthor, $tempItemDate, and $tempItemYear bugs.
* Added support for item type preprint.

= 7.3.5 = 

* Fixed security bug.
* Fixed "limit" bug related to assuming is_dropdown is true.
* Fixed disambiguation.
* Optimized bib and in-text loading.

= 7.3.4 =

* Fixed OAuth vulnerability and simplified authentication procedure.
* Fixed in-text date ID.
* Fixed cite URL, broken due to api_user_id.

= 7.3.3 =

* Fixed limit attribute issues in PHP 8.

= 7.3.2 =

* Added support for PHP 8 (tested in PHP 8.0.8), with thanks to K.G. Feuerherm (@kfeuerherm) and Mark Dingemanse (@codeispoetry).

= 7.3.1.2 =

* Fixed Library "Browse" page account, default account, and subcollection bugs.
* Minor style fixes for Library.

= 7.3.1.1 =

* Fixed Library collection navigation bug.
* Fixed minor Library style issues.
* Added Library dropdown browsing scroll effect.

= 7.3.1 =

* New! Show or hide browse bar for Zotpress Library Dropdown.
* Fixed single account library errors.
* Fixed minor issues with thumbnail image.
* Modified Library to allow multiple instances in one page.
* Modified Library pagination styles for modernization.

= 7.3 =

* New! Target attribute for Zotpress Library.
* Fixed task separation between install and update.
* Fixed caching issue for multiple collections.
* Fixed multipart names and apostrophes.
* Fixed some request errors.
* Fixed mdash/ndash bug.
* Fixed hardcoded paths.
* Added localizable error messages.
* Added error checks when collections or tags are empty.

= 7.2 =

* New! Added Gutenberg editor toolbar shortcode builder.
* New! Can now retrieve items by itemtype.
* Added disambiguation for in-text citations.
* Added new screenshot.
* Added brackets attribute to in-text shortcode builder/reference.
* Added support for more itemtypes for the title attribute, which should fix incorrect sorting.
* Changed download filename to original filename.
* Fixed shortcode builder bug for order by DESC.
* Fixed non-author types bug for in-text citations.
* Fixed media library set image bug.
* Fixed multisite install/reset bug.
* Fixed highlight-on-scroll issue.
* Updated Help page.
* Minor style and text formatting corrections.

= 7.1.6 =

* New! Can use the "showtags" attribute with the Library shortcodes.
* Refined "download" attribute to include linked URL downloads.
* Fixed "false" error for author names and the "highlight" attribute.
* Fixed Browse page error when there are no default accounts.
* Optimized for the Twenty Twenty theme.

= 7.1.5 =

* Fixed bug with shortcodes that use collections and tags.
* Fixed cite/RIS bug.
* Updated notifications to only display if Zotpress accounts exist.
* Updated Account page styles and notifications.
* Several improvements and fixes for Library Dropdown navigation.

= 7.1.4 =

* Fixed title sort bug with itemtype and date secondary sort.
* Fixed cache bug related to large numbers of tags.
* Fixed Library collection and tag display bugs.
* Refined admin CSS, especially notifications.

= 7.1.3 =

* Fixed error reporting, esp. for cache.
* Fixed tags with apostrophes error.
* Fixed numbering when multiple posts.
* Fixed title sort bugs.
* Updated icons.

= 7.1.2 =

* Fixed multi-part last name bug.
* Fixed error feedback-related bug.
* Fixed Zotpress Sidebar widget loading bugs.
* Fixed In-Text Citation separator bug.
* Refined and fixed bugs around sorting/ordering in general.
* Refined In-Text Citation "and" attribute; default returned to ampersand.
* Refined error reporting.
* Refined parameter cleaning of quote characters.

= 7.1.1 =

* Increased "title" attribute speed and refined its sorting/ordering.
* Fixed Library dropdown version pagination styles.
* Fixed numeric in-text bibliography numbering with cache retrieval.
* Fixed showtags request bug.
* Fixed bibliography "one item" bug.

= 7.1 =

* New! Now supporting the {api_user_id:item_key,pages} format for in-text citations.
* New! Now displaying cached citations via PHP for SEO purposes.
* New! Can use the "title" attribute to sort by item type.
* New! Can set a "toplevel" collection for the dropdown (default) version of the Library shortcode.
* Several in-text citation bugs fixed.
* Refined request error feedback.
* Refined pagination for the Library display with a scroller.
* Fixed date/numbering issue for american-antiquity style.
* Fixed forcenumber issue for chicago style.
* Fixed several sortby and order issues.

= 7.0.3 =

* Fixed Sort Order bug.
* More Japanese translations added.
* Account management style updates.

= 7.0.2 =

* Fixed Cite with RIS bug.
* Fixed activation error.

= 7.0.1 =

* Started preparing for localization; preliminary Japanese available.
* Fixed single-item error for the In-Text Citation shortcode.
* Fixed note display error for the Bibliography shortcode.
* Fixed download URL error for the Biblography shortcode.

= 7.0 =

* Introducing new Bibliography shortcode format to support citing items from multiple accounts in one shortcode: {api_user_id:item_key}
* Optimized for WordPress 5 and Gutenberg.
* Reduced the request load (esp. tags) by default.
* In-text numerical citations now support page numbers.
* Fixed date sort for multiple collections filtered by author.
* Fixed forcenumber/s bug.
* Added apostrophe support for tags.
* Minified styles and scripts.
* Updated branding.
* Added search bar for shortcode attributes to Help page.
* Minor style and UI updates to Zotpress Library.
* Removed experimental tinymce code.

= 6.2.4 =

* Updated shortcode styles and fixed the Search Bar display.
* Updated Zotpress Reset to remove additional options and tables from db.
* Download now looks for import_url as well as import_file.
* Updated the readme and screenshots.

= 6.2.3 =

* Updated Sidebar Widget constructor.
* Updated Library Shortcode to return rather than echo output.
* Clarified how "showimage" works on the Help page.
* Fixed Library Shortcode display bug for numbered bibliographies.

= 6.2.2 =

* Fixed notification error for older versions of PHP.

= 6.2.1 =

* Fixed compression library issue encountered by some users.
* Added notification to clear cache for database update.
* Applied fix for dois that are already hyperlinked.

= 6.2 =

* Added compression to bring down the size of the cache.
* Fixed logic error around WordPress Media Library images vs. Open Library images.
* Browse page Tags dropdown now accounts for no tags.
* Browse page has clearer loading feedback.
* Browse page collection and item keys are now text inputs to prevent styled text from being copied into the visual editor.
* Now hyperlinking https-based DOI URLs.
* Help page tab links have been fixed.

= 6.1.6 =

* Fixed wp_remote_retrieve_headers issue (WP core change) that limited total results.
* Removed Google Fonts for quicker admin-side loading.

= 6.1.5 =

* Fixed author sort in Chrome.
* Fixed urlwrap for chicago-author-date (using ireplace).
* Case applied for author filtering when single field is used instead of double field even though first/last structure exists.

= 6.1.4 =

* Fixes and extensions made to the "urlwrap" feature for titles, esp. quotes.

= 6.1.3 =

* Fixed security issue with zp_get_account().
* New! "style" attribute for searchbar.
* Updated widget account selection and fixed retrieval error.
* Minor image and code style updates.

= 6.1.2 =

* Applied more cases for URL wrapping.
* Fix for encyclopedia author/editor cases for in-text citations.
* Fix for authors who are also editors for in-text citations.
* Fixed in-text number-based sorting for 50+ items.
* Better in-text error handling.

= 6.1.1 =

* Applied secondary year sorting for author sort.
* Fixed default ordering for number-based in-text citations.
* Fixed erroneous fix for in-text editors of books.
* Fixed dropdown update bug for collections.
* Migrated Options AJAX to WP AJAX; fixed Default Style option.
* Updated cURL request approach.
* Fixed title wrap issues related to mdashes and quotes in titles.
* Fixed chicago style DOI linking issue.
* Minor style updates.

= 6.1 =

* Quicker load times through more effective use of cache.
* New! "urlwrap" attribute for wrapping titles and images with the citation URL.
* New! "highlight" attribute for highlighting text in a citation.
* New! Can now use showimage="openlib" to display book covers from the Open Library.
* In-text citations will no longer erroneously display editors.
* Order for In-Text Bib sort fixed.
* Browse navigation for multiple accounts fixed.
* Fixed "target" attribute for in-text bibliography DOI links.
* Now auto-detects https for Google Fonts links.
* Fixed multiple tags unique ID issue.
* Minor style updates.

= 6.0.5 =

* New way of selecting in-text citations without relying on post IDs as HTML IDs or classes.
* The "items" and "collections" parameters in bib and in-text shortcodes are now flexible around extra spaces.
* Fixed Zotpress In-Text Bibliography non-unique ID issue.
* Fixed parenthesis formatting issue for number-based in-text citations.
* Number-based in-text citations now have bibliographic details on mouse hover.

= 6.0.4 =

* Re-added item keys on Browse page.
* Set withCredentials attribute to true for AJAX requests.
* Fixed Zotpress Bibliography unique identifier bug.
* Fixed default sorting for in-text citations.
* Fixed in-text format <sup> (superscript).
* Re-added bib info as title for non-number-based in-text citation anchors.
* New! Can now use &, + and / with in-text page numbers.

= 6.0.3 =

* Fixed inclusive="no" bug for tags.
* Fixed pagination for searchbar.
* Fixed non-English characters bug for the Zotpress Reference widget.
* Fixed sidebar widget issues.
* Fixed in-text bug that assumed id rather than class for posts/pages.
* Fixed various in-text formatting bugs.

= 6.0.2 =

* Fixed multiple terms bug for searchbar.
* Fixed non-English characters bug for searchbar.
* Fixed sortby/order for searchbars.
* Fixed style value "default" bug.
* Pagination for searchbars now hidden on search and no items found.

= 6.0.1 =

* Fixed "limit" attribute bug for the Zotpress Bibliography Shortcode.
* Fixed single item bug for the Zotpress In-Text Shortcode.

= 6.0 =

* New! Auto-updating/syncing through realtime data access with cURL and AJAX.
* Now using Zotero API version 3.
* Retired Zotpress Bibliography Shortcode attribute "linkedlist"/"link"; use Zotpress Library Shortcode instead.
* Retired Zotpress Bibliography Shortcode attribute "datatype"; use Zotpress Library Shortcode instead.
* Modified Zotpress Bibliography Shortcode attribute "inclusive"; use with multiple authors only.
* Zotpress Reference widget refined; now uses WP AJAX.
* Fixed "cite" bug for the Library shortcode.
* Shortcode parameter "searchby" for the Library SearchBar is now limited to items and tags.
* Updated deprecated code to WP 4.3 standards.
* Minor bug fixes and updates throughout.

= 5.5.5 =

* Bug fix for searchbar filters.

= 5.5.4 =

* Minor update to date formatting.
* Searchbar links are now hyperlinked.
* Updated searchbar labels.

= 5.5.3 =

* Browse page bug fixed.
* Added "download" attribute to Library shortcode (dropdown-only).

= 5.5.2 =

* Added support for "cite" to Library shortcode (dropdown-only).
* Fixed "target" attribute for DOI links.
* Updated styles for download and cite links.
* Default style option label clarified for importing.

= 5.5.1 =

* Fixed ampersand error for tags (must re-import).
* Fixed author filter error with same last name. New format: (last, first)

= 5.5 =

* New! Autocomplete search bar option for "zotpressLib" shortcode.
* Updated DOI hyperlinking function.
* Fixed item import bugs related to quotes and empty queries.
* Thanks to @mlwk for a thorough, 2+ level nested collections fix.
* Thanks to Joe Alberts for fixing the permission denied error reporting.
* Added support for "month-month year" format to zp_date_format.

= 5.4.2 =

* Fixed code-breaking error that occurred for some users.

= 5.4.1 =

* Fixed display bug for single libraries on Browse page.
* Set nonce lifetime to 12 hours.

= 5.4 =

* New! "zotpressLib" shortcode for displaying your library on the front-end of your website.
* Fixed "set image" bug for single-account setups.
* Fixed "remove image" bug.
* Optimized nested collections display.
* Optimized notes with display for single notes and anchors.

= 5.3.3 =

* Better error messages.
* Table updates now check for existence of table first.
* Corrected HTML for nested lists of collections and validation.

= 5.3.2 =

* Security enhancements for import script.
* Fixed minor shortcode and import bugs and warnings throughout.

= 5.3.1 =

* Fixed custom tags interference issue.
* Added plugin icons.

= 5.3 =

* New "brackets" in-text citation shortcode attribute.
* Re-structured import and admin scripts.
* Expanded in-text formatting.
* Added submenu to admin sidebar.
* Added ability to set default accounts on Browse and Accounts and selectively import on Browse.
* Applied fix for array_multisort warning.
* Minor style updates throughout.

= 5.2.10 =

* Added support for multiple last names to author filtering.
* Removed survey link.
* Minor style updates to the metabox.

= 5.2.9.1 =

* Fixed minor sort error for in-text bibliographies.

= 5.2.9 =

* Author attribute is now strict, e.g. "Gret" will not return "Gretel."
* Sort attribute now understands most common date formats.
* Added "nick" version of "nickname" attribute to in-text shortcode.
* Fixed widget display issues for new posts.

= 5.2.8.1 =

* Fixed zp_get_year warning messages.

= 5.2.8 =

* Fixed in-text bibliography sorting errors.
* Fixed in-text citation display for no authors and no dates.
* Fixed image table issue that caused collection and/or item display bugs.
* Updated add account form labels.

= 5.2.7 =

* Added notice of research survey.
* Fixed inclusive filtering bug for multiple collections.
* Fixed prepare issues with metabox.

= 5.2.6 =

* Fixed import bug related authors with one name / full name meta.
* Fixed import bug related to non-English characters for in-text citations.
* Fixed abstract percent sign display bug.
* Added longer delay to in-text anchor highlight effect.

= 5.2.5 =

* Confirmed that Zotpress works with WP 4.0.
* New! "forcenumber" attribute numbers bibliographies, even when the style doesn't.
* New! Clicking in-text anchors highlights the corresponding entry in the bibliography.
* New! DOIs are automatically hyperlinked.
* New! Authors (and below) can only see Browse and Help pages.
* Notice: Database update to fix overwriting images during selective import.

= 5.2.4 =

* Fixed in-text citation title formatting issues.
* Fixed incorrect duplicates for items cited in-text.

= 5.2.3 =

* Fixed broken image URLs.

= 5.2.2 =

* Fixed displayed duplicates generated by multiple download links.
* Removed old code.
* Minor content and style updates on the Help page.

= 5.2.1 =

* Fixed selective import bugs related to duplicate items.
* Updated table structure to optimize script.

= 5.2 =

* IMPORTANT! You must re-import your Zotero library/libraries in Zotpress when you install this version or your shortcodes will not display correctly.
* Fixed selective import bug for libraries with more than 50 top-level collections.
* Optimized import script and enforced Zotero API Version 2.
* Increased time length before timeout for import scripts.
* Fixed database updating bug and sort/order bug.

= 5.1 =

* Zotpress now requires version 3.5 of WordPress.
* New! In-text citation attributes "and" and "separator" for greater flexibility in format and style.
* New! Bibliography shortcode attributes "showtags" for displaying a citation's tags and "target" for HTML5 compliance.
* New! Enable or disable Zotpress Reference Widget for specific post types.
* Updated import script, including compliance testing and friendly error messages.
* Updated style names, e.g. mla is now modern-language-association.
* Updated look and feel to match WordPress 3.8+.
* Multiple bug fixes.

= 5.0.10 =

* Fixed display issue for multiple in-text citations per shortcode.
* Updated the Help/FAQ page.
* Restructured request class and enforced Zotoro API Version 1.
* Fixed style update errors related to undefined variables.
* Added "citeable" as an alternate for the "cite" attribute.

= 5.0.9 =

* Fixed incorrect abstracts issue.
* Fixed "years", "authors" and "nick" shortcode attributes.
* Help information for finding Group IDs has been updated.
* Lowercase style names enforced.

= 5.0.8 =

* New! Import functionality check added.
* New! Reset Zotpress feature on Options page.
* New! Added security measures to prevent direct access.
* New! Import items, collections and tags separately.
* Updated import script; sessions removed.
* Fixed minor shortcode bugs.
* Removed extraneous code.
* Minor style updates.

= 5.0.7 =

* New! Import script now uses WordPress functionality for improved compatibility.
* New! "Link" attribute for tag and collection lists.
* Updated help page to include how to use in-text brackets.
* In-text format updates, including new "etal" attribute.
* URLs are now converted two-ways to account for prior encoding.
* Updated styles for a more consistent look.

= 5.0.6 =

* Fixed incompatibility bugs with jQuery UI; some style updates, too.
* Fixed "abstract" attribute bug.
* Fixed in-text %num% numbering bug.
* In-text shortcodes are now unique to posts.
* Fixed hyperlink bug for special characters.
* Fixed search when no default account set.
* New! In-text citations are now hyperlinked to the generated bibliography.

= 5.0.5 =

* New! Can now display abstracts using the "abstract" parameter.
* Blank author fields now filled with other author (e.g. editor) information.
* Added support for %num% formatting option (in-text).
* Fixed front-end style update script for large (50+) items.
* Fixed "datatype" filtering error.
* Fixed typos in the Help page.
* Added default account constraint to Zotpress Reference results.

= 5.0.4 =

* Fixed table install, update and uninstall issues.
* New import and sync scripts for large libraries.
* Zotpress admin now accessible to Editors.
* Styles updated and Help page shortcode documentation redesigned.

= 5.0.3 =

* Fixed import and sync for large libraries.
* Removed autoupdate feature. Please use the sync feature for the time being.
* Fixed metabox widget error.
* Minor bug fixes.

= 5.02 =

* Fixed display of styles with numbered lists, e.g. IEEE, nature, etc.
* Fixed critical in-text citation bug when multiple accounts are synced.
* Long URLs in citations will now wrap.

= 5.01 =

* Critical patch for case-sensitive tables and import functionality.

= 5.0 =

* Happy new year! Zotpress is now optimized for Wordpress 3.5.
* Near-complete rewrite of the code for greater loading speeds across the board.
* Revamped Browse page and Reference Widget with autocomplete and a new shortcode builder.
* Expanded Zotpress In-Text capabilities, including multiple items and formatting.
* New shortcode parameters, including ones for filtering and providing RIS links for citing.
* Greater shortcode flexibility, including support for multiple items and new sort options.
* Many more additions and bug fixes. Explore and enjoy the new Zotpress!

= 4.5.4 =

* Fixed "downloadable" bug.
* Updated Help page.

= 4.5.3 =

* Styles for metabox tabs added.
* In-Text Bibliography fixed (for real this time).

= 4.5.2 =

* New! Options page to set blog-wide defaults (more settings coming).
* New(ish)! Post-specific style defaults.
* In-Text Bibliography display fixed AGAIN!

= 4.5.1 =

* In-Text Bibliography display fixed (or, made better).
* New! Set default citation style (for all posts, via Zotpress Reference Metabox Widget).

= 4.5 =

* New: Shortcode Creator in the Zotpress Reference Metabox Widget.
* "Show Image" and "Sort" bugs fixed.

= 4.4.1 =

* Security fix!

= 4.4 =

* A number of security measures added.
* Fixed "Help" page shortcode for in-text citations and private vs. public groups: oops!
* The Zotpress shortcode now accepts lists for these parameters: collection, item.
* Notes can now be shown, if made publicly available through Zotero.
* Zotpress Reference should now show up on custom post type writing/editing pages.
* Zotpress Reference now working with the latest versions of Chrome and Safari.

= 4.3 =

* Introducing "Zotpress InText", a new shortcode that let's you add in-text citations, and then auto-generates a bibliography for you. jQuery must be enabled. Only supports APA style; requests can be made in the forums. Use information can be found in your Zotpress installation's "Help" page.
* Recaching and auto-checking for new or updated Zotero data back in action.
* The "collection" shortcode parameter now working.
* Zotero XML data gathering functions optimized.
* Tags with spaces are now working again for the "tag" shortcode parameter.
* Tag shortcode parameter now accepts nonexistent tags.

= 4.2.7 =

* Error display error fixed.

= 4.2.6 =

* Fixed bullet image issue.

= 4.2.5 =

* Fixed sidebar issue: having an author is no longer required.

= 4.2.4 =

* Fixed sidebar widget error and display issue.
* Added more information to and sorting of citations listed in the Zotpress Reference widget.

= 4.2.3 =

* More friendly XML error messages, including ability to (at least try) repeating the Zotero request.
* Spite-ified most images for quicker display.
* Citation images can now be deleted.

= 4.2.2 =

* Bugfix: Typo!

= 4.2.1 =

* Bugfix: Limit issue resolved.

= 4.2 =

* Bugfix: Styles now working again.
* Bugfix: Now only grabbing top level items.
* Bugfix: Sidebar widget working again.
* Metabox widget refined: Limit removed, account info integrated, and tags and collections alphabetized.

= 4.1.1 =

* Bugfix: Can now sort by ASC or DESC order.

= 4.1 =

* Bugfixes: Filtering by author and date reinstated.
* New: Titles by year. (New parameter: title)

= 4.0 =

* Switched method of requesting from jQuery to PHP. Should mean a speed increase (particularly for Firefox users).
* Many shortcode parameters have been changed; these parameters are now deprecated: api_user_id (now userid), item_key (now item), tag_name (now tag), data_type (now datatype), collection_id (now collection), download (now downloadable), image (now showimage).
* New shortcode parameter "sortby" allows you to sort by "author" (first author) and "date" (publication date). By default, citations are sorted by latest added.

= 3.1.3 =

* Temporary fix for web servers that don't support long URLs. Unfortunately no special caching for these folks. New solution in the works.

= 3.1.2 =

* Added backwards compatibility measure with respect to the new api_user_id / nickname requirement.
* Fixed citation display positioning bugs.
* Applied new caching method to sidebar widget.

= 3.1.1 =

* Fix: Sidebar widget bug.

= 3.1 =

* New way of caching requests. Speed increase for requests that have already been cached.
* No more multiple accounts per shortcode. A "user_api_id" or "nickname" must be set.
* No more collection titles. You can use the Zotero Reference meta box to find and add this information above collection shortcode calls.

= 3.0.4 =

* Fixed display images issue.
* Separated out sidebar widget code from main file.

= 3.0.3 =

* Groups accounts citation display fixed.

= 3.0.2 =

* Meta box fixed in IE and Safari.
* Styles fixed in IE and Safari.

= 3.0.1 =

* Sidebar widget fixed.
* Styles in IE refined.
* Conditional OAuth messages implemented.

= 3.0 =

* New "Zotpress Reference" widget, meant to speed up the process of adding shortcodes to your posts and pages by allowing you to selectively search for ids directly on the add and edit pages.
* OAuth is now supported, which means that you don't have to go out of your way to generate the required private key for your Zotero account anymore (unless your server doesn't support OAuth, of course).
* I've changed the way Zotpress's admin splash page loads. Before, the page would hang until finished loading the latest citations from Zotero. This is a friendlier way of letting you know what Zotpress is up to.
* Manual re-caching and clear cache options added, for those who desire to refresh the cache at their leisure.
* Citations that have URLs will now have their URLs automatically hyperlinked.
* More IDs and classes added for greater CSS styling possibilities.
* Improved handling of multiple Zotpress shortcode calls on a single page.
* Code reduced and refined plugin-wide, which should equal an overall performance improvement.
* "Order" parameter no longer available, at least for now; see http://www.zotero.org/support/dev/server_api
* "Forcing cURL" option abandoned. If your server supports it, cURL will be used; otherwise, Zotpress will resort to file_get_contents().

= 2.6.1 =

* Can now give group accounts a public key.
* Downloads can now be accessed by anyone (assuming you've enabled downloading).

= 2.6 =

* Important: Reduced multiple instantiations of JavaScript.
* Download option added to Widget.
* Proper download links for PDFs implemented.

= 2.5.2 =

* Fixed image display for author/year citations.

= 2.5.1 =

* Fixed single citation display bug.

= 2.5 =

* Re-wrote display code.
* Tidied up JavaScript.
* Fixed update table code.

= 2.4 =

* Can now display by year.
* New option to display download links, should they be available.

= 2.3 =

* Fixed Group "invalid key" error.

= 2.2 =

* Fixed CURLOPT_FOLLOWLOCATION error.

= 2.1 =

* Now cURL-friendly again.

= 2.0 =

* Zotpress completely restructured.
* Most requests now made through PHP. Shortcode requests made through PHP/jQuery combo for user-friendliness on the front-end.
* Cross-user caching implemented. Updates request data every 10 minutes and only if request made.
* Increased security now that private keys are no longer exposed through JavaScript.
* Can now filter by Tag in admin.

= 1.6 =

* Critical request method issue fixed.

= 1.5 =

* Groups citation style issue fixed.

= 1.4 =

* Caching enabled, which should speed things up a bit.

= 1.3 =

* Added cURL, which is (maybe?) quicker, (definitely?) safer, and (more likely to be?) supported. Requests default to cURL first now.

= 1.2 =

* Optimized JavaScript functions. Fixed some grammatical errors on the Help page. More selective loading of JavaScript. And most importantly ... added a Zotpress widget option. This also means you can have more than one Zotpress call on a single page.

= 1.1 =

* Fixed up the readme.txt. Added a friendly redirect for new users. Made IE8-compliant. Moved some JS calls to footer. Now selectively loads some JS. Made tags and collections into lists for easier formatting.

= 1.0 =

* Zotpress makes its debut.

== Upgrade Notice ==

= 7.3.4 =

Fixed vulnerability related to optional OAuth authentication procedure.

= 7.3.2 =

Updated to work with PHP 8.

= 6.2.4 =

Updates what kinds of metadata the "download" feature will look for and what's removed from the database on reset. Minor style updates.
