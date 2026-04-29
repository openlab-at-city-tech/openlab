<?php
/**
 * Update Sticky Sidebar Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Sticky_Sidebar
 */
class Astra_Update_Sticky_Sidebar extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-sidebar-sticky';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Sticky Sidebar Status', 'astra' );
		$this->description = __( 'Updates the sticky sidebar setting for the Astra theme (enable or disable).', 'astra' );

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
					'description' => 'Enable or disable sticky sidebar.',
				),
			),
			'required'   => array( 'enabled' ),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'enable sticky sidebar',
			'disable sticky sidebar',
			'turn on sticky sidebar',
			'turn off sticky sidebar',
			'activate sticky sidebar',
			'deactivate sticky sidebar',
			'enable sidebar sticky',
			'disable sidebar sticky',
			'make sidebar sticky',
			'remove sticky sidebar',
			'set sticky sidebar on',
			'set sticky sidebar off',
			'turn sticky sidebar on',
			'turn sticky sidebar off',
			'enable sidebar to stick',
			'disable sidebar sticking',
			'make sidebar stay fixed',
			'remove sidebar sticky behavior',
			'enable sticky sidebar feature',
			'disable sticky sidebar feature',
			'activate sidebar sticky',
			'deactivate sidebar sticky',
			'turn on sidebar sticky',
			'turn off sidebar sticky',
			'enable sidebar fixed position',
			'disable sidebar fixed position',
			'make sidebar sticky on scroll',
			'remove sidebar sticky on scroll',
			'enable sticky sidebar functionality',
			'disable sticky sidebar functionality',
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
				__( 'Enabled status is required.', 'astra' ),
				__( 'Please provide enabled as true or false.', 'astra' )
			);
		}

		$enabled = (bool) $args['enabled'];

		astra_update_option( 'site-sticky-sidebar', $enabled );

		$message = sprintf(
			/* translators: %s: enabled or disabled */
			__( 'Sticky sidebar %s.', 'astra' ),
			$enabled ? __( 'enabled', 'astra' ) : __( 'disabled', 'astra' )
		);

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
				'enabled' => $enabled,
			)
		);
	}
}

Astra_Update_Sticky_Sidebar::register();
