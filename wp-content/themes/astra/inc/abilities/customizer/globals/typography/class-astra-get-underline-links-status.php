<?php
/**
 * Get Underline Links Status Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Underline_Links_Status
 */
class Astra_Get_Underline_Links_Status extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-link-underline';
		$this->label       = __( 'Get Underline Links Status', 'astra' );
		$this->description = __( 'Retrieves whether content link underlines are enabled or disabled in the Astra theme.', 'astra' );
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
				'enabled' => array(
					'type'        => 'boolean',
					'description' => 'Whether content link underlines are enabled.',
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
			'get link underline status',
			'show link underline setting',
			'check if links are underlined',
			'view underline links configuration',
			'display link decoration status',
			'get content links underline state',
			'show current link underline setting',
			'check link underline enabled',
			'view link text decoration status',
			'display if underlines are on',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$enabled = astra_get_option( 'underline-content-links', false );

		return Astra_Abilities_Response::success(
			/* translators: %s: enabled or disabled */
			sprintf( __( 'Content link underlines are %s.', 'astra' ), $enabled ? __( 'enabled', 'astra' ) : __( 'disabled', 'astra' ) ),
			array(
				'enabled' => (bool) $enabled,
			)
		);
	}
}

Astra_Get_Underline_Links_Status::register();
