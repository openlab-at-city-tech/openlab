=== Require Post Category ===
Contributors: joshhartman
Tags: post, category, require, force, publish, draft, admin
Requires at least: 3.3
Tested up to: 4.7
Stable tag: 1.0.7

Require users to choose a post category before saving a draft, updating a post, or publishing a post.

== Description ==

Tired of uncategorized posts? Use this simple plugin to require users to choose a post category before saving a draft, updating a post, or publishing a post.  This applies to normal posts and is not compatible with custom write panels or custom post types without modification.

= Translators =

* French (fr_FR) - Dominique V.
* Spanish (es_ES) - [Andrew Kurtis - WebHostingHub](http://www.webhostinghub.com)
* Polish (pl_PL) - Michał Papliński

If you have created your own language pack, or have an update of an existing one, you can send the [PO and MO files](https://codex.wordpress.org/Translating_WordPress) to [me](https://www.warpconduit.net/contact) so that I can bundle it into the plugin. [Download the latest POT file](https://plugins.svn.wordpress.org/require-post-category/trunk/languages/require-post-category.pot).

== Installation ==

1. Extract the `require-post-category` folder to your `wp-content/plugins` directory
1. Activate the plugin through the admin interface

== Frequently Asked Questions ==

= Are there any settings I can adjust? =

Nope, just install and activate, that's it!

= Have a question that is not addressed here? =

Visit this plugin's WordPress support forum at https://wordpress.org/support/plugin/require-post-category

== Screenshots ==

1. Alert appears when you try to save a post without choosing a category

== Changelog ==

= 1.0.7 =
* Moved JavaScript to separate file and updated to use `wp_enqueue_script` and `wp_localize_script`

= 1.0.6 =
* Added French (fr_FR) translation by Dominique V.

= 1.0.5 =
* Added Polish (pl_PL) translation by Michał P.

= 1.0.4 =
* Added Spanish (es_ES) translation by Andrew K.

= 1.0.3 =
* Added i18n support

= 1.0.2 =
* Updated for WordPress 3.6

= 1.0.1 =
* FIXED: Disabled script when adding/editing pages

= 1.0 =
* First stable release
