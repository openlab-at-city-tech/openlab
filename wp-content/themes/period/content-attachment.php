<div <?php post_class(); ?>>
	<?php do_action( 'attachment_before' ); ?>
	<article>
		<div class="post-container">
			<div class='post-header'>
				<h1 class='post-title'><?php the_title(); ?></h1>
			</div>
			<div class="post-content">
				<?php
				$image = wp_get_attachment_image($post->ID, 'full');
				$image_meta = wp_prepare_attachment_for_js($post->ID);
				?>
				<div class="attachment-container">
					<?php echo $image; ?>
					<span class="attachment-caption">
						<?php echo esc_html( $image_meta['caption'] ); ?>
					</span>
				</div>
				<?php echo wpautop( esc_html( $image_meta['description'] ) ); ?>
				<div class="post-meta">
					<?php get_template_part( 'content/post-nav-attachment' ); ?>
				</div>
			</div>
		</div>
	</article>
	<?php do_action( 'attachment_after' ); ?>
	<?php comments_template(); ?>
</div>