<?php

/**
 * Control front-end editor for KB page configuration
 */
class EPKB_Editor_Controller {

	function __construct() {
		add_action( 'wp_ajax_eckb_apply_editor_changes', array( $this, 'apply_editor_changes' ) );
		add_action( 'wp_ajax_nopriv_eckb_apply_editor_changes', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_editor_error', array( 'EPKB_Controller', 'handle_report_admin_error' ) );
		add_action( 'wp_ajax_nopriv_epkb_editor_error', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * User clicked to Save their frontend changes
	 */
	public function apply_editor_changes() {

		// verify that request is authentic and user has correct permissions
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		// get current KB ID
		$editor_kb_id = EPKB_Utilities::post( 'epkb_editor_kb_id' );
		if ( empty($editor_kb_id) || ! EPKB_Utilities::is_positive_int( $editor_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Invalid parameters. Please refresh your page.', 'echo-knowledge-base' ) );
		}

		// get type of page we are saving
		$page_type = EPKB_Utilities::post( 'page_type' );
		if ( empty($page_type) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Invalid parameters. Please refresh your page.', 'echo-knowledge-base' ) );
		}

		// get new KB configuration
		$new_config = EPKB_Utilities::post( 'kb_config', [], 'db-config-json' );
		if ( empty( $new_config ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Invalid parameters. Please refresh your page.', 'echo-knowledge-base' ) );
		}

		// get current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $editor_kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
		}

		// overwrite current KB configuration with new configuration from this editor
		$new_config = array_merge( $orig_config, $new_config );

		// save based on type of page
		switch( $page_type ) {
			case 'main-page':
				$this->update_main_page( $editor_kb_id, $orig_config, $new_config );
				break;
			case 'article-page':
				$this->update_article_page( $editor_kb_id, $orig_config, $new_config );
				break;
			case 'archive-page':
			case 'search-page':
				$this->update_other_page( $editor_kb_id, $orig_config, $new_config );
				break;
		}

		EPKB_Core_Utilities::add_kb_flag( 'settings_tab_visited' );

		wp_die( wp_json_encode( array( 'message' => esc_html__('Configuration Saved', 'echo-knowledge-base') ) ) );
	}

	/**
	 * Save KB Main Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 */
	private function update_main_page( $editor_kb_id, $orig_config, $new_config ) {

		$chosen_preset = empty( $new_config['theme_presets'] ) || $new_config['theme_presets'] == 'current' ? '' : $new_config['theme_presets'];
		$new_config['theme_name'] = $chosen_preset;

		// if user selected a theme presets then Copy search setting from main to article and update icons
		if ( ! empty( $chosen_preset ) ) {
			$new_config = EPKB_KB_Wizard_Themes::copy_search_mp_to_ap( $new_config );
			EPKB_Core_Utilities::get_or_update_new_category_icons( $new_config, $chosen_preset, true );
		}

		// detect user changed kb template
		if ( $orig_config['templates_for_kb'] != $new_config['templates_for_kb'] ) {
			$new_config['article_content_enable_article_title'] = $new_config['templates_for_kb'] == 'current_theme_templates' ? 'off' : 'on';
		}

		EPKB_Core_Utilities::start_update_kb_configuration( $editor_kb_id, $new_config );
	}

	/**
	 * Save KB Article Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 */
	private function update_article_page( $editor_kb_id, $orig_config, $new_config ) {

		// start_update_kb_configuration() expects article_sidebar_component_priority to be an array in $kb_config
		$article_sidebar_component_priority = array();
		foreach( EPKB_KB_Config_Specs::get_sidebar_component_priority_names() as $component ) {
			if ( isset( $new_config['article_sidebar_component_priority'][$component] ) ) {
				$article_sidebar_component_priority[$component] = sanitize_text_field( $new_config['article_sidebar_component_priority'][$component] );
			}
		}
		$new_config['article_sidebar_component_priority'] = $article_sidebar_component_priority;

		// update KB and add-ons configuration
		EPKB_Core_Utilities::start_update_kb_configuration( $editor_kb_id, $new_config );
	}

	/**
	 * Save KB Archive Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 */
	private function update_other_page( $editor_kb_id, $orig_config, $new_config ) {

		// get current KB configuration from add-ons
		$orig_config = apply_filters( 'eckb_all_editors_get_current_config', $orig_config, $editor_kb_id );
		if ( empty( $orig_config ) || count( $orig_config ) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 149 ) );
		}

		$new_config = array_merge( $orig_config, $new_config );

		// update KB and add-ons configuration
		$update_kb_msg = EPKB_Core_Utilities::prepare_update_to_kb_configuration( $editor_kb_id, $orig_config, $new_config );
		if ( ! empty( $update_kb_msg ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Could not save the new configuration.', 'echo-knowledge-base' ) . $update_kb_msg . '(32) ' . EPKB_Utilities::contact_us_for_support() );
		}
	}
}