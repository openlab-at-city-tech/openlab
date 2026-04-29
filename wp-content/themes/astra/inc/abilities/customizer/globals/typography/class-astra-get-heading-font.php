<?php
/**
 * Get Individual Heading Font Ability
 *
 * Registers get abilities for each heading level (H1-H6).
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Heading_Font
 *
 * Parameterized ability that registers a get-font ability
 * for a specific heading level (h1 through h6).
 */
class Astra_Get_Heading_Font extends Astra_Abstract_Ability {
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
		$this->id          = 'astra/get-font-h' . $this->level;
		$this->label       = /* translators: %d: heading level */ sprintf( __( 'Get Astra H%d Font', 'astra' ), $this->level );
		$this->description = /* translators: %d: heading level */ sprintf( __( 'Retrieves the current Astra theme H%d heading font settings including font family, weight, size, line height, and other typography properties.', 'astra' ), $this->level );
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
		$tag = 'H' . $this->level;

		return $this->build_output_schema(
			array(
				'font_family' => array(
					'type'        => 'string',
					'description' => 'Current ' . $tag . ' font family.',
				),
				'font_weight' => array(
					'type'        => 'string',
					'description' => 'Current ' . $tag . ' font weight.',
				),
				'font_size'   => array(
					'type'        => 'object',
					'description' => 'Responsive font size with desktop, tablet, mobile values and units.',
				),
				'font_extras' => array(
					'type'        => 'object',
					'description' => 'Additional typography settings (line height, text transform, letter spacing).',
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
			'get current ' . $tag . ' font',
			'show ' . $tag . ' font settings',
			'view ' . $tag . ' typography',
			'display ' . $tag . ' font family',
			'get ' . $tag . ' font configuration',
			'show ' . $tag . ' font weight',
			'display ' . $tag . ' font size',
			'get ' . $tag . ' heading settings',
			'view ' . $tag . ' font details',
			'show ' . $tag . ' typography config',
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

		return Astra_Abilities_Response::success(
			/* translators: %s: heading tag */
			sprintf( __( 'Retrieved %s font settings successfully.', 'astra' ), strtoupper( $tag ) ),
			array(
				'font_family' => astra_get_option( 'font-family-' . $tag, '' ),
				'font_weight' => astra_get_option( 'font-weight-' . $tag, '' ),
				'font_size'   => astra_get_option( 'font-size-' . $tag, array() ),
				'font_extras' => astra_get_option( $tag . '-font-extras', array() ),
			)
		);
	}

	/**
	 * Register get-font abilities for all heading levels (H1-H6).
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

Astra_Get_Heading_Font::register_all();
