<?php defined( 'ABSPATH' ) || exit();

/**
 * REST API Controller for AI Search functionality
 * 
 * Provides secure REST endpoints for search operations
 */
class EPKB_AI_REST_Search_Controller extends EPKB_AI_REST_Base_Controller {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Register the routes for AI Search
	 */
	public function register_routes() {

		if ( ! EPKB_AI_Utilities::is_ai_search_enabled() ) {
			return;
		}

		register_rest_route( $this->public_namespace, '/ai-search/search', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'search' ),
				'permission_callback' => [ $this, 'check_rest_nonce' ],
				'args'                => $this->get_search_params(),
			),
		) );

		// register admin routes only if in admin context
		$request_uri = $_SERVER['REQUEST_URI'] ?? '';
		if ( strpos( $request_uri, 'epkb-admin' ) === false ) {
			return;
		}

		// Admin endpoints for search conversation management
		register_rest_route( $this->admin_namespace, '/ai-search/searches', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_searches_history'),
				'permission_callback' => array( 'EPKB_AI_Security', 'can_access_settings' ),
				'args'                => $this->get_table_params(),
			),
		) );

		// Bulk delete conversations
		register_rest_route( $this->admin_namespace, '/ai-search/searches/bulk', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_selected_conversations' ),
				'permission_callback' => array( 'EPKB_AI_Security', 'can_access_settings' ),
				'args'                => array(
					'ids' => array(
						'required'          => true,
						'validate_callback' => function( $param ) {
							return is_array( $param ) && ! empty( $param );
						},
						'sanitize_callback' => function( $param ) {
							return array_map( 'absint', $param );
						},
					),
				),
			),
		) );
	}

	public function check_rest_nonce( $request ) {

		if ( ! EPKB_AI_Utilities::is_ai_search_enabled() ) {
			return new WP_Error( 'ai_search_disabled', __( 'AI search is not enabled', 'echo-knowledge-base' ), array( 'status' => 403 ) );
		}

		return EPKB_AI_Security::check_rest_nonce( $request );
	}

	/**
	 * Handle search request
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function search( $request ) {

		// Get search query
		$query = $request->get_param( 'query' );
		if ( empty( $query ) || strlen( $query ) < 3 ) {
			return $this->create_rest_response( array( 'message' => __( 'Search query must be at least 3 characters long.', 'echo-knowledge-base' ) ), 400 );
		}

		$kb_id = absint( $request->get_param( 'kb_id' ) );
		if ( empty( $kb_id ) ) {
			return $this->create_rest_response( array( 'message' => __( 'Knowledge Base ID is required.', 'echo-knowledge-base' ) ), 400 );
		}

		// collection_id is required - set via kb_ai_collection_id in KB Config or shortcode/block attribute
		$collection_id = $request->get_param( 'collection_id' );
		if ( empty( $collection_id ) ) {
			return $this->create_rest_response( array( 'message' => __( 'No AI data collection selected. Please select a collection in KB Configuration.', 'echo-knowledge-base' ) ), 400 );
		}

		$collection_access = $this->validate_public_collection_request( $request, $kb_id, $collection_id );
		if ( is_wp_error( $collection_access ) ) {
			return $this->create_rest_response( array(), 403, $collection_access );
		}

		/* $rate_limit_check = EPKB_AI_Security::check_search_rate_limit();
		if ( is_wp_error( $rate_limit_check ) ) {
			return $this->create_rest_response( array(), 429, $rate_limit_check );
		} */

		// Validate collection configuration (provider match, vector store exists)
		$config_error = $this->get_collection_configuration_error( $collection_id );
		if ( $config_error !== null ) {
			return $this->create_rest_response( array( 'message' => $config_error['message'], 'error_type' => $config_error['type'] ), 400 );
		}

		// Initialize search handler
		$search_handler = new EPKB_AI_Search_Handler();
		$result = $search_handler->search( $query, $collection_id );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array( 'query' => $query ), 500, $result );
		}

		// Return result directly - handler already returns the correct format for JavaScript
		return $this->create_rest_response( $result );
	}

	/**
	 * Get history of searches (conversations) for admin
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_searches_history( $request ) {

		// Get parameters
		$per_page = absint( $request->get_param( 'per_page' ) ?: 10 );
		$page = absint( $request->get_param( 'page' ) ?: 1 );
		$offset = $request->get_param( 'offset' );
		if ( $offset !== null && $offset !== '' ) {
			$offset = absint( $offset );
		} else {
			$offset = null;
		}
		$search = sanitize_text_field( $request->get_param( 'search' ) ?: '' );
		$orderby = sanitize_text_field( $request->get_param( 'orderby' ) ?: 'created' );
		$order = strtoupper( sanitize_text_field( $request->get_param( 'order' ) ?: 'DESC' ) );

		// Validate order
		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
			$order = 'DESC';
		}

		// Get data using existing table operations
		$args = array(
			'per_page' => $per_page,
			'page'     => $page,
			's'        => $search,
			'orderby'  => $orderby,
			'order'    => $order
		);
		if ( $offset !== null ) {
			$args['offset'] = $offset;
		}
		$result = EPKB_AI_Table_Operations::get_table_data( 'search', $args );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array(), 500, $result );
		}

		// Transform 'items' to 'searches' for frontend compatibility
		if ( isset( $result['items'] ) ) {
			$result['searches'] = $result['items'];
			unset( $result['items'] );
		}

		return $this->create_rest_response( $result );
	}


	/**
	 * Delete selected conversations
	 * 
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function delete_selected_conversations( $request ) {
		
		$ids = $request->get_param( 'ids' );

		$result = EPKB_AI_Table_Operations::delete_selected_rows( 'search', $ids );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array(), 500, $result );
		}

		// translators: %d is the number of deleted search conversations
		return $this->create_rest_response( array( 'message' => sprintf( __( '%d search conversations deleted successfully.', 'echo-knowledge-base' ), $result ), 'deleted' => $result ) );
	}


	/**
	 * Check if collection configuration is valid for search
	 *
	 * @param int $collection_id
	 * @return array|null Error info or null if no error
	 */
	private function get_collection_configuration_error( $collection_id ) {

		$is_admin = EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' );

		// Check for provider mismatch first
		$provider_mismatch = EPKB_AI_Training_Data_Config_Specs::get_active_and_selected_provider_if_mismatched( $collection_id );
		if ( $provider_mismatch !== null ) {
			return array(
				'type'    => 'provider_mismatch',
				// translators: %1$s is the collection provider, %2$s is the active provider
				'message' => $is_admin
					? sprintf(
						__( 'AI Search unavailable: Collection uses %1$s but active provider is %2$s.', 'echo-knowledge-base' ),
						$provider_mismatch['collection_provider'],
						$provider_mismatch['active_provider']
					)
					: __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
			);
		}

		// Check for collection/vector store issues
		$vector_store_result = EPKB_AI_Training_Data_Config_Specs::get_vector_store_id_by_collection( $collection_id );
		if ( is_wp_error( $vector_store_result ) ) {
			$error_code = $vector_store_result->get_error_code();

			// Provide helpful messages for specific error types
			if ( $error_code === 'collection_not_found' ) {
				return array(
					'type'    => $error_code,
					'message' => $is_admin
						? __( 'The selected AI data collection no longer exists. Please select a different collection in KB Configuration.', 'echo-knowledge-base' )
						: __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
				);
			}

			if ( $error_code === 'no_vector_store' ) {
				return array(
					'type'    => $error_code,
					'message' => $is_admin
						? __( 'The selected collection has not been synced yet. Please sync it in the Training Data settings.', 'echo-knowledge-base' )
						: __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
				);
			}

			// Fallback for other errors
			return array(
				'type'    => $error_code,
				'message' => $is_admin ? $vector_store_result->get_error_message() : __( 'AI Search is temporarily unavailable. Please try again later.', 'echo-knowledge-base' ),
			);
		}

		return null;
	}

	/**
	 * Get schema for search parameters
	 *
	 * @return array
	 */
	protected function get_search_params() {
		return array(
			'query' => array(
				'required'          => true,
				'type'              => 'string',
				'description'       => __( 'The search query', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_string( $param ) && strlen( trim( $param ) ) >= 3;
				},
				'sanitize_callback' => 'sanitize_text_field',
			),
				'kb_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'description'       => __( 'Knowledge base ID', 'echo-knowledge-base' ),
					'validate_callback' => function( $param ) {
						return is_numeric( $param ) && $param > 0;
					},
					'sanitize_callback' => 'absint',
				),
				'collection_id' => array(
					'required'          => false,
				'type'              => 'integer',
				'description'       => __( 'AI Training Data Collection ID (optional, overrides KB default)', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return is_numeric( $param ) && $param > 0;
					},
					'sanitize_callback' => 'absint',
				),
				'collection_token' => array(
					'required'          => false,
					'type'              => 'string',
					'description'       => __( 'Access token for collection overrides', 'echo-knowledge-base' ),
					'sanitize_callback' => 'sanitize_text_field',
				),
				'limit' => array(
					'type'              => 'integer',
					'description'       => __( 'Maximum number of results', 'echo-knowledge-base' ),
				'default'           => 5,
				'minimum'           => 1,
				'maximum'           => 20,
				'sanitize_callback' => 'absint',
			),
		);
	}

	/**
	 * Get schema for table parameters (admin history endpoints)
	 *
	 * @return array
	 */
	protected function get_table_params() {
		return array(
			'per_page' => array(
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'page' => array(
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
			),
			'offset' => array(
				'type'              => 'integer',
				'minimum'           => 0,
				'sanitize_callback' => 'absint',
			),
			'search' => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'orderby' => array(
				'type'              => 'string',
				'default'           => 'created',
				'enum'              => array( 'id', 'created', 'user' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'order' => array(
				'type'              => 'string',
				'default'           => 'desc',
				'enum'              => array( 'asc', 'desc' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}
