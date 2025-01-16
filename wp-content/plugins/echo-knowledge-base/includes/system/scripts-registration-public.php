<?php

/**  Register JS and CSS files  */

/**
 * FRONT-END pages using our plugin features
 */
function epkb_load_public_resources() {
    global $post;

	$eckb_kb_id = EPKB_Utilities::get_eckb_kb_id( '' );

	/**
	 * ALL PAGES
	 */
    // always register KB resources for possible add-ons usage or KB shortcodes outside KB pages - enqueue only if needed
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'epkb-icon-fonts', Echo_Knowledge_Base::$plugin_url . 'css/epkb-icon-fonts' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_register_style( 'epkb-shortcodes', Echo_Knowledge_Base::$plugin_url . 'css/shortcodes' . $suffix . '.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );
	wp_register_style( 'epkb-frontend-visual-helper', Echo_Knowledge_Base::$plugin_url . 'css/frontend-visual-helper' . $suffix . '.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );
	wp_register_script( 'epkb-public-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_register_script( 'epkb-faq-shortcode-scripts', Echo_Knowledge_Base::$plugin_url . 'js/faq-shortcode-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_register_script( 'epkb-frontend-visual-helper', Echo_Knowledge_Base::$plugin_url . 'js/frontend-visual-helper' . $suffix . '.js', array('jquery', 'epkb-public-scripts'), Echo_Knowledge_Base::$version );

	$epkb_vars = array(
		'ajaxurl'                       => admin_url( 'admin-ajax.php', 'relative' ),
		'msg_try_again'                 => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'                => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (1936)',
		'not_saved'                     => esc_html__( 'Error occurred', 'echo-knowledge-base' ). ' (2456)',
		'unknown_error'                 => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1247)',
		'reload_try_again'              => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'                   => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'                => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'nonce'                         => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
		'toc_editor_msg'                => esc_html__( 'The TOC is not displayed because there are no matching headers in the article.', 'echo-knowledge-base' ),
		'toc_aria_label'                => esc_html__( 'Article outline', 'echo-knowledge-base' ),
		'creating_demo_data'            => esc_html__( 'Creating a Knowledge Base with demo categories and articles. It will be completed shortly.', 'echo-knowledge-base' )
	);

	// add article views counter method only for KB article pages
	if ( ! empty( $eckb_kb_id ) && ! empty( $post ) && EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );
		if ( $kb_config['article_views_counter_enable'] == 'on' && ! EPKB_Article_Count_Handler::is_article_recently_viewed( $post->ID ) ) {
			$epkb_vars['article_views_counter_method'] = $kb_config['article_views_counter_method'];
		}
	}

	wp_localize_script( 'epkb-public-scripts', 'epkb_vars', $epkb_vars );

    // load public resources only if this is: KB Main Page, Article Page, or Category Archive page
    if ( empty( $eckb_kb_id ) ) {
        return;
    }

	/**
	 * KB PAGES
	 */

	$has_vital_css_flag = false;

	// CASE: KB Category Archive page
	$current_css_file_slug = '';
	if ( is_archive() ) {
		$current_css_file_slug = 'cp-frontend-layout';

		if ( EPKB_KB_Handler::is_kb_tag_taxonomy( $GLOBALS['taxonomy'] ) ) {
			$current_css_file_slug = 'tp-frontend-layout';
        }

	// CASE: KB Main Page
	} else if ( EPKB_Utilities::is_kb_main_page() ) {

		$search_query_param = '';
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );

		// Search Page
		if ( EPKB_Utilities::is_advanced_search_enabled() ) {
			$search_query_param = apply_filters( 'eckb_search_query_param', '', $eckb_kb_id );
		}

		if ( isset( $_GET[$search_query_param] ) ) {
			$current_css_file_slug = 'sp-frontend-layout';
		} else {
			$is_modular_main_page = $kb_config['modular_main_page_toggle'] == 'on';
			$modular_css_file_slug = $is_modular_main_page ? '-modular' : '';
			switch ( $kb_config['kb_main_page_layout'] ) {
				case 'Tabs': $current_css_file_slug = 'mp-frontend' . $modular_css_file_slug . '-tab-layout'; break;
				case 'Categories': $current_css_file_slug = 'mp-frontend' . $modular_css_file_slug . '-category-layout'; break;
				case 'Grid': $current_css_file_slug = EPKB_Utilities::is_elegant_layouts_enabled() ? 'mp-frontend' . $modular_css_file_slug . '-grid-layout' : 'mp-frontend' . $modular_css_file_slug . '-basic-layout'; break;
				case 'Sidebar': $current_css_file_slug = EPKB_Utilities::is_elegant_layouts_enabled() ? 'mp-frontend' . $modular_css_file_slug . '-sidebar-layout' : 'mp-frontend' . $modular_css_file_slug . '-basic-layout'; break;
				case 'Classic': $current_css_file_slug = $is_modular_main_page ? 'mp-frontend-modular-classic-layout' : 'mp-frontend-basic-layout'; break;
				case 'Drill-Down': $current_css_file_slug = $is_modular_main_page ? 'mp-frontend-modular-drill-down-layout' : 'mp-frontend-basic-layout'; break;
				case 'Basic':
				default: $current_css_file_slug = 'mp-frontend' . $modular_css_file_slug . '-basic-layout'; break;
			}
		}

		// add user's custom CSS separately to ensure the possibly incorrect CSS cannot affect main inline CSS - render it at the end to give it higher priority
		if ( $kb_config['modular_main_page_custom_css_toggle'] == 'on' ) {
			$custom_inline_css = EPKB_Utilities::get_kb_option( $eckb_kb_id, 'epkb_ml_custom_css', '' );
			if ( ! empty( $custom_inline_css ) ) {
				wp_register_style( 'epkb-' . $current_css_file_slug . '-custom', false );
			}
		}

	// CASE: KB Article
	} else if ( ! empty( $post ) && EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
		$current_css_file_slug = 'ap-frontend-layout';
		$has_vital_css_flag = true;
	}

	if ( ! empty( $current_css_file_slug ) ) {
		if ( $has_vital_css_flag ) {
			wp_register_style( 'epkb-' . $current_css_file_slug . '-vital', Echo_Knowledge_Base::$plugin_url . 'css/' . $current_css_file_slug . '-vital' . $suffix . '.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );
			wp_register_style( 'epkb-' . $current_css_file_slug, Echo_Knowledge_Base::$plugin_url . 'css/' . $current_css_file_slug . $suffix . '.css', array( 'epkb-' . $current_css_file_slug . '-vital' ), Echo_Knowledge_Base::$version );
		} else {
			wp_register_style( 'epkb-' . $current_css_file_slug, Echo_Knowledge_Base::$plugin_url . 'css/' . $current_css_file_slug . $suffix . '.css', array( 'epkb-icon-fonts' ), Echo_Knowledge_Base::$version );
		}
		if ( is_rtl() ) {
			wp_register_style( 'epkb-' . $current_css_file_slug . '-rtl', Echo_Knowledge_Base::$plugin_url . 'css/' . $current_css_file_slug . '-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		}
	}

	// if user has enough capability, then add Frontend Editor button to the admin panel but only for KB Main Page, KB Article Pages and Category Archive page that has at least one article
	add_action( 'admin_bar_menu', 'epkb_add_admin_bar_button', 1000 );

	epkb_enqueue_public_resources();
}
add_action( 'wp_enqueue_scripts', 'epkb_load_public_resources', 500 );

/**
 * Queue for FRONT-END pages using our plugin features
 * @noinspection PhpUnusedParameterInspection
 * @param int $kb_id - legacy
 */
function epkb_enqueue_public_resources( $kb_id=0 ) {

	// KB blocks handle their styles and scripts themselves
	if ( EPKB_Block_Utilities::current_post_has_kb_layout_blocks() ) {
		return;
	}

	$eckb_kb_id = EPKB_Utilities::get_eckb_kb_id( '' );
	$kb_id = empty( $eckb_kb_id ) ? EPKB_KB_Handler::get_kb_id_from_kb_main_page() : $eckb_kb_id;
	if ( empty( $kb_id ) ) {
		return;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
	$kb_config = apply_filters( 'eckb_kb_config', $kb_config );

	$css_slugs = [
		'cp-frontend-layout',
		'tp-frontend-layout',
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

	// enqueue once only slug that was registered earlier
	foreach ( $css_slugs as $one_slug ) {
		if ( ! wp_style_is( 'epkb-' . $one_slug, 'registered' ) || wp_style_is( 'epkb-' . $one_slug ) ) {
			continue;
		}

		wp_add_inline_style( 'epkb-' . $one_slug, epkb_frontend_kb_theme_styles_now( $kb_config, $one_slug ) );
		wp_enqueue_style('epkb-' .  $one_slug );
		if ( is_rtl() ) {
			wp_enqueue_style( 'epkb-' . $one_slug . '-rtl' );
		}

		// add user's custom CSS separately to ensure the possibly incorrect CSS cannot affect main inline CSS - render it at the end to give it higher priority
		if ( $kb_config['modular_main_page_custom_css_toggle'] == 'on' ) {
			$custom_inline_css = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_ml_custom_css', '' );
			if ( ! empty( $custom_inline_css ) ) {
				wp_add_inline_style('epkb-' . $one_slug . '-custom', EPKB_Utilities::minify_css( $custom_inline_css ) );
				wp_enqueue_style( 'epkb-' . $one_slug . '-custom' );
			}
		}
	}

	wp_enqueue_script( 'epkb-public-scripts' );

	epkb_enqueue_google_fonts();
	epkb_enqueue_the_content_scripts();
}
add_action( 'epkb_enqueue_scripts', 'epkb_enqueue_public_resources' ); // use this action in any place to add scripts $kb_id as a parameter

/**
 * Certain styles need to be inserted in the header.
 *
 * @param $kb_config
 * @param $css_file_slug
 * @return string
 */
function epkb_frontend_kb_theme_styles_now( $kb_config, $css_file_slug ) {

	$is_kb_main_page = in_array( $css_file_slug, [
		'mp-frontend-basic-layout',
		'mp-frontend-tab-layout',
		'mp-frontend-category-layout',
		'mp-frontend-grid-layout',
		'mp-frontend-sidebar-layout',
		'mp-frontend-modular-basic-layout',
		'mp-frontend-modular-tab-layout',
		'mp-frontend-modular-category-layout',
		'mp-frontend-modular-classic-layout',
		'mp-frontend-modular-drill-down-layout',
		'mp-frontend-modular-grid-layout',
		'mp-frontend-modular-sidebar-layout', ] );

	// get any style from add-ons
	$add_on_output = apply_filters( 'eckb_frontend_kb_theme_style', '', $kb_config['id'], $is_kb_main_page );
	if ( empty( $add_on_output ) || ! is_string( $add_on_output ) )  {
		$add_on_output = '';
	}

	$output = '';

	// Basic Layout --------------------------------------------------/
	if ( in_array( $css_file_slug, [ 'mp-frontend-basic-layout', 'mp-frontend-tab-layout', 'mp-frontend-category-layout' ] ) ) {

		// General -------------------------------------------/
		$output .= ' 
                #epkb-main-page-container, 
				#epkb-main-page-container .epkb-doc-search-container__title, 
				#epkb-main-page-container #epkb-search-kb, 
				#epkb-main-page-container #epkb_search_terms, 
				#epkb-main-page-container .epkb-cat-name, 
				#epkb-main-page-container .epkb-cat-desc, 
				#epkb-main-page-container .eckb-article-title, 
				#epkb-main-page-container .epkb-category-level-2-3__cat-name,
				#epkb-main-page-container .epkb-articles-coming-soon,
				#epkb-main-page-container .epkb-show-all-articles { 
				    	font-family: ' . ( ! empty( $kb_config['general_typography']['font-family'] ) ? $kb_config['general_typography']['font-family'] .'!important' : 'inherit !important' ) . ';
				}';
		// Headings  -----------------------------------------/
		$output .= '
			#epkb-main-page-container .epkb-cat-name { 
				font-size: ' . ( ! empty( $kb_config['section_head_typography']['font-size'] ) ? $kb_config['section_head_typography']['font-size'] . 'px !important' : 'inherit !important' ) . ';
				font-weight: ' . ( ! empty( $kb_config['section_head_typography']['font-weight'] ) ? $kb_config['section_head_typography']['font-weight'] : 'inherit !important' ) . ';
			}';
		$output .= '
			#epkb-main-page-container .epkb-cat-desc { 
				font-size: ' . ( ! empty( $kb_config['section_head_description_typography']['font-size'] ) ? $kb_config['section_head_description_typography']['font-size'] . 'px !important' : 'inherit !important' ) . ';
				font-weight: ' . ( ! empty( $kb_config['section_head_description_typography']['font-weight'] ) ? $kb_config['section_head_description_typography']['font-weight'] : 'inherit !important' ) . ';
			}
			#epkb-main-page-container .epkb-category-level-2-3,
			#epkb-main-page-container .epkb-category-level-2-3__cat-name {
		        font-size: ' . ( empty( $kb_config['section_typography']['font-size'] ) ? 'inherit;' : $kb_config['section_typography']['font-size'] . 'px!important;' ) . '
	            font-weight: ' . ( ! empty( $kb_config['section_typography']['font-weight'] ) ? $kb_config['section_typography']['font-weight'] : 'inherit !important' ) . ';
			}';

		// Articles  -----------------------------------------/
		$output .= '
			#epkb-main-page-container .epkb-section-body .eckb-article-title { 
				font-size: ' . ( ! empty( $kb_config['article_typography']['font-size'] ) ? $kb_config['article_typography']['font-size'] . 'px !important' : 'inherit !important' ) . ';
				font-weight: ' . ( ! empty( $kb_config['article_typography']['font-weight'] ) ? $kb_config['article_typography']['font-weight'] : 'inherit !important' ) . ';
			}';

		$output .= '
			#epkb-main-page-container .epkb-articles-coming-soon, 
			#epkb-main-page-container .epkb-show-all-articles { 
				font-size: ' . ( ! empty( $kb_config['section_typography']['font-size'] ) ? $kb_config['section_typography']['font-size'] . 'px !important' : 'inherit !important' ) . ';
				font-weight: ' . ( ! empty( $kb_config['section_typography']['font-weight'] ) ? $kb_config['section_typography']['font-weight'] : 'inherit !important' ) . ';
			} ';
	}

	// Tab Layout ----------------------------------------------------/
	if ( $css_file_slug == 'mp-frontend-tab-layout' ) {
		$output .= '
		#epkb-main-page-container .epkb-nav-tabs .epkb-cat-name { 
				font-size: ' . ( ! empty( $kb_config['tab_typography']['font-size'] ) ? $kb_config['tab_typography']['font-size'] . 'px !important' : 'inherit !important' ) . ';
				font-weight: ' . ( ! empty( $kb_config['tab_typography']['font-weight'] ) ? $kb_config['tab_typography']['font-weight'] : 'inherit !important' ) . ';
			}
		#epkb-content-container .epkb-nav-tabs .active:after {
			border-top-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active {
			background-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active .epkb-category-level-1,
		#epkb-content-container .epkb-nav-tabs .active p {
			color: ' . $kb_config['tab_nav_active_font_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active:before {
			border-top-color: ' . $kb_config['tab_nav_border_color'] . '!important
		}';
	}

	if ( in_array( $css_file_slug, [ 'mp-frontend-tab-layout', 'mp-frontend-category-layout' ] ) ) {

		$kb_config['section_typography'] = array_merge( EPKB_Typography::$typography_defaults, $kb_config['section_typography'] );
		$kb_config['article_typography'] = array_merge( EPKB_Typography::$typography_defaults, $kb_config['article_typography'] );

		// General Typography ----------------------------------------------/
		if ( ! empty( $kb_config['general_typography']['font-family'] ) ) {
			$output .= '
			#epkb-main-page-container,
			#epkb-main-page-container .epkb-top-category-box 
			 {
			    ' . 'font-family:' . $kb_config['general_typography']['font-family'] . ' !important;' . '
			}';
		}

		$output .= '

		/* Container
		 * Category Body - section_typography ( Categories , Coming Soon )
		-----------------------------------------------------------------------*/
		#epkb-main-page-container .epkb-category-level-2-3__cat-name, 
		#epkb-main-page-container .epkb-articles-coming-soon { ' . '
				font-size: ' . ( ! empty( $kb_config['section_typography']['font-size'] ) ? $kb_config['section_typography']['font-size'] . 'px !important' : 'inherit !important' ) . ';
				font-weight: ' . ( ! empty( $kb_config['section_typography']['font-weight'] ) ? $kb_config['section_typography']['font-weight'] : 'inherit !important' ) . ';
		}

		/* Article list Settings
		 * Articles - article_typography ( Articles, Show Remaining Articles )
		-----------------------------------------------------------------------*/
		#epkb-main-page-container .epkb-section-body .eckb-article-title,
		#epkb-main-page-container .epkb-show-all-articles { ' . '
				font-size: ' . ( ! empty( $kb_config['article_typography']['font-size'] ) ? $kb_config['article_typography']['font-size'] . 'px !important' : 'inherit !important' ) . ';
				font-weight: ' . ( ! empty( $kb_config['article_typography']['font-weight'] ) ? $kb_config['article_typography']['font-weight'] : 'inherit !important' ) . ';
		}
		';
	}

	if ( $is_kb_main_page && $kb_config['modular_main_page_toggle'] == 'on' ) {
		$output .= EPKB_Modular_Main_Page::get_all_inline_styles( $kb_config );
	}

	// Article Page CSS and Sidebar Layout Main Page CSS
	if ( in_array( $css_file_slug, ['ap-frontend-layout', 'mp-frontend-sidebar-layout'] ) ) {
		$output .= EPKB_Articles_Setup::generate_article_structure_css_v2( $kb_config );

		// Elegant Layout outputs its own Sidebar and CSS - Elegant Layout version 2.15.3 and earlier does not have method get_inline_styles() and outputs the inline CSS directly itself
		if ( EPKB_Utilities::is_elegant_layouts_enabled() && class_exists( 'ELAY_Layout_Sidebar_v2' ) && method_exists( 'ELAY_Layout_Sidebar_v2', 'get_inline_styles' ) ) {
			$output .= ELAY_Layout_Sidebar_v2::get_inline_styles( $output, $kb_config );
		}

		// KB Core Article Page Sidebar CSS
		if ( ! in_array( $css_file_slug, ['mp-frontend-sidebar-layout'] ) ) {
			$output .= EPKB_Layout_Article_Sidebar::generate_sidebar_CSS_V2( $kb_config );
		}
	}

	if ( $css_file_slug == 'mp-frontend-modular-sidebar-layout' ) {
		$output .= EPKB_Layout_Article_Sidebar::generate_sidebar_CSS_V2( $kb_config );
	}

	// Article Page Modular Search
	if ( in_array( $css_file_slug, [ 'mp-frontend-sidebar-layout', 'ap-frontend-layout' ] ) ) {
		$output .= '
		/* Article Page Sidebar and Sidebar Layout
		-----------------------------------------------------------------------*/
		#epkb-sidebar-container-v2 .epkb-category-level-2-3 .epkb-category-level-2-3__cat-name {
		    color: ' . $kb_config['sidebar_section_category_font_color'] . '!important;
		}';

		// include inline styles for Search Module for Articles page only if it is used:
		// - is Article CSS slug and Modular toggle is 'on' (only Modular Search has inline CSS)
		// - is not Advanced search (Advanced Search uses its own Search box and styles)
		// - is first KB version 7.3.0 or higher
		if ( $css_file_slug == 'ap-frontend-layout' && $kb_config['modular_main_page_toggle'] == 'on'
			&& ! EPKB_Utilities::is_advanced_search_enabled( $kb_config ) ) {
			$output .= EPKB_ML_Search::get_inline_styles( $kb_config, true );
		}
	}

	$output .= '
		/* Frontend Editor button on top admin bar (frontend)
		-----------------------------------------------------------------------*/
		#wpadminbar #wp-admin-bar-epkb-edit-mode-button > .ab-item:before {
			content: "\f118";
			top: 2px;
			float: left;
			font: normal 20px/1 dashicons;
			speak: none;
			padding: 4px 0;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			background-image: none !important;
			margin-right: 6px;
			color: #4391F3;
		}
		#wpadminbar #wp-admin-bar-epkb-edit-mode-button > .ab-item:hover:before{
			color:#4391F3;
		}';

	// Category Archive Page V3
	if (  in_array( $css_file_slug, array( 'cp-frontend-layout', 'tp-frontend-layout' ) ) && $kb_config['archive_page_v3_toggle'] == 'on' ) {
        if (  EPKB_KB_Handler::is_kb_category_taxonomy( $GLOBALS['taxonomy'] ) ) {
	        $output .= EPKB_Category_Archive_Setup::get_all_inline_styles( $kb_config );
        } else if( EPKB_KB_Handler::is_kb_tag_taxonomy( $GLOBALS['taxonomy'] ) ) {
            $output .= EPKB_Tag_Archive_Setup::get_all_inline_styles( $kb_config );
        }

		if ( $kb_config['archive_left_sidebar_toggle'] == 'on' || $kb_config['archive_right_sidebar_toggle'] == 'on' ) {
			$output .= EPKB_Layout_Article_Sidebar::generate_sidebar_CSS_V2( $kb_config );
			$output .= apply_filters( 'epkb_ml_sidebar_layout_styles', '', $kb_config );
		}
	}

	$output .= $add_on_output;

	return EPKB_Utilities::minify_css( $output );
}

