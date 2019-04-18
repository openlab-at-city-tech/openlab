<?php

/**
 * body_class callback
 *
 * Checks for specific options and applies additional class to body element
 *
 * @since  1.0
 */

add_filter( 'body_class', 'johannes_body_class' );

if ( !function_exists( 'johannes_body_class' ) ):
	function johannes_body_class( $classes ) {

		if ( johannes_has_sidebar( 'right' ) ) {
			$classes[] = 'johannes-sidebar-right';
		}

		if ( johannes_has_sidebar( 'left' ) ) {
			$classes[] = 'johannes-sidebar-left';
		}

		if ( johannes_has_sidebar( 'none' ) ) {
			$classes[] = 'johannes-sidebar-none';
		}

		if ( johannes_get_option('header_orientation') == 'window') {
			$classes[] = 'johannes-header-window';
		}

		if ( johannes_get_option('header_cover_indent') && johannes_get('cover') && in_array( johannes_get_option( 'header_layout' ), array('1', '2', '3', '4') ) ) {
			$classes[] = 'johannes-cover-indent';
		}

		if ( johannes_get_option('header_multicolor') ) {
			$classes[] = 'johannes-header-multicolor';
		}

		if( !johannes_is_color_light( johannes_get_option('color_bg_alt_1') ) ){
			$classes[] = 'white-bg-alt-1';
		}

		if( !johannes_is_color_light( johannes_get_option('color_bg_alt_2') ) ){
			$classes[] = 'white-bg-alt-2';
		}

		if ( johannes_get_option('overlays') != 'dark' ) {
			$classes[] = 'johannes-overlays-' . johannes_get_option('overlays');
		}

		if( !in_array( johannes_get_option( 'header_layout' ), array('4', '6', '9') ) && !in_array( 'hamburger', johannes_get_option( 'header_actions' ) ) ) {
			$classes[] = 'johannes-hamburger-hidden';
		}

		if( in_array( johannes_get_option( 'header_layout' ), array('4', '6', '9') ) && !in_array( 'hamburger', johannes_get_option( 'header_actions_l' ) ) && !in_array( 'hamburger', johannes_get_option( 'header_actions_r' ) )) {
			$classes[] = 'johannes-hamburger-hidden';
		}

		if ( johannes_get_option('header_orientation') == 'content' && johannes_get_option('header_bottom_style') == 'boxed' && in_array( johannes_get_option( 'header_layout' ), array('5', '6', '7', '8', '9', '10') ) ) {
			$classes[] = 'johannes-header-bottom-boxed';
		}

		if ( in_array( johannes_get_option( 'header_layout' ), array('5', '6', '7', '8', '9', '10') ) && johannes_get_option('color_header_middle_bg') != johannes_get_option('color_header_bottom_bg')) {
			$classes[] = 'johannes-header-bottom-color';
		}
		
		if ( in_array( johannes_get_option( 'header_layout' ), array('1', '2', '3', '4') ) && johannes_get_option('color_header_middle_bg') == johannes_get_option('color_bg') && !johannes_get_option('header_multicolor')) {
			$classes[] = 'johannes-header-no-margin';
		}

		if( johannes_get('cover') ){
			$classes[] = 'johannes-header-no-margin';
		}

		if ( !johannes_get_option('header_labels') ) {
			$classes[] = 'johannes-header-labels-hidden';
		}

		if ( johannes_get_option('color_bg') != johannes_get_option('color_footer_bg') ) {
			$classes[] = 'johannes-footer-margin';
		}

		$classes[] = 'johannes-v_' . str_replace( '.', '_', JOHANNES_THEME_VERSION );

		if ( is_child_theme() ) {
			$classes[] = 'johannes-child';
		}

		return $classes;
	}
endif;


/**
 * Content width
 *
 * Checks for specific options and change content width global based on the current layout
 *
 * @since  1.0
 */

add_action( 'template_redirect', 'johannes_content_width', 0 );

if ( !function_exists( 'johannes_content_width' ) ):
	function johannes_content_width() {

		if ( is_page() ) {
            $content_width = johannes_size_by_col( johannes_get_option('page_width') );
        } elseif ( is_single() ) {
        	$content_width = johannes_size_by_col( johannes_get_option('single_width') );
        } else {
        	$content_width = johannes_size_by_col( 12 );
        }

		$GLOBALS['content_width'] = $content_width;
	}
endif;


/**
 * frontpage_template filter callback
 *
 * Use front-page.php template only if a user enabled it in theme options.
 * This provides a possibility for the user to opt-out and use wordpress default reading settings. 
 *
 * @since  1.0
 */

add_filter( 'frontpage_template',  'johannes_front_page_template' );

