<?php if ( $more_link = get_next_posts_link( __johannes('load_more') ) ) : ?>

	<div class="col-12 text-center johannes-order-2">
	    <nav class="johannes-pagination load-more">
	        <?php echo wp_kses_post( $more_link ); ?>
	        <div class="johannes-loader">
				<div class="johannes-ellipsis"><div></div><div></div><div></div><div></div></div>
	        </div>
	    </nav>
	</div>

<?php endif; ?>