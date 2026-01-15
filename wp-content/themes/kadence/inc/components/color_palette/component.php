<?php
/**
 * Kadence\Color_Palette\Component class
 *
 * @package kadence
 */

namespace Kadence\Color_Palette;

use Kadence\Component_Interface;
use Kadence_Control_Color_Palette;
use function Kadence\kadence;
use function add_action;
use function add_theme_support;
use function apply_filters;

/**
 * Class for adding custom logo support.
 *
 * @link https://codex.wordpress.org/Theme_Logo
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'color_palette';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'customize_register', array( $this, 'color_palette_register' ), 80 );
		add_action( 'after_setup_theme', array( $this, 'action_add_editor_support' ) );
	}

	/**
	 * Add settings
	 *
	 * @access public
	 * @param object $wp_customize the customizer object.
	 * @return void
	 */
	public function color_palette_register( $wp_customize ) {
		$wp_customize->add_setting(
			'kadence_global_palette',
			array(
				'transport'         => 'postMessage',
				'type'              => 'option',
				'default'           => kadence()->get_palette_for_customizer(),
				'capability'        => apply_filters( 'kadence_palette_customizer_capability', 'manage_options' ),
				'sanitize_callback' => 'wp_kses',
			)
		);
		$wp_customize->add_control(
			new Kadence_Control_Color_Palette(
				$wp_customize,
				'kadence_color_palette',
				array(
					'label'       => __( 'Global Palette', 'kadence' ),
					'description' => __( 'Learn how to use this', 'kadence' ),
					'section'     => 'kadence_customizer_general_colors',
					'settings'    => 'kadence_global_palette',
					'priority'    => 8,
				)
			)
		);
	}
	/**
	 * Adds support for various editor features.
	 */
	public function action_add_editor_support() {

		/**
		 * Add support for color palettes.
		 */
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => __( 'Accent', 'kadence' ),
					'slug'  => 'theme-palette1',
					'color' => kadence()->palette_option( 'palette1' ),
				),
				array(
					'name'  => __( 'Accent - alt', 'kadence' ),
					'slug'  => 'theme-palette2',
					'color' => kadence()->palette_option( 'palette2' ),
				),
				array(
					'name'  => __( 'Strongest text', 'kadence' ),
					'slug'  => 'theme-palette3',
					'color' => kadence()->palette_option( 'palette3' ),
				),
				array(
					'name'  => __( 'Strong Text', 'kadence' ),
					'slug'  => 'theme-palette4',
					'color' => kadence()->palette_option( 'palette4' ),
				),
				array(
					'name'  => __( 'Medium text', 'kadence' ),
					'slug'  => 'theme-palette5',
					'color' => kadence()->palette_option( 'palette5' ),
				),
				array(
					'name'  => __( 'Subtle Text', 'kadence' ),
					'slug'  => 'theme-palette6',
					'color' => kadence()->palette_option( 'palette6' ),
				),
				array(
					'name'  => __( 'Subtle Background', 'kadence' ),
					'slug'  => 'theme-palette7',
					'color' => kadence()->palette_option( 'palette7' ),
				),
				array(
					'name'  => __( 'Lighter Background', 'kadence' ),
					'slug'  => 'theme-palette8',
					'color' => kadence()->palette_option( 'palette8' ),
				),
				array(
					'name'  => __( 'White or offwhite', 'kadence' ),
					'slug'  => 'theme-palette9',
					'color' => kadence()->palette_option( 'palette9' ),
				),
				array(
					'name'  => __( 'Accent - Complement', 'kadence' ),
					'slug'  => 'theme-palette10',
					'color' => kadence()->palette_option( 'palette10' ),
				),
				array(
					'name'  => __( 'Notices - Success', 'kadence' ),
					'slug'  => 'theme-palette11',
					'color' => kadence()->palette_option( 'palette11' ),
				),
				array(
					'name'  => __( 'Notices - Info', 'kadence' ),
					'slug'  => 'theme-palette12',
					'color' => kadence()->palette_option( 'palette12' ),
				),
				array(
					'name'  => __( 'Notices - Alert', 'kadence' ),
					'slug'  => 'theme-palette13',
					'color' => kadence()->palette_option( 'palette13' ),
				),
				array(
					'name'  => __( 'Notices - Warning', 'kadence' ),
					'slug'  => 'theme-palette14',
					'color' => kadence()->palette_option( 'palette14' ),
				),
				array(
					'name'  => __( 'Notices - Rating', 'kadence' ),
					'slug'  => 'theme-palette15',
					'color' => kadence()->palette_option( 'palette15' ),
				)
			)
		);
	}
}
