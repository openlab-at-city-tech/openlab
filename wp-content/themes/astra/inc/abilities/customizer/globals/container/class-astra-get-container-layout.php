<?php
/**
 * Get Container Layout Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Container_Layout
 */
class Astra_Get_Container_Layout extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-container-layout';
		$this->label       = __( 'Get Astra Container Layout', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme container layout settings including container layout, container style, container width, and narrow container width.', 'astra' );
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
				'container_layout'       => array(
					'type'        => 'string',
					'description' => 'Current container layout slug.',
				),
				'container_layout_label' => array(
					'type'        => 'string',
					'description' => 'Human-readable container layout name.',
				),
				'container_style'        => array(
					'type'        => 'string',
					'description' => 'Current container style slug.',
				),
				'container_style_label'  => array(
					'type'        => 'string',
					'description' => 'Human-readable container style name.',
				),
				'available_layouts'      => array(
					'type'        => 'object',
					'description' => 'Map of available layout slugs to labels.',
				),
				'available_styles'       => array(
					'type'        => 'object',
					'description' => 'Map of available style slugs to labels.',
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
			'get current container layout',
			'show container layout settings',
			'view container style',
			'display current container width',
			'get site container settings',
			'show container layout configuration',
			'view container width values',
			'display container style settings',
			'get narrow container width',
			'show current site layout',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$container_layout = astra_get_option( 'ast-site-content-layout', 'normal-width-container' );
		$container_style  = astra_get_option( 'site-content-style', 'boxed' );

		$layout_labels = array(
			'normal-width-container' => __( 'Normal', 'astra' ),
			'narrow-width-container' => __( 'Narrow', 'astra' ),
			'full-width-container'   => __( 'Full Width', 'astra' ),
		);

		$style_labels = array(
			'boxed'   => __( 'Boxed', 'astra' ),
			'unboxed' => __( 'Unboxed', 'astra' ),
		);

		return Astra_Abilities_Response::success(
			__( 'Retrieved container layout settings successfully.', 'astra' ),
			array(
				'container_layout'       => $container_layout,
				'container_layout_label' => isset( $layout_labels[ $container_layout ] ) ? $layout_labels[ $container_layout ] : $container_layout,
				'container_style'        => $container_style,
				'container_style_label'  => isset( $style_labels[ $container_style ] ) ? $style_labels[ $container_style ] : $container_style,
				'available_layouts'      => array(
					'normal-width-container' => __( 'Normal', 'astra' ),
					'narrow-width-container' => __( 'Narrow', 'astra' ),
					'full-width-container'   => __( 'Full Width', 'astra' ),
				),
				'available_styles'       => array(
					'boxed'   => __( 'Boxed', 'astra' ),
					'unboxed' => __( 'Unboxed', 'astra' ),
				),
				'note'                   => __( 'Container style applies only when layout is set to Normal or Narrow.', 'astra' ),
			)
		);
	}
}

Astra_Get_Container_Layout::register();
