<?php
/**
 * Update Header Builder Design Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Header_Builder_Design
 */
class Astra_Update_Header_Builder_Design extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-header-builder-design';
		$this->label       = __( 'Update Astra Header Builder Design Options', 'astra' );
		$this->description = __( 'Updates design options for Astra header builder sections (above, primary, below) including background, border color, border size, padding and margin.', 'astra' );
		$this->category    = 'astra';

		$this->meta = array(
			'tool_type'   => 'write',
			'constraints' => array(
				'dependencies' => array(
					array(
						'tool'             => 'astra/get-header-builder-design',
						'description_hint' => 'Get current design settings before updating to preserve existing values',
					),
				),
				'usage_hints'  => array(
					'section'      => 'Required. Target header section to update: "above", "primary", or "below". Each section has independent design settings. Primary header is main navigation bar. DEFAULT to "primary" if user does not explicitly mention above/below header - most users refer to the main primary header when saying "header".',
					'background'   => 'Responsive background object with desktop/tablet/mobile keys. Each device can have: background-color (hex/rgba for header color), background-image (URL for hero header), background-repeat (repeat/no-repeat/repeat-x/repeat-y), background-position (e.g., "center center"), background-size (auto/cover/contain), background-attachment (scroll for normal, fixed for parallax), background-type (color/image/gradient), overlay-type (color/gradient), overlay-color, overlay-gradient. Primary header typically uses solid background-color. Partial updates merge with existing.',
					'border_color' => 'Bottom border color in CSS format (hex like "#dddddd", rgba like "rgba(0,0,0,0.08)", or color name). Creates separation line below header section. Common for primary header to separate from page content. Empty string removes border.',
					'border_size'  => 'Bottom border thickness in pixels (0-10). 0 = no border. Common values: 1px (standard subtle separation), 2px (more prominent). Max 10px for headers (vs 600px for footer) as headers need subtler styling. Works with border_color.',
					'padding'      => 'Responsive internal spacing with desktop/tablet/mobile keys. Each device has top/right/bottom/left (numeric values) and units via desktop-unit/tablet-unit/mobile-unit (px/em/rem/%). Example: {"desktop": {"top": "15", "bottom": "15"}, "desktop-unit": "px"} for compact header. Primary header padding directly affects perceived header height and logo/menu spacing. Common desktop: 10-30px for compact, 20-60px for spacious. Mobile often smaller for screen space.',
					'margin'       => 'Responsive external spacing with desktop/tablet/mobile keys. Each device has top/right/bottom/left (numeric values) and units. Rarely used for headers (more common: padding). Typically 0 for sticky/fixed headers. May add top margin to push header down from viewport edge.',
					'responsive'   => 'All design properties support device-specific values (desktop/tablet/mobile). Desktop values required at minimum. Tablet/mobile inherit from desktop if not specified. Headers often need different padding on mobile (smaller for space). Use responsive backgrounds to hide header images on mobile.',
					'workflow'     => 'Best practice: 1) Call get-header-builder-design to see current values, 2) Modify only properties you want to change, 3) Provide section parameter (required), 4) Use CSS color formats (hex/rgba), numeric values with units for spacing, 5) Test responsive values especially on mobile where header space is limited.',
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
				'section'      => array(
					'type'        => 'string',
					'description' => 'Header section to update: above, primary, or below.',
					'enum'        => array( 'above', 'primary', 'below' ),
				),
				'background'   => array(
					'type'        => 'object',
					'description' => 'Background settings (responsive) with desktop, tablet, mobile keys. Each contains background-color, background-image, background-repeat, background-position, background-size, background-attachment, background-media, background-type, overlay-type, overlay-color, overlay-gradient.',
				),
				'border_color' => array(
					'type'        => 'string',
					'description' => 'Bottom border color (CSS color value).',
				),
				'border_size'  => array(
					'type'        => 'number',
					'description' => 'Bottom border size in pixels (0-10).',
				),
				'padding'      => array(
					'type'        => 'object',
					'description' => 'Padding settings (responsive) with desktop, tablet, mobile keys. Each contains top, right, bottom, left values. Also includes desktop-unit, tablet-unit, mobile-unit.',
				),
				'margin'       => array(
					'type'        => 'object',
					'description' => 'Margin settings (responsive) with desktop, tablet, mobile keys. Each contains top, right, bottom, left values. Also includes desktop-unit, tablet-unit, mobile-unit.',
				),
			),
			'required'   => array( 'section' ),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'update header background',
			'change header border color',
			'set header border size',
			'update header padding',
			'change header margin',
			'update above header design',
			'change primary header background',
			'set below header spacing',
			'set header background to white',
			'change above header border to gray',
			'update primary header padding to 20px',
			'set below header margin to 10px',
			'change header background color',
			'update header border to 2px',
			'set header top padding',
			'change header bottom border color',
			'update above header background',
			'set primary header border size',
			'change below header padding',
			'update header section margins',
			'set header background image',
			'change header gradient background',
			'update header spacing values',
			'set above header border',
			'change primary header spacing',
			'update below header design',
			'set header design options',
			'change header visual styling',
			'update header builder design',
			'set all header spacing',
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

		if ( ! isset( $args['section'] ) ) {
			return Astra_Abilities_Response::error(
				__( 'Section is required.', 'astra' ),
				__( 'Please provide a section: above, primary, or below.', 'astra' )
			);
		}

		$section        = $args['section'];
		$valid_sections = array( 'above', 'primary', 'below' );

		if ( ! in_array( $section, $valid_sections, true ) ) {
			return Astra_Abilities_Response::error(
				__( 'Invalid section provided.', 'astra' ),
				__( 'Section must be one of: above, primary, below.', 'astra' )
			);
		}

		$prefix     = $this->get_section_prefix( $section );
		$section_id = $this->get_section_id( $section );

		$updated         = false;
		$update_messages = array();

		if ( isset( $args['background'] ) && is_array( $args['background'] ) ) {
			$sanitized_bg = $this->sanitize_background( $args['background'] );
			astra_update_option( $prefix . '-bg-obj-responsive', $sanitized_bg );
			$updated           = true;
			$update_messages[] = 'Background updated';
		}

		if ( isset( $args['border_color'] ) ) {
			$sanitized_color = sanitize_text_field( $args['border_color'] );
			astra_update_option( $prefix . '-bottom-border-color', $sanitized_color );
			$updated           = true;
			$update_messages[] = 'Border color updated';
		}

		if ( isset( $args['border_size'] ) ) {
			$size = intval( $args['border_size'] );
			$size = max( 0, min( 10, $size ) );
			astra_update_option( $prefix . '-separator', $size );
			$updated           = true;
			$update_messages[] = 'Border size updated';
		}

		if ( isset( $args['padding'] ) && is_array( $args['padding'] ) ) {
			$sanitized_padding = $this->sanitize_spacing( $args['padding'] );
			astra_update_option( $section_id . '-padding', $sanitized_padding );
			$updated           = true;
			$update_messages[] = 'Padding updated';
		}

		if ( isset( $args['margin'] ) && is_array( $args['margin'] ) ) {
			$sanitized_margin = $this->sanitize_spacing( $args['margin'] );
			astra_update_option( $section_id . '-margin', $sanitized_margin );
			$updated           = true;
			$update_messages[] = 'Margin updated';
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one design option to update.', 'astra' )
			);
		}

		/* translators: 1: section name, 2: list of updates */
		$message = sprintf(
			__( '%1$s header design updated: %2$s.', 'astra' ),
			ucfirst( $section ),
			implode( ', ', $update_messages )
		);

		return Astra_Abilities_Response::success(
			$message,
			array( 'updated' => true )
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

	/**
	 * Sanitize background settings.
	 *
	 * @param array $background Background data.
	 * @return array Sanitized background data.
	 */
	private function sanitize_background( $background ) {
		$sanitized = array();
		$devices   = array( 'desktop', 'tablet', 'mobile' );

		foreach ( $devices as $device ) {
			if ( isset( $background[ $device ] ) && is_array( $background[ $device ] ) ) {
				$sanitized[ $device ] = array();
				$allowed_keys         = array(
					'background-color',
					'background-image',
					'background-repeat',
					'background-position',
					'background-size',
					'background-attachment',
					'background-media',
					'background-type',
					'overlay-type',
					'overlay-color',
					'overlay-gradient',
				);

				foreach ( $allowed_keys as $key ) {
					if ( isset( $background[ $device ][ $key ] ) ) {
						$sanitized[ $device ][ $key ] = sanitize_text_field( $background[ $device ][ $key ] );
					}
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize spacing settings.
	 *
	 * @param array $spacing Spacing data.
	 * @return array Sanitized spacing data.
	 */
	private function sanitize_spacing( $spacing ) {
		$sanitized = array();
		$devices   = array( 'desktop', 'tablet', 'mobile' );
		$sides     = array( 'top', 'right', 'bottom', 'left' );

		foreach ( $devices as $device ) {
			if ( isset( $spacing[ $device ] ) && is_array( $spacing[ $device ] ) ) {
				$sanitized[ $device ] = array();
				foreach ( $sides as $side ) {
					if ( isset( $spacing[ $device ][ $side ] ) ) {
						$sanitized[ $device ][ $side ] = sanitize_text_field( $spacing[ $device ][ $side ] );
					}
				}
			}

			$unit_key = $device . '-unit';
			if ( isset( $spacing[ $unit_key ] ) ) {
				$sanitized[ $unit_key ] = sanitize_text_field( $spacing[ $unit_key ] );
			}
		}

		return $sanitized;
	}
}

Astra_Update_Header_Builder_Design::register();
