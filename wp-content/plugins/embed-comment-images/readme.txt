=== Embed Images in Comments ===
Contributors: H3llas, soulseekah
Tags: embed, images, comments, convert, links
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=hverona%40gmail%2ecom&lc=US&item_name=Donation%20for%20Embed%20Comment%20Images&button_subtype=services&no_note=0&cn=Add%20special%20instructions%20to%20the%20seller%3a&no_shipping=2¤cy_code=USD&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHosted
Requires at least: 3.7.1
Tested up to: 4.8.1
Stable tag: 0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed direct image links in your comments with an img tag.

== Description ==
This plugins embeds image links in comments with the img tag so the image are visible in your comment timeline. 

Image formats supported:
1. .jpg
2. .gif
3. .png

You can specify your comment width so the images are fitted nicely. Images are not hosted on your server neither this plugin pickups any data. 

Do note that people can link extremely large images and your page loading can be compromised because of that.

Demo:
[Embed Comment Images](http://www.ascic.net/embed-comment-images/ "Embed Images in Comments")

== Installation ==
1. Upload "eiic.php" to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress backend.


== Frequently Asked Questions ==
= Does this plugin import images to my server? =
No. It just wrap any URL in comments which points to image into IMG tag.

= Can I set size of images? =
Yes. But setting size only create a container for image. Image still can be any resolution.

== Screenshots ==
1. The screenshot description corresponds to screenshot-1.(png|jpg|jpeg|gif).
2. The screenshot description corresponds to screenshot-2.(png|jpg|jpeg|gif).
3. The screenshot description corresponds to screenshot-3.(png|jpg|jpeg|gif).

== Changelog ==
= 0.6 =
* <a href="https://profiles.wordpress.org/soulseekah">Gennady Kovshenin</a> found and fixed XSS vulnerability.

= 0.5 =
* Wrapped inserted image with a link tag which points to original image, so the click on image opens large original version of the image.

= 0.4 =
* Bug fix. When link of the image has extension in capitalized letters e.g. .JPG image is not wrapped with the img tag.

= 0.3 =
* Added support for images residing on https urls.

= 0.2 =
* Added image resize support.

= 0.1 =
* Initial release.

== Upgrade Notice ==
= 0.6 =
* XSS vulnerability fix.

= 0.3 =
* Added support for images residing on https urls.

= 0.2 =
* Added image resize option.

= 0.1 =
* Initial release.