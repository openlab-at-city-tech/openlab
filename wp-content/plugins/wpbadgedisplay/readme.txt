=== WPBadgeDisplay ===
Contributors: davelester,mackensen
Tags: Awards,badges,openbadges,widget,wpbadgedisplay
Requires at least: 3.4.2
Tested up to: 4.9.6
Stable tag: 1.1.0
License: MPL-2.0
License URI: https://www.mozilla.org/en-US/MPL/2.0/

Adds a widget for displaying Open Badges on your blog.

== Description ==
WPBadgeDisplay is a WordPress plugin for displaying [Open Badges](http://www.openbadges.org) on your blog. The plugin's theme widget allows users to easily configure the display of badges that are associated with a particular email address.

See the [WPBadgeDisplay wiki](https://github.com/LafColITS/WPBadgeDisplay/wiki) for details on the plugin's roadmap and contact information. If you run into a problem, share your problem on the [issue tracker](https://github.com/LafColITS/WPBadgeDisplay/issues).

== Installation ==
1. Download the WPBadgeDisplay plugin, moving the WPBadgeDisplay folder into the /wp-content/plugins/ directory on your server, and install it like any other WordPress plugin.
1. Add the badge widget to your theme by navigating to Appearance -> Widgets in the WordPress administrative panel. There, you can specify where you'd like to display badges (for example, your theme's main sidebar).
1. Configure the widget by adding the email address badges are associated with, and adding an optional title that will display above your badges.

== Privacy ==

WPBadgeDisplay stores an email and user id associated with an [Open Badges Backpack](https://backpack.openbadges.org/) account. This information is used to pull in data from Open Badges Backpack for display on the host site. The Badges data itself is not stored.

The email, user id, and badge data are not shared with any undisclosed third parties. This information can be deleted at any time by removing the email from the widget settings form, disabling or uninstalling this plugin altogether, or using the WordPress [Personal Data Eraser](https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-eraser-to-your-plugin/) feature.

A user can see the id of any WPBadgeDisplay widgets configured with their email, their Open Badges ID as determined by WPBadgeDisplay, and their Badge data as WPBadgeDisplay sees it, by using the WordPress [Personal Data Exporter](https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-exporter-to-your-plugin/)

== Changelog ==

= 1.1.0 =
* Updated for GDPR compliance
* Code cleanup

= 1.0.0 =
* Support for multiple widgets per site with different email addresses

= 0.9.0 =
* Updated to use production endpoints

= 0.8 =
* Initial release
