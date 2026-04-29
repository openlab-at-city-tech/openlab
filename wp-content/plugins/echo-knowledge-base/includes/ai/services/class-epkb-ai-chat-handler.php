<?php
/**
 * AI Message Handler
 *
 * Three-phase message processing so every user question is recorded even when the AI call fails:
 *   1) Save the user message immediately (status = pending).
 *   2) Call the AI provider.
 *   3) Append the assistant response (status = answered) or an error message (status = failed).
 */
class EPKB_AI_Chat_Handler extends EPKB_AI_Base_Handler {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Use chat-specific collection error messages.
	 *
	 * @return string
	 */
	protected function get_collection_validation_context() {
		return 'chat';
	}

	/**
	 * Process a chat message
	 *
	 * @param string $user_message User's message
	 * @param EPKB_AI_Conversation_Model $conversation_obj Conversation object
	 * @param int $collection_id Collection ID to use for the chat
	 * @return array|WP_Error Result array or error
	 */
	public function process_message( $user_message, $conversation_obj, $collection_id ) {

		// Idempotent retry: the JS client reuses the idempotency key on timeouts/5xx. Replay the stored answer or skip re-appending the user turn.
		$idempotent_retry = $this->handle_idempotent_retry( $conversation_obj );
		if ( isset( $idempotent_retry['return'] ) ) {
			return $idempotent_retry['return'];
		}

		// Phase 1: persist the user question so the conversation is recorded even if the AI call fails
		if ( empty( $idempotent_retry['skip_save'] ) ) {
			$save_result = $this->save_user_message( $conversation_obj, $user_message );
			if ( is_wp_error( $save_result ) ) {
				return $save_result;
			}
		}

		// Phase 2: call the AI provider. Gemini replays the full history and appends $user_message itself, so strip the current user turn (and any trailing error stubs from a retried failed attempt) before passing history along.
		$model = EPKB_AI_Provider::get_chat_model();
		$conversation_history = $this->build_history_excluding_current_turn( $conversation_obj->get_messages(), $user_message );
		$ai_response = $this->get_ai_response( $user_message, $model, $conversation_obj->get_conversation_id(), $collection_id, $conversation_history );

		// Phase 3a: AI failed - record the error in the conversation and bubble the WP_Error up to the REST layer
		if ( is_wp_error( $ai_response ) ) {
			$this->append_error_message( $conversation_obj, $ai_response );
			return $ai_response;
		}

		// Phase 3b: AI succeeded - append the assistant response
		$assistant_message_id = 'msg_assistant_' . time() . '_' . uniqid();
		$save_result = $this->append_assistant_message( $conversation_obj, $ai_response, $assistant_message_id );
		if ( is_wp_error( $save_result ) ) {
			return $save_result;
		}

		// Check if source references should be included
		$show_sources = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_show_sources' ) === 'on';
		$sources = $show_sources && EPKB_AI_Utilities::should_show_sources_for_answer( $ai_response['content'] ) && ! empty( $ai_response['sources'] ) ? $ai_response['sources'] : array();

		return array(
			'success'      => true,
			'response'     => $ai_response['content'],
			'message_id'   => $assistant_message_id,
			'chat_id'      => $conversation_obj->get_chat_id(),
			'is_duplicate' => false,
			'sources'      => $sources
		);
	}

	/**
	 * Persist the user message. Creates the conversation record for a brand-new chat, appends otherwise.
	 *
	 * @param EPKB_AI_Conversation_Model $conversation_obj
	 * @param string $user_message
	 * @return true|WP_Error
	 */
	private function save_user_message( $conversation_obj, $user_message ) {

		$user_message_entry = array(
			'id'        => 'msg_user_' . time() . '_' . uniqid(),
			'role'      => 'user',
			'content'   => $user_message,
			'timestamp' => gmdate( 'Y-m-d H:i:s' )
		);

		$metadata = $conversation_obj->get_metadata();
		$metadata['status'] = 'pending';
		$conversation_obj->set_metadata( $metadata );

		// a) Brand new conversation - insert row
		if ( empty( $conversation_obj->get_chat_id() ) ) {

			$new_chat_id = EPKB_AI_Security::CHAT_ID_PREFIX . EPKB_AI_Utilities::generate_uuid_v4();
			$language = EPKB_Language_Utilities::detect_current_language();
			$title = EPKB_AI_Content_Processor::generate_title_from_content( $user_message );
			$ip = EPKB_AI_Utilities::get_hashed_ip();
			$data = array(
				'session_id'      => $conversation_obj->get_session_id(),
				'chat_id'         => $new_chat_id,
				'title'           => $title,
				'messages'        => array( $user_message_entry ),
				'conversation_id' => '',
				'widget_id'       => $conversation_obj->get_widget_id(),
				'mode'            => 'chat',
				'last_idemp_key'  => $conversation_obj->get_idempotency_key(),
				'user_id'         => get_current_user_id(),
				'ip'              => $ip,
				'language'        => $language['locale'],
				'metadata'        => $metadata
			);

			$insert_id = $this->messages_db->insert_chat_with_messages( $data );
			if ( is_wp_error( $insert_id ) ) {
				return $insert_id;
			}

			// Sync conversation_obj with the freshly inserted row so the next update_chat_with_version_check call lines up
			$conversation_obj->set_chat_id( $new_chat_id );
			$conversation_obj->set_id( $insert_id );
			$conversation_obj->set_mode( 'chat' );
			$conversation_obj->set_title( $title );
			$conversation_obj->set_language( $language['locale'] );
			$conversation_obj->set_ip( $ip );
			$conversation_obj->row_version = 1;
			$conversation_obj->set_messages( array( $user_message_entry ) );
			return true;
		}

		// b) Existing conversation - append the user message
		$messages = $conversation_obj->get_messages();
		$messages[] = $user_message_entry;
		$conversation_obj->set_messages( $messages );

		return $this->update_with_retry( $conversation_obj );
	}

