<?php

/**
 * Conversation Model
 * 
 * Represents a complete conversation with messages, metadata, and state.
 */
class EPKB_AI_Conversation_Model {
	
	/**
	 * Conversation ID
	 * @var int
	 */
	protected $id;

	/**
	 * User ID
	 * @var int
	 */
	protected $user_id;
	
	/**
	 * Session ID
	 * @var string
	 */
	protected $session_id;
	
	/**
	 * Chat ID (unique identifier)
	 * @var string
	 */
	protected $chat_id;
	
	/**
	 * OpenAI conversation ID
	 * @var string
	 */
	protected $conversation_id;
	
	/**
	 * Row version for optimistic concurrency
	 * @var int
	 */
	public $row_version;

	/**
	 * Mode (search or chat)
	 * @var string
	 */
	protected $mode;
	
	/**
	 * Title
	 * @var string
	 */
	protected $title;
	
	/**
	 * Messages
	 * @var array
	 */
	public $messages;
	
	/**
	 * Widget ID
	 * @var string
	 */
	protected $widget_id;

	/**
	 * Idempotency key
	 * @var string
	 */
	protected $idempotency_key;

	/**
	 * Language
	 * @var string
	 */
	protected $language;
	
	/**
	 * IP address (hashed)
	 * @var string
	 */
	protected $ip;
	
	/**
	 * Metadata
	 * @var array
	 */
	protected $metadata;
	
	/**
	 * Created timestamp
	 * @var string
	 */
	protected $created;
	
	/**
	 * Updated timestamp
	 * @var string
	 */
	protected $updated;
	
	/**
	 * Constructor
	 *
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		$this->id = isset( $data['id'] ) ? absint( $data['id'] ) : 0;
		$this->user_id = isset( $data['user_id'] ) ? absint( $data['user_id'] ) : get_current_user_id();
		$this->session_id = isset( $data['session_id'] ) ? sanitize_text_field( $data['session_id'] ) : '';
		$this->chat_id = isset( $data['chat_id'] ) ? $this->validate_id( $data['chat_id'], 'chat' ) : '';
		$this->conversation_id = isset( $data['conversation_id'] ) ? $this->validate_id( $data['conversation_id'], 'conversation' ) : '';
		$this->row_version = isset( $data['row_version'] ) ? absint( $data['row_version'] ) : 1;
		$this->mode = isset( $data['mode'] ) ? $this->validate_mode( $data['mode'] ) : 'search';
		$this->title = isset( $data['title'] ) ? EPKB_AI_Validation::validate_title( $data['title'] ) : '';
		$this->messages = isset( $data['messages'] ) ? $this->parse_messages( $data['messages'] ) : array();
		$this->widget_id = isset( $data['widget_id'] ) ? EPKB_AI_Validation::validate_widget_id( $data['widget_id'] ) : '1';
		$this->idempotency_key = isset( $data['idempotency_key'] ) ? EPKB_AI_Validation::validate_idempotency_key( $data['idempotency_key'] ) : '';
		$this->language = isset( $data['language'] ) ? EPKB_AI_Validation::validate_language( $data['language'] ) : '';
		$this->ip = isset( $data['ip'] ) ? sanitize_text_field( $data['ip'] ) : '';
		$this->metadata = isset( $data['metadata'] ) ? $this->parse_metadata( $data['metadata'] ) : array();
		$this->created = isset( $data['created'] ) ? $data['created'] : gmdate( 'Y-m-d H:i:s' );
		$this->updated = isset( $data['updated'] ) ? $data['updated'] : gmdate( 'Y-m-d H:i:s' );
	}

	public function set_chat_id( $chat_id ) {
		$this->chat_id = $this->validate_id( $chat_id, 'chat' );
	}	

	public function set_session_id( $session_id ) {
		$this->session_id = $this->validate_id( $session_id, 'session' );
	}

	/**
	 * Parse messages
	 *
	 * @param mixed $messages
	 * @return array
	 */
	protected function parse_messages( $messages ) {
		if ( is_string( $messages ) ) {
			$decoded = json_decode( $messages, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				EPKB_AI_Log::add_log( 'Failed to decode messages JSON: ' . json_last_error_msg(), $messages );
				return array();
			}
			$messages = is_array( $decoded ) ? $decoded : array();
		}
		
		return is_array( $messages ) ? $messages : array();
	}
	
	/**
	 * Parse metadata
	 *
	 * @param mixed $metadata
	 * @return array
	 */
	protected function parse_metadata( $metadata ) {
		if ( is_string( $metadata ) ) {
			$decoded = json_decode( $metadata, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				EPKB_AI_Log::add_log( 'Failed to decode metadata JSON: ' . json_last_error_msg(), $metadata );
				return array();
			}
			$metadata = is_array( $decoded ) ? $decoded : array();
		}
		
		return is_array( $metadata ) ? $metadata : array();
	}
	
