=== Advanced Sidebar Menu ===

Contributors: Mat Lipe, onpointplugins
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40onpointplugins%2ecom&lc=US&item_name=Advanced%20Sidebar%20Menu&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: block, widget, dynamic, hierarchy, menus, sidebar menu, category, pages, parent, child, automatic
Requires at least: 6.0.0
Tested up to: 6.4.1
Requires PHP: 7.2.0
Stable tag: 9.4.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

<h3>Fully automatic sidebar menus.</h3>

Uses the parent/child relationship of your pages or categories to generate menus based on the current section of your site. Assign a page or category to a parent and this will do the rest for you.

Keeps the menu clean and usable. Only related items display, so you don't have to worry about keeping a custom menu up to date or displaying links to items that don't belong.

Widgets and blocks are available to display menus where you need them. Look for the "Advanced Sidebar - Pages" widget or block, or the "Advanced Sidebar - Categories" widget or block.

<strong>Check out <a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/?utm_source=readme&utm_campaign=gopro&utm_medium=dot-org">Advanced Sidebar Menu PRO</a> for more features including accordion menus, menu colors and styles, custom link text, excluding of pages, category ordering, custom post types, custom taxonomies, priority support, and so much more!</strong>

<blockquote><a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/?utm_source=readme&utm_campaign=gopro&utm_medium=dot-org" target="_blank">PRO version 9.2.0</a> is now available with the ability to exclude pages or categories using intuitive search and select!</blockquote>

<h3>Features</h3>
* Page and Category menu Gutenberg blocks.
* Page and Category menu widgets.
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
* Navigation menu Gutenberg block.
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
* Ability to display category post counts.
* Exclude pages or categories using intuitive search and select. **NEW**
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

<h3>Documentation</h3>
The official documentation for the plugin <a target="_blank" href="https://onpointplugins.com/advanced-sidebar-menu/">may be found here</a>.

<h3>Developers</h3>
Developer docs <a target="_blank" href="https://onpointplugins.com/advanced-sidebar-menu/developer-docs/">may be found here</a>.

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
4. Example of a category menu ordered by title using the 2017 theme and default styles.


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
= 9.4.1 =
* Dropped support for PHP 7.0 in favor of 7.2.
* Bumped minimum supported WordPress version to 6.0.0.
* Improved block preview link handling.
* Required PRO version 9.1.8+.

= 9.3.4 =
* Passed block attributes and supports to JavaScript as well as PHP.
* Fixed issues with block previews in WordPress 6.4.
* Fixed issues with widget transformations in WordPress 6.4.
* Improved styles of legacy widget's info panel.
* Updated PHPStan level to 8 and fixed all warnings.

= 9.3.3 =
* Fixed issue with blocks loading in WordPress 6.4.
* Tested to WordPress 6.4.1.

= 9.3.2 =
* Fixed deployignore configurations.

= 9.3.1 =
* Improved select input styles in widget settings.
* Introduced 'advanced-sidebar-menu/scripts/admin-scripts' action.
* Aligned supported browsers with WordPress core.
* Updated the Node to version 18.
* Fixed all ESLint and Stylelint notices.
* Loosely a requirement for PRO version 9.3.0.

= 9.3.0 =
* Modernized legacy widget JS and CSS into the Webpack structure.
* Reduce bundle size of admin resources.
* Renamed CSS files with the "advanced-sidebar-menu" prefix.
* Added support for collapsing info panels on page builders and theme customizers.
* Updated the block `apiVersion` to "3".
* Tested to WordPress 6.3.2.
* Greatly improved widgets styles in Beaver Builder and Elementor.

= 9.2.1 =
* Introduced transform legacy widgets to blocks prompts.
* Moved configurations to the root of the plugin and general modernization.
* Made "Go PRO" callouts more tasteful and less intrusive.
* Improved styles of the info panels in widgets and blocks.
* Improved styles of buttons and fields in Beaver Builder.
* Updated WP-PHPCS to version 3 and fixed all warnings.

= 9.1.0 =
* Separated the exclude pages/categories field into a filterable component.
* Updated links to various documentation.
* Added default values to most block attributes.
* Updated block attribute TS definitions to accurately reflect default values.
* Updated TS to version 5.
* Updated PHPCS scanning to version 3 of WP PHPCS standards.
* Loosely update the minimum requirement for PRO to version 9.2.0.

= 9.0.11 =
* Changed default limit of child pages to 200 instead of 100.
* Simplified and improved the `List_Pages::parse_args` method.
* Added links to documentation in the plugins list.
* Tested to WordPress core 6.3.1.

= 9.0.10 =
* Fixed compatibility with Jetpack widget visibility.
* Included the screen and section information in ErrorBoundary data.
* Improved static analysis testing.
* Improved unit testing for WordPress 5.8.
* Tested to WordPress core 6.3.0.

= 9.0.9 =
* Fixed conflict with [Stackable WordPress plugin](https://wordpress.org/plugins/stackable-ultimate-gutenberg-blocks/) in the theme customizer.
* Improved block script reliability.
* Improved handling of Elementor previews.
* Improved error boundary informational messages.
* Removed conflicts with POST method requests.

= 9.0.8 =
* Disabled legacy widgets by default [see docs]( https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-gutenberg-blocks/#enable-widgets).
* Update browser list support.
* Improved the widget/block transformation logic and types.
* Removed dangling WordPress version < 5.6 requirements.
* Tested to WordPress core 6.2.2.

= 9.0.7 =
* Fixed `data-level` on category menus larger than 3 levels.
* Included classic widgets flag in debug information.
* Required PRO version 9.1.2+.

= 9.0.6 =
* Improved extendability by removing all `private` access modifiers.
* Fully support PHP 8.1.
* Tested to WordPress Core 6.2.0.

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

== Upgrade Notice ==
= 9.3.4 =
Update to support WordPress 6.4.

= 9.1.0 =
Update to support PRO version 9.2.0.

= 9.0.1 =
Introducing <a href="https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-gutenberg-blocks/">Gutenberg blocks</a>.
