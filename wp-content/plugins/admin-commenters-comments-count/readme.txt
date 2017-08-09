=== Admin Commenters Comments Count ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: commenters, comment count, comment author, comments, comment, admin, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 1.8

Displays a count of each commenter's total number of comments (linked to those comments) next to their name on any admin page.


== Description ==

Next to all appearances of each commenter's name in the admin, this plugin shows a comments bubble identical to the one shown for posts in the admin listing of posts. The comments bubble shows the number of approved comments for that person and potentially a red superscript circle indicating the number of pending comments for the person (assuming they have any). The comment counts are linked to listings of comments associated solely with that particular commenter.

By default in WordPress, it is not possible to tell via a single glance whether a particular commenter has commented before or how many times the've commented.

This plugin adds this handy capability to the WordPress admin pages that allows you to:

* Quickly identify a first-time commenter
* Quickly identify unfamiliar commenters that have in fact commented before
* Quickly see how many total comments a particular commenter has made, and how many comments are pending
* Easily navigate to a listing of all approved comments and all moderated comments by a commenter, in order to see what post and when they last commented (or first commented), get a feel for the nature of their comments, or find something they've said in the past

Specifically, the linked comment count appears next to commenters in:

* The "Comments" listing of comments (including comment search results)
* The "Comments for 'POST_TITLE'" listing of post-specific comments
* The "Discussion" box of the "Edit Post" page for a post with comments
* The "Recent Comments" admin dashboard widget
* The "Users" listing of users (as the column "Comments")

