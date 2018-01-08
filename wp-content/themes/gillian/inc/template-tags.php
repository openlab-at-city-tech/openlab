<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package gillian
 */

if ( ! function_exists( 'gillian_entry_meta' ) ) :
/**
 * Prints HTML with meta information for the author, current post-date/time, 
 * comments, categories, and tags.
 */
function gillian_entry_meta() {
	
	// byline
	echo '<p>';
	_e( 'by ', 'gillian' );
	the_author_posts_link();
	echo '</p>';
	
	// date
	echo '<p><i class="fa fa-calendar-o" aria-hidden="true"></i>';
	the_time(get_option('date_format'));
	echo '</p>';

	// time
	echo '<p><i class="fa fa-clock-o" aria-hidden="true"></i>';
	the_time();
	echo '</p>';
	
	// comments
	echo '<p><i class="fa fa-comment" aria-hidden="true"></i>';
	/* translators: %s: post title */
	comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'gillian' ), get_the_title() ) );
	echo '</p>';

	// categories if they exist
	if(has_category()) {
		echo '<p><i class="fa fa-bookmark" aria-hidden="true"></i>';
		the_category(', ');
		echo '</p>';
	}

	// tags if they exist
	if(has_tag()) {
		echo '<p>';
		the_tags('<i class="fa fa-tag" aria-hidden="true"></i> ', ', ');
		echo '</p>';
	}

	// edit link
	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'gillian' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<p><i class="fa fa-pencil" aria-hidden="true"></i>',
		'</p>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function gillian_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'gillian_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'gillian_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so gillian_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so gillian_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in gillian_categorized_blog.
 */
function gillian_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'gillian_categories' );
}
add_action( 'edit_category', 'gillian_category_transient_flusher' );
add_action( 'save_post',     'gillian_category_transient_flusher' );

/* extra menus */

function gillian_top_menu() {
    if ( has_nav_menu( 'top-menu' ) ) {
	wp_nav_menu(
		array(
			'theme_location'  => 'top-menu',
			'menu_id'		  => 'top-menu',
			'fallback_cb'     => '',
		)
	);
    }
}

function gillian_social_menu() {
    if ( has_nav_menu( 'social' ) ) {
	wp_nav_menu(
		array(
			'theme_location'  => 'social',
			'container'       => 'div',
			'container_id'    => 'menu-social',
			'container_class' => 'menu-social',
			'menu_id'         => 'menu-social-items',
			'menu_class'      => 'menu-items',
			'depth'           => 1,
			'link_before'     => '<span class="screen-reader-text">',
			'link_after'      => '</span>',
			'fallback_cb'     => '',
		)
	);
    }
}