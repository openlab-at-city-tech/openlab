=== Resize Image After Upload ===
Contributors: ShortPixel
Donate link: https://www.paypal.me/resizeImage
Tags: image, resize, rescale, bulk resize, bulk rescale, downsize, 
Requires at least: 3.5
Tested up to: 4.9
Stable tag: 1.8.3

Automatically resize your images after upload using this plugin. Specify height&width, the plugin will do the rest quickly and transparently.

== Description ==

**A free, fast, easy to use, stable and frequently updated plugin to resize your images after upload. Supported by the friendly team that created <a href="https://wordpress.org/plugins/shortpixel-image-optimiser/" target="_blank">ShortPixel</a>  :)**

This plugin automatically resizes images (JPEG, GIF, and PNG) when they are uploaded to within a given maximum width and/or height to reduce server space usage, speed up your website, save you time and boost your site's SEO. 
Imagine that nowadays images can be over 4-5MB and using this plugin you can reduce them to 100-200KB with no extra effort on your side!

In addition, the plugin can force re-compression of uploaded JPEG images and convert PNGs to JPEG (if they don't have a transparency layer), regardless of whether they are resized or not.

Is that simple, just give it a try, it is safe and free! :-)

== Installation ==

1. Upload the plugin 'resize-image-after-upload' to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Edit the max-width/max-height settings under 'Settings > Resize Image Upload'.
4. Once active, just upload images as normal and it will just work!

== Frequently Asked Questions ==

= Is this plugin compressing the images? =
	Yes, this plugin compresses the original images, you can select the JPEG quality for example. For a professional image optimization solution though we recommend you <a rel="friend" href="https://wordpress.org/plugins/shortpixel-image-optimiser/" target="_blank">this</a> image optimization plugin.

= Is this plugin resizing also older images? =
	This plugin will *not* resize images that have already been uploaded. For this you can use <a rel="friend" href="https://wordpress.org/plugins/shortpixel-image-optimiser/" target="_blank">ShortPixel</a>, it can not only resize your images but it can compressthem as well!

= Is the original image and/or its EXIF data kept? =
	The resizing/recompression process will discard the original uploaded file including EXIF data.

== Screenshots ==

1. Full preview of the settings screen.

== Changelog ==

= 1.8.3 =

Release date: 20th December 2017

* [Fix] Skip animated GIFs that GD can't resize.

= 1.8.2 =

Release date: 21th August 2017

* [Fix] Better sanitize post data when saving settings.

= 1.8.1 =

Release date: 18th June 2017

* [Fix] Notice not dismissing

= 1.8.0 =

Release date: 18th June 2017

* [Update] Add convert PNG to JPG option

= 1.7.2 =

Release date: 18th September 2015

* [Fixed] Undefined option notices when running WordPress in debug mode.

= 1.7.1 =

Release date: 26th February 2015

* [Update] After reports of the plugin resizing PDF files, added check to ensure the plugin only attempts a resize on JPEG, GIF, and PNG file types other than relying on the response from WP_Image_Editor.

= 1.7 =
This is a major under-the-hood release to change core workings of the plugin. The plugin still functions as normal, but the way in which it resizes images has now changed to use standard WordPress libraries. This means that should your server have better image processing libraries than the GD default (e.g. ImageMagick), then the resizing method should make use of them. This should improve the output of your resized images!

* [Update] Plugin completely re-engineered to use WP_Image_Editor when resizing images.

= 1.6.2 =
Minor maintenance release:

* [Fix] Correcting an error in the documentation.

= 1.6.1 =
Fix a few edge case bugs, I go and break the main functionality - that's life! This is a maintenance release to fix a bug.

* [Fix] Correct the logic behind which direction to perform resizing in.

= 1.6 =
This is a major maintenance release to squash a few long outstanding bugs.

* [Update] Tidied up the plugin settings page.
* [Fix] Significant number of bug fixes through extensive testing.
* [Fix] Resizing wasn't running on square images.
* [FIX] Media upload was failing for BMP images.
* [Fix] Replaced use of deprecated PHP functions with correct ones.

= 1.5 =
* [Added] Ability to force re-compression even if resize is not required.
* [Fix] Compression quality value was not adhered to.

= 1.4.2 =
* [Update] Added ability to enter a resize value of zero (0) to prevent resizing in a particular dimension.

= 1.4.1 =
* [Fix] Reverting code back to how it was in v1.3.0 after previous premature deployment of v1.4.0. Please use this version.

= 1.4 =
* [Error] Code was deployed prematurely and should not have made its way to the live repository. Please do NOT use this version.

= 1.3 =
* [Update] Added ability to set the JPEG compression quality level when JPEGs are resized. Default is 90.

= 1.2 =
* [Update] Now only runs images through the resizer if required, i.e. the image is actually larger than the max height/width.

= 1.1.1 =
* [Fix] Corrected functionality that sets default plugin options if the plugin version number changes.
* [Fix] Adds default option for max-height value.
* [Update] Updated the screenshot to include new maximum height field.
* [Update] Increased the default maximum height and width to 1200px each.

= 1.1 =
* [Update] Added ability to set maximum height (thanks @Wonderm00n).

= 1.0.1 =
* [Update] Update to read me, no feature updates unfortunately.

= 1.0 =
* [Added] Initial release.
