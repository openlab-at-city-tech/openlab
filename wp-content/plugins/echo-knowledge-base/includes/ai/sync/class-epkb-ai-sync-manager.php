<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Sync Manager
 * 
 * Simplified sync manager that handles individual post syncing.
 * Batch processing and job management is handled by EPKB_AI_Sync_Job_Manager.
 * 
 * Summary Mode:
 * When ai_training_data_use_summary is enabled, content is rewritten using OpenAI
 * before being uploaded to the vector store. 
 */
class EPKB_AI_Sync_Manager {

	/**
	 * Training data database
	 * @var EPKB_AI_Training_Data_DB
	 */
	private $training_data_db;

	/**
	 * OpenAI handler
	 * @var EPKB_AI_OpenAI_Vector_Store
	 */
	private $vector_store;

	public function __construct() {
		$this->training_data_db = new EPKB_AI_Training_Data_DB();
		$this->vector_store = new EPKB_AI_OpenAI_Vector_Store();
	}

	/**
	 * Process a single post - called from batch or single post Admin UI
	 *
	 * @param int $post_id Post ID
	 * @param string $item_type typically 'post'
	 * @param int $collection_id Collection ID
	 * @return array|WP_Error Result
	 */
	public function sync_post( $post_id, $item_type, $collection_id ) {
		
		$collection_id = EPKB_AI_Validation::validate_collection_id( $collection_id );
		if ( is_wp_error( $collection_id ) ) {
			return $collection_id;
		}

		// Get or create vector store
		$openai_handler = new EPKB_AI_OpenAI_Vector_Store();
		$vector_store_id = $openai_handler->get_or_create_vector_store( $collection_id );
		if ( is_wp_error( $vector_store_id ) ) {
			return $vector_store_id;
		}

		// 1. Get file content
		$content_data = $this->get_content( $post_id, $item_type );
		if ( is_wp_error( $content_data ) ) {
			return $content_data;
		}

		// 2. Calculate content hash
		$file_content = $content_data['content'];
		$content_title = $content_data['title'];
		$content_hash = md5( $file_content );
		
		$content_size = strlen( $file_content );
		if ( $content_size > EPKB_OpenAI_Client::MAX_FILE_SIZE ) {
			return new WP_Error( 'content_too_large', sprintf( __( 'Content size (%s) > allowed size (%s)', 'echo-knowledge-base' ), size_format( $content_size ), size_format( EPKB_OpenAI_Client::MAX_FILE_SIZE ) ) );
		}

		// 3. Get existing or create a new training data record in DB
		$training_data_result = $this->get_training_data_record_for_sync( $collection_id, $post_id, $content_title, $content_hash, $item_type, $vector_store_id );
		if ( is_wp_error( $training_data_result ) ) {
			return $training_data_result;
		}

		$training_data_id = $training_data_result['training_data_id'];
		$training_record = $training_data_result['training_record'];
		$add_to_file_system = ! empty( $training_data_result['add_to_file_system'] );
		$add_to_vector_store = ! empty( $training_data_result['add_to_vector_store'] );
		$remove_from_file_system = ! empty( $training_data_result['remove_from_file_system'] );
		$remove_from_vector_store = ! empty( $training_data_result['remove_from_vector_store'] );

		// a) remove the file from the file system
		if ( $remove_from_file_system ) {
			$file_result = $this->vector_store->delete_file_from_file_storage( $training_record->file_id );
			if ( is_wp_error( $file_result ) ) {
				return $file_result;
			}
			$file_id = '';
			$add_to_file_system = true;
		}

		// b) add the file content to the file system
		if ( $add_to_file_system ) {
			$file_result = $this->vector_store->upload_file_to_file_storage( $post_id, $file_content, $item_type );
			if ( is_wp_error( $file_result ) ) {
				return $file_result;
			}

			$file_id = $file_result['id'];

			// update the training data record with the file id
			$result = $this->training_data_db->update_training_data( $training_data_id, array( 'file_id' => $file_id ) );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

		} else {
			$file_id = $training_record->file_id;
		}


		// c) remove the file from the vector store
		if ( $remove_from_vector_store ) {
			$file_result = $this->vector_store->remove_file_from_vector_store( $vector_store_id, $training_record->file_id );
			if ( is_wp_error( $file_result ) ) {
				return $file_result;
			}
		}

		// d) add the file content to the vector store
		if ( $add_to_vector_store ) {
			$file_result = $this->vector_store->add_file_to_vector_store( $vector_store_id, $file_id, true );	// checks file is in vector store
			if ( is_wp_error( $file_result ) ) {
				return $file_result;
			}
		}

		// Mark as synced
		$sync_data = array(
			'file_id' => $file_id,
			'store_id' => $vector_store_id,
			'content_hash' => $content_hash
		);
		
		// Update title and URL for regular WordPress posts (KB articles, pages, etc.)
		// Note: 'epkb_kb_files' is an extensibility feature for non-post content sources:
		//   - Backend support: Load plugin source files/documentation for AI training
		//   - Future use: External file uploads (PDFs, docs, etc.)
		// These files use the 'epkb_process_kb_file' filter for content and don't have a $post object
		if ( $item_type !== 'epkb_kb_files' ) {
			$sync_data['title'] = $content_title;
			$sync_data['url'] = get_permalink( $post_id );
		}
		
		$return_data = array(
			'success' => true,
			'training_data_id' => $training_data_id,
			'sync_data' => $sync_data
		);

		return $return_data;
	}


