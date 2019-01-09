<div class="su-posts su-posts-default-loop">

	<?php if ( $posts->have_posts() ) : ?>

		<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>

			<div id="su-post-<?php the_ID(); ?>" class="su-post">

				<?php if ( has_post_thumbnail( get_the_ID() ) ) : ?>
					<a class="su-post-thumbnail" href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
				<?php endif; ?>

				<h2 class="su-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

				<div class="su-post-meta">
					<?php _e( 'Posted', 'shortcodes-ultimate' ); ?>: <?php the_time( get_option( 'date_format' ) ); ?>
				</div>

				<div class="su-post-excerpt">
					<?php the_excerpt(); ?>
				</div>

				<a href="<?php comments_link(); ?>" class="su-post-comments-link"><?php comments_number( __( '0 comments', 'shortcodes-ultimate' ), __( '1 comment', 'shortcodes-ultimate' ), '% comments' ); ?></a>

			</div>

		<?php endwhile; ?>

	<?php else : ?>
		<h4><?php _e( 'Posts not found', 'shortcodes-ultimate' ); ?></h4>
	<?php endif; ?>

</div>
