=== Lingonberry ===
Contributors: Anlino
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anders%40andersnoren%2ese&lc=US&item_name=Free%20WordPress%20Themes%20from%20Anders%20Noren&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Requires at least: 4.4
Tested up to: 5.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


== Installation ==

1. Upload the theme
2. Activate the theme

All theme specific options are handled through the WordPress Customizer.


== Licenses ==

Lato
License: SIL Open Font License, 1.1 
Source: https://fonts.google.com/specimen/Lato

Raleway
License: SIL Open Font License, 1.1 
Source: https://fonts.google.com/specimen/Raleway

FontAwesome
License: SIL Open Font License, 1.1 
Source: https://fontawesome.io

DoubleTapToGo.js
License: MIT License
Source: https://github.com/dachcom-digital/jquery-doubletaptogo

Fitvids.js
License: WTFPL
Source: http://fitvidsjs.com

Flexslider 2
License: GPLv2 
Source: http://flexslider.woothemes.com

screenshot.png header image
License: Public Domain 
Source: Taken by the theme author

screenshot.png post image 
License: CC0 Public Domain 
Source: http://www.unsplash.com


== Changelog ==

Version 1.47 (2019-04-07)
-------------------------
- Added the new wp_body_open() function, along with a function_exists check

Version 1.46 (2018-12-07)
-------------------------
- Fixed Gutenberg style changes required due to changes in the block editor CSS and classes
- Fixed the Classic Block TinyMCE buttons being set to the wrong font
- Adjusted custom font sizes
- Removed old vendor prefixes from the CSS

Version 1.45 (2018-11-30)
-------------------------
- Fixed Gutenberg editor styles font being overwritten

Version 1.44 (2018-11-03)
-------------------------
- Fixed the archive template date formatting

Version 1.43 (2018-10-05)
-------------------------
- Added Gutenberg support
- Improved support for < PHP 5.5
- Escaping of variables

Version 1.42 (2018-05-24)
-------------------------
- Fixed search field icon size
- Fixed output of comments cookie checkbox

Version 1.41 (2017-12-03)
-------------------------
- The pluggable update: Made all functions in functions.php pluggable

Version 1.40 (2017-11-28)
-------------------------
- Removed conditionals around the_content() output, as the conditional was interfering with plugins using the_content() to output stuff

Version 1.39 (2017-11-26)
-------------------------
- Updated to the new readme.txt format, with changelog.txt incorporated into it
- Removed the old video widget included in the theme, as there's one in core now
- Added a demo link to the stylesheet theme description
- Fixed notice in comments.php
- Changed closing element comment structure
- General code cleanup, improvements in readability
- Removed duplicate comment-reply enqueueing from the header (already in functions)
- SEO improvements (title structure, mostly)
- Better handling of edge cases (missing title, missing content)

Version 1.38 (2016-06-18)
-------------------------
- Added the new theme directory tags

Version 1.37 (2016-03-12)
-------------------------
- Removed wp_title() function from header.php

Version 1.36 (2015-08-25)
-------------------------
- Removed a superfluous jQuery function causing errors

Version 1.35 (2015-08-25)
-------------------------
- Fixed an issue with overflowing images
- Added the .screen-reader-text class

Version 1.34 (2015-08-11)
-------------------------
- Removed the title_tag fallback

Version 1.33 (2015-08-11)
------------------------- 
- Removed the post meta fields from post-new.php for format-video and format-audio
- Adjusted the styling of format-video
- Adjusted the styling of format-audio
- Removed mediaelement.js
- Placed the post meta in each content-[format] in a function: lingonberry_meta()
- The commments string is now hidden from the post meta if comments are closed
- Removed an add_shortcode() functions from functions.php
- Made post titles on single posts/pages h1 for SEO reasons
- Modified the theme widgets to use __construct(), in prep for WP 4.3
- Added sanitize_callback for the custom accent color control
- Removed custom title function and replaced it with title_tag()
- Added support for js comment-reply
- Fixed a styling bug in the comment navigation
- Updated the theme description
- Updated the Swedish translation

Version 1.32 (2014-09-02)
------------------------- 
- Fixed a bug that broke the video/audio format input fields

Version 1.31 (2014-08-07)
------------------------- 
- Removed conditional statement surrounding edit post link
- Added unique slugs for post meta boxes
- Various bug fixes
- Updated the Swedish translation

