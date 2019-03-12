<?php if( $pagination = get_the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __typology( 'previous_posts' ), 'next_text' => __typology( 'next_posts' ) ) ) ) : ?>
	<div class="typology-pagination">
		<?php echo wp_kses_post( $pagination ); ?>
	</div>
<?php endif; ?>
