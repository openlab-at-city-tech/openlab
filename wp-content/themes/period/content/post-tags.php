<?php
$tags   = get_the_tags( $post->ID );
$output = '';
if ( $tags ) {
	echo '<div class="post-tags">';
		echo '<ul>';
			foreach ( $tags as $tag ) {
				echo '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" title="' . esc_attr( sprintf( esc_html__( "View all posts tagged %s", 'period' ), $tag->name ) ) . '">' . esc_html( $tag->name ) . '</a></li>';
			}
		echo '</ul>';
	echo '</div>';
}