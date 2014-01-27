=== Resize Image After Upload ===
Contributors: jepsonrae
Donate link: http://www.jepsonrae.com/?utm_campaign=plugins&utm_source=wp-resize-image-after-upload&utm_medium=donate-link
Tags: image, plugin, resize, upload
Requires at least: 2.6
Tested up to: 3.5.2
Stable tag: 1.4.2

This plugin resizes uploaded images to within a given maximum width and height after uploading, discarding the original uploaded file in the process.

== Description ==

This plugin resizes uploaded images to within a given maximum width and height after uploading, discarding the original uploaded file in the process. The original image is destroyed to save space, and unfortunately EXIF dataa is lost due to this process.

The requirement for this plugin is due to the fact that digital cameras and mobile phones now take pictures of over 4000x3000 pixels in dimension, and can range in size from a few MB, to 20MB. Having these original images stored on the server can quickly consume up valuable disk space. This plugin will reduce the size of the uploaded image at point of upload; then either WordPress or some other resize script such as TimThumb can then further reduce the image size to suit positioning in the website theme.

This plugin uses standard PHP resizing functions so resizing is not on par with what you could produce in Photoshop. However for the large majority of cases, this is not noticeable in the slightest.

The plugin uses a class originally from Jacob Wyke (www.redvodkajelly.com) and is a direct update of another plugin called Resize at Upload which is no longer maintained.

== Installation ==

1. Upload the plugin 'resize-image-after-upload' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Activate the resize function and set maximum width and height through the 'Settings > Resize Image Upload' menu in WordPress
4. Upload images while writing posts and pages.

== Screenshots ==

1. Full preview of the settings screen

== Changelog ==

= 1.4.2 =
* [update] Added ability to enter a resize value of zero (0) to prevent resizing in a particular dimension.

= 1.4.1 =
* [fix] Reverting code back to how it was in v1.3.0 after previous premature deployment of v1.4.0. Please use this version.

= 1.4.0 =
* [error] Code was deployed prematurely and should not have made its way to the live repository. Please do NOT use this version.

= 1.3.0 =
* [update] Added ability to set the JPEG compression quality level when JPEGs are resized. Default is 90.

= 1.2.0 =
* [Update] Now only runs images through the resizer if required, i.e. the image is actually larger than the max height/width.

= 1.1.1 =
* [Fix] Corrected functionality that sets default plugin options if the plugin version number changes.
* [Fix] Adds default option for max-height value.
* [Update] Updated the screenshot to include new maximum height field.
* [Update] Increased the default maximum height and width to 1200px each. 

= 1.1.0 =
* [Update] Added ability to set maximum height (thanks @Wonderm00n).

= 1.0.1 =
* [Update] Update to read me, no feature updates unfortunately.

= 1.0.0 =
* [Added] Initial release.