<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Setup WordPress menu for this plugin
 */

/**
 *  Register plugin menus
 */
function epkb_add_plugin_menus() {
	global $eckb_kb_id;

	// Add KB menu that belongs to the post type that is listed in the URL or use default one if none specified
	$post_type_name = empty( $eckb_kb_id ) ? EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) : EPKB_KB_Handler::get_post_type( $eckb_kb_id );
	$parent_slug   = 'edit.php?post_type=' . $post_type_name;

	// KB Menu
	add_submenu_page( $parent_slug, esc_html__( 'Dashboard - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Dashboard', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_editor_capability( $eckb_kb_id ), 'epkb-dashboard', array( new EPKB_Dashboard_Page(), 'display_dashboard_page' ) );

	// Setup Guide page - right after Dashboard
	add_submenu_page( $parent_slug, esc_html__( 'Setup Guide - Echo Knowledge Base', 'echo-knowledge-base' ),
		'<span style="color: #FFD700; font-weight: bold;">' . esc_html__( 'Setup Guide', 'echo-knowledge-base' ) . '</span>',
		EPKB_Admin_UI_Access::get_editor_capability( $eckb_kb_id ), 'epkb-help-resources', array( new EPKB_Help_Resources_Page(), 'display_page' ) );

	add_submenu_page( $parent_slug, esc_html__( 'FAQs - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'FAQs', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_faqs_write'] ), 'epkb-faqs', array( new EPKB_FAQs_Page(), 'display_faqs_page') );

	do_action( 'eckb_add_kb_submenu', $parent_slug );

	add_submenu_page( $parent_slug, esc_html__( 'Configuration - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Configuration', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_order_articles_write', 'admin_eckb_access_frontend_editor_write'] ), 'epkb-kb-configuration', array( new EPKB_Config_Page(), 'display_kb_config_page') );

	add_submenu_page( $parent_slug, esc_html__( 'AI', 'echo-knowledge-base' ), esc_html__( 'AI', 'echo-knowledge-base' ),
		'manage_options', 'epkb-kb-ai-features', array( new EPKB_AI_Admin_Page(), 'display_page' ) );

	do_action( 'eckb_add_kb_menu_item', $post_type_name );

	add_submenu_page( $parent_slug, esc_html__( 'Analytics - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Analytics', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ), 'epkb-plugin-analytics', array( new EPKB_Analytics_Page(), 'display_plugin_analytics_page' ) );

	// Add-ons page is hidden from menu but accessible via Tools > Add-ons sub-tab
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'epkb-add-ons' ) {
		add_submenu_page( $parent_slug, esc_html__( 'Add-ons - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Add-ons', 'echo-knowledge-base' ),
			EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ), 'epkb-add-ons', array( new EPKB_Add_Ons_Page(), 'display_add_ons_page') );
	}
}
add_action( 'admin_menu', 'epkb_add_plugin_menus', 10 );

/**
 * Set top level admin submenu page
 */
function epkb_set_top_level_admin_submenu_page(){
	global $submenu, $eckb_kb_id, $title, $plugin_page;

	if ( empty( $submenu ) || ! is_array( $submenu ) ) {
		return;
	}

	if ( ! empty( $plugin_page ) && $plugin_page == 'epkb-dashboard' ) {
		$title = esc_html__( 'Dashboard - Echo Knowledge Base', 'echo-knowledge-base' );
	}

	// Add KB menu that belongs to the post type that is listed in the URL or use default one if none specified
	$post_type_name = empty( $eckb_kb_id ) ? EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) : EPKB_KB_Handler::get_post_type( $eckb_kb_id );

	$find_page = 'edit.php?post_type=' . $post_type_name;

	if ( empty( $submenu[$find_page] ) || ! is_array( $submenu[$find_page] ) ) {
		return;
	}

	$dashboard_item = null;
	$dashboard_id = null;
	$help_item = null;
	$help_id = null;

	// Find Dashboard and Help menu items
	foreach ( $submenu[$find_page] as $id => $meta ) {
		if ( empty( $meta[2] ) ) {
			continue;
		}
		if ( $meta[2] === 'epkb-dashboard' ) {
			$dashboard_item = $meta;
			$dashboard_id = $id;
		}
		if ( $meta[2] === 'epkb-help-resources' ) {
			$help_item = $meta;
			$help_id = $id;
		}
	}

	// Remove items from original positions first to avoid collisions when original position matches target position
	if ( $dashboard_item !== null && $dashboard_id !== null ) {
		unset( $submenu[$find_page][$dashboard_id] );
	}
	if ( $help_item !== null && $help_id !== null ) {
		unset( $submenu[$find_page][$help_id] );
	}

	// Place items at desired positions
	if ( $dashboard_item !== null ) {
		$submenu[$find_page][1] = $dashboard_item;
		$submenu[$find_page][1][2] = 'edit.php?post_type=' . $post_type_name . '&page=epkb-dashboard';
	}
	if ( $help_item !== null ) {
		$submenu[$find_page][2] = $help_item;
	}

	// Sort submenu pages accordingly to their position
	ksort( $submenu[$find_page] );
}
add_action( 'admin_menu', 'epkb_set_top_level_admin_submenu_page', 11 );

