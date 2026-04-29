<?php
/**
 * Get Sidebar Layout Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Sidebar_Layout
 */
class Astra_Get_Sidebar_Layout extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-sidebar-layout';
		$this->label       = __( 'Get Astra Sidebar Layout', 'astra' );
		$this->description = __( 'Retrieves the default sidebar layout setting for the Astra theme (no sidebar, left sidebar, or right sidebar).', 'astra' );
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
			'get sidebar layout',
			'show sidebar position',
			'view sidebar layout',
			'display sidebar configuration',
			'get default sidebar',
			'show sidebar setting',
			'view sidebar placement',
			'display sidebar layout option',
			'get current sidebar layout',
			'show which sidebar is active',
			'view sidebar configuration',
			'display sidebar position setting',
			'get sidebar layout setting',
			'show default sidebar position',
			'view current sidebar placement',
			'display sidebar layout configuration',
			'get site sidebar layout',
			'show sidebar location',
			'view sidebar side',
			'display where sidebar appears',
			'get sidebar layout option',
			'show sidebar arrangement',
			'view sidebar positioning',
			'display sidebar layout choice',
			'get active sidebar layout',
			'show sidebar display setting',
			'view sidebar layout status',
			'display current sidebar option',
			'get sidebar placement setting',
			'show site sidebar configuration',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$layout = astra_get_option( 'site-sidebar-layout', 'no-sidebar' );

		$layout_labels = array(
			'no-sidebar'    => 'No Sidebar',
			'left-sidebar'  => 'Left Sidebar',
			'right-sidebar' => 'Right Sidebar',
		);

		return Astra_Abilities_Response::success(
			__( 'Retrieved sidebar layout successfully.', 'astra' ),
			array(
				'layout'            => $layout,
				'layout_label'      => isset( $layout_labels[ $layout ] ) ? $layout_labels[ $layout ] : $layout,
				'available_layouts' => $layout_labels,
			)
		);
	}
}

Astra_Get_Sidebar_Layout::register();