	/**********************************************************************************
	 * Helper functions
	 **********************************************************************************/

	private function get_training_data_record_for_sync( $collection_id, $post_id, $content_title, $content_hash, $item_type, $vector_store_id ) {

		$existing_record = $this->training_data_db->get_training_data_record_by_item_id( $collection_id, $post_id );
		if ( is_wp_error( $existing_record ) ) {
			return $existing_record;
		}

		$is_new_record = empty( $existing_record );
		$file_id = $is_new_record ? '' : ($existing_record->file_id ?? '');

		// 1. new record - no file id: i.e. not in file system and not in vector store -> add file to file system and vector store
		if ( $is_new_record ) {
			// Insert new record
			$training_data = array(
				'collection_id' => $collection_id,
				'item_id' => $post_id,
				'store_id' => $vector_store_id,
				'title' => $content_title,
				'type' => $item_type,
				'status' => 'adding',
				'content_hash' => $content_hash,
				'url' => $item_type === 'epkb_kb_files' ? '' : get_permalink( $post_id )
			);

			$training_data_id = $this->training_data_db->insert_training_data( $training_data );
			if ( is_wp_error( $training_data_id ) ) {
				return $training_data_id;
			}

			$training_record = $this->training_data_db->get_training_data_row_by_id( $training_data_id );
			if ( is_wp_error( $training_record ) ) {
				return $training_record;
			}

			return array( 'training_record' => $training_record, 'training_data_id' => $training_data_id, 'add_to_file_system' => true, 'add_to_vector_store' => true );
		}

		// 2. update record - no file id: i.e. not in file system and not in vector store -> add file to file system and vector store
		if ( empty( $file_id ) ) {
			return array( 'training_record' => $existing_record, 'training_data_id' => $existing_record->id, 'add_to_file_system' => true, 'add_to_vector_store' => true );
		}

		$is_in_file_system = $this->vector_store->verify_file_exists_in_file_storage( $file_id );
		if ( is_wp_error( $is_in_file_system ) ) {
			return $is_in_file_system;
		}

		// 3. update record - file id, not in file system and in vector store -> remove file id and remove from vector store then add file to file system and vector store
		if ( ! $is_in_file_system ) {
			$result = $this->training_data_db->update_training_data( $existing_record->id, array( 'file_id' => '' ) );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			// remove from vector store in case it was added previously
			$result = $this->vector_store->remove_file_from_vector_store( $existing_record->store_id, $file_id );
			if ( is_wp_error( $result ) ) {
				//ignore error
			}
			
			return array( 'training_record' => $existing_record, 'training_data_id' => $existing_record->id, 'remove_from_file_system' => true, 'add_to_file_system' => true, 'add_to_vector_store' => true );
		}

		$is_in_vector_store = $this->vector_store->get_file_details_from_vector_store( $existing_record->store_id, $file_id );
		if ( is_wp_error( $is_in_vector_store ) ) {
			return $is_in_vector_store;
		}

		// 4. update record - file id, in file system and not in vector store -> add file to vector store
		if ( ! $is_in_vector_store ) {
			return array( 'training_record' => $existing_record, 'training_data_id' => $existing_record->id, 'file_id' => $file_id, 'add_to_vector_store' => true );
		}

		// 5. update record - file id, in file system, in vector store BUT content changed -> remove old file from vector store and add new file to vector store
		if ( $existing_record->content_hash !== $content_hash ) {
			return array( 'training_record' => $existing_record, 'training_data_id' => $existing_record->id, 'remove_from_vector_store' => true, 'add_to_vector_store' => true );
		}

		// 6. update record - file id, in file system and in vector store and content hash matches -> no action needed
		return array( 'training_record' => $existing_record, 'training_data_id' => $existing_record->id );
	}

