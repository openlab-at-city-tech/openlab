=== CAC Featured Content ===
Contributors: michael@castironcoding.com
Tags: buddypress, feature, featured, multisite, highlight
Requires at least: 3.0.1
Tested up to: 3.0.2
Stable tag: 0.8.4

The CAC Featured Content plugin provides a widget that allows you to select among five different content "types" to feature in a widget area. 

== Description ==
The CAC Featured Content plugin was developed for the CUNY Academic Commons ( http://commons.gc.cuny.edu ) — an academic social network powered by WordPress, BuddyPress, and MediaWiki. It is assumed that the plugin will be installed alongside BuddyPress, although it could easily be modified to run without BuddyPress content.

The widget provides the user with several useful tools for using a widget to feature content, such as Featured Blog Posts, Featured Members, Featured Resources, and Featured Blogs from a WordPress installation. The widget can be customized to work with many different themes; for example, the widget editor has the ability to specify a length (in characters) that will be used to crop text content.

Whenever possible, the widget attempts to find an image to use along with a set of featured content. For groups, the image is the group’s avatar; for members the individual’s avatar is used; for the blog the author’s avatar is used; and for posts the image used is either the first image within the post or the avatar of the post author. The resource type allows the user to specify an image from an external source or to use the media browser, built in to the widget (much of the code used to implement this feature comes from Image Widget, by Shane & Peterm, Inc.). The incorporation of additional methods for including images are planned for future releases.

Image cropping is possible by way of the excellent TimThumb: http://code.google.com/p/timthumb/

Text cropping comes by way of the TYPO3 project: http://typo3.org/

Author: michael@castironcoding.com, cuny-academic-commons

== Installation ==

1. Download and unzip cac-featured-content.zip to your plugin folder.
2. Activate the plugin from the Plugins section of your dashboard.
3. Place a "CAC Featured Content" widget in a widget area and edit some content. 
4. Edit the "views" in cac-featured-content.php to wrap your content in the appropriate markup. Using the markup shipped with the widget may or may not look broken within your template!

== Additional Notes ==

* You'll need to edit the views for each content type in cac-featured-content.php. The view methods are in the form renderType\_CONTENT_TYPE_IN_ALL_CAPS(). The included view methods start at about line 815.



== Frequently Asked Questions ==

There is not currently a FAQ available for this plugin. Please check back later. 

== Changelog ==

= 0.8.4 =
* Fixes code that pulls first image from WP posts when blog posts are featured
* Fixes auto-height and width for BP avatars
* Removes invalid WP_ADMIN constant reference
* Fixes PHP notices

= 0.8.3 =
* Security update: updates TimThumb script to latest version
