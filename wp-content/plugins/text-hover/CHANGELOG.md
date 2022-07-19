# Changelog

## 4.2 _(2022-03-22)_

### Highlights:

This release introduces security hardening to restrict HTML tags that can be used as hover text in fancy tooltips, adds DEVELOPER-DOCS.md, notes compatibility through WP 5.9, and minor settings page and documentation tweaks.

### Details:

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

## 4.1 _(2021-06-29)_

### Highlights:

This feature release adds a new setting to allow for user-specified filters to be processed, updates the plugin framework significantly, improves the plugin settings page, restructures unit test files, notes compatibility through WP 5.7, and more.

### Details:

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

## 4.0 _(2020-07-16)_

### Highlights:

This minor release adds a new setting that can allow the plugin to run later to avoid potential conflicts with other plugins, now allows hover strings to begin or end with punctuation, updates its plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, and updates compatibility to be WP 4.9-5.4+.

### Details:

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

## 3.9.1 _(2020-01-12)_

* Fix: Revert to apply to the `the_excerpt` filter, which was mistakenly changed to `get_the_excerpt`
* Change: Update some inline documentation relating to third-party plugin hook support
* Unit tests:
    * Change: Implement a more generic approach to capture default values provided for a filter
    * New: Add test to verify the lack of any defined hover text doesn't remove zeroes from text
    * Fix: Correct typo in function name used

## 3.9 _(2020-01-08)_

### Highlights:

This minor release adds support for select third-party plugins (Advanced Custom Fields, Elementor), tweaks plugin initialization, fixes a minor bug, updates the plugin framework to 049, notes compatibility through WP 5.3+, creates CHANGELOG.md, and updates copyright date (2020).

### Details:

* New: Add support for third-party plugins: Advanced Custom Fields, Elementor
* New: Add filter `c2c_text_hover_third_party_filters` for filtering third party filters
* Fix: Define `uninstall()` as being `static`
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Update plugin framework to 049
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
* Change: Variablize the qTip2 version and use it when enqueuing its JS and CSS
* New: Add CHANGELOG.md and move all but most recent changelog entries into it
* New: Add inline documentation for hooks
* Unit tests:
     * Change: Update unit test install script and bootstrap to use latest WP unit test repo
     * Change: Explicitly check hook priority when checking that hook is registered
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

## 3.8 _(2018-08-01)_
* New: Ensure longer, more precise strings match before shorter strings that might also match, regardless of order defined
* New: Add support for finding text to hover that may span more than one line or whose internal spaces vary in number and type
* Fix: Prevent hover text from being embedded within other hover text
* Change: Switch for using deprecated 'acronym' tag to using 'abbr'
* Change: Display fancy hover text as white text on a dark gray background
* Change: Cast return values of hooks to expected data types
* Change: Add version number when enqueuing CSS files
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
* Change: Drop compatibility with version of WP older than 4.7.
* Change: Tweak plugin description
* Change: Minor code reformatting
* Change: Add example of better looking tooltip alongside basic tooltip example
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Update installation instruction to prefer built-in installer over .zip file
* Change: Update copyright date (2018)

## 3.7.1 _(2016-06-10)_
* Change: Update qTip2 to v3.0.3.
    * Fixes a JS invalid .min.map file reference.
    * Add plugin IE6 support.
* Change: Update plugin framework to 044.
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`.
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes.
    * Add  `add_option()` to add a new option to the plugin's configuration.
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names.
    * Change: Refactor `get_option_names()`.

## 3.7 _(2016-04-28)_
* New: Allow HTML to be matched for text hovering. Recommended only for non-block level tags.
* New: Allow single replacement (based on setting) for multibyte strings.
* Bugfix: Improve text replacement regex to account for text immediately bounded by HTML tags.
* Change: Update plugin framework to 043:
    * Change class name to `c2c_TextHover_Plugin_043` to be plugin-specific.
    * Disregard invalid lines supplied as part of a hash option value.
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Change admin page header from 'h2' to 'h1' tag.
    * Add `c2c_plugin_version()`.
    * Formatting improvements to inline docs.
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Remove .pot file and /lang subdirectory.
    * Remove 'Domain Path' from plugin header.
* Change: Add many more unit tests.
* Change: Prevent web invocation of unit test bootstrap.php.
* New: Add LICENSE file.
* New: Add empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Minor code reformatting.
* Change: Add proper docblocks to examples in readme.txt.
* Change: Note compatibility through WP 4.5+.
* Change: Dropped compatibility with version of WP older than 4.1.
* Change: Update copyright date (2016).

## 3.6 _(2015-02-19)_
* Improve support of '&' in text to be replaced by recognizing its encoded alternatives ('`&amp;`', '`&#038;`') as equivalents
* Support replacing multibyte strings. NOTE: Multibyte strings don't honor limiting their replacement within a piece of text to once
* Add class of 'c2c-text-hover' to acronym tags added by plugin
* Update packaged qTip2 JS library to v2.2.1
* Limit qTip2 only to acronyms added by the plugin
* Update plugin framework to 039
* Add more unit tests
* Explicitly declare `activation()` static
* Cast filtered value of `c2c_text_hover` filter as array
* Reformat plugin header
* Change regex delimiter from '|' to '~'
* Change documentation links to wp.org to be https
* Minor documentation spacing changes throughout
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Add plugin icon
* Regenerate .pot

