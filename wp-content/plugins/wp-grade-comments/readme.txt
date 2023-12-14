=== WP Grade Comments ===
Contributors: boonebgorges
Tags: comments, grade, course, privacy
Requires at least: 4.4
Tested up to: 6.2
Stable tag: 1.5.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WP Grade Comments makes it easy for instructors who use WordPress in a course setting to give private feedback and/or grades to post authors, all without leaving the familiar commenting interface.

== Description ==

When reading posts while logged in as an Administrator, the comment reply form will contain two additional checkboxes:

1. "Make this comment private" - When checked, only site Administrators and the author of the current post will be able to see the comment. Threaded to private comments, whether left by an admin or by the post author, are always private as well.
1. "Add a grade" - When checked, a Grade field will appear. Grades can appear in private or public comments, while the grade itself will always be private (visible only to the Administrator and the post author). Grades are also visible to administrators as a new column in Dashboard > Posts.

This plugin was developed for the [https://openlab.citytech.cuny.edu](City Tech OpenLab).

== Installation ==

1. Install and activate from Dashboard > Plugins.
2. That's it.

== Screenshots ==

1. Comment display form. Only administrators see this form.
2. Grades are always private, even in public comments.
3. Grades are visible on Dashboard > Posts.

== Changelog ==

= 1.5.0 =
* Allow post authors to post private comments.
* Ensure that comments are private if there's a grade attached.
* Don't show the "Comment (Private)" UI if the comment content is empty.

= 1.4.6 =
* More flexible sanitization of comment content.
* Ensure that a grade of 0 can be submitted.
* Don't show grades on RSS and Atom feeds, even for logged-in users.

= 1.4.5 =
* Allow empty comments to be submitted when a grade is present.

= 1.4.4 =
* Ensure that empty grades are not recorded in the database, which could cause visibility issues.

= 1.4.3 =
* Avoid fatal errors when checking compatibility with openlab-private-comments.

= 1.4.2 =
* Improved compatibility with the OpenLab Private Comments package of Commons In A Box.

= 1.4.1 =
* Hide comments from public feeds/streams when there's only a grade and no content.

= 1.4.0 =
* Introduce notice that educates admin about the effects of deactivating the plugin when there are private comments.
* Improve compatibility between form elements and theme styles.
* Allow comment authors to see private replies to those comments.
* Improve comment counts to account for private comments.

= 1.3.2 =
* Add custom CSS selectors for private comments and comments with grades.

= 1.3.1 =
* Allow empty comments when a grade is present.

= 1.3.0 =
* Add show/hide toggle for private comments.
* Improve internationalization support.
* Fix bug that prevented 0 grades from showing on Dashboard.

= 1.2.0 =
* Better support for query edge cases.
* Now requires WP 4.4+.
* On front-end, grades are now shown via show/hide toggle.
* Fix bug that caused untrashed private comments to generate BuddyPress activity items.
* Fix bug that prevented grades of 0 from being saved and displayed.
* Fix bug that caused labels not to display properly on some themes.
* Fix PHP notice.

= 1.1.1 =
* Improve compatibility with recent versions of BuddyPress.

= 1.1.0 =
* Allow grade and privacy to be edited when editing comment via Dashboard.
* Allow non-instructors to see their own grades on Dashboard > Posts.
* Add a grade column to Dashboard > Comments, instead of showing grades in the comment content.

= 1.0.2 =
* Ensure that comment privacy is respected for trashed comments
* Prevent non-admins from editing comments that are private or contain grades, even when they are the post author
* Ensure that comment privacy is respected in feeds
* Ensure that all private comments are visible to all administrators, not just the comment author

= 1.0.1 =
* Fix name of plugin in readme header

= 1.0.0 =
* Initial release
