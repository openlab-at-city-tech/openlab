=== Simple Drop Cap ===
Contributors: maurisrx
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=D2ZLXZ8VQKPE2
Tags: post, page, shortcode, edit, text, widget
Requires at least: 3.0
Tested up to: 4.6.1
Stable tag: 1.2.8
License: GPLv2

Transform the first letter of a post into a drop cap or initial letter automatically or simply by wrapping the first word with shortcode [dropcap].

== Description ==

[Buy now to get the lowest price!](http://www.wphouse.net/products/simple-drop-cap-pro/)

This plugin helps you transform the first letter of a word into a drop cap or initial letter automatically or simply by wrapping the first word with shortcode [dropcap]. If you want to know more about drop cap, please read [this article](http://en.wikipedia.org/wiki/Initial) from Wikipedia.

= Pro version is available now! =

Here are some features in pro version:

* Enable drop cap transformation on per-post basis, per-post type basis, or all posts.
* Check a box to enable or disable drop cap on a certain post. No need to use shortcode.
* Shortcode is still available in pro version to create drop cap in text widget, post, etc.
* Unlimited styles of drop cap. You can have different styles for every drop cap.
* Intuitive design panel lets you to style your drop cap without knowing CSS or any single code.
* Dedicated support forum, better support priority than the free version.

[Click here](http://www.wphouse.net/products/simple-drop-cap-pro/) for pro version screenshots!

= How to use this plugin: =

1. Install and activate the plugin.
2. Customize the settings on Simple Drop Cap settings page.
3. For version 1.1 and later, you can transform the first letter of all posts, pages, and custom post types automatically by checking a checkbox on the settings page.

* If you want to do it manually: 
1. You can manually wrap a word with the shortcode like this: [dropcap]word[/dropcap].

= Plugin Translation =

As of version 1.2.0, this plugin supports translation. If you want to translate the plugin, contact me at mauris [at] yudhistiramauris [dot] com. Here are some available languages:

* Serbian by [Ogi Djuraskovic](http://firstsiteguide.com/)

== Installation ==

= How to install this plugin: =

1. Upload 'simple-drop-cap' to the '/wp-content/plugins/' directory.
2. Activate the plugin through the plugin dashboard.

== Frequently Asked Questions ==

= Can I automatically transform the first letter of all posts into a drop cap? =

Yes, you can. Enable drop cap automation option on Simple Drop Cap settings page.

= How do I change the color of the drop cap? =

For version 1.1 and later, you can change the drop cap color using color picker on Simple Drop Cap settings page.

= How do I change the style of the drop cap? =

For version 1.1 and later, you can change the style of the drop cap directly on Simple Drop Cap settings page using custom CSS.

== Screenshots ==

1. Float Mode
2. Normal Mode
3. Drop Cap on a Widget

== Changelog ==

= 1.0 =
* First official release

= 1.0.1 =
* Add prefix to variables

= 1.0.3 =
* Fix widget support
* Cleaner code

= 1.0.4 =
* Fix dropcap in post excerpt
* Use custom wp_trim_excerpt() function

= 1.0.5 =
* Enable dropcap button on all post type

= 1.0.6 =
* Add multi byte character support

= 1.1.0 =
* Add custom color feature
* Add custom CSS feature
* Add drop cap automation feature for post, page, custom post type

= 1.1.1 =
* Fix issues with other plugins that insert HTML tags into the_content

= 1.1.2 =
* Fix minor bug

= 1.1.3 =
* Fix minor bug

= 1.1.4 =
* Fix minor bug

= 1.1.5 =
* Bug fix: Use preg_match() to match letter/number that will be dropcapped

= 1.1.6 =
* Fix: improved regex rule to match html closing tag too.

= 1.2.0 =
* New feature: support plugin translation.
* New feature: added Serbian (sr_RS) translation.
* Fix: improved regex rule

= 1.2.1 =
* Fix: fixed text formatting on excerpt

= 1.2.2 =
* Fix: better multi byte character support. Credit: @florinoprea.eu
* Fix: add conditional check to decide drop capped character. Credit: @florinoprea.eu

= 1.2.3 =
* Fix: regex match for cyrillic characters

= 1.2.4 =
* Add: enable shortcode when automatic feature is enabled

= 1.2.5 =
* Fix: improved automatic drop cap regex.

= 1.2.6 =
* Fix: remove `wpautop` filter.

= 1.2.7 =
* Fix: improve regex rule

= 1.2.8 =
* Fix and improve: tinyMCE drop cap button

== Upgrade Notice ==