<?php
/**
 * Update Container Layout Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Container_Layout
 */
class Astra_Update_Container_Layout extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-container-layout';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Container Layout', 'astra' );
		$this->description = __( 'Updates the Astra theme container layout and style settings. You can set container layout (normal, narrow, or full width) and container style (boxed or unboxed). Container style applies only when layout is normal or narrow.', 'astra' );

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
				'container_layout' => array(
					'type'        => 'string',
					'description' => __( 'The container layout to apply. Options: "normal-width-container" (Normal width), "narrow-width-container" (Narrow width), "full-width-container" (Full width / Stretched).', 'astra' ),
					'enum'        => array( 'normal-width-container', 'narrow-width-container', 'full-width-container' ),
				),
				'container_style'  => array(
					'type'        => 'string',
					'description' => __( 'The container style to apply. Options: "boxed" (Boxed style with padding), "unboxed" (Unboxed/Plain style). Note: This only applies when layout is normal or narrow.', 'astra' ),
					'enum'        => array( 'boxed', 'unboxed' ),
				),
			),
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
				'container_layout'       => array(
					'type'        => 'string',
					'description' => 'Updated container layout slug.',
				),
				'container_layout_label' => array(
					'type'        => 'string',
					'description' => 'Human-readable container layout name.',
				),
				'container_style'        => array(
					'type'        => 'string',
					'description' => 'Updated container style slug.',
				),
				'container_style_label'  => array(
					'type'        => 'string',
					'description' => 'Human-readable container style name.',
				),
				'note'                   => array(
					'type'        => 'string',
					'description' => 'Additional context about settings.',
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
			'set container layout to full width',
			'change to full width container',
			'make site full width',
			'change container to normal width',
			'switch to narrow container',
			'set container style to boxed',
			'make container unboxed',
			'set full width with boxed style',
			'make site narrow and boxed',
			'use normal width with unboxed style',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$theme_options = get_option( ASTRA_THEME_SETTINGS, array() );
		if ( ! is_array( $theme_options ) ) {
			$theme_options = array();
		}

		$updated         = false;
		$update_messages = array();

		$layout_labels = array(
			'normal-width-container' => __( 'Normal', 'astra' ),
			'narrow-width-container' => __( 'Narrow', 'astra' ),
			'full-width-container'   => __( 'Full Width', 'astra' ),
		);

		$style_labels = array(
			'boxed'   => __( 'Boxed', 'astra' ),
			'unboxed' => __( 'Unboxed', 'astra' ),
		);

		if ( isset( $args['container_layout'] ) && ! empty( $args['container_layout'] ) ) {
			$container_layout = sanitize_text_field( $args['container_layout'] );

			$valid_layouts = array( 'normal-width-container', 'narrow-width-container', 'full-width-container' );
			if ( ! in_array( $container_layout, $valid_layouts, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: invalid layout value */
					sprintf( __( 'Invalid container layout: %s.', 'astra' ), $container_layout ),
					__( 'Valid options: normal-width-container, narrow-width-container, full-width-container', 'astra' )
				);
			}

			$theme_options['ast-site-content-layout'] = $container_layout;
			$updated                                  = true;

			/* translators: %s: layout label */
			$update_messages[] = sprintf( __( 'Container layout set to %s', 'astra' ), $layout_labels[ $container_layout ] );
		}

		if ( isset( $args['container_style'] ) && ! empty( $args['container_style'] ) ) {
			$container_style = sanitize_text_field( $args['container_style'] );

			$valid_styles = array( 'boxed', 'unboxed' );
			if ( ! in_array( $container_style, $valid_styles, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: invalid style value */
					sprintf( __( 'Invalid container style: %s.', 'astra' ), $container_style ),
					__( 'Valid options: boxed, unboxed', 'astra' )
				);
			}

			$theme_options['site-content-style'] = $container_style;
			$updated                             = true;

			/* translators: %s: style label */
			$update_messages[] = sprintf( __( 'Container style set to %s', 'astra' ), $style_labels[ $container_style ] );
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide either container_layout or container_style to update.', 'astra' )
			);
		}

		update_option( ASTRA_THEME_SETTINGS, $theme_options );

		$current_layout = isset( $theme_options['ast-site-content-layout'] ) ? $theme_options['ast-site-content-layout'] : 'normal-width-container';
		$current_style  = isset( $theme_options['site-content-style'] ) ? $theme_options['site-content-style'] : 'boxed';

		$message = implode( ' and ', $update_messages ) . '.';

		return Astra_Abilities_Response::success(
			$message,
			array(
				'container_layout'       => $current_layout,
				'container_layout_label' => isset( $layout_labels[ $current_layout ] ) ? $layout_labels[ $current_layout ] : $current_layout,
				'container_style'        => $current_style,
				'container_style_label'  => isset( $style_labels[ $current_style ] ) ? $style_labels[ $current_style ] : $current_style,
				'note'                   => __( 'Container style applies only when layout is set to Normal or Narrow.', 'astra' ),
			)
		);
	}
}

Astra_Update_Container_Layout::register();
