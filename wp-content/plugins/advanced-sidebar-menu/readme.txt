=== Advanced Sidebar Menu ===

Contributors: Mat Lipe, onpointplugins
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40onpointplugins%2ecom&lc=US&item_name=Advanced%20Sidebar%20Menu&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: block, widget, dynamic, hierarchy, menus, sidebar menu, category, pages, parent, child, automatic
Requires at least: 5.8.0
Tested up to: 6.1.1
Requires PHP: 7.0.0
Stable tag: 9.0.5
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

<h3>Fully automatic sidebar menus.</h3>

Uses the parent/child relationship of your pages or categories to generate menus based on the current section of your site. Assign a page or category to a parent and this will do the rest for you.

Keeps the menu clean and usable. Only related items display, so you don't have to worry about keeping a custom menu up to date or displaying links to items that don't belong. 

<strong>Check out <a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/">Advanced Sidebar Menu PRO</a> for more features including accordion menus, menu colors and styles, custom link text, excluding of pages, category ordering, custom post types, custom taxonomies, priority support, and so much more!</strong>

<blockquote><a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/" target="_blank">PRO version 9.0.0</a> is now available with Gutenberg blocks!</blockquote>

<h3>Features</h3>
* Page and Category widgets.
* Page and Category blocks. **NEW**
* Option to display or not display the highest level parent page or category.
* Option to display the menu when there is only the highest level parent.
* Ability to order pages by (date, title, page order).
* Exclude pages or categories by entering a comma separated list of ids.
* Option to always display child pages or categories.
* Option to select the levels of pages or categories to display when always display child is used.
* Option to display or not display categories on single posts.
* Ability to display each single post's category in a new widget/block or in same list.

<h3>Page Menu Options</h3>
* Display the highest level parent page.
* Display menu when there is only the parent page.
* Order pages by (date, title, page order).
* Exclude pages.
* Always display child Pages.
* Levels of child pages to display when always display child pages is checked.

<h3>Category Menu Options</h3>
* Display the highest level parent category.
* Display menu when there is only the parent category.
* Display categories on single posts.
* Display each single post's category in a new widget/block or in same list.
* Exclude categories.
* Always display child categories.
* Levels of Categories to display when always display child categories is checked.

<h3>PRO Features</h3>
* Navigation menu widget.		
* Navigation menu Gutenberg block. **NEW**
* Ability to customize each page or navigation menu item link's text.
* Click-and-drag styling for page, category, and navigation menus.
* Styling options for links including color, background color, size, hover, and font weight.
* Styling options for different levels of links.
* Styling options for the current page or category.
* Styling options for the parent of the current page or category.
* Blocked styling options including borders, border width, and border colors.
* Option to choose from 7 bullet styles or no bullets.
* Accordion menu support for pages, categories, and navigation menus.
* Accordion icon style and color selection.
* Accordion option to keep all sections closed until clicked.
* Accordion option to include highest level parent in accordion.
* Accordion option to use links for open/close.
* Ability to exclude a page from all menus using a simple checkbox.
* Link ordering for the category menus.
* Number of levels of pages to show when "always display child pages" is not checked.
* Ability to select and display custom post types.
* Ability to select and display custom taxonomies.
* Option to display only the current page's parents, grandparents, and children.
* Option to display child page siblings when on a child page (with or without grandchildren available).
* Ability to display the menu everywhere the widget area is used (including homepage if applicable).
* Ability to select the highest level parent page/category.
* Ability to select which levels of categories assigned posts will display under.
* Ability to display assigned posts or custom post types under categories or taxonomies.
* Ability to limit the number of posts or custom post types to display under categories.
* Support for custom navigation menus from Appearance -> Menus.
* Ability to display the current navigation menu item's parents and children only.
* Option to display the top-level navigation menu items when there are no child items or not viewing a menu item.
* Priority support with access to members only support area.

<h3>Included Language Translations</h3>
* English (en_US).
* French (fr_FR).
* German (de_DE).
* Spanish (es_ES).
   
<h3>Developers</h3>
Developer docs may be found <a target="_blank" href="https://onpointplugins.com/advanced-sidebar-menu/developer-docs/">here</a>.

<h3>Contribute</h3>
Send pull requests via the <a target="_blank" href="https://github.com/lipemat/advanced-sidebar-menu">GitHub Repo</a>


