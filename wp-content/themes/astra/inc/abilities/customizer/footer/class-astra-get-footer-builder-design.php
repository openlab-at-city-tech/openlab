<?php
/**
 * Get Footer Builder Design Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Footer_Builder_Design
 */
class Astra_Get_Footer_Builder_Design extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-footer-builder-design';
		$this->label       = __( 'Get Astra Footer Builder Design Options', 'astra' );
		$this->description = __( 'Retrieves design options for Astra footer builder sections (above, primary, below) including background, border color, border size, padding and margin.', 'astra' );
		$this->category    = 'astra';

		$this->meta = array(
			'tool_type'   => 'read',
			'constraints' => array(
				'usage_hints' => array(
					'section'               => 'Optional. Specify "above", "primary", or "below" to get design for specific section. If omitted, returns all sections. When user says "footer design" without specifying section, omit this parameter to show all sections. When user says "primary footer design" or clearly wants one section, use that specific section.',
					'background'            => 'Responsive background object with desktop/tablet/mobile keys. Each device contains: background-color (CSS color), background-image (URL), background-repeat (repeat/no-repeat/repeat-x/repeat-y), background-position (CSS position), background-size (auto/cover/contain), background-attachment (scroll/fixed), background-media (media ID), background-type (color/image/gradient), overlay-type (color/gradient), overlay-color (CSS color), overlay-gradient (CSS gradient).',
					'border_color'          => 'Top border color in CSS color format (hex, rgba, color name, or empty string if not set). Adds visual separation between footer section and content above it.',
					'border_size'           => 'Top border thickness in pixels (0-600). Value of 0 means no border. Commonly used values: 1-5px for subtle separation.',
					'padding'               => 'Responsive spacing object with desktop/tablet/mobile keys, each containing top/right/bottom/left values (numeric with unit), plus desktop-unit/tablet-unit/mobile-unit (px/em/rem/%). Controls internal spacing of the footer section.',
					'margin'                => 'Responsive spacing object with desktop/tablet/mobile keys, each containing top/right/bottom/left values (numeric with unit), plus desktop-unit/tablet-unit/mobile-unit (px/em/rem/%). Controls external spacing around the footer section.',
					'response_presentation' => 'When presenting results to user, provide a clear summary of the design settings for the requested section(s). Include: 1) Background settings (color/image/gradient if set), 2) Border configuration (color and size if present), 3) Padding values (desktop at minimum), 4) Margin values if set. Mention which sections were retrieved. Example: "Primary footer has white background (#ffffff), 1px gray border on top, 60px top/bottom padding on desktop. Above footer has no border, gradient background."',
				),
			),
		);
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
					'description' => 'Footer section to get design options for: above, primary, or below. If not provided, returns all sections.',
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
			'get footer design options',
			'show footer background settings',
			'view footer border settings',
			'display footer spacing',
			'get above footer design',
			'show primary footer design',
			'view below footer design',
			'get footer builder design',
			'show footer background color',
			'view footer padding settings',
			'display footer margin values',
			'get primary footer background',
			'show above footer border',
			'view below footer spacing',
			'display footer design configuration',
			'get footer section background',
			'show footer border color',
			'view footer border size',
			'display footer builder styling',
			'get above footer background color',
			'show primary footer border',
			'view below footer padding',
			'display footer section spacing',
			'get footer background image',
			'show footer gradient background',
			'view all footer design options',
			'display footer styling settings',
			'get footer section margins',
			'show footer design values',
			'view footer visual settings',
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
				'border_color' => astra_get_option( $prefix . '-top-border-color', '' ),
				'border_size'  => astra_get_option( $prefix . '-separator', 0 ),
				'padding'      => astra_get_option( $section_id . '-padding', array() ),
				'margin'       => astra_get_option( $section_id . '-margin', array() ),
			);
		}

		return Astra_Abilities_Response::success(
			__( 'Retrieved footer builder design options successfully. IMPORTANT: When presenting any colors to the user, you MUST show visual color previews using this markdown format if valid color code: ![#HEXCODE](https://via.placeholder.com/15/HEXCODE/000000?text=+) `#HEXCODE` - Replace HEXCODE with the actual hex color value.', 'astra' ),
			$design_options
		);
	}

	/**
	 * Get the option prefix for a footer section.
	 *
	 * @param string $section Footer section name.
	 * @return string Option prefix.
	 */
	private function get_section_prefix( $section ) {
		$prefixes = array(
			'above'   => 'hba-footer',
			'primary' => 'hb-footer',
			'below'   => 'hbb-footer',
		);
		return $prefixes[ $section ];
	}

	/**
	 * Get the section ID for a footer section.
	 *
	 * @param string $section Footer section name.
	 * @return string Section ID.
	 */
	private function get_section_id( $section ) {
		$section_ids = array(
			'above'   => 'section-above-footer-builder',
			'primary' => 'section-primary-footer-builder',
			'below'   => 'section-below-footer-builder',
		);
		return $section_ids[ $section ];
	}
}

Astra_Get_Footer_Builder_Design::register();
