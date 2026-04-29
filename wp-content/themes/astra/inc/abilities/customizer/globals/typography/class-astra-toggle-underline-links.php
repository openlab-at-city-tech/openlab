<?php
/**
 * Toggle Underline Links Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Toggle_Underline_Links
 */
class Astra_Toggle_Underline_Links extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-link-underline';
		$this->category    = 'astra';
		$this->label       = __( 'Toggle Content Link Underlines', 'astra' );
		$this->description = __( 'Enable or disable underlines on content links in the Astra theme.', 'astra' );

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
				'enabled' => array(
					'type'        => 'boolean',
					'description' => 'Whether to enable link underlines',
				),
			),
			'required'   => array( 'enabled' ),
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
				'enabled' => array(
					'type'        => 'boolean',
					'description' => 'Whether content link underlines are now enabled.',
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
			'enable link underlines',
			'disable content link underlines',
			'turn on underlines for links',
			'turn off link underlines',
			'show underlines on all links',
			'hide underlines from links',
			'activate link underline styling',
			'deactivate link underlines',
			'make links underlined',
			'remove underlines from hyperlinks',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! isset( $args['enabled'] ) ) {
			return Astra_Abilities_Response::error(
				__( 'Enabled parameter is required.', 'astra' ),
				''
			);
		}

		$enabled = (bool) $args['enabled'];

		astra_update_option( 'underline-content-links', $enabled );

		return Astra_Abilities_Response::success(
			/* translators: %s: enabled or disabled */
			sprintf( __( 'Content link underlines %s.', 'astra' ), $enabled ? __( 'enabled', 'astra' ) : __( 'disabled', 'astra' ) ),
			array(
				'enabled' => $enabled,
			)
		);
	}
}

Astra_Toggle_Underline_Links::register();
