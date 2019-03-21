# Genesis Framework Change Log

https://my.studiopress.com/themes/genesis/

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Up until release 2.7.0, this project did _not_ follow semantic versioning. It followed the WordPress policy where updates of x and y in an x.y.z version number means a major release, and updates to z means a patch release.

## [2.8.1] - 2019-01-30
### Fixed
- Fixed `genesis_human_time_diff()` to display accurate relative dates.
- Fixed a problem with `aria-hidden` and `tabindex` attributes were being escaped, causing the quotes to be unintentionally encoded.

## [2.8.0] - 2019-01-16
### Added
- Add a `genesis_get_config()` function, to locate and load config files from Genesis and a child theme.
- Add a new "onboarding" feature that allows users to import homepage demo content in WordPress 5.0.
- Add a new function that allows you to get an author box by specified user.

### Changed
- Improved/clarified the labels on settings/customizer pages.
- Changed references of "- None -" to "None" in forms, for better accessibility.

## [2.7.3] - 2018-12-19
### Fixed
- Fixed an issue with the search form, where some elements were missing attributes, or had the wrong attributes.

## [2.7.2] - 2018-12-13
### Fixed
- Fixed issue with schema on the breadcrumbs wrapper by removing breadcrumb div schema.org attributes when not needed, use RDFa for Breadcrumb NavXT.
- Fixed issue with the search form not properly outputting a label when a11y is enabled.

## [2.7.1] - 2018-11-15
### Fixed
- Fixed issue with filtered content being passed to `wp_kses_post()`.
- Fixed issue with the `genesis_search_form()` function returning nothing if used directly.

## [2.7.0] - 2018-11-14
### Added
- Added soft PHP 5.3 requirement, with admin messaging.
- Added meta tag for breadcrumb position.
- Added ability to export or remove private data via the WordPress privacy tools.
- Added ability to autoload namespaced classes.
- Added `genesis_is_amp()` utility function for detecting when the request is an AMP URL.
- Added `minimum-scale` to the viewport meta tag when the request is an AMP URL.
- Added a `genesis_more_text` filter.
- Added a `/docs` folder for housing Genesis documentation.
- Added individual changelog files for each release.
- Added SEO support for SEOPress.
- Added Genesis version to the "At a Glance" dashboard widget.
- Added `rel="noopener noreferrer"` to new window links.
- Added `aria-current` to pagination for accessibility.

