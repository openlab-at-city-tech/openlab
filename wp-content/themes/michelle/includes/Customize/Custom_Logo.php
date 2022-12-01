<?php
/**
 * Theme additional custom logo component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.3.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Header\Body_Class;
use WP_Customize_Cropped_Image_Control;
use WP_Customize_Manager;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Custom_Logo implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.3.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'customize_register', __CLASS__ . '::customize_register', 100 );

				add_action( 'tha_header_before', __CLASS__ . '::set_custom_logo' );

	} // /init

	/**
	 * Add new custom logo options.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function customize_register( WP_Customize_Manager $wp_customize ) {

		// Variables

			$custom_logo_args = get_theme_support( 'custom-logo' );


		// Processing

			// Light logo.

				$wp_customize->add_setting(
					'custom_logo_light',
					array(
						'theme_supports'    => array( 'custom-logo' ),
						'transport'         => 'postMessage',
						'sanitize_callback' => 'absint',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Cropped_Image_Control(
						$wp_customize,
						'custom_logo_light',
						array(
							'label'           => esc_html__( 'Logo variation: light', 'michelle' ),
							'description'     => esc_html__( 'This logo image will be displayed in overlaid header with light color.', 'michelle' ),
							'section'         => 'title_tagline',
							'priority'        => 8,
							'height'          => isset( $custom_logo_args[0]['height'] ) ? $custom_logo_args[0]['height'] : null,
							'width'           => isset( $custom_logo_args[0]['width'] ) ? $custom_logo_args[0]['width'] : null,
							'flex_height'     => isset( $custom_logo_args[0]['flex-height'] ) ? $custom_logo_args[0]['flex-height'] : null,
							'flex_width'      => isset( $custom_logo_args[0]['flex-width'] ) ? $custom_logo_args[0]['flex-width'] : null,
							'button_labels'   => array(
								'select'       => esc_html__( 'Select logo', 'michelle' ),
								'change'       => esc_html__( 'Change logo', 'michelle' ),
								'remove'       => esc_html__( 'Remove', 'michelle' ),
								'default'      => esc_html__( 'Default', 'michelle' ),
								'placeholder'  => esc_html__( 'No logo selected', 'michelle' ),
								'frame_title'  => esc_html__( 'Select logo', 'michelle' ),
								'frame_button' => esc_html__( 'Choose logo', 'michelle' ),
							),
						)
					)
				);

				$wp_customize->selective_refresh->add_partial(
					'custom_logo_light',
					array(
						'selector'            => 'body[class*="-header-overlaid-light"] .custom-logo-link',
						'render_callback'     => array( $wp_customize, '_render_custom_logo_partial' ),
						'container_inclusive' => true,
					)
				);

			// Dark logo.

				$wp_customize->add_setting(
					'custom_logo_dark',
					array(
						'theme_supports'    => array( 'custom-logo' ),
						'transport'         => 'postMessage',
						'sanitize_callback' => 'absint',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Cropped_Image_Control(
						$wp_customize,
						'custom_logo_dark',
						array(
							'label'           => esc_html__( 'Logo variation: dark', 'michelle' ),
							'description'     => esc_html__( 'This logo image will be displayed in overlaid header with dark color.', 'michelle' ),
							'section'         => 'title_tagline',
							'priority'        => 8,
							'height'          => isset( $custom_logo_args[0]['height'] ) ? $custom_logo_args[0]['height'] : null,
							'width'           => isset( $custom_logo_args[0]['width'] ) ? $custom_logo_args[0]['width'] : null,
							'flex_height'     => isset( $custom_logo_args[0]['flex-height'] ) ? $custom_logo_args[0]['flex-height'] : null,
							'flex_width'      => isset( $custom_logo_args[0]['flex-width'] ) ? $custom_logo_args[0]['flex-width'] : null,
							'button_labels'   => array(
								'select'       => esc_html__( 'Select logo', 'michelle' ),
								'change'       => esc_html__( 'Change logo', 'michelle' ),
								'remove'       => esc_html__( 'Remove', 'michelle' ),
								'default'      => esc_html__( 'Default', 'michelle' ),
								'placeholder'  => esc_html__( 'No logo selected', 'michelle' ),
								'frame_title'  => esc_html__( 'Select logo', 'michelle' ),
								'frame_button' => esc_html__( 'Choose logo', 'michelle' ),
							),
						)
					)
				);

				$wp_customize->selective_refresh->add_partial(
					'custom_logo_dark',
					array(
						'selector'            => 'body[class*="-header-overlaid-dark"] .custom-logo-link',
						'render_callback'     => array( $wp_customize, '_render_custom_logo_partial' ),
						'container_inclusive' => true,
					)
				);

	} // /customize_register

	/**
	 * Set custom logo variation in header only.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  mixed
	 */
	public static function set_custom_logo() {

		// Requirements check

			if ( ! stripos( implode( ' ', Body_Class::get_body_class() ), '-header-overlaid-' ) ) {
				return;
			}


		// Processing

			if ( doing_action( 'tha_header_before' ) ) {
				add_filter( 'theme_mod_custom_logo', __CLASS__ . '::custom_logo' );
			} else {
				remove_filter( 'theme_mod_custom_logo', __CLASS__ . '::custom_logo' );
			}

	} // /set_custom_logo

	/**
	 * Display custom logo variation.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  mixed $value
	 *
	 * @return  mixed
	 */
	public static function custom_logo( $value ) {

		// Variables

			$body_classes = implode( ' ', Body_Class::get_body_class() );


		// Processing

			if (
				stripos( $body_classes, '-header-overlaid-light' )
				&& $logo_light = get_theme_mod( 'custom_logo_light' )
			) {
				return $logo_light;
			} elseif (
				stripos( $body_classes, '-header-overlaid-dark' )
				&& $logo_dark = get_theme_mod( 'custom_logo_dark' )
			) {
				return $logo_dark;
			}


		// Output

			return $value;

	} // /custom_logo

}
