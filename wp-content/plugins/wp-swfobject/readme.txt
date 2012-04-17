=== Plugin Name ===
Contributors: unijimpe
Donate link: http://blog.unijimpe.net/
Tags: flash, swf, flv, swfobject, video, youtube, media, player, post
Requires at least: 1.5
Tested up to: 3.2.1
Stable tag: 2.4

Insert Flash Movies into WordPress.

== Description ==

This plugin enable insert flash movies into WordPress using **SWFObject** with simple quicktag <code>[swf][/swf]</code> . 

**Features**

*	Easy install and easy use on content and widgets
*	Insert Flash movie with simple shortcode
*	Panel for easy configuration
*	Allow config flash player version required
*	Allow config message for iPhone Browser
*	Support FlashVars param
*	Support FullScreen param
*	Generate `<object>` code for RSS and iPhone compatibility	
*	Select version of SWFObject (1.5 or 2.0)
*	Allow insert SWFObject from Google AJAX Libraries API
*	Detect iPhone Browser to show message o link for Youtube Videos
*	Easy integration with Youtube videos
*	Support for show Loading image

To insert swf into post content or text widget use:

`[swf]movie.swf, width, heigth[/swf]`

To insert swf with flashvars use:

`[swf]movie.swf, width, heigth, var1=val1&var2=val2[/swf]`

To insert swf on template, use the php code:

`<?php wp_swfobject_echo("movie.swf", "width", "heigth"); ?>`

To insert swf with flashvars on template, use the php code:

`<?php wp_swfobject_echo("movie.swf", "width", "heigth", "var1=val1&var2=val2"); ?>`

For more information visit [plugin website](http://blog.unijimpe.net/wp-swfobject/ "plugin website")



== Installation ==

This section describes how to install the plugin and get it working.

1. Upload folder `wp-swfobject` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure plugin into 'Settings' -> 'WP-SWFObject' menu


== Screenshots ==

1. Install and Activate plugin is easy.
2. Config panel for WP-SWFObject.


== Changelog ==

= 2.4 =
* Add property allowScriptAccess in config panel
* Updated library to SWFObjectc 2.2 
* Add Support shortcode in text widgets
* Update docs

= 2.3 =
* Fixed embed method
* Allow use SWFObject from Google Ajax Library

= 2.2 =
* Fixed wmode param
* New format XHTML to embed code
* New param to allow fullscreen on Youtube videos
* Detect iPhone and show text warning
* Show object code to youtube videos on iPhone

= 1.0 =
* First version