### Changed
- Use [Semantic Versioning](https://semver.org/) for all future releases.
- Use config file for breadcrumb arguments.
- Use Markup API to build breadcrumb links.
- Redirect to the "What's New" page on all upgrades, not just "major" ones.
- Change the license line in all file headers to "GPL-2.0-or-later".
- Use `wp_strip_all_tags()` instead of `strip_tags()`.
- Replace all references to "Copyblogger" with "StudioPress".
- Refresh `.editorconfig`.
- Use Markup API for opening and closing `entry-content` tags.
- Clear cache at the end of an upgrade.

### Fixed
- Fixed various code standards violations.
- Fixed various missing or incorrect inline documentation.
- Fixed issue where avatars were fetched even when the size to fetch is `0`.
- Fixed issue where `genesis_update_action_links()` was not returning an array.
- Fixed potential null pointer exceptions.
- Fixed misuses of `mb_strlen()`.
- Fixed Tiago Hillebrandt's Twitter link.

### Removed
- Deprecated `genesis_is_major_version()`.
- Removed direct file access block from `comments.php`.
- Removed an unused variable assignment in the entry content output function.
- Removed a duplicate `description` from `composer.json`.
- Removed tab stop on `aria-hidden` featured images.
- Remove all references to "Scribe".

## [2.6.1] - 2018-03-14
### Fixed
- Fix compatibility issue with breadcrumbs in Yoast SEO.
- Fix issue with extra slashes in settings when using Customizer.
- Fix PHP 7 issue with non-static methods being used statically.
- Fix empty string warning in `skip-links.js`.

## [2.6.0] - 2018-03-05
### Added
- Add option to sort Featured Posts by date modified.
- Add contextual filter for `content` passed through the Markup API.
- Add `Genesis_Customizer` class.
- Add `Genesis_SEO_Document_Title_Parts` class.
- Add `title-tag` theme support by default.
- Add class autoloader.
- Add support for AdSense Auto Ads.
- Add `aria-label` attribute to secondary `nav` element.
- Add allowance for extra attributes on script tags for registered scripts.

### Changed
- Change URLs to `https` wherever possible.
- Update normalize.css to `7.0.0`.
- Duplicate all theme and SEO settings in the Customizer.
- Move all classes to their own files in `lib/classes`.
- Use Markup API for `entry-title-link`.
- Use Markup API for 404 page title.
- Change description for headings on archive pages to account for accessibility.
- Improve color scheme retrieval function.

### Fixed
- More compliance with WordPress coding standards.
- Set ID of `entry-pings` to `comments` if only pings exist.
- Ensure default settings get saved to database in new installs.
- Change `h3` to `h2` for titles in admin metaboxes.
- Ensure theme support for Genesis import / export menu before outputting.
- Check for post parents before outputting parent in breadcrumbs.
- Ensure `[post_tags]` and `[post_categories]` are valid for post type before outputting.
- Update `aria-label` attributes for `nav` elements to remove redundant "navigation" word.

### Removed
- Remove duplicate `genesis_load_favicon` from being hooked to `wp_head`.
- Remove screen reader `h2` from inside Header Right widget area.
- Remove screen reader `h2` from inside primary `nav` element.
- Remove feed settings if Genesis 2.6 is your first version.

## [2.5.3] - 2017-09-27
### Fixed
- Prevent global scripts being slashed if they are unchanged.

## [2.5.2] - 2017-06-09
### Fixed
- Alternate method for preventing attribute filter on closing tags.

## [2.5.1] - 2017-06-08
### Added
- Add logic to detect post-upgrade redirect type.

### Changed
- Updated docblock for `genesis_post_meta()`.

### Fixed
- Fix issue with script loading logic.
- Fix issue with Layout API fallback logic.
- Fix issue with Layout API type priority determination.
- Fix issue with posts not being excluded in Featured Posts widget.
- Fix issue with `entry` attribute filter being applied to closing tag.
- Fix issue with use of `require` by switching back to `require_once`.

## [2.5.0] - 2017-04-20
_Requires WordPress 4.7.0.__
### Added
- Add instances of markup API use in several locations where it was previously not used.
- Add any missed XHTML markup to the XHTML markup filter.
- Add `Genesis_Contributors` and `Genesis_Contributor` classes.
- Add `views` directory and extracted output to organized view files.
- Add full support for WordPress's new title tag.
- Add slashing for user script input fields.
- Add primary category support when Yoast SEO is on, but breadcrumb feature is off.
- Add support for multiple layout types depending on context.
- Add script loader class.
- Add ability to specify location of entry scripts via a second option.
- Add filter for capability required to use CPT archive settings.
- Add filter to disable layout settings on CPT archive settings page.
- Add sanitizer for layout settings on CPT archive settings page.
- Add a posts page check to `genesis_do_blog_template_heading()`.
- Add filter for entry content display options in the customizer.
- Add terms back to terms array in our terms filter.
- Add `genesis_strip_p_tags()` function.
- Add center alignment option to featured image alignment setting.
- Add more filters to breadcrumb class.

### Changed
- Split featured post and page widget entry header markup, gave markup API context for each.
- Restored adding `tabindex` via JavaScript when `genesis-accessibility` is supported.
- Prevent smushed offscreen accessible text.
- Reorganized `init.php`.
- Strip paragraph tags from filtered credits text to avoid paragraph nesting.
- Standardize the context naming in widget markup.
- Flag entry markup as `is_widget` via the params array so it can be modified without affecting other entries.
- Restored new line between admin screen buttons.
- Improvements to composer, PHPCS, and unit tests.
- Switch all schema.org URLs to `https`.
- Formally deprecate `genesis_get_additional_image_sizes()`.
- Formally deprecate `genesis_contributors()`.
- Formally deprecate `genesis_register_scripts()`.
- Formally deprecate `genesis_load_scripts()`.
- Formally deprecate `genesis_load_admin_scripts()`.
- Formally deprecate `genesis_load_admin_js()`.
- CSS improvements.
- Code optimization and documentation improvements.
- Ensure skip links filter returns an array.
- Improve randomness of search form ID.
- Fix potential issue with footer scripts filter.
- Move `aria-label` to the anchor element so screen readers will announce it.
- Add capability check to CPT archive settings link in the toolbar.
- Refactor and improve archive headings.
- Fix typo in comments feed setting.

### Removed
- Remove semantic headings SEO option, with fallback for backward compatibility.
- Disable `backtotop` output if HTML5 is on.
- Remove output buffering on search form.
- Remove unnecessary heading on skip links.

## [2.4.2] - 2016-10-04
### Fixed
- Fix issue with featured post/page widget outputting `entry-content` div when XHTML is active.

## [2.4.1] - 2016-09-30
### Fixed
- Fix issue with filters on featured post and page widget content output.
- Fix some typos in the What's New page, as well as the `CHANGELOG.md` file.

## [2.4.0] - 2016-09-28
_Requires WordPress 4.4.0.__
### Added
- Add `unfiltered_or_safe_html` sanitizer.
- Add or correct lots of inline documentation.
- Add `phpcs.xml` file for code standards testing.
- Add identifying classes to featured posts' "More Posts" section title and list.
- Add `$wrap` and `$title` to the passed arguments of the `genesis_post_title_output` filter.
- Add new features to the Markup API, allowing for open and close arguments, passing content, and new filters.
- Add `js-superfish` class to all menus that support it.
- Add missing "to" in `genesis_prev_next_post_nav()`'s comment header.
- Add new functions that handle the logic for meta and favicon markup, and amended existing output functions to use them.
- Add release notes going back to 1.6.0 to `CHANGELOG.md`.

### Changed
- Extract XHTML from Genesis output, and added it back in with new Markup API filters if HTML5 is not supported.
- Move `genesis_create_initial_layouts()` to the `genesis_setup` hook. Possible breaking change, in order to ensure compatibility with WordPress 4.7+.
- Move `h1` elements outside the form on admin settings pages.
- Move SEO tooltips to Help tab on post editor screen.
- Change URLs for gravatars on the "What's New" page to use HTTPS.
- Change Featured Post widget to use placeholder instead of default value for number of posts to show.
- Change CPT archive intro setting to use `unfiltered_or_safe_html` sanitizer.
- Change some code and most documentation to better match WordPress coding standards.
- Change to use of time constants in update check transients.
- Change sitemap to hide Posts-related sections if the site has no Posts.
- Change `genesis_user_meta_default_on()` and `Genesis_Admin::create()` to do return checks earlier.

### Removed
- Remove colons from labels on settings screens.
- Remove errant `$` in the URL used in the "parent theme active" admin notice.
- Remove unused global for Admin Readme class.
- Remove dead code in two post shortcode callback functions.
- Remove unused parameters in `genesis_nav_menu_link_attributes()`.

### Fixed
- Fix heading on the import/export admin page to be `<h1>`.
- Fix Featured Post entry header to display `<header>` wrapper even when only byline is showing.
- Fix typo on SEO settings screen.

## [2.3.1] - 2016-08-02
### Changed
- Optimize `genesis_truncate_phrase()` by returning early if `$max_characters` is falsy.

### Removed
- Remove type hinting in `Genesis_Admin_CPT_Archive_Settings` constructor to prevent fatal error in WordPress 4.6.

## [2.3.0] - 2016-06-15
### Added
- Apply identifying class to entry image link.
- Add a toolbar link to edit custom post type archive settings.
- Add filter for the viewport meta tag value.
- Add shortcodes for site title and home link.
- Add filters for Genesis default theme support items.
- Add ability to specify post ID when using `genesis_custom_field()`.
- Add admin notice when Genesis is activated directly.
- Add a11y to the paginaged post navigation.
- Add relative_depth parameter to date shortcodes.

### Changed
- Allow custom post classes on Ajax requests to account for endless scroll.
- Change "Save Settings" to "Save Changes", as WordPress core does.
- Use version constant rather than database setting for reporting theme version in Settings.
- Use sfHover for superfish hover state.
- Prevent empty footer widgets markup.
- Prevent empty spaces in entry footer of custom post types.
- Trim filtered value of entry meta.
- Update and simplify favicon markup for the modern web.
- Prevent author shortcode from outputting empty markup when no author is assigned.
- Disable author box on entries where post type doesn't support author.
- Change the label on the update setting to reflect what it actually does, check for updates.
- Update theme tags.
- Enable after entry widget area for all post types via post type support.
- Hide layout selector when only one layout is supported.
- Disable author shortcode output if author is not supported by post type.
- Improve image size retrieval function and usage.
- Update to `normalize.css` 4.1.1.
- Use TinyMCE for archive intro text input.
- Allow foreign language characters in content limit functions.
- Pass entry image link through markup API.
- Allow adjacent single entry navigation via post type support.
- Exclude posts page from page selection dropdown in Featured Page widget.

### Removed
- Remove the top buttons (save and reset) from Genesis admin classes.
- Remove right float on admin buttons.
- Remove unnecessary warning from theme description in `style.css`.

### Fixed
- Fix issue with no sitemap when running html5 and no a11y support for 404 page.

## [2.2.7] - 2016-03-04
### Changed
- Limit entry class filters to the front end.

### Removed
- Remove Scribe nag.

### Fixed
- Fix issue with multisite installs where Genesis could technically upgrade before WordPress.
- Fix issue with Genesis using old style term meta method in some places.

## [2.2.6] - 2016-01-05
### Added
- Include and use local html5shiv file, rather than the one hosted at Google Code.

### Changed
- Use CreativeWork as default content type.
- Update Term Meta for WordPress 4.4.

## [2.2.5] - 2015-12-17
_Requires WordPress 4.3.0._
### Fixed
- Fix issue with entries not honoring selected layout.

## [2.2.4] - 2015-12-16
### Changed
- Use form-table style on all Genesis admin areas.
- Make posts page (when static homepage selected) honor selected page layout.
- Make a11y features available only to HTML5 themes.
- Limit markup API filter for nav links to HTML5.
- Allow archive layout selector to be disabled by removing theme support.
- Relative timestamp enhancement.
- Later priority for Genesis entry redirect.

### Removed
- Remove unintended rel="next" code output on archive pages.

### Fixed
- Fix Genesis settings screen styling for WordPress 4.4.

## [2.2.3] - 2015-10-12
### Added
- Add screen reader text to read more link in Featured Page Widget.

### Changed
- Prevent automatic support of all a11y features if no argument is supplied.
- Require explicit 404-page a11y feature.
- Turn on screen-reader-text a11y support if any a11y support is enabled.

### Fixed
- Fix uneven spacing in numeric pagination.
- Fix Featured Post Widget double outputs screen reader text on read more link.
- Fix Read More link not outputting screen reader text when "more" tag is used.
- Fix potential for 2 H1 titles on author archives.
- Fix a11y heading output for primary nav, even if no menu is assigned.
- Fix potential for multiple H1 titles on homepage.
- Fix small bug with screen-reader-text and RTL support.
- Fix double separator character in feed title.

## [2.2.2] - 2015-09-08
### Fixed
- Released to correct corrupted zip from 2.2.1 release.

## [2.2.1] - 2015-09-08
### Added
- Add boolean attribute option to markup API.
- Add H1 to posts page when using static front page and theme supports a11y.
- Add helper function to filter markup to add .screen-reader-text class to markup.

### Changed
- Better logic for generating H1 on front page.
- Prevent duplicate H1 elements on author archives.
- Only output http://schema.org/WebSite on front page.
- Disable http://schema.org/WebSite if SEO plugin is active, to prevent conflicts.
- Pass archive title / description wrappers through markup API.

### Removed
- Remove incorrect usage of mainContentOfPage.
- Remove a11y checks for titles that were previously output by default.

### Fixed
- Fix issue with Schema.org microdata when using Blog template.
- Fix breadcrumb Schema.org microdata for breadcrumb items.

## [2.2.0] - 2015-09-01
### Changed
- Allow child themes to enable accessibility features for web users with disabilities.
- Improvements to the Schema.org microdata Genesis outputs.
- Compatibility with WordPress's generated Title Tag output.
- Compatibility with WordPress's new Site Icon feature.
- Allow entry meta to be turned off on a per post type level.
- Many other improvements and bug fixes.

## [2.1.3] - 2015-08-12
_Requires WordPress 3.8.0._
### Changed
- Prepare taxonomy term meta for mandatory split in WordPress 4.3.

## [2.1.2] - 2014-07-15
### Changed
- Updated the `.pot` file with the new strings.

### Fixed
- Fix untranslatable strings in the Customizer.
- Fix comment author link bug.

## [2.1.1] - 2014-07-01
### Fixed
- Fix secondary navigation ID on XHTML child themes.
- Fix After Entry widget area not checking for theme support.
- Fix Archive Settings menu item not showing for custom post types.
- Fix `sprintf()` warnings in post info and post meta.

## [2.1.0] - 2014-06-30
### Added
- Add Customizer settings.
- Add content archives image alignment option.
- Add centre alignment option to featured widgets.
- Add gallery and caption styles.
- Add Google Web Font Lato weight 400.
- Add admin RTL style sheet.
- Add `genesis_before_while` action hook.
- Add `genesis_user_meta_defaults` filter hook.
- Add $args argument to `genesis_get_image_default_args` filter hook.
- Add `genesis_register_widget_area_defaults` filter hook.
- Add context to post info and post meta areas to allow filtering.
- Add `genesis_get_nav_menu and genesis_nav_menu()` functions.
- Add `post_modified_date` and `post_modified_time shortcodes`.
- Add echo methods to admin class for field name, id and value.
- Add genesis-form class to main wrap on `Genesis_Admin_Form` pages.
- Add gallery and caption HTML5 support.
- Add support for `DISALLOW_FILE_MODS` when displaying update notifications.
- Add `genesis_regster_widget_area()` function.
- Add new widget area with genesis-after-entry-widget-area theme support.
- Add Feedblitz support.
- Add compatibility for WordPress SEO 1.5+ breadcrumb changes.
- Add email address sanitization filter.
- Add more of comment markup through Markup API.
- Add check for `HTTP_USER_AGENT` for feed redirection.
- Add `genesis_is_blog_template()` function.
- Add fresh install detection.
- Add grunt tasks.
- Add some unit tests.
- Add some new hooks documentation.

### Changed
- Improve SEO section title on user settings page.
- Improve term meta fields to only show for public taxonomy.
- Improve header widget area description to list appropriate widgets.
- Improve layout names.
- Improve appearance of inputs on settings pages.
- Improve style header tag fixed-width to responsive-layout.
- Improve (updated) `normalize.css` from 2.1.2 to 3.0.1.
- Improve design for wider screens, largest breakpoint now 1139px to 1200px.
- Improve favicon.
- Improve general design.
- Improve optimisation of images.
- Improve screenshot.
- Improve when `genesis_pre_get_option_-` filter hook fires.
- Improve SEO disabling by amending hooks.
- Improve hook names to use interpolation not concatenation.
- Improve author box to obey semantic headings setting.
- Improve how admin classes autoload scripts, styles and help content.
- Improve `genesis_get_image()` to accept `$post_id`.
- Improve `genesis_save_custom_field()` to formally deprecate `$post_id` argument.
- Improve `_genesis_update_settings()` to make it a public function.
- Improve nav menu registration.
- Improve term-meta callbacks to move them into a more suitable file.
- Improve variables in `genesis_custom_header()`.
- Improve style sheet documentation to use Markdown.
- Improve documentation for globals.

### Removed
- Remove filter for layout columns.
- Remove Primary Nav Extras (for fresh installs).
- Remove unnecessary title attributes.
- Remove Roboto Google Web Font.
- Remove styles for Gravity Forms.
- Remove styles for Genesis Latest Tweets.
- Remove rem units.
- Remove references to admin screen icons.
- Remove (deprecated) `genesis_doctitle_wrap()`.
- Remove `genesis_add_user_profile_fields()` function.
- Remove all uses of `extract()` function.
- Remove global $post in favour of functions where possible.
- Remove last var keyword.
- Remove dead code.

### Fixed
- Fix layout not selectable with IE11.
- Fix empty post titles in featured widgets.
- Fix location of Semantic Headings description.
- Fix SEO user option showing when SEO is disabled.
- Fix default layout for RTL.
- Fix formatting of CSS.
- Fix JavaScript code practices.
- Fix duplicate `.pot` file headers.
- Fix Language Team `.pot` value.
- Fix POEdit keyword list.
- Fix missing text domains.
- Fix `genesis_structural_wrap` filter hook.
- Fix title tags being added to all instances of `wp_title()`.
- Fix more tag on home page loop with Featured Page.
- Fix array to string conversion error from taxonomy meta data.
- Fix multiple calls to update API server.

## [2.0.2] - 2014-01-09
### Added
- Add Lauren Mancke to Contributor List.
- Add Google+ Publisher URL field.

### Changed
- Improve import button user interface consistency.
- Improve copyright shortcode by using non-breaking space between symbol and year.
- Improve pagination setting by using numeric as default.
- Improve search field to use value instead of placeholder when query is present.
- Improve SEO Settings user interface.
- Improve `rel=author` output to only target posts.
- Improve screenshot.
- Improve menu icon.

### Removed
- Remove Homepage Author field.

### Fixed
- Fix incorrect Genesis and child themes updates from WordPress.org.
- Fix radio button appearance in WordPress 3.8 admin.
- Fix metabox textarea widths.
- Fix hidden text box handles.
- Fix admin style references to MP6 plugin.
- Fix `genesis_human_time_diff()`.
- Fix assign by reference Strict Standards warning.
- Fix order of Contributors.

## [2.0.1] - 2013-08-21
_Requires WordPress 3.5.0._
### Changed
- Improve `genesis_get_cpt_archive_types_names()` to always return an array.
- Improve external resources by using relative protocol.
- Improve term meta field names.
- Improve files to consistently use Unix line-endings.

### Removed
- Remove type hint from sanitization filter.

### Fixed
- Fix `post_author_link` shortcode for XHTML themes.
- Fix empty document title for custom post type archive settings usage.
- Fix more tag on home page loop with Featured Post.
- Fix Leave a Comment link when no comments are present.

## 2.0.0 - 2013-08-07
### Added
- Add semantic HTML5 elements across all output.
- Add attributes markup functions `genesis_attr()` and `genesis_parse_attr()`, allowing key elements to have their attributes filtered in.
- Add default microdata that covers itemtypes of WebPage, Blog, SearchResultsPage, WPHeader, WPSideBar, WPFooter, SiteNavigationElement, CreativeWork, BlogPosting, UserComments, and Person, and their corresponding properties.
- Add role attributes to assist with accessibility.
- Add more classes for pagination elements.
- Add HTML5-specific hooks that better match the new semantic structure and be post type agnostic.
- Add HTML5 shiv for Internet Explorer 8 and below.
- Add archive settings for custom post types that are (filterable conditions) public, show a UI, show a menu, have an archive, and support `genesis-cpt-archives-settings`.
- Add contextual help to settings pages, allowing better explanation of settings, and potentially reducing some visual distractions amongst the settings.
- Add distinct admin menu icon, instead of using default favicon.
- Add an unsaved settings alert, when the user is about to navigate away from a settings page after changing a value but not yet saved.
- Add semantic headings setting for using multiple h1 elements on a page.
- Add permalink on posts with no title.
- Add recognition of SEO Ultimate plugin, to enable Genesis SEO to automatically disable.
- Add iframe to CSS to cover responsive video.
- Add new clearfix method for block elements.
- Add `rtl.css` file to automatically display sites set-up as right-to-left language better, and gives theme authors a good starting point.
- Add updated screenshot.
- Add JSLint Closure Compiler instructions to Superfish args non-minified file.
- Add minified JavaScript (`-.min.js`) files that are used by default, unless `SCRIPT_DEBUG` is true.
- Add minified admin style sheet (`-.min.css`) files that are used by default, unless `SCRIPT_DEBUG` is true.
- Add early registration of Superfish files.
- Add header logo files.
- Add `absint` and `safe_html` new settings sanitization types.
- Add sanitization for custom body and post classes.
- Add filter to disable loading of deprecated functions file.
- Add filter to Superfish args URL.
- Add filter to initial layouts.
- Add filters to structural wraps – attributes and output.
- Add ability to wrap markup around output of `genesis_custom_field()`.
- Add two new breadcrumb-related filters, `genesis_build_crumbs` and `genesis_breadcrumb_link`.
- Add `$args` to sidebar defaults filter.
- Add `$footer_widgets` to `genesis_footer_widget_areas` filter.
- Add context arg in `genesis_get_image()` to allow for more control when filtering output.
- Add fallback arg in `genesis_get_image()` to decide what thumbnail to show if a featured image is not set.
- Add array type hints where possible. Methods with the same name in classes extended from WP can't have them, not can methods which accept array or strings arguments.
- Add global displayed IDs variable to track which posts are being shown across any loop.
- Add setting to Featured Post widget to exclude already displayed posts.
- Add third parameter to `shortcode_atts()` to utilize new WordPress 3.6 filter.
- Add network-wide update, to eliminate the need to visit each site to trigger database changes.
- Add blank line at the end of each file for cleaner files and diffs.
- Add some preparatory functions for Theme Customizer (full support not until at least Genesis 2.1)
- Add archive description box markup around search result page heading for consistency.
- Add common class for all archive description boxes.
- Add common class for both Featured widgets.
- Add `widget-title` class next to `widgettitle`.
- Add `lib/functions/breadcrumb.php` for breadcrumb-related functions.

### Changed
- Improve in-post scripts box by moving it to its own box, that won't be hidden when an SEO plugin is active.
- Improve feedback for navigation settings.
- Improve What's New page with new content, and random order of contributors.
- Improve admin styles to work better with MP6 plugin.
- Improve wording for email notification setting.
- Improve labels containing URI to use URL instead.
- Improve widget areas by only showing default content to those who can edit widgets.
- Improve organization of style sheet into a more logical grouping.
- Improve reset styles by switching to `normalize.css`.
- Improve selectors by removing all use of ID selectors in `style.css`, down from 107 in Genesis Framework 1.9.2.
- Improve development speed, by switching to 62.5% (10px) default font-size.
- Improve Google Web Fonts usage by using a protocol-less URL.
- Improve Featured Page and Featured Post widgets to utilize the global `$wp_query` so that `is_main_query()` works correctly against it.
- Improve code that toggles display of extra settings, to allow extra settings to be shown when checkbox is not checked.
- Improve inline settings for Closure Compiler so it uses the latest jQuery externs file (1.8).
- Improve Superfish by updating to the latest version (1.7.4) that supports the version of jQuery that ships with WP 3.6, and has touch event support. Includes back-compat file for arrows support.
- Improve support for languages with multibyte characters by replacing both instances of `substr()` with `mb_substr()`.
- Improve widgets by calling `parent::__construct()` directly when registering widgets.
- Improve output from `get_terms()` by making Genesis term metadata available.
- Improve `genesis_do_noposts()` to be post type agnostic.
- Improve `genesis_do_noposts()` to use consistent entry markup.
- Improve admin metabox abstraction so that it hooks in the previously hard-coded metabox container markup.
- Improve import feature to only import Genesis-related settings.
- Improve multi-page navigation code, by moving it out of post content function into its own hooked in function.
- Improve menus by not showing empty markup if there are no menu items.
- Improve unpaged comment navigation by not showing empty markup.
- Improve filtering of terms, by doing nothing if term variable is not an object.
- Improve `genesis_get_custom_field()` by allowing custom fields to return as arrays.
- Improve checkbox inputs to utilize WP admin styling, by wrapping label element around them.
- Improve the organization of the `lib/structure/header.php` file.
- Improve JavaScript classes, by adding `js-` prefix to them.
- Improve breadcrumbs class to refactor large methods into several smaller ones.
- Improve default sidebar contents by refactoring it into a single re-usable function.
- Improve `genesis_search_form()` escaping and logic.
- Improve check for presence of Header Right sidebar before displaying markup.
- Improve internationalization so that menu location names are translatable, by moving loading of text domain earlier.
- Improve internationalization by simplifying strings.
- Improve README file by changing it from a `.txt` to `.md` file.
- Improve single line comment format to be consistent, allowing easier block-commenting around and from the single line comment.
- Improve overall code by using identity comparisons wherever suitable.
- Improve inline documentation throughout.

### Removed
- Remove display of `entry-footer` for everything except posts.
- Remove loading of Superfish script by default. Can be added back by filtering `genesis_superfish_enabled` to be true, or use Genesis Fancy Dropdowns.
- Remove Microformat classes such as hentry.
- Remove global `$loop_counter` since `$wp_query->current_post` does the same job.
- Remove back to top text.
- Remove custom comment form arguments, resulting in default "Leave your Reply" and "You may use these HTML tags and attributes…" showing.
- Remove Fancy Dropdown settings for each menu in favour of more explicit Load Superfish Script setting.
- Remove the now empty Secondary Navigation settings, and which just leaves Primary Navigation Extras.
- Remove Theme Information setting, since parent and child theme information is publicly available in the style sheets.
- Remove child theme README admin menu item.
- Remove RSS and Twitter images.
- Remove device-specific subheadings.
- Remove support for five-column layout.
- Remove previously deprecated eNews widget. Use Genesis eNews Extended plugin as an enhanced replacement.
- Remove previously deprecated Latest Tweets widget. Use Genesis Latest Tweets plugin, or official Twitter widget.
- Remove ternary part of `genesis.confirm()` JavaScript function.
- Remove (deprecated) `genesis_show_theme_info_in_head()`.
- Remove (deprecated) `genesis_theme_files_to_edit()`.
- Remove (deprecated) `g_ent()`.
- Remove (deprecated) `genesis_tweet_linkify()`.
- Remove (deprecated) `genesis_custom_header_admin_style()`.
- Remove (deprecated) `genesis_older_newer_posts_nav()`.
- Remove `GENESIS_LANGUAGES_URL` constant.
- Remove redundant calls and globals from various functions.
- Remove redundant escaping on in-post meta boxes save.
- Remove post templates functionality. Use Single Post Template plugin as a replacement.
- Remove all remaining style attributes.
- Remove all but two of the remaining inline event handlers (on- attributes). Only `onfocus` and `onblur` remain on the XHTML search form in lieu of no placeholder attribute support.
- Remove closing element HTML comments.
- Remove empty files and a directory.
- Remove the Older Posts / Newer Posts archive pagination format in favour of existing Next Page / Previous Page.

### Fixed
- Fix mis-alignment of settings page boxes.
- Fix inconsistent term meta user interface.
- Fix Closure Compiler output file name for `admin.js`.
- Fix `wp_footer()` so it fires right before `</body>`, now after `genesis_after` hook.
- Fix duplicate IDs on top and bottom submit and reset admin buttons.
- Fix invalid HTML output in user profile widget.
- Fix duplicate calls to `genesis_no_comments_text` filter.
- Fix structural wrap function so support for them can be removed completely.
- Fix incorrectly linked label on noarchive post setting.
- Fix out-of-date Theme and SEO Settings defaults and sanitising.
- Fix redundant parameter in `genesis_save_custom_fields()`.
- Fix breadcrumb issue for date archives.

## 1.9.2 - 2013-04-10
### Fixed
- Fix potential notice when saving post custom fields.
- Fix potential security issue in the search form (props Sucuri Security team and Alun Jones).
- Fix duplicate ID attributes on admin save and reset buttons.
- Fix notice when trying to filter a term that is not an object.
- Fix missing class on layout selector default radio input.
- Fix distorted images in IE8.

## 1.9.1 - 2013-01-08
### Fixed
- Fix loading of child theme main style sheet, so it is referenced before any other extra child theme style sheets.

## 1.9.0 - 2013-01-07
### Added
- Add `.entry` class to all content, in preparation for the potential absence of `.hentry` in a HTML5-flavoured Genesis that prefers Microdata over Microformats.
- Add filter for term meta defaults.
- Add comment header wrapping div.
- Add ability to disable the loading of all breadcrumb features.
- Add `archive-title` class to archive titles.
- Add fallback parameter to `genesis_get_image()`.
- Add a What's New page.
- Add front page and posts page breadcrumb settings.
- Add search result page title.
- Add menu highlight class.
- Add link to download Genesis for Beginners to readme.
- Add support for `rel="author"` link tag, allowing author highlighting on Google result pages.

### Changed
- Improve `genesis_site_layout()` by allowing cache to be bypassed.
- Improve custom field saving function.
- Improve how Genesis / child theme style sheet is referenced, by enqueueing it.
- Improve post title output, adding a filter to decide if it should be linked to the single post on archive pages (default is true, as currently).
- Improve user meta fields integration by limiting to admin back-end only.
- Improve method to check to see if Scribe is installed.
- Improve breadcrumb class for PHP 5.
- Improve comment template by only loading it when needed.
- Improve wording on SEO Settings page, including Scribe marketing notice.
- Improve theme settings page by hiding update options when automatic updates are programatically disabled.
- Improve organization of CSS.
- Improve overall base design:
    - Increased maximum width, 1152px.
    - Different font.
    - Default styles for HTML5 elements.
    - Fluid-width columns.
    - Use of rem units with pixel fallback.
- Improve usage of proper defaults in eNews widget.
- Improve License description by changing from "GPL v2.0 (or later)" to "GPL-2.0+" as per SPDX open source license registry.
- Improve default document title separator from being a hyphen-minus character to an em-dash.
- Improve `.pot` file.

### Removed
- Remove `i18n.php` and moved textdomain load to `init.php`.
- Remove legacy customer header code.
- Remove on / off setting for primary and secondary menus in favour of theme nav menu locations to determine visibility.
- Remove settings for eNews widget (consider it deprecated).
- Remove settings for Latest Tweets widget (consider it deprecated).

### Fixed
- Fix call to `genesis_site_layout()` resetting the query.
- Fix the custom header body class conditional for WP 3.4.
- Fix warnings when saving posts.
- Fix footer scripts setting having incorrect ID.
- Fix extra quote in Author Box setting markup.
- Fix empty post image link, when there is no post image.
- Fix empty featured post / page widget image link, when there is no image to display.
- Fix use of path constants in post-templates to use functions instead.
- Fix comments template loading on custom post type single posts, if it supports comments.
- Fix post class field not saving.
- Fix inconsistency with comments and trackback edit links.
- Fix robots meta tag help links to point to articles by Yoast.
- Fix dropdown size issue in widget forms.
- Fix trackback URL output showing when post type does not support trackbacks.
- Fix post meta section showing for pages in search results page.
- Fix grid loop problems.
- Fix spacing between bottom buttons on settings pages.

### Security
- Improve sanitization on some settings inputs.
- Improve search form security by escaping input and button text outside of filter – you should remove any `esc_attr()` calls in functions that filter these strings and just return plain text.
- Add a new sanitization filter, `url`.
- Add escaping to names and dimensions of image sizes used in image size dropdowns.

## 1.8.2 - 2012-06-20
_Requires WordPress 3.3.0._
### Changed
- Improve user interface by removing Header setting box if WP native custom-header has theme support.

### Fixed
- Fix term meta data from being deleted when quick editing a term.
- Fix warning when showing theme info in the head.
- Fix warnings in theme editor by no longer hiding Genesis Framework files.
- Fix warnings related to custom header by supporting native functionality if WordPress ≥ 3.4.

## 1.8.1 - 2012-04-30
### Security
- This was a security release. Details of what was actually fixed will be revealed when users have had chance to update their Genesis installs (recommended immediately).

## 1.8.0 - 2012-01-20
### Added
- Add new color scheme / style metabox on Theme Settings page which child themes can use instead of building their own.
- Add setting to enable / disable breadcrumbs on attachment pages.
- Add Genesis features to post and page editors via post type support, instead of hard-coding – you can now disable the inpost metaboxes by removing post type support with a single line of code.
- Add separate custom title and description on term archives (displayed content defaults to existing title and description if not customized further).
- Add vendor-prefixed border-radius properties.
- Add posts-link class to user profile widget to accompany the now deprecated `posts_link` class.
- Add extended page link text setting for the user profile widget. No longer hard-coded as `[Read more…]`.
- Add warning to Genesis Page and Category Menu widget descriptions, to gently deprecate them (use WP Custom Menu widget instead).
- Add `Genesis_Admin` classes – a set of 1+3 abstract classes from which all Genesis admin pages now extend from.
- Add `genesis_is_menu_page()` helper function to check we're targeting a specific admin page.
- Add new `genesis_widget_area()` helper function for use in child themes.
- Add `author` value to `rel` attribute for author link shortcode functions.
- Add argument to `genesis_get_option()` and others to not use the Genesis cache.
- Add ability to make nav menu support conditional.
- Add search form label filter, so themes can add a visual label in if they wish.
- Add filter to disable edit post / page link.
- Add filter to Content Archives display types.
- Add filter to the options sent to `wp_remote_post()` when doing an update check.
- Add filter on custom header defaults.
- Add filters for term meta.
- Add filters for previous and next links text.
- Add `genesis_formatting_kses()` to be used as a filter function.
- Add crop parameter to return value of `genesis_get_image_sizes()`.
- Add a complete overhaul of DocBlock documentation at the page-, class-, method- and function-level. See an example of the generated documentation for Genesis 1.8.0. Comment lines now make up over 40% of all lines of code in Genesis 1.8.0, up from 30% in Genesis 1.6, with a significant amount of non-comment code having been added in the meantime as well.

### Changed
- Improve admin labels by reducing conspicuousness (basically, removing "Genesis" from several headings also displayed on wordpress.com installs).
- Improve image dimensions dropdown to use correct multiplication character, not the letter x.
- Improve label relationships with the `for` attribute to make them explicitly linked as per accessibility best practices.
- Improve top buttons to work better with non-English languages.
- Improve metabox order on Theme Settings page.
- Improve specific case CSS for input buttons with more generic selectors.
- Improve styles for new default Genesis appearance, including responsive design.
- Improve classes used for menus to be more consistent with WP, and allow simpler selectors. See [Brian's post](http://www.briangardner.com/genesis-customize-menus/) for more info.
- Improve eNews widget to now pass WP locale to Feedburner, instead of hard-coded `en_US`.
- Improve "Header Right" widget area to display as "Header Left" if right-to-left language is used.
- Improve the image alignment option "None" by giving it a value of alignnone in featured post and page widgets.
- Improve user profile author dropdown to only show actual authors, not all users.
- Improve `admin.js` with a complete rewrite to separate functions from events, make functions re-usable under genesis namespace, switch to using `on()` method for jQuery 1.7.1 and ensure all event bindings are namespaced.
- Improve ability to amend togglable settings by moving the config to PHP where they can be more easily filtered, before sending to JavaScript.
- Improve admin scripts to only appear on the appropriate admin pages.
- Improve submit button markup by using `submit_button()` instead of hard-coding it.
- Improve structural wrap usage.
- Improve `genesis_layout_selector()` by allowing layout options to be shown by type.
- Improve code quality by refactoring widget defaults into the constructor to avoid duplication.
- Improve some functions to return earlier if conditions aren't correct.
- Improve `genesis_strip_attr()` to accept a string for the elements arguments.
- Improve featured post widget performance by sanitizing byline with KSES on save, not output.
- Improve taxonomy term performance by sanitizing description on save, not output.
- Improve `comment_form()` by passing filterable comment form args.
- Improve `genesis_admin_redirect()` by eliminating multiple calls to `add_query_arg()`.
- Improve order of the notice checks to avoid the reset notice still showing after saving settings.
- Improve `genesis_custom_loop()` by refactoring it to use `genesis_standard_loop()`.
- Improve updates procedure by ensuring a fresh request for database options at each incremental stage.
- Improve notice to actually check if settings save was actually sucessfull or not.
- Improve custom post type (custom post type) archive breadcrumb by only linking if custom post type has an archive.
- Improve post date title attribute for hEntry by using HTML5-compatible format.
- Improve `_genesis_update_settings()` by moving it to the correct file.
- Improve code organization by moving general sanitization functions to the sanitization file from theme settings file.
- Improve code organization by moving per-page sanitization code to the related admin page class.
- Improve theme screenshot.
- Improve favicon.
- Improve default footer wording credits.
- Improve readme content with Header Right info.
- Improve `.pot` file with additional and corrected headers and updated to 381 strings in total.
- Improve documentation by moving warning message in top-level files to outside of docblocks so they don't count as short descriptions.
- Improve code so it is now written to WordPress Code Standards, programatically testable via WordPress Code Sniffs.
- Improve translation of strings by extracting `<code>` bits to simplify them and reduce the number of unique strings to translate.

### Removed
- Remove settings form from Genesis Page and Category Menu widgets, to further deprecate them.
- Remove now-deprecated functions from `lib/functions/admin.php` and deprecated file.
- Remove duplicated custom post class handling code.
- Remove (deprecated) `genesis_filter_attachment_image_attributes()` function as WP has since improved.
- Remove `genesis_load_styles()` as it was an empty function that was never used.
- Remove remaining PHP4-compatible class constructor names in favour of `__construct()`.
- Remove unnecessary check for WordPress SEO plugin to re-enable title and description output on term archive pages when WordPress SEO is active.
- Remove SEO options that remove some of the relationship link tags from the head. See [\[18680\]](http://core.trac.wordpress.org/changeset/18680) for more info.

### Fixed
- Fix appearance of layout selector for IE8 users.
- Fix issue with incorrect CSS being output for custom header text color.
- Fix issue with new WP install default widgets appearing in Header Right widget area when switching themes.
- Fix escaping of some values in theme settings.
- Fix rare `add_query_arg()` bug by not passing it an encoded URL.
- Fix issue with duplicate canonical tags in the head when an SEO plugin is active.
- Fix missing second and third parameters when applying the `widget_title filter`.
- Fix empty anchor in `post_author_posts_link` shortcode function.
- Fix clash with grid loop features and features taxonomy (as in AgentPress Listings plugin).
- Fix variable name under which JavaScript strings are localized, from `genesis` to `genesisL10n` to be consistent with WordPress practices.
- Fix license compatibility for child themes by changing license from "GPLv2" to "GPLv2 (or later)".
- Fix missing text-domain for footer widget area description, post author link shortcode, and user profile widget.
- Fix the Scribe notice to be translatable.

## 1.7.1 - 2011-07-18
_Requires WordPress 3.2.0._
### Added
- Add new conditionals to feed filter to ensure compatibility with other code that amend the feed link.

### Changed
- Improve CSS for new default look.

### Fixed
- Fix bug with `__genesis_return_content_sidebar` returning the wrong value.
- Fix tweet text escaping not working as intended, so reverted.

## 1.7.0 - 2011-07-06
### Added
- Add `genesis_human_time_diff()` to use on relative post dates, as a replacement for poor WP function.
- Add `genesis_canonical` filter.
- Add version number to `admin.js` to bust cache when updating Genesis.
- Add database version string to theme info stored in the database.
- Add private function to update database settings more easily.
- Add ability to return array values from database via `genesis_get_option()`.
- Add structural wrap fallback for child themes that do not load `init.php`.
- Add structural wrap support for sidebars.
- Add new layout images and visual selector feature.
- Add link to support forums on Theme Settings page.
- Add `.gallery-caption` and `.bypostauthor` classes (empty) to meet Theme Review guidelines.
- Add updated `.pot` file, now with 385 strings in total.
- Add class and method-level documentation for widget classes.

### Changed
- Improve settings page user interface to match new user interface for WordPress 3.2.
- Improve settings pages to be a single column.
- Improve organization of settings by combining some settings into other meta boxes, removing other meta boxes and conditionally hiding some depending on theme support for features.
- Improve user interface on User Profile page by amending widths of input and textarea fields.
- Improve wording on all admin pages to be clearer.
- Improve wording in notices, and to use WordPress wording where possible.
- Improve naming of layout choices.
- Improve capability check for Genesis pages by changing from `manage_options` to `edit_theme_options`.
- Improve old hook functions by formally deprecating them.
- Improve init to use WordPress function `require_if_theme_supports()` instead of using Genesis conditional.
- Improve widget organization and registration.
- Improve breadcrumbs to remove entry crumbs – allows Home crumb and separator to be remove, for instance.
- Improve README to be formatted for viewing inside WP Dashboard.
- Improve code standards by correcting whitespace and formatting issues in CSS.
- Improve code standards by correcting some whitespace issues in PHP.
- Improve styles for:
    - defaults
    - body
    - header
    - title
    - description
    - menus (including superfish)
    - breadcrumbs
    - headings (all levels)
    - blockquotes
    - inputs
    - ordered lists
    - list items
    - captions
    - taxonomy descriptions
    - images
    - post icons
    - featured images
    - sticky
    - avatars
    - post navigation
    - comments
    - subscribe-to-comments
    - sidebars
    - widgets

### Removed
- Remove "NOTE:" prefix for settings descriptions.
- Remove Header Right theme setting – sidebar now always registered but only shown if it contains a widget.
- Remove `strip_tags()` call on page title in breadcrumbs.
- Remove existing meta box order settings from the database.
- Remove `lib/functions/hooks.php` file as all contents have been moved to `lib/functions/deprecated.php`.

### Fixed
- Fix issue with menu separator having a class.
- Fix issues with post info and post meta not showing up on custom pages.
- Fix issue with feed redirection being too inclusive and breaking other plugins.
- Fix breadcrumb issue which stopped breadcrumbs from being turned off on blog pages for sites with a static front page.
- Fix Genesis to use `genesis_formatting_allowedtags()` instead of the global `$_genesis_formatting_allowedtags`.
- Fix load superfish script if custom menu widget is active.
- Fix Nav Extra posts feed to use RSS2 instead of RSS.
- Fix issue with toggle checkboxes in page / category widget checklist.
- Fix wording in latest tweets, categories menu, pages menu and user profile widgets to be translatable.
- Fix "Theme URL" to be "Theme URI".

### Security
- Security Audit by Mark Jaquith.
- Fix wrong escaping on comment permalink.
- Improve performance and security by sanitizing widget option values on save, instead of on display.
- Add a capability check before displaying Header and Footer scripts meta box.
- Add complete new settings sanitization class and API, aimed at core, extendable to child themes.

## 1.6.1 - 2011-05-02
_Requires WordPress 3.1.0._
### Fixed
- Fix robots meta not outputting unless all meta tags were sent.
- Fix minor CSS issues.

## 1.6.0 - 2011-04-26
### Added
- Add select / deselect all checkbox switch to category menu widget.
- Add plugin detection function.
- Add an edit link to breadcrumbs of all term archive pages.
- Add filter for text shown when comment is awaiting moderation.
- Add filter to sidebar registration defaults.
- Add filters to `genesis_do_nav()` and `genesis_do_subnav()`.
- Add filters for post navigation text.
- Add custom header functionality. Can now be enabled via a single line of code in a child theme.
- Add footer widgets functionality. Can now be enabled via a single line of code in a child theme.
- Add trailing slash to breadcrumb home link.
- Add content width filter for variable layouts.
- Add option to show features on page 2+ with the grid loop.
- Add relative time option to the post date shortcode options – `[post_date format="relative"]`.
- Add inline documentation in multiple files to some locations where it was missing (ongoing – remaining to be done post-1.6 release).
- Add conditional structural wrap system.
- Add `sidebar` class to primary and secondary sidebar divs.
- Add `widget-area` class to widget areas in footer widgets.

### Changed
- Improve Export to use checkboxes instead of dropdown for export options – now filterable to allow themes and plugins to hook in.
- Improve Theme Settings user interface by decluttering and toggling secondary options via JavaScript.
- Improve breadcrumbs settings – now off by default.
- Improve admin pages document title to ensure default is shown.
- Improve headline and intro text fields (taxonomy and user) by moving to their own function so they do not get unhooked when an SEO plugin is active.
- Improve image size dropdown in Theme Settings by making it use `genesis_get_image_sizes()`.
- Improve footer credit wording.
- Improve code to use available WP functions – `is_child_theme()`, `menu_page_url()` and more.
- Improve `init.php` content by putting into hooked functions.
- Improve theme speed by loading admin files on admin pages only.
- Improve the post format image function to harden it.
- Improve `genesis_get_custom_field()` to use $id if available.
- Improve data sent when doing an update check.
- Improve check for third party SEO plugins by using plugin detection function.
- Improve admin styles by moving most inline styles from widgets and admin pages to `admin.css`.
- Improve Genesis `style.css` to new header standard for giving an explicit license.

### Removed
- Remove Genesis Menu options. Existing Genesis menus still supported, but amendments will need to be done by creating and using a WordPress Custom Menu.
- Remove XML demo file from Genesis – kept in with Sample Child Theme.
- Remove (deprecated) `genesis_ie8_js()`.
- Remove (to be formally deprecated next version) the hook functions, in favour of direct `do_action()` calls.
- Remove rogue `li` tag from category menu widget.
- Remove WordPress 3.0 compatibility checks in breadcrumb class.
- Remove redundant use of sidebar IDs in `style.css`.
- Remove admin CSS related to purchase themes menu.

### Fixed
- Fix typo on Import / Export page.
- Fix two bugs in `genesis_admin_redirect()`.
- Fix SEO Settings reset action.
- Fix bug with new installs not pushing all the default SEO settings.
- Fix empty site description outputting redudant markup.
- Fix issue with SEO plugin compatibility.
- Fix notice on categories menu widget.
- Fix footer markup typo.
- Fix bug in title output of featured post / page widgets.
- Fix issue with filter in `genesis_custom_header()` not returning an appropriate value, causing conflicts.
- Fix inline documentation in multiple files – moved docblocks directly above functions so they are correctly associated.
- Fix a lot of code that was inconsistent with coding standards, including whitespace (ongoing).
- Fix list styles on archive pages.
- Fix `sub-sub-menu` issue on non-superfish dropdowns.
- Fix CSS conflict with admin bar.

## 1.5.0 - 2011-02-08

- https://www.studiopress.com/genesis-framework-v15/

## 1.4.1 - 2010-12-10

## 1.4.0 - 2010-11-17

- https://www.studiopress.com/genesis-framework-v14/

## 1.3.1 - 2010-09-15

## 1.3.0 - 2010-08-10

- https://www.studiopress.com/genesis-framework-v13/

## 1.2.1 - 2010-06-23

## 1.2.0 - 2010-06-17

- https://www.studiopress.com/genesis-framework-v12/

## 1.1.3 - 2010-05-04

## 1.1.2 - 2010-04-26

## 1.1.1 - 2010-04-09

- https://www.studiopress.com/genesis-framework-v111/

## 1.1.0 - 2010-03-26

- https://www.studiopress.com/genesis-framework-v11/

## 1.0.0 - 2010-02-01

- https://www.studiopress.com/genesis-framework-v10/

First public release.

[2.8.1]: https://github.com/studiopress/genesis/compare/2.8.0...2.8.1
[2.8.0]: https://github.com/studiopress/genesis/compare/2.7.3...2.8.0
[2.7.3]: https://github.com/studiopress/genesis/compare/2.7.2...2.7.3
[2.7.2]: https://github.com/studiopress/genesis/compare/2.7.1...2.7.2
[2.7.1]: https://github.com/studiopress/genesis/compare/2.7.0...2.7.1
[2.7.0]: https://github.com/studiopress/genesis/compare/2.6.1...2.7.0
[2.6.1]: https://github.com/studiopress/genesis/compare/2.6.0...2.6.1
[2.6.0]: https://github.com/studiopress/genesis/compare/2.5.3...2.6.0
[2.5.3]: https://github.com/studiopress/genesis/compare/2.5.2...2.5.3
[2.5.3]: https://github.com/studiopress/genesis/compare/2.5.2...2.5.3
[2.5.2]: https://github.com/studiopress/genesis/compare/2.5.1...2.5.2
[2.5.1]: https://github.com/studiopress/genesis/compare/2.5.0...2.5.1
[2.5.0]: https://github.com/studiopress/genesis/compare/2.4.2...2.5.0
[2.4.2]: https://github.com/studiopress/genesis/compare/2.4.1...2.4.2
[2.4.1]: https://github.com/studiopress/genesis/compare/2.4.0...2.4.1
[2.4.0]: https://github.com/studiopress/genesis/compare/2.3.1...2.4.0
[2.3.1]: https://github.com/studiopress/genesis/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/studiopress/genesis/compare/2.2.7...2.3.0
[2.2.7]: https://github.com/studiopress/genesis/compare/2.2.6...2.2.7
[2.2.6]: https://github.com/studiopress/genesis/compare/2.2.5...2.2.6
[2.2.5]: https://github.com/studiopress/genesis/compare/2.2.4...2.2.5
[2.2.4]: https://github.com/studiopress/genesis/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/studiopress/genesis/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/studiopress/genesis/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/studiopress/genesis/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/studiopress/genesis/compare/2.1.3...2.2.0
[2.1.3]: https://github.com/studiopress/genesis/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/studiopress/genesis/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/studiopress/genesis/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/studiopress/genesis/compare/2.0.2...2.1.0
[2.0.2]: https://github.com/studiopress/genesis/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/studiopress/genesis/compare/2.0.0...2.0.1
