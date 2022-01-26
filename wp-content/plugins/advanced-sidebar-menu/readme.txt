=== Advanced Sidebar Menu ===

Contributors: Mat Lipe, onpointplugins
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40onpointplugins%2ecom&lc=US&item_name=Advanced%20Sidebar%20Menu&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: menus, sidebar menu, hierarchy, category menu, pages menu, dynamic
Requires at least: 5.2.0
Tested up to: 5.8.2
Requires PHP: 5.6.0
Stable tag: 8.6.4

== Description ==

Uses the parent/child relationship of your pages or categories to generate menus based on the current section of your site. Assign a page or category to a parent and this will do the rest for you.

Keeps the menu clean and usable. Only related items display so you don't have to worry about keeping a custom menu up to date or displaying links to items that don't belong. 

<strong>Check out <a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/">Advanced Sidebar Menu PRO</a> for more features including accordion menus, menu colors and styles, custom link text, excluding of pages, category ordering, custom post types, custom taxonomies, priority support, and so much more!</strong>

<blockquote><a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/" target="_blank">PRO version 8.4.0</a> is now available with accordion options to use links for open/close!</blockquote>

<h3>Features</h3>
* Page and Category widgets.
* Option to display or not display the highest level parent page or category.
* Option to display the menu when there is only the highest level parent.
* Ability to order pages by (date, title, page order).
* Exclude pages or categories by entering a comma separated list of ids.
* Option to always display child pages or categories.
* Option to select the levels of pages or categories to display when always display child is used.
* Option to display or not display categories on single posts.
* Ability to display each single post's category in a new widget or in same list.

<h3>Page Widget Options</h3>
* Add a title to the widget
* Display the highest level parent page
* Display menu when there is only the parent page
* Order pages by (date, title, page order)
* Exclude pages
* Always display child Pages
* Number of levels of child pages to display when always display child pages is checked

<h3>Category Widget Options</h3>
* Add a title to the widget
* Display the highest level parent category
* Display menu when there is only the parent category
* Display categories on single posts
* Display each single post's category in a new widget or in same list
* Exclude categories
* Always display child categories
* Levels of Categories to display when always display child categories is checked

<h3>PRO Features</h3>
* Ability to customize each page or navigation menu item link's text.
* Click-and-drag styling for page, category, and navigation menu widgets.
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
* Accordion option to use links for open/close. **NEW**
* Ability to exclude a page from all menus using a simple checkbox.
* Link ordering for the category widget.
* Number of levels of pages to show when "always display child pages" is not checked.
* Ability to select and display custom post types.
* Ability to select and display custom taxonomies.
* Option to display only the current page's parents, grandparents, and children.
* Option to display child page siblings when on a child page (with or without grandchildren available).
* Ability to display the widgets everywhere the widget area is used (including homepage if applicable).
* Ability to select the highest level parent page/category.
* Ability to select which levels of categories assigned posts will display under.
* Ability to display assigned posts or custom post types under categories or taxonomies.
* Ability to limit the number of posts or custom post types to display under categories.
* Support for custom navigation menus from Appearance -> Menus.
* Ability to display the current navigation menu item's parents and children only.
* Option to display the top-level navigation menu items when there are no child items or not viewing a menu item.
* Priority support with access to members only support area.

<h3>Translations</h3>
This plugin includes the following languages:
* English (en_US)
* French (fr_FR)
* German (de_DE)
* Spanish (es_ES)
   
<h3>Developers</h3>
Developer docs may be found <a target="_blank" href="https://onpointplugins.com/advanced-sidebar-menu/developer-docs/">here</a>.

<h3>Contribute</h3>
Send pull requests via the <a target="_blank" href="https://github.com/lipemat/advanced-sidebar-menu">GitHub Repo</a>


== Installation ==

Use the standard WordPress plugins search and install.

Manual Installation

1. Upload the `advanced-sidebar-menu` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Drag the "Advanced Sidebar Pages Menu" widget, or the "Advanced Sidebar Categories Menu" widget into a sidebar.


== Screenshots ==

1. Page widget options
2. Category widget options
3. Example of a page menu using the 2017 theme and default styles
3. Example of a category menu ordered by title using the 2017 theme and default styles


== Frequently Asked Questions ==

= The widget won't show up?

The widgets in this plugin are smart enough to not show up on pages or categories where the only thing that would display is the title. While it may appear like the widget is broken, it is actually doing what it is intended to do.

The most common causes for this confusion come from one of these reasons:
1. The incorrect widget was selected. Categories have their own widget as pages have their own widget.
2. "Display the highest level parent page" or "Display the highest level parent category" is not checked.
3. The widget is currently not being viewed on a page (for the pages widget) or category (for the categories widget).

= How do I change the styling of the current page? =

You may add CSS to your theme's style.css to change the way the menu looks.

For example the following CSS would:
1. Remove the dot to the left of the menu item.
2. Change the link color.
3. Add a background on hover.

<code>
.advanced-sidebar-menu li.current-cat a,
.advanced-sidebar-menu li.current_page_item a {
    color: black;
}

.advanced-sidebar-menu li.current-cat,
.advanced-sidebar-menu li.current_page_item {
    list-style-type: none !important;
}

.advanced-sidebar-menu li.current-cat > a:hover,
.advanced-sidebar-menu li.current_page_item > a:hover {
	background: teal;
}
</code>

To style your menu without using any code <a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/" target="_blank">upgrade to PRO</a>.

= How do you get the categories to display on single post pages? =

The Categories Menu widget contains a "Display categories on single posts" checkbox, which will display the category menus based on the categories the current post is assigned to.

= Does the menu change for each page you are on? =

Yes. Based on whatever page, post or category you are on, the menu will change automatically to display the current parents and children.


== Changelog ==
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

* Entirely new code structure.
* Removed all deprecated code and filters.
* Improved filter and action names.
* Improved performance.
* Remove default plugin styling.

= 7.7.3 =
* Fix widget info pane links.
* Fix widget editing on mobile devices.
* Tested up to PHP 7.4
* Tested up to WordPress Core version 5.4.1 

= 7.7.2 =
* Tested to 5.3.3.
* Change default "levels to display" to All.
* Fix notice level errors when retrieving current page.

= 7.7.0 =
* Enable accordion previews when editing via Beaver Builder.
* Greatly improved widget styles and UI when using Elementor.
* Overall third party page builder improvements.
* Move scripts and styles into new Scripts class.
* Introduced a Singleton trait.

= 7.6.0 =
* Elementor support for multiple widgets of the same type on the same page.
* Automatically increment widget ids under any cases where they would duplicate.
* Bump required WordPress Core version to 4.8.0.

= 7.5.0 =
* Convert "Always display child pages" to use our List_Pages structure and support all widget options.
* Bump required PHP version to 5.4.4.

== Upgrade Notice ==
= 8.6.0 = 
Update to support WordPress version 5.8.

= 8.5.0 =
Update to support PRO version 8.4.0.
