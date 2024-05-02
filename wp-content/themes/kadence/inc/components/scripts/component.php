<?php
/**
 * Kadence\Scripts\Component class
 *
 * @package kadence
 */

namespace Kadence\Scripts;

use Kadence\Component_Interface;
use function Kadence\kadence;
use WP_Post;
use function add_action;
use function add_filter;
use function wp_enqueue_script;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_script_add_data;
use function wp_localize_script;

/**
 * Class for adding scripts to the front end.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'scripts';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ie_11_support_scripts' ), 60 );
	}
	/**
	 * Add some very basic support for IE11
	 */
	public function ie_11_support_scripts() {
		if ( apply_filters( 'kadence_add_ie11_support', false ) || kadence()->option( 'ie11_basic_support' ) ) {
			wp_enqueue_style( 'kadence-ie11', get_theme_file_uri( '/assets/css/ie.min.css' ), array(), KADENCE_VERSION );
			wp_enqueue_script(
				'kadence-css-vars-poly',
				get_theme_file_uri( '/assets/js/css-vars-ponyfill.min.js' ),
				array(),
				KADENCE_VERSION,
				true
			);
			wp_script_add_data( 'kadence-css-vars-poly', 'async', true );
			wp_script_add_data( 'kadence-css-vars-poly', 'precache', true );
			wp_enqueue_script(
				'kadence-ie11',
				get_theme_file_uri( '/assets/js/ie.min.js' ),
				array(),
				KADENCE_VERSION,
				true
			);
			wp_script_add_data( 'kadence-ie11', 'async', true );
			wp_script_add_data( 'kadence-ie11', 'precache', true );
		}
	}
	/**
	 * Enqueues a script that improves navigation menu accessibility as well as sticky header etc.
	 */
	public function action_enqueue_scripts() {

		// If the AMP plugin is active, return early.
		if ( kadence()->is_amp() ) {
			return;
		}

		$breakpoint = 1024;
		if ( kadence()->sub_option( 'header_mobile_switch', 'size' ) ) {
			$breakpoint = kadence()->sub_option( 'header_mobile_switch', 'size' );
		}
		// Enqueue the slide script.
		wp_register_script(
			'kad-splide',
			get_theme_file_uri( '/assets/js/splide.min.js' ),
			array(),
			KADENCE_VERSION,
			true
		);
		wp_script_add_data( 'kad-splide', 'async', true );
		wp_script_add_data( 'kad-splide', 'precache', true );
		// Enqueue the slide script.
		wp_register_script(
			'kadence-slide-init',
			get_theme_file_uri( '/assets/js/splide-init.min.js' ),
			array( 'kad-splide', 'kadence-navigation' ),
			KADENCE_VERSION,
			true
		);
		wp_script_add_data( 'kadence-slide-init', 'async', true );
		wp_script_add_data( 'kadence-slide-init', 'precache', true );
		wp_localize_script(
			'kadence-slide-init',
			'kadenceSlideConfig',
			array(
				'of'    => __( 'of', 'kadence' ),
				'to'    => __( 'to', 'kadence' ),
				'slide' => __( 'Slide', 'kadence' ),
				'next'  => __( 'Next', 'kadence' ),
				'prev'  => __( 'Previous', 'kadence' ),
			)
		);
		if ( kadence()->option( 'lightbox' ) ) {
			// Enqueue the lightbox script.
			wp_enqueue_script(
				'kadence-simplelightbox',
				get_theme_file_uri( '/assets/js/simplelightbox.min.js' ),
				array(),
				KADENCE_VERSION,
				true
			);
			wp_script_add_data( 'kadence-simplelightbox', 'async', true );
			wp_script_add_data( 'kadence-simplelightbox', 'precache', true );
			// Enqueue the slide script.
			wp_enqueue_script(
				'kadence-lightbox-init',
				get_theme_file_uri( '/assets/js/lightbox-init.min.js' ),
				array( 'kadence-simplelightbox' ),
				KADENCE_VERSION,
				true
			);
			wp_script_add_data( 'kadence-lightbox-init', 'async', true );
			wp_script_add_data( 'kadence-lightbox-init', 'precache', true );
		}
		// Main js file.
		$file = 'navigation.min.js';
		// Lets make it possile to load a lighter file if things are not being used.
		if ( 'no' === kadence()->option( 'header_sticky' ) && 'no' === kadence()->option( 'mobile_header_sticky' ) && ! kadence()->option( 'enable_scroll_to_id' ) && ! kadence()->option( 'scroll_up' ) ) {
			$file = 'navigation-lite.min.js';
		}
		wp_enqueue_script(
			'kadence-navigation',
			get_theme_file_uri( '/assets/js/' . $file ),
			array(),
			KADENCE_VERSION,
			true
		);
		wp_script_add_data( 'kadence-navigation', 'async', true );
		wp_script_add_data( 'kadence-navigation', 'precache', true );
		wp_localize_script(
			'kadence-navigation',
			'kadenceConfig',
			array(
				'screenReader' => array(
					'expand'     => __( 'Child menu', 'kadence' ),
					'expandOf'   => __( 'Child menu of', 'kadence' ),
					'collapse'   => __( 'Child menu', 'kadence' ),
					'collapseOf' => __( 'Child menu of', 'kadence' ),
				),
				'breakPoints' => array(
					'desktop' => esc_attr( $breakpoint ),
					'tablet' => 768,
				),
				'scrollOffset' => apply_filters( 'kadence_scroll_to_id_additional_offset', 0 ),
			)
		);
	}
}
