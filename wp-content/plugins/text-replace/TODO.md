# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Shortcode, template tag, widget?, Gutenberg panel to display listing of all supported text replacements (filterable)
* Prevent replacement text from getting replacements, e.g. given `['test this' => 'good test', 'test' => 'cat']` the text 'test this' should become 'good test' and not 'good cat'. See unit test `test_does_not_replace_a_previous_replacement_KNOWN_FAILURE()`.
* Prevent replacements from affecting shortcode tags and attributes. Perhaps control behavior via filter and (advanced) setting. See unit tests `test_does_not_replace_shortcode_KNOWN_FAILURE()` and `test_does_not_replace_within_shortcodes_KNOWN_FAILURE()`.
* Consider defaulting 'when' setting to 'late' instead of 'early'
* Make matching require word barriers on either end to prevent partial string matching (e.g. "cat" should not match "category"). Also control behavior via filter and (advanced) setting.
* Settings page tool to allow for scanning of all posts to find matches for a given text replacement match term, as a way to preview what would get affected.
* Settings page tool to allow testing saved replacements against sample text.
* Add setting to specify additional filters to be handled by the plugin (basically a UI for the `c2c_text_replace_filters` filter)
* Add ability to comment out an entry (e.g. starting line with '#')
* Change from a simple textarea input field to a repeatable field with separate fields for search string and replacement string
* Support and enforce a conifgurable max number of total replacements on a per post basis
* Add an admin notice that warns if any of the text to replace are alphanumerical and of 3 characters or less. Add filter to allow customizing threshold and/or adding exclusions.
* Disable support for attempts to use single character replacements. Add admin notice to alert user when present. (Perhaps when saved, also auto-comment them out?)
* Auto-detect if it looks like smilies are being defined (at least those recognized by WP) AND the setting for WP to convert those is enabled. Show admin notice when this conflict arises, with suggestion to disable the WP setting. Can then remove the "Other considerations" item regarding this.

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/text-replace/) or on [GitHub](https://github.com/coffee2code/text-replace/) as an issue or PR).