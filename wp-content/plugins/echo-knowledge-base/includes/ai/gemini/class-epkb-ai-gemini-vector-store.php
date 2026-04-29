<?php defined( 'ABSPATH' ) || exit();

/**
 * Gemini File Search Store Handler
 *
 * Provides operations for Gemini File Search Stores and documents.
 * Mirrors EPKB_AI_ChatGPT_Vector_Store interface for uniform handling.
 *
 * KEY DIFFERENCE FROM CHATGPT:
 * - ChatGPT has two layers: File Storage (/files) + Vector Store (/vector_stores)
 * - Gemini has one layer: File Search Store (documents uploaded directly to store)
 *
 * This class makes Gemini work with the same interface by treating the
 * File Search Store as both "file storage" and "vector store".
 */
class EPKB_AI_Gemini_Vector_Store {

	const VECTOR_STORES_ENDPOINT = '/fileSearchStores';

	/**
	 * Gemini client
	 * @var EPKB_Gemini_Client
	 */
	private $client;

	/**
	 * Cache of verified store IDs per collection (static to persist across instances within a request)
	 * Key: collection_id, Value: store_id
	 * @var array
	 */
	private static $verified_stores = array();

	public function __construct() {
		$this->client = new EPKB_Gemini_Client();
	}


	/************************************************************************************
	 * Manage File Search Stores
	 ************************************************************************************/

	/**
	 * Create a file search store
	 *
	 * @param array $data Store data with 'name' (used as displayName)
	 * @return array|WP_Error Store object with 'id' or error
	 */
	public function create_vector_store( $data ) {

		$store_data = array(
			'displayName' => isset( $data['name'] ) ? $data['name'] : ''
		);

		$response = $this->client->request( self::VECTOR_STORES_ENDPOINT, $store_data, 'POST', 'file_search_store' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Extract store ID from resource name (format: fileSearchStores/xxx)
		$store_id = $this->extract_store_id_from_response( $response );

		return array(
			'id'   => $store_id,
			'name' => isset( $response['displayName'] ) ? $response['displayName'] : ''
		);
	}

	/**
	 * Get or create vector store for collection
	 *
	 * @param int $collection_id Collection ID
	 * @return string|WP_Error Store ID or error
	 */
	public function get_or_create_vector_store( $collection_id ) {

		// Check cache first (avoids redundant API calls during batch sync)
		if ( isset( self::$verified_stores[ $collection_id ] ) ) {
			return self::$verified_stores[ $collection_id ];
		}

		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $collection_config;
		}

		// Check if store already exists
		$existing_store_id = isset( $collection_config['ai_training_data_store_id'] ) ? $collection_config['ai_training_data_store_id'] : '';
		if ( ! empty( $existing_store_id ) ) {
			// Verify the store still exists
			$store_info = $this->get_vector_store_info_by_id( $existing_store_id );
			if ( ! is_wp_error( $store_info ) ) {
				self::$verified_stores[ $collection_id ] = $existing_store_id;
				return $existing_store_id;
			}

			// Store doesn't exist anymore, clear it
			$collection_config['ai_training_data_store_id'] = '';
			$collection_config['override_vector_store_id'] = true;
			EPKB_AI_Training_Data_Config_Specs::update_training_data_collection( $collection_id, $collection_config );
		}

		// Create new store
		$store_name = ! empty( $collection_config['ai_training_data_store_name'] )
			? $collection_config['ai_training_data_store_name']
			: EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id );

		$response = $this->create_vector_store( array( 'name' => $store_name ) );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$store_id = $response['id'];
		if ( empty( $store_id ) ) {
			return new WP_Error( 'invalid_store_id', __( 'Failed to create file search store', 'echo-knowledge-base' ) );
		}

		// Save store ID
		$collection_config['ai_training_data_store_id'] = $store_id;
		$collection_config['override_vector_store_id'] = true;
		$save_result = EPKB_AI_Training_Data_Config_Specs::update_training_data_collection( $collection_id, $collection_config );
		if ( is_wp_error( $save_result ) ) {
			return $save_result;
		}

		self::$verified_stores[ $collection_id ] = $store_id;

