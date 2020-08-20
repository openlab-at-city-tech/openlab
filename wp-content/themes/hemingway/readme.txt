=== Hemingway ===
Contributors: Anlino
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anders%40andersnoren%2ese&lc=US&item_name=Free%20WordPress%20Themes%20from%20Anders%20Noren&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Requires at least: 4.5
Tested up to: 5.4.1
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


== Installation ==

1. Upload the theme
2. Activate the theme

All theme specific options are handled through the WordPress Customizer.


== Licenses ==

Lato Font
License: SIL Open Font License, 1.1 
Source: https://fonts.google.com/specimen/Lato

Raleway Font
License: SIL Open Font License, 1.1 
Source: https://fonts.google.com/specimen/Raleway

Default header image included in the theme
License: CC0 Public Domain 
Source: http://www.unsplash.com

screenshot.png header image
License: CC0 Public Domain 
Source: http://www.unsplash.com

screenshot.png post image 
License: CC0 Public Domain 
Source: http://www.unsplash.com


== Changelog ==

Version 2.0.3 (2020-05-05)
-------------------------
- Updated the targeting of styles removing the padding, margin and border of the last post/page, to account for code added after the closing body tag.
- Added the `.clear` selector to the clearfix styles, to account for cached markup and markup in child themes.

Version 2.0.2 (2020-05-04)
-------------------------
- Fixed the title and featured image being displayed for post formats on archive pages when it shouldn't be (thanks, @daimonialisch).
- Fixed the style of the `cite` element in the quote post format having been unintentionally changed in 2.0.0 (thanks again).
- Adjusted the font size of the cite element in quote blocks with the large style.
- Tweaked blockquote margins.
- Block editor styles: Fixed the block appender paragraph having the wrong font family.
- Bumped "Tested up to" to 5.4.1.

Version 2.0.1 (2020-04-22)
-------------------------
- Media & Text block: Removed top margin of first item, and bottom margin of last item.
- Post meta: Added a filter for setting which post types should display post meta (defaults to post).
- Post meta: Added actions before and after post meta output.
- Post meta: Don't output the comments link if comments are closed.

