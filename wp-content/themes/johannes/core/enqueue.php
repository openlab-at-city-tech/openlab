<?php

/* Load frontend scripts and styles */
add_action( 'wp_enqueue_scripts', 'johannes_load_scripts' );

/**
 * Load scripts and styles on frontend
 *
 * It just wraps two other separate functions for loading css and js files
 *
 * @since  1.0
 */

function johannes_load_scripts() {
	johannes_load_css();
	johannes_load_js();
}

/**
 * Load frontend css files
 *
 * @since  1.0
 */

function johannes_load_css() {

	//Load fonts
	if ( $fonts_link = johannes_generate_fonts_link() ) {
		wp_enqueue_style( 'johannes-fonts', $fonts_link, false, JOHANNES_THEME_VERSION );
	}

	//Check if is minified option active and load appropriate files
	if ( johannes_get_option( 'minify_css' ) ) {
		wp_enqueue_style( 'johannes-main', get_parent_theme_file_uri( '/assets/css/min.css' ) , false, JOHANNES_THEME_VERSION );
	} else {

		$styles = array(
			'iconfont' => 'iconfont.css',
			'photoswipe' => 'photoswipe.css',
			'photoswipe-skin' => 'photoswipe-default-skin.css',
			'owl-carousel' => 'owl-carousel.css',
			'main' => 'main.css'
		);

		foreach ( $styles as $id => $style ) {
			wp_enqueue_style( 'johannes-' . $id, get_parent_theme_file_uri( '/assets/css/'. $style ) , false, JOHANNES_THEME_VERSION );
		}
	}


	//Append dynamic css
	wp_add_inline_style( 'johannes-main', johannes_generate_dynamic_css() );


	//Woocomerce styles
	if ( johannes_is_woocommerce_active() ) {
		wp_enqueue_style( 'johannes-woocommerce', get_parent_theme_file_uri( '/assets/css/johannes-woocommerce.css' ), array( 'johannes-main' ), JOHANNES_THEME_VERSION );
		wp_dequeue_style( 'photoswipe-default-skin' );
	}

	//Load RTL css
	if ( johannes_is_rtl() ) {
		wp_enqueue_style( 'johannes-rtl', get_parent_theme_file_uri( '/assets/css/rtl.css' ), array( 'johannes-main' ), JOHANNES_THEME_VERSION );
	}

}


/**
 * Load frontend js files
 *
 * @since  1.0
 */

function johannes_load_js() {

	//Check if is minified option active and load appropriate files
	if ( johannes_get_option( 'minify_js' ) ) {

		wp_enqueue_script( 'johannes-main', get_parent_theme_file_uri( '/assets/js/min.js' ) , array( 'jquery', 'jquery-masonry', 'imagesloaded' ), JOHANNES_THEME_VERSION, true );

	} else {

		$scripts = array(
			'photoswipe' => 'photoswipe.js',
			'photoswipe-ui' => 'photoswipe-ui-default.js',
			'owl-carousel' => 'owl-carousel.js',
			'sticky-kit' => 'sticky-kit.js',
			'object-fit' => 'ofi.js',
			'picturefill' => 'picturefill.js',
			'main' => 'main.js'
		);

		foreach ( $scripts as $id => $script ) {
			wp_enqueue_script( 'johannes-'.$id, get_parent_theme_file_uri( '/assets/js/'. $script ), array( 'jquery', 'jquery-masonry', 'imagesloaded' ), JOHANNES_THEME_VERSION, true );
		}
	}

	//Load JS settings object
	wp_localize_script( 'johannes-main', 'johannes_js_settings', johannes_get_js_settings() );

	//Load comment reply js
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}


/**
 * Load customizer/preview js files
 *
 * @since  1.0
 */

add_action( 'customize_preview_init', 'johannes_preview_js' );
//add_action( 'customize_controls_print_scripts', 'johannes_preview_js' );


function johannes_preview_js() {
	
  	wp_enqueue_script( 'johannes-customizer', get_parent_theme_file_uri( '/assets/js/admin/customizer.js' ), array( 'customize-preview', 'jquery' ), JOHANNES_THEME_VERSION, true );
}

?>