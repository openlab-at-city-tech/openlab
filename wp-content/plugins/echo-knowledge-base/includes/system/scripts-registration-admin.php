<?php

/**  Register JS and CSS files  */

/**
 * Output the admin-icon.css file for logged-in users only.
 * This is to show KB Icons in the:
 *  - Top Admin Bar ( KB Icon, Help Dialog Icon )
 *  - Left Admin Sidebar ( Help Dialog Icon )
 * For more details, see admin_icon.scss comments.
 */

// Not required anymore, keep this here for now? Maybe we might reuse this.
/*function epkb_enqueue_admin_icon_resources() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style( 'epkb-admin-icon-style', Echo_Knowledge_Base::$plugin_url . 'css/admin-icon' . $suffix . '.css' );
}
add_action( 'admin_enqueue_scripts','epkb_enqueue_admin_icon_resources' );*/

/**
 * ADMIN-PLUGIN MENU PAGES (Plugin settings, reports, lists etc.)
 */
function epkb_load_admin_plugin_pages_resources() {
	global $pagenow;

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-admin-plugin-pages-rtl', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	}

	wp_enqueue_style( 'wp-color-picker' );

	if ( EPKB_Utilities::get('page', '', false) == 'epkb-plugin-analytics' ) {
		wp_enqueue_script( 'epkb-admin-jquery-chart', Echo_Knowledge_Base::$plugin_url . 'js/lib/chart.min.js', array( 'jquery' ), Echo_Knowledge_Base::$version );
		wp_enqueue_script( 'epkb-admin-analytics-scripts', Echo_Knowledge_Base::$plugin_url . 'js/admin-analytics' . $suffix . '.js',
			array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core'), Echo_Knowledge_Base::$version );
	}
	wp_enqueue_script( 'epkb-admin-plugin-pages-ui', Echo_Knowledge_Base::$plugin_url . 'js/admin-ui' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-admin-plugin-pages-convert', Echo_Knowledge_Base::$plugin_url . 'js/admin-convert' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );

	wp_register_script( 'epkb-admin-plugin-pages-scripts', Echo_Knowledge_Base::$plugin_url . 'js/admin-plugin-pages' . $suffix . '.js',
		array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core','jquery-effects-bounce', 'jquery-ui-sortable'), Echo_Knowledge_Base::$version );
	if ( EPKB_Utilities::is_advanced_search_enabled() ) {
		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
		$epkb_editor_addon_data = apply_filters( 'epkb_editor_addon_data', array(), $kb_config );   // Advanced Search presets
		wp_add_inline_script( 'epkb-admin-plugin-pages-scripts', 'var epkb_editor_addon_data = ' . wp_json_encode( $epkb_editor_addon_data, ENT_QUOTES ) . ';' );
	}
	wp_enqueue_script( 'epkb-admin-plugin-pages-scripts' );

	wp_localize_script( 'epkb-admin-plugin-pages-scripts', 'epkb_vars', array(
		'msg_try_again'                 => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'                => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (151)',
		'not_saved'                 	=> esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (152)',
		'unknown_error'                 => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1783)',
		'reload_try_again'              => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'                   => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'                => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'sending_feedback'              => esc_html__('Sending feedback ...', 'echo-knowledge-base' ),
		'changing_debug'                => esc_html__('Changing debug ...', 'echo-knowledge-base' ),
		'help_text_coming'              => esc_html__('Help text is coming soon.', 'echo-knowledge-base' ),
		'nonce'                         => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
		'msg_reading_posts'             => esc_html__('Reading posts', 'echo-knowledge-base') . '...',
		'msg_confirm_kb'                => esc_html__('Please confirm Knowledge Base to import into.', 'echo-knowledge-base'),
		'msg_confirm_backup'            => esc_html__('Please confirm you backed up your database or understand that import can potentially make undesirable changes.', 'echo-knowledge-base'),
		'msg_empty_post_type'           => esc_html__('Please select post type.', 'echo-knowledge-base'),
		'msg_nothing_to_convert'        => esc_html__('No posts to convert.', 'echo-knowledge-base'),
		'msg_select_article'            => esc_html__('Please select posts to convert.', 'echo-knowledge-base'),
		'msg_articles_converted'        => esc_html__('Articles converted', 'echo-knowledge-base'),
		'msg_converting'                => esc_html__('Converting articles, please wait...', 'echo-knowledge-base'),
		'on_kb_main_page_layout'        => esc_html__( 'First, the selected layout will be saved.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then, the page will reload and you can see the layout change on the KB frontend.', 'echo-knowledge-base' ),
		'on_kb_templates'               => esc_html__( 'First, the KB Base Template will be enabled.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then the page will reload after which you can see the layout change on the KB frontend.', 'echo-knowledge-base' ),
		'on_current_theme_templates'    => esc_html__( 'First, the Current Theme Template will be enabled.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then the page will reload after which you can see the layout change on the KB frontend.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'If you have issues using the Current Theme Template, switch back to the KB Template or contact us for help.', 'echo-knowledge-base' ),
		'on_modular_main_page_toggle'   => esc_html__( 'First, the Modular Main Page settings will be saved.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then, the page will reload and you can see the page structure change on the KB frontend.', 'echo-knowledge-base' ),
		'on_article_search_sync_toggle' => esc_html__( 'First, the current settings will be saved.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then, the page will reload.', 'echo-knowledge-base' ),
		'on_article_search_toggle'      => esc_html__( 'First, the current settings will be saved.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then, the page will reload.', 'echo-knowledge-base' ),
		'on_asea_presets_selection'     => esc_html__( 'First, the current settings will be saved.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then, the page will reload.', 'echo-knowledge-base' ),
		'on_faqs_presets_selection'     => esc_html__( 'First, the current settings will be saved.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then, the page will reload.', 'echo-knowledge-base' ),
		'on_archive_page_v3_toggle'     => esc_html__( 'First, the current settings will be saved.', 'echo-knowledge-base' ) .
			' ' . esc_html__( 'Then, the page will reload.', 'echo-knowledge-base' ),
	));

	// used by WordPress color picker  ( wpColorPicker() )
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n',
		array(
			'clear'            =>   esc_html__( 'Reset', 'echo-knowledge-base' ),
			'clearAriaLabel'   =>   esc_html__( 'Reset color', 'echo-knowledge-base' ),
			'defaultString'    =>   esc_html__( 'Default', 'echo-knowledge-base' ),
			'defaultAriaLabel' =>   esc_html__( 'Select default color', 'echo-knowledge-base' ),
			'pick'             =>   '',
			'defaultLabel'     =>   esc_html__( 'Color value', 'echo-knowledge-base' ),
		));
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );

	// add for Category icon upload
	if ( $pagenow == 'term.php' || $pagenow == 'edit-tags.php' || $pagenow == 'edit.php' ) {
		wp_enqueue_media();
	}

	// add frontend styles for order settings
	$page = EPKB_Utilities::get( 'page' );
	if ( $page == 'epkb-kb-configuration' ) {
		wp_enqueue_style( 'epkb-mp-frontend-basic-layout', Echo_Knowledge_Base::$plugin_url . 'css/mp-frontend-basic-layout' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	}

}

// Old Wizards
function epkb_load_admin_kb_wizards_script() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'epkb-admin-kb-wizard-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-wizard-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-wizard-script', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (1334)',
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved', 'echo-knowledge-base' ) . ' (1335)',
		'unknown_error'         => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1336)',
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'wizard_help_images_path' => Echo_Knowledge_Base::$plugin_url . 'img/',
		'asea_wizard_help_images_path' => class_exists( 'Echo_Advanced_Search' ) && ! empty(Echo_Advanced_Search::$plugin_url) ? Echo_Advanced_Search::$plugin_url . 'img/' : '',
		'elay_wizard_help_images_path' => class_exists( 'Echo_Elegant_Layouts' ) && ! empty(Echo_Elegant_Layouts::$plugin_url) ? Echo_Elegant_Layouts::$plugin_url . 'img/' : '',
		'eprf_wizard_help_images_path' => class_exists( 'Echo_Article_Rating_And_Feedback' ) && ! empty(Echo_Article_Rating_And_Feedback::$plugin_url) ? Echo_Article_Rating_And_Feedback::$plugin_url . 'img/' : ''
	));
}

