<?php
/**
 * TablePress REST API Integration.
 *
 * @package TablePress
 * @subpackage REST API
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the TablePress REST API integration.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_REST_API extends WP_REST_Controller {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * The table property to sort by.
	 *
	 * @since 2.4.0
	 * @var string
	 */
	protected $orderby;

	/**
	 * The sort direction when sorting by a table property.
	 * Either 'asc' or 'desc'.
	 *
	 * @since 2.4.0
	 * @var string
	 */
	protected $order;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->namespace = 'tablepress/v1';
		$this->rest_base = 'tables';

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 2.0.0
	 */
	#[\Override]
	public function register_routes(): void {
		// Return the List of Tables.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				// Individual endpoints.
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				// Options for all endpoints.
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		// Return information about a single table.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[A-Za-z1-9_-]|[A-Za-z0-9_-]{2,})', // Table IDs must only contain letters, numbers, hyphens (-), and underscores (_). The string "0" is not allowed.
			array(
				// Common arguments for all endpoints.
				'args'   => array(
					'id' => array(
						'description' => __( 'A table ID consisting of letters, numbers, hyphens (-), and underscores (_). The string "0" is not allowed.', 'tablepress' ),
						'type'        => 'string',
					),
				),
				// Individual endpoints.
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'edit' ) ),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
				// Options for all endpoints.
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has permission to read tables.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error True if the request has read access, WP_Error or false otherwise.
	 */
	#[\Override]
	public function get_items_permissions_check( /* WP_REST_Request */ $request ) /* : bool|WP_Error */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		/**
		 * Allows overriding the TablePress REST API permission check.
		 *
		 * If the filter returns `true` or `false` that will be used to short-circuit the permissions checks.
		 *
		 * @since 2.1.0
		 *
		 * @param bool|null    $permissions_check Overriding permission check value.
		 * @param WP_REST_Request $request           Full details about the request.
		 */
		$permissions_check = apply_filters( 'tablepress_rest_api_permissions_check', null, $request );
		if ( ! is_null( $permissions_check ) ) {
			return $permissions_check;
		}

		if ( current_user_can( 'tablepress_list_tables' ) ) {
			return true;
		}

		return new WP_Error(
			'tablepress_rest_api:missing_capability:tablepress_list_tables',
			__( 'Sorry, you are not allowed to view the list of tables.', 'tablepress' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Callback to for the array sort function.
	 *
	 * @since 2.4.0
	 *
	 * @param array<string, mixed> $item_a First item that shall be compared to.
	 * @param array<string, mixed> $item_b The second item for the comparison.
	 * @return int (-1, 0, 1) depending on which item sorts "higher".
	 */
	protected function order_callback( array $item_a, array $item_b ): int {
		if ( 'last_modified_by' !== $this->orderby ) {
			if ( $item_a[ $this->orderby ] === $item_b[ $this->orderby ] ) {
				return 0;
			}
		} else { // phpcs:ignore Universal.ControlStructures.DisallowLonelyIf.Found
			if ( $item_a['options']['last_editor'] === $item_b['options']['last_editor'] ) {
				return 0;
			}
		}

		// Certain fields require some extra work before being sortable.
		switch ( $this->orderby ) {
			case 'last_modified':
				// Compare UNIX timestamps for "last modified", which actually is a mySQL datetime string.
				$result = ( strtotime( $item_a['last_modified'] ) > strtotime( $item_b['last_modified'] ) ) ? 1 : -1;
				break;
			case 'author':
				// Get the actual author name, plain value is just the user ID.
				$result = strnatcasecmp( TablePress::get_user_display_name( $item_a['author'] ), TablePress::get_user_display_name( $item_b['author'] ) );
				break;
			case 'last_modified_by':
				// Get the actual last editor name, plain value is just the user ID.
				$result = strnatcasecmp( TablePress::get_user_display_name( $item_a['options']['last_editor'] ), TablePress::get_user_display_name( $item_b['options']['last_editor'] ) );
				break;
			default:
				// Other fields (ID, name, description) are sorted as strings.
				$result = strnatcasecmp( $item_a[ $this->orderby ], $item_b[ $this->orderby ] );
		}

		return ( 'asc' === $this->order ) ? $result : - $result;
	}

	/**
	 * Retrieves all tables.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error on failure.
	 */
	#[\Override]
	public function get_items( /* WP_REST_Request */ $request ) /* : WP_Error|WP_REST_Response */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		$prime_meta_cache = ( 'list' !== $request['context'] );
		$table_ids = TablePress::$model_table->load_all( $prime_meta_cache );
		$tables = array();

		foreach ( $table_ids as $table_id ) {
			if ( 'list' === $request['context'] ) {
				$table = array(
					'id' => $table_id,
				);
			} else {
				$table = TablePress::$model_table->load( $table_id );
			}
			$table = $this->prepare_item_for_response( $table, $request ); // @phpstan-ignore-line
			$tables[] = $this->prepare_response_for_collection( $table ); // @phpstan-ignore-line
		}

		$this->orderby = $request['orderby'];
		$this->order = $request['order'];

		// Sort the list of tables, except if it's empty or already has the desired order.
		if ( ! empty( $tables ) && ! ( 'id' === $this->orderby && 'asc' === $this->order ) ) {
			// For the "list" context, only the table ID is available, so we need to load the full table.
			if ( 'list' === $request['context'] ) {
				foreach ( $tables as &$table ) {
					// Don't load data, but load table options for access to last_editor.
					$table = TablePress::$model_table->load( $table['id'], false, true );
				}
				unset( $table ); // Unset use-by-reference parameter of foreach loop.
			}

			usort( $tables, array( $this, 'order_callback' ) );

			// Restore the "list" context response format, by removing the table data.
			if ( 'list' === $request['context'] ) {
				foreach ( $tables as &$table ) {
					$table = array(
						'id' => $table['id'],
					);
					$table = $this->prepare_item_for_response( $table, $request );
					$table = $this->prepare_response_for_collection( $table ); // @phpstan-ignore-line
				}
				unset( $table ); // Unset use-by-reference parameter of foreach loop.
			}
		}

		$tables_per_page = $request['per_page'];
		$page = $request['page'];
		$offset = $request['offset'];

		$total_tables = count( $tables );
		$max_pages = (int) ceil( $total_tables / $tables_per_page );
		if ( $page > $max_pages ) {
			$page = $max_pages;
		}

		$offset = ( $page - 1 ) * $tables_per_page + $offset;

		$tables = array_slice( $tables, $offset, $tables_per_page );

		$response = rest_ensure_response( $tables );
		$response->header( 'X-WP-Total', (string) $total_tables );
		$response->header( 'X-WP-TotalPages', (string) $max_pages );

		$request_params = $request->get_query_params();
		$collection_url = rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) );
		$base           = add_query_arg( urlencode_deep( $request_params ), $collection_url );

		if ( $page > 1 ) {
			$prev_page = $page - 1;
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $page < $max_pages ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Checks if a given request has permission to add a new table.
	 *
	 * @since 2.2.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|true True if the request has access to add a new table, WP_Error object otherwise.
	 */
	#[\Override]
	public function create_item_permissions_check( /* WP_REST_Request */ $request ) /* : WP_Error|true */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		/** This filter is documented in modules/controllers/rest-api.php */
		$permissions_check = apply_filters( 'tablepress_rest_api_permissions_check', null, $request );
		if ( ! is_null( $permissions_check ) ) {
			return $permissions_check;
		}

		if ( current_user_can( 'tablepress_add_tables' ) ) {
			return true;
		}

		return new WP_Error(
			'tablepress_rest_api:missing_capability:tablepress_add_tables',
			__( 'Sorry, you are not allowed to add a new table.', 'tablepress' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Creates a new table.
	 *
	 * @since 2.2.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	#[\Override]
	public function create_item( /* WP_REST_Request */ $request ) /* : WP_REST_Response|WP_Error */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		// Create a new table array with information from the posted data.
		$new_table = array(
			'name'        => $request['name'],
			'description' => $request['description'],
			'data'        => array_fill( 0, $request['rows'], array_fill( 0, $request['columns'], '' ) ),
			'visibility'  => array(
				'rows'    => array_fill( 0, $request['rows'], 1 ),
				'columns' => array_fill( 0, $request['columns'], 1 ),
			),
		);

		// Merge this data into an empty table template.
		$table = TablePress::$model_table->prepare_table( TablePress::$model_table->get_table_template(), $new_table, false );
		if ( is_wp_error( $table ) ) {
			$error = new WP_Error(
				'tablepress_rest_api:add_table:error_prepare',
				__( 'The new table could not be added.', 'tablepress' ),
				array( 'status' => 500 )
			);
			$error->merge_from( $table );
			return $error;
		}

		// Add the new table and get its first ID.
		$table_id = TablePress::$model_table->add( $table );
		if ( is_wp_error( $table_id ) ) {
			$error = new WP_Error(
				'tablepress_rest_api:add_table:error_add',
				__( 'The new table could not be added.', 'tablepress' ),
				array( 'status' => 500 )
			);
			$error->merge_from( $table_id );
			return $error;
		}

		// Load table, with table data, options, and visibility settings.
		$table = TablePress::$model_table->load( $table_id, true, true );
		if ( is_wp_error( $table ) ) {
			$error = new WP_Error(
				'tablepress_rest_api:add_table:error_load',
				__( 'Could not load table', 'tablepress' ),
				array( 'status' => 500 )
			);
			$error->merge_from( $table );
			return $error;
		}

		$request->set_param( 'context', 'edit' );

		$data = $this->prepare_item_for_response( $table, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has permission to read a specific table.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|true True if the request has read access, WP_Error otherwise.
	 */
	#[\Override]
	public function get_item_permissions_check( /* WP_REST_Request */ $request ) /* : WP_Error|true */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		/** This filter is documented in modules/controllers/rest-api.php */
		$permissions_check = apply_filters( 'tablepress_rest_api_permissions_check', null, $request );
		if ( ! is_null( $permissions_check ) ) {
			return $permissions_check;
		}

		if ( current_user_can( 'tablepress_list_tables' ) && current_user_can( 'tablepress_edit_table', $request['id'] ) ) {
			return true;
		}

		return new WP_Error(
			'tablepress_rest_api:missing_capability:tablepress_edit_table',
			__( 'Sorry, you are not allowed to view this table.', 'tablepress' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Retrieves a specific table.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error on failure.
	 */
	#[\Override]
	public function get_item( /* WP_REST_Request */ $request ) /* : WP_Error|WP_REST_Response */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		if ( ! TablePress::$model_table->table_exists( $request['id'] ) ) {
			return new WP_Error(
				'tablepress_rest_api:table_not_found',
				__( 'Table not found.', 'tablepress' ),
				array( 'status' => 404 )
			);
		}

		// Load table, with table data, options, and visibility settings.
		$table = TablePress::$model_table->load( $request['id'], true, true );

		if ( is_wp_error( $table ) ) {
			$error = new WP_Error(
				'tablepress_rest_api:error_load_table',
				__( 'Could not load the table.', 'tablepress' ),
				array( 'status' => 500 )
			);
			$error->merge_from( $table );
			return $error;
		}

		$data = $this->prepare_item_for_response( $table, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to delete a specific table.
	 *
	 * @since 2.2.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to delete the table, WP_Error object otherwise.
	 */
	#[\Override]
	public function delete_item_permissions_check( /* WP_REST_Request */ $request ) /* : true|WP_Error */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		/** This filter is documented in modules/controllers/rest-api.php */
		$permissions_check = apply_filters( 'tablepress_rest_api_permissions_check', null, $request );
		if ( ! is_null( $permissions_check ) ) {
			return $permissions_check;
		}

		if ( current_user_can( 'tablepress_delete_table', $request['id'] ) ) {
			return true;
		}

		return new WP_Error(
			'tablepress_rest_api:missing_capability:tablepress_delete_table',
			__( 'Sorry, you are not allowed to delete this table.', 'tablepress' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Deletes a specific table.
	 *
	 * @since 2.2.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	#[\Override]
	public function delete_item( /* WP_REST_Request */ $request ) /* : WP_REST_Response|WP_Error */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		$deleted = TablePress::$model_table->delete( $request['id'] );
		if ( is_wp_error( $deleted ) ) {
			$error = new WP_Error(
				'tablepress_rest_api:delete_table:error_delete',
				__( 'Could not delete the table.', 'tablepress' ),
				array( 'status' => 500 )
			);
			$error->merge_from( $deleted );
			return $error;
		}

		return rest_ensure_response( $deleted );
	}

	/**
	 * Prepares a table for the response.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table   The TablePress table.
	 * @param WP_REST_Request      $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	#[\Override]
	public function prepare_item_for_response( /* array */ $table, /* WP_REST_Request */ $request ) /* : WP_REST_Response|WP_Error */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		$fields = $this->get_fields_for_response( $request );

		$table = $this->add_additional_fields_to_object( $table, $request );
		$table = $this->filter_response_by_context( $table, $request['context'] );

		$response = rest_ensure_response( $table );
		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$response->add_links( $this->prepare_links( $table ) );
		}

		return $response;
	}

	/**
	 * Prepares links for the table request.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table The TablePress table.
	 * @return array<string, array<string, string>> Links for the given table.
	 */
	protected function prepare_links( array $table ): array {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $table['id'] ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);
		return $links;
	}

	/**
	 * Retrieves the table's schema, conforming to JSON Schema.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string, mixed> Item schema data.
	 */
	#[\Override]
	public function get_item_schema(): array {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'TablePress table',
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'description' => __( 'The table ID.', 'tablepress' ),
					'type'        => 'string',
					'pattern'     => '[A-Za-z1-9_-]|[A-Za-z0-9_-]{2,}',
					'context'     => array( 'edit', 'list' ),
					'readonly'    => true,
				),
				'name'          => array(
					'description' => __( 'The table name.', 'tablepress' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'default'     => '',
				),
				'description'   => array(
					'description' => __( 'The table description.', 'tablepress' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'default'     => '',
				),
				'rows'          => array(
					'description' => __( 'The number of rows in the table.', 'tablepress' ),
					'type'        => 'integer',
					'context'     => array(),
					'required'    => true,
					'minimum'     => 1,
				),
				'columns'       => array(
					'description' => __( 'The number of columns in the table.', 'tablepress' ),
					'type'        => 'integer',
					'context'     => array(),
					'required'    => true,
					'minimum'     => 1,
				),
				'data'          => array(
					'description' => __( 'The table data.', 'tablepress' ),
					'type'        => 'array',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'options'       => array(
					'description' => __( 'The table options.', 'tablepress' ),
					'type'        => 'array',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'visibility'    => array(
					'description' => __( 'The table visibility settings.', 'tablepress' ),
					'type'        => 'array',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'author'        => array(
					'description' => __( 'The table author.', 'tablepress' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'last_modified' => array(
					'description' => __( 'The table’s Last Modified time and date.', 'tablepress' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
			),
		);
		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string, mixed> Collection parameters.
	 */
	#[\Override]
	public function get_collection_params(): array {
		return array(
			'context'  => $this->get_context_param( array( 'default' => 'list' ) ),
			'page'     => array(
				'description'       => __( 'Current page of the list of tables.', 'tablepress' ),
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of tables to be returned in result set.', 'tablepress' ),
				'type'              => 'integer',
				'default'           => 100,
				'minimum'           => 1,
				'maximum'           => 1000,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'offset'   => array(
				'description'       => __( 'Offset the result set by a specific number of tables.', 'tablepress' ),
				'type'              => 'integer',
				'default'           => 0,
				'minimum'           => 0,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'order'    => array(
				'description' => __( 'Order the result set ascending or descending.', 'tablepress' ),
				'type'        => 'string',
				'default'     => 'asc',
				'enum'        => array( 'asc', 'desc' ),
			),
			'orderby'  => array(
				'description' => __( 'Sort collection by table attribute.', 'tablepress' ),
				'type'        => 'string',
				'default'     => 'id',
				'enum'        => array(
					'id',
					'name',
					'description',
					'author',
					'last_modified_by',
					'last_modified',
				),
			),
		);
	}

} // class TablePress_Module_REST_API
