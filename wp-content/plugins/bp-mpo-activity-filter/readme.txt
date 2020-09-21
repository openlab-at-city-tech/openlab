=== BP MPO Activity Filter ===
Contributors: boonebgorges, cuny-academic-commons
Tags: buddypress, activity, privacy, more privacy options, filter
Requires at least: 3.5
Tested up to: 5.5
Requires PHP: 5.3
Donate link: http://teleogistic.net/donate/
Stable tag: 1.3.2

When using More Privacy Options, this plugin removes items from BP activity streams according to user roles.

== Description ==

More Privacy Options is a plugin for WPMu that allows blog owners to fine-tune their blog's privacy settings, expanding on the default privacy settings offered in the WP core. Putting this plugin together with BuddyPress has been problematic, however, because BuddyPress is not built to recognize the new privacy settings defined by MPO. As a result, even private blog posts get put into the public activity feed.

This plugin, BP MPO Activity Filter, does just what the name suggests: it filters BuddyPress activity feeds (wherever bp_has_activities appears) and filters the output based on the privacy settings of the source blogs. For example, if a blog is set to be visible only to logged in members of the community, BP MPO Activity Filter will only display activity items corresponding to that blog (both posts and comments) to users who are logged in. Sitewide administrators will have an unfiltered activity stream.

Activity items stored with BP 1.1.3 or lower have a slightly different data format, which makes them incompatible with this plugin.

I borrowed the idea, and a little bit of the code, from this plugin: http://blogs.zmml.uni-bremen.de/olio.

== Installation ==

* Upload the directory '/bp-mpo-activity-filter/' to your WP plugins directory and activate from the Dashboard of the main blog.

== Changelog ==

= 1.3.2 =
* Improves PHP 7 compatibility

= 1.3.1 =
* Fixes PHP 5.3 incompatibility

= 1.3.0 =
* Fixed bugs when checking for component activation
* Fixed bugs related to groupblog activity items

= 1.2.1 =
* Fixed bug in activity filter callback.
* Fixed bug that caused new_blog activity items not to be filtered properly.
* Fixed compatibility with BP Groupblog.
* Added filter for additional activity types.

= 1.2 =
* Refactored some queries to avoid unnecessary switch_to_blog() usage

= 1.1.1 =
* Oops

= 1.1 =
* Upgraded to reduce unnecessary switch_to_blog()

= 1.0.1 =
* Added code to ensure that plugin is not loaded before BuddyPress is
* Updated readme file to include more information on compatibility with BP < 1.2

= 1.0 =
* Initial release

