# Changelog

## 4.0 _(2021-07-04)_

### Highlights:

This feature release adds a new setting to allow for user-specified filters to be processed, updates the plugin framework significantly, improves the plugin settings page, extracts developer docs from readme into new DEVELOPER-DOCS.md, restructures unit test files, notes compatibility through WP 5.7, and more.

### Details:

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

## 3.9.1 _(2020-07-11)_

### Highlights:

This minor release updates a bunch of documentation, updates a few URLs to be HTTPS, improves unit testing, and notes compatibility through WP 5.4+.

### Details:

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

## 3.9 _(2020-01-15)_

### Highlights:

This feature release adds support for Advanced Custom Fields and Elementor, adds a new setting that can allow the plugin to run later to avoid potential conflicts with other plugins, adds a number of filters, updates compatibility to be WP 4.9-5.3+, and more.

### Details:

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

## 3.8 _(2018-07-14)_

### Highlights:

This release adds a setting for links to open in a new window, adds support for linkable text spanning multiple lines in your post, adds a filter for customizing link attributes, improves performance, and makes numerous behind-the-scenes improvements and changes.

### Details:

* New: Ensure longer, more precise link strings match before shorter strings that might also match, regardless of order defined
* Fix: Honor setting to limit text replacements to just once a post for multibyte strings
* New: Add support for finding text to replace that may span more than one line or whose internal spaces vary in number and type
* Change: Update plugin framework to 048
    * 048:
    * When resetting options, delete the option rather than setting it with default values
    * Prevent double "Settings reset" admin notice upon settings reset
    * 047:
    * Don't save default setting values to database on install
    * Change "Cheatin', huh?" error messages to "Something went wrong.", consistent with WP core
    * Note compatibility through WP 4.9+
    * Drop compatibility with version of WP older than 4.7
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`
    * Note compatibility through WP 4.7+
    * Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database
    * 044:
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes
    * Add `add_option()` to add a new option to the plugin's configuration
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names
    * Change: Refactor `get_option_names()`
* Change: Cast return values of hooks to expected data types
* New: Add README.md
* New: Add GitHub link to readme
* Change: Store setting name in constant
* Unit tests:
    * Change: Improve test initialization
    * Change: Improve tests for settings handling
    * Change: Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Change: Enable more error output for unit tests
    * New: Add more tests
    * New: Add header comments to bootstrap
* Change: Note compatibility through WP 4.9+
* Change: Drop compatibility with version of WP older than 4.7
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Update installation instruction to prefer built-in installer over .zip file
* Change: Update URLs used in examples and docs to be HTTPS where appropriate
* Change: Update copyright date (2018)

## 3.7 _(2016-05-01)_
* Change: Update plugin framework to 043:
    * Fix error message when text replacement field has trailing blank line
    * Change class name to `c2c_TextReplace_Plugin_043` to be plugin-specific
    * Disregard invalid lines supplied as part of hash option value
    * Set textdomain using a string instead of a variable
    * Don't load textdomain from file
    * Change admin page header from 'h2' to 'h1' tag
    * Add `c2c_plugin_version()`
    * Formatting improvements to inline docs
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable
    * Remove .pot file and /lang subdirectory
    * Remove 'Domain Path' from plugin header
* Change: Add many more unit tests
* Change: Prevent web invocation of unit test bootstrap.php
* New: Add LICENSE file
* New: Add empty index.php to prevent files from being listed if web server has enabled directory listings
* Change: Minor code reformatting
* Change: Add proper docblocks to examples in readme.txt
* Change: Note compatibility through WP 4.5+
* Change: Dropped compatibility with version of WP older than 4.1
* Change: Update copyright date (2016)

## 3.6.1 _(2015-02-19)_
* Revert back to using `dirname(__FILE__)`; `__DIR__` is only PHP 5.3+

## 3.6 _(2015-02-18)_
* Improve support of '&' in text to be replaced by recognizing its encoded alternatives ('&amp;', '&#038;') as equivalents
* Support replacing multibyte strings. NOTE: Multibyte strings don't honor limiting their replacement within a piece of text to once
* Update plugin framework to 039
* Add more unit tests
* Explicitly declare `activation()` static
* Cast filtered value of `c2c_text_replace` filter as array
* Reformat plugin header
* Use `__DIR__` instead of `dirname(__FILE__)`
* Change regex delimiter from '|' to '~'
* Change documentation links to wp.org to be https
* Minor documentation spacing changes throughout
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Add plugin icon
* Regenerate .pot

## 3.5.1 _(2014-01-28)_
* Fix logic evaluation to properly honor case_sensitive and replace_once checkbox values

## 3.5 _(2014-01-05)_
* Fix to allow case insensitivity to work when the text being replaced includes HTML
* Add setting to allow limiting text replacement to once per term per text
* Add filter `c2c_text_replace_once`
* Change to just-in-time (rather than on init) determination if comments should be filtered
* Add `text_replace_comment_text()`
* Add unit tests
* Update plugin framework to 037
* Better singleton implementation:
    * Add `get_instance()` static method for returning/creating singleton instance
    * Make static variable 'instance' private
    * Make constructor protected
    * Make class final
    * Additional related changes in plugin framework (protected constructor, erroring `__clone()` and `__wakeup()`)
* Add checks to prevent execution of code if file is directly accessed
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Use explicit path for `require_once()`
* Discontinue use of PHP4-style constructor
* Discontinue use of explicit pass-by-reference for objects
* Remove ending PHP close tag
* Documentation improvements
* Minor code reformatting (spacing)
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.6
* Update copyright date (2014)
* Regenerate .pot
* Change donate link
* Add assets directory to plugin repository checkout
* Update screenshot
* Move screenshot into repo's assets directory
* Add banner

## 3.2.2
* Fix bug where special characters were being double-escaped prior to use in regex
* Update plugin framework to 034
* Minor readme.txt formatting tweaks

## 3.2.1
* Fix bug where `$x` (where x is number) when used in replacement text gets removed on display
* Fix to properly escape shortcut keys prior to internal use in preg_replace()
* Change default for case_sensitive to true
* Add `handle_plugin_upgrade()` to fix logic inversion for case_sensitive setting
* Fix incorrect help text (inverted logic) for case_sensitive setting
* Update plugin framework to 032
* Regenerate .pot

## 3.2
* Fix bug with settings form not appearing in MS
* Update plugin framework to 030
* Remove support for `$c2c_text_replace` global
* Note compatibility through WP 3.3+
* Drop support for versions of WP older than 3.1
* Regenerate .pot
* Add 'Domain Path' directive to top of main plugin file
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

## 3.1.1
* Fix cross-browser (namely IE) handling of non-wrapping textarea text (flat out can't use CSS for it)
* Update plugin framework to version 028
* Change parent constructor invocation
* Create 'lang' subdirectory and move .pot file into it
* Regenerate .pot

## 3.1
* Fix to properly register activation and uninstall hooks
* Update plugin framework to version v023
* Save a static version of itself in class variable $instance
* Deprecate use of global variable `$c2c_text_replace` to store instance
* Add `__construct()` and `activation()`
* Note compatibility through WP 3.2+
* Drop compatibility with version of WP older than 3.0
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

## 3.0.2
* Update plugin framework to version 021
* Delete plugin options upon uninstallation
* Explicitly declare all class functions public static
* Note compatibility through WP 3.1+
* Update copyright date (2011)

## 3.0.1
* Update plugin framework to version 018
* Fix so that textarea displays vertical scrollbar when lines exceed visible textarea space

## 3.0
* Re-implementation by extending `C2C_Plugin_012`, which among other things adds support for:
    * Reset of options to default values
    * Better sanitization of input values
    * Offload of core/basic functionality to generic plugin framework
    * Additional hooks for various stages/places of plugin operation
    * Easier localization support
* Full localization support
* Allow for replacement of tags, not just text wrapped by tags
* Disable auto-wrapping of text in the textarea input field for replacements
* Support localization of strings
* Add option to indicate if text replacement should be case sensitive. Default is true.
* NOTE: The plugin is now by default case sensitive when searching for potential replacements
* For `text_replace()`, remove 'case_sensitive' argument
* Allow filtering of text replacements via `c2c_text_replace` filter
* Allow filtering of hooks that get text replaced via `c2c_text_replace_filters` filter
* Allow filtering/overriding of text_replace_comments option via `c2c_text_replace_comments` filter
* Allow filtering/overriding of case_sensitive option via `c2c_text_replace_case_sensitive` filter
* Filter `widget_text` for text replacement
* Rename class from `TextReplace` to `c2c_TextReplace`
* Assign object instance to global variable, `$c2c_text_replace`, to allow for external manipulation
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Change description
* Update readme
* Minor code reformatting (spacing)
* Add Filters and Upgrade Notice sections to readme.txt
* Add .pot file
* Update screenshot
* Add PHPDoc documentation
* Add package info to top of file
* Update copyright date
* Remove trailing whitespace

## 2.5
* Fixed path-related issue for options page
* Added 'Settings' link for plugin on admin Plugins page
* Changed permission check
* More localization-related work
* Minor code reformatting (spacing)
* Removed hardcoded path
* Updated copyright
* Noted compatibility through 2.8+
* Dropped compatibility with versions of WP older than 2.6

## 2.0
* Handled case where shortcut appears at the very beginning or ending of the text
* Created its own class to encapsulate plugin functionality
* Added an admin options page
* Added option `text_replace_comments` (defaulted to false) to control whether text replacements should occur in comments
* Tweaked description and installation instructions
* Added compatibility note
* Updated copyright date
* Added readme.txt and screenshot
* Tested compatibility with WP 2.3.3 and 2.5

## 1.0
* Moved the array `$text_to_replace` outside of the function and into global space
* Renamed function from `text_replace()` to `c2c_text_replace()`
* Added installation instruction and notes to plugin file
* Verified that plugin works for WordPress v1.2+ and v1.5+

## 0.92
* Added optional argument `$case_sensitive` (defaulted to "false")
* Changed from BSD-new to MIT license

## 0.91
* Removed the need to escape special characters used in the shortcut text. Now "?", "(", ")", "[", "]", etc can be used without problems. However, the backspace character "\" should be avoided.
* Employed a new pattern for matching and replacing text. A huge advantage of this new matching pattern is that it won't match text in a tag (text appearing between "<" and ">").

## 0.9
* Initial release