// Setup Wizard
function epkb_load_admin_kb_setup_wizard_script() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-admin-plugin-pages-rtl', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	}

	wp_enqueue_script( 'epkb-admin-kb-setup-wizard-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-setup-wizard-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-setup-wizard-script', 'epkb_vars', array(
		'msg_try_again'             => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'            => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (1444)',
		'not_saved'                 => esc_html__( 'Error occurred - configuration NOT saved', 'echo-knowledge-base' ) . ' (1445)',
		'unknown_error'             => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1446)',
		'reload_try_again'          => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'input_required'            => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'sending_error_report' 		=> esc_html__( 'Sending, please wait', 'echo-knowledge-base' ),
		'send_report_error' 	    => esc_html__( 'Could not submit the error.', 'echo-knowledge-base' ) . EPKB_Utilities::contact_us_for_support(),
		'setup_wizard_error_title'  => esc_html__( 'Setup Wizard encountered an error.', 'echo-knowledge-base' ),
		'setup_wizard_error_desc'   => esc_html__( 'We have detected an error. Please report the issue so that we can help you resolve it.', 'echo-knowledge-base' ),
		'wizard_help_images_path'   => Echo_Knowledge_Base::$plugin_url . 'img/',
		'need_help_url'             => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID . '&page=epkb-kb-need-help' ),
		'saving_changes'            => esc_html__( 'Saving changes...', 'echo-knowledge-base' ),
		'creating_demo_data'        => esc_html__( 'Creating a Knowledge Base with demo categories and articles. It will be completed shortly.', 'echo-knowledge-base' ),
	));
}

