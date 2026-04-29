<?php
/**
 * Get Headings Font Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Headings_Font
 */
class Astra_Get_Headings_Font extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-font-heading';
		$this->label       = __( 'Get Astra Headings Font', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme headings font settings including font family, weight, line height, and other typography properties for all headings.', 'astra' );
		$this->category    = 'astra';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array();
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
					'description' => 'Current headings font family.',
				),
				'font_weight' => array(
					'type'        => 'string',
					'description' => 'Current headings font weight.',
				),
				'font_extras' => array(
					'type'        => 'object',
					'description' => 'Additional typography settings (line height, text transform, letter spacing).',
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
			'get current headings font',
			'show headings font settings',
			'view headings typography',
			'display headings font family',
			'get headings font configuration',
			'show all headings font',
			'get h1-h6 common font settings',
			'show global heading font',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		return Astra_Abilities_Response::success(
			__( 'Retrieved headings font settings successfully.', 'astra' ),
			array(
				'font_family' => astra_get_option( 'headings-font-family', '' ),
				'font_weight' => astra_get_option( 'headings-font-weight', '' ),
				'font_extras' => astra_get_option( 'headings-font-extras', array() ),
			)
		);
	}
}

Astra_Get_Headings_Font::register();