/**
 * Set correct active top level submenu page
 *
 * @param $submenu_file
 * @param $parent_file
 * @return mixed|string
 */
function epkb_set_active_top_level_admin_submenu_page( $submenu_file, $parent_file ) {
	global $eckb_kb_id;

	// Add KB menu that belongs to the post type that is listed in the URL or use default one if none specified
	$post_type_name = empty( $eckb_kb_id ) ? EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) : EPKB_KB_Handler::get_post_type( $eckb_kb_id );

	// If the 'Dashboard' submenu page is active, then make sure it has correct source to be displayed as currently active
	if ( $parent_file === 'edit.php?post_type=' . $post_type_name && isset( $_GET['page'] ) && $_GET['page'] === 'epkb-dashboard' ) {
		return 'edit.php?post_type=' . $post_type_name . '&page=epkb-dashboard';
	}
	return $submenu_file;
}
add_filter( 'submenu_file', 'epkb_set_active_top_level_admin_submenu_page', 10, 2 );

/**
 * Display tabs representing existing knowledge bases at the top of each KB admin page
 */
function epkb_add_page_tabs() {

	global $current_screen;

	// first determine if this page belongs to Knowledge Base and return if it does not
	$current_kb_id = EPKB_KB_Handler::get_current_kb_id();
	if ( empty( $current_kb_id ) ) {
		return;
	}

	// retrieve current KB configuration
	$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
	if ( is_wp_error( $kb_config ) || empty($kb_config) || ! is_array($kb_config) || count($kb_config) < 100 ) {
		$kb_config = EPKB_KB_Config_Specs::get_default_kb_config();
	}

	// determine tab label e.g. 'Templates For:'
	$screen_id = isset( $current_screen->id ) ? $current_screen->id : '';
	$screen_id = str_replace( EPKB_KB_Handler::get_post_type( $current_kb_id ), 'EKB_SCREEN', $screen_id );

	// if add-on is not using tabs then exit
	$no_kb_tabs = apply_filters( 'eckb_hide_kb_tabs', $screen_id );
	if ( isset($no_kb_tabs) && $no_kb_tabs == 'no_kb_tabs' ) {
		return;
	}

	switch ( $screen_id ) {

		case 'edit-EKB_SCREEN':                         // All Articles page
		case 'edit-EKB_SCREEN_tag':                     // Tags page
		case 'edit-EKB_SCREEN_category':                // Categories page
			EPKB_HTML_Admin::admin_header( $kb_config, [], 'header' );
			return;

		case 'EKB_SCREEN':                              // Add New Article page
			break;

		case 'EKB_SCREEN_page_epkb-dashboard':          // Dashboard page
		case 'EKB_SCREEN_page_epkb-faqs':               // FAQs page
		case 'EKB_SCREEN_page_epkb-quizzes':            // Quizzes page
		case 'EKB_SCREEN_page_epkb-glossary':           // Glossary page
		case 'EKB_SCREEN_page_epkb-kb-configuration':   // KB Configuration page
		case 'EKB_SCREEN_page_epkb-kb-ai-features':     // AI page
		case 'EKB_SCREEN_page_epkb-plugin-analytics':   // Analytics page
		case 'EKB_SCREEN_page_epkb-add-ons':            // Add-ons page
		case 'EKB_SCREEN_page_epkb-new-features':       // New Features page
		case 'EKB_SCREEN_page_epkb-manage-kb':          // Manage KBs
			return;

		default:
			break;
	}
}
add_action( 'all_admin_notices', 'epkb_add_page_tabs', 99999 );
