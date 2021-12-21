# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Shortcode, template tag, widget?, Gutenberg panel to display listing of all supported text replacements (filterable)
* Prevent replacement text from getting replacements, e.g. given `['test this' => 'good test', 'test' => 'cat']` the text 'test this' should become 'good test' and not 'good cat'. See unit test `test_does_not_replace_a_previous_replacement()`.
  * Existing behavior may actually be desired
* Prevent replacements from affecting shortcode tags and attributes. Perhaps control behavior via filter and (advanced) setting. See unit tests `test_does_not_replace_shortcode()` and `test_does_not_replace_within_shortcodes()`.
  * Existing behavior may actually be desired
* Consider defaulting 'when' setting to 'late' instead of 'early'
* Make matching require word barriers on either end to prevent partial string matching (e.g. "cat" should not match "category"). Also control behavior via filter and (advanced) setting.
* Settings page tool to allow for scanning of all posts to find matches for a given text replacement match term, as a way to preview what would get affected.
* Settings page tool to allow testing saved replacements against sample text.
* Add ability to comment out an entry (e.g. starting line with '#')
* Change from a simple textarea input field to a repeatable field with separate fields for search string and replacement string. This could allow for multiline text replacements.
* Support and enforce a configurable max number of total replacements on a per post basis
* Add an admin notice that warns if any of the text to replace are alphanumerical and of 3 characters or less. Add filter to allow customizing threshold and/or adding exclusions.
* Disable support for attempts to use single character replacements. Add admin notice to alert user when present. (Perhaps when saved, also auto-comment them out?)
* Auto-detect if it looks like smilies are being defined (at least those recognized by WP) AND the setting for WP to convert those is enabled. Show admin notice when this conflict arises, with suggestion to disable the WP setting. Can then remove the "Other considerations" item regarding this.
* Support preventing a filter from being text replaced (needed to override those added via filters, namely some third party filters). Could use a '-' prefix for more_filters filter (e.g. "-acf/format_value/type=url" prevents a filter from adding that filter). Or maybe better to have a never_filters setting to separately set those.
* The 'more_filters' setting help text should include amongst default filters the third party filters handled by the plugin, but only if the related plugin(s) are activated
* Move hook documentation out of readme.txt and into something like DEVELOPER.md
* Add per-post setting/meta to disable post from getting processed by plugin, e.g. "Prevent text replacements for this post?"
* Add ability to have text replacements occur prior to post being saved to database. This prevents the dynamic nature of how replacements currently happen.
  * This should be a per-post setting (the default should be false, but configurable via setting and/or filter).
  * The per-post setting should be saved as post meta for potential future reference.
  * The original post content should be stored somewhere to facilitate reversion. Maybe as an explicit revision.
    * If such a post is edited and on-save pre-processed content is known, perhaps add a notice or "revert" link somewhere to facilitate restoring the post in case replacements went unexpectedly. This would be disabled once the user saved a newer version of the post.
* Add ability (with setting and hook) to disable partial word matching.

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/text-replace/) or on [GitHub](https://github.com/coffee2code/text-replace/) as an issue or PR).