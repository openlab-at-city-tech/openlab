<div class="section-content">
	<div class="entry-content">
		<?php if(is_search()) : ?>
			<p><?php echo __typology( 'content_none_search' ); ?></p>
			<p><?php get_search_form(); ?></p>
		<?php else: ?>
				<p><?php echo __typology( 'content_none' ); ?></p>
			<?php endif; ?>
	</div>
</div>