<?php
/**
 * Theme option partial refresh component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WP_Customize_Manager;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Options_Partial_Refresh implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'after_setup_theme', __CLASS__ . '::after_setup_theme' );

				add_action( 'customize_register', __CLASS__ . '::setup', 100 );

	} // /init

	/**
	 * After setup theme.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function after_setup_theme() {

		// Processing

			// Customizer: Add theme support for selective widget refresh.
			add_theme_support( 'customize-selective-refresh-widgets' );

	} // /after_setup_theme

	/**
	 * Setup partial refresh.
	 *
	 * @since  1.0.0
	 *
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function setup( WP_Customize_Manager $wp_customize ) {

		// Processing

			// Site title.
			$wp_customize->selective_refresh->add_partial( 'blogname', array(
				'selector'        => '.site-title',
				'render_callback' => __CLASS__ . '::render__blogname',
			) );

			// Site description.
			$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
				'selector'        => '.site-description',
				'render_callback' => __CLASS__ . '::render__blogdescription',
			) );

	} // /setup

	/**
	 * Render the site title for the selective refresh partial
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function render__blogname() {

		// Output

			bloginfo( 'name' );

	} // /render__blogname

	/**
	 * Render the site tagline for the selective refresh partial
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function render__blogdescription() {

		// Output

			bloginfo( 'description' );

	} // /render__blogdescription

}
