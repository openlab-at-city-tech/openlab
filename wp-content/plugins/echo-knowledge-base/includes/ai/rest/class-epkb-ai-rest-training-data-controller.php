<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * REST API Controller for AI Sync operations
 * 
 * Provides endpoints for managing training data synchronization.
 */
class EPKB_AI_REST_Training_Data_Controller extends EPKB_AI_REST_Base_Controller {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Register routes
	 */
	public function register_routes() {

		// Get sync status
		register_rest_route( $this->admin_namespace, '/sync/status', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_sync_status' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'type' => 'integer',
					'required' => true
				)
			)
		) );

		/** MANAGE ROWS OF TRAINING DATA */

		// Get training data rows
		register_rest_route( $this->admin_namespace, '/training-data', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_training_data_rows'),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'type' => 'integer',
					'required' => true
				),
				'status' => array(
					'type' => 'string',
					'enum' => array( '', 'pending', 'adding', 'added', 'updating', 'updated', 'outdated', 'error', 'skipped' )
				),
				'type' => array(
					'type' => 'string'
				),
				'page' => array(
					'type' => 'integer',
					'default' => 1,
					'minimum' => 1
				),
				'per_page' => array(
					'type' => 'integer',
					'default' => 50,
					'minimum' => 1,
					'maximum' => 100
				),
				'search' => array(
					'type' => 'string'
				),
				'reconcile' => array(
					'type' => 'boolean',
					'default' => false
				)
			)
		) );

		// Delete selected training data rows
		register_rest_route( $this->admin_namespace, '/training-data/delete-selected', array(
			'methods'             => WP_REST_Server::DELETABLE,
			'callback'            => array( $this, 'delete_training_data_rows'),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'ids' => array(
					'required' => true,
					'type' => 'array',
					'items' => array( 'type' => 'integer' )
				)
			)
		) );

		// Get training data content
		register_rest_route( $this->admin_namespace, '/training-data/(?P<id>\d+)/content', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_training_data_content'),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'id' => array(
					'required' => true,
					'type' => 'integer'
				)
			)
		) );

		/** MANAGE COLLECTIONS OF TRAINING DATA */

		// Get training data collections
		register_rest_route( $this->admin_namespace, '/training-collections', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_training_collections' ),
			'permission_callback' => array( $this, 'check_admin_permission' )
		) );
		
		// Create training data collection
		register_rest_route( $this->admin_namespace, '/training-collections', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'create_training_collection' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'name' => array(
					'required' => true,
					'type' => 'string',
					'minLength' => 3,
					'maxLength' => 80,
					'sanitize_callback' => 'sanitize_text_field'
				),
				'post_types' => array(
					'type' => 'array',
					'items' => array( 'type' => 'string' ),
					'default' => array()
				)
			)
		) );
		
		// Update training data collection
		register_rest_route( $this->admin_namespace, '/training-collections/(?P<collection_id>\d+)', array(
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => array( $this, 'update_training_collection' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'required' => true,
					'type' => 'integer'
				),
				'name' => array(
					'type' => 'string',
					'minLength' => 3,
					'maxLength' => 80,
					'sanitize_callback' => 'sanitize_text_field'
				),
				'post_types' => array(
					'type' => 'array',
					'items' => array( 'type' => 'string' )
				)
			)
		) );
		
		// Delete training data collection
		register_rest_route( $this->admin_namespace, '/training-collections/(?P<collection_id>\d+)', array(
			'methods'             => WP_REST_Server::DELETABLE,
			'callback'            => array( $this, 'delete_training_collection' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'required' => true,
					'type' => 'integer'
				)
			)
		) );
		
		// Get collection post stats (for Add Training Data section)
		register_rest_route( $this->admin_namespace, '/training-collections/(?P<collection_id>\d+)/post-stats', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_collection_post_stats' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'required' => true,
					'type' => 'integer'
				)
			)
		) );

		// Get eligible individual items for a collection and post type
		register_rest_route( $this->admin_namespace, '/training-collections/(?P<collection_id>\d+)/eligible-items', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_collection_eligible_items' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'required' => true,
					'type' => 'integer'
				),
				'data_type' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_key'
				)
			)
		) );
		
		// Add data to training data collection
		register_rest_route( $this->admin_namespace, '/training-collections/(?P<collection_id>\d+)/add-data', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'add_data_to_collection' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'required' => true,
					'type' => 'integer'
				),
				'data_types' => array(
					'type' => 'array',
					'items' => array( 'type' => 'string' ),
					'default' => array()
				),
				'data_type' => array(
					'type' => 'string',
					'sanitize_callback' => 'sanitize_key'
				),
				'item_ids' => array(
					'type' => 'array',
					'items' => array( 'type' => 'integer' ),
					'default' => array()
				)
			)
		) );
		
		// Toggle summary mode for training data collection - Disabled for now as we will not use it
		// register_rest_route( $this->admin_namespace, '/training-collections/(?P<collection_id>\d+)/toggle-summary', array(
		// 	'methods'             => WP_REST_Server::CREATABLE,
		// 	'callback'            => array( $this, 'toggle_summary_mode' ),
		// 	'permission_callback' => array( $this, 'check_admin_permission' ),
		// 	'args'                => array(
		// 		'collection_id' => array(
		// 			'required' => true,
		// 			'type' => 'integer'
		// 		),
		// 		'use_summary' => array(
		// 			'required' => true,
		// 			'type' => 'boolean'
		// 		)
		// 	)
		// ) );
		
		/** PDF UPLOAD TO VECTOR STORE */

		// Upload PDF directly to AI vector store
		register_rest_route( $this->admin_namespace, '/training-data/upload-pdf', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'upload_pdf_to_vector_store' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'required' => true,
					'type' => 'integer'
				),
				'file_name' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_file_name'
				),
				'file_data' => array(
					'required' => true,
					'type' => 'string'
				),
				'attachment_url' => array(
					'type' => 'string',
					'sanitize_callback' => 'esc_url_raw'
				),
				'upload_mode' => array(
					'type' => 'string',
					'enum' => array( 'raw_pdf', 'extract_pdf_text' ),
					'default' => 'raw_pdf'
				),
				'raw_text' => array(
					'type' => 'string'
				)
			)
		) );

		/** TRAINING NOTES (AI FEATURES PRO) */
		
		// Create training note
		register_rest_route( $this->admin_namespace, '/training-notes', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'create_training_note' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'collection_id' => array(
					'required' => true,
					'type' => 'integer'
				),
				'title' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field'
				),
				'content' => array(
					'type' => 'string',
					'sanitize_callback' => 'wp_kses_post'
				),
				'note_type' => array(
					'type' => 'string',
					'enum' => array( 'text', 'pdf' ),
					'default' => 'text'
				),
				'original_file_name' => array(
					'type' => 'string',
					'sanitize_callback' => 'sanitize_file_name'
				),
				'conversion_time' => array(
					'type' => 'integer'
				),
				'format_mode' => array(
					'type' => 'string',
					'enum' => array( 'none', 'basic', 'ai' ),
					'default' => 'none'
				),
				'pdf_base64' => array(
					'type' => 'string'
				),
				'file_name' => array(
					'type' => 'string',
					'sanitize_callback' => 'sanitize_file_name'
				)
			)
		) );

		// Update training note
		register_rest_route( $this->admin_namespace, '/training-notes', array(
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => array( $this, 'update_training_note' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'note_id' => array(
					'required' => true,
					'type' => 'integer'
				),
				'title' => array(
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field'
				),
				'content' => array(
					'type' => 'string',
					'sanitize_callback' => 'wp_kses_post'
				),
				'training_id' => array(
					'type' => 'integer',
					'required' => true
				)
			)
		) );
		
		// Delete training note
		register_rest_route( $this->admin_namespace, '/training-notes/(?P<note_id>\d+)', array(
			'methods'             => WP_REST_Server::DELETABLE,
			'callback'            => array( $this, 'delete_training_note' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
			'args'                => array(
				'note_id' => array(
					'required' => true,
					'type' => 'integer'
				),
				'collection_id' => array(
					'type' => 'integer'
				)
			)
		) );

		// Dismiss the Validate & Fix recommendation notice
		register_rest_route( $this->admin_namespace, '/training-data/dismiss-validate-fix-notice', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'dismiss_validate_fix_notice' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );
	}

	/**
	 * Persist the user's dismissal of the Validate & Fix recommendation notice.
	 *
	 * @return WP_REST_Response
	 */
	public function dismiss_validate_fix_notice() {
		EPKB_Core_Utilities::add_kb_flag( 'ai_validate_fix_notice_dismissed' );
		return $this->create_rest_response( array( 'success' => true ) );
	}

	/**
	 * Get sync status
	 *
	 * Returns comprehensive sync status information including progress tracking,
	 * health checks, and recent sync history. This uses the get_status() method
	 * which provides full status details, not just basic statistics.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_sync_status( $request ) {

		$collection_id = $request->get_param( 'collection_id' );

		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}

		// Get database statistics
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$db_stats = $training_data_db->get_status_statistics( $collection_id );
		
		// Get sync job status from new job manager
		$job = EPKB_AI_Sync_Job_Manager::get_sync_job();
		
		// Build simple status response
		$status = array(
			'is_running' => EPKB_AI_Sync_Job_Manager::is_job_active(),
			'database' => $db_stats,
			'progress' => array(
				'percentage' => $job['percent'],
				'phase' => $job['status'],
				'processed' => $job['processed'],
				'total' => $job['total'],
				'errors' => $job['errors']
			)
		);

		return $this->create_rest_response( array( 'success' => true, 'status' => $status ) );
	}

	/**********************************************************************
	 * Training Data Rows
	 **********************************************************************/

	/**
	 * Get training data list
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_training_data_rows( $request ) {

		$collection_id = $request->get_param( 'collection_id' );

		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}
		$status = $request->get_param( 'status' );
		$type = $request->get_param( 'type' );
		$page = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$search = $request->get_param( 'search' );
		$reconcile = $request->get_param( 'reconcile' );

		if ( is_string( $type ) && strpos( $type, ',' ) !== false ) {
			$type = array_map( 'trim', explode( ',', $type ) );
		}

		if ( is_array( $type ) ) {
			$type = array_filter( array_map( 'sanitize_key', $type ) );
		} elseif ( ! empty( $type ) ) {
			$type = sanitize_key( $type );
		}

		// Build query args
		$args = array(
			'collection_id' => $collection_id,
			'page' => $page,
			'per_page' => $per_page,
			'orderby' => 'updated',
			'order' => 'DESC'
		);

		if ( ! empty( $status ) ) {
			$args['status'] = $status;
		}

		if ( ! empty( $type ) ) {
			$args['type'] = $type;
		}

		if ( ! empty( $search ) ) {
			$args['search'] = $search;
		}

		// Get training data from database
		$training_data_db = new EPKB_AI_Training_Data_DB();
		if ( ! empty( $reconcile ) ) {
			EPKB_AI_Utilities::reconcile_collection_source_updates( $collection_id, $training_data_db );
		}
		$data = $training_data_db->get_training_data_list( $args );
		if ( is_wp_error( $data ) ) {
			return $this->create_rest_response( [], 500, $data );
		}

		// Add display metadata needed by the admin table.
		foreach ( $data as &$item ) {
			$item = EPKB_AI_Utilities::prepare_training_data_item_for_display( $item );
		}

		$total = $training_data_db->get_training_data_count( $args );

		// Calculate pagination
		$total_pages = ceil( $total / $per_page );

		// Get total status counts for the collection
		$status_stats = $training_data_db->get_status_statistics( $collection_id );

		// Get all available types for the current status filter
		$available_types = $training_data_db->get_collection_types( $collection_id, $status );

		return $this->create_rest_response( array( 'success' => true, 'data' => $data, 'pagination' => array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => $total,
			'total_pages' => $total_pages
		),
		'status_counts' => $status_stats,
		'available_types' => $available_types
		) );
	}

	/**********************************************************************
	 * Training Data Collections Management
	 **********************************************************************/

	/**
	 * Get training data collections
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_training_collections( $request ) {
		
		$collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections();
		if ( is_wp_error( $collections ) ) {
			return $this->create_rest_response( [], 500, $collections );
		}
		$formatted_collections = array();
		
		$training_data_db = new EPKB_AI_Training_Data_DB();
		
		foreach ( $collections as $collection_id => $collection_config ) {
			// Get database stats for this collection
			$db_stats = $training_data_db->get_status_statistics( $collection_id );
			$last_sync_date = $training_data_db->get_last_sync_date( $collection_id );
			
			$formatted_collections[] = array(
				'id' => $collection_id,
				'name' => empty( $collection_config['ai_training_data_store_name'] ) ? EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id ) : $collection_config['ai_training_data_store_name'],
				'post_types' => $collection_config['ai_training_data_store_post_types'],
				'item_count' => isset( $db_stats['synced'] ) ? $db_stats['synced'] : 0,
				'last_synced' => $last_sync_date
			);
		}
		
		return $this->create_rest_response( array( 'success' => true, 'collections' => $formatted_collections ) );
	}
	
	/**
	 * Create training data collection
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function create_training_collection( $request ) {
		
		$name = $request->get_param( 'name' );
		$post_types = $request->get_param( 'post_types' );
		
		// Get next collection ID
		$collection_id = EPKB_AI_Training_Data_Config_Specs::get_next_collection_id();
		if ( is_wp_error( $collection_id ) ) {
			return $this->create_rest_response( [], 500, $collection_id );
		}
		
		// Clear any potential existing cache for this collection ID (in case of ID reuse)
		delete_transient( 'epkb_sync_progress_' . $collection_id );
		
		// Create collection config - start with empty post types
		$collection_config = array(
			'ai_training_data_store_name' => empty( $name ) ? EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id ) : $name,
			'ai_training_data_store_id' => '',
			'ai_training_data_provider' => EPKB_AI_Provider::get_active_provider(),
			'ai_training_data_store_post_types' => array() // Start empty - user will add data types later
		);
		
		// Save the collection
		$saved = EPKB_AI_Training_Data_Config_Specs::update_training_data_collection( $collection_id, $collection_config );
		
		if ( is_wp_error( $saved ) ) {
			return $this->create_rest_response( [], 400, $saved );
		}
		
		if ( ! $saved ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'save_failed', __( 'Failed to save training data collection', 'echo-knowledge-base' ) ) );
		}
		
		return $this->create_rest_response( array( 'success' => true, 'collection_id' => $collection_id, 'message' => __( 'Training data collection created successfully', 'echo-knowledge-base' ) ) );
	}
	
	/**
	 * Update training data collection
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function update_training_collection( $request ) {
		
		$collection_id = $request->get_param( 'collection_id' );
		$name = $request->get_param( 'name' );
		$post_types = $request->get_param( 'post_types' );
		
		// Get existing collection
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}
		
		// Store the old name to check if it changed
		$old_name = $collection_config['ai_training_data_store_name'];
		
		// Update fields
		if ( $name !== null ) {
			$collection_config['ai_training_data_store_name'] = $name;
		}
		if ( $post_types !== null ) {
			$collection_config['ai_training_data_store_post_types'] = $post_types;
		}
		
		// Save the collection
		$saved = EPKB_AI_Training_Data_Config_Specs::update_training_data_collection( $collection_id, $collection_config );
		
		if ( is_wp_error( $saved ) ) {
			return $this->create_rest_response( [], 400, $saved );
		}
		
		if ( ! $saved ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'save_failed', __( 'Failed to update training data collection', 'echo-knowledge-base' ) ) );
		}
		
		// If the name changed and there's a vector store, update its name too
		if ( $name !== null && $name !== $old_name && ! empty( $collection_config['ai_training_data_store_id'] ) ) {

			$vector_store_handler = EPKB_AI_Provider::get_vector_store_handler();
			$update_result = $vector_store_handler->update_vector_store( $collection_config['ai_training_data_store_id'], array( 'name' => $name ) );
			if ( is_wp_error( $update_result ) ) {
				// Log the error but don't fail the entire operation
				EPKB_AI_Log::add_log( $update_result, array( 'collection_id' => $collection_id, 'vector_store_id' => $collection_config['ai_training_data_store_id'],
										'new_name' => $name, 'message' => 'Failed to update vector store name' ) );
			}
		}
		
		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Training data collection updated successfully', 'echo-knowledge-base' )	) );
	}

	/**
	 * Delete training data rows user selected (REST endpoint).
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function delete_training_data_rows( $request ) {

		$ids = $request->get_param( 'ids' );

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'invalid_ids', __( 'No IDs provided', 'echo-knowledge-base' ) ) );
		}

		// Use internal method to delete
		$results = $this->delete_training_data_by_ids( $ids );

		// translators: %1$d is the number of deleted items, %2$d is the number of failed items
		$message = sprintf( __( 'Deleted %1$d items, %2$d failed', 'echo-knowledge-base' ), $results['deleted'], $results['failed'] );
		if ( $results['vector_store_errors'] > 0 ) {
			$message .= ' ' . __( 'vector store errors:', 'echo-knowledge-base' ) . ' ' . $results['vector_store_errors'];
		}

		return $this->create_rest_response( array(
			'success' => true,
			'deleted' => $results['deleted'],
			'failed' => $results['failed'],
			'vector_store_errors' => $results['vector_store_errors'],
			'message' => $message
		) );
	}

	/**
	 * Get training data content
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_training_data_content( $request ) {
		
		$id = $request->get_param( 'id' );
		
		// Get the training data record
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$training_data = $training_data_db->get_training_data_row_by_id( $id );
		if ( is_wp_error( $training_data ) ) {
			return $this->create_rest_response( [], 500, $training_data, array( 'training_data_id' => $id ) );
		}
		if ( empty( $training_data ) ) {
			return $this->create_rest_response( [], 404, new WP_Error( 'not_found', __( 'Training data not found', 'echo-knowledge-base' ), array( 'training_data_id' => $id ) ) );
		}

		if ( in_array( $training_data->type, array( 'PDF', 'HTML' ), true ) ) {
			$original_url = ! empty( $training_data->url ) ? esc_url_raw( $training_data->url ) : '';

			return $this->create_rest_response( array(
				'success' => true,
				'data' => array(
					'title'        => $training_data->title,
					'content_type' => 'uploaded_file',
					'status'       => $training_data->status,
					'last_synced'  => $training_data->last_synced,
					'upload_info'  => array(
						'file_name'        => $training_data->title,
						'original_url'     => $original_url,
						'has_original_url' => ! empty( $original_url ),
						'provider'         => $training_data->provider,
						'file_id'          => $training_data->file_id,
						'created'          => $training_data->created,
						'storage_type'     => $training_data->type,
					),
				),
			) );
		}

		if ( ! EPKB_AI_Utilities::is_post_backed_training_data_type( $training_data->type ) ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'content_not_available', __( 'Content preview is not available for this training data source.', 'echo-knowledge-base' ), array( 'training_data_id' => $id ) ) );
		}

		// Regular post handling
		$post_id = intval( $training_data->item_id );
		$post = get_post( $post_id );
		if ( ! $post ) {
			$training_data_db->mark_as_skipped( $id, 404, 'post_not_found' );
			return $this->create_rest_response( [], 404, new WP_Error( 'post_not_found', __( 'Original post not found. It may have been deleted.', 'echo-knowledge-base' ), array( 'training_data_id' => $id, 'post_id' => $post_id ) ) );
		}

		// Process the content for display
		$content_processor = new EPKB_AI_Content_Processor();
		$prepared_content = $content_processor->prepare_post( $post );
		if ( is_wp_error( $prepared_content ) ) {
			$error_code = $prepared_content->get_error_code();

			// For empty content errors, return original HTML so admin can inspect it
			if ( in_array( $error_code, array( 'empty_markdown', 'empty_content' ), true ) ) {
				$training_data_db->mark_as_skipped( $id, 500, $error_code );
				return $this->create_rest_response( array(
					'success' => true,
					'data' => array(
						'title' => $post->post_title,
						'doc_content' => $post->post_content,
						'processed_content' => '',
						'error_type' => $error_code,
						'error_message' => $prepared_content->get_error_message(),
						'url' => get_permalink( $post_id ),
						'post_type' => $post->post_type,
						'status' => $training_data->status,
						'item_id' => $training_data->item_id,
						'user_email' => wp_get_current_user()->user_email
					)
				) );
			}

			$training_data_db->mark_as_error( $id, $error_code === 'post_not_published' ? 404 : 500, $prepared_content->get_error_message() );
			return $this->create_rest_response( [], 500, $prepared_content );
		}

		return $this->create_rest_response( array(
			'success' => true,
			'data' => array(
				'title' => $post->post_title,
				'doc_content' => $post->post_content,
				'processed_content' => $prepared_content['content'],
				'metadata' => $prepared_content['metadata'],
				'url' => get_permalink( $post_id ),
				'post_type' => $post->post_type,
				'last_modified' => $post->post_modified,
				'status' => $training_data->status,
				'last_synced' => $training_data->last_synced,
				'item_id' => $training_data->item_id // Add the post ID for retrieving cached content
			)
		) );
	}

	/**
	 * Delete training data collection
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function delete_training_collection( $request ) {
		
		$collection_id = $request->get_param( 'collection_id' );
		
		// Initialize components
		$training_data_db = new EPKB_AI_Training_Data_DB();
		
		// Get the collection configuration to retrieve the vector store ID
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}
		
		// Store the vector store ID before deleting the collection
		$vector_store_id = isset( $collection_config['ai_training_data_store_id'] ) ? $collection_config['ai_training_data_store_id'] : '';
		
		// First, get all training data for this collection
		$training_data = $training_data_db->get_training_data_by_collection( $collection_id );
		
		// If there's data, delete it first
		if ( ! empty( $training_data ) ) {
			// Extract IDs from the training data records
			$ids = array_map( function( $record ) {
				return $record->id;
			}, $training_data );
			
			// Delete all training data records and their Vector Store files
			$delete_result = $this->delete_training_data_by_ids( $ids );
			if ( is_wp_error( $delete_result ) ) {
				return $this->create_rest_response( [], 400, $delete_result );
			}
		}
		
		// Delete the vector store if it exists
		if ( ! empty( $vector_store_id ) ) {
			$vector_store_handler = EPKB_AI_Provider::get_vector_store_handler();
			$vector_store_delete_result = $vector_store_handler->delete_vector_store( $vector_store_id );
			if ( is_wp_error( $vector_store_delete_result ) ) {
				// Log the error but don't fail the entire operation
				EPKB_AI_Log::add_log( $vector_store_delete_result, array( 'collection_id' => $collection_id, 'vector_store_id' => $vector_store_id, 
										'message' => 'Failed to delete vector store during collection deletion' ) );
			} 
		}
		
		// Clear any sync progress for this collection
		delete_transient( 'epkb_sync_progress_' . $collection_id );
		
		// Delete the collection (this will also reset kb_ai_collection_id in all KB configs using this collection)
		$result = EPKB_AI_Training_Data_Config_Specs::delete_training_data_collection( $collection_id );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 400, $result );
		}

		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Collection and associated vector store deleted successfully.', 'echo-knowledge-base' ) ) );
	}

	/**
	 * Delete training data rows by IDs (internal method)
	 *
	 * @param array $ids Array of training data record IDs to delete
	 * @return array Results array with deleted count, failed count, and vector store errors
	 */
	private function delete_training_data_by_ids( $ids ) {

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return array( 'deleted' => 0, 'failed' => 0, 'vector_store_errors' => 0 );
		}

		$training_data_db = new EPKB_AI_Training_Data_DB();
		$deleted = 0;
		$failed = 0;
		$vector_store_errors = 0;

		foreach ( $ids as $id ) {
			// Get the training data record before deleting to get file IDs
			$record = $training_data_db->get_training_data_row_by_id( $id );

			if ( $record ) {
				$provider_cleanup = $this->cleanup_training_data_record_from_provider( $record, 'training_data_delete' );
				if ( is_wp_error( $provider_cleanup ) ) {
					$vector_store_errors++;
				}

				// Delete the WordPress post for AI Notes
				if ( $record->type === EPKB_AI_Utilities::AI_PRO_NOTES_POST_TYPE && ! empty( $record->item_id ) ) {
					$delete_callable = array( 'AIPRO_AI_Notes', 'delete_ai_note' ); /* @disregard PREFIX */
					if ( EPKB_Utilities::is_ai_features_pro_enabled() && class_exists( 'AIPRO_AI_Notes' ) && is_callable( $delete_callable ) ) { /* @disregard PREFIX */
						call_user_func( $delete_callable, $record->item_id, true );
					}
				}
			}

			// Delete from database
			$result = $training_data_db->delete_training_data_record( $id );
			if ( is_wp_error( $result ) ) {
				$failed++;
			} else {
				$deleted++;
			}
		}

		return array( 'deleted' => $deleted, 'failed' => $failed, 'vector_store_errors' => $vector_store_errors );
	}

	/**
	 * Add data to training data collection
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function add_data_to_collection( $request ) {
		
		$collection_id = absint( $request->get_param( 'collection_id' ) );
		$data_type = sanitize_key( (string) $request->get_param( 'data_type' ) );
		$data_types = array_values( array_unique( array_filter( array_map( 'sanitize_key', (array) $request->get_param( 'data_types' ) ) ) ) );
		$item_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $request->get_param( 'item_ids' ) ) ) ) );
		
		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}
		
		// Initialize counters
		$total_added = 0;

		// Determine which post types are allowed (UI and API gate)
		$available_post_types = array_keys( EPKB_AI_Utilities::get_available_post_types_for_ai() );

		// Add only the user-selected individual items for a single type.
		if ( ! empty( $item_ids ) ) {
			if ( empty( $data_type ) ) {
				return $this->create_rest_response( [], 400, new WP_Error( 'missing_post_type', __( 'Please select one content type before adding individual items.', 'echo-knowledge-base' ) ) );
			}

			if ( ! in_array( $data_type, $available_post_types, true ) ) {
				return $this->create_rest_response( [], 400, new WP_Error( 'invalid_post_type', __( 'The selected content type is not available for AI training.', 'echo-knowledge-base' ) ) );
			}

			$result = $this->add_selected_posts_to_collection( $collection_id, $data_type, $item_ids );
			if ( is_wp_error( $result ) ) {
				return $this->create_rest_response( [], 400, $result );
			}

			$total_added = $result;
			if ( $total_added > 0 ) {
				return $this->create_rest_response( array(
					'success' => true,
					'message' => sprintf(
						_n( 'Successfully added %d selected item to the collection', 'Successfully added %d selected items to the collection', $total_added, 'echo-knowledge-base' ),
						$total_added
					),
					'items_added' => $total_added
				) );
			}

			return $this->create_rest_response( [], 400, new WP_Error( 'no_items_added', __( 'The selected items are already in the collection or are no longer available.', 'echo-knowledge-base' ) ) );
		}

		if ( empty( $data_types ) ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'missing_post_types', __( 'Please select at least one content type to add.', 'echo-knowledge-base' ) ) );
		}
		
		// Update collection configuration to include the selected post types FIRST (merge with existing)
		$existing_post_types = isset( $collection_config['ai_training_data_store_post_types'] ) ? $collection_config['ai_training_data_store_post_types'] : array();
		$collection_config['ai_training_data_store_post_types'] = array_values( array_unique( array_merge( $existing_post_types, $data_types ) ) );
		$update_result = EPKB_AI_Training_Data_Config_Specs::update_training_data_collection( $collection_id, $collection_config );
		if ( is_wp_error( $update_result ) ) {
			return $this->create_rest_response( [], 400, $update_result );
		}
		
		// Process each data type
		foreach ( $data_types as $data_type ) {
			// Security: skip types not available
			if ( ! in_array( $data_type, $available_post_types, true ) ) {
				continue;
			}

			// Default handling: treat $data_type as a WP post type slug and add its posts
			$result = $this->add_posts_to_collection( $collection_id, $data_type );
			if ( is_wp_error( $result ) ) {
				return $this->create_rest_response( [], 400, $result );
			} else {
				$total_added += $result;
			}
		}
		
		// Prepare response message
		if ( $total_added > 0 ) {
			// translators: %d is the number of items added
			return $this->create_rest_response( array( 'success' => true, 'message' => sprintf( __( 'Successfully added %d new items to the collection', 'echo-knowledge-base' ), $total_added ), 'items_added' => $total_added ) );
		}

		// Nothing added; determine a more precise message for selected types
		$training_data_db = new EPKB_AI_Training_Data_DB( true );
		$existing_for_selected = 0;
		foreach ( (array) $data_types as $selected_type ) {
			$existing_for_selected += $training_data_db->get_training_data_count( array( 'collection_id' => $collection_id, 'type' => $selected_type ) );
		}

		if ( $existing_for_selected > 0 ) {
			return $this->create_rest_response( array( 'success' => true, 'message' => __( 'All selected content types are already in the collection. No new items were found to add.', 'echo-knowledge-base' ), 'items_added' => 0 ) );
		}

		return $this->create_rest_response( [], 400, new WP_Error( 'no_data_added', __( 'No published items were found for the selected content types.', 'echo-knowledge-base' ) ) );
	}

	/**
	 * Get eligible individual items for one content type in a collection.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_collection_eligible_items( $request ) {

		$collection_id = absint( $request->get_param( 'collection_id' ) );
		$data_type = sanitize_key( (string) $request->get_param( 'data_type' ) );

		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}

		$available_post_types = EPKB_AI_Utilities::get_available_post_types_for_ai();
		if ( empty( $data_type ) || ! isset( $available_post_types[ $data_type ] ) ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'invalid_post_type', __( 'The selected content type is not available for AI training.', 'echo-knowledge-base' ) ) );
		}

		$posts_data = $this->get_eligible_posts_for_collection( $collection_id, $data_type );
		if ( is_wp_error( $posts_data ) ) {
			return $this->create_rest_response( [], 400, $posts_data );
		}

		$filter_taxonomy = $this->get_category_filter_taxonomy_for_post_type( $data_type );
		$categories = array();
		$items = array();

			foreach ( $posts_data['eligible_posts'] as $post ) {
				$title = EPKB_AI_Validation::validate_title( get_the_title( $post ) );
				if ( $title === '' ) {
					$title = __( 'Untitled', 'echo-knowledge-base' ) . ' ' . $post->ID;
				}

			$item_category_ids = array();
			$item_category_names = array();

			if ( ! empty( $filter_taxonomy ) ) {
				$post_terms = get_the_terms( $post->ID, $filter_taxonomy->name );
				if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {
					foreach ( $post_terms as $term ) {
						$item_category_ids[] = (int) $term->term_id;
						$item_category_names[] = $term->name;
						$categories[ $term->term_id ] = array(
							'id' => (int) $term->term_id,
							'name' => $term->name
						);
					}
				}
			}

			sort( $item_category_ids, SORT_NUMERIC );
			natcasesort( $item_category_names );

			$items[] = array(
				'id' => (int) $post->ID,
				'title' => $title,
				'category_ids' => $item_category_ids,
				'category_names' => array_values( $item_category_names )
			);
		}

		usort( $items, function( $left, $right ) {
			return strcasecmp( $left['title'], $right['title'] );
		} );

		if ( ! empty( $categories ) ) {
			uasort( $categories, function( $left, $right ) {
				return strcasecmp( $left['name'], $right['name'] );
			} );
		}

		return $this->create_rest_response( array(
			'success' => true,
			'data_type' => $data_type,
			'data_type_label' => $available_post_types[ $data_type ],
			'category_filter' => empty( $filter_taxonomy ) ? null : array(
				'taxonomy' => $filter_taxonomy->name,
				'label' => ! empty( $filter_taxonomy->labels->singular_name ) ? $filter_taxonomy->labels->singular_name : __( 'Category', 'echo-knowledge-base' )
			),
			'categories' => array_values( $categories ),
			'items' => $items
		) );
	}

	/**
	 * Get post statistics for a collection (expensive operation, called on-demand)
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_collection_post_stats( $request ) {

		$collection_id = $request->get_param( 'collection_id' );

		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}

		$training_data_db = new EPKB_AI_Training_Data_DB();
		$is_ai_pro_enabled = EPKB_Utilities::is_ai_features_pro_enabled();

		// Get available post types with their content status
		$post_types_with_status = array();
		$available_post_types = EPKB_AI_Utilities::get_available_post_types_for_ai();
		foreach ( $available_post_types as $post_type => $label ) {

			// Check if this post type requires AI Pro (all non-KB post types require AI Pro)
			$requires_ai_pro = ! EPKB_KB_Handler::is_kb_post_type( $post_type ) && ! $is_ai_pro_enabled;

			// Use common function to get eligible posts - use stats_only mode for performance
			$posts_data = $this->get_eligible_posts_for_collection( $collection_id, $post_type, true );
			if ( is_wp_error( $posts_data ) ) {
				return $this->create_rest_response( [], 400, $collection_config );
			}

			// Handle the count - it could be a number or '500+'
			$eligible_count = isset( $posts_data['count'] ) ? $posts_data['count'] : count( $posts_data['eligible_posts'] );
			$is_approximate = isset( $posts_data['is_approximate'] ) && $posts_data['is_approximate'];

			// Get total count for this post type in the collection (already added)
			$already_added_in_collection = $training_data_db->get_training_data_count( array(
				'collection_id' => $collection_id,
				'type' => $post_type
			) );

			$post_types_with_status[$post_type] = array(
				'label' => $label,
				'available' => $eligible_count === '500+' || $eligible_count > 0,
				'count' => $eligible_count,
				'linked_articles' => $posts_data['linked_articles_count'],
				'password_protected' => $posts_data['password_protected_count'],
				'excluded' => $posts_data['excluded_count'],
				'already_added' => $already_added_in_collection,
				'new_items' => $eligible_count,
				'is_approximate' => $is_approximate,
				'requires_ai_pro' => $requires_ai_pro
			);
		}

		return $this->create_rest_response( array( 'success' => true, 'post_types_status' => $post_types_with_status ) );
	}
	
	/**
	 * Add posts to collection (extracted from load_posts_for_default_collection)
	 *
	 * @param int $collection_id
	 * @param string $post_type
	 * @return int|WP_Error Number of posts added or WP_Error on failure
	 */
	private function add_posts_to_collection( $collection_id, $post_type ) {
		
		// Use common function to get eligible posts
		$posts_data = $this->get_eligible_posts_for_collection( $collection_id, $post_type );
		if ( is_wp_error( $posts_data ) ) {
			return $posts_data;
		}
		
		// Get eligible posts that are not already in the collection
		return $this->insert_posts_into_collection( $collection_id, $posts_data['eligible_posts'] );
	}

	/**
	 * Add selected posts to collection.
	 *
	 * @param int    $collection_id Collection ID.
	 * @param string $post_type Post type slug.
	 * @param array  $item_ids Selected post IDs.
	 * @return int|WP_Error Number of posts added or WP_Error on failure.
	 */
	private function add_selected_posts_to_collection( $collection_id, $post_type, $item_ids ) {

		$item_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $item_ids ) ) ) );
		if ( empty( $item_ids ) ) {
			return 0;
		}

		$posts_data = $this->get_eligible_posts_for_collection( $collection_id, $post_type );
		if ( is_wp_error( $posts_data ) ) {
			return $posts_data;
		}

		$eligible_posts_by_id = array();
		foreach ( $posts_data['eligible_posts'] as $post ) {
			$eligible_posts_by_id[ (int) $post->ID ] = $post;
		}

		$selected_posts = array();
		foreach ( $item_ids as $item_id ) {
			if ( isset( $eligible_posts_by_id[ $item_id ] ) ) {
				$selected_posts[] = $eligible_posts_by_id[ $item_id ];
			}
		}

		return $this->insert_posts_into_collection( $collection_id, $selected_posts );
	}

	/**
	 * Insert posts into the training data collection.
	 *
	 * @param int   $collection_id Collection ID.
	 * @param array $posts Posts to insert.
	 * @return int|WP_Error Number of posts added or WP_Error on failure.
	 */
	private function insert_posts_into_collection( $collection_id, $posts ) {

		if ( empty( $posts ) ) {
			return 0;
		}

		$training_data_db = new EPKB_AI_Training_Data_DB( true );
		$batch_size = 100;
		$batches = array_chunk( $posts, $batch_size );
		$total_loaded = 0;

		foreach ( $batches as $batch ) {
			foreach ( $batch as $post ) {
				$training_data = array(
					'collection_id' => $collection_id,
					'provider'               => EPKB_AI_Provider::get_active_provider(),
					'item_id'                => (string) $post->ID,
					'title'                  => EPKB_AI_Validation::validate_title( $post->post_title ),
					'type'                   => $post->post_type,
					'status'                 => 'pending',
					'url'                    => get_permalink( $post->ID ),
					'content_hash'           => md5( $post->post_content ),
					'user_id'                => get_current_user_id()
				);

				$result = $training_data_db->insert_training_data( $training_data );
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				$total_loaded++;
			}
		}

		return $total_loaded;
	}

	/**
	 * Get the category taxonomy to use for item filtering.
	 *
	 * @param string $post_type Post type slug.
	 * @return WP_Taxonomy|null
	 */
	private function get_category_filter_taxonomy_for_post_type( $post_type ) {

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		if ( empty( $taxonomies ) ) {
			return null;
		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( empty( $taxonomy->hierarchical ) || $taxonomy->name === 'post_format' ) {
				continue;
			}

			return $taxonomy;
		}

		return null;
	}

	/**
	 * Get eligible posts for a collection
	 *
	 * @param int $collection_id
	 * @param string $post_type
	 * @param bool $stats_only If true, returns approximate stats for large datasets
	 * @return array|WP_Error Contains: eligible_posts, linked_articles_count, password_protected_count, excluded_count, already_added_ids, or WP_Error on database failure
	 */
	private function get_eligible_posts_for_collection( $collection_id, $post_type, $stats_only = false ) {
		
		$result = array(
			'eligible_posts' => array(),
			'linked_articles_count' => 0,
			'password_protected_count' => 0,
			'excluded_count' => 0,
			'already_added_ids' => array(),
			'is_approximate' => false
		);
		
		// Validate post type exists
		if ( ! post_type_exists( $post_type ) ) {
			return $result;
		}
		
		// Get existing post IDs for this collection
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$existing_post_ids = $training_data_db->get_existing_post_ids( $collection_id );
		if ( is_wp_error( $existing_post_ids ) ) {
			return $existing_post_ids;
		}
		$result['already_added_ids'] = $existing_post_ids;
		
		// Determine query limit based on mode
		$query_limit = -1; // Default: get all posts
		$check_for_more = false;
		
		if ( $stats_only ) {
			// For stats mode, limit to 501 posts to check if there are 500+
			$query_limit = 501;
			$check_for_more = true;
		}
		
		// Determine allowed statuses - AI Notes allow 'private'
		$post_status = array( 'publish' );
		if ( $post_type === EPKB_AI_Utilities::AI_PRO_NOTES_POST_TYPE ) {
			$post_status[] = 'private';
		}

		// Get eligible posts with controlled limit
		$posts_query = new WP_Query( array(
			'post_type' => $post_type,
			'post_status' => $post_status,
			'posts_per_page' => $query_limit,
			'fields' => 'ids',
			'no_found_rows' => ! $check_for_more, // Only get total count if checking for more
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'post__not_in' => ! empty( $existing_post_ids ) ? $existing_post_ids : array( 0 )
		) );
		
		// Check if we have 500+ posts available
		$has_more_than_500 = false;
		if ( $check_for_more && $posts_query->found_posts > 500 ) {
			$has_more_than_500 = true;
			$result['is_approximate'] = true;
		}
		
		// Process posts to check eligibility
		$eligible_count = 0;
		$posts_to_check = $posts_query->posts;
		
		// If we have 500+, just check a sample of 100 for stats
		if ( $has_more_than_500 && $stats_only ) {
			$posts_to_check = array_slice( $posts_to_check, 0, 100 );
		}
		
		foreach ( $posts_to_check as $post_id ) {
			$post = get_post( $post_id );
			if ( ! $post ) {
				continue;
			}
			
			$eligibility_check = EPKB_Admin_UI_Access::is_post_eligible_for_ai_training( $post );
			if ( $eligibility_check === true ) {
				if ( $stats_only ) {
					$eligible_count++;
				} else {
					$result['eligible_posts'][] = $post;
				}
			} else if ( is_wp_error( $eligibility_check ) ) {
				// Count different types of exclusions
				$error_code = $eligibility_check->get_error_code();
				if ( $error_code === 'linked_article' ) {
					$result['linked_articles_count']++;
				} else if ( $error_code === 'post_password_protected' ) {
					$result['password_protected_count']++;
				} else {
					$result['excluded_count']++;
				}
			}
		}
		
		// Set the count for stats mode
		if ( $stats_only ) {
			if ( $has_more_than_500 && $eligible_count > 0 ) {
				// If we found eligible items in our sample and have 500+ posts, show 500+
				$result['count'] = '500+';
			} else {
				$result['count'] = $eligible_count;
			}
			$result['eligible_posts'] = array(); // Empty array for stats only
		}
		
		return $result;
	}

	
	/**********************************************************************
	 * PDF Upload to Vector Store
	 **********************************************************************/

	/**
	 * Upload a PDF file directly to AI vector store
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function upload_pdf_to_vector_store( $request ) {

		EPKB_PDF_Utilities::setup_timeout_error_handling( 'rest' );

		$collection_id = (int) $request->get_param( 'collection_id' );
		$file_name = $request->get_param( 'file_name' );
		$file_data = $request->get_param( 'file_data' );
		$upload_mode = $request->get_param( 'upload_mode' ) === 'extract_pdf_text' ? 'extract_pdf_text' : 'raw_pdf';
		$attachment_url = $request->get_param( 'attachment_url' );
		$attachment_url = ! empty( $attachment_url ) ? esc_url_raw( $attachment_url ) : '';

		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}

		$pdf_data = EPKB_PDF_Utilities::decode_base64_pdf( $file_data, $file_name );
		if ( is_wp_error( $pdf_data ) ) {
			return $this->create_rest_response( [], 400, $pdf_data );
		}

		// Get vector store handler
		$vector_store_handler = EPKB_AI_Provider::get_vector_store_handler();

		// Get or create vector store for this collection
		$store_id = $vector_store_handler->get_or_create_vector_store( $collection_id );
		if ( is_wp_error( $store_id ) ) {
			return $this->create_rest_response( [], 500, $store_id );
		}

		// Generate a unique ID for this PDF upload
		$pdf_id = EPKB_AI_Utilities::generate_uuid_v4();

		$content_hash = $pdf_data['content_hash'];
		$training_data_type = 'PDF';

		if ( $upload_mode === 'extract_pdf_text' ) {
			$formatted_content = EPKB_PDF_Utilities::format_text_for_display( $request->get_param( 'raw_text' ), 'basic' );
			if ( is_wp_error( $formatted_content ) ) {
				return $this->create_rest_response( [], 400, $formatted_content );
			}

			$formatted_content = EPKB_PDF_Utilities::sanitize_note_content( $formatted_content );
			$plain_content = EPKB_PDF_Utilities::strip_html_for_ai( $formatted_content );
			if ( $plain_content === '' ) {
				return $this->create_rest_response( [], 400, new WP_Error( 'empty_content', __( 'PDF text content is empty.', 'echo-knowledge-base' ) ) );
			}

			$content_hash = md5( $plain_content );
			$training_data_type = 'HTML';
			$upload_result = $vector_store_handler->upload_file_to_file_storage(
				$pdf_id,
				$this->build_html_document_for_vector_store_upload( $pdf_data['file_name'], $formatted_content ),
				'pdf_html',
				$store_id,
				'html',
				'text/html'
			);
		} else {
			// Upload PDF binary to AI file storage
			$upload_result = $vector_store_handler->upload_pdf_to_file_storage( $pdf_id, $pdf_data['binary'], $store_id );
		}

		if ( is_wp_error( $upload_result ) ) {
			return $this->create_rest_response( [], 500, $upload_result );
		}

		$file_id = isset( $upload_result['id'] ) ? $upload_result['id'] : '';
		if ( empty( $file_id ) ) {
			return $this->create_rest_response( [], 500, new WP_Error( 'upload_failed', __( 'Failed to upload PDF to AI storage.', 'echo-knowledge-base' ) ) );
		}

		// For ChatGPT, add file to vector store (Gemini does this during upload)
		$provider = EPKB_AI_Provider::get_active_provider();
		if ( $provider !== EPKB_AI_Provider::PROVIDER_GEMINI ) {
			$add_result = $vector_store_handler->add_file_to_vector_store( $store_id, $file_id, true );
			if ( is_wp_error( $add_result ) ) {
				$this->cleanup_failed_pdf_upload( $vector_store_handler, $store_id, $file_id, $collection_id, $provider, 'add_failed' );
				return $this->create_rest_response( [], 500, $add_result );
			}

			$processing_result = $vector_store_handler->wait_for_file_to_complete_in_vector_store( $store_id, $file_id );
			if ( is_wp_error( $processing_result ) ) {
				$this->cleanup_failed_pdf_upload( $vector_store_handler, $store_id, $file_id, $collection_id, $provider, 'processing_failed' );
				return $this->create_rest_response( [], 500, $processing_result );
			}
		}

		// Insert record in training data table
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$training_data = array(
			'collection_id' => $collection_id,
			'provider'      => $provider,
			'item_id'       => $pdf_id,
			'store_id'      => $store_id,
			'file_id'       => $file_id,
			'title'         => $pdf_data['file_name'],
			'type'          => $training_data_type,
			'status'        => 'added',
			'content_hash'  => $content_hash,
			'url'           => $attachment_url ?: null,
			'user_id'       => get_current_user_id(),
			'last_synced'   => gmdate( 'Y-m-d H:i:s' ),
		);

		$insert_id = $training_data_db->insert_training_data( $training_data );
		if ( is_wp_error( $insert_id ) ) {
			$this->cleanup_failed_pdf_upload( $vector_store_handler, $store_id, $file_id, $collection_id, $provider, 'insert_failed' );
			return $this->create_rest_response( [], 500, $insert_id );
		}

		return $this->create_rest_response( array(
			'success' => true,
			'message' => $upload_mode === 'extract_pdf_text'
				? __( 'PDF text extracted and uploaded successfully.', 'echo-knowledge-base' )
				: __( 'PDF uploaded successfully.', 'echo-knowledge-base' ),
			'record'  => array(
				'id'          => $insert_id,
				'title'       => $pdf_data['file_name'],
				'type'        => $training_data_type,
				'status'      => 'added',
				'file_id'     => $file_id,
				'upload_mode' => $upload_mode,
				'created'     => gmdate( 'Y-m-d H:i:s' ),
			),
		) );
	}

	/**
	 * Wrap sanitized HTML in a complete document before uploading it to AI storage.
	 *
	 * @param string $file_name Original PDF file name.
	 * @param string $content Sanitized HTML fragment.
	 * @return string
	 */
	private function build_html_document_for_vector_store_upload( $file_name, $content ) {

		$document_title = sanitize_text_field( wp_strip_all_tags( pathinfo( (string) $file_name, PATHINFO_FILENAME ) ) );
		if ( $document_title === '' ) {
			$document_title = __( 'PDF Document', 'echo-knowledge-base' );
		}

		return "<!DOCTYPE html>\n<html>\n<head>\n<meta charset=\"utf-8\">\n<title>" . esc_html( $document_title ) . "</title>\n</head>\n<body>\n" . $content . "\n</body>\n</html>";
	}

	/**
	 * Remove a PDF file from the provider when upload processing fails before DB insert.
	 *
	 * @param EPKB_AI_ChatGPT_Vector_Store|EPKB_AI_Gemini_Vector_Store $vector_store_handler Vector store handler.
	 * @param string $store_id Store ID.
	 * @param string $file_id File ID.
	 * @param int $collection_id Collection ID.
	 * @param string $provider Provider slug.
	 * @param string $reason Cleanup reason.
	 */
	private function cleanup_failed_pdf_upload( $vector_store_handler, $store_id, $file_id, $collection_id, $provider, $reason ) {

		if ( empty( $file_id ) ) {
			return;
		}

		if ( ! empty( $store_id ) ) {
			$remove_result = $vector_store_handler->remove_file_from_vector_store( $store_id, $file_id );
			if ( is_wp_error( $remove_result ) && $remove_result->get_error_code() !== 'not_found' ) {
				EPKB_AI_Log::add_log( $remove_result, array(
					'collection_id' => $collection_id,
					'provider'      => $provider,
					'store_id'      => $store_id,
					'file_id'       => $file_id,
					'reason'        => $reason,
					'message'       => 'Failed to remove PDF from vector store during cleanup'
				) );
			}
		}

		$delete_result = $vector_store_handler->delete_file_from_file_storage( $file_id, $store_id );
		if ( is_wp_error( $delete_result ) && $delete_result->get_error_code() !== 'not_found' ) {
			EPKB_AI_Log::add_log( $delete_result, array(
				'collection_id' => $collection_id,
				'provider'      => $provider,
				'store_id'      => $store_id,
				'file_id'       => $file_id,
				'reason'        => $reason,
				'message'       => 'Failed to delete PDF from AI storage during cleanup'
			) );
		}
	}

	/**
	 * Get the training data record for an AI Note, scoped to the active collection when provided.
	 *
	 * @param int $note_id
	 * @param int $collection_id
	 * @return object|WP_Error|null
	 */
	private function get_training_data_note_record( $note_id, $collection_id = 0 ) {

		$note_id = absint( $note_id );
		$collection_id = absint( $collection_id );
		if ( $note_id <= 0 ) {
			return null;
		}

		$collection_ids = array();
		if ( $collection_id > 0 ) {
			$collection_ids[] = $collection_id;
		}

		$post_collection_id = (int) get_post_meta( $note_id, '_epkb_collection_id', true );
		if ( $post_collection_id > 0 && ! in_array( $post_collection_id, $collection_ids, true ) ) {
			$collection_ids[] = $post_collection_id;
		}

		if ( empty( $collection_ids ) ) {
			$collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections( false, false );
			if ( ! is_wp_error( $collections ) && ! empty( $collections ) ) {
				foreach ( array_keys( $collections ) as $available_collection_id ) {
					$available_collection_id = (int) $available_collection_id;
					if ( $available_collection_id > 0 ) {
						$collection_ids[] = $available_collection_id;
					}
				}
			}
		}

		$training_data_db = new EPKB_AI_Training_Data_DB();
		foreach ( $collection_ids as $target_collection_id ) {
			$record = $training_data_db->get_training_data_record_by_item_id( $target_collection_id, (string) $note_id );
			if ( is_wp_error( $record ) ) {
				return $record;
			}
			if ( ! empty( $record ) ) {
				return $record;
			}
		}

		return null;
	}

	/**
	 * Delete provider-side file references for a training data record.
	 *
	 * @param object $record Training data record.
	 * @param string $reason Cleanup reason for logs.
	 * @return bool|WP_Error
	 */
	private function cleanup_training_data_record_from_provider( $record, $reason = 'training_data_delete' ) {
		return EPKB_AI_Utilities::cleanup_training_data_record_from_provider( $record, $reason );
	}

	/**********************************************************************
	 * Training Notes (AI Features Pro)
	 **********************************************************************/
	
	/**
	 * Create a training note
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function create_training_note( $request ) {

		// Check if AI Features Pro is enabled
		$create_callable = array( 'AIPRO_AI_Notes', 'create_ai_note' ); /* @disregard PREFIX */
		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() || ! class_exists( 'AIPRO_AI_Notes' ) || ! is_callable( $create_callable ) ) {  /* @disregard PREFIX */
			return $this->create_rest_response( [], 400, new WP_Error( 'ai_pro_required', __( 'AI Features Pro is required for training notes', 'echo-knowledge-base' ) ) );
		}

		$collection_id = (int) $request->get_param( 'collection_id' );
		$title = $request->get_param( 'title' );
		$content = $request->get_param( 'content' );

		// Optional PDF meta parameters
		$note_type = $request->get_param( 'note_type' ) ?: 'text';
		$original_file_name = $request->get_param( 'original_file_name' );
		$conversion_time = $request->get_param( 'conversion_time' );
		$format_mode = $request->get_param( 'format_mode' ) ?: 'none';

		// Apply PDF formatting before storing note content.
		if ( $note_type === 'pdf' ) {
			EPKB_PDF_Utilities::setup_timeout_error_handling( 'rest' );

			if ( $format_mode === 'ai' ) {
				$pdf_data = EPKB_PDF_Utilities::decode_base64_pdf(
					$request->get_param( 'pdf_base64' ),
					$request->get_param( 'file_name' ) ?: ( $original_file_name ?: 'document.pdf' )
				);
				if ( is_wp_error( $pdf_data ) ) {
					return $this->create_rest_response( [], 400, $pdf_data );
				}

				$content = EPKB_PDF_Utilities::format_pdf_for_display( $pdf_data['binary'], $pdf_data['file_name'], 'ai' );
			} elseif ( $format_mode !== 'none' ) {
				$content = EPKB_PDF_Utilities::format_text_for_display( $content, $format_mode );
			}

			if ( is_wp_error( $content ) ) {
				return $this->create_rest_response( [], 400, $content );
			}
		}

		$content = EPKB_PDF_Utilities::sanitize_note_content( $content );
		$plain_content = EPKB_PDF_Utilities::strip_html_for_ai( $content );
		if ( empty( $plain_content ) ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'empty_content', __( 'No content to save.', 'echo-knowledge-base' ) ) );
		}
		$content_hash = md5( $plain_content );

		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}

		// Build meta array for note creation
		$meta = array(
			'_epkb_collection_id' => $collection_id,
			'_in_training_data'   => '1'
		);

		// Add PDF-specific meta if this is a PDF note
		if ( $note_type === 'pdf' ) {
			$meta['_note_type'] = 'pdf';
			if ( $original_file_name ) {
				$meta['_pdf_original_file_name'] = sanitize_file_name( $original_file_name );
			}
			if ( $conversion_time ) {
				$meta['_pdf_conversion_time'] = (int) $conversion_time;
			}
		}

		// Use AI Features Pro to create note
		$note_id = call_user_func( $create_callable, $title, $content, $meta );
		if ( is_wp_error( $note_id ) ) {
			return $this->create_rest_response( [], 500, $note_id );
		}
		
		// Also add to training data table for unified display
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$training_data = array(
			'collection_id' => $collection_id,
			'provider'      => EPKB_AI_Provider::get_active_provider(),
			'item_id'       => (string) $note_id,
			'title'         => $title,
			'type'          => EPKB_AI_Utilities::AI_PRO_NOTES_POST_TYPE,
			'status'        => 'pending',
			'url'           => '',
			'content_hash'  => $content_hash,
			'user_id'       => get_current_user_id()
		);
		
		$result = $training_data_db->insert_training_data( $training_data );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 500, $result );
		}
		
		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Note created successfully', 'echo-knowledge-base' ), 'note_id' => $note_id ) );
	}
	
	/**
	 * Update a training note
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_training_note( $request ) {
		
		// Check if AI Features Pro is enabled
		$update_callable = array( 'AIPRO_AI_Notes', 'update_ai_note' ); /* @disregard PREFIX */
		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() || ! class_exists( 'AIPRO_AI_Notes' ) || ! is_callable( $update_callable ) ) {  /* @disregard PREFIX */
			return $this->create_rest_response( [], 400, new WP_Error( 'ai_pro_required', __( 'AI Features Pro is required for training notes', 'echo-knowledge-base' ) ) );
		}
		
		$note_id = $request->get_param( 'note_id' );
		$title = $request->get_param( 'title' );
		$content = $request->get_param( 'content' );
		$training_id = $request->get_param( 'training_id' );

		$update_data = array();
		if ( $title !== null ) {
			$update_data['title'] = $title;
		}
		if ( $content !== null ) {
			$update_data['content'] = EPKB_PDF_Utilities::sanitize_note_content( $content );
		}
		if ( empty( $update_data ) ) {
			return $this->create_rest_response( [], 400, new WP_Error( 'no_changes', __( 'No changes provided', 'echo-knowledge-base' ) ) );
		}
		
		// Use AI Features Pro to update note
		$result = call_user_func( $update_callable, $note_id, $update_data );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 500, $result );
		}
		
		// Update training data table entry to keep it in sync
		if ( $training_id ) {
			$training_update = array( 'status' => 'pending' );
			if ( $title !== null ) {
				$training_update['title'] = $title;
			}
			if ( isset( $update_data['content'] ) ) {
				$training_update['content_hash'] = md5( EPKB_PDF_Utilities::strip_html_for_ai( $update_data['content'] ) );
			}

			$training_data_db = new EPKB_AI_Training_Data_DB();
			$result = $training_data_db->update_training_data( $training_id, $training_update );
			if ( is_wp_error( $result ) ) {
				return $this->create_rest_response( [], 500, $result );
			}
		}
		
		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Note updated successfully', 'echo-knowledge-base' ) ) );
	}
	
	/**
	 * Delete a training note
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function delete_training_note( $request ) {
		
		// Check if AI Features Pro is enabled
		$delete_callable = array( 'AIPRO_AI_Notes', 'delete_ai_note' ); /* @disregard PREFIX */
		if ( ! EPKB_Utilities::is_ai_features_pro_enabled() || ! class_exists( 'AIPRO_AI_Notes' ) || ! is_callable( $delete_callable ) ) {  /* @disregard PREFIX */
			return $this->create_rest_response( [], 400, new WP_Error( 'ai_pro_required', __( 'AI Features Pro is required for training notes', 'echo-knowledge-base' ) ) );
		}
		
		$note_id = absint( $request->get_param( 'note_id' ) );
		$collection_id = absint( $request->get_param( 'collection_id' ) );
		$training_record = $this->get_training_data_note_record( $note_id, $collection_id );
		if ( is_wp_error( $training_record ) ) {
			return $this->create_rest_response( [], 500, $training_record );
		}
		
		// Use AI Features Pro to delete note
		$result = call_user_func( $delete_callable, $note_id, true );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 500, $result );
		}
		
		// Delete from training data table
		$training_data_db = new EPKB_AI_Training_Data_DB();
		if ( $training_record && ! empty( $training_record->id ) ) {
			$result = $training_data_db->delete_training_data_record( (int) $training_record->id );
		} else {
			$result = $training_data_db->delete_training_data_by_source( EPKB_AI_Utilities::AI_PRO_NOTES_POST_TYPE, (string) $note_id );
		}
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 500, $result );
		}

		if ( $training_record ) {
			$this->cleanup_training_data_record_from_provider( $training_record, 'training_note_delete' );
		}
		
		return $this->create_rest_response( array( 'success' => true, 'message' => __( 'Note deleted successfully', 'echo-knowledge-base' ) ) );
	}
	
	/**
	 * Check admin permission
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_admin_permission( $request ) {
		
		// Check nonce
		$nonce_check = EPKB_AI_Security::check_rest_nonce( $request );
		if ( is_wp_error( $nonce_check ) ) {
			return $nonce_check;
		}
		
		// Check user capability
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_ai_feature' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission to manage AI sync', 'echo-knowledge-base' ),	array( 'status' => 403 ) );
		}
		
		return true;
	}
}
