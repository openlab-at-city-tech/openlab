<?php

/**
 * Get the list of available post listing layouts
 *
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_post_layouts' ) ):
	function johannes_get_post_layouts( $filter = array() ) {

		$layouts = johannes_get_layouts_map();

		if ( !empty( $filter ) ) {
			foreach ( $layouts as $id => $layout ) {
				foreach ( $filter as $what => $value ) {
					if ( ( isset( $layout[$what] ) && $layout[$what] == $value ) ||  ( !isset( $layout[$what] ) && $value == false ) ) {
						continue;
					}

					unset( $layouts[$id] );
				}
			}
		}

		$layouts = apply_filters( 'johannes_modify_post_layouts', $layouts );

		return $layouts;

	}
endif;


/**
 * Get the list of available featured post layouts
 *
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_featured_layouts' ) ):
	function johannes_get_featured_layouts( $filter = array() ) {

		$layouts = johannes_get_featured_layouts_map();

		if ( !empty( $filter ) ) {
			foreach ( $layouts as $id => $layout ) {

				foreach ( $filter as $what => $value ) {

					if ( ( isset( $layout[$what] ) && $layout[$what] == $value ) || ( !isset( $layout[$what] ) && $value == false ) ) {
						continue;
					}

					unset( $layouts[$id] );

				}
			}
		}

		$layouts = apply_filters( 'johannes_modify_featured_layouts', $layouts );

		return $layouts;

	}
endif;

/**
 * Get the list of welcome area layouts
 *
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_welcome_layouts' ) ):
	function johannes_get_welcome_layouts() {

		$layouts = array();

		$layouts['1'] = array( 'alt' => esc_html__( 'Layout 1', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/welcome_layout_1.svg' ) );
		$layouts['2'] = array( 'alt' => esc_html__( 'Layout 2', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/welcome_layout_2.svg' ) );
		$layouts['3'] = array( 'alt' => esc_html__( 'Layout 3', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/welcome_layout_3.svg' ) );
		$layouts['4'] = array( 'alt' => esc_html__( 'Layout 4', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/welcome_layout_4.svg' ) );

		$layouts = apply_filters( 'johannes_modify_welcome_layouts', $layouts );

		return $layouts;

	}
endif;



/**
 * Get the list of header layouts
 *
 * @param bool    $inherit Wheter to display "inherit" option
 * @param bool    $none    Wheter to display "none" option
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_header_layouts' ) ):
	function johannes_get_header_layouts( $exclude = array() ) {

		$layouts = array();

		$layouts['1'] = array( 'alt' => esc_html__( 'Layout 1', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_1.svg' ) );
		$layouts['2'] = array( 'alt' => esc_html__( 'Layout 2', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_2.svg' ) );
		$layouts['3'] = array( 'alt' => esc_html__( 'Layout 3', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_3.svg' ) );
		$layouts['4'] = array( 'alt' => esc_html__( 'Layout 4', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_4.svg' ) );
		$layouts['5'] = array( 'alt' => esc_html__( 'Layout 5', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_5.svg' ) );
		$layouts['6'] = array( 'alt' => esc_html__( 'Layout 6', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_6.svg' ) );
		$layouts['7'] = array( 'alt' => esc_html__( 'Layout 7', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_7.svg' ) );
		$layouts['8'] = array( 'alt' => esc_html__( 'Layout 8', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_8.svg' ) );
		$layouts['9'] = array( 'alt' => esc_html__( 'Layout 9', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_9.svg' ) );
		$layouts['10'] = array( 'alt' => esc_html__( 'Layout 10', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/header_layout_10.svg' ) );

		if ( !empty( $exclude ) ) {
			foreach ( $exclude as $element ) {
				if ( isset( $layouts[$element] ) ) {
					unset( $layouts[$element] );
				}
			}
		}

		$layouts = apply_filters( 'johannes_modify_header_layouts', $layouts );

		return $layouts;

	}
endif;


/**
 * Get meta options
 *
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_meta_opts' ) ):
	function johannes_get_meta_opts() {

		$options = array();
		$options['date'] = esc_html__( 'Date', 'johannes' );
		$options['author'] = esc_html__( 'Author', 'johannes' );
		$options['rtime'] = esc_html__( 'Reading time', 'johannes' );
		$options['comments'] = esc_html__( 'Comments', 'johannes' );

		$options = apply_filters( 'johannes_modify_meta_opts', $options );

		return $options;
	}
endif;



/**
 * Get header actions options
 *
 * @return array List of available options
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_header_main_area_actions' ) ):
	function johannes_get_header_main_area_actions( $exclude = array() ) {
		$actions = array(
			'search' => esc_html__( 'Search form', 'johannes' ),
			'search-modal' => esc_html__( 'Search button', 'johannes' ),
			'social' => esc_html__( 'Social menu', 'johannes' ),
			'social-modal' => esc_html__( 'Social button', 'johannes' ),
		);

		if ( johannes_is_woocommerce_active() ) {
			$actions['cart'] = esc_html__( 'WooCommerce cart', 'johannes' );
		}

		$actions['hamburger'] = esc_html__( 'Hamburger menu (hidden sidebar)', 'johannes' );

		if ( !empty( $exclude ) ) {
			foreach ( $exclude as $element ) {
				if ( isset( $actions[$element] ) ) {
					unset( $actions[$element] );
				}
			}
		}

		$actions = apply_filters( 'johannes_modify_header_main_area_actions', $actions );

		return $actions;
	}
endif;


/**
 * Get header top elements options
 *
 * @return array List of available options
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_header_top_elements' ) ):
	function johannes_get_header_top_elements( $exclude = array() ) {
		$actions = array(
			'menu-secondary-1' => esc_html__( 'Secondary menu 1', 'johannes' ),
			'menu-secondary-2' => esc_html__( 'Secondary menu 2', 'johannes' ),
			'date'           => esc_html__( 'Date', 'johannes' ),
			'search' => esc_html__( 'Search form', 'johannes' ),
			'search-modal' => esc_html__( 'Search button', 'johannes' ),
			'social' => esc_html__( 'Social menu', 'johannes' ),
			'social-modal' => esc_html__( 'Social button', 'johannes' )
		);

		if ( johannes_is_woocommerce_active() ) {
			$actions['cart'] = esc_html__( 'WooCommerce cart', 'johannes' );
		}

		if ( !empty( $exclude ) ) {
			foreach ( $exclude as $element ) {
				if ( isset( $actions[$element] ) ) {
					unset( $actions[$element] );
				}
			}
		}

		$actions = apply_filters( 'johannes_modify_header_top_elements', $actions );

		return $actions;
	}
endif;



/**
 * Get the list of available pagination types
 *
 * @param bool    $ihnerit Whether you want to include "inherit" option in the list
 * @param bool    $none    Whether you want to add "none" option ( to set layout to "off")
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_pagination_layouts' ) ):
	function johannes_get_pagination_layouts( $inherit = false, $none = false ) {

		$layouts = array();

		if ( $inherit ) {
			$layouts['inherit'] = array( 'alt' => esc_html__( 'Inherit', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/inherit.svg' ) );
		}

		if ( $none ) {
			$layouts['none'] = array( 'alt' => esc_html__( 'None', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/pagination_none.svg' ) );
		}

		$layouts['numeric'] = array( 'alt' => esc_html__( 'Numeric pagination links', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/pagination_numeric.svg' ) );
		$layouts['prev-next'] = array( 'alt' => esc_html__( 'Prev/Next page links', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/pagination_prevnext.svg' ) );
		$layouts['load-more'] = array( 'alt' => esc_html__( 'Load more button', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/pagination_loadmore.svg' )  );
		$layouts['infinite-scroll'] = array( 'alt' => esc_html__( 'Infinite scroll', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/pagination_infinite.svg' ) );

		$layouts = apply_filters( 'johannes_modify_pagination_layouts', $layouts );

		return $layouts;
	}
endif;


/**
 * Get footer layouts options
 *
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_footer_layouts' ) ):
	function johannes_get_footer_layouts() {
		$layouts = array(
			'12'      => array( 'alt' => '12', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_12.svg' ) ),
			'6-6'     => array( 'alt' => '6-6', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_6_6.svg' ) ),
			'4-4-4'   => array( 'alt' => '4-4-4', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_4_4_4.svg' ) ),
			'3-3-3-3'   => array( 'alt' => '3-3-3-3', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_3_3_3_3.svg' ) ),
			'8-4'   => array( 'alt' => '8-4', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_8_4.svg' ) ),
			'4-8'   => array( 'alt' => '4-8', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_4_8.svg' ) ),
			'6-3-3'   => array( 'alt' => '6-3-3', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_6_3_3.svg' ) ),
			'3-3-6'   => array( 'alt' => '3-3-6', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_3_3_6.svg' ) ),
			'3-6-3'   => array( 'alt' => '3-6-3', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_3_6_3.svg' ) ),
			'3-4-5'   => array( 'alt' => '3-4-5', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_3_4_5.svg' ) ),
			'5-4-3'   => array( 'alt' => '5-4-3', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_5_4_3.svg' ) ),
			'3-5-4'   => array( 'alt' => '3-5-4', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_3_5_4.svg' ) ),
			'4-5-3'   => array( 'alt' => '4-5-3', 'src' => get_parent_theme_file_uri( '/assets/img/admin/footer_4_5_3.svg' ) ),
		);

		$layouts = apply_filters( 'johannes_modify_footer_layouts', $layouts );

		return $layouts;
	}
endif;


/**
 * Get image ratio options
 *
 * @param bool    $johannes Wheter to include "johannes (not cropped)" ratio option
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_image_ratio_opts' ) ):
	function johannes_get_image_ratio_opts( $original = false ) {

		$options = array();

		if ( $original ) {
			$options['johannes'] = esc_html__( 'Original (ratio as uploaded - do not crop)', 'johannes' );
		}

		$options['21_9'] = esc_html__( '21:9', 'johannes' );
		$options['16_9'] = esc_html__( '16:9', 'johannes' );
		$options['3_2'] = esc_html__( '3:2', 'johannes' );
		$options['4_3'] = esc_html__( '4:3', 'johannes' );
		$options['1_1'] = esc_html__( '1:1 (square)', 'johannes' );
		$options['3_4'] = esc_html__( '3:4', 'johannes' );
		$options['custom'] = esc_html__( 'Custom ratio', 'johannes' );

		$options = apply_filters( 'johannes_modify_ratio_opts', $options );
		return $options;
	}
endif;

/**
 * Get the list of available single post layouts
 *
 * @param bool    $ihnerit Whether you want to add "inherit" option
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_single_layouts' ) ):
	function johannes_get_single_layouts( $inherit = false ) {

		$layouts = array();

		if ( $inherit ) {
			$layouts['inherit'] = array( 'alt' => esc_html__( 'Inherit', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/inherit.png' ) );
		}

		$layouts['1'] = array( 'alt' => esc_html__( 'Layout 1', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/single_layout_1.svg' ) );
		$layouts['2'] = array( 'alt' => esc_html__( 'Layout 2', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/single_layout_2.svg' ) );
		$layouts['3'] = array( 'alt' => esc_html__( 'Layout 3', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/single_layout_3.svg' ) );
		$layouts['4'] = array( 'alt' => esc_html__( 'Layout 4', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/single_layout_4.svg' ) );
		$layouts['5'] = array( 'alt' => esc_html__( 'Layout 5', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/single_layout_5.svg' ) );

		$layouts = apply_filters( 'johannes_modify_single_layouts', $layouts );

		return $layouts;
	}
endif;


/**
 * Get the list of available page layouts
 *
 * @param bool    $ihnerit Whether you want to add "inherit" option
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_page_layouts' ) ):
	function johannes_get_page_layouts( $inherit = false ) {

		$layouts = array();

		if ( $inherit ) {
			$layouts['inherit'] = array( 'alt' => esc_html__( 'Inherit', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/inherit.png' ) );
		}

		$layouts['1'] = array( 'alt' => esc_html__( 'Layout 1', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/page_layout_1.svg' ) );
		$layouts['2'] = array( 'alt' => esc_html__( 'Layout 2', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/page_layout_2.svg' ) );
		$layouts['3'] = array( 'alt' => esc_html__( 'Layout 3', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/page_layout_3.svg' ) );
		$layouts['4'] = array( 'alt' => esc_html__( 'Layout 4', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/page_layout_4.svg' ) );

		$layouts = apply_filters( 'johannes_modify_page_layouts', $layouts );

		return $layouts;

	}
endif;


/**
 * Get the list of available archive layouts
 *
 * @param bool    $ihnerit Whether you want to add "inherit" option
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_archive_layouts' ) ):
	function johannes_get_archive_layouts( $inherit = false ) {

		$layouts = array();

		if ( $inherit ) {
			$layouts['inherit'] = array( 'alt' => esc_html__( 'Inherit', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/inherit.png' ) );
		}

		$layouts['1'] = array( 'alt' => esc_html__( 'Layout 1', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/archive_layout_1.svg' ) );
		$layouts['2'] = array( 'alt' => esc_html__( 'Layout 2', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/archive_layout_2.svg' ) );
		$layouts['3'] = array( 'alt' => esc_html__( 'Layout 3', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/archive_layout_3.svg' ) );

		$layouts = apply_filters( 'johannes_modify_archive_layouts', $layouts );

		return $layouts;

	}
endif;


/**
 * Get the list of available sidebar layouts
 *
 * You may have left sidebar, right sidebar or no sidebar
 *
 * @param bool    $ihnerit Whether you want to include "inherit" option in the list
 * @return array List of available sidebar layouts
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_sidebar_layouts' ) ):
	function johannes_get_sidebar_layouts( $inherit = false, $none = false ) {

		$layouts = array();

		if ( $inherit ) {
			$layouts['inherit'] = array( 'alt' => esc_html__( 'Inherit', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/inherit.svg' ) );
		}

		if ( $none ) {
			$layouts['none'] = array( 'alt' => esc_html__( 'None', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/sidebar_none.svg' ) );
		}

		$layouts['left'] = array( 'alt' => esc_html__( 'Left sidebar', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/sidebar_left.svg' ) );
		$layouts['right'] = array( 'alt' => esc_html__( 'Right sidebar', 'johannes' ), 'src' => get_parent_theme_file_uri( '/assets/img/admin/sidebar_right.svg' ) );

		$layouts = apply_filters( 'johannes_modify_sidebar_layouts', $layouts );

		return $layouts;
	}
endif;


/**
 * Get the list of registered sidebars
 *
 * @param bool    $ihnerit Whether you want to include "inherit" option in the list
 * @return array Returns list of available sidebars
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_sidebars_list' ) ):
	function johannes_get_sidebars_list( $inherit = false ) {

		$sidebars = array();

		if ( $inherit ) {
			$sidebars['inherit'] = esc_html__( 'Inherit', 'johannes' );
		}

		$sidebars['none'] = esc_html__( 'None', 'johannes' );

		global $wp_registered_sidebars;

		if ( !empty( $wp_registered_sidebars ) ) {

			foreach ( $wp_registered_sidebars as $sidebar ) {
				$sidebars[ $sidebar['id'] ] = $sidebar['name'];
			}

		}
		//Get sidebars from wp_options if global var is not loaded yet
		$fallback_sidebars = get_option( 'johannes_registered_sidebars' );
		if ( !empty( $fallback_sidebars ) ) {
			foreach ( $fallback_sidebars as $sidebar ) {
				if ( !array_key_exists( $sidebar['id'], $sidebars ) ) {
					$sidebars[ $sidebar['id'] ] = $sidebar['name'];
				}
			}
		}

		//Check for theme additional sidebars
		$custom_sidebars = johannes_get_option( 'sidebars' );

		if ( $custom_sidebars ) {
			foreach ( $custom_sidebars as $k => $sidebar ) {
				if ( is_numeric( $k ) && !array_key_exists( 'johannes_sidebar_' . $k, $sidebars ) ) {
					$sidebars[ 'johannes_sidebar_' . $k ] = $sidebar['name'];
				}
			}
		}

		//Do not display footer sidebars for selection
		unset( $sidebars['johannes_sidebar_footer_1'] );
		unset( $sidebars['johannes_sidebar_footer_2'] );
		unset( $sidebars['johannes_sidebar_footer_3'] );
		unset( $sidebars['johannes_sidebar_footer_4'] );

		//Do not display hidden sidebar for selection
		unset( $sidebars['johannes_sidebar_hidden'] );

		$sidebars = apply_filters( 'johannes_modify_sidebars_list', $sidebars );

		return $sidebars;
	}
endif;


/**
 * Get the list of registered sidebars
 *
 * @param bool    $ihnerit Whether you want to include "inherit" option in the list
 * @return array Returns list of available sidebars
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_menus_list' ) ):
	function johannes_get_menus_list( ) {

		$menus = array();

		$menus['none'] = esc_html__( 'None', 'johannes' );

		$registered_menus = get_registered_nav_menus();

		if ( !empty( $registered_menus ) ) {
			foreach ( $registered_menus as $id => $menu ) {
				$menus[$id] = $menu;
			}

		}

		//Get menus from wp_options if global var is not loaded yet
		$fallback_menus = get_option( 'johannes_registered_menus' );

		//print_r( $fallback_menus );

		if ( !empty( $fallback_menus ) ) {
			foreach ( $fallback_menus as $id => $menu ) {
				if ( !array_key_exists( $id, $menus ) ) {
					$menus[ $id ] = $menu;
				}
			}
		}



		$menus = apply_filters( 'johannes_modify_menus_list', $menus );

		return $menus;
	}
endif;

/**
 * Get the list of available options for post ordering
 *
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_post_order_opts' ) ) :
	function johannes_get_post_order_opts() {

		$options = array(
			'date'          => esc_html__( 'Date', 'johannes' ),
			'comment_count' => esc_html__( 'Number of comments', 'johannes' ),
			'title'         => esc_html__( 'Title (alphabetically)', 'johannes' )
		);

		$options = apply_filters( 'johannes_modify_post_order_opts', $options );

		return $options;
	}
endif;


/**
 * Get the list of available options for background of an element
 *
 * @param bool    $none wheter to include "none" option
 * @return array List of available options
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_background_opts' ) ) :
	function johannes_get_background_opts( $none = false ) {
		$options = array();

		if ( $none ) {
			$options['none'] = esc_html__( 'None (transparent)', 'johannes' );
		}

		$options['alt-1'] = esc_html__( 'Alt background 1', 'johannes' );
		$options['alt-2'] = esc_html__( 'Alt background 2', 'johannes' );

		$options = apply_filters( 'johannes_modify_background_opts', $options );

		return $options;
	}
endif;



/**
 * Get the list of available options to filter posts by format
 *
 * @return array List of available post formats
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_post_format_opts' ) ) :
	function johannes_get_post_format_opts() {

		$options = array();
		$options['standard'] = esc_html__( 'Standard', 'johannes' );

		$formats = get_theme_support( 'post-formats' );
		if ( !empty( $formats ) && is_array( $formats[0] ) ) {
			foreach ( $formats[0] as $format ) {
				$options[$format] = ucfirst( $format );
			}
		}

		$options['0'] = esc_html__( 'All', 'johannes' );

		$options = apply_filters( 'johannes_modify_post_format_opts', $options );
		return $options;
	}
endif;


/**
 * Get the list of available user roles
 *
 * @return array List of available user roles
 * @since  1.0
 */

