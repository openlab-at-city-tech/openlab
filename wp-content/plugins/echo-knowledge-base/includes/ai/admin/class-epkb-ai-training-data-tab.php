<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI Training Data tab with React implementation
 *
 * @copyright   Copyright (C) 2025, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_AI_Training_Data_Tab {

	const TAB_RECONCILE_COOLDOWN = 300; // 5 minutes.

	/**
	 * Get the configuration for the Training Data tab
	 * @return array
	 */
	public static function get_tab_config() {

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		
		// Sync collections from database first to ensure we have all collections
		EPKB_AI_Training_Data_Config_Specs::sync_collections_from_database();
		
		// Get training data collections
		$data_collections_result = self::get_data_collections_post_stats();
		$data_collections = $data_collections_result['collections'];
		$collections_error = $data_collections_result['error'];
		
		// Get next available collection ID for new collections
		$next_collection_id = EPKB_AI_Training_Data_Config_Specs::get_next_collection_id();
		if ( is_wp_error( $next_collection_id ) ) {
			// If error, let the UI handle it appropriately
			$next_collection_id = null;
		}
		
		// Build sub_tabs array from collections
		$sub_tabs = array();

		// Add each collection as a sub-tab (only active provider collections)
		foreach ( $data_collections as $collection ) {
			$sub_tabs['collection-' . $collection['id']] = array(
				'id' => 'collection-' . $collection['id'],
				'title' => $collection['name'],
				'icon' => 'epkbfa epkbfa-database',
				'collection_id' => $collection['id'],
				'provider' => $collection['provider'],
				'is_active_provider' => $collection['is_active_provider']
			);
		}
		
		// Add "Add New" button as the last sub-tab
		$sub_tabs['add-new'] = array(
			'id' => 'add-new',
			'title' => __( 'Add New Collection', 'echo-knowledge-base' ),
			'icon' => 'epkbfa epkbfa-plus',
			'is_add_new' => true
		);
		
		 /** @disregard P1011 */
		$config = array(
			'tab_id' => 'training-data',
			'title' => __( 'Training Data', 'echo-knowledge-base' ),
			'sub_tabs' => $sub_tabs,
			'ai_config' => $ai_config,
			'data_collections' => $data_collections,
			'next_collection_id' => $next_collection_id,
			'vector_store_files' => self::get_vector_store_files(),
			'available_post_types' => EPKB_AI_Utilities::get_available_post_types_for_ai(),
            'is_wp_cron_disabled' => ( defined( 'DISABLE_WP_CRON' ) && constant( 'DISABLE_WP_CRON' ) ),
			'is_ai_features_pro_enabled' => EPKB_Utilities::is_ai_features_pro_enabled(),
			'is_access_manager_active' => EPKB_Utilities::is_amag_on(),
			'active_provider' => EPKB_AI_Provider::get_active_provider(),
			'active_provider_label' => EPKB_AI_Provider::get_provider_label(),
			'setup_steps' => EPKB_AI_Admin_Page::get_setup_steps_for_tab( 'training-data' ),
			'show_validate_fix_notice' => ! EPKB_Core_Utilities::is_kb_flag_set( 'ai_validate_fix_notice_dismissed' )
		);
		
		// Add error information if collections couldn't be retrieved
		if ( $collections_error ) {
			$config['collections_error'] = __( 'Unable to retrieve training data collections. Please try again later.', 'echo-knowledge-base' );
		}
		
		return $config;
	}

	/**
	 * Get data collections to display in the Training Data tab (optimized version without expensive post stats)
	 * Only shows collections from the active provider
	 * @return array
	 */
	private static function get_data_collections_post_stats() {

		// Get collections from active provider only
		$collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections( false, true );
		$has_error = false;
		if ( is_wp_error( $collections ) ) {
			// Return empty array if there's an error retrieving collections
			$collections = array();
			$has_error = true;
		}

		$active_provider = EPKB_AI_Provider::get_active_provider();
		$active_collection_id = self::get_requested_active_collection_id( $collections );
		$should_reconcile_active_collection = self::should_reconcile_active_collection_on_tab_load( $active_collection_id );
		
		$formatted_collections = array();
		$training_data_db = new EPKB_AI_Training_Data_DB();

		foreach ( $collections as $collection_id => $collection_config ) {

			if ( $should_reconcile_active_collection && $collection_id === $active_collection_id ) {
				EPKB_AI_Utilities::reconcile_collection_source_updates( $collection_id, $training_data_db );
				self::mark_collection_reconciled_on_tab_load( $collection_id );
			}

			$db_stats = $training_data_db->get_status_statistics( $collection_id );
			$last_sync_date = $training_data_db->get_last_sync_date( $collection_id );

			// OPTIMIZATION: Don't calculate expensive post type stats here
			// These will be loaded on-demand when user clicks "Add Training Data"
			// Just set a flag that post types options need to be loaded
			$collection_config['ai_training_data_store_post_types_options'] = 'load_on_demand';

			// Pre-load first page of training data for this collection
			$preloaded_data = self::get_preloaded_training_data( $collection_id );

			// Determine provider info for this collection
			$collection_provider = isset( $collection_config['ai_training_data_provider'] )
				? $collection_config['ai_training_data_provider'] : '';
			$is_active_provider = ( $collection_provider === $active_provider );
			$provider_label = EPKB_AI_Provider::get_provider_label( $collection_provider );

			$formatted_collections[] = array(
				'id' => $collection_id,
				'name' => empty( $collection_config['ai_training_data_store_name'] ) ? EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id ) : $collection_config['ai_training_data_store_name'],
				'status' => 'active',
				'item_count' => isset( $db_stats['total'] ) ? $db_stats['total'] : 0,
				'last_synced' => $last_sync_date,
				'post_types' => $collection_config['ai_training_data_store_post_types'],
				'config' => $collection_config,
				'stats' => $db_stats, // Include full stats for immediate display
				'preloaded_data' => $preloaded_data, // Include pre-loaded first page data
				'provider' => $collection_provider,
				'provider_label' => $provider_label,
				'is_active_provider' => $is_active_provider
			);
		}
		
		return array(
			'collections' => $formatted_collections,
			'error' => $has_error
		);
	}

	/**
	 * Determine whether the active collection should be reconciled during tab load.
	 *
	 * Manual refresh bypasses this cooldown through the REST endpoint.
	 *
	 * @param int $collection_id Collection ID.
	 * @return bool
	 */
	private static function should_reconcile_active_collection_on_tab_load( $collection_id ) {

		$collection_id = absint( $collection_id );
		if ( empty( $collection_id ) ) {
			return false;
		}

		if ( EPKB_Utilities::get( 'active_tab', 'dashboard' ) !== 'training-data' ) {
			return false;
		}

		return get_transient( self::get_collection_reconcile_cooldown_key( $collection_id ) ) === false;
	}

	/**
	 * Store the last Training Data tab reconciliation time for a collection.
	 *
	 * @param int $collection_id Collection ID.
	 * @return void
	 */
	private static function mark_collection_reconciled_on_tab_load( $collection_id ) {

		$collection_id = absint( $collection_id );
		if ( empty( $collection_id ) ) {
			return;
		}

		set_transient(
			self::get_collection_reconcile_cooldown_key( $collection_id ),
			time(),
			self::TAB_RECONCILE_COOLDOWN
		);
	}

	/**
	 * Get the cooldown transient key for a Training Data collection.
	 *
	 * @param int $collection_id Collection ID.
	 * @return string
	 */
	private static function get_collection_reconcile_cooldown_key( $collection_id ) {
		return 'epkb_ai_td_tab_reconcile_' . sanitize_key( EPKB_AI_Provider::get_active_provider() ) . '_' . absint( $collection_id );
	}

	/**
	 * Get the collection that is active in the current request.
	 *
	 * @param array $collections Available collections keyed by collection ID.
	 * @return int
	 */
	private static function get_requested_active_collection_id( $collections ) {

		if ( empty( $collections ) || ! is_array( $collections ) ) {
			return 0;
		}

		if ( ! empty( $_GET['active_sub_tab'] ) ) {
			$active_sub_tab = sanitize_text_field( wp_unslash( $_GET['active_sub_tab'] ) );
			if ( preg_match( '/^collection-(\d+)$/', $active_sub_tab, $matches ) ) {
				$collection_id = absint( $matches[1] );
				if ( isset( $collections[ $collection_id ] ) ) {
					return $collection_id;
				}
			}
		}

		$collection_ids = array_keys( $collections );
		return (int) reset( $collection_ids );
	}
	
	/**
	 * Get pre-loaded training data for initial page load
	 * @param int $collection_id
	 * @return array
	 */
	private static function get_preloaded_training_data( $collection_id ) {
		
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$preloaded = array();
		
		// Status tabs to pre-load (all, pending, added, updated, outdated, error)
		$statuses = array( 'all', 'pending', 'added', 'updated', 'outdated', 'error', 'skipped' );
		
		foreach ( $statuses as $status ) {
			// Pre-load first page with default settings for each status
			$args = array(
				'collection_id' => $collection_id,
				'page' => 1,
				'per_page' => 20, // Match the React component's itemsPerPage
				'orderby' => 'updated',
				'order' => 'DESC'
			);
			
			// Add status filter if not 'all'
			if ( $status !== 'all' ) {
				$args['status'] = $status;
			}
			
			// Get training data from database
			$data = $training_data_db->get_training_data_list( $args );
			if ( is_wp_error( $data ) ) {
				$preloaded[$status] = array(
					'data' => array(),
					'available_types' => array(),
					'pagination' => array(
						'page' => 1,
						'per_page' => 20,
						'total' => 0,
						'total_pages' => 1
					)
				);
				continue;
			}
			
			// Add display metadata needed by the admin table.
			foreach ( $data as &$item ) {
				$item = EPKB_AI_Utilities::prepare_training_data_item_for_display( $item );
			}
			
			// Get total count for pagination
			$total = $training_data_db->get_training_data_count( $args );
			$total_pages = ceil( $total / 20 );
			$available_types = $training_data_db->get_collection_types( $collection_id, $status );
			
			$preloaded[$status] = array(
				'data' => $data,
				'available_types' => $available_types,
				'pagination' => array(
					'page' => 1,
					'per_page' => 20,
					'total' => $total,
					'total_pages' => $total_pages
				)
			);
		}
		
		return $preloaded;
	}
	
	/**
	 * Get vector store files
	 * @return array
	 */
	private static function get_vector_store_files() {
		// This will be loaded dynamically via REST API
		// Return empty array as placeholder
		return array();
	}
}
