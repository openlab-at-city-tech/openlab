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

	wp_register_script( 'epkb-admin-form-controls-scripts', Echo_Knowledge_Base::$plugin_url . 'js/admin-form-controls' . $suffix . '.js',
		array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core','jquery-effects-bounce', 'jquery-ui-sortable'), Echo_Knowledge_Base::$version );
	wp_register_script( 'epkb-admin-plugin-pages-scripts', Echo_Knowledge_Base::$plugin_url . 'js/admin-plugin-pages' . $suffix . '.js',
		array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core','jquery-effects-bounce', 'jquery-ui-sortable', 'wp-color-picker'), Echo_Knowledge_Base::$version );

	if ( EPKB_Utilities::is_advanced_search_enabled() ) {
		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
		$epkb_editor_addon_data = apply_filters( 'epkb_editor_addon_data', array(), $kb_config );   // Advanced Search presets
		wp_add_inline_script( 'epkb-admin-plugin-pages-scripts', 'var epkb_editor_addon_data = ' . wp_json_encode( $epkb_editor_addon_data, ENT_QUOTES ) . ';' );
	}
	wp_enqueue_script( 'epkb-admin-form-controls-scripts' );
	wp_enqueue_script( 'epkb-admin-plugin-pages-scripts' );

	wp_localize_script( 'epkb-admin-plugin-pages-scripts', 'epkb_vars', array(
		'msg_try_again'                 => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'                => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (151)',
		'not_saved'                 	=> esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (152)',
		'unknown_error'                 => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1783)',
		'reload_try_again'              => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'                   => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'                => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'sending_feedback'              => esc_html__('Sending feedback', 'echo-knowledge-base' ) . '...',
		'changing_debug'                => esc_html__('Changing debug', 'echo-knowledge-base' ) . '...',
		'help_text_coming'              => esc_html__('Help text is coming soon.', 'echo-knowledge-base' ),
		'nonce'                         => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
		'msg_reading_posts'             => esc_html__('Reading items', 'echo-knowledge-base') . '...',
		'msg_confirm_kb'                => esc_html__('Please confirm Knowledge Base to import into.', 'echo-knowledge-base'),
		'msg_confirm_backup'            => esc_html__('Please confirm you backed up your database or understand that import can potentially make undesirable changes.', 'echo-knowledge-base'),
		'msg_empty_post_type'           => esc_html__('Please select post type.', 'echo-knowledge-base'),
		'msg_nothing_to_convert'        => esc_html__('No posts to convert.', 'echo-knowledge-base'),
		'msg_select_article'            => esc_html__('Please select posts to convert.', 'echo-knowledge-base'),
		'msg_articles_converted'        => esc_html__('Items converted', 'echo-knowledge-base'),
		'msg_converting'                => esc_html__('Converting, please wait', 'echo-knowledge-base') . '...',
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
		'preview_not_available'			=> esc_html__( 'Preview functionality will be implemented soon.', 'echo-knowledge-base' ),
		'msg_empty_input'               => esc_html__( 'Missing input', 'echo-knowledge-base' ),
		'msg_no_key_admin'              => esc_html__( 'You have no API key. Please add it here', 'echo-knowledge-base' ),
		'msg_no_key'                    => esc_html__( 'You have no API key.', 'echo-knowledge-base' ),
		'ai_help_button_title'          => esc_html__( 'AI Help', 'echo-knowledge-base' ),
		'msg_ai_help_loading'           => esc_html__( 'Processing...', 'echo-knowledge-base' ),
		'msg_ai_copied_to_clipboard'    => esc_html__( 'Copied to clipboard', 'echo-knowledge-base' ),
		'copied_text'					=> esc_html__( 'Copied!', 'echo-knowledge-base' ),
		'group_selected_singular'		=> esc_html__( 'group selected', 'echo-knowledge-base' ),
		'group_selected_plural'			=> esc_html__( 'groups selected', 'echo-knowledge-base' ),
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

	$page = EPKB_Utilities::get( 'page' );
	if ( $page == 'epkb-faqs' ) {
		wp_register_style( 'epkb-icon-fonts', Echo_Knowledge_Base::$plugin_url . 'css/epkb-icon-fonts' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		wp_register_style( 'epkb-shortcodes', Echo_Knowledge_Base::$plugin_url . 'css/shortcodes' . $suffix . '.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );
		wp_register_script( 'epkb-faq-shortcode-scripts', Echo_Knowledge_Base::$plugin_url . 'js/faq-shortcode-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
		wp_enqueue_script( 'epkb-faq-shortcode-scripts' );
		wp_enqueue_script( 'epkb-icon-fonts' );
		wp_enqueue_style( 'epkb-shortcodes' );
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
		'asea_wizard_help_images_path' => EPKB_Core_Utilities::get_asea_plugin_url(),
		'elay_wizard_help_images_path' => EPKB_Core_Utilities::get_elay_plugin_url(),
		'eprf_wizard_help_images_path' => EPKB_Core_Utilities::get_eprf_plugin_url()
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

	if ( ! EPKB_Core_Utilities::is_kb_flag_set( 'enable_legacy_open_ai' ) ) {
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
