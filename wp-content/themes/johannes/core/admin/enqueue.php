<?php

/* Load admin scripts and styles */
add_action( 'admin_enqueue_scripts', 'johannes_load_admin_scripts' );


/**
 * Load scripts and styles in admin
 *
 * It just wrapps two other separate functions for loading css and js files in admin
 *
 * @since  1.0
 */

function johannes_load_admin_scripts() {
	johannes_load_admin_css();
	johannes_load_admin_js();
}


/**
 * Load admin css files
 *
 * @since  1.0
 */

function johannes_load_admin_css() {

	global $pagenow, $typenow;

	//Load minor admin style tweaks
	wp_enqueue_style( 'johannes-global', get_parent_theme_file_uri( '/assets/css/admin/global.css' ), false, JOHANNES_THEME_VERSION );
}


/**
 * Load admin js files
 *
 * @since  1.0
 */

function johannes_load_admin_js() {

	global $pagenow, $typenow;

	//Load global js
	wp_enqueue_script( 'johannes-global', get_parent_theme_file_uri( '/assets/js/admin/global.js' ) , array( 'jquery' ), JOHANNES_THEME_VERSION );

	//Load category JS
	if ( in_array( $pagenow, array( 'edit-tags.php', 'term.php' ) ) && isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] == 'category' ) {
	 wp_enqueue_media();
	 wp_enqueue_script( 'johannes-category', get_parent_theme_file_uri( '/assets/js/admin/metaboxes-category.js' ), array( 'jquery' ), JOHANNES_THEME_VERSION );
	}

	//Load post & page js
	if ( $typenow == 'page' && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
	  wp_enqueue_script( 'johannes-page', get_parent_theme_file_uri( '/assets/js/admin/metaboxes-page.js' ), array( 'jquery' ), JOHANNES_THEME_VERSION );
	  wp_localize_script( 'johannes-page', 'johannes_js_settings', johannes_get_admin_js_settings() );
	}


	//Load widgets JS`
	if ( $pagenow == 'widgets.php' ) {
	 wp_enqueue_script( 'johannes-widgets', get_parent_theme_file_uri( '/assets/js/admin/widgets.js' ), array( 'jquery', 'jquery-ui-sortable' ), JOHANNES_THEME_VERSION );
	}


}

/**
 * Load editor styles
 *
 * @since  1.0
 */

function johannes_load_editor_styles() {

	if ( $fonts_link = johannes_generate_fonts_link() ) {
		add_editor_style( $fonts_link );
	}

	add_editor_style( get_parent_theme_file_uri( '/assets/css/admin/editor-style.css' ) );

}

/**
 * Load dynamic editor styles
 *
 * @since  1.0
 */

add_action( 'enqueue_block_editor_assets', 'johannes_block_editor_styles', 99 );

function johannes_block_editor_styles() {
	
	wp_register_style( 'johannes-editor-styles', false, JOHANNES_THEME_VERSION );

	wp_enqueue_style( 'johannes-editor-styles');
	wp_add_inline_style( 'johannes-editor-styles', johannes_generate_dynamic_editor_css() );

}