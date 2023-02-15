=== BU Learning Blocks ===
Contributors: carlosesilva, dannycrews, jdub233
Tags: learning, teaching, education, online courses, boston university, bu
Requires at least: 5.3.2
Tested up to: 6.0
Stable tag: 1.1.4
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

BU Learning BLocks is a plugin to facilitate online learning.

== Description ==

BU Learning Blocks (BULB) is a collection of Gutenberg blocks and WordPress Custom Post Types that enable the easy creation of academic lessons. With BULB you can facilitate online learning by embedding self-assessment questions directly into your lesson. Creating and publishing a BULB Lesson is no different than creating a standard WordPress Page. The plugin provides two key capabilities that are not provided by WordPress:

- A set of blocks that help you add different types of self-assessment questions
- A way to order and navigate multiple Lesson Pages in a specific sequence

BULB is not a Learning Management System (LMS) and, currently, does not have typical LMS features such as scoring or timers. The objective of BULB is to improve learning and retention through in-line questions that reinforce the subject matter and allow students to test their understanding directly within the Lesson Page.

BULB questions are inserted into the lesson content through the placement of blocks into the page editor. The questions are added and articulated in the WordPress block editor and are saved within the Lesson Page content. BULB does not add any tables to the WordPress database.

BULB is compatible with WordPress 5.3.2 and above and the Gutenberg editor must be enabled.

Additional documentation is available in the [BULB user guide](https://developer.bu.edu/bulb/).

# Installing and activating

BULB can be installed and activated like any other WordPress plugin.  

When activated, BU Learning Blocks presents a choice to install only the question blocks or both the question blocks and the BULB custom post type. BULB Question Blocks can be used on WordPress Posts or Pages, and on BULB Lesson Pages. If you wish to use the question blocks in your site content, but do not wish to create BULB Lessons, select “Install Blocks Only”.

The plugin can be activated and deactivated, no custom posts will be deleted.  Deleting the plugin will cause all of the custom post type data to be deleted as well.

# Development

Development takes place at https://github.com/bu-ist/bu-learning-blocks/

== Changelog ==

= 1.1.4 =
* Patch issues with 5.8 and 5.9 compatibility

= 1.1.3 =
* Add Github publish action

= 1.1.2 =
* Prefixing, function name, and variable name changes

= 1.1.1 =
* Initial public release
