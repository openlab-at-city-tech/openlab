=== Text Hover ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: tooltips, abbreviations, terms, acronyms, help, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.9
Stable tag: 4.2

Add hover text (aka tooltips) to content in posts. Handy for providing explanations of names, terms, phrases, abbreviations, and acronyms.


== Description ==

This plugin allows you to easily define help text that appears when a visitor hovers their mouse over a word or phrase in a post or page.

Via the plugin's settings, simply specify the words or phrases that you want to be associated with hover text, and of course, the desired hover texts themselves. The format is quite simple; an example of which is shown here:

`WP => WordPress
Matt => Matt Mullenweg
The Scooby Shack => the bar where the gang hangs out`

Additional features of the plugin controlled both via settings and filters:

* Hover text can be enabled for comments (it isn't by default)
* Hover text can be made case insensitive (it is case sensitive by default)
* Hover text can be limited to doing only one replacement per term, per post (by default, all occurrences of a term are given hovertext)
* Hover text can be rendered using the default browser tooltip (by default, the better-looking <a href="http://qtip2.com/">qTip2</a> library is used)
* Hover text can be expanded to affect other filters

**Note:** This is not the same as my [Text Replace](https://wordpress.org/plugins/text-replace) plugin, which defines terms or phrases that you want replaced by replacement text when displayed on your site. Text Hover instead adds the hover text as additional information for when visitors hover over the term, which is otherwise displayed in the post as you typed it.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/text-hover/) | [Plugin Directory Page](https://wordpress.org/plugins/text-hover/) | [GitHub](https://github.com/coffee2code/text-hover/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or download and unzip `text-hover.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to the `Settings` -> `Text Hover` admin settings page and customize the settings (namely to define the terms/abbreviations and their explanations).
1. Optional: Configure other plugin settings as desired.
1. Use the terms/abbreviations in posts and/or pages (terms/abbreviations appearing in existing posts will also be affected by this plugin)


== Screenshots ==

1. The admin options page for the plugin, where you define the terms/acronyms/phrases and their related hover text
2. The plugin in action for a post when the mouse is hovering over a defined hover text term using the pretty tooltips
3. The plugin in action for a post when the mouse is hovering over a defined hover text term using default browser tooltips (in this case, Chrome on OSX)


== Frequently Asked Questions ==

= In my posts, hover text terms do not appear any differently than regular text (though I can hover over them and see the hover text)! What gives? =

The plugin currently makes use of the standard HTML tag `abbr` to specify the terms and their hover text. Browsers have default handling and display of `abbr`. It's possibly that the CSS for your theme is overriding the default display. I use the following in my site's styles.css file to ensure it displays for me in the manner I prefer (which, by the same token, you can use more CSS formatting to further format the hover terms) :

`
abbr {
	text-decoration: underline dotted #000;
}
`

= Does this plugin modify the post content in the database? =

No. The plugin filters post content on-the-fly.

= Will this work for posts I wrote prior to installing this plugin? =

Yes, if they include strings that you've now defined as terms.

= What post fields get handled by this plugin? =

By default, the plugin filters the post content, post excerpt fields, widget text, and optionally comments and comment excerpts. You can use the 'c2c_text_hover_filters' filter to modify that behavior (see Hooks section).

= How can I get text hover to apply for post titles (or something not processed for text hover by default)? =

The easiest way would be to add "the_title" (or some other filter's name) as a line in the "More filters" setting. That setting allows any additional specified filters to be processed for text hovers.

You can also programmatically add to the list of filters that get processed for text hover terms. See the Hooks section for an example.

= Is the plugin case sensitive? =

By default, yes. There is a setting you can change to make it case insensitive. Or you can use the 'c2c_text_hover_case_sensitive' filter (see Hooks section). Note that the option applies to all terms/abbreviations. If you want to selectively have terms/acronyms be case insensitive, you should leave the case sensitive setting checked and add a listing for each case variation you wish to support.

= Will all instances of a given term be hovered in a single post? =

By default, yes. There is a setting you can change so that only the first occurrence of the term in the post gets hovered. Or if you are a coder, you can use the 'c2c_text_hover_replace_once' filter (see Hooks section).

= Can I style the tooltip? =

Yes, but only if you have the pretty tooltips enabled (via settings or the filter). The class you want to style in your custom CSS is '.text-hover-qtip'.

= Does this plugin explicitly support any third-party plugins? =

Yes. While this plugin is compatible with many other plugins that modify post and widget text, this plugin has explicit built-in support for Advanced Custom Fields and Elementor, which provide additional content areas. This plugin provides hooks that can be used to enable compatibility with other plugins and themes.

If you know the name of the filter provided by a plugin, you can add it to the "More filters" setting to have its value processed for text hover.

= Why can't I find or access the plugin's settings page even though the plugin is activated? =

The plugin's settings page is found at "Settings" -> "Text Hover" in the admin sidebar menu.

In order to see that link in the menu and to access the plugin's settings page to configure the plugin, you must be logged in as an administrator. More specifically, you must be a user with the 'manage_options' and 'unfiltered_html' capabilities, which by default are capabilities of the 'administrator' role. If you have a custom role, or your administrator role has been customized, such that both capabilities are not assigned to you, then you cannot configure the plugin.

= Does this plugin include unit tests? =

Yes.


== Developer Documentation ==

Developer documentation can be found in [DEVELOPER-DOCS.md](https://github.com/coffee2code/text-hover/blob/master/DEVELOPER-DOCS.md). That documentation covers the numerous hooks provided by the plugin. Those hooks are listed below to provide an overview of what's available.

* `c2c_text_hover_filters` : Customize what hooks get text hover applied to them.
* `c2c_text_hover_third_party_filters` : Customize what third-party hooks get text hover applied to them.
* `c2c_text_hover_filter_priority` : Override the default priority for the `c2c_text_hover` filter.
* `c2c_text_hover` Customize or override the setting defining all of the text hover terms and their hover texts.
* `c2c_text_hover_comments` : Customize or override the setting indicating if text hover should be enabled in comments.
* `c2c_text_hover_case_sensitive` : Customize or override the setting indicating if text hover should be case sensitive.
* `c2c_text_hover_once` : Customize or override the setting indicating if text hovering should be limited to once per term per piece of text being processed regardless of how many times the term appears.
* `c2c_text_hover_use_pretty_tooltips` : Customize or override the setting indicating if prettier tooltips should be used.


== Changelog ==

= 4.2 (2022-03-22) =
Highlights:

This release introduces security hardening to restrict HTML tags that can be used as hover text in fancy tooltips, adds DEVELOPER-DOCS.md, notes compatibility through WP 5.9, and minor settings page and documentation tweaks.

Details:

* Change: Disallow all but the most basic formatting markup within hover text. Props Rohan Chaudhari.
    * As continues to be the case, markup only ever works in the better looking tooltips.
    * This only enforces the already documented limited markup support to basic formatting tags.
    * Existing text hovers will be unaffected until the next time settings get saved.
* New: Add DEVELOPER-DOCS.md and move hooks documentation into it
* Change: Remove settings page helptext about 'replace_once' setting not applying to multibyte strings since it's no longer true
* Change: Lowercase the displayed values for 'when' setting
* Change: Move 'code' tags out of translatable string for 'when' setting
* Change: Note compatibility through WP 5.9+
* Change: Remove "A screenshot of" prefix from all screenshot captions
* Change: Tweak installation instructions in README.md
* Change: Fix typo in function docblock
* Change: Update copyright date (2022)
* New: Add a few more possible TODO items

= 4.1 (2021-06-29) =
Highlights:

This feature release adds a new setting to allow for user-specified filters to be processed, updates the plugin framework significantly, improves the plugin settings page, restructures unit test files, notes compatibility through WP 5.7, and more.

Details:

* New: Add new setting "More filters" to allow for user-specified filters to be processed
* Change: Update plugin framework to 064
    * 064:
    * New: For checkbox settings, support a 'more_help' config option for defining help text to appear below checkbox and its label
    * Fix: Fix URL for plugin listing donate link
    * Change: Store donation URL as object variable
    * Change: Update strings used for settings page donation link
    * 063:
    * Fix: Simplify settings initialization to prevent conflicts with other plugins
    * Change: Remove ability to detect plugin settings page before current screen is set, as it is no longer needed
    * Change: Enqueue thickbox during `'admin_enqueue_scripts'` action instead of during `'init'`
    * Change: Use `is_plugin_admin_page()` in `help_tabs()` instead of reproducing its functionality
    * Change: Trigger a debugging warning if `is_plugin_admin_page()` is used before `'admin_init'` action is fired
    * 062:
    * Change: Update `is_plugin_admin_page()` to use `get_current_screen()` when available
    * Change: Actually prevent object cloning and unserialization by throwing an error
    * Change: Check that there is a current screen before attempting to access its property
    * Change: Remove 'type' attribute from `style` tag
    * Change: Incorporate commonly defined styling for inline_textarea
    * 061:
    * Fix bug preventing settings from getting saved
    * 060:
    * Rename class from `c2c_{PluginName}_Plugin_051` to `c2c_Plugin_060`
    * Move string translation handling into inheriting class making the plugin framework code plugin-agnostic
        * Add abstract function `get_c2c_string()` as a getter for translated strings
        * Replace all existing string usage with calls to `get_c2c_string()`
    * Handle WordPress's deprecation of the use of the term "whitelist"
        * Change: Rename `whitelist_options()` to `allowed_options()`
        * Change: Use `add_allowed_options()` instead of deprecated `add_option_whitelist()` for WP 5.5+
        * Change: Hook `allowed_options` filter instead of deprecated `whitelist_options` for WP 5.5+
    * New: Add initial unit tests (currently just covering `is_wp_version_cmp()` and `get_c2c_string()`)
    * Add `is_wp_version_cmp()` as a utility to compare current WP version against a given WP version
    * Refactor `contextual_help()` to be easier to read, and correct function docblocks
    * Don't translate urlencoded donation email body text
    * Add inline comments for translators to clarify purpose of placeholders
    * Change PHP package name (make it singular)
    * Tweak inline function description
    * Note compatibility through WP 5.7+
    * Update copyright date (2021)
    * 051:
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
* New: Add `get_default_filters()` as getter for core filters, third-party filters, or both
* Change: Allow displayed dropdown values for 'when' setting to be translated
* Change: Improve settings page help text by adding, rephrasing, relocating, and tweaaking some formatting
* Change: Change text_to_hover setting from being a textarea to inline textarea
* Change: Move translation of all parent class strings into main plugin file
* Change: Output newlines after block-level tags in settings page
* Change: Omit 'cols' attribute for textarea since it is overridden
* Change: Note compatibility through WP 5.7+
* Change: Update copyright date (2021)
* Change: Change plugin's short description
* Change: Tweak plugin's readme.txt tags
* Change: Sync installation instructions in README.txt with what's in readme.txt
* Fix: Use correct textdomain for a string translation
* Unit tests:
    * Change: Restructure unit test directories and files into `tests/` top-level directory
        * Change: Move `bin/` into `tests/`
        * Change: Move `tests/bootstrap.php` into `tests/phpunit/`
        * Change: In bootstrap, store path to plugin file constant so its value can be used within that file and in test file
        * Change: Move `tests/*.php` into `tests/phpunit/tests/`
        * Change: Remove 'test-' prefix from unit test files
        * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
    * New: Add additional punctuation-related test cases
    * New: Add helper function `get_core_filters()` and `get_3rd_party_filters()` to DRY up data reuse
* New: Add a few more possible TODO items
* Change: Updated screenshot for settings page

= 4.0 (2020-07-16) =
Highlights:

This minor release adds a new setting that can allow the plugin to run later to avoid potential conflicts with other plugins, now allows hover strings to begin or end with punctuation, updates its plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, and updates compatibility to be WP 4.9-5.4+.

Details:

* New: Add new setting to allow control over when text hovers are handled early or late in text processing process
* New: Add filter `c2c_text_hover_filter_priority` for filtering hook priority for text hover handler
* New: Allow text to hover string to begin and/or end in punctuation.
* New: Add TODO.md and move existing TODO list from top of main plugin file into it
* Change: Update plugin framework to 050
    * 050:
    * Allow a hash entry to literally have '0' as a value without being entirely omitted when saved
    * Output donation markup using `printf()` rather than using string concatenation
    * Update copyright date (2020)
    * Note compatibility through WP 5.4+
    * Drop compatibility with versions of WP older than 4.9
* Change: Remove plugin setting page help text indicating order matters (it hasn't since v3.8)
* Change: Note compatibility through WP 5.4+
* Change: Drop compatibility with versions of WP older than 4.9
* Change: Update links to coffee2code.com to be HTTPS
* Unit tests:
    * New: Add `get_filter_names()` as a helper method for getting the default and third-party filter names
    * New: Add `unhook_default_filters()` as a helper method to unhook plugin's default filters hooked to `text_hover()`
    * New: Add test case for hover text that includes HTML
    * New: Add tests for `enqueue_scripts()`, `options_page_description()`
    * New: Add test for setting name
    * New: Add tests for setting defaults
    * New: Add explicit tests to ensure falsey hover text values don't alter original text
    * New: Add explicit tests to ensure text replacements don't occur within `abbr` tag contents or in any tag attributes
    * Change: Store plugin instance in test object to simplify referencing it
    * Change: Remove unnecessary unregistering of hooks in `tearDown()`
    * Change: Add `$priority` argument to `test_hover_applies_to_default_filters()`
    * Change: Remove duplicative `reset_options()` call
    * Change: Rename unit test function so that it is treated as a unit test
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)
* Change: Update screenshot

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/text-hover/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 4.2 =
Recommended hardening release: restricted HTML tags that can be used as hover text in fancy tooltips, added DEVELOPER-DOCS.md, noted compatibility through WP 5.9, and minor settings page and documentation tweaks.

= 4.1 =
Recommended feature release: added new setting to allow for user-specified filters to be processed, updated plugin framework significantly, improved plugin settings page, restructured unit test files, noted compatibility through WP 5.7, and more.

= 4.0 =
Minor release: added setting to allow plugin to run later to avoid potential conflicts with other plugins, allowed hover strings to begin/end in punctuation, updated plugin framework, added TODO.md, updated some URLs to be HTTPS, expanded unit testing, and updated compatibility to be WP 4.9-5.4+.

= 3.9.1 =
Minor bugfix release: restored hooking of WP's `the_excerpt` filter instead of `get_the_excerpt`, corrected some inline documentation, and made minor improvements to unit tests.

= 3.9 =
Recommended update: added support for select third-party plugins (Advanced Custom Fields, Elementor), tweaked plugin initialization, minor bugfix, updated plugin framework to 049, noted compatibility through WP 5.3+, created CHANGELOG.md, and updated copyright date (2020)

= 3.8 =
Major update: changed default appearance of better-looking tooltip; switched to using `abbr` tag instead of `acronym` tag; misc improvements; updated plugin framework to 048; verified compatibility through WP 4.9; dropped compatibility with WP older than 4.7; updated copyright date (2018)

= 3.7.1 =
Minor bugfix release: updated qTip2 library, which fixes a JavaScript error it had; updated plugin framework to 044.

= 3.7 =
Recommended update: added support for single replacement of multibyte strings; added support for replacing HTML; improved support for localization; verified compatibility through WP 4.5; dropped compatibility with WP older than 4.1; updated copyright date (2016)

= 3.6 =
Recommended update: improved support of '&' in text to be replaced; added support for replacing multibyte text; added more unit tests; updated plugin framework to version 039; noted compatibility through WP 4.1+; added plugin icon

= 3.5.1 =
Recommended minor bugfix: fix to honor replace_once checkbox value

= 3.5 =
Major update: added qTip library for better looking hover popups; added ability to do one hover text per term per post; added ability to enable hover text in comments; added unit tests; compatibility now WP 3.6-3.8+

= 3.2.2 =
Minor bugfix release: fixed bug where special characters were being double-escaped; updated plugin framework.

= 3.2.1 =
Recommended bugfix release: fixed bug when $x (where x is a number) would not display when used in replacement string; fix to properly escape shortcut keys prior to internal use; and updated plugin framework.

= 3.2 =
Recommended update. Highlights: fixed bug with settings not appearing in MS; updated plugin framework; noted compatibility with WP 3.3+; dropped compatibility with versions of WP older than 3.1.

= 3.1.1 =
Bugfix release: fixed bug with cross-browser (mainly, IE) handling of non-wrapping textarea text; updated plugin framework; regenerated .pot file and put it into 'lang' subdirectory.

= 3.1 =
Recommended update. Highlights: updated compatibility through WP 3.2; dropped compatibility with version of WP older than 3.0; updated plugin framework, bugfix; and more.

= 3.0.3 =
Trivial update: updated plugin framework to v021; noted compatibility with WP 3.1+ and updated copyright date.

= 3.0.2 =
Minor plugin framework update and fix so that plugin form's textarea displays vertical scrollbar when lines exceed visible textarea space

= 3.0 =
Significant and recommended update. Highlights: re-implementation; added more settings and hooks for customization; disable autowrap in textarea; misc improvements; verified WP 3.0 compatibility; dropped compatibility with WP older than 2.8.
