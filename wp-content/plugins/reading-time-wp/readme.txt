=== Reading Time WP ===
Contributors: yingling017, jvarn13, bonaparte
Donate link: https://jasonyingling.me/donations/buy-me-a-coffee/
Tags: reading time, estimated time, word count, time, posts, page, reading
Requires at least: 3.0.1
Tested up to: 6.5.2
Stable tag: 2.0.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Reading Time WP creates an estimated reading time of your posts that is inserted above the content or by using a shortcode.

== Description ==

WP Reading Time let's you easily add an estimated reading time to your WordPress posts. Activating the plugin will automatically add the reading time to the beginning of your post's content. This can be deactivated in the Reading Time settings which can be accessed from your Dashboard's Settings menu. You can also edit the label and postfix from this menu.

If you'd prefer more control over where you add your reading time you can use the the [rt_reading_time] shortcode to insert the time into a post. This shortcode also excepts values for label and postfix. These are optional. Ex. [rt_reading_time label="Reading Time:" postfix="minutes"].

== Installation ==

1. Upload the 'rt-reading-time-wp' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it! Your reading time will now be inserted at the beginning of every post.
4. If you would like to edit settings or turn off reading time, select Reading Time from the WP Dashboard Settings menu

== Frequently Asked Questions ==

= How do I insert my reading time before posts. =

On initial installation your reading time should be showing where the_content is called in your template. If it is not, navigate to the Reading Time WP settings menu in the WP Dashboard Settings menu and make sure the "Insert Reading Time before content" option is checked.

= Great, but how do I control the post types, like pages and custom post types, the reading times shows on? =

Just navigate to the Reading Time WP settings page and select which post types you want your Reading Times to display on. Reading Time WP defaults to posts and pages.

= But I want more individual control to insert reading time only on specific posts. How can I do that? =

Easy, turn off the "Insert Reading Time before content" option form the Reading Time settings within your WP Dashboard's settings. Then use the Reading Time WP shortcode [rt_reading_time label="Reading Time:" postfix="minutes"]. Best of all the label and postfix parameters are optional.

= That's good and all, but how do I insert it into my theme? =

Still easy, but you'll need to use WordPress' built in do_shortcode function. Simply place `<?php echo do_shortcode('[rt_reading_time label="Reading Time:" postfix="minutes"]'); ?>` into your theme wherever you please.

= I'll just go with it entering before the_content. How can I change what appears before and after the reading time? =

Just edit the Reading time label and Reading time postfix fields in the Reading Time WP Settings. The label appears before the time and the postfix after. Feel free to leave either blank to not use that field.

= Does this count images in the Reading Time? =

Yes! Reading Time WP calculates images based on Medium's article on the topic here: https://blog.medium.com/read-time-and-you-bc2048ab620c.

So for the first image add 12 seconds, second image add 11, ..., for image 10+ add 3 seconds each.

= How can I only display reading time on single posts? =

Drop the code from this Gist in your functions.php. https://gist.github.com/jasonyingling/ad2832bc1768d1fbb63341aef072908b

= How do I remove the Reading Time from Yoast's meta description? =

Drop the code from this Gist in your functions.php https://gist.github.com/jasonyingling/5917dc97b302ca37abce7ceb93a7f4b8

= How can I add meta fields, say from Advanced Custom Fields, into the Reading Time WP count? =

Just hook into the `rtwp_filter_wordcount` filter and increment the word count the reading time is based on.

= How do I specify a different post ID in the shortcode, e.g. to show each post's reading time on a page that lists many posts? =

Use the optional page_id attribute on the shortcode. e.g. [rt_reading_time label="Reading Time:" postfix="minutes" postfix_singular="minute" post_id="123"]


== Screenshots ==

1. An example of an estimated reading time entered before "the_content".
2. The options available in Reading Time WP.

== Changelog ==

= 2.0.16 =
* WordPress 6.5 support

= 2.0.15 =
* Updating stable tag to correct version

= 2.0.14 =
* Switched plugin to `init` hook
* If admin file is called directly, abort.
* Fixed issue with postfix when reading time is `< 1`
* Tested with WordPress 6.3.1 and PHP 8.2

= 2.0.13 =
* Tested on WordPress 6.2
* Bug: Fixed potential undefined index errors
* Developer note: Updated variable name $rt_after_content to $rt_before_excerpt.

= 2.0.12 =
* Been awhile since I deployed and missed a version number update

= 2.0.11 =
* Fixed notice if $rt_reading_time_options['post_types'] is not set
* Testing on WordPress 6.0.2 and PHP 8.0

= 2.0.10 =
* WordPress 5.5 compatability

= 2.0.8 =
* Improving conditional logic checks
* Fixing a bug where post types would show as checked on refresh if all post types were unchecked

= 2.0.7 =
* Switching words per minute to use number input for better validation

= 2.0.6 =
* Adding better post sanitization
* Allowing for reading times under 1 minute

= 2.0.5 =
* Adding a post_id attribute to the shortcode
* Adding `rt_add_postfix` function for outputting the postfix
* Adding `rt_edit_postfix` filter for editing output postfix
* Grammar edits.

= 2.0.4 =
* Fixing a PHP Warning for users that installed priort to version 1.2.0 and hadn't updated the settings page since.

= 2.0.3 =
* Adjusting how post types are output on admin page for better translations
* Including nl_NL translation courtesy of @bonaparte

= 2.0.2 =
* Improved support for more languages with a new count function. Props to jvarn13.
* Loaded textdomain for plugin
* Updated POT file

= 2.0.1 =
* Fixing error with Reading Time shortcode when using postfix_singular attribute

= 2.0.0 =
* Updating plugin to better meet WordPress Coding Standards. This includes renaming variables throughout the plugin.
* Note: If you've hooked into Reading Time WP's class, variables, or functions this update could cause issues.
* Fixing HTML output when using shortcode to match auto inserted reading times

= 1.2.2 =
* Switched default for all post types to display reading time if option has not been set
* Add option to include shortcodes in the reading time count

= 1.2.0 =
* Hoo boy do we have a big one.
* Allowed for filtering of the `$shortcode_atts`.
* Added the much requested ability to control which post types reading times display on.
* Added a filter for adding to the word count the reading time is based on.
* Added in an actual text-domain and translation functions and generated a .pot file.
* Cleaned up a bit for better coding standards.

= 1.1.0 =
* Added images into reading time calculations based on Medium's suggestion. https://blog.medium.com/read-time-and-you-bc2048ab620c

= 1.0.10 =
* Tested in WordPress 4.9

= 1.0.9 =
* Fixed typo in shortcode and implemented better sanitization from github

= 1.0.8 =
* Added in singular postfix setting. Added in separate control to display reading time on excerpts.

= 1.0.7 =
* Switched to using span elements instead of divs for inserting before content and excerpt

= 1.0.6 =
* Updated the way the word count is calculated to be more accurate when using images and links

= 1.0.5 =
* Plugin tested for WordPress 4.1

= 1.0.4 =
* Minor fix to stable version tags, updating readme after fixes in 1.0.2 and 1.0.3

= 1.0.3 =
* Fixes issue with miscalculating the reading time when using <!--more--> tags and the_content. Also fixes issue with reading time appearing inline when using the_excerpt.

= 1.0.2 =
* Fixing bug with more tags in the_content

= 1.0.1 =
* Converting the plugin to a class based structure

= 1.0.0 =
* Initial release
