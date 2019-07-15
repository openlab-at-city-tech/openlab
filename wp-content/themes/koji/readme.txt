=== Koji ===
Contributors: Anlino
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anders%40andersnoren%2ese&lc=US&item_name=Free%20WordPress%20Themes%20from%20Anders%20Noren&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Requires at least: 4.5
Tested up to: 5.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


== Installation ==

1. Upload the theme
2. Activate the theme


== Change pagination type ==

1. Log in to the administration panel of your site.
2. Go to Appearance → Customize.
3. Click the "Pagination" panel.
4. There are three options for the pagination:
	a. "Load more on button click": Displays a button that, when clicked, loads more posts without a hard reload.
	b. "Load more on scroll": When the visitor has reached the bottom of the page, more posts are loaded without a hard reload.
	c. "Previoius and next page links": Displays links that, when clicked, takes the visitor to then next or previous archive page with a hard reload.
5. Select the type you want to use, and click the blue "Publish" button to save your changes.


== Add social icons ==

1. Log in to the administration panel of your site.
2. Go to Appearance → Menus.
3. Click in the "Create a new menu" link. Give it a name, and click the "Create Menu" button.
4. Click on "Custom Links" in the "Add menu items" panel, and add the URL of the social media page you would like to link to. The icon is selected automatically based on the URL. Repeat for every link you want to add.
5. Scroll down to the bottom of the page, and in the "Menu Settings" list, select the "Social Menu" display location.
6. Click the blue "Save Menu" button. Your social menu will now be displayed on the site.


== Disable search ==

1. Log in to the administration panel of your site.
2. Go to Appearance → Customize.
3. Click the "Search" panel, and check the "Disable Search" checkbox.
4. Click the blue "Publish" button. The search toggle in the sidebar/mobile menu will now be hidden.


== Licenses ==

Images in screenshot.png from Pexels
License: Creative Commons Zero (CC0), https://creativecommons.org/publicdomain/zero/1.0/
Source: https://www.pexels.com/
Images (top left to bottom right):
- Scenic View of Rice Paddy: https://www.pexels.com/photo/scenic-view-of-rice-paddy-247599/
- Woman in Illuminated City at Night: https://www.pexels.com/photo/side-view-of-woman-in-illuminated-city-at-night-315191/
- Group of people: https://www.pexels.com/photo/adult-audience-celebration-ceremony-260907/
- Brick house: https://www.pexels.com/photo/architecture-building-cars-pavement-169572/
- London Phone Booth: https://www.pexels.com/photo/architecture-booth-buildings-bus-374815/
- Flock of Birds: https://www.pexels.com/photo/flock-of-birds-flying-and-diving-over-water-during-daytime-129848/
- Person on Train: https://www.pexels.com/photo/man-person-people-train-527/
- A Yellow Targa: https://www.pexels.com/photo/asphalt-auto-automobile-automotive-416757/
- Concert: https://www.pexels.com/photo/people-festival-party-dancing-849/ 

FontAwesome Icons
License: SIL Open Font License, 1.1, https://opensource.org/licenses/OFL-1.1
Source: https://www.fontawesome.io

FontAwesome Code
License: MIT License, https://opensource.org/licenses/MIT
Source: https://www.fontawesome.io

Feather Icons
License: MIT License, https://opensource.org/licenses/MIT
Source: https://feathericons.com


== Changelog ==

Version 1.44 (2019-06-08)
-------------------------
- Updated updateHistory() to work with permalink structure without an ending slash
- Set links inside figcaption/.wp-caption-text to inline
- Modified some social icons to check for the name without domain suffix, preventing issues with national domains (like pinterest.es)
- Fixed the mobile menu top padding compensating for admin bar when the admin bar isn't displayed

Version 1.43 (2019-04-07)
-------------------------
- Added the new wp_body_open() function, along with a function_exists check

Version 1.42 (2019-01-15)
-------------------------
- Fixed incorrect conditional messing up comments on sub pages

Version 1.41 (2018-12-28)
-------------------------
- Updated the aria-hidden attribute to be aria-hidden="true"
- Added the accessibility-ready theme tag to style.css, after review by @poena

Version 1.40 (2018-12-20)
-------------------------
- Fixed bad targeting in the focusLoop js function
- Fixed a variable being unset in the elementInView js function
- Untoggle the main menu if the window is resized to a bigger size than when the menu should be visible
- Updated the hover effect for links in the content
- Updated the modals to account for the height of the admin bar, preventing modal elements to be obscured
- Fixed the old light-gray color still being used in some places
- Unified the link styling in the widget areas

Version 1.39 (2018-12-15)
-------------------------
- Removed troubleshooting code from construct.js

