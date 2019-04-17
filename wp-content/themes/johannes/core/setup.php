<?php

/**
 * After Theme Setup
 *
 * Callback for after_theme_setup hook
 *
 * @since  1.0
 */

add_action( 'after_setup_theme', 'johannes_theme_setup' );

function johannes_theme_setup() {

    /* Define default content width */
    $GLOBALS['content_width'] = johannes_size_by_col( 12 );

    /* Localization */
    load_theme_textdomain( 'johannes', get_parent_theme_file_path( '/languages' ) );

    /* Add thumbnails support */
    add_theme_support( 'post-thumbnails' );

    /* Add post formats support */
    add_theme_support( 'post-formats', array( 'audio', 'gallery', 'video' ) );

    /* Add theme support for title tag */
    add_theme_support( 'title-tag' );

    /* Add image sizes */
    $image_sizes = johannes_get_image_sizes();
    //print_r( $image_sizes );
    if ( !empty( $image_sizes ) ) {
        foreach ( $image_sizes as $id => $size ) {
            add_image_size( $id, $size['w'], $size['h'], $size['crop'] );
        }
    }

    /* Support for HTML5 elements */
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

    /* Automatic feed links */
    add_theme_support( 'automatic-feed-links' );

    /* WooCommerce features support */
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    /* Load editor styles */
    add_theme_support( 'editor-styles' );

    if ( is_admin() ) {
        johannes_load_editor_styles();
    }

    /* Support for alignwide elements */
    add_theme_support( 'align-wide' );

     /* Support for responsive embeds */
    add_theme_support( 'responsive-embeds' );

    /* Support for predefined colors in editor */
    add_theme_support( 'editor-color-palette', johannes_get_editor_colors() );

    /* Support for predefined font-sizes in editor */
    add_theme_support( 'editor-font-sizes', johannes_get_editor_font_sizes() );

   
}


/**
 * Check all display settings from theme options
 * and store it globally as a query var so we can access it from any template file
 *
 * @since  1.0
 */

add_action( 'template_redirect', 'johannes_templates_setup' );

if ( ! function_exists( 'johannes_templates_setup' ) ):
    function johannes_templates_setup() {

        // delete_option('johannes_settings');
        // delete_option('johannes_welcome_box_displayed');
        // delete_option('merlin_johannes_completed');

        $defaults = johannes_get_default_template_options();

        if ( is_front_page() ) {

            if ( johannes_get_option( 'front_page_template' ) ) {

                $johannes = johannes_get_front_page_template_options();
            
            } else {

                if ( 'posts' == get_option( 'show_on_front' ) ) {
                   $johannes = johannes_get_archive_template_options();
                } else {
                    $johannes = johannes_get_page_template_options();
                }
            }

        } elseif ( is_page_template( 'template-blank.php' ) ) {
            $johannes = johannes_get_blank_template_options();
        } elseif ( is_page_template( 'template-authors.php' ) ) {
            $johannes = johannes_get_authors_template_options();
        } elseif ( johannes_is_woocommerce_page() ) {
            $johannes = johannes_get_woocommerce_template_options();
        } elseif ( is_page() ) {
            $johannes = johannes_get_page_template_options();
        } elseif ( is_single() ) {
            $johannes = johannes_get_single_template_options();
        }  elseif ( is_category() ) {
            $johannes = johannes_get_category_template_options();
        } elseif ( is_404() ) {
            $johannes = johannes_get_404_template_options();
        } else {
            $johannes = johannes_get_archive_template_options();
        }

        $johannes['header'] = johannes_get_header_options();
        $johannes['footer'] = johannes_get_footer_options();
        $johannes['ads'] = johannes_get_ads_options();

        $johannes = johannes_parse_args( $johannes, $defaults );
        $johannes = apply_filters( 'johannes_modify_templates_setup', $johannes );

        set_query_var( 'johannes', $johannes );

        //print_r( $johannes );
        //print_r( get_option( 'johannes_settings') );
    }
endif;