if ( !function_exists( 'johannes_front_page_template' ) ):
function johannes_front_page_template( $template ) {

	if( johannes_get_option('front_page_template') ){
		return $template;
	}

	if ( 'posts' == get_option( 'show_on_front' ) ) {
    	$template = get_home_template();
	} else {
	    $template = get_page_template();
	}

	return $template;
}

endif;

/**
 *
 * Add span elements to post count number in category widget
 *
 * @since  1.0
 */


add_filter( 'wp_list_categories', 'johannes_modify_category_widget_post_count', 10, 2 );

if ( !function_exists( 'johannes_modify_category_widget_post_count' ) ):
	function johannes_modify_category_widget_post_count( $links, $args ) {

		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] != 'category' ) {
			return $links;
		}

		if ( !isset( $args['show_count'] ) ||  !$args['show_count'] ) {
			return $links;
		}

		$links = str_replace( '(', '<span class="dots"></span><span class="count">', $links );
		$links = str_replace( ')', '</span>', $links );

		return $links;
	}
endif;

/**
 *
 * Add css class to parent in category widget so we can have an accordion menu
 *
 * @since  1.0
 */

add_filter( 'category_css_class', 'johannes_modify_category_widget_css_class', 10, 4 );

if ( !function_exists( 'johannes_modify_category_widget_css_class' ) ):
	function johannes_modify_category_widget_css_class( $css_classes, $category, $depth, $args ) {
		if ( isset( $args['hierarchical'] ) && $args['hierarchical'] ) {
			$term = get_queried_object();
			$children = get_terms( $category->taxonomy, array(
					'parent'    => $category->term_id,
					'hide_empty' => false
				) );

			if ( !empty( $children ) ) {
				$css_classes[] = 'cat-parent';
			}

		}
		return $css_classes;
	}
endif;


/**
 *
 * Add span elements to post count number in archives widget
 *
 * @since  1.0
 */

add_filter( 'get_archives_link', 'johannes_modify_archive_widget_post_count', 10, 6 );

if ( !function_exists( 'johannes_modify_archive_widget_post_count' ) ):
	function johannes_modify_archive_widget_post_count( $link_html, $url, $text, $format, $before, $after ) {

		if ( $format == 'html' && !empty( $after ) ) {
			$new_after = str_replace( '(', '<span class="dots"></span><span class="count">', $after );
			$new_after = str_replace( ')', '</span>', $new_after );

			$link_html = str_replace( $after, $new_after, $link_html );
		}

		return $link_html;
	}
endif;


/**
 * Widget display callback
 *
 * Check if background option is selected and add css class to widget
 *
 * @return void
 * @since  1.0
 */

add_filter( 'dynamic_sidebar_params', 'johannes_modify_widget_display' );

if ( !function_exists( 'johannes_modify_widget_display' ) ) :
	function johannes_modify_widget_display( $params ) {

		if ( strpos( $params[0]['id'], 'johannes_sidebar_footer' ) !== false ) {
			return $params; //do not apply styling for footer widgets
		}

		if( is_customize_preview() ){
			$default = johannes_get_background_css_class( johannes_get_option( 'widget_bg' ) );

			$params[0]['before_widget'] = str_replace( 'johannes-bg-alt-1', $default, $params[0]['before_widget'] );
			$params[0]['before_widget'] = str_replace( 'johannes-bg-alt-2', $default, $params[0]['before_widget'] );
		}

		global $wp_registered_widgets;

		$widget_id              = $params[0]['widget_id'];
		$widget_obj             = $wp_registered_widgets[$widget_id];
		$widget_num             = $widget_obj['params'][0]['number'];
		$widget_opt = get_option( $widget_obj['callback'][0]->option_name );


		if ( isset( $widget_opt[$widget_num]['johannes-bg'] ) && $widget_opt[$widget_num]['johannes-bg'] ) {

			$css_class = johannes_get_background_css_class( $widget_opt[$widget_num]['johannes-bg'] );
			$default = johannes_get_background_css_class( johannes_get_option( 'widget_bg' ) );
			$params[0]['before_widget'] = str_replace( $default, $css_class, $params[0]['before_widget'] );

		
		}

		return $params;

	}

endif;


/**
 * Add media grabber features
 *
 * We use it to pull audio,video or gallery instead of featured image when post formats are used
 *
 */

add_action( 'init', 'johannes_add_media_grabber' );

if ( !function_exists( 'johannes_add_media_grabber' ) ):
	function johannes_add_media_grabber() {
		if ( !class_exists( 'Hybrid_Media_Grabber' ) ) {

			include_once get_template_directory() .'/inc/media-grabber/class-hybrid-media-grabber.php';
		}
	}
endif;


/**
 * Modify WooCommerce wrappers
 *
 * Provide support for WooCommerce pages to match theme HTML markup
 *
 * @return HTML output
 * @since  1.0
 */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
