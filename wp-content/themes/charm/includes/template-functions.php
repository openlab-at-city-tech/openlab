<?php

/**
 * Custom excerpt more.
 */
function tr_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'tr_excerpt_more' );

/**
 * Exclude pages and projects from search.
 */
function tr_search_filter( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( $query->is_search ) {
			$query->set( 'post_type', 'post' );
		}
	}
}
add_filter( 'pre_get_posts', 'tr_search_filter' );

/**
 * Add fancyBox to gallery images.
 */
function tr_gallery_fancybox( $content, $id ) {
	$post = get_post( $id );
	return str_replace( '<a ', '<a data-fancybox="gallery" data-caption="' . $post->post_excerpt . '" ', $content );
}
add_filter( 'wp_get_attachment_link', 'tr_gallery_fancybox', 10, 4 );

/**
 * Add fancyBox to images.
 */
function tr_image_fancybox( $content ) {
	$pattern = '/<a href="(.*?).(jpg|jpeg|png|gif|bmp|ico)"><img(.*?)class="(.*?)wp-image-(.*?)" \/><\/a>/i';
	preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

	foreach ( $matches as $val ) {
		$post = get_post( $val[5] );
		$string = '<a href="' . $val[1] . '.' . $val[2] . '"><img' . $val[3] . 'class="' . $val[4] . 'wp-image-' . $val[5] . '" /></a>';
		$replace = '<a href="' . $val[1] . '.' . $val[2] . '" data-fancybox="gallery" data-caption="' . $post->post_excerpt . '"><img' . $val[3] . 'class="' . $val[4] . 'wp-image-' . $val[5] . '" /></a>';
		$content = str_replace( $string, $replace, $content );
	}

	return $content;
}
add_filter( 'the_content', 'tr_image_fancybox' );