Version 1.38 (2018-12-15)
-------------------------
- Fixed an undefined variable notice in comments.php
- More accessibility improvements (thanks to @poena for the accessibility review!):
	- Keyboard navigation
		- Reordered search overlay elements to place the search untoggle after the search field, and added hover/focus style to the search-untoggle button
		- After a new page has been AJAX loaded, set focus on the first item in the set of new posts
		- Removed keyboard navigation traps by hiding the overlays entirely when they’re not toggled
		- Hide skip links when the scroll is locked
	- Forms
		- Enabled access to the submit button in the search overlay, using screen-reader-text
	- Headings
		- Updated widget title and related posts title to h2, to prevent skipped heading levels
	- Contrasts
		- Made the background color of the body slightly lighter, to increase contrast of all elements outside of the main wrapper
		- Replaced the color #6d7781 with #68717b, to ensure a color contrast of 4.5:1 against light gray backgrounds
		- Changed hover effect of the following elements to underline instead of color change: single post pagination links, leave a comment link, theme credit link
		- Added a blue accent color for hover/focus effects in entry-content, comment-content and widget-content

Version 1.37 (2018-12-14)
-------------------------
- Removed a superfluous title attribute

Version 1.36 (2018-12-13)
-------------------------
- Accessibility improvements:
	- Checked all color contrast against the WCAG 2.0 AA standard
	- Updated toggles and toggle targets with the aria-pressed and aria-expanded attributes
	- Added aria-live and aria-controls attributes to the load more implementation
	- Added :focus styles for all of the things
	- Checked screen reader text
	- Changed the search toggle from a link element to a button element
	- Added skip links for the main menu and content
	- Added alt to images, and aria-hidden to icons
- Refactored the bypostauthor styles to work better with nested comments
- Fixed loadMore bungling the URL when dealing with query strings and hashes
- Adjusted preview title size
- Improved fallback image handling
- Various tweaks and fixes

Version 1.35 (2018-12-08)
-------------------------
- Fixed the default block appender having the wrong font family

Version 1.34 (2018-12-07)
-------------------------
- More Block Editor editor styles improvements
- Implemented the History API for AJAX loading
- Tested up to 5.0

Version 1.33 (2018-12-07)
-------------------------
- Updated Gutenberg editor styles

Version 1.32 (2018-12-07)
-------------------------
- Note: Bumped Koji to 1.31 by mistake in the previous version
- Fixed Gutenberg style changes required due to changes in the default block editor CSS and classes

Version 1.31 (2018-12-07)
-------------------------
- Fixed the Classic Block TinyMCE buttons being set to the wrong font

Version 1.12 (2018-11-30)
-------------------------
- Fixed Gutenberg editor styles font being overwritten

Version 1.11 (2018-11-10)
-------------------------
- Added the current theme version as the 'version' param for style.css and construct.js enqueues (thanks, @drivingralle)
- Updated the enqueue structure of style.css

Version 1.10 (2018-11-10)
-------------------------
- Changed all toggles to button elements, for a11y reasons
- Adjusted styling of the mobile menu
- Remove use of smallcaps, to improve the styling in older browsers
- Set the main element to display: block, to improve IE11 compat

Version 1.09 (2018-11-04)
-------------------------
- Removed min-width property from buttons
- Adjusted the widget comment author styling
- Fixed an issue with sidebar padding

Version 1.08 (2018-11-04)
-------------------------
- Added an additional widget area, which is displayed in the toggleable menu on mobile and in the sidebar on desktop
- Updated theme tags to indicate footer widget support

Version 1.07 (2018-11-04)
-------------------------
- Added three widget areas to the site footer
- Updated theme description

Version 1.06 (2018-10-14)
-------------------------
- Added the FontAwesome Solid font to enable more icons:
	- Unsplash
	- mailto:
	- Fallback icon for links without matching URLs
- Fixed the social icons list not wrapping

Version 1.05 (2018-10-10)
-------------------------
- Fixed the font size of the cite element when included in a pullquote

Version 1.04 (2018-10-09)
-------------------------
- Added an option for disabling the fallback image
- Added an option for disabling the related posts
- Added an option for using low-resolution images in previews
- Fixed overflow in the mobile menu

Version 1.03 (2018-10-07)
-------------------------
- More Gutenberg pull quote fixes
- Additional Gutenberg editor style fixes

Version 1.02 (2018-10-06)
-------------------------
- Fixed grammatical error in the theme description
- Updated theme description with link to demo site
- Adjusted Gutenberg blockquote styling
- Updated Gutenberg editor styles

Version 1.01 (2018-10-05)
-------------------------
- Escaped $ajax_url

Version 1.00 (2018-09-29)
-------------------------