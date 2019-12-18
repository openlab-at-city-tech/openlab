=== WP SIMILE Timeline ===
Contributors: freshlabs
Tags: api, widget, visualization, javascript, simile, timeline, time, posts, post, ajax, integration, rss, feed, links, lifestream, twitter, xml
Requires at least: 5.0.1
Tested up to: 5.2
Stable tag: 0.5.0

Integrates the SIMILE Timeline into WordPress and provides an option interface for the various timeline settings.

== Description ==
This plugin integrates the [SIMILE Timeline](http://www.simile-widgets.org/timeline/ "SIMILE Timeline at SIMILE Labs") into WordPress and provides an option interface for the various timeline settings.
Developed by SIMILE Labs, the Timeline is a web widget for visualizing temporal data. 

Features:

*   Include individual categories to be displayed in the timeline
*	Build a lifestream timeline with RSS feeds from your WordPress link archive
*   Customize colors for timeline bands and categories
*   Supports multiple instances on different pages by using a template tag
*   Display individual icons or images with custom fields
*	Display image attachments from posts in the timeline
*	Comes with English, German, Italian and Spanish language files

Thanks to:

*	[SIMILE Project](http://simile.mit.edu) for providing the Timeline script API and releasing useful, semantic applications.
*	[John Lim of PHP Everywhere](http://phplens.com/phpeverywhere/adodb_date_library) for creating the ADOdb Date Library, making date formatting with dates before 1970 a charm.
*	Gianni Diurno for the Italian translations.
*	[Marcis G.](http://pc.de) for the Belorussian translation

== Installation ==

1. Unpack the ZIP-archive
2. Delete or deactivate any previous versions of this plugin.
3. Copy or upload the folder `wp-simile-timeline` to the `/wp-content/plugins/`directory
4. Activate the plugin through the WordPress plugins page
5. Include `<?php stl_simile_timeline(); ?>` or the shortcode `[similetimeline]` in your page or post
6. Specify the page ID of the page where you included the Timeline on the Options > SIMILE Timeline page
	This will output the necessary JavaScript and CSS in the HTML head
	(Set this value to 0 to include the Timeline script on all pages)
7. Enable categories on the Timeline options page

= Documentation =

To include the Timeline in a page or post, you can use a PHP template tag or WordPress shortcode:

*	`[similetimeline]`
*	`<?php stl_simile_timeline(); ?>`

== Frequently Asked Questions ==

= General problem solving =

*	If you encounter JavaScript alert dialogs/popups, the timeline has initialization or parsing problems. These error messages are triggered inside the SIMILE API and cannot be controlled by the plugin author.
	For support in this direction I recommend the [SIMILE Google Group](http://groups.google.com/group/simile-widgets/) and the [SIMILE Wiki](http://code.google.com/p/simile-widgets/wiki/Timeline).
*	Most basic problems have been discussed and mostly fixed in the [blog comments](http://www.freshlabs.de/journal/archives/2006/10/wordpress-plugin-simile-timeline/#comments).
*	[http://groups.google.com/group/wp-simile-timeline](http://groups.google.com/group/wp-simile-timeline) is the official discussion group of this plugin which replaces the blog comment support since 21-03-2009. 

= The timeline does not show up in my page or post =

*	Make sure you have the shortcode `[similetimeline]` included or `<?php stl_simile_timeline(); ?>` set up in your page or post.
*	Make sure the page-ID setting for the timeline is either 0 (zero) or an individual post ID. This option sets whether the neccessary JavaScript is loaded in the &#60;head&#62; section of your HTML. 
*	Try to clean your browser cache and then refresh the timeline page. Since the widget uses loads of JavaScript, changed settings often take a while until they replace the cached version.
*	Make sure that you have included the `wp_head()` function in your template header file. Otherwise the required JavaScript library by SIMILE will not be loaded.

= The timeline does not show any posts =

*	The content for the timeline is retrieved inside the XML file `/wp-content/plugins/wp-simile-timeline/data/timeline.xml.php`. Try to
call that file directly to see if posts show up there.
*	When the XML file shows posts and seems valid, try to clean your browser cache and refresh the timeline page.
*	Make sure you have checked at least one category to be timelined on the settings page and that there are public posts in that category.

= There are no titles showing in the timeline =

*	Please check if you have marked the checkbox *Show labels* for the desired band.
*	Try to clean your browser cache and then refresh the timeline page. Since the widget uses loads of JavaScript, changed settings often take a while until they replace the cached version.

== Screenshots ==

1. Timeline Widget Example
2. Configuration page
3. Content option page
4. Design and layout option page
5. Plugin Extras: Uninstaller and contextual help

== Upgrade Notice ==

= 0.5.1 =
Typo fixed

= 0.5 =
WordPress 5.2 + compatibility
PHP 7.2.1 + compatibility
Date format fixes

= 0.4.9 =
WordPress 4.0 compatibility
Fixes calls to readTerm() with empty ID
Fixed sql statements for creating additional posts columns

= 0.4.8.6 =
Fixed wpdb::prepare() calls with second parameter

= 0.4.8.5 =
Fix for custom taxonomies with empty query_var
Fixes 'insufficient permissions error' when deleting hotzones
Fix for direct link of posts when image attachments are shown in timeline
Event dates enabled for custom post types with category or post_tag support

= 0.4.8.4 =
Fixes issue with uncontrollable redirect to __history.html__?0
Dropped support for WordPress 2.x
Support for custom taxonomies

= 0.4.8.3 =
Fixes issue with hotzones and decorators not saving

= 0.4.8.2 =
Fixes initialization issues with WP 3.0 (Status code 404 on external scripts)

= 0.4.8.1 =
Fixes initialization issues when prototype.js is used (empty timeline frame)

= 0.4.8 =
Fixes missing argument error in template function & WordPress 3.0 compatibility

== Changelog ==

**0.4.9 (26/09/2014)**

*       WordPress 4.0 compatibility
*       Fixed start and end dates for posts
*	Fixed faulty SQL queries
*       Various bugfixes

**0.4.8.6 (10/01/2013)**

*	Fixed wpdb::prepare() calls with second parameter

**0.4.8.5 (21/03/2012)**

*	Fix for custom taxonomies with empty query_var
*	Fixes 'insufficient permissions error' when deleting hotzones
*	Fix for direct link of posts when image attachments are shown in timeline
*	Event dates enabled for custom post types with category or post_tag support

**0.4.8.4 (12/12/2011)**

*	Fixes issue with uncontrollable redirect to __history.html__?0
*	Support for WordPress tags and custom taxonomies
*	Support for custom post types that use standard taxonomies (categories and tags)
*	WordPress 3.3 compatibility (WordPress 2.x not supported anymore)


**0.4.8.3 (21/07/2010)**

*	Changed the date formatting for info bubbles according to WordPress date/time option
*	New option to show/hide the event date inside the popup bubble
*	Correctly parsing RSS links with SimplePie (fixes XML parsing errors)
*	Fixed issues with hotzones and decorators not saving at all on certain installations
*	Plugin activation hook is now correctly executed

**0.4.8.2 (21/06/2010)**

*	Fixed initialization issues with WP 3.0 (Status code 404 on external scripts)

**0.4.8.1 (16/06/2010)**

*	Fixed initialization issues when prototype.js is used (empty timeline frame)

**0.4.8 (13/06/2010)**

*	Fixed missing argument error in template function
*	Timeline is correctly inserted at the position of the shortcode
*	Timeline start and stop boundaries can be defined
*	Added contextual documentation links (WordPress help tab)
*	Added Belorussian language files (thanks to Marcis G.)
*	Various fixes for WordPress 3.0 compatibility

**0.4.7 (18/12/2009)**

*	Implemented contextual help links (inside the small help tab in the admin interface). The documentation will move to the Google group soon. 
*	Renamed custom stylesheet template timeline.css to /data/custom.css
*	Fully switched admin page implementation to PHP classes
*	Updated Italian locale
*	Updated screenshots
*	Fixed bug when creating link categories
*	JavaScript bugfixes for UI tabs
*	Admin CSS, JavaScript fixes
*	WordPress 2.9 ready

**0.4.6.6 (05/12/2009)**

*	Fixed output post content in popup bubbles (Formatting and more tag are preseved)
*	Added confirmation dialog on hotzone and decorator deletion (Admin page)

**0.4.6.5 (25/11/2009)**

*	Admin page bugfixes
*	Reverted to default styling of event icons (removes grey border)
*	Solved issues with PHP 4 compatibility

**0.4.6.4 (24/11/2009)**

*	Added support for customizable Hotzones and Highlight Decorators
*	New option to use image attachments from posts to display in the timeline (Compact Painter)
*	Uses new API version from SIMILE servers 
*	Updated interface options in the design tab
*	Fixed saving bug of active categories (again)
*	Fixed color picker position on options page
*	Implemented security measures for query strings in frontend files (js,xml)
*	Fixed bug when adding categories after plugin initialization

**0.4.6.3 (23/10/2009)**

*	Fixed two misspelled language namespace identifiers on options page
*	Updated i18n: Fully translated in Italian

**0.4.6.2 (23/10/2009)**

*	Markup fixes on admin options page (plus new color picker)
*	Compatible up to WordPress 2.8.5

**0.4.6.1 (20/10/2009)**

*	Fixes major bug when saving category settings on timeline options page (due to a deprecated action hook).
*	Updated i18n: Fully translated in German

**0.4.6 (08/09/2009)**

*	WordPress 2.8.4 compatibility
*	Markup and stability fixes on option pages. Fixes Setting-get-erased-bug (thanks to Shane and Erin)
*	Fixed Jump to link location-bug (thanks to rudevich)
*	JavaScript fixes on option pages (jQuery tabs)

**0.4.5.4 (02/04/2009)**

*	Fixed collision with JavaScript (jQuery UI Tabs) when inserting media in posts.

**0.4.5.3 (22/03/2009)**

*	Fixed: Database-options were initialized with empty values

**0.4.5.2 (21/03/2009)**

*	IMPORTANT: The plugin drops compatibility with WP 2.3 due to the usage of WP 2.5 specific post interface methods as well as jQuery UI tabs.
*	IMPORTANT: The custom field options `timeline_icon`, `timeline_image` and `timeline_link` were renamed! See the online-documentation for new syntax!
*	Implemented new date options for imprecise events (latestStart and earliestEnd) in post panel
*	Tab-enhanced admin interface using WordPress built-in jQuery UI Tabs (renders plugin only compatible with WP 2.5+)
*	Implemented uninstall method to remove plugin-related database entries (also helpful for troubleshooting)
*	Removed redundant color settings in JavaScript files (all done with CSS now)
*	Fixed: Only active categories with at least one post were listed on the options page
*	Fixed: IE JavaScript error in onload.js ('Event' is undefined) by simply removing that statement

**0.4.5.1 (11/03/2009)**

*	Updated SIMILE API URL to new location
*	Generic fixes for changes in the new SIMILE API
*	Timeline-themes are handled completely via CSS (An integrated dynamic theme processes the option settings).
*	Updated Italian locale files (thanks to Gianni Diurno)
*	Fixed directory names in FAQ

**0.4.5 (01/03/2009)**

*	Implemented new shortcode and function parameter `scriptfile` to load custom JS init file from the data folder.
*	Added reset option for start and end date option in post interface.
*	Simplified JavaScript initialization (thanks to Pete Myers).
*	Added new option for the timeline start focus.
*	Added new option to change click event for timeline entries.

**0.4.4 (14/01/2009)**

*	Included [ADOdb Date Time Library](http://phplens.com/phpeverywhere/adodb_date_library) to make dates before 1970 possible (replaced `mysql2date()` with `adodb_date2()`).
*	Added version check on option-page entry to prevent update and initialisation problems

**0.4.3 (07/01/2009)**

*	New post option to define event start date (in addition to the end date) to visualize durations
*	Fixed date formatting in XML file (thanks to Ben ter Stal)
*	Fixed IE XML parsing errors 

**0.4.2 (04/01/2009)**

*	Extended shortcode and template functions with optional parameters for categories
*	Removed duplicate closing brace in timeline.xml
*	Fixed link to options page inside plugin description

**0.4.1 (30/12/2008)**

*	Fixed whitespace error in XML data when using `timeline_icon` or `timeline_image`

**0.4.0 (02/11/2008)**

*	Fixed PHP error on plugin activation.
*	Included Italian locale files (contributed by Gianni Diurno)

**0.3.9a (20/10/2008)**

*	Link categories can also be selected for the timeline: If a link holds an RSS-link, the specific feed is parsed and read into the timeline.
	Specify a link image to style your feed entries in the timeline (e.g a favicon of the feed service).
*	Refactored acquisition of categories with WP core functions (taxonomy.php)

**0.3.8a (17/10/2008)**

*	Fixed blank admin menu page on some installations. Now uses the correct API hook `admin_menu`.
*	Fixed inital installation of database table for MySQL4 and default configuration options.

**0.3.7a (15/10/2008)**

*	Added optional custom field *timeline_link* to define a custom link for the title inside the timeline bubble.
*	Set showposts to maximum. All entries from the selected categories are shown.
*	Fixed XML output of the content using WordPress core sanitization functions from `wp-includes/formatting`. Styled content is possible now.
*	Introduced localized option page for en_US and de_DE. Contributors are welcome!

**0.3.6a (14/10/2008)**

*	Added options for timeline resolution (year, month, week, day, hour) and its interval width in pixels. This replaces the vague resolution selection from previous versions and allows better customisation.
*	Fixed markup and CSS for end date in the post write panel.
  
**0.3.5a (12/10/2008)**

*	Fixed broken option page form action
  
**0.3.4a (11/10/2008)**

*	Fixed add_option_page file parameter. Got invalid link options page on some installations
*	Fixed register_activation_hook for installing Timeline category table
*	Fixed plugin folder references due to new foldername by WP plugin directory
*	Replaced key-comment with shortcode [similetimeline]
  
**0.3.3a**

*	WP 2.6 compatibility
*	Added date string to javascript file call to prevent caching issues
*	Updated backend GUI with new WordPress CSS classes
*	Fixed the loop in the xml file. The content now shows up in the info bubbles.
*	Added Ootion to display future posts
  
**0.3.2a**

*	WP 2.3 compatibility. post2cat queries replaced with inner joins on the taxonomy tables (see http://wordpress.org/support/topic/137793)
*	Added database table stl_timeline_terms for category options to database (outsourcing from core tables)
  
**0.3.1a**

*	Fixed option "Show Labels" - nothing was saved because of a typo in the name attribute
  
**0.3a**

*	Colors can be defined for categories and the Timeline design
*	Future Posts that are published will be displayed by the timeline
*	Loading notification for larger timelines now works
*	WordPress MU compatibility
*	Introduced CSS file to style timeline size, border and info bubbles
  
**0.2a**

*	Posts can now have an end-date to visualize durations
*	Titles are linked to full entries
*	Multiple categories can be "timelined"
  		
**0.1a**

*	(17/10/2006) r69: The Timeline JavaScript is now loaded only on the page where a timeline is found. This reduces server load at the Simile labs ;)
*	(16/10/2006) r66: Fixed a JavaScript error on pages where no timeline is included. The script now degrades gracefully.
*	(15/10/2006) r60: Initial release