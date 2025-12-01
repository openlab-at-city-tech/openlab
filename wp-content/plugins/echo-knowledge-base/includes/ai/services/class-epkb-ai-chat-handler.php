<?php
/**
 * AI Message Handler
 * 
 * Handles the two-phase message processing:
 * 1. Call AI API first
 * 2. Save both user and assistant messages together after successful AI response
 * 
 * Includes idempotency, error handling, and recovery mechanisms
 */
class EPKB_AI_Chat_Handler extends EPKB_AI_Base_Handler {
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Process a chat message with two-phase approach
	 *
	 * @param string $user_message User's message
	 * @param EPKB_AI_Conversation_Model $conversation_obj Conversation object
	 * @param int $collection_id Collection ID to use for the chat
	 * @return array|WP_Error Result array or error
	 */
	public function process_message( $user_message, $conversation_obj, $collection_id ) {

		// Call AI API to get response for user message
		$model = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_model' );
		$ai_response = $this->get_ai_response( $user_message, $model, $conversation_obj->get_conversation_id(), $collection_id );
		if ( is_wp_error( $ai_response ) ) {
			return $ai_response;
		}
		
		// Save both messages together after successful AI response
		$save_result = $this->save_messages( $conversation_obj, $user_message, $ai_response);
		if ( is_wp_error( $save_result ) ) {
			return $save_result;
		}
		
		// Return successful response
		return array(
			'success' => true,
			'response' => $ai_response['content'],
			'message_id' => 'msg_assistant_' . time() . '_' . uniqid(),
			'chat_id' => $conversation_obj->get_chat_id(),
			'is_duplicate' => false
		);
	}

	/**
	 * Save messages to database
	 *
	 * @param EPKB_AI_Conversation_Model $conversation_obj
	 * @param string $user_message
	 * @param array $ai_response
	 * @return true|WP_Error
	 */
	private function save_messages( $conversation_obj, $user_message, $ai_response ) {
		
		// Prepare messages array
		$messages = array();

		// Add existing messages if updating
		if ( ! empty( $conversation_obj->get_chat_id() ) ) {
			$messages = $conversation_obj->get_messages();
		}
		
		// Add user message
		$timestamp = gmdate( 'Y-m-d H:i:s' );
		$messages[] = array(
			'role' => 'user',
			'content' => $user_message,
			'timestamp' => $timestamp
		);

		// Add AI response message
		$messages[] = array(
			'role' => 'assistant',
			'content' => $ai_response['content'],
			'timestamp' => $timestamp,
			//'usage' => $ai_response['usage']
		);
		
		// a) Save new conversation
		if ( empty( $conversation_obj->get_chat_id() ) ) {

			// Generate chat_id for new conversation
			$new_chat_id = EPKB_AI_Security::CHAT_ID_PREFIX . EPKB_AI_Utilities::generate_uuid_v4();

			// Prepare data for save
			$language = EPKB_Language_Utilities::detect_current_language();
			$data = array(
				'session_id' => $conversation_obj->get_session_id(),
				'chat_id' => $new_chat_id,
				'title' => EPKB_AI_Content_Processor::generate_title_from_content( $user_message ),
				'messages' => $messages,
				'conversation_id' => $ai_response['response_id'],
				'widget_id' => $conversation_obj->get_widget_id(),
				'mode' => 'chat',
				'last_idemp_key' => $conversation_obj->get_idempotency_key(),
				'user_id' => get_current_user_id(),
				'ip' => EPKB_AI_Utilities::get_hashed_ip(),
				'language' => $language['locale']
			);

			$result = $this->messages_db->insert_chat_with_messages( $data );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			// CRITICAL: Set the chat_id on the conversation object so it's returned to the frontend
			// Without this, the frontend doesn't know which conversation to continue
			$conversation_obj->set_chat_id( $new_chat_id );

			return true;
		}

		// b) Update existing conversation with version check
		$conversation_obj->set_messages( $messages );
		$conversation_obj->set_conversation_id( $ai_response['response_id'] );

		$result = $this->messages_db->update_chat_with_version_check( $conversation_obj );
		if ( is_wp_error( $result ) && $result->get_error_code() === 'version_conflict' ) {

			// Reload and retry once
			$fresh_conversation = $this->messages_db->get_conversation_by_chat_id( $conversation_obj->get_chat_id() );
			if ( $fresh_conversation ) {
				// Check if our message was already added
				$array = $fresh_conversation->get_messages();
				$last_message = end( $array );
				if ( $last_message && $last_message['content'] === $ai_response['content'] ) {
					return true; // Already saved by another request
				}

				// Try again with fresh version
				$result = $this->messages_db->update_chat_with_version_check( $conversation_obj );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
			} else {
				// Failed to reload conversation
				return new WP_Error( 'conversation_reload_failed', __( 'Failed to reload conversation', 'echo-knowledge-base' ) );
			}
		}
		
		return true;
	}
}