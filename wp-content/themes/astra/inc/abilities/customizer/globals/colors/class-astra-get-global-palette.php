<?php
/**
 * Get Global Palette Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Global_Palette
 */
class Astra_Get_Global_Palette extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-global-palette';
		$this->label       = __( 'Get Astra Global Palette', 'astra' );
		$this->description = __( 'Retrieves the current active Astra theme global color palette (palette_1 through palette_4) with its color values. Also returns all available color presets (Oak, Viola, Cedar, Willow, Lily, Rose, Sage, Flare, Maple, Birch, Dark) that can be applied to any palette.', 'astra' );
		$this->category    = 'astra';
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
				'palette_id' => array(
					'type'        => 'string',
					'description' => 'Optional: Specific palette ID to retrieve (palette_1, palette_2, palette_3, palette_4). If not provided, returns the current active palette.',
					'enum'        => array( 'palette_1', 'palette_2', 'palette_3', 'palette_4' ),
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
				'active_palette'    => array(
					'type'        => 'object',
					'description' => 'Active palette details including palette_id, is_current flag, and colors with hex values and labels.',
				),
				'current_palette'   => array(
					'type'        => 'string',
					'description' => 'Current active palette ID.',
				),
				'available_presets' => array(
					'type'        => 'object',
					'description' => 'Available color presets with name and color values.',
				),
				'total_presets'     => array(
					'type'        => 'integer',
					'description' => 'Total number of available presets.',
				),
				'all_palettes'      => array(
					'type'        => 'object',
					'description' => 'All palettes (palette_1 through palette_4) with their colors when no specific palette is requested.',
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
			'get current global palette',
			'show active color palette',
			'view global color scheme',
			'display theme palette colors',
			'get palette settings',
			'show current color palette values',
			'get all palette colors',
			'display global color settings',
			'show current color values',
			'get palette 1 colors',
			'view available color presets',
			'list all color presets',
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

		$requested_palette_id = isset( $args['palette_id'] ) ? sanitize_text_field( $args['palette_id'] ) : null;
		$current_palette_id   = isset( $palette_data['currentPalette'] ) && is_string( $palette_data['currentPalette'] ) ? $palette_data['currentPalette'] : 'palette_1';
		$palette_id           = null !== $requested_palette_id ? $requested_palette_id : $current_palette_id;

		if ( ! in_array( $palette_id, array( 'palette_1', 'palette_2', 'palette_3', 'palette_4' ), true ) ) {
			return Astra_Abilities_Response::error(
				/* translators: %s: palette ID */
				sprintf( __( 'Invalid palette ID: %s.', 'astra' ), $palette_id ),
				__( 'Must be one of: palette_1, palette_2, palette_3, palette_4', 'astra' )
			);
		}

		$palettes       = isset( $palette_data['palettes'] ) && is_array( $palette_data['palettes'] ) ? $palette_data['palettes'] : array();
		$palette_colors = isset( $palettes[ $palette_id ] ) ? $palettes[ $palette_id ] : array_fill( 0, 9, '#000000' );

		// Get available presets with full color data.
		$available_presets = array();
		if ( function_exists( 'astra_get_palette_presets' ) ) {
			$presets = astra_get_palette_presets();

			foreach ( $presets as $preset_name => $preset_colors ) {
				$formatted_colors = array();
				foreach ( $preset_colors as $index => $color ) {
					$formatted_colors[ (string) $index ] = array(
						'hex'   => $color,
						'label' => $this->get_color_label( $index ),
					);
				}
				$available_presets[ $preset_name ] = array(
					'name'   => $preset_name,
					'colors' => $formatted_colors,
				);
			}
		}

		// Get all palettes if requesting current palette.
		$all_palettes = null;
		if ( ! $requested_palette_id ) {
			$all_palettes = array();
			foreach ( array( 'palette_1', 'palette_2', 'palette_3', 'palette_4' ) as $pid ) {
				if ( isset( $palette_data['palettes'][ $pid ] ) ) {
					$colors = array();
					for ( $i = 0; $i <= 8; $i++ ) {
						$colors[ (string) $i ] = isset( $palette_data['palettes'][ $pid ][ $i ] ) ? $palette_data['palettes'][ $pid ][ $i ] : '';
					}
					$all_palettes[ $pid ] = array(
						'is_current' => $pid === $current_palette_id,
						'colors'     => $colors,
					);
				}
			}
		}

		// Format current palette colors with labels.
		$formatted_current_colors = array();
		foreach ( $palette_colors as $index => $color ) {
			$formatted_current_colors[ (string) $index ] = array(
				'hex'   => $color,
				'label' => $this->get_color_label( $index ),
			);
		}

		return Astra_Abilities_Response::success(
			/* translators: 1: palette ID, 2: number of colors, 3: number of presets, 4: preset names */
			sprintf(
				__( 'Retrieved active global palette: %1$s (currently using %2$d colors). Also retrieved %3$d available color presets: %4$s.', 'astra' ),
				$palette_id,
				count( $formatted_current_colors ),
				count( $available_presets ),
				implode( ', ', array_keys( $available_presets ) )
			),
			array(
				'active_palette'    => array(
					'palette_id' => $palette_id,
					'is_current' => $palette_id === $current_palette_id,
					'colors'     => $formatted_current_colors,
				),
				'current_palette'   => $current_palette_id,
				'available_presets' => $available_presets,
				'total_presets'     => count( $available_presets ),
				'all_palettes'      => null !== $all_palettes ? $all_palettes : new stdClass(),
			)
		);
	}

	/**
	 * Get color label by index.
	 *
	 * @param int $index Color index (0-8).
	 * @return string Color label.
	 */
	private function get_color_label( $index ) {
		$labels = array(
			0 => 'Brand',
			1 => 'Alternate Brand',
			2 => 'Headings',
			3 => 'Text',
			4 => 'Primary Background',
			5 => 'Secondary Background',
			6 => 'Alternate Background',
			7 => 'Subtle Background',
			8 => 'Other Supporting',
		);

		return isset( $labels[ $index ] ) ? $labels[ $index ] : sprintf( 'Color %d', $index );
	}
}

Astra_Get_Global_Palette::register();
