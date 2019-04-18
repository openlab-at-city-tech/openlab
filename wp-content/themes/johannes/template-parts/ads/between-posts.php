<?php if( $ad = johannes_get('ads', 'between_posts') ): ?>
	<div class="col-12 johannes-ad ad-between-posts d-flex justify-content-center vertical-gutter-flow"><?php echo do_shortcode( $ad ); ?></div>
<?php endif; ?>