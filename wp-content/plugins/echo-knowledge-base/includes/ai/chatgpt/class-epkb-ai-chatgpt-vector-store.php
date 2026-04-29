<?php

/**
 * ChatGPT Handler
 *
 * Provides high-level operations for ChatGPT API including vector stores and files.
 * Wraps the ChatGPT client and vector store service for use by the sync manager.
 */
class EPKB_AI_ChatGPT_Vector_Store {

	const VECTOR_STORES_ENDPOINT = '/vector_stores';
	const FILES_ENDPOINT = '/files';
	const FILE_PROCESSING_POLL_ATTEMPTS = 30;
	const FILE_PROCESSING_POLL_INTERVAL = 2;

	/**
	 * ChatGPT client
	 * @var EPKB_ChatGPT_Client
	 */
	private $client;

	/**
	 * Cache of verified store IDs per collection (static to persist across instances within a request)
	 * Key: collection_id, Value: store_id
	 * @var array
	 */
	private static $verified_stores = array();

	public function __construct() {
		$this->client = new EPKB_ChatGPT_Client();
	}


	/************************************************************************************
	 * Manage vector stores
	 ************************************************************************************/

	/**
	 * Create a vector store
	 *
	 * @param array $data Vector store data with 'name' and optional 'metadata'
	 * @return array|WP_Error Vector store object with 'id' or error
	 */
	public function create_vector_store( $data ) {

		$vector_store_data = array(
			'name' => $data['name'],
			//'metadata' => EPKB_AI_Validation::validate_metadata( $data )
		);

		return $this->client->request( self::VECTOR_STORES_ENDPOINT, $vector_store_data, 'POST', 'vector_store' );
	}

	/**
	 * Get or create vector store for collection
	 *
	 * @param int $collection_id Collection ID
	 * @return string|WP_Error Vector store ID or error
	 */
	public function get_or_create_vector_store( $collection_id ) {

		// Check cache first (avoids redundant API calls during batch sync)
		if ( isset( self::$verified_stores[ $collection_id ] ) ) {
			return self::$verified_stores[ $collection_id ];
		}

		// Get collection configuration
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $collection_config;
		}

		// Check if vector store already exists for this collection
		$existing_store_id = $collection_config['ai_training_data_store_id'];
		if ( ! empty( $existing_store_id ) ) {
			// Verify the store still exists in ChatGPT
			$store_info = $this->get_vector_store_info_by_collection_id( $collection_id );
			if ( ! is_wp_error( $store_info ) ) {
				self::$verified_stores[ $collection_id ] = $existing_store_id;
				return $existing_store_id;
			}

			// If store doesn't exist anymore, clear it from the collection
			$collection_config['ai_training_data_store_id'] = '';
			$collection_config['override_vector_store_id'] = true; // Allow overriding the vector store ID
			$save_result = EPKB_AI_Training_Data_Config_Specs::update_training_data_collection( $collection_id, $collection_config );
			if ( is_wp_error( $save_result ) ) {
				return $save_result;
			}
		}

