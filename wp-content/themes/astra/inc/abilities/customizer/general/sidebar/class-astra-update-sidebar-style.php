<?php
/**
 * Update Sidebar Style Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Sidebar_Style
 */
class Astra_Update_Sidebar_Style extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-sidebar-style';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Sidebar Style', 'astra' );
		$this->description = __( 'Updates the sidebar style setting for the Astra theme (boxed or unboxed).', 'astra' );

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
				'style' => array(
					'type'        => 'string',
					'description' => 'Sidebar style. Options: "unboxed" (Unboxed), "boxed" (Boxed).',
					'enum'        => array( 'unboxed', 'boxed' ),
				),
			),
			'required'   => array( 'style' ),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'set sidebar style to boxed',
			'change sidebar to unboxed',
			'make sidebar boxed',
			'set unboxed sidebar',
			'change sidebar style to boxed',
			'update sidebar to unboxed',
			'set boxed sidebar style',
			'change to unboxed sidebar',
			'make sidebar unboxed',
			'set sidebar boxed',
			'update sidebar style to boxed',
			'change sidebar design to unboxed',
			'set sidebar to boxed style',
			'update to boxed sidebar',
			'change sidebar to boxed design',
			'set unboxed sidebar design',
			'make sidebar style boxed',
			'update sidebar appearance to unboxed',
			'change to boxed sidebar style',
			'set sidebar container to boxed',
			'update sidebar wrapper to unboxed',
			'change sidebar box style',
			'set sidebar design boxed',
			'make sidebar container boxed',
			'update sidebar to boxed container',
			'change sidebar style unboxed',
			'set default sidebar style',
			'update site sidebar style',
			'change sidebar visual style',
			'configure sidebar to boxed',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! isset( $args['style'] ) ) {
			return Astra_Abilities_Response::error(
				__( 'Style is required.', 'astra' ),
				__( 'Please provide a style: unboxed or boxed.', 'astra' )
			);
		}

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

		$style_labels = array(
			'unboxed' => 'Unboxed',
			'boxed'   => 'Boxed',
		);

		astra_update_option( 'site-sidebar-style', $style );

		$message = sprintf(
			/* translators: %s: style label */
			__( 'Sidebar style set to %s.', 'astra' ),
			$style_labels[ $style ]
		);

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
				'style'   => $style,
			)
		);
	}
}

Astra_Update_Sidebar_Style::register();