Version 1.30 (2014-08-06)
------------------------- 
- Updated the Swedish translation
- Fixed a typo in functions.php

Version 1.29 (2014-08-05)
------------------------- 
- Fixed the <head> and <meta> tags in header.php
- Optimized functions.php
- Fixed so that the navigation is visible when javascript is disabled
- Improved the styling of the comment respond area
- Improved the styling of forms in the post content area
- Adjusted the styling of the navigation toggle
- Fixed so that the time is displayed using the time format specified in the settings
- The footer is now hidden if no widgets have been added
- Added custom accent color support
- Added editor styles
- Updated the theme description and tags

Version 1.28 (2014-06-11)
------------------------- 
- Replaced esc_attr() with the_title_attribute()

Version 1.27 (2014-06-11)
------------------------- 
- Added esc_attr() to the the_title() tags in links

Version 1.26 (2014-06-09)
------------------------- 
- Fixed a bug where the comments in template-archive.php always would be displayed
- Added title="<?php the_title(); ?>" to post format icons
- Added a #comments link to comments.php
- Fixed some incongruities in the post-meta element in a couple of post formats

Version 1.25 (2014-04-24)
------------------------- 
- Added Media Element to enqueued scripts

Version 1.24 (2014-04-08)
------------------------- 
- Added a closing bracket to the html tag (thanks, bjamieson @ WPorg)

Version 1.23 (2014-03-20)
------------------------- 
- Fixed a bug in video-widget.php

Version 1.22 (2014-03-20)
------------------------- 
- Fixed video overflowing containers in IE, Chrome and Firefox

Version 1.21 (2014-03-17)
------------------------- 
- Fixed a bug in Chrome that stops fonts from rendering

Version 1.20 (2014-03-17)
------------------------- 
- Fixed minor bug in header.php

Version 1.19 (2014-03-14)
------------------------- 
- Fixed image.php bug, improved image.php styling

Version 1.18 (2014-03-12)
------------------------- 
- Fixed mixed textdomains

Version 1.17 (2014-02-24)
------------------------- 
- Replaced deprecated theme tags

Version 1.16 (2014-02-24)
------------------------- 
- Replace iframes with WordPress built-in oEmbed function
- Added support for featured images to pages
- Added retina ready theme screenshot
- Updated theme description
- Improved support for responsive videos
- Fixed a small positioning bug with the dropdown menu arrow signifying that it has children
- Updated the look of post-meta

Version 1.15 (2013-08-17)
------------------------- 
- Fixed a bug with content-audio (thanks @mbaker000!)

Version 1.14 (2013-08-11)
------------------------- 
- Fixed size and positioning of the format-status icon

Version 1.13 (2013-08-11)
------------------------- 
- Improved header search form
- Minor bug fixes

Version 1.12 (2013-08-11)
------------------------- 
- Added Swedish to /languages/
- Fixed a CSS bug at max-width: 600px

Version 1.11 (2013-08-11)
------------------------- 
- Fixed styling of some form elements

Version 1.10 (2013-08-11)
------------------------- 
- Added default widgets that are displayed when no widgets have been selected.

Version 1.09 (2013-08-10)
------------------------- 
- Improved pingback styling

Version 1.08 (2013-08-10)
------------------------- 
- Fixed pingback bug in comments.php

Version 1.07 (2013-08-08)
------------------------- 
- Changed styling of widget-less footer
- Added Theme URI to theme info
- Code cleaning and optimization

Version 1.06 (2013-08-08)
------------------------- 
- Fixed so that the footer is hidden if there are no widgets

Version 1.05 (2013-07-31)
------------------------- 
- Added slug to wp_enqueue_style

Version 1.04 (2013-07-31)
------------------------- 
- Bug fixes
- Updated readme.txt with correct license information for FitVids.js

Version 1.03 (2013-07-28)
------------------------- 
- Bug fixes
- Switched icon set from Entypo to Font Awesome, which is GPL 2.0 compatible

Version 1.02 (2013-07-25)
------------------------- 
- Improved credits link in footer.php

Version 1.01 (2013-07-25)
------------------------- 
- Bug fixes 
- Added license.txt, readme.txt and changelog.txt

Version 1 (2013-07-24)
-------------------------