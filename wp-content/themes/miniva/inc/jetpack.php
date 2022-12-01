<?php
/**
 * Jetpack Compatibility File
 *
 * @link https://jetpack.com/
 *
 * @package Miniva
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 * See: https://jetpack.com/support/content-options/
 * See: https://jetpack.com/support/social-menu/
 * See: https://jetpack.com/support/featured-content/
 */
function miniva_jetpack_setup() {
	// Add theme support for Infinite Scroll.
	add_theme_support(
		'infinite-scroll',
		apply_filters(
			'miniva_jetpack_infinite_scroll_args',
			array(
				'container'      => miniva_is_grid() ? 'posts-container' : 'main',
				'render'         => 'miniva_infinite_scroll_render',
				'footer'         => 'page',
				'footer_widgets' => array( 'footer-1', 'footer-2', 'footer-3' ),
			)
		)
	);

	// Add theme support for Content Options.
	add_theme_support(
		'jetpack-content-options',
		apply_filters(
			'miniva_jetpack_content_options_args',
			array(
				'author-bio'      => true,
				'post-details'    => array(
					'stylesheet' => 'miniva-style',
					'date'       => '.posted-on',
					'categories' => '.cat-links',
					'tags'       => '.tags-links',
					'author'     => '.byline',
					'comment'    => '.comments-link',
				),
				'featured-images' => array(
					'archive' => true,
					'post'    => true,
					'page'    => true,
				),
			)
		)
	);

	// Add theme support for Featured Content.
	add_theme_support(
		'featured-content',
		apply_filters(
			'miniva_jetpack_featured_content_args',
			array(
				'filter'     => 'miniva_featured_content',
				'max_posts'  => apply_filters( 'miniva_featured_content_max_posts', 3 ),
				'post_types' => array( 'post' ),
			)
		)
	);

	// Add theme support for Social Menu.
	add_theme_support( 'jetpack-social-menu', 'genericons' );
}
add_action( 'after_setup_theme', 'miniva_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function miniva_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		if ( is_search() ) :
			get_template_part( 'template-parts/content', 'search' );
		else :
			get_template_part( 'template-parts/content', get_post_type() );
		endif;
	}
}

/**
 * Get featured posts
 *
 * @return array
 */
function miniva_get_featured_posts() {
	return apply_filters( 'miniva_featured_content', array() );
}

/**
 * Insert featured content after the header.
 */
function miniva_header_after() {
	if ( ! is_front_page() ) {
		return;
	}
	$featured_posts = miniva_get_featured_posts();
	if ( is_array( $featured_posts ) ) {
		$count = count( $featured_posts );
		$count = min( max( $count, 0 ), 3 );
		if ( 0 === $count ) {
			return;
		}
		global $miniva_featured_thumbnail_size;
		switch ( $count ) {
			case 1:
				$miniva_featured_thumbnail_size = 'miniva-large';
				break;
			default:
				$miniva_featured_thumbnail_size = 'post-thumbnail';
				break;
		}
		global $post;
		miniva_container_open( 'featured-content featured-' . $count );
		miniva_container_open();
		$i = 1;
		foreach ( $featured_posts as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			if ( $i > $count ) {
				break;
			}
			setup_postdata( $post );
			get_template_part( 'template-parts/content', 'featured' );
			$i++;
		}
		miniva_container_close();
		miniva_container_close();
		wp_reset_postdata();
	}
}
add_action( 'miniva_header_after', 'miniva_header_after' );

/**
 * Show jetpack social menu in footer
 */
function miniva_social_menu() {
	if ( function_exists( 'jetpack_social_menu' ) ) {
		jetpack_social_menu();
	}
}
add_action( 'miniva_footer_start', 'miniva_social_menu' );

/**
 * Disable unneeded styles/scripts.
 */
function miniva_jetpack_disable_scripts() {
	// Disable social menu style if no menu assigned to it.
	if ( ! has_nav_menu( 'jetpack-social-menu' ) ) {
		wp_dequeue_style( 'jetpack-social-menu' );
	}
	// Disable devicepx script.
	wp_dequeue_script( 'devicepx' );
}
add_action( 'wp_enqueue_scripts', 'miniva_jetpack_disable_scripts', 100 );
