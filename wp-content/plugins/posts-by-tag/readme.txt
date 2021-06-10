=== Posts By Tag ===
Contributors: sudar  
Tags: posts, sidebar, widget, tag, cache  
Requires at least: 4.0  
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me  
Tested up to: 5.5
Stable tag: 3.2.1  

Provide sidebar widget, shortcode and template functions that can be used to display posts from a set of tags using various options in the sidebar or anywhere in a post.

== Description ==

Posts By Tag WordPress Plugin, provides sidebar widgets which can be used to display posts from a specific set of tags in the sidebar.

These tags can be specified in the widget or the Plugin can automatically retrieve them from the current post tags, post slug or form custom field. The custom fields can be specified in the edit post or page screen.

You can also use shortcode or template function to display the posts.

The Plugin caches the posts of each widget separately, and issues database queries only when needed. This will reduce the amount of database queries involved for each page load and will therefore be light on your server. If this clashes with other Plugins, you also have an option to disable it.

### Features

#### Sidebar Widget

Posts By Tag Plugin provides a sidebar widget which can be configured to display posts from a set of tags in the sidebar. You can have multiple widgets with different set of tags configured for each one of them.

Each widget allows you to choose

-   The set of tags from where posts should be selected (or excluded)
-   The number of posts to be displayed.
-   Whether to pick the tags from current post
-   Whether to pick the tags from current post slug
-   Whether to pick the tags from current post's custom field
-   Option to enable post excerpts to be displayed with post titles.
-   Option to display post thumbnail if present.
-   Option to display post author.
-   Option to display post date.
-   Option to display post content.
-   Choose the order in which the posts should be displayed.
-   Option to exclude current post/page.
-   Option to specify the target attribute for links
-   Option to display links to tag archive pages.
-   Option to disable the cache if needed.
-   Option to enable Google Analytics tracking on the links

To add the widget, log into your WordPress admin console and go to Appearances -> Widgets. You will find the widget with the title "Posts By Tag". Drag and drop it in the sidebar where you want the widget to be displayed.

#### Template function

In addition to using the widget, you can also use the following template function to display posts from a set of tags, anywhere in the theme.

`posts_by_tag($tags, $options);`

The following options can be passed in the $options array

- `$tags` (string) - set of comma separated tags. If you leave this empty, then the tags from the current post will be used.
- `$options` (array) - set of options. The following are the fields that are allowed
  - `number` (number) - default 5 - number of posts to display
  - `tag_from_post` (bool) - default FALSE - whether to pick up tags from current post's tag
  - `tag_from_post_slug` (bool) - default FALSE - whether to pick up tags from current post's slug
  - `tag_from_post_custom_field` (bool) - default FALSE - whether to pick up tags from current post's custom field
  - `exclude` (bool) - default FALSE - Where to include the tags or exclude the tags
  - `excerpt` (bool)  - default FALSE - To display post excerpts or not
  - `excerpt_filter` (bool) - default TRUE Whether to enable or disable excerpt filter
  - `thumbnail` (bool) - default FALSE  - To display post thumbnails or not
  - `thumbnail_size` (string/array) - default thumbnail  - Size of the thumbnail image. Refer to http://codex.wordpress.org/Function_Reference/get_the_post_thumbnail#Thumbnail_Sizes
  - `order_by` (date,title, random) - default date - Whether to order by date or by title or show them randomly
  - `order` (asc,desc) - default desc - To change the order in which the posts are displayed.
  - `author` (bool) - default FALSE - To display author name or not.
  - `date` (bool) - default FALSE - To display post date or not.
  - `content` (bool) - default FALSE - To display post content or not.
  - `content_filter` (bool) - default TRUE Whether to enable or disable content filter
  - `exclude_current_post` (bool) - default TRUE - To exclude current post/page.
  - `tag_links` (bool) - default FALSE - To display link to tag archive page or not.
  - `link_target` (string) - default empty - target attribute for the permalink links.

