<?php
/**
 * Update Headings Font Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Headings_Font
 */
class Astra_Update_Headings_Font extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-font-heading';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Headings Font', 'astra' );
		$this->description = __( 'Updates the Astra theme headings font family, weight, and other typography settings for all headings.', 'astra' );

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
					'description' => 'Font family name for all headings',
				),
				'font_weight'    => array(
					'type'        => 'string',
					'description' => 'Font weight for all headings',
				),
				'line_height'    => array(
					'type'        => 'string',
					'description' => 'Line height value for headings',
				),
				'text_transform' => array(
					'type'        => 'string',
					'description' => 'Text transform for headings',
					'enum'        => array( 'uppercase', 'lowercase', 'capitalize', 'none', '' ),
				),
				'letter_spacing' => array(
					'type'        => 'string',
					'description' => 'Letter spacing value for headings',
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
					'description' => 'Updated headings font family.',
				),
				'font_weight' => array(
					'type'        => 'string',
					'description' => 'Updated headings font weight.',
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
			'update headings font to Playfair Display',
			'set all heading fonts to Montserrat',
			'change headings font family to Georgia',
			'update heading font weight to 700',
			'set headings text transform to uppercase',
			'change headings line height to 1.2',
			'set consistent font for h1 through h6',
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
			astra_update_option( 'headings-font-family', $formatted_font );
		}

		if ( isset( $args['font_weight'] ) ) {
			astra_update_option( 'headings-font-weight', sanitize_text_field( $args['font_weight'] ) );
		}

		Astra_Abilities_Helper::update_font_extras( $args, 'headings-font-extras' );

		return Astra_Abilities_Response::success(
			__( 'Headings font settings updated successfully.', 'astra' ),
			array(
				'font_family' => astra_get_option( 'headings-font-family', '' ),
				'font_weight' => astra_get_option( 'headings-font-weight', '' ),
				'font_extras' => astra_get_option( 'headings-font-extras', array() ),
			)
		);
	}
}

Astra_Update_Headings_Font::register();
