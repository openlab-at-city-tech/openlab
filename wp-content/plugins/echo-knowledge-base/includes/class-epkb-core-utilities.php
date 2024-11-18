<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Various KB Core utility functions
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Core_Utilities {

	/**
	 * Retrieve a KB article with security checks
	 *
	 * @param $post_id
	 * @return null|WP_Post - return null if this is NOT KB post
	 */
	public static function get_kb_post_secure( $post_id ) {

		if ( empty($post_id) ) {
			return null;
		}

		// ensure post_id is valid
		$post_id = EPKB_Utilities::sanitize_int( $post_id );
		if ( empty( $post_id ) ) {
			return null;
		}

		// retrieve the post and ensure it is one
		$post = get_post( $post_id );
		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			return null;
		}

		// verify it is a KB article
		if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return null;
		}

		return $post;
	}

	/**
	 * Retrieve KB ID.
	 *
	 * @param WP_Post $post
	 * @return int|NULL on ERROR
	 */
	public static function get_kb_id( $post=null ) {
		global $eckb_kb_id;

		$kb_id = '';
		$post = $post === null ? get_post() : $post;
		if ( ! empty( $post ) && $post instanceof WP_Post ) {
			$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		}

		$kb_id = empty($kb_id) || is_wp_error($kb_id) ? ( empty($eckb_kb_id) ? '' : $eckb_kb_id ) : $kb_id;
		if ( empty($kb_id) ) {
			EPKB_Logging::add_log("KB ID not found", $kb_id);
			return null;
		}

		return $kb_id;
	}

	/**
	 * Verify kb id is number and is an existing KB ID
	 * @param $kb_id
	 * @return int
	 */
	public static function sanitize_kb_id( $kb_id ) {
		$kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids();
		$kb_id = EPKB_Utilities::sanitize_int( $kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );
		return in_array( $kb_id, $kb_ids ) ? $kb_id : EPKB_KB_Config_DB::DEFAULT_KB_ID;
	}

	// true if demo KB not yet created after installation
	public static function run_setup_wizard_first_time() {

		$run_setup = self::is_kb_flag_set( 'epkb_run_setup' );
		if ( ! $run_setup ) {
			return false;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// not first time if older plugin version
		if ( $kb_config['first_plugin_version'] != EPKB_Upgrades::NOT_INITIALIZED && version_compare( $kb_config['first_plugin_version'], Echo_Knowledge_Base::$version, '<' ) ) {
			EPKB_Core_Utilities::remove_kb_flag( 'epkb_run_setup' );
			return false;
		}

		return empty( $kb_config['kb_main_pages'] );
	}

	/**
	 * Merge core KB config with add-ons KB specs
	 *
	 * @param $kb_id
	 *
	 * @return array|false
	 */
	public static function retrieve_all_kb_specs( $kb_id ) {

		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );

		// get add-on configuration from user changes if applicable
		if ( has_filter( 'epkb_add_on_config_specs' ) ) {
			$add_on_specs = apply_filters( 'epkb_add_on_config_specs', array() );
			if ( !is_array( $add_on_specs ) || is_wp_error( $add_on_specs ) ) {
				return false;
			}
			$feature_specs = array_merge( $add_on_specs, $feature_specs );
		}

		return $feature_specs;
	}

	/**
	 * Get list of archived KBs
	 *
	 * @return array
	 */
	public static function get_archived_kbs() {
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		$archived_kbs = [];
		foreach ( $all_kb_configs as $one_kb_config ) {
			if ( $one_kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && self::is_kb_archived( $one_kb_config['status'] ) ) {
				$archived_kbs[] = $one_kb_config;
			}
		}
		return $archived_kbs;
	}

	/**
	 * For given Main Page, retrieve its slug by passed page ID
	 *
	 * @param $kb_main_page_id
	 *
	 * @return string
	 */
	public static function get_main_page_slug( $kb_main_page_id ) {

		$kb_page = get_post( $kb_main_page_id );
		if ( empty( $kb_page ) ) {
			return '';
		}

		$slug = urldecode( sanitize_title_with_dashes( $kb_page->post_name, '', 'save' ) );
		$ancestors = get_post_ancestors( $kb_page );
		foreach ( $ancestors as $ancestor_id ) {
			$post_ancestor = get_post( $ancestor_id );
			if ( empty( $post_ancestor ) ) {
				continue;
			}
			$slug = urldecode( sanitize_title_with_dashes( $post_ancestor->post_name, '', 'save' ) ) . '/' . $slug;
			if ( $kb_main_page_id == $ancestor_id ) {
				break;
			}
		}

		return $slug;
	}

	/**
	 * For given Main Page, retrieve its slug by passed page object
	 *
	 * @param $kb_main_page
	 * @return string
	 */
	public static function get_main_page_slug_by_obj( $kb_main_page ) {

		if ( empty( $kb_main_page ) || empty( $kb_main_page->post_name ) ) {
			return '';
		}

		$slug = urldecode( sanitize_title_with_dashes( $kb_main_page->post_name, '', 'save' ) );
		$ancestors = get_post_ancestors( $kb_main_page );
		foreach ( $ancestors as $ancestor_id ) {
			$post_ancestor = get_post( $ancestor_id );
			if ( empty( $post_ancestor ) ) {
				continue;
			}
			$slug = urldecode( sanitize_title_with_dashes( $post_ancestor->post_name, '', 'save' ) ) . '/' . $slug;
			if ( $kb_main_page->ID == $ancestor_id ) {
				break;
			}
		}

		return $slug;
	}

	/**
	 * Check if KB is ARCHIVED.
	 *
	 * @param $kb_status
	 * @return bool
	 */
	public static function is_kb_archived( $kb_status ) {
		return $kb_status === 'archived';
	}


	/**************************************************************************************************************************
	 *
	 *                     KB CONFIGURATION UPDATE
	 *
	 *************************************************************************************************************************/

	public static function start_update_kb_configuration( $kb_id, $new_config, $is_theme_selected=false ) {

		// validate TOC Hy, Hx levels: Hy cannot be less than Hx
		if ( $new_config['article_toc_hy_level'] < $new_config['article_toc_hx_level'] ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'HTML Header range is invalid', 'echo-knowledge-base' ) );
		}

		// get current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
		}

		// get current KB configuration from add-ons
		$orig_config = apply_filters( 'eckb_all_editors_get_current_config', $orig_config, $kb_id );
		if ( empty( $orig_config ) || count( $orig_config ) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 149 ) );
		}

		$new_config = self::update_article_sidebar_priority( $orig_config, $new_config );

		// save Modular Main Page custom CSS if defined
		$new_config['modular_main_page_custom_css_toggle'] = 'off';
		if ( isset( $new_config['epkb_ml_custom_css'] ) ) {
			$ml_custom_css = trim( wp_kses( $new_config['epkb_ml_custom_css'], [] ) );
			unset( $new_config['epkb_ml_custom_css'] );
			$new_config['modular_main_page_custom_css_toggle'] = empty( $ml_custom_css ) ? 'off' : 'on';
			if ( $new_config['modular_main_page_custom_css_toggle'] == 'on' ) {
				$result = EPKB_Utilities::save_kb_option( $kb_id, 'epkb_ml_custom_css', $ml_custom_css );
				if ( is_wp_error( $result ) ) {
					EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 35, $result ) );
				}
			}
		}

		if ( isset( $new_config['general_typography_font_family'] ) ) {
			$new_config['general_typography']['font-family'] = $new_config['general_typography_font_family'] == 'Inherit' ? '' : $new_config['general_typography_font_family'];
			unset( $new_config['general_typography_font_family'] );
			// $new_config = self::adjust_topography( $orig_config, $new_config );
		}

		// apply Advanced Search presets (ensure all settings of selected preset are applied, including those settings which are not listed in the Settings UI)
		if ( isset( $new_config['advanced_search_mp_presets'] ) && $new_config['advanced_search_mp_presets'] != 'current' ) {
			$asea_preset_name = $new_config['advanced_search_mp_presets'];
			$addons_data = apply_filters( 'epkb_editor_addon_data', array(), $new_config );
			if ( isset( $addons_data['asea_presets'][ $asea_preset_name ] ) ) {
				foreach ( $addons_data['asea_presets'][$asea_preset_name] as $key => $value ) {

					// apply only valid and relevant settings
					if ( ! isset( $orig_config[$key] ) || strpos( $key, 'advanced_search_mp_' ) === false ) {
						continue;
					}

					$new_config[$key] = $value;
				}
			}
		}
		if ( isset( $new_config['advanced_search_ap_presets'] ) && $new_config['advanced_search_ap_presets'] != 'current' ) {
			$asea_preset_name = $new_config['advanced_search_ap_presets'];
			$addons_data = apply_filters( 'epkb_editor_addon_data', array(), $new_config );
			if ( isset( $addons_data['asea_presets'][ $asea_preset_name ] ) ) {
				foreach ( $addons_data['asea_presets'][$asea_preset_name] as $key => $value ) {

					// apply only valid and relevant settings
					if ( ! isset( $orig_config[$key] ) || strpos( $key, 'advanced_search_ap_' ) === false ) {
						continue;
					}

					$new_config[$key] = $value;
				}
			}
		}

		// Category Archive Page design
		if ( isset( $new_config['archive_content_sub_categories_display_mode'] ) && $new_config['archive_content_sub_categories_display_mode'] != 'current' ) {
			$new_config = self::get_category_archive_page_design( $new_config );
		}

		// switch off Article Page search sync if the Main Page search is off (modular_main_page_toggle is present only in full config)
		$is_kb_main_page_search_off = $orig_config['modular_main_page_toggle'] == 'on' ? ! self::is_module_present( $new_config, 'search' ) : $orig_config['search_layout'] == 'epkb-search-form-0';
		if ( $is_kb_main_page_search_off ) {
			$new_config['article_search_sync_toggle'] = 'off';
		}

		// sync Article Search with Main Search settings - Sidebar layout does not use Article Search settings, still keep the settings synced if required
		if ( ( isset( $new_config['article_search_sync_toggle'] ) && $new_config['article_search_sync_toggle'] == 'on' ) ||
			( empty( $new_config['article_search_sync_toggle'] ) && $orig_config['article_search_sync_toggle'] == 'on' ) ) {

			foreach ( $orig_config as $setting_name => $orig_setting_value ) {

				if ( in_array( $setting_name, ['admin_eckb_access_search_analytics_read','archive_search_toggle'] ) ) {
					continue;
				}

				// ignore Article Page Search settings - can be present when turning the toggle 'on' ( page reloads with saving settings )
				if ( strpos( $setting_name, 'article_search_' ) !== false || strpos( $setting_name, 'advanced_search_ap_' ) !== false ) {
					continue;
				}

				$ap_search_setting_name = '';

				// sync Advanced Search settings
				if ( strpos( $setting_name, 'advanced_search_mp_' ) !== false ) {
					$ap_search_setting_name = str_replace( 'advanced_search_mp_', 'advanced_search_ap_', $setting_name );
				}
				// sync KB core search settings
				else if ( strpos( $setting_name, 'search_' ) !== false ) {
					$ap_search_setting_name = str_replace( 'search_', 'article_search_', $setting_name );
				}

				// sync Article Page Search setting from new config if the value is set in the new config
				if ( ! empty( $ap_search_setting_name ) && isset( $new_config[$setting_name] ) ) {
					$new_config[$ap_search_setting_name] = $new_config[$setting_name];
				// still sync Article Page Search setting from origin config if the value is not set in the new config
				} else if ( ! empty( $ap_search_setting_name ) && isset( $orig_setting_value ) ) {
					$new_config[$ap_search_setting_name] = $orig_setting_value;
				}
			}
		}

		// Legacy: save KB ID for source of Modular Main Page FAQs Module
		if ( ! empty( $new_config['ml_faqs_kb_id'] ) ) {
			$result = EPKB_Utilities::save_kb_option( $kb_id, EPKB_ML_FAQs::FAQS_KB_ID, $new_config['ml_faqs_kb_id'] );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 36, $result ) );
			}
			unset( $new_config['ml_faqs_kb_id'] );

			// save categories list for source of Modular Main Page FAQs Module
			$ml_faqs_category_ids = isset( $new_config['ml_faqs_category_ids'] ) ? $new_config['ml_faqs_category_ids'] : [];
			unset( $new_config['ml_faqs_category_ids'] );
			$result = EPKB_Utilities::save_kb_option( $kb_id, EPKB_ML_FAQs::FAQS_CATEGORY_IDS, $ml_faqs_category_ids );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 38, $result ) );
			}
		}

		// save FAQ Group IDs for source of FAQs Module when:
		// - not set (if not used in UI)
		// - int array (if selected any FAQ Group(s))
		// - '0' (if no FAQ Groups were selected)
		if ( isset( $new_config['faq_group_ids'] ) ) {
			$faq_group_ids = empty( $new_config['faq_group_ids'] ) ? [] : $new_config['faq_group_ids'];
			unset( $new_config['faq_group_ids'] );
			$result = EPKB_Utilities::save_kb_option( $kb_id, EPKB_ML_FAQs::FAQ_GROUP_IDS, $faq_group_ids );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 39, $result ) );
			}
		}

		// apply design preset for FAQs module
		if ( isset( $new_config['faq_preset_name'] ) && $new_config['faq_preset_name'] != 'current' ) {
			$preset_settings = EPKB_FAQs_Utilities::get_design_settings( $new_config['faq_preset_name'] );
			unset( $new_config['faq_preset_name'] );
			$new_config = array_merge( $new_config, $preset_settings );
		}

		// TODO remove Jan 2025
		if ( empty( $new_config['ml_faqs_title_text'] ) ) {
			$new_config['ml_faqs_title_text'] = esc_html__( 'Frequently Asked Questions', 'echo-knowledge-base' );
			$new_config['ml_faqs_title_location'] = 'none';
		}
		if ( empty( $new_config['ml_articles_list_title_text'] ) ) {
			$new_config['ml_articles_list_title_text'] = esc_html__( 'Featured Articles', 'echo-knowledge-base' );
			$new_config['ml_articles_list_title_location'] = 'none';
		}

		// do not adjust settings if user selected theme - wizard preset should handle all settings itself
		$new_config = self::adjust_settings_on_layout_change( $orig_config, $new_config, $is_theme_selected );

		// user switches from Category Archive page V2 to V3 TODO REMOVE 2025
		if ( $orig_config['archive_page_v3_toggle'] == 'off' && isset( $new_config['archive_page_v3_toggle'] ) && $new_config['archive_page_v3_toggle'] == 'on' ) {
			$new_config['archive_header_desktop_width'] = $orig_config['archive-container-width-v2'];
			$new_config['archive_header_desktop_width_units'] = $orig_config['archive-container-width-units-v2'];
			$new_config['archive_content_desktop_width'] = $orig_config['archive-container-width-v2'];
			$new_config['archive_content_desktop_width_units'] = $orig_config['archive-container-width-units-v2'];
		}

		// overwrite current KB configuration with new configuration from this editor
		$new_config = array_merge( $orig_config, $new_config );

		// recalculate width
		$new_config = self::reset_article_sidebar_widths( $new_config );

		// check article bottom meta
		if ( isset( $new_config['rating_stats_footer_toggle'] ) && isset( $orig_config['rating_stats_footer_toggle'] ) && $new_config['rating_stats_footer_toggle'] != $orig_config['rating_stats_footer_toggle'] ) {
			if ( $new_config['rating_stats_footer_toggle'] == 'on' && $new_config['meta-data-footer-toggle'] == 'off' ) {
				$new_config['meta-data-footer-toggle'] = 'on';
			}

			if ( $new_config['rating_stats_footer_toggle'] == 'off' && $new_config['meta-data-footer-toggle'] == 'on' && $new_config['last_updated_on_footer_toggle'] == 'off'
				&& $new_config['created_on_footer_toggle'] == 'off' && $new_config['author_footer_toggle'] == 'off' ) {
				$new_config['meta-data-footer-toggle'] = 'off';
			}
		}

		// update KB and add-ons configuration
		$update_kb_msg = self::prepare_update_to_kb_configuration( $kb_id, $orig_config, $new_config );
		if ( ! empty( $update_kb_msg ) ) {
			EPKB_Utilities::ajax_show_error_die( esc_html__( 'Could not save the new configuration.', 'echo-knowledge-base' ) . ' ' . $update_kb_msg . '. (32) ' . EPKB_Utilities::contact_us_for_support() );
		}

		if ( ! self::run_setup_wizard_first_time() ) {
			self::add_kb_flag( 'settings_tab_visited' );
		}
	}

	private static function update_article_sidebar_priority( $orig_config, $new_config ) {

		// sanitize sidebar priority
		$article_sidebar_component_priority = array();
		foreach( EPKB_KB_Config_Specs::get_sidebar_component_priority_names() as $component ) {
			if ( $new_config['article_sidebar_component_priority'][$component] != $orig_config['article_sidebar_component_priority'][$component] ) {
				$article_sidebar_component_priority[$component] = sanitize_text_field( $new_config['article_sidebar_component_priority'][$component] );
			}
		}
		$article_sidebar_component_priority = array_merge( $orig_config['article_sidebar_component_priority'], $article_sidebar_component_priority );
		$article_sidebar_component_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $article_sidebar_component_priority );
		$new_config['article_sidebar_component_priority'] = $article_sidebar_component_priority;

		// Sidebar Layout needs to have at least one navigation sidebar enabled
		if ( isset( $new_config['kb_main_page_layout'] ) && $new_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT ) {
			if ( $new_config['article-left-sidebar-toggle'] != 'on' && $new_config['article-right-sidebar-toggle'] != 'on' ) {
				$new_config['article-left-sidebar-toggle'] = 'on';
			}
			if ( $new_config['article_nav_sidebar_type_left'] == 'eckb-nav-sidebar-none' && $new_config['article_nav_sidebar_type_right'] == 'eckb-nav-sidebar-none' ) {
				if ( $new_config['article-left-sidebar-toggle'] == 'on' ) {
					$new_config['article_nav_sidebar_type_left'] = 'eckb-nav-sidebar-v1';
				} else if ( $new_config['article-right-sidebar-toggle'] == 'on' ) {
					$new_config['article_nav_sidebar_type_right'] = 'eckb-nav-sidebar-v1';
				}
			}
		}

		// ensure Article Sidebar content is shown if enabled and its priority was missed; 0 means do not show
		$is_left_article_sidebar_on = $new_config['article-left-sidebar-toggle'] == 'on' && $new_config['article_nav_sidebar_type_left'] != 'eckb-nav-sidebar-none';
		$is_right_article_sidebar_on = $new_config['article-right-sidebar-toggle'] == 'on' && $new_config['article_nav_sidebar_type_right'] != 'eckb-nav-sidebar-none';
		$left_article_sidebar_priority = $new_config['article_sidebar_component_priority']['nav_sidebar_left'];
		$right_article_sidebar_priority = $new_config['article_sidebar_component_priority']['nav_sidebar_right'];
		if ( $is_left_article_sidebar_on && $is_right_article_sidebar_on ) {
			if ( $left_article_sidebar_priority == 0 && $right_article_sidebar_priority == 0 ) {
				$new_config['article_sidebar_component_priority']['nav_sidebar_left'] = 1;
			}
		} else if ( $is_left_article_sidebar_on && $left_article_sidebar_priority == 0 ) {
			$new_config['article_sidebar_component_priority']['nav_sidebar_left'] = 1;
		} else if ( $is_right_article_sidebar_on && $right_article_sidebar_priority == 0 ) {
			$new_config['article_sidebar_component_priority']['nav_sidebar_right'] = 1;
		}

		return $new_config;
	}

	public static function is_module_present( $kb_config, $module_name ) {
		for ( $row_number = 1; $row_number <= 5; $row_number ++ ) {
			$row_key = 'ml_row_' . $row_number . '_module';
			if ( isset( $kb_config[ $row_key ] ) && $kb_config[ $row_key ] == $module_name ) {
				return true;
			}
		}
		return false;
	}

	// sync General typography for all parts of KB Main Page only if the General typography setting was changed
	private static function adjust_topography( $orig_config, $new_config ) {
		$new_config['general_typography']['font-family'] = $new_config['general_typography_font_family'] == 'Inherit' ? '' : $new_config['general_typography_font_family'];
		if ( $new_config['general_typography']['font-family'] != $orig_config['general_typography']['font-family'] ) {

			// initialize settings in new config (until Configuration admin page does not have these typography settings, we need to set them from $orig_config to preserve font-size values)
			$new_config['section_typography'] = $orig_config['section_typography'];
			$new_config['search_input_typography'] = $orig_config['search_input_typography'];
			$new_config['search_title_typography'] = $orig_config['search_title_typography'];
			$new_config['article_typography'] = $orig_config['article_typography'];
			$new_config['section_head_typography'] = $orig_config['section_head_typography'];
			$new_config['section_head_description_typography'] = $orig_config['section_head_description_typography'];
			$new_config['tab_typography'] = $orig_config['tab_typography'];

			// sync font-family
			$new_config['section_typography']['font-family'] = $new_config['general_typography']['font-family'];
			$new_config['search_input_typography']['font-family'] = $new_config['general_typography']['font-family'];
			$new_config['search_title_typography']['font-family'] = $new_config['general_typography']['font-family'];
			$new_config['article_typography']['font-family'] = $new_config['general_typography']['font-family'];
			$new_config['section_head_typography']['font-family'] = $new_config['general_typography']['font-family'];
			$new_config['section_head_description_typography']['font-family'] = $new_config['general_typography']['font-family'];
			$new_config['tab_typography']['font-family'] = $new_config['general_typography']['font-family'];

			// sync font-weight
			$new_config['section_typography']['font-weight'] = '';
			$new_config['search_input_typography']['font-weight'] = '';
			$new_config['search_title_typography']['font-weight'] = '';
			$new_config['article_typography']['font-weight'] = '';
			$new_config['section_head_typography']['font-weight'] = '';
			$new_config['section_head_description_typography']['font-weight'] = '';
			$new_config['tab_typography']['font-weight'] = '';

			// sync Layout specific typography
			switch ( $new_config['kb_main_page_layout'] ) {
				case EPKB_Layout::BASIC_LAYOUT:
				case EPKB_Layout::TABS_LAYOUT:
				case EPKB_Layout::CATEGORIES_LAYOUT:
				case EPKB_Layout::CLASSIC_LAYOUT:
				case EPKB_Layout::DRILL_DOWN_LAYOUT:
				default:
					break;

				case EPKB_Layout::GRID_LAYOUT:
					if ( isset( $orig_config['grid_section_typography'] ) ) {
						$new_config['grid_section_typography'] = $orig_config['grid_section_typography'];
						$new_config['grid_section_typography']['font-family'] = $new_config['general_typography']['font-family'];
						$new_config['grid_section_typography']['font-weight'] = '';
					}
					if ( isset( $orig_config['grid_section_description_typography'] ) ) {
						$new_config['grid_section_description_typography'] = $orig_config['grid_section_description_typography'];
						$new_config['grid_section_description_typography']['font-family'] = $new_config['general_typography']['font-family'];
						$new_config['grid_section_description_typography']['font-weight'] = '';
					}
					if ( isset( $orig_config['grid_section_article_typography'] ) ) {
						$new_config['grid_section_article_typography'] = $orig_config['grid_section_article_typography'];
						$new_config['grid_section_article_typography']['font-family'] = $new_config['general_typography']['font-family'];
						$new_config['grid_section_article_typography']['font-weight'] = '';
					}
					break;

				case EPKB_Layout::SIDEBAR_LAYOUT:
					if ( isset( $orig_config['sidebar_section_category_typography'] ) ) {
						$new_config['sidebar_section_category_typography'] = $orig_config['sidebar_section_category_typography'];
						$new_config['sidebar_section_category_typography']['font-family'] = $new_config['general_typography']['font-family'];
						$new_config['sidebar_section_category_typography']['font-weight'] = '';
					}
					if ( isset( $orig_config['sidebar_section_category_typography_desc'] ) ) {
						$new_config['sidebar_section_category_typography_desc'] = $orig_config['sidebar_section_category_typography_desc'];
						$new_config['sidebar_section_category_typography_desc']['font-family'] = $new_config['general_typography']['font-family'];
						$new_config['sidebar_section_category_typography_desc']['font-weight'] = '';
					}
					if ( isset( $orig_config['sidebar_section_body_typography'] ) ) {
						$new_config['sidebar_section_body_typography'] = $orig_config['sidebar_section_body_typography'];
						$new_config['sidebar_section_body_typography']['font-family'] = $new_config['general_typography']['font-family'];
						$new_config['sidebar_section_body_typography']['font-weight'] = '';
					}
					break;
			}

			// sync Advanced Search typography
			if ( EPKB_Utilities::is_advanced_search_enabled() ) {
				if ( isset( $orig_config['advanced_search_mp_title_typography'] ) ) {
					$new_config['advanced_search_mp_title_typography'] = $orig_config['advanced_search_mp_title_typography'];
					$new_config['advanced_search_mp_title_typography']['font-family'] = $new_config['general_typography']['font-family'];
					$new_config['advanced_search_mp_title_typography']['font-weight'] = '';
				}
				if ( isset( $orig_config['advanced_search_mp_description_below_title_typography'] ) ) {
					$new_config['advanced_search_mp_description_below_title_typography'] = $orig_config['advanced_search_mp_description_below_title_typography'];
					$new_config['advanced_search_mp_description_below_title_typography']['font-family'] = $new_config['general_typography']['font-family'];
					$new_config['advanced_search_mp_description_below_title_typography']['font-weight'] = '';
				}
				if ( isset( $orig_config['advanced_search_mp_input_box_typography'] ) ) {
					$new_config['advanced_search_mp_input_box_typography'] = $orig_config['advanced_search_mp_input_box_typography'];
					$new_config['advanced_search_mp_input_box_typography']['font-family'] = $new_config['general_typography']['font-family'];
					$new_config['advanced_search_mp_input_box_typography']['font-weight'] = '';
				}
				if ( isset( $orig_config['advanced_search_mp_description_below_input_typography'] ) ) {
					$new_config['advanced_search_mp_description_below_input_typography'] = $orig_config['advanced_search_mp_description_below_input_typography'];
					$new_config['advanced_search_mp_description_below_input_typography']['font-family'] = $new_config['general_typography']['font-family'];
					$new_config['advanced_search_mp_description_below_input_typography']['font-weight'] = '';
				}
			}

			return $new_config;
		}
	}

	public static function reset_article_sidebar_widths( $new_config ) {

		$is_left_sidebar_on = EPKB_Articles_Setup::is_left_sidebar_on( $new_config );
		$is_right_sidebar_on = EPKB_Articles_Setup::is_right_sidebar_on( $new_config );

		$left_sidebar_width = 0;
		$left_sidebar_tablet_width = 0;
		if ( $is_left_sidebar_on ) {
			$left_sidebar_width = $new_config['article-left-sidebar-desktop-width-v2'] ?: 24;
			$left_sidebar_tablet_width = $new_config['article-left-sidebar-tablet-width-v2'] ?: 24;
		}

		$right_sidebar_width = 0;
		$right_sidebar_tablet_width = 0;
		if ( $is_right_sidebar_on ) {
			$right_sidebar_width = $new_config['article-right-sidebar-desktop-width-v2'] ?: 24;
			$right_sidebar_tablet_width = $new_config['article-right-sidebar-tablet-width-v2'] ?: 24;
		}

		$new_config['article-left-sidebar-desktop-width-v2'] = $left_sidebar_width;
		$new_config['article-left-sidebar-tablet-width-v2'] = $left_sidebar_tablet_width;
		$new_config['article-right-sidebar-desktop-width-v2'] = $right_sidebar_width;
		$new_config['article-right-sidebar-tablet-width-v2'] = $right_sidebar_tablet_width;

		return $new_config;
	}

	public static function adjust_settings_on_layout_change( $orig_config, $new_config, $is_theme_selected=false ) {

		// do nothing if layout was not changed
		if ( empty( $new_config['kb_main_page_layout'] ) || empty( $orig_config['kb_main_page_layout'] ) || $orig_config['kb_main_page_layout'] == $new_config['kb_main_page_layout'] ) {
			return $new_config;
		}

		EPKB_KB_Demo_Data::reassign_categories_to_articles_based_on_layout( $orig_config['id'], $new_config['kb_main_page_layout'] );

		$to_layout = $new_config['kb_main_page_layout'];

		// Categories Layout has sidebar
		$new_config['archive-content-width-v2'] = $to_layout ==	EPKB_Layout::CATEGORIES_LAYOUT ? 76 : 100;

		// reset certain settings
		if ( ! $is_theme_selected ) {
			$new_config['section_desc_text_on'] = 'off';
		}
		$new_config['nof_articles_displayed'] = 8;

		// get theme to get defaults for given Layout for important settings that preserve the Layout look
		switch ( $to_layout ) {
			default:
			case EPKB_Layout::BASIC_LAYOUT:
				$default_theme = $is_theme_selected ? '' : EPKB_KB_Wizard_Themes::get_theme( 'office', $orig_config );
				break;

			case EPKB_Layout::TABS_LAYOUT:
				$default_theme = $is_theme_selected ? '' : EPKB_KB_Wizard_Themes::get_theme( 'office_tabs', $orig_config );
				break;

			case EPKB_Layout::CATEGORIES_LAYOUT:
				$default_theme = $is_theme_selected ? '' : EPKB_KB_Wizard_Themes::get_theme( 'office_categories', $orig_config );
				break;

			case EPKB_Layout::CLASSIC_LAYOUT:
				$default_theme = $is_theme_selected ? '' : EPKB_KB_Wizard_Themes::get_theme( 'standard_classic', $orig_config );
				break;

			case EPKB_Layout::DRILL_DOWN_LAYOUT:
				$default_theme = $is_theme_selected ? '' : EPKB_KB_Wizard_Themes::get_theme( 'standard_drill_down', $orig_config );
				break;

			case EPKB_Layout::GRID_LAYOUT:
				if ( ! $is_theme_selected ) {
					$default_theme = EPKB_KB_Wizard_Themes::get_theme( 'grid_basic', $orig_config );
					$new_config['grid_section_icon_size'] = $default_theme['grid_section_icon_size'];
					$new_config['grid_category_icon_location'] = $default_theme['grid_category_icon_location'];
					$new_config['grid_section_head_alignment'] = $default_theme['grid_section_head_alignment'];
					$new_config['grid_section_box_height_mode'] = $default_theme['grid_section_box_height_mode'];
					$new_config['grid_section_desc_text_on'] = 'off';
					$new_config['grid_section_body_padding_top'] = 20;
					$new_config['grid_section_body_padding_bottom'] = 0;
					$new_config['grid_section_body_padding_left'] = 0;
					$new_config['grid_section_body_padding_right'] = 0;
					$new_config['grid_section_icon_padding_top'] = 20;
					$new_config['grid_section_icon_padding_bottom'] = 20;
					$new_config['grid_section_icon_padding_left'] = 0;
					$new_config['grid_section_icon_padding_right'] = 0;
				}

				$new_config['grid_section_body_height'] = 350;

				break;

			case EPKB_Layout::SIDEBAR_LAYOUT:
				if ( ! $is_theme_selected ) {
					$default_theme = EPKB_KB_Wizard_Themes::get_theme( 'sidebar_basic', $orig_config );
					$new_config['sidebar_section_head_alignment'] = $default_theme['sidebar_section_head_alignment'];
					$new_config['sidebar_section_box_height_mode'] = $default_theme['sidebar_section_box_height_mode'];
				}
				$new_config['sidebar_section_desc_text_on'] = 'off';
				break;
		}

		if ( ! EPKB_Layouts_Setup::is_elay_layout( $to_layout ) && ! $is_theme_selected ) {
			$new_config['section_head_category_icon_size'] = $default_theme['section_head_category_icon_size'];
			$new_config['section_head_category_icon_location'] = $default_theme['section_head_category_icon_location'];
			$new_config['section_head_alignment'] = $default_theme['section_head_alignment'];
			$new_config['section_box_height_mode'] = $default_theme['section_box_height_mode'];
			$new_config['section_body_height'] = $default_theme['section_body_height'];
			$new_config['section_body_padding_top'] = $default_theme['section_body_padding_top'];
			$new_config['section_body_padding_bottom'] = $default_theme['section_body_padding_bottom'];
			$new_config['section_body_padding_left'] = $default_theme['section_body_padding_left'];
			$new_config['section_body_padding_right'] = $default_theme['section_body_padding_right'];
		}

		return $new_config;
	}

	/**
	 * Triggered when user submits changes to KB configuration
	 *
	 * @param $kb_id
	 * @param $orig_config
	 * @param $new_config
	 *
	 * @return string - updated category icons or empty if no update required
	 */
	public static function prepare_update_to_kb_configuration( $kb_id, $orig_config, $new_config ) {

		// core handles only default KB
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			return esc_html__('Ensure that Unlimited KBs add-on is active and refresh this page', 'echo-knowledge-base');
		}

		// retrieve core KB config with add-ons KB specs
		$field_specs = self::retrieve_all_kb_specs( $kb_id );
		if ( empty( $field_specs ) ) {
			return esc_html__( 'Error occurred. Please refresh your browser and try again.', 'echo-knowledge-base' ) . ' (961)';
		}

		$article_sidebar_priority = $new_config['article_sidebar_component_priority'];

		$new_kb_main_pages = $new_config['kb_main_pages'];

		// sanitize all fields in POST message
		$form_fields = EPKB_Utilities::retrieve_and_sanitize_form( $new_config, $field_specs );
		if ( empty( $form_fields ) ) {
			EPKB_Logging::add_log("form fields missing");
			return esc_html__( 'Error occurred. Please refresh your browser and try again.', 'echo-knowledge-base' ) . ' (962)';
		} else if ( count( $form_fields ) < 100 ) {
			return esc_html__( 'Error occurred. Please refresh your browser and try again.', 'echo-knowledge-base' ) . ' (943)';
		}

		// sanitize fields based on each field type
		$input_handler = new EPKB_Input_Filter();
		$new_config = $input_handler->retrieve_and_sanitize_form_fields( $form_fields, $field_specs, $orig_config );

		// prevent new config to overwrite essential fields
		$new_config['kb_main_pages'] = $new_kb_main_pages;
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		if ( ! is_array( $new_config['kb_main_pages'] ) || empty( $new_config['kb_main_pages'] ) || empty( $new_config['kb_articles_common_path'] ) ) {
			$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
			$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];
		}

		// save KB core configuration
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_config );
		if ( is_wp_error( $result ) ) {

			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty( $message ) ) {
				return esc_html__( 'Could not save the new configuration', 'echo-knowledge-base' ) . ' (31)';
			} else {
				return esc_html__( 'Configuration NOT saved due to following problem:', 'echo-knowledge-base' ) . $message;
			}
		}

		// if sidebar configuration changed then save it - the EPKB_Input_Filter()->retrieve_and_sanitize_form_fields() keeps old values, so save this separately
		$update_sidebar_priority = false;
		foreach( EPKB_KB_Config_Specs::get_sidebar_component_priority_names() as $component ) {
			if ( $article_sidebar_priority[$component] != $orig_config['article_sidebar_component_priority'][$component] ) {
				$article_sidebar_priority[$component] = sanitize_text_field( $article_sidebar_priority[$component] );
				$update_sidebar_priority = true;
			}
		}
		if ( $update_sidebar_priority ) {
			$result = epkb_get_instance()->kb_config_obj->set_value( $orig_config['id'], 'article_sidebar_component_priority', $article_sidebar_priority );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 37, $result ) );
			}
		}

		// save add-ons configuration
		$result = apply_filters( 'eckb_kb_config_save_input_v3', '', $kb_id, $new_config );
		if ( is_wp_error( $result ) ) {
			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty( $message ) ) {
				return esc_html__( 'Could not save the new configuration', 'echo-knowledge-base' ) . ' (31)';
			} else {
				return esc_html__( 'Configuration NOT saved due to following problem', 'echo-knowledge-base' ) . ':' . $message;
			}
		}

		return '';
	}

	/**
	 * If user switches theme presets replace the icons intelligently i.e. replace all non-user defined icons.
	 * Used by Visual Editor and Setup Wizard. Set icon size regardless.
	 * @param $new_config
	 * @param $chosen_preset
	 * @param bool $save_update
	 * @return array
	 */
	public static function get_or_update_new_category_icons( $new_config, $chosen_preset, $save_update=false ) {

		$category_icons = EPKB_KB_Config_Category::get_category_data_option( $new_config['id'] );
		$categories_icons_ids = array();
		foreach( $category_icons as $category_id => $categories_icon ) {
			$categories_icons_ids[] = $category_id;
		}

		// for Tabs Layout, we want to update child categories instead of top level categories which are tabs
		$kb_categories = EPKB_Categories_DB::get_top_level_categories( $new_config['id'] );
		if ( ! empty( $kb_categories ) && $new_config['kb_main_page_layout'] == EPKB_Layout::TABS_LAYOUT ) {
			$kb_categories_child = array();
			foreach( $kb_categories as $kb_category ) {
				$child_categories = EPKB_Categories_DB::get_child_categories( $new_config['id'], $kb_category->term_id );
				foreach( $child_categories as $child_category ) {
					$kb_categories_child[] = $child_category;
				}
			}
			$kb_categories = $kb_categories_child;
		}

		// handle WPML
		if ( EPKB_Utilities::is_wpml_enabled( $new_config ) ) {
			$current_lang = apply_filters( 'wpml_current_language', null );
			foreach ( $kb_categories as $ix => $term ) {
				if ( EPKB_WPML::remove_language_category( $term->term_id, $current_lang ) ) {
					unset( $kb_categories[$ix] );
				}
			}
		}

		$new_icon_type = EPKB_Icons::is_theme_with_image_icons( $new_config ) ? 'image' : 'font';
		$is_photo_icons_preset = EPKB_Icons::is_theme_with_photo_icons( $chosen_preset );

		$default_font_icons = array(
			'image_1'    => 'epkbfa-user',
			'image_2'    => 'epkbfa-pencil',
			'image_3'    => 'epkbfa-sitemap',
			'image_4'    => 'epkbfa-area-chart',
			'image_5'    => 'epkbfa-table',
			'image_6'    => 'epkbfa-cubes'
		);
		$default_theme_image_icons = EPKB_Icons::get_theme_image_icons( $chosen_preset );

		// find and replace defaults with preset default image; prepare font icons and image icons
		$all_default_icons = array_merge( $default_font_icons, [EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME, 'epkbfa-book', 'ep_font_icon_gears', 'epkbfa-cube'] );
		$ix = 0;
		$icons_updated = false;
		foreach ( $kb_categories as $kb_category ) {

			if ( empty( $kb_category->term_id ) ) {
				continue;
			}
			$term_id = $kb_category->term_id;
			if ( empty( $category_icons[$term_id] ) ) {
				continue;
			}

			// don't change user data, only default
			if ( get_term_meta( $kb_category->term_id, EPKB_Icons::CATEGORY_ICON_USER_CHANGED_FLAG, true ) == '1' ) {
				continue;
			}

			// ignore icons that were set by user already
			$current_icon_type = $category_icons[$term_id]['type'];
			if ( ! in_array( $term_id, $categories_icons_ids ) ) {    // category icon is not stored in KB categories icons option
				$user_defined = false;
			} else if ( $new_icon_type == 'font' ) {    // font icon
				$user_defined = $current_icon_type == 'font' && ! in_array( $category_icons[$term_id]['name'], $all_default_icons );
			} else {    // image icon
				$user_defined = $current_icon_type == 'image' && strpos( $category_icons[$term_id]['image_thumbnail_url'], 'img/demo-icons' ) == false &&
					strpos( $category_icons[$term_id]['image_thumbnail_url'], 'www.echoknowledgebase.com' ) == false;
			}

			// do not change user-defined icons
			if ( $user_defined ) {
				continue;
			}

			$chosen_preset = empty( $chosen_preset ) ? 'default' : $chosen_preset;

			$ix = min( ++$ix, 6 );

			// handle font or image category icon
			if ( $new_icon_type == 'font' ) {
				$icon_name = $default_font_icons['image_' . $ix];
				$icon_url = '';

			} else {
				$icon_name = EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME;

				// icon images are local and photo images are elsewhere
				if ( $is_photo_icons_preset ) {
					$icon_url = $default_theme_image_icons['image_' . $ix];
				} else {
					$icon_url = Echo_Knowledge_Base::$plugin_url . ( empty( $default_theme_image_icons['image_' . $ix] ) ? EPKB_Icons::DEFAULT_IMAGE_SLUG : $default_theme_image_icons['image_' . $ix] );
				}
			}

			// update category icon data
			$image_icon = array(
				'type' => $new_icon_type,
				'name' => $icon_name,
				'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
				'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
				'image_thumbnail_url' => $icon_url,
				'color' => '#000000'
			);
			$category_icons[$term_id] = $image_icon;

			$icons_updated = true;
		}

		if ( $icons_updated && $save_update ) {
			EPKB_Utilities::save_kb_option( $new_config['id'], EPKB_Icons::CATEGORIES_ICONS, $category_icons );
		}

		return $icons_updated ? $category_icons : [];
	}

	private static function get_category_archive_page_design( $new_config ) {

		$defaults = array(
			'archive_content_sub_categories_nof_columns' => 2,
			'archive_content_sub_categories_with_articles_toggle' => 'on',
			'archive_content_sub_categories_nof_articles_displayed' => 8,
			'archive_content_sub_categories_icon_toggle' => 'off',
			'archive_content_sub_categories_border_toggle' => 'on',
			'archive_content_sub_categories_background_color' => '#FFFFFF'
		);

		switch ( $new_config['archive_content_sub_categories_display_mode'] ) {
			case 'design-1':
			default:
				$design_settings = array(
					'archive_content_sub_categories_nof_articles_displayed' => 3,
				);
				break;
			case 'design-2':
				$design_settings = array(
					'archive_content_sub_categories_nof_columns' => 3,
					'archive_content_sub_categories_with_articles_toggle' => 'off',
					'archive_content_sub_categories_background_color' => '#F6F6F6'
				);
				break;
			case 'design-3':
				$design_settings = array(
					'archive_content_sub_categories_nof_articles_displayed' => 50,
					'archive_content_sub_categories_icon_toggle' => 'on',
					'archive_content_sub_categories_border_toggle' => 'off',
					'archive_content_articles_arrow_toggle' => 'off',
				);
				break;
		}

		$design_settings = array_merge( $defaults, $design_settings );

		return array_merge( $new_config, $design_settings );
	}


	/**************************************************************************************************************************
	 *
	 *                     CATEGORIES
	 *
	 *************************************************************************************************************************/

	/**
	 *
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Get all existing KB categories.
	 *
	 * @param $kb_id
	 * @param string $order_by
	 * @return array|null - return array of KB categories (empty if not found) or null on error
	 */
	public static function get_kb_categories_unfiltered( $kb_id, $order_by='name' ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		$order = $order_by == 'name' ? 'ASC' : 'DESC';
		$order_by = $order_by == 'date' ? 'term_id' : $order_by;   // terms don't have date so use id
		$kb_category_taxonomy_name = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* " .
												   " FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id " .
												   " WHERE tt.taxonomy = %s ORDER BY " . esc_sql( 't.' . $order_by ) . ' ' . esc_sql( $order ), $kb_category_taxonomy_name ) );
		return isset($result) && is_array($result) ? $result : null;
	}

	/**
	 *
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS. Check Draft field
	 *
	 * Get all existing KB categories.
	 *
	 * @param $kb_id
	 * @param string $order_by
	 * @return array|null - return array of KB categories (empty if not found) or null on error
	 */
	public static function get_kb_categories_visible( $kb_id, $order_by='name' ) {

		$all_categories = self::get_kb_categories_unfiltered( $kb_id, $order_by );
		if ( empty( $all_categories ) ) {
			return $all_categories;
		}

		$categories_data = EPKB_KB_Config_Category::get_category_data_option( $kb_id );
		foreach( $all_categories as $key => $category ) {
			$term_id = $category->term_id;

			if ( empty( $term_id ) ) {
				continue;
			}

			if ( empty( $categories_data[$term_id] ) ) {
				continue;
			}

			// remove draft categories
			if ( ! empty( $categories_data[$term_id]['is_draft'] ) ) {
				unset( $all_categories[$key] );
			}
		}

		return $all_categories;
	}

	/**
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Get KB Article categories.
	 *
	 * @param $kb_id
	 * @param $article_id
	 * @return array|null - categories belonging to the given KB Article or null on error
	 */
	public static function get_article_categories_unfiltered( $kb_id, $article_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( empty($article_id) ) {
			return null;
		}

		// get article categories
		$post_taxonomy_objs = $wpdb->get_results( $wpdb->prepare(
					"SELECT * FROM $wpdb->term_taxonomy
					 WHERE taxonomy = %s and term_taxonomy_id in
					(SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = %d) ",
						EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), $article_id ) );
		if ( ! empty($wpdb->last_error) ) {
			return null;
		}

		$categories = $post_taxonomy_objs === null || ! is_array($post_taxonomy_objs) ? array() : $post_taxonomy_objs;

		// convert to term objects
		$categories_obj = [];
		foreach ( $categories as $key => $category ) {
			if ( empty( $category->term_id ) || empty( $category->taxonomy ) ) {
				continue;
			}
			$term = get_term( $category->term_id, $category->taxonomy );
			if ( ! empty( $term ) && ! is_wp_error( $term ) && property_exists( $term, 'term_id' ) ) {
				$categories_obj[$key] = $term;
			}
		}

		return $categories_obj;
	}

	/**
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Get KB Article categories.
	 *
	 * @param $kb_id
	 * @param $article_id
	 * @return array|null - categories belonging to the given KB Article or null on error
	 */
	public static function get_article_categories_visible( $kb_id, $article_id ) {

		$categories = self::get_article_categories_unfiltered( $kb_id, $article_id );
		if ( empty( $categories ) ) {
			return $categories;
		}

		$categories_data = EPKB_KB_Config_Category::get_category_data_option( $kb_id );
		foreach( $categories as $key => $category ) {

			$term_id = $category->term_id;
			if ( empty( $term_id ) ) {
				continue;
			}

			if ( empty( $categories_data[$term_id] ) ) {
				continue;
			}

			// remove draft categories
			if ( ! empty( $categories_data[$term_id]['is_draft'] ) ) {
				unset( $categories[$key] );
			}
		}

		return $categories;
	}

	/**
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Retrieve KB Category.
	 *
	 * @param $kb_id
	 * @param $kb_category_id
	 * @return WP_Term|false
	 */
	public static function get_kb_category_unfiltered( $kb_id, $kb_category_id ) {
		$term = get_term_by('id', $kb_category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) );
		if ( empty($term) || ! $term instanceof WP_Term ) {
			EPKB_Logging::add_log( "Category is not KB Category: " . $kb_category_id . " (35)", $kb_id );
			return false;
		}

		return $term;
	}


	/********************************************************************************
	 *
	 *                                   VARIOUS
	 *
	 ********************************************************************************/

	public static function get_style_setting_name( $layout ) {

		$shadow_setting_name = 'section_box_shadow';
		$background_color_setting_name = 'section_body_background_color';
		$border_setting_prefix = 'section_border';
		$head_font_color_setting_name = 'section_head_font_color';
		$head_typography_setting_name = 'section_head_typography';
		$article_typography_setting_name = 'article_typography';
		$article_font_color_setting_name = 'article_font_color';
		$article_icon_color_setting_name = 'article_icon_color';
		if ( EPKB_Utilities::is_elegant_layouts_enabled() ) {
			switch ( $layout ) {
				case EPKB_Layout::GRID_LAYOUT:
					$shadow_setting_name = 'grid_section_box_shadow';
					$border_setting_prefix = 'grid_section_border';
					$head_typography_setting_name = 'grid_section_typography';
					$article_font_color_setting_name = 'section_category_font_color';
					break;
				case EPKB_Layout::SIDEBAR_LAYOUT:
					$shadow_setting_name = 'sidebar_section_box_shadow';
					$background_color_setting_name = 'sidebar_background_color';
					$border_setting_prefix = 'sidebar_section_border';
					$head_font_color_setting_name = 'sidebar_section_head_font_color';
					$head_typography_setting_name = 'sidebar_section_category_typography';
					$article_typography_setting_name = 'sidebar_section_body_typography';
					$article_font_color_setting_name = 'sidebar_article_font_color';
					$article_icon_color_setting_name = 'sidebar_article_icon_color';
					break;
				default: break;
			}
		}

		return array(
			'shadow' => $shadow_setting_name,
			'background_color' => $background_color_setting_name,
			'border_prefix' => $border_setting_prefix,
			'head_font_color' => $head_font_color_setting_name,
			'head_typography' => $head_typography_setting_name,
			'article_typography' => $article_typography_setting_name,
			'article_font_color' => $article_font_color_setting_name,
			'article_icon_color' => $article_icon_color_setting_name
		);
	}


	/**
	 * Retrieve the KB flag. If preset it indicates true and if missing it indicated false flag.
	 *
	 * @param $flag_name
	 * @return true|false
	 */
	public static function is_kb_flag_set($flag_name ) {
		$kb_flags = EPKB_Utilities::get_wp_option( 'epkb_flags', [], true );

		$kb_flags = is_array( $kb_flags ) ? $kb_flags : [];

		return in_array( $flag_name, $kb_flags );
	}

	public static function add_kb_flag( $flag_name ) {
		return self::update_kb_flag( $flag_name );
	}

	public static function remove_kb_flag( $flag_name ) {
		return self::update_kb_flag( $flag_name, false );
	}

	/**
	 * Update KB Flag value to either add the flag or remove the flag.
	 *
	 * @param $flag_name - true to ADD and false to REMOVE
	 * @param $add_flag
	 *
	 * @return mixed|WP_Error true if the value was changed, false if not, WP_Error if something went wrong
	 */
	public static function update_kb_flag( $flag_name, $add_flag = true ) {

		$kb_flags = EPKB_Utilities::get_wp_option( 'epkb_flags', [], true );

		// need value true and already true
		if ( $add_flag && in_array( $flag_name, $kb_flags ) ) {
			return false;
		}

		// need false and already false
		if ( empty( $add_flag ) && ! in_array( $flag_name, $kb_flags ) ) {
			return false;
		}

		// need false but true
		if ( empty( $add_flag ) && in_array( $flag_name, $kb_flags ) ) {
			$kb_flags = array_diff( $kb_flags, [ $flag_name ] );
		}

		// need true but false
		if ( $add_flag && ! in_array( $flag_name, $kb_flags ) ) {
			$kb_flags[] = $flag_name;
		}

		$result = EPKB_Utilities::save_wp_option( 'epkb_flags', $kb_flags );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Return sales page for given plugin
	 *
	 * @param $plugin_name
	 * @return String
	 */
	public static function get_plugin_sales_page( $plugin_name ) {
		switch( $plugin_name ) {
			case 'amgr':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/';
			case 'kblk':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/';
			case 'elay':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/';
			case 'eprf':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/';
			case 'asea':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/';
			case 'widg':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/widgets/';
			case 'amcr':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/custom-roles/';
			case 'amgp':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/kb-groups/';
			case 'emkb':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/';
			case 'epie':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/';
			case 'crel':
				return 'https://wordpress.org/plugins/creative-addons-for-elementor/?utm_source=wp-repo&utm_medium=link&utm_campaign=readme';
			case 'ep'.'hd':
				return 'https://wordpress.org/plugins/help-dialog/?utm_source=wp-repo&utm_medium=link&utm_campaign=readme';
			case 'am'.'gp':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/kb-groups/?utm_source=wp-repo&utm_medium=link&utm_campaign=readme';
			case 'am'.'cr':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/custom-roles/?utm_source=wp-repo&utm_medium=link&utm_campaign=readme';
		}

		return 'https://www.echoknowledgebase.com/wordpress-add-ons/';
	}

	/**
	 * Is this search for the main page or for the article page; consider search sync, Sidebar Layout and search results page (is_kb_main_page)
	 * @param $kb_config
	 * @return boolean
	 */
	public static function is_main_page_search( $kb_config ) {
		// $kb_config['article_search_sync_toggle'] == 'on' -> handled by copying Main Page search settings to Article Page on settings save operation
		return $kb_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT || EPKB_Utilities::is_kb_main_page() || EPKB_Utilities::get( 'is_kb_main_page' ) == 1 || is_archive();
	}

	/**
	 * Get link to the current KB main page
	 *
	 * @param $kb_config
	 * @param $link_text
	 * @param string $link_class
	 * @return string
	 */
	public static function get_current_kb_main_page_link( $kb_config, $link_text, $link_class='' ) {

		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
		if ( empty( $link_output ) ) {
			return false;
		}
		return '<a href="' . esc_url( $link_output ) . '" target="_blank" class="' . esc_attr( $link_class ) . '">' . esc_html( $link_text ) . '</a>';
	}

	/**
	 * Get link to KB admin page
	 *
	 * @param $url_param
	 * @param $label_text
	 * @param bool $target_blank
	 * @param string $css_class
	 * @return string
	 */
	public static function get_kb_admin_page_link( $url_param, $label_text, $target_blank=true, $css_class='' ) {
		return '<a class="epkb-kb__wizard-link ' . esc_attr( $css_class ) . '" href="' . esc_url( admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) .
		                                                             ( empty( $url_param ) ? '' : '&' ) . $url_param ) ) . '"' . ( empty( $target_blank ) ? '' : ' target="_blank"' ) . '>' . esc_html( $label_text ) . '</a>';
	}

	/**
	 * Show list of KBs.
	 *
	 * @param $kb_config
	 * @param array $contexts
	 */
	public static function admin_list_of_kbs( $kb_config, $contexts=[] ) {    ?>

		<select id="epkb-list-of-kbs" data-active-kb-id="<?php echo esc_attr( $kb_config['id'] ); ?>">      <?php

			$found_archived_kbs = false;
			$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
			foreach ( $all_kb_configs as $one_kb_config ) {

				$one_kb_id = $one_kb_config['id'];

				// Do not show archived KBs
				if ( $one_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && self::is_kb_archived( $one_kb_config['status'] ) ) {
					$found_archived_kbs = true;
					continue;
				}

				// Do not render the KB into the dropdown if the current user does not have at least minimum required capability (covers KB Groups)
				$required_capability = EPKB_Admin_UI_Access::get_contributor_capability( $one_kb_id );
				if ( ! current_user_can( $required_capability ) ) {
					continue;
				}

				// Redirect to All Articles page if the user does not have access for the current page for this KB in drop down
				$redirect_url = '';
				if ( ! empty( $contexts ) ) {
					$required_capability = EPKB_Admin_UI_Access::get_context_required_capability( $contexts, $one_kb_config );
					if ( ! current_user_can( $required_capability ) ) {
						$redirect_url = admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $one_kb_id ) );
					}
				}

				$kb_name = $one_kb_config[ 'kb_name' ];
				$active = ( $kb_config['id'] == $one_kb_id && ! isset( $_GET['archived-kbs'] ) ? 'selected' : '' );   ?>

				<option data-plugin="core" value="<?php echo empty( $redirect_url ) ? esc_attr( $one_kb_id ) : 'closed'; ?>"<?php echo empty( $redirect_url ) ? '' : ' data-target="' . esc_url( $redirect_url ) . '"'; ?> <?php echo esc_attr( $active ); ?>><?php
					esc_html_e( $kb_name ); ?>
				</option>      <?php
			}

			if ( $found_archived_kbs && EPKB_Utilities::post( 'page' ) == 'epkb-kb-configuration' ) {
				//phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<option data-plugin="core" value="archived"<?php echo isset( $_GET['archived-kbs'] ) ? ' selected' : ''; ?>><?php esc_html_e( 'View Archived KBs', 'echo-knowledge-base' ); ?></option>  <?php
			}

			if ( ! EPKB_Utilities::is_multiple_kbs_enabled() && count($all_kb_configs) == 1 ) {     ?>
				<option data-plugin="core" data-link="https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/"><?php esc_html_e( 'Get Additional Knowledge Bases', 'echo-knowledge-base' ); ?></option>  <?php
			}

			// Hook to add new options to the admin header dropdown
			if ( current_user_can( EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY ) ) {
				do_action( 'eckb_kb_admin_header_dropdown' );
			}   ?>

		</select>   <?php
	}

	public static function get_nav_sidebar_type( $kb_config, $side ) {

		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );

		// nav sidebar is set to Not displayed if the priority is 0
		if ( $side == 'left' && $sidebar_priority['nav_sidebar_left'] == '0' ) {
			return 'eckb-nav-sidebar-none';
		}

		if ( $side == 'right' && $sidebar_priority['nav_sidebar_right'] == '0' ) {
			return 'eckb-nav-sidebar-none';
		}

		return $side == 'left' ? $kb_config['article_nav_sidebar_type_left'] : $kb_config['article_nav_sidebar_type_right'];
	}

	public static function get_nav_sidebar_priority( $kb_config, $side ) {

		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );

		$nav_left_priority = empty( $sidebar_priority['nav_sidebar_left'] ) ? '0' : $sidebar_priority['nav_sidebar_left'];
		$nav_right_priority = empty( $sidebar_priority['nav_sidebar_right'] ) ? '0' : $sidebar_priority['nav_sidebar_right'];

		return $side == 'left' ? $nav_left_priority : $nav_right_priority;
	}

	/**
	 * Detect if we are on the editor backend mode
	 * @return bool
	 */
	public static function is_backend_editor_iframe() {

		$is_editor_backend_mode = self::is_kb_flag_set( 'editor_backend_mode' );
		if ( empty( $is_editor_backend_mode ) ) {
			return false;
		}

		// backend iframe with editor
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'epkb_load_editor' );
	}

	/**
	 * Retrieve user IP address if possible.
	 *
	 * @return string
	 */
	public static function get_ip_address() {

		$ip_params = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' );
		foreach ( $ip_params as $ip_param ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_SERVER[$ip_param] ) ) {
				//phpcs:ignore WordPress.Security.NonceVerification.Recommended
				foreach ( explode( ',', sanitize_text_field( wp_unslash( $_SERVER[$ip_param] ) ) ) as $ip ) {
					$ip = trim( $ip );

					// validate IP address
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
						return esc_attr( $ip );
					}
				}
			}
		}

		return '';
	}

	public static function display_missing_css_message( $kb_config=[] ) {

		$kb_config = empty( $kb_config ) ? epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID ) : $kb_config;

		// display warning only for new users
		if ( $kb_config['first_plugin_version'] != $kb_config['upgrade_plugin_version'] ) {
			return;
		}

		if ( ! EPKB_Utilities::is_user_admin() ) {
			return;
		}

		echo
		'<!-- This is for cases of CSS incorrect loading -->

		<style>
			.epkb-css-missing-message {
				color: red !important;
			    line-height: 1.2em !important;
			    text-align: center !important;
			    background-color: #eaeaea !important;
			    border: solid 1px #ddd !important;
			    padding: 20px !important;
			    max-width: 1000px !important;
			    margin: 20px auto !important;
			    font-size: 20px !important;
			}
			.epkb-css-missing-message a {
				color: #077add !important;
		        text-decoration: underline !important;
			}
		</style>
		
		<h1 class="epkb-css-missing-message epkb-css-working-hide-message">' .
			esc_html__( 'The Knowledge Base files containing CSS are missing, causing page elements below to misalign. This issue may be due to a 3rd-party plugin or caching conflict. ' .
						'Please contact us for help or ensure the KB CSS files are correctly included.', 'echo-knowledge-base' ) . ' ' .
				'<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank">' . esc_html__( 'Our Contact Form', 'echo-knowledge-base' ) . '</a>' .
		'</h1>';
	}

	/**
	 * Generic admin page to display message on configuration error
	 */
	public static function display_config_error_page( $message='', $html='' ) {    ?>
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap--config-error">    <?php
			$message = empty( $message ) ? esc_html__( 'Cannot load configuration.', 'echo-knowledge-base' ) : $message;
			$html = empty( $html ) ? EPKB_Utilities::contact_us_for_support() : $html;
			EPKB_HTML_Forms::notification_box_middle( [ 'type' => 'error', 'title' => $message, 'html' => $html ] );  ?>
		</div>  <?php

	}
}