== Installation ==

Use the standard WordPress plugins search and install.

Manual Installation

1. Upload the `advanced-sidebar-menu` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Drag the "Advanced Sidebar - Pages" widget, or the "Advanced Sidebar - Categories" widget into a sidebar.
4. Use the block inserter to insert the "Advanced Sidebar - Pages" block, or the "Advanced Sidebar - Categories" block into Gutenberg content.



== Screenshots ==

1. Page widget options.
2. Category widget options.
3. Example of a page menu using the 2017 theme and default styles.
3. Example of a category menu ordered by title using the 2017 theme and default styles.


== Frequently Asked Questions ==

= The menu won't show up?

The menu in this plugin are smart enough to not show up on pages or categories where the only thing that would display is the title. While it may appear like the menu is broken, it is actually doing what it is intended to do.

The most common causes for this confusion come from one of these reasons:
1. The incorrect menu was selected. Categories have their own widget/block as pages have their own widget/block.
2. "Display the highest level parent page" or "Display the highest level parent category" is not checked.
3. The Pages menu is currently not being viewed on a page.
4. The Categories menu is not currently being view on a category.

= How do I change the styling of the current page? =

You may add CSS to your theme's style.css to change the way the menu looks.

For example the following CSS would:
1. Remove the dot to the left of the menu item.
2. Change the link color.
3. Add a background on hover.

<code>
.advanced-sidebar-menu li.current-menu-item a {
    color: black;
}

.advanced-sidebar-menu li.current-menu-item {
    list-style-type: none !important;
}

.advanced-sidebar-menu li.current-menu-item > a:hover {
	background: teal;
}
</code>

To style your menu without using any code <a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/" target="_blank">upgrade to PRO</a>.

= How do you get the categories to display on single post pages? =

The Categories Menu widget/block contains a "Display categories on single posts" checkbox, which will display the category menus based on the categories the current post is assigned to.

= Does the menu change for each page you are on? =

Yes. Based on whatever page, post or category you are on, the menu will change automatically to display the current parents and children.


== Changelog ==
= 9.0.5 =
* Switched to static uses of class constants to improve extendability.
* Switched to full namespaced constants.
* Improved PHPCS definitions.
* Improved translations.
* Included help information for the category widget exclude settings.

= 9.0.4 =
* Added "current-menu-ancestor" CSS class to Pages and Categories menus.
* Introduced "advanced-sidebar-page" CSS class to Pages menus.
* Tested to WordPress Core 6.1.1.

= 9.0.3 =
* Fixed issue with styles not loading in Elementor.
* Tested to WordPress Core 6.0.2.

= 9.0.2 =
* Moved `advanced-sidebar-menu` CSS class to block widget wraps.
* Introduced `isScreen` helper to make screen conditionals cleaner.
* Included "Display each single post's categories" option on the customizer screen.
* Included PHP version in debug information.
* Improved readme.

= 9.0.0 =
<a href="https://onpointplugins.com/advanced-sidebar-gutenberg-blocks/">Full release notes</a>.

* Introduced Gutenberg blocks.
* Improved translations.
* Improved Elementor support.
* Removed all deprecated functionality.
* Required PRO version 9.0.0+.
* Required WordPress Core 5.8.0+.
* Drop support for PHP 5.6 if favor of PHP 7.0+.
* Numerous bug fixes.

= 8.8.3 = 
* Introduced `advanced-sidebar-menu/menus/category/top-level-term-ids` filter.
* Supported PRO version 8.9.2.

= 8.8.2 =
* Fixed widget id generation with block based widgets.
* Introduced `advanced-sidebar-menu/core/include-template-parts-comments` filter.
* Organized the `Menu_Abstract` class constants.
* Tested to WordPress Core 6.0.1.

= 8.8.1 = 
* Introduced `advanced-sidebar-menu/menus/page/is-excluded` filter.
* Introduced `advanced-sidebar-menu/menus/category/is-excluded` filter.
* Tested to WordPress Core 6.0.0.
* Required PRO version 8.7.0+.

= 8.8.0 =
* Implement universal 'menu-item' style CSS classes to all menus.
* Introduced `Category::is_current_top_level_term` method.
* Introduced `Category::get_current_ancestors` method.
* Introduced `Category::is_current_term` method.
* Enabled PHPCS caching.
* Required WordPress core version 5.4.0+.
* Tested to WordPress 5.9.3.

