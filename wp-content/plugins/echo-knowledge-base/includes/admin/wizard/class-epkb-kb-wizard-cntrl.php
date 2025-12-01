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

		add_action( 'wp_ajax_epkb_get_wizard_preset_preview',  array( $this, 'get_wizard_preset_preview' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_wizard_preset_preview', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
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
		
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $wizard_kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
		}

		// get current Add-ons configuration
		$orig_config = EPKB_Core_Utilities::get_add_ons_config( $wizard_kb_id, $orig_config );
		if ( $orig_config === false ) {
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

		EPKB_KB_Handler::reset_kb_main_pages();

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
			if ( ! empty( $article_seq_data ) ) {
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

		$new_kb_config['ml_row_1_module'] = 'categories_articles';
		$new_kb_config['ml_row_2_module'] = 'none';
		$new_kb_config['ml_row_3_module'] = 'none';
		$new_kb_config['ml_row_4_module'] = 'none';
		$new_kb_config['ml_row_5_module'] = 'none';

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

		// use KB blocks or shortcode for KB Main Page
		$use_kb_blocks = EPKB_Utilities::post( 'kb_main_page_type' ) === 'kb-blocks';

		// create shortcode KB Main Page: create demo KB only for the first time and save it; ignore errors
		if ( $is_setup_run_first_time && ! $use_kb_blocks ) {
			EPKB_KB_Handler::add_new_knowledge_base( EPKB_KB_Config_DB::DEFAULT_KB_ID, '', '', $layout_name );
			EPKB_Core_Utilities::remove_kb_flag( 'epkb_run_setup' );
		}

		// for new KB the Wizard is running first time:
		//		- KB block Main Page - retrieve default configuration for origin configuration (populate the new config with Wizard data before create the new KB, because KB blocks store data via attributes and require actual values on creation)
		//		- KB shortcode Main Page - retrieve existing origin configuration (KB is already created at this point, because shortcode Main Page is using stored KB configuration and can be created before applying Wizard data)
		$orig_config = $is_setup_run_first_time && $use_kb_blocks
			? epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID )
			: epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );

		// error can be only on existing configuration retrieval
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config, false ) );
		}

		// get current Add-ons configuration
		$orig_config = EPKB_Core_Utilities::get_add_ons_config( $kb_id, $orig_config );
		if ( $orig_config === false ) {
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
		if ( ! empty( $categories_articles_preset_name ) && $categories_articles_preset_name != 'current' ) {
			$is_theme_selected = true;
			$new_config = EPKB_KB_Wizard_Themes::get_theme( $categories_articles_preset_name, $orig_config );
		}

		// apply Layout Name
		$new_config['kb_main_page_layout'] = empty( $layout_name ) ? $orig_config['kb_main_page_layout'] : $layout_name;

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

		// apply Featured Articles Sidebar location for Categories & Articles module
		$categories_articles_sidebar_location = EPKB_Utilities::post( 'categories_articles_sidebar_location' );
		$new_config['ml_categories_articles_sidebar_toggle'] = empty( $categories_articles_sidebar_location )
			? $new_config['ml_categories_articles_sidebar_toggle']
			: ( $categories_articles_sidebar_location == 'none' ? 'off' : 'on' );
		$new_config['ml_categories_articles_sidebar_location'] = empty( $categories_articles_sidebar_location ) || $categories_articles_sidebar_location == 'none'
			? $new_config['ml_categories_articles_sidebar_location']
			: $categories_articles_sidebar_location;

		// set better Featured Articles Sidebar width when user switched it 'on' (KB Main Page)
		if ( $new_config['ml_categories_articles_sidebar_toggle'] == 'on' && $orig_config['ml_categories_articles_sidebar_toggle'] == 'off' && EPKB_Core_Utilities::is_module_present( $new_config, 'categories_articles' ) ) {
			$new_config['ml_categories_articles_sidebar_desktop_width'] = 28;
		}

		// always enable Sidebar Article Active Bold
		$new_config['sidebar_article_active_bold'] = 'on';

		// create KB blocks Main Page: create demo KB only for the first time and save it; ignore errors
		if ( $is_setup_run_first_time && $use_kb_blocks ) {
			EPKB_KB_Handler::add_new_knowledge_base( EPKB_KB_Config_DB::DEFAULT_KB_ID, '', '', $layout_name, true, $new_config );
			EPKB_Core_Utilities::remove_kb_flag( 'epkb_run_setup' );

			// get updated configuration after the new KB was added
			$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
			if ( is_wp_error( $orig_config ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 9, $orig_config, false ) );
			}

			// apply changes in the updated origin configuration into the new configuration (after the new KB was added)
			$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
			$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];
		}

		// add menu link
		$this->add_kb_link_to_top_menu( $new_config['kb_main_pages'] );

		// get and sanitize KB Nickname
		$kb_nickname = EPKB_Utilities::post( 'kb_name', '', 'text', 50 );
		if ( empty( $kb_nickname ) ) {
			$kb_nickname = esc_html__( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : ' ' . $kb_id );
		}
		$new_config['kb_name'] = $kb_nickname;

		$this->create_main_page_if_missing( $new_config, $use_kb_blocks );

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
			'redirect_to_url' => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $new_config['id'] ) . '&page=epkb-dashboard&epkb_after_kb_setup' ) ) ) );
	}

	/**
	 * if no KB Main Page found, e.g. user deleted it after running Setup Wizard the first time, then try to create a new one
	 *
	 * @param $new_config
	 * @param $use_kb_blocks
	 */
	private function create_main_page_if_missing( &$new_config, $use_kb_blocks ) {

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
		$new_kb_main_page = EPKB_KB_Handler::create_kb_main_page( $kb_id, $kb_nickname, $kb_slug, $new_config, $use_kb_blocks );
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

	/**
	 * Get live preview of a preset for the setup wizard
	 * This implementation follows the exact pattern from EPKB_Frontend_Editor::update_preview_and_settings()
	 */
	public function get_wizard_preset_preview() {
		global $eckb_is_kb_main_page, $eckb_kb_id;

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$kb_id = EPKB_Utilities::post( 'epkb_wizard_kb_id' );
		if ( empty( $kb_id ) || ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 180 ) );
		}

		$layout_name = EPKB_Utilities::post( 'layout' );
		if ( empty( $layout_name ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Invalid layout name', 'echo-knowledge-base' ) );
		}

		$preset_name = EPKB_Utilities::post( 'preset' );
		if ( empty( $preset_name ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Invalid preset name', 'echo-knowledge-base' ) );
		}

		// set global vars that the layout classes expect (same as FE line 611)
		$eckb_is_kb_main_page = true;
		$eckb_kb_id = $kb_id;

		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 181, $orig_config ) );
		}

		// get add-ons configuration
		$orig_config = EPKB_Core_Utilities::get_add_ons_config( $kb_id, $orig_config );
		if ( $orig_config === false ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 182 ) );
		}

		// apply the preset theme to the configuration (similar to FE line 627)
		$new_config = EPKB_KB_Wizard_Themes::get_theme( $preset_name, $orig_config );

		// set the layout and enable modular page
		$new_config['kb_main_page_layout'] = $layout_name;
		$new_config['modular_main_page_toggle'] = 'on';
		
		// disable sidebar for cleaner preview
		$new_config['ml_categories_articles_sidebar_toggle'] = 'off';

		// adjust settings based on layout change - following FE lines 617-623
		$orig_config['kb_main_page_layout'] = $layout_name; // temporarily set layout to capture change
		$new_config_result = EPKB_Core_Utilities::adjust_settings_on_layout_change( $orig_config, $new_config );
		$new_config = $new_config_result['new_config'];
		$seq_meta = $new_config_result['seq_meta'];

		// define AMAG constant to bypass permission checks for demo articles
		if ( ! defined( 'AMAG_PLUGIN_NAME' ) ) {
			define( 'AMAG_PLUGIN_NAME', 'demo' );
		}

		// start output buffering (FE line 562)
		ob_start();

		// create and setup the handler (FE lines 631-632)
		$handler = new EPKB_Modular_Main_Page();

		// ALWAYS use demo data for wizard preview to show consistent previews
		$demo_seq_meta = array(
			'articles_seq_meta' => $this->get_demo_articles( $layout_name ),
			'categories_seq_meta' => $this->get_demo_categories( $layout_name ),
			'category_icons' => $this->get_demo_category_icons( $new_config, $layout_name, $preset_name )
		);
		$handler->setup_layout_data( $new_config, $demo_seq_meta );

		// render the categories and articles module
		$handler->categories_articles_module( $new_config );

		$preview_html = ob_get_clean();

		// get CSS file slug using same method as FE (line 1349)
		$css_file_slug = $this->get_current_css_slug( $new_config );
		
		// generate inline CSS using the exact same function as Frontend Editor (FE line 1261)
		$inline_styles = epkb_frontend_kb_theme_styles_now( $new_config, $css_file_slug );
		
		// Scope the CSS to this preset's container to prevent conflicts with other presets
		$inline_styles = $this->scope_preset_css( $inline_styles, $preset_name );

		// get CSS file URL - for Grid/Sidebar layouts, use Elegant Layouts plugin URL
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		if ( ( $layout_name === 'Grid' || $layout_name === 'Sidebar' ) && class_exists( 'Echo_Elegant_Layouts' ) ) {
			$css_file_url = Echo_Elegant_Layouts::$plugin_url . 'css/' . $css_file_slug . $suffix . '.css?ver=' . Echo_Elegant_Layouts::$version;
		} else {
			$css_file_url = Echo_Knowledge_Base::$plugin_url . 'css/' . $css_file_slug . $suffix . '.css?ver=' . Echo_Knowledge_Base::$version;
		}
		
		// check for RTL - use same plugin URL as main CSS
		$css_file_rtl_url = '';
		if ( is_rtl() ) {
			if ( ( $layout_name === 'Grid' || $layout_name === 'Sidebar' ) && class_exists( 'Echo_Elegant_Layouts' ) ) {
				$css_file_rtl_url = Echo_Elegant_Layouts::$plugin_url . 'css/' . $css_file_slug . '-rtl' . $suffix . '.css?ver=' . Echo_Elegant_Layouts::$version;
			} else {
				$css_file_rtl_url = Echo_Knowledge_Base::$plugin_url . 'css/' . $css_file_slug . '-rtl' . $suffix . '.css?ver=' . Echo_Knowledge_Base::$version;
			}
		}

		// return response in same format as FE with CSS file info
		wp_send_json_success( array(
			'html' => $preview_html,
			'css' => EPKB_Utilities::minify_css( $inline_styles ),
			'css_file_url' => $css_file_url,
			'css_file_rtl_url' => $css_file_rtl_url,
			'css_file_slug' => $css_file_slug
		) );
	}

	/**
	 * Scope CSS selectors to a preset-specific container to prevent conflicts
	 * Prepends .epkb-setup-wizard-module-preset--{preset_name} to all CSS selectors
	 */
	private function scope_preset_css( $css, $preset_name ) {
		$scoped_css = '';
		$preset_scope = '.epkb-setup-wizard-module-preset--' . $preset_name;
		
		// Split CSS into rules
		$rules = explode( '}', $css );
		
		foreach ( $rules as $rule ) {
			$rule = trim( $rule );
			if ( empty( $rule ) ) {
				continue;
			}
			
			// Split into selector and properties
			$parts = explode( '{', $rule, 2 );
			if ( count( $parts ) < 2 ) {
				continue;
			}
			
			$selectors_string = trim( $parts[0] );
			$properties = trim( $parts[1] );
			
			// Split multiple selectors (comma-separated)
			$selectors = array_map( 'trim', explode( ',', $selectors_string ) );
			$scoped_selectors = array();
			
			foreach ( $selectors as $selector ) {
				if ( empty( $selector ) ) {
					continue;
				}
				// Prepend the preset scope to make this CSS specific to this preset only
				$scoped_selectors[] = $preset_scope . ' ' . $selector;
			}
			
			// Rebuild the rule
			if ( ! empty( $scoped_selectors ) ) {
				$scoped_css .= implode( ', ', $scoped_selectors ) . " {\n" . $properties . "\n}\n";
			}
		}
		
		return $scoped_css;
	}

	/**
	 * Get current CSS slug based on layout - same logic as Frontend Editor
	 * @param $kb_config
	 * @return string
	 */
	private function get_current_css_slug( $kb_config ) {
		switch ( $kb_config['kb_main_page_layout'] ) {
			case 'Tabs': return 'mp-frontend-modular-tab-layout';
			case 'Categories': return 'mp-frontend-modular-category-layout';
			case 'Grid': return EPKB_Utilities::is_elegant_layouts_enabled() ? 'mp-frontend-modular-grid-layout' : 'mp-frontend-modular-basic-layout';
			case 'Sidebar': return EPKB_Utilities::is_elegant_layouts_enabled() ? 'mp-frontend-modular-sidebar-layout' : 'mp-frontend-modular-basic-layout';
			case 'Classic': return 'mp-frontend-modular-classic-layout';
			case 'Drill-Down': return 'mp-frontend-modular-drill-down-layout';
			case 'Basic':
			default: return 'mp-frontend-modular-basic-layout';
		}
	}

	/**
	 * Get demo category icons for preview based on the chosen preset theme.
	 * Icons vary by preset:
	 * - Font icon presets (standard, basic, etc.): Use font icons (epkbfa-*)
	 * - Image icon presets (modern, office, organized, teal, sharp): Use local plugin images
	 * - Photo icon presets (image, image_tabs): Use external photo URLs from echoknowledgebase.com
	 *
	 * @param $kb_config - KB configuration with theme applied
	 * @param string $layout_name
	 * @param string $preset_name - Preset name as fallback for theme_name
	 * @return array - Category icons array with category_id as key
	 */
	private function get_demo_category_icons( $kb_config, $layout_name = '', $preset_name = '' ) {

		// use preset name as fallback if theme_name not set in config
		$theme_name = empty( $kb_config['theme_name'] ) ? $preset_name : $kb_config['theme_name'];
		$theme_name = empty( $theme_name ) ? 'default' : $theme_name;

		// ensure theme_name is set in config for icon type check
		if ( empty( $kb_config['theme_name'] ) ) {
			$kb_config['theme_name'] = $theme_name;
		}

		// determine icon type based on theme - only certain themes use image icons
		$new_icon_type = EPKB_Icons::is_theme_with_image_icons( $kb_config ) ? 'image' : 'font';

		$default_font_icons = array(
			'epkbfa-user',
			'epkbfa-pencil',
			'epkbfa-sitemap',
			'epkbfa-area-chart',
			'epkbfa-table',
			'epkbfa-cubes'
		);

		// map theme names that don't have explicit entries to their base themes
		$theme_name_for_icons = $theme_name;
		if ( in_array( $theme_name, array( 'office', 'modern', 'office_tabs', 'modern_tabs' ) ) ) {
			$theme_name_for_icons = 'default';
		}

		$default_theme_image_icons = EPKB_Icons::get_theme_image_icons( $theme_name_for_icons );
		$is_photo_icons_preset = EPKB_Icons::is_theme_with_photo_icons( $theme_name );

		// For Tabs layout, generate icons for both top categories (tabs) and sub-categories (boxes) - match frontend structure
		if ( $layout_name === 'Tabs' ) {
			$category_ids = array_merge( range( 2, 4 ), range( 10, 15 ) );  // 3 tabs + 6 subcategories
		} else {
			// For other layouts, generate icons for top categories (2-7)
			$category_ids = range( 2, 7 );
		}

		// Icon mapping for demo categories to match frontend demo data
		// For non-tabs layouts: Sales and Marketing, Operations and Logistics, Human Resources, Finance and Expenses, IT Support, Professional Development
		// Icon theme mapping: 1=Finance, 2=HR, 3=IT, 4=Operations, 5=ProfDev, 6=Sales
		$icon_mapping = array(
			2 => 6,  // Sales and Marketing => employee-onboarding
			3 => 4,  // Operations and Logistics => feedback-form
			4 => 2,  // Human Resources => task-assignment
			5 => 1,  // Finance and Expenses => budget
			6 => 3,  // IT Support => api-integration
			7 => 5,  // Professional Development => performance-metrics
			// For Tabs layout subcategories under Department Resources
			10 => 6, // Sales and Marketing
			11 => 4, // Operations and Logistics
			12 => 2, // Human Resources => task-assignment
			13 => 1, // Finance and Expenses => budget
			14 => 3, // IT Support => api-integration
			15 => 5, // Professional Development => performance-metrics
		);

		$category_icons = array();
		foreach ( $category_ids as $index => $category_id ) {
			// Use the mapped icon index for specific category IDs, otherwise cycle through 1-6
			if ( isset( $icon_mapping[$category_id] ) ) {
				$icon_index = $icon_mapping[$category_id];
			} else {
				$icon_index = ( $index % 6 ) + 1;
			}

			if ( $new_icon_type == 'font' ) {
				$icon_name = $default_font_icons[ $icon_index - 1 ];
				$icon_url = '';
			} else {
				$icon_name = EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME;
				if ( $is_photo_icons_preset ) {
					$icon_url = $default_theme_image_icons['image_' . $icon_index];
				} else {
					$icon_url = Echo_Knowledge_Base::$plugin_url . ( empty( $default_theme_image_icons['image_' . $icon_index] ) ? EPKB_Icons::DEFAULT_IMAGE_SLUG : $default_theme_image_icons['image_' . $icon_index] );
				}
			}

			$category_icons[$category_id] = array(
				'type' => $new_icon_type,
				'name' => $icon_name,
				'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
				'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
				'image_thumbnail_url' => $icon_url,
				'image_alt' => '',
				'color' => '#000000'
			);
		}

		return $category_icons;
	}

	/**
	 * Get demo categories for preview when KB has no content
	 * Categories sequence is a nested array: category_id => array of sub-category IDs
	 * For Tabs layout: top-level categories have sub-categories
	 * @param string $layout_name
	 * @return array
	 */
	private function get_demo_categories( $layout_name = '' ) {

		// For Tabs layout, top categories become tabs and have sub-categories displayed as boxes
		// Match frontend structure: 3 tabs, with 6 subcategories under the first tab only
		if ( $layout_name === 'Tabs' ) {
			return array(
				2 => array( 10 => array(), 11 => array(), 12 => array(), 13 => array(), 14 => array(), 15 => array() ),  // Department Resources (tab) -> 6 subcategories
				3 => array(),  // Employee Handbook (tab) -> empty
				4 => array(),  // How-To Center (tab) -> empty
			);
		}

		// For other layouts: flat structure with no sub-categories
		return array(
			2 => array(),  // Getting Started
			3 => array(),  // Account & Billing
			4 => array(),  // Technical Support
			5 => array(),  // Advanced Features
			6 => array(),  // Documentation
			7 => array(),  // Community
		);
	}

	/**
	 * Get demo articles for preview when KB has no content
	 * Structure: category_id => array( 0 => 'Category Name', 1 => 'Category Description', article_id => 'Article Title', ... )
	 * For Tabs layout: articles are in sub-categories (10-21), top categories (2-7) only have name
	 * @param string $layout_name
	 * @return array
	 */
	private function get_demo_articles( $layout_name = '' ) {

		// For Tabs layout: top categories are tabs, articles are in sub-categories - use same names/descriptions as frontend demo data
		if ( $layout_name === 'Tabs' ) {
			return array(
				// Top-level categories (tabs) - match frontend demo data
				2 => array( 0 => esc_html__( 'Department Resources', 'echo-knowledge-base' ), 1 => esc_html__( 'Resources and tools for each department to enhance productivity and efficiency.', 'echo-knowledge-base' ) ),
				3 => array( 0 => esc_html__( 'Employee Handbook', 'echo-knowledge-base' ), 1 => esc_html__( 'Guidelines, policies, and procedures to ensure a safe and productive work environment.', 'echo-knowledge-base' ) ),
				4 => array( 0 => esc_html__( 'How-To Center', 'echo-knowledge-base' ), 1 => esc_html__( 'Step-by-step guides and tutorials to help you navigate the company\'s tools and resources.', 'echo-knowledge-base' ) ),
				// Sub-categories with articles under Department Resources - match frontend demo data structure with 3 articles each
				10 => array( 0 => esc_html__( 'Sales and Marketing', 'echo-knowledge-base' ), 1 => esc_html__( 'Innovative strategies for promoting products and effectively reaching new customers.', 'echo-knowledge-base' ), 101 => esc_html__( 'Introduction to Our Sales Process', 'echo-knowledge-base' ), 102 => esc_html__( 'Creating Effective Marketing Campaigns', 'echo-knowledge-base' ), 103 => esc_html__( 'Using the CRM Software', 'echo-knowledge-base' ) ),
				11 => array( 0 => esc_html__( 'Operations and Logistics', 'echo-knowledge-base' ), 1 => esc_html__( 'Streamline processes for efficient, agile, and scalable business operations.', 'echo-knowledge-base' ), 104 => esc_html__( 'Managing Inventory', 'echo-knowledge-base' ), 105 => esc_html__( 'Shipping and Fulfillment', 'echo-knowledge-base' ), 106 => esc_html__( 'Supply Chain Management', 'echo-knowledge-base' ) ),
				12 => array( 0 => esc_html__( 'Human Resources', 'echo-knowledge-base' ), 1 => esc_html__( 'Policies, procedures, and support for effective workforce management.', 'echo-knowledge-base' ), 107 => esc_html__( 'Employee Onboarding', 'echo-knowledge-base' ), 108 => esc_html__( 'Benefits and Compensation', 'echo-knowledge-base' ), 109 => esc_html__( 'Performance Reviews', 'echo-knowledge-base' ) ),
				13 => array( 0 => esc_html__( 'Finance and Expenses', 'echo-knowledge-base' ), 1 => esc_html__( 'Efficiently manage finances, track expenditure accurately, and optimize budgets.', 'echo-knowledge-base' ), 110 => esc_html__( 'Expense Reporting', 'echo-knowledge-base' ), 111 => esc_html__( 'Budget Planning', 'echo-knowledge-base' ), 112 => esc_html__( 'Financial Policies', 'echo-knowledge-base' ) ),
				14 => array( 0 => esc_html__( 'IT Support', 'echo-knowledge-base' ), 1 => esc_html__( 'Comprehensive technical assistance and forward‑thinking solutions for resilient digital infrastructure.', 'echo-knowledge-base' ), 113 => esc_html__( 'Technical Troubleshooting', 'echo-knowledge-base' ), 114 => esc_html__( 'Software Installation', 'echo-knowledge-base' ), 115 => esc_html__( 'Security Best Practices', 'echo-knowledge-base' ) ),
				15 => array( 0 => esc_html__( 'Professional Development', 'echo-knowledge-base' ), 1 => esc_html__( 'Enhance skills, explore career growth opportunities, and foster professional development.', 'echo-knowledge-base' ), 116 => esc_html__( 'Training Programs', 'echo-knowledge-base' ), 117 => esc_html__( 'Career Advancement', 'echo-knowledge-base' ), 118 => esc_html__( 'Learning Resources', 'echo-knowledge-base' ) ),
			);
		}

		// For other layouts: articles directly in top categories - use same names/descriptions as frontend demo data
		return array(
			2 => array(
				0 => esc_html__( 'Sales and Marketing', 'echo-knowledge-base' ),
				1 => esc_html__( 'Innovative strategies for promoting products and effectively reaching new customers.', 'echo-knowledge-base' ),
				101 => esc_html__( 'Introduction to Our Sales Process', 'echo-knowledge-base' ),
				102 => esc_html__( 'Creating Effective Marketing Campaigns', 'echo-knowledge-base' ),
				103 => esc_html__( 'Using the CRM Software', 'echo-knowledge-base' )
			),
			3 => array(
				0 => esc_html__( 'Operations and Logistics', 'echo-knowledge-base' ),
				1 => esc_html__( 'Streamline processes for efficient, agile, and scalable business operations.', 'echo-knowledge-base' ),
				104 => esc_html__( 'Managing Inventory', 'echo-knowledge-base' ),
				105 => esc_html__( 'Shipping and Fulfillment', 'echo-knowledge-base' ),
				106 => esc_html__( 'Supply Chain Management', 'echo-knowledge-base' )
			),
			4 => array(
				0 => esc_html__( 'Human Resources', 'echo-knowledge-base' ),
				1 => esc_html__( 'Policies, procedures, and support for effective workforce management.', 'echo-knowledge-base' ),
				107 => esc_html__( 'Employee Onboarding', 'echo-knowledge-base' ),
				108 => esc_html__( 'Benefits and Compensation', 'echo-knowledge-base' ),
				109 => esc_html__( 'Performance Reviews', 'echo-knowledge-base' )
			),
			5 => array(
				0 => esc_html__( 'Finance and Expenses', 'echo-knowledge-base' ),
				1 => esc_html__( 'Efficiently manage finances, track expenditure accurately, and optimize budgets.', 'echo-knowledge-base' ),
				110 => esc_html__( 'Expense Reporting', 'echo-knowledge-base' ),
				111 => esc_html__( 'Budget Planning', 'echo-knowledge-base' ),
				112 => esc_html__( 'Financial Policies', 'echo-knowledge-base' )
			),
			6 => array(
				0 => esc_html__( 'IT Support', 'echo-knowledge-base' ),
				1 => esc_html__( 'Comprehensive technical assistance and forward‑thinking solutions for resilient digital infrastructure.', 'echo-knowledge-base' ),
				113 => esc_html__( 'Technical Troubleshooting', 'echo-knowledge-base' ),
				114 => esc_html__( 'Software Installation', 'echo-knowledge-base' ),
				115 => esc_html__( 'Security Best Practices', 'echo-knowledge-base' )
			),
			7 => array(
				0 => esc_html__( 'Professional Development', 'echo-knowledge-base' ),
				1 => esc_html__( 'Enhance skills, explore career growth opportunities, and foster professional development.', 'echo-knowledge-base' ),
				116 => esc_html__( 'Training Programs', 'echo-knowledge-base' ),
				117 => esc_html__( 'Career Advancement', 'echo-knowledge-base' ),
				118 => esc_html__( 'Learning Resources', 'echo-knowledge-base' )
			)
		);
	}
}
