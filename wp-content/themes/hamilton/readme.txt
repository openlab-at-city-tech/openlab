=== Hamilton ===
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


== Resume Page Template ==

1. Create a new page, or edit an existing one
2. Click the dropdown beneath "Template" in "Page Attributes", and select Resum.

In the resume page template, all titles span the entire width of the content, whereas all other elements are aligned to the right. This enables you to create sections in the resume content by simple adding another title. For instance, adding a title called "Education" and adding a paragraph of text beneath it will automatically create a section with the "Education" title to the left and the paragraph of text to the right.


== Frequently Asked Questions ==

= How do I activate infinite scroll? =
Hamilton uses the Jetpack module for infinite scroll. To activate it, install the Jetpack plugin and activate the infinite scroll module in the Jetpack settings. The theme will take care of the rest.

= What do the Hamilton theme options in the WordPress customizer do? =
Show Primary Menu in the Header — Replaces the navigation toggle in the header with the Primary Menu on desktop.
Three Columns — Displays the post grid with up to three columns on desktop. The grid will still be displayed with two columns on tablets and mobile screen sizes.
Show Preview Titles — Always display the post titles on top of the images in post previews, rather than on hover which is the default behaviour.
Front Page Title – The title you want shown on the front page when the "Front page displays" setting is set to "Your latest posts" in Settings > Reading.
Dark Mode (displayed in the Colors tab) — Displays the site with white text and black background. If Background Color is set, only the text color will change. You can combine the background color with the dark mode to, for instance, display the site with a dark purple background color and white text color.


== Licenses ==

Libre Franklin font
License: SIL Open Font License, 1.1
Source: https://fonts.google.com/specimen/Libre+Franklin

Images in screenshot.png by Fancycrave, supplied through Pexels
License: Creative Commons Zero (CC0), https://creativecommons.org/publicdomain/zero/1.0/
Source: https://www.pexels.com/u/fancycrave-60738/


== Changelog ==

Version 2.0.7 (2020-05-20)
-------------------------
- Improved the stacked gallery behavior when using the old markup structure.

