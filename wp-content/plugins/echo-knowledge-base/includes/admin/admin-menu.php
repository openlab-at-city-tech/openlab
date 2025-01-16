<?php

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

	// KB Menu
	add_submenu_page( 'edit.php?post_type=' . $post_type_name, esc_html__( 'Get Started - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Get Started', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_need_help_read', 'admin_eckb_access_frontend_editor_write'] ), 'epkb-kb-need-help', array( new EPKB_Need_Help_Page(), 'display_need_help_page' ) );

	add_submenu_page( 'edit.php?post_type=' . $post_type_name, esc_html__( 'FAQs - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'FAQs', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_faqs_write'] ), 'epkb-faqs', array( new EPKB_FAQs_Page(), 'display_faqs_page') );

	add_submenu_page( 'edit.php?post_type=' . $post_type_name, esc_html__( 'Configuration - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Configuration', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_order_articles_write', 'admin_eckb_access_frontend_editor_write'] ), 'epkb-kb-configuration', array( new EPKB_Config_Page(), 'display_kb_config_page') );

	do_action( 'eckb_add_kb_menu_item', $post_type_name );

	add_submenu_page( 'edit.php?post_type=' . $post_type_name, esc_html__( 'Analytics - Echo Knowledge Base', 'echo-knowledge-base' ), esc_html__( 'Analytics', 'echo-knowledge-base' ),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ), 'epkb-plugin-analytics', array( new EPKB_Analytics_Page(), 'display_plugin_analytics_page' ) );

	add_submenu_page( 'edit.php?post_type=' . $post_type_name, esc_html__( 'Add-ons - Echo Knowledge Base', 'echo-knowledge-base' ), EPKB_Add_Ons_Page::get_menu_item_title(),
		EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ), 'epkb-add-ons', array( new EPKB_Add_Ons_Page(), 'display_add_ons_page') );
}
add_action( 'admin_menu', 'epkb_add_plugin_menus', 10 );

/**
 * Set top level admin submenu page
 */
function epkb_set_top_level_admin_submenu_page(){
	global $submenu, $eckb_kb_id, $title, $plugin_page;

	if ( empty($submenu) || ! is_array($submenu) ) {
		return;
	}

	if ( ! empty( $plugin_page ) && $plugin_page == 'epkb-kb-need-help' ) {
		$title = esc_html__( 'Get Started - Echo Knowledge Base', 'echo-knowledge-base' );
	}

	// Add KB menu that belongs to the post type that is listed in the URL or use default one if none specified
	$post_type_name = empty( $eckb_kb_id ) ? EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) : EPKB_KB_Handler::get_post_type( $eckb_kb_id );

	$find_page = 'edit.php?post_type=' . $post_type_name;
	$find_sub_slug = 'epkb-kb-need-help';

	foreach( $submenu as $page => $items ) {

		if ( empty($items) || ! is_array($items) || $page !== $find_page ) {
			continue;
		}

		// Sort submenu pages
		foreach( $items as $id => $meta ) {

			if ( empty( $meta[2] ) )	{
				continue;
			}

			if ( $meta[2] === $find_sub_slug ) {

				// Set 'Need Help' submenu page to the top level position
				$submenu[$find_page][1] = $meta;
				$submenu[$find_page][1][2] = 'edit.php?post_type=' . $post_type_name . '&page=epkb-kb-need-help';

				// Remove 'Need Help' submenu page from previous position
				unset( $submenu[$find_page][$id] );

				// Sort submenu pages accordingly to their position
				ksort( $submenu[$find_page] );
			}
		}
	}
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

	// If the 'Need Help' submenu page is active, then make sure it has correct source to be displayed as currently active
	if ( $parent_file === 'edit.php?post_type=' . $post_type_name && isset( $_GET['page'] ) && $_GET['page'] === 'epkb-kb-need-help' ) {
		return 'edit.php?post_type=' . $post_type_name . '&page=epkb-kb-need-help';
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
	if ( empty($current_kb_id) ) {
		return;
	}

	// retrieve current KB configuration
	$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
	if ( is_wp_error( $kb_config ) || empty($kb_config) || ! is_array($kb_config) || count($kb_config) < 100 ) {
		$kb_config = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
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
			EPKB_HTML_Admin::admin_header( $kb_config, [], 'header', 'left' );
			return;

		case 'EKB_SCREEN':                              // Add New Article page
			break;

		case 'EKB_SCREEN_page_epkb-kb-need-help':       // Need Help page
		case 'EKB_SCREEN_page_epkb-kb-configuration':   // KB Configuration page
		case 'EKB_SCREEN_page_epkb-add-ons':            // Add-ons page
		case 'EKB_SCREEN_page_epkb-new-features':       // New Features page
		case 'EKB_SCREEN_page_epkb-manage-kb':          // Manage KBs
		case 'EKB_SCREEN_page_epkb-plugin-analytics':   // Analytics page
			return;

		default:
			break;
	}
}
add_action( 'all_admin_notices', 'epkb_add_page_tabs', 99999 );
