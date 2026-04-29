<?php
/**
 * Update Body Font Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Body_Font
 */
class Astra_Update_Body_Font extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-font-body';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Body Font', 'astra' );
		$this->description = __( 'Updates the Astra theme body font family, weight, size, and other typography settings. IMPORTANT: When user specifies size like "16px" or "1.5rem", you must separate the numeric value from the unit: font_size.desktop = 16 (number), font_size.desktop-unit = "px" (string).', 'astra' );

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
				'font_family'    => array(
					'type'        => 'string',
					'description' => 'Font family name (e.g., "Inter", "Roboto", "Arial")',
				),
				'font_weight'    => array(
					'type'        => 'string',
					'description' => 'Font weight (e.g., "400", "500", "700")',
				),
				'font_size'      => array(
					'type'        => 'object',
					'description' => 'Font size object with separate numeric values and unit strings. Parse user input: "20px" becomes desktop:"20", desktop-unit:"px". Example: {"desktop": "16", "tablet": "16", "mobile": "16", "desktop-unit": "px", "tablet-unit": "px", "mobile-unit": "px"}',
					'properties'  => array(
						'desktop'      => array(
							'type'        => 'string',
							'description' => 'Numeric value as string (e.g., "16" not "16px")',
						),
						'tablet'       => array(
							'type'        => 'string',
							'description' => 'Numeric value as string',
						),
						'mobile'       => array(
							'type'        => 'string',
							'description' => 'Numeric value as string',
						),
						'desktop-unit' => array(
							'type'        => 'string',
							'description' => 'Unit only: px, em, rem, or vw',
							'enum'        => array( 'px', 'em', 'rem', 'vw' ),
						),
						'tablet-unit'  => array(
							'type'        => 'string',
							'description' => 'Unit only: px, em, rem, or vw',
							'enum'        => array( 'px', 'em', 'rem', 'vw' ),
						),
						'mobile-unit'  => array(
							'type'        => 'string',
							'description' => 'Unit only: px, em, rem, or vw',
							'enum'        => array( 'px', 'em', 'rem', 'vw' ),
						),
					),
				),
				'line_height'    => array(
					'type'        => 'string',
					'description' => 'Line height value',
				),
				'text_transform' => array(
					'type'        => 'string',
					'description' => 'Text transform (uppercase, lowercase, capitalize, none)',
					'enum'        => array( 'uppercase', 'lowercase', 'capitalize', 'none', '' ),
				),
				'letter_spacing' => array(
					'type'        => 'string',
					'description' => 'Letter spacing value',
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
				'font_family' => array(
					'type'        => 'string',
					'description' => 'Updated body font family.',
				),
				'font_weight' => array(
					'type'        => 'string',
					'description' => 'Updated body font weight.',
				),
				'font_size'   => array(
					'type'        => 'object',
					'description' => 'Updated responsive font size with desktop, tablet, mobile values and units.',
				),
				'font_extras' => array(
					'type'        => 'object',
					'description' => 'Updated additional typography settings (line height, text transform, letter spacing).',
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
			'update body font to Inter',
			'set body font family to Roboto',
			'change body font size to 16px',
			'update body typography to Inter 18px',
			'set body text font to Arial',
			'change body font weight to 400',
			'set body font line height to 1.6',
			'update body text letter spacing',
			'set responsive body font sizes',
			'change body font to system font',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( isset( $args['font_family'] ) ) {
			$font_family    = sanitize_text_field( $args['font_family'] );
			$formatted_font = "'" . $font_family . "', sans-serif";
			astra_update_option( 'body-font-family', $formatted_font );
		}

		if ( isset( $args['font_weight'] ) ) {
			astra_update_option( 'body-font-weight', sanitize_text_field( $args['font_weight'] ) );
		}

		if ( isset( $args['font_size'] ) && is_array( $args['font_size'] ) ) {
			$existing = astra_get_option( 'font-size-body', array() );
			$merged   = array_merge( $existing, $args['font_size'] );
			astra_update_option( 'font-size-body', Astra_Abilities_Helper::sanitize_responsive_typo( $merged ) );
		}

		Astra_Abilities_Helper::update_font_extras( $args, 'body-font-extras' );

		return Astra_Abilities_Response::success(
			__( 'Body font settings updated successfully.', 'astra' ),
			array(
				'font_family' => astra_get_option( 'body-font-family', '' ),
				'font_weight' => astra_get_option( 'body-font-weight', '' ),
				'font_size'   => astra_get_option( 'font-size-body', array() ),
				'font_extras' => astra_get_option( 'body-font-extras', array() ),
			)
		);
	}
}

Astra_Update_Body_Font::register();
