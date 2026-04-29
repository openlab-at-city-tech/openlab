<?php
/**
 * Get Paragraph Margin Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Paragraph_Margin
 */
class Astra_Get_Paragraph_Margin extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-paragraph-margin';
		$this->label       = __( 'Get Paragraph Margin', 'astra' );
		$this->description = __( 'Retrieves the current paragraph margin bottom setting in the Astra theme.', 'astra' );
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
				'margin_bottom' => array(
					'type'        => 'number',
					'description' => 'Current paragraph margin bottom value in em.',
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
			'get current paragraph margin',
			'show paragraph spacing',
			'view paragraph margin bottom',
			'display paragraph gap setting',
			'get paragraph bottom spacing',
			'show current paragraph margin value',
			'view paragraph vertical spacing',
			'display paragraph separation',
			'get space between paragraphs',
			'show paragraph margin settings',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$margin_bottom = floatval( astra_get_option( 'para-margin-bottom', 1.6 ) );

		return Astra_Abilities_Response::success(
			/* translators: %s: margin bottom value */
			sprintf( __( 'Current paragraph margin bottom: %s em', 'astra' ), $margin_bottom ),
			array(
				'margin_bottom' => $margin_bottom,
			)
		);
	}
}

Astra_Get_Paragraph_Margin::register();
