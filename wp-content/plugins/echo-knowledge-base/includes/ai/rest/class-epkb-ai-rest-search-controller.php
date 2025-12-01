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
				'args'                => $this->get_collection_params(),
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

		// Check rate limit before processing
		/* $rate_limit_check = EPKB_AI_Security::check_rate_limit();
		if ( is_wp_error( $rate_limit_check ) ) {
			return 'TODO";
		} */

		// Get search query
		$query = $request->get_param( 'query' );
		if ( empty( $query ) || strlen( $query ) < 3 ) {
			return $this->create_rest_response( array( 'message' => __( 'Search query must be at least 3 characters long.', 'echo-knowledge-base' ) ), 400 );
		}

		$collection_id = $request->get_param( 'collection_id' );
		$collection_id = empty( $collection_id ) ? EPKB_AI_Training_Data_Config_Specs::DEFAULT_COLLECTION_ID : $collection_id;

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
		$params = array(
			'per_page' => $request->get_param( 'per_page' ),
			'page'     => $request->get_param( 'page' ),
			's'        => $request->get_param( 'search' ),
			'orderby'  => $request->get_param( 'orderby' ),
			'order'    => strtoupper( $request->get_param( 'order' ) )
		);

		// Get data using existing table operations
		$result = EPKB_AI_Table_Operations::get_table_data( 'search', $params );
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

		return $this->create_rest_response( array( 'message' => sprintf( __( '%d search conversations deleted successfully.', 'echo-knowledge-base' ), $result ), 'deleted' => $result ) );
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
				'required'          => false,
				'type'              => 'integer',
				'description'       => __( 'Knowledge base ID', 'echo-knowledge-base' ),
				'validate_callback' => function( $param ) {
					return empty( $param ) || ( is_numeric( $param ) && $param > 0 );
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
}