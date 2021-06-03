=== Advanced Excerpt ===
Contributors: fancythemes, WPKube
Tags: post excerpt, excerpt, post, content, formatting
Requires at least: 3.2
Tested up to: 5.7
Stable tag: 4.3.9
License: GPLv3

Control the appearance of WordPress post excerpts

== Description ==

This plugin adds several improvements to WordPress' default way of creating excerpts.

1. Keeps HTML markup in the excerpt (and you get to choose which tags are included)
2. Trims the excerpt to a given length using either character count or word count
3. Only the 'real' text is counted (HTML is ignored but kept)
4. Customizes the excerpt length and the ellipsis character that are used
5. Completes the last word or sentence in an excerpt (no weird cuts)
6. Adds a *read-more* link to the text
7. Ignores custom excerpts and use the generated one instead
8. Theme developers can use `the_advanced_excerpt()` for even more control (see the FAQ)

Most of the above features are optional and/or can be customized by the user or theme developer.

Banner image credit - [chillihead](https://www.flickr.com/photos/chillihead/)

Original plugin author - [basvd](http://profiles.wordpress.org/basvd)

**Useful Resources**

- <a href="https://fancythemes.com/what-is-wordpress/" rel="friend">What is WordPress</a>
- <a href="https://fancythemes.com/wordpress-themes" rel="friend">Fee Themes</a> and <a href="https://fancythemes.com/wordpress-plugins/" rel="friend">plugins</a>

== Installation ==

1. Use WordPress' built-in installer
2. Access the "Excerpt" menu option under Settings

== Frequently Asked Questions ==

= What's an excerpt? =

A short version of a post that is usually displayed wherever the whole post would be too much (eg. search results, news feeds, archives). You can write them yourself, but if you don't, WordPress will make a very basic one instead.

= Why do I need this plugin? =

The default excerpt created by WordPress removes all HTML. If your theme uses `the_excerpt()` or `the_content()` to view excerpts, they might look weird because of this (smilies are removed, lists are flattened, etc.) This plugin fixes that and also gives you more control over excerpts.

= Does it work for WordPress version x.x.x? =

During development, the plugin is tested with the most recent version(s) of WordPress. It might work on older versions, but it's better to just keep your installation up-to-date.

= Is this plugin available in my language? =

Advanced Excerpt is internationalization (i18n) friendly.

= Does this plugin support multibyte characters, such as Chinese? =

Before 4.1, multibyte characters were supported directly by this plugin. This feature has been removed because it added irrelevant code for a 'problem' that isn't actually specific to the plugin.

If you require multibyte character support on your website, you can [override the default text operations](http://www.php.net/manual/en/mbstring.overload.php) in PHP.

= Can I manually call the filter in my WP theme or plugin? =

The plugin automatically hooks on `the_excerpt()` and `the_content()` functions and uses the parameters specified in the options panel.

If you want to call the filter with different options, you can use `the_advanced_excerpt()` template tag provided by this plugin. This tag accepts [query-string-style parameters](http://codex.wordpress.org/Template_Tags/How_to_Pass_Tag_Parameters#Tags_with_query-string-style_parameters) (theme developers will be familiar with this notation).

The following parameters can be set:

* `length`, an integer that determines the length of the excerpt
* `length_type`, enumeration, if set to `words` the excerpt length will be in words; if set to `characters` the excerpt length will be in characters
* `no_custom`, if set to `1`, an excerpt will be generated even if the post has a custom excerpt; if set to `0`, the custom excerpt will be used
* `no_shortcode`, if set to `1`, shortcodes are removed from the excerpt; if set to `0`, shortcodes will be parsed
* `finish`, enumeration, if set to `exact` the excerpt will be the exact lenth as defined by the "Excerpt Length" option. If set to `word` the last word in the excerpt will be completed. If set to `sentence` the last sentence in the excerpt will be completed.
* `ellipsis`, the string that will substitute the omitted part of the post; if you want to use HTML entities in the string, use `%26` instead of the `&` prefix to avoid breaking the query
* `read_more`, the text used in the read-more link
* `add_link`, if set to `1`, the read-more link will be appended; if `0`, no link will be added
* `allowed_tags`, a comma-separated list of HTML tags that are allowed in the excerpt. Entering `_all` will preserve all tags.
* `exclude_tags`, a comma-separated list of HTML tags that must be removed from the excerpt. Using this setting in combination with `allowed_tags` makes no sense

A custom advanced excerpt call could look like this:

`the_advanced_excerpt('length=320&length_type=words&no_custom=1&ellipsis=%26hellip;&exclude_tags=img,p,strong');`

= Does this plugin work outside the Loop? =

No, this plugin fetches the post from The Loop and there is currently no way to pass a post ID or any custom input to it.
However, you can [start The Loop manually](http://codex.wordpress.org/The_Loop#Multiple_Loops) and apply the plugin as usual.

== Screenshots ==

1. The options page
2. An example of an excerpt generated by the plugin

== Changelog ==
= 4.3.9 (April 13th, 2021)  =
* New - Option to enable adding the "read more link" for custom excerpts as well

= 4.3.8 (December 28th, 2020) =
* New - Multilanguage compatibility for the "read more" text. The shortcode [advanced_excerpt_text] is used. Example [advanced_excerpt_text lang="en_US"]English[/advanced_excerpt_text][advanced_excerpt_text lang="de_DE"]German[/advanced_excerpt_text]

= 4.3.7 ( July 10th, 2020 ) =
* New - Option to link the whole excerpt to the single post page
* Fix - Issue with "sentence finish" when there is a number with decimal points
* Fix - Updated POT file for translations

= 4.3.6 ( June 1st, 2020 ) =
* Fix - Issue with undefined index PHP notice

= 4.3.5 ( February 6th, 2020 ) =
* Fix - Issue when excerpt ends in a foreign character

= 4.3.4 ( December 17th, 2019 ) =
* Fix - Sentence finish option not working properly if . ? or ! are followed by another character such as " for example

= 4.3.3 ( October 5th, 2019 ) =
* Fix - Conflict with "WPTouch" plugin
* Fix - Conflict with "bbPress" plugin

= 4.3.2 ( September 24th, 2019 ) =
* Fix - Conflict with "The Events Calendar" plugin

= 4.3.1 ( June 21st, 2019 ) =
* New - Option to disable the functionality for WooCommerce products

= 4.3.0 ( June 14th, 2019 ) =
* Fix - Conflict with Sassy Social Share plugin

= 4.2.9 ( May 27th, 2019) =
* New - Filter for developers to change the read more text. The filter is advanced_excerpt_read_more_text
* Tweak - The read more text option now accept shortcodes

= 4.2.8 =
* New - Screen reader text is now optional, can be enabled in the options
* New - Added option to remove the "read more link" if the full post content is shown
* New - Title can be included in the "read more link" by adding {title} to the option

= 4.2.7 =
* New - Option to open "read more" in a new tab
* New - Added screen reader text for "read more" link

= 4.2.6 =
* New "Finish" option that counts exact characters including blank spaces.

= 4.2.5 =
* Fixed the "remove shortcodes from excerpt" functionality

= 4.2.4 =
* Compatibility tested for latest version of WordPress ( 4.9.6 )
* Minor tweaks

= 4.2.3 =
* Fix: The "Remove all tags except the following" wasn't excluding tags as expected
* Fix: Call `remove_all_filter()` on the `the_excerpt` hook to improve excerpt rendering
* Fix: Only honor the "Only filter `the_content()` when there's no break (&lt;!--more--&gt;) tag in the post content" setting when hooking into `the_content` filter
* Improvement: Improve backwards compatibility by reverting back to using `get_the_content()` for the base excerpt text
* Improvement: Added the `advanced_excerpt_skip_excerpt_filtering` filter allowing users to skip excerpt filtering on a per excerpt basis

= 4.2.2 =
* Fix: The `the_advanced_excerpt()` function was not working on singular page types (pages / posts)

= 4.2.1 =
* Fix: Undefined index errors when using the `the_advanced_excerpt()` function
* Fix: Not excluding tags when using the `exclude_tags` argument in the `the_advanced_excerpt()` function 

= 4.2 =
* Feature: Toggle excerpt filtering when there's no break (&lt;!--more--&gt;) tag in the post content
* Feature: Toggle excerpt filtering for the `the_excerpt()` and `the_content()` functions
* Feature: Toggle excerpt filtering on certain page types
* Improvement: Added HTML5 tags to the allowed tags list
* Improvement: Options are now automatically removed from `wp_options` when the plugin is deleted from the dashboard
* Improvement: Added several WordPress filters, allowing developers to extend/modify the default functionality of the plugin
* Improvement: Additional strings were made i18n friendly
* Improvement: All options are now stored in one row in wp_options rather than one row per option
* Improvement: Several UI elements have be reworded and styled differently to improve user experience
* Fix: Now works with themes using `the_content()` on archive pages (i.e. WordPress default themes and others)
* Fix: Notices/warning were appearing when the options were saved while having a checkbox option unchecked
* Fix: The "Read More" link was being incorrectly appended into certain HTML tags, e.g. table tags and list tags

= 4.1 =
* Fix: Template function with custom options works again
* Fix: Data before header bug (retro-fixed in 4.0)
* Feature: Template function also works with array-style parameters
* Removed multibyte support
* Removed PHP 4 support (WP 3.2+ users should be fine, others should update)
* Better code testing before release!

= 4.0 =
* Feature: Brand new parsing algorithm which should resolve some running time issues
* Feature: Options to finish a word or sentence before cutting the excerpt
* Fix: A few small bugs

= 3.1 =

* Fix: A few bugs with custom and character-based excerpts

= 3.0 =

* First major release since 0.2.2 (also removed the `0.` prefix from the version number)
* Feature: Shortcodes can be removed from the excerpt
* Feature: Virtually any HTML tag may now be stripped
* Feature: A read-more link with custom text can be added
* Fix: Word-based excerpt speed improved
* Fix: Template tag function improved
* Fix: Better ellipsis placement
