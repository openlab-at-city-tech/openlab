<?php
/**
 * Update Sidebar Layout Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Sidebar_Layout
 */
class Astra_Update_Sidebar_Layout extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-sidebar-layout';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Sidebar Layout', 'astra' );
		$this->description = __( 'Updates the default sidebar layout setting for the Astra theme (no sidebar, left sidebar, or right sidebar).', 'astra' );

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
				'layout' => array(
					'type'        => 'string',
					'description' => 'Sidebar layout. Options: "no-sidebar" (No Sidebar), "left-sidebar" (Left Sidebar), "right-sidebar" (Right Sidebar).',
					'enum'        => array( 'no-sidebar', 'left-sidebar', 'right-sidebar' ),
				),
			),
			'required'   => array( 'layout' ),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'set sidebar to right',
			'change sidebar to left',
			'disable sidebar',
			'enable right sidebar',
			'set left sidebar',
			'remove sidebar',
			'add right sidebar',
			'change sidebar position to right',
			'set no sidebar',
			'enable left sidebar',
			'switch to right sidebar',
			'change to no sidebar',
			'set sidebar layout to left',
			'update sidebar to right side',
			'change sidebar placement to left',
			'set sidebar on right',
			'move sidebar to left',
			'configure sidebar to right',
			'update sidebar position to left',
			'set default sidebar to right',
			'change sidebar to right side',
			'enable sidebar on left',
			'set sidebar layout right',
			'update to left sidebar',
			'change default sidebar position',
			'set site sidebar to right',
			'move sidebar right',
			'configure left sidebar',
			'update sidebar layout to no sidebar',
			'change to right sidebar layout',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! isset( $args['layout'] ) ) {
			return Astra_Abilities_Response::error(
				__( 'Layout is required.', 'astra' ),
				__( 'Please provide a layout: no-sidebar, left-sidebar, or right-sidebar.', 'astra' )
			);
		}

		$layout        = sanitize_text_field( $args['layout'] );
		$valid_layouts = array( 'no-sidebar', 'left-sidebar', 'right-sidebar' );

		if ( ! in_array( $layout, $valid_layouts, true ) ) {
			return Astra_Abilities_Response::error(
				sprintf(
					/* translators: %s: layout value */
					__( 'Invalid layout: %s.', 'astra' ),
					$layout
				),
				__( 'Valid options: no-sidebar, left-sidebar, right-sidebar', 'astra' )
			);
		}

		$layout_labels = array(
			'no-sidebar'    => 'No Sidebar',
			'left-sidebar'  => 'Left Sidebar',
			'right-sidebar' => 'Right Sidebar',
		);

		astra_update_option( 'site-sidebar-layout', $layout );

		$message = sprintf(
			/* translators: %s: layout label */
			__( 'Sidebar layout set to %s.', 'astra' ),
			$layout_labels[ $layout ]
		);

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
				'layout'  => $layout,
			)
		);
	}
}

Astra_Update_Sidebar_Layout::register();
