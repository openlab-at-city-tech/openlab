<?php
/**
 * TablePress REST API Integration for the DataTables Server-side Processing module.
 *
 * @package TablePress
 * @subpackage REST API
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the TablePress REST API integration for the DataTables Server-side Processing feature.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_ServerSide_Processing_REST_API extends WP_REST_Controller {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->namespace = 'tablepress/v1';
		$this->rest_base = 'ssp';

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 2.0.0
	 */
	#[\Override]
	public function register_routes(): void {
		// Return DataTables Server-side Processing data for a table.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<table_id>[A-Za-z1-9_-]|[A-Za-z0-9_-]{2,})',
			array(
				// Common arguments for all endpoints.
				'args'   => array(
					'table_id' => array(
						'description'       => __( 'A table ID consisting of letters, numbers, hyphens, and underscores.', 'tablepress' ),
						'type'              => 'string',
						'validate_callback' => array( $this, 'item_validate_callback_arg_table' ),
					),
				),
				// Individual endpoints.
				array(
					'methods'             => array( WP_REST_Server::READABLE, WP_REST_Server::CREATABLE ), // Allow HTTP GET and POST requests for this endpoint, so that long request URIs can be circumvented with a POST request.
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				// Options for all endpoints.
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Validates that a passed table ID has a valid format.
	 *
	 * @since 2.0.0
	 *
	 * @param string          $table_id The table ID.
	 * @param WP_REST_Request $request  Full details about the request.
	 * @param string          $key      Name of the passed variable (here: "table").
	 * @return true|WP_Error True if the passed value is valid, WP_Error otherwise.
	 */
	public function item_validate_callback_arg_table( string $table_id, WP_REST_Request $request, string $key ) /* : true|WP_Error */ {
		// Table IDs must only contain letters, numbers, hyphens (-), and underscores (_). The string "0" is not allowed.
		if ( 0 === preg_match( '/[^a-zA-Z0-9_-]/', $table_id ) && '0' !== $table_id ) {
			return true;
		}

		return new WP_Error(
			'tablepress_rest_api:invalid_argument:table_id',
			__( 'Table IDs must only contain letters, numbers, hyphens (-), and underscores (_). The string "0" is not allowed.', 'tablepress' )
		);
	}

	/**
	 * Checks if a given request has permission to edit a specific table.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|true True if the request has read access, WP_Error otherwise.
	 */
	#[\Override]
	public function get_item_permissions_check( /* WP_REST_Request */ $request ) /* : WP_Error|true */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		// Allow unauthenticated requests to the Server-side Processing endpoint.
		if ( 'view' === $request['context'] ) {
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

		if ( ! TablePress::$model_table->table_exists( $request['table_id'] ) ) {
			return new WP_Error(
				'tablepress_rest_api:table_not_found',
				__( 'Table not found.', 'tablepress' ),
				array( 'status' => 404 )
			);
		}

		// Load table, with table data, options, and visibility settings.
		$table = TablePress::$model_table->load( $request['table_id'], true, true );

		if ( is_wp_error( $table ) ) {
			$error = new WP_Error(
				'tablepress_rest_api:error_load_table',
				__( 'Could not load table', 'tablepress' ),
				array( 'status' => 500 )
			);
			$error->merge_from( $table );
			return $error;
		}

		$table = $this->generate_datatables_serverside_processing_response( $table, $request );

		$data = $this->prepare_item_for_response( $table, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares the necessary response data for a DataTables Server-side Processing request.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table   The TablePress table.
	 * @param WP_REST_Request      $request Full details about the request.
	 * @return array<string, mixed> Response data.
	 */
	public function generate_datatables_serverside_processing_response( array $table, WP_REST_Request $request ): array {
		// The `draw` parameter is required for DataTables.
		$draw = $request->get_param( 'draw' );
		if ( is_null( $draw ) || ! is_numeric( $draw ) ) {
			return array(
				'error' => sprintf( __( 'The REST API request parameter "%s" is invalid.', 'tablepress' ), 'draw' ),
			);
		}

		// The required `start` and `length` parameters define the chunk of rows.
		$start = $request->get_param( 'start' );
		if ( is_null( $start ) || ! is_numeric( $start ) ) {
			return array(
				'error' => sprintf( __( 'The REST API request parameter "%s" is invalid.', 'tablepress' ), 'start' ),
			);
		}
		$length = $request->get_param( 'length' );
		if ( is_null( $length ) || ! is_numeric( $length ) ) {
			return array(
				'error' => sprintf( __( 'The REST API request parameter "%s" is invalid.', 'tablepress' ), 'length' ),
			);
		}

		// The required `columns` parameter defines the search and sort options for the columns.
		$columns = $request->get_param( 'columns' );
		if ( is_null( $columns ) || ! is_array( $columns ) ) {
			return array(
				'error' => sprintf( __( 'The REST API request parameter "%s" is invalid.', 'tablepress' ), 'columns' ),
			);
		}

		$encrypted_render_options = $request->get_param( 'r' );
		if ( is_null( $encrypted_render_options ) || ! is_string( $encrypted_render_options ) ) {
			return array(
				'error' => sprintf( __( 'The REST API request parameter "%s" is invalid.', 'tablepress' ), 'r' ),
			);
		}

		$encrypted_render_options_nonce = $request->get_param( 'n' );
		if ( is_null( $encrypted_render_options_nonce ) || ! is_string( $encrypted_render_options_nonce ) ) {
			return array(
				'error' => sprintf( __( 'The REST API request parameter "%s" is invalid.', 'tablepress' ), 'n' ),
			);
		}

		$encrypted_render_options = TablePress_Module_DataTables_ServerSide_Processing::base64_url_decode( $encrypted_render_options );
		$encrypted_render_options_nonce = TablePress_Module_DataTables_ServerSide_Processing::base64_url_decode( $encrypted_render_options_nonce );
		if ( false === $encrypted_render_options || false === $encrypted_render_options_nonce ) {
			return array(
				'error' => __( 'The REST API request parameters do not have the correct format.', 'tablepress' ),
			);
		}

		// Try to decrypt the sent encrypted request.
		try {
			$secret_key = sodium_crypto_generichash( wp_salt( 'nonce' ), '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
			$render_options = sodium_crypto_secretbox_open( $encrypted_render_options, $encrypted_render_options_nonce, $secret_key );
		} catch ( Error $error ) {
			return array(
				'error' => __( 'The REST API request parameters do not have the correct format.', 'tablepress' ),
			);
		}

		if ( false === $render_options ) {
			return array(
				'error' => __( 'The REST API request parameters do not have the correct format.', 'tablepress' ),
			);
		}

		// Decode the decrypted JSON request.
		$render_options = json_decode( $render_options, true );
		if ( is_null( $render_options ) ) {
			return array(
				'error' => __( 'The REST API request parameters do not have the correct format.', 'tablepress' ),
			);
		}

		if ( $table['id'] !== $render_options['id'] ) {
			return array(
				'error' => __( 'The REST API request parameters do not have the correct format.', 'tablepress' ),
			);
		}

		// Prevent infinite loops.
		$render_options['datatables_serverside_processing'] = false;

		// Render/generate the table output.
		$_render = TablePress::load_class( 'TablePress_Render', 'class-render.php', 'classes' );
		// Merge desired options with default render options (see TablePress_Controller_Frontend::shortcode_table()).
		$default_render_options = $_render->get_default_render_options();
		/** This filter is documented in controllers/controller-frontend.php */
		$default_render_options = apply_filters( 'tablepress_shortcode_table_default_shortcode_atts', $default_render_options );
		$render_options = shortcode_atts( $default_render_options, $render_options );
		/** This filter is documented in controllers/controller-frontend.php */
		$render_options = apply_filters( 'tablepress_shortcode_table_shortcode_atts', $render_options );

		foreach ( $render_options as $key => $value ) {
			if ( is_null( $value ) && isset( $table['options'][ $key ] ) ) {
				$render_options[ $key ] = $table['options'][ $key ];
			}
		}

		// Check if table output shall and can be loaded from the transient cache, otherwise generate the output.
		if ( $render_options['cache_table_output'] && ! is_user_logged_in() ) {
			// Hash the Render Options array to get a unique cache identifier.
			$table_hash = md5( wp_json_encode( $render_options, TABLEPRESS_JSON_OPTIONS ) ); // @phpstan-ignore-line
			$transient_name = 'tablepress_j_' . $table_hash; // Attention: This string must not be longer than 45 characters!
			$cached_table_data = get_transient( $transient_name );
			$table_data = array(); // This line is needed to prevent a bunch of false PHPStan errors.
			if ( false !== $cached_table_data && '' !== $cached_table_data ) {
				$table_data = json_decode( $cached_table_data, true );
				// Check if JSON could be decoded.
				if ( is_null( $table_data ) ) {
					$cached_table_data = false;
				}
			}
			if ( false === $cached_table_data || '' === $cached_table_data ) {
				// Render/generate the table, as it was not found in the cache or could not be decoded.
				$_render->set_input( $table, $render_options );
				$table_data = $_render->get_output( 'array' );
				// Save render output in a transient, set cache timeout to 24 hours.
				set_transient( $transient_name, wp_json_encode( $table_data, TABLEPRESS_JSON_OPTIONS ), DAY_IN_SECONDS );
				// Update output caches list transient (necessary for cache invalidation upon table saving).
				$caches_list_transient_name = 'tablepress_c_' . md5( $table['id'] );
				$caches_list = get_transient( $caches_list_transient_name );
				if ( false === $caches_list ) {
					$caches_list = array();
				} else {
					$caches_list = (array) json_decode( $caches_list, true );
				}
				if ( ! in_array( $transient_name, $caches_list, true ) ) {
					$caches_list[] = $transient_name;
				}
				set_transient( $caches_list_transient_name, wp_json_encode( $caches_list, TABLEPRESS_JSON_OPTIONS ), 2 * DAY_IN_SECONDS );
			}
		} else {
			// Render/generate the table HTML, as no cache is to be used.
			$_render->set_input( $table, $render_options );
			$table_data = $_render->get_output( 'array' );
		}

		// Remove table head and foot rows from processing, as these are set in the HTML already.
		if ( $render_options['table_head'] ) {
			array_shift( $table_data );
		}
		if ( $render_options['table_foot'] ) {
			array_pop( $table_data );
		}

		$records_total = count( $table_data );

		/*
		 * Filter the table data.
		 */

		// Add the global filter term to the $columns array, so that it can use the parsing loop for filter terms below as well.
		$search = $request->get_param( 'search' );
		if ( ! is_null( $search ) ) {
			$columns['global'] = array(
				'searchable' => 'true',
				'search'     => $search,
			);
		}

		$run_filter_loop = false;

		// Convert filter terms from string to array, and generate full regexp strings where set.
		$not_filtered_column_indices = array();
		foreach ( $columns as $col_idx => $column ) {
			if ( 'false' === $column['searchable'] ) {
				$not_filtered_column_indices[] = $col_idx;
				unset( $columns[ $col_idx ] );
				continue;
			}

			if ( '' === trim( $column['search']['value'] ) ) {
				$columns[ $col_idx ]['search'] = array(
					'regex' => 'false',
					'value' => array(),
				);
				continue;
			}

			// If this is reached, at least one column is searchable and has a non-empty filter term.
			$run_filter_loop = true;

			if ( 'true' === $column['search']['regex'] ) {
				$regex = str_replace( '#', '\#', $column['search']['value'] ); // Escape regex delimiter.
				$columns[ $col_idx ]['search']['value'] = "#{$regex}#i";
			} else {
				$column_filter_terms = array();
				// Split column filter term at spaces and terms wrapped in quotation marks.
				preg_match_all( '#"[^"]+"|[^ ]+#', $column['search']['value'], $column_filter_terms, PREG_SET_ORDER );
				// Extract first array element (the regexp match) and remove any quotation marks around filter terms.
				$column_filter_terms = array_map(
					static function ( array $filter_term ): string {
						return trim( $filter_term[0], '"' );
					},
					$column_filter_terms
				);
				$columns[ $col_idx ]['search']['value'] = array_unique( $column_filter_terms );
			}
		}

		if ( $run_filter_loop ) { // Nothing to do if no to-be-filtered columns with filter terms exist.
			// Generate filter data, by removing columns that shall not be filtered or that have no filter terms.
			$filter_data = $table_data;
			foreach ( $filter_data as &$row ) {
				foreach ( $not_filtered_column_indices as $col_idx ) {
					unset( $row[ $col_idx ] );
				}
			}
			unset( $row ); // Unset use-by-reference parameter of foreach loop.

			// Return all rows that contain all filter terms.
			$filtered_table_data = array();
			foreach ( $filter_data as $row_idx => $row ) {
				foreach ( $columns as $col_idx => $column ) {
					if ( 'global' === $col_idx ) {
						if ( 'true' === $column['search']['regex'] ) {
							$filter_term_found = false;
							foreach ( $row as $cell_content ) {
								if ( 1 === preg_match( $column['search']['value'], $cell_content ) ) {
									$filter_term_found = true;
									break;
								}
							}
							if ( ! $filter_term_found ) {
								continue 2; // Continue with next row, as not all global filter terms were found.
							}
						} else {
							foreach ( $column['search']['value'] as $filter_term ) {
								$filter_term_found = false;
								foreach ( $row as $cell_content ) {
									if ( false !== stripos( $cell_content, $filter_term ) ) {
										$filter_term_found = true;
										break;
									}
								}
								if ( ! $filter_term_found ) {
									continue 3; // Continue with next row, as not all global filter terms were found.
								}
							}
						}
					} else { // phpcs:ignore Universal.ControlStructures.DisallowLonelyIf.Found
						// Search for individual column filter terms.
						if ( 'true' === $column['search']['regex'] ) {
							if ( 1 !== preg_match( $column['search']['value'], $row[ $col_idx ] ) ) {
								continue 2; // Continue with next row, as a column filter term was not found.
							}
						} else {
							foreach ( $column['search']['value'] as $filter_term ) {
								if ( false === stripos( $row[ $col_idx ], $filter_term ) ) {
									continue 3; // Continue with next row, as a column filter term was not found.
								}
							}
						}
					}
				}

				// If this is reached, all global and column filter terms were found.
				$filtered_table_data[] = $table_data[ $row_idx ];
			}
			$table_data = $filtered_table_data;
			unset( $filtered_table_data, $filter_data );
		}

		$records_filtered = count( $table_data );

		/*
		 * Sort the (maybe filtered) table data.
		 */

		$order = $request->get_param( 'order' );
		if ( ! is_null( $order ) && is_array( $order ) && count( $table_data ) > 1 ) { // Nothing to sort if no order command was given or if the table only has one row left.

			/*
			 * Swap rows and columns to be able to use `array_multisort()`.
			 * This fails for single-row tables, but that case is excluded above.
			 */
			$sort_data = array_map( null, ...$table_data );

			$order_commands = array();
			foreach ( $order as $order_command ) {
				$column_data = $sort_data[ $order_command['column'] ];
				/**
				 * Filters the sort data for a column.
				 *
				 * This can be used to modify a column's data as used for sorting. For rendering, the original data will be used.
				 *
				 * @since 2.0.0
				 *
				 * @param string[] $column_data Sort data for the column.
				 * @param string   $table_id    Table ID.
				 * @param int      $col_idx     Index of the column that is to be sorted.
				 */
				$column_data = apply_filters( 'tablepress_serverside_processing_order_column_data', $column_data, $table['id'], $order_command['column'] );

				// Sort numerically for pure number columns, and "natural" otherwise.
				$column_type = SORT_NUMERIC;
				foreach ( $column_data as $cell_content ) {
					if ( ! is_numeric( $cell_content ) ) {
						$column_type = SORT_NATURAL | SORT_FLAG_CASE; // Sort non-numeric columns "naturally" and case-insensitive.
						break;
					}
				}

				$order_commands[] = $column_data;
				$order_commands[] = 'asc' === $order_command['dir'] ? SORT_ASC : SORT_DESC;
				$order_commands[] = $column_type;
			}

			/*
			 * The actual table data is added as the last array argument, together with the sorting direction and type.
			 * Before that, a temporary numeric column is added as the first column.
			 * This is used to preserve the original order of rows with same values in the sorting columns.
			 * That extra column is removed again after the sorting.
			 */
			foreach ( $table_data as $row_idx => &$row ) {
				array_unshift( $row, $row_idx );
			}
			unset( $row ); // Unset use-by-reference parameter of foreach loop.

			$order_commands[] = &$table_data; // The by-reference here is needed due to the passing of the function arguments via an array.
			$order_commands[] = SORT_ASC;
			$order_commands[] = SORT_NUMERIC;

			array_multisort( ...$order_commands );

			// Remove the temporary first column again.
			foreach ( $table_data as &$row ) {
				array_shift( $row );
			}
			unset( $row ); // Unset use-by-reference parameter of foreach loop.
		}

		/*
		 * Apply pagination to only return desired chunk of rows.
		 */

		$start = absint( $start );
		$length = (int) $length;
		// Pass null as the length value, if -1 is used, to indicate that all rows should be returned.
		if ( -1 === $length ) {
			$length = null;
		}
		$table_data = array_slice( $table_data, $start, $length );

		return array(
			'draw'            => absint( $draw ),
			'recordsTotal'    => absint( $records_total ),
			'recordsFiltered' => absint( $records_filtered ),
			'data'            => $table_data,
		);
	}

	/**
	 * Prepares the generated data for the response.
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

		$table = $this->add_additional_fields_to_object( $table, $request );
		$table = $this->filter_response_by_context( $table, $request['context'] );

		return rest_ensure_response( $table );
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
				'data'            => array(
					'description' => __( 'The table data.', 'tablepress' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'draw'            => array(
					'description' => __( 'DataTables Server-side Processing draw counter.', 'tablepress' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'recordsTotal'    => array(
					'description' => __( 'DataTables Server-side Processing total number of rows.', 'tablepress' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'recordsFiltered' => array(
					'description' => __( 'DataTables Server-side Processing number of filtered rows.', 'tablepress' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'error'           => array(
					'description' => __( 'DataTables Server-side Processing error.', 'tablepress' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
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
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

} // class TablePress_Module_DataTables_ServerSide_Processing_REST_API