= 8.7.3 = 
* Include WP core version in debug info.
* Tested to WordPress 5.9.2.

= 8.7.2 = 
* Improved position of close icon in widget previews.
* Improved plugin links utm structure.
* Added a "Go PRO" action to the plugins list.
* Prevented overrides of non-public post types during debugging.

= 8.7.1 =
* Fine tune widgets styles for WordPress 5.9.
* Tested to WordPress 5.9.

= 8.7.0 =
* Use Webp extension for preview images.
* Simplify the Widget names.
* Introduce `Utils::array_map_recursive` method for deep sanitization.
* Support multidimensional arrays in debug overrides. 
* Tested to WordPress 5.8.3.
* Required PRO version 8.5.0+.

= 8.6.4 = 
* Introduced `advanced-sidebar-menu/debug/print-instance` filter.
* Improved FAQ information.
* Remove dangling reference to old built in styles from FAQ.

= 8.6.3 =
* Fix issue with CSS classnames on the current page's children.

= 8.6.2 =
* Assured consistency for levels of page menu's CSS classes.
* Converted category get the highest parent logic to `get_ancestors`.
* Gracefully handle invalid taxonomies in Category widgets.
* Gracefully handle widgets without ids.
* Fixed color pickers in Elementor.
* Fixed color pickers in Beaver Builder.

= 8.6.1 = 
* Improved widget interaction handling.
* Synced styles between block, classic and customizer widgets.
* Fixed customizer widget buttons.

= 8.6.0 =
* Support WordPress version 5.8.
* Support Gutenberg widgets screen.
* Minimum required version for PRO 8.5.

= 8.5.0 =
* Introduce `Utils` class for shared non specific functionality.
* Introduce `is_checked` method for determining checkbox state from anywhere.
* Make `Widget_Abstract::set_instance` public for external use.
* Complete preparations for PRO version 8.4.
* Minimum required version for PRO 8.4.

= 8.4.0 =
* Introduce new Category Walker to increase extensibility.
* Support `data-level` on all widgets.

= 8.3.4 =
* Support widget fields with array values.

= 8.3.3 =
* Introduce `data-level` on all page menu levels for specific targeting.
* Tested to WordPress 5.7.2.

= 8.3.2 =
* Fully compatible with PHP8.
* Tested to WordPress 5.7.1.

= 8.3.1 =
* Add readme for translations.
* Tested to WordPress 5.7.
* Require WordPress core 5.2+.
* Improved PHPCS configuration.

= 8.3.0 =
* Improved plugin headers.
* Added translations for French (fr_FR).
* Added translations for Spanish (es_ES).
* Expose `Category::is_tax` method for public filters.
* Improved type casting and PHPStan static type checking.
* Fix tense in readme.txt words.

= 8.2.0 =
* Improved widget labels, descriptions and styles.
* Support blocked styling borders on all levels.
* Improved Beaver Builder and Elementor styles.
* Improved info panel.

= 8.1.1 =
* Improved readme.
* Tested to WordPress 5.6.0.

= 8.1.0 =
* Restructure widget info panels.
* Introduced new `advanced-sidebar-menu/widget/page/before-columns` action.
* Introduced new `advanced-sidebar-menu/widget/category/before-columns` action.
* Improved PHPCS exclusion declarations.
* Improved CSS structure.
* Improved JavaScript structure.

= 8.0.4 =
* Improved styles when used with Beaver Builder.
* Require WordPress version 5.0.0+.

= 8.0.3 =
* Allow `List_Pages::get_args()` to be filtered on any level.
* Make debugging functionality more stable.

= 8.0.2 = 
* Introduced new `advanced-sidebar-menu/menus/category/get-child-terms` filter
* Support filtering the first level of categories.
* Use `is_excluded` vs `is_first_level_category` in category view.

= 8.0.0 =
Major version update. See <a href="https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-version-8-migration-guide/">migration guide</a> if you are extending the plugin's functionality via action, filters, or calling plugin classes.

== Upgrade Notice ==
= 9.0.1 = 
Introducing <a href="https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-gutenberg-blocks/">Gutenberg blocks</a>.
