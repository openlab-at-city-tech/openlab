<?php
/**
 * Get Background Colors Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Background_Colors
 */
class Astra_Get_Background_Colors extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-color-background';
		$this->label       = __( 'Get Astra Background Colors', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme background color settings including site background and content background configurations.', 'astra' );
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
				'site_background'    => array(
					'type'        => 'object',
					'description' => 'Site background configuration (responsive with desktop, tablet, mobile).',
				),
				'content_background' => array(
					'type'        => 'object',
					'description' => 'Content area background configuration (responsive with desktop, tablet, mobile).',
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
			'get current background colors',
			'show site background settings',
			'view content background configuration',
			'display background color values',
			'get site and content backgrounds',
			'show current background setup',
			'view background settings',
			'get background image settings',
			'show background repeat and position',
			'get complete background configuration',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$site_background    = astra_get_option( 'site-layout-outside-bg-obj-responsive', array() );
		$content_background = astra_get_option( 'content-bg-obj-responsive', array() );

		return Astra_Abilities_Response::success(
			__( 'Retrieved background color settings successfully.', 'astra' ),
			array(
				'site_background'    => $site_background,
				'content_background' => $content_background,
			)
		);
	}
}

Astra_Get_Background_Colors::register();
