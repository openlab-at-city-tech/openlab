<?php

/**
 * Control for KB Configuration admin page
 */
class EPKB_KB_Config_Controller {

	public function __construct() {

		add_action( 'wp_ajax_epkb_wpml_enable', array( $this, 'wpml_enable' ) );
		add_action( 'wp_ajax_nopriv_epkb_wpml_enable', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_eckb_update_category_slug_parameter', array( $this, 'update_category_slug_param' ) );
		add_action( 'wp_ajax_nopriv_eckb_update_query_parameter', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_eckb_update_tag_slug_parameter', array( $this, 'update_tag_slug_param' ) );
		add_action( 'wp_ajax_nopriv_eckb_update_query_parameter', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_preload_fonts', array( $this, 'preload_fonts' ) );
		add_action( 'wp_ajax_nopriv_epkb_preload_fonts', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_enable_legacy_open_ai', array( $this, 'enable_legacy_open_ai' ) );
		add_action( 'wp_ajax_nopriv_epkb_enable_legacy_open_ai', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_load_resource_links_icons', array( $this, 'load_resource_links_icons' ) );
		add_action( 'wp_ajax_nopriv_epkb_load_resource_links_icons', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_load_general_typography', array( $this, 'load_general_typography' ) );
		add_action( 'wp_ajax_nopriv_epkb_load_general_typography', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_save_access_control', array( 'EPKB_Admin_UI_Access', 'save_access_control' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_access_control', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_apply_settings_changes', array( $this, 'apply_settings_changes' ) );
		add_action( 'wp_ajax_nopriv_epkb_apply_settings_changes', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_save_tools_settings', array( $this, 'save_tools_settings' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_tools_settings', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Triggered when user clicks to toggle wpml setting.
	 */
	public function wpml_enable() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// get KB ID
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 410 ) );
		}

		$wpml_enable = EPKB_Utilities::post( 'wpml_enable' );
		if ( $wpml_enable != 'on' ) {
			$wpml_enable = 'off';
		}

		$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'wpml_is_enabled', $wpml_enable );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 412, $result ) );
		}

		// flush rules in case category and/or tag slug has value
		update_option( 'epkb_flush_rewrite_rules', true );

		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}

	/**
	 * Triggered when user sets category slug.
	 */
	public function update_category_slug_param() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// get KB ID
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 410 ) );
		}

		$category_slug_param = EPKB_Utilities::post( 'category_slug_param' );

		// allow only letters, numbers, dash, underscore
		$category_slug_param = preg_replace( '/[^a-zA-Z0-9-_]/', '', $category_slug_param );

		$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'category_slug', $category_slug_param );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 412, $result ) );
		}

		update_option( 'epkb_flush_rewrite_rules', true );

		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}

	/**
	 * Triggered when user sets tag slug.
	 */
	public function update_tag_slug_param() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// get KB ID
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 410 ) );
		}

		$tag_slug_param = EPKB_Utilities::post( 'tag_slug_param' );

		// allow only letters, numbers, dash, underscore
		$tag_slug_param = preg_replace( '/[^a-zA-Z0-9-_]/', '', $tag_slug_param );

		$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'tag_slug', $tag_slug_param );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 412, $result ) );
		}

		update_option( 'epkb_flush_rewrite_rules', true );

		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}

	/**
	 * Triggered when user clicks to toggle Preload Fonts setting.
	 */
	public function preload_fonts() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$preload_fonts = EPKB_Utilities::post( 'preload_fonts', 'on' ) == 'on';

		$result = EPKB_Core_Utilities::update_kb_flag( 'preload_fonts', $preload_fonts );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 415, $result ) );
		}

		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}

	/**
	 * Triggered when user clicks to toggle OpenAI setting.
	 */
	public function enable_legacy_open_ai() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$enable_legacy_open_ai = EPKB_Utilities::post( 'enable_legacy_open_ai', 'off' ) == 'on';

		$result = EPKB_Core_Utilities::update_kb_flag( 'enable_legacy_open_ai', $enable_legacy_open_ai );
		if ( is_wp_error( $result ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 418, $result ) );
		}

		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}

	/**
	 * Triggered when user clicks to Choose Icon setting for Resource Links feature.
	 */
	public static function load_resource_links_icons() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$active_icon = EPKB_Utilities::post( 'active_icon' );

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => 'success',
			'data'    => EPKB_Icons::get_icons_pack_html( false, $active_icon ),
		) ) );
	}

	/**
	 * Triggered when user clicks to Choose General Typography setting.
	 */
	public static function load_general_typography() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$active_font_family = EPKB_Utilities::post( 'active_font_family' );
		if ( empty( $active_font_family ) ) {
			$active_font_family = 'inherit';
		}

		ob_start();

		EPKB_HTML_Elements::custom_dropdown( [
			'label' => esc_html__( 'Font Family', 'echo-knowledge-base' ),
			'name' => 'general_typography_font_family',
			'specs' => '',
			'value' => $active_font_family,
			'input_group_class' => '',
			'options' => array_merge( array( 'inherit' => 'inherit' ) , array_combine( EPKB_Typography::get_google_fonts_family_list(), EPKB_Typography::get_google_fonts_family_list() ) ),
		] );

		$font_families = ob_get_clean();

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => 'success',
			'data'    => $font_families,
		) ) );
	}

	/**
	 * Handle actions that need reload of the page - KB Configuration page and other from addons
	 */
	public static function handle_form_actions() {

		$action = EPKB_Utilities::post( 'action' );
		if ( empty( $action ) || ! in_array( $action, ['epkb_export_knowledge_base', 'epkb_import_knowledge_base', 'emkb_archive_knowledge_base_v2',
														'emkb_activate_knowledge_base_v2', 'emkb_delete_knowledge_base_v2'] ) ) {
			return [];
		}

		EPKB_Utilities::ajax_verify_nonce_and_capability_or_error_die( EPKB_Utilities::ADMIN_CAPABILITY );

		// retrieve KB ID we are saving
		$kb_id = EPKB_Utilities::post( 'emkb_kb_id' );
		$kb_id = empty( $kb_id ) ? EPKB_Utilities::post( 'kb_id' ) : $kb_id;
		$kb_id = EPKB_Utilities::sanitize_get_id( $kb_id );
		if ( empty( $kb_id ) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "received invalid kb_id for action " . $action, $kb_id );
			return [ 'error' => EPKB_Utilities::report_generic_error( 2 ) ];
		}

		// EXPORT CONFIG
		if ( $action == 'epkb_export_knowledge_base' ) {
			$export = new EPKB_Export_Import();
			$message = $export->download_export_file( $kb_id );

			// stop php because we sent the file
			if ( empty( $message ) ) {
				exit;
			}

			return $message;
		}

		// IMPORT CONFIG
		if ( $action == 'epkb_import_knowledge_base' ) {
			$import = new EPKB_Export_Import();
			return $import->import_kb_config( $kb_id );
		}

		// retrieve current KB configuration
		$current_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $current_config ) ) {
			EPKB_Logging::add_log( 'Could not retrieve KB config when manage KB', $kb_id );
			return ['error' => EPKB_Utilities::report_generic_error( 5, $current_config )];
		}

		// Multiple KBs actions
		$message = apply_filters( 'eckb_handle_manage_kb_actions', [], $kb_id, $current_config );

		return is_array( $message ) ? $message : [];
	}

	/**
	 * Handle update for KB Config Options
	 */
	public function apply_settings_changes() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		// ensure that user has correct permissions
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 414 ) );
		}

		// get new KB configuration
		$new_config = EPKB_Utilities::post( 'kb_config', [], 'db-config-json' );
		if ( empty( $new_config ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Invalid parameters. Please refresh your page.', 'echo-knowledge-base' ) );
		}

		// if we are not showing all settings in UI because user is using FE Editor, we need to add some default values
		if ( count( $new_config ) < 100 ) {

			// get current KB configuration
			$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
			if ( is_wp_error( $orig_config ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
			}
			// get current KB configuration from add-ons
			$orig_config = EPKB_Core_Utilities::get_add_ons_config( $kb_id, $orig_config );
			if ( $orig_config === false ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 149 ) );
			}
			$new_config = array_merge( $orig_config, $new_config );

			// update KB and add-ons configuration
			EPKB_Core_Utilities::prepare_update_to_kb_configuration( $kb_id, $orig_config, $new_config, true );

		// save all settings from backend
		} else {

			// prepare article sidebar component priority
			$new_config['article_sidebar_component_priority'] = self::convert_ui_data_to_article_sidebar_component_priority( $new_config );

			EPKB_Core_Utilities::start_update_kb_configuration( $kb_id, $new_config );
		}

		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}

	/**
	 * In settings UI we display controls which represent individual component priority settings; while storing them as single combined setting (see 'article_sidebar_component_priority' in EPKB_KB_Config_Specs class)
	 * @param $new_config
	 * @return array
	 */
	public static function convert_ui_data_to_article_sidebar_component_priority( $new_config ) {
		$article_sidebar_component_priority = array();
		foreach( EPKB_KB_Config_Specs::get_sidebar_component_priority_names() as $component ) {
			$article_sidebar_component_priority[ $component ] = '0';

			// set component priority
			foreach ( [ '_left', '_right' ] as $sidebar_suffix ) {

				// Categories and Articles Navigation
				if ( $component == 'nav_sidebar' . $sidebar_suffix && isset( $new_config[ 'nav_sidebar' . $sidebar_suffix ] ) && $new_config[ 'nav_sidebar' . $sidebar_suffix ] > 0 ) {
					$article_sidebar_component_priority[ $component ] = sanitize_text_field( $new_config[ 'nav_sidebar' . $sidebar_suffix ] );

				// Widgets from KB Sidebar
				} else if ( $component == 'kb_sidebar' . $sidebar_suffix && isset( $new_config[ 'kb_sidebar' . $sidebar_suffix ] ) && $new_config[ 'kb_sidebar' . $sidebar_suffix ] > 0 ) {
					$article_sidebar_component_priority[ $component ] = sanitize_text_field( $new_config[ 'kb_sidebar' . $sidebar_suffix ] );

				// Table of Contents ( TOC )
				} else if ( $component == 'toc' . $sidebar_suffix && isset( $new_config[ 'toc' . $sidebar_suffix ] ) && $new_config[ 'toc' . $sidebar_suffix ] > 0 ) {
					$article_sidebar_component_priority[ $component ] = sanitize_text_field( $new_config[ 'toc' . $sidebar_suffix ] );
				}
			}
		}
		$article_sidebar_component_priority['toc_content'] = sanitize_text_field( $new_config['toc_content'] );

		return $article_sidebar_component_priority;
	}

	/**
	 * Save tools settings
	 */
	public function save_tools_settings() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// get KB ID
		$kb_id = (int)EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 410 ) );
		}

		// get current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
		}

		$new_config = array();

		// Handle KB Main Page Title setting
		if ( EPKB_Utilities::post( 'template_main_page_display_title' ) !== null ) {
			$new_config['template_main_page_display_title'] = EPKB_Utilities::post( 'template_main_page_display_title' ) === 'on' ? 'on' : 'off';
		}

		// Handle Typography settings
		$font_family = EPKB_Utilities::post( 'general_typography' );
		if ( ! empty( $font_family ) && is_array( $font_family ) && isset( $font_family['font-family'] ) ) {
			$new_config['general_typography'] = $orig_config['general_typography'];
			$new_config['general_typography']['font-family'] = sanitize_text_field( $font_family['font-family'] );
		}

		// Handle KB Nickname
		$kb_name = EPKB_Utilities::post( 'kb_name' );
		if ( ! empty( $kb_name ) ) {
			$new_config['kb_name'] = sanitize_text_field( $kb_name );
		}

		// Handle Frontend Editor Toggle
		if ( EPKB_Utilities::post( 'frontend_editor_switch_visibility_toggle' ) !== null ) {
			$new_config['frontend_editor_switch_visibility_toggle'] = EPKB_Utilities::post( 'frontend_editor_switch_visibility_toggle' ) === 'on' ? 'on' : 'off';
		}

		// save Modular Main Page custom CSS if defined
		$custom_css = EPKB_Utilities::post( 'epkb_ml_custom_css' );
		$new_config['modular_main_page_custom_css_toggle'] = 'off';
		if ( ! empty( $custom_css ) ) {
			$ml_custom_css = trim( wp_kses( $custom_css, [] ) );
			$new_config['modular_main_page_custom_css_toggle'] = empty( $ml_custom_css ) ? 'off' : 'on';
			if ( $new_config['modular_main_page_custom_css_toggle'] == 'on' ) {
				$result = EPKB_Utilities::save_kb_option( $kb_id, 'epkb_ml_custom_css', $ml_custom_css );
				if ( is_wp_error( $result ) ) {
					EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 35, $result ) );
				}
			}
		}

		// If we have config changes to save
		if ( ! empty( $new_config ) ) {
			$new_config = array_merge( $orig_config, $new_config );
			$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_config );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 412, $result ) );
			}
		}

		EPKB_Utilities::ajax_show_info_die( esc_html__( 'Configuration saved', 'echo-knowledge-base' ) );
	}
}
