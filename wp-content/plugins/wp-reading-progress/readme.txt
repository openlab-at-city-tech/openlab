=== WP Reading Progress ===
Contributors: ruigehond
Tags: reading, progress, progressbar, estimated reading time
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=hallo@ruigehond.nl&lc=US&item_name=WP+reading+progress+plugin&no_note=0&cn=&currency_code=EUR&bn=PP-DonationsBF:btn_donateCC_LG.gif:NonHosted
Requires at least: 4.9
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 1.6.0
License: GPLv3

Light weight fully customizable reading progress bar. Sticks to top, bottom or sticky menu, with fallback for small screens. Includes estimated reading time functionality (beta).

== Description ==
The reading progress bar is a great user experience on longreads. Especially if it accurately depicts the reading progress in the article text, and nothing else. This is standard on single blog posts and enabled by default.

Customization:

- Location top of screen, bottom of screen or below a sticky menu.

- Choose color of the reading progress bar.

- Have the bar start at 0% even when part of the article is visible.

- Select post types you wish the bar to appear, or individual posts.

Behaviour:

- The reading progress bar has smooth initializing since part of the text may already be visible, after that a lightweight update-function ensures quick response while scrolling.

- The bar can attach itself to any (sticky) element that you define as an admin, when there are multiple, the first visible element will be used.

- When there is no (longer a) visible element to attach to, the bar displays at the top.

- If there is no single article identified (by class names or id) it uses the whole page to calculate progress.

Estimated reading time (beta)

Since 1.6.0 this plugin has rudimentary estimated reading time functionality, for when your theme does not support it out of the box.
There are some potential issues, some of which cannot be fixed in a plugin. If it does not work for you, switch it off. It will have no effect on the plugin then.

This is my 6th WordPress plugin but my first one freely available to everybody. I hope you enjoy using it as much as I enjoy building it!

Regards,
Joeri

== Installation ==
1. Install the plugin by clicking ‘Install now’ below, or the ‘Download’ button, and put the WP-reading-progress folder in your plugins folder

2. By default, it only works on single blog posts and uses an orange colour

3. Go to settings->WP Reading Progress to customize it

Upon uninstall WP Reading Progress removes its own options and `post_meta` data (if any) leaving no traces.

== Screenshots ==
1. Example of the reading progress bar on my photography blog
2. WP Reading Progress settings page
3. Activate the bar for an individual post (if that post type is not enabled)

== Changelog ==

1.6.0: add estimated reading time as beta functionality, improve sticking and detecting article

1.5.7: fix save settings and calculation correction (again)

1.5.6: adapt calculation to safari and chrome alike

1.5.5: bar top position calculation improved and allowing fractional pixels

1.5.4: escape translate strings

1.5.3: update screenshots

1.5.2: simplify progress calculation accounting for incorrect bounding client rect reporting of body

1.5.1: remember (cache) the elements to attach to, for speed

1.5.0: allow multiple menu selectors and pick the first visible one to attach to

1.4.0: compatible with fixed menus that consist of different elements depending on screen size, as long as they have a common selector

1.3.8: refactor javascript slightly smaller, make scroll eventlistener passive

1.3.7: added aria-role and aria-value updating for screenreaders

1.3.6: moved css to head to avoid render blocking, added option ‘no css’ if you want to handle it yourself

1.3.5: improved get top position custom function to include edge cases, debounced resize event

1.3.4: removed jQuery dependency

1.3.3: fixed implode deprecated notice

1.3.2: fix getBoundingClientRect does not work on iOS 8 and 9 (at least), now using custom function for it

1.3.1: some optimizations regarding the on scroll function

1.3.0: now positions itself snugly to element using top-margin or fixed automatically to top when element is not in viewport or gone

1.2.5: improved fallback for mobile, added rtl support (html tag must contain dir="rtl")

1.2.4: added regular post type to settings, added fallback find post by id when not found by class names, added option to display on specific posts only

1.2.3: fixed bug initializing window height to 0 on page load in some cases

1.2.2: increased compatibility with themes regarding looking for single article

1.2.1: added option to start bar at 0%, slightly optimized progress function

1.2.0: improved behaviour upon resize of the window

1.1.0: now identifies single post reading area for all post-types, fallback to body when not found in DOM

1.0.3: fixed translation, corrected license indication

1.0.2: translated to Dutch

1.0.1: minified javascript and css, fixed issue of bar sometimes momentarily disappearing on mobile device while scrolling

1.0.0: release

== Upgrade Notice ==

= 1.5.1 =

For speed the plugin looks for elements to stick to only once per request, this should work. If not, please let me know and downgrade to 1.5.0 in the meantime.

= 1.4.0 =

The bar will select between multiple elements when available and pick the first one in the viewport. If you use it with a sticky element please check whether it still behaves as you expect, or stick it to a better element from now on.

If you upgrade from a version before 1.3.0, you have to check the `post` post type in the WP Reading Bar settings manually to keep the same behaviour.
