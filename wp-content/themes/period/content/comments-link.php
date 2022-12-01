<span class="comments-link">
	<i class="fas fa-comment" aria-hidden="true" title="<?php esc_attr_e( 'comment icon', 'period' ); ?>"></i>
	<?php
	if ( ! comments_open() && get_comments_number() < 1 ) :
		comments_number( esc_html__( 'Comments closed', 'period' ), esc_html__( '1 Comment', 'period' ), esc_html_x( '% Comments', 'noun: 5 comments', 'period' ) );
	else :
		echo '<a href="' . esc_url( get_comments_link() ) . '">';
		comments_number( esc_html__( 'Leave a Comment', 'period' ), esc_html__( '1 Comment', 'period' ), esc_html_x( '% Comments', 'noun: 5 comments', 'period' ) );
		echo '</a>';
	endif;
	?>
</span>