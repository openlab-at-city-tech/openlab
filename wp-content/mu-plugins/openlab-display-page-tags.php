<?php

function openlab_display_page_tags( $content ) {
	if ( ! defined( 'PAGE_TAGGER_PARENT_DIR' ) ) {
		return $content;
	}

	if ( 'page' !== get_post_type( get_the_ID() ) ) {
		return $content;
	}

	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$content .= '<br />Tags: ' . $tag_list;
	}

	return $content;
}
add_filter( 'the_content', 'openlab_display_page_tags', 100 );

