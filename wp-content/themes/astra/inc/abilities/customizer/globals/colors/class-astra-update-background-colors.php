<?php
/**
 * Update Background Colors Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Background_Colors
 */
class Astra_Update_Background_Colors extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-color-background';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Background Colors', 'astra' );
		$this->description = __( 'Updates the Astra theme background colors including site background and content background. Supports solid colors, gradients, and images.', 'astra' );

		$this->meta = array(
			'tool_type' => 'write',
		);
	}

	/**
	 * Get tool type.
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'write';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'site_background'    => array(
					'type'        => 'object',
					'description' => 'Site background configuration (body background)',
					'properties'  => array(
						'desktop' => array(
							'type'        => 'object',
							'description' => 'Desktop background settings',
							'properties'  => array(
								'background-color'      => array(
									'type'        => 'string',
									'description' => 'Background color (hex or CSS variable)',
								),
								'background-image'      => array(
									'type'        => 'string',
									'description' => 'Background image URL',
								),
								'background-repeat'     => array(
									'type'        => 'string',
									'description' => 'Background repeat (repeat, no-repeat, etc.)',
								),
								'background-position'   => array(
									'type'        => 'string',
									'description' => 'Background position',
								),
								'background-size'       => array(
									'type'        => 'string',
									'description' => 'Background size (auto, cover, contain)',
								),
								'background-attachment' => array(
									'type'        => 'string',
									'description' => 'Background attachment (scroll, fixed)',
								),
								'background-type'       => array(
									'type'        => 'string',
									'description' => 'Background type (color, image, gradient)',
								),
								'background-media'      => array(
									'type'        => 'string',
									'description' => 'Background media ID',
								),
							),
						),
						'tablet'  => array(
							'type'        => 'object',
							'description' => 'Tablet background settings',
						),
						'mobile'  => array(
							'type'        => 'object',
							'description' => 'Mobile background settings',
						),
					),
				),
				'content_background' => array(
					'type'        => 'object',
					'description' => 'Content background configuration',
					'properties'  => array(
						'desktop' => array(
							'type'        => 'object',
							'description' => 'Desktop background settings',
							'properties'  => array(
								'background-color'      => array(
									'type'        => 'string',
									'description' => 'Background color (hex or CSS variable)',
								),
								'background-image'      => array(
									'type'        => 'string',
									'description' => 'Background image URL',
								),
								'background-repeat'     => array(
									'type'        => 'string',
									'description' => 'Background repeat',
								),
								'background-position'   => array(
									'type'        => 'string',
									'description' => 'Background position',
								),
								'background-size'       => array(
									'type'        => 'string',
									'description' => 'Background size',
								),
								'background-attachment' => array(
									'type'        => 'string',
									'description' => 'Background attachment',
								),
								'background-type'       => array(
									'type'        => 'string',
									'description' => 'Background type',
								),
								'background-media'      => array(
									'type'        => 'string',
									'description' => 'Background media ID',
								),
							),
						),
						'tablet'  => array(
							'type'        => 'object',
							'description' => 'Tablet background settings',
						),
						'mobile'  => array(
							'type'        => 'object',
							'description' => 'Mobile background settings',
						),
					),
				),
			),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->build_output_schema(
			array(
				'updated'            => array(
					'type'        => 'array',
					'description' => 'List of updated background types (site_background, content_background).',
					'items'       => array( 'type' => 'string' ),
				),
				'site_background'    => array(
					'type'        => 'object',
					'description' => 'Updated site background configuration.',
				),
				'content_background' => array(
					'type'        => 'object',
					'description' => 'Updated content background configuration.',
				),
			)
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'set site background to white',
			'change content background color to light gray',
			'update body background to #f5f5f5',
			'set site background image',
			'change page background to gradient',
			'set content area background to transparent',
			'change background color for mobile',
			'set different background for tablet',
			'make site background cover the page',
			'set responsive background colors',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! defined( 'ASTRA_THEME_SETTINGS' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra theme is not active.', 'astra' ),
				__( 'Option functions are not available.', 'astra' )
			);
		}

		if ( empty( $args['site_background'] ) && empty( $args['content_background'] ) ) {
			return Astra_Abilities_Response::error(
				__( 'No background specified.', 'astra' ),
				__( 'Please provide either site_background or content_background to update.', 'astra' )
			);
		}

		$theme_options = get_option( ASTRA_THEME_SETTINGS, array() );
		if ( ! is_array( $theme_options ) ) {
			$theme_options = array();
		}

		$updated = array();

		if ( ! empty( $args['site_background'] ) ) {
			$current_site_bg = astra_get_option( 'site-layout-outside-bg-obj-responsive' );
			$new_site_bg     = $this->merge_background_config( $current_site_bg, $args['site_background'] );

			$theme_options['site-layout-outside-bg-obj-responsive'] = $new_site_bg;
			$updated[] = 'site_background';
		}

		if ( ! empty( $args['content_background'] ) ) {
			$current_content_bg = astra_get_option( 'content-bg-obj-responsive' );
			$new_content_bg     = $this->merge_background_config( $current_content_bg, $args['content_background'] );

			$theme_options['content-bg-obj-responsive'] = $new_content_bg;
			$updated[]                                  = 'content_background';
		}

		update_option( ASTRA_THEME_SETTINGS, $theme_options );

		return Astra_Abilities_Response::success(
			/* translators: %s: comma-separated list of updated backgrounds */
			sprintf( __( 'Background colors updated successfully: %s', 'astra' ), implode( ', ', $updated ) ),
			array(
				'updated'            => $updated,
				'site_background'    => isset( $theme_options['site-layout-outside-bg-obj-responsive'] ) ? $theme_options['site-layout-outside-bg-obj-responsive'] : array(),
				'content_background' => isset( $theme_options['content-bg-obj-responsive'] ) ? $theme_options['content-bg-obj-responsive'] : array(),
			)
		);
	}

	/**
	 * Merge background configuration.
	 *
	 * @param mixed $current Current background config.
	 * @param array $new     New background config.
	 * @return array Merged config.
	 */
	private function merge_background_config( $current, $new ) {
		$default_device_config = array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'background-type'       => '',
			'background-media'      => '',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		);

		if ( ! is_array( $current ) ) {
			$current = array(
				'desktop' => $default_device_config,
				'tablet'  => $default_device_config,
				'mobile'  => $default_device_config,
			);
		}

		foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
			if ( ! isset( $current[ $device ] ) ) {
				$current[ $device ] = $default_device_config;
			}

			if ( isset( $new[ $device ] ) && is_array( $new[ $device ] ) ) {
				foreach ( $new[ $device ] as $key => $value ) {
					$sanitized_key = sanitize_text_field( $key );
					if ( 'background-color' === $sanitized_key ) {
						$current[ $device ][ $sanitized_key ] = sanitize_text_field( $value );
					} elseif ( 'background-image' === $sanitized_key ) {
						$current[ $device ][ $sanitized_key ] = esc_url_raw( $value );
					} else {
						$current[ $device ][ $sanitized_key ] = sanitize_text_field( $value );
					}
				}
			}
		}

		return $current;
	}
}

Astra_Update_Background_Colors::register();
