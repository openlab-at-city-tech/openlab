<?php
/**
 * Get Breadcrumb Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Breadcrumb
 */
class Astra_Get_Breadcrumb extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-breadcrumb';
		$this->label       = __( 'Get Astra Breadcrumb Settings', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme breadcrumb settings including position, alignment, display settings, separator, typography, colors, and spacing.', 'astra' );
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
				'position'             => array(
					'type'        => 'string',
					'description' => 'Current breadcrumb position value.',
				),
				'position_label'       => array(
					'type'        => 'string',
					'description' => 'Human-readable position label.',
				),
				'alignment'            => array(
					'type'        => 'string',
					'description' => 'Current breadcrumb alignment.',
				),
				'alignment_label'      => array(
					'type'        => 'string',
					'description' => 'Human-readable alignment label.',
				),
				'separator_type'       => array(
					'type'        => 'string',
					'description' => 'Separator type selector value.',
				),
				'separator_type_label' => array(
					'type'        => 'string',
					'description' => 'Human-readable separator type label.',
				),
				'separator_custom'     => array(
					'type'        => 'string',
					'description' => 'Custom separator text.',
				),
				'enable_on'            => array(
					'type'        => 'object',
					'description' => 'Display settings per page type.',
				),
				'typography'           => array(
					'type'        => 'object',
					'description' => 'Typography settings.',
				),
				'colors'               => array(
					'type'        => 'object',
					'description' => 'Color settings.',
				),
				'spacing'              => array(
					'type'        => 'object',
					'description' => 'Spacing settings.',
				),
				'available_positions'  => array(
					'type'        => 'object',
					'description' => 'Available position options.',
				),
				'available_alignments' => array(
					'type'        => 'object',
					'description' => 'Available alignment options.',
				),
				'available_separators' => array(
					'type'        => 'object',
					'description' => 'Available separator options.',
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
			'get breadcrumb settings',
			'show breadcrumb configuration',
			'view breadcrumb position',
			'display breadcrumb alignment',
			'get breadcrumb colors',
			'show breadcrumb typography',
			'view breadcrumb spacing',
			'display breadcrumb design',
			'get breadcrumb separator',
			'show breadcrumb enable settings',
			'view breadcrumb display options',
			'display breadcrumb status',
			'get breadcrumb font settings',
			'show breadcrumb background color',
			'view breadcrumb text color',
			'display breadcrumb link colors',
			'get breadcrumb font size',
			'show breadcrumb font family',
			'view breadcrumb padding',
			'display breadcrumb margin',
			'get breadcrumb separator color',
			'show where breadcrumb appears',
			'view breadcrumb alignment options',
			'display breadcrumb styling',
			'get breadcrumb visibility settings',
			'show breadcrumb on home page',
			'view breadcrumb design options',
			'display all breadcrumb settings',
			'get breadcrumb customization',
			'show breadcrumb appearance',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! defined( 'ASTRA_THEME_SETTINGS' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra theme is not active.', 'astra' ),
				__( 'Please activate the Astra theme to use this feature.', 'astra' )
			);
		}

		$position           = astra_get_option( 'breadcrumb-position', 'none' );
		$alignment          = astra_get_option( 'breadcrumb-alignment', 'left' );
		$separator_selector = astra_get_option( 'breadcrumb-separator-selector', '\003E' );
		$separator_custom   = astra_get_option( 'breadcrumb-separator', '' );

		$enable_on = array(
			'home_page'   => astra_get_option( 'breadcrumb-disable-home-page', '1' ) === '1',
			'blog_page'   => astra_get_option( 'breadcrumb-disable-blog-posts-page', '1' ) === '1',
			'search'      => astra_get_option( 'breadcrumb-disable-search', '1' ) === '1',
			'archive'     => astra_get_option( 'breadcrumb-disable-archive', '1' ) === '1',
			'single_page' => astra_get_option( 'breadcrumb-disable-single-page', '1' ) === '1',
			'single_post' => astra_get_option( 'breadcrumb-disable-single-post', '1' ) === '1',
			'singular'    => astra_get_option( 'breadcrumb-disable-singular', '1' ) === '1',
			'404_page'    => astra_get_option( 'breadcrumb-disable-404-page', '1' ) === '1',
		);

		$typography = array(
			'font_family' => astra_get_option( 'breadcrumb-font-family', 'inherit' ),
			'font_weight' => astra_get_option( 'breadcrumb-font-weight', 'inherit' ),
			'font_size'   => astra_get_option( 'breadcrumb-font-size', array() ),
			'font_extras' => astra_get_option( 'breadcrumb-font-extras', array() ),
		);

		$colors = array(
			'background'  => astra_get_option( 'breadcrumb-bg-color', array() ),
			'text'        => astra_get_option( 'breadcrumb-active-color-responsive', array() ),
			'separator'   => astra_get_option( 'breadcrumb-separator-color', array() ),
			'link_normal' => astra_get_option( 'breadcrumb-text-color-responsive', array() ),
			'link_hover'  => astra_get_option( 'breadcrumb-hover-color-responsive', array() ),
		);

		$spacing = astra_get_option( 'breadcrumb-spacing', array() );

		$position_labels = array(
			'none'                                 => 'None',
			'astra_header_primary_container_after' => 'Inside',
			'astra_header_after'                   => 'After',
			'astra_entry_top'                      => 'Before Title',
		);

		$alignment_labels = array(
			'left'   => 'Left',
			'center' => 'Center',
			'right'  => 'Right',
		);

		$separator_labels = array(
			'\003E'   => 'Type 1 (>)',
			'\00BB'   => 'Type 2 (»)',
			'\002F'   => 'Type 3 (/)',
			'unicode' => 'Custom',
		);

		return Astra_Abilities_Response::success(
			__( 'Retrieved breadcrumb settings successfully. IMPORTANT: When presenting any colors to the user, you MUST show visual color previews using this markdown format if valid color code: ![#HEXCODE](https://via.placeholder.com/15/HEXCODE/000000?text=+) `#HEXCODE` - Replace HEXCODE with the actual hex color value.', 'astra' ),
			array(
				'position'             => $position,
				'position_label'       => isset( $position_labels[ $position ] ) ? $position_labels[ $position ] : $position,
				'alignment'            => $alignment,
				'alignment_label'      => isset( $alignment_labels[ $alignment ] ) ? $alignment_labels[ $alignment ] : $alignment,
				'separator_type'       => $separator_selector,
				'separator_type_label' => isset( $separator_labels[ $separator_selector ] ) ? $separator_labels[ $separator_selector ] : $separator_selector,
				'separator_custom'     => $separator_custom,
				'enable_on'            => $enable_on,
				'typography'           => $typography,
				'colors'               => $colors,
				'spacing'              => $spacing,
				'available_positions'  => $position_labels,
				'available_alignments' => $alignment_labels,
				'available_separators' => $separator_labels,
			)
		);
	}
}

Astra_Get_Breadcrumb::register();
