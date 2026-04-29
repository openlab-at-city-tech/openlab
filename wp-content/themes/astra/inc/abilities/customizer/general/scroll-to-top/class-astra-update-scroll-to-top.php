<?php
/**
 * Update Scroll to Top Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Scroll_To_Top
 */
class Astra_Update_Scroll_To_Top extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-scroll-to-top';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Scroll to Top Settings', 'astra' );
		$this->description = __( 'Updates the Astra theme scroll-to-top button settings including enable state, position, device visibility, icon size, colors, and border radius.', 'astra' );

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
				'enabled'      => array(
					'type'        => 'boolean',
					'description' => 'Enable or disable the scroll-to-top button.',
				),
				'position'     => array(
					'type'        => 'string',
					'description' => 'Button position. Options: "right", "left".',
					'enum'        => array( 'right', 'left' ),
				),
				'on_devices'   => array(
					'type'        => 'string',
					'description' => 'Show on which devices. Options: "both", "desktop", "mobile".',
					'enum'        => array( 'both', 'desktop', 'mobile' ),
				),
				'icon_size'    => array(
					'type'        => 'integer',
					'description' => 'Icon size in pixels (1-50).',
				),
				'colors'       => array(
					'type'        => 'object',
					'description' => 'Color settings for the scroll-to-top button.',
					'properties'  => array(
						'icon_color'      => array(
							'type'        => 'string',
							'description' => 'Icon color (hex value).',
						),
						'icon_bg_color'   => array(
							'type'        => 'string',
							'description' => 'Icon background color (hex value).',
						),
						'icon_h_color'    => array(
							'type'        => 'string',
							'description' => 'Icon hover color (hex value).',
						),
						'icon_h_bg_color' => array(
							'type'        => 'string',
							'description' => 'Icon hover background color (hex value).',
						),
					),
				),
				'border_radius' => array(
					'type'        => 'object',
					'description' => 'Border radius responsive fields with desktop, tablet, mobile keys. Each contains top, right, bottom, left values.',
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
				'updated' => array(
					'type'        => 'boolean',
					'description' => 'Whether any settings were updated.',
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
			'enable scroll to top button',
			'disable scroll to top',
			'move scroll to top to left',
			'change scroll to top icon size',
			'update scroll to top colors',
			'set scroll to top on desktop only',
			'change back to top button color',
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
				__( 'Please activate the Astra theme to use this feature.', 'astra' )
			);
		}

		$updated         = false;
		$update_messages = array();

		if ( isset( $args['enabled'] ) ) {
			astra_update_option( 'scroll-to-top-enable', (bool) $args['enabled'] );
			$updated           = true;
			$update_messages[] = $args['enabled'] ? __( 'Scroll to top enabled', 'astra' ) : __( 'Scroll to top disabled', 'astra' );
		}

		if ( isset( $args['position'] ) ) {
			$position        = sanitize_text_field( $args['position'] );
			$valid_positions = array( 'right', 'left' );

			if ( ! in_array( $position, $valid_positions, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: invalid position value */
					sprintf( __( 'Invalid position: %s.', 'astra' ), $position ),
					__( 'Valid options: right, left', 'astra' )
				);
			}

			astra_update_option( 'scroll-to-top-icon-position', $position );
			$updated           = true;
			/* translators: %s: position value */
			$update_messages[] = sprintf( __( 'Position set to %s', 'astra' ), $position );
		}

		if ( isset( $args['on_devices'] ) ) {
			$on_devices    = sanitize_text_field( $args['on_devices'] );
			$valid_devices = array( 'both', 'desktop', 'mobile' );

			if ( ! in_array( $on_devices, $valid_devices, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: invalid device value */
					sprintf( __( 'Invalid on_devices: %s.', 'astra' ), $on_devices ),
					__( 'Valid options: both, desktop, mobile', 'astra' )
				);
			}

			astra_update_option( 'scroll-to-top-on-devices', $on_devices );
			$updated           = true;
			/* translators: %s: device visibility value */
			$update_messages[] = sprintf( __( 'Device visibility set to %s', 'astra' ), $on_devices );
		}

		if ( isset( $args['icon_size'] ) ) {
			$icon_size = absint( $args['icon_size'] );
			astra_update_option( 'scroll-to-top-icon-size', $icon_size );
			$updated           = true;
			/* translators: %d: icon size in pixels */
			$update_messages[] = sprintf( __( 'Icon size set to %dpx', 'astra' ), $icon_size );
		}

		if ( isset( $args['colors'] ) && is_array( $args['colors'] ) ) {
			$color_map = array(
				'icon_color'      => 'scroll-to-top-icon-color',
				'icon_bg_color'   => 'scroll-to-top-icon-bg-color',
				'icon_h_color'    => 'scroll-to-top-icon-h-color',
				'icon_h_bg_color' => 'scroll-to-top-icon-h-bg-color',
			);

			foreach ( $color_map as $key => $option_key ) {
				if ( isset( $args['colors'][ $key ] ) ) {
					astra_update_option( $option_key, sanitize_text_field( $args['colors'][ $key ] ) );
					$updated = true;
				}
			}

			$update_messages[] = __( 'Colors updated', 'astra' );
		}

		if ( isset( $args['border_radius'] ) && is_array( $args['border_radius'] ) ) {
			$sanitized = $this->sanitize_spacing( $args['border_radius'] );
			astra_update_option( 'scroll-to-top-icon-radius-fields', $sanitized );
			$updated           = true;
			$update_messages[] = __( 'Border radius updated', 'astra' );
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one setting to update.', 'astra' )
			);
		}

		/* translators: %s: comma-separated list of updated settings */
		$message = sprintf( __( 'Scroll to top settings updated: %s.', 'astra' ), implode( ', ', $update_messages ) );

		return Astra_Abilities_Response::success(
			$message,
			array( 'updated' => true )
		);
	}

	/**
	 * Sanitize a spacing/radius array.
	 *
	 * @param array $spacing Spacing with desktop, tablet, mobile keys containing top, right, bottom, left.
	 * @return array Sanitized spacing.
	 */
	private function sanitize_spacing( $spacing ) {
		$sanitized = array();
		$devices   = array( 'desktop', 'tablet', 'mobile' );
		$sides     = array( 'top', 'right', 'bottom', 'left' );

		foreach ( $devices as $device ) {
			if ( isset( $spacing[ $device ] ) && is_array( $spacing[ $device ] ) ) {
				$sanitized[ $device ] = array();
				foreach ( $sides as $side ) {
					if ( isset( $spacing[ $device ][ $side ] ) ) {
						$sanitized[ $device ][ $side ] = sanitize_text_field( $spacing[ $device ][ $side ] );
					}
				}
			}

			$unit_key = $device . '-unit';
			if ( isset( $spacing[ $unit_key ] ) ) {
				$sanitized[ $unit_key ] = sanitize_text_field( $spacing[ $unit_key ] );
			}
		}

		return $sanitized;
	}
}

Astra_Update_Scroll_To_Top::register();