	/**
	 * Add message to the conversation; ensures unique message ID (generated if not provided) to avoid duplicates
	 *
	 * @param string $role
	 * @param string $content
	 * @param array $metadata
	 * @param string $message_id Optional message ID used to avoid duplicates; not in DB right now
	 */
	public function add_message( $role, $content, $metadata = array(), $message_id = '' ) {
		// Generate message ID if not provided
		if ( empty( $message_id ) ) {
			$message_id = 'msg_' . uniqid( $role . '_', true );
		}
		
		// Check for duplicate message ID
		if ( $this->has_message_id( $message_id ) ) {
			EPKB_AI_Log::add_log( 'Duplicate message ID detected: ' . $message_id );
			return false;
		}
		
		$message = array(
			'id' => $message_id,
			'role' => $role,
			'content' => $content,
			'timestamp' => gmdate( 'Y-m-d H:i:s' ),
			'metadata' => $metadata
		);
		
		$this->messages[] = $message;
		$this->updated = gmdate( 'Y-m-d H:i:s' );
		
		// Update title from first user message if empty
		if ( empty( $this->title ) && $role === 'user' ) {
			// Generate title from content without creating circular dependency
			$this->title = EPKB_AI_Content_Processor::generate_title_from_content( $content );
		}
		
		return true;
	}

	/**
	 * Validate mode
	 *
	 * @param string $mode
	 * @return string
	 */
	protected function validate_mode( $mode ) {
		$valid_modes = array( 'search', 'chat', 'advanced_search' );
		return in_array( $mode, $valid_modes ) ? $mode : 'search';
	}

	/**
	 * Validate ID format
	 *
	 * @param string $id
	 * @param string $type
	 * @return string
	 */
	protected function validate_id( $id, $type = 'generic' ) {
		$id = sanitize_text_field( $id );

		// Validate based on type
		switch ( $type ) {
			case 'chat':
				// Chat IDs should be UUID format or similar
				if ( strlen( $id ) > 64 ) {
					return substr( $id, 0, 64 );
				}
				break;

			case 'conversation':
				// OpenAI IDs have specific format
				if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $id ) || strlen( $id ) > 64 ) {
					return '';
				}
				break;
		}

		return $id;
	}


	public function has_message_id( $message_id ) {
		foreach ( $this->messages as $message ) {
			if ( isset( $message['id'] ) && $message['id'] === $message_id ) {
				return true;
			}
		}
		return false;
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function get_messages_array() {
		return $this->messages;
	}
	
	public function set_messages( $messages ) {
		$this->messages = $this->parse_messages( $messages );
		$this->updated = gmdate( 'Y-m-d H:i:s' );
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_chat_id() {
		return $this->chat_id;
	}
	
	public function get_conversation_id() {
		return $this->conversation_id;
	}
	
	public function set_conversation_id( $conversation_id ) {
		$this->conversation_id = $conversation_id;
		// Update the timestamp when setting new conversation ID
		$this->updated = gmdate( 'Y-m-d H:i:s' );
	}

	/**
	 * Check if conversation expired
	 *
	 * @return bool
	 */
	public function is_conversation_expired() {
		if ( empty( $this->conversation_id ) ) {
			return true;
		}
		// Check if conversation is older than x days based on last update
		return ( time() - strtotime( $this->updated ) ) > ( EPKB_OpenAI_Client::DEFAULT_CONVERSATION_EXPIRY_DAYS * DAY_IN_SECONDS );
	}

	public function get_mode() {
		return $this->mode;
	}
	
	public function is_search() {
		return $this->mode === 'search';
	}
	
	public function is_chat() {
		return $this->mode === 'chat';
	}

	/**
	 * Convert to array for database
	 *
	 * @return array|WP_Error
	 */
	public function to_db_array() {
		$messages_json = wp_json_encode( $this->get_messages_array() );
		if ( $messages_json === false ) {
			return new WP_Error( 'json_encode_error', 'Failed to encode messages: ' . json_last_error_msg() );
		}
		
		$metadata_json = wp_json_encode( $this->metadata );
		if ( $metadata_json === false ) {
			return new WP_Error( 'json_encode_error', 'Failed to encode metadata: ' . json_last_error_msg() );
		}
		
		return array(
			'user_id'         => $this->user_id,
			'session_id'      => $this->session_id,
			'chat_id'         => $this->chat_id,
			'conversation_id' => $this->conversation_id,
			'mode'            => $this->mode,
			'title'           => $this->title,
			'messages'        => $messages_json,
			'widget_id'       => $this->widget_id,
			'language'        => $this->language,
			'ip'              => $this->ip,
			'metadata'        => $metadata_json,
			'updated'         => $this->updated
		);
	}
	
	/**
	 * Create from database row
	 *
	 * @param object $row
	 * @return self
	 */
	public static function from_db_row( $row ) {
		$data = (array) $row;
		return new self( $data );
	}
	

	public function get_created() {
		return $this->created;
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function get_title() {
		return $this->title;
	}
	
	public function get_widget_id() {
		return $this->widget_id;
	}
	
	public function set_widget_id( $widget_id ) {
		$this->widget_id = EPKB_AI_Validation::validate_widget_id( $widget_id );
	}
	
	public function get_idempotency_key() {
		return $this->idempotency_key;
	}

	public function set_idempotency_key( $idempotency_key ) {
		$this->idempotency_key = EPKB_AI_Validation::validate_idempotency_key( $idempotency_key );
	}

	public function get_session_id() {
		return $this->session_id;
	}
	
	public function get_row_version() {
		return $this->row_version;
	}

	public function get_updated() {
		return $this->updated;
	}

	public function get_metadata() {
		return $this->metadata;
	}

	public function set_metadata( $metadata ) {
		$this->metadata = $metadata;
	}
}