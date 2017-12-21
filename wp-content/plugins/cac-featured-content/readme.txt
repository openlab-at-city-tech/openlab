=== Plugin Name ===
Contributors: humanshell, boonebgorges, cuny-academic-commons
Author URI: http://humanshell.net
Plugin URI: https://github.com/cuny-academic-commons/cac-featured-content
Tags: buddypress, multisite, feature, featured, highlight
Requires at least: 3.3
Tested up to: 4.9
Stable tag: 1.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The CAC Featured Content plugin provides a widget that allows you to select from five different content "types" to feature in a widget area.

== Description ==

The CAC Featured Content Widget is a plugin developed for the [CUNY Academic Commons](http://commons.gc.cuny.edu) — an academic social network powered by WordPress, BuddyPress, and MediaWiki. The widget provides several useful tools for featuring selected content, such as Featured Blogs, Featured Groups, Featured Posts, Featured Members and Featured Resources. Currently the plugin will work on both single and multisite installs, but is BuddyPress **dependent**. It will **not** work on a non-BuddyPress install.

The five featured content types (blog, group, post, member and resource) share a lot of the code behind the scenes. Their layouts (views) and structure are all very similar. It's only the specific details about each that change. The admin interface for the plugin has been augmented with autocomplete functionality to help simplify administrative tasks. The featured blog address, featured group name, featured post name and featured member username will all provide results from your BuddyPress/MultiSite installation as you type.

If the description provided by the chosen featured content type is not suitable, for whatever reason, the plugin offers a **Custom Description** text field to provide an alternative. The text entered in this field will override any description that was automatically parsed by the plugin while querying for your featured content type. This field also serves as the only description for the **Featured Resource** content type.

The crop length (in characters) of the description can be controlled via the plugin's **Crop Length** input field. Either the automatically parsed description, or the custom description text, will be cropped with ellipses appended to the end. The default is 250 characters.

The widget that's displayed to the user on the front of the site provides a link after the description to allow the visitor to view the remainder of the featured content in its full glory. The link text defaults to "Read More...", but can be customized through the **Read More Label** field.

Because many site admins do not have access to how a theme styles the HTML output of the widgets added to a sidebar, the admin section allows you to choose what heading element will be used to wrap the widget's title. You can choose from an &lt;h1&gt; all the way down to an &lt;h6&gt;. This allows you to add the widget to any number of sidebars (or widgetized areas) in a theme that has defined different looks between different page sections.

You have almost complete control over the plugin's image handling capabilities. The **Display Images** checkbox toggles the displaying of all images. When images are displayed they will be chosen based on the type of featured content, unless you enter a URL to a specific image in the **Image URL** field. For groups, the image is the group’s avatar; for members their personal avatar is used; for a blog the author’s avatar is used; and for posts the image used is either the first image within the post or the avatar of the post's author. The resource type will use the URL from the **Image URL** field to load an image from an external source. The size of the thumbnail displayed in the widget can be controlled through the **Image Width** and **Image Height** fields, which are both set to 50px by default.

Additional technical details can be found in the plugin's README on [Github](https://github.com/cuny-academic-commons/cac-featured-content)

== Installation ==

1. Download and unzip cac-featured-content.zip to your plugin folder.
2. Activate the plugin from the Plugins section of your dashboard.
3. Place a "CAC Featured Content" widget in a widget area and edit some content.
4. Enjoy all the positive feedback your site now receives

== Upgrade Notice ==

= 1.0.9 =
* Fix regression in 1.0.8.

= 1.0.8 =
* Improved compatibility with PHP 7.2.

= 1.0.7 =
* Security fixes.
* Better support for subdirectory multisite installations.

= 1.0.6 =
* Content-type specific templates can now be overridden from a theme directory called 'cac-featured-content'.

= 1.0.3 =
Each instance of the Featured Content Widget will need to be re-saved after upgrading due to the addition of a new database option.

= 1.0.2 =
Each instance of the Featured Content Widget will need to be re-saved after upgrading due to the addition of a new database option.

= 1.0.1 =
Each instance of the Featured Content Widget will need to be re-saved after upgrading due to the addition of a new database option.

= 1.0.0 =
All current widgets will need to be repopulated with featured content due to structural changes to the core of the plugin that break backwards compatibility with versions <= 0.8.4.

== Other Notes ==

= Notice =
This release is a complete rewrite of most of the core functionality of the plugin. Many old features were removed and new functionality has been added.

= Upgrading =
Version 1.0.0 breaks backwards compatibility as of version 0.8.4, the prior stable release. Some filters that existed in the previous version no longer exist. Additionally, any current widgets will need to be repopulated with featured content due to structural changes to the core of the plugin. Please be aware of these issues before deciding to upgrade to the newly redesigned version 1.0.0.

= Support =
Please direct all support requests, feedback and patches (pull requests) to the [issues section](https://github.com/cuny-academic-commons/cac-featured-content/issues) of the plugin's Github repo.

== Frequently Asked Questions ==

There is not currently a FAQ available for this plugin. I'm workin' on it though...

== Screenshots ==

1. Admin Interface
2. Front End

== Changelog ==

= 1.0.5 =
* Fixed bug that prevented Featured Member from being found when a username was provided

= 1.0.4 =
* multisite optimizations for Featured "Post" Content type
* fixed $wpdb->prepare warning

= 1.0.3 =
* fixed js error that was improperly referencing widget number on new widgets

= 1.0.2 =
* fixed small readme error
* updated javascript to support new features
* improved multi-widget support
* fixed an autocomplete bug

= 1.0.1 =
* improved code to work better with 3rd party plugins (Domain Mapping)
* added the ability to choose the widget title's HTML element
* adjusted layout of some admin input elements
* added some more default styles
* updated HTML output in content type views

= 1.0.0 =
* separated plugin responsibilities into MVC-like structure
* rewrote readme files
* added code to handle multiste and non-multisite installs
* simplified html structure in all view files
* added autocomplete functionality
* rewrote helper class to work with new admin widget layout
* rewrote admin js file to work with new admin widget layout
* started new clean cac-featured-content plugin structure
