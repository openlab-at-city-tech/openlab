<?php
/**
 * Get Body Font Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Body_Font
 */
class Astra_Get_Body_Font extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-font-body';
		$this->label       = __( 'Get Astra Body Font', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme body font settings including font family, weight, size, line height, and other typography properties.', 'astra' );
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
					'description' => 'Current body font family.',
				),
				'font_weight' => array(
					'type'        => 'string',
					'description' => 'Current body font weight.',
				),
				'font_size'   => array(
					'type'        => 'object',
					'description' => 'Responsive font size with desktop, tablet, mobile values and units.',
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
			'get current body font',
			'show body font settings',
			'view body typography',
			'display body text font',
			'get body font family',
			'show body font weight',
			'view body font size',
			'display body text settings',
			'get body typography configuration',
			'show current body font values',
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
			__( 'Retrieved body font settings successfully.', 'astra' ),
			array(
				'font_family' => astra_get_option( 'body-font-family', '' ),
				'font_weight' => astra_get_option( 'body-font-weight', '' ),
				'font_size'   => astra_get_option( 'font-size-body', array() ),
				'font_extras' => astra_get_option( 'body-font-extras', array() ),
			)
		);
	}
}

Astra_Get_Body_Font::register();
