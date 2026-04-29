<?php
/**
 * Update Individual Heading Font Ability
 *
 * Registers update abilities for each heading level (H1-H6).
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Heading_Font
 *
 * Parameterized ability that registers an update-font ability
 * for a specific heading level (h1 through h6).
 */
class Astra_Update_Heading_Font extends Astra_Abstract_Ability {
	/**
	 * Heading level (1-6).
	 *
	 * @var int
	 */
	protected $level = 1;

	/**
	 * Set the heading level before configure runs.
	 *
	 * @param int $level Heading level 1-6.
	 * @return void
	 */
	public function set_level( $level ) {
		$level = absint( $level );

		if ( $level < 1 || $level > 6 ) {
			$level = 1;
		}

		$this->level = $level;
	}

	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-font-h' . $this->level;
		$this->category    = 'astra';
		$this->label       = /* translators: %d: heading level */ sprintf( __( 'Update Astra H%d Font', 'astra' ), $this->level );
		$this->description = /* translators: %d: heading level */ sprintf( __( 'Updates the Astra theme H%d heading font family, weight, size, and other typography settings. IMPORTANT: When user specifies size like "32px" or "2rem", you must separate the numeric value from the unit: font_size.desktop = 32 (number), font_size.desktop-unit = "px" (string).', 'astra' ), $this->level );

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
				'font_family'    => array(
					'type'        => 'string',
					'description' => 'Font family name (e.g., "Playfair Display", "Montserrat", "Arial")',
				),
				'font_weight'    => array(
					'type'        => 'string',
					'description' => 'Font weight (e.g., "400", "500", "700")',
				),
				'font_size'      => array(
					'type'        => 'object',
					'description' => 'Font size with responsive values. Separate numeric value from unit. Example: {"desktop": "32", "tablet": "28", "mobile": "24", "desktop-unit": "px", "tablet-unit": "px", "mobile-unit": "px"}',
					'properties'  => array(
						'desktop'      => array(
							'type'        => 'string',
							'description' => 'Desktop size value only, without unit (e.g., "32", "2")',
						),
						'tablet'       => array(
							'type'        => 'string',
							'description' => 'Tablet size value only, without unit',
						),
						'mobile'       => array(
							'type'        => 'string',
							'description' => 'Mobile size value only, without unit',
						),
						'desktop-unit' => array(
							'type'        => 'string',
							'description' => 'Desktop unit (px, em, rem, vw)',
							'enum'        => array( 'px', 'em', 'rem', 'vw' ),
						),
						'tablet-unit'  => array(
							'type'        => 'string',
							'description' => 'Tablet unit (px, em, rem, vw)',
							'enum'        => array( 'px', 'em', 'rem', 'vw' ),
						),
						'mobile-unit'  => array(
							'type'        => 'string',
							'description' => 'Mobile unit (px, em, rem, vw)',
							'enum'        => array( 'px', 'em', 'rem', 'vw' ),
						),
					),
				),
				'line_height'    => array(
					'type'        => 'string',
					'description' => 'Line height value',
				),
				'text_transform' => array(
					'type'        => 'string',
					'description' => 'Text transform (uppercase, lowercase, capitalize, none)',
					'enum'        => array( 'uppercase', 'lowercase', 'capitalize', 'none', '' ),
				),
				'letter_spacing' => array(
					'type'        => 'string',
					'description' => 'Letter spacing value',
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
		$tag = 'H' . $this->level;

		return $this->build_output_schema(
			array(
				'font_family' => array(
					'type'        => 'string',
					'description' => 'Updated ' . $tag . ' font family.',
				),
				'font_weight' => array(
					'type'        => 'string',
					'description' => 'Updated ' . $tag . ' font weight.',
				),
				'font_size'   => array(
					'type'        => 'object',
					'description' => 'Updated responsive font size with desktop, tablet, mobile values and units.',
				),
				'font_extras' => array(
					'type'        => 'object',
					'description' => 'Updated additional typography settings (line height, text transform, letter spacing).',
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
		$tag = 'h' . $this->level;

		return array(
			'update ' . $tag . ' font to Playfair Display',
			'set ' . $tag . ' font size to 32px',
			'change ' . $tag . ' font family to Montserrat',
			'update ' . $tag . ' font weight to 700',
			'set ' . $tag . ' line height to 1.2',
			'make ' . $tag . ' uppercase',
			'change ' . $tag . ' letter spacing to 2px',
			'set ' . $tag . ' font to Inter',
			'update ' . $tag . ' typography settings',
			'make ' . $tag . ' font bold',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$tag = 'h' . $this->level;

		if ( isset( $args['font_family'] ) ) {
			$font_family    = sanitize_text_field( $args['font_family'] );
			$formatted_font = "'" . $font_family . "', sans-serif";
			astra_update_option( 'font-family-' . $tag, $formatted_font );
		}

		if ( isset( $args['font_weight'] ) ) {
			astra_update_option( 'font-weight-' . $tag, sanitize_text_field( $args['font_weight'] ) );
		}

		if ( isset( $args['font_size'] ) && is_array( $args['font_size'] ) ) {
			$existing = astra_get_option( 'font-size-' . $tag, array() );
			$merged   = array_merge( $existing, $args['font_size'] );
			astra_update_option( 'font-size-' . $tag, Astra_Abilities_Helper::sanitize_responsive_typo( $merged ) );
		}

		Astra_Abilities_Helper::update_font_extras( $args, $tag . '-font-extras' );

		return Astra_Abilities_Response::success(
			/* translators: %s: heading tag */
			sprintf( __( '%s font settings updated successfully.', 'astra' ), strtoupper( $tag ) ),
			array(
				'font_family' => astra_get_option( 'font-family-' . $tag, '' ),
				'font_weight' => astra_get_option( 'font-weight-' . $tag, '' ),
				'font_size'   => astra_get_option( 'font-size-' . $tag, array() ),
				'font_extras' => astra_get_option( $tag . '-font-extras', array() ),
			)
		);
	}

	/**
	 * Register update-font abilities for all heading levels (H1-H6).
	 *
	 * @return void
	 */
	public static function register_all() {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		for ( $level = 1; $level <= 6; $level++ ) {
			$instance = new self();
			$instance->set_level( $level );
			$instance->configure();

			if ( empty( $instance->id ) ) {
				continue;
			}

			$meta = array(
				'tool_type'    => $instance->get_tool_type(),
				'examples'     => $instance->get_examples(),
				'version'      => $instance->get_version(),
				'show_in_rest' => $instance->get_show_in_rest(),
				'annotations'  => $instance->get_annotations(),
				'mcp'          => $instance->get_mcp(),
			);

			$meta = array_replace_recursive( $meta, $instance->meta );

			$args = array(
				'label'               => $instance->get_label(),
				'description'         => $instance->get_description(),
				'category'            => $instance->get_category(),
				'input_schema'        => $instance->get_final_input_schema(),
				'execute_callback'    => array( $instance, 'handle_execute' ),
				'permission_callback' => array( $instance, 'check_permission' ),
				'meta'                => $meta,
			);

			$output_schema = $instance->get_output_schema();
			if ( ! empty( $output_schema ) ) {
				$args['output_schema'] = $output_schema;
			}

			wp_register_ability( $instance->id, $args );
		}
	}
}

Astra_Update_Heading_Font::register_all();
