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
	wp_enqueue_style( 'typology-global', get_template_directory_uri() . '/assets/css/admin/global.css', false, TYPOLOGY_THEME_VERSION, 'screen, print' );
}


/**
 * Load admin js files
 *
 * @since  1.0
 */

function typology_load_admin_js() {

	global $pagenow, $typenow;

	wp_enqueue_script( 'typology-global', get_template_directory_uri().'/assets/js/admin/global.js', array( 'jquery' ), TYPOLOGY_THEME_VERSION );

	if( $pagenow == 'widgets.php' ){
		wp_enqueue_script( 'typology-widgets', get_template_directory_uri().'/assets/js/admin/widgets.js', array( 'jquery', 'jquery-ui-sortable'), TYPOLOGY_THEME_VERSION );
	}

}


/**
 * Load editor styles
 * 
 * @since  1.2
 */

function typology_load_editor_styles() {	

	add_editor_style( array(
            get_template_directory_uri() . '/assets/css/editor-style.css',
            typology_generate_fonts_link(),
            add_query_arg( 'action', 'typology_dynamic_editor_styles', admin_url( 'admin-ajax.php' ) ),
        )
	);
}

add_action( 'wp_ajax_typology_dynamic_editor_styles', 'typology_dynamic_editor_styles' );

function typology_dynamic_editor_styles() {
	header("Content-type: text/css");
	echo typology_generate_dynamic_css();
    wp_die();
}

?>