=== Plugin Name ===
Contributors: boonebgorges, cuny-academic-commons
Tags: buddypress, comments, blogs, activity, non-members
Requires at least: WPMU 2.8, BuddyPress 1.1
Tested up to: WPMU 2.9.2, BuddyPress 1.2.3
Donate link: http://teleogistic.net/donate/
Stable tag: 1.3

Inserts blog comments from non-logged-in users into the activity stream

== Description ==

By default, BuddyPress does not include comments from non-members (or non-logged-in users more generally) in the sitewide activity stream. This plugin records activity items for those comments.

Please note: the latest version of this plugin (1.2) will NOT work with versions of BuddyPress between 1.2RC and 1.2.1. BP versions 1.2.2+ are supported. Please download an earlier version of this plugin for compatibility with older versions of BuddyPress

== Installation ==

* Upload the directory '/bp-include-non-member-comments/' to your WP plugins directory and activate from the Dashboard of the main blog.
* If you're using a version of BP prior to 1.2, you'll need to uncomment lines 13 and 14 of the plugin in order to activate it.

== Changelog ==

= 1.3 =
* Fixed some PHP warnings
* Improved PHP 5.4 performance
* Unit tests

= 1.2.1 =
* Added checks for spam status 
* Fixed bug that made approved comments from site members appear twice

= 1.2 =
* Adapted to BuddyPress's new comment activity recording method
* Comment approval now posts to the activity stream as well (as a new comment)

= 1.1.1 =
* Fixed comment link bug (thanks unknown!)

= 1.1 =
* Normalized file structure to latest BP standards (bp_init)
* Fixed problem with deprecated bp_post_get_permalink

= 1.0 =
* Initial release



