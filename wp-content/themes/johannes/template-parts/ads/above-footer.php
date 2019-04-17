<?php if( $ad = johannes_get('ads', 'above_footer') ): ?>
	<div class="container">
	    <div class="johannes-ad ad-above-footer d-flex justify-content-center vertical-gutter-flow"><?php echo do_shortcode( $ad ); ?></div>
	</div>
<?php endif; ?>