<?php defined( 'ABSPATH' ) || exit();

/**
 * AI Widget Configuration Specifications
 * 
 * Defines AI widget-related configuration settings. This demonstrates how
 * the base configuration class can be extended for specific feature sets.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_AI_Chat_Widget_Config_Specs extends EPKB_AI_Config_Base {

	const OPTION_NAME_PREFIX = 'epkb_ai_widget_configuration_';
	const DEFAULT_WIDGET_ID = 1;
	
	// Parent class compatibility - not used for widgets but required by parent
	const OPTION_NAME = 'epkb_ai_widget_configuration_1';

	/**
	 * Get all AI widget configuration specifications
	 * These define the schema for each widget
	 *
	 * @return array
	 */
	public static function get_config_fields_specifications() {

		$widget_specs = array(

			// general settings
			'widget_enabled' => array(
				'name'        => 'widget_enabled',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'widget_name' => array(
				'name'        => 'widget_name',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Default Chat Widget', 'echo-knowledge-base' ),
				'min'         => 1,
				'max'         => 100
			),
			
			// Colors
			'launcher_background_color' => array(
				'name'        => 'launcher_background_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#0073aa'
			),
			'widget_header_background_color' => array(
				'name'        => 'widget_header_background_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#0073aa'
			),
			
			// Text customization
			'widget_header_title' => array(
				'name'        => 'widget_header_title',
				'type'        => EPKB_Input_Filter::TEXT,
				'min'         => 1,
				'max'         => 100,
				'default'     => __( 'AI Assistant', 'echo-knowledge-base' )
			),
			'input_placeholder_text' => array(
				'name'        => 'input_placeholder_text',
				'required'    => false,
				'min'         => 1,
				'max'         => 100,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Type your message...', 'echo-knowledge-base' )
			),
			'welcome_message' => array(
				'name'        => 'welcome_message',
				'type'        => EPKB_Input_Filter::WP_EDITOR,
				'min'         => 1,
				'max'         => 1000,
				'default'     => __( 'Hello! How can I help you today?', 'echo-knowledge-base' )
			),
			
			// Error messages
			'error_generic_message' => array(
				'name'        => 'error_generic_message',
				'type'        => EPKB_Input_Filter::TEXT,
				'min'         => 1,
				'max'         => 200,
				'default'     => __( 'Sorry, something went wrong. Please try again.', 'echo-knowledge-base' )
			),
			'error_network_message' => array(
				'name'        => 'error_network_message',
				'type'        => EPKB_Input_Filter::TEXT,
				'min'         => 1,
				'max'         => 200,
				'default'     => __( 'Connection error. Please check your connection and try again.', 'echo-knowledge-base' )
			),
			'error_timeout_message' => array(
				'name'        => 'error_timeout_message',
				'type'        => EPKB_Input_Filter::TEXT,
				'min'         => 1,
				'max'         => 200,
				'default'     => __( 'Delayed response. Please try again.', 'echo-knowledge-base' )
			),
			'error_rate_limit_message' => array(
				'name'        => 'error_rate_limit_message',
				'type'        => EPKB_Input_Filter::TEXT,
				'min'         => 1,
				'max'         => 200,
				'default'     => __( 'Too many requests. Please wait a moment and try again later.', 'echo-knowledge-base' )
			),
		);

		return $widget_specs;
	}

	/**
	 * Get configuration for a specific widget
	 *
	 * @param int $widget_id Widget ID
	 * @return array Widget configuration
	 */
	public static function get_widget_config( $widget_id = 1 ) {

		$widget_id = absint( $widget_id );
		if ( empty( $widget_id ) ) {
			$widget_id = self::DEFAULT_WIDGET_ID;
		}

		// Ensure default widget exists
		if ( $widget_id == self::DEFAULT_WIDGET_ID ) {
			self::ensure_default_widget_exists();
		}

		$option_name = self::OPTION_NAME_PREFIX . $widget_id;
		$widget_config = get_option( $option_name, null );

		// If widget doesn't exist, return default configuration
		if ( $widget_config === null ) {
			return self::get_default_config();
		}

		// Ensure all fields exist with proper defaults
		return wp_parse_args( $widget_config, self::get_default_config() );
	}

	/**
	 * Get all widget configurations
	 *
	 * @return array Array of widget configurations indexed by widget ID
	 */
	public static function get_all_widget_configs() {

		$ai_config = EPKB_AI_Config_Specs::get_config();
		$widget_list = is_array( $ai_config['ai_chat_widgets'] ) ? $ai_config['ai_chat_widgets'] : array( self::DEFAULT_WIDGET_ID );
		if ( ! in_array( self::DEFAULT_WIDGET_ID, $widget_list ) ) {
			array_unshift( $widget_list, self::DEFAULT_WIDGET_ID );
		}

		$widgets = array();
		foreach ( $widget_list as $widget_id ) {
			$widgets[ $widget_id ] = self::get_widget_config( $widget_id );
		}

		return $widgets;
	}

	/**
	 * Update configuration for a specific widget
	 *
	 * @param int $widget_id Widget ID
	 * @param array $new_config New configuration values
	 * @return array|WP_Error Updated configuration or error
	 */
	public static function update_widget_config( $widget_id, $new_config ) {
		$widget_id = absint( $widget_id );
		if ( empty( $widget_id ) ) {
			$widget_id = self::DEFAULT_WIDGET_ID;
		}

		// Get current widget configuration
		$current_config = self::get_widget_config( $widget_id );

		// Validate and sanitize new configuration
		$specs = self::get_config_fields_specifications();
		$validated_config = self::sanitize_config( $new_config, $current_config, $specs );
		if ( is_wp_error( $validated_config ) ) {
			return $validated_config;
		}

		// Save to database
		$option_name = self::OPTION_NAME_PREFIX . $widget_id;
		$result = update_option( $option_name, $validated_config, true );
		if ( ! $result && $validated_config !== get_option( $option_name ) ) {
			return new WP_Error( 'save_failed', __( 'Failed to save widget configuration', 'echo-knowledge-base' ) );
		}

		// Clear cache
		wp_cache_delete( $option_name, 'options' );

		// Update widget list if this is a new widget
		self::update_widget_list( $widget_id );

		return $validated_config;
	}

	/**
	 * Reset widget configuration to defaults
	 *
	 * @param int $widget_id Widget ID
	 * @return array|WP_Error Default configuration or error
	 */
	public static function reset_widget_config( $widget_id ) {

		$widget_id = absint( $widget_id );
		if ( empty( $widget_id ) ) {
			$widget_id = self::DEFAULT_WIDGET_ID;
		}

		$default_config = self::get_default_config();
		$option_name = self::OPTION_NAME_PREFIX . $widget_id;
		
		$result = update_option( $option_name, $default_config, true );
		if ( ! $result && $default_config !== get_option( $option_name ) ) {
			return new WP_Error( 'reset_failed', __( 'Failed to reset widget configuration', 'echo-knowledge-base' ) );
		}

		// Clear cache
		wp_cache_delete( $option_name, 'options' );

		return $default_config;
	}

	/**
	 * Ensure the default widget (ID 1) exists
	 */
	private static function ensure_default_widget_exists() {

		$option_name = self::OPTION_NAME_PREFIX . self::DEFAULT_WIDGET_ID;
		$widget_config = get_option( $option_name, null );

		// If default widget doesn't exist, create it
		if ( $widget_config === null ) {
			$default_config = self::get_default_config();
			update_option( $option_name, $default_config, true );
			
			// Ensure widget ID 1 is in the widget list
			self::update_widget_list( self::DEFAULT_WIDGET_ID );
		}
	}

	/**
	 * Delete widget configuration
	 *
	 * @param int $widget_id Widget ID
	 * @return bool|WP_Error Success or error
	 */
	public static function delete_widget_config( $widget_id ) {

		$widget_id = absint( $widget_id );
		
		// Cannot delete default widget
		if ( $widget_id == self::DEFAULT_WIDGET_ID ) {
			return new WP_Error( 'cannot_delete_default', __( 'Cannot delete the default widget', 'echo-knowledge-base' ) );
		}

		$option_name = self::OPTION_NAME_PREFIX . $widget_id;
		$result = delete_option( $option_name );
		
		if ( ! $result ) {
			return new WP_Error( 'delete_failed', __( 'Failed to delete widget configuration', 'echo-knowledge-base' ) );
		}

		// Remove from widget list
		$ai_config = EPKB_AI_Config_Specs::get_config();
		$widget_list = is_array( $ai_config['ai_chat_widgets'] ) ? $ai_config['ai_chat_widgets'] : array();
		$widget_list = array_diff( $widget_list, array( $widget_id ) );
		sort( $widget_list );
		EPKB_AI_Config_Specs::update_config_value( 'ai_chat_widgets', $widget_list );

		// Clear cache
		wp_cache_delete( $option_name, 'options' );

		return true;
	}

	/**
	 * Update the widget list in AI configuration
	 *
	 * @param int $widget_id Widget ID to add to the list
	 */
	private static function update_widget_list( $widget_id ) {

		$widget_id = absint( $widget_id );
		
		// Get current widget list
		$ai_config = EPKB_AI_Config_Specs::get_config();
		$widget_list = is_array( $ai_config['ai_chat_widgets'] ) ? $ai_config['ai_chat_widgets'] : array();
		
		// Add widget ID if not already in list
		if ( ! in_array( $widget_id, $widget_list ) ) {
			$widget_list[] = $widget_id;
			sort( $widget_list );
			EPKB_AI_Config_Specs::update_config_value( 'ai_chat_widgets', $widget_list );
		}
	}
}