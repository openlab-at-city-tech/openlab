<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Flawless Blog
 */

function flawless_blog_font_choices() {
	$font_family_arr     = array();
	$font_family_arr[''] = esc_html__( '--Default--', 'flawless-blog' );

	// Make the request.
	$request = wp_remote_get( get_theme_file_uri( 'assets/webfonts.json' ) );

	if ( is_wp_error( $request ) ) {
		return false; // Bail early.
	}
	// Retrieve the data.
	$body = wp_remote_retrieve_body( $request );
	$data = json_decode( $body );
	if ( ! empty( $data ) ) {
		foreach ( $data->items as $items => $fonts ) {
			$family_str_arr                   = explode( ' ', $fonts->family );
			$family_value                     = implode( '+', $family_str_arr );
			$font_family_arr[ $family_value ] = $fonts->family;
		}
	}

	return $font_family_arr;
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function flawless_blog_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	$classes[] = flawless_blog_sidebar_layout();

	return $classes;
}
add_filter( 'body_class', 'flawless_blog_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function flawless_blog_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'flawless_blog_pingback_header' );

/**
 * Get an array of post id and title.
 */
function flawless_blog_get_post_choices() {
	$choices = array( '' => esc_html__( '--Select--', 'flawless-blog' ) );
	$args    = array( 'numberposts' => -1 );
	$posts   = get_posts( $args );

	foreach ( $posts as $post ) {
		$id             = $post->ID;
		$title          = $post->post_title;
		$choices[ $id ] = $title;
	}

	return $choices;
	wp_reset_postdata();
}

/**
 * Get an array of cat id and title.
 */
function flawless_blog_get_post_cat_choices() {
	$choices = array( '' => esc_html__( '--Select--', 'flawless-blog' ) );
	$cats    = get_categories();

	foreach ( $cats as $cat ) {
		$id             = $cat->term_id;
		$title          = $cat->name;
		$choices[ $id ] = $title;
	}

	return $choices;
}

/**
 * Checks to see if we're on the homepage or not.
 */
function flawless_blog_is_frontpage() {
	return ( is_front_page() && ! is_home() );
}

/**
 * Checks to see if Static Front Page is set to "Your latest posts".
 */
function flawless_blog_is_latest_posts() {
	return ( is_front_page() && is_home() );
}

/**
 * Checks to see if Static Front Page is set to "Posts page".
 */
function flawless_blog_is_frontpage_blog() {
	return ( is_home() && ! is_front_page() );
}

/**
 * Shows a breadcrumb for all types of pages.  This is a wrapper function for the Breadcrumb_Trail class,
 * which should be used in theme templates.
 *
 * @since  1.0.0
 * @access public
 * @param  array $args Arguments to pass to Breadcrumb_Trail.
 * @return void
 */
function flawless_blog_breadcrumb( $args = array() ) {
	$breadcrumb = apply_filters( 'breadcrumb_trail_object', null, $args );

	if ( ! is_object( $breadcrumb ) ) {
		$breadcrumb = new Breadcrumb_Trail( $args );
	}

	return $breadcrumb->trail();
}

/**
 * Add separator for breadcrumb trail.
 */
function flawless_blog_breadcrumb_trail_print_styles() {
	$breadcrumb_separator = get_theme_mod( 'flawless_blog_breadcrumb_separator', '/' );

	$style = '
	.trail-items li:not(:last-child):after {
		content: "' . $breadcrumb_separator . '";
	}';

	$style = apply_filters( 'flawless_blog_breadcrumb_trail_inline_style', trim( str_replace( array( "\r", "\n", "\t", '  ' ), '', $style ) ) );

	if ( $style ) {
		echo "\n" . '<style type="text/css" id="breadcrumb-trail-css">' . $style . '</style>' . "\n";
	}
}
add_action( 'wp_head', 'flawless_blog_breadcrumb_trail_print_styles' );

if ( ! function_exists( 'flawless_blog_sidebar_layout' ) ) {
	/**
	 * Get sidebar layout.
	 */
	function flawless_blog_sidebar_layout() {
		$sidebar_position      = get_theme_mod( 'flawless_blog_sidebar_position', 'right-sidebar' );
		$sidebar_position_post = get_theme_mod( 'flawless_blog_post_sidebar_position', 'right-sidebar' );
		$sidebar_position_page = get_theme_mod( 'flawless_blog_page_sidebar_position', 'right-sidebar' );

		if ( is_single() ) {
			$sidebar_position = $sidebar_position_post;
		} elseif ( is_page() ) {
			$sidebar_position = $sidebar_position_page;
		}

		return $sidebar_position;
	}
}

if ( ! function_exists( 'flawless_blog_is_sidebar_enabled' ) ) {
	/**
	 * Check if sidebar is enabled.
	 */
	function flawless_blog_is_sidebar_enabled() {
		$sidebar_position      = get_theme_mod( 'flawless_blog_sidebar_position', 'right-sidebar' );
		$sidebar_position_post = get_theme_mod( 'flawless_blog_post_sidebar_position', 'right-sidebar' );
		$sidebar_position_page = get_theme_mod( 'flawless_blog_page_sidebar_position', 'right-sidebar' );

		$sidebar_enabled = true;
		if ( is_home() || is_archive() || is_search() ) {
			if ( 'no-sidebar' === $sidebar_position ) {
				$sidebar_enabled = false;
			}
		} elseif ( is_single() ) {
			if ( 'no-sidebar' === $sidebar_position || 'no-sidebar' === $sidebar_position_post ) {
				$sidebar_enabled = false;
			}
		} elseif ( is_page() ) {
			if ( 'no-sidebar' === $sidebar_position || 'no-sidebar' === $sidebar_position_page ) {
				$sidebar_enabled = false;
			}
		}
		return $sidebar_enabled;
	}
}

/**
 * Pagination for archive.
 */
function flawless_blog_render_posts_pagination() {
	$is_pagination_enabled = get_theme_mod( 'flawless_blog_pagination_enable', true );
	if ( $is_pagination_enabled ) {
		$pagination_type = get_theme_mod( 'flawless_blog_pagination_type', 'numeric' );
		if ( 'default' === $pagination_type ) :
			the_posts_navigation();
		else :
			the_posts_pagination();
		endif;
	}
}
add_action( 'flawless_blog_posts_pagination', 'flawless_blog_render_posts_pagination', 10 );

/**
 * Pagination for single post.
 */
function flawless_blog_render_post_navigation() {
	the_post_navigation(
		array(
			'prev_text' => '<span>&#10229;</span> <span class="nav-title">%title</span>',
			'next_text' => '<span class="nav-title">%title</span> <span>&#10230;</span>',
		)
	);
}
add_action( 'flawless_blog_post_navigation', 'flawless_blog_render_post_navigation' );

if ( ! function_exists( 'flawless_blog_excerpt_length' ) ) :
	/**
	 * Excerpt length.
	 */

	function flawless_blog_excerpt_length( $length ) {
		if ( is_admin() ) {
			return $length;
		}

		$length = get_theme_mod( 'flawless_blog_excerpt_length', 15 );
		return $length;
	}
endif;
add_filter( 'excerpt_length', 'flawless_blog_excerpt_length', 999 );

if ( ! function_exists( 'flawless_blog_the_excerpt' ) ) :

	/**
	 * Generate excerpt.

	 * @since 1.0.0

	 * @param int     $length Excerpt length in words.
	 * @param WP_Post $post_obj WP_Post instance (Optional).
	 * @return string Excerpt.
	 */
	function flawless_blog_the_excerpt( $length = 0, $post_obj = null ) {

		global $post;

		if ( is_null( $post_obj ) ) {
			$post_obj = $post;
		}

		$length = absint( $length );

		if ( 0 === $length ) {
			return;
		}

		$source_content = $post_obj->post_content;

		if ( ! empty( $post_obj->post_excerpt ) ) {
			$source_content = $post_obj->post_excerpt;
		}

		$source_content  = preg_replace( '`\[[^\]]*\]`', '', $source_content );
		$trimmed_content = wp_trim_words( $source_content, $length, '&hellip;' );
		return $trimmed_content;

	}

endif;

// Add auto p to the palces where get_the_excerpt is being called.
add_filter( 'get_the_excerpt', 'wpautop' );

/*excerpt length validation*/
if ( ! function_exists( 'flawless_blog_excerpt_length_validation' ) ) :
	function flawless_blog_excerpt_length_validation( $validity, $value ) {
		$value = intval( $value );
		if ( empty( $value ) || ! is_numeric( $value ) ) {
			$validity->add( 'required', esc_html__( 'You must supply a valid number.', 'flawless-blog' ) );
		} elseif ( $value < 1 ) {
			$validity->add( 'min_no_of_posts', esc_html__( 'Minimum no of posts is 1', 'flawless-blog' ) );
		} elseif ( $value > 100 ) {
			$validity->add( 'max_no_of_posts', esc_html__( 'Maximum no of posts is 100', 'flawless-blog' ) );
		}
		return $validity;
	}
endif;

/**
 * Renders Reading Time
 */

if ( ! function_exists( 'flawless_blog_time_interval' ) ) :

	function flawless_blog_time_interval( $content = '', $wpm = 200 ) {
		$clean_content = strip_shortcodes( $content );
		$clean_content = strip_tags( $clean_content );
		$word_count    = str_word_count( $clean_content );
		$time          = ceil( $word_count / $wpm );
		return absint( $time );
	}

endif;
