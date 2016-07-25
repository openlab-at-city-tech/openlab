=== Text Replace ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: text, replace, shortcut, shortcuts, post, post content, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.1
Tested up to: 4.5
Stable tag: 3.7

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

* List the more specific matches early, to avoid stomping on another of your shortcuts. For example, if you have both
`:p` and `:pout:` as shortcuts, put `:pout:` first, otherwise, the `:p` will match against all the `:pout:` in your text.

* If you intend to use this plugin to handle smilies, you should probably disable WordPress's default smilie handler.

* This plugin is set to filter the_content, the_excerpt, widget_text, and optionally, get_comment_text and get_comment_excerpt. The filter 'c2c_text_replace_filters' can be used to add or modify the list of filters affected.

* Text inside of HTML tags (such as tag names and attributes) will not be matched. So, for example, you can't expect the :mycss: shortcut to work in: &lt;a href="" :mycss:&gt;text&lt;/a&gt;.'.

* **SPECIAL CONSIDERATION:** Be aware that the shortcut text that you use in your posts will be stored that way in the database (naturally). While calls to display the posts will see the filtered, text replaced version, anything that operates directly on the database will not see the expanded replacement text. So if you only ever referred to "America Online" as ":aol:" (where ":aol:" => "<a href='http://www.aol.com'>America Online</a>"), visitors to your site will see the linked, expanded text due to the text replace, but a database search would never turn up a match for "America Online".