// load style for Admin Article Page
function epkb_load_admin_article_page_styles() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-article-page' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
}

// load resources for Admin AI Help Sidebar
function epkb_load_admin_ai_help_sidebar_resources() {

	if ( EPKB_Core_Utilities::is_kb_flag_set( 'disable_openai' ) ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'epkb-admin-kb-ai-help-sidebar-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-ai-help-sidebar' . $suffix . '.js', array( 'jquery' ), Echo_Knowledge_Base::$version );
	wp_enqueue_style( 'epkb-admin-kb-ai-help-sidebar-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-ai-help-sidebar' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-ai-help-sidebar-script', 'epkb_ai_vars', array(
		'nonce'                         => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
		'msg_empty_input'               => esc_html__( 'Missing input', 'echo-knowledge-base' ),
		'reload_try_again'              => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'msg_try_again'                 => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'                => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (1641)',
		'unknown_error'                 => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1643)',
		'msg_no_key_admin'              => esc_html__( 'You have no API key. Please add it here', 'echo-knowledge-base' ),
		'msg_no_key'                    => esc_html__( 'You have no API key.', 'echo-knowledge-base' ),
		'ai_help_button_title'          => esc_html__( 'AI Help', 'echo-knowledge-base' ),
		'msg_ai_help_loading'           => esc_html__( 'Processing...', 'echo-knowledge-base' ),
		'msg_ai_copied_to_clipboard'    => esc_html__( 'Copied to clipboard', 'echo-knowledge-base' ),
	) );
}

/**************  Frontend Editor  *****************/

/**
 * Load scripts for Frontend Editor
 */
