<?php
/**
 * Update Sidebar Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Sidebar
 */
class Astra_Update_Sidebar extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-sidebar';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Sidebar Settings', 'astra' );
		$this->description = __( 'Updates sidebar settings for the Astra theme including layout, style, width, and sticky sidebar.', 'astra' );

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
				'layout'         => array(
					'type'        => 'string',
					'description' => 'Sidebar layout. Options: "no-sidebar", "left-sidebar", "right-sidebar".',
					'enum'        => array( 'no-sidebar', 'left-sidebar', 'right-sidebar' ),
				),
				'style'          => array(
					'type'        => 'string',
					'description' => 'Sidebar style. Options: "unboxed", "boxed".',
					'enum'        => array( 'unboxed', 'boxed' ),
				),
				'width'          => array(
					'type'        => 'integer',
					'description' => 'Sidebar width in percentage (15-50).',
					'minimum'     => 15,
					'maximum'     => 50,
				),
				'sticky_enabled' => array(
					'type'        => 'boolean',
					'description' => 'Enable or disable sticky sidebar.',
				),
			),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'update sidebar settings',
			'configure sidebar',
			'set sidebar to right with boxed style',
			'change sidebar to left 30% boxed',
			'update sidebar layout and style',
			'set sidebar right 25% sticky',
			'configure left sidebar boxed',
			'update sidebar to right unboxed 30%',
			'set no sidebar',
			'change sidebar to left sticky',
			'update sidebar right 35%',
			'configure sidebar left boxed sticky',
			'set sidebar layout to right',
			'update all sidebar settings',
			'change sidebar preferences',
			'set sidebar style and width',
			'update sidebar to left 30%',
			'configure sidebar right boxed 25%',
			'set sidebar with sticky enabled',
			'change sidebar layout and width',
			'update sidebar style to boxed',
			'set right sidebar 30% sticky',
			'configure complete sidebar',
			'update sidebar design',
			'set sidebar appearance',
			'change all sidebar options',
			'update sidebar configuration',
			'set default sidebar settings',
			'configure site sidebar',
			'update sidebar customization',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$updated         = false;
		$update_messages = array();

		if ( isset( $args['layout'] ) ) {
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

			astra_update_option( 'site-sidebar-layout', $layout );
			$updated           = true;
			$layout_labels     = array(
				'no-sidebar'    => 'No Sidebar',
				'left-sidebar'  => 'Left Sidebar',
				'right-sidebar' => 'Right Sidebar',
			);
			$update_messages[] = sprintf( 'Layout set to %s', $layout_labels[ $layout ] );
		}

		if ( isset( $args['style'] ) ) {
			$style        = sanitize_text_field( $args['style'] );
			$valid_styles = array( 'unboxed', 'boxed' );

			if ( ! in_array( $style, $valid_styles, true ) ) {
				return Astra_Abilities_Response::error(
					sprintf(
						/* translators: %s: style value */
						__( 'Invalid style: %s.', 'astra' ),
						$style
					),
					__( 'Valid options: unboxed, boxed', 'astra' )
				);
			}

			astra_update_option( 'site-sidebar-style', $style );
			$updated           = true;
			$update_messages[] = sprintf( 'Style set to %s', ucfirst( $style ) );
		}

		if ( isset( $args['width'] ) ) {
			$width = absint( $args['width'] );

			if ( $width < 15 || $width > 50 ) {
				return Astra_Abilities_Response::error(
					sprintf(
						/* translators: %d: width value */
						__( 'Invalid width: %d.', 'astra' ),
						$width
					),
					__( 'Width must be between 15 and 50 percent.', 'astra' )
				);
			}

			astra_update_option( 'site-sidebar-width', $width );
			$updated           = true;
			$update_messages[] = sprintf( 'Width set to %d%%', $width );
		}

		if ( isset( $args['sticky_enabled'] ) ) {
			$sticky = (bool) $args['sticky_enabled'];
			astra_update_option( 'site-sticky-sidebar', $sticky );
			$updated           = true;
			$update_messages[] = sprintf( 'Sticky sidebar %s', $sticky ? 'enabled' : 'disabled' );
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one setting to update.', 'astra' )
			);
		}

		$message = 'Sidebar settings updated: ' . implode( ', ', $update_messages ) . '.';

		return Astra_Abilities_Response::success(
			$message,
			array( 'updated' => true )
		);
	}
}

Astra_Update_Sidebar::register();
