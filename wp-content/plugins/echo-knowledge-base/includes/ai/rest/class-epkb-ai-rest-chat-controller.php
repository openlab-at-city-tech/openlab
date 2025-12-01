<?php defined( 'ABSPATH' ) || exit();

/**
 * REST API Controller for AI Chat functionality
 * 
 * Provides secure REST endpoints for chat operations
 */
class EPKB_AI_REST_Chat_Controller extends EPKB_AI_REST_Base_Controller {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Register the routes for the AI Chat
	 */
	public function register_routes() {

		if ( ! EPKB_AI_Utilities::is_ai_chat_enabled() ) {
			return;
		}

		// Start session endpoint - creates httpOnly session cookie
		register_rest_route( $this->public_namespace, '/ai-chat/start-session', array(
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'start_session' ),
				'permission_callback' => '__return_true' // Public endpoint for guest users
			),
		) );

		// Get conversation (with optional chat_id)
		// If chat_id is provided, retrieves specific conversation
		// If chat_id is null/empty, retrieves latest active conversation
		register_rest_route( $this->public_namespace, '/ai-chat/conversation', array(
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_conversation'),
				'permission_callback' => [ $this, 'check_rest_nonce' ],
			),
		) );

		// Session/No session -> user submits message
		register_rest_route( $this->public_namespace, '/ai-chat/send-message', array(
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'send_message' ),
				'permission_callback' => [ $this, 'check_rest_nonce' ]
			),
		) );

		// register admin routes only if in admin context
		$request_uri = $_SERVER['REQUEST_URI'] ?? '';
		if ( strpos( $request_uri, 'epkb-admin' ) === false ) {
			return;
		}

		// Admin endpoints for conversation management
		register_rest_route( $this->admin_namespace, '/ai-chat/conversations', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_conversations_table' ),
				'permission_callback' => array( 'EPKB_AI_Security', 'can_access_settings' ),
				'args'                => $this->get_table_params(),
			),
		) );

		// Admin bulk delete conversations
		register_rest_route( $this->admin_namespace, '/ai-chat/conversations/bulk', array(
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

	/**
	 * Start a new session (creates httpOnly session cookie)
	 * 
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function start_session( $request ) {
		try {
			// Get or create session (this sets the httpOnly cookie)
			$session_id = EPKB_AI_Security::get_or_create_session();
			if ( is_wp_error( $session_id ) ) {
				return $this->create_rest_response( array(), 500, $session_id );
			}
			
			// Generate fresh nonce for the session
			$security = new EPKB_AI_Security();
			$rest_nonce = $security->get_nonce();
			
			return $this->create_rest_response( array( 'success' => true, 'rest_nonce' => $rest_nonce ) );
			
		} catch ( Exception $e ) {
			return $this->create_rest_response( [], 500, new WP_Error( 'session_error', $e->getMessage() ) );
		}
	}

	/**
	 * Handle sending a chat message with or without an active session
	 * 
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function send_message( $request ) {

		try {
			$result = $this->retrieve_conversation( $request, true );
			if ( ! empty( $result['wp_error'] ) && is_wp_error( $result['wp_error'] ) ) {
				$data = isset( $result['data'] ) ? $result['data'] : array();
				return $this->create_rest_response( $data, $result['status'], $result['wp_error'] );
			}

			$conversation_obj = isset( $result['data']['conversation'] ) ? $result['data']['conversation'] : new EPKB_AI_Conversation_Model();
			$conversation_obj->set_session_id( $result['request_data']['session_id'] );
			$conversation_obj->set_widget_id( $result['request_data']['widget_id'] );
			$conversation_obj->set_idempotency_key( $result['request_data']['idempotency_key'] );

			// Process the message using the handler; new chat conversation is created if chat_id is empty
			$this->message_handler = new EPKB_AI_Chat_Handler();
			$result = $this->message_handler->process_message( $result['request_data']['message'], $conversation_obj, $result['request_data']['collection_id'] );
			if ( is_wp_error( $result ) ) {
				return $this->create_rest_response( array(), 400, $result );
			}
			
			// Return successful response
			return $this->create_rest_response( $result );
			
		} catch ( Exception $e ) {  // Catch any unexpected exceptions during frontend request processing
			return $this->create_rest_response( [], 500, new WP_Error( 'unexpected_error', $e->getMessage() ) );
		}
	}

	/**
	 * UNIFIED ENDPOINT: Get conversation with optional chat_id
	 * If chat_id is provided, retrieves specific conversation
	 * If chat_id is null/empty, retrieves latest active conversation
	 * 
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_conversation( $request ) {

		$result = $this->retrieve_conversation( $request, false );

		$data = isset( $result['data'] ) ? $result['data'] : array();
		$status = isset( $result['status'] ) ? $result['status'] : 500;

		if ( ! empty( $result['wp_error'] ) && is_wp_error( $result['wp_error'] ) ) {
			return $this->create_rest_response( $data, $status, $result['wp_error'] );
		}

		// Format conversation data for response
		if ( isset( $data['conversation'] ) && $data['conversation'] instanceof EPKB_AI_Conversation_Model ) {
			$conversation = $data['conversation'];
			$messages = $conversation->get_messages();	
			$data = array(
				'chat_id' => $conversation->get_chat_id(),
				'messages' => $this->format_messages_for_response( $messages )
			);
		}
		
		return $this->create_rest_response( $data, $status );
	}

	/**
	 * Retrieve conversation from database if it exists or new conversation is not forced
	 * @param mixed $request 
	 * @param mixed $is_validate_message 
	 * @return array{status: int, wp_error: null|bool|\WP_Error|string|array, data: array{conversation: \EPKB_AI_Conversation_Model}, request_data: array|\WP_Error}
	 */
	private function retrieve_conversation( $request, $is_validate_message ) {	// TODO try()

		$request_data = $this->extract_request_data( $request, $is_validate_message );
		if ( is_wp_error( $request_data ) ) {
			return [ 'status' => 400, 'wp_error' => $request_data ];
		}

		$chat_id = $request_data['chat_id'];

		// Get and validate session
		$session_id = EPKB_AI_Security::get_or_create_session();
		if ( is_wp_error( $session_id ) ) {
			return [ 'status' => 503, 'wp_error' => $session_id ];
		}
		
		$request_data['session_id'] = $session_id;

		// If force_new_conversation is true, create a new conversation
		if ( ! empty( $request_data['force_new_conversation'] ) ) {
			$request_data['chat_id'] = '';
			return [ 'status' => 200, 'wp_error' => null, 'data' => [], 'request_data' => $request_data ];
		}

		// Special handling for cron/wp-cli jobs
		if ( $session_id === 'wp-cron' || $session_id === 'wp-cli' ) {
			return [ 'status' => 200, 'wp_error' => null, 'data' => [], 'request_data' => $request_data ];
		}
		
		$messages_db = new EPKB_AI_Messages_DB();
		
		// a) we have only Session
		if ( empty( $chat_id ) ) {
			// Get active conversation for the session
			$conversation = $messages_db->get_latest_active_chat_for_session( $session_id );
			if ( ! $conversation ) {
				// No RECENT active conversation found so create a new one
				return [ 'status' => 200, 'wp_error' => null, 'data' => [], 'request_data' => $request_data ];
			}

			// Check for data corruption - chat_id should never be empty for saved conversations
			if ( ! $conversation->get_chat_id() ) {
				EPKB_AI_Log::add_log( 'Conversation found with empty chat_id', array('conversation_id' => $conversation->get_id(), 'session_id' => $session_id) );
				// Treat as no active conversation - force creation of new one
				return [ 'status' => 200, 'wp_error' => null, 'data' => [], 'request_data' => $request_data ];
			}
			
		// b) we have both Session and chat ID
		} else {
			// Validate chat belongs to session
			if ( ! EPKB_AI_Security::validate_chat_session( $chat_id, $session_id ) ) {
				return [ 'status' => 403, 'wp_error' => new WP_Error( 'unauthorized', __( 'Unauthorized access to conversation.', 'echo-knowledge-base' ) ) ];
			}
			
			// Get conversation from database
			$conversation = $messages_db->get_conversation_by_chat_and_session( $chat_id, $session_id );
			if ( ! $conversation ) {
				return [ 'status' => 200, 'wp_error' => null, 'data' => [], 'request_data' => $request_data ];
			}
		}

		// Additional user matching validation
		$user_validation = EPKB_AI_Security::validate_user_matching( $conversation->get_chat_id() );
		if ( is_wp_error( $user_validation ) ) {
			return [ 'status' => 403, 'wp_error' => $user_validation ];
		}
		
		return [ 'status' => 200, 'wp_error' => null, 'data' => [ 'conversation' => $conversation ], 'request_data' => $request_data ];
	}

	// access by WP_REST_Server
	public function check_rest_nonce( $request ) {

		if ( ! EPKB_AI_Utilities::is_ai_chat_enabled() ) {
			return new WP_Error( 'ai_chat_disabled', __( 'AI chat is not enabled', 'echo-knowledge-base' ), array( 'status' => 403 ) );
		}

		// Check rate limit before processing
		/** @disregard P1011 */
		if ( ! is_admin() && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {	
			/* $result = EPKB_AI_Security::check_rate_limit();   // TODO
			if ( is_wp_error( $result ) ) {
				return new WP_Error( 'rate_limit' ); //$this->create_rest_response( array(), 429, $result );
			} */
		}

		return EPKB_AI_Security::check_rest_nonce( $request );
	}

	/*******************************************************************
	 * Admin Conversation UI
	 *******************************************************************/

	/**
	 * Get conversations table data for admin
	 * 
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_conversations_table( $request ) {
		
		// Get parameters
		$per_page = absint( $request->get_param( 'per_page' ) ?: 10 );
		$page = absint( $request->get_param( 'page' ) ?: 1 );
		$search = sanitize_text_field( $request->get_param( 'search' ) ?: '' );
		$orderby = sanitize_text_field( $request->get_param( 'orderby' ) ?: 'created' );
		$order = strtoupper( sanitize_text_field( $request->get_param( 'order' ) ?: 'DESC' ) );

		// Validate order
		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
			$order = 'DESC';
		}

		// Get data using existing table operations
		$result = EPKB_AI_Table_Operations::get_table_data( 'chat', array(
			'per_page' => $per_page,
			'page'     => $page,
			's'        => $search,
			'orderby'  => $orderby,
			'order'    => $order
		) );

		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array(), 500, $result );
		}

		// Transform items to include checkbox-ready data and formatted columns
		if ( isset( $result['items'] ) && is_array( $result['items'] ) ) {
			$transformed_items = array();
			$messages_db = new EPKB_AI_Messages_DB();
			
			foreach ( $result['items'] as $item ) {
				// Get first message for conversation column
				$first_message = isset( $item['question'] ) ? $item['question'] : '';
				
				// Get full conversation details
				$conversation = $messages_db->get_conversation( $item['id'] );
				if ( is_wp_error( $conversation ) ) {
					$conversation = null;
				}

				$messages = array();
				
				if ( $conversation ) {
					$raw_messages = $conversation->get_messages();
					foreach ( $raw_messages as $message ) {
						$messages[] = array(
							'role'      => $message['role'],
							'content'   => $message['content'],
							'timestamp' => isset( $message['timestamp'] ) ? $message['timestamp'] : ''
						);
					}
				}
				
				// Get user ID if available
				$user_id = 0;
				if ( $conversation ) {
					$user_id = $conversation->get_user_id();
				}
				
				$transformed_items[] = array(
					'id' => $item['id'],
					'checkbox' => true, // Enable checkbox for this row
					'time' => $item['submit_date'],
					'user' => $item['name'],
					'user_id' => $user_id,
					'conversation' => $first_message,
					'message_count' => isset( $item['message_count'] ) ? $item['message_count'] : 0,
					'status' => isset( $item['status'] ) ? $item['status'] : 'answered',
					'rating' => isset( $item['rating'] ) ? $item['rating'] : 0,
					'messages' => $messages // Include full conversation messages
				);
			}
			$result['conversations'] = $transformed_items;
			// Remove 'items' key to avoid confusion
			unset($result['items']);
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

		$result = EPKB_AI_Table_Operations::delete_selected_rows( 'chat', $ids );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( array(), 500, $result );
		}

		return $this->create_rest_response( array( 'message' => sprintf( __( '%d conversations deleted successfully.', 'echo-knowledge-base' ), $result ), 'deleted' => $result ) );
	}


	/**
	 * Extract and validate request data
	 * 
	 * @param WP_REST_Request $request
	 * @return array|WP_Error
	 */
	private function extract_request_data( $request, $is_validate_message = false ) {
		$params = $request->get_json_params();
		
		// Get and validate message
		$message = isset( $params['message'] ) ? $params['message'] : '';
		if ( $is_validate_message ) {
			$message = $this->optimize_message( $message );
			$message = EPKB_AI_Validation::validate_message( $message );
			if ( is_wp_error( $message ) ) {
				return $message;
			}
		}
		
		// Get optional parameters; keep empty if not provided (handled in Message_Handler)
		$chat_id = isset( $params['chat_id'] ) ? sanitize_text_field( $params['chat_id'] ) : '';
		if ( !empty( $chat_id ) && ! EPKB_AI_Validation::validate_uuid( str_replace( EPKB_AI_Security::CHAT_ID_PREFIX, '', $chat_id ) ) ) {
			$chat_id = ''; // Reset if invalid
		}

		// Get idempotency key
		$idempotency_key = isset( $params['idempotency_key'] ) ? sanitize_text_field( $params['idempotency_key'] ) : '';
		$idempotency_key = EPKB_AI_Validation::validate_idempotency_key( $idempotency_key );
		if ( is_wp_error( $idempotency_key ) ) {
			$idempotency_key = '';
		}

		// Get widget ID
		$widget_id = isset( $params['widget_id'] ) ? absint( $params['widget_id'] ) : 1;
		$widget_id = EPKB_AI_Validation::validate_widget_id( $widget_id );
		if ( is_wp_error( $widget_id ) ) {
			$widget_id = 1;
		}

		// Get collection ID
		$collection_id = isset( $params['collection_id'] ) ? absint( $params['collection_id'] ) : EPKB_AI_Training_Data_Config_Specs::DEFAULT_COLLECTION_ID;

		return array(
			'message'         => $message,
			'chat_id'         => $chat_id,
			'idempotency_key' => $idempotency_key,
			'widget_id'       => $widget_id,
			'collection_id'   => $collection_id,
			'user_id'         => get_current_user_id(),
			'force_new_conversation' => ! empty( $params['force_new_conversation'] )
		);
	}
	
	/**
	 * Format messages for API response
	 * 
	 * @param array $messages
	 * @return array
	 */
	private function format_messages_for_response( $messages ) {
		$formatted = array();
		
		foreach ( $messages as $message ) {
			$formatted[] = array(
				'role'      => $message['role'],
				'content'   => EPKB_AI_Security::sanitize_output( $message['content'] ),
				'timestamp' => isset( $message['timestamp'] ) ? $message['timestamp'] : ''
			);
		}
		
		return $formatted;
	}

	/**
	 * Get schema for table parameters
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
			'search' => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'orderby' => array(
				'type'              => 'string',
				'default'           => 'created',
				'enum'              => array( 'id', 'created', 'user', 'messages' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'order' => array(
				'type'              => 'string',
				'default'           => 'DESC',
				'enum'              => array( 'ASC', 'DESC' ),
				'sanitize_callback' => function( $param ) {
					return strtoupper( sanitize_text_field( $param ) );
				},
			),
		);
	}

	/**
	 * Optimize message for transmission (same as AJAX handler)
	 *
	 * @param string $message
	 * @return string
	 */
	private function optimize_message( $message ) {
		// Remove excessive whitespace
		$message = preg_replace( '/\s+/', ' ', $message );

		// Trim message
		$message = trim( $message );

		// Remove zero-width characters
		$message = preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $message );

		return $message;
	}
}