function epkb_load_front_end_editor() {
	global $post;

	$editor_page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();

	// do not load the Editor and thus the strip-down KB Main page if not necessary
	if ( empty($post->post_type ) || // not a page
		defined( 'DOING_AJAX' ) && DOING_AJAX || // return if we get page by ajax
		! empty( $_REQUEST['elementor-preview'] ) || // elementor preview
		! empty( $_REQUEST['et_fb'] ) // if we are on DIVI page
	) {
		return;
	}

	$post_id = is_object( $post ) && ! empty( $post->ID ) ? $post->ID : 0;

	// see this page is actually KB Main Page
	$kb_id = EPKB_Utilities::get_eckb_kb_id( '' );
	$kb_id = epkb_check_kb_main_page( $kb_id, $post_id );

	if ( empty( $kb_id ) ) {
		return;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

	// is this frontend Editor page?
	if ( ! in_array( $editor_page_type, EPKB_Editor_Config_Base::EDITOR_PAGE_TYPES ) ) {
		return;
	}

	// add config from addons
	$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
	if ( empty($kb_config)  || is_wp_error($kb_config) ) {
		return;
	}

	$config_settings = EPKB_Editor_Utilities::get_editor_settings( $editor_page_type );
	if ( empty($config_settings) ) {
		return;
	}

	EPKB_Error_Handler::add_assets();

	wp_enqueue_style( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'css/editor.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );

	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-editor-rtl', Echo_Knowledge_Base::$plugin_url . 'css/editor-rtl.css', array(), Echo_Knowledge_Base::$version );
	}

	// compatibility with Cloudflare Rocket Loader requires 'data-cfasync' attr to exclude the JS from its process and load vital JS first
	// TODO in future: since WP4.1 is available 'script_loader_tag' filter to add 'data-cfasync' attribute to scripts - currently plugin requires min WP4.0
	echo	"<script data-cfasync='false'>
				var epkb_editor_config = " . wp_json_encode( $config_settings, ENT_QUOTES ) . ";
			</script>";
}

/**
 * Add Frontend Editor option in the WordPress admin bar.
 * Fired by `admin_bar_menu` filter.
 * @param WP_Admin_Bar $wp_admin_bar
 */
function epkb_add_admin_bar_button( WP_Admin_Bar $wp_admin_bar ) {

	if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
		return;
	}

	// do not load FE Editor for blocks
	if ( EPKB_Block_Utilities::current_post_has_kb_layout_blocks() ) {
		return;
	}

	// show frontend Editor link on KB Main Page, KB Article Pages
	$title = epkb_front_end_editor_title();
	if ( ! empty( $title ) ) {

		$kb_id = EPKB_KB_Handler::get_current_kb_id();

		// do not show Editor link for Archive Page V3
		if ( is_archive() ) {
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
			if ( $kb_config['archive_page_v3_toggle'] == 'on' ) {
				return;
			}
		}

		$open_editor_hash = EPKB_Editor_Utilities::epkb_front_end_editor_type();
		$open_editor_hash = empty( $open_editor_hash ) ? '' : '____' . $open_editor_hash . '-editor';

		$url = EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ?
			admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-kb-configuration#settings__general__general-full-editor' . $open_editor_hash ) :
			add_query_arg( [ 'action' => 'epkb_load_editor' ] );
		$wp_admin_bar->add_menu( array( 'id' => 'epkb-edit-mode-button', 'title' => $title, 'href' => $url ) );

		return;
	}
}

function epkb_front_end_editor_title() {

	$title = '';
	switch ( EPKB_Editor_Utilities::epkb_front_end_editor_type() ) {
		case 'article-page':
			$title = esc_html__( 'Edit KB Article Page', 'echo-knowledge-base' );
			break;
		case 'main-page':
			$title = esc_html__( 'Edit KB Main Page', 'echo-knowledge-base' );
			break;
		case 'archive-page':
			$title = esc_html__( 'Edit KB Archive Page', 'echo-knowledge-base' );
			break;
		case 'search-page':
			$title = esc_html__( 'Edit KB Search Page', 'echo-knowledge-base' );
			break;
	}

	return $title;
}

