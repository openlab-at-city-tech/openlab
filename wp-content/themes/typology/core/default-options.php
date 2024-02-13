<?php
/**
 * Get default option by passing option id
 *
 * @param string  $option
 * @return array|mixed|false
 * @param since   1.0
 */

if ( !function_exists( 'typology_get_default_option' ) ):
	function typology_get_default_option( $option = null ) {


		if ( empty( $option ) ) {
			return false;
		}


		$defaults = array(

			'logo'   => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/typology_logo.png' ) ) ),
			'logo_retina'   => array( 'url' => esc_url( get_parent_theme_file_uri( '/assets/img/typology_logo@2x.png' ) ) ),
			'logo_custom_url'   => '',

			'style'   => 'material',
			'color_header_bg' => '#c62641',
			'cover_gradient' => false,
			'cover_gradient_color'  => '#000000',
			'cover_gradient_orientation'  => 'to right top',
			'cover_bg_media' => 'image',
			'cover_bg_img' => '',
			'cover_bg_video' => '',
			'cover_bg_video_image' => '',
			'cover_bg_opacity' => 0.6,
			'color_header_txt' => '#ffffff',
			'color_body_bg' => '#f8f8f8',
			'color_content_bg' => '#ffffff',
			'color_content_h' => '#333333',
			'color_content_txt' => '#444444',
			'color_content_acc' => '#c62641',
			'color_content_meta' => '#888888',
			'color_footer_bg' => '#f8f8f8',
			'color_footer_txt' => '#aaaaaa',
			'color_footer_acc' => '#888888',

			'header_layout'   => 1,
			'header_elements'   => array( 'main-menu' => 1, 'sidebar-button' => 1 ),
			'header_height' => 110,
			'header_orientation'   => 'content',
			'header_sticky'   => true,

			'footer_layout'  => '4-4-4',

			'layout_a_dropcap'   => true,
			'layout_a_fimg'   => false,
			'layout_a_meta' => array( 'author' => 1, 'category' => 1, 'rtime' => 1 ),
			'layout_a_content' => 'excerpt',
			'layout_a_excerpt_limit' => '400',
			'layout_a_buttons' => array( 'rm' => 1, 'rl' => 1 ),

			'layout_b_fimg'   => false,
			'layout_b_meta' => array( 'author' => 1, 'rtime' => 1, 'comments' => 1 ),
			'layout_b_excerpt'   => true,
			'layout_b_excerpt_limit' => '400',
			'layout_b_buttons' => array(),

			'layout_c_dropcap'   => true,
			'layout_c_fimg'   => false,
			'layout_c_meta' => array( 'date' => 1 ),

			'front_page_cover' => 'posts',
			'front_page_cover_posts' => 'date',
			'front_page_cover_posts_cat' => array(),
			'front_page_cover_posts_tag' => array(),
			'front_page_cover_posts_manual' => '',
			'front_page_cover_posts_num' => 5,
			'front_page_cover_posts_unique'  => true,
			'front_page_cover_dropcap'   => true,
			'layout_cover_meta' => array( 'author' => 1, 'category' => 1, 'comments' => 1 ),
			'layout_cover_buttons' => array( 'rm' => 1, 'rl' => 1 ),
			'front_page_cover_posts_fimg'  => false,
			'front_page_cover_autoplay' => false,
			'front_page_cover_autoplay_time' => 4,
			'front_page_cover_on_first_page' => false,

			'front_page_intro' => '0',
			'front_page_intro_on_first_page' => false,

			'front_page_posts' => 'date',
			'front_page_posts_cat' => array(),
			'front_page_posts_tag' => array(),
			'front_page_posts_manual' => '',
			'front_page_posts_ppp'   => 'inherit',
			'front_page_posts_num'   => get_option( 'posts_per_page' ),
			'front_page_posts_layout'   => 'a',
			'front_page_posts_pagination'   => 'load-more',

			'archive_cover' => false,
			'archive_dropcap'   => true,
			'archive_description' => true,
			'archive_layout'   => 'a',
			'archive_ppp'   => 'inherit',
			'archive_ppp_num'   => get_option( 'posts_per_page' ),
			'archive_pagination'   => 'load-more',

			'category_settings_type' => 'inherit',
			'category_cover'  => false,
			'category_layout'  => 'a',
			'category_ppp'  => 'inherit',
			'category_ppp_num'  => get_option( 'posts_per_page' ),
			'category_pagination'  => 'load-more',

			'tag_settings_type' => 'inherit',
			'tag_cover' => false,
			'tag_layout'   => 'a',
			'tag_ppp'   => 'inherit',
			'tag_ppp_num'   => get_option( 'posts_per_page' ),
			'tag_pagination'   => 'load-more',

			'author_settings_type' => 'inherit',
			'use_author_image' => false,
			'author_cover' => false,
			'author_layout'   => 'a',
			'author_ppp'   => 'inherit',
			'author_ppp_num'   => get_option( 'posts_per_page' ),
			'author_pagination'   => 'load-more',

			'single_cover' => false,
			'single_dropcap'   => true,
			'layout_single_meta' => array( 'author' => 1, 'comments' => 1, 'date' => 1, 'rtime' => 1, 'category' => 1 ),
			'single_fimg' => 'none',
			'single_fimg_cap' => false,
			'single_share' => true,
			'single_share_options' => 'below',
			'single_tags' => true,
			'single_author' => true,
			'single_sticky_bottom_bar' => true,
			'single_prev_next_in_same_term' => true,
			'single_related' => true,
			'related_type'   => 'cat',
			'related_order'   => 'date',
			'related_limit'   => 4,
			'related_layout'   => 'c',

			'page_cover' => false,
			'page_dropcap'   => true,
			'page_fimg' => 'none',

			'main_font'     => array(
				'google'      => true,
				'font-weight'  => '400',
				'font-family' => 'Domine',
				'subsets' => 'latin-ext'
			),
			'h_font'     => array(
				'google'      => true,
				'font-weight'  => '600',
				'font-family' => 'Josefin Sans',
				'subsets' => 'latin-ext'
			),
			'nav_font'     => array(
				'font-weight'  => '600',
				'font-family' => 'Josefin Sans',
				'subsets' => 'latin-ext'
			),
			'font_size_p'  => '16',
			'font_size_nav'  => '11',
			'font_size_small'  => '14',
			'font_size_meta'  => '13',
			'font_size_cover'  => '64',
			'font_size_cover_dropcap'  => '600',
			'font_size_dropcap'  => '260',
			'font_size_h1'  => '48',
			'font_size_h2'  => '35',
			'font_size_h3'  => '28',
			'font_size_h4'  => '23',
			'font_size_h5'  => '18',
			'font_size_h6'  => '15',
			'uppercase' => array(
				'.site-title' => 1,
				'.typology-site-description' => 0,
				'.typology-nav' => 1,
				'h1, h2, h3, h4, h5, h6, .wp-block-cover-text, .wp-block-cover-image-text' => 1,
				'.section-title' => 1,
				'.widget-title' => 1,
				'.meta-item' => 0,
				'.typology-button' => 1
			),
			'content-paragraph-width'   => 720,

			'ad_top' => '',
			'ad_bottom' => '',
			'ad_between_posts' => '',
			'ad_between_posts_position' => 4,
			'ad_exclude_404'  => true,
			'ad_exclude_from_pages'  => array(),

			'rtl_mode' => false,
			'rtl_lang_skip' => '',
			'more_string' => '...',
			'use_gallery' => true,
			'on_single_img_popup' => false,
			'scroll_down_arrow' => false,
			'words_read_per_minute' => 200,
			'post_modified_date' => false,

			'enable_translate' => '1',

			'minify_css' => true,
			'minify_js' => true,
			'disable_img_sizes' => array()


		);

		$defaults = apply_filters( 'typology_modify_default_options', $defaults );

		if ( isset( $defaults[$option] ) ) {
			return $defaults[$option];
		}

		return false;
	}
endif;

?>