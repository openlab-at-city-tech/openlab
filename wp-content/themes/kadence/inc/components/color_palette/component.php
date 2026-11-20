<?php
/**
 * Kadence\Color_Palette\Component class
 *
 * @package kadence
 */

namespace Kadence\Color_Palette;

use Kadence\Component_Interface;
use Kadence_Control_Blank;
use Kadence_Control_Color_Palette;
use WP_Error;
use function Kadence\is_fse_mode;
use function Kadence\kadence;

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
	public function get_slug(): string {
		return 'color_palette';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'customize_register', [ $this, 'color_palette_register' ], 80 );
		add_action( 'after_setup_theme', [ $this, 'action_add_editor_support' ] );
	}

	/**
	 * Add settings
	 *
	 * @since 1.6.0 Hides the palette control in Full Site Editing mode and links to the Site Editor in its place.
	 *
	 * @access public
	 * @param object $wp_customize the customizer object.
	 * @return void
	 */
	public function color_palette_register( $wp_customize ) {
		$wp_customize->add_setting(
			'kadence_global_palette',
			[
				'transport'         => 'postMessage',
				'type'              => 'option',
				'default'           => kadence()->get_palette_for_customizer(),
				'capability'        => apply_filters( 'kadence_palette_customizer_capability', 'manage_options' ),
				'sanitize_callback' => 'wp_kses',
				'validate_callback' => [ $this, 'validate_palette' ],
			]
		);

		/*
		 * The Site Editor owns the palette in Full Site Editing mode, so the
		 * control is not shown there. It stays registered because every other
		 * color picker reads its swatches through it, and unregistering it
		 * would break them. The stored value is left untouched, so classic mode
		 * shows the same palette again if the mode is turned back off.
		 */
		$wp_customize->add_control(
			new Kadence_Control_Color_Palette(
				$wp_customize,
				'kadence_color_palette',
				[
					'label'           => __( 'Global Palette', 'kadence' ),
					'description'     => __( 'Learn how to use this', 'kadence' ),
					'section'         => 'kadence_customizer_general_colors',
					'settings'        => 'kadence_global_palette',
					'priority'        => 8,
					'active_callback' => static function () {
						return ! is_fse_mode();
					},
				]
			)
		);

		// Takes the palette control's place in Full Site Editing mode and links to where the palette moved.
		// It is only registered there, so classic mode's Customizer is the same as before.
		if ( ! is_fse_mode() ) {
			return;
		}

		$site_editor_palette_url = add_query_arg(
			[
				'p'       => '/styles',
				'section' => '/colors/palette',
			],
			admin_url( 'site-editor.php' )
		);
		$wp_customize->add_control(
			new Kadence_Control_Blank(
				$wp_customize,
				'kadence_color_palette_fse_notice',
				[
					'label'       => esc_html__( 'Global Palette', 'kadence' ),
					'description' => sprintf(
						'%1$s <a href="%2$s">%3$s</a>',
						esc_html__( 'The Global Palette has moved to the Site Editor.', 'kadence' ),
						esc_url( $site_editor_palette_url ),
						esc_html__( 'Edit the palette in the Site Editor', 'kadence' )
					),
					'section'     => 'kadence_customizer_general_colors',
					'settings'    => [],
					'capability'  => 'edit_theme_options',
					'priority'    => 8,
				]
			)
		);
	}

	/**
	 * Rejects palette changes in Full Site Editing mode, where the Site Editor owns the palette.
	 *
	 * The hidden control no longer sends changes, but a changeset can still carry one.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Error $validity Errors found so far.
	 * @return WP_Error
	 */
	public function validate_palette( $validity ) {
		if ( is_fse_mode() ) {
			$validity->add( 'kadence_palette_in_site_editor', __( 'The Global Palette is edited in the Site Editor in Full Site Editing mode.', 'kadence' ) );
		}

		return $validity;
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
			[
				[
					'name'  => __( 'Accent', 'kadence' ),
					'slug'  => 'theme-palette1',
					'color' => kadence()->palette_option( 'palette1' ),
				],
				[
					'name'  => __( 'Accent - alt', 'kadence' ),
					'slug'  => 'theme-palette2',
					'color' => kadence()->palette_option( 'palette2' ),
				],
				[
					'name'  => __( 'Strongest text', 'kadence' ),
					'slug'  => 'theme-palette3',
					'color' => kadence()->palette_option( 'palette3' ),
				],
				[
					'name'  => __( 'Strong Text', 'kadence' ),
					'slug'  => 'theme-palette4',
					'color' => kadence()->palette_option( 'palette4' ),
				],
				[
					'name'  => __( 'Medium text', 'kadence' ),
					'slug'  => 'theme-palette5',
					'color' => kadence()->palette_option( 'palette5' ),
				],
				[
					'name'  => __( 'Subtle Text', 'kadence' ),
					'slug'  => 'theme-palette6',
					'color' => kadence()->palette_option( 'palette6' ),
				],
				[
					'name'  => __( 'Subtle Background', 'kadence' ),
					'slug'  => 'theme-palette7',
					'color' => kadence()->palette_option( 'palette7' ),
				],
				[
					'name'  => __( 'Lighter Background', 'kadence' ),
					'slug'  => 'theme-palette8',
					'color' => kadence()->palette_option( 'palette8' ),
				],
				[
					'name'  => __( 'White or offwhite', 'kadence' ),
					'slug'  => 'theme-palette9',
					'color' => kadence()->palette_option( 'palette9' ),
				],
				[
					'name'  => __( 'Accent - Complement', 'kadence' ),
					'slug'  => 'theme-palette10',
					'color' => kadence()->palette_option( 'palette10' ),
				],
				[
					'name'  => __( 'Notices - Success', 'kadence' ),
					'slug'  => 'theme-palette11',
					'color' => kadence()->palette_option( 'palette11' ),
				],
				[
					'name'  => __( 'Notices - Info', 'kadence' ),
					'slug'  => 'theme-palette12',
					'color' => kadence()->palette_option( 'palette12' ),
				],
				[
					'name'  => __( 'Notices - Alert', 'kadence' ),
					'slug'  => 'theme-palette13',
					'color' => kadence()->palette_option( 'palette13' ),
				],
				[
					'name'  => __( 'Notices - Warning', 'kadence' ),
					'slug'  => 'theme-palette14',
					'color' => kadence()->palette_option( 'palette14' ),
				],
				[
					'name'  => __( 'Notices - Rating', 'kadence' ),
					'slug'  => 'theme-palette15',
					'color' => kadence()->palette_option( 'palette15' ),
				],
			]
		);
	}
}
