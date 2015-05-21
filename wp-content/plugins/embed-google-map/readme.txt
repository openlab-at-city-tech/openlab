=== Embed Google Map ===
Contributors: petkivim
Tags: Google, address, coordinates, location, map, map size, map type, zoom level
Requires at least: 3.0.1
Tested up to: 4.0.1
Stable tag: 3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed Google Map is a plugin for embedding one or more Google Maps to WordPress posts and pages.

== Description ==

Embed Google Map is a plugin for embedding one or more Google Maps to WordPress posts, pages, text widgets and templates. Adding maps is very simple, just add the address or the coordinates which location you want to show on a map inside google_map tags to a post or a page or a text widget or a template, and that's it!

The plugin supports Google Maps, Google Maps Classic and Google Maps Embed API. The version to be used can be set by using the Version setting (supported values: new, classic, embed). Google Maps and Google Maps Classic do not require an API key, but for Google Maps Embed API an API key is required instead. 

It's possible to define the version of Google Maps, the type of the map (normal, satellite, hybrid, terrain), the size of the map, the language of the Google Maps interface, custom labels, zoom level, border layout and link to the full size map. Both HTTP and HTTPS protocols are supported. The default settings defined in the admin panel are used for all the maps in the site, and they can be overridden for individual maps. 

= Features =

* It's possible to embed one or more Google Maps within a post, page, text widget or template.
* Support for [google_map] shortcode.
* The address or the coordinates which location is shown on a map is given as a parameter.
* Define Google Maps version to be used.
* Define the language of the Google Maps interface.
* Define the type of the map (normal, satellite, hybrid, terrain).
* Define the size of the map.
* Define the zoom level.
* Define custom labels.
* Show/hide the info label.
* Define the border width, border style and border color.
* Add link to the full size map.
* Define the link label.
* Support for HTTP and HTTPS.

= Basic Usage =
To embed a map in a post, page or text widget use the following code:

{google_map}address{/google_map}

*{google_map}latitude,longitude{/google_map}

**{google_map}url{/google_map}

*latitude,longitude = coordinates in decimal degrees

** URL of a map stored under My Places on Google Maps

= Overriding Default Settings =

To override one or more default settings use the following code:

{google_map}address|zoom:10{/google_map}

{google_map}address|version:classic|zoom:10{/google_map}

{google_map}address|zoom:10|lang:it{/google_map}

{google_map}address|lang:system{/google_map}

{google_map}address|width:200|height:200|border:1|border_style:solid|border_color:#000000{/google_map}

{google_map}address|width:200|height:200|link:yes|link_label:Label{/google_map}

{google_map}address|link:yes{/google_map}

{google_map}address|type:satellite{/google_map}

{google_map}address|show_info:yes|info_label:Label{/google_map}

{google_map}address|link_full:yes{/google_map}

{google_map}address|https:yes{/google_map}

*{google_map}latitude,longitude{/google_map}

**{google_map}url|width:200|height:200|border:1{/google_map}
		
*latitude,longitude = coordinates in decimal degrees

** URL of a map stored under My Places on Google Maps

= Shortcode =

To embed a map in a template use [google_map] shortcode. For example:

echo do_shortcode('[google_map]'.$address.'[/google_map]');

All the settings supported by Embed Google Maps plugin can be set as shortcode attributes. For example:

echo do_shortcode('[google_map version="classic" lang="en" link="yes" width="200" height="200"]'.$address.'[/google_map]');

In addition, [google_map] shorcode can be used inside pages, post and text widgets too.

== Installation ==

Adding the plugin using the built-in plugin installer:

1. Download embed_google_map.zip file on your computer.
2. Go to Plugins > Add new > Upload
3. Click Browse, select embed_google_map.zip file and click Install Now.
4. Click Activate Plugin
5. The installation is complete. Go to Settings > Embed Google Map and define the default settings.
6. Add Google Maps to your site!

Or:

1. Download embed_google_map.zip file on your computer.
2. Extract the zip package in a folder.
2. Upload the folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. The installation is complete. Go to Settings > Embed Google Map and define the default settings.
5. Add Google Maps to your site!


