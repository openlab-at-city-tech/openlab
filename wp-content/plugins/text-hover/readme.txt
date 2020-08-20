=== Text Hover ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: text, post content, abbreviations, terms, acronyms, hover, help, tooltips, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.3
Stable tag: 3.9.1

Add hover text to regular text in posts. Handy for providing explanations of names, terms, phrases, abbreviations, and acronyms mentioned in posts/pages.


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

**Note:** This is not the same as my [Text Replace](https://wordpress.org/plugins/text-replace) plugin, which defines terms or phrases that you want replaced by replacement text when displayed on your site. Text Hover instead adds the hover text as additional information for when visitors hover over the term, which is otherwise displayed in the post as you typed it.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/text-hover/) | [Plugin Directory Page](https://wordpress.org/plugins/text-hover/) | [GitHub](https://github.com/coffee2code/text-hover/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or download and unzip `text-hover.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to the `Settings` -> `Text Hover` admin settings page and customize the settings (namely to define the terms/abbreviations and their explanations).
1. Use the terms/abbreviations in posts and/or pages (terms/abbreviations appearing in existing posts will also be affected by this plugin)


== Screenshots ==

1. A screenshot of the admin options page for the plugin, where you define the terms/acronyms/phrases and their related hover text
2. A screenshot of the plugin in action for a post when the mouse is hovering over a defined hover text term using the pretty tooltips
3. A screenshot of the plugin in action for a post when the mouse is hovering over a defined hover text term using default browser tooltips (in this case, Chrome on OSX)


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

You can add to the list of filters that get processed for text hover terms. See the Hooks section for an example.

= Is the plugin case sensitive? =

By default, yes. There is a setting you can change to make it case insensitive. Or you can use the 'c2c_text_hover_case_sensitive' filter (see Hooks section). Note that the option applies to all terms/abbreviations. If you want to selectively have terms/acronyms be case insensitive, you should leave the case sensitive setting checked and add a listing for each case variation you wish to support.

= Will all instances of a given term be hovered in a single post? =

By default, yes. There is a setting you can change so that only the first occurrence of the term in the post gets hovered. Or if you are a coder, you can use the 'c2c_text_hover_replace_once' filter (see Hooks section).

= Can I style the tooltip? =

Yes, but only if you have the pretty tooltips enabled (via settings or the filter). The class you want to style in your custom CSS is '.text-hover-qtip'.

= Does this plugin explicitly support any third-party plugins? =

Yes. While this plugin is compatible with many other plugins that modify post and widget text, this plugin has explicit built-in support for Advanced Custom Fields and Elementor, which provide additional content areas. This plugin provides hooks that can be used to enable compatibility with other plugins and themes.

= Does this plugin include unit tests? =

Yes.


== Hooks ==

The plugin exposes a number of filters for hooking. Typically, the code to utilize these hooks would go inside your active theme's functions.php file. Bear in mind that all of the features controlled by these filters are configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.

**c2c_text_hover_filters (filter)**

The 'c2c_text_hover_filters' hook allows you to customize what hooks get text hover applied to them.

Arguments:

* $hooks (array): Array of hooks that will be text hovered.

Example:

`
/**
 * Enable text hover for post/page titles.
 *
 * @param array $filters Filters handled by the Text Hover plugin.
 * @return array
 */
function more_text_hovers( $filters ) {
	$filters[] = 'the_title'; // Here you could put in the name of any filter you want
	return $filters;
}
add_filter( 'c2c_text_hover_filters', 'more_text_hovers' );
`

**c2c_text_hover_third_party_filters (filter)**

The 'c2c_text_hover_third_party_filters' hook allows you to customize what third-party hooks get text hover applied to them. Note: the results of this filter are then passed through the `c2c_text_hover_filters` filter, so third-party filters can be modified using either hook.

Arguments:

* $filters (array): The third-party filters whose text should have text hover applied. Default `array( 'acf/format_value/type=text', 'acf/format_value/type=textarea', 'acf/format_value/type=url', 'acf_the_content', 'elementor/frontend/the_content', 'elementor/widget/render_content' )`.

Example:

`
/**
 * Stop text hovers for ACF text fields and add text hovers for a custom filter.
 *
 * @param array $filters
 * @return array
 */
function my_c2c_text_hover_third_party_filters( $filters ) {
	// Remove a filter already in the list.
	unset( $filters[ 'acf/format_value/type=text' ] );
	// Add a filter to the list.
	$filters[] = 'my_plugin_filter';
	return $filters;
}
add_filter( 'c2c_text_hover_third_party_filters', 'my_c2c_text_hover_third_party_filters' );
`

**c2c_text_hover (filter)**

The 'c2c_text_hover' hook allows you to customize or override the setting defining all of the text hover terms and their hover texts.

Arguments:

* $text_hover_array (array): Array of text hover terms and their hover texts. This will be the value set via the plugin's settings page.

Example:

`
/**
 * Add dynamic text hover.
 *
 * @param array $text_hover_array Array of all text hover terms and their hover texts.
 * @return array
 */
function my_text_hovers( $text_hover_array ) {
	// Add new term and hover text
	$text_hover_array['Matt'] => 'Matt Mullenweg';
	// Unset a term that we never want hover texted
	if ( isset( $text_hover_array['Drupal'] ) )
		unset( $text_hover_array['Drupal'] );
	// Important!
	return $text_hover_array;
}
add_filter( 'c2c_text_hover', 'my_text_hovers' );
`

**c2c_text_hover_text_comments (filter)**

The 'c2c_text_hover_text_comments' hook allows you to customize or override the setting indicating if text linkification should be enabled in comments.

Arguments:

* $state (bool): Either true or false indicating if text linkification is enabled for comments. The default value will be the value set via the plugin's settings page.

Example:

`// Prevent text linkification from ever being enabled in comments.
add_filter( 'c2c_linkify_text_comments', '__return_false' );`

**c2c_text_hover_case_sensitive (filter)**

The 'c2c_text_hover_case_sensitive' hook allows you to customize or override the setting indicating if text hover should be case sensitive.

Arguments:

* $state (bool): Either true or false indicating if text hover is case sensitive. This will be the value set via the plugin's settings page.

Example:

`// Prevent text hover from ever being case sensitive.
add_filter( 'c2c_text_hover_case_sensitive', '__return_false' );`

**c2c_text_hover_once (filter)**

The 'c2c_text_hover_once' hook allows you to customize or override the setting indicating if text hovering should be limited to once per term per piece of text being processed regardless of how many times the term appears.

Arguments:

* $state (bool): Either true or false indicating if text hovering is to only occur once per term. The default value will be the value set via the plugin's settings page.

Example:

`// Only show hovertext for a term/shortcut once per post.
add_filter( 'c2c_text_hover_once', '__return_true' );`

**c2c_text_hover_use_pretty_tooltips (filter)**

The 'c2c_text_hover_use_pretty_tooltips' hook allows you to customize or override the setting indicating if text hovering should use prettier tooltips to display the hover text. If false, the browser's default tooltips will be used.

Arguments:

* $state (bool): Either true or false indicating if prettier tooltips should be used. The default value will be the value set via the plugin's settings page.

Example:

`// Prevent pretty tooltips from being used.
add_filter( 'c2c_text_hover_use_pretty_tooltips', '__return_false' );`


== Changelog ==

= 3.9.1 (2020-01-12) =
* Fix: Revert to apply to the `the_excerpt` filter, which was mistakenly changed to `get_the_excerpt`
* Change: Update some inline documentation relating to third-party plugin hook support
* Unit tests:
    * Change: Implement a more generic approach to capture default values provided for a filter
    * New: Add test to verify the lack of any defined hover text doesn't remove zeroes from text
    * Fix: Correct typo in function name used

= 3.9 (2020-01-08) =
Highlights:

* This minor release adds support for select third-party plugins (Advanced Custom Fields, Elementor), tweaks plugin initialization, fixes a minor bug, updates the plugin framework to 049, notes compatibility through WP 5.3+, creates CHANGELOG.md, and updates copyright date (2020).

Details:

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

= 3.8 (2018-08-01) =
* New: Ensure longer, more precise link strings match before shorter strings that might also match, regardless of order defined
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

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/text-hover/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

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