	/**
	 * Append the assistant response to an existing conversation and mark the conversation answered.
	 *
	 * @param EPKB_AI_Conversation_Model $conversation_obj
	 * @param array $ai_response
	 * @param string $assistant_message_id
	 * @return true|WP_Error
	 */
	private function append_assistant_message( $conversation_obj, $ai_response, $assistant_message_id ) {

		$assistant_metadata = array();
		if ( ! empty( $ai_response['thought_signature'] ) ) {
			$assistant_metadata['thought_signature'] = $ai_response['thought_signature'];
		}
		if ( ! empty( $ai_response['response_parts'] ) && is_array( $ai_response['response_parts'] ) ) {
			$assistant_metadata['gemini_parts'] = $ai_response['response_parts'];
		}
		// Preserve sources so an idempotent retry can replay them with the stored answer
		if ( ! empty( $ai_response['sources'] ) && is_array( $ai_response['sources'] ) ) {
			$assistant_metadata['sources'] = $ai_response['sources'];
		}

		$message = array(
			'id'        => $assistant_message_id,
			'role'      => 'assistant',
			'content'   => $ai_response['content'],
			'timestamp' => gmdate( 'Y-m-d H:i:s' ),
			'metadata'  => $assistant_metadata
		);

		$messages = $conversation_obj->get_messages();
		$messages[] = $message;
		$conversation_obj->set_messages( $messages );
		$conversation_obj->set_conversation_id( $ai_response['response_id'] );

		$metadata = $conversation_obj->get_metadata();
		$metadata['status'] = 'answered';
		$conversation_obj->set_metadata( $metadata );

		return $this->update_with_retry( $conversation_obj );
	}

	/**
	 * Append a synthetic assistant message describing the error, and mark the conversation failed.
	 *
	 * @param EPKB_AI_Conversation_Model $conversation_obj
	 * @param WP_Error $wp_error
	 * @return true|WP_Error
	 */
	private function append_error_message( $conversation_obj, $wp_error ) {

		$error_data = $wp_error->get_error_data();
		$error_content = __( 'The AI could not generate a response.', 'echo-knowledge-base' );

		$message = array(
			'id'        => 'msg_error_' . time() . '_' . uniqid(),
			'role'      => 'assistant',
			'content'   => $error_content,
			'timestamp' => gmdate( 'Y-m-d H:i:s' ),
			'metadata'  => array(
				'error' => array(
					'code'    => $wp_error->get_error_code(),
					'message' => $wp_error->get_error_message(),
					'data'    => is_scalar( $error_data ) || is_array( $error_data ) ? $error_data : null
				)
			)
		);

		$messages = $conversation_obj->get_messages();
		$messages[] = $message;
		$conversation_obj->set_messages( $messages );

		$metadata = $conversation_obj->get_metadata();
		$metadata['status'] = 'failed';
		$conversation_obj->set_metadata( $metadata );

		return $this->update_with_retry( $conversation_obj );
	}

