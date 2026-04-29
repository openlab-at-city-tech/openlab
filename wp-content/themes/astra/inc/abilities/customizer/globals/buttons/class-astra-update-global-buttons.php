<?php
/**
 * Update Global Buttons Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Global_Buttons
 */
class Astra_Update_Global_Buttons extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-global-buttons';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Global Buttons', 'astra' );
		$this->description = __( 'Updates the Astra theme global button settings for primary and/or secondary buttons. You can set button presets, text colors, background colors, border colors, padding, border width, and border radius.', 'astra' );

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
				'button_type'            => array(
					'type'        => 'string',
					'description' => __( 'Which button to update: "primary", "secondary", or "both".', 'astra' ),
					'enum'        => array( 'primary', 'secondary', 'both' ),
				),
				'preset'                 => array(
					'type'        => 'string',
					'description' => __( 'Button preset style to apply. Options: "button_01" (Square), "button_02" (Rounded), "button_03" (Pill), "button_04" (Square Outline), "button_05" (Rounded Outline), "button_06" (Pill Outline).', 'astra' ),
					'enum'        => array( 'button_01', 'button_02', 'button_03', 'button_04', 'button_05', 'button_06' ),
				),
				'text_color'             => array(
					'type'        => 'string',
					'description' => __( 'Button text color in hex, rgb, or rgba format (e.g., "#ffffff", "rgb(255,255,255)").', 'astra' ),
				),
				'text_hover_color'       => array(
					'type'        => 'string',
					'description' => __( 'Button text hover color in hex, rgb, or rgba format.', 'astra' ),
				),
				'background_color'       => array(
					'type'        => 'string',
					'description' => __( 'Button background color in hex, rgb, or rgba format.', 'astra' ),
				),
				'background_hover_color' => array(
					'type'        => 'string',
					'description' => __( 'Button background hover color in hex, rgb, or rgba format.', 'astra' ),
				),
				'border_color'           => array(
					'type'        => 'string',
					'description' => __( 'Button border color in hex, rgb, or rgba format.', 'astra' ),
				),
				'border_hover_color'     => array(
					'type'        => 'string',
					'description' => __( 'Button border hover color in hex, rgb, or rgba format.', 'astra' ),
				),
				'padding'                => array(
					'type'        => 'object',
					'description' => __( 'Button padding. Provide an object with desktop/tablet/mobile and top/right/bottom/left values.', 'astra' ),
				),
				'border_width'           => array(
					'type'        => 'object',
					'description' => __( 'Button border width. Provide an object with top/right/bottom/left values.', 'astra' ),
				),
				'border_radius'          => array(
					'type'        => 'object',
					'description' => __( 'Button border radius. Provide an object with desktop/tablet/mobile and top/right/bottom/left values.', 'astra' ),
				),
			),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'change button background color to red',
			'make button background red',
			'set button background to red',
			'change button color to blue',
			'set primary button background to green',
			'change primary button to red',
			'update button text color to white',
			'change primary button to rounded',
			'make button rounded',
			'set button to pill style',
			'update button border color to blue',
			'set button hover color to red',
			'update secondary button colors',
			'change secondary button background',
			'set both buttons to red',
			'update primary button padding',
			'set button border radius',
			'change button to outline style',
			'update global button styles',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$button_type = isset( $args['button_type'] ) ? sanitize_text_field( $args['button_type'] ) : 'primary';
		$valid_types = array( 'primary', 'secondary', 'both' );
		if ( ! in_array( $button_type, $valid_types, true ) ) {
			return Astra_Abilities_Response::error(
				/* translators: %s: button type value */
				sprintf( __( 'Invalid button_type: %s.', 'astra' ), $button_type ),
				__( 'Valid options: primary, secondary, both', 'astra' )
			);
		}

		$updated         = false;
		$update_messages = array();

		if ( isset( $args['preset'] ) && ! empty( $args['preset'] ) ) {
			$preset        = sanitize_text_field( $args['preset'] );
			$valid_presets = array( 'button_01', 'button_02', 'button_03', 'button_04', 'button_05', 'button_06' );
			if ( ! in_array( $preset, $valid_presets, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: preset value */
					sprintf( __( 'Invalid preset: %s.', 'astra' ), $preset ),
					__( 'Valid options: button_01, button_02, button_03, button_04, button_05, button_06', 'astra' )
				);
			}

			$preset_labels = array(
				'button_01' => 'Button 01 - Square',
				'button_02' => 'Button 02 - Rounded',
				'button_03' => 'Button 03 - Pill',
				'button_04' => 'Button 04 - Square Outline',
				'button_05' => 'Button 05 - Rounded Outline',
				'button_06' => 'Button 06 - Pill Outline',
			);

			if ( in_array( $button_type, array( 'primary', 'both' ), true ) ) {
				astra_update_option( 'button-preset-style', $preset );
				$updated           = true;
				$update_messages[] = sprintf( 'Primary button preset set to %s', $preset_labels[ $preset ] );
			}

			if ( in_array( $button_type, array( 'secondary', 'both' ), true ) ) {
				astra_update_option( 'secondary-button-preset-style', $preset );
				$updated           = true;
				$update_messages[] = sprintf( 'Secondary button preset set to %s', $preset_labels[ $preset ] );
			}
		}

		$color_fields = array(
			'text_color'             => array(
				'primary'   => 'button-color',
				'secondary' => 'secondary-button-color',
				'label'     => 'Text color',
			),
			'text_hover_color'       => array(
				'primary'   => 'button-h-color',
				'secondary' => 'secondary-button-h-color',
				'label'     => 'Text hover color',
			),
			'background_color'       => array(
				'primary'   => 'button-bg-color',
				'secondary' => 'secondary-button-bg-color',
				'label'     => 'Background color',
			),
			'background_hover_color' => array(
				'primary'   => 'button-bg-h-color',
				'secondary' => 'secondary-button-bg-h-color',
				'label'     => 'Background hover color',
			),
			'border_color'           => array(
				'primary'   => 'theme-button-border-group-border-color',
				'secondary' => 'secondary-theme-button-border-group-border-color',
				'label'     => 'Border color',
			),
			'border_hover_color'     => array(
				'primary'   => 'theme-button-border-group-border-h-color',
				'secondary' => 'secondary-theme-button-border-group-border-h-color',
				'label'     => 'Border hover color',
			),
		);

		foreach ( $color_fields as $field_key => $field_config ) {
			if ( isset( $args[ $field_key ] ) && ! empty( $args[ $field_key ] ) ) {
				$color_value = sanitize_text_field( $args[ $field_key ] );

				if ( in_array( $button_type, array( 'primary', 'both' ), true ) ) {
					astra_update_option( $field_config['primary'], $color_value );
					$updated      = true;
					$button_label = 'both' === $button_type ? 'Primary button' : 'Button';

					$update_messages[] = sprintf( '%s %s set', $button_label, $field_config['label'] );
				}

				if ( in_array( $button_type, array( 'secondary', 'both' ), true ) ) {
					astra_update_option( $field_config['secondary'], $color_value );
					$updated      = true;
					$button_label = 'both' === $button_type ? 'Secondary button' : 'Button';

					$update_messages[] = sprintf( '%s %s set', $button_label, $field_config['label'] );
				}
			}
		}

		$spacing_fields = array(
			'padding'       => array(
				'primary'   => 'theme-button-padding',
				'secondary' => 'secondary-theme-button-padding',
				'label'     => 'Padding',
			),
			'border_width'  => array(
				'primary'   => 'theme-button-border-group-border-size',
				'secondary' => 'secondary-theme-button-border-group-border-size',
				'label'     => 'Border width',
			),
			'border_radius' => array(
				'primary'   => 'button-radius-fields',
				'secondary' => 'secondary-button-radius-fields',
				'label'     => 'Border radius',
			),
		);

		foreach ( $spacing_fields as $field_key => $field_config ) {
			if ( isset( $args[ $field_key ] ) && is_array( $args[ $field_key ] ) ) {
				$spacing_value = $args[ $field_key ];

				if ( in_array( $button_type, array( 'primary', 'both' ), true ) ) {
					astra_update_option( $field_config['primary'], $spacing_value );
					$updated      = true;
					$button_label = 'both' === $button_type ? 'Primary button' : 'Button';

					$update_messages[] = sprintf( '%s %s updated', $button_label, $field_config['label'] );
				}

				if ( in_array( $button_type, array( 'secondary', 'both' ), true ) ) {
					astra_update_option( $field_config['secondary'], $spacing_value );
					$updated      = true;
					$button_label = 'both' === $button_type ? 'Secondary button' : 'Button';

					$update_messages[] = sprintf( '%s %s updated', $button_label, $field_config['label'] );
				}
			}
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one button property to update (preset, colors, padding, border_width, or border_radius).', 'astra' )
			);
		}

		$message = implode( ', ', $update_messages ) . '.';

		return Astra_Abilities_Response::success(
			$message,
			array(
				'button_type' => $button_type,
				'updated'     => true,
			)
		);
	}
}

Astra_Update_Global_Buttons::register();
