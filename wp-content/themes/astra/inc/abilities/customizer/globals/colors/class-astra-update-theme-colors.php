<?php
/**
 * Update Theme Colors Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Theme_Colors
 */
class Astra_Update_Theme_Colors extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-theme-color';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Theme Colors', 'astra' );
		$this->description = __( 'Updates the Astra theme global colors including accent color, link colors, heading colors, body text color, and border color. These are the core colors that define your theme\'s color scheme.', 'astra' );

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
				'accent_color'     => array(
					'type'        => 'string',
					'description' => 'Theme accent color (hex format, e.g., #046bd2). Used for buttons, highlights, and other accent elements.',
					'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
				),
				'link_color'       => array(
					'type'        => 'string',
					'description' => 'Link color (hex format). Sets the color for all links on the site.',
					'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
				),
				'link_hover_color' => array(
					'type'        => 'string',
					'description' => 'Link hover color (hex format). Sets the color when hovering over links.',
					'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
				),
				'heading_color'    => array(
					'type'        => 'string',
					'description' => 'Heading color for H1-H6 (hex format). Sets the default color for all heading elements.',
					'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
				),
				'text_color'       => array(
					'type'        => 'string',
					'description' => 'Body text color (hex format). Sets the main text color for the website.',
					'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
				),
				'border_color'     => array(
					'type'        => 'string',
					'description' => 'Border color (hex format). Sets the color for borders throughout the site.',
					'pattern'     => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
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
				'updated'        => array(
					'type'        => 'array',
					'description' => 'List of updated color names.',
					'items'       => array( 'type' => 'string' ),
				),
				'updated_colors' => array(
					'type'        => 'object',
					'description' => 'Map of updated color keys to their new hex values.',
				),
				'current_colors' => array(
					'type'        => 'object',
					'description' => 'All current theme color values after the update.',
				),
				'color_labels'   => array(
					'type'        => 'object',
					'description' => 'Human-readable labels for each color key.',
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
			'set accent color to blue',
			'change theme accent color to #046bd2',
			'update link color to match brand',
			'set link color to #3498db',
			'change link hover color to darker blue',
			'update heading color to dark gray',
			'set all headings color to #2c3e50',
			'change body text color to #333333',
			'set border color to light gray',
			'change all theme colors at once',
			'update heading and text colors',
			'set link color and hover color',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		// Map of input args to Astra settings keys.
		$color_settings = array(
			'accent_color'     => 'theme-color',
			'link_color'       => 'link-color',
			'link_hover_color' => 'link-h-color',
			'heading_color'    => 'heading-base-color',
			'text_color'       => 'text-color',
			'border_color'     => 'border-color',
		);

		// Check if at least one color is provided.
		$has_updates = false;
		foreach ( array_keys( $color_settings ) as $key ) {
			if ( ! empty( $args[ $key ] ) ) {
				$has_updates = true;
				break;
			}
		}

		if ( ! $has_updates ) {
			return Astra_Abilities_Response::error(
				__( 'No colors specified.', 'astra' ),
				__( 'Please provide at least one color to update.', 'astra' )
			);
		}

		$updated        = array();
		$updated_colors = array();

		foreach ( $color_settings as $arg_key => $settings_key ) {
			if ( ! empty( $args[ $arg_key ] ) ) {
				$color = sanitize_hex_color( $args[ $arg_key ] );

				if ( empty( $color ) ) {
					continue;
				}

				astra_update_option( $settings_key, $color );
				$updated[]                  = str_replace( '_', ' ', $arg_key );
				$updated_colors[ $arg_key ] = $color;
			}
		}

		if ( empty( $updated ) ) {
			return Astra_Abilities_Response::error(
				__( 'Invalid color values.', 'astra' ),
				__( 'Please provide valid hex color values (e.g., #046bd2).', 'astra' )
			);
		}

		// Get all current values for response.
		$current_colors = array();
		foreach ( $color_settings as $arg_key => $settings_key ) {
			$current_colors[ $arg_key ] = astra_get_option( $settings_key );
		}

		return Astra_Abilities_Response::success(
			/* translators: %s: comma-separated list of updated colors */
			sprintf( __( 'Theme colors updated successfully: %s', 'astra' ), implode( ', ', $updated ) ),
			array(
				'updated'        => $updated,
				'updated_colors' => $updated_colors,
				'current_colors' => $current_colors,
				'color_labels'   => array(
					'accent_color'     => 'Accent Color',
					'link_color'       => 'Link Color',
					'link_hover_color' => 'Link Hover Color',
					'heading_color'    => 'Heading (H1-H6) Color',
					'text_color'       => 'Body Text Color',
					'border_color'     => 'Border Color',
				),
			)
		);
	}
}

Astra_Update_Theme_Colors::register();
