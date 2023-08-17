<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package ePortfolio
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function eportfolio_body_classes( $classes ) {
    global $post;
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}
    $global_layout = eportfolio_get_option( 'global_layout' );
	
    if ( $post && is_singular() ) {
        $post_options = get_post_meta( $post->ID, 'eportfolio-meta-select-layout', true );

        if (empty( $post_options ) ) {
            $global_layout = esc_attr( eportfolio_get_option('global_layout') );
        } else{
            $global_layout = esc_attr($post_options);
        }
    }

    if ( is_page_template( 'page-template/portfolio-template.php' ) ) {
      if ( (eportfolio_get_option('enable_portfolio_masonry_section') == 1) && (eportfolio_get_option('enable_portfolio_widget_sidebar') == 1) ) { 
        $classes[]= 'twp-page-template-portfolio-with-widget';
      } elseif ((eportfolio_get_option('enable_portfolio_masonry_section') == 1) && (eportfolio_get_option('enable_portfolio_widget_sidebar') != 1)) {
        $classes[]= 'twp-page-template-portfolio-only-gallery';
      }elseif ((eportfolio_get_option('enable_portfolio_masonry_section') != 1) && (eportfolio_get_option('enable_portfolio_widget_sidebar') == 1)) {
        $classes[]= 'twp-page-template-portfolio-only-widget';
      }
    }

    if ($global_layout == 'left-sidebar') {
        $classes[]= 'left-sidebar';
    }
    elseif ($global_layout == 'no-sidebar') {
        $classes[]= 'no-sidebar';
    }
    else{
        $classes[]= 'right-sidebar';
    }
	return $classes;
}
add_filter( 'body_class', 'eportfolio_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function eportfolio_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'eportfolio_pingback_header' );

if ( ! function_exists( 'eportfolio_display_posts_navigation' ) ) :
  /**
   * Display Pagination.
   *
   * @since 1.0.0
   */
  function eportfolio_display_posts_navigation() {
        $pagination_type = eportfolio_get_option( 'pagination_type', true );
        switch ( $pagination_type ) {
            case 'default':
                the_posts_navigation();
                break;
            case 'numeric':
                the_posts_pagination();
                break;
            default:
                break;
        }
    return;
  }
endif;
add_action( 'eportfolio_posts_navigation', 'eportfolio_display_posts_navigation' );


if ( ! function_exists( 'eportfolio_archive_title' ) ) :
    /**
     * Modifies post archive titles
     */
    function eportfolio_archive_title( $title) {
        if ( is_category() ) {
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>';
        } elseif ( is_post_type_archive() ) {
            $title = post_type_archive_title( '', false );
        } elseif ( is_tax() ) {
            $title = single_term_title( '', false );
        }
        return $title;
    }
endif;
add_filter( 'get_the_archive_title', 'eportfolio_archive_title' );


if( ! function_exists( 'eportfolio_recommended_plugins' ) ) :

  /**
   * Recommended plugins
   *
   */
  function eportfolio_recommended_plugins(){
      $eportfolio_plugins = array(
        array(
            'name'     => __('Elementor Page Builder', 'eportfolio'),
            'slug'     => 'elementor',
            'required' => false,
        ),
        array(
          'name'     => __('Contact Form 7', 'eportfolio'),
          'slug'     => 'contact-form-7',
          'required' => false,
        ),
        array(
          'name'      => esc_html__('Demo Import Kit','eportfolio'),
          'slug'      => 'demo-import-kit',
          'required'  => false,
        ),
        array(
          'name'      => esc_html__('Themeinwp Import Companion','eportfolio'),
          'slug'      => 'themeinwp-import-companion',
          'required'  => false,
        ),
      );
      $eportfolio_plugins_config = array(
          'dismissable' => true,
      );
      
      tgmpa( $eportfolio_plugins, $eportfolio_plugins_config );
  }
endif;
add_action( 'tgmpa_register', 'eportfolio_recommended_plugins' );

/**
 * OCDI support.
 *
 * @package eportfolio
 */

/*Disable PT branding.*/
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
/**
 * OCDI after import.
 *
 * @since 1.0.0
 */

function eportfolio_after_import_setup() {
    // Assign front page and posts page (blog page).
    $front_page_id = null;
    $blog_page_id  = null;

    $front_page = get_page_by_title( 'Portfolio' );

    if ( $front_page ) {
        if ( is_array( $front_page ) ) {
            $first_page = array_shift( $front_page );
            $front_page_id = $first_page->ID;
        } else {
            $front_page_id = $front_page->ID;
        }
    }

    $blog_page = get_page_by_title( 'Blog' );

    if ( $blog_page ) {
        if ( is_array( $blog_page ) ) {
            $first_page = array_shift( $blog_page );
            $blog_page_id = $first_page->ID;
        } else {
            $blog_page_id = $blog_page->ID;
        }
    }

    if ( $front_page_id && $blog_page_id ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $front_page_id );
        update_option( 'page_for_posts', $blog_page_id );
    }

    // Assign navigation menu locations.
    $menu_location_details = array(
        'primary-nav' => 'primary-menu',
        'social-nav' => 'social-menu',
    );

    if ( ! empty( $menu_location_details ) ) {
        $navigation_settings = array();
        $current_navigation_menus = wp_get_nav_menus();
        if ( ! empty( $current_navigation_menus ) && ! is_wp_error( $current_navigation_menus ) ) {
            foreach ( $current_navigation_menus as $menu ) {
                foreach ( $menu_location_details as $location => $menu_slug ) {
                    if ( $menu->slug === $menu_slug ) {
                        $navigation_settings[ $location ] = $menu->term_id;
                    }
                }
            }
        }
        set_theme_mod( 'nav_menu_locations', $navigation_settings );
    }
}
add_action( 'pt-ocdi/after_import', 'eportfolio_after_import_setup' );



if( !function_exists( 'eportfolio_add_sub_toggles_to_main_menu' ) ) :

    function eportfolio_add_sub_toggles_to_main_menu( $args, $item, $depth ) {

        // Add sub menu toggles to the Expanded Menu with toggles.
        if( $args->theme_location == 'primary-nav' ){

            // Wrap the menu item link contents in a div, used for positioning.
            $args->before = '<div class="submenu-wrapper">';
            $args->after  = '';

            // Add a toggle to items with children.
            if( in_array( 'menu-item-has-children', $item->classes, true ) ){

                $toggle_target_string = '.menu-item.menu-item-' . $item->ID . ' > .sub-menu';
                // Add the sub menu toggle.
                $args->after .= '<button class="theme-btn-toggle submenu-toggle" data-toggle-target="' . $toggle_target_string . '" data-toggle-type="slidetoggle" data-toggle-duration="250" aria-expanded="false"><span class="btn__content" tabindex="-1"><span class="screen-reader-text">' . __( 'Show sub menu', 'eportfolio' ) . '</span><span class="fa fa-chevron-down"></span></span></button>';

            }

            // Close the wrapper.
            $args->after .= '</div>';

            // Add sub menu icons to the primary menu without toggles.
        }

        return $args;

    }

endif;

add_filter( 'nav_menu_item_args', 'eportfolio_add_sub_toggles_to_main_menu', 10, 3 );