* However, a benefit of the replacement text not being saved to the database and instead evaluated when the data is being loaded into a web page is that if the replacement text is modified, all pages making use of the shortcut will henceforth use the updated replacement text.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/text-replace/) | [Plugin Directory Page](https://wordpress.org/plugins/text-replace/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Unzip `text-replace.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. (optional) Go to the `Settings` -> `Text Replace` admin options page and customize the options (notably to define the shortcuts and their replacements).


== Frequently Asked Questions ==

= Does this plugin modify the post content in the database? =

No. The plugin filters post content on-the-fly.

= Will this work for posts I wrote prior to installing this plugin? =

Yes, if they include strings that you've now defined as shortcuts.

= What post fields get handled by this plugin? =

By default, the plugin filters the post content, post excerpt fields, widget text, and optionally comments and comment excerpts. You can use the 'c2c_text_replace_filters' filter to modify that behavior (see Filters section).

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

By default, yes. There is a setting you can change to make it case insensitive. Or you can use the 'c2c_text_replace_case_sensitive' filter (see Filters section).

= I use :wp: all the time as a shortcut for WordPress, but when I search posts for the term "WordPress", I don't see posts where I used the shortcut; why not? =

Rest assured search engines will see those posts since they only ever see the posts after the shortcuts have been replaced. However, WordPress's search function searches the database directly, where only the shortcut exists, so WordPress doesn't know about the replacement text you've defined.

= Will all instances of a given term be replaced in a single post? =

By default, yes. There is a setting you can change so that only the first occurrence of the term in the post gets replaced. Or if you are a coder, you can use the 'c2c_text_replace_once' filter (see Filters section).

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the admin options page for the plugin, where you define the terms/phrases/shortcuts and their related replacement text


== Filters ==

The plugin exposes five filters for hooking. Typically, the code to utilize these hooks would go inside your active theme's functions.php file. Bear in mind that all of the features controlled by these filters are configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.

= c2c_text_replace_filters (filter) =

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

= c2c_text_replace_comments (filter) =

The 'c2c_text_replace_comments' hook allows you to customize or override the setting indicating if text replacement should be enabled in comments.

Arguments:

* $state (bool): Either true or false indicating if text replacement is enabled for comments. This will be the value set via the plugin's settings page.

Example:

`// Prevent text replacement from ever being enabled.
add_filter( 'c2c_text_replace_comments', '__return_false' );`

= c2c_text_replace (filter) =

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

= c2c_text_replace_comments (filter) =

The 'c2c_text_replace_comments' hook allows you to customize or override the setting indicating if text replacement should be enabled in comments.

Arguments:

* $state (bool): Either true or false indicating if text replacement is enabled for comments. The default value will be the value set via the plugin's settings page.

Example:

`// Prevent text replacements from ever being enabled in comments.
add_filter( 'c2c_text_replace_comments', '__return_false' );`

= c2c_text_replace_case_sensitive (filter) =

The 'c2c_text_replace_case_sensitive' hook allows you to customize or override the setting indicating if text replacement should be case sensitive.

Arguments:

* $state (bool): Either true or false indicating if text replacement is case sensitive. This will be the value set via the plugin's settings page.

Example:

`// Prevent text replacement from ever being case sensitive.
add_filter( 'c2c_text_replace_case_sensitive', '__return_false' );`

= c2c_text_replace_once (filter) =

The 'c2c_text_replace_once' hook allows you to customize or override the setting indicating if text replacement should be limited to once per term per piece of text being processed regardless of how many times the term appears.

Arguments:

* $state (bool): Either true or false indicating if text replacement is to only occur once per term. The default value will be the value set via the plugin's settings page.

Example:

`// Only replace a term/shortcut once per post.
add_filter( 'c2c_text_replace_once', '__return_true' );`


== Changelog ==

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

= 3.6.1 (2015-02-19) =
* Revert back to using `dirname(__FILE__)`; __DIR__ is only PHP 5.3+

= 3.6 (2015-02-18) =
* Improve support of '&' in text to be replaced by recognizing its encoded alternatives ('&amp;', '&#038;') as equivalents
* Support replacing multibyte strings. NOTE: Multibyte strings don't honor limiting their replacement within a piece of text to once
* Update plugin framework to 039
* Add more unit tests
* Explicitly declare `activation()` static
* Cast filtered value of 'c2c_text_replace' filter as array
* Reformat plugin header
* Use __DIR__ instead of `dirname(__FILE__)`
* Change regex delimiter from '|' to '~'
* Change documentation links to wp.org to be https
* Minor documentation spacing changes throughout
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Add plugin icon
* Regenerate .pot

= 3.5.1 (2014-01-28) =
* Fix logic evaluation to properly honor case_sensitive and replace_once checkbox values

= 3.5 (2014-01-05) =
* Fix to allow case insensitivity to work when the text being replaced includes HTML
* Add setting to allow limiting text replacement to once per term per text
* Add filter 'c2c_text_replace_once'
* Change to just-in-time (rather than on init) determination if comments should be filtered
* Add text_replace_comment_text()
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
* Use explicit path for require_once()
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

= 3.2.2 =
* Fix bug where special characters were being double-escaped prior to use in regex
* Update plugin framework to 034
* Minor readme.txt formatting tweaks

= 3.2.1 =
* Fix bug where $x (where x is number) when used in replacement text gets removed on display
* Fix to properly escape shortcut keys prior to internal use in preg_replace()
* Change default for case_sensitive to true
* Add handle_plugin_upgrade() to fix logic inversion for case_sensitive setting
* Fix incorrect help text (inverted logic) for case_sensitive setting
* Update plugin framework to 032
* Regenerate .pot

= 3.2 =
* Fix bug with settings form not appearing in MS
* Update plugin framework to 030
* Remove support for 'c2c_text_replace' global
* Note compatibility through WP 3.3+
* Drop support for versions of WP older than 3.1
* Regenerate .pot
* Add 'Domain Path' directive to top of main plugin file
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

= 3.1.1 =
* Fix cross-browser (namely IE) handling of non-wrapping textarea text (flat out can't use CSS for it)
* Update plugin framework to version 028
* Change parent constructor invocation
* Create 'lang' subdirectory and move .pot file into it
* Regenerate .pot

= 3.1 =
* Fix to properly register activation and uninstall hooks
* Update plugin framework to version v023
* Save a static version of itself in class variable $instance
* Deprecate use of global variable $c2c_text_replace to store instance
* Add __construct() and activation()
* Note compatibility through WP 3.2+
* Drop compatibility with version of WP older than 3.0
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

= 3.0.2 =
* Update plugin framework to version 021
* Delete plugin options upon uninstallation
* Explicitly declare all class functions public static
* Note compatibility through WP 3.1+
* Update copyright date (2011)

= 3.0.1 =
* Update plugin framework to version 018
* Fix so that textarea displays vertical scrollbar when lines exceed visible textarea space

= 3.0 =
* Re-implementation by extending C2C_Plugin_012, which among other things adds support for:
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
* For text_replace(), remove 'case_sensitive' argument
* Allow filtering of text replacements via 'c2c_text_replace' filter
* Allow filtering of hooks that get text replaced via 'c2c_text_replace_filters' filter
* Allow filtering/overriding of text_replace_comments option via 'c2c_text_replace_comments' filter
* Allow filtering/overriding of case_sensitive option via 'c2c_text_replace_case_sensitive' filter
* Filter 'widget_text' for text replacement
* Rename class from 'TextReplace' to 'c2c_TextReplace'
* Assign object instance to global variable, $c2c_text_replace, to allow for external manipulation
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

= 2.5 =
* Fixed path-related issue for options page
* Added 'Settings' link for plugin on admin Plugins page
* Changed permission check
* More localization-related work
* Minor code reformatting (spacing)
* Removed hardcoded path
* Updated copyright
* Noted compatibility through 2.8+
* Dropped compatibility with versions of WP older than 2.6

= 2.0 =
* Handled case where shortcut appears at the very beginning or ending of the text
* Created its own class to encapsulate plugin functionality
* Added an admin options page
* Added option text_replace_comments (defaulted to false) to control whether text replacements should occur in comments
* Tweaked description and installation instructions
* Added compatibility note
* Updated copyright date
* Added readme.txt and screenshot
* Tested compatibility with WP 2.3.3 and 2.5

= 1.0 =
* Moved the array $text_to_replace outside of the function and into global space
* Renamed function from text_replace() to c2c_text_replace()
* Added installation instruction and notes to plugin file
* Verified that plugin works for WordPress v1.2+ and v1.5+

= 0.92 =
* Added optional argument $case_sensitive (defaulted to "false")
* Changed from BSD-new to MIT license

= 0.91 =
* Removed the need to escape special characters used in the shortcut text. Now "?", "(", ")", "[", "]", etc can be used without problems. However, the backspace character "\" should be avoided.
* Employed a new pattern for matching and replacing text. A huge advantage of this new matching pattern is that it won't match text in a tag (text appearing between "<" and ">").

= 0.9 =
* Initial release


== Upgrade Notice ==

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
