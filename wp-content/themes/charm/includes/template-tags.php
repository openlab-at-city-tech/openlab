<?php

/**
 * Logo
 */
function tr_logo() {
	$logo = get_theme_mod( 'rain_logo' );
	$width = ( get_theme_mod( 'rain_logo_width' ) ) ? 'style="max-width: ' . get_theme_mod( 'rain_logo_width' ) . 'px;"' : '';

	echo '<a class="site-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
		if ( $logo ) {
			echo '<img src="' . $logo . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" ' . $width . '>';
		} else {
			echo get_bloginfo( 'name' );
		}
	echo '</a>';
}

/**
 * Menu
 */
function tr_menu() {
	echo '<nav class="site-navigation">';
		echo '<div class="menu-toggle"><span></span></div>';

		if ( has_nav_menu( 'menu-header' ) ) {
			wp_nav_menu( array( 'theme_location' => 'menu-header', 'container' => false, 'menu_class' => 'top-menu', 'depth' => 3 ) );
		} else {
			echo '<ul class="top-menu"><li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">Set up your menu</a></li></ul>';
		}
	echo '</nav>';
}

/**
 * Post share
 */
function tr_share() {
	echo '<div class="post-share">';
		echo '<h6>' . __( 'Share', 'themerain' ) . '</h6>';

		echo '<a class="fa fa-twitter" target="_blank" href="https://twitter.com/home?status=' . get_the_title() . '+' . get_the_permalink() . '"></a>';
		echo '<a class="fa fa-facebook" target="_blank" href="https://www.facebook.com/share.php?u=' . get_the_permalink() . '&title=' . get_the_title() . '"></a>';
		echo '<a class="fa fa-google-plus" target="_blank" href="https://plus.google.com/share?url=' . get_the_permalink() . '"></a>';
		echo '<a class="fa fa-pinterest-p" target="_blank" href="https://pinterest.com/pin/create/bookmarklet/?media=' . get_the_permalink() . '&is_video=false&description=' . get_the_title() . '"></a>';
	echo '</div>';
}

/**
 * -----------------------------------------------------------------------------
 * Page header
 * -----------------------------------------------------------------------------
 */

function rain_page_header() {
	$title = '';
	$subtitle = '';
	$image = '';
	$project_image = '';
	$category = '';
	$hide_page_header = get_post_meta( get_the_ID(), 'rain_hide_page_header', true );

	if ( is_home() && is_front_page() ) {
		$title = get_theme_mod( 'rain_default_blog_title', 'Blog' );
		$subtitle = get_theme_mod( 'rain_default_blog_subtitle', 'Blog subtitle' );
		$image = get_theme_mod( 'rain_default_blog_image' );
	} elseif ( is_home() && ! is_front_page() ) {
		$blog = get_post( get_option( 'page_for_posts' ) );
		$title = $blog->post_title;
		$subtitle = get_post_meta( $blog->ID, 'rain_subtitle', true );
		$image = wp_get_attachment_url( get_post_thumbnail_id( get_option( 'page_for_posts' ) ) );
	} elseif ( is_singular( 'post' ) ) {
		$title = get_the_title();
		$subtitle = get_the_date();
		$image = wp_get_attachment_url( get_post_thumbnail_id() );
		$category = get_the_category_list( ', ' );
	} elseif ( is_archive() ) {
		$title = get_the_archive_title();
		$subtitle = get_the_archive_description();
	} elseif ( is_search() ) {
		$title = __( 'Search results for: ', 'themerain' ) . get_search_query();
	} elseif ( is_404() ) {
		$title = __( 'Error 404', 'themerain' );
	} elseif ( is_attachment() ) {
		$title = get_the_title();
	} else {
		$title = get_the_title();
		$subtitle = get_post_meta( get_the_ID(), 'rain_subtitle', true );
		$image = wp_get_attachment_url( get_post_thumbnail_id() );
		$project_image = get_post_meta( get_the_ID(), 'rain_project_image', true );
	}

	if ( $hide_page_header ) {
		return;
	}

	if ( $image ) {
		echo '<div class="page-header">';
			echo '<div class="page-header-content">';
				if ( $category ) {
					echo '<div class="page-header-category">' . $category . '</div>';
				}

				echo '<h1 class="page-header-title">' . $title . '</h1>';

				if ( $subtitle ) {
					echo '<div class="page-header-subtitle">' . $subtitle . '</div>';
				}
			echo '</div>';

			if ( $project_image ) {
				echo '<div class="page-header-image" style="background-image: url(' . esc_url( $project_image ) . ')"></div>';
			} else {
				echo '<div class="page-header-image" style="background-image: url(' . esc_url( $image ) . ')"></div>';
			}
		echo '</div>';
	} else {
		echo '<h1 class="page-title">' . $title . '</h1>';
	}
}

/**
 * -----------------------------------------------------------------------------
 * Posts pagination
 * -----------------------------------------------------------------------------
 */

function rain_posts_pagination() {
	$links = paginate_links( array(
		'prev_text' => '&larr;',
		'next_text' => '&rarr;'
	) );

	if ( $links ) {
		echo '<nav class="posts-pagination" role="navigation">';
			echo $links;
		echo '</nav>';
	}
}

/**
 * -----------------------------------------------------------------------------
 * Post navigation
 * -----------------------------------------------------------------------------
 */

function rain_post_navigation() {
	$previous = get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( ! $previous && ! $next ) {
		return;
	}

	echo '<nav class="post-navigation" role="navigation">';
		previous_post_link( '<div class="nav-prev"><i class="fa fa-angle-left"></i> ' . __( 'Previous Post', 'themerain' ) . '<br>%link</div>', '%title' );
		next_post_link( '<div class="nav-next">' . __( 'Next Post', 'themerain' ) . ' <i class="fa fa-angle-right"></i><br>%link</div>', '%title' );
	echo '</nav>';
}

/**
 * -----------------------------------------------------------------------------
 * Project navigation
 * -----------------------------------------------------------------------------
 */

function rain_project_navigation() {
	$page_id = get_theme_mod( 'rain_default_portfolio_page' );
	$permalink = get_permalink( $page_id );

	echo '<nav class="project-navigation" role="navigation">';
		echo '<span>';
			if ( get_adjacent_post( false, '', false ) ) {
				next_post_link( '%link', '<i class="fa fa-caret-left"></i>' );
			} else {
				echo '<i class="inactive fa fa-caret-left"></i>';
			};
		echo '</span>';

		if ( $page_id ) {
			echo '<span><a href="' . $permalink . '"><i class="fa fa-th"></i></a></span>';
		}

		echo '<span>';
			if ( get_adjacent_post( false, '', true ) ) {
				previous_post_link( '%link', '<i class="fa fa-caret-right"></i>' );
			} else {
				echo '<i class="inactive fa fa-caret-right"></i>';
			};
		echo '</span>';
	echo '</nav>';
}