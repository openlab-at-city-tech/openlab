=== Awesome Flickr Gallery ===
Contributors: ronakg
Donate link: http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/
Tags: awesome, gallery, flickr, photo, image, slideshow, colorbox, portfolio, group, photoset, yahoo, slider, thumbnail, images
Requires at least: 3.0
Tested up to: 4.5.2
Stable tag: 3.5.5
License: GPLv2 or later

Create a photo gallery of your Flickr photos on your WordPress enabled Website enabling you to customize it the way you like it.

== Description ==

**Awesome Flickr Gallery** is a simple, fast and light plugin to create a
gallery of your Flickr photos on your WordPress enabled website.  This plugin
aims at providing a simple yet customizable way to create clean and professional looking Flickr
galleries.

**Features:**

* Fast and light - uses intelligent caching to load galleries instantly
* Support for both Public and Private photos
* Create multiple galleries with different parameters
* Select Photos from your Flickr Photostream, a Photoset, a Gallery, a Group or a set of tags
* Multiple sorting options available so that you don't have to rely on Flickr's sorting options
* Customizable image sizes with cropping settings
* Infinitely customizable with custom CSS field
* Fits into a sidebar widget too
* Insert multiple galleries on same page with independent slideshow and pagination
* Fits automatically according to the width of the theme or you can select the width of the gallery yourself
* Ability to disable built-in slideshow so that you can use a slideshow plugin of your choice
* Intuitive menu pages with easy configuration options and photo previews
* SEO friendly, all your photos are available to search engine crawlers

