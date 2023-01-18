# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Shortcode and template tag to display listing of all supported text hovers (filterable)
* Switch to pure-CSS tooltips? At the very least, away from deprecated qTip2 library.
  * Consider: https://github.com/calebjacob/tooltipster
  * Consider: https://medium.com/two-factor-authenticity/tiny-design-bite-transitioning-tooltip-text-with-pseudo-elements-hover-states-82fbe00e8c33
  * Consider: https://blog.logrocket.com/creating-beautiful-tooltips-with-only-css/
* Metabox to display listing of all supported text hovers
* Smarter input form for text hovers. Repeatable field with sub-fields name and hover text. (This will allow having multiline hover text).
* Ability for users to set the text color, background color, and border color of their tooltips.
* Settings page text area for testing sample text. Use AJAX to fetch parsed text from server for display. Applies same styles as it would on frontend.
* Add FAQ regarding if the text hover affect and styling can be used on-the-fly in posts. E.g. how they can write a one-off abbreviation themselves.
* The 'more_filters' setting help text should include amongst default filters the third party filters handled by the plugin, but only if the related plugin(s) are activated
* Support preventing a filter from being text hovered (needed to override those added via filters, namely some third party filters). Could use a '-' prefix for more_filters filter (e.g. "-acf/format_value/type=url" prevents a filter from adding that filter). Or maybe better to have a never_filters setting to separately set those.
* Add per-post setting/meta to disable post from getting processed by plugin, e.g. "Prevent text hover for this post?"
* Add per-post setting/meta to disable text hovering for specific terms, e.g. "Prevent text hover for these terms:"
  See: https://wordpress.org/support/topic/hovered-terms-appearing-in-all-posts/

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/text-hover/) or on [GitHub](https://github.com/coffee2code/text-hover/) as an issue or PR).