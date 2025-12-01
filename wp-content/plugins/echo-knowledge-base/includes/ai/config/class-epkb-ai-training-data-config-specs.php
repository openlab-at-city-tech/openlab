<?php defined( 'ABSPATH' ) || exit();

/**
 * AI Training Data Collection Configuration Specifications
 * 
 * Defines configuration for AI training data collection settings.
 * This manages how content is collected, processed, and synchronized
 * with AI services.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_AI_Training_Data_Config_Specs extends EPKB_AI_Config_Base {

	const OPTION_NAME = 'epkb_ai_training_data_configuration';
	const DEFAULT_COLLECTION_ID = 1;

	public static function get_config_fields_specifications() {

		$training_data_specs = array(
			'ai_training_data_store_name' => array(
				'name'        => 'ai_training_data_store_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'min'		  => 3,
				'max'         => 80,
				'default'     => ''
			),
			'ai_training_data_store_id' => array(  // e.g. Vector Store
				'name'        => 'ai_training_data_store_id',
				'type'        => EPKB_Input_Filter::TEXT,
				'min'		  => 3,
				'max'         => 80,
				'default'     => ''
			),
			'ai_training_data_store_post_types' => array(
				'name'        => 'ai_training_data_store_post_types',
				'type'        => EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT,
				'default'     => [EPKB_KB_Handler::KB_POST_TYPE_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID], // Default to the main knowledge base post type
				'options'     => array()  // Options will be populated when needed via get_field_options()
			),
			// Summarization feature disabled for now
			'ai_training_data_use_summary' => array(
				'name'        => 'ai_training_data_use_summary',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'  // Always off - feature disabled
			),
		);

		return $training_data_specs;
	}

	/**
	 * Get default configuration for a single training data collection
	 *
	 * @return array
	 */
	public static function get_default_collection_config() {
		return array(
			'ai_training_data_store_name' => self::get_default_collection_name( self::DEFAULT_COLLECTION_ID ),
			'ai_training_data_store_id' => '',
			'ai_training_data_store_post_types' => [EPKB_KB_Handler::KB_POST_TYPE_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID], // Default to the main knowledge base post type
			'ai_training_data_use_summary' => 'off',
		);
	}

	/**
	 * Get all training data collections
	 *
	 * @param bool $strict_mode If true, returns WP_Error on failure instead of empty array
	 * @return array|WP_Error Array of training data collection configurations or WP_Error if strict mode and error occurs
	 */
	public static function get_training_data_collections( $strict_mode = false ) {

		$collections = get_option( self::OPTION_NAME, array() );

		// Check if get_option failed (returns false on failure)
		if ( $collections === false && $strict_mode ) {
			return new WP_Error( 'option_read_failed', __( 'Failed to read training data collections from database', 'echo-knowledge-base' ) );
		}
		
		if ( ! is_array( $collections ) ) {
			if ( $strict_mode ) {
				return new WP_Error( 'invalid_data_format', __( 'Training data collections is corrupted', 'echo-knowledge-base' ) );
			}
			
			return array();
		}
		
		// If no collection exists, return empty array instead of creating a default one
		if ( empty( $collections ) ) {
			return array();
		}

		return $collections;
	}

	/**
	 * Get a specific training data collection by ID
	 *
	 * @param int $collection_id
	 * @return array|WP_Error Collection configuration or WP_Error if not found
	 */
	public static function get_training_data_collection( $collection_id ) {
		$collections = self::get_training_data_collections();
		if ( is_wp_error( $collections ) ) {
			return $collections;
		}

		return isset( $collections[$collection_id] ) ? $collections[$collection_id] : new WP_Error( 'collection_not_found', sprintf( __( 'Collection %d does not exist', 'echo-knowledge-base' ), $collection_id ) );
	}

	/**
	 * Get vector store ID from a specific training data collection
	 *
	 * @param int $collection_id Collection ID to get vector store from
	 * @return string|null Vector store ID or null if not available
	 */
	public static function get_vector_store_id_by_collection( $collection_id ) {

		if ( ! is_numeric( $collection_id ) || $collection_id <= 0 ) {
			return null;
		}

		$collection = self::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection ) || ! is_array( $collection ) ) {
			return null;
		}

		return ! empty( $collection['ai_training_data_store_id'] ) ? $collection['ai_training_data_store_id'] : null;
	}

	/**
	 * Sync collections from database with configuration
	 * Ensures all collections that exist in the database are also in the configuration
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure
	 */
	public static function sync_collections_from_database() {

		$training_data_db = new EPKB_AI_Training_Data_DB( true );
		
		// Get all collection IDs from the database
		$db_collection_ids = $training_data_db->get_all_collection_ids_from_db();
		if ( is_wp_error( $db_collection_ids ) ) {
			return $db_collection_ids;
		}
		
		// Get existing collections from configuration
		$config_collections = self::get_training_data_collections();
		if ( is_wp_error( $config_collections ) ) {
			$config_collections = array(); // Start with empty if error
		}
		
		$updated = false;
		
		// Check each database collection ID
		foreach ( $db_collection_ids as $collection_id ) {
			// If collection doesn't exist in config, add it with default settings
			if ( ! isset( $config_collections[$collection_id] ) ) {
				$config_collections[$collection_id] = array(
					'ai_training_data_store_name' => self::get_default_collection_name( $collection_id ),
					'ai_training_data_store_id' => '',
					'ai_training_data_store_post_types' => [EPKB_KB_Handler::KB_POST_TYPE_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID],
					'ai_training_data_use_summary' => 'off',
				);
				$updated = true;
			}
		}
		
		// Save updated collections if changes were made
		if ( $updated ) {
			$result = self::update_training_data_collections( $config_collections, true );
			if ( is_wp_error( $result ) || ! $result ) {
				return new WP_Error( 'save_failed', __( 'Failed to save synchronized collections', 'echo-knowledge-base' ) );
			}
		}
		
		return true;
	}

	/**
	 * Save training data collections
	 *
	 * @param array $collections Array of training data collection configurations
	 * @param bool $bypass_individual_validation Whether to bypass individual collection validation (internal use only)
	 * @return bool|WP_Error
	 */
	private static function update_training_data_collections( $collections, $bypass_individual_validation = false ) {

		$validation_result = self::validate_collections_structure( $collections );
		if ( is_wp_error( $validation_result ) ) {
			return $validation_result;
		}
		
		// Get existing collections for comparison with strict mode to prevent data loss
		$existing_collections = self::get_training_data_collections( true );
		if ( is_wp_error( $existing_collections ) ) {
			return $existing_collections;
		}
		
		// If not bypassing validation, validate each collection individually
		if ( ! $bypass_individual_validation ) {
			// Temporarily store the validated collections
			$validated_collections = array();
			
			// Validate each collection using the individual validation method
			foreach ( $collections as $collection_id => $collection_config ) {
				$validation_result = self::validate_single_collection( $collection_id, $collection_config, $existing_collections );
				if ( is_wp_error( $validation_result ) ) {
					return $validation_result;
				}
				
				$validated_collections[$collection_id] = $validation_result;
			}
			
			$collections = $validated_collections;
		}

		// Check if the new value is the same as existing to avoid false return from update_option
		$current_value = get_option( self::OPTION_NAME );
		if ( $current_value !== false && $current_value === $collections ) {
			return true;
		}

		return update_option( self::OPTION_NAME, $collections );
	}

	/**
	 * Save a specific training data collection
	 *
	 * @param int $collection_id
	 * @param array $collection_config
	 * @return bool|WP_Error
	 */
	public static function update_training_data_collection( $collection_id, $collection_config ) {
		
		$existing_collections = self::get_training_data_collections( true );
		if ( is_wp_error( $existing_collections ) ) {
			return $existing_collections;
		}
		
		// Validate the single collection
		$validated_config = self::validate_single_collection( $collection_id, $collection_config, $existing_collections );
		if ( is_wp_error( $validated_config ) ) {
			return $validated_config;
		}
		
		$existing_collections[$collection_id] = $validated_config;
		
		// Save all collections, bypassing individual validation since we already validated this one
		return self::update_training_data_collections( $existing_collections, true );
	}

	/**
	 * Delete a training data collection
	 *
	 * @param int $collection_id
	 * @return bool|WP_Error
	 */
	public static function delete_training_data_collection( $collection_id ) {

		if ( ! is_numeric( $collection_id ) || $collection_id <= 0 ) {
			return new WP_Error( 'invalid_collection_id', __( 'Invalid collection ID', 'echo-knowledge-base' ) );
		}

		$collections = self::get_training_data_collections( true );
		if ( is_wp_error( $collections ) ) {
			return $collections;
		}

		// Check if collection exists
		if ( ! isset( $collections[$collection_id] ) ) {
			return new WP_Error( 'collection_not_found', sprintf( __( 'Collection %d does not exist', 'echo-knowledge-base' ), $collection_id ) );
		}

		// Check if collection has data
		if ( self::collection_has_data( $collection_id ) ) {
			return new WP_Error( 'collection_has_data', __( 'Cannot delete collection with existing training data', 'echo-knowledge-base' ) );
		}

		unset( $collections[$collection_id] );

		$result = self::update_training_data_collections( $collections );
		if ( is_wp_error( $result ) || ! $result ) {
			return $result;
		}

		// Reset kb_ai_collection_id in all KB configurations that were using this collection
		$kb_config_obj = new EPKB_KB_Config_DB();
		$all_kb_configs = $kb_config_obj->get_kb_configs();

		foreach ( $all_kb_configs as $kb_id => $kb_config ) {
			// Check if this KB is using the deleted collection
			if ( isset( $kb_config['kb_ai_collection_id'] ) && $kb_config['kb_ai_collection_id'] == $collection_id ) {
				// Reset to default
				$kb_config['kb_ai_collection_id'] = EPKB_AI_Training_Data_Config_Specs::DEFAULT_COLLECTION_ID;
				$update_result = $kb_config_obj->update_kb_configuration( $kb_id, $kb_config );
				if ( is_wp_error( $update_result ) ) {
					EPKB_Logging::add_log( 'Failed to reset kb_ai_collection_id after collection deletion', 'KB ID: ' . $kb_id . ', Collection ID: ' . $collection_id, $update_result );
				}
			}
		}

		return true;
	}

	/**
	 * Get next available collection ID to use for a new collection
	 *
	 * @return int|WP_Error
	 */
	public static function get_next_collection_id() {
		$collections = self::get_training_data_collections();
		if ( is_wp_error( $collections ) ) {
			return $collections;
		}

		if ( empty( $collections ) ) {
			return 1; // Start with ID 1 if no collections exist
		}
		
		return max( array_keys( $collections ) ) + 1;
	}

	/**
	 * Get field options dynamically
	 * Overrides parent method to provide collection-specific options
	 *
	 * @param string $field_name
	 * @return array
	 */
	public static function get_field_options( $field_name ) {
		switch ( $field_name ) {
			case 'ai_training_data_store_post_types':
				return EPKB_AI_Utilities::get_available_post_types_for_ai();
			default:
				return parent::get_field_options( $field_name );
		}
	}

	/**
	 * Sanitize collection configuration
	 *
	 * @param array $collection_config
	 * @return array|WP_Error
	 */
	private static function sanitize_collection_config( $collection_config ) {
		
		// Validate required fields before sanitization
		if ( empty( $collection_config['ai_training_data_store_name'] ) ) {
			return new WP_Error( 'missing_required_field', __( 'Collection name is required', 'echo-knowledge-base' ) );
		}
		
		// Use parent's sanitize_config method
		$sanitized_config = parent::sanitize_config( $collection_config );
		if ( is_wp_error( $sanitized_config ) ) {
			return $sanitized_config;
		}
		
		// Additional validation for specific fields after sanitization
		if ( empty( $sanitized_config['ai_training_data_store_name'] ) ) {
			return new WP_Error( 'invalid_name', __( 'Collection name cannot be empty', 'echo-knowledge-base' ) );
		}
		
		return $sanitized_config;
	}
	
	/**
	 * Check if a collection has associated training data
	 *
	 * @param int $collection_id
	 * @return bool
	 */
	private static function collection_has_data( $collection_id ) {
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$count = $training_data_db->get_training_data_count( array( 'collection_id' => $collection_id ) );
		return $count > 0;
	}

	/**
	 * Validate a single collection
	 *
	 * @param int $collection_id
	 * @param array $collection_config
	 * @param array $existing_collections
	 * @return array|WP_Error Validated config or error
	 */
	private static function validate_single_collection( $collection_id, $collection_config, $existing_collections ) {

		// Validate collection ID
		if ( ! is_numeric( $collection_id ) || $collection_id <= 0 ) {
			return new WP_Error( 'invalid_collection_id', __( 'Collection ID must be a positive number', 'echo-knowledge-base' ) );
		}

		// Validate collection config
		if ( ! is_array( $collection_config ) ) {
			return new WP_Error( 'invalid_config', __( 'Collection configuration must be an array', 'echo-knowledge-base' ) );
		}

		// Check if we're updating an existing collection with data
		if ( isset( $existing_collections[$collection_id] ) ) {
			$has_data = self::collection_has_data( $collection_id );
			if ( $has_data ) {
				$allow_vector_store_override = isset( $collection_config['override_vector_store_id'] );
				
				// For existing collections with data, validate critical fields haven't changed
				$critical_fields = array( 'ai_training_data_store_id' );
				foreach ( $critical_fields as $field ) {
					if ( isset( $collection_config[$field] ) &&
						!empty( $existing_collections[$collection_id][$field] ) &&
						$collection_config[$field] !== $existing_collections[$collection_id][$field] ) {
						// Allow override if flag is set and field is ai_training_data_store_id
						if ( $field === 'ai_training_data_store_id' && $allow_vector_store_override ) {
							continue;
						}
						return new WP_Error( 'cannot_modify_critical_field',
							sprintf( __( 'Cannot modify %s for collection with existing data', 'echo-knowledge-base' ), $field ) );
					}
				}
			}
		}

		// Remove the override flag before sanitizing
		unset( $collection_config['override_vector_store_id'] );
		
		// Sanitize the collection config
		return self::sanitize_collection_config( $collection_config );
	}

	/**
	 * Validate collections structure
	 *
	 * @param array $collections
	 * @return true|WP_Error
	 */
	private static function validate_collections_structure( $collections ) {

		foreach ( $collections as $id => $config ) {
			if ( ! is_numeric( $id ) || $id <= 0 ) {
				return new WP_Error( 'invalid_structure', __( 'Collection IDs must be positive integers', 'echo-knowledge-base' ) );
			}
			if ( ! is_array( $config ) ) {
				return new WP_Error( 'invalid_structure', __( 'Each collection must be an array', 'echo-knowledge-base' ) );
			}
		}

		return true;
	}

	public static function get_default_collection_name( $collection_id ) {
		return esc_html__( 'Data Collection', 'echo-knowledge-base' ) . ' ' . $collection_id;
	}
}