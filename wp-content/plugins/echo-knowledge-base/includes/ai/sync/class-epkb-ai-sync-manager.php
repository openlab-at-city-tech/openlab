<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Sync Manager
 *
 * Simplified sync manager that handles individual post syncing.
 * Batch processing and job management is handled by EPKB_AI_Sync_Job_Manager.
 * Supports multiple AI providers (ChatGPT, Gemini) via provider factory.
 */
class EPKB_AI_Sync_Manager {

	/**
	 * Training data database
	 * @var EPKB_AI_Training_Data_DB
	 */
	private $training_data_db;

	/**
	 * Vector store handler
	 * @var EPKB_AI_ChatGPT_Vector_Store|EPKB_AI_Gemini_Vector_Store
	 */
	private $vector_store;

	public function __construct() {
		$this->training_data_db = new EPKB_AI_Training_Data_DB();
		$this->vector_store = EPKB_AI_Provider::get_vector_store_handler();
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

		// Get or create vector store (use $this->vector_store to ensure current_store_id is set for Gemini)
		$vector_store_id = $this->vector_store->get_or_create_vector_store( $collection_id );
		if ( is_wp_error( $vector_store_id ) ) {
			EPKB_AI_Log::add_log( $vector_store_id, array( 'item_id' => $post_id, 'collection_id' => $collection_id, 'message' => 'Failed to get or create vector store' ) );
			return $vector_store_id;
		}

		// 1. Get file content
		$content_data = $this->get_content( $post_id );
		if ( is_wp_error( $content_data ) ) {
			return $content_data;
		}

		// 2. Calculate content hash
		$file_content = $content_data['content'];
		$content_title = $content_data['title'];
		$content_hash = md5( $file_content );

		$content_size = strlen( $file_content );
		if ( $content_size > EPKB_AI_Provider::get_max_file_size() ) {
			// translators: %1$s is the content size, %2$s is the maximum allowed size
			return new WP_Error( 'content_too_large', sprintf( __( 'Content size (%1$s) > allowed size (%2$s)', 'echo-knowledge-base' ), size_format( $content_size ), size_format( EPKB_AI_Provider::get_max_file_size() ) ) );
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
		$existing_file_id = empty( $training_record->file_id ) ? '' : $training_record->file_id;
		$file_id = $existing_file_id;
		$uploaded_file_id = '';

		// a) remove the file from the file system
		if ( $remove_from_file_system ) {
			$file_result = $this->vector_store->delete_file_from_file_storage( $existing_file_id, $vector_store_id );
			if ( is_wp_error( $file_result ) ) {
				EPKB_AI_Log::add_log( $file_result, array( 'item_id' => $post_id, 'file_id' => $existing_file_id, 'message' => 'Failed to delete file from file storage' ) );
				return $file_result;
			}
			$file_id = '';
			$add_to_file_system = true;
		}

		// b) add the file content to the file system
		if ( $add_to_file_system ) {
			$file_result = $this->vector_store->upload_file_to_file_storage( $post_id, $file_content, $item_type, $vector_store_id );
			if ( is_wp_error( $file_result ) ) {
				EPKB_AI_Log::add_log( $file_result, array( 'item_id' => $post_id, 'message' => 'Failed to upload file to file storage' ) );
				return $file_result;
			}

			$file_id = $file_result['id'];
			$uploaded_file_id = $file_id;

		} else {
			$file_id = $existing_file_id;
		}


		// c) remove the file from the vector store
		if ( $remove_from_vector_store ) {
			$file_result = $this->vector_store->remove_file_from_vector_store( $vector_store_id, $existing_file_id );
			if ( is_wp_error( $file_result ) ) {
				$this->rollback_uploaded_file( $vector_store_id, $uploaded_file_id, $post_id );
				EPKB_AI_Log::add_log( $file_result, array( 'item_id' => $post_id, 'file_id' => $existing_file_id, 'message' => 'Failed to remove file from vector store' ) );
				return $file_result;
			}
		}

		// d) add the file content to the vector store
		if ( $add_to_vector_store ) {
			$file_result = $this->vector_store->add_file_to_vector_store( $vector_store_id, $file_id, true );	// checks file is in vector store
			if ( is_wp_error( $file_result ) ) {
				$this->rollback_uploaded_file( $vector_store_id, $uploaded_file_id, $post_id );
				EPKB_AI_Log::add_log( $file_result, array( 'item_id' => $post_id, 'file_id' => $file_id, 'message' => 'Failed to add file to vector store' ) );
				return $file_result;
			}
		}

		// Mark as synced.
		$sync_data = array(
			'file_id'      => $file_id,
			'store_id'     => $vector_store_id,
			'content_hash' => $content_hash,
			'title'        => $content_title,
			'url'          => get_permalink( $post_id ),
		);

		$return_data = array(
			'success' => true,
			'training_data_id' => $training_data_id,
			'sync_data' => $sync_data
		);

		return $return_data;
	}


	/**
	 * Verify a single synced item exists in the AI store. If missing, mark as "outdated".
	 *
	 * @param int $item_id Item ID
	 * @param int $collection_id Collection ID
	 * @return array Result with 'status' ('ok', 'outdated', 'skipped') and optional 'message'
	 */
	public function verify_item( $item_id, $collection_id ) {

		$record = $this->training_data_db->get_training_data_record_by_item_id( $collection_id, $item_id );
		if ( is_wp_error( $record ) || empty( $record ) ) {
			return array( 'status' => 'skipped', 'message' => __( 'Record not found', 'echo-knowledge-base' ) );
		}

		// Only verify synced items (status 'added' or 'updated')
		if ( ! in_array( $record->status, array( 'added', 'updated' ), true ) ) {
			return array( 'status' => 'skipped' );
		}

		// No file_id means nothing to verify
		if ( empty( $record->file_id ) ) {
			$this->training_data_db->update_training_data( $record->id, array( 'status' => 'outdated' ) );
			return array( 'status' => 'outdated', 'message' => __( 'No file ID', 'echo-knowledge-base' ) );
		}

		$vector_store_id = ! empty( $record->store_id ) ? $record->store_id : '';
		if ( empty( $vector_store_id ) ) {
			$this->training_data_db->update_training_data( $record->id, array( 'status' => 'outdated' ) );
			return array( 'status' => 'outdated', 'message' => __( 'No store ID', 'echo-knowledge-base' ) );
		}

		// Check if file exists in file storage
		$is_in_file_storage = $this->vector_store->verify_file_exists_in_file_storage( $record->file_id, $vector_store_id );
		if ( is_wp_error( $is_in_file_storage ) ) {
			return array( 'status' => 'error', 'message' => $is_in_file_storage->get_error_message() );
		}

		if ( ! $is_in_file_storage ) {
			$this->training_data_db->update_training_data( $record->id, array( 'status' => 'outdated', 'file_id' => '' ) );
			return array( 'status' => 'outdated', 'message' => __( 'File missing from file storage', 'echo-knowledge-base' ) );
		}

		// Check if file exists in vector store
		$is_in_vector_store = $this->vector_store->get_file_details_from_vector_store( $vector_store_id, $record->file_id );
		if ( is_wp_error( $is_in_vector_store ) ) {
			return array( 'status' => 'error', 'message' => $is_in_vector_store->get_error_message() );
		}

		if ( ! $is_in_vector_store ) {
			$this->training_data_db->update_training_data( $record->id, array( 'status' => 'outdated' ) );
			return array( 'status' => 'outdated', 'message' => __( 'File missing from vector store', 'echo-knowledge-base' ) );
		}

		return array( 'status' => 'ok' );
	}

	/**
	 * Remove orphaned vector store files that are no longer tracked in the local DB.
	 *
	 * This only removes files from the AI vector store. It never deletes local DB rows.
	 *
	 * @param int $collection_id Collection ID
	 * @return array
	 */
	public function cleanup_orphaned_vector_store_files( $collection_id ) {

		$collection_id = EPKB_AI_Validation::validate_collection_id( $collection_id );
		if ( is_wp_error( $collection_id ) ) {
			return array(
				'status'  => 'error',
				'message' => $collection_id->get_error_message(),
			);
		}

		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return array(
				'status'  => 'error',
				'message' => $collection_config->get_error_message(),
			);
		}

		$vector_store_id = isset( $collection_config['ai_training_data_store_id'] ) ? (string) $collection_config['ai_training_data_store_id'] : '';
		if ( $vector_store_id === '' ) {
			return $this->get_empty_orphan_cleanup_result();
		}

		$ai_file_ids = $this->vector_store->list_vector_store_file_ids( $vector_store_id );
		if ( is_wp_error( $ai_file_ids ) ) {
			return array(
				'status'  => 'error',
				'message' => $ai_file_ids->get_error_message(),
			);
		}

		if ( empty( $ai_file_ids ) ) {
			return $this->get_empty_orphan_cleanup_result();
		}

		$tracked_file_ids = $this->get_tracked_vector_store_file_ids( $collection_id, $vector_store_id );
		$provider = EPKB_AI_Provider::get_active_provider();
		$result = $this->get_empty_orphan_cleanup_result();
		$result['status'] = 'ok';

		foreach ( $ai_file_ids as $file_id ) {
			if ( isset( $tracked_file_ids[ $file_id ] ) ) {
				continue;
			}

			$file_type = $this->detect_orphaned_vector_store_file_type( $file_id, $vector_store_id );
			$remove_result = $this->vector_store->remove_file_from_vector_store( $vector_store_id, $file_id );
			if ( is_wp_error( $remove_result ) ) {
				$result['errors']++;
				EPKB_AI_Log::add_log( $remove_result, array(
					'collection_id' => $collection_id,
					'provider'      => $provider,
					'store_id'      => $vector_store_id,
					'file_id'       => $file_id,
					'message'       => 'Failed to remove orphaned file from vector store'
				) );
				continue;
			}

			$result['removed_orphan_files']++;
			if ( $file_type === 'pdf' ) {
				$result['removed_orphan_pdfs']++;
			} elseif ( $file_type === 'post' ) {
				$result['removed_orphan_posts']++;
			} elseif ( $file_type === 'note' ) {
				$result['removed_orphan_notes']++;
			}
		}

		return $result;
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
		$file_id = $is_new_record ? '' : ( $existing_record->file_id ?? '' );

		// 1. new record - no file id: i.e. not in file system and not in vector store -> add file to file system and vector store
		if ( $is_new_record ) {
			// Insert new record
			$training_data = array(
				'collection_id' => $collection_id,
				'provider'      => EPKB_AI_Provider::get_active_provider(),
				'item_id'       => $post_id,
				'store_id' 		=> $vector_store_id,
				'title'         => $content_title,
				'type'          => $item_type,
				'status'        => 'adding',
				'content_hash'  => $content_hash,
				'url'           => get_permalink( $post_id )
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

		$is_in_file_system = $this->vector_store->verify_file_exists_in_file_storage( $file_id, $vector_store_id );
		if ( is_wp_error( $is_in_file_system ) ) {
			return $is_in_file_system;
		}

		// 3. update record - file id, file storage is missing -> retry cleanup, then upload and re-add the replacement
		if ( ! $is_in_file_system ) {
			return array(
				'training_record'          => $existing_record,
				'training_data_id'         => $existing_record->id,
				'remove_from_file_system'  => true,
				'remove_from_vector_store' => true,
				'add_to_file_system'       => true,
				'add_to_vector_store'      => true
			);
		}

		// File storage content is immutable, so changed content must be uploaded as a new file.
		if ( $existing_record->content_hash !== $content_hash ) {
			return array(
				'training_record'          => $existing_record,
				'training_data_id'         => $existing_record->id,
				'remove_from_file_system'  => true,
				'remove_from_vector_store' => true,
				'add_to_file_system'       => true,
				'add_to_vector_store'      => true
			);
		}

		$is_in_vector_store = $this->vector_store->get_file_details_from_vector_store( $existing_record->store_id, $file_id );
		if ( is_wp_error( $is_in_vector_store ) ) {
			return $is_in_vector_store;
		}

		// 4. update record - file id, in file system and not in vector store -> add file to vector store
		if ( ! $is_in_vector_store ) {
			return array( 'training_record' => $existing_record, 'training_data_id' => $existing_record->id, 'file_id' => $file_id, 'add_to_vector_store' => true );
		}

		// 5. update record - file id, in file system and in vector store and content hash matches -> no action needed
		return array( 'training_record' => $existing_record, 'training_data_id' => $existing_record->id );
	}

	private function get_content( $post_id ) {

		$post = get_post( $post_id );
		if ( ! $post ) {
			$error_msg = __( 'Post not found. It may have been deleted.', 'echo-knowledge-base' );
			return new WP_Error( 'invalid_post', $error_msg, array( 'post_id' => $post_id ) );
		}

		// Use centralized eligibility check.
		$eligibility_check = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
		if ( is_wp_error( $eligibility_check ) ) {
			EPKB_AI_Log::add_log( 'Post excluded from sync: ' . $eligibility_check->get_error_message(), array( 'post_id' => $post_id, 'title' => $post->post_title ) );
			return $eligibility_check;
		}

		// Prepare content for regular posts.
		$content_processor = new EPKB_AI_Content_Processor();
		$prepared = $content_processor->prepare_post( $post );
		if ( is_wp_error( $prepared ) ) {
			return $prepared;
		}

		// Check for empty content (shouldn't happen if prepare_post is working correctly).
		if ( empty( $prepared['content'] ) ) {
			return new WP_Error( 'empty_content', __( 'Content is empty after processing', 'echo-knowledge-base' ), array( 'post_id' => $post_id ) );
		}

		return array(
			'content' => $prepared['content'],
			'title'   => $post->post_title,
		);
	}

	private function rollback_uploaded_file( $vector_store_id, $file_id, $post_id ) {

		if ( empty( $file_id ) ) {
			return;
		}

		$result = $this->vector_store->remove_file_from_vector_store( $vector_store_id, $file_id );
		if ( is_wp_error( $result ) ) {
			EPKB_AI_Log::add_log( $result, array( 'item_id' => $post_id, 'file_id' => $file_id, 'message' => 'Failed to roll back file from vector store' ) );
		}

		$result = $this->vector_store->delete_file_from_file_storage( $file_id, $vector_store_id );
		if ( is_wp_error( $result ) ) {
			EPKB_AI_Log::add_log( $result, array( 'item_id' => $post_id, 'file_id' => $file_id, 'message' => 'Failed to roll back file from file storage' ) );
		}
	}

	/**
	 * Get tracked file IDs for the current collection/store combination.
	 *
	 * @param int $collection_id Collection ID.
	 * @param string $vector_store_id Vector store ID.
	 * @return array
	 */
	private function get_tracked_vector_store_file_ids( $collection_id, $vector_store_id ) {

		$rows = $this->training_data_db->get_training_data_by_collection( $collection_id );
		$provider = EPKB_AI_Provider::get_active_provider();
		$file_ids = array();

		foreach ( $rows as $row ) {
			if ( empty( $row->file_id ) ) {
				continue;
			}

			$row_provider = isset( $row->provider ) ? (string) $row->provider : '';
			if ( $row_provider !== '' ) {
				$row_provider = EPKB_AI_Provider::normalize_provider( $row_provider );
			}
			if ( $row_provider !== '' && $row_provider !== $provider ) {
				continue;
			}

			$row_store_id = isset( $row->store_id ) ? (string) $row->store_id : '';
			if ( $row_store_id !== '' && $row_store_id !== $vector_store_id ) {
				continue;
			}

			$file_ids[ (string) $row->file_id ] = true;
		}

		return $file_ids;
	}

	/**
	 * Detect an orphaned vector store file type based on the provider filename.
	 *
	 * @param string $file_id File ID.
	 * @param string $vector_store_id Vector store ID.
	 * @return string
	 */
	private function detect_orphaned_vector_store_file_type( $file_id, $vector_store_id ) {

		$file_name = $this->get_vector_store_file_name( $file_id, $vector_store_id );
		if ( $file_name === '' ) {
			return 'other';
		}

		$file_name = strtolower( $file_name );

		if ( strpos( $file_name, 'kb_aipro_ai_note_' ) === 0 ) {
			return 'note';
		}

		if ( strpos( $file_name, 'kb_epkb_ai_note_' ) === 0 ) {
			return 'note';
		}

		if ( strpos( $file_name, 'kb_pdf_' ) === 0 || strpos( $file_name, 'kb_html_' ) === 0 || substr( $file_name, -4 ) === '.pdf' ) {
			return 'pdf';
		}

		if ( preg_match( '/^kb_[a-z0-9_-]+_[0-9]+_\d+(?:\.[a-z0-9]+)?$/', $file_name ) ) {
			return 'post';
		}

		return 'other';
	}

	/**
	 * Resolve a provider filename/display name for a vector store file.
	 *
	 * @param string $file_id File ID.
	 * @param string $vector_store_id Vector store ID.
	 * @return string
	 */
	private function get_vector_store_file_name( $file_id, $vector_store_id ) {

		if ( ! method_exists( $this->vector_store, 'get_file_details_from_file_storage' ) ) {
			return '';
		}

		$file_details = $this->vector_store->get_file_details_from_file_storage( $file_id, $vector_store_id );
		if ( is_wp_error( $file_details ) || $file_details === false || ! is_array( $file_details ) ) {
			return '';
		}

		foreach ( array( 'filename', 'displayName', 'display_name', 'name' ) as $key ) {
			if ( ! empty( $file_details[ $key ] ) && is_string( $file_details[ $key ] ) ) {
				return $file_details[ $key ];
			}
		}

		return '';
	}

	/**
	 * Default orphan cleanup response payload.
	 *
	 * @return array
	 */
	private function get_empty_orphan_cleanup_result() {
		return array(
			'status'               => 'ok',
			'removed_orphan_files' => 0,
			'removed_orphan_posts' => 0,
			'removed_orphan_pdfs'  => 0,
			'removed_orphan_notes' => 0,
			'errors'               => 0,
		);
	}
}
