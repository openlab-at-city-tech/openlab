<?php

/* Load admin scripts and styles */
add_action( 'admin_enqueue_scripts', 'typology_load_admin_scripts' );


/**
 * Load scripts and styles in admin
 *
 * It just wrapps two other separate functions for loading css and js files in admin
 *
 * @since  1.0
 */

function typology_load_admin_scripts() {
	typology_load_admin_css();
	typology_load_admin_js();
}


/**
 * Load admin css files
 *
 * @since  1.0
 */

function typology_load_admin_css() {

	global $pagenow, $typenow;

	//Load small admin style tweaks
	wp_enqueue_style( 'typology-global', get_parent_theme_file_uri( '/assets/css/admin/global.css' ), false, TYPOLOGY_THEME_VERSION, 'screen, print' );
}


/**
 * Load admin js files
 *
 * @since  1.0
 */

function typology_load_admin_js() {

	global $pagenow, $typenow;

	wp_enqueue_script( 'typology-global', get_parent_theme_file_uri( '/assets/js/admin/global.js' ), array( 'jquery' ), TYPOLOGY_THEME_VERSION );

}

/**
 * Load editor styles
 *
 * @since  1.0
 */

function typology_load_editor_styles() {

	if ( $fonts_link = typology_generate_fonts_link() ) {
		add_editor_style( $fonts_link );
	}

	add_editor_style( get_parent_theme_file_uri( '/assets/css/admin/editor-style.css' ) );

}


/**
 * Load dynamic editor styles
 *
 * @since  1.0
 */

add_action( 'enqueue_block_editor_assets', 'typology_block_editor_styles', 99 );

function typology_block_editor_styles() {

	wp_register_style( 'typology-editor-styles', false, TYPOLOGY_THEME_VERSION );
	wp_enqueue_style( 'typology-editor-styles');

	wp_add_inline_style( 'typology-editor-styles', typology_generate_dynamic_editor_css() );

}