Version 2.0.6 (2020-05-20)
-------------------------
- Removed some gallery block styles no longer needed since the entry-content style restructure in 2.0.0.
- Removed the extra 16px bottom margin from gallery blocks when the markup structure is ´.wp-block-gallery > .blocks-gallery-grid`.
- Changed the entry content links from using borders to using `text-decoration: underline`, to make them work better with block styles.
- Tweaked list styles to give them better default margins.
- Improved structure of bottom meta with flex, removed excessive vertical margin of the meta paragraphs.
- Removed the vertical margin between stacked gallery blocks of the same alignment, to match the styling of the old `[gallery]` shortcode.
- Added the "Requires at least and "Tested up to" headers to `style.css`, per new Theme Directory requirements.

Version 2.0.5 (2020-05-19)
-------------------------
- Wrapped the comments section in a div with the `comments` ID, so the `comments_popup_link()` has an element to point to (thanks, @jeroenrotty).

Version 2.0.4 (2020-05-07)
-------------------------
- Removed the `header-text` parameter from `add_theme_support( 'custom-logo' );`, fixing issue where the logo might become hidden.

Version 2.0.3 (2020-05-07)
-------------------------
- Fixed the navigation toggle having a background color when dark mode is active (thanks, Melvyn Tan).

Version 2.0.2 (2020-05-01)
-------------------------
- Fixed the archive navigation having underlined links (thanks, @ventair).

Version 2.0.1 (2020-04-30)
-------------------------
- Don't output the default archive title ("Archive") on the blog page when a hamilton_home_title isn't set in the Customizer.
- Fixed preview refresh when changing hamilton_home_title.

Version 2.0.0 (2020-04-30)
-------------------------
- Removed all title attributes from links.
- Removed default removal of list style from ordered and unordered lists.
- Reworked the CSS reset to inherit rather than unset.
- Updated "Requires at least" to 4.5, since we're using custom_logo
- Bumped "Tested up to" to 5.4.1.
- Reworked the header title output to be simpler, output a H1 heading in the right circumstances, and include the site title as screen reader text when a logo is set.
- Removed code specific to the languages folder, which no longer exists (localization is handled through GlotPress on WordPress.org).
- Added theme version to enqueues.
- Renamed the Hamilton_Customize class to be camelcased, and moved it to the new `/inc/classes/` folder.
- Moved modifications of the archive title and description to filters for get_the_archive_title/_description, and simplified `index.php`.
- Updated the archive title element to be either h1 or h2, depending on the page being displayed.
- Changed the featured image wrapping element to a `figure`.
- Added a `global $post;` before using `setup_postdata()` in `related-posts.php`.
- Changed targeting of block editor colors and font sizes to apply outside of the entry content.
- Fixed base block margins targeting of the social block.
- CSS: Added new sections for Element Base and Blocks, and restructured the file accordingly.
- Set links to be underlined by default, and inherit their colors.
- Changed styles for lists, headings and paragraphs to be global instead of entry content specific, which reduces specificity and makes it easier to maintain compatibility with the Core block editor styles.
- Removed removal of outline from inputs.
- Added base styles for more inputs.
- Changed the navigation toggle to a button, and added screen reader text.
- Reworked the site navigation so the nav footer is not sticky.
- Fixed issue with Jetpack infinite scroll.
- Converted the theme screenshot to JPG, reducing file size by 500 KB.

Version 1.28 (2020-04-25)
-------------------------
- Singular: Added output of edit link after the entry content.
- Singular: Changed the post_class element to a `article` element.
- Singular: Added the `entry` class to the `article` element, and the `entry-title` class to the post heading.
- Set the post thumbnail size to the size of the `hamilton_fullscreen-image` image size, and removed said image size.
- Moved the editor styles to the new `/assets/css/` folder, and renamed them.
- Removed output of "Comments closed" when the comments field is closed.
- Fixed targeting of the "Sticky post" string in post previews.
- Updated Firefox text aliasing to better match Safari and Chrome.
- Increased the color contrast of the light gray color.

Version 1.27 (2020-04-02)
-------------------------
- Updated alignwide width to match the featured image width (1240px).
- Added clearfix to the entry-content.
- Bumped "Tested up to" to 5.4.
- Updated styles to work with the new markup structure for the gallery block.
- New block styles: Social, Buttons.
- Added base block margins (for blocks without alignments set)

Version 1.26 (2019-07-20)
-------------------------
- Fixed issue with images on archive pages

Version 1.25 (2019-07-20)
-------------------------
- Added theme URI to style.css
- Updated "Tested up to"
- Added theme tags
- Added skip link
- Don't show comments if the post is password protected
- Don't show the post thumbnail if the post is password protected
- Fixed font issues in the block editor styles
- Improvments to the alt nav/JS fallback
- Added search form to no search results page
- Input styling improvements

Version 1.24 (2019-04-07)
-------------------------
- Added the new wp_body_open() function, along with a function_exists check

Version 1.23 (2018-12-07)
-------------------------
- Fixed Gutenberg style changes required due to changes in the block editor CSS and classes
- Fixed the Classic Block TinyMCE buttons being set to the wrong font

Version 1.22 (2018-11-30)
-------------------------
- Fixed Gutenberg editor styles font being overwritten

Version 1.21 (2018-11-06)
-------------------------
- Fixed color of links in pre block
- Fixed post titles being closed incorrectly in singular.php

Version 1.20 (2018-11-04)
-------------------------
- Fixed MediaElement.js player button styling issue

Version 1.19 (2018-11-03)
-------------------------
- Updated with Gutenberg support
	- Gutenberg editor styles
	- Styling of Gutenberg blocks
	- Custom Hamilton Gutenberg palette
	- Custom Hamilton Gutenberg typography styles
- Added option to disable Google Fonts with a translateable string
- Updated theme description

Version 1.18 (2018-05-24)
-------------------------
- Improved styling of checkboxes in the comment form

Version 1.17 (2017-12-20)
-------------------------
- Fixed caption text color when in dark mode

Version 1.16 (2017-12-03)
-------------------------
- Made functions.php functions pluggable

Version 1.15 (2017-11-29)
-------------------------
- Switched from wp_print_styles to wp_enqueue_scripts to enqueue scripts and styles

Version 1.14 (2017-11-22)
-------------------------
- Set the featured image wrapper to position: relative, so it's displayed over the post header during the scroll transition

Version 1.13 (2017-11-22)
-------------------------
- Added a flex based CSS fix for a site header layout issue occurring when using the alt navigation in combination with a really big header logo

Version 1.12 (2017-09-15)
-------------------------
- Updated the site-nav to adjust the top padding depending on the dimensions of the custom logo, preventing an overflow issue

Version 1.11 (2017-07-20)
-------------------------
- Removed the included ImagesLoaded file, as it has been replaced by the bundled WP one

Version 1.10 (2017-07-20)
-------------------------
- Added a demo URL to the theme description

Version 1.09 (2017-07-18)
-------------------------
- Replaced imagesloaded with bundled WordPress version
- Added escaping of home_url() and get_theme_mod()
- Added prefixes to image sizes
- Changed sanitize callback for hamilton_home_title customizer option
- Added a wp_list_pages fallback to the primary menu

Version 1.08 (2017-07-12)
-------------------------
- Mentioned the resume page template in the theme description

Version 1.07 (2017-07-10)
-------------------------
- Added the resume page template

Version 1.06 (2017-07-10)
-------------------------
- Various visual tweaks, improvements and adjustments
- Added a smooth scroll to anchor links

Version 1.05 (2017-07-10)
-------------------------
- Typography tweaks, CSS only

Version 1.04 (2017-07-10)
-------------------------
- Replaced the Unsplash images in screenshot.png, and updated the Licenses section of the readme accordingly
- Updated the style.css TOC to match the section names in the CSS

Version 1.03 (2017-07-10)
-------------------------
- Added the fade-in on visible effect to post previews in the related posts section on single posts as well

Version 1.02 (2017-07-09)
-------------------------
- Hide gallery captions on mobile to prevent clipping

Version 1.01 (2017-07-09)
-------------------------
- Fixed margins between multiple stacked galleries on small screen sizes

Version 1.0 (2017-07-09)
-------------------------