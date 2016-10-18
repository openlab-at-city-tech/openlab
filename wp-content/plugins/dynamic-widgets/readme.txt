=== Plugin Name ===
Contributors: Qurl
Donate link: http://www.qurl.nl/dynamic-widgets/donate/
Tags: widget, widgets, dynamic, sidebar, custom, rules, logic, display, admin, condition, conditional tags, hide, show, wpml, qtranslate, wpec, buddypress, pods, bbpress
Requires at least: 3.0.0
Tested up to: 4.6
Stable tag: 1.5.12

Dynamic Widgets gives you full control on which pages a widget will display. It lets you dynamicly show or hide widgets on WordPress pages.

== Description ==

Dynamic Widgets gives you full control on which pages a widget will display. It lets you dynamically show or hide widgets on WordPress pages by setting conditional logic rules on a widget with just a few mouse clicks. No knowledge of PHP required. No fiddling around with conditional tags. You can set conditional rules by Role, Dates, Browser, Featured image, IP Address, Mobile devices, Theme Template, Language (WPML or QTranslate), URL, for the Homepage, Single Posts, Attachments, Pages, Authors, Categories, Tags, Archives, Error Page, Search Page, Custom Post Types, Custom Post Type Archives, Custom Taxonomies in Custom Post Types, Custom Taxonomies Archives, WPEC/WPSC Categories, BuddyPress Components, BuddyPress Groups, Pods pages and bbPress.

