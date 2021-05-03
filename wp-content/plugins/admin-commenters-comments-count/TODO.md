# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* When a comment gets approved/unapproved via comment action links, update commenter's count accordingly
  * Update comment count and pending comment count (just fetch an updated bubble -- most accurate, or update the bubble directly -- least surprising to user, especially if the counts would jump due to updates made since page render)
  * Also update author comment bubbles found elsewhere on the page
  * JS: Hook when post row gets updated/changed as a result of click of unapprove/approve link. (hook 'click', then wait for change to row, then trigger updater)
    Updater: Did comment state change? If so, determine new comment bubble. (quickest would be to request the markup via API)
      Update bubble for current row. Then find other rows for person with same email address
      `document.querySelectorAll('.column-author a[href="mailto:${email}"]').closest('td').querySelector('.column-response');`
      and replace the comment bubble for each with what was returned by API
* Allow admin to manually group commenters with different email addresses
  * Allows for grouping a person who may be using multiple email addresses, or maybe admin prefers to group people per organization
  * The reported counts would be for the group and not the individual
  * The link to see the emails would search for all of the email addresses in the group. Via filter maybe?
* Add sortability to 'Comments' column in user table
* Consider inserting commenter comment bubble via 'comment_row_actions' hook like Akismet does, though that requires introducing a JS dependency

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/admin-commenters-comments-count/) or on [GitHub](https://github.com/coffee2code/admin-commenters-comments-count/) as an issue or PR).