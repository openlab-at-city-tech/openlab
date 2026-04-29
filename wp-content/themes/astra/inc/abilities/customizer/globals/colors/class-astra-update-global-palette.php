<?php
/**
 * Update Global Palette Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Global_Palette
 */
class Astra_Update_Global_Palette extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-global-palette';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Global Palette', 'astra' );
		$this->description = __( 'Updates the Astra theme global color palette. You can apply a preset (Oak, Lily, Viola, Cedar, Willow, Rose, Sage, Flare, Maple, Birch, Dark) or set individual colors. Colors should be in hex format (e.g., #046bd2).', 'astra' );

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
				'preset'         => array(
					'type'        => 'string',
					'description' => 'The preset name to apply (e.g., "Oak", "Lily", "Viola", "Cedar", "Willow", "Rose", "Sage", "Flare", "Maple", "Birch", "Dark"). This will apply all preset colors.',
					'enum'        => array( 'Oak', 'Lily', 'Viola', 'Cedar', 'Willow', 'Rose', 'Sage', 'Flare', 'Maple', 'Birch', 'Dark' ),
				),
				'palette_id'     => array(
					'type'        => 'string',
					'description' => 'The palette ID to update (palette_1, palette_2, palette_3, palette_4). If not provided, updates the current active palette.',
					'enum'        => array( 'palette_1', 'palette_2', 'palette_3', 'palette_4' ),
				),
				'colors'         => array(
					'type'        => 'object',
					'description' => 'Color values to update. Use color index (0-8) as keys and hex color values. Example: {"0": "#046bd2", "1": "#045cb4"}. These will override preset colors if both are provided.',
					'properties'  => array(
						'0' => array(
							'type'        => 'string',
							'description' => 'Color 0 - Primary color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'1' => array(
							'type'        => 'string',
							'description' => 'Color 1 - Primary hover color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'2' => array(
							'type'        => 'string',
							'description' => 'Color 2 - Heading color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'3' => array(
							'type'        => 'string',
							'description' => 'Color 3 - Text color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'4' => array(
							'type'        => 'string',
							'description' => 'Color 4 - Background color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'5' => array(
							'type'        => 'string',
							'description' => 'Color 5 - Secondary background color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'6' => array(
							'type'        => 'string',
							'description' => 'Color 6 - Border color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'7' => array(
							'type'        => 'string',
							'description' => 'Color 7 - Secondary border color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
						'8' => array(
							'type'        => 'string',
							'description' => 'Color 8 - Accent color (hex format)',
							'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
						),
					),
				),
				'set_as_current' => array(
					'type'        => 'boolean',
					'description' => 'If true, sets this palette as the currently active palette.',
					'default'     => true,
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
				'palette_id'     => array(
					'type'        => 'string',
					'description' => 'Updated palette ID.',
				),
				'is_current'     => array(
					'type'        => 'boolean',
					'description' => 'Whether this palette is the currently active one.',
				),
				'colors'         => array(
					'type'        => 'object',
					'description' => 'Updated palette colors (indices 0-8).',
				),
				'color_labels'   => array(
					'type'        => 'object',
					'description' => 'Labels for each color index.',
				),
				'applied_preset' => array(
					'type'        => 'string',
					'description' => 'Name of the preset applied, if any.',
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
			'apply Oak preset to global palette',
			'set global palette to Lily',
			'use Viola color preset',
			'apply Cedar palette colors',
			'change theme colors to Flare preset',
			'switch to Willow color scheme',
			'update palette primary color to #046bd2',
			'change palette heading color to #2c3e50',
			'set palette text color to #34495e',
			'update palette background to #ffffff',
			'apply preset and override primary color',
			'set active palette to palette_3',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$palette_data = get_option( 'astra-color-palettes', array() );

		if ( empty( $palette_data ) && ! is_array( $palette_data ) ) {
			if ( class_exists( 'Astra_Global_Palette' ) ) {
				$palette_data = Astra_Global_Palette::get_default_color_palette();
			} else {
				return Astra_Abilities_Response::error(
					__( 'Astra Global Palette is not available.', 'astra' ),
					__( 'Please ensure Astra theme is active.', 'astra' )
				);
			}
		}

		$current_palette_id = isset( $palette_data['currentPalette'] ) && is_string( $palette_data['currentPalette'] ) ? $palette_data['currentPalette'] : 'palette_1';
		$palette_id         = isset( $args['palette_id'] ) ? sanitize_text_field( $args['palette_id'] ) : $current_palette_id;

		if ( ! in_array( $palette_id, array( 'palette_1', 'palette_2', 'palette_3', 'palette_4' ), true ) ) {
			return Astra_Abilities_Response::error(
				/* translators: %s: palette ID */
				sprintf( __( 'Invalid palette ID: %s.', 'astra' ), $palette_id ),
				__( 'Must be one of: palette_1, palette_2, palette_3, palette_4', 'astra' )
			);
		}

		$palette_data['palettes'] = isset( $palette_data['palettes'] ) && is_array( $palette_data['palettes'] ) ? $palette_data['palettes'] : array();

		if ( ! isset( $palette_data['palettes'][ $palette_id ] ) ) {
			$palette_data['palettes'][ $palette_id ] = array_fill( 0, 9, '#000000' );
		}

		$applied_preset = '';

		// Apply preset if provided.
		if ( isset( $args['preset'] ) && ! empty( $args['preset'] ) ) {
			$preset_name = sanitize_text_field( $args['preset'] );

			if ( function_exists( 'astra_get_palette_presets' ) ) {
				$presets = astra_get_palette_presets();

				$preset_key = null;
				foreach ( $presets as $key => $preset_colors ) {
					if ( 0 === strcasecmp( $key, $preset_name ) ) {
						$preset_key = $key;
						break;
					}
				}

				if ( $preset_key && isset( $presets[ $preset_key ] ) ) {
					$palette_data['palettes'][ $palette_id ] = $presets[ $preset_key ];
					$applied_preset                          = $preset_key;
				} else {
					return Astra_Abilities_Response::error(
						/* translators: %s: preset name */
						sprintf( __( 'Invalid preset name: %s.', 'astra' ), $preset_name ),
						__( 'Available presets: Oak, Lily, Viola, Cedar, Willow, Rose, Sage, Flare, Maple, Birch, Dark', 'astra' )
					);
				}
			} else {
				return Astra_Abilities_Response::error(
					__( 'Preset function not available.', 'astra' ),
					__( 'Please ensure Astra theme is active and up to date.', 'astra' )
				);
			}
		}

		// Apply individual color overrides.
		if ( isset( $args['colors'] ) && is_array( $args['colors'] ) ) {
			foreach ( $args['colors'] as $index => $color ) {
				$color_index = intval( $index );
				if ( $color_index < 0 || $color_index > 8 ) {
					continue;
				}

				$color = sanitize_hex_color( $color );
				if ( empty( $color ) ) {
					continue;
				}

				$palette_data['palettes'][ $palette_id ][ $color_index ] = $color;
			}
		}

		if ( empty( $applied_preset ) && ( ! isset( $args['colors'] ) || empty( $args['colors'] ) ) ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide either a preset name or color values to update.', 'astra' )
			);
		}

		// Set as current palette if requested (default is true).
		$set_as_current = isset( $args['set_as_current'] ) ? (bool) $args['set_as_current'] : true;
		if ( $set_as_current ) {
			$palette_data['currentPalette'] = $palette_id;
		}

		update_option( 'astra-color-palettes', $palette_data );

		// Sync with theme options when palette is set as current.
		$palettes           = isset( $palette_data['palettes'] ) && is_array( $palette_data['palettes'] ) ? $palette_data['palettes'] : array();
		$is_current_palette = ( isset( $palette_data['currentPalette'] ) && is_string( $palette_data['currentPalette'] ) ? $palette_data['currentPalette'] : '' ) === $palette_id;
		if ( $is_current_palette && function_exists( 'astra_get_option' ) && defined( 'ASTRA_THEME_SETTINGS' ) ) {
			$global_palette = astra_get_option( 'global-color-palette', array( 'palette' => array() ) );

			if ( ! isset( $global_palette['palette'] ) || ! is_array( $global_palette['palette'] ) ) {
				$global_palette['palette'] = array();
			}

			$active_palette = isset( $palettes[ $palette_id ] ) ? $palettes[ $palette_id ] : array();
			foreach ( $active_palette as $index => $color ) {
				$global_palette['palette'][ $index ] = $color;
			}

			$theme_options = get_option( ASTRA_THEME_SETTINGS, array() );
			if ( ! is_array( $theme_options ) ) {
				$theme_options = array();
			}
			$theme_options['global-color-palette'] = $global_palette;
			update_option( ASTRA_THEME_SETTINGS, $theme_options );
		}

		// Prepare response.
		$updated_palette = isset( $palettes[ $palette_id ] ) ? $palettes[ $palette_id ] : array();
		$is_current      = ( isset( $palette_data['currentPalette'] ) && is_string( $palette_data['currentPalette'] ) ? $palette_data['currentPalette'] : '' ) === $palette_id;

		$colors = array();
		for ( $i = 0; $i <= 8; $i++ ) {
			$colors[ (string) $i ] = isset( $updated_palette[ $i ] ) ? $updated_palette[ $i ] : '';
		}

		$message = ! empty( $applied_preset )
			/* translators: %s: preset name */
			? sprintf( __( 'Global palette updated successfully with %s preset.', 'astra' ), $applied_preset )
			: __( 'Global palette updated successfully.', 'astra' );

		return Astra_Abilities_Response::success(
			$message,
			array(
				'palette_id'     => $palette_id,
				'is_current'     => $is_current,
				'colors'         => $colors,
				'color_labels'   => array(
					'0' => 'Primary',
					'1' => 'Primary Hover',
					'2' => 'Heading',
					'3' => 'Text',
					'4' => 'Background',
					'5' => 'Secondary Background',
					'6' => 'Border',
					'7' => 'Secondary Border',
					'8' => 'Accent',
				),
				'applied_preset' => ! empty( $applied_preset ) ? $applied_preset : null,
			)
		);
	}
}

Astra_Update_Global_Palette::register();
