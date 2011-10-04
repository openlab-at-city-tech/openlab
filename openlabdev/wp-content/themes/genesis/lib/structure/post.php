<?php
/**
 * Controls output elements in post structures.
 *
 * @package Genesis
 */

/**
 * This function restores all default post loop output by rehooking all default functions.
 *
 * Useful in the event that you need to unhook something in a particular context,
 * but don't want to restore it for all subsequent loop instances.
 *
 * @since 1.5
 */
function genesis_reset_loops() {
	add_action('genesis_before_post_title', 'genesis_do_post_format_image');
	add_action('genesis_post_title', 'genesis_do_post_title');
	add_action('genesis_post_content', 'genesis_do_post_image');
	add_action('genesis_post_content', 'genesis_do_post_content');
	add_action('genesis_loop_else', 'genesis_do_noposts');
	add_action('genesis_before_post_content', 'genesis_post_info');
	add_action('genesis_after_post_content', 'genesis_post_meta');
	add_action('genesis_after_post', 'genesis_do_author_box_single');
	add_action('genesis_after_endwhile', 'genesis_posts_nav');

	/** Reset loop args **/
	global $_genesis_loop_args;
	$_genesis_loop_args = array();

	do_action( 'genesis_reset_loops' );
}

add_filter('post_class', 'genesis_custom_post_class', 15);
/**
 * This function/filter adds a custom post class based on
 * the value stored as a custom field.
 *
 * @since 1.4
 */
function genesis_custom_post_class( $classes ) {

	$new_class = genesis_get_custom_field( '_genesis_custom_post_class' );

	if ( $new_class ) $classes[] = esc_attr( sanitize_html_class( $new_class ) );

	return $classes;

}

add_action( 'genesis_before_post_title', 'genesis_do_post_format_image' );
/**
 * Post format icon.
 * Adds an image, corresponding to the post format, before the post title
 *
 * @since 1.4
 */
function genesis_do_post_format_image() {

	global $post;

	/** Do nothing if post formats aren't supported */
	if ( ! current_theme_supports( 'post-formats' ) || ! current_theme_supports( 'genesis-post-format-images' ) )
		return;

	/** Get post format */
	$post_format = get_post_format( $post );

	/** If post format is set, look for post format image */
	if ( $post_format && file_exists( sprintf( '%s/images/post-formats/%s.png', CHILD_DIR, $post_format ) ) ) {
		printf( '<a href="%s" title="%s" rel="bookmark"><img src="%s" class="post-format-image" alt="%s" /></a>', get_permalink(), the_title_attribute('echo=0'), sprintf( '%s/images/post-formats/%s.png', CHILD_URL, $post_format ), $post_format );
	}
	/** Else, look for the default post format image */
	elseif ( file_exists( sprintf( '%s/images/post-formats/default.png', CHILD_DIR ) ) ) {
		printf( '<a href="%s" title="%s" rel="bookmark"><img src="%s/images/post-formats/default.png" class="post-format-image" alt="%s" /></a>', get_permalink(), the_title_attribute('echo=0'), CHILD_URL, 'post' );
	}

}

add_action('genesis_post_title', 'genesis_do_post_title');
/**
 * Post Title
 */
function genesis_do_post_title() {

	$title = get_the_title();

	if ( strlen( $title ) == 0 )
		return;

	if ( is_singular() ) {
		$title = sprintf( '<h1 class="entry-title">%s</h1>', apply_filters( 'genesis_post_title_text', $title ) );
	} else {
		$title = sprintf( '<h2 class="entry-title"><a href="%s" title="%s" rel="bookmark">%s</a></h2>', get_permalink(), the_title_attribute('echo=0'), apply_filters( 'genesis_post_title_text', $title ) );
	}

	echo apply_filters('genesis_post_title_output', $title) . "\n";

}

add_action('genesis_post_content', 'genesis_do_post_image');
/**
 * Post Image
 */
function genesis_do_post_image() {

	if ( !is_singular() && genesis_get_option('content_archive_thumbnail') ) {
		$img = genesis_get_image( array( 'format' => 'html', 'size' => genesis_get_option('image_size'), 'attr' => array( 'class' => 'alignleft post-image' ) ) );
		printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute('echo=0'), $img );
	}

}

add_action('genesis_post_content', 'genesis_do_post_content');
/**
 * Post Content
 */
