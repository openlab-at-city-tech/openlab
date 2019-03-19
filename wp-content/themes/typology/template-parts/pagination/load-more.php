<?php if ( $more_link = get_next_posts_link( __typology('load_more') ) ) : ?>
	<div class="typology-pagination">
		<nav class="navigation load-more">
		    <?php echo wp_kses_post( $more_link ); ?>
		    <div class="typology-loader">
				  <div class="dot dot1"></div>
				  <div class="dot dot2"></div>
				  <div class="dot dot3"></div>
				  <div class="dot dot4"></div>
		    </div>
		</nav>
	</div>
<?php endif; ?>