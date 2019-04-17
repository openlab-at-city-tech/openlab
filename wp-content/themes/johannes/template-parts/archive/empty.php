<div class="col-12">
	<article <?php post_class( 'johannes-post' ); ?>>
	        <div class="entry-content">
	            <?php if ( is_search() ) : ?>
						<p><?php echo __johannes( 'content_none_search' ); ?></p>
				<?php else: ?>
					<p><?php echo __johannes( 'content_none' ); ?></p>
				<?php endif; ?>
	        </div>
	</article>
</div>
