<?php if( $ad = johannes_get('ads', 'above_archive') ): ?>
	<div class="container">
	    <div class="johannes-ad ad-above-archive d-flex justify-content-center vertical-gutter-flow"><?php echo do_shortcode( $ad ); ?></div>
	</div>
<?php endif; ?>