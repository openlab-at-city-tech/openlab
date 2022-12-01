<?php
/**
 * Posts related functions
 *
 * @package Miniva
 */

/**
 * Add custom classes to the array of body classes.
 *
 * @param  array $classes Classes for the body element.
 * @return array
 */
function miniva_posts_body_classes( $classes ) {
	if ( is_home() || is_archive() || is_search() ) {
		$layout    = miniva_get_posts_layout();
		$classes[] = 'posts-' . esc_attr( $layout );
	}

	return $classes;
}
add_filter( 'body_class', 'miniva_posts_body_classes' );

/**
 * Insert grid container before the loop.
 *
 * @param obj $query The WP_Query instance.
 */
function miniva_loop_start( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_feed() ) {
		return;
	}
	if ( miniva_is_grid() ) {
		miniva_container_open( 'grid', 'posts-container' );
	}
}
add_action( 'loop_start', 'miniva_loop_start' );

/**
 * Insert grid container after the loop.
 *
 * @param obj $query The WP_Query instance.
 */
function miniva_loop_end( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_feed() ) {
		return;
	}
	if ( miniva_is_grid() ) {
		miniva_container_close();
	}
}
add_action( 'loop_end', 'miniva_loop_end' );

/**
 * Insert container in posts
 */
function miniva_post_before() {
	if ( miniva_is_grid() ) {
		miniva_container_open( 'grid-item' );
	}
}
add_action( 'miniva_post_before', 'miniva_post_before' );

/**
 * Insert container in posts
 */
function miniva_post_after() {
	if ( miniva_is_grid() ) {
		miniva_container_close();
	}
}
add_action( 'miniva_post_after', 'miniva_post_after' );

/**
 * Insert post thumbnails in posts
 */
function miniva_post_start() {
	if ( is_single() ) {
		return;
	}
	$layout = miniva_get_posts_layout();
	if ( 'small' === $layout ) {
		miniva_container_open( 'entry-media' );
		miniva_post_thumbnail( 'miniva-small' );
		miniva_container_close();
		miniva_container_open( 'entry-body' );
	} elseif ( strpos( $layout, 'grid' ) === 0 ) {
		miniva_post_thumbnail( 'miniva-medium' );
	}
}
add_action( 'miniva_post_start', 'miniva_post_start' );

/**
 * Insert post thumbnails in posts & pages
 */
function miniva_post_middle() {
	$featured_image_middle = apply_filters( 'miniva_featured_image_middle', true );
	if ( ! $featured_image_middle ) {
		return;
	}
	if ( is_single() || is_page() ) {
		$wide = is_page_template( array( 'template-fullwidth.php', 'template-centered.php' ) );
	} else {
		$layout = miniva_get_posts_layout();
		if ( 'large' === $layout ) {
			$wide = get_theme_mod( 'sidebar_layout', 'right' ) === 'no';
		}
	}

	if ( isset( $wide ) ) {
		$nocrop = get_theme_mod( 'featured_image_nocrop', false );
		$wide   = apply_filters( 'miniva_is_wide', $wide );
		if ( $nocrop ) {
			$size = $wide ? 'miniva-large-nocrop' : 'miniva-post-nocrop';
		} else {
			$size = $wide ? 'miniva-large' : 'post-thumbnail';
		}
		miniva_post_thumbnail( $size );
	}
}
add_action( 'miniva_post_middle', 'miniva_post_middle' );
add_action( 'miniva_page_middle', 'miniva_post_middle' );

/**
 * Insert container close at the end of posts
 */
function miniva_post_end() {
	if ( is_single() ) {
		return;
	}
	$layout = miniva_get_posts_layout();
	if ( 'small' === $layout ) {
		miniva_container_close();
	}
}
add_action( 'miniva_post_end', 'miniva_post_end' );

/**
 * Add custom classes to post_class.
 *
 * @param array $classes classes for the post.
 */
function miniva_post_class( $classes ) {
	if ( is_main_query() ) {
		if ( is_singular() ) {
			$classes[] = 'post-single';
		} else {
			$classes[] = 'post-archive';
		}
	}
	return $classes;
}
add_filter( 'post_class', 'miniva_post_class' );

/**
 * Add extra information in posts
 */
function miniva_post_info() {
	if ( function_exists( 'the_views' ) ) {
		miniva_container_open( 'postviews' );
		the_views();
		miniva_container_close();
	}
}
add_action( 'miniva_post_end', 'miniva_post_info', 9 );
add_action( 'miniva_page_end', 'miniva_post_info' );

/**
 * Display post content or excerpt
 */
function miniva_the_content() {
	if ( is_single() ) {
		$display = 'content';
	} else {
		$display = get_theme_mod( 'blog_display' );
		if ( empty( $display ) ) {
			$display = get_option( 'jetpack_content_blog_display' );
			if ( empty( $display ) ) {
				$display = 'content';
			}
		}
	}

	if ( 'excerpt' === $display ) {
		the_excerpt();
	} else {
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'miniva' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'miniva' ),
				'after'  => '</div>',
			)
		);
	}
}
