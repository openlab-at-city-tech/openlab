=== Text Replace ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: text, replace, shortcut, substitution, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.7
Stable tag: 4.0

Replace text with other text. Handy for creating shortcuts to common, lengthy, or frequently changing text/HTML, or for smilies.

== Description ==

This plugin allows you to easily define text or HTML that should be used in your posts in place of words or phrases that are actually present in the posts. This is a handy technique for creating shortcuts to common, lengthy, or frequently changing text/HTML, or for smilies.

Additional features of the plugin controlled both via settings and filters:

* Text replacement can be enabled for comments (it isn't by default)
* Text replacement can be made case insensitive (it is case sensitive by default)
* Text replacement can be limited to doing only one replacement per term, per post (by default, all occurrences of a term are replaced)
* Text replacement can be handled early or late in WordPress's text filtering process (it's early by default)
* Text replacement can be expanded to affect other filters

A few things to keep these things in mind:

* Your best bet with defining shortcuts is to define something that would never otherwise appear in your text. For instance, bookend the shortcut with colons:

`
:wp: => <a href='https://wordpress.org'>WordPress</a>
:aol: => <a href='http://www.aol.com'>America Online, Inc.</a>

`

Otherwise, you risk proper but undesired replacements:

`Hi => Hello`

Would have the effect of changing "His majesty" to "Hellos majesty".

* If you intend to use this plugin to handle smilies, you should probably disable WordPress's default smilie handler on the Writing Settings admin page.

* This plugin is set to filter 'the_content', 'the_excerpt', 'widget_text', and optionally, 'get_comment_text' and 'get_comment_excerpt'. Filters from popular plugins such as Advanced Custom Fields (ACF) and Elementor are also handled by default (see FAQ for specifics). The "More filters" setting can be used to specify additional filters that should be handled by the plugin. The filter 'c2c_text_replace_filters' can also be used to add or modify the list of filters affected.

* Text inside of HTML tags (such as tag names and attributes) will not be matched. So, for example, you can't expect the :mycss: shortcut to work in `<a href="" style=":mycss:">text</a>`

* **SPECIAL CONSIDERATION:** Be aware that the shortcut text that you use in your posts will be stored that way in the database. While calls to display the posts will see the filtered, text-replaced version, anything that operates directly on the database will not see the expanded replacement text. So if you only ever referred to "America Online" as ":aol:" (where `:aol: => <a href='http://www.aol.com'>America Online</a>`), visitors to your site will see the linked, expanded text due to the text replace, but a database search would never turn up a match for "America Online".