Commenters are identified by the email address they provided when commenting. If your site does not require that commenters submit their email address when commenting, this plugin will use the commenter's name as the identifier, though since this is a publicly viewable piece of data it's possible that multiple people could be posting under the same "name", so this method has the potential to be not as accurate.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/admin-commenters-comments-count/) | [Plugin Directory Page](https://wordpress.org/plugins/admin-commenters-comments-count/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or download and unzip `admin-commenters-comments-count.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
2. Activate the plugin through the 'Plugins' admin menu in WordPress


== Frequently Asked Questions ==

= Why would I want to see a count of how many comments someone made? =

There are many reasons, some of which might include:

* Quickly identify a first-time commenter
* Quickly identify unfamiliar commenters that have in fact commented before
* Quickly see how many total comments a particular commenter has made, and how many comments are pending
* Easily navigate to a listing of all approved comments and all moderated comments by a commenter, in order to see what post and when they last commented (or first commented), get a feel for the nature of their comments, or find something they've said in the past

= How does the plugin know about all of the comments someone made to the site? =

Commenters are identified by the email address they provided when making a comment. If commenters are allowed to omit providing an email address, then their name is used to identify them (though this is potentially less accurate).

= Why does it report someone as having less comments than I know they've actually made? =

Since commenters are identified by the email address they provided when making a comment, if they supply an alternative email address for a comment, the plugin treats that email address as a separate person.

= How do I hide (or show) the "Comments" column in the listing of the admin Users page? =

Click the "Screen Options" link in the upper-right of the page. It will slide down a form. Click (or unclick) the checkbox for "Comments" to show (or hide) the column.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the 'Comments' admin page with the comment count appearing next to the commenter's name. The most recent comment is from someone who has one approved commented on the site. The second comment is from someone who hasn't commented on the site before and has one comment in moderation. The third comment is from someone who has commented 12 times before and has 3 additional comments in moderation.
2. A screenshot of the 'Comments on POST TITLE' admin page with the comment count appearing next to the commenter's name.
3. A screenshot of the 'Activity' admin dashboard widget with the comment count appearing next to the commenter's name.
4. A screenshot of the 'Comments' metabox on the 'Edit Post' admin page with the comment count appearing next to the commenter's name.
5. A screenshot of the 'Comments' column added to the admin users listing.


== Changelog ==

= 1.8 (2017-03-04) =
* Change: Adopt WP core style of showing pending comments in a red circle superscript to comments bubble icon
* Change: Show comments bubble in "Comments" column of user listings instead of plain integer
* Change: Don't link comments bubble when there are zero comments since approved and pending comments are available as separate links
* Change: Extract comments bubble markup generation from `comment_author()` into new `get_comments_bubble()`
* Change: Remove support for pre-WP 4.3 markup
* Change: Prevent object instantiation of the class
* Change: Use `sprintf()` to produce markup rather than concatenating various strings, function calls, and variables
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Note compatibility through WP 4.7+
* Change: Remove support for WordPress older than 4.6 (should still work for earlier versions back to WP 4.3)
* Change: Update readme.txt content and formatting
* Change: Update copyright date (2017)
* Change: Update screenshots
* New: Add LICENSE file

= 1.7 (2016-01-11) =
* Bugfix: Fix bug preventing display of values for other custom columns in users table.
* Add: Memoize commenter counts so they aren't re-queried more than once per page load.
* Add: Add support for language packs by loading textdomain and explicit using it.
* Add: Add inline docs for class variables.
* Add: Add public method `reset_cache()`.
* Add: Add private variable `$memoized`.
* Change: Note compatibility through WP 4.4+.
* Change: Explicitly declare methods in unit tests as public.
* Change: Update copyright date (2016).
* Add: Create empty index.php to prevent files from being listed if web server has enabled directory listings.

= 1.6 (2015-09-19) =
* Bugfix: Add support for changes in WP 4.3 (to fix display of comment bubble background).
* Change: Minor inline documentation spacing tweaks.
* Add: Add 'Text Domain' field to plugin header.
* Change: Note compatibility through WP 4.3+.

= 1.5 (2015-02-05) =
* Add `is_admin()` check to `comment_author()`
* Always load class rather than just in the admin
* Add to, and improve, unit tests
* Add screenshot showing 'Comments' column in user listing
* Curly-braced variables used in strings
* Note compatibility through WP 4.1+
* Update copyright date (2015)

= 1.4 (2014-08-30) =
* Add 'Comments' column to admin user listing with linked count of that user's comments
* Modify markup output to accommodate changes made in WP 3.9
* Abstract comment count logic into get_comments_count()
* Abstract admin comments URL link into get_comments_url()
* Remove commented out styles
* Minor plugin header reformatting
* Add more unit tests
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Note compatibility through WP 4.0+
* Drop compatibility with version of WP older than 3.9
* Change banner image
* Add plugin icon

= 1.3 (2013-12-23) =
* Enqueue custom CSS file instead of adding CSS to page head
* Change CSS to allow comment bubbles to take on colors of active admin theme
* Change initialization to fire on 'plugins_loaded'
* Add unit tests
* Minor documentation tweaks
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.8
* Update copyright date (2014)
* Change donate link
* Update screenshots for WP 3.8 admin refresh
* Update banner for WP 3.8 admin refresh

= 1.2.1 =
* Add check to prevent execution of code if file is directly accessed
* Note compatibility through WP 3.5+
* Update copyright date (2013)
* Move screenshots into repo's assets directory

= 1.2 =
* Add CSS rule to set text color to white to supersede CSS styling done by latest Akismet
* Default to gray comment bubble
* Show blue comment bubble for authors with pending comment (consistent with how WP does it for posts)
* Add 'author-com-pending' class to link when author has pending comments
* Show orange comment bubble on hover over comment bubble
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Add banner image for plugin page
* Remove ending PHP close tag
* Note compatibility through WP 3.4+

= 1.1.4 =
* Bugfix for notices when non-standard comment types are present (by explicitly supporting pingbacks and trackbacks, and ignoring non-standard comment types)
* CSS tweak to prevent top of comment bubble from being clipped
* Prefix class name with 'c2c_'
* Add version() to return plugin version
* Note compatibility through WP 3.3+
* Fix typo in readme.txt
* Update screenshot-3
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

= 1.1.3 =
* Properly encode emails in links to commenter's comments listing (fixes bug where a '+' in email prevented being able to see their listing)
* Invoke class function internally via self instead of using actual classname
* Note compatibility through WP 3.2+
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

= 1.1.2 =
* Explicitly declare all class functions public static
* Minor code reformatting (spacing) and doc tweaks
* Note compatibility with WP 3.1+
* Update copyright date (2011)

= 1.1.1. =
* Bug fix (missing argument for sprintf() replacement)

= 1.1 =
* If a commenter does not have an email provided, search for other comments based on the provided name
* Treat class as a namespace rather than instantiating it as an object
* Check for is_admin() before defining class rather than during constructor
* Proper conditional string pluralization and localization support
* Use esc_attr() instead of attribute_escape()
* Fix dashboard display of commenter comment counts (prevent clipping of top of bubble, bubble background is now blue instead of gray)
* No longer define background-position in CSS
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Minor code reformatting (spacing)
* Add package info to top of plugin file
* Remove trailing whitespace in docs
* Add Upgrade Notice section to readme.txt
* Note compatibility with WP 3.0+
* Drop compatibility with version of WP older than 2.8

= 1.0.1 =
* Add PHPDoc documentation
* Note compatibility with WP 2.9+
* Update copyright date and readme.txt

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.8 =
Recommended update: adopted WP comments bubble red circle superscript to display pending comments count, shown comments bubble in users listing, noted compatibility through WP 4.7+, dropped compatibility with WP older than 4.6, more

= 1.7 =
Recommended update: bugfix for causing blank custom user columns, adjustments to utilize language packs, minor unit test tweaks, noted compatibility through WP 4.4+, and updated copyright date

= 1.6 =
Recommended update: fixed to display comment count background in WP 4.3; noted compatibility through WP 4.3+.

= 1.5 =
Minor update: added and improved unit tests, added new screenshot, noted compatibility through WP 4.1+, and updated copyright date.

= 1.4 =
Recommended update: fixed appearance through WP 4.0+; added "Comments" column to admin Users page; dropped pre-WP 3.9 compatibility; added plugin icon.

= 1.3 =
Recommended update: enqueue custom CSS file instead of adding to page head; added unit tests; modified initialization; noted compatibility through WP 3.8+; dropped pre-WP 3.8 compatibility

= 1.2.1 =
Trivial update: noted compatibility through WP 3.5+

= 1.2 =
Recommended update: minor interface changes related to comment bubble coloring; noted compatibility through WP 3.4+; explicitly stated license.

= 1.1.4 =
Minor bugfix update: prevent PHP notices when non-standard comment types are present; noted compatibility through WP 3.3+.

= 1.1.3 =
Minor bugfix update: properly encode emails in links to commenter's comments listing; noted compatibility through WP 3.2+.

= 1.1.2 =
Trivial update: noted compatibility with WP 3.1+ and updated copyright date.

= 1.1.1 =
Minor bug fix.

= 1.1 =
Recommended update. Highlights: search for other comments by commenter name if no email is provided, fixed clipping of comment bubble on admin dashboard, miscellaneous tweaks, verified WP 3.0 compatibility.
