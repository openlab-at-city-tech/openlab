=== Reckoning ===
Contributors: shawn-patrick-rice
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=rice@shawnrice.com
Tags: comments, posts, tools, reckoning, reckon, tally, assessment, teaching, education
Requires at least: 3.5.1
Tested up to: 4.7.2
Stable tag: 2.0.1
License: MIT
License URI: https://opensource.org/licenses/MIT

Adds a submenu (under Users) that tallies all the users' posts and comments on a blog, especially useful for assessment of blogs for classes.

== Description ==

Reckoning provides a nice overview of a blog's content sorted by user. It provides a page in the Admin Dashboard page ("Users->User Summary") that lists the users' post titles and dates underneath their names with a total count, and it does the same for the comments. If you click on an author's name, then you'll see an overview page for the author with the same information, but the content of the posts and the comments will be listed there too.

This plugin creates no database tables, and it does not store or alter any data; it simply displays data in a convenient fashion.

The initial use-case for Reckoning was for professors who use class blogs to assess their students' work at the end of the semester (read: The Reckoning). It basically gives a birds-eye-view of who has posted and commented.

Note: WordPress Multisite only.

<img src='https://raw.githubusercontent.com/shawnrice/reckoning/master/assets/screenshot-1.png' width='250px' alt='screenshot' />

_Developed for [Blogs@Baruch](http://blsciblogs.baruch.cuny.edu/) with support from CUNY: Baruch College's [Bernard L. Schwartz Communications Institute](http://blsci.baruch.cuny.edu) and the [Center for Teaching and Learning](http://ctl.baruch.cuny.edu)._

== Screenshots ==

1. The overview page
2. Comments on a single author page
3. A non-prolific single author page
4. The menu item (called "User Summary")

== Installation ==

Just install like you would regularly. This plugin does not create any table or data; it simply displays some data nicely.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'reckoning'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `reckoning.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `reckoning.zip`
2. Extract the `reckoning` directory to your computer
3. Upload the `reckoning` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Github Repo ==

Please report any issues with the plugin via the [Github repository](https://github.com/shawnrice/wp-reckoning).

== Changelog ==
= 2.0.1 =
* Rollback array declarations to be compatible with PHP < 5.4

= 2.0.0 =
* Include private posts (breaking change) [PR](https://github.com/shawnrice/reckoning/pull/2) [@boonebgorges](https://github.com/boonebgorges)
* Fix coding standards
* Better sanitization

= 1.0.1 =
* Added headers to tables for clarity.

= 1.0.0 =
* Initial Commit.
