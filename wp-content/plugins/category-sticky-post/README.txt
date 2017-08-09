=== Category Sticky Post ===
Contributors: professionalthemes, philiparthurmoore
Donate link: https://www.paypal.me/professionalthemes
Tags: categories, post
Requires at least: 3.4.1
Tested up to: 4.4.2
Stable tag: 2.10.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Mark a post to be placed at the top of a specified category archive. It's sticky posts specifically for categories.

== Description ==

Category Sticky Post allows you to mark a post to be displayed - or stuck - to the top of each archive page for the specified
category.

Category Sticky Post...

* Allows you to select which category in which to stick a post
* Will display the post on the top of the first page of the archive just like built-in sticky posts
* Will only allow you to stick a single post per category
* Displays whether or not a post is stuck in a category on the Post Edit dashboard
* Provides light styling that should look good in most themes
* Is available on each post editor page
* Is fully localized and ready for translation

For more information or to follow the project, check out the [project page](http://tommcfarlin.com/category-sticky-post/).

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' Plugin Dashboard
1. Select `category-sticky-post.zip` from your computer
1. Upload
1. Activate the plugin on the WordPress Plugin Dashboard

= Using FTP =

1. Extract `category-sticky-post.zip` to your computer
1. Upload the `category-sticky-post` directory to your `wp-content/plugins` directory
1. Activate the plugin on the WordPress Plugins dashboard

== Screenshots ==

1. A post marked as a 'Category Sticky' displaying at the top of an archive page
2. The new menu item for selecting which in which category to stick the given post
3. Disabled options show that a category already has a sticky post
4. The post dashboard indicating which entries are category sticky posts

== Changelog ==

= 2.10.2 =

* Change plugin authorship.

= 2.10.1 =

* Fix to plugin ownership name.

= 2.10.0 =

* Changing plugin ownership.

= 2.9.0 =

* Adding Serbian language translation (props George Dragojevic)

= 2.8.0 =

* WordPress 4.3 Compatibility
* Updating author URLs
* Removing the `disabled` functionality that would prevent you from selecting the same
  category a post originally had (props marc)
* Removing some unused functions
* Cleaning up some of the PHP

= 2.7.0 =

* WordPress 4.2.1 compatibility
* Updating copyright dates

= 2.6.0 =

* WordPress 4.0 compatibility
* Checking the main query to avoid conflicts with other plugins that deal with the main query


= 2.4.0 =
* Verifying WordPress 3.9 compatibility

= 2.3.0 =
* Removing the ability to add the sticky post to Pages (this should not have been possible earlier)
* Verifying WordPress 3.8 compatibility

= 2.2.0 =
* Adding Spanish translations (props to Andrew Kurtis)

= 2.1.1 =
* Updating the plugin so that the `category-sticky` class is applied *only* on category archive pages (props http://davidpratten.com).

= 2.1.0 =
* Updating the plugin to support pages custom post types
* Moving the screenshots to the `/assets/` directory to make the download a bit smaller

= 2.0.0 =
* Resolving a bug that marked the category as 'unstuck' when updating a post
* Introduced a feature for disabling the category sticky border
* Improving the coding standards of the plugin be separating the class into its own file
* Improving the PHPDoc of the plugin

= 1.2.1 =
* Removing the custom.css line in the README file

= 1.2 =
* Now posts that belong to multiple categories are properly styled when they are marked as sticky
* Removing some of the styles that were causing posts to look incorrect in certain themes
* Documenting all of the functions that exist in the source code
* Fully removing custom.css support

= 1.1.2 =
* Removing the custom.css support as it was causing issues with other plugin upgrades. Will be restored later, if requested.

= 1.1.1 =
* Improving support for adding custom.css so that the file is also managed properly during the plugin update process
* Updating localization files

= 1.1 =
* Updating function calls to use updated PHP conventions
* Adding a function to dynamically create a custom.css file if one doesn't exist
* Verifying compatibility with WordPress 3.5

= 1.0 =
* Initial release

== Development Information ==

Category Sticky Post was built with...

* The desire to perform the same functionality on my own blog
* [WordPress Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards)
* Native WordPress API's (specifically the [Plugin API](http://codex.wordpress.org/Plugin_API))
* [CodeKit](http://incident57.com/codekit/) using [LESS](http://lesscss.org/), [JSLint](http://www.jslint.com/lint.html), and [jQuery](http://jquery.com/)
* Some advice from [Konstantin Kovshenin](http://twitter.com/kovshenin) on query optimization
* Respect for WordPress bloggers everywhere :)
