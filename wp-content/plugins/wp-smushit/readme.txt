=== WP Smush.it ===
Plugin Name: WP Smush.it
Version: 1.6.5.3
Author: WPMU DEV
Author URI: http://premium.wpmudev.org
Contributors: WPMUDEV, alexdunae
Tags: images, image, attachments, attachment, Smush.it
Requires at least: 3.5
Tested up to: 3.6.1
Stable tag: 1.6.5.4
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Reduce image file sizes and improve performance using the <a href="http://smush.it/">Smush.it</a> API within WordPress.

== Description ==

Yahoo's excellent <a href="http://developer.yahoo.com/performance/">Exceptional Performance series</a> recommends <a href="http://developer.yahoo.com/performance/rules.html#opt_images">optimizing images</a> in several lossless ways:

* stripping meta data from JPEGs
* optimizing JPEG compression
* converting certain GIFs to indexed PNGs
* stripping the un-used colours from indexed images

<a href="http://smush.it/">Smush.it</a> offers an API that performs these optimizations (except for stripping JPEG meta data) automatically, and this plugin seamlessly integrates Smush.it with WordPress.

= Dear Smushers: WPMU DEV has taken over maintenance and support for WP Smush.it =
With the backing of <a href="http://premium.wpmudev.org/">WPMU DEV's professional WordPress team</a> you can expect faster support, bug-fixes, and new features!

= How does it work? =
Every image you add to a page or post will be automatically run through Smush.it behind the scenes.  You don&rsquo;t have to do anything different.

= Existing images =
You can also run your existing images through Smush.it via the WordPress `Media Library`.  Click on the `Smush.it now!` link for any image you'd like to smush.

As of version 1.4.0 there is a new, experimental `Bulk Smush.it` feature.  You can find the link under the `Media Library` tab.

= Errors =

Sometimes the Smush.it service goes down or is under heavy load. If the plugin has difficulty connecting to Smush.it then automatically smushing is temporarily disabled (currently for 6 hours). You can always re-enable it via the `Media > Settings` screen or manually smush the image from the Media Library.

You can also define how long you want to wait for the Smush.it server to respond.

= Privacy =
Be sure you&rsquo;re comfortable with Smush.it&rsquo;s privacy policy (found on their <a href="http://info.yahoo.com/legal/us/yahoo/smush_it/smush_it-4378.html">FAQ</a>).

= About Us =
WPMU DEV is a premium supplier of quality WordPress plugins and themes. For premium support with any WordPress related issues you can join us here:
<a href="http://premium.wpmudev.org/join/">http://premium.wpmudev.org/join/</a>

Don't forget to stay up to date on everything WordPress from the Internet's number one resource:
<a href="http://wpmu.org/">http://wpmu.org</a>


== Screenshots ==

1. See the savings from Smush.it in the Media Library.

== Installation ==

