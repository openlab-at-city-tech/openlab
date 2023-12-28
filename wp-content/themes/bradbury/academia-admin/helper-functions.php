<?php

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_breadcrumbs' ) ) {
	function academiathemes_helper_display_breadcrumbs() {

		// CONDITIONAL FOR "Breadcrumb NavXT" plugin OR Yoast SEO Breadcrumbs
		// https://wordpress.org/plugins/breadcrumb-navxt/

		if ( function_exists('bcn_display') ) { ?>
		<div class="site-breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
			<p class="site-breadcrumbs-p"><?php bcn_display(); ?></p>
		</div><!-- .site-breadcrumbs--><?php }

		// CONDITIONAL FOR "Yoast SEO" plugin, Breadcrumbs feature
		// https://wordpress.org/plugins/wordpress-seo/
		if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<div class="site-breadcrumbs"><p class="site-breadcrumbs-p">','</p></div>');
		}

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_title' ) ) {
	function academiathemes_helper_display_title($post) {

		if( ! is_object( $post ) ) return;
		the_title( '<h1 class="page-title">', '</h1>' );
	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_entry_title' ) ) {
	function academiathemes_helper_display_entry_title($post) {

		if( ! is_object( $post ) ) return;
		return the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>', 0 );

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_entry_title_custom' ) ) {
	function academiathemes_helper_display_entry_title_custom($post,$title) {

		if( ! is_object( $post ) ) return;

		return '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . esc_html($title) . '</a></h2>';

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_datetime' ) ) {
	function academiathemes_helper_display_datetime($post) {
		
		if( ! is_object( $post ) ) return;

		return '<p class="entry-descriptor"><span class="entry-descriptor-span"><time class="entry-date published" datetime="' . esc_attr(get_the_date('c')) . '">' . get_the_date() . '</time></span></p>';

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_excerpt' ) ) {
	function academiathemes_helper_display_excerpt($post) {

		if( ! is_object( $post ) ) return;

		return '<p class="entry-excerpt">' . get_the_excerpt() . '</p>';

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_button_readmore' ) ) {
	function academiathemes_helper_display_button_readmore($post) {

		if( ! is_object( $post ) ) return;

		return '<p class="entry-actions"><span class="site-readmore-span"><a href="' . esc_url( get_permalink() ) . '" title="' . sprintf( /* translators: %s: Link tittle attribute */ esc_attr__( 'Continue Reading: %s', 'bradbury' ), the_title_attribute( 'echo=0' ) ) . '" class="site-readmore-anchor" rel="bookmark">' . __('Read More','bradbury') . '</a></span></p>';
		
	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_comments' ) ) {
	function academiathemes_helper_display_comments($post) {

		if( ! is_object( $post ) ) return;

		if ( comments_open() || get_comments_number() ) :

			echo '<hr /><div id="academia-comments"">';
			comments_template();
			echo '</div><!-- #academia-comments -->';

		endif;

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_content' ) ) {
	function academiathemes_helper_display_content($post) {

		if( ! is_object( $post ) ) return;

		echo '<div class="entry-content">';
			
			the_content();
			
			wp_link_pages(array('before' => '<p class="page-navigation"><strong>'.__('Pages', 'bradbury').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number'));

		echo '</div><!-- .entry-content -->';

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_tags' ) ) {
	function academiathemes_helper_display_tags($post) {

		if( ! is_object( $post ) ) return;

		if ( get_post_type($post->ID) == 'post' ) { 
			the_tags( '<p class="post-meta post-tags"><strong>'.__('Tags', 'bradbury').':</strong> ', ' ', '</p>');
		}

	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_postmeta' ) ) {
	function academiathemes_helper_display_postmeta($post) {

		if( ! is_object( $post ) ) return;

		if ( get_post_type($post->ID) == 'post' ) { 

			echo '<p class="entry-tagline">';
			echo '<span class="post-meta-span"><time datetime="' . esc_attr(get_the_time("Y-m-d")) . '" pubdate>' . esc_html(get_the_time(get_option('date_format'))) . '</time></span>';
			echo '<span class="post-meta-span category">'; the_category(', '); echo '</span>';
			echo '</p><!-- .entry-tagline -->';

		}

	}
}

// Get Sidebar Position for Current Page or Post
if( ! function_exists( 'academiathemes_helper_get_sidebar_position' ) ) {
	function academiathemes_helper_get_sidebar_position() {

		global $post;

		$themeoptions_sidebar_position = esc_attr(get_theme_mod( 'theme-sidebar-position', 'left' ));

		if ( $themeoptions_sidebar_position == 'left' ) {
			$default_position = 'page-sidebar-left';
		} elseif ( $themeoptions_sidebar_position == 'right' ) {
			$default_position = 'page-sidebar-right';
		} elseif ( $themeoptions_sidebar_position == 'hidden' ) {
			$default_position = 'page-sidebar-hidden';
		}

		return $default_position;
	}
}

// Page/Post Title
if( ! function_exists( 'academiathemes_helper_display_page_sidebar_column' ) ) {
	function academiathemes_helper_display_page_sidebar_column() {

		$display_sidebar_position = academiathemes_helper_get_sidebar_position();

		if ( isset($display_sidebar_position) && ( $display_sidebar_position == 'page-sidebar-right' || $display_sidebar_position == 'page-sidebar-left' ) ) {

		?><div class="site-column site-column-aside">

			<div class="site-column-wrapper clearfix">

				<?php get_sidebar(); ?>

			</div><!-- .site-column-wrapper .clearfix -->

		</div><!-- .site-column .site-column-aside --><?php
		}

	}
}

// Content Column Wrapper Start
if( ! function_exists( 'academiathemes_helper_display_page_content_wrapper_start' ) ) {
	function academiathemes_helper_display_page_content_wrapper_start() {

		?><div class="site-column site-column-content"><div class="site-column-wrapper clearfix"><!-- .site-column .site-column-1 .site-column-aside --><?php

	}
}

// Content Column Wrapper Start
if( ! function_exists( 'academiathemes_helper_display_page_content_wrapper_end' ) ) {
	function academiathemes_helper_display_page_content_wrapper_end() {

		?></div><!-- .site-column-wrapper .clearfix --></div><!-- .site-column .site-column-content --><?php

	}
}

/**
 * Adds a Sub Nav Toggle to the Expanded Menu and Mobile Menu.
 *
 * @param stdClass $args  An object of wp_nav_menu() arguments.
 * @param WP_Post  $item  Menu item data object.
 * @param int      $depth Depth of menu item. Used for padding.
 * @return stdClass An object of wp_nav_menu() arguments.
 */
function bradbury_add_sub_toggles_to_main_menu( $args, $item, $depth ) {

	// Add sub menu toggles to the Expanded Menu with toggles.
	if ( isset( $args->show_toggles ) && $args->show_toggles ) {

		$args->after  = '';

		if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {

			$args->after .= '<button class="sub-menu-toggle toggle-anchor"><span class="screen-reader-text">' . __( 'Show sub menu', 'bradbury' ) . '</span><span class="icon-icomoon academia-icon-chevron-down"></span></span></button>';

		}
	} 

	return $args;

}

add_filter( 'nav_menu_item_args', 'bradbury_add_sub_toggles_to_main_menu', 10, 3 );