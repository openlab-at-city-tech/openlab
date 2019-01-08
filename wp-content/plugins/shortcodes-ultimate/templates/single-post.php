<div class="su-posts su-posts-single-post">
	<?php
		// Prepare marker to show only one post
		$first = true;
		// Posts are found
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) :
				$posts->the_post();
				global $post;

				// Show oly first post
				if ( $first ) {
					$first = false;
					?>
					<div id="su-post-<?php the_ID(); ?>" class="su-post">
						<h1 class="su-post-title"><?php the_title(); ?></h1>
						<div class="su-post-meta"><?php _e( 'Posted', 'shortcodes-ultimate' ); ?>: <?php the_time( get_option( 'date_format' ) ); ?> | <a href="<?php comments_link(); ?>" class="su-post-comments-link"><?php comments_number( __( '0 comments', 'shortcodes-ultimate' ), __( '1 comment', 'shortcodes-ultimate' ), __( '%n comments', 'shortcodes-ultimate' ) ); ?></a></div>
						<div class="su-post-content">
							<?php the_content(); ?>
						</div>
					</div>
					<?php
				}
			endwhile;
		}
		// Posts not found
		else {
			echo '<h4>' . __( 'Posts not found', 'shortcodes-ultimate' ) . '</h4>';
		}
	?>
</div>