function genesis_do_post_content() {

	if ( is_singular() ) {
		the_content(); // display content on posts/pages

		if ( is_single() && get_option('default_ping_status') == 'open' ) {
			echo '<!--'; trackback_rdf(); echo '-->' ."\n";
		}

		if ( is_page() ) {
			edit_post_link(__('(Edit)', 'genesis'), '', '');
		}
	}
	elseif ( genesis_get_option('content_archive') == 'excerpts' ) {
		the_excerpt();
	}
	else {
		if ( genesis_get_option('content_archive_limit') )
			the_content_limit( (int)genesis_get_option('content_archive_limit'), __('[Read more...]', 'genesis') );
		else
			the_content(__('[Read more...]', 'genesis'));
	}

	wp_link_pages( array( 'before' => '<p class="pages">' . __( 'Pages:', 'genesis' ), 'after' => '</p>' ) );

}

add_action('genesis_loop_else', 'genesis_do_noposts');
/**
 * No Posts
 */
function genesis_do_noposts() {

	printf( '<p>%s</p>', apply_filters( 'genesis_noposts_text', __('Sorry, no posts matched your criteria.', 'genesis') ) );

}

add_filter('genesis_post_info', 'do_shortcode', 20);
add_action('genesis_before_post_content', 'genesis_post_info');
/**
 * Add the post info (byline) under the title
 *
 * @since 0.2.3
 */
function genesis_post_info() {

	if ( is_page() )
		return; // don't do post-info on pages

	$post_info = '[post_date] ' . __('By', 'genesis') . ' [post_author_posts_link] [post_comments] [post_edit]';
	printf( '<div class="post-info">%s</div>', apply_filters('genesis_post_info', $post_info) );

}

add_filter('genesis_post_meta', 'do_shortcode', 20);
add_action('genesis_after_post_content', 'genesis_post_meta');
/**
 * Add the post meta after the post content
 *
 * @since 0.2.3
 */
function genesis_post_meta() {

	if ( is_page() )
		return; // don't do post-meta on pages

	$post_meta = '[post_categories] [post_tags]';
	printf( '<div class="post-meta">%s</div>', apply_filters('genesis_post_meta', $post_meta) );

}

add_action('genesis_after_post', 'genesis_do_author_box_single');
/**
 * This function runs some conditional code and calls the
 * genesis_author_box() function, if necessary.
 *
 * @uses genesis_author_box()
 *
 * @since 1.0
 */
function genesis_do_author_box_single() {

	if ( !is_single() )
		return;

	if ( get_the_author_meta( 'genesis_author_box_single', get_the_author_meta('ID') ) ) {
		genesis_author_box( 'single' );
	}

}

/**
 * This function outputs the content of the author box.
 * The title is filterable, and the description is dynamic.
 *
 * @uses get_the_author_meta(), get_the_author
 *
 * @since 1.3
 */
function genesis_author_box( $context = '' ) {

	global $authordata;
	$authordata = is_object( $authordata ) ? $authordata : get_userdata( get_query_var('author') );

	$gravatar_size = apply_filters('genesis_author_box_gravatar_size', '70', $context);
	$gravatar = get_avatar( get_the_author_meta('email'), $gravatar_size );
	$title = apply_filters( 'genesis_author_box_title', sprintf( '<strong>%s %s</strong>', __('About', 'genesis'), get_the_author() ), $context );
	$description = wpautop( get_the_author_meta('description') );

	// The author box markup, contextual.
	$pattern = $context == 'single' ? '<div class="author-box"><div>%s %s<br />%s</div></div><!-- end .authorbox-->' : '<div class="author-box">%s<h1>%s</h1><div>%s</div></div><!-- end .authorbox-->';

	echo apply_filters( 'genesis_author_box', sprintf( $pattern, $gravatar, $title, $description ), $context, $pattern, $gravatar, $title, $description );

}

add_action('genesis_after_endwhile', 'genesis_posts_nav');
/**
 * The default post navigation, hooked to genesis_after_endwhile
 *
 * @since 0.2.3
 */
function genesis_posts_nav() {
	$nav = genesis_get_option('posts_nav');

	if($nav == 'prev-next')
		genesis_prev_next_posts_nav();
	elseif($nav == 'numeric')
		genesis_numeric_posts_nav();
	else
		genesis_older_newer_posts_nav();
}

/**
 * Display older/newer posts navigation
 *
 * @since 0.2.2
 */
