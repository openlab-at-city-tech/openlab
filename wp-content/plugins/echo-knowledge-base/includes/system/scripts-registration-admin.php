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
	
	// Enqueue WordPress editor CSS for TinyMCE inline link toolbar
	if ( EPKB_Utilities::get( 'page' ) == 'epkb-kb-configuration' ) {
		wp_enqueue_style( 'epkb-wp-editor-css', includes_url( 'css/editor.min.css' ), array(), get_bloginfo( 'version' ) );
	}

	$page_slug = EPKB_Utilities::get( 'page', '', false );

	// KB Analytics page - separate from Content Analysis
	if ( $page_slug === 'epkb-plugin-analytics' ) {
		// Load analytics admin styles (includes tab styling and all analytics components)
		wp_enqueue_style( 'epkb-admin-analytics-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

		// Load Chart.js for analytics visualizations
		wp_enqueue_script( 'epkb-admin-jquery-chart', Echo_Knowledge_Base::$plugin_url . 'js/lib/chart.min.js', array( 'jquery' ), Echo_Knowledge_Base::$version );

		// Load analytics-specific JavaScript
		wp_enqueue_script( 'epkb-admin-analytics-scripts', Echo_Knowledge_Base::$plugin_url . 'js/admin-analytics' . $suffix . '.js',
			array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core', 'epkb-admin-jquery-chart'), Echo_Knowledge_Base::$version );

		// Localize script for analytics with proper nonce
		wp_localize_script( 'epkb-admin-analytics-scripts', 'epkb_vars', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( '_wpnonce_epkb_ajax_action' ),
		));
	}

	// Content Analysis page - separate from KB Analytics
	if ( $page_slug === 'epkb-content-analysis' ) {
		// Load AI admin page styles for Content Analysis
		wp_enqueue_style( 'epkb-admin-ai-page-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-ai-page' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
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
		'no_kb_main_page_title'         => esc_html__( 'Setup Required', 'echo-knowledge-base' ),
		'no_kb_main_page_msg'           => esc_html__( 'KB Main Page is not set. Please run the Setup Wizard first to create a KB Main Page. Would you like to run the Setup Wizard now?', 'echo-knowledge-base' ),
		'setup_wizard_btn_text'         => esc_html__( 'Run Setup Wizard', 'echo-knowledge-base' ),
		'cancel_text'                   => esc_html__( 'Cancel', 'echo-knowledge-base' ),
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
		wp_enqueue_style( 'epkb-mp-frontend-basic-layout', Echo_Knowledge_Base::$plugin_url . 'css/mp-frontend-modular-basic-layout' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
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

	// add script for AI admin page
	if ( $page == 'epkb-kb-ai-features' ) {
		
		// Load React and WordPress components
		wp_enqueue_script( 'wp-element' );
		wp_enqueue_script( 'wp-components' );
		wp_enqueue_script( 'wp-i18n' );
		wp_enqueue_script( 'wp-api-fetch' );
		wp_enqueue_script( 'wp-data' );
		wp_enqueue_style( 'wp-components' );
		
		// Load AI admin page styles
		$css_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'epkb-admin-ai-page-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-ai-page' . $css_suffix . '.css', array(), Echo_Knowledge_Base::$version );
		
		$ai_suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG && file_exists( Echo_Knowledge_Base::$plugin_dir . 'js/ai/admin-ai-app.js' ) ) ? '' : '.min';
		
		// Register ai-chat-util if not already registered (contains error handling utilities)
		if ( ! wp_script_is( 'epkb-ai-chat-util', 'registered' ) ) {
			wp_register_script( 'epkb-ai-chat-util', Echo_Knowledge_Base::$plugin_url . 'js/ai/ai-chat-util' . $ai_suffix . '.js', array(), Echo_Knowledge_Base::$version );
		}
		
		// Register and enqueue React utilities first
		wp_enqueue_script( 'epkb-admin-ai-util', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-util' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-api-fetch', 'wp-i18n'), Echo_Knowledge_Base::$version );

		// Load React tab components
		wp_enqueue_script( 'epkb-admin-ai-dashboard', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-dashboard' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util'), Echo_Knowledge_Base::$version );
		
		wp_enqueue_script( 'epkb-admin-ai-general-settings', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-general-settings' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util'), Echo_Knowledge_Base::$version );
		
		wp_enqueue_script( 'epkb-admin-ai-chat', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-chat' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-ai-chat-util'), Echo_Knowledge_Base::$version );
		
		wp_enqueue_script( 'epkb-admin-ai-search', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-search' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util'), Echo_Knowledge_Base::$version );
		
		// Sync component (must load before training data files)
		wp_enqueue_script( 'epkb-admin-ai-sync', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-sync' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-ai-chat-util'), Echo_Knowledge_Base::$version );

		// Register marked library for markdown parsing (if not already registered)
		if ( ! wp_script_is( 'epkb-marked', 'registered' ) ) {
			wp_register_script( 'epkb-marked', Echo_Knowledge_Base::$plugin_url . 'js/lib/marked' . $suffix . '.js', array(), Echo_Knowledge_Base::$version );
		}

		// Training data table component (must load before main training data file)
		wp_enqueue_script( 'epkb-admin-ai-training-data-table', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-training-data-table' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-marked'), Echo_Knowledge_Base::$version );

		wp_enqueue_script( 'epkb-admin-ai-training-data', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-training-data' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-admin-ai-training-data-table', 'epkb-admin-ai-sync', 'epkb-ai-chat-util'), Echo_Knowledge_Base::$version );

		// Load Tools tab component
		wp_enqueue_script( 'epkb-admin-ai-tools', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-tools' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util'), Echo_Knowledge_Base::$version );
		
		// Load PRO Features tab component
		wp_enqueue_script( 'epkb-admin-ai-pro-features', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-pro-features' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util'), Echo_Knowledge_Base::$version );
		
		// Load main app last as it depends on all other components
		wp_enqueue_script( 'epkb-admin-ai-app', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-app' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-admin-ai-dashboard', 'epkb-admin-ai-general-settings',
					'epkb-admin-ai-chat', 'epkb-admin-ai-search', 'epkb-admin-ai-training-data', 'epkb-admin-ai-tools', 'epkb-admin-ai-pro-features'),
			Echo_Knowledge_Base::$version );

		// Set JavaScript translations for all AI scripts
		wp_set_script_translations( 'epkb-admin-ai-util', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-dashboard', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-general-settings', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-chat', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-search', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-sync', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-training-data-table', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-training-data', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-tools', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-pro-features', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-app', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );

		// Set up API Fetch middleware and AI presets
		$ai_presets = array();
		$chat_presets = EPKB_OpenAI_Client::get_model_presets( 'chat' );
		$search_presets = EPKB_OpenAI_Client::get_model_presets( 'search' );
		$layout_presets = EPKB_AI_Search_Tab::get_search_results_presets();

		// Convert presets to simpler format for JS (removing label and description)
		foreach ( $chat_presets as $key => $preset ) {
			// Skip custom preset as it doesn't have actual values
			if ( $key === 'custom' ) {
				continue;
			}
			$ai_presets['chat'][$key] = array();
			// Only include parameters that actually exist in the preset
			if ( isset( $preset['model'] ) ) {
				$ai_presets['chat'][$key]['model'] = $preset['model'];
			}
			if ( isset( $preset['verbosity'] ) ) {
				$ai_presets['chat'][$key]['verbosity'] = $preset['verbosity'];
			}
			if ( isset( $preset['reasoning'] ) ) {
				$ai_presets['chat'][$key]['reasoning'] = $preset['reasoning'];
			}
			if ( isset( $preset['temperature'] ) ) {
				$ai_presets['chat'][$key]['temperature'] = $preset['temperature'];
			}
			if ( isset( $preset['max_output_tokens'] ) ) {
				$ai_presets['chat'][$key]['max_output_tokens'] = $preset['max_output_tokens'];
			}
			if ( isset( $preset['top_p'] ) ) {
				$ai_presets['chat'][$key]['top_p'] = $preset['top_p'];
			}
		}

		foreach ( $search_presets as $key => $preset ) {
			// Skip custom preset as it doesn't have actual values
			if ( $key === 'custom' ) {
				continue;
			}
			$ai_presets['search'][$key] = array();
			// Only include parameters that actually exist in the preset
			if ( isset( $preset['model'] ) ) {
				$ai_presets['search'][$key]['model'] = $preset['model'];
			}
			if ( isset( $preset['verbosity'] ) ) {
				$ai_presets['search'][$key]['verbosity'] = $preset['verbosity'];
			}
			if ( isset( $preset['reasoning'] ) ) {
				$ai_presets['search'][$key]['reasoning'] = $preset['reasoning'];
			}
			if ( isset( $preset['temperature'] ) ) {
				$ai_presets['search'][$key]['temperature'] = $preset['temperature'];
			}
			if ( isset( $preset['max_output_tokens'] ) ) {
				$ai_presets['search'][$key]['max_output_tokens'] = $preset['max_output_tokens'];
			}
			if ( isset( $preset['top_p'] ) ) {
				$ai_presets['search'][$key]['top_p'] = $preset['top_p'];
			}
		}

		// Add layout presets for search results
		foreach ( $layout_presets as $key => $preset ) {
			// Skip custom preset
			if ( $key === 'custom' ) {
				continue;
			}
			$ai_presets['search_layout'][$key] = $preset['settings'];
		}

		wp_localize_script( 'epkb-admin-ai-util', 'epkb_ai_api', array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'rest_url' => esc_url_raw( rest_url() ),
			'admin_url' => esc_url_raw( admin_url() ),
			'presets' => $ai_presets,
			'timezone_string' => wp_timezone_string(),
			'gmt_offset' => get_option( 'gmt_offset', 0 )
		) );
		
		// Initialize nonce middleware
		wp_add_inline_script( 'epkb-admin-ai-util', sprintf( 'wp.apiFetch.use( wp.apiFetch.createNonceMiddleware( "%s" ) );', wp_create_nonce( 'wp_rest' ) ), 'after' );
		
		// Localize epkb_vars for AI dashboard voting feature
		wp_localize_script( 'epkb-admin-ai-dashboard', 'epkb_vars', array(
			'nonce'     => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
			'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
		));

	}

	// add script for standalone Content Analysis page
	if ( $page == 'epkb-content-analysis' ) {

		// Load React and WordPress components
		wp_enqueue_script( 'wp-element' );
		wp_enqueue_script( 'wp-components' );
		wp_enqueue_script( 'wp-i18n' );
		wp_enqueue_script( 'wp-api-fetch' );
		wp_enqueue_script( 'wp-data' );
		wp_enqueue_style( 'wp-components' );

		// Load AI admin page styles
		$css_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'epkb-admin-ai-page-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-ai-page' . $css_suffix . '.css', array(), Echo_Knowledge_Base::$version );

		$ai_suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG && file_exists( Echo_Knowledge_Base::$plugin_dir . 'js/ai/admin-ai-app.js' ) ) ? '' : '.min';

		// Register ai-chat-util if not already registered (contains error handling utilities)
		if ( ! wp_script_is( 'epkb-ai-chat-util', 'registered' ) ) {
			wp_register_script( 'epkb-ai-chat-util', Echo_Knowledge_Base::$plugin_url . 'js/ai/ai-chat-util' . $ai_suffix . '.js', array(), Echo_Knowledge_Base::$version );
		}

		// Register and enqueue React utilities first
		wp_enqueue_script( 'epkb-admin-ai-util', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-util' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-api-fetch', 'wp-i18n'), Echo_Knowledge_Base::$version );

		// Register marked library for markdown parsing (if not already registered)
		if ( ! wp_script_is( 'epkb-marked', 'registered' ) ) {
			wp_register_script( 'epkb-marked', Echo_Knowledge_Base::$plugin_url . 'js/lib/marked' . $suffix . '.js', array(), Echo_Knowledge_Base::$version );
		}

		// Content analysis table component (must load before main content analysis file)
		wp_enqueue_script( 'epkb-admin-ai-content-analysis-table', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-content-analysis-table' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util'), Echo_Knowledge_Base::$version );

		// Content analysis sync component (handles batch processing and progress tracking)
		wp_enqueue_script( 'epkb-admin-ai-content-analysis-sync', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-content-analysis-sync' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-ai-chat-util'), Echo_Knowledge_Base::$version );

		// Content analysis details component (displays detailed analysis when Improve button is clicked)
		wp_enqueue_script( 'epkb-admin-ai-content-analysis-details', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-content-analysis-details' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch'), Echo_Knowledge_Base::$version );

		// Load Content Analysis tab component
		wp_enqueue_script( 'epkb-admin-ai-content-analysis', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-content-analysis' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-admin-ai-content-analysis-table', 'epkb-admin-ai-content-analysis-sync', 'epkb-admin-ai-content-analysis-details', 'epkb-marked'), Echo_Knowledge_Base::$version );

		// Load standalone app script for content analysis page
		wp_enqueue_script( 'epkb-admin-ai-content-analysis-app', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-content-analysis-app' . $ai_suffix . '.js',
			array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch', 'epkb-admin-ai-util', 'epkb-admin-ai-content-analysis'),
			Echo_Knowledge_Base::$version );

		// Set JavaScript translations for content analysis scripts
		wp_set_script_translations( 'epkb-admin-ai-content-analysis-table', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-content-analysis-sync', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-content-analysis-details', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-content-analysis', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );
		wp_set_script_translations( 'epkb-admin-ai-content-analysis-app', 'echo-knowledge-base', Echo_Knowledge_Base::$plugin_dir . 'languages' );

		// Set up API Fetch middleware
		wp_localize_script( 'epkb-admin-ai-util', 'epkb_ai_api', array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'rest_url' => esc_url_raw( rest_url() ),
			'admin_url' => esc_url_raw( admin_url() ),
			'timezone_string' => wp_timezone_string(),
			'gmt_offset' => get_option( 'gmt_offset', 0 )
		) );

		// Initialize nonce middleware
		wp_add_inline_script( 'epkb-admin-ai-util', sprintf( 'wp.apiFetch.use( wp.apiFetch.createNonceMiddleware( "%s" ) );', wp_create_nonce( 'wp_rest' ) ), 'after' );
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
		'need_help_url'             => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID . '&page=epkb-dashboard' ),
		'saving_changes'            => esc_html__( 'Saving changes...', 'echo-knowledge-base' ),
		'creating_demo_data'        => esc_html__( 'Creating a Knowledge Base with demo categories and articles. It will be completed shortly.', 'echo-knowledge-base' ),
	));
}

