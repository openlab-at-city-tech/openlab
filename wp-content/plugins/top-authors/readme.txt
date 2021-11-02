=== Top Authors ===
Contributors: danielpataki
Tags: authors, list, widget, gravatar, posts
Requires at least: 3.5.0
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


A highly customizable widget that allows you to display the top authors of your website easily.

== Description ==

Top authors allows yoy to list your top authors with plenty of options. You can set the following in each widget:

* Widget title
* Roles to exclude
* Post types to include
* Authors to show
* 4 Preset display templates
* Custom display template that allows you to create a completely custom structure and modify the output before and after the list.
* Archive (category/tag/taxonomy) specific author lists

For a more detailed description of how you can set up custom author lists take a look at the other notes section. The plugin also has some developer friendly features, take a look at the other notes section for more.

= Thanks =

* [Seb Van Dijk](https://twitter.com/sebvandijk) for donating this plugin to me for free, I owe you one :)
* [Font Awesome](http://fortawesome.github.io/Font-Awesome/) for the plugin icon


== Installation ==

= Automatic Installation =

Installing this plugin automatically is the easiest option. You can install the plugin automatically by going to the plugins section in WordPress and clicking Add New. Type Top Authors" in the search bar and install the plugin by clicking the Install Now button.

= Manual Installation =

To manually install the plugin you'll need to download the plugin to your computer and upload it to your server via FTP or another method. The plugin needs to be extracted in the `wp-content/plugins` folder. Once done you should be able to activate it as usual.

If you are having trouble, take a look at the [Managing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation) section in the WordPress Codex, it has more information on this topic.

== Other Notes ==

= Usage =

Most of the options for the plugin are self explanatory, I thought I'd highlight the archive specific authors and the display template.

If Archive specific authors is checked the plugin will handle authors differently on category, tag and taxonomy archive pages. It will narrow the posts down to only those in the given archive. Practically this means the following:

Say John wrote 10 posts on the website and Jill wrote 5. On normal pages John would be listed first, Jull would be listed second. However, John wrote 3 posts in the "Food" category while Jill wrote 4. On the archive page for the "Food" category - if Archive specific authors is checked - Jill will be shown first with 4 posts and John second with 3.

When you select "Custom Structure" as the preset display you should see three new fields: Display Template, Before List and After List. These fields can be used to control the HTML display of your authors. To make sure you can use the data retireved by the widget I've added placeholders which will be replaced by real data:

* %posts_url%: The URL to the user's post archive page
* %website_url%: The URL to the user's website
* %gravatar_SIZE%: The gravatar of the user at the given size. For example, to display a 50px Gravatar your would use %gravatar_50%
* %firstname%: The user's first name
* %lastname%: The user's last name
* %displayname%: The user's display name
* %username%: The user's username
* %post_count%: Number of posts
* %meta_FIELD%: Displays the given meta field. If you store a user's Twitter name in the 'twitter' meta field you could use %meta_twitter% to display it.

As of 1.0.9 there is also a custom ID field. This is for advanced use, mainly for developers. It allows for custom CSS stylings and even custom queries on a widget-to-widget basis.

= For Developers =

Currently there are three filters you can use to control the options available in the widget.

* `ta/usable_roles` allows you to change the roles that can be selected. It should return an array of roles in the form of slug=>name
* `ta/usable_opst_types` allows you to change the post_types that can be selected. It should return an array of post type objects
* `ta/post_query` allows you to modify the arguments of the WP_Query which retrieves the posts that we look up the authors for. Modify the arguments if you want to force category-specific top authors on single post pages, or other similar uses

== Screenshots ==

1. Gravatar Only Preset
2. Gravatar And Name Preset
3. List With Post Count Preset
4. Gravatar List With Post Count Preset
5. Custom Setup
6. Widget Settings


== Changelog ==

= 1.0.11 (2015-06-10) =
* Prevented warning from appearing if there were no posts in the main query

= 1.0.10 (2015-05-12) =
* Added widget_title filter to the Widget title

= 1.0.9 (2015-05-12) =
* Added the custom ID field
* Widget hidden if no authors


= 1.0.8 (2015-05-06) =
* Added the ta/post_query filter for more control over the authors shown

= 1.0.7 (2015-05-06) =
* Corrected a typo in a variable name

= 1.0.6 (2015-05-06) =
* Corrected an issue with the Authors to show parameter

= 1.0.5 (2015-05-05) =
* Corrected an typo in 1.0.4 which mixed up author ordering

= 1.0.4 (2015-05-05) =
* A user count issue coupled with role restrictions has been fixed

= 1.0.3 (2015-05-05) =
* Made sure post types work properly

= 1.0.2 (2015-05-05) =
* Added textdomain properly

= 1.0.1 (2015-04-29) =
* Implemented role exclusion properly

= 1.0.0 (2015-04-28) =

* Category/Tag/Taxonomy archives can have separate author lists based on the category shown
* Post types can now be specified
* Gravatar sizes are now specified within the placeholder
* User meta fields can be pulled with a placeholder
* Completely recoded
* Standardized Widget UI
* Plugin can now be translated
* Added some developer friendly hooks

= 0.5.7 =
* Tested up to WP 3.5.1
* Added CPT type support and option in widget (check settings to turn on or off) - user request (realtega). Please feedback via: http://wordpress.org/support/topic/custom-post-type-support-1)
* Pages will never be counted


= 0.5.6 =
* Tested with WP 3.5
* Added author id function on request of Gornahoor %author_id% will be the author ID
* Added author id link (non permalink) %link_author_id% will be www.linktoblog.com?author=1

= 0.5.5 =
* WP 3.3.1 update.
* Replaced deprecated function (now useing get_users)
* Fixed all notices when wp-debug is on.

= 0.5.4 =
* Fixed error when wordpress is installed outside of wp directory // thanks Crhis Nolan

= 0.5.3 =
* Tested WP3.1, no big changes.

= 0.5.2 =
* Nickname support
* Display name support
* Custom slug support (when using a plugin to rewrite author slug)
* Custom author link after slug (choose between username | nickname | display name)

= 0.5.1 =
* New feature to exclude authors with 0 posts (Thanks paul for request)

= 0.5 =
* New feature requested by vectorism (thank you): Exclude administrator users from the list.
	Exclude function get information from wp_capabilities or blog_capabilities. If it's not working on your blog, please contact me.

= 0.4.2 =
* bugfix sorting thanx Yusuf Savci for reporting!

= 0.4.1 =
* Replaced deprecated fuction (http://codex.wordpress.org/Function_Reference/get_usernumposts) with count_many_users_posts
* Did some underwater code improvements.
* Added feedback link in widget, to get u guys involved :)

= 0.4 =
* Small bugfix in html template.
* added gravatar support
* added custom before and after the list tags

= 0.3.1 =
* readme.txt updated

= 0.3 =
* Cleaner and more effective PHP code
* Added templating / self html support
* Replaced space in author name by dash so the link is more WP friendly

= 0.2 =
* Check if input is nummeric and between 1 and 99

= 0.1 =
* Initial release


== Upgrade Notice ==
= 1.0.0 =
* This is a complete recode of the plugin, if you run into any issues please let me know!

= 0.5.2 =
* New features like display and nickname support

= 0.5 =
* This update will add the option to exclude administrator users

= 0.4.1 =
* Important update: widget was using deprecated function that maybe will be removed by Wordpress.

= 0.4 =
* This update contains new features as: Gravatar support and control over the begin and end tag.