function genesis_older_newer_posts_nav() {

	$older_link = get_next_posts_link( apply_filters( 'genesis_older_link_text', g_ent('&laquo; ') . __( 'Older Posts', 'genesis' ) ) );
	$newer_link = get_previous_posts_link( apply_filters( 'genesis_newer_link_text', __('Newer Posts', 'genesis') . g_ent(' &raquo;') ) );

	$older = $older_link ? '<div class="alignleft">' . $older_link . '</div>' : '';
	$newer = $newer_link ? '<div class="alignright">' . $newer_link . '</div>' : '';

	$nav = '<div class="navigation">' . $older . $newer . '</div><!-- end .navigation -->';

	if ( ! empty( $older ) || ! empty( $newer ) )
		echo $nav;
}

/**
 * Display prev/next posts navigation
 *
 * @since 0.2.2
 */
function genesis_prev_next_posts_nav() {

	$prev_link = get_previous_posts_link( apply_filters( 'genesis_prev_link_text', g_ent( '&laquo; ' ) . __( 'Previous Page', 'genesis' ) ) );
	$next_link = get_next_posts_link( apply_filters( 'genesis_next_link_text', __( 'Next Page', 'genesis' ) . g_ent( ' &raquo;' ) ) );

	$prev = $prev_link ? '<div class="alignleft">' . $prev_link . '</div>' : '';
	$next = $next_link ? '<div class="alignright">' . $next_link . '</div>' : '';

	$nav = '<div class="navigation">' . $prev . $next . '</div><!-- end .navigation -->';

	if ( !empty( $prev ) || !empty( $next ) )
		echo $nav;
}

/**
 * Display links to previous/next post
 *
 * @since 1.5.1
 */
function genesis_prev_next_post_nav() {

	if ( !is_singular('post') )
		return;
?>

	<div class="navigation">
		<div class="alignleft"><?php previous_post_link(); ?></div>
		<div class="alignright"><?php next_post_link(); ?></div>
	</div>

<?php
}

/**
 * Display numeric posts navigation (similar to WP-PageNavi)
 *
 * @since 0.2.3
 */
function genesis_numeric_posts_nav() {
	if( is_singular() ) return; // do nothing

	global $wp_query;

	// Stop execution if there's only 1 page
	if( $wp_query->max_num_pages <= 1 ) return;

	$paged = get_query_var('paged') ? absint( get_query_var('paged') ) : 1;
	$max = intval( $wp_query->max_num_pages );

	echo '<div class="navigation"><ul>' . "\n";

	//	add current page to the array
	if ( $paged >= 1 )
		$links[] = $paged;

	//	add the pages around the current page to the array
	if ( $paged >= 3 ) {
		$links[] = $paged - 1; $links[] = $paged - 2;
	}
	if ( ($paged + 2) <= $max ) {
		$links[] = $paged + 2; $links[] = $paged + 1;
	}

	//	Previous Post Link
	if ( get_previous_posts_link() )
		printf( '<li>%s</li>' . "\n", get_previous_posts_link( g_ent( __('&laquo; Previous', 'genesis') ) ) );

	//	Link to first Page, plus ellipeses, if necessary
	if ( !in_array( 1, $links ) ) {
		if ( $paged == 1 ) $current = ' class="active"'; else $current = null;
		printf( '<li %s><a href="%s">%s</a></li>' . "\n", $current, get_pagenum_link(1), '1' );

		if ( !in_array( 2, $links ) )
			echo g_ent('<li>&hellip;</li>');
	}

	//	Link to Current page, plus 2 pages in either direction (if necessary).
	sort( $links );
	foreach( (array)$links as $link ) {
		$current = ( $paged == $link ) ? 'class="active"' : '';
		printf( '<li %s><a href="%s">%s</a></li>' . "\n", $current, get_pagenum_link($link), $link );
	}

	//	Link to last Page, plus ellipses, if necessary
	if ( !in_array( $max, $links ) ) {
		if ( !in_array( $max - 1, $links ) )
			echo g_ent('<li>&hellip;</li>') . "\n";

		$current = ( $paged == $max ) ? 'class="active"' : '';
		printf( '<li %s><a href="%s">%s</a></li>' . "\n", $current, get_pagenum_link($max), $max );
	}

	//	Next Post Link
	if ( get_next_posts_link() )
		printf( '<li>%s</li>' . "\n", get_next_posts_link( g_ent( __('Next &raquo;', 'genesis') ) ) );

	echo '</ul></div>' . "\n";
}