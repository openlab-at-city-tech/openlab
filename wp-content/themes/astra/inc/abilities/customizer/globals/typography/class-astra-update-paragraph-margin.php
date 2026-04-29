<?php
/**
 * Update Paragraph Margin Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Paragraph_Margin
 */
class Astra_Update_Paragraph_Margin extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-paragraph-margin';
		$this->category    = 'astra';
		$this->label       = __( 'Update Paragraph Margin', 'astra' );
		$this->description = __( 'Updates the paragraph margin bottom in the Astra theme.', 'astra' );

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
				'margin_bottom' => array(
					'type'        => 'number',
					'description' => 'Margin bottom value in em (0.5 to 5)',
					'minimum'     => 0.5,
					'maximum'     => 5,
				),
			),
			'required'   => array( 'margin_bottom' ),
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
				'margin_bottom' => array(
					'type'        => 'number',
					'description' => 'Updated paragraph margin bottom value in em.',
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
			'set paragraph margin to 1.5em',
			'update paragraph bottom margin to 2em',
			'change paragraph spacing to 1em',
			'increase paragraph spacing',
			'decrease paragraph margin',
			'set paragraph gap to 1.8em',
			'update paragraph bottom spacing',
			'change space between paragraphs',
			'set paragraph margin bottom to 1.2em',
			'update text block spacing',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! isset( $args['margin_bottom'] ) ) {
			return Astra_Abilities_Response::error(
				__( 'Margin bottom is required.', 'astra' ),
				''
			);
		}

		$margin_bottom = floatval( $args['margin_bottom'] );

		// Validate range.
		if ( $margin_bottom < 0.5 || $margin_bottom > 5 ) {
			return Astra_Abilities_Response::error(
				__( 'Invalid margin value.', 'astra' ),
				__( 'Margin bottom must be between 0.5 and 5 em.', 'astra' )
			);
		}

		astra_update_option( 'para-margin-bottom', $margin_bottom );

		return Astra_Abilities_Response::success(
			/* translators: %s: margin bottom value */
			sprintf( __( 'Paragraph margin bottom updated to %s em.', 'astra' ), $margin_bottom ),
			array(
				'margin_bottom' => $margin_bottom,
			)
		);
	}
}

Astra_Update_Paragraph_Margin::register();