	private function get_content( $post_id, $item_type ) {
		$post_title = '';

		if ( $item_type === 'epkb_kb_files' ) {
			// For KB files, get content from filter
			$content = '';
			if ( has_filter( 'epkb_process_kb_file' ) ) {
				$content = apply_filters( 'epkb_process_kb_file', $post_id );
			}
			if ( is_wp_error( $content ) ) {
				$this->training_data_db->mark_as_error( $post_id, 500, $content->get_error_message() );
				return $content;
			}
			if ( empty( $content ) ) {
				$this->training_data_db->mark_as_error( $post_id, 404, __( 'Content not found', 'echo-knowledge-base' ) );
				return new WP_Error( 'invalid_content', __( 'Content not found', 'echo-knowledge-base' ), array( 'post_id' => $post_id ) );
			}

			$prepared = array(
				'content' => $content,
				'size' => strlen( $content )
			);

		} else {

			$post = get_post( $post_id );
			if ( ! $post ) {
				$this->training_data_db->mark_as_error( $post_id, 404, __( 'Post not found', 'echo-knowledge-base' ) );
				return new WP_Error( 'invalid_post', __( 'Post not found', 'echo-knowledge-base' ), array( 'post_id' => $post_id ) );
			}

			// Use centralized eligibility check
			$eligibility_check = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
			if ( is_wp_error( $eligibility_check ) ) {
				$error_code = 404; // Default error code
				if ( $eligibility_check->get_error_code() === 'post_password_protected' ) {
					$error_code = 403;
				}
				EPKB_AI_Log::add_log( 'Post excluded from sync: ' . $eligibility_check->get_error_message(), array( 'post_id' => $post_id, 'title' => $post->post_title ) );
				$this->training_data_db->mark_as_error( $post_id, $error_code, $eligibility_check->get_error_message() );
				return $eligibility_check;
			}

			// Prepare content for regular posts
			$content_processor = new EPKB_AI_Content_Processor();
			$prepared = $content_processor->prepare_post( $post );
			if ( is_wp_error( $prepared ) ) {
				$error_code = $prepared->get_error_code() === 'post_not_published' ? 404 : 500;
				$this->training_data_db->mark_as_error( $post_id, $error_code, $prepared->get_error_message() );
				return $prepared;
			}

			// Check for empty content (shouldn't happen if prepare_post is working correctly)
			if ( empty( $prepared['content'] ) ) {
				return new WP_Error( 'empty_content', __( 'Content is empty', 'echo-knowledge-base' ), array( 'post_id' => $post_id ) );
			}
		}

		return [ 'content' => $prepared['content'], 'title' => isset( $post ) ? $post->post_title : $post_title ];
	}
}