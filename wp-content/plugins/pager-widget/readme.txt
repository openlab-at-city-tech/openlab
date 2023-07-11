=== Pager Widget ===
Contributors: figureone
Tags: pager, navigation, next, back, previous, menu, widget
Tested up to: 6.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Prints "Parent | Previous | Next" links to navigate between pages at the same level in the page
hierarchy (and up to the parent page).

== Description ==

Description: Widget that provides "Parent | Previous | Next" buttons to navigate between pages at the same
hierarchy level (and up to the parent page). You can modify the settings to choose the words you want to
use. To enable, first activate the plugin, then add the widget to a sidebar in the Widgets settings page.

View or contribute to the plugin source on github: [https://github.com/uhm-coe/pager-widget](https://github.com/uhm-coe/pager-widget)

== Installation ==

1. Upload `pager-widget.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the widget where you want it through the 'Widgets' menu in Wordpress
1. Adjust the settings, if necessary, after placing the widget in a widget area

== Screenshots ==

1. The Pager Widget added to the page bottom widget area.

== Changelog ==

= 1.8.3 =
* Fix log warnings about undefined sortStoryModeAlphabetically.
* Tested up to WordPress 6.2.2.

= 1.8.2 =
* Don’t render widget on non-hierarchical post types.
* Tested up to WordPress 5.8.1.
* Apply coding standards via phpcs.xml.

= 1.8.1 =
* Tested up to WordPress 5.4.2.
* Fix deprecation notice about create_function() on PHP 7.2 or higher.
* Fix PHP notice about isStoryMode when checkbox is toggled when saving widget.

= 1.8.0 =
* Remove empty &lt;h2 class="widget-title">&lt;/h2> above widget.
* Add page titles to anchor title attribute (so they show as tooltips). Props @UsuallyLogical for the suggestion!

= 1.7.5 =
* Add feature to sort story mode alphabetically. Props @deruyck for the suggestion!

= 1.7.4 =
* Fix for %title on parent link (was using current page title, not parent page). Props @Cornwell for tracking down the bug!

= 1.7.3 =
* Updated for WordPress 3.9.

= 1.7.2 =
* Fixed a story mode bug where the second-to-last page wouldn't display a next link.

= 1.7.1 =
* Fixed a sort order bug with the new story mode (oops).

= 1.7 =
* Add Story Mode, which pages through all site content, not just content under a parent item. It basically walks through the site's tree structure.

= 1.6 =
* Add variable %title so you can display the name of the page on the pager links

= 1.5 =
* Bug fix release (missed an $after_widget print on pages without the pager)

= 1.0 =
* Public release.

= 0.9 =
* Added ability to change level and text of links in the Widget panel.

= 0.8 =
* Converted plugin to 2.8 architecture to support multiple instances.

= 0.5 =
* Added link to parent.

= 0.1 =
* First development version.

== Upgrade Notice ==

= 1.0 =
First public release.
