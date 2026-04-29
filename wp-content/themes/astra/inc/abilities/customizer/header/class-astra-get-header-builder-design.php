<?php
/**
 * Get Header Builder Design Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Header_Builder_Design
 */
class Astra_Get_Header_Builder_Design extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-header-builder-design';
		$this->label       = __( 'Get Astra Header Builder Design Options', 'astra' );
		$this->description = __( 'Retrieves design options for Astra header builder sections (above, primary, below) including background, border color, border size, padding and margin.', 'astra' );
		$this->category    = 'astra';

		$this->meta = array(
			'tool_type'   => 'read',
			'constraints' => array(
				'usage_hints' => array(
					'section'               => 'Optional. Specify "above", "primary", or "below" to get design for specific section. If omitted, returns all sections. When user says "header design" without specifying section, omit this parameter to show all sections. When user says "primary header design" or clearly wants one section, use that specific section.',
					'background'            => 'Responsive background object with desktop/tablet/mobile keys. Each device contains: background-color (CSS color), background-image (URL), background-repeat (repeat/no-repeat/repeat-x/repeat-y), background-position (CSS position), background-size (auto/cover/contain), background-attachment (scroll/fixed), background-media (media ID), background-type (color/image/gradient), overlay-type (color/gradient), overlay-color (CSS color), overlay-gradient (CSS gradient). Primary header typically has solid background color, above/below may use gradients or images.',
					'border_color'          => 'Bottom border color in CSS color format (hex, rgba, color name, or empty string if not set). Adds visual separation line below header section. Common for primary header to separate from page content.',
					'border_size'           => 'Bottom border thickness in pixels (0-10). Value of 0 means no border. Commonly used values: 1px (subtle), 2px (standard). Smaller range than footer (0-10 vs 0-600) as headers need subtler borders.',
					'padding'               => 'Responsive spacing object with desktop/tablet/mobile keys, each containing top/right/bottom/left values (numeric with unit), plus desktop-unit/tablet-unit/mobile-unit (px/em/rem/%). Controls internal spacing of header section. Primary header padding affects logo/menu spacing. Common values: 10-30px vertical padding for compact headers, 20-60px for spacious headers.',
					'margin'                => 'Responsive spacing object with desktop/tablet/mobile keys, each containing top/right/bottom/left values (numeric with unit), plus desktop-unit/tablet-unit/mobile-unit (px/em/rem/%). Controls external spacing around header section. Less commonly used than padding for headers. Usually 0 for sticky headers.',
					'response_presentation' => 'When presenting results to user, provide a clear summary of the design settings for the requested section(s). Include: 1) Background settings (color/image/gradient if set), 2) Border configuration (color and size if present), 3) Padding values (especially for primary header which affects header height), 4) Notable responsive differences (tablet/mobile if different from desktop). Example: "Primary header: White background, 1px light gray bottom border, 15px top/bottom padding (compact header style). Mobile padding reduced to 10px."',
				),
			),
		);
	}

	/**
	 * Get tool type.
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'read';
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
				'section' => array(
					'type'        => 'string',
					'description' => 'Header section to get design options for: above, primary, or below. If not provided, returns all sections.',
					'enum'        => array( 'above', 'primary', 'below' ),
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
			'get header design options',
			'show header background settings',
			'view header border settings',
			'display header spacing',
			'get above header design',
			'show primary header design',
			'view below header design',
			'get header builder design',
			'show header background color',
			'view header padding settings',
			'display header margin values',
			'get primary header background',
			'show above header border',
			'view below header spacing',
			'display header design configuration',
			'get header section background',
			'show header border color',
			'view header border size',
			'display header builder styling',
			'get above header background color',
			'show primary header border',
			'view below header padding',
			'display header section spacing',
			'get header background image',
			'show header gradient background',
			'view all header design options',
			'display header styling settings',
			'get header section margins',
			'show header design values',
			'view header visual settings',
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

		$section  = isset( $args['section'] ) ? $args['section'] : null;
		$sections = array( 'above', 'primary', 'below' );

		if ( $section && ! in_array( $section, $sections, true ) ) {
			return Astra_Abilities_Response::error(
				__( 'Invalid section provided.', 'astra' ),
				__( 'Section must be one of: above, primary, below.', 'astra' )
			);
		}

		$sections_to_fetch = $section ? array( $section ) : $sections;
		$design_options    = array();

		foreach ( $sections_to_fetch as $sect ) {
			$prefix     = $this->get_section_prefix( $sect );
			$section_id = $this->get_section_id( $sect );

			$design_options[ $sect ] = array(
				'background'   => astra_get_option( $prefix . '-bg-obj-responsive', array() ),
				'border_color' => astra_get_option( $prefix . '-bottom-border-color', '' ),
				'border_size'  => astra_get_option( $prefix . '-separator', 0 ),
				'padding'      => astra_get_option( $section_id . '-padding', array() ),
				'margin'       => astra_get_option( $section_id . '-margin', array() ),
			);
		}

		return Astra_Abilities_Response::success(
			__( 'Retrieved header builder design options successfully. IMPORTANT: When presenting any colors to the user, you MUST show visual color previews using this markdown format if valid color code: ![#HEXCODE](https://via.placeholder.com/15/HEXCODE/000000?text=+) `#HEXCODE` - Replace HEXCODE with the actual hex color value.', 'astra' ),
			$design_options
		);
	}

	/**
	 * Get the option prefix for a header section.
	 *
	 * @param string $section Section name (above, primary, below).
	 * @return string Option prefix.
	 */
	private function get_section_prefix( $section ) {
		$prefixes = array(
			'above'   => 'hba-header',
			'primary' => 'hb-header',
			'below'   => 'hbb-header',
		);
		return $prefixes[ $section ];
	}

	/**
	 * Get the section ID for a header section.
	 *
	 * @param string $section Section name (above, primary, below).
	 * @return string Section ID.
	 */
	private function get_section_id( $section ) {
		$section_ids = array(
			'above'   => 'section-above-header-builder',
			'primary' => 'section-primary-header-builder',
			'below'   => 'section-below-header-builder',
		);
		return $section_ids[ $section ];
	}
}

Astra_Get_Header_Builder_Design::register();
