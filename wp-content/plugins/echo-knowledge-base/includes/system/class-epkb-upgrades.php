<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if plugin upgrade to a new version requires any actions like database upgrade
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Upgrades {

	const GRID_UPGRADE_DONE = 3;
	const NOT_INITIALIZED = '12.30.99'; // TODO remove 2025

	public function __construct() {
		// will run after plugin is updated but not always like front-end rendering
		add_action( 'admin_init', array( 'EPKB_Upgrades', 'update_plugin_version' ) );

		// show initial page after install addons
		//add_action( 'admin_init', array( 'EPKB_Upgrades', 'initial_addons_setup' ), 1 );

		// show initial page after install
		add_action( 'admin_init', array( 'EPKB_Upgrades', 'initial_setup' ), 20 );

		// show additional messages on the plugins page
		add_action( 'in_plugin_update_message-echo-knowledge-base/echo-knowledge-base.php',  array( $this, 'in_plugin_update_message' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
	}

	/**
	 * Display license screen on addon first activation or upgrade - redirect admin user once on visiting any KB admin page
	 */
	public static function initial_addons_setup() {

		// continue only for admin user, on any KB admin page
		if ( ! current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) || ! is_admin() || ! EPKB_KB_Handler::is_kb_request() ) {
			return;
		}

		// ensure all transients are deleted before redirecting user
		$redirect_to_licenses = false;
		$addons = [ 'emkb', 'epie',	'elay', 'kblk', 'eprf',	'asea',	'widg', 'amgp', 'amcr' ];
		foreach ( $addons as $addon ) {

			// check is addon not recently activated
			$addon_activated = get_transient( "_{$addon}_plugin_activated" );
			if ( ! empty( $addon_activated ) ) {
				delete_transient( "_{$addon}_plugin_activated" );
				$redirect_to_licenses = true;
			}
		}

		// redirect to Getting Started Licenses tab
		if ( ! empty( $redirect_to_licenses ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=ep'.'kb-add-ons&epkb_after_addons_setup#licenses') );
			exit;
		}
	}

	/**
	 * Trigger display of wizard setup screen on plugin first activation or upgrade; does NOT work if multiple plugins installed at the same time
	 */
	public static function initial_setup() {

		$kb_version = EPKB_Utilities::get_wp_option( 'epkb_version', null );
		if ( empty( $kb_version) ) {
			return;
		}

		// ignore if plugin not recently activated
		$plugin_installed = get_transient( '_epkb_plugin_installed' );
		if ( empty( $plugin_installed ) ) {
			return;
		}

		// return if activating from network or doing bulk activation
		if ( is_network_admin() || isset($_GET['activate-multi']) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_epkb_plugin_installed' );

		// if setup ran then do not proceed
		if ( ! EPKB_Core_Utilities::run_setup_wizard_first_time() ) {
			return;
		}

		// run setup wizard
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=epkb-kb-configuration&setup-wizard-on' ) );
		exit;
	}

	/**
	 * If necessary run plugin database updates
	 */
	public static function update_plugin_version() {

		// ensure the plugin version and configuration is set
		$last_version = EPKB_Utilities::get_wp_option( 'epkb_version', null );
		if ( empty( $last_version ) ) {
			EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version );
			epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'first_plugin_version', Echo_Knowledge_Base::$version ); // TODO 2025 remove and in the specs
			return;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID, true );
		if ( is_wp_error( $kb_config ) ) {
			// TODO report error in admin page
			return;
		}

		// initialize plugin first version if empty or not initialized
		if ( empty( $kb_config['first_plugin_version'] ) || $kb_config['first_plugin_version'] == self::NOT_INITIALIZED ) {
			$first_plugin_version = EPKB_Utilities::get_wp_option( 'epkb_version_first', '' );
			$first_plugin_version = empty( $first_plugin_version ) ? $last_version : $first_plugin_version;
			epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'first_plugin_version', $first_plugin_version );
		}

		// initialize plugin upgraded version if empty or not initialized
		$last_upgrade_version = $kb_config['upgrade_plugin_version'];
		if ( empty( $last_upgrade_version ) || $last_upgrade_version == self::NOT_INITIALIZED ) {
			$last_upgrade_version = $last_version;
			epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'upgrade_plugin_version', $last_upgrade_version );
		}

		// if plugin is up-to-date then return
		if ( version_compare( $last_upgrade_version, Echo_Knowledge_Base::$version, '>=' ) ) {
			return;
		}

		// upgrade the plugin
		self::invoke_upgrades( $last_upgrade_version );

		EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version );
	}

	/**
	 * Invoke each database update as necessary.
	 *
	 * @param $last_version
	 */
	private static function invoke_upgrades( $last_version ) {

		// update all KBs
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $kb_config ) {

			self::run_upgrades( $kb_config, $last_version );

			$kb_config['upgrade_plugin_version'] = Echo_Knowledge_Base::$version;

			// store the updated KB data
			epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_config['id'], $kb_config );
		}

		// ensure default KB is updated
		epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'upgrade_plugin_version', Echo_Knowledge_Base::$version );
	}

	public static function run_upgrades( &$kb_config, $last_version ) {

	    if ( version_compare( $last_version, '11.0.1', '<' ) ) {
		    self::upgrade_to_v11_0_1( $kb_config );
	    }

		if ( version_compare( $last_version, '11.30.0', '<' ) ) {
			self::upgrade_to_v11_30_0( $kb_config );
		}

		if ( version_compare( $last_version, '11.30.1', '<' ) ) {
			self::upgrade_to_v11_30_1( $kb_config );
		}

		if ( version_compare( $last_version, '11.31.0', '<' ) ) {
			self::upgrade_to_v11_31_0( $kb_config );
		}
		
		if ( version_compare( $last_version, '11.40.0', '<' ) ) {
			self::upgrade_to_v11_40_0( $kb_config );
		}

		if ( version_compare( $last_version, '11.41.0', '<' ) ) {
			self::upgrade_to_v11_41_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.0.0', '<' ) ) {
			self::upgrade_to_v12_0_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.11.0', '<' ) ) {
			self::upgrade_to_v12_11_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.21.0', '<' ) ) {
			self::upgrade_to_v12_21_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.30.0', '<' ) ) {
			self::upgrade_to_v12_30_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.32.0', '<' ) ) {
			self::upgrade_to_v12_32_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.42.0', '<' ) ) {
			self::upgrade_to_v12_42_0( $kb_config );
		}
	}

	private static function upgrade_to_v12_42_0( &$kb_config ) {
		$api_key = EPKB_Utilities::get_wp_option( 'epkb_openai_api_key', '' );
		if ( empty( $api_key ) || ! is_string( $api_key ) ) {
			$api_key = '';
		}

		$api_key = EPKB_Utilities::encrypt_data( $api_key );

		$result = EPKB_Utilities::save_wp_option('epkb_openai_key', $api_key );
		if ( ! is_wp_error( $result ) ) {
			delete_option( 'epkb_openai_api_key' );
		}
	}

	private static function upgrade_to_v12_32_0( &$kb_config ) {
		$kb_config['visual_helper_switch_visibility_toggle'] = 'off';
	}

	private static function upgrade_to_v12_30_0( &$kb_config ) {
		$kb_config['template_for_archive_page'] = $kb_config['templates_for_kb'];
	}

	private static function upgrade_to_v12_21_0( &$kb_config ) {
		if ( EPKB_Utilities::is_advanced_search_enabled() && function_exists( 'asea_get_instance' ) && isset( asea_get_instance()->kb_config_obj ) ) {

			$asea_config = asea_get_instance()->kb_config_obj->get_kb_config( $kb_config['id'] );
			$asea_config_valid = !empty( $asea_config ) && is_array( $asea_config ) && !is_wp_error( $asea_config );

			if ( $asea_config_valid ) {
				$kb_config['search_box_hint'] = empty( $asea_config['advanced_search_mp_box_hint'] ) ? $kb_config['search_box_hint'] : $asea_config['advanced_search_mp_box_hint'];
				$kb_config['article_search_box_hint'] = empty( $asea_config['advanced_search_ap_box_hint'] ) ? $kb_config['article_search_box_hint'] : $asea_config['advanced_search_ap_box_hint'];
			}
		}
	}

	private static function upgrade_to_v12_11_0( &$kb_config ) {
		$kb_config['archive_content_articles_display_mode'] =  empty( $kb_config['archive_content_article_display_mode'] ) ? 'title' : $kb_config['archive_content_article_display_mode'];
	}

	private static function upgrade_to_v12_0_0( &$kb_config ) {
		// starting from version 12.00.0 the Archive Page is V3 by default (the toggle is 'on' in specs); ensure it is set to 'off' for all previous KB versions during the upgrade
		$kb_config['archive_page_v3_toggle'] = 'off';
	}

	private static function upgrade_to_v11_41_0( &$kb_config ) {

		if ( empty( $kb_config['ml_faqs_title_text'] ) ) {
			$kb_config['ml_faqs_title_text'] = esc_html__( 'Frequently Asked Questions', 'echo-knowledge-base' );
			$kb_config['ml_faqs_title_location'] = 'none';
		}
		if ( empty( $kb_config['ml_articles_list_title_text'] ) ) {
			$kb_config['ml_articles_list_title_text'] = esc_html__( 'Featured Articles', 'echo-knowledge-base' );
			$kb_config['ml_articles_list_title_location'] = 'none';
		}

		if ( EPKB_Utilities::is_advanced_search_enabled() && function_exists( 'asea_get_instance' ) && isset( asea_get_instance()->kb_config_obj ) ) {

			$asea_config = asea_get_instance()->kb_config_obj->get_kb_config( $kb_config['id'] );
			$asea_config_valid = !empty( $asea_config ) && is_array( $asea_config ) && !is_wp_error( $asea_config );

			if ( $asea_config_valid ) {
				$kb_config['article_search_toggle'] = ! empty( $asea_config['advanced_search_ap_box_visibility'] ) && $asea_config['advanced_search_ap_box_visibility'] == 'asea-visibility-search-form-2' ? 'off' : 'on';
			}
		}
	}

	private static function upgrade_to_v11_40_0( &$kb_config ) 	{

		// switch to single font family
		if ( ! EPKB_Utilities::is_new_user( $kb_config, '11.40.0' ) && ! empty( $kb_config['section_head_typography']['font-family'] ) ) {
			$kb_config['general_typography']['font-family'] = $kb_config['section_head_typography']['font-family'];
		}

		// remove common fields from Grid Layout; fix Sidebar typography
		if ( ( $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT || $kb_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT ) &&
				EPKB_Utilities::is_elegant_layouts_enabled() && function_exists( 'elay_get_instance' ) && isset( elay_get_instance()->kb_config_obj ) ) {

			$elay_config = elay_get_instance()->kb_config_obj->get_kb_config( $kb_config['id'] );
			$elay_config_valid = ! empty( $elay_config ) && is_array( $elay_config) && ! is_wp_error( $elay_config );

			// switch to single font family
			if ( $elay_config_valid && ! EPKB_Utilities::is_new_user( $kb_config, '11.40.0' ) ) {
				if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT && ! empty( $elay_config['grid_section_typography']['font-family'] ) ) {
					$kb_config['general_typography']['font-family'] = $elay_config['grid_section_typography']['font-family'];
				} else if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && ! empty( $elay_config['sidebar_section_category_typography']['font-family'] ) ) {
					$kb_config['general_typography']['font-family'] = $elay_config['sidebar_section_category_typography']['font-family'];
				}
			}

			if ( $elay_config_valid && $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT ) {
				$kb_config['section_head_category_icon_color'] = empty( $elay_config['grid_section_head_icon_color'] ) ? $kb_config['section_head_category_icon_color'] : $elay_config['grid_section_head_icon_color'];
				$kb_config['section_category_font_color'] = empty( $elay_config['grid_section_body_text_color'] ) ? $kb_config['section_category_font_color'] : $elay_config['grid_section_body_text_color'];
				$kb_config['section_border_radius'] = empty( $elay_config['grid_section_border_radius'] ) ? $kb_config['section_border_radius'] : $elay_config['grid_section_border_radius'];
				$kb_config['section_border_width'] = empty( $elay_config['grid_section_border_width'] ) ? $kb_config['section_border_width'] : $elay_config['grid_section_border_width'];
				$kb_config['section_border_color'] = empty( $elay_config['grid_section_border_color'] ) ? $kb_config['section_border_color'] : $elay_config['grid_section_border_color'];
				$kb_config['section_body_background_color'] = empty( $elay_config['grid_section_body_background_color'] ) ? $kb_config['section_body_background_color'] : $elay_config['grid_section_body_background_color'];
				$kb_config['section_head_background_color'] = empty( $elay_config['grid_section_head_background_color'] ) ? $kb_config['section_head_background_color'] : $elay_config['grid_section_head_background_color'];
				$kb_config['section_divider_color'] = empty( $elay_config['grid_section_divider_color'] ) ? $kb_config['section_divider_color'] : $elay_config['grid_section_divider_color'];
				$kb_config['section_head_font_color'] = empty( $elay_config['grid_section_head_font_color'] ) ? $kb_config['section_head_font_color'] : $elay_config['grid_section_head_font_color'];
				$kb_config['section_head_description_font_color'] = empty( $elay_config['grid_section_head_description_font_color'] ) ? $kb_config['section_head_description_font_color'] : $elay_config['grid_section_head_description_font_color'];
				$kb_config['category_empty_msg'] = empty( $elay_config['grid_category_empty_msg'] ) ? $kb_config['category_empty_msg'] : $elay_config['grid_category_empty_msg'];

				$kb_config['sidebar_article_list_spacing'] = self::GRID_UPGRADE_DONE;
			}
		}

		$kb_config['ml_categories_articles_sidebar_desktop_width'] = self::update_modular_sidebar_width( $kb_config );

		$kb_config['section_head_category_icon_size'] = $kb_config['section_head_category_icon_size'] > 225 ? 225 : $kb_config['section_head_category_icon_size'];
	}

	public static function update_modular_sidebar_width( $kb_config ) {

		// Find which Row the Categories Module is saved too.
		$module_name = '';
		foreach ( $kb_config as $key => $value ) {
			if ( $value === 'categories_articles' ) {
				$module_name = $key;
			}
		}

		if ( empty( $module_name ) ) {
			return 28;
		}

		// Get the Row Values based on which row the Category articles module has been assigned to.
		$row_width = '';
		$row_units = '';
		switch ( $module_name ) {
			case 'ml_row_1_module':
				$row_width = $kb_config['ml_row_1_desktop_width'];
				$row_units = $kb_config['ml_row_1_desktop_width_units'];
				break;
			case 'ml_row_2_module':
				$row_width = $kb_config['ml_row_2_desktop_width'];
				$row_units = $kb_config['ml_row_2_desktop_width_units'];
				break;
			case 'ml_row_3_module':
				$row_width = $kb_config['ml_row_3_desktop_width'];
				$row_units = $kb_config['ml_row_3_desktop_width_units'];
				break;
			case 'ml_row_4_module':
				$row_width = $kb_config['ml_row_4_desktop_width'];
				$row_units = $kb_config['ml_row_4_desktop_width_units'];
				break;
			case 'ml_row_5_module':
				$row_width = $kb_config['ml_row_5_desktop_width'];
				$row_units = $kb_config['ml_row_5_desktop_width_units'];
				break;
			default:
				break;
		}

		if ( empty( $row_units ) || ( $row_units == 'px' && empty( $row_width ) ) || ! is_numeric( $row_width ) ) {
			return 28;
		}

		// find closest standard value
		$width = $kb_config['ml_categories_articles_sidebar_desktop_width'];
		if ( $row_units == 'px' && ! empty( $row_width ) ) {
			$width = round( 100 * $kb_config['ml_categories_articles_sidebar_desktop_width'] / $row_width );
		}

		return $width < 27 ? 25 : ( $width < 29 ? 28 : 30 );
	}

	private static function upgrade_to_v11_31_0( &$kb_config ) {

		// do not upgrade if already upgraded
		if ( empty( $kb_config['article_toc_position'] ) || empty( $kb_config['article-structure-version'] ) || $kb_config['article-structure-version'] != 'version-1' ) {
			return;
		}

		// user with article v1 is switched to article v2
		if ( $kb_config['article_toc_enable'] == 'on' ) {

			if ( $kb_config['article_toc_position'] == 'left' ) {
				$kb_config['article_sidebar_component_priority']['toc_left'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'right' ) {
				$kb_config['article_sidebar_component_priority']['toc_right'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'middle' ) {
				$kb_config['article_sidebar_component_priority']['toc_content'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			}
		}

		// recalculate width for version 1 article page
		$kb_config = EPKB_Core_Utilities::reset_article_sidebar_widths( $kb_config );
	}

	private static function upgrade_to_v11_30_1( &$kb_config ) {

		// handle article list spacing
		if ( EPKB_Utilities::is_elegant_layouts_enabled() && function_exists( 'elay_get_instance' ) && isset( elay_get_instance()->kb_config_obj ) ) {
			$elay_config = elay_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_config['id'] );
			if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT && isset( $elay_config['grid_article_list_spacing'] ) ) {
				$kb_config['article_list_spacing'] = $elay_config['grid_article_list_spacing'];
			}
			if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && isset( $elay_config['sidebar_article_list_spacing'] ) ) {
				$kb_config['article_list_spacing'] = $elay_config['sidebar_article_list_spacing'];
			}

			// ensure $kb_config['article_list_spacing'] is valid parameter for min function
			$article_list_spacing = (int)$kb_config['article_list_spacing'];
			$article_list_spacing =  min( $article_list_spacing, 50 );
			$kb_config['article_list_spacing'] = empty( $article_list_spacing ) ? 8 : $article_list_spacing;
		}

		// previously Article Page Search had the same layout as Main Page Search
		$kb_config['ml_article_search_layout'] = isset( $kb_config['ml_search_layout'] ) ? $kb_config['ml_search_layout'] : 'classic';

		// only new users have Article Page Search synced with Main Page Search by default
		$kb_config['article_search_sync_toggle'] = 'off';
	}

	private static function upgrade_to_v11_30_0( &$kb_config ) {

		$kb_config['ml_categories_articles_sidebar_location'] = isset( $kb_config['ml_categories_articles_sidebar_location'] ) ? $kb_config['ml_categories_articles_sidebar_location'] : 'right';
		if ( $kb_config['ml_categories_articles_sidebar_location'] == 'none' ) {
			$kb_config['ml_categories_articles_sidebar_toggle'] = 'off';
			$kb_config['ml_categories_articles_sidebar_location'] = 'right';
		}

		// starting from version 11.30.0 the Main Page is Modular by default (the toggle is 'on' in specs); ensure it is 'off' if the user did not use Modular before the upgrade
		if ( $kb_config['kb_main_page_layout'] != 'Modular' ) {
			$kb_config['modular_main_page_toggle'] = 'off';
		}

		// transfer storing values of Modular config to corresponding refactored settings only if the Modular Main Page Layout is enabled, otherwise the default values will be used from specs
		if ( $kb_config['kb_main_page_layout'] == 'Modular' ) {
			$kb_config['modular_main_page_toggle'] = 'on';

			// do not add Popular Articles to Articles List module after upgrade
			$kb_config['ml_articles_list_column_1'] = 'none';

			// refactor Modular settings for Categories & Articles module to use shared configuration
			$kb_config['section_head_category_icon_size'] = isset( $kb_config['ml_categories_articles_icon_size'] ) ? $kb_config['ml_categories_articles_icon_size'] : $kb_config['section_head_category_icon_size'];
			$kb_config['section_head_category_icon_color'] = isset( $kb_config['ml_categories_articles_icon_color'] ) ? $kb_config['ml_categories_articles_icon_color'] : $kb_config['section_head_category_icon_color'];
			if ( isset( $kb_config['ml_categories_articles_height_mode'] ) ) {
				$kb_config['section_box_height_mode'] = $kb_config['ml_categories_articles_height_mode'] == 'variable' ? 'section_no_height' : 'section_min_height';
			}
			$kb_config['section_body_height'] = isset( $kb_config['ml_categories_articles_fixed_height'] ) ? $kb_config['ml_categories_articles_fixed_height'] : $kb_config['section_body_height'];
			$kb_config['nof_articles_displayed'] = isset( $kb_config['ml_categories_articles_nof_articles_displayed'] ) ? $kb_config['ml_categories_articles_nof_articles_displayed'] : $kb_config['nof_articles_displayed'];
			$kb_config['section_head_font_color'] = isset( $kb_config['ml_categories_articles_top_category_title_color'] ) ? $kb_config['ml_categories_articles_top_category_title_color'] : $kb_config['section_head_font_color'];
			if ( isset( $kb_config['ml_categories_articles_sub_category_color'] ) ) {
				$kb_config['section_category_font_color'] = $kb_config['ml_categories_articles_sub_category_color'];
				$kb_config['section_category_icon_color'] = $kb_config['ml_categories_articles_sub_category_color'];
			}
			if ( isset( $kb_config['ml_categories_articles_article_color'] ) ) {
				$kb_config['article_font_color'] = $kb_config['ml_categories_articles_article_color'];
				$kb_config['article_icon_color'] = $kb_config['ml_categories_articles_article_color'];
			}
			$kb_config['section_head_description_font_color'] = isset( $kb_config['ml_categories_articles_cat_desc_color'] ) ? $kb_config['ml_categories_articles_cat_desc_color'] : $kb_config['section_head_description_font_color'];
			if ( isset( $kb_config['ml_categories_columns'] ) ) {
				switch ( $kb_config['ml_categories_columns'] ) {
					case '2-col': $kb_config['nof_columns'] = 'two-col'; break;
					case '3-col': $kb_config['nof_columns'] = 'three-col'; break;
					case '4-col': $kb_config['nof_columns'] = 'four-col'; break;
					default: break;
				}
			}

			// refactor Modular to Classic and Drill-Down
			if ( isset( $kb_config['ml_categories_articles_layout'] ) && $kb_config['ml_categories_articles_layout'] == 'classic' ) {
				$kb_config['kb_main_page_layout'] = EPKB_Layout::CLASSIC_LAYOUT;

				// fit previous styles in .css file
				$kb_config['section_border_color'] = '#ffffff';

			} else {
				$kb_config['kb_main_page_layout'] = EPKB_Layout::DRILL_DOWN_LAYOUT;

				// fit previous styles in .css file
				if( isset( $kb_config['ml_categories_articles_border_color'] ) ) {
					$kb_config['section_border_color'] = $kb_config['ml_categories_articles_border_color'];
				}
			}

			$kb_config['section_desc_text_on'] = 'on';

			// ensure icons are at the same place after refactoring from Modular to Classic or Drill-Down layout
			$kb_config['section_head_category_icon_location'] = 'top';

			// fit previous styles in .css file
			$kb_config['section_border_width'] = '1';
			$kb_config['section_border_radius'] = '15';
			$kb_config['background_color'] = '';
		}

		// rename settings
		$kb_config['ml_categories_articles_category_title_html_tag'] = isset( $kb_config['ml_categories_articles_title_html_tag'] ) ? $kb_config['ml_categories_articles_title_html_tag'] :
			( isset( $kb_config['ml_categories_articles_category_title_html_tag'] ) ? $kb_config['ml_categories_articles_category_title_html_tag'] : 'h2' );
		$kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] = isset( $kb_config['ml_categories_articles_icon_background_color_toggle'] ) ? $kb_config['ml_categories_articles_icon_background_color_toggle'] :
			( isset( $kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] ) ? $kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] : 'on' );
		$kb_config['ml_categories_articles_top_category_icon_bg_color'] = isset( $kb_config['ml_categories_articles_icon_background_color'] ) ? $kb_config['ml_categories_articles_icon_background_color'] :
			( isset( $kb_config['ml_categories_articles_top_category_icon_bg_color'] ) ? $kb_config['ml_categories_articles_top_category_icon_bg_color'] : '#e9f6ff' );

		// Copy search width to row settings
		$row_number = 5;
		while ( $row_number > 0 ) {
			if ( ! empty( $kb_config['ml_row_' . $row_number . '_module'] ) && $kb_config['ml_row_' . $row_number . '_module'] == 'search' ) {

				if ( $kb_config['width'] == 'epkb-boxed' ) {
					$kb_config['ml_row_' . $row_number . '_desktop_width'] = '1080';
					$kb_config['ml_row_' . $row_number . '_desktop_width_units'] = 'px';
				} else {
					$kb_config['ml_row_' . $row_number . '_desktop_width'] = '100';
					$kb_config['ml_row_' . $row_number . '_desktop_width_units'] = '%';
				}
			}

			$row_number--;
		}

		$plugin_first_version = EPKB_Utilities::get_wp_option( 'epkb_version_first', '' );
		if ( ! empty( $plugin_first_version ) ) {
			$kb_config['first_plugin_version'] = $plugin_first_version;
		}
	}

	private static function upgrade_to_v11_0_1( &$kb_config ) {
		if ( isset( $kb_config['ml_faqs_kb_id'] ) ) {
			$ml_faqs_kb_id = $kb_config['ml_faqs_kb_id'];
			EPKB_Utilities::save_kb_option( $kb_config['id'], EPKB_ML_FAQs::FAQS_KB_ID, $ml_faqs_kb_id );
		}
		if ( isset( $kb_config['ml_faqs_category_ids'] ) ) {
			$faqs_category_ids = explode( ',', $kb_config['ml_faqs_category_ids'] );
			EPKB_Utilities::save_kb_option( $kb_config['id'], EPKB_ML_FAQs::FAQS_CATEGORY_IDS, $faqs_category_ids );
		}
	}

	/**
	 * Function for major updates
	 *
	 * @param $args
	 */
	public function in_plugin_update_message( $args ) {

		$current_version = Echo_Knowledge_Base::$version;
		$new_version = empty( $args['new_version'] ) ? $current_version : $args['new_version'];

		// versions x.y0.z are major releases
		if ( ! preg_match( '/.*\.\d0\..*/', $new_version ) ) {
			return;
		}

		echo '<style> .epkb-update-warning+p { opacity: 0; height: 0;} </style> ';
		echo '<hr style="clear:left"><div class="epkb-update-warning"><span class="dashicons dashicons-info" style="float:left;margin-right: 6px;color: #d63638;"></span>';
		echo '<div class="epkb-update-warning__title">' . esc_html__( 'We highly recommend you back up your site before upgrading. Next, run the update in a staging environment.', 'echo-knowledge-base' ) . '</div>';
		echo '<div class="epkb-update-warning__message">' .	esc_html__( 'After you run the update, clear your browser cache, hosting cache, and caching plugins.', 'echo-knowledge-base' ) . '</div>';
		echo '<div class="epkb-update-warning__message">' .	esc_html__( 'The latest update includes some substantial changes across different areas of the plugin', 'echo-knowledge-base' ) . '</div>';
	}

	/**
	 * Avoid duplicate content on Article Page.
	 * @return void
	 */
	function after_switch_theme() {
		EPKB_Core_Utilities::remove_kb_flag( 'epkb_the_content_fix' );
	}
}