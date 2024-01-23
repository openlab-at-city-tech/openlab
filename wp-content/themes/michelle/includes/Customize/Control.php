<?php
/**
 * Customizer control renderer component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WP_Customize_Color_Control;
use WP_Customize_Image_Control;
use WP_Customize_Media_Control;
use WP_Customize_Manager;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Control implements Component_Interface {

	/**
	 * Customizer option type.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     string
	 */
	public static $option_type = 'theme_mod';

	/**
	 * Sanitize callbacks for different controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     array
	 */
	public static $sanitize = array(
		'checkbox'      => __NAMESPACE__ . '\Sanitize::checkbox',
		'color'         => 'sanitize_hex_color_no_hash',
		'email'         => 'sanitize_email',
		'image'         => 'esc_url_raw',
		'multicheckbox' => __NAMESPACE__ . '\Sanitize::array_value',
		'multiselect'   => __NAMESPACE__ . '\Sanitize::array_value',
		'radio'         => __NAMESPACE__ . '\Sanitize::select',
		'range'         => 'absint',
		'select'        => __NAMESPACE__ . '\Sanitize::select',
		'text'          => 'esc_textarea',
		'textarea'      => 'esc_textarea',
		'url'           => 'esc_url',
	);

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

				add_action( 'customize_controls_enqueue_scripts', __CLASS__ . '::assets' );

	} // /init

	/**
	 * Customizer controls assets.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function assets() {

		// Processing

			wp_enqueue_style(
				'michelle-customize-controls',
				get_theme_file_uri( 'assets/css/customize-controls.css' ),
				false,
				MICHELLE_THEME_VERSION,
				'screen'
			);
			wp_style_add_data( 'michelle-customize-controls', 'rtl', 'replace' );

			wp_enqueue_script(
				'michelle-customize-controls',
				get_theme_file_uri( 'assets/js/customize-controls.min.js' ),
				array( 'customize-controls' ),
				MICHELLE_THEME_VERSION,
				true
			);

	} // /assets

	/**
	 * Adds, creates customizer option control.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_option( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			self::add_setting( $option, $wp_customize );
			self::add_control( $option, $wp_customize );

	} // /add_option

	/**
	 * Add setting.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_setting( array $option, WP_Customize_Manager $wp_customize ) {

		// Variables

			$default = ( isset( $option['default'] ) ) ? ( $option['default'] ) : ( '' );

			if ( isset( $option['sanitize_callback'] ) ) {
				$sanitize = $option['sanitize_callback'];
			} elseif ( isset( $option['return'] ) && 'id' === $option['return'] ) {
				$sanitize = 'absint';
			} elseif ( isset( self::$sanitize[ $option['type'] ] ) ) {
				$sanitize = self::$sanitize[ $option['type'] ];
			} else {
				$sanitize = 'esc_attr';
			}

			switch ( $sanitize ) {

				case 'sanitize_hex_color_no_hash':
					$sanitize_js = 'maybe_hash_hex_color';
					break;

				case 'wp_kses_post':
					$sanitize_js = 'wp_filter_post_kses';
					break;

				default:
					$sanitize_js = $sanitize;
					break;

			}


		// Processing

			$wp_customize->add_setting(
				$option['id'],
				array(
					'type'                 => self::$option_type,
					'default'              => ( 'color' === $option['type'] ) ? ( trim( $default, '#' ) ) : ( $default ),
					'transport'            => ( isset( $option['preview_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' ),
					'sanitize_callback'    => $sanitize,
					'sanitize_js_callback' => $sanitize_js,
					'validate_callback'    => ( isset( $option['validate_callback'] ) ) ? ( $option['validate_callback'] ) : ( '' ),
				)
			);

	} // /add_setting

	/**
	 * Add control.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			if ( method_exists( __CLASS__, 'add_control_' . $option['type'] ) ) {
				// Render custom or special control.
				call_user_func(
					array( __CLASS__, 'add_control_' . $option['type'] ),
					$option,
					$wp_customize
				);
			} else {
				// Render default control.
				$wp_customize->add_control(
					$option['id'],
					self::get_args( $option )
				);
			}

	} // /add_control

	/**
	 * Control: color.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_color( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$option['id'],
					self::get_args( $option )
				)
			);

	} // /add_control_color

	/**
	 * Control: html.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_html( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			$wp_customize->add_control(
				new Control\HTML(
					$wp_customize,
					$option['id'],
					array_merge(
						self::get_args( $option ),
						array(
							'content' => $option['content'],
						)
					)
				)
			);

	} // /add_control_html

	/**
	 * Control: image.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_image( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			if (
				isset( $option['return'] )
				&& 'id' === $option['return']
			) {
				$wp_customize->add_control(
					new WP_Customize_Media_Control(
						$wp_customize,
						$option['id'],
						array_merge(
							self::get_args( $option ),
							array(
								'type'      => 'media',
								'context'   => $option['id'],
								'mime_type' => 'image',
							)
						)
					)
				);
			} else {
				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						$option['id'],
						array_merge(
							self::get_args( $option ),
							array(
								'context' => $option['id'],
							)
						)
					)
				);
			}

	} // /add_control_image

	/**
	 * Control: multicheckbox.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_multicheckbox( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			self::add_control_multiselect( $option, $wp_customize );

	} // /add_control_multicheckbox

	/**
	 * Control: multiselect.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_multiselect( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			$wp_customize->add_control(
				new Control\Multiselect(
					$wp_customize,
					$option['id'],
					self::get_args( $option )
				)
			);

	} // /add_control_multiselect

	/**
	 * Control: range.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_range( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			$wp_customize->add_control(
				$option['id'],
				array_merge(
					self::get_args( $option ),
					array(
						'input_attrs' => array(
							'min'           => $option['min'],
							'max'           => $option['max'],
							'step'          => $option['step'],
							'data-multiply' => ( isset( $option['multiplier'] ) ) ? ( $option['multiplier'] ) : ( 1 ),
							'data-prefix'   => ( isset( $option['prefix'] ) ) ? ( $option['prefix'] ) : ( '' ),
							'data-suffix'   => ( isset( $option['suffix'] ) ) ? ( $option['suffix'] ) : ( '' ),
							'data-decimals' => ( isset( $option['decimal_places'] ) ) ? ( absint( $option['decimal_places'] ) ) : ( 0 ),
						),
					)
				)
			);

	} // /add_control_range

	/**
	 * Control: select.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_select( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			$wp_customize->add_control(
				new Control\Select(
					$wp_customize,
					$option['id'],
					self::get_args( $option )
				)
			);

	} // /add_control_select

	/**
	 * Control: text.
	 *
	 * @since  1.0.0
	 *
	 * @param  array                $option
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function add_control_text( array $option, WP_Customize_Manager $wp_customize ) {

		// Processing

			$wp_customize->add_control(
				new Control\Text(
					$wp_customize,
					$option['id'],
					array_merge(
						self::get_args( $option ),
						array(
							'choices' => ( isset( $option['datalist'] ) ) ? ( $option['datalist'] ) : ( array() ),
						)
					)
				)
			);

	} // /add_control_text

	/**
	 * Get generic option args.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $option
	 *
	 * @return  array
	 */
	public static function get_args( array $option ): array {

		// Output

			return array(
				'active_callback' => ( isset( $option['active_callback'] ) ) ? ( $option['active_callback'] ) : ( '' ),
				'choices'         => ( isset( $option['choices'] ) ) ? ( $option['choices'] ) : ( null ),
				'description'     => ( isset( $option['description'] ) ) ? ( $option['description'] ) : ( '' ),
				'input_attrs'     => ( isset( $option['input_attrs'] ) ) ? ( $option['input_attrs'] ) : ( array() ),
				'label'           => ( isset( $option['label'] ) ) ? ( $option['label'] ) : ( '' ),
				'priority'        => ( isset( $option['priority'] ) ) ? ( $option['priority'] ) : ( 0 ),
				'section'         => ( isset( $option['section'] ) ) ? ( $option['section'] ) : ( 'michelle' ),
				'type'            => $option['type'],
			);

	} // /get_args

}
