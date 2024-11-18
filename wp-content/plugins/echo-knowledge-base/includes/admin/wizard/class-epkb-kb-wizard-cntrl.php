<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Cntrl {

	function __construct() {
		add_action( 'wp_ajax_epkb_apply_wizard_changes', array( $this, 'apply_wizard_changes' ) );
		add_action( 'wp_ajax_nopriv_epkb_apply_wizard_changes', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_wizard_update_order_view', array( $this, 'wizard_update_order_view' ) );
		add_action( 'wp_ajax_nopriv_epkb_wizard_update_order_view', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_apply_setup_wizard_changes',  array( $this, 'apply_setup_wizard_changes' ) );
		add_action( 'wp_ajax_nopriv_epkb_apply_setup_wizard_changes', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_report_admin_error',  array( 'EPKB_Controller', 'handle_report_admin_error' ) );
		add_action( 'wp_ajax_nopriv_epkb_report_admin_error', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Apply GLOBAL URL OR ORDERING WIZARD CHANGES
	 * @return void
	 */
	public function apply_wizard_changes() {

		// get Wizard type
		$wizard_type = EPKB_Utilities::post( 'wizard_type' );
		if ( empty( $wizard_type ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 164 ) );
		}

		// wp_die if nonce invalid or user does not have correct permission
		if ( $wizard_type == 'ordering' ) {
			EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_order_articles_write' );
		} else if ( $wizard_type == 'global' ) {   // KB URLs page
			EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );
		} else {
			EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();
		}

		// get current KB ID
		$wizard_kb_id = EPKB_Utilities::post( 'epkb_wizard_kb_id' );
		if ( empty( $wizard_kb_id ) || ! EPKB_Utilities::is_positive_int( $wizard_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 163 ) );
		}

		// get new KB template related configuration
		$new_config_post = EPKB_Utilities::post( 'kb_config', [], 'db-config' );
		if ( empty( $new_config_post ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 165 ) );
		}

		// get Wizard type specific filter
		switch( $wizard_type ) {
			case 'ordering':
				$wizard_fields = EPKB_KB_Wizard_Ordering::$ordering_fields;
				break;
			case 'global':
				$wizard_fields = EPKB_KB_Wizard_Global::$global_fields;
				break;
			default:
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 166 ) );
				return;
		}

		// filter fields from Wizard to ensure we are saving only configuration that is applicable for this Wizard
		$new_config = array();
		foreach( $new_config_post as $field_name => $field_value ) {
			if ( in_array( $field_name, $wizard_fields ) ) {
				$new_config[$field_name] = $field_value;
			}
		}

		// get current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $wizard_kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
		}

		// get current Add-ons configuration
		$orig_config = apply_filters( 'epkb_all_wizards_get_current_config', $orig_config, $wizard_kb_id );
		if ( empty( $orig_config ) || count( $orig_config ) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 169, $orig_config ) );
		}

		// overwrite current KB configuration with new configuration from this Wizard
		$new_config = array_merge( $orig_config, $new_config );

		// call Wizard type specific saving function
		switch( $wizard_type ) {
			case 'ordering':
				$this->apply_ordering_wizard_changes( $orig_config, $new_config );
				break;
			case 'global':
				$this->apply_url_wizard_changes( $orig_config, $new_config );
				break;
			default:
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 170 ) );
				return;
		}
	}

	/**
	 * Apply URL Wizard changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 */
	private function apply_url_wizard_changes( $orig_config, $new_config ) {

		// make sure currently active KB Main Page is at the top of KB Main Pages list if the current KB has more than one Main Page
		$active_kb_main_page_id = EPKB_Utilities::post( 'kb_main_page_id' );
		if ( count( $orig_config['kb_main_pages'] ) > 1 && isset( $orig_config['kb_main_pages'][$active_kb_main_page_id] ) ) {
			$active_kb_main_page_title = $orig_config['kb_main_pages'][$active_kb_main_page_id];
			unset( $orig_config['kb_main_pages'][$active_kb_main_page_id] );
			$orig_config['kb_main_pages'] = array( $active_kb_main_page_id => $active_kb_main_page_title ) + $orig_config['kb_main_pages'];
		}

		// ensure the common path is always set
		$articles_common_path = empty( $new_config['kb_articles_common_path'] ) ? EPKB_KB_Handler::get_default_slug( $orig_config['id'] ) : $new_config['kb_articles_common_path'];

		// sanitize article path 
		$pieces = explode( '/', $articles_common_path );
        $articles_common_path_out = '';
        $first_piece = true;
        foreach( $pieces as $piece ) {
            $articles_common_path_out .= ( $first_piece ? '' : '/' ) . urldecode( sanitize_title_with_dashes( $piece, '', 'save' ) );
            $first_piece = false;
        }
		$new_config['kb_articles_common_path'] = $articles_common_path_out;
		$new_common_path = $new_config['kb_articles_common_path'] != $orig_config['kb_articles_common_path'] || $new_config['categories_in_url_enabled'] != $orig_config['categories_in_url_enabled'];

		// update KB and add-ons configuration
		$orig_config['kb_articles_common_path'] = $new_config['kb_articles_common_path']; // this is needed for prepare_update_to_kb_configuration() to work properly
		$update_kb_msg = EPKB_Core_Utilities::prepare_update_to_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty( $update_kb_msg ) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}

		// in case user changed article common path, flush the rules
		if ( $new_common_path ) {
			EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

			// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
			flush_rewrite_rules( false );
			update_option( 'epkb_flush_rewrite_rules', true );

			EPKB_Admin_Notices::remove_ongoing_notice( 'epkb_changed_slug' );
		}

		wp_die( wp_json_encode( array(
			'message' => esc_html__( 'Configuration Saved', 'echo-knowledge-base' ),
			'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Apply ORDERING Wizard changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 */
	private function apply_ordering_wizard_changes( $orig_config, $new_config ) {
		global $eckb_kb_id;

		$eckb_kb_id = $new_config['id'];
		
		// update KB and add-ons configuration
		$update_kb_msg = EPKB_Core_Utilities::prepare_update_to_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty( $update_kb_msg ) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}
		
		// update sequence of articles and categories
		$sync_sequence = new EPKB_KB_Config_Sequence();
		
		$sync_sequence->update_articles_sequence( $orig_config['id'], $new_config );
		$sync_sequence->update_categories_sequence( $orig_config['id'], $new_config );

		wp_die( wp_json_encode( array(
			'message' => esc_html__( 'Configuration Saved', 'echo-knowledge-base' ),
			'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Based on user selection of article/category ordering in the first step, set up the second step of KB Main Page preview
	 */
	public function wizard_update_order_view() {
		global $eckb_is_kb_main_page;

		EPKB_Utilities::ajax_verify_nonce_and_capability_or_error_die();

		$sequence_settings = EPKB_Utilities::post( 'sequence_settings', [] );
		$kb_id = EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( empty( $sequence_settings ) || empty( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Invalid parameters. Please refresh your page.', 'echo-knowledge-base' ) . ' (174)' );
		}

		// allows to show articles without links and without show more feature
		$_GET['ordering-wizard-on'] = true;
		
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		$new_kb_config = array_merge( $orig_config, $sequence_settings );
		
		$articles_sequence_new_value = $new_kb_config['articles_display_sequence'];
		$categories_sequence_new_value = $new_kb_config['categories_display_sequence'];
		
		$articles_order_method = $articles_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $articles_sequence_new_value;
		
		$articles_admin = new EPKB_Articles_Admin();
		$article_seq = $articles_admin->get_articles_sequence_non_custom( $kb_id, $articles_order_method );
		if ( $article_seq === false ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 175 ) );
		}

		// ARTICLES: change to custom sequence if necessary
		if ( $articles_sequence_new_value == 'user-sequenced' ) {
			$article_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
			if ( ! empty($article_seq_data) ) {
				$article_seq = $article_seq_data;
			}
		}

		// get non-custom ordering regardless (default to by title if this IS custom order)
		$categories_order_method = $categories_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $categories_sequence_new_value;
		$cat_admin = new EPKB_Categories_Admin();
		$category_seq = $cat_admin->get_categories_sequence_non_custom( $kb_id, $categories_order_method );
		if ( $category_seq === false ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 176 ) );
		}

		// CATEGORIES: change to custom sequence if necessary
		if ( $categories_sequence_new_value == 'user-sequenced' ) {
			$custom_categories_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
			if ( ! empty($custom_categories_data) ) {
				$category_seq = $custom_categories_data;
			}
		}

		if ( ! is_array( $article_seq ) || ! is_array( $category_seq ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 177 ) );
		}

		// ensure user can order articles and categories easily
		$new_kb_config['nof_articles_displayed'] = '200';
		$new_kb_config['sidebar_top_categories_collapsed'] = 'off';
		$new_kb_config['article_toc_title'] = '';

		$new_kb_config['kb_main_page_layout'] = EPKB_Layout::BASIC_LAYOUT;
		$new_kb_config['expand_articles_icon'] = "ep_font_icon_arrow_carrot_right";

		$new_kb_config['search_layout'] = 'epkb-search-form-0';

		$new_kb_config['modular_main_page_toggle'] = 'off';

		// plain Colors
		$new_kb_config['section_head_category_icon_color'] = '#000000';
		$new_kb_config['section_head_font_color'] = '#000000';
		$new_kb_config['article_font_color'] = '#000000';
		$new_kb_config['article_icon_color'] = '#459fed';
		$new_kb_config['section_category_font_color'] = '#000000';
		$new_kb_config['section_category_icon_color'] = '#000000';
		$new_kb_config['section_body_background_color'] = '#f5f5f5';
		$new_kb_config['section_head_background_color'] = '#f5f5f5';
		$new_kb_config['background_color'] = '';

		$eckb_is_kb_main_page = true;   // pretend this is Main Page
		$main_page_output = EPKB_Layouts_Setup::output_main_page( $new_kb_config, true, $article_seq, $category_seq );
		
		wp_die( wp_json_encode( array( 'message' => '', 'html' => $main_page_output ) ) );
	}

	/**
	 * Apply SETUP WIZARD CHANGES
	 */
	public function apply_setup_wizard_changes() {

		$is_setup_run_first_time = EPKB_Core_Utilities::run_setup_wizard_first_time() || EPKB_Utilities::post( 'emkb_admin_notice' ) == 'kb_add_success';

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		// get current KB ID
		$kb_id = EPKB_Utilities::post( 'epkb_wizard_kb_id' );
		if ( empty( $kb_id ) || ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 159, '', false ) );
		}

		// get Layout Name
		$layout_name = EPKB_Utilities::post( 'layout_name' );

		// create demo KB only for the first time and save it; ignore errors
		if ( $is_setup_run_first_time ) {
			EPKB_KB_Handler::add_new_knowledge_base( EPKB_KB_Config_DB::DEFAULT_KB_ID, '', '', $layout_name );
			EPKB_Core_Utilities::remove_kb_flag( 'epkb_run_setup' );
		}

		// get current KB configuration (or new one if first time setup)
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config, false ) );
		}

		// get current Add-ons configuration
		$orig_config = apply_filters( 'epkb_all_wizards_get_current_config', $orig_config, $kb_id );
		if ( empty( $orig_config ) || ! is_array( $orig_config ) || count( $orig_config ) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 500, EPKB_Utilities::get_variable_string( $orig_config ), false ) );
		}

		$new_config = $orig_config;

		// initially if WPML is active or enabled then enable it by default
		if ( $is_setup_run_first_time && ( EPKB_Utilities::is_wpml_plugin_active() || EPKB_Utilities::is_wpml_enabled( $new_config ) ) ) {
			$new_config['wpml_is_enabled'] = 'on';
		}

		// apply Categories & Articles module theme preset; set to 'current' if user did not select a new theme i.e. keep current settings
		$is_theme_selected = false;
		$categories_articles_preset_name = EPKB_Utilities::post( 'categories_articles_preset_name' );
		if ( $categories_articles_preset_name != 'current' ) {
			$is_theme_selected = true;
			$new_config = EPKB_KB_Wizard_Themes::get_theme( $categories_articles_preset_name, $orig_config );
		}

		// apply Layout Name
		$new_config['kb_main_page_layout'] = empty( $layout_name ) ? $new_config['kb_main_page_layout'] : $layout_name;

		// apply selected Modules
		$row_1_module = EPKB_Utilities::post( 'row_1_module' );
		$new_config['ml_row_1_module'] = empty( $row_1_module ) ? $new_config['ml_row_1_module'] : $row_1_module;
		$row_2_module = EPKB_Utilities::post( 'row_2_module' );
		$new_config['ml_row_2_module'] = empty( $row_2_module ) ? $new_config['ml_row_2_module'] : $row_2_module;
		$row_3_module = EPKB_Utilities::post( 'row_3_module' );
		$new_config['ml_row_3_module'] = empty( $row_3_module ) ? $new_config['ml_row_3_module'] : $row_3_module;
		$row_4_module = EPKB_Utilities::post( 'row_4_module' );
		$new_config['ml_row_4_module'] = empty( $row_4_module ) ? $new_config['ml_row_4_module'] : $row_4_module;
		$row_5_module = EPKB_Utilities::post( 'row_5_module' );
		$new_config['ml_row_5_module'] = empty( $row_5_module ) ? $new_config['ml_row_5_module'] : $row_5_module;

		// apply Modular Sidebar location for Categories & Articles module
		$categories_articles_sidebar_location = EPKB_Utilities::post( 'categories_articles_sidebar_location' );
		$new_config['ml_categories_articles_sidebar_toggle'] = empty( $categories_articles_sidebar_location )
			? $new_config['ml_categories_articles_sidebar_toggle']
			: ( $categories_articles_sidebar_location == 'none' ? 'off' : 'on' );
		$new_config['ml_categories_articles_sidebar_location'] = empty( $categories_articles_sidebar_location ) || $categories_articles_sidebar_location == 'none'
			? $new_config['ml_categories_articles_sidebar_location']
			: $categories_articles_sidebar_location;

		// set better Modular Sidebar width when user switched it 'on' (KB Main Page)
		if ( $new_config['ml_categories_articles_sidebar_toggle'] == 'on' && $orig_config['ml_categories_articles_sidebar_toggle'] == 'off' && EPKB_Core_Utilities::is_module_present( $new_config, 'categories_articles' ) ) {
			$new_config['ml_categories_articles_sidebar_desktop_width'] = 28;
		}

		// always enable Sidebar Article Active Bold
		$new_config['sidebar_article_active_bold'] = 'on';

		// add menu link
		$this->add_kb_link_to_top_menu( $new_config['kb_main_pages'] );

		// get and sanitize KB Nickname
		$kb_nickname = EPKB_Utilities::post( 'kb_name', '', 'text', 50 );
		if ( empty( $kb_nickname ) ) {
			$kb_nickname = esc_html__( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : ' ' . $kb_id );
		}
		$new_config['kb_name'] = $kb_nickname;

		$this->create_main_page_if_missing( $new_config );

		$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $new_config );

		// allow change slug only for users with admin capability
		$kb_slug_changed = $is_setup_run_first_time;
		if ( EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {

			// allow change slug if Setup Wizard is running for the first time or no KB Main Pages detected
			if ( $is_setup_run_first_time || EPKB_Utilities::get_wp_option( 'epkb_not_completed_setup_wizard_' . $kb_id, false ) || empty( EPKB_KB_Handler::get_kb_main_pages( $orig_config ) ) ) {
				$kb_slug = EPKB_Utilities::post( 'kb_slug', '', 'text', 100 );
				$kb_slug = empty( $kb_slug ) ? EPKB_KB_Handler::get_default_slug( $kb_id ) : sanitize_title_with_dashes( $kb_slug );
				wp_update_post( array( 'ID' => $main_page_id, 'post_name' => $kb_slug ) );
				$kb_slug_changed = true;
			}

			// ensure that KB URL and article common path are the same; if not make them so
			$main_page_slug = EPKB_Core_Utilities::get_main_page_slug( $main_page_id );
			if ( empty( $new_config['kb_articles_common_path'] ) || $new_config['kb_articles_common_path'] != $main_page_slug ) {
				$new_config['kb_articles_common_path'] = $main_page_slug;
				$kb_slug_changed = true;
			}
		}

		// update article sidebar based on user selection of predefined sidebar variations
		$sidebar_settings_id = (int)EPKB_Utilities::post( 'sidebar_selection', 0 );
		if ( $sidebar_settings_id ) {

			foreach ( EPKB_KB_Wizard_Themes::$sidebar_themes as $setting_name => $values ) {
				// something went wrong with the settings
				if ( ! isset( $values[ $sidebar_settings_id ] ) ) {
					continue;
				}

				if ( isset( $new_config[ $setting_name ] ) ) {
					if ( $new_config[ $setting_name ] != $values[ $sidebar_settings_id ] ) {
						$new_config[ $setting_name ] = $values[ $sidebar_settings_id ];
					}
					continue;
				}

				if ( $new_config['article_sidebar_component_priority'][ $setting_name ] != $values[ $sidebar_settings_id ] ) {
					$new_config['article_sidebar_component_priority'][ $setting_name ] = $values[ $sidebar_settings_id ];
				}
			}

			// if user has KB Sidebar on, then preserve it
			if ( $new_config['article_sidebar_component_priority']['kb_sidebar_left'] != '0' ) {
				$new_config['article-left-sidebar-toggle'] = 'on';
				$new_config['article_sidebar_component_priority']['kb_sidebar_left'] = '2';
			}

			if ( $new_config['article_sidebar_component_priority']['kb_sidebar_right'] != '0' ) {
				$new_config['article-right-sidebar-toggle'] = 'on';
				$new_config['article_sidebar_component_priority']['kb_sidebar_right'] = '2';
			}
		}

		// Don't change sidebar intro text on the second setup
		if ( ! $is_setup_run_first_time && isset( $new_config['sidebar_main_page_intro_text'] ) && isset( $orig_config['sidebar_main_page_intro_text'] ) ) {
			$new_config['sidebar_main_page_intro_text'] = $orig_config['sidebar_main_page_intro_text'];
		}

		EPKB_Core_Utilities::start_update_kb_configuration( $kb_id, $new_config, $is_theme_selected );

		// update icons if user chose another theme design
		if ( $is_theme_selected ) {
			// if user selects Image theme then change font icons to image icons
			EPKB_Core_Utilities::get_or_update_new_category_icons( $new_config, $categories_articles_preset_name, true );
		}

		if ( $kb_slug_changed && EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {

			// in case user changed article common path, flush the rules
			EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

			// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
			flush_rewrite_rules( false );
			update_option( 'epkb_flush_rewrite_rules', true );

			EPKB_Admin_Notices::remove_ongoing_notice( 'epkb_changed_slug' );
		}

		// setup wizard was completed at least once for the current KB - does not matter admin or editor user
		delete_option( 'epkb_not_completed_setup_wizard_' . $kb_id );

		// update KB ids list option that indicates for which KBs the Setup Wizard is completed at least once
		EPKB_Core_Utilities::add_kb_flag( 'completed_setup_wizard_' . $new_config['id'] );

		EPKB_Core_Utilities::remove_kb_flag( 'epkb_run_setup' );

		wp_die( wp_json_encode( array(
			'message' => 'success',
			'redirect_to_url' => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $new_config['id'] ) . '&page=epkb-kb-need-help&epkb_after_kb_setup' ) ) ) );
	}

	/**
	 * if no KB Main Page found, e.g. user deleted it after running Setup Wizard the first time, then try to create a new one
	 *
	 * @param $new_config
	 */
	private function create_main_page_if_missing( &$new_config ) {

		$kb_id = $new_config['id'];
		$kb_nickname = $new_config['kb_name'];

		$kb_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $new_config );
		if ( ! empty( $kb_page_id ) ) {
			return;
		}

		// get and sanitize KB slug
		$kb_slug = EPKB_Utilities::post( 'kb_slug', '', 'text', 100 );
		$kb_slug = empty( $kb_slug ) ? EPKB_KB_Handler::get_default_slug( $kb_id ) : sanitize_title_with_dashes( $kb_slug );

		// create new KB Main Page using blocks if user selected blocks
		$use_kb_blocks = EPKB_Utilities::post( 'kb_main_page_type' ) === 'kb-blocks';
		$kb_blocks = array();
		if ( $use_kb_blocks ) {
			for ( $i = 1; $i <= 5; $i++ ) {
				// TODO: define blocks config depending on selected modules
			}
		}

		$new_kb_main_page = EPKB_KB_Handler::create_kb_main_page( $kb_id, $kb_nickname, $kb_slug, $use_kb_blocks, $kb_blocks );
		if ( is_wp_error( $new_kb_main_page ) ) {
			EPKB_Logging::add_log( 'Could not create KB main page', $kb_id, $new_kb_main_page );
		} else {
			$new_config['kb_articles_common_path'] = urldecode( sanitize_title_with_dashes( $new_kb_main_page->post_name, '', 'save' ) );
			$kb_main_pages[ $new_kb_main_page->ID ] = $new_kb_main_page->post_title;
			$new_config['kb_main_pages'] = $kb_main_pages;
		}
	}

	/**
	 * Add KB link to top menu
	 *
	 * @param $kb_main_pages
	 */
	private function add_kb_link_to_top_menu( $kb_main_pages ) {

		// add items to menus if needed
		$menu_ids = EPKB_Utilities::post( 'menu_ids', [] );
		if ( $menu_ids && ! empty( $kb_main_pages ) ) {
			foreach ( $menu_ids as $id ) {
				$itemData =  array(
					'menu-item-object-id'   => key($kb_main_pages),
					'menu-item-parent-id'   => 0,
					'menu-item-position'    => 99,
					'menu-item-object'      => 'page',
					'menu-item-type'        => 'post_type',
					'menu-item-status'      => 'publish'
				);

				wp_update_nav_menu_item( $id, 0, $itemData );
			}
		}
	}
}
