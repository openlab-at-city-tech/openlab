<?php if( typology_get_option('single_sticky_bottom_bar') ) : ?>
	<div id="typology-single-sticky" class="typology-single-sticky">
		
		<div class="typology-sticky-content meta">
			<?php get_template_part('template-parts/single/sticky-meta'); ?>
		</div>

		<div class="typology-sticky-content prev-next">
			<?php get_template_part('template-parts/single/sticky-prev-next'); ?>
		</div>
	</div>
<?php endif; ?>