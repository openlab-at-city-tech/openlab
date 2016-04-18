=== Post Gallery Widget ===
Contributors: wpmuguru, cuny-academic-commons
Tags: custom, post, gallery, widget
Requires at least: 3.0
Tested up to: 4.3
Stable tag: 0.3.1.1

A rotating gallery widget using a custom post type for gallery content.

== Description ==

This plugin adds a Gallery Posts menu to your WordPress dashboard. To add content to the rotating gallery:

1. Add a new Gallery Post for each image/headline to be shown in the rotating gallery
1. Attach one or more images to the gallery post by uploading the image(s) with the media uploader while editing the post
1. Enter the title & content that you would like to overlay the image
1. Publish the post(s)
1. Add the Rotating Post Gallery widget to widget area on your home page
1. Enter the number of Posts to rotate in the gallery (default = All).
1. Choose the size of image to display in the gallery (based on your Media Settings)

When multiple images are attached to a Gallery Post one of the images is randomly selected to be shown on each page view.

In the initial verion, you will have to add some CSS to your theme's stylesheet:

`.slideshow {`
`	width: 123px;`
`	height: 456px;`
`}`

Substitute 123 & 456 with the width & height in your Media Settings.

This plugin was written by [Ron Rennick](http://ronandandrea.com/) in collaboration with the [CUNY Academic Commons](http://dev.commons.gc.cuny.edu/).

[Plugin Page](http://wpmututorials.com/plugins/post-gallery-widget/)

== Installation ==

1. Upload the entire `post-gallery-widget` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 0.3 =
* add post order option to widget
* auto reorder menu_order and push duplicate menu_order posts down in order

= 0.2.1 =
* Removed the inadvertent inclusion of rewrite rules.

= 0.2 =
* Add a metabox in the edit post area that shows thumbnails of attached images.

= 0.1 =
* Original version.