// load style for Admin Article Page
function epkb_load_admin_article_page_styles() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-article-page' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
}

// Admin Help Chat - Temporarily disabled (not released yet)
/* Temporarily disabled - backend help chat not released yet
function epkb_enqueue_admin_help_chat() {
	
	$screen = get_current_screen();
	if ( empty( $screen ) || strpos( $screen->id, 'epkb' ) === false ) {
		return;
	}

	// Only for admin users
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Enqueue React (use WordPress bundled version)
	wp_enqueue_script( 'react' );
	wp_enqueue_script( 'react-dom' );

	// Enqueue admin help chat CSS
	wp_enqueue_style( 'epkb-admin-help-chat-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-help-chat' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

	// Check if admin AI utilities are already loaded (enqueued or registered)
	$ai_suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG && file_exists( Echo_Knowledge_Base::$plugin_dir . 'js/ai/admin-ai-app.js' ) ) ? '' : '.min';
	if ( ! wp_script_is( 'epkb-admin-ai-util', 'registered' ) && ! wp_script_is( 'epkb-admin-ai-util', 'enqueued' ) ) {
		wp_register_script( 'epkb-admin-ai-util', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-ai-util' . $ai_suffix . '.js', array('jquery', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch'), Echo_Knowledge_Base::$version );
	}
	
	// Register ai-chat-util if not already registered (contains error handling utilities)
	if ( ! wp_script_is( 'epkb-ai-chat-util', 'registered' ) ) {
		wp_register_script( 'epkb-ai-chat-util', Echo_Knowledge_Base::$plugin_url . 'js/ai/ai-chat-util' . $ai_suffix . '.js', array(), Echo_Knowledge_Base::$version );
	}
	
	// Enqueue help chat script with proper suffix - WordPress will automatically enqueue dependencies
	wp_enqueue_script( 'epkb-admin-help-chat', Echo_Knowledge_Base::$plugin_url . 'js/ai/admin-help-chat' . $ai_suffix . '.js', array( 'react', 'react-dom', 'epkb-admin-ai-util', 'epkb-ai-chat-util' ), Echo_Knowledge_Base::$version, true );

	// Get AI configuration status (non-identifiable)
	$ai_chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled' );
	$ai_search_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled' );
	$ai_config = array(
		'chat_enabled' => $ai_chat_enabled !== 'off',
		'search_enabled' => $ai_search_enabled !== 'off',
	);

	// Determine endpoint - use local for testing if on localhost
	$endpoint = 'http://kb.local//wp-json/epkb/v1/ai-support/chat';
	
	// Get current user info
	$current_user = wp_get_current_user();
	
	// Localize script with necessary data
	wp_localize_script( 'epkb-admin-help-chat', 'epkbAdminHelp', array(
		'endpoint' => $endpoint,
		'ticket_endpoint' => 'https://www.echoknowledgebase.com/wp-json/epkb/v1/support/ticket',
		'token' => 'epkb_support_2024_token',
		'plugin_version' => Echo_Knowledge_Base::$version,
		'wp_version' => get_bloginfo( 'version' ),
		'ai_config' => $ai_config,
		'nonce' => wp_create_nonce( 'epkb_admin_help' ),
		'user' => array(
			'name' => $current_user->display_name,
			'email' => $current_user->user_email
		),
		'i18n' => array(
			'help_button' => __( 'Need Help?', 'echo-knowledge-base' ),
			'welcome' => __( 'Hi! I can help you with the Knowledge Base plugin. What would you like to know?', 'echo-knowledge-base' ),
			'placeholder' => __( 'Type your question...', 'echo-knowledge-base' ),
			'send' => __( 'Send', 'echo-knowledge-base' ),
			'close' => __( 'Close', 'echo-knowledge-base' ),
			'error' => __( 'Sorry, I couldn\'t get a response. Please reload your page and try again.', 'echo-knowledge-base' ),
			'contact_support' => __( 'contact support', 'echo-knowledge-base' ),
			'if_issue_persists' => __( 'If the issue persists, please', 'echo-knowledge-base' ),
			'typing' => __( 'Typing...', 'echo-knowledge-base' ),
			'need_human' => __( 'Need a human?', 'echo-knowledge-base' ),
			'submit_ticket' => __( 'Submit Support Ticket', 'echo-knowledge-base' ),
			'your_name' => __( 'Your Name', 'echo-knowledge-base' ),
			'your_email' => __( 'Your Email', 'echo-knowledge-base' ),
			'ticket_intro' => __( 'We\'ll create a support ticket for you and our team will review your conversation.', 'echo-knowledge-base' ),
			'cancel' => __( 'Cancel', 'echo-knowledge-base' ),
			'submit' => __( 'Submit Ticket', 'echo-knowledge-base' ),
			'submitting' => __( 'Submitting...', 'echo-knowledge-base' ),
			'ticket_submitted' => __( 'Your support ticket has been submitted successfully! Our team will get back to you soon.', 'echo-knowledge-base' ),
			'ticket_error' => __( 'Failed to submit ticket. Please try again or contact support directly.', 'echo-knowledge-base' ),
			'fill_required' => __( 'Please fill in all required fields.', 'echo-knowledge-base' ),
			'new_conversation' => __( 'New Conversation', 'echo-knowledge-base' )
		)
	));
}
add_action( 'admin_enqueue_scripts', 'epkb_enqueue_admin_help_chat' );
*/