		return $store_id;
	}

	/**
	 * Get vector store info by collection ID
	 *
	 * @param int $collection_id
	 * @return array|WP_Error
	 */
	public function get_vector_store_info_by_collection_id( $collection_id ) {

		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $collection_config;
		}

		$store_id = isset( $collection_config['ai_training_data_store_id'] ) ? $collection_config['ai_training_data_store_id'] : '';
		if ( empty( $store_id ) ) {
			return new WP_Error( 'no_vector_store', __( 'No file search store found', 'echo-knowledge-base' ) );
		}

		return $this->get_vector_store_info_by_id( $store_id );
	}

	/**
	 * Get vector store info by store ID
	 *
	 * @param string $store_id
	 * @return array|WP_Error
	 */
	public function get_vector_store_info_by_id( $store_id ) {

		// Gemini expects the full resource name
		$resource_name = $this->get_resource_name( $store_id );

		$response = $this->client->request( '/' . $resource_name, array(), 'GET', 'file_search_store' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Get document count by listing all document IDs
		$doc_ids = $this->list_vector_store_file_ids( $store_id );
		$doc_count = is_wp_error( $doc_ids ) ? 0 : count( $doc_ids );

		return array(
			'id'          => $this->extract_store_id_from_response( $response ),
			'name'        => isset( $response['displayName'] ) ? $response['displayName'] : '',
			'status'      => 'completed', // Gemini stores are immediately ready
			'file_counts' => array(
				'total'       => $doc_count,
				'in_progress' => 0,
				'completed'   => $doc_count,
				'failed'      => 0,
				'cancelled'   => 0
			),
			'created_at'  => isset( $response['createTime'] ) ? $response['createTime'] : '',
			'metadata'    => array()
		);
	}

	/**
	 * Update vector store
	 *
	 * @param string $store_id
	 * @param array $data
	 * @return array|WP_Error
	 */
	public function update_vector_store( $store_id, $data ) {

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_id', __( 'Store ID is required', 'echo-knowledge-base' ) );
		}

		$update_data = array();
		if ( isset( $data['name'] ) ) {
			$update_data['displayName'] = $data['name'];
		}

		if ( empty( $update_data ) ) {
			return new WP_Error( 'no_data', __( 'Store name is required', 'echo-knowledge-base' ) );
		}

		$resource_name = $this->get_resource_name( $store_id );
		return $this->client->request( '/' . $resource_name, $update_data, 'PATCH', 'file_search_store' );
	}

	/**
	 * Delete a vector store
	 *
	 * @param string $store_id
	 * @return bool|WP_Error
	 */
	public function delete_vector_store( $store_id ) {

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_id', __( 'Store ID is required', 'echo-knowledge-base' ) );
		}

		$resource_name = $this->get_resource_name( $store_id );
		$response = $this->client->request( '/' . $resource_name . '?force=true', array(), 'DELETE', 'file_search_store' );
		if ( is_wp_error( $response ) ) {
			if ( $response->get_error_code() === 'not_found' ) {
				return true;
			}
			return $response;
		}

		return true;
	}


	/************************************************************************************
	 * Manage Documents in File Search Store
	 *
	 * For interface compatibility with ChatGPT Vector Store:
	 * - "file storage" = File Search Store (same thing in Gemini)
	 * - "vector store" = File Search Store (same thing in Gemini)
	 ************************************************************************************/

	/**
	 * Upload file to file storage (uploads directly to specified store)
	 *
	 * In Gemini, there is no separate file storage - files are uploaded directly to stores.
	 *
	 * @param string $id Entity ID (e.g., post ID)
	 * @param string $file_content File content
	 * @param string $file_type File type (e.g., post type)
	 * @param string $store_id Store ID (required for Gemini, ignored by ChatGPT)
	 * @param string $file_extension File extension for the uploaded file
	 * @param string $mime_type MIME type for the uploaded file
	 * @return array|WP_Error File object with 'id' or error
	 */
	public function upload_file_to_file_storage( $id, $file_content, $file_type, $store_id = '', $file_extension = 'txt', $mime_type = 'text/plain' ) {

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_store_id', __( 'Store ID is required for Gemini', 'echo-knowledge-base' ) );
		}

		return $this->upload_document_to_store( $store_id, $id, $file_content, $file_type, $file_extension, $mime_type );
	}

	/**
	 * Upload document directly to a specific store using resumable upload
	 *
	 * @param string $store_id Store ID
	 * @param string $id Entity ID
	 * @param string $file_content Content
	 * @param string $file_type Type
	 * @param string $file_extension File extension for display name
	 * @param string $mime_type MIME type for the uploaded content
	 * @return array|WP_Error
	 */
	public function upload_document_to_store( $store_id, $id, $file_content, $file_type, $file_extension = 'txt', $mime_type = 'text/plain' ) {

		if ( empty( $store_id ) || empty( $file_content ) ) {
			return new WP_Error( 'missing_params', __( 'Store ID and content are required', 'echo-knowledge-base' ) );
		}

		// Build display name
		$safe_type = strpos( $file_type, 'epkb_post_type_' ) === 0 ? 'article' : preg_replace( '/[^a-z0-9_-]/', '_', strtolower( $file_type ) );
		$safe_type = empty( $safe_type ) ? 'article' : $safe_type;
		$display_name = 'kb_' . $safe_type . '_' . $id . '_' . time();
		$safe_extension = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $file_extension ) );
		if ( ! empty( $safe_extension ) ) {
			$display_name .= '.' . $safe_extension;
		}

		$resource_name = $this->get_resource_name( $store_id );
		$upload_endpoint = '/' . $resource_name . ':uploadToFileSearchStore';

		// Use resumable upload protocol (required by Gemini API)
		$metadata = array( 'displayName' => $display_name );
		$response = $this->client->upload_file_resumable( $upload_endpoint, $file_content, $metadata, $mime_type );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Handle long-running operation
		$doc_response = $this->handle_operation_response( $response );
		if ( is_wp_error( $doc_response ) ) {
			return $doc_response;
		}

		// Extract document ID
		$document_id = $this->extract_document_id_from_response( $doc_response );

		return array(
			'id'     => $document_id,
			'name'   => isset( $doc_response['displayName'] ) ? $doc_response['displayName'] : $display_name,
			'status' => isset( $doc_response['state'] ) ? $doc_response['state'] : 'completed'
		);
	}

	/**
	 * Upload a PDF file directly to a store using resumable upload
	 *
	 * @param string $id Entity ID (e.g., unique identifier for the PDF)
	 * @param string $pdf_binary_content Raw PDF binary content
	 * @param string $store_id Store ID (required for Gemini)
	 * @return array|WP_Error File object with 'id' or error
	 */
	public function upload_pdf_to_file_storage( $id, $pdf_binary_content, $store_id = '' ) {

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_store_id', __( 'Store ID is required for Gemini', 'echo-knowledge-base' ) );
		}

		$display_name = 'kb_pdf_' . $id . '_' . time();
		$resource_name = $this->get_resource_name( $store_id );
		$upload_endpoint = '/' . $resource_name . ':uploadToFileSearchStore';

		$metadata = array( 'displayName' => $display_name );
		$response = $this->client->upload_file_resumable( $upload_endpoint, $pdf_binary_content, $metadata, 'application/pdf' );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Handle long-running operation
		$doc_response = $this->handle_operation_response( $response );
		if ( is_wp_error( $doc_response ) ) {
			return $doc_response;
		}

		$document_id = $this->extract_document_id_from_response( $doc_response );

		return array(
			'id'     => $document_id,
			'name'   => isset( $doc_response['displayName'] ) ? $doc_response['displayName'] : $display_name,
			'status' => isset( $doc_response['state'] ) ? $doc_response['state'] : 'completed'
		);
	}

	/**
	 * Add file to vector store
	 *
	 * In Gemini, files are uploaded directly to stores, so this is a verification-only operation.
	 *
	 * @param string $store_id
	 * @param string $file_id Document ID
	 * @param bool $skip_verification
	 * @return array|WP_Error
	 */
	public function add_file_to_vector_store( $store_id, $file_id, $skip_verification = false ) {

		if ( empty( $store_id ) || empty( $file_id ) ) {
			return new WP_Error( 'missing_params', __( 'Store ID and file ID are required', 'echo-knowledge-base' ) );
		}

		// In Gemini, documents are added during upload. Just verify it exists.
		if ( ! $skip_verification ) {
			$details = $this->get_file_details_from_vector_store( $store_id, $file_id );
			if ( is_wp_error( $details ) ) {
				return $details;
			}
			if ( $details === false ) {
				return new WP_Error( 'file_not_found', __( 'Document not found in store', 'echo-knowledge-base' ) );
			}
		}

		return array( 'id' => $file_id, 'status' => 'completed' );
	}

	/**
	 * Remove file from vector store
	 *
	 * @param string $store_id
	 * @param string $file_id Document ID
	 * @return bool|WP_Error
	 */
	public function remove_file_from_vector_store( $store_id, $file_id ) {

		if ( empty( $store_id ) || empty( $file_id ) ) {
			return new WP_Error( 'missing_params', __( 'Store ID and document ID are required', 'echo-knowledge-base' ) );
		}

		$resource_name = $this->get_resource_name( $store_id );
		$doc_resource = $this->get_document_resource_name( $file_id );

		$response = $this->client->request( '/' . $resource_name . '/documents/' . $doc_resource . '?force=true', array(), 'DELETE', 'file_search_store' );
		if ( is_wp_error( $response ) ) {
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
	 * @param string $store_id
	 * @param string $file_id Document ID
	 * @return array|bool|WP_Error Document details, false if not found, or error
	 */
	public function get_file_details_from_vector_store( $store_id, $file_id ) {

		if ( empty( $store_id ) || empty( $file_id ) ) {
			return new WP_Error( 'missing_params', __( 'Store ID and document ID are required', 'echo-knowledge-base' ) );
		}

		$resource_name = $this->get_resource_name( $store_id );
		$doc_resource = $this->get_document_resource_name( $file_id );

		$response = $this->client->request( '/' . $resource_name . '/documents/' . $doc_resource, array(), 'GET', 'file_search_store' );
		if ( is_wp_error( $response ) ) {
			if ( $response->get_error_code() === 'not_found' ) {
				return false;
			}
			return $response;
		}

		return $response;
	}

	/**
	 * List all document IDs in a file search store
	 *
	 * @param string $store_id Store ID
	 * @return array|WP_Error Array of document ID strings or error
	 */
	public function list_vector_store_file_ids( $store_id ) {

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_id', __( 'Store ID is required', 'echo-knowledge-base' ) );
		}

		$resource_name = $this->get_resource_name( $store_id );
		$doc_ids = array();
		$page_token = '';
		$seen_page_tokens = array();

		while ( true ) {
			$params = array( 'pageSize' => 20 );
			if ( ! empty( $page_token ) ) {
				$params['pageToken'] = $page_token;
			}

			$response = $this->client->request( '/' . $resource_name . '/documents', $params, 'GET', 'file_search_store' );
			if ( is_wp_error( $response ) ) {
				return $response;
			}

			if ( empty( $response['documents'] ) || ! is_array( $response['documents'] ) ) {
				break;
			}

			foreach ( $response['documents'] as $doc ) {
				$doc_id = $this->extract_document_id_from_response( $doc );
				if ( ! empty( $doc_id ) ) {
					$doc_ids[] = $doc_id;
				}
			}

			// Check if there are more pages
			if ( empty( $response['nextPageToken'] ) ) {
				break;
			}

			$next_page_token = (string) $response['nextPageToken'];
			if ( isset( $seen_page_tokens[ $next_page_token ] ) ) {
				return new WP_Error( 'invalid_pagination_cursor', __( 'Vector store pagination cursor repeated while listing files', 'echo-knowledge-base' ) );
			}

			$seen_page_tokens[ $next_page_token ] = true;
			$page_token = $next_page_token;
		}

		return $doc_ids;
	}

	/**
	 * Get file details from file storage
	 *
	 * In Gemini, "file storage" is the file search store, so this proxies to the document lookup.
	 *
	 * @param string $file_id Document ID
	 * @param string $store_id Store ID
	 * @return array|bool|WP_Error Document details, false if not found, or error
	 */
	public function get_file_details_from_file_storage( $file_id, $store_id = '' ) {

		if ( empty( $file_id ) ) {
			return new WP_Error( 'missing_id', __( 'File ID is required', 'echo-knowledge-base' ) );
		}

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_store_id', __( 'Store ID is required for Gemini', 'echo-knowledge-base' ) );
		}

		return $this->get_file_details_from_vector_store( $store_id, $file_id );
	}

	/**
	 * Verify file exists in file storage
	 *
	 * In Gemini, "file storage" IS the store.
	 *
	 * @param string $file_id Document ID
	 * @param string $store_id Store ID (required for Gemini, ignored by ChatGPT)
	 * @return bool|WP_Error True if exists, false if not, WP_Error on other errors
	 */
	public function verify_file_exists_in_file_storage( $file_id, $store_id = '' ) {

		if ( empty( $file_id ) ) {
			return new WP_Error( 'missing_id', __( 'File ID is required', 'echo-knowledge-base' ) );
		}

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_store_id', __( 'Store ID is required for Gemini', 'echo-knowledge-base' ) );
		}

		$result = $this->get_file_details_from_file_storage( $file_id, $store_id );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $result !== false;
	}

	/**
	 * Delete file from file storage
	 *
	 * In Gemini, deletes from the specified store.
	 *
	 * @param string $file_id Document ID
	 * @param string $store_id Store ID (required for Gemini, ignored by ChatGPT)
	 * @return bool|WP_Error
	 */
	public function delete_file_from_file_storage( $file_id, $store_id = '' ) {

		if ( empty( $file_id ) ) {
			return new WP_Error( 'missing_id', __( 'File ID is required', 'echo-knowledge-base' ) );
		}

		if ( empty( $store_id ) ) {
			return new WP_Error( 'missing_store_id', __( 'Store ID is required for Gemini', 'echo-knowledge-base' ) );
		}

		return $this->remove_file_from_vector_store( $store_id, $file_id );
	}


	/************************************************************************************
	 * Helper Methods
	 ************************************************************************************/

	/**
	 * Get full resource name for API calls
	 *
	 * @param string $store_id
	 * @return string
	 */
	private function get_resource_name( $store_id ) {
		// If already a full resource name, return as-is
		if ( strpos( $store_id, 'fileSearchStores/' ) === 0 ) {
			return $store_id;
		}
		return 'fileSearchStores/' . $store_id;
	}

	/**
	 * Get document resource name (just the ID part)
	 *
	 * @param string $doc_id
	 * @return string
	 */
	private function get_document_resource_name( $doc_id ) {
		// Extract just the document ID if it's a full path
		if ( strpos( $doc_id, '/documents/' ) !== false ) {
			$parts = explode( '/documents/', $doc_id );
			return end( $parts );
		}
		return $doc_id;
	}

	/**
	 * Extract store ID from API response
	 *
	 * @param array $response
	 * @return string
	 */
	private function extract_store_id_from_response( $response ) {
		if ( isset( $response['name'] ) ) {
			// Format: fileSearchStores/xxx
			$parts = explode( '/', $response['name'] );
			return end( $parts );
		}
		return isset( $response['id'] ) ? $response['id'] : '';
	}

	/**
	 * Extract document ID from API response
	 *
	 * @param array $response
	 * @return string
	 */
	private function extract_document_id_from_response( $response ) {
		if ( isset( $response['name'] ) ) {
			// Format: fileSearchStores/xxx/documents/yyy
			$parts = explode( '/', $response['name'] );
			return end( $parts );
		}
		return isset( $response['id'] ) ? $response['id'] : '';
	}

	/**
	 * Handle long-running operation response
	 *
	 * @param array $response
	 * @return array|WP_Error
	 */
	private function handle_operation_response( $response ) {
		// If done immediately
		if ( isset( $response['done'] ) && $response['done'] === true ) {
			if ( isset( $response['error'] ) ) {
				return new WP_Error( 'operation_failed', isset( $response['error']['message'] ) ? $response['error']['message'] : __( 'Operation failed', 'echo-knowledge-base' ) );
			}
			return isset( $response['response'] ) ? $response['response'] : $response;
		}

		// If it's an operation that needs polling
		if ( isset( $response['name'] ) && strpos( $response['name'], 'operations/' ) === 0 ) {
			return $this->poll_operation( $response['name'] );
		}

		// Direct response (not an operation)
		return $response;
	}

	/**
	 * Poll operation until complete
	 *
	 * @param string $operation_name
	 * @param int $max_attempts
	 * @return array|WP_Error
	 */
	private function poll_operation( $operation_name, $max_attempts = 30 ) {

		for ( $i = 0; $i < $max_attempts; $i++ ) {
			$response = $this->client->request( '/' . $operation_name, array(), 'GET', 'operation_poll' );
			if ( is_wp_error( $response ) ) {
				return $response;
			}

			if ( isset( $response['done'] ) && $response['done'] === true ) {
				if ( isset( $response['error'] ) ) {
					return new WP_Error( 'operation_failed', isset( $response['error']['message'] ) ? $response['error']['message'] : __( 'Operation failed', 'echo-knowledge-base' ) );
				}
				return isset( $response['response'] ) ? $response['response'] : $response;
			}

			// Exponential backoff
			EPKB_AI_Utilities::safe_sleep( min( 2 * ( $i + 1 ), 10 ) );
		}

		return new WP_Error( 'operation_timeout', __( 'Operation timed out', 'echo-knowledge-base' ) );
	}
}