if ( !function_exists( 'johannes_get_user_role_opts' ) ) :
	function johannes_get_user_role_opts() {

		$options = array();

		$data = wp_roles();

		foreach ( $data->roles as $id => $role ) {
			$options[$id] = $role['name'];
		}

		$options = apply_filters( 'johannes_modify_user_role_opts', $options );
		return $options;
	}
endif;


/**
 * Get related plugins
 *
 * Check if Yet Another Related Posts Plugin (YARPP) or Contextual Related Posts or WordPress Related Posts or Jetpack by WordPress.com is active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_related_posts_plugins' ) ):
	function johannes_get_related_posts_plugins() {
		$related_plugins['default'] = esc_html__( 'Built-in (Johannes) related posts', 'johannes' );
		$related_plugins['yarpp'] = esc_html__( 'Yet Another Related Posts Plugin (YARPP)', 'johannes' );
		$related_plugins['crp'] = esc_html__( 'Contextual Related Posts', 'johannes' );
		$related_plugins['wrpr'] = esc_html__( 'WordPress Related Posts', 'johannes' );
		$related_plugins['jetpack'] = esc_html__( 'Jetpack by WordPress.com', 'johannes' );

		return $related_plugins;
	}
endif;


/**
 * Get breadcrumbs by options
 *
 * Check breadcrumbs support depending on witch plugins are active
 *
 * @return bool
 * @since  1.0
 */
if ( !function_exists( 'johannes_get_breadcrumbs_options' ) ):
	function johannes_get_breadcrumbs_options() {

		$options['none'] = esc_html__( 'None', 'johannes' );
		$options['yoast'] = esc_html__( 'Yoast SEO (or Yoast Breadcrumbs)', 'johannes' );
		$options['bcn'] = esc_html__( 'Breadcrumb NavXT', 'johannes' );

		$options = apply_filters( 'johannes_modify_breadcrumbs_options', $options );

		return $options;
	}
endif;


/**
 * Get Admin JS localized variables
 *
 * Function creates list of variables from theme to pass
 * them to global JS variable so we can use it in JS files
 *
 * @since  1.0
 *
 * @return array List of JS settings
 */
if ( !function_exists( 'johannes_get_admin_js_settings' ) ):
	function johannes_get_admin_js_settings() {

		$js_settings = array();
		$js_settings['ajax_url'] = admin_url( 'admin-ajax.php' );
		return $js_settings;
	}
endif;


?>