/**
 * Get default display options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_default_template_options' ) ):
    function johannes_get_default_template_options() {

        $args = array();

        $args['sidebar'] = array( 'position' => 'none' );
        $args['display'] = array(
            'header' => true,
            'footer' => true,
            'title' => true
        );

        return apply_filters( 'johannes_modify_default_template_options', $args );
    }
endif;


/**
 * Get single template options
 * Return single post params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_single_template_options' ) ):
    function johannes_get_single_template_options() {

        $meta = johannes_get_post_meta();

        $args = array();

        $args['layout'] = $meta['layout'];
        $args['sidebar'] = $meta['sidebar'];
        $args['category'] = johannes_get_option( 'single_cat' );
        $args['meta'] = johannes_get_option( 'single_meta' );
        $args['headline'] = johannes_get_option( 'single_headline' );
        $args['fimg'] = johannes_get_option( 'single_fimg' );
        $args['fimg_cap'] = johannes_get_option( 'single_fimg_cap' );
        $args['avatar'] = johannes_get_option( 'single_avatar' );
        $args['tags'] = johannes_get_option( 'single_tags' );
        $args['share'] = johannes_get_option( 'single_share' );
        $args['author'] = johannes_get_option( 'single_author' );
        $args['related'] = johannes_get_option( 'single_related' );
        $args['related_layout'] = johannes_get_option( 'related_layout' );

        if ( in_array( $args['layout'], array( '3', '4' ) ) ) {
            $args['cover'] = true;
        }

        return apply_filters( 'johannes_modify_single_template_options', $args );
    }
endif;


/**
 * Get page template options
 * Return page template params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_page_template_options' ) ):
    function johannes_get_page_template_options() {

        $meta = johannes_get_page_meta();

        $args = array();

        $args['layout'] = $meta['layout'];
        $args['sidebar'] = $meta['sidebar'];
        $args['fimg'] = johannes_get_option( 'page_fimg' );
        $args['fimg_cap'] = johannes_get_option( 'page_fimg_cap' );

        if ( in_array( $args['layout'], array( '3', '4' ) ) ) {
            $args['cover'] = true;
        }

        return apply_filters( 'johannes_modify_page_template_options', $args );
    }
endif;

/**
 * Get authors page template options
 * Return page template params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_authors_template_options' ) ):
    function johannes_get_authors_template_options() {

        $args = johannes_get_page_template_options();

        $args['authors_query_args'] = array(
            'fields'    => array( 'ID' ),
            'orderby' => 'post_count',
            'order' => 'DESC',
            'has_published_posts' => array( 'post' )
        );

        return apply_filters( 'johannes_modify_authors_template_options', $args );
    }
endif;


/**
 * Get archives options
 * Return archives params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_archive_template_options' ) ):
    function johannes_get_archive_template_options() {

        $args = array();

        $args['layout'] = johannes_get_option( 'archive_layout' );
        $args['loop'] = johannes_get_option( 'archive_loop' );
        $args['pagination'] = johannes_get_option( 'archive_pagination' );

        if ( johannes_loop_has_sidebar( $args['loop'] ) ) {
            $args['sidebar'] = array(
                'position' => johannes_get_option( 'archive_sidebar_position' ),
                'classic' => johannes_get_option( 'archive_sidebar_standard' ),
                'sticky' => johannes_get_option( 'archive_sidebar_sticky' )
            );
        }

        $archive = johannes_get_archive_content();

        if ( $archive ) {
            $args['archive_content'] = true;
            $args['archive_title'] = $archive['title'];
            $args['archive_description'] = johannes_get_option( 'archive_description' ) ? $archive['description'] : '';
            $args['archive_meta'] = johannes_get_option( 'archive_meta' ) ? $archive['meta'] : '';
            $args['archive_avatar'] = $archive['avatar'];
            $args['archive_subnav'] = $archive['subnav'];
        } else {
            $args['archive_content'] = false;
        }

        if ( in_array( $args['layout'], array( '2', '3' ) ) ) {
            $args['cover'] = true;
        }

        $args = apply_filters( 'johannes_modify_archive_template_options', $args );

        return $args;
    }
endif;

/**
 * Get category options
 * Return category params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_category_template_options' ) ):
    function johannes_get_category_template_options() {

        $meta = johannes_get_category_meta( get_queried_object_id() );

        $args = array();

        $args['layout'] = $meta['layout'];
        $args['loop'] = $meta['loop'];
        $args['pagination'] = $meta['pagination'];

        if ( johannes_loop_has_sidebar( $args['loop'] ) ) {
            $args['sidebar'] = array(
                'position' => $meta['sidebar']['position'],
                'classic' => $meta['sidebar']['classic'],
                'sticky' => $meta['sidebar']['sticky']
            );
        }

        $archive = johannes_get_archive_content();

        if ( $archive ) {
            $args['archive_content'] = true;
            $args['archive_title'] = $archive['title'];
            $args['archive_description'] = $meta['archive']['description'] ? $archive['description'] : '';
            $args['archive_meta'] = $meta['archive']['meta'] ? $archive['meta'] : '';
        } else {
            $args['archive_content'] = false;
        }


        if ( in_array( $args['layout'], array( '2', '3' ) ) ) {
            $args['cover'] = true;
        }

        return apply_filters( 'johannes_modify_category_template_options', $args );

    }
endif;

/**
 * Get front page options
 * Return hront page template params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_front_page_template_options' ) ):
    function johannes_get_front_page_template_options() {
        global $paged;

        $args = array();

        $args['front_page_sections'] = !is_paged() ? johannes_get_option( 'front_page_sections' ) : array( 'classic' );

        //Classic section
        $args['display_title'] = johannes_get_option( 'front_page_classic_display_title' );
        $args['pagination'] = johannes_get_option( 'front_page_pagination' ) != 'none' ? johannes_get_option( 'front_page_pagination' ) : false;
        $args['loop'] = johannes_get_option( 'front_page_loop' );
        $ppp = johannes_get_option( 'front_page_ppp' ) == 'inherit' ? get_option( 'posts_per_page' ) : absint( johannes_get_option( 'front_page_ppp_num' ) );

        $args['query_args'] = array(
            'post_type' => 'post',
            'posts_per_page' => $ppp,
            'ignore_sticky_posts' => 1
        );

        if ( $args['pagination'] ) {
            $paged = 'posts' == get_option( 'show_on_front' ) ? absint( get_query_var( 'paged' ) ) : absint( get_query_var( 'page' ) );
            $args['query_args']['paged'] = $paged;
        }

        $tax_query = array();

        $cat = johannes_get_option( 'front_page_cat' );

        if ( !empty( $cat ) ) {
            $tax_query[] = array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $cat
            );
        }

        $tag = johannes_get_option( 'front_page_tag' );

        if ( !empty( $tag ) ) {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $tag
            );
        }

        if ( !empty( $tax_query ) ) {
            $args['query_args']['tax_query'] = $tax_query;
        }


        if ( johannes_loop_has_sidebar( $args['loop'] ) ) {

            $args['sidebar'] = array(
                'position' => johannes_get_option( 'front_page_sidebar_position' ),
                'classic' => johannes_get_option( 'front_page_sidebar_standard' ),
                'sticky' => johannes_get_option( 'front_page_sidebar_sticky' )
            );

        }

        //Featured section
        $args['fa_display_title'] = johannes_get_option( 'front_page_fa_display_title' );
        $args['fa_loop'] = johannes_get_option( 'front_page_fa_loop' );
        $fa_params = johannes_get_featured_layouts_map();

        $fa_ppp = isset( $fa_params[$args['fa_loop']]['ppp'] ) ?  $fa_params[$args['fa_loop']]['ppp'] : absint( johannes_get_option( 'front_page_fa_ppp' ) );
        $fa_orderby = johannes_get_option( 'front_page_fa_orderby' );
        $fa_order = $fa_orderby == 'title' ? 'ASC' : 'DESC';

        $args['fa_query_args'] = array(
            'post_type' => 'post',
            'posts_per_page' => $fa_ppp,
            'orderby' => $fa_orderby,
            'order' => $fa_order,
            'ignore_sticky_posts' => 1
        );

        $fa_tax_query = array();

        $fa_cat = johannes_get_option( 'front_page_fa_cat' );

        if ( !empty( $fa_cat ) ) {
            $fa_tax_query[] = array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $fa_cat
            );
        }

        $fa_tag = johannes_get_option( 'front_page_fa_tag' );

        if ( !empty( $fa_tag ) ) {
            $fa_tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $fa_tag
            );
        }

        if ( !empty( $fa_tax_query ) ) {
            $args['fa_query_args']['tax_query'] = $fa_tax_query;
        }

        if ( johannes_get_option( 'front_page_fa_unique' ) ) {
            $fa_query = new WP_Query( $args['fa_query_args'] );
            if ( !empty( $fa_query ) ) {
                $fa_posts_ids = wp_list_pluck( $fa_query->posts, 'ID' );
                $args['query_args']['post__not_in'] = $fa_posts_ids;
            }

        }

        //Welcome section
        $args['wa_display_title'] = johannes_get_option( 'front_page_wa_display_title' );
        $args['wa_layout'] = johannes_get_option( 'front_page_wa_layout' );
        $args['wa_cta'] =  johannes_get_option( 'front_page_wa_cta' );
        $args['wa_cta_url'] = johannes_get_option( 'front_page_wa_cta_url' );
        $args['wa_img'] = '';

        if ( johannes_get_option( 'front_page_wa_img', 'image' ) ) {

            $wa_img_id = johannes_get_image_id_by_url( johannes_get_option( 'front_page_wa_img', 'image' ) );

            if ( !empty( $wa_img_id ) ) {
                $args['wa_img'] = wp_get_attachment_image( $wa_img_id, 'johannes-wa-'.$args['wa_layout'] );
            } else {
                $args['wa_img'] = '<img src="'.esc_url( johannes_get_option( 'front_page_wa_img', 'image' ) ).'" class="'.esc_attr( 'size-johannes-wa-' . $args['wa_layout'] ).'" />';
            }
        }

        if ( !empty( $args['front_page_sections']) && $args['front_page_sections'][0] == 'welcome' && !$args['wa_display_title'] && in_array( $args['wa_layout'], array( '3', '4' ) ) ) {
            $args['cover'] = true;
        }

        if ( !empty( $args['front_page_sections']) && $args['front_page_sections'][0] == 'featured' && !$args['fa_display_title'] && in_array( $args['fa_loop'], array( '1', '2' ) ) ) {
            $args['cover'] = true;
        }

        $args = apply_filters( 'johannes_modify_front_page_template_options', $args );

        return $args;
    }
endif;


/**
 * Get 404 template options
 * Return page template params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_404_template_options' ) ):
    function johannes_get_404_template_options() {

        $args = array();

        $args['title'] = __johannes( '404_title' );
        $args['text'] = __johannes( '404_text' );

        return apply_filters( 'johannes_modify_404_template_options', $args );
    }
endif;


/**
 * Get page template options
 * Return page template params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_blank_template_options' ) ):
    function johannes_get_blank_template_options() {
        $args = array();

        $args['display'] = array(
            'header' => false,
            'footer' => false,
            'title' => false
        );

        return apply_filters( 'johannes_modify_blank_template_options', $args );
    }
endif;


/**
 * Get header options
 * Return header params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_header_options' ) ):
    function johannes_get_header_options() {

        $args = array();

        if ( johannes_get_option( 'header_top' ) ) {
            $args['top'] = array(
                'l' =>  johannes_get_option( 'header_top_l' ),
                'c' =>  johannes_get_option( 'header_top_c' ),
                'r' =>  johannes_get_option( 'header_top_r' ),
            );
        }

        $args['actions'] = johannes_get_option( 'header_actions' );

        if (  johannes_get_option( 'woocommerce_cart_force' ) &&  johannes_is_woocommerce_page() && !in_array( 'cart', $args['actions'] ) ) {
            $args['actions'][] = 'cart';
        }

        if ( !in_array( 'hamburger', $args['actions'] ) ) {
            $args['actions'][] = 'hamburger';
        }

        $args['actions_l'] = johannes_get_option( 'header_actions_l' );
        $args['actions_r'] = johannes_get_option( 'header_actions_r' );

        if ( !in_array( 'hamburger', $args['actions_l'] ) && !in_array( 'hamburger', $args['actions_r'] ) ) {
            $args['actions_r'][] = 'hamburger';
        }

        $args['layout'] = johannes_get_option( 'header_layout' );
        $args['nav'] = johannes_get_option( 'header_main_nav' );

        if( $args['layout'] == 4 ){
            $args['nav'] = false;
        }

        $args['sticky'] = johannes_get_option( 'header_sticky' );
        $args['sticky_layout'] = johannes_get_option( 'header_sticky_layout' );
        $args['sticky_single'] = johannes_get_option( 'header_sticky' ) && johannes_get_option( 'header_sticky_contextual' ) && is_single();

        $args['actions_responsive'] = johannes_get_option( 'header_actions_responsive' );

        $args = apply_filters( 'johannes_modify_header_options', $args );

        return $args;
    }
endif;

/**
 * Get footer options
 * Return header params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_footer_options' ) ):
    function johannes_get_footer_options() {
        $args = array();

        $args['instagram'] = !is_404() && johannes_get_option( 'footer_instagram' ) ? johannes_get_option( 'footer_instagram_username' ) : false;

        if ( !is_front_page() && johannes_get_option( 'footer_instagram_front' ) ) {
            $args['instagram'] = false;
        }

        $args['widgets'] = johannes_get_option( 'footer_widgets' ) ? explode( '-', johannes_get_option( 'footer_widgets_layout' ) ) : false;
        $args['copyright'] = johannes_get_option( 'footer_copyright' ) ? str_replace( '{current_year}', date( 'Y' ), johannes_get_option( 'footer_copyright' ) ) : '';
        $args['go_to_top'] = johannes_get_option( 'go_to_top' );

        if ( johannes_get_option( 'popup' ) && function_exists( 'has_block' ) && ( is_single()|| is_page() ) ) {

            $id = get_the_ID();

            if ( has_block( 'image', $id ) || has_block( 'gallery', $id ) ) {
                $args['popup'] = true;
            }
        }

        $args = apply_filters( 'johannes_modify_footer_options', $args );

        return $args;
    }
endif;


/**
 * Get ads options
 * Return ad slots content based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_ads_options' ) ):
    function johannes_get_ads_options() {
        $args = array();

        if ( !is_404() ) {

            $args['header'] = johannes_get_option( 'ad_header' ); 
            $args['above_archive'] = johannes_get_option( 'ad_above_archive' );
            $args['above_singular'] = johannes_get_option( 'ad_above_singular' );
            $args['above_footer'] = johannes_get_option( 'ad_above_footer' );
            $args['between_posts'] = johannes_get_option( 'ad_between_posts' );

            $args['between_position'] = !empty( $args['between_posts'] ) ? absint( johannes_get_option( 'ad_between_position' ) ) - 1 : false;

            if ( is_page() && in_array( get_the_ID(), johannes_get_option( 'ad_exclude' ) ) ) {
                $args = array();
            }
        }

        $args = apply_filters( 'johannes_modify_ads_options', $args );

        return $args;
    }
endif;


/**
 * Get woocommerce template options
 * Return woocommerce template params based on theme options
 *
 * @since  1.0
 * @return array
 */

if ( !function_exists( 'johannes_get_woocommerce_template_options' ) ):
    function johannes_get_woocommerce_template_options() {

        $args = array();

        $args['sidebar'] = array(
            'position' => johannes_get_option( 'woocommerce_sidebar_position' ),
            'classic' => johannes_get_option( 'woocommerce_sidebar_standard' ),
            'sticky' => johannes_get_option( 'woocommerce_sidebar_sticky' )
        );

        $args['layout'] = '1';

        return apply_filters( 'johannes_modify_woocommerce_template_options', $args );
    }
endif;
