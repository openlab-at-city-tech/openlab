=== Require Post Category ===
Contributors: joshhartman
Tags: post, category, tags, taxonomy, require, force, publish, save, admin, gutenberg, editor
Requires at least: 5.5
Tested up to: 5.5
Stable tag: 2.1

Require users to choose a post category before updating or publishing a post.

== Description ==

Tired of uncategorized posts? Use this simple plugin to require users to choose a post category before updating or publishing a post. By default this only applies to normal posts. If you wish to require a category/taxonomy for a custom post type see the FAQ for filter hook usage examples.

= Translators =

* French (fr_FR) - Dominique V.
* Spanish (es_ES) - [Andrew Kurtis - WebHostingHub](http://www.webhostinghub.com)
* Polish (pl_PL) - Michał Papliński
* Finnish (fi) - Sanapaino

If you have created your own language pack, or have an update of an existing one, you can send the [PO and MO files](https://codex.wordpress.org/Translating_WordPress) to [me](https://www.warpconduit.net/contact) so that I can bundle it into the plugin. [Download the latest POT file](https://plugins.svn.wordpress.org/require-post-category/trunk/languages/require-post-category.pot).

== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New'
2. Search for 'require post category' and click the Install button
3. Activate Require Post Category from your Plugins page.

= From WordPress.org =

1. Download Require Post Category.
2. Upload the 'require-post-category' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate Require Post Category from your Plugins page.

== Frequently Asked Questions ==

= Are there any settings I can adjust? =

Nope, just install and activate, that's it!

= How do I use this for custom post types and/or custom taxonomies? =

Use the `rpc_post_types` filter hook in your theme's `functions.php` or a must-use plugin.

Usage examples:

`function custom_rpc_post_types( $post_types ) {
	// Add a key to the $post_types array for each post type and list the slugs of the taxonomies you wish to require

	// Simplest usage
	$post_types['book'] = array( 'genre' );

	// Multiple taxonomies
	$post_types['recipe'] = array( 'cookbook_category', 'geographic_origin', 'flavor_tags' );

	// Set your own alert message for each taxonomy, or let the plugin generate the alert message
	$post_types['inventory'] = array(
		// Let the plugin generate a relevant alert message
		'manufacturer',
		// Or specify a custom alert message
		'inventory_category' => array(
			'message' => "Please choose a category for this fine inventory item."
		)
	);

	// Always return $post_types after your modifications
	return $post_types;
}

add_filter( 'rpc_post_types', 'custom_rpc_post_types' );`

The default `$post_types` contains the following:

`$post_types['post'] = array(
 	'category' => array(
 		'message' => 'Please select a category before publishing this post.'
 	)
);`

This maintains the plugin's original functionality. However, you can remove this functionality with `unset($post_types['post']);` or by redefining `$post_types` in your hook function.

= You’ve added code to require a custom taxonomy but can’t get it to work in the Gutenberg editor? =

One reason may be that your custom taxonomy does not have the `show_in_rest` argument enabled. Without it enabled the Gutenberg editor cannot access it.

= Have a question that is not addressed here? =

Visit this plugin's WordPress support forum at https://wordpress.org/support/plugin/require-post-category

== Screenshots ==

1. Notice appears when editing a post without selecting a category (Gutenberg)
2. Alert appears when you try to save a post without selecting a category (Classic)

== Changelog ==

= 2.1 =
* Updated for WordPress 5.5
* FIXED: Deprecated function notice listed in jQuery Migrate Helper plugin (Classic Editor)
* FIXED: Submitting post form by pressing enter in the title field would bypass checks (Classic Editor)

= 2.0.3 =
* Added Finnish (fi) translation by Sanapaino

= 2.0.2 =
* FIXED: Custom taxonomy `rest_base` value of boolean false was not being properly handled.

= 2.0.1 =
* Repaired translation files.

= 2.0 =
* Rewritten for the new Gutenberg editor (first introduced in WordPress 5.0)

= 1.1 =
* NEW: Added a filter hook and related code to allow developers to easily add support for custom post types and custom taxonomies
* Updated to meet WordPress PHP coding standards

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