* However, a benefit of the replacement text not being saved to the database and instead evaluated when the data is being loaded into a web page is that if the replacement text is modified, all pages making use of the shortcut will henceforth use the updated replacement text.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/text-replace/) | [Plugin Directory Page](https://wordpress.org/plugins/text-replace/) | [GitHub](https://github.com/coffee2code/text-replace/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or install the plugin code inside the plugins directory for your site (typically `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to the `Settings` -> `Text Replace` admin options page and customize the options (notably to define the shortcuts and their replacements)
1. Optional: Configure other plugin options as desired.
1. Use the shortcuts in posts/pages. Shortcuts appearing in existing posts will also be affected by this plugin.


== Frequently Asked Questions ==

= Does this plugin modify the post content before it gets saved to the database? =

No. The plugin filters post content on-the-fly as it is being output or displayed. The data saved to the database is as you typed it.

= Does this plugin modify post content that already exists in the database? =

No. The plugin filters post content on-the-fly as it is being output or displayed. The actual data stored in the database, whether it be pre-existing or new, is never affected by this plugin.

= Will this work for posts I wrote prior to installing this plugin? =

Yes, if they include strings that you've now defined as shortcuts.

= What post fields get handled by this plugin? =

By default, the plugin filters the post content, post excerpt, widget text, and optionally comments and comment excerpts. You can use the "More filters" setting to specify additional filters to be processed for text replacement. You can also programmatically use the 'c2c_text_replace_filters' filter to modify the affected filters (see Developer Documentation section).

= How can I get text replacements to apply for post titles (or something not text-replaced by default)? =

The easiest way would be to add "the_title" (or some other filter's name) as a line in the "More filters" setting. That setting allows any additional specified filters to be processed for text replacement.

You can also programmatically add to the list of filters that get text replacements. See the Developer Documentation section for an example.

= Is the plugin case sensitive? =

By default, yes. There is a setting you can change to make it case insensitive. Or you can use the 'c2c_text_replace_case_sensitive' filter (see Developer Documentation section).

= I use :wp: all the time as a shortcut for WordPress, but when I search posts for the term "WordPress", I don't see posts where I used the shortcut; why not? =

Rest assured search engines will see those posts since they only ever see the posts after the shortcuts have been replaced. However, WordPress's search function searches the database directly, where only the shortcut exists, so WordPress doesn't know about the replacement text you've defined.

= Will all instances of a given term be replaced in a single post? =

By default, yes. There is a setting you can change so that only the first occurrence of the term in the post gets replaced. Or if you are a coder, you can use the 'c2c_text_replace_once' filter (see Developer Documentation section).

= Does this plugin explicitly support any third-party plugins? =

Yes. While this plugin is compatible with many other plugins that modify post and widget text, this plugin has explicit built-in support for Advanced Custom Fields and Elementor, which provide additional content areas. See documentation on the hook 'c2c_text_replace_third_party_filters' for a complete list of default supported third-party filters and how to enable compatibility with other plugins and themes.

If you know the name of the filter provided by a plugin, you can add it to the "More filters" setting to have its value processed for text replacement.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. The admin options page for the plugin, where you define the terms/phrases/shortcuts and their related replacement text


== Developer Documentation ==

Developer documentation can be found in [DEVELOPER-DOCS.md](https://github.com/coffee2code/text-replace/blob/master/DEVELOPER-DOCS.md). That documentation covers the numerous hooks provided by the plugin. Those hooks are listed below to provide an overview of what's available.

* `c2c_text_replace_filters` : Customize what hooks get text replacement applied to them.
* `c2c_text_replace_third_party_filters` : Customize what third-party hooks get text replacement applied to them.
* `c2c_text_replace_filter_priority` : Override the default priority for the 'c2c_text_replace' filter.
* `c2c_text_replace` Customize or override the setting defining all of the text replacement shortcuts and their replacements.
* `c2c_text_replace_comments` : Customize or override the setting indicating if text replacement should be enabled in comments.
* `c2c_text_replace_case_sensitive` : Customize or override the setting indicating if text replacement should be case sensitive.
* `c2c_text_replace_once` : Customize or override the setting indicating if text replacement should be limited to once per term per piece of text being processed regardless of how many times the term appears.


== Changelog ==

= 4.0 (2021-07-04) =
Highlights:

This feature release adds a new setting to allow for user-specified filters to be processed, updates the plugin framework significantly, improves the plugin settings page, extracts developer docs from readme into new DEVELOPER-DOCS.md, restructures unit test files, notes compatibility through WP 5.7, and more.

Details:

* New: Add new setting "More filters" to allow for user-specified filters to be processed for text replacements
* New: Add `get_default_filters()` to return the default core and/or third-party filters processed by the plugin
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
* Change: Allow displayed dropdown values for 'when' setting to be translated
* Change: Improve settings page help text by adding, rephrasing, relocating, and tweaaking some formatting
* Change: Change text_to_replace setting from being a textarea to inline textarea
* Change: Move translation of all parent class strings into main plugin file
* New: Add DEVELOPER-DOCS.md and move hooks documentation into it
* New: Suggest Text Hover plugin as an option for those needing support for a specific subset use case
* Change: Output newlines after block-level tags in settings page
* Change: Omit 'cols' attribute for textarea since it is overridden
* Change: Move 'code' tags out of translatable string for 'when' setting
* Change: Note compatibility through WP 5.7+
* Change: Update copyright date (2021)
* Change: Improve documentation in readme.txt
* Change: Tweak plugin's readme.txt tags
* Change: Sync installation instructions in README.txt with what's in readme.txt
* Change: Remove "A screenshot of" prefix from caption
* Unit tests:
    * Change: Restructure unit test file structure
        * New: Create new subdirectory `tests/phpunit/` to house all files related to PHP unit testing
        * Change: Move `bin/` to `tests/bin/`
        * Change: Move `tests/bootstrap.php` into `tests/phpunit/`
        * Change: In bootstrap, store path to plugin file constant so its value can be used within that file and in test file
        * Change: Move tests from `tests/` to `tests/phpunit/tests/`
        * Change: Remove 'test-' prefix from unit test files
        * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
    * New: Add unit tests for shortcuts adjacent to punction and special characters
    * Change: Output custom error message for known failing tests explaining the issues and why they may not actually be bugs
    * Change: Rename improperly named unit test
* New: Add a few more possible TODO items
* Change: Updated screenshot for settings page

= 3.9.1 (2020-07-11) =
Highlights:

This minor release updates a bunch of documentation, updates a few URLs to be HTTPS, improves unit testing, and notes compatibility through WP 5.4+.

Details:

* Change: Revamp a lot of the help text on the settings page
* Change: Improve and expand upon documentation
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* Change: Add a number of new TODO items
* Unit tests:
    * New: Add test for `options_page_description()`
    * New: Add test for setting name
    * Change: Remove unnecessary unregistering of hooks in `tearDown()`
    * Change: Remove duplicative `reset_options()` call
    * Change: Store plugin instance in test object to simplify referencing it
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)

= 3.9 (2020-01-15) =
Highlights:

This feature release adds support for Advanced Custom Fields and Elementor, adds a new setting that can allow the plugin to run later to avoid potential conflicts with other plugins, adds a number of filters, updates compatibility to be WP 4.9-5.3+, and more.

Details:

* New: Add support for third-party plugins: Advanced Custom Fields, Elementor
* New: Add filter `c2c_text_replace_third_party_filters` for filtering third party filters
* New: Add new setting to allow control over when text replacements are handled early or late in text processing process
* New: Add filter `c2c_text_replace_filter_priority` for filtering hook priority for text replacement handler
* Fix: Ensure the lack of any defined replacements doesn't remove zeroes from text
* Change: Alter handling of `replace_once` value to ensure a valid value is used as arg for `preg_replace()`
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Remove plugin setting page help text indicating order matters (it hasn't since v3.8)
* Change: Update plugin framework to 050
    * 050:
    * Allow a hash entry to literally have '0' as a value without being entirely omitted when saved
    * Output donation markup using `printf()` rather than using string concatenation
    * Update copyright date (2020)
    * Note compatibility through WP 5.3+
    * Drop compatibility with version of WP older than 4.9
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
* New: Add CHANGELOG.md and move all but most recent changelog entries into it
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add more items to the list)
* New: Add inline documentation for hooks
* Unit tests:
    * New: Add `capture_filter_value()` as a method for capturing default values provided for a filter
    * New: Add `get_filter_names()` as a helper method for getting the default and third-party filter names
    * New: Add `unhook_default_filters()` as a helper method to unhook plugin's default filters hooked to `text_replace()`
    * New: Add tests for setting defaults
    * New: Add text_to_replace example values to verify replacement to 0 and an empty string are valid
    * New: Add failing tests for replacements affecting shortcode tags and shortcode attributes (though current behavior may be desired)
    * New: Add failing test for replacement text itself getting a replacement (though current behavior may be desired)
    * New: Add new `test_does_not_replace_within_markup_attributes()`
    * Change: Rename old `test_does_not_replace_within_markup_attributes()` to `test_does_not_replace_within_markup_attributes_but_does_between_tags()`
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Explicitly check hook priority when checking that hook is registered
    * Change: Update some inline docs and function names to reflect their relevance to this plugin (and not to the plugin they were copied from)
    * Fix: Fix unit test function name so that it is treated as a unit test
* Change: Note compatibility through WP 5.3+
* Change: Drop compatibility with version of WP older than 4.9
* Change: Tweak some documentation in readme.txt
* Change: Update copyright date (2020)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two
* Fix: Correct typo in GitHub URL

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/text-replace/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 4.0 =
Recommended release: added new setting to allow user-specified filters to be processed, updated plugin framework significantly, improved plugin settings page, extracted developer docs from readme into new DEVELOPER-DOCS.md, restructured unit test files, noted compatibility through WP 5.7, +more.

= 3.9.1 =
Minor update: updated a bunch of documentation, updated a few URLs to be HTTPS, improved unit testing, and noted compatibility through WP 5.4+.

= 3.9 =
Feature update: added support for Advanced Custom Fields and Elementor, added new setting to allow the plugin to run later to avoid potential conflicts with other plugins, added a number of filters, updated compatibility to be WP 4.9-5.3+, added CHANGELOG.md and TODO.md, and more.

= 3.8 =
Recommended update: fixed to honor 'replace once' setting, including for multibyte strings; allow for whitespace in text to replace to represent any number of whitespace; updated plugin framework to v048; compatibility is now WP 4.7-4.9; added README.md; more.

= 3.7 =
Minor update: improved support for localization; updated plugin framework to 042; verified compatibility through WP 4.5; dropped compatibility with WP older than 4.1; updated copyright date (2016)

= 3.6.1 =
Bugfix release: revert use of __DIR__ constant since it isn't supported on older installations (PHP 5.2)

= 3.6 =
Recommended update: improved support of '&' in text to be replaced; added support for replacing multibyte text; updated plugin framework to version 039; noted compatibility through WP 4.1+; added plugin icon

= 3.5.1 =
Recommended minor bugfix: fix to honor case sensitivity setting when HTML is being replaced

= 3.5 =
Recommended update: fix to allow case insensitivity when HTML is being replaced; added ability to do one replacement per term per post; added unit tests; compatibility now WP 3.6-3.8+

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

= 3.0.2 =
Trivial update: updated plugin framework to v021; noted compatibility with WP 3.1+ and updated copyright date.

= 3.0 =
Significant and recommended update. Highlights: re-implementation; added more settings and hooks for customization; allow replacing HTML; allow case insensitivity; disable autowrap in textarea; misc improvements; verified WP 3.0 compatibility; dropped compatibility with WP older than 2.8.
