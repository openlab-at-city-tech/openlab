<?php

/* Define Theme Vars */
define( 'TYPOLOGY_THEME_VERSION', '1.5.3' );

/* Define content width */
if ( !isset( $content_width ) ) {
	$content_width = 720;
}

/* Localization */
load_theme_textdomain( 'typology', get_template_directory()  . '/languages' );


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

	/* Allow custom editor color picker only */
	add_theme_support( 'editor-color-palette', array() );

	/* Load editor styles */
	if ( is_admin() && !typology_get_option('disable_editor_style') ) {
		typology_load_editor_styles();
	}

}


/* Helpers and utility functions */
include_once get_template_directory() . '/core/helpers.php';

/* Load frontend scripts */
include_once get_template_directory() . '/core/enqueue.php';

/* Template functions */
include_once get_template_directory() . '/core/template-functions.php';

/* Menus */
include_once get_template_directory() . '/core/menus.php';

/* Sidebars */
include_once get_template_directory() . '/core/sidebars.php';

/* Widgets */
include_once get_template_directory() . '/core/widgets.php';

/* Extensions (hooks and filters to add/modify specific features ) */
include_once get_template_directory() . '/core/extensions.php';


if ( is_admin() ) {

	/* Admin helpers and utility functions  */
	include_once get_template_directory() . '/core/admin/helpers.php';

	/* Load admin scripts */
	include_once get_template_directory() . '/core/admin/enqueue.php';

	/* Theme Options */
	include_once get_template_directory() . '/core/admin/options.php';

	/* Include plugins - TGM */
	include_once get_template_directory() . '/core/admin/plugins.php';

	/* Include AJAX action handlers */
	include_once get_template_directory() . '/core/admin/ajax.php';

	/* Extensions ( hooks and filters to add/modify specific features ) */
	include_once get_template_directory() . '/core/admin/extensions.php';

	/* Demo importer panel */
	include_once get_template_directory() . '/core/admin/demo-importer.php';
	
	/* Metaboxes */
	include_once get_template_directory() . '/core/admin/metaboxes.php';

}


?>
