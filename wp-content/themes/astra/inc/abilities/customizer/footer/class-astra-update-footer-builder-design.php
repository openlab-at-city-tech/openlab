<?php
/**
 * Update Footer Builder Design Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Footer_Builder_Design
 */
class Astra_Update_Footer_Builder_Design extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-footer-builder-design';
		$this->label       = __( 'Update Astra Footer Builder Design Options', 'astra' );
		$this->description = __( 'Updates design options for Astra footer builder sections (above, primary, below) including background, border color, border size, padding and margin.', 'astra' );
		$this->category    = 'astra';

		$this->meta = array(
			'tool_type'   => 'write',
			'constraints' => array(
				'dependencies' => array(
					array(
						'tool'             => 'astra/get-footer-builder-design',
						'description_hint' => 'Get current design settings before updating to preserve existing values',
					),
				),
				'usage_hints'  => array(
					'section'      => 'Required. Target footer section to update: "above", "primary", or "below". Each section has independent design settings. DEFAULT to "primary" if user does not explicitly mention above/below footer - most users refer to the main primary footer section when saying "footer".',
					'background'   => 'Responsive background object with desktop/tablet/mobile keys. Each device can have: background-color (hex/rgba), background-image (URL), background-repeat (repeat/no-repeat/repeat-x/repeat-y), background-position (e.g., "center center"), background-size (auto/cover/contain), background-attachment (scroll/fixed), background-type (color/image/gradient), overlay-type (color/gradient), overlay-color, overlay-gradient. Partial updates merge with existing values.',
					'border_color' => 'Top border color in CSS format (hex like "#e5e5e5", rgba like "rgba(0,0,0,0.1)", or color name). Creates visual separation line at top of footer section. Empty string removes border color.',
					'border_size'  => 'Top border thickness in pixels (0-600). 0 = no border. Common values: 1px (subtle), 2-3px (medium), 5px+ (bold accent). Works with border_color to create separator.',
					'padding'      => 'Responsive internal spacing with desktop/tablet/mobile keys. Each device has top/right/bottom/left (numeric values) and units via desktop-unit/tablet-unit/mobile-unit (px/em/rem/%). Example: {"desktop": {"top": "60", "bottom": "60"}, "desktop-unit": "px"}. Controls space inside footer section.',
					'margin'       => 'Responsive external spacing with desktop/tablet/mobile keys. Each device has top/right/bottom/left (numeric values) and units via desktop-unit/tablet-unit/mobile-unit (px/em/rem/%). Controls space around footer section, separating it from adjacent sections.',
					'responsive'   => 'All design properties support device-specific values. Always provide desktop values at minimum. Tablet/mobile inherit from desktop if not specified. Use responsive settings to create optimal layouts per device.',
					'workflow'     => 'Best practice: 1) Call get-footer-builder-design to see current values, 2) Modify only properties you want to change, 3) Provide section parameter (required), 4) Use CSS color formats for colors, numeric values with units for spacing.',
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
					'description' => 'Footer section to update: above, primary, or below.',
					'enum'        => array( 'above', 'primary', 'below' ),
				),
				'background'   => array(
					'type'        => 'object',
					'description' => 'Background settings (responsive) with desktop, tablet, mobile keys. Each contains background-color, background-image, background-repeat, background-position, background-size, background-attachment, background-media, background-type, overlay-type, overlay-color, overlay-gradient.',
				),
				'border_color' => array(
					'type'        => 'string',
					'description' => 'Top border color (CSS color value).',
				),
				'border_size'  => array(
					'type'        => 'number',
					'description' => 'Top border size in pixels (0-600).',
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
			'update footer background',
			'change footer border color',
			'set footer border size',
			'update footer padding',
			'change footer margin',
			'update above footer design',
			'change primary footer background',
			'set below footer spacing',
			'set footer background to dark',
			'change above footer border to light gray',
			'update primary footer padding to 30px',
			'set below footer margin to 20px',
			'change footer background color',
			'update footer border to 1px',
			'set footer top padding',
			'change footer top border color',
			'update above footer background',
			'set primary footer border size',
			'change below footer padding',
			'update footer section margins',
			'set footer background image',
			'change footer gradient background',
			'update footer spacing values',
			'set above footer border',
			'change primary footer spacing',
			'update below footer design',
			'set footer design options',
			'change footer visual styling',
			'update footer builder design',
			'set all footer spacing',
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
			astra_update_option( $prefix . '-top-border-color', $sanitized_color );
			$updated           = true;
			$update_messages[] = 'Border color updated';
		}

		if ( isset( $args['border_size'] ) ) {
			$size = intval( $args['border_size'] );
			$size = max( 0, min( 600, $size ) );
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
			__( '%1$s footer design updated: %2$s.', 'astra' ),
			ucfirst( $section ),
			implode( ', ', $update_messages )
		);

		return Astra_Abilities_Response::success(
			$message,
			array( 'updated' => true )
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

	/**
	 * Sanitize background settings.
	 *
	 * @param array $background Background data to sanitize.
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
	 * @param array $spacing Spacing data to sanitize.
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

Astra_Update_Footer_Builder_Design::register();
