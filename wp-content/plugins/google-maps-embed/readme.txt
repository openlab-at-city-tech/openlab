=== Google Maps Embed ===
Contributors: DeannaS, kgraeme
Tags: google maps, WPMU
Requires at least: 2.7
Tested up to: 3.0 
Stable tag: trunk



Tiny MCE button for embedding a google map via google maps' link. No API key needed. Also includes shortcode ability.

== Description ==

If getting an API key is beyond the skill set or desires of your users, but they still want to be able to have a Google map, this plugin is the answer. It adds a button to the visual editor that allows them to use the link from Google Maps to embed a plugin. Blog administrators can set defaults for width, height, marginwidth, marginheight, frameborder, and scrolling properties for the embed code. Users can override these defaults when they are creating a post or page. Power users can directly enter a shortcode in a post, page or widget. Shortcode should be entered in the following format:

[cetsEmbedGmap src=http://maps.google.com/?ie=UTF8&ll=37.0625,-95.677068&spn=53.609468,107.138672&z=4 width=350 height=425 marginwidth=0 marginheight=0 frameborder=0 scrolling=no]

This version adds a quicktag to the html editor, opens the larger map in a new window, and defaults to map view instead of sat view.



== Installation ==

1. Place the cets\_EmbedGmaps folder in the wp-content/plugins folder.
1. Activate via the plugin activation screens.
1. If upgrading from 1.3.1 to 1.4, you can delete the cets\_embedGmaps\_config.php file and the tinymce\window.php file.



== Frequently Asked Questions ==
1. Can it default to street view?

No, unfortunately I have not been able to get it to default to streetview.


== Screenshots ==
1. User View of pop-up window for entering Google Maps link via visual editor.
2. User View of pop-up window for entering Google Maps link via html editor.
3. Site visitor view of embedded map with default attributes.

== Changelog ==
1.5 - allowed for regional version of maps.google to be used. Tested on 3.0

1.4.1 - fixed pathing issue

1.4 - updated to make both the visual and html editor use the same popup, eliminating the need for wp-load.

1.3.1 - Updated project structure to work with Wordpress.org auto install/update.