Version 2.0.0 (2020-04-09)
-------------------------
- Added "block-styles" to the theme tags in `style.css`.
- Deleted license.txt from the theme files.
- Deleted the `languages` folder, since the language files were incomplete and translations are handled by GlotPress on WordPress.org.
- Added the new `/assets/` sub folder, and moved the `/js/` and `/images/` folders into it.
- Renamed the editor style CSS files, and moved them to the new `/assets/css/` folder.
- Renamed `hemingway_add_gutenberg_features()` to `hemingway_block_editor_features()`.
- Created a separate file for the Hemingway_Customize class in `/inc/classes/`.
- Moved the `/widgets/` folder to the new `/inc/` folder.
- Removed the unused `hemingway_options` Customizer section.
- Added support for the core custom_logo setting, and updated the old hemingway_logo setting to only be displayed if you already have a hemingway_logo image set.
- Bumped the "Requires at least" tag to 4.5.0, since Baskerville is now using custom_logo.
- Updated "Tested up to" to 5.4.
- Header: Updated markup for better SEO, cleaned up code.
- Added screen reader text to the menu toggle and search toggle.
- Removed the `hemingway_nav_walker` navigation walker class, since it wasn't needed.
- Updated main menu sub menu targeting to use the built-in `has-children` class, instead of the one added by the custom walker.
- Remove text antialiasing in Webkit browsers.
- Updated widget area registration to include widget IDs in output.
- Removed `<div class="clear"></div>` elements, and replaced them with pseudo clearing or flexing.
- Customizer: Removed postMessage updating, due to faulty implementation in the theme.
- Set links to get underline on hover by default.
- Updated the light gray text color (#999) to have higher contrast (#767676).
- Removed `hemingway_body_classes()`, since the only class added wasn't being used in the stylesheets.
- Removed admin CSS setting a max width on the post thumbnail when editing a post, since it hasn't been needed for 5+ years.
- Added a helper function used for getting the Google Fonts URL, used when enqueueing style.css and the editor styles.
- Made the Google Fonts families used filterable.
- Added new function for checking if a comment is posted by the post author.
- Made the entire comment timestamp string translateable.
- Removed output of "Comments are closed" on posts.
- Set the post title to use the `h1` heading element on singularm, and `h2` on archive pages.
- Added a sensible element base, so elements like headings have hierarchical font sizes outside of the post content.
- Removed removal of outline on focus.
- Restructured Block Editor specific CSS entirely.
- Removed the border around images in the content – it was causing too many issues with images used in blocks.
- Don't output the inline color styles if the color is the same as the default color.
- Restructured the custom accent color output code to be more compact and flexible, and added a filter for the selectors targeted.
- Updated the calendar widget styles for 5.4.
- Updated edit post link output to not include a custom permissions check.
- Updated the screenshot to be 1200x900px, and changed the file format to JPG for smaller file size.
- Output excerpts instead of the full content on the search results page.
- Restructured the archive header, and added output of archive description.
- Added a helper function for output of the featured media, to reduce the amount of repeated code.
- Updated featured media to use HTML5 elements, and fixed markup structure issue with links in featured media captions.
- Updated `singular.php`, ´template-fullwidth.php` and ´template-nosidebar.php` to use `content.php`, reducing duplicate code.
- Better Block Editor styles.
- Cleaned up the custom widgets.

Version 1.75 (2019-04-07)
-------------------------
- Added the new wp_body_open() function, along with a function_exists check

Version 1.74 (2019-01-08)
-------------------------
- Changed the entry title in singular.php to only be a link when displaying posts

Version 1.73 (2018-12-23)
-------------------------
- Updated index.php so the page title is still displayed if no results have been found

Version 1.72 (2018-12-23)
-------------------------
- Updated index.php with output if no search results have been found

Version 1.71 (2018-12-15)
-------------------------
- Unified index.php, archive.php and search.php into index.php
- Unified page.php and single.php into singular.php
- Unified all post formats into content.php
- Removed searchform.php from the theme
- Unified and improved search styles
- General styling improvements, old vendor prefix cleanup
- Changed the toggles to button elements
- Removed styling that removed outlines from links on focus
- Compressed the default header
- Set the version variable on enqueues for cache busting

Version 1.70 (2018-12-07)
-------------------------
- Fixed Gutenberg style changes required due to changes in the block editor CSS and classes
- Fixed the Classic Block TinyMCE buttons being set to the wrong font
- Fixed a couple of front-end formatting issues in Gutenberg

Version 1.69 (2018-11-30)
-------------------------
- Fixed Gutenberg editor styles font being overwritten

Version 1.68 (2018-11-13)
-------------------------
- Fixed aligncenter issue in Gutenberg
- Fixed alignment of the last item in WP Block Gallery
- Updated theme description

Version 1.67 (2018-09-08)
-------------------------
- Fixed four _x() translateable string without instructions

Version 1.66 (2018-09-08)
-------------------------
- The Gutenberg [G] update!
- [G] Added front-end Gutenberg style
- [G] Added Gutenberg editor styles
- [G] Added a Gutenberg color palette, with custom accent color support
- [G] Added Gutenberg font sizes
– Refined the custom CSS code for the accent color to be less messy
– Made it possible to deactivate Google Fonts by a translateable string, same as the TwentyXXX implementation
- Made the name of the "primary" menu theme location translateable
- CSS formatting and other minor tweaks and fixes

Version 1.65 (2018-06-01)
-------------------------
- Fixed the date output for the 30 most recent posts in template-archives.php
- Fixed error in pre-PHP 5.5

Version 1.64 (2018-05-24)
-------------------------
- Fixed output of cookie checkbox in comments

Version 1.63 (2018-03-29)
-------------------------
- Adjusted viewport meta tag element from 1.60

Version 1.62 (2018-03-28)
-------------------------
- Version bump due to issues with the theme uploader on 2018-03-27

Version 1.61 (2018-03-27)
-------------------------
- Updated footer colors to have stronger contrast
- Updated header colors to also pass the WCAG AA standard

Version 1.60 (2018-03-27)
-------------------------
- Removed the viewport meta tag from the head
- Moved clear element out of .blog-menu element
- Tweaked blog description styling
- Updated footer colors to make sure they pass the WCAG AA standard
- Changed ternarys to be full-length, to retain compatibility with older PHP versions
- Set links within .gallery-caption and .wp-caption-text to be inline
- General cleanup

Version 1.59 (2017-12-03)
-------------------------
- The pluggable update: made all functions in functions.php pluggable

Version 1.58 (2017-11-28)
-------------------------
- Cleaned up a bit in archive.php
- Removed conditionals around the_content() output in single.php, as the conditional was interfering with plugins using the_content() to output stuff

Version 1.57 (2017-11-26)
-------------------------
- Updated to the new readme.txt structure, with changelog included in readme
- Added demo URL to the stylesheet theme description
- Removed the video widget, as WordPress core now provides its own
- Updated comment header structure in functions.php
- Fixed stylesheet not being included when Hemingway is used as a parent theme
- Removed comment-reply.js output in header.php (already enqueued in functions)
- General cleanup and code readability improvements
- Replaced the_title() with the_title_attribute(), when used for title attributes in link elements
- Fixed transition on has-children indicator in the main menu
- Fixed notice in comments.php

Version 1.56 (2016-06-18)
-------------------------
- Added the new theme directory tags
- Fixed video widget notice
- Some PHP cleanup

Version 1.55 (2016-03-12)
-------------------------
- Removed wp_title() from header.php

Version 1.54 (2015-08-25)
-------------------------
- Fixed overflow bug with wp-caption
- Added screen-reader-text styling

Version 1.53 (2015-08-24)
-------------------------
- Fixed a floating bug with the single post navigation

Version 1.52 (2015-08-11)
-------------------------
- Removed the title_tag fallback

Version 1.51 (2015-08-11)
-------------------------
- Added a missing margin between the format-video post content and post meta on archive pages

Version 1.50 (2015-08-11)
-------------------------
- Removed a add_shortcode() function from functions.php
- Added title_tag() support
- Fixed a busted sanitize_callback in functions.php

Version 1.49 (2015-08-10)
-------------------------
- Added UTF-8 as charset in style.css
- Updated widgets with PHP5 object constructors for WordPress 4.3
- Removed meta fields for the video post format in order to comply with WordPress theme review guidelines (presentation vs. functionality)
- Condensed functions.php and added the comment-reply script
- ...as well as styling for the comment form when it is within .commentlist
- Fixed so that the featured image is centered if it's smaller than the containing element
- Fixed so that comments will be displayed on pages if there are comments, even if "Allow Comments" is not checked (the reply form is still hidden)
- Fixed a bug with floating elements in the comments navigation
- Fixed a styling error with the dropdown arrow in the main menu (one level deep)
- Changed titles on single posts/pages from h2 to the h1 element

Version 1.48 (2014-10-01)
-------------------------
- Added a width attribute in the inline style of images in posts (thanks, RavanH!)
- Added styling of input elements of the email type to style.css (Ibid)

Version 1.47 (2014-08-06)
-------------------------
- Added missing form/input elements to the custom accent color control
- Added license information for screenshot.png

Version 1.46 (2014-08-06)
-------------------------
- Fixed a clearing bug in .blog-menu
- Optimized the CSS for brevity and browser compatibility
- Improved the display of forms and inputs in the post-content
- Improved the display of the comment form
- Updated the Swedish translation, added missing namespaces

Version 1.45 (2014-07-25)
-------------------------
- Fixed so that the header height is fixed, rather than relative to the width of the screen
- Fixed a bug in functions.php which would prevent plugins from setting featured images
- Added in some missing accent color elements
- Fixed so that the "Comments are closed" message isn't displayed on pages with comments deactivated
- Removed the default widgets that previously would've been displayed in no widgets had been entered

Version 1.44 (2014-05-08)
-------------------------
- Added a query reset to template-archives.php to prevent the wrong comments from loading

Version 1.43 (2014-05-06)
-------------------------
- Fixed so that the recent posts widgets only display published posts
- Added a full width page template

Version 1.42 (2014-04-13)
-------------------------
- Added support for editor styles

Version 1.41 (2014-04-08)
-------------------------
- Fixed so that .widget_links is included in the custom accent color settings

Version 1.40 (2014-03-31)
-------------------------
- Added a function for uploading a custom logo in place of the site title and description in the header
- Fixed the styling of current-menu-item in the navigation

Version 1.39 (2014-03-28)
-------------------------
- Fixed the styling of form elements

Version 1.38 (2014-03-25)
-------------------------
- Fixed centering of .aligncenter images

Version 1.37 (2014-03-24)
-------------------------
- Added anchor link "#comments" to comments.php
– Fixed Google Fonts enqueue to avoid SSL issues
– Fixed a developer notice on search.php and 404.php
– Removed some superfluous lines from functions.php
- Updated theme description and tags

Version 1.35 (2014-03-20)
-------------------------
- Added a theme option for accent color


Version 1.34 (2014-03-20)
-------------------------
- Fixed a video-widget bug

Version 1.33 (2014-03-20)
-------------------------
- Fixed a bug where the site title in the header would be hidden if site description is empty

Version 1.32 (2014-03-18)
-------------------------
- Fixed a bug displaying content.php as excerpts on archives

Version 1.31 (2014-03-17)
-------------------------
- Hides blog-info div in header if both site title and site description are empty
- Added title to changelog.txt

Version 1.30 (2014-03-11)
-------------------------
- Fixed main-menu hover on tablets

Version 1.28 (2014-02-24)
-------------------------
- Replaced the embeds in video-widget.php with WordPress built-in oEmbed function

Version 1.27 (2014-02-24)
-------------------------
- Replaced the embeds in content-video.php and single.php with WordPress built-in oEmbed function

Version 1.26 (2014-02-18)
-------------------------
- Fixed an issue with iframes

Version 1.25 (2014-02-18)
-------------------------
- Fixed a bug with custom header images

Version 1.24 (2014-02-18)
-------------------------
- Fixed a security glitch allowing users to enter js in the video post format url

Version 1.23 (2014-02-17)
-------------------------
- Add max-width to .featured-media iframe

Version 1.22 (2014-02-17)
-------------------------
- Fixed tag cloud styling

Version 1.21 (2014-02-17)
-------------------------
- Fixed a bug in the condititional statement from the last update

Version 1.20 (2014-02-17)
-------------------------
- Added condititional statement for blog-description to prevent empty h3 in header

Version 1.19 (2014-02-17)
-------------------------
- Hidden img#wpstats smiley

Version 1.18 (2014-02-17)
-------------------------
- Another update to the theme description (sorry, theme reviewers)

Version 1.16 (2014-02-17)
-------------------------
- Minor change to the theme description

Version 1.15 (2014-02-17)
-------------------------
- Added font-smoothing to bdoy to make sure that the WordPress.org theme demo renders the theme correctly

Version 1.14 (2014-02-17)
-------------------------
- Fixed the meta tag to be HTML 5 compliant
- Fixed so that placeholder widgets are shown if no widgets have been added

Version 1.13 (2014-02-16)
-------------------------
- Added post thumbnail support for page and template-nosidebar

Version 1.12 (2014-02-13)
-------------------------
- Fixed meta tag http-equiv

Version 1.11 (2014-02-11)
-------------------------
- Added a Swedish translation

Version 1.10 (2014-02-09)
-------------------------
- Fixed a styling issue with align-left and align-right @600px

Version 1.09 (2014-02-09)
-------------------------
- Fixed alignment on post-nav-newer

Version 1.08 (2014-02-05)
-------------------------
- Misc bug fixes

Version 1.07 (2014-02-03)
-------------------------
- Misc bug fixes

Version 1.06 (2014-02-03)
-------------------------
- More post-meta-bottom adjustments
– Improved footer styling
- Misc bug fixes

Version 1.05 (2014-02-02)
-------------------------
- Cleaned up post-meta-bottom
- Misc bug fixes

Version 1.04 (2014-02-01)
-------------------------
- Fixed styling for the archive template
- Cleaned up style.css, fixed section numbering
– Misc bug fixes

Version 1.03 (2014-01-31)
-------------------------
- Added larger screenshot.png to account for high DPI displays
- Misc bug fixes

Version 1.02 (2014-01-31)
-------------------------
- Added video post format
- Added aside post format

Version 1.01 (2014-01-31)
-------------------------
- Changed the theme description in style.css

Version 1 (2014-01-31)
-------------------------