In addition to the above options the following options are available in the [Pro addon](http://sudarmuthu.com/wordpress/posts-by-tag/pro-addons)

- `campaign` (string) - The Google Analytics campaign code that needs to be appended to every link
- `event` (string) - The Google Analytics events code that needs to be appended to every link

You can checkout [some example PHP code](http://sudarmuthu.com/wordpress/posts-by-tag#example-template) that shows how you can call the template function with different options.

#### Shortcode

You can also include the following shortcode in your blog posts or WordPress page, to display the posts from the set of tags.

`posts-by-tag tags = "tag1, tag2"]`

All the parameters that are accepted by the template tag can also be used in the shortcode.

You can checkout [some example shortcodes](http://sudarmuthu.com/wordpress/posts-by-tag#example-shortcode) that shows how you can use the shortcode with different options.

#### Custom field

You can also specify the tags for each post or page and a custom title using custom field. The UI for the custom field is available on the right side of the add/edit post/page screen in WordPress admin console.

#### Styling using CSS

The Plugin adds the following CSS classes. If you want to customize the look of the widget then can change it by adding custom styles to these CSS classes and ids.

- The `UL` tag has the class `posts-by-tag-list`
- Every `LI` tag has the class `posts-by-tag-item`
- Every `LI` tag also has all tags names to which the post belongs as part of the class attribute
- Each `LI` tag also has the id `posts-by-tag-item-{id}`, where id is the post id.
- Each `<a>` tag inside `LI` that contains title has the class `posts-by-tag-item-title`.

If you want to output categories of the post as class names(so that you can style them differently), then you can get the code from this [forum thread](http://wordpress.org/support/topic/plugin-posts-by-tag-display-post-category-classes-in-outputted-code).

#### Caching

If you are using the widget, then the Plugin automatically caches the db queries. This will greatly improve the performance of you page. If this clashes with other Plugins or if you want to manage the cache yourself, then you disable the cache if needed.

However if you are going to use the shortcode or the template directly, then you might have to cache the output yourself.

### Development and Support

The development of the Plugin happens over at [github][13]. If you want to contribute to the Plugin, fork the [project at github][13] and send me a pull request.

If you are not familiar with either git or Github then refer to this [guide to see how fork and send pull request](http://sudarmuthu.com/blog/contributing-to-project-hosted-in-github).

If you are looking for ideas, then you can start with one of the following TODO items :)

### TODO

The following are the features that I am thinking of adding to the Plugin, when I get some free time. If you have any feature request or want to increase the priority of a particular feature, then let me know by adding them to [github issues][7].

- Provide template tags for widget title, that will be dynamically expanded.
- Add support for custom post types
- Ability to sort posts alphabetically
- Ability to [exclude posts by id](http://sudarmuthu.com/wordpress/posts-by-tag#comment-783250)
- Ability to [show comment count](http://sudarmuthu.com/wordpress/posts-by-tag#comment-783248)
- Ability to [retrieve posts by date range](http://sudarmuthu.com/wordpress/posts-by-tag#comment-780935)
- <del>Ability to pull posts randomly.</del> - Added in v3.0

### Support

- If you have found a bug/issue or have a feature request, then post them in [github issues][7]
- If you have a question about usage or need help to troubleshoot, then post in WordPress forums or leave a comment in [Plugins's home page][1]
- If you like the Plugin, then kindly leave a review/feedback at [WordPress repo page][8].
- If you find this Plugin useful or and wanted to say thank you, then there are ways to [make me happy](http://sudarmuthu.com/if-you-wanna-thank-me) :) and I would really appreciate if you can do one of those.
- Checkout other [WordPress Plugins][10] that I have written
- If anything else, then contact me in [twitter][3].

 [1]: http://sudarmuthu.com/wordpress/posts-by-tag
 [3]: http://twitter.com/sudarmuthu
 [7]: http://github.com/sudar/posts-by-tag/issues
 [8]: http://wordpress.org/extend/plugins/posts-by-tag/
 [9]: http://sudarmuthu.com/feed
 [10]: http://sudarmuthu.com/wordpress
 [13]: http://github.com/sudar/posts-by-tag

== Translation ==

*   Swedish (Thanks Gunnar Lindberg Årneby)
*   Turkish (Thanks Yakup Gövler)
*   Belorussian (Thanks FatCow)
*   German (Thanks Renate)
*   Dutch (Thanks Rene)
*   Hebrew (Thanks Sagive SEO)
*   Spanish (Thanks Brian Flores of InMotion Hosting)
*   Bulgarian (Thanks Nikolay Nikolov of [IQ Test)
*   Lithuanian (Thanks  Vincent G)
*   Hindi (Thanks Love Chandel)
*   Gujarati (Thanks Punnet of Resolutions Mart)

The pot file is available with the Plugin. If you are willing to do translation for the Plugin, use the pot file to create the .po files for your language and let me know. I will add it to the Plugin after giving credit to you.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page. You should see a new widget called "Tag Posts" in the widgets pages, which you can drag and drop in the sidebar of your theme.

== Screenshots ==


1. Widget settings page. This is how the sidebar widget settings page looks like


2. Custom fields meta box. This is how the custom fields meta box looks like in the add or edit post/page screen

== Readme Generator ==

This Readme file was generated using <a href = 'http://sudarmuthu.com/wordpress/wp-readme'>wp-readme</a>, which generates readme files for WordPress Plugins.
== Changelog ==

= v0.1 (2009-07-26)  =

*   Initial Version

= v0.2 (2009-08-02)  =

*   Added template functions
*   Added Swedish translation (Thanks Gunnar Lindberg Årneby)

= v0.3 (2009-08-14)  =

*   Improved caching performance
*   Added Turkish translation (Thanks Yakup Gövler)

= v0.4 (2009-09-16)  =

*   Added support for sorting the posts (Thanks to Michael http://mfields.org/)

= v0.5 (2010-01-03) =

*   Removed JavaScript from unwanted admin pages and added Belorussian translation.

= v0.6 (2010-03-18) =

*   Added option to hide author links.

= v0.7 (2010-04-16) =

*   Fixed an issue in showing the number of posts.

= v0.8 (2010-05-08) =

 *  Added support for shortcode and sorting by title.

= v0.9 (2010-06-18) =

 *  Fixed an issue with the order by option.

= v1.0 (2010-06-19) =

 *  Fixed issue with shortcode.

= v1.1 (2010-06-23) =

 *  Fixed issue with shortcode, which was not fixed properly in 1.0

= v1.2 (2010-06-25) =

 *  Fixed issue with shortcode, which was not fixed properly in 1.0 and 1.1

= v1.3 (2010-07-12) =

 *  Fixed some inconsistency in documentation and code

= v1.4 (2010-08-04) =

 *  Added German translations

= v1.5 (2010-08-26) =

 *  Added Dutch translations and fixed typos

= v1.6 (2011-02-17) =

 *  Fixed an issue in handling boolean in shortcode

= v1.7 (2011-05-11) =

 *  Added support for displaying post dates.
 *  Fixed a bug which was corrupting the loop.

= v1.8 (2011-09-07) =

 *  Added support for displaying content (Thanks rjune)

= v1.9 (2011-11-13) =

 * Added Spanish and Hebrew translations.

= v2.0 (2011-11-20) =

  * Added option to exclude tags.
  * Fixed bug in displaying author name
  * Added support for post thumbnails
  * Don't display widget title if posts are not found
  * Added Tag links
  * Added the option to take tags from the current post
  * Added the option to take tags from the custom fields of current page

= v2.1 (2011-11-22) =

 * Added option to include tag links from shortcode and template function.

= v2.1.1 (2011-12-31) =

 *  Fixed undefined notices for nouncename while creating new posts

= v2.2 (2012-01-31) =

 *  Fixed issues with order by option.
 *  Added Bulgarian translations

= v2.3 (2012-04-04) (Dev time - 3 hours) =
* Added filter to the get_the_content() call
* Moved caching logic to widget
* Added the option to exclude current post/page
* Added Lithuanian translations

= v2.4 (2012-04-15) (Dev time - 0.5 hours) =
* Added option to disable cache if needed

= v2.5 (2012-04-30) (Dev time - 0.5 hours) =
* Fixed the sorting by title issue

= v2.6 (2012-05-31) (Dev time: 2 hours) =
* Added support for specifying link targets
* Changed the argument list for the posts_by_tag template functions

= v2.7 (2012-06-23) (Dev time: 1 hour) =
* Added support for custom fields to all post types
* Added autocomplete for tag fields in custom field boxes
* Added Hindi translations

= v2.7.1 (2012-07-23) (Dev time: 0.5 hour) =
* Renamed all template functions with a prefix to avoid clash with other Plugins

= v2.7.2 (2012-12-30) (Dev time: 1 hour) =
* Fixed the bug which caused the comment to be posted to another post

= v2.7.3 (2013-01-23) - (Dev time: 1 hour) =
* Fixed the bug which caused PHP to timeout when content option is set to true

= v2.7.4 (2013-01-26) - (Dev time: 0.5 hour) =
* Exclude current post by default

= v2.8 (2013-05-25) - (Dev time: 20 hour) =

- Added underscore to meta key so it is protected and also code to migrate date from old key
- Added an option to disable content filter
- Added an option to disable excerpt filter
- Make thumbnail to link to post
- Added tag names as class in <li> to additional styling
- Added the ability to specify the size of thumbnail
- Added support for Pro addons
- Added Gujarati translations

= v2.9 (2013-05-27) - (Dev time: 0.5 hour) =
- Fixed a bug that caused the widget to fail when custom fields are enabled

= v3.0 (2013-05-28) - (Dev time: 0.5 hour) =
- Added the ability to sort the posts randomly

### v3.0.1 (2013-06-18) - (Dev time: 0.5 hour)
- Fix undefined variable warnings

### v3.0.2 (2013-07-04) - (Dev time: 0.5 hour)
- Added CSS class to the post title generated by widget

### v3.0.3 (2013-07-06) - (Dev time: 0.5 hour)
- Fixed the bug, that prevented shortcodes inside posts from getting expanded, when content is enabled in widget

### v3.0.4 (2013-12-19) - (Dev time: 0.5 hour)
- Fix: Remove undefined notices and warnings

### v3.1 (2014-02-19) - (Dev time: 10 hours)
- Add: Add the ability to specify tags from post slug
- Tweak: Move Widget class to a separate file
- Tweak: Move template functions to a separate file

### v3.1.1 (2014-02-26) - (Dev time: 0.5 hours)
- Fix: If tags is empty and no options are set, then try to get tags from post tags

### v3.1.2 (2014-03-06) - (Dev time: 0.5 hours)
- Fix: In some cases Widget was not able to retrieve tags and title from custom field

### v3.1.3 (2014-05-07) - (Dev time: 0.5 hours)
- Fix: Fixed an undefined variable warning
- Fix: Reset global post details

### v3.2 (2015-08-16) - (Dev time: 1 hours)
- Fix: Added compatibility with WordPress 4.3

### v3.2.1 (2019-04-19)
- Fix: Added compatibility with PHP 7.2
- Merged Google Analytics add-on to core plugin.

== Upgrade Notice ==

= 3.2 =
Added compatibility with WordPress 4.3

= 3.1.2 =
Fixed a bug that causes Widgets not to work in certain cases

= 3.1 =
Ability to pick up tags from post slug

= 3.0.2 =
Added CSS class to the post title generated by widget

= 3.0 =

Added the ability to order posts randomly

= 2.9 =

Fixed a bug that caused the widget to fail when custom fields are used
