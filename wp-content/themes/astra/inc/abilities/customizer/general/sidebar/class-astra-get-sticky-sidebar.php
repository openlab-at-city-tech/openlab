<?php
/**
 * Get Sticky Sidebar Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Sticky_Sidebar
 */
class Astra_Get_Sticky_Sidebar extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-sidebar-sticky';
		$this->label       = __( 'Get Astra Sticky Sidebar Status', 'astra' );
		$this->description = __( 'Retrieves the sticky sidebar setting for the Astra theme (enabled or disabled).', 'astra' );
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
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'get sticky sidebar status',
			'show sticky sidebar setting',
			'view sticky sidebar',
			'display sticky sidebar status',
			'get sticky sidebar enabled',
			'show if sticky sidebar is on',
			'view sticky sidebar option',
			'display sticky sidebar setting',
			'get sticky sidebar configuration',
			'show sticky sidebar state',
			'view if sidebar is sticky',
			'display sidebar sticky status',
			'get sidebar sticky setting',
			'show sidebar sticky option',
			'view sticky sidebar enabled',
			'display sticky sidebar enabled',
			'get current sticky sidebar',
			'show sticky sidebar active',
			'view sidebar sticky state',
			'display if sidebar sticky',
			'get sticky sidebar on or off',
			'show sticky sidebar feature',
			'view sticky sidebar functionality',
			'display sticky sidebar behavior',
			'get sidebar sticky behavior',
			'show if sidebar stays fixed',
			'view sidebar sticky option',
			'display sidebar sticky configuration',
			'get site sticky sidebar',
			'show sticky sidebar preference',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$enabled = astra_get_option( 'site-sticky-sidebar', false );

		return Astra_Abilities_Response::success(
			__( 'Retrieved sticky sidebar status successfully.', 'astra' ),
			array(
				'enabled'       => (bool) $enabled,
				'enabled_label' => $enabled ? 'Enabled' : 'Disabled',
			)
		);
	}
}

Astra_Get_Sticky_Sidebar::register();
