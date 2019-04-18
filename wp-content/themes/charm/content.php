<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_single() ) : ?>

		<div class="post-content"><?php the_content(); ?></div>

		<div class="post-footer">
			<?php tr_share(); ?>
			<?php the_tags( '<div class="post-tags"><h6>' . __( 'Tags', 'themerain' ) . '</h6>', '', '</div>' ); ?>
		</div>

	<?php else : ?>

		<?php if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) { ?>
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail(); ?>
				</a>
			</div>
		<?php } ?>

		<div class="post-header">
			<div class="post-category"><?php the_category( ', ' ); ?></div>
			<?php the_title( '<h1 class="post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
			<div class="post-time"><?php the_time( get_option( 'date_format' ) ); ?></div>
		</div>

		<div class="post-content"><?php the_excerpt(); ?></div>

		<div class="post-more"><a href="<?php the_permalink(); ?>"><?php _e( 'Read more', 'themerain' ); ?></a></div>

	<?php endif; ?>

</article>