## 3.5.1 _(2014-01-28)_
* Fix logic evaluation to properly honor `replace_once` checkbox value
* Minor code reformatting

## 3.5 _(2014-01-05)_
* Add setting to allow limiting text replacement to once per term per text
* Add filter `c2c_text_hover_once`
* Add qTip2 library for better looking hover popups
* Add setting to allow use of prettier tooltips (i.e. the qTip2 library). Default is true.
* Add filter `c2c_text_hover_use_pretty_tooltips`
* Add setting to allow text hover to apply to comments (default is for it not to)
* Add filter `c2c_text_hover_comments`
* Add `text_hover_comment_text()`
* Add preview for tooltips to plugin's settings page
* Add unit tests
* Add file assets/text-hover.js (to enable qTip)
* Add file assets/text-hover.css (to provide default styling for qTip)
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
* Minor documentation improvements
* Minor code reformatting (spacing)
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.6
* Update copyright date (2014)
* Regenerate .pot
* Change donate link
* Add assets directory to plugin repository checkout
* Update screenshots
* Add third screenshot
* Move screenshots into repo's assets directory
* Add banner

## 3.2.2
* Fix bug where special characters were being double-escaped prior to use in regex
* Update plugin framework to 034

## 3.2.1
* Fix bug where `$x` (where x is number) when used in hover text gets removed on display
* Fix to properly escape shortcut keys prior to internal use in `preg_replace()`
* Update plugin framework to 032

## 3.2
* Fix bug with settings form not appearing in MS
* Update plugin framework to 030
* Remove support for `$c2c_text_hover` global
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
* Tweaked description

## 3.1
* Fix to properly register activation and uninstall hooks
* Update plugin framework to version 023
* Save a static version of itself in class variable `$instance`
* Deprecate use of global variable `$c2c_text_hover` to store instance
* Add `__construct()` and `activation()`
* Note compatibility through WP 3.2+
* Drop compatibility with version of WP older than 3.0
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

## 3.0.3
* Update plugin framework to version 021
* Delete plugin options upon uninstallation
* Explicitly declare all class functions public static
* Note compatibility through WP 3.1+
* Update copyright date (2011)

## 3.0.2
* Update plugin framework to version 018
* Fix so that textarea displays vertical scrollbar when lines exceed visible textarea space

## 3.0.1
* Update plugin framework to version 016

## 3.0
* Re-implementation by extending `C2C_Plugin_015`, which among other things adds support for:
    * Reset of options to default values
    * Better sanitization of input values
    * Offload of core/basic functionality to generic plugin framework
    * Additional hooks for various stages/places of plugin operation
    * Easier localization support
* Full localization support
* Disable auto-wrapping of text in the textarea input field for hovers
* Allow filtering of text hover terms and replacement via `c2c_text_hover` filter
* Allow filtering of hooks that get text hover processing via `c2c_text_hover_filters` filter
* Allow filtering/overriding of case_sensitive option via `c2c_text_hover_case_sensitive` filter
* Filter `widget_text` for text hover
* Rename class from `TextHover` to `c2c_TextHover`
* Assign object instance to global variable, `$c2c_text_hover`, to allow for external manipulation
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Update readme.txt
* Minor code reformatting (spacing)
* Add Filters and Upgrade Notice sections to readme.txt
* Note compatibility with WP 3.0+
* Drop support for versions of WordPress older than 2.8
* Add .pot file
* Update screenshot
* Add PHPDoc documentation
* Add package info to top of file
* Update copyright date
* Remove trailing whitespace

## 2.2
* Fixed bug that allowed text within tag attributes to be potentially replaced
* Fixed bug that prevented case sensitivity-related option from being taken into account
* Removed `$case_sensitive` argument from `text_replace()` function since it is controlled by a setting
* Changed pattern matching criteria to allow text-to-be-hovered to be book-ended on either side with single or double quotes (either plain or curly), square brackets, curly braces, or parentheses
* Added ability to filter text hover shortcuts via `c2c_text_hover_option_text_to_hover`
* Changed the number of rows for textarea input from 5 to 15
* Changed plugin_basename to be a class variable initialized during constructor
* Removed use of single-use temp variable (and instead directly used the value it was holding)
* Minor code reformatting (mostly spacing)

## 2.1
* (Privately released betas previewing features released as part of v2.2)

## 2.0
* Encapsulated all functionality into its own class
* Added 'Settings' link to plugin's plugin listing entry
* Noted compatibility with WP2.8+
* Dropped support for pre-WP2.6
* Updated screenshots
* Updated copyright date

## 1.0
* Initial release