	/**
	 * Run update_chat_with_version_check with a single retry on optimistic-concurrency conflict.
	 * On conflict, rebases our freshly appended message onto the fresh DB transcript so a concurrent writer's messages are preserved.
	 *
	 * @param EPKB_AI_Conversation_Model $conversation_obj
	 * @return true|WP_Error
	 */
	private function update_with_retry( $conversation_obj ) {

		$expected_version = $conversation_obj->get_row_version();
		$result = $this->messages_db->update_chat_with_version_check( $conversation_obj );

		if ( is_wp_error( $result ) && $result->get_error_code() === 'version_conflict' ) {
			$fresh = $this->messages_db->get_conversation_by_chat_id( $conversation_obj->get_chat_id() );
			if ( ! $fresh ) {
				return new WP_Error( 'conversation_reload_failed', __( 'Failed to reload conversation', 'echo-knowledge-base' ) );
			}

			$our_messages = $conversation_obj->get_messages();
			$new_message = end( $our_messages );
			$fresh_messages = $fresh->get_messages();
			$last_fresh = end( $fresh_messages );

			// Another retry of the same request already saved this message - no-op
			if ( $new_message && $last_fresh && isset( $new_message['id'], $last_fresh['id'] ) && $new_message['id'] === $last_fresh['id'] ) {
				$conversation_obj->row_version = $fresh->get_row_version();
				return true;
			}

			// A concurrent in-flight request with the same idempotency key may have already written this turn. Ids differ between overlapping attempts, so match by role on the stored answer.
			$our_idemp = $conversation_obj->get_idempotency_key();
			if ( ! empty( $our_idemp ) && $new_message ) {
				$idemp_row = $this->messages_db->check_idempotent_request( $conversation_obj->get_chat_id(), $our_idemp );
				if ( ! empty( $idemp_row ) && ! is_wp_error( $idemp_row ) ) {
					$other = EPKB_AI_Conversation_Model::from_db_row( $idemp_row );
					$other_msgs = $other->get_messages();
					$last_other = end( $other_msgs );
					if ( $last_other && isset( $last_other['role'], $new_message['role'] ) && $last_other['role'] === $new_message['role'] ) {
						$conversation_obj->set_messages( $other_msgs );
						$conversation_obj->row_version = $other->get_row_version();
						return true;
					}
				}
			}

			// Rebase our new message onto the fresh transcript to avoid clobbering concurrent writes
			if ( $new_message ) {
				$fresh_messages[] = $new_message;
			}
			$conversation_obj->set_messages( $fresh_messages );
			$conversation_obj->row_version = $fresh->get_row_version();
			$expected_version = $conversation_obj->get_row_version();
			$result = $this->messages_db->update_chat_with_version_check( $conversation_obj );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$conversation_obj->row_version = $expected_version + 1;
		return true;
	}

	/**
	 * Detect an idempotent retry of a previous send-message call.
	 *
	 * @param EPKB_AI_Conversation_Model $conversation_obj
	 * @return array Either empty (not a retry), ['return' => <response array>] (replay stored answer), or ['skip_save' => true] (skip re-appending the user turn).
	 */
	private function handle_idempotent_retry( $conversation_obj ) {

		$chat_id = $conversation_obj->get_chat_id();
		$idempotency_key = $conversation_obj->get_idempotency_key();
		if ( empty( $chat_id ) || empty( $idempotency_key ) ) {
			return array();
		}

		$row = $this->messages_db->check_idempotent_request( $chat_id, $idempotency_key );
		if ( empty( $row ) || is_wp_error( $row ) ) {
			return array();
		}

		// Reload state so subsequent saves see the prior attempt's messages and row_version
		$existing = EPKB_AI_Conversation_Model::from_db_row( $row );
		$messages = $existing->get_messages();
		$conversation_obj->set_messages( $messages );
		$conversation_obj->row_version = $existing->get_row_version();

		$last = end( $messages );
		if ( $last && isset( $last['role'] ) && $last['role'] === 'assistant' && empty( $last['metadata']['error'] ) ) {
			$stored_sources = ! empty( $last['metadata']['sources'] ) && is_array( $last['metadata']['sources'] ) ? $last['metadata']['sources'] : array();
			$show_sources = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_show_sources' ) === 'on';
			$sources = $show_sources && EPKB_AI_Utilities::should_show_sources_for_answer( $last['content'] ) && ! empty( $stored_sources ) ? $stored_sources : array();
			return array( 'return' => array(
				'success'      => true,
				'response'     => $last['content'],
				'message_id'   => isset( $last['id'] ) ? $last['id'] : '',
				'chat_id'      => $existing->get_chat_id(),
				'is_duplicate' => true,
				'sources'      => $sources
			) );
		}

		// Prior attempt persisted the user turn (and possibly an error stub). Skip re-appending so the transcript is not duplicated.
		return array( 'skip_save' => true );
	}

	/**
	 * Strip the current user turn (and any trailing messages that followed it) from the transcript
	 * before passing it to the provider. Covers both fresh requests (current user turn just appended)
	 * and idempotent retries (previous attempt's user turn + trailing error stub).
	 *
	 * @param array $messages
	 * @param string $user_message
	 * @return array
	 */
	private function build_history_excluding_current_turn( $messages, $user_message ) {
		for ( $i = count( $messages ) - 1; $i >= 0; $i-- ) {
			if ( isset( $messages[$i]['role'], $messages[$i]['content'] ) && $messages[$i]['role'] === 'user' && $messages[$i]['content'] === $user_message ) {
				return array_slice( $messages, 0, $i );
			}
		}
		return $messages;
	}
}