function epkb_load_editor_styles() {
	global $post;

	if ( empty($post->post_type) || // not a page
		defined( 'DOING_AJAX' ) && DOING_AJAX || // return if we get page by ajax
		! empty( $_REQUEST['elementor-preview'] ) || // elementor preview
		! empty( $_REQUEST['et_fb'] ) // if we are on DIVI page
	) {
		return;
	}

	$post_id = is_object( $post ) && ! empty( $post->ID ) ? $post->ID : 0;
	$kb_id = EPKB_Utilities::get_eckb_kb_id( '' );

	$kb_id = epkb_check_kb_main_page( $kb_id, $post_id );
	if ( empty( $kb_id ) ) {
		return;
	}

	// is this frontend Editor page?
	$editor_page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();
	if ( ! in_array( $editor_page_type, EPKB_Editor_Config_Base::EDITOR_PAGE_TYPES ) ) {
		return;
	}

	// add config from addons
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
	$kb_config = apply_filters( 'eckb_kb_config', $kb_config );

	if ( empty($kb_config)  || is_wp_error($kb_config) ) {
		return;
	}

	$config_settings = EPKB_Editor_Utilities::get_editor_settings( $editor_page_type );
	if ( empty($config_settings) ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	$epkb_editor_params = array(
		'_wpnonce_epkb_ajax_action' => wp_create_nonce( '_wpnonce_epkb_ajax_action' ),
		'ajaxurl' 						=> admin_url( 'admin-ajax.php', 'relative' ),
		'kb_url' 						=> admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . $kb_id ),
		'epkb_editor_kb_id' 			=> $kb_id,
		'page_type' 					=> $editor_page_type,
		'turned_on'         			=> esc_html__( 'Hide KB Settings', 'echo-knowledge-base' ),
		'loading'           			=> esc_html__( 'Loading...', 'echo-knowledge-base' ),
		'turned_off'        			=> epkb_front_end_editor_title(),
		'default_header'    			=> esc_html__( 'Settings panel', 'echo-knowledge-base' ),
		'epkb_name'         			=> esc_html__( 'Echo Knowledge Base', 'echo-knowledge-base' ),
		'tab_content'       			=> esc_html__( 'Content', 'echo-knowledge-base' ),
		'tab_style'         			=> esc_html__( 'Style', 'echo-knowledge-base' ),
		'tab_features'      			=> esc_html__( 'Features', 'echo-knowledge-base' ),
		'tab_advanced'      			=> esc_html__( 'Advanced', 'echo-knowledge-base' ),
		'tab_global'      				=> esc_html__( 'General Settings', 'echo-knowledge-base' ),
		'tab_hidden'      				=> esc_html__( 'Disabled Sections', 'echo-knowledge-base' ),
		'save_button'       			=> esc_html__( 'Save', 'echo-knowledge-base' ),
		'exit_button'       			=> esc_html__( 'Exit Editor', 'echo-knowledge-base' ),
		'clear_modal_notice' 			=> esc_html__( 'Click on any page element to change its settings', 'echo-knowledge-base' ),
		'no_settings'     				=> esc_html__( 'This zone have no settings yet', 'echo-knowledge-base' ),
		'checkbox_on'    				=> esc_html__( 'Yes', 'echo-knowledge-base' ),
		'checkbox_off'    				=> esc_html__( 'No', 'echo-knowledge-base' ),
		'wrong_dimensions' 				=> esc_html__( 'Invalid dimensions', 'echo-knowledge-base' ),
		'left_panel' 					=> esc_html__( 'Left Panel', 'echo-knowledge-base' ),
		'right_panel' 					=> esc_html__( 'Right Panel', 'echo-knowledge-base' ),
		'edit_button' 					=> esc_html__( 'Edit', 'echo-knowledge-base' ),
		'preopen_zone' 					=> EPKB_Utilities::post( 'preopen_zone', '' ),
		'preopen_setting' 				=> EPKB_Utilities::post( 'preopen_setting', '' ),
		'settings_html' 				=> EPKB_Editor_View::get_editor_settings_html(),
		'menu_links_html'				=> EPKB_Editor_View::get_editor_modal_menu_links( $editor_page_type, $kb_config ),
		'urls_and_slug' 				=> esc_html__( 'URLs and Slug', 'echo-knowledge-base' ),
		'urls_and_slug_url'				=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#kb-url' ),
		'order_categories' 				=> esc_html__( 'Order Categories and Articles', 'echo-knowledge-base' ),
		'order_categories_url'			=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#ordering' ),
		'rename_kb' 					=> esc_html__( 'Rename KB Name', 'echo-knowledge-base' ),
		'rename_kb_url'					=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#settings__general' ),
		'theme_link' 					=> esc_html__( 'Theme Compatibility Mode', 'echo-knowledge-base' ),
		'color_value' 					=> esc_html__( 'Color value', 'echo-knowledge-base' ),
		'select_color' 					=> esc_html__( 'Select Color', 'echo-knowledge-base' ),
		'default' 						=> esc_html__( 'Default', 'echo-knowledge-base' ),
		'inherit' 						=> esc_html__( 'Inherit', 'echo-knowledge-base' ),
		'select_default_color' 			=> esc_html__( 'Select default color', 'echo-knowledge-base' ),
		'clear' 						=> esc_html__( 'Clear', 'echo-knowledge-base' ),
		'clear_color' 					=> esc_html__( 'Clear color', 'echo-knowledge-base' ),
		'sidebar_settings'				=> esc_html__( 'The Sidebar setting can be changed on the article page.', 'echo-knowledge-base' ),
		'navigation' 					=> esc_html__( 'Navigation', 'echo-knowledge-base' ),
		'enabled_list' 					=> esc_html__( 'Enabled Sections', 'echo-knowledge-base' ),
		'enable_disable_sections_link' 	=> esc_html__( 'Disabled Sections', 'echo-knowledge-base' ),
		'all_zones_active' 				=> esc_html__( 'All Sections are enabled', 'echo-knowledge-base' ),
		'edit_zone' 					=> esc_html__( 'Edit Section', 'echo-knowledge-base' ),
		'need_help' 					=> esc_html__( 'Need Help', 'echo-knowledge-base' ),
		'sending_error_report' 			=> esc_html__( 'Sending, please wait', 'echo-knowledge-base' ),
		'send_report_error' 			=> esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (2214) - ' . EPKB_Utilities::contact_us_for_support(),
		'timeout2_error' 				=> EPKB_Error_Handler::timeout2_error(),
		'other_error_found' 			=> EPKB_Error_Handler::other_error_found(),
		'csr_error' 					=> EPKB_Error_Handler::get_csr_error_text(),
		'ns_error_unexpected_error' 	=> EPKB_Error_Handler::get_ns_error_text(),
		'wrong_select' 					=> esc_html__( 'No value to select', 'echo-knowledge-base' ),
		'article_header_rows' 			=> esc_html__( 'Article Header Rows', 'echo-knowledge-base' ),
		'typography_defaults' 			=> EPKB_Typography::$typography_defaults,
		// 'typography_fonts' 				=> EPKB_Typography::$font_data, TODO: is not used - probably remove
		'typography_title' 				=> esc_html__( 'Typograhy', 'echo-knowledge-base' ),
		'typography_font_family' 		=> esc_html__( 'Font Family', 'echo-knowledge-base' ),
		'typography_font_size' 			=> esc_html__( 'Font Size (px)', 'echo-knowledge-base' ),
		'typography_font_weight' 		=> esc_html__( 'Font Weight', 'echo-knowledge-base' ),
		'zone_absent_error'				=> esc_html__( 'Cannot open settings for', 'echo-knowledge-base' ),
		'zone_disabled_error'			=> esc_html__( 'Cannot open settings because the section is disabled. You can turn it on here', 'echo-knowledge-base' ) . ':',
		'zone_disabled_text'			=> esc_html__( 'Settings Zone was disabled. You can enable it back.', 'echo-knowledge-base' ),
		'config_backend_mode_link'  	=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration&action=enable_editor_backend_mode&_wpnonce_epkb_ajax_action=' . wp_create_nonce( '_wpnonce_epkb_ajax_action' ) . '#settings__editor' ),
		'outside_editor_msg'            => esc_html__( 'This area is controlled by your theme.', 'echo-knowledge-base' ),
		'outside_editor_msg_with_link'  => sprintf( '%s <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank">%s</a>',
			__( 'This area is controlled by your theme.', 'echo-knowledge-base' ),
			__( 'Read more about Current Theme Template', 'echo-knowledge-base' ) ),
		'inside_modules_editor_msg'     => esc_html__( 'These settings are available in the admin configurations.', 'echo-knowledge-base' ),
		'archive_page_v3_edit_msg'      => sprintf( '%s <a href="%s" target="_blank">%s</a>',
			__( 'These settings are available in the', 'echo-knowledge-base' ),
			esc_url( admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#settings__archive-page' ) ),
			__( 'Category Archive Page settings', 'echo-knowledge-base' ) ),
	);

	$epkb_editor_params = apply_filters( 'epkb_editor_localize', $epkb_editor_params );
	$epkb_editor_addon_data = apply_filters( 'epkb_editor_addon_data', array(), $kb_config );   // Advanced Search presets

	wp_register_style( 'epkb-js-error-handlers', Echo_Knowledge_Base::$plugin_url . 'css/error-handlers' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-js-error-handlers' ) );

	// Should be before all else scripts
	wp_register_script( 'epkb-js-error-handlers', Echo_Knowledge_Base::$plugin_url . 'js/error-handlers' .  $suffix . '.js', array(), Echo_Knowledge_Base::$version );
	wp_print_scripts( array( 'epkb-js-error-handlers' ) );

	wp_register_style( 'epkb-editor-ui', Echo_Knowledge_Base::$plugin_url . 'css/editor-ui' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-editor-ui' ) );

	epkb_load_public_resources();

	wp_register_style( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'css/editor' . $suffix . '.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-editor' ) );

	if ( is_rtl() ) {
		wp_register_style( 'epkb-editor-rtl', Echo_Knowledge_Base::$plugin_url . 'css/editor-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		wp_print_styles( array( 'epkb-editor-rtl' ) );
	}

	wp_register_script( 'iris', admin_url() . 'js/iris.min.js' );
	wp_print_scripts( array( 'jquery', 'jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-slider', 'jquery-ui-draggable','jquery-touch-punch', 'iris' ) );

	wp_register_script( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'js/editor' . $suffix . '.js', array(), Echo_Knowledge_Base::$version );
	wp_add_inline_script( 'epkb-editor', '
		var epkb_editor_config = ' . wp_json_encode( $config_settings, ENT_QUOTES ) . ';
		var epkb_editor = ' . wp_json_encode( $epkb_editor_params, ENT_QUOTES ) . ';
		var epkb_editor_font_links = ' . wp_json_encode( EPKB_Typography::$google_fonts_10_links, ENT_QUOTES ) . ';
		var epkb_editor_addon_data = ' . wp_json_encode( $epkb_editor_addon_data, ENT_QUOTES ) . ';' );
	wp_print_scripts( array( 'epkb-editor' ) );

	wp_register_script( 'epkb-color-picker', Echo_Knowledge_Base::$plugin_url . 'js/lib/color-picker' . $suffix . '.js', array(), Echo_Knowledge_Base::$version );
	wp_print_scripts( array( 'epkb-color-picker' ) );
}

function epkb_check_kb_main_page( $eckb_kb_id, $post_id ) {

	if ( ! empty( $eckb_kb_id ) ) {
		return $eckb_kb_id;
	}

	$all_kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids();
	foreach ( $all_kb_ids as $kb_id ) {
		$kb_main_pages = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'kb_main_pages' );
		if ( empty( $kb_main_pages ) || ! is_array($kb_main_pages) ) {
			continue;
		}

		if ( isset($kb_main_pages[$post_id]) ) {
			$kb_id = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'id', null );
			if ( empty( $kb_id ) ) {
				return $eckb_kb_id;
			}

			return $kb_id;
		}
	}

	return $eckb_kb_id;
}

/**
 * Use for backend mode iframe
 */
function epkb_load_editor_backend_mode_styles_inline() {

	$kb_id = EPKB_Utilities::get_eckb_kb_id();
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
	$kb_config = apply_filters( 'eckb_kb_config', $kb_config );

	// on the backend iframe we need force register public resources
	epkb_load_public_resources();

	$css_slugs = [
		'cp-frontend-layout',
		'sp-frontend-layout',
		'mp-frontend-basic-layout',
		'mp-frontend-tab-layout',
		'mp-frontend-category-layout',
		'mp-frontend-modular-basic-layout',
		'mp-frontend-modular-tab-layout',
		'mp-frontend-modular-category-layout',
		'mp-frontend-modular-classic-layout',
		'mp-frontend-modular-drill-down-layout',
		'mp-frontend-modular-grid-layout',
		'mp-frontend-modular-sidebar-layout',
		'mp-frontend-grid-layout',
		'mp-frontend-sidebar-layout',
		'ap-frontend-layout',
	];

	// print only slug that was registered earlier - on the backend iframe we need force print them instead of enqueue
	foreach ( $css_slugs as $one_slug ) {
		if ( ! wp_style_is( 'epkb-' . $one_slug, 'registered' ) ) {
			continue;
		}
		wp_add_inline_style( 'epkb-' . $one_slug, epkb_frontend_kb_theme_styles_now( $kb_config, $one_slug ) );
		wp_print_styles('epkb-' .  $one_slug );
		if ( is_rtl() ) {
			wp_print_styles( 'epkb-' . $one_slug . '-rtl' );
		}

		// add user's custom CSS separately to ensure the possibly incorrect CSS cannot affect main inline CSS - render it at the end to give it higher priority
		if ( $kb_config['modular_main_page_custom_css_toggle'] == 'on' ) {
			$custom_inline_css = EPKB_Utilities::get_kb_option($kb_id, 'epkb_ml_custom_css', '');
			if ( ! empty( $custom_inline_css ) ) {
				wp_add_inline_style('epkb-' . $one_slug . '-custom', EPKB_Utilities::minify_css( $custom_inline_css ) );
				wp_print_styles('epkb-' . $one_slug . '-custom' );
			}
		}
	}

	wp_print_scripts( 'epkb-public-scripts' );

	// wp_print_scripts( array( 'jquery', 'epkb-public-scripts' ) );
	wp_register_style( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'css/editor.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-editor' ) );

	if ( is_rtl() ) {
		wp_register_style( 'epkb-editor-rtl', Echo_Knowledge_Base::$plugin_url . 'css/editor-rtl.css', array(), Echo_Knowledge_Base::$version );
		wp_print_styles( array( 'epkb-editor-rtl' ) );
	}

	foreach ( $kb_config as $name => $value ) {
		if ( is_array( $value ) && ! empty( $value['font-family'] ) ) {
			$font_link = EPKB_Typography::get_google_font_link( $value['font-family'] );
			if ( ! empty($font_link) ) {
				wp_register_style( 'epkb-font-' . sanitize_title( $value['font-family']), $font_link );
				wp_print_styles( array( 'epkb-font-' . sanitize_title( $value['font-family']) ) );
			}
		}
	}

	// for addons
	do_action( 'epkb_load_editor_backend_mode_styles_inline' );
}
