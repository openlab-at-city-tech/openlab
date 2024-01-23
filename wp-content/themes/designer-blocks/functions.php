<?php
/**
 * Rainfall functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package designer-blocks
 * @since 1.0.0
 */


if ( ! function_exists( 'designer_blocks_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function designer_blocks_support() {

		// Enqueue editor styles.
		add_editor_style( 'style.css' );
		
		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Make theme available for translation.
		load_theme_textdomain( 'designer_blocks' );
	}

endif;

add_action( 'after_setup_theme', 'designer_blocks_support' );

if ( ! function_exists( 'designer_blocks_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function designer_blocks_styles() {

		// Register theme stylesheet.
		wp_register_style(
			'creative-blocks-style',
			get_template_directory_uri() . '/style.css',
			array(),
			wp_get_theme()->get( 'Version' )
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'designer-blocks-style' );

	}

endif;

add_action( 'wp_enqueue_scripts', 'designer_blocks_styles' );