/**
 * Enqueue fonts that are configured in KB config
 */
function epkb_enqueue_google_fonts() {

	$kb_id = EPKB_Utilities::get_eckb_kb_id();
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
	foreach ( $kb_config as $name => $value ) {
		if ( is_array( $value ) && ! empty( $value['font-family'] ) ) {
			$font_link = EPKB_Typography::get_google_font_link( $value['font-family'] );
			if ( ! empty($font_link) ) {
				wp_enqueue_style( 'epkb-font-' . sanitize_title( $value['font-family']), $font_link );
			}
		}
	}
}

/**
 * Load assets to fix double article content
 * - Only admins
 * - Only if selected theme template
 * - Only if not applied yet
 * - One time fix - will not work if applied once
 */
function epkb_enqueue_the_content_scripts() {

	// for KB article, ignore if not post, is archive or current theme with any layout
	$post = empty( $GLOBALS['post'] ) ? '' : $GLOBALS['post'];
	if ( empty( $post ) || ! $post instanceof WP_Post || empty( $post->post_type ) || is_archive() || ! is_main_query() ) {
		return;
	}

	// exit if NOT KB Article URL
	if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
		return;
	}

	// we have KB Article
	$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
	if ( is_wp_error( $kb_id ) ) {
		return;
	}

	// if flag is already set - don't need the scripts anymore
	if ( EPKB_Core_Utilities::is_kb_flag_set( 'epkb_the_content_fix' ) ) {
		return;
	}

	// initialize KB config to be accessible to templates
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

	// check template used to prevent the_content filtering for our KB template
	if ( empty( $kb_config['templates_for_kb'] ) || $kb_config['templates_for_kb'] != 'current_theme_templates' ) {
		return;
	}

	// fix the content issue only if author, editor, or admin is reviewing the page
	if ( ! EPKB_Admin_UI_Access::is_user_admin_editor_author() ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'epkb-public-the-content-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-the-content' . $suffix . '.js', [], Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-public-the-content-scripts', 'epkb_the_content_i18n', array(
		'nonce'         => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
		'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
	));
}

