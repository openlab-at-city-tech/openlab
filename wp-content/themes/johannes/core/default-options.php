<?php
/**
 * Get default option by passing option id or don't pass anything to function and get all options
 *
 * @param string  $option
 * @return array|mixed|false
 * @param since   1.0
 */

if ( !function_exists( 'johannes_get_default_option' ) ):
	function johannes_get_default_option( $option = null ) {

		global $johannes_translate;

		if ( empty( $option ) ) {
			return false;
		}


		$defaults = array(

			// Header
			'header_layout' => '1',
			'header_orientation' => 'content',
			'header_cover_indent' => '0',
			'header_multicolor' => false,
			'header_bottom_style' => 'unboxed',
			'header_height' => 130,
			'header_main_nav' => true,
			'header_site_desc' => false,
			'header_actions' => array( 'hamburger' ),
			'header_actions_l' => array( 'search-modal' ),
			'header_actions_r' => array( 'hamburger' ),
			'header_actions_responsive' => array( 'social' ),
			'header_labels' => true,

			// Top bar
			'header_top' => false,
			'header_top_l' => array( 'menu-secondary-1' ),
			'header_top_c' => array( 'date' ),
			'header_top_r' => array( 'social' ),

			// Sticky
			'header_sticky' => true,
			'header_sticky_layout' => '1',
			'header_sticky_offset' => 300,
			'header_sticky_up' => false,
			'header_sticky_logo' => 'mini',
			'header_sticky_height' => 60,
			'header_sticky_contextual' => true,
			
			//Logo
			'logo' => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/johannes_logo.png' ) ) ),
			'logo_retina' => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/johannes_logo@2x.png' ) ) ),
			'logo_mini' => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/johannes_logo_mini.png' ) ) ),
			'logo_mini_retina' => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/johannes_logo_mini@2x.png' ) ) ),
			'logo_custom_url' => '',


			// Responsive
			'header_responsive_actions' => array(),

			//Megamenu
			'mega_menu' => true,
			'mega_menu_ppp' => 6,

			//Header colors
			'color_header_top_bg' => '#424851',
			'color_header_top_txt' => '#989da2',
			'color_header_top_acc' => '#ffffff',

			'color_header_middle_bg' => '#ffffff',
			'color_header_middle_txt' => '#424851',
			'color_header_middle_acc' => '#f13b3b',
			'color_header_middle_bg_multi' => '#424851',

			'color_header_bottom_bg' => '#ffffff',
			'color_header_bottom_txt' => '#424851',
			'color_header_bottom_acc' => '#f13b3b',

			// Content
			'color_bg' => '#ffffff',
			'color_h' => '#424851',
			'color_txt' => '#424851',
			'color_acc' => '#f13b3b',
			'color_meta' => '#989da2',
			'color_bg_alt_1' => '#f2f3f3',
			'color_bg_alt_2' => '#424851',
			'overlays' => 'dark',

			'sidebars' => array(),
			'widget_bg' => 'alt-1',

			// Footer
			'footer_instagram' => true,
			'footer_instagram_front' => false,
			'footer_instagram_username' => 'unsplash',
			'footer_widgets' => true,
			'footer_widgets_layout' => '3-3-3-3',
			'footer_copyright' => wp_kses_post( sprintf( __( 'Created by %s &middot; Copyright {current_year} &middot; All rights reserved', 'johannes' ), '<a href="https://mekshq.com" target="_blank">Meks</a>' ) ),

			'color_footer_bg' => '#ffffff',
			'color_footer_txt' => '#424851',
			'color_footer_acc' => '#f13b3b',
			'color_footer_meta' => '#989da2',


			/**
			 * Post layouts
			 */

			'layout_a_cat' => true,
			'layout_a_format' => true,
			'layout_a_meta' => array( 'author', 'date' ),
			'layout_a_excerpt' => true,
			'layout_a_excerpt_limit' => 250,
			'layout_a_excerpt_type' => 'auto',
			'layout_a_width' => '6',
			'layout_a_rm' => false,
			'layout_a_img_ratio' => '21_9',
			'layout_a_img_custom' => '',

			'layout_b_cat' => true,
			'layout_b_format' => true,
			'layout_b_meta' => array( 'author' ),
			'layout_b_excerpt' => true,
			'layout_b_excerpt_limit' => 250,
			'layout_b_excerpt_type' => 'auto',
			'layout_b_width' => '9',
			'layout_b_rm' => false,
			'layout_b_img_ratio' => '3_2',
			'layout_b_img_custom' => '',

			'layout_c_cat' => true,
			'layout_c_format' => true,
			'layout_c_meta' => array( 'rtime' ),
			'layout_c_excerpt' => true,
			'layout_c_excerpt_limit' => 250,
			'layout_c_rm' => false,
			'layout_c_img_ratio' => '16_9',
			'layout_c_img_custom' => '',

			'layout_d_cat' => true,
			'layout_d_format' => true,
			'layout_d_meta' => array( 'date' ),
			'layout_d_excerpt' => false,
			'layout_d_excerpt_limit' => 150,
			'layout_d_rm' => false,
			'layout_d_img_ratio' => '16_9',
			'layout_d_img_custom' => '',

			'layout_e_cat' => true,
			'layout_e_format' => true,
			'layout_e_meta' => array( 'author' ),
			'layout_e_excerpt' => true,
			'layout_e_excerpt_limit' => 250,
			'layout_e_rm' => false,
			'layout_e_img_ratio' => '16_9',
			'layout_e_img_custom' => '',

			'layout_f_cat' => true,
			'layout_f_format' => true,
			'layout_f_meta' => array( 'date' ),
			'layout_f_img_ratio' => '1_1',
			'layout_f_img_custom' => '',

			'layout_fa_a_cat' => true,
			'layout_fa_a_format' => true,
			'layout_fa_a_meta' => array( 'author', 'date' ),
			'layout_fa_a_height' => 500,

			'layout_fa_b_cat' => true,
			'layout_fa_b_format' => true,
			'layout_fa_b_meta' => array( 'comments', 'date' ),
			'layout_fa_b_img_ratio' => '16_9',
			'layout_fa_b_img_custom' => '',

			'layout_fa_c_cat' => true,
			'layout_fa_c_format' => true,
			'layout_fa_c_meta' => array( 'date' ),
			'layout_fa_c_img_ratio' => '1_1',
			'layout_fa_c_img_custom' => '',

			'layout_fa_d_cat' => true,
			'layout_fa_d_format' => true,
			'layout_fa_d_meta' => array( 'rtime' ),
			'layout_fa_d_img_ratio' => '1_1',
			'layout_fa_d_img_custom' => '',

			'layout_fa_e_cat' => true,
			'layout_fa_e_format' => true,
			'layout_fa_e_meta' => array( 'author' ),
			'layout_fa_e_img_ratio' => '1_1',
			'layout_fa_e_img_custom' => '',

			// Front page
			
			'front_page_template' => true,
			'front_page_sections' => array( 'classic' ),
			'front_page_loop' => '3',
			'front_page_ppp' => 'inherit',
			'front_page_ppp_num' =>  get_option( 'posts_per_page' ),
			'front_page_cat' => array(),
			'front_page_tag' => array(),
			'front_page_pagination' => 'prev-next',
			'front_page_sidebar_position' => 'right',
			'front_page_sidebar_standard' => 'johannes_sidebar_default',
			'front_page_sidebar_sticky' => 'johannes_sidebar_default_sticky',
			'front_page_classic_display_title' => true,
			'tr_front_page_classic_title' => $johannes_translate['front_page_classic_title']['text'],

			'front_page_fa_display_title' => true,
			'tr_front_page_featured_title' => $johannes_translate['front_page_featured_title']['text'],
			'front_page_fa_loop' => '12',
			'front_page_fa_ppp' => '5',
			'front_page_fa_orderby' => 'date',
			'front_page_fa_cat' => array(),
			'front_page_fa_tag' => array(),
			'front_page_fa_unique' => false,

			'front_page_wa_layout' => '2',
			'front_page_wa_img' => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/johannes_default.jpg' ) ) ),
			'front_page_wa_display_title' => false,
			'tr_front_page_wa_title' => $johannes_translate['front_page_wa_title']['text'],
			'tr_front_page_wa_punchline' => $johannes_translate['front_page_wa_punchline']['text'],
			'tr_front_page_wa_text' => $johannes_translate['front_page_wa_text']['text'],
			'front_page_wa_cta' => true,
			'tr_front_page_wa_cta_label' => $johannes_translate['front_page_wa_cta_label']['text'],
			'front_page_wa_cta_url' => home_url( '/' ),
			'wa_layout_1_img_ratio' => '1_1',
			'wa_layout_1_img_custom' => '',
			'wa_layout_2_img_ratio' => '1_1',
			'wa_layout_2_img_custom' => '',
			'wa_layout_3_height' => 500,
			'wa_layout_4_height' => 600,

			// Single Post
			'single_layout' => '1',
			'single_sidebar_position' => 'none',
			'single_sidebar_standard' => 'johannes_sidebar_default',
			'single_sidebar_sticky' => 'johannes_sidebar_default_sticky',
			'single_width' => '6',
			'single_cat' => true,
			'single_meta' => array( 'comments', 'date', 'rtime' ),
			'single_fimg' => true,
			'single_fimg_cap' => true,
			'single_headline' => true,
			'single_avatar' => true,
			'single_tags' => true,
			'single_share' => 'below',
			'single_author' => true,
			'single_related' => true,
			'single_related_using' => 'default',
			'related_limit' => 6,
			'related_layout' => '5',
			'related_type' => 'cat',
			'related_order' => 'date',
			'single_layout_1_img_ratio' => '21_9',
			'single_layout_1_img_custom' => '',
			'single_layout_2_img_ratio' => '21_9',
			'single_layout_2_img_custom' => '',
			'single_layout_3_height' => 500,
			'single_layout_4_height' => 600,
			'single_layout_5_img_ratio' => '1_1',
			'single_layout_5_img_custom' => '',


			// Page
			'page_layout' => '1',
			'page_sidebar_position' => 'none',
			'page_sidebar_standard' => 'johannes_sidebar_default',
			'page_sidebar_sticky' => 'johannes_sidebar_default_sticky',
			'page_width' => '6',
			'page_fimg' => true,
			'page_fimg_cap' => false,
			'page_layout_1_img_ratio' => '21_9',
			'page_layout_1_img_custom' => '',
			'page_layout_2_img_ratio' => '21_9',
			'page_layout_2_img_custom' => '',
			'page_layout_3_height' => 400,
			'page_layout_4_height' => 450,

			// Archive
			'archive_layout' => '1',
			'archive_loop' => '4',
			'archive_description' => true,
			'archive_meta' => true,
			'archive_ppp' => 'inherit',
			'archive_ppp_num' =>  get_option( 'posts_per_page' ),
			'archive_pagination' => 'numeric',
			'archive_sidebar_position' => 'right',
			'archive_sidebar_standard' => 'johannes_sidebar_default',
			'archive_sidebar_sticky' => 'johannes_sidebar_default_sticky',
			'archive_layout_2_height' => 400,
			'archive_layout_3_height' => 450,

			// Category

			'category_settings' => 'custom',
			'category_layout' => '1',
			'category_loop' => '5',
			'category_description' => true,
			'category_meta' => true,
			'category_subnav' => true,
			'category_ppp' => 'inherit',
			'category_ppp_num' =>  get_option( 'posts_per_page' ),
			'category_pagination' => 'infinite-scroll',
			'category_sidebar_position' => 'right',
			'category_sidebar_standard' => 'johannes_sidebar_default',
			'category_sidebar_sticky' => 'johannes_sidebar_default_sticky',

			// Typography
			'main_font' => array(
				'font-family' => 'Muli',
				'variant'  => '400',
				'font-weight' => '400'
			),

			'h_font' => array(
				'font-family' => 'Muli',
				'variant' => '900',
				'font-weight' => '900'
			),

			'nav_font' => array(
				'font-family' => 'Muli',
				'variant' => '700',
				'font-weight' => '700'
			),

			'button_font' => array(
				'font-family' => 'Muli',
				'variant' => '900',
				'font-weight' => '900'
			),

			'font_size_p' => '16',
			'font_size_small' => '14',
			'font_size_nav' => '14',
			'font_size_nav_ico' => '24',
			'font_size_section_title' => '40',
			'font_size_widget_title' => '20',
			'font_size_punchline' => '52',
			'font_size_h1' => '48',
			'font_size_h2' => '40',
			'font_size_h3' => '36',
			'font_size_h4' => '32',
			'font_size_h5' => '28',
			'font_size_h6' => '24',

			'uppercase' => array(),

			// Misc.
			'default_fimg' => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/johannes_default.jpg' ) ) ),
			'breadcrumbs' => 'none',
			'rtl_mode' => false,
			'rtl_lang_skip' => '',
			'more_string' => '...',
			'words_read_per_minute' => 180,
			'popup' => true,
			'social_share' => array(
				'facebook',
				'twitter',
				'pinterest',
				'whatsapp'
			),
			'go_to_top' => true,

			// Ads
			'ad_header' => '',
			'ad_above_archive' => '',
			'ad_above_singular' => '',
			'ad_above_footer' => '',
			'ad_between_posts' => '',
			'ad_between_position' => 6,
			'ad_exclude' => array(),

			// WooCommerce
			'woocommerce_sidebar_position' => 'right',
			'woocommerce_sidebar_standard' => 'johannes_sidebar_default',
			'woocommerce_sidebar_sticky' => 'johannes_sidebar_default_sticky',
			'woocommerce_cart_force' => true,

			// Translation Options
			'enable_translate' => true,

			// Performance
			'minify_css' => true,
			'minify_js' => true,
			'disable_img_sizes' => array(),
		);

		$translate_strings = johannes_get_translate_options();

		foreach ( $translate_strings as $string_key => $item ) {

			if ( isset( $item['hidden'] ) ) {
				continue;
			}

			if ( isset( $item['default'] ) ) {
				$defaults['tr_' . $string_key] = $item['default'];
			}
		}

		$defaults = apply_filters( 'johannes_modify_default_options', $defaults );

		if ( isset( $defaults[$option] ) ) {
			return $defaults[$option];
		}

		return false;
	}
endif;

?>