<?php
/**
 * Abstract Ability Class
 *
 * Base class for all Astra abilities.
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Class Astra_Abstract_Ability
 */
abstract class Astra_Abstract_Ability {
	/**
	 * Ability ID (e.g. 'astra/get-font-body').
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Ability Category.
	 *
	 * @var string
	 */
	protected $category = 'astra';

	/**
	 * Ability Label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Ability Description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Required capability for this ability.
	 *
	 * @var string
	 */
	protected $capability = 'edit_theme_options';

	/**
	 * Ability Meta Data.
	 *
	 * @var array
	 */
	protected $meta = array();

	/**
	 * Tool version.
	 *
	 * @var string
	 */
	protected $version = '1.0.0';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->configure();
	}

	/**
	 * Configure the ability (set ID, label, description, etc.).
	 *
	 * @return void
	 */
	abstract public function configure();

	/**
	 * Get the input schema for the ability.
	 *
	 * Override in child classes that accept input parameters.
	 * Returns empty array by default (no input required).
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array();
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	abstract public function execute( $args );

	/**
	 * Get the final input schema.
	 *
	 * @return array
	 */
	public function get_final_input_schema() {
		$schema = $this->get_input_schema();

		if ( ! isset( $schema['type'] ) ) {
			$schema['type'] = 'object';
		}

		if ( ! isset( $schema['properties'] ) || ( is_array( $schema['properties'] ) && empty( $schema['properties'] ) ) ) {
			$schema['properties'] = new \stdClass();
		}

		return $schema;
	}

	/**
	 * Handle execution with error handling.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function handle_execute( $args ) {
		try {
			return $this->execute( $args );
		} catch ( Exception $e ) {
			/* translators: %s: error message */
			return Astra_Abilities_Response::error( sprintf( __( 'An unexpected error occurred: %s', 'astra' ), $e->getMessage() ) );
		} catch ( Error $e ) {
			/* translators: %s: error message */
			return Astra_Abilities_Response::error( sprintf( __( 'A system error occurred: %s', 'astra' ), $e->getMessage() ) );
		}
	}

	/**
	 * Get the output schema for the ability.
	 *
	 * Override in child classes to define the data properties
	 * returned in the success response.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return array();
	}

	/**
	 * Build a standardized output schema wrapping data properties
	 * in the Astra_Abilities_Response::success() format.
	 *
	 * @param array $data_properties Properties for the 'data' key.
	 * @return array Full output schema.
	 */
	protected function build_output_schema( $data_properties ) {
		return array(
			'type'       => 'object',
			'required'   => array( 'success', 'message' ),
			'properties' => array(
				'success' => array(
					'type'        => 'boolean',
					'description' => 'Whether the operation succeeded.',
				),
				'message' => array(
					'type'        => 'string',
					'description' => 'Human-readable result message.',
				),
				'data'    => array(
					'type'       => 'object',
					'properties' => $data_properties,
				),
			),
		);
	}

	/**
	 * Get usage examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array();
	}

	/**
	 * Get tool type (read, write, list).
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'read';
	}

	/**
	 * Get whether to show this ability in the REST API.
	 *
	 * @return bool
	 */
	public function get_show_in_rest() {
		/**
		 * Filter whether to show this ability in the REST API.
		 *
		 * @param bool   $show_in_rest     Whether to show in REST API. Default true.
		 * @param string $ability_id       The ability ID (e.g. 'astra/get-font-body').
		 * @param self   $ability_instance The ability instance.
		 * @since 4.12.6
		 */
		/** @psalm-suppress TooManyArguments -- WordPress apply_filters accepts variadic args for filter callbacks. */
		return apply_filters( 'astra_ability_show_in_rest', true, $this->id, $this );
	}

	/**
	 * Get MCP annotations based on tool type.
	 *
	 * Returns semantic hints for the MCP Adapter describing the ability's
	 * behavioral characteristics. Override in child classes for custom behavior.
	 *
	 * @return array{readonly: bool, destructive: bool, idempotent: bool}
	 */
	public function get_annotations() {
		$tool_type = $this->get_tool_type();

		// Read and list tools are readonly, write tools are not.
		return array(
			'readonly'    => 'write' !== $tool_type,
			'destructive' => false,
			'idempotent'  => true,
		);
	}

	/**
	 * Get MCP meta configuration for this ability.
	 *
	 * Returns the MCP Adapter metadata including public visibility
	 * and MCP type. The public flag is filterable via 'astra_ability_mcp_public'.
	 *
	 * @return array{public: bool, type: string}
	 */
	public function get_mcp() {
		/**
		 * Filter whether an Astra ability is publicly exposed via MCP.
		 *
		 * @since 4.12.6
		 *
		 * @param bool   $is_public        Whether the ability is public for MCP. Default true.
		 * @param string $ability_id       The ability ID (e.g. 'astra/get-font-body').
		 * @param self   $ability_instance The ability instance.
		 */
		/** @psalm-suppress TooManyArguments -- WordPress apply_filters accepts variadic args for filter callbacks. */
		$is_public = apply_filters( 'astra_ability_mcp_public', true, $this->id, $this );

		return array(
			'public' => (bool) $is_public,
			'type'   => 'tool',
		);
	}

	/**
	 * Check permissions.
	 *
	 * @param WP_REST_Request $request REST Request.
	 * @return bool|WP_Error
	 */
	public function check_permission( $request ) {
		return current_user_can( $this->capability );
	}

	/**
	 * Get the ability ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the ability label.
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get the ability description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get the category.
	 *
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * Get the meta data.
	 *
	 * @return array
	 */
	public function get_meta_data() {
		return $this->meta;
	}

	/**
	 * Get the tool version.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register this ability with the Abilities API.
	 *
	 * @return void
	 */
	public static function register() {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		/** @psalm-suppress UnsafeInstantiation -- Intentional: child classes share the same constructor signature. */
		$instance = new static();

		if ( empty( $instance->id ) ) {
			return;
		}

		// Skip write abilities when edit abilities are disabled.
		if ( 'write' === $instance->get_tool_type() && ! Astra_API_Init::get_admin_settings_option( 'enable_edit_abilities', true ) ) {
			return;
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
			'label'               => $instance->label,
			'description'         => $instance->description,
			'category'            => $instance->category,
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