add_action( 'woocommerce_before_main_content', 'johannes_woocommerce_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'johannes_woocommerce_wrapper_end', 10 );

if ( !function_exists( 'johannes_woocommerce_wrapper_start' ) ):
	function johannes_woocommerce_wrapper_start() {

		echo '<div class="johannes-section">';
		echo '<div class="container">';
		echo '<div class="section-content row">';

		if ( johannes_has_sidebar( 'left' ) ) {
			echo '<div class="col-12 col-lg-4 johannes-order-3">';
			get_sidebar();
			echo '</div>';
		}

		$class =  johannes_has_sidebar( 'none' ) ? '' : 'col-lg-8';
		
		echo '<div class="col-12 johannes-order-1 '.esc_attr( $class ).'">';

	}
endif;

if ( !function_exists( 'johannes_woocommerce_wrapper_end' ) ):
	function johannes_woocommerce_wrapper_end() {
		echo '</div>';
		if ( johannes_has_sidebar( 'right' ) ) {
			echo '<div class="col-12 col-lg-4 johannes-order-3">';
			get_sidebar();
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
endif;


/**
 * pre_get_posts filter callback
 *
 * If a user select custom number of posts per specific archive
 * template, override default post per page value
 *
 * @since  1.0
 */

add_action( 'pre_get_posts', 'johannes_pre_get_posts' );

if ( !function_exists( 'johannes_pre_get_posts' ) ):
	function johannes_pre_get_posts( $query ) {

		if ( !is_admin() && $query->is_main_query() && ( $query->is_archive() || $query->is_search() || $query->is_posts_page ) && !$query->is_feed() ) {

			$ppp = get_option( 'posts_per_page' );

			if( $query->is_category() ){
				$ppp = johannes_get_category_meta( get_queried_object_id(), 'ppp_num' );
			} else {
				$ppp = johannes_get_option( 'archive_ppp' ) == 'custom' ? johannes_get_option(  'archive_ppp_num' ) : $ppp;
			}

			$query->set( 'posts_per_page', absint( $ppp ) );

		}

	}
endif;


/**
 * wp_link_pages_link filter callback
 *
 * Used to add css classes to style paginated post links
 *
 * @since  1.0
 */

add_filter( 'wp_link_pages_link', 'johannes_wp_link_pages_link' );

if ( !function_exists( 'johannes_wp_link_pages_link' ) ):
	function johannes_wp_link_pages_link( $link ) {

		if ( stripos( $link, '<a' ) !== false ) {
			$link = str_replace( '<a' , '<a class="johannes-button johannes-button-primary johannes-button-square"'  , $link );
		} else {
			$link = '<span class="johannes-button johannes-button-primary johannes-button-square">'.$link.'</span>';
		}

		return $link;
	}
endif;


/**
 * Woocommerce Ajaxify Cart
 *
 * @return bool
 * @since  1.0
 */


add_filter( 'woocommerce_add_to_cart_fragments', 'johannes_woocommerce_ajax_fragments' );

if ( !function_exists( 'johannes_woocommerce_ajax_fragments' ) ):

function johannes_woocommerce_ajax_fragments( $fragments ) {
	ob_start();
	get_template_part( 'template-parts/header/elements/cart' );
	$fragments['.johannes-cart'] = ob_get_clean();
	return $fragments;
}

endif;


/**
 * Add comment form default fields args filter
 * to replace comment fields labels
 *
 * @since  1.0
 */

add_filter( 'comment_form_default_fields', 'johannes_comment_fields_labels' );

if ( !function_exists( 'johannes_comment_fields_labels' ) ):
	function johannes_comment_fields_labels( $fields ) {

		$replace = array(
			'author' => array(
				'old' => __( 'Name', 'johannes' ),
				'new' =>__johannes( 'comment_name' )
			),
			'email' => array(
				'old' => __( 'Email', 'johannes' ),
				'new' =>__johannes( 'comment_email' )
			),
			'url' => array(
				'old' => __( 'Website', 'johannes' ),
				'new' =>__johannes( 'comment_website' )
			),

			'cookies' => array(
				'old' => __( 'Save my name, email, and website in this browser for the next time I comment.', 'johannes' ),
				'new' =>__johannes( 'comment_cookie_gdpr' )
			)
		);

		foreach ( $fields as $key => $field ) {

			if ( array_key_exists( $key, $replace ) ) {
				$fields[$key] = str_replace( $replace[$key]['old'], $replace[$key]['new'], $fields[$key] );
			}

		}

		return $fields;

	}

endif;

/**
 * Hook onto Meks Easy Social Share plugin to fill the options
 *
 * @since  1.0
 */

add_filter( 'meks_ess_modify_share_options', 'johannes_meks_ess_modify_share_options' );

function johannes_meks_ess_modify_share_options( $options ) {
	if ( johannes_is_meks_ess_active() && is_single() ) {

		$options = johannes_get_option( 'social_share' );

	}
	return $options;
}

?>