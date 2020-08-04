=== Text Replace ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: text, replace, shortcut, shortcuts, post, post content, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.3
Stable tag: 3.9

Replace text with other text. Handy for creating shortcuts to common, lengthy, or frequently changing text/HTML, or for smilies.

== Description ==

This plugin allows you to easily define text or HTML that should be used in your posts in place of words or phrases that are actually present in the posts. This is a handy technique for creating shortcuts to common, lengthy, or frequently changing text/HTML, or for smilies.

Additional features of the plugin controlled both via settings and filters:

* Text replacement can be enabled for comments (it isn't by default)
* Text replacement can be made case insensitive (it is case sensitive by default)
* Text replacement can be limited to doing only one replacement per term, per post (by default, all occurrences of a term are replaced)

A few things to keep these things in mind:

* Your best bet with defining shortcuts is to define something that would never otherwise appear in your text. For instance, bookend the shortcut with colons:

`
:wp: => <a href='https://wordpress.org'>WordPress</a>
:aol: => <a href='http://www.aol.com'>America Online, Inc.</a>

`

Otherwise, you risk proper but undesired replacements:

`Hi => Hello`

Would have the effect of changing "His majesty" to "Hellos majesty".

* If you intend to use this plugin to handle smilies, you should probably disable WordPress's default smilie handler.

* This plugin is set to filter the_content, the_excerpt, widget_text, and optionally, get_comment_text and get_comment_excerpt. The filter 'c2c_text_replace_filters' can be used to add or modify the list of filters affected.

* Text inside of HTML tags (such as tag names and attributes) will not be matched. So, for example, you can't expect the :mycss: shortcut to work in: &lt;a href="" :mycss:&gt;text&lt;/a&gt;.'.

* **SPECIAL CONSIDERATION:** Be aware that the shortcut text that you use in your posts will be stored that way in the database (naturally). While calls to display the posts will see the filtered, text replaced version, anything that operates directly on the database will not see the expanded replacement text. So if you only ever referred to "America Online" as ":aol:" (where ":aol:" => "<a href='http://www.aol.com'>America Online</a>"), visitors to your site will see the linked, expanded text due to the text replace, but a database search would never turn up a match for "America Online".

* However, a benefit of the replacement text not being saved to the database and instead evaluated when the data is being loaded into a web page is that if the replacement text is modified, all pages making use of the shortcut will henceforth use the updated replacement text.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/text-replace/) | [Plugin Directory Page](https://wordpress.org/plugins/text-replace/) | [GitHub](https://github.com/coffee2code/text-replace/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or download and unzip `text-replace.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to the `Settings` -> `Text Replace` admin options page and customize the options (notably to define the shortcuts and their replacements)


== Frequently Asked Questions ==

= Does this plugin modify the post content in the database? =

No. The plugin filters post content on-the-fly.

= Will this work for posts I wrote prior to installing this plugin? =

Yes, if they include strings that you've now defined as shortcuts.

= What post fields get handled by this plugin? =

By default, the plugin filters the post content, post excerpt fields, widget text, and optionally comments and comment excerpts. You can use the 'c2c_text_replace_filters' filter to modify that behavior (see Hooks section).

= How can I get text replacements to apply for post titles (or something not text-replaced by default)? =

You can add to the list of filters that get text replacements using something like this (added to your theme's functions.php file, for instance):

`
function more_text_replacements( $filters ) {
	$filters[] = 'the_title'; // Here you could put in the name of any filter you want
	return $filters;
}
add_filter( 'c2c_text_replace_filters', 'more_text_replacements' );
`

= Is the plugin case sensitive? =

By default, yes. There is a setting you can change to make it case insensitive. Or you can use the 'c2c_text_replace_case_sensitive' filter (see Hooks section).

= I use :wp: all the time as a shortcut for WordPress, but when I search posts for the term "WordPress", I don't see posts where I used the shortcut; why not? =

Rest assured search engines will see those posts since they only ever see the posts after the shortcuts have been replaced. However, WordPress's search function searches the database directly, where only the shortcut exists, so WordPress doesn't know about the replacement text you've defined.

= Will all instances of a given term be replaced in a single post? =

By default, yes. There is a setting you can change so that only the first occurrence of the term in the post gets replaced. Or if you are a coder, you can use the 'c2c_text_replace_once' filter (see Hooks section).

= Does this plugin explicitly support any third-party plugins? =

Yes. While this plugin is compatible with many other plugins that modify post and widget text, this plugin has explicit built-in support for Advanced Custom Fields and Elementor, which provide additional content areas. This plugin provides hooks that can be used to enable compatibility with other plugins and themes.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the admin options page for the plugin, where you define the terms/phrases/shortcuts and their related replacement text


== Hooks ==

The plugin exposes a number of filters for hooking. Typically, the code to utilize these hooks would go inside your active theme's functions.php file. Bear in mind that all of the features controlled by these filters are configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.

**c2c_text_replace_filters (filter)**

The 'c2c_text_replace_filters' hook allows you to customize what hooks get text replacement applied to them.

Arguments:

* $hooks (array): Array of hooks that will be text replaced.

Example:

`
/**
 * Enable text replacement for post/page titles.
 *
 * @param array $filters Filters handled by the Text Replace plugin.
 * @return array
 */
function more_text_replacements( $filters ) {
	$filters[] = 'the_title'; // Here you could put in the name of any filter you want
	return $filters;
}
add_filter( 'c2c_text_replace_filters', 'more_text_replacements' );
`

**c2c_text_replace_third_party_filters (filter)**

The 'c2c_text_replace_third_party_filters' hook allows you to customize what third-party hooks get text replacement applied to them. Note: the results of this filter are then passed through the `c2c_text_replace_filters` filter, so third-party filters can be modified using either hook.

Arguments:

* $filters (array): The third-party filters whose text should have text replacement applied. Default `array( 'acf/format_value/type=text', 'acf/format_value/type=textarea', 'acf/format_value/type=url', 'acf_the_content', 'elementor/frontend/the_content', 'elementor/widget/render_content' )`.

Example:

`
/**
 * Stop text replacements for ACF text fields and add text replacements for a custom filter.
 *
 * @param array $filters
 * @return array
 */
function my_c2c_text_replace_third_party_filters( $filters ) {
	// Remove a filter already in the list.
	unset( $filters[ 'acf/format_value/type=text' ] );
	// Add a filter to the list.
	$filters[] = 'my_plugin_filter';
	return $filters;
}
add_filter( 'c2c_text_replace_third_party_filters', 'my_c2c_text_replace_third_party_filters' );
`

**c2c_text_replace_filter_priority (filter)**

The 'c2c_text_replace_filter_priority' hook allows you to override the default priority for the 'c2c_text_replace' filter.

Arguments:

* $priority (int): The priority for the 'c2c_text_replace' filter. The default value is 2.
* $filter (string): The filter name.

Example:

`
/**
 * Change the default priority of the 'c2c_text_replace' filter to run after most other plugins.
 *
 * @param int $priority The priority for the 'c2c_text_replace' filter.
 * @return int
 */
function my_change_priority_for_c2c_text_replace( $priority, $filter ) {
	return 1000;
}
add_filter( 'c2c_text_replace_filter_priority', 'my_change_priority_for_c2c_text_replace', 10, 2 );
`

**c2c_text_replace (filter)**

The 'c2c_text_replace' hook allows you to customize or override the setting defining all of the text replacement shortcuts and their replacements.

Arguments:

* $text_replacement_array (array): Array of text replacement shortcuts and their replacements. This will be the value set via the plugin's settings page.

Example:

`
/**
 * Add dynamic shortcuts.
 *
 * @param array $replacements Array of replacement terms and their replacement text.
 * @return array
 */
function my_text_replacements( $replacements ) {
	// Add replacement
	$replacements[':matt:'] => 'Matt Mullenweg';
	// Unset a replacement that we never want defined
	if ( isset( $replacements[':wp:'] ) )
		unset( $replacements[':wp:'] );
	// Important!
	return $replacements;
}
add_filter( 'c2c_text_replace', 'my_text_replacements' );
`

**c2c_text_replace_comments (filter)**

The 'c2c_text_replace_comments' hook allows you to customize or override the setting indicating if text replacement should be enabled in comments.

Arguments:

* $state (bool): Either true or false indicating if text replacement is enabled for comments. The default value will be the value set via the plugin's settings page.

Example:

`// Prevent text replacements from ever being enabled in comments.
add_filter( 'c2c_text_replace_comments', '__return_false' );`

**c2c_text_replace_case_sensitive (filter)**

The 'c2c_text_replace_case_sensitive' hook allows you to customize or override the setting indicating if text replacement should be case sensitive.

Arguments:

* $state (bool): Either true or false indicating if text replacement is case sensitive. This will be the value set via the plugin's settings page.

Example:

`// Prevent text replacement from ever being case sensitive.
add_filter( 'c2c_text_replace_case_sensitive', '__return_false' );`

**c2c_text_replace_once (filter)**

The 'c2c_text_replace_once' hook allows you to customize or override the setting indicating if text replacement should be limited to once per term per piece of text being processed regardless of how many times the term appears.

Arguments:

* $state (bool): Either true or false indicating if text replacement is to only occur once per term. The default value will be the value set via the plugin's settings page.

Example:

`// Only replace a term/shortcut once per post.
add_filter( 'c2c_text_replace_once', '__return_true' );`


== Changelog ==

= 3.9 (2020-01-15) =
Highlights:
* This feature release adds support for Advanced Custom Fields and Elementor, adds a new setting that can allow the plugin to run later to avoid potential conflicts with other plugins, adds a number of filters, updates compatibility to be WP 4.9-5.3+, and more.

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

= 3.8 (2018-07-14) =
Highlights:

* This release adds a setting for links to open in a new window, adds support for linkable text spanning multiple lines in your post, adds a filter for customizing link attributes, improves performance, and makes numerous behind-the-scenes improvements and changes.

Details:
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
* Change: Drop compatibility with version of WP older than 4.7.
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Update installation instruction to prefer built-in installer over .zip file
* Change: Update URLs used in examples and docs to be HTTPS where appropriate
* Change: Update copyright date (2018)

= 3.7 (2016-05-01) =
* Change: Update plugin framework to 043:
    * Fix error message when text replacement field has trailing blank line.
    * Change class name to c2c_TextReplace_Plugin_043 to be plugin-specific.
    * Disregard invalid lines supplied as part of hash option value.
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

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/text-replace/blob/master/CHANGELOG.md)._



== Upgrade Notice ==

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