/**
 * Queue for FRONT-END pages using our plugin features
 */
function epkb_enqueue_font() {
	wp_enqueue_style( 'epkb-icon-fonts' );
}
add_action( 'epkb_enqueue_font_scripts', 'epkb_enqueue_font' ); // use this action in any place to add scripts $kb_id as a parameter

/**
 * Load TOC classes to counter theme issues
 * @param $classes
 * @return array
 */
function epkb_front_end_body_classes( $classes ) {

	if ( EPKB_Utilities::is_kb_main_page() ) {
		return $classes;
	}

	$kb_id = EPKB_Utilities::get_eckb_kb_id( '' );

	// load only on article pages
	if ( empty( $kb_id ) )  {
		return $classes;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

	// load only if TOC is active
	if ( 'on' != $kb_config['article_toc_enable'] ) {
		return $classes;
	}

	// get current post
	$post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : '';
	if ( empty( $post ) || ! $post instanceof WP_Post ) {
		return $classes;
	}

	$classes[] = 'eckb-front-end-body';

	return $classes;

}
add_filter( 'body_class','epkb_front_end_body_classes' );

/**
 * Register KB areas for widgets to be added to
 */
function epkb_register_kb_sidebar() {

	$kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs( true );

	foreach( $kb_configs as $kb_config ) {

		$widget_kb_name = count( $kb_configs ) > 1 ? ' - ' . $kb_config['kb_name'] : '';
		$widget_id = $kb_config['id'] == 1 ? 'eckb_articles_sidebar' : 'eckb_articles_sidebar_' . $kb_config['id'];

		// add KB sidebar area
		register_sidebar( array(
			'name' => esc_html__('KB Sidebar' , 'echo-knowledge-base') . esc_html( $widget_kb_name ),
			'id' => $widget_id,
			'before_widget' => '<div id="eckb-%1$s" class="eckb-article-widget-sidebar-body__widget">',
			'after_widget' => '</div> <!-- end Widget -->',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		) );
	}
}
add_action( 'widgets_init', 'epkb_register_kb_sidebar' );

/**
 * Add KB filters for other plugins to use
 */
add_filter( 'kb_core/kb_config/get_kb_configs', function() {
	return epkb_get_instance()->kb_config_obj->get_kb_configs();
} );

/**
 * Add KB filters for other plugins to use
 */
add_filter( 'kb_core/kb_config/get_kb_config', function( $kb_id ) {
	return epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
} );

/**
 * Add KB filters for other plugins to use
 */
add_filter( 'kb_core/kb_icons/get_category_icon', function( $term_id, $categories_icons ) {
	return EPKB_KB_Config_Category::get_category_icon( $term_id, $categories_icons );
}, 10, 2 );

/**
 * Preload fonts for better performance
 */
function epkb_preload_fonts() {

	// if this is not KB Main Page or Article Page or Category Archive page then do not preload fonts
	$kb_id = EPKB_Utilities::get_eckb_kb_id( '' );
	if ( empty( $kb_id ) ) {
		return;
	}

	// preload fonts only if user enabled this feature in settings
	if ( ! EPKB_Core_Utilities::is_kb_flag_set( 'preload_fonts' ) ) {
		return;
	}

	$ep_icons_version = 'e3s9pc';       // see 'css/scss/shared/_icon-fonts.scss'
	$font_awesome_version = '4.7.0';    // see 'css/scss/shared/vendor/font-awesome/_variables.scss'    ?>
	<link rel="preload" as="font" href="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'css/fonts/ep_icons.woff?' . $ep_icons_version ); ?>" type="font/woff" crossorigin="anonymous">
	<link rel="preload" as="font" href="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'css/fonts/font-awesome/fontawesome-webfont.woff2?v=' . $font_awesome_version ); ?>" type="font/woff2" crossorigin="anonymous">  <?php
}
add_action( 'wp_head', 'epkb_preload_fonts', 1 );