You can see a *live demo* of this plugin on my personal photography page -
[Photos | RonakG.com](http://www.ronakg.com/photos/)

Check out my home page to see a demo of how the Gallery fits into a sidebar widget - [RonakG.com | Live life to the fullest...](http://www.ronakg.com/)

**More Examples:**

* [Demo Page](http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/demo-page/)

**Support:**

Can't get the plugin working?  Head to the discussion forum for solution -
[Discussions | RonakG.com](http://www.ronakg.com/discussions/)

**Video Tutorial on How to Configure Awesome Flickr Gallery**

[youtube http://www.youtube.com/watch?v=ZGKcptkTSIs]

== Installation ==

1. Extract the contents of the zip archive to the `/wp-content/plugins/` directory or install the plugin from your WordPress dashboard -> plugins -> add new menu
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure plugin using Awesome Flickr Gallery settings page
1. Place [AFG_gallery] in your posts and/or pages to show the default gallery or create new galleries with different settings and insert the generated code

== Frequently Asked Questions ==

= After upgrade to version 3.0.0, my photo descriptions appear as "array". =

Just delete the cached gallery data from "Default Settings" page of the plugin and also delete any cached pages from your caching plugins (like mentioned below).

= After upgrade, only one column appears in the gallery. =

This happens when you have a cache plugin (like WP Super Cache or W3 All Cache) installed. Old cached CSS file is loaded instead of the new one. Just delete the cached pages from your cache plugin and refresh the gallery page 2-3 times, it will appear fine.

= I have activated the plugin, but gallery doesn't load. =

Make sure your Flickr API key and Flickr User ID are correct.

= My Flickr API key and User ID are correct but the gallery doesn't load =

Make sure you add the shortcode [AFG_gallery] to your post or page where you
want to load the gallery.  This code is case-sensitive.

= When I click the photo, it doesn't open full size photo. =

Awesome Flickr Gallery uses *Colorbox* to display full size photos.  Most likey
you have another plugin enabled, which also uses the colorbox and is overriding
the Awesome Flickr Gallery settings.  It is recommended to deactivate any other
plugins that uses colorbox.

Also, some themes have built-in settings to display images using lightbox or
colorbox etc.  If your theme has such an option, turn it off.

= I have created separate galleries with different photosets as Gallery Source, but all the galleries are using Photostream as source. =

This typically happens when you are using a plugin for editing your posts/pages. Try to remove the quotes from id parameter of the shortcode and it should work fine. For example, if the shortcode for your gallery is

[AFG_gallery id='1']

, use

[AFG_gallery id=1]

instead.

Also, some themes have built-in settings to display images using lightbox or colorbox etc. If your theme has such an option, turn it off.

= I made changes to my Flickr account but they don't reflect on my website. =

Awesome Flickr Gallery uses caching to avoid expensive calls to Flickr servers.  It intelligently figures out if cache needs to be updated or not.  However, sometimes it may not work as expected.  You should go to Default Settings and delete all cached data.

= I created a gallery with source as a Group.  In this gallery, only 500 photos are appearing. =

As Flickr Groups have thousands of photos, it becomes very expensive to fetch all the photos from Flickr.  Hence, Groups galleries are limited to latest 500 photos.

Still can't get the plugin working?  Head to the discussion forum for solution -
[Discussions | RonakG.com](http://www.ronakg.com/discussions/)

== Upgrade Notice ==

= 3.5.3 =
[MAJOR CHANGE] I had to remove the Highslide option from list of slideshows. Apparently it is not compatible with WordPress's set of rules for licensing.
[Enhancement] Highslide is replaced with Swipebox. A much better slideshow plugin which also supports touch swipes.
[Enhancement] Add option "Cache Refresh Interval" to improve performance.

= 3.3.5 =
[Bug Fix] Add support for Flickr API changes related to SSL.
[Bug Fix] When adding gallery, photosets and groups are now sorted alphabetically.
[Bug Fix] Sometimes only one column shows up even when configured value is higher.
[Bug Fix] Other minor bug fixes.

= 3.3.4 =
[Bug Fix] Get rid of annoying warning messages when DEBUG is enabled.

= 3.3.3 =
[FEATURE] This version introduces a capability to update the plugin from a different host other than WordPress. This will allow me to bring back HighSlide slideshow (and few other options), which I can't if the plugin is hosted on WordPress.org. Update to this version so that you can get all the exciting features in future.

= 3.3.2 =
[ENHANCEMENT] Intelligently resize images based on orientation of the image (landscape vs portrait).
[BUG FIX] Do not include Colorbox script if it is not enabled.
[BUG FIX] Square option for Custom Photo Size gets reset

= 3.3.1 =
[MAJOR CHANGE] Highslide had to be removed from Slideshow options as WordPress moderators objected about the same due to licensing issues. This update removes the Highslide option. If you are using Highslide, you will be migrated to use Colorbox instead. 

= 3.3.0 =
[FEATURE] Ability to set the slideshow per gallery.
[FEATURE] Two new options for slideshow behavior - Link to Flickr photo page and non clickable thumbnails
[BUG FIX] Custom CSS styling doesn't appear in highslide thumbnail slider

= 3.2.14 =
[FEATURE] A new sort order option "Random" added
[BUG FIX] Tool-tip text of the thumbnails contain HTML code
[BUG FIX] Photo descriptions with hyperlinks do not render properly
[BUG FIX] Photo titles with apostrophe appear truncated

= 3.2.10 =
[Bug Fix] Gallery is messed up with some themes where photo description contains either a hyperlink or new line characters

= 3.2.9 =
[Enhancement] Description of the photos visible during slideshow with Highslide option
[Enhancement] View on Flickr link added to slideshow
[Bug Fix] PHP Warning comes when there are no photosets, groups or galleries associated with the account
[Bug Fix] Photo title gets truncated when an apostrophe is present

= 3.2.8 =
[Update] Update image resizing script to the latest version with many bug fixes

= 3.2.7 =
[BUG FIX] When a theme has a built-in colorbox, plugin shows two colorbox instances during slideshow.
[BUG FIX] When pagination is hidden using custom CSS, extra line breaks appear at the bottom of the gallery.

= 3.2.4 =
[COMPATIBILITY] Compatibilty with WordPress 3.3
[ENHANCEMENT] Enhanced 3d look (still subtle) for the gallery

= 3.2.3 =

[FEATURE] Show off your Popular Flickr photos by selecting My Popular Photos as the source for the gallery.

= 3.2.1 =
[MAJOR FEATURE] Now galleries can be created using tags.
[Enhancement] For galleries created using a Flickr Gallery, URL of the owner's Flickr profile page appears as part of the title of the photo
[Bug Fix] Slideshow doesn't show all the photos when accessed from any page but first

= 3.1.7 =
[FEATURE] Multiple sorting options now available for galleries. You can now use these options to override your sorting options set on Flickr.

= 3.1.5 =
[ENHANCEMENT] Editors can now access Add/Delete/Saved Galleries page.
[ENHANCEMENT] Colorbox and HighSlide updated to latest versions.
[ENHANCEMENT] Each gallery gets a unique class, so that it can be customized at high level.

= 3.1.2 =
[ENHANCEMENT] Performance improvement for Colorbox gallery. Page load time should improve.

= 3.1.1 =
[BUG FIX] Gallery with width less than 100% appears unaligned

= 3.1.0 =
[Feature] A custom CSS field which can be used to override plugin's default CSS so that you can infinitely customize your galleries
- [Bug Fix] On Safari browser, Add Gallery page goes into an alert loop

= 3.0.8 =
[BUG FIX] Including private photos doesn't work, gives Invalid Auth Token error

= 3.0.7 =
[BUG FIX] Weird formatting after upgrading to 3.0.5
[BUG FIX] Custom sized thumbnails don't appear

= 3.0.5 =
[FEATURE] Select custom sizes for thumbnails in gallery. 
[ENHANCEMENT] Flexibility to modify gallery CSS to get desired look
[ENHANCEMENT] Improved error reporting for better debuggability

= 3.0.1 =
[Bug Fix] Awesome Flickr Gallery pagination doesn't work properly when qtranslate plugin is activated
[Bug Fix] For galleries having more than 500 photos, it starts again with same set of photos

= 3.0.0 =
[FEATURE] Now you can include your PRIVATE PHOTOS also in galleries -
[ENHANCEMENT] Group galleries now show owner of the photo and a link to owner's photostream -
[STABILITY] Moved to a standard API to talk to Flickr which is more reliable and stable -
[Bug Fix] Highslide slideshow doesn't work on Advanced Settings page -
[PERFORMANCE] Performance enhancement for galleries with Square size photos

= 2.9.4 =
[ENHANCEMENT] Subtle mouse-over effect for gallery images
[PERFORMANCE] Large galleries load faster
[Bug Fix] Sidebar appears at bottom of the page with AFG activated

= 2.9.2 =
****** MUST UPGRADE ******. A NEW slideshow option now available which has a built in thumbnail slider for even easier navigation.

= 2.7.11 =
[ENHANCEMENT] Ability to create Gallery using any of your plublic Groups on Flickr.
[ENHANCEMENT] Ability to disable built-in slideshow so that you can use slideshow from any other plugin or theme of your choice
[ENHANCEMENT] Max photo per page limit extended to 999
[ENHANCEMENT] Max columns limit extended to 12
[Bug Fix] Improved stability

= 2.7.7 =
[ENHANCEMENT] Improved caching mechanism extended to descriptions too.  Now switching ON descriptions is NOT expensive.
[Bug Fix] This photo is not available on Flickr error fixed.  Intelligently identify low resolution photos and show a smaller version for slideshow.

= 2.7.5 =
Added caching mechanism to avoid expensive calls to Flickr servers.  This should improve page loading times a lot, especially for those with 500+ photos.

= 2.7.2 =
[Bug Fix] Can't see photos when there are more than 500 photos
[Enhancement] View full size photos in slideshow
[Enhancement] Minimum number of photos in gallery reduced to 4

= 2.7.0 =
Now slideshow displays all the pages in the gallery so that users can navigate through without having to visit all pages.  Support for multiple galleries with independent slideshows and pagination.
MUST UPGRADE for all who are facing conflict issue with other plugin or themes.

= 2.6.5 =
- [Bug Fix] Awesome Flickr Gallery conflicts with other slider plugins
- Minor improvements in UI

== Screenshots ==

1. Awesome Flickr Gallery with Thumbnail size photos with white background
2. Awesome Flickr Gallery with photos of size Square with Title and Description OFF
3. Awesome Flickr Gallery with photos of size Small with Title and Description ON
4. Default Settings Page
5. Add Gallery Page
6. Edit Galleries Page
7. Saved Galleries Page
8. Advanced Settings Page

== User Testimonials ==

* Awesome Flickr Gallery plugin is legend by the way - thanks for such a fantastic tool - LeonsLens
* This remains the best way to set up flickr galleries! – forpetessake93
* The Tag feature works great! I have been waiting for a plugin like this. I love your plugin. - ryangirtler
* The name of AFG says it all. I really like the way you’ve taken the time to make a UI for creating & editing galleries. – chassy
* this plugin is AMAZING!! Thank you – Yardena
* The gallery looks exceptionally clean and professional. – sherrieJD
* Awesome plugin! Awesome support! – zumine
* Super. It is a pleasure to work with you. Very professional and highly reactive. – fibonaccifactory
* Awesome. You rock. – VisionsInEd
* Thanks Ronak. It works beautifully. Awesome app you have built here. – miracleboy31
* You rock so much. Such a quick response time. Your plugin is SO SO SO much better than other flickr plugins. – AutoEntropy
* this gallery plugin is excellent I really like it and working with Flickr and WordPress makes my workflow much easier. – AtlantisWeb
* I have to say that I am loving what I am seeing. I set up a gallery and it looks great.  – svogt
* MANY thanks for your awesome Awesome Flickr Gallery! Works like a charm. -  Jessin
* i prefer awesome flickr gallery (i think is smoother and faster than slickr flickr) – apocalipsis1234
* your Awesome Flickr Gallery is one of the best plugins out there!! – RichardF

== Changelog ==

= 3.5.3 =
* [MAJOR CHANGE] I had to remove the Highslide option from list of slideshows. Apparently it is not compatible with WordPress's set of rules for licensing.
* [Enhancement] Highslide is replaced with Swipebox. A much better slideshow plugin which also supports touch swipes.
* [Enhancement] Add option "Cache Refresh Interval" to improve performance.

= 3.3.5 =
* [Bug Fix] Add support for Flickr API changes related to SSL.
* [Bug Fix] When adding gallery, photosets and groups are now sorted alphabetically.
* [Bug Fix] Sometimes only one column shows up even when configured value is higher.
* [Bug Fix] Other minor bug fixes.

= 3.3.4 =
[Bug Fix] Get rid of annoying warning messages when DEBUG is enabled.

= 3.3.2 =
* [ENHANCEMENT] Intelligently resize images based on orientation of the image (landscape vs portrait).
* [BUG FIX] Do not include Colorbox script if it is not enabled.
* [BUG FIX] Square option for Custom Photo Size gets reset

= 3.3.1 =
* [MAJOR CHANGE] Highslide had to be removed from Slideshow options as WordPress moderators objected about the same due to licensing issues. This update removes the Highslide option. If you are using Highslide, you will be migrated to use Colorbox instead. 

= 3.3.0 =
* [Feature] Ability to set the slideshow per gallery.
* [Feature] Two new options for slideshow behavior - Link to Flickr photo page and non clickable thumbnails
* [Bug Fix] Custom CSS styling doesn't appear in highslide thumbnail slider

= 3.2.14 =
* [Feature] A new sort order option "Random" added
* [Bug Fix] Tool-tip text of the thumbnails contain HTML code
* [Bug Fix] Photo descriptions with hyperlinks do not render properly
* [Bug Fix] Photo titles with apostrophe appear truncated

= 3.2.10 =
* [Bug Fix] Gallery is messed up with some themes where photo description contains either a hyperlink or new line characters

= 3.2.9 =
* [Enhancement] Description of the photos visible during slideshow with Highslide option
* [Enhancement] View on Flickr link added to slideshow
* [Bug Fix] PHP Warning comes when there are no photosets, groups or galleries associated with the account
* [Bug Fix] Photo title gets truncated when an apostrophe is present

= 3.2.8 =
* [Update] Update image resizing script to the latest version with many bug fixes

= 3.2.7 =
* [Bug Fix] When a theme has a built-in colorbox, plugin shows two colorbox instances during slideshow.
* [Bug Fix] When pagination is hidden using custom CSS, extra line breaks appear at the bottom of the gallery.

= 3.2.4 =
* [Compatibility] Compatibilty with WordPress 3.3
* [Enhancement] Enhanced 3d look (still subtle) for the gallery

= 3.2.3 =

* [Feature] Show off your Popular Flickr photos by selecting My Popular Photos as the source for the gallery.

= 3.2.1 =
* [Major Feature] Now galleries can be created using tags.
* [Enhancement] For galleries created using a Flickr Gallery, URL of the owner's Flickr profile page appears as part of the title of the photo
* [Bug Fix] Slideshow doesn't show all the photos when accessed from any page but first

= 3.1.7 =
* [Feature] Multiple sorting options now available for galleries. You can now use these options to override your sorting options set on Flickr.

= 3.1.5 =
* [Enhancement] Editors can now access Add/Delete/Saved Galleries page.
* [Enhancement] Colorbox and HighSlide updated to latest versions.
* [Enhancement] Each gallery gets a unique class, so that it can be customized at high level.

= 3.1.2 =
* [ENHANCEMENT] Performance improvement for Colorbox gallery. Page load time should improve.

= 3.1.1 =
* [Bug Fix] Gallery with width less than 100% appears unaligned

= 3.1.0 =
* [Feature] A custom CSS field which can be used to override plugin's default CSS so that you can infinitely customize your galleries
* [Bug Fix] On Safari browser, Add Gallery page goes into an alert loop

= 3.0.8 =
* [Bug Fix] Including private photos doesn't work, gives Invalid Auth Token error

= 3.0.7 =
* [Bug Fix] Website components messed up after upgrading to 3.0.5
* [Bug Fix] Custom size thumbnails don't appear

= 3.0.5 =
* [Feature] Select custom sizes for thumbnails in gallery. 
* [Enhancement] Flexibility to modify gallery CSS to get desired look
* [Enhancement] Improved error reporting for better debuggability

= 3.0.1 =
* [Bug Fix] Awesome Flickr Gallery pagination doesn't work properly when qtranslate plugin is activated
* [Bug Fix] For galleries having more than 500 photos, it starts again with same set of photos

= 3.0.0 =
* [Feature] Now you can include your PRIVATE PHOTOS also in galleries -
* [Enhancement] Group galleries now show owner of the photo and a link to owner's photostream -
* [Stability] Moved to a standard API to talk to Flickr which is more reliable and stable -
* [Bug Fix] Highslide slideshow doesn't work on Advanced Settings page -
* [Performance] Performance enhancement for galleries with Square size photos

= 2.9.4 =
* [Enhancement] Subtle mouse-over effect for gallery images
* [Performance] Large galleries load faster
* [Bug Fix] Sidebar appears at bottom of the page with AFG activated

= 2.9.2 =
* [Feature] A NEW slideshow option now available which has a built in thumbnail slider for even easier navigation.
* Minor bug fixes

= 2.7.11 =
* [Enhancement] Ability to create Gallery using any of your plublic Groups on Flickr.
* [Enhancement] Ability to disable built-in slideshow so that you can use slideshow from any other plugin or theme of your choice
* [Enhancement] Max photo per page limit extended to 999
* [Enhancement] Max columns limit extended to 12
* [Bug Fix] Improved stability

= 2.7.7 =
* [ENHANCEMENT] Improved caching mechanism extended to descriptions too.  Now switching ON descriptions is NOT expensive.
* [Bug Fix] This photo is not available on Flickr error fixed.  Intelligently identify low resolution photos and show a smaller version for slideshow.

= 2.7.5 =
* Added caching mechanism to avoid expensive calls to Flickr servers.  This should improve page loading times a lot, especially for those with 500+ photos.

= 2.7.0 =
* Now slideshow displays all the pages in the gallery so that users can navigate through without having to visit all pages.
* Support for multiple galleries with independent slideshows and pagination.
* MUST UPGRADE for all who are facing conflict issue with other plugin or themes. Everyone should be happy now.

= 2.6.1 =
* Added a configuration option to control width of the gallery for better flexibility.
* Added a configuration option to enale/disable pagination.  This helps if you want to insert the gallery into a sidebar widget.
* Change slideshow behavior based on feedback.  Now it doesn't start automatically.

= 2.5.0 =
* Create multiple instances of your gallery with different settings
* Select photo source from your Flickr photostream, photoset or galleries
* New improved admin panel with slick menus and photo previews
* Bug fixes

= 1.3.0 =
* Added SlideShow option when viewing full size photos
* Moved to colorbox from lightbox for displaying full size photos

= 1.1.0 =
* Added page navigation to the gallery.  Now max photos setting refers to max photos per page.
* Better exception handling
* Minor bug fixes

= 1.0.0 =
* Release
