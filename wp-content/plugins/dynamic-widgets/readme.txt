=== Dynamic Widgets ===
Contributors: vivwebs, Qurl
Tags: widget, dynamic, sidebar, custom, rules, logic, display, condition, conditional content, hide, show
Tested up to: 6.4
Stable tag: 1.6.3

Dynamic Widgets gives you full control on which pages a widget will display. It lets you dynamicly show or hide widgets on WordPress pages.

== Description ==
**Dynamic Widgets only supports classic WordPress widgets. If you're looking to use this plugin for conditionalizing Gutenberg blocks, [let us know](https://docs.google.com/forms/d/e/1FAIpQLSeiKnmBSkcz_av_XEm8Po--SE4n7cKD68g6radpk8hujxWS7Q/viewform?usp=sf_link) and we will email you when it's in the works.**

Dynamic Widgets gives you full control on which pages a widget will display. It lets you dynamically show or hide widgets on WordPress pages by setting conditional logic rules on a widget with just a few mouse clicks. No knowledge of PHP required. No fiddling around with conditional tags. You can set conditional rules by Role, Dates, Browser, Featured image, IP Address, Mobile devices, Theme Template, Language (WPML or QTranslate), URL, for the Homepage, Single Posts, Attachments, Pages, Authors, Categories, Tags, Archives, Error Page, Search Page, Custom Post Types, Custom Post Type Archives, Custom Taxonomies in Custom Post Types, Custom Taxonomies Archives, WPEC/WPSC Categories, BuddyPress Components, BuddyPress Groups, Pods pages and bbPress.

= Works or broken? =

If the plugin is broken for you, please let us know in the [Forum](http://wordpress.org/support/plugin/dynamic-widgets). We like to know, so we might be able to fix it to make the plugin also work for you.

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
  - Languages (WPML)
  - URL
  - Domain name / Server name
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
  - Languages (WPML) on language
  - URL on starting with URL, ending on URL or exact match
  - Domain name / Server name on name
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
	- QTranslate and it's forks (currently disabled)
	- Pods
	- WP MultiLingual (WPML)
	- WP Shopping Cart / WP E-Commerce (WPSC / WPEC)

* Language files provided:
	- Brazil Portuguese (pt_BR) by [Renato Tavares](http://www.renatotavares.com)
	- Chech (cs_CZ) by [Pavel Bilek](http://chcistranky.eu/zdarma/)
	- Chinese (Simplified) (zh_CN) by Hanolex
	- Danish (da_DK) by Morten Nalholm
	- Dutch (nl) by Jacco Drabbe
	- French (fr_FR) by Alexis Nomine
	- German (de_DE) by Daniel Bihler
	- Japanese (ja) by chacomv
	- Lithuanian (lt_LT) by Liudas Ali�auskas
	- Portuguese (pt_PT) by Pedro Nave
	- Serbo-Croatian (sr_RS) by [Borisa Djuraskovic](http://www.webhostinghub.com/)
	- Slovak (sk_SK) by Serg
	- Spanish (es_ES) by Eduardo Larequi

== Installation ==

Installation of this plugin is fairly easy:

1. Unpack `dynamic-widgets.zip`
2. Upload the whole directory and everything underneath to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Visit the Dynamic Widgets Configuration page (settings link).
5. Edit the desired widgets.

== Frequently Asked Questions ==
= What are the (system) requirements to use this plugin? =

1. A properly working WordPress site (doh!).
2. Your theme must have at least one dynamic sidebar.
3. Your theme must call `wp_head()`.
4. Minimum of PHP version 5.2.7, PHP 5.3 is highly recommended.

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

= How do I completely remove Dynamic Widgets? =

* Click at the bottom of the Widgets Overview page on the 'Advanced >' link.
* Now a button 'Uninstall' appears a bit below.
* Click that button.
* Confirm you really want to uninstall the plugin. After the cleanup, the plugin is deactivated automaticly.
* Remove the directory 'dynamic-widgets' underneath to the `/wp-content/plugins/` directory.

== Changelog ==


= Version 1.6.3 =

* Fixed "dynamic property" error
* Fixed "Fatal error: Uncaught TypeError"

= Version 1.6 =
* Several bugfixes including XSS vulnerability
* Fixed typos throughout UI

= Version 1.5.16 =

* Bugfix for Parameter must be an array or an object that implements Countable in dynwid_worker.php on line 526 when using PHP > 7.3

= Version 1.5.15 =

* Bugfix for Pages childs not being saved anymore and als going into opposite direction. Thanks to @sovabarmak for debugging and fixing!

= Version 1.5.14 =

* Bugfix for a problem introduced in WordPress 5 when using the Pages module. Kudo's to @fjaeker for doing debugging for this!

= Version 1.5.13 =

* Widened the database fields
* Added domain name / servername support

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

== Upgrade Notice ==

When you upgrade manually, do a deactivate - activate cycle of the plugin.

== Screenshots ==

1. Widgets overview page
2. Widget Options page
3. Widget with Dynamic Widgets info and link

== Privacy Policy ==

Dynamic Widgets does not collect any private data, nor does it send any private data to remote servers. Please be aware that widgets might do. Dynamic Widgets does not read, write or alter contents or functionality of a widget it self, so this is beyond the scope and control of Dynamic Widgets.
