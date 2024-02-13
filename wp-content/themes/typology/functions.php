<?php

/* Define Theme Vars */
define( 'TYPOLOGY_THEME_VERSION', '1.7.6' );

/* Define content width */
if ( !isset( $content_width ) ) {
	$content_width = 720;
}


/* Localization */
load_theme_textdomain( 'typology', get_parent_theme_file_path( '/languages' ) );


/* After theme setup main hook */
add_action( 'after_setup_theme', 'typology_theme_setup' );

/**
 * After Theme Setup
 *
 * Callback for after_theme_setup hook
 *
 * @since  1.0
 */

function typology_theme_setup() {

	/* Add thumbnails support */
	add_theme_support( 'post-thumbnails' );

	/* Add theme support for title tag */
	add_theme_support( 'title-tag' );

	/* Add image sizes */
	$image_sizes = typology_get_image_sizes();
	if ( !empty( $image_sizes ) ) {
		foreach ( $image_sizes as $id => $size ) {
			add_image_size( $id, $size['w'], $size['h'], $size['crop'] );
		}
	}

	/* Support for HTML5 */
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

	add_theme_support( 'customize-selective-refresh-widgets' );

	/* Automatic Feed Links */
	add_theme_support( 'automatic-feed-links' );

	/* WooCommerce support */
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	/* Load editor styles */
	add_theme_support( 'editor-styles' );

	/* Support for alignwide elements */
	add_theme_support( 'align-wide' );

	/* Support for responsive embeds */
	add_theme_support( 'responsive-embeds' );

	/* Support for predefined colors in editor */
	add_theme_support( 'editor-color-palette', typology_get_editor_colors() );

	/* Support for predefined font-sizes in editor */
	add_theme_support( 'editor-font-sizes', typology_get_editor_font_sizes() );

	// load admin styles
	if ( is_admin() ) {
		typology_load_editor_styles();
	}

	// Remove 5.8 widget block editor
	remove_theme_support( 'widgets-block-editor' );	

}


/* Helpers and utility functions */
include_once get_parent_theme_file_path( '/core/helpers.php' );

/* Default options */
include_once get_parent_theme_file_path( '/core/default-options.php' );

/* Load frontend scripts */
include_once get_parent_theme_file_path( '/core/enqueue.php' );

/* Template functions */
include_once get_parent_theme_file_path( '/core/template-functions.php' );

/* Menus */
include_once get_parent_theme_file_path( '/core/menus.php' );

/* Sidebars */
include_once get_parent_theme_file_path( '/core/sidebars.php' );

/* Extensions (hooks and filters to add/modify specific features ) */
include_once get_parent_theme_file_path( '/core/extensions.php' );


if ( is_admin() ) {

	/* Admin helpers and utility functions  */
	include_once get_parent_theme_file_path( '/core/admin/helpers.php' );

	/* Load admin scripts */
	include_once get_parent_theme_file_path( '/core/admin/enqueue.php' );

	/* Theme Options */
	include_once get_parent_theme_file_path( '/core/admin/options.php' );

	/* Include plugins - TGM */
	include_once get_parent_theme_file_path( '/core/admin/plugins.php' );

	/* Include AJAX action handlers */
	include_once get_parent_theme_file_path( '/core/admin/ajax.php' );

	/* Extensions ( hooks and filters to add/modify specific features ) */
	include_once get_parent_theme_file_path( '/core/admin/extensions.php' );

	/* Demo importer panel */
	include_once get_parent_theme_file_path( '/core/admin/demo-importer.php' );

	/* Metaboxes */
	include_once get_parent_theme_file_path( '/core/admin/metaboxes.php' );

}


?>