<?php

/* Load frontend scripts and styles */
add_action( 'wp_enqueue_scripts', 'typology_load_scripts' );

/**
 * Load scripts and styles on frontend
 *
 * It just wraps two other separate functions for loading css and js files
 *
 * @since  1.0
 */

function typology_load_scripts() {
	typology_load_css();
	typology_load_js();
}

/**
 * Load frontend css files
 *
 * @since  1.0
 */

function typology_load_css() {

	//Load google fonts
	if ( $fonts_link = typology_generate_fonts_link() ) {
		wp_enqueue_style( 'typology-fonts', $fonts_link, false, TYPOLOGY_THEME_VERSION );
	}

	//Check if is minified option active and load appropriate files
	if ( typology_get_option( 'minify_css' ) ) {

		wp_enqueue_style( 'typology-main', get_parent_theme_file_uri( '/assets/css/min.css' ), false, TYPOLOGY_THEME_VERSION );

	} else {

		$styles = array(
			'font-awesome' => 'font-awesome.css',
			'normalize' => 'normalize.css',
			'magnific-popup' => 'magnific-popup.css',
			'owl-carousel' => 'owl-carousel.css',
			'main' => 'main.css'
		);

		foreach ( $styles as $id => $style ) {
			wp_enqueue_style( 'typology-' . $id, get_parent_theme_file_uri( '/assets/css/' . $style ), false, TYPOLOGY_THEME_VERSION );
		}
	}

	//Append dynamic css
	wp_add_inline_style( 'typology-main', typology_generate_dynamic_css() );


	//Load RTL css
	if ( typology_is_rtl() ) {
		wp_enqueue_style( 'typology-rtl', get_parent_theme_file_uri( '/assets/css/rtl.css' ), array( 'typology-main' ), TYPOLOGY_THEME_VERSION );
	}

	//Load WooCommerce css
	if ( typology_is_woocommerce_active() ) {
		wp_enqueue_style( 'typology-woocommerce', get_parent_theme_file_uri( '/assets/css/typology-woocommerce.css' ), array( 'typology-main' ), TYPOLOGY_THEME_VERSION );
	}

	//Do not load font awesome from our shortcodes plugin
	wp_dequeue_style( 'mks_shortcodes_fntawsm_css' );

}


/**
 * Load frontend js files
 *
 * @since  1.0
 */

function typology_load_js() {

	//Load comment reply js
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	//Check if is minified option active and load appropriate files
	if ( typology_get_option( 'minify_js' ) ) {

		wp_enqueue_script( 'typology-main', get_parent_theme_file_uri( '/assets/js/min.js' ), array( 'jquery', 'imagesloaded' ), TYPOLOGY_THEME_VERSION, true );

	} else {

		$scripts = array(
			'magnific-popup' => 'magnific-popup.js',
			'fitvids' => 'fitvids.js',
			'owl-carousel' => 'owl-carousel.js',
			'main' => 'main.js'
		);

		foreach ( $scripts as $id => $script ) {
			wp_enqueue_script( 'typology-'.$id, get_parent_theme_file_uri( '/assets/js/'. $script ), array( 'jquery', 'imagesloaded' ), TYPOLOGY_THEME_VERSION, true );
		}
	}

	wp_localize_script( 'typology-main', 'typology_js_settings', typology_get_js_settings() );
}
?>