1. Upload the `wp-smushit` plugin to your `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Automatic smushing of uploaded images can be controlled on the `Settings > Media` screen
1. Done!

== Upgrade Notice ==

In this first official release from WPMU DEV, we've done a code cleanup and reformat to get started, as well as  
attempting to handle smush.it API errors a bit better. We've also made all the text fully i18n translatable.

This will give us a good foundation to start adding some new features!


== Changelog ==

= 1.6.5.4 =
* Added settings option to disable check for home url.
* for PHP 5.4.x reordered class WpSmushit contructors to prevent Strict Standards Exception

= 1.6.5.3 =
* Removed check for file within local site home path. 

= 1.6.5.2 =
* Corrected issues with Windows paths compare.
* Added debug output option to help with user support issues. 

= 1.6.5.1 =
* Correct Settings > Media issue causing settings to report warnings and not save. 
* Corrected some processing logic to better handling or image path. Images still need to be within ABSPATH of site
* Correct image URL passed to Smush.it API to convert https:// urls to http:// since the API does not allow https:// images


= 1.6.5 =
* Codes reformatted and cleaned up into a php class
* More texts are translatable now

= 1.6.4 =
* Fixed a bug that prevents execution

= 1.6.3 =
* check image size before uploading (1 MB limit)
* attempt to smush more than one image before bailing (kind thanks to <a href="http://wordpress.org/support/profile/xrampage16">xrampage16</a>)
* allow setting timeout value under `Media > Settings` (default is 60 seconds)

= 1.6.2 =
* about to get a new lease on life notice

= 1.6.1 =
* no longer maintained notice

= 1.6.0 =
* added setting to disable automatic smushing on upload (default is true)
* on HTTP error, smushing will be temporarily disabled for 6 hours

= 1.5.0 =
* added basic integration for the <a href="http://wordpress.org/extend/plugins/wp-smushit-nextgen-gallery-integration/">NextGEN gallery plugin</a>
* add support for media bulk action dropdown
* compatibility with WordPress earlier than 3.1
* added a <a href="http://dunae.ca/donate.html">donate link</a>

= 1.4.3 =
* cleaner handling of file paths

= 1.4.2 =
* bulk smush.it will no longer re-smush images that were successful

= 1.4.1 =
* bug fixes

= 1.4.0 =
* bulk smush.it

= 1.3.4 =
* bug fixes

= 1.3.3 =
* add debugging output on failure

= 1.3.2 =
* removed realpath() call
* IPv6 compat

= 1.3.1 =
* handle images stored on other domains -- props to [ka-ri-ne](http://wordpress.org/support/profile/ka-ri-ne) for the fix
* avoid time-out errors when working with larger files -- props to [Milan Dinić](http://wordpress.org/support/profile/dimadin) for the fix

= 1.2.10 =
* removed testing link

= 1.2.9 =
* updated Smush.it endpoint URL

= 1.2.8 =
* fixed path checking on Windows servers

= 1.2.7 =
* update to workaround WordPress's new JSON compat layer (see [trac ticket](http://core.trac.wordpress.org/ticket/11827))

= 1.2.6 =
* updated Smush.it endpoint URL
* fixed undefined constant

= 1.2.5 =
* updated Smush.it endpoint URL

= 1.2.4 =
* removed debugging code that was interfering with the Flash uploader

= 1.2.3 =
* bug fix

= 1.2.2 =
* updated to use Yahoo! hosted Smush.it service
* added security checks to files passed to `wp_smushit()`

= 1.2.1 =
* added support for PHP 4
* created admin action hook as workaround to WordPress 2.9's `$_registered_pages` security (see [changeset 11596](http://core.trac.wordpress.org/changeset/11596))
* add savings amount in bytes to Media Library (thx [Yoast](http://www.yoast.com/))

= 1.2 =
* added support for `WP_Http`

= 1.1.3 =
* fixed activation error when the PEAR JSON library is already loaded

= 1.1.2 =
* added test for `allow_url_fopen`

= 1.1.1 =
* added error message on PHP copy error

= 1.1 =
* improved handling of errors from Smush.it
* added ability to manually smush images from media library
* fixed inconsistent path handling from WP 2.5 -> WP 2.7

= 1.0.2 =
* added 'Not processed' status message when browsing media library

= 1.0.1 =
* added i10n functions

= 1.0 =
* first edition

== About Us ==
WPMU DEV is a premium supplier of quality WordPress plugins and themes. For premium support with any WordPress related issues you can join us here:
<a href="http://premium.wpmudev.org/join/">http://premium.wpmudev.org/join/</a>

Don't forget to stay up to date on everything WordPress from the Internet's number one resource:
<a href="http://wpmu.org/">http://wpmu.org</a>

Hey, one more thing... we hope you <a href="http://profiles.wordpress.org/WPMUDEV/">enjoy our free offerings</a> as much as we've loved making them for you!

== Contact and Credits ==

Originally written by Alex Dunae at Dialect ([dialect.ca](http://dialect.ca/?wp_smush_it), e-mail 'alex' at 'dialect dot ca'), 2008-11.

WP Smush.it includes a copy of the [PEAR JSON library](http://pear.php.net/pepr/pepr-proposal-show.php?id=198) written by Michal Migurski.

Smush.it was created by [Nicole Sullivan](http://www.stubbornella.org/content/) and [Stoyan Stefanov](http://phpied.com/).
