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
					'enum' => array( '', 'pending', 'adding', 'added', 'updating', 'updated', 'outdated', 'error' )
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
					'required' => true,
					'type' => 'array',
					'items' => array( 'type' => 'string' )
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
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'wp_kses_post'
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
				)
			)
		) );
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
		$data = $training_data_db->get_training_data_list( $args );
		if ( is_wp_error( $data ) ) {
			return $this->create_rest_response( [], 500, $data );
		}

		// Add post type name to each item
		foreach ( $data as &$item ) {
			// Get the post type object to get the label
			$post_type_obj = get_post_type_object( $item->type );
			if ( $post_type_obj && isset( $post_type_obj->labels->name ) ) {

				if ( strpos( $item->type, 'epkb_post_type' ) === 0 && isset( $post_type_obj->labels->name ) ) {
					$type_name = $post_type_obj->labels->name;
				} else {
					// For standard post types, use singular_name
					$type_name = $post_type_obj->labels->singular_name;
				}
			} else {
				// Fallback to the type if post type object not found
				$type_name = ucfirst( $item->type );
			}
			
			// Limit to 20 characters with ellipsis if longer
			if ( strlen( $type_name ) > 20 ) {
				$item->type_name = substr( $type_name, 0, 18 ) . '..';
			} else {
				$item->type_name = $type_name;
			}
			// Keep the original type as item_type for filtering
			$item->item_type = $item->type;
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

			$openai_handler = new EPKB_AI_OpenAI_Vector_Store();
			$update_result = $openai_handler->update_vector_store( $collection_config['ai_training_data_store_id'], array( 'name' => $name ) );
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

		$message = sprintf( __( 'Deleted %d items, %d failed', 'echo-knowledge-base' ), $results['deleted'], $results['failed'] );
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

		// Regular post handling
		$post_id = intval( $training_data->item_id );
		$post = get_post( $post_id );
		if ( ! $post ) {
			$training_data_db->mark_as_error( $id, 404, __( 'Original post not found', 'echo-knowledge-base' ) );
			return $this->create_rest_response( [], 404, new WP_Error( 'post_not_found', __( 'Original post not found', 'echo-knowledge-base' ), array( 'training_data_id' => $id, 'post_id' => $post_id ) ) );
		}
		
		// Check if this is a KB files type - content comes from filter
		if ( $post->post_type === 'epkb_kb_files' ) {
			// For KB files, get content from filter
			$content = '';
			if ( has_filter( 'epkb_process_kb_file' ) ) {
				$content = apply_filters( 'epkb_process_kb_file', '', $post );
			}
			
			$prepared_content = array(
				'content' => $content,
				'metadata' => array(
					'post_id' => strval( $post->ID ),
					'title' => $post->post_title,
					'type' => 'epkb_kb_files',
					'url' => get_permalink( $post->ID )
				),
				'size' => strlen( $content )
			);
		} else {
			// Process the content for display
			$content_processor = new EPKB_AI_Content_Processor();
			$prepared_content = $content_processor->prepare_post( $post );
			if ( is_wp_error( $prepared_content ) ) {
				$training_data_db->mark_as_error( $id, $prepared_content->get_error_code() === 'post_not_published' ? 404 : 500, $prepared_content->get_error_message() );
				return $this->create_rest_response( [], 500, $prepared_content );
			}
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
			$openai_handler = new EPKB_AI_OpenAI_Vector_Store();
			$vector_store_delete_result = $openai_handler->delete_vector_store( $vector_store_id );
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
		$openai_handler = new EPKB_AI_OpenAI_Vector_Store();
		$deleted = 0;
		$failed = 0;
		$vector_store_errors = 0;

		foreach ( $ids as $id ) {
			// Get the training data record before deleting to get file IDs
			$record = $training_data_db->get_training_data_row_by_id( $id );

			if ( $record ) {
				// Delete from OpenAI if file ID exists
				if ( ! empty( $record->file_id ) ) {
					$delete_file_result = $openai_handler->delete_file_from_file_storage( $record->file_id );
					if ( is_wp_error( $delete_file_result ) ) {
						$vector_store_errors++;
						EPKB_AI_Log::add_log( $delete_file_result, array( 'file_id' => $record->file_id, 'message' => 'Failed to delete file from OpenAI' ) );
					}
				}

				// Remove from vector store if file_id exists
				if ( ! empty( $record->store_id ) && ! empty( $record->file_id ) ) {
					$remove_result = $openai_handler->remove_file_from_vector_store( $record->store_id, $record->file_id );
					if ( is_wp_error( $remove_result ) ) {
						$vector_store_errors++;
						EPKB_AI_Log::add_log( $remove_result, array( 'store_id' => $record->store_id, 'file_id' => $record->file_id, 'message' => 'Failed to remove file from vector store' ) );
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
		
		$collection_id = $request->get_param( 'collection_id' );
		$data_types = $request->get_param( 'data_types' );
		
		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}
		
		// Initialize counters
		$total_added = 0;

		// Determine which post types are allowed (UI and API gate)
		$available_post_types = array_keys( EPKB_AI_Utilities::get_available_post_types_for_ai() );
		
		// Update collection configuration to include the selected post types FIRST
		$collection_config['ai_training_data_store_post_types'] = $data_types;
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
		
		// Get available post types with their content status
		$post_types_with_status = array();
		$available_post_types = EPKB_AI_Utilities::get_available_post_types_for_ai();
		foreach ( $available_post_types as $post_type => $label ) {
			
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
				'is_approximate' => $is_approximate
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
		
		$training_data_db = new EPKB_AI_Training_Data_DB( true );
		
		// Use common function to get eligible posts
		$posts_data = $this->get_eligible_posts_for_collection( $collection_id, $post_type );
		if ( is_wp_error( $posts_data ) ) {
			return $posts_data;
		}
		
		// Get eligible posts that are not already in the collection
		$eligible_posts = $posts_data['eligible_posts'];
		if ( empty( $eligible_posts ) ) {
			// No eligible posts to add
			return 0;
		}
		
		// Batch insert for better performance
		$batch_size = 100;
		$batches = array_chunk( $eligible_posts, $batch_size );
		$total_loaded = 0;
		
		foreach ( $batches as $batch ) {
			foreach ( $batch as $post ) {
				// Prepare training data record
				$training_data = array(
					'collection_id' => $collection_id,
					'item_id'                => (string) $post->ID,
					'title'                  => EPKB_AI_Validation::validate_title( $post->post_title ),
					'type'                   => $post->post_type,
					'status'                 => 'pending', // Default status for new records
					'url'                    => get_permalink( $post->ID ),
					'content_hash'           => md5( $post->post_content ),
					'user_id'                => get_current_user_id()
				);
				
				// Insert the record
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
		
		// Get published posts with controlled limit
		$posts_query = new WP_Query( array(
			'post_type' => $post_type,
			'post_status' => 'publish',
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
		
		// Validate collection exists
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $this->create_rest_response( [], 400, $collection_config );
		}
		
		// Use AI Features Pro to create note
		$note_id = call_user_func( $create_callable, $title, $content, array( '_epkb_collection_id' => $collection_id, '_in_training_data' => '1' ) );
		if ( is_wp_error( $note_id ) ) {
			return $this->create_rest_response( [], 500, $note_id );
		}
		
		// Also add to training data table for unified display
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$training_data = array(
			'collection_id' => $collection_id,
			'item_id'                => (string) $note_id,
			'title'                  => $title,
			'type'                   => EPKB_AI_Utilities::AI_PRO_NOTES_POST_TYPE,
			'status'                 => 'pending',
			'url'                    => '',
			'content_hash'           => md5( $content ),
			'user_id'                => get_current_user_id()
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
		
		// Use AI Features Pro to update note
		$result = call_user_func( $update_callable, $note_id, array( 'title' => $title, 'content' => $content ) );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 500, $result );
		}
		
		// Update training data table entry to keep it in sync
		if ( $training_id ) {
			$training_data_db = new EPKB_AI_Training_Data_DB();
			$result = $training_data_db->update_training_data( $training_id, array( 'title' => $title, 'content_hash' => md5( $content ), 'status' => 'pending' ) );
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
		
		$note_id = $request->get_param( 'note_id' );
		
		// Use AI Features Pro to delete note
		$result = call_user_func( $delete_callable, $note_id, true );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 500, $result );
		}
		
		// Delete from training data table
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$result = $training_data_db->delete_training_data_by_source( EPKB_AI_Utilities::AI_PRO_NOTES_POST_TYPE, (string) $note_id );
		if ( is_wp_error( $result ) ) {
			return $this->create_rest_response( [], 500, $result );
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