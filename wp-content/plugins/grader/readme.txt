=== Grader ===
Contributors: michael_porter
Donate link: none
Tags: education, grading, evaluation
Requires at least: 3.0
Tested up to: 3.1
Stable tag: .4

Grader allows site administrators and editors to grade user posts.

== Description ==

Grader allows site administrator's to grade user posts.  Grades are only visible to administrators, editors, and the post's author.  Administrators and editors can grade a post by adding a comment that starts with a token.  The default token is @grade, but this can be changed through the plugin's option page.  Administrator's can also add a comment to a grade.  Comments and grades are seperated by a delimieter.  The default delimeter is ".", but this can also be changed through the plugin's option page.  Grades are visible to admins and the post's authors (but not to anyone else) when viewing the post or in the post admin panel.

== Instructions ==

When the comment:

@grade A. Great Work!!!

is added to a post, it can be broken down into three parts.  First, the token (@grade) signifies that this is a grade.  Second, the grade (A) which follows the token, and third the comment (Great Work!!!) which is seperated from the grade by the delimeter (.).  If an administrator grades a post a second time, the plugin automatically deletes the first grade.

Grader changes behaviors in three locations.

1. When Viewing the Post
When viewing the post (not through admin panels), only the post's authors and the site's administrators and editors can view grades.  For all other users, the grade does not appear and the number of comments is lessened by 1.  For the site's administrator,editors, and the post's author grades appear as follows:

GRADE: A

Great Work!!!

Only instructors and the post's author can see the grade for a post.

The message at the bottom can be changed through the plugin's option screen.

2. The manage posts panel
On the manage posts panel, grader adds a Grade column.  For posts with a grade, grader also adds a "Edit Grade" action.

3. The manage comments panel
Because comments cannot be removed from the manage comments panel, for users other than the post's author or the site's administrator or editor the grade and the comment have been replaced by a hidden text message.  The hidden text message defaults to "Private", but can be changed through the plugin's admin panel.

== Installation ==

1. Upload `grader.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= .3 =
- Site administrators and editors can now enable users to edit graded posts (but not the grade comment) through an option in the "Grader Option" screen.
- Both editors and administrators can now grade (before it was only administrators).

= .4 =
- minor revision for wp 3.1 compatability