For the latest news about Dynamic Widgets visit [my website](http://www.qurl.nl/).

= Works or broken? =

Please, vote for it. When you vote for broken, pick up some courage and tell in the [Forum](http://wordpress.org/support/plugin/dynamic-widgets) what is broken. I like to know, so I might be able to fix it to make the plugin also work for you.

= I need your support =

It is very hard to continue development and support for this plugin without contributions from users like you. If you enjoy using Dynamic Widgets and find it useful, please consider [making a donation](http://www.qurl.nl/dynamic-widgets/donate/). Your donation will help encourage and support the plugin's continued development and better user support.

= Features =

* Default widget display setting is supported for:
  - User roles
  - Dates
  - Day of week
  - Weeknumbers
  - Browsers
  - IP ranges
  - Featured image
  - Devices (mobile, desktop)
  - Theme Templates
  - Languages (WPML or QTranslate)
  - URL
  - Shortcode
  - Front page
  - Single post pages
  - Attachment pages
  - Pages
  - Author pages
  - Category pages
  - Tag pages
  - Archive pages
  - Error Page
  - Search Page
  - Custom Post Types
  - Custom Post Type Archive pages
  - Custom Taxonomy Archive pages
  - WP Shopping Cart / WP E-Commerce Categories
  - BuddyPress Components pages
  - BuddyPress Groups
  - Pods pages
  - bbPress User Profile pages

* Exception rules can be created for:
  - User roles on role, including not logged in (anonymous) users
  - Dates on from, to or range
  - Day of week on day
  - Weeknumer on number
  - Browsers on browser name
  - IP on ranges
  - Featued image on existence
  - Devices on type
  - Theme Templates on template name
  - Languages (WPML or QTranslate) on language
  - URL on starting with URL, ending on URL or exact match
  - Shortcode on value match
  - Front page on first page
  - Single post pages on Author, Categories (including inheritance from hierarchical parents), Tags, Custom Taxonomies and/or Individual posts
  - Pages on Page Title and Custom Taxonomies, including inheritance from hierarchical parents
  - Author pages on Author
  - Category pages on Category name, including inheritance from hierarchical parents
  - Tag pages on Tag
  - Custom Posts Type on Custom Taxonomy and Custom Post Name, including inheritance from hierarchical parents
  - Custom Posts Type Archive pages on Custom Post Type
  - Custom Taxonomy Archive pages on Custom Taxonomy Name, including inheritance from hierarchical parents
  - WP Shopping Cart / WP E-Commerce Categories on Category name
  - BuddyPress Component pages on Component
  - BuddyPress Groups on Group, including hierarchical Groups provided by BP Group Hierarchy or Component
  - Pods pages on page

* Plugin support for:
	- bbPress
	- BuddyPress
	- BuddyPress Group Hierarchy
	- QTranslate
	- Pods
	- WP MultiLingual (WPML)
	- WP Shopping Cart / WP E-Commerce (WPSC / WPEC)

* Language files provided:
	- Brazil Portuguese (pt_BR) by [Renato Tavares](http://www.renatotavares.com)
	- Chech (cs_CZ) by [Pavel Bilek](http://chcistranky.eu/zdarma/)
	- Chinese (Simplified) (zh_CN) by Hanolex
	- Danish (da_DK) by Morten Nalholm
	- French (fr_FR) by Alexis Nomine
	- German (de_DE) by Daniel Bihler
	- Japanese (ja) by chacomv
	- Lithuanian (lt_LT) by Liudas Ali�auskas
	- Portuguese (pt_PT) by Pedro Nave
	- Serbo-Croatian (sr_RS) by [Borisa Djuraskovic](http://www.webhostinghub.com/)
	- Spanish (es_ES) by Eduardo Larequi

== Installation ==

Installation of this plugin is fairly easy:

1. Unpack `dynamic-widgets.zip`
2. Upload the whole directory and everything underneath to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Visit the Dynamic Widgets Configuration page (settings link).
5. Edit the desired widgets.

== Frequently Asked Questions ==

For the latest FAQ, please visit the [online FAQ](http://www.qurl.nl/dynamic-widgets/faq/).

= What are the (system) requirements to use this plugin? =

1. A properly working WordPress site (doh!).
2. Your theme must have at least one dynamic sidebar.
3. Your theme must call `wp_head()`.
4. Minimum of PHP version 5.2.7, PHP 5.3 is highly recommended.

= My hoster is (still) using PHP4 =

Sorry, Dynamic Widgets is not compatible with PHP4. Look for another hosting company. The one you're having now don't deserve your money.

= I'm not sure my theme is calling `wp_head()`. Can I check? =

Yes, you can. In the Dynamic Widgets Overview page, click the 'Advanced >' link at the bottom. You should see if `wp_head()` is called in your theme. It is possible Dynamic Widgets can't detect if the theme is calling `wp_head()`. Please contact the author of the theme to ask for it. You can also of course just try Dynamic Widgets to see if it works.

= Does the plugin work on a WordPress Network? =

Yes, but only if you activate the plugin on a per site base. Network Activation is not supported.
Extra note: It seems that sometimes for some reason DW does not show up on individual sites within a WP Network without a network activation. You can use [Multisite Plugin Manager](http://wordpress.org/extend/plugins/multisite-plugin-manager/) to overcome this problem.

= I checked the "Make exception rule available to individual posts and tags" option, but nothing happens. =

Did you save the options? If you did, you may try to hit the (i) icon a bit to the right and read the text which appears below.

= What do you mean with logical AND / OR? =

A logical AND means that ALL rules must be met before the action takes place.
A logical OR means that when ANY rule is met, the action takes place.

= According to the featurelist I should be able to use a hierarchical structure in static pages, but I don't see it. Where is it? =

You probably have more than 500 pages. Building a tree with so many pages slows down the performance of the plugin dramatically. To prevent time-out errors, the child-function has been automatically disabled. You can however raise this limit by clicking on the 'Advanced >' link at the bottom of the Widgets Overview page and raise the number next to the Page limit box.

= The plugin slows down the loading of a page dramatically. Can you do something about it? =

Try setting the plugin to the 'OLD' method. You can do this by clicking on the 'Advanced >' link at the bottom of the Widgets Overview page and check the box next to 'Use OLD method'. See if that helps. Setting the plugin using the 'OLD' method comes with a downside unfortunately. It may leave you behind with a visible empty sidebar.

= I want to check if the 'OLD' method suits me better, is there a way back if it doesn't? =

Yes! You can switch between FILTER and OLD method without any loss of widgets configuration or whatsoever.

= I want in Page X the sidebar becomes empty, but instead several widgets are shown in that sidebar. Am I doing something wrong? =

Your theme probably uses a 'default display widgets policy'. When a sidebar becomes empty, the theme detects this and places widgets by default in it. The plugin can't do anything about that. Ask the theme creator how to fix this.

= I'm using WPEC 3.8 or higher and I don't see the WPEC Categories option anymore. Where is it? =

Since version 3.8, WPEC uses the by WordPress provided Custom Post Types and Custom Taxonomies. Dynamic Widgets supports Custom Post Types and Custom Taxonomies. You'll find the WPEC Categories under the 'Categories (Products)' section.

= You asked me to create a dump. How do I do that? =

* Click at the bottom of the Widgets Overview page on the 'Advanced >' link.
* Now a button 'Create dump' appears a bit below.
* Click that button.
* Save the text file.
* Remember where you saved it.

= I have found a bug! Now what? =

Please check the [Issue Tracker](http://www.qurl.nl/dynamic-widgets/issue-tracker/) first. The bug might have been reported and even maybe already fixed.  When not, you can file a [bugreport](http://www.qurl.nl/dynamic-widgets/bugreport/). Please note the procedure how to create a dump in the previous answer. After you've filed the report, I'll get back to you asap.

= How do I completely remove Dynamic Widgets? =

* Click at the bottom of the Widgets Overview page on the 'Advanced >' link.
* Now a button 'Uninstall' appears a bit below.
* Click that button.
* Confirm you really want to uninstall the plugin. After the cleanup, the plugin is deactivated automaticly.
* Remove the directory 'dynamic-widgets' underneath to the `/wp-content/plugins/` directory.

== Changelog ==

= Version 1.5.12 =

* Added conditional check for client IP address (handy when using CLI).
* Added support for featured image
* (Temporary) removed QTranslate and all it's forks support because of code clashes.

= Version 1.5.11 =

* Added Shortcode matching support by request of Nathan Wright of NW Consulting who made a financial contribution to make this feature possible.
* Fixed a possible vulnerability in the DW settings found by Mike Esptein

= Version 1.5.10 =

* Added Japanese language file (locale: ja) - Arigato chacomv!
* Added security preventing calling scripts creatively
* Added detection of QTranslate-X

= Version 1.5.9 =

* Added support for IP range
* Bugfix for URL and Pages module does not show the green checkmark and not showing the settings made
* Bugfix for Single Post Catregory is saved wrong
* Bugfix for Strict warning notice in author_module.php at line 42

= Version 1.5.8 =

* Renamed Mobile Device to Device for more expansion. WARNING: When you upgrade manually, remove the dynamic-widgets directory first!
* Removed the version number from the HTML comment.
* Fixed a few strict warnings.
* Fixed a few vulnerabilities discovered by Leon Juranic.

= Version 1.5.7 =

* Added Serbo-Croation language file (locale: sr_RS) - Hvala Borisa!
* Added Mobile detection support
* Bugfix for fatal error: Call to undefined function add_submenu_page() in dynamic-widgets.php on line 633 when the host is not meeting the requirements (anymore).
* Bugfix for several notices about an undefined index and deprecated wpdb::escape()
* Bugfix for Invalid arguments passed in custompost_module.php on line 216

= Version 1.5.6 =

* Added WordPress filter 'dynwid_urlprefix' in case Dynamic Widgets detects the URL wrong.
* Added WordPress filter 'dynwid_taxonomies' to be able to remove taxonomies from the admin view.
* Added WordPress filter 'dynwid_admin_modules' to be able to remove modules from the admin view by request of Rick Anderson from [Build Your Own Business Website](http://www.byobwebsite.com/) who made a financial contribution to make this possible.
* Added Toggle all sections button for visually impaired users.
* Bugfix for Notice: Trying to get property of non-object in post-template.php on line 1275.
* Bugfix for not using a full PHP opening tag in dynwid_admin_overview.php.
* Bugfix for exception rules in single post types sometimes not followed.
* Bugfix for Warning: Declaration of DW_CustomPost::registerOption() should be compatible with DWModule::registerOption($dwoption)
* Bugfix for WPML category fails to follow the rules with the help and financial support by [Advancis](http://advancis.net/).
* Changed the taxonomy terms tree to use 'lazy loading' by request of Rick Anderson from [Build Your Own Business Website](http://www.byobwebsite.com/) who made a financial contribution to make this possible.
* Moved minimum PHP version up to 5.2.7 because of a PHP bug in lower versions not exposing all class properties while in the right scope. Thanks S�bastien for finding out!

= Version 1.5.4 =

* Added Danish language file (locale: da_DK) - Tak Morten!
* Bugfix for WPML postID finder reports 0 when WPML language is unknown.
* Bugfix for Single Post Rules are lost when autosave of single post is made
* Bugfix for Warning: Missing argument 2 for wpdb::prepare().
* Upgraded the Pods module to support Pods version 2. - Thanks Scott for the help!
* Widend the widget_id space because of looooong widget names.
* Moved to WordPress time in stead of server time for the date/time based modules.

= Version 1.5.3 =

* Added URL support.
* Added Day of the week support.
* Added Weeknumber support.
* Added Custom Taxonomies support for Posts.
* Added Chech language files (locale: cs_CZ) - Dekuji Pavel!
* Added Brazil Portuguese language files (locale: pt_BR) - Obrigado Renato!
* Bugfix for Notice: Undefined property: stdClass::$post_name.
* Bugfix for Missing options (Archive, Error, Search); Stopped using the accordion.
* Bugfix for Notice: Undefined index: single_author_act in dynwid_admin_save.php on line 138.
* Bugfix for not showing the green checkmark when an option is set for single posts.

= Version 1.5.2 =

* Bugfix for Tag archive rules are not followed correctly.
* Bugfix to prevent the error: WidgetID is invalid.
* Security fix for a possible XSS vulnerability.

= Version 1.5.1 =

* Added Custom Taxonomies support for Pages
* Added Portuguese language files (locale: pt_PT) - Obrigado Pedro!
* Bugfix for Illegal offset type in isset or empty in /wp-includes/taxonomy.php on line 211.
* Bugfix for Options disappear when adding a widget to a sidebar.
* Bugfix for Taxonomy childs are displayed when Taxonomy is not hierarchical.
* Bugfix for deprecated call-time pass-by-reference in custompost_module.php on line 226.
* Bugfix for showing info balloon at Tag archive Pages.
* Bugfix for Invalid argument supplied for foreach() in dynwid_admin_save.php on line 158.
* Bugfix for Cannot modify header information - headers already sent,
* Changed the way the hierarchical tree for static pages is build. It should be faster now.

= Version 1.5.0 =

* Added Pods plugin support.
* Added Tag Archive Pages support.
* Added hierarchical inheritance for Categories.
* Added workaround to detect correct template when using Pods.
* Added negative exceptions for Role.
* Added an "Only on first page" exception for Front Page.
* Added support for BP Group Hierarchy.
* Added support for bbPress User Profile pages.
* Added Chinese (Simplified) language files (locale: zh_CN) - xie xie Hanolex!
* Added Lithuanian language files (locale: lt_LT) - dekoju Liudas!
* Added a "Save & Return" button to the widget options page.
* Bugfix for subscribers show up in the author lists in WP > 3.1.
* Bugfix for Taxonomies in Custom Post Types are not always saved.
* Bugfix for Custom Posts box is sometimes empty.
* Fixed several PHP notices about undefined variables.
* Removed support for individual Custom Posts.
* Removed PHP4 support, minimum PHP version is now 5.1.0.
* Removed WP < 3.0 support. Minimum WordPress version is now 3.0.

= Version 1.4.2 =

* Added QTranslate support.
* Added hierarchical inheritance for Custom Taxonomies and Custom Post Types.
* Added same behaviour in the Author list for WP < 3.1 as in WP > 3.1
* Bugfix for PHP error in WPML module.
* Bugfix for not showing WPML languages in WPML module.
* Bugfix for PHP error in Single posts module when using WP < 3.1.
* Bugfix for possible PHP notice when a child does not exist in the hierarchical tree.
* Bugfix for wrong page count.
* Broadend the capability for changing DW configuration from 'switch_themes' to 'edit_theme_options'.
* Disabled 'WPSC Category' when using WPEC > 3.8 as it's now covered by Custom Taxonomies Archives.

= Version 1.4.1 =

* Added Custom Taxonomies support for Custom Post Types.
* Added WPML support to Custom Taxonomies.
* Added support for Custom Taxonomies Archives.
* Added support for Theme Templates.
* Added hierarchical structure overview for Categories.
* Added Component exceptions support in BuddyPress Groups.
* Added a Quick setting: 'Set all options to Off'.
* Added 'Internet Explorer 6' to the browser detection.
* Added advanced option setting for the page limit.
* Bugfix for not selecting the WPML main language ID for Custom Post Types.
* Bugfix for showing all WPML translated Custom Post Type titles
* Bugfix for not correct displaying of options string in the widget admin when having options set for Custom Post Type Archives, BuddyPress, BuddyPress Groups.
* Bugfix for losing exception rules for single posts and tags in rare cases.
* Bugfix for showing empty Custom Post Type Archives option in settings screen.
* Bugfix for unexpected behaviour when setting BP groups default to 'No'.
* Bugfix for only showing the last Custom Posts in the list.
* Limited the list of authors to users with user level > 0. (WP 3.1 and higher)
* Security fix in the usage of the returnURL.
* Workaround when using prototype theme.
* Workaround for certain themes claiming an invalid BP component confusing Dynamic Widgets.

= Version 1.4.0 =

* Added more l10n text strings
* Added support for browser options.
* Added support for attachments.
* Added support for Custom Post Type Archive pages (native in WordPress 3.1, via plugin in 3.0.x).
* Added support for BuddyPress Component pages.
* Added support for BuddyPress Group pages.
* Added German language files (locale: de_DE) - Vielen dank Daniel!
* Added Spanish language files (locale: es_ES) - Muchas gracias Eduardo!
* Bugfix for unexpected behavior when subsequent widgets are in opposite config for WPML.
* Bugfix for not correct displaying of options string in the widget admin when having options set for childs in Pages or Custom Post Types.
* Bugfix for an error 404 (file not found) when an error occurs while saving options.
* Bugfix for unnecessary double creation of the hierarchical tree in Static Pages and Custom Post Types.
* Modified admin UI for compatibility with WordPress 3.1.
* Upgrade for WP Shopping Cart / WP E-Commerce to support version 3.8 *** SEE RELEASE NOTES ***
* Workaround in admin UI when using (a child of) the default BuddyPress theme or the BP Template Pack plugin bombing the accordion.
* Modularized admin scripts
* Standarized the use of JavaScript (jQuery)

= Version 1.3.7 =

* Added more l10n text strings.
* Added French language files (locale: fr_FR) - Merci beaucoup Alexis!
* Added language (WPML) as an option.
* Added hierarchical inheritance support for Pages and Custom Post Types
* Bugfix for unexpected behavior when two widgets are in opposite config of eachother.
* Fixed a couple of l10n text strings
* Changed UI in edit options screen (Thanks Alexis for the help!).
* Speeded up the removing process in FILTER method.

= Version 1.3.6 =

* Added l10n support.
* Added Dutch language files (locale: nl)
* Added support for WP Shopping Cart / WP E-Commerce Categories.
* Bugfix for error 404 (file not found) when saving options.
* Bugfix for unexpected behavior in subsequent category pages.
* Bugfix for unexpected behavior in single post when using individual exception rules.
* Bugfix for unexpected behavior in Custom Post Types.
* Bugfix for incorrect use and display of Custom Post Types in Widget Edit Options screen.
* Removed several PHP notices.

= Version 1.3.5 =

* Added support for themes which use the WP function is_active_sidebar() when the method is set to FILTER (default).
* Bugfix by removing a possible unnecessary loop for dynamic widget options.

= Version 1.3.4 =

* Bugfix for minor flaw "Invalid argument supplied for foreach() in dynwid_admin_save.php on line 203"

= Version 1.3.3 =

* Added Custom Post Types support for WordPress 3.0.
* Added WPML support for static pages, category pages, category in single posts and custom post types.
* Bugfix for not resetting checked count when enabling individual posts with authors and/or category set.

= Version 1.3.2 =

* Added an internal filter when checking for widget options to make the plugin faster.

= Version 1.3.1 =

* Maintenance release for WordPress 3.0 support.

= Version 1.3 =

* Added support for dates functionality.

= Version 1.2.6 =

* Another bugfix try for nasty PHP warning "Cannot use a scalar value as an array".

= Version 1.2.5 =

* Bugfix for user role detection when using SPF.

= Version 1.2.4 =

* Bugfix(?) for PHP warning "Cannot use a scalar value as an array"

= Version 1.2.3 =

* Added default widget display setting option for Search Page.

= Version 1.2.2 =

* Added detection for posts page when front page display is set to static page (more or less a bugfix for 1.2.1).

= Version 1.2.1 =

* Added functionality when front page display is set to static page.

= Version 1.2 =

* Added support for PHP4 (not fully tested).
* Added Dynamic Widgets info and edit link in the widgets admin itself.
* Added support for widget display setting options for Author Pages.
* Added support for Single Posts exception rules for tags.
* Added support for Single Posts exception rules for individual posts.
* Bugfix for rare cases not selecting the right default option for single posts.
* Bugfix for wrong exception rules were applied in rare cases when rules are set for a page or archive page.
* Bugfix for displaying confusing success and error message.
* Bugfix for not displaying checked checkboxes in MS Internet Explorer.
* Workaround to stop showing invalid (not clean unregistered) widgets without a name.
* Some small textual changes.
* Moved general helpinfo to standard WordPress contextual help screen.

= Version 1.1.1 =

* Bugfix for unexpected default option values when using role options.

= Version 1.1 =

* Added support for widget display settings based on role, including not logged in (anonymous) users.

= Version 1.0.1 =

* Added default widget display setting option for 'Not Found' Error (404) Page.

== Release notes ==

This version has 4 new features and 5 bugs fixed. See the changelog for details.

== Upgrade Notice ==

= 1.5.8 =
When you upgrade manually, remove the dynamic-widgets directory first.

This version has 1 feature added, 1 language added and 3 bugs fixed.

== Screenshots ==

1. Widgets overview page
2. Widget Options page
3. Widget with Dynamic Widgets info and link
