=== Easy Custom Sidebars ===
Contributors: sunny_johal, amit_kayasth
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=28M8NWPHVQNYU
Tags: custom sidebars, unlimited sidebars, replace sidebars, dynamic sidebar, create sidebars, sidebar replacement, sidebar manager, widget area manager, widget area replacement, unlimited sidebar generator, custom widget areas, wordpress multiple sidebars, sidebar plugin for wordpress, wordpress sidebar plugin,
Requires at least: 4.8
Tested up to: 4.8
Stable tag: 1.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to replace any sidebar/widget area in your theme without writing a single line of code!


== Description ==
> **Our new WordPress theme is almost ready!** Want to know when we launch? Awesome! [Visit our website](http://www.titaniumthemes.com) and enter your details and we will e-mail you as soon as we are ready :)

For a quick video demo please view the [vimeo screencast](https://vimeo.com/titaniumthemes/easy-custom-sidebars).

The documentation for this plugin can be found [here](http://titaniumthemes.com/easy-custom-sidebars/).

[Follow us on twitter!](https://twitter.com/titaniumthemes)

If you have found this plugin useful please [donate here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=28M8NWPHVQNYU)

A simple and easy way to replace any sidebar or widget area in any WordPress theme without coding. This plugin integrates with the WordPress Customizer so you can preview your custom widget areas on your site in realtime. It's compatible with any theme and you can even replace more than one widget area on the same page!

= What does this plugin do? =

* This plugin allows you to **replace any sidebar/widget area** in any WordPress theme (no coding required).
* You are able to **replace multiple sidebars/widget areas on the same page**.
* It allows you to **manage your custom sidebar replacements** in the WordPress Admin area.
* Allows you to **apply the same sidebar replacement across all posts / all pages / all taxonomies / all custom post types** with the click of a button.
* It also **automatically detects any type of content in your theme:** e.g. custom post types / pages / posts and allows you to replace any widget areas that exist on those pages.
* **Automatic theme style detection:** detects your themes styles and styles any custom sidebar replacements to match your theme styles. (no CSS styling required).
* **New: Live customizer widget preview**. Your custom sidebars are now available in the WordPress Customizer allowing you to add widgets in realtime.

= Plugin Features =

* **Create Unlimited Custom Sidebar Replacements:** Replace widget areas on any page on your website.
* **Activate/Deactivate Custom Sidebars** with the click of a button.
* **Works with any WordPress Theme** that has any widget areas.
* **Core WordPress Design:** the admin area looks like its part of WordPress (adopts the new Nav Menu UI style used in WordPress 3.6+).
* **Ajax Search:** Easily find the page/post that contains the widget area you are looking to replace for without refreshing the page.
* **Customise the Admin Page:** Show/Hide metabox elements on the admin page with the click of a button.
* **Translation Ready:** MO and PO files are included.

= New: WordPress Customizer Widget Integration =

We have created the first sidebar manager that integrates into the new WordPress widget customizer. Now all of your custom widget areas are available in the WordPress customizer complete with live preview.

* **Manage your sidebars in realtime:** The first plugin that is integrated with the new live widget previewer in the customizer, allowing you to **make changes to your widget area and see the results instantly**.

= Who is this Plugin ideal for? =
* Anyone who is looking for an easy way to replace sidebars / widget areas without coding.
* Theme Authors: you can use this pluin to add unlimited sidebar functionality to your theme.
* Great for use on client projects or for use on existing websites with limited sidebars.


= Developer Features =
**Please note:** We are currently working on producing in-depth documentation for theme developers which will be available shortly.

* **Cross Browser and Backwards Browser Compatible** (Tested in all major browsers).
* Already tested and works in WordPress 4.0.
* **Seamless WordPress Integration:**  Live preview is integrated into the WordPress Customizer and the settings page follows core WordPress design guidelines.
* Uses the WordPress **Options API** to store and retrieve options.
* **Highly Secure:** Checks user permissions, uses nonces and the WordPress Security API.
* Uses the Screen Options API and Ajax to allow you to easily show/hide metaboxes on the admin page.
* **Rock solid, Robust Code:** handcoded, tested and beautifully formatted in Sublime Text.
* **Uses WP Ajax** for an enhanced admin experience.
* Fully Commented Source Code.
* Strong Usability Testing.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Download the plugin
1. Unzip the package and upload to your /wp-content/plugins/ directory or upload in the admin area.
1. Log into WordPress and navigate to the "Plugins" panel.
1. Activate the plugin labeled "Easy Custom Sidebars".
1. You're done!

== Screenshots ==

1. Here is a theme with the default sidebar, we can replace this sidebar without writing a single line of code.
2. Here is the same page but now we have replaced the default sidebar with our own. You can apply widget area replacements to a single page or across all pages/categories etc.
3. Create new sidebars quickly in an easy to use admin interface.
4. Choose where to apply your replacement. Show your sidebar on any/all posts/pages/categories/templates (and much more!).
5. Instant ajax search allowing you to quickly find the post/page/category that you want to apply this replacement to.
6. Manage the widgets for your custom sidebar on the native widgets screen. Integrates seamlessly with the WordPress admin area.
7. Seamlessly integrates into the customizer so that you can manage your sidebars in realtime!
8. Seamlessly integrates into the customizer so that you can manage your sidebars in realtime!
9. Show/hide elements on the admin screen (Uses the WordPress Screen Options API)
10. Quickly manage all of your custom sidebars on the manage sidebar replacements screen.

== Changelog ==
= 1.0.9 - Update for new WordPress Version =
* Updated CSS for the Admin UI.

= 1.0.8 - Update for new WordPress Version =
* Fixed compatibility issue with WordPress 4.7 and the save_posts action.

= 1.0.7 - Update for new WordPress Version =
* Fixed visual display issues on the settings page.

= 1.0.6 - Minor Bug Fix =
* Fixed issue where pages with ampersands in the title weren't being added to a custom sidebar.

= 1.0.5 - Bug Fix =
* Fixed issue where sidebar was not displaying for post type archives.

= 1.0.4 - Feature Update and Bug Fix =
* New Feature: Can add sidebars for author archive pages.
* The sidebar now persists on empty taxonomy pages.
* The sidebar now persists when there are no posts in search results.
* Fixed issue whereby taxonomy names with ampersands weren't being added to the sidebar (had to manually esc_html for get_term_by function).
* Removed dependancy for global $post object and use the get_queried_object() function when looking for a replacement.

= 1.0.3 - Admin Capability Check =
* Prevents an error from being triggered in admin when a non-admin user doesn't have the required capability to use the plugin.

= 1.0.2 - Important Update =
* Addressed potential security issue on the plugin admin page - Hardened security and escaped any attributes passed via the URL throughout the plugin.

= 1.0.1 =
* Updated translation method.

= 1.0 =
* First plugin release.

== Upgrade Notice ==
Nothing to see here...

== Frequently Asked Questions ==
For a quick video demo please view the [vimeo screencast](https://vimeo.com/titaniumthemes/easy-custom-sidebars).

The documentation for this plugin can be found [here](http://titaniumthemes.com/easy-custom-sidebars/).

== Credits and Donation ==

If you have found this plugin useful please [donate here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=28M8NWPHVQNYU)
