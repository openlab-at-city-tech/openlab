=== Admin Commenters Comments Count ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: commenters, comment count, comment author, comments, comment, admin, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 5.5
Stable tag: 1.9.4

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

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/admin-commenters-comments-count/) | [Plugin Directory Page](https://wordpress.org/plugins/admin-commenters-comments-count/) | [GitHub](https://github.com/coffee2code/admin-commenters-comments-count/) | [Author Homepage](https://coffee2code.com)


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

= 1.9.4 (2020-09-13) =
* Change: Convert to use of strict equality checks
* Change: Restructure unit test file structure
    * New: Create new subdirectory `phpunit/` to house all files related to unit testing
    * Change: Move `bin/` to `phpunit/bin/`
    * Change: Move `tests/bootstrap.php` to `phpunit/`
    * Change: Move `tests/` to `phpunit/tests/`
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Note compatibility through WP 5.5+
* Change: Tweak inline function documentation
* Change: Update list of TODO items to add some considerations to an existing item, fix a type, change sublist syntax
* Unit tests:
    * New: Add tests for `add_user_column()`, `enqueue_admin_css()`, `handle_column_data()`

= 1.9.3 (2020-06-03) =
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add to it)
* Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and remove commented-out code)
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* New: Unit tests: Add test and data provider for hooking actions and filters

= 1.9.2 (2019-12-07) =
* Fix: Correct typo in GitHub URL
* Unit test:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Update expected frontend output to include additional "ugc" (user generated content) value now included for `rel="nofollow"` attribute of commenter links
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/admin-commenters-comments-count/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.9.4 =
Trivial update: Restructured unit test file structure, expanded unit test coverage, and noted compatibility through WP 5.5+.

= 1.9.3 =
Trivial update: Added TODO.md file, updated a few URLs to be HTTPS, expanded unit testing, and noted compatibility through WP 5.4+

= 1.9.2 =
Trivial update: modernized unit tests, noted compatibility through WP 5.3+, and updated copyright date (2020)

= 1.9.1 =
Trivial update: tweaked plugin initialization, noted compatibility through WP 5.1+, created CHANGELOG.md to store historical changelog outside of readme.txt, and updated copyright date (2019).

= 1.9 =
Recommended update: fixed compatibility conflicts with Akismet; fixed incorrect counts for pingbacks/trackbacks; verified compatibility through WP 4.9; updated copyright date (2018).

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