		// Create new vector store
		$store_name = empty( $collection_config['ai_training_data_store_name'] ) ? EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id ) : $collection_config['ai_training_data_store_name'];

		$response = $this->create_vector_store( array(
			'name'     => $store_name,
			'metadata' => array(
				'collection_id' => strval( $collection_id ),
				'kb_id'        => strval( $collection_id ),
				'created_by'   => 'echo_kb'
			)
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$store_id = isset( $response['id'] ) ? $response['id'] : '';
		if ( empty( $store_id ) ) {
			return new WP_Error( 'invalid_store_id', __( 'Failed to create vector store', 'echo-knowledge-base' ) );
		}

		$collection_config['ai_training_data_store_id'] = $store_id;
		$collection_config['override_vector_store_id'] = true; // Allow overriding the vector store ID
		$save_result = EPKB_AI_Training_Data_Config_Specs::update_training_data_collection( $collection_id, $collection_config );
		if ( is_wp_error( $save_result ) ) {
			return $save_result;
		}

		self::$verified_stores[ $collection_id ] = $store_id;

		return $store_id;
	}

	/**
	 * Get vector store info by collection id
	 *
	 * @param int $collection_id Collection ID
	 * @return array|WP_Error Vector store info or error
	 */
	public function get_vector_store_info_by_collection_id( $collection_id ) {

		// Get collection configuration
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $collection_config;
		}

		// Get vector store ID from collection config
		$vector_store_id = isset( $collection_config['ai_training_data_store_id'] ) ? $collection_config['ai_training_data_store_id'] : '';
		if ( empty( $vector_store_id ) ) {
			return new WP_Error( 'no_vector_store', __( 'No vector store found', 'echo-knowledge-base' ) );
		}

		return $this->get_vector_store_info_by_id( $vector_store_id );
	}

	/**
	 * Get vector store info by store id
	 *
	 * @param string $vector_store_id
	 * @return array|WP_Error
	 */
	public function get_vector_store_info_by_id( $vector_store_id ) {

		// Get vector store details from ChatGPT
		$response = $this->client->request( self::VECTOR_STORES_ENDPOINT . '/' . $vector_store_id, array(), 'GET', 'vector_store' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Get file count
		$files_response = $this->client->request( self::VECTOR_STORES_ENDPOINT . '/' . $vector_store_id . '/files', array( 'limit' => 1 ), 'GET', 'vector_store' );
		$file_count = 0;
		if ( ! is_wp_error( $files_response ) && isset( $files_response['data'] ) ) {
			$file_count = isset( $files_response['total'] ) ? $files_response['total'] : count( $files_response['data'] );
		}

		return array(
			'id' => $response['id'],
			'name' => isset( $response['name'] ) ? $response['name'] : '',
			'status' => isset( $response['status'] ) ? $response['status'] : 'unknown',
			'file_counts' => array(
				'total' => $file_count,
				'in_progress' => isset( $response['file_counts']['in_progress'] ) ? $response['file_counts']['in_progress'] : 0,
				'completed' => isset( $response['file_counts']['completed'] ) ? $response['file_counts']['completed'] : 0,
				'failed' => isset( $response['file_counts']['failed'] ) ? $response['file_counts']['failed'] : 0,
				'cancelled' => isset( $response['file_counts']['cancelled'] ) ? $response['file_counts']['cancelled'] : 0
			),
			'created_at' => isset( $response['created_at'] ) ? $response['created_at'] : '',
			'metadata' => isset( $response['metadata'] ) ? $response['metadata'] : array()
		);
	}

	/**
	 * Update vector store
	 *
	 * @param string $vector_store_id Vector store ID
	 * @param array $data Data to update (e.g., 'name')
	 * @return array|WP_Error Updated vector store object or error
	 */
	public function update_vector_store( $vector_store_id, $data ) {

		if ( empty( $vector_store_id ) ) {
			return new WP_Error( 'missing_id', __( 'Vector store ID is required', 'echo-knowledge-base' ) );
		}

		$update_data = array();
		if ( isset( $data['name'] ) ) {
			$update_data['name'] = $data['name'];
		}

		if ( empty( $update_data ) ) {
			return new WP_Error( 'no_data', __( 'Vector store name is required', 'echo-knowledge-base' ) );
		}

		return $this->client->request( self::VECTOR_STORES_ENDPOINT . "/{$vector_store_id}", $update_data, 'POST', 'vector_store' );
	}

	/**
	 * Delete a vector store
	 *
	 * @param string $vector_store_id Vector store ID
	 * @return bool|WP_Error True on success or error
	 */
	public function delete_vector_store( $vector_store_id ) {

		if ( empty( $vector_store_id ) ) {
			return new WP_Error( 'missing_id', __( 'Vector store ID is required', 'echo-knowledge-base' ) );
		}

		$response = $this->client->request( self::VECTOR_STORES_ENDPOINT . "/{$vector_store_id}", array(), 'DELETE', 'vector_store' );
		if ( is_wp_error( $response ) ) {
			// Ignore 404 errors - vector store already deleted
			if ( $response->get_error_code() === 'not_found' ) {
				return true;
			}
			return $response;
		}

		// Check if deletion was successful
		if ( isset( $response['deleted'] ) && $response['deleted'] === true ) {
			return true;
		}

		return new WP_Error( 'delete_failed', __( 'Failed to delete vector store', 'echo-knowledge-base' ) . ' ' . $vector_store_id );
	}


	/************************************************************************************
	 * Manage files in Vector Store
	 ************************************************************************************/

	/**
	 * Add a file to a vector store. Ensure the file exists in ChatGPT file storage first.
	 *
	 * @param string $vector_store_id
	 * @param string $file_id
	 * @param bool $skip_file_verification
	 * @return array|WP_Error
	 */
	public function add_file_to_vector_store( $vector_store_id, $file_id, $skip_file_verification = false ) {

		if ( empty( $vector_store_id ) || empty( $file_id ) ) {
			return new WP_Error( 'missing_params', __( 'Vector store ID and file ID are required', 'echo-knowledge-base' ) );
		}

		// 1. ensure the file exists in the ChatGPT file storage
		if ( ! $skip_file_verification ) {
			$file_exists = $this->verify_file_exists_in_file_storage( $file_id );
			if ( is_wp_error( $file_exists ) ) {
				return $file_exists;
			}
		}

		// 2. add the file to the vector store
		return $this->client->request( self::VECTOR_STORES_ENDPOINT . "/{$vector_store_id}" . self::FILES_ENDPOINT, [ 'file_id' => $file_id ], 'POST', 'vector_store_file' );
	}

	/**
	 * Remove a file from a vector store
	 *
	 * @param string $vector_store_id Vector store ID
	 * @param string $file_id File ID (file-xxx) - ChatGPT uses original file ID for vector store operations
	 * @return bool|WP_Error True on success or error
	 */
	public function remove_file_from_vector_store( $vector_store_id, $file_id ) {

		if ( empty( $vector_store_id ) || empty( $file_id ) ) {
			return new WP_Error( 'missing_params', __( 'Vector store ID and file ID are required', 'echo-knowledge-base' ) );
		}

		$response = $this->client->request(	self::VECTOR_STORES_ENDPOINT . "/{$vector_store_id}" . self::FILES_ENDPOINT . "/{$file_id}", array(), 'DELETE', 'vector_store_file' );
		if ( is_wp_error( $response ) ) {
			// Ignore 404 errors - file already removed
			if ( $response->get_error_code() === 'not_found' ) {
				return true;
			}
			return $response;
		}

		return true;
	}

	/**
	 * Get file details from vector store
	 *
	 * @param string $vector_store_id Vector store ID
	 * @param string $file_id File ID
	 * @return array|WP_Error File details or error
	 */
	public function get_file_details_from_vector_store( $vector_store_id, $file_id ) {

		if ( empty( $vector_store_id ) || empty( $file_id ) ) {
			return new WP_Error( 'missing_params', __( 'Vector store ID and file ID are required', 'echo-knowledge-base' ) );
		}

		return $this->client->request( self::VECTOR_STORES_ENDPOINT . "/{$vector_store_id}" . self::FILES_ENDPOINT . "/{$file_id}", array(), 'GET', 'vector_store_file' );
	}

	/**
	 * Wait until a vector store file is ready for search.
	 *
	 * @param string $vector_store_id Vector store ID.
	 * @param string $file_id File ID.
	 * @return array|WP_Error
	 */
	public function wait_for_file_to_complete_in_vector_store( $vector_store_id, $file_id ) {

		if ( empty( $vector_store_id ) || empty( $file_id ) ) {
			return new WP_Error( 'missing_params', __( 'Vector store ID and file ID are required', 'echo-knowledge-base' ) );
		}

		for ( $attempt = 0; $attempt < self::FILE_PROCESSING_POLL_ATTEMPTS; $attempt++ ) {
			$file_details = $this->get_file_details_from_vector_store( $vector_store_id, $file_id );
			if ( is_wp_error( $file_details ) ) {
				if ( $file_details->get_error_code() !== 'not_found' ) {
					return $file_details;
				}
			} else {
				$status = isset( $file_details['status'] ) ? strtolower( (string) $file_details['status'] ) : '';
				if ( $status === '' || $status === 'completed' ) {
					return $file_details;
				}

				if ( in_array( $status, array( 'failed', 'cancelled' ), true ) ) {
					$message = __( 'AI provider failed to process the uploaded file.', 'echo-knowledge-base' );
					if ( ! empty( $file_details['last_error']['message'] ) ) {
						$message = 'AI ERROR: ' . $file_details['last_error']['message'];
					}

					return new WP_Error( 'file_processing_failed', $message, array( 'status' => $status ) );
				}
			}

			if ( $attempt < self::FILE_PROCESSING_POLL_ATTEMPTS - 1 ) {
				EPKB_AI_Utilities::safe_sleep( self::FILE_PROCESSING_POLL_INTERVAL );
			}
		}

		return new WP_Error( 'file_processing_timeout', __( 'AI provider is still processing the uploaded file. Please try again.', 'echo-knowledge-base' ) );
	}


	/************************************************************************************
	 * Manage files in AI file storage
	 ************************************************************************************/

	/**
	 * Upload a file to file storage
	 *
	 * @param string $id Related entity ID (e.g., post ID)
	 * @param string $file_content File content
	 * @param string $file_type File type (e.g., post type)
	 * @param string $store_id Store ID (ignored for ChatGPT - files exist independently of stores)
	 * @param string $file_extension File extension for the uploaded file
	 * @param string $file_content_type MIME type for the uploaded file
	 * @return array|WP_Error File object with 'id' or error
	 */
	public function upload_file_to_file_storage( $id, $file_content, $file_type, $store_id = '', $file_extension = 'txt', $file_content_type = 'text/plain' ) {

		// Map KB post types to 'article' for clarity, use the actual type for others
		if ( strpos( $file_type, 'epkb_post_type_' ) === 0 ) {
			$safe_type = 'article';
		} else {
			// WordPress post_type is already slug-like (lowercase, underscores, safe)
			// Just do minimal sanitization to ensure it's safe for ChatGPT API
			$safe_type = preg_replace( '/[^a-z0-9_-]/', '_', strtolower( $file_type ) );
		}

		$safe_type = empty( $safe_type ) ? 'article' : $safe_type;
		$safe_extension = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $file_extension ) );
		$safe_extension = empty( $safe_extension ) ? 'txt' : $safe_extension;
		$file_name = 'kb_' . $safe_type . '_' . $id . '_' . time() . '.' . $safe_extension;

		return $this->client->request( self::FILES_ENDPOINT, array(
			'file_name'         => $file_name,
			'file_content'      => $file_content,
			'file_purpose'      => 'assistants',
			'file_content_type' => $file_content_type,
		), 'POST', 'file_storage_upload' );
	}

	/**
	 * Upload a PDF file to file storage
	 *
	 * @param string $id Related entity ID (e.g., unique identifier for the PDF)
	 * @param string $pdf_binary_content Raw PDF binary content
	 * @param string $store_id Store ID (ignored for ChatGPT - files exist independently of stores)
	 * @return array|WP_Error File object with 'id' or error
	 */
	public function upload_pdf_to_file_storage( $id, $pdf_binary_content, $store_id = '' ) {

		$file_name = 'kb_pdf_' . $id . '_' . time() . '.pdf';

		return $this->client->request( self::FILES_ENDPOINT, array(
			'file_name'        => $file_name,
			'file_content'     => $pdf_binary_content,
			'file_purpose'     => 'assistants',
			'file_content_type' => 'application/pdf',
		), 'POST', 'file_storage_upload' );
	}

	/**
	 * Delete file from storage
	 *
	 * @param string $file_id File ID
	 * @param string $store_id Store ID (ignored for ChatGPT - files exist independently of stores)
	 * @return bool|WP_Error True on success or error
	 */
	public function delete_file_from_file_storage( $file_id, $store_id = '' ) {

		if ( empty( $file_id ) ) {
			return new WP_Error( 'missing_id', __( 'File ID is required', 'echo-knowledge-base' ) );
		}

		$response = $this->client->request( self::FILES_ENDPOINT . "/{$file_id}", array(), 'DELETE', 'file_storage' );
		if ( is_wp_error( $response ) ) {
			// Ignore 404 errors - file already deleted
			if ( $response->get_error_code() === 'not_found' ) {
				return true;
			}
			return $response;
		}

		return true;
	}

	/**
	 * List all file IDs in a vector store
	 *
	 * @param string $vector_store_id Vector store ID
	 * @return array|WP_Error Array of file ID strings or error
	 */
	public function list_vector_store_file_ids( $vector_store_id ) {

		if ( empty( $vector_store_id ) ) {
			return new WP_Error( 'missing_id', __( 'Vector store ID is required', 'echo-knowledge-base' ) );
		}

		$file_ids = array();
		$after = '';
		$seen_cursors = array();

		while ( true ) {
			$params = array( 'limit' => 100 );
			if ( ! empty( $after ) ) {
				$params['after'] = $after;
			}

			$response = $this->client->request( self::VECTOR_STORES_ENDPOINT . "/{$vector_store_id}" . self::FILES_ENDPOINT, $params, 'GET', 'vector_store_file' );
			if ( is_wp_error( $response ) ) {
				return $response;
			}

			if ( empty( $response['data'] ) || ! is_array( $response['data'] ) ) {
				break;
			}

			foreach ( $response['data'] as $file ) {
				if ( isset( $file['id'] ) && ( ! isset( $file['status'] ) || in_array( $file['status'], array( 'completed', 'in_progress' ), true ) ) ) {
					$file_ids[] = $file['id'];
				}
			}

			// Check if there are more pages
			if ( empty( $response['has_more'] ) ) {
				break;
			}

			// Get the last file ID for cursor pagination
			$last_file = end( $response['data'] );
			$next_after = isset( $last_file['id'] ) ? (string) $last_file['id'] : '';
			if ( $next_after === '' ) {
				break;
			}

			if ( isset( $seen_cursors[ $next_after ] ) ) {
				return new WP_Error( 'invalid_pagination_cursor', __( 'Vector store pagination cursor repeated while listing files', 'echo-knowledge-base' ) );
			}

			$seen_cursors[ $next_after ] = true;
			$after = $next_after;
		}

		return $file_ids;
	}

	/**
	 * Get file details from file storage
	 *
	 * @param string $file_id File ID
	 * @param string $store_id Store ID (ignored for ChatGPT - files exist independently of stores)
	 * @return array|bool|WP_Error File details, false if not found, or error
	 */
	public function get_file_details_from_file_storage( $file_id, $store_id = '' ) {

		if ( empty( $file_id ) ) {
			return new WP_Error( 'missing_id', __( 'File ID is required', 'echo-knowledge-base' ) );
		}

		$response = $this->client->request( self::FILES_ENDPOINT . '/' . $file_id, array(), 'GET', 'file_storage' );
		if ( is_wp_error( $response ) ) {
			if ( $response->get_error_code() === 'not_found' ) {
				return false;
			}
			return $response;
		}

		return $response;
	}

	/**
	 * Verify file existence
	 *
	 * @param string $file_id File ID to verify
	 * @param string $store_id Store ID (ignored for ChatGPT - files exist independently of stores)
	 * @return bool|WP_Error True if exists, false if not found, WP_Error on other errors
	 */
	public function verify_file_exists_in_file_storage( $file_id, $store_id = '' ) {

		if ( empty( $file_id ) ) {
			return new WP_Error( 'missing_id', __( 'File ID is required', 'echo-knowledge-base' ) );
		}

		$response = $this->get_file_details_from_file_storage( $file_id, $store_id );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response !== false;
	}
}
