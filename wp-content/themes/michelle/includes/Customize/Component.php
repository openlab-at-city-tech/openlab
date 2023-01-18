<?php
/**
 * Customize component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Assets;
use WP_Customize_Manager;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Customizer functionality.
			Control::init();
			Preview::init();
			// Theme options.
			Options::init();
			Custom_Logo::init();
			RGBA::init();
			Colors::init();
			Options_Partial_Refresh::init();
			// Front-end styles.
			CSS_Variables::init();
			Styles::init();

			// Actions

				add_action( 'customize_register', __CLASS__ . '::render', 50 );

				add_action( 'michelle/customize/render/option', __NAMESPACE__ . '\Control::add_option', 10, 2 );

	} // /init

	/**
	 * Customizer renderer.
	 *
	 * @since  1.0.0
	 *
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function render( WP_Customize_Manager $wp_customize ) {

		// Pre

			/**
			 * Bypass filter for WebManDesign\Michelle\Customize\Component::register().
			 *
			 * Returning a non-null value will short-circuit the method,
			 * returning the passed value instead.
			 *
			 * @since  1.0.0
			 *
			 * @param  mixed                $pre           Default: null. If not null, method returns the value.
			 * @param  WP_Customize_Manager $wp_customize  Customizer object.
			 */
			$pre = apply_filters( 'pre/michelle/customize/render', null, $wp_customize );

			if ( null !== $pre ) {
				return $pre;
			}


		// Variables

			$panel   = '';
			$section = 'michelle';
			$options = Options::get();

			// Theme options comes first.
			$priority = absint(
				/**
				 * Filters customizer theme options priority.
				 *
				 * @since  1.0.0
				 *
				 * @param  int $priority
				 */
				apply_filters( 'michelle/customize/render/priority', 0 )
			);


		// Processing

			ksort( $options );

			// Generate customizer options.
			foreach ( $options as $key => $option ) {
				if ( isset( $option['type'] ) ) {
					$priority++;

					// Preset option args.
					if ( ! isset( $option['id'] ) ) {
						$option['id'] = 'michelle' . '_key_' . sanitize_title( $key );
					}
					if ( ! isset( $option['priority'] ) ) {
						$option['priority'] = $priority;
					}

					/**
					 * Create panel.
					 *
					 * Note that the panel will not display unless sections are assigned to it.
					 * Set the panel name in the section declaration with `in_panel`:
					 * - if text, this will become a panel title (ID defaults to `theme-options`),
					 * - if array, you can set `title`, `id` and `type` (the type will affect panel class).
					 * Panel has to be defined for each section to prevent all sections within a single panel.
					 */
					if ( isset( $option['in_panel'] ) ) {
						$panel_args = array(
							'type' => 'theme-options',
						);

						if ( is_array( $option['in_panel'] ) ) {
							$panel_args['title'] = ( isset( $option['in_panel']['title'] ) ) ? ( $option['in_panel']['title'] ) : ( '&mdash;' );
							$panel_args['id']    = ( isset( $option['in_panel']['id'] ) ) ? ( $option['in_panel']['id'] ) : ( $panel_args['type'] );

							if ( isset( $option['in_panel']['type'] ) ) {
								$panel_args['type'] = $option['in_panel']['type'];
							}
						} else {
							$panel_args['title'] = $option['in_panel'];
							$panel_args['id']    = $panel_args['type'];
						}

						/**
						 * Filters customizer theme options panel setup arguments.
						 *
						 * @since  1.0.0
						 *
						 * @param  array                $panel_args
						 * @param  array                $option
						 * @param  WP_Customize_Manager $wp_customize
						 * @param  array                $options
						 */
						$panel_args = (array) apply_filters( 'michelle/customize/render/panel_args', $panel_args, $option, $wp_customize, $options );

						// Create a new panel only if the previous panel declared in theme options differs.
						if ( $panel !== $panel_args['id'] ) {
							$wp_customize->add_panel(
								$panel_args['id'],
								array(
									'title'    => esc_html( $panel_args['title'] ),
									'priority' => $option['priority'],
									// Type also sets the panel class.
									'type' => $panel_args['type'],
									// Description is hidden at the top of the panel.
									'description' => ( isset( $option['in_panel-description'] ) ) ? ( $option['in_panel-description'] ) : ( '' ),
								)
							);
							$panel = $panel_args['id'];
						}
					}

					// Create section.
					if ( isset( $option['create_section'] ) && trim( $option['create_section'] ) ) {
						$section = array(
							'id'    => $option['id'],
							'setup' => array(
								'title'       => $option['create_section'],
								'description' => ( isset( $option['create_section-description'] ) ) ? ( $option['create_section-description'] ) : ( '' ),
								'priority'    => $option['priority'],
								// Type also sets the section class.
								'type' => 'theme-options',
							)
						);

						if ( isset( $option['in_panel'] ) ) {
							$section['setup']['panel'] = $panel;
						} else {
							$panel = '';
						}

						$wp_customize->add_section(
							$section['id'],
							$section['setup']
						);

						$section = $section['id'];
					}

					// Now that the section is created set it for the option.
					if ( ! isset( $option['section'] ) ) {
						$option['section'] = $section;
					}

					// Generate option control.
					if ( ! in_array( $option['type'], array( 'panel', 'section' ) ) ) {
						/**
						 * Action for creating a theme option in customizer.
						 *
						 * @since  1.0.0
						 *
						 * @param  array                $option
						 * @param  WP_Customize_Manager $wp_customize
						 * @param  array                $options
						 */
						do_action( 'michelle/customize/render/option', $option, $wp_customize, $options );
					}
				}
			}

			// Assets needed for customizer preview.
			if ( $wp_customize->is_preview() ) {
				add_action( 'customize_preview_init', function() {

					// Processing

						wp_add_inline_script(
							'michelle-customize-preview',
							Preview::get_js()
						);

				} );
			}

	} // /render

}
