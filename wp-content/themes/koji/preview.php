<article <?php post_class( 'preview preview-' . get_post_type() . ' do-spot' ); ?> id="post-<?php the_ID(); ?>">

	<div class="preview-wrapper">

		<?php if ( ( has_post_thumbnail() && ! post_password_required() ) || koji_get_fallback_image_url() ) : ?>

			<a href="<?php the_permalink(); ?>" class="preview-image">

				<?php
				if ( has_post_thumbnail() && ! post_password_required() ) {
					$image_size = koji_get_preview_image_size();
					the_post_thumbnail( $image_size );
				} else {
					koji_the_fallback_image();
				}
				?>
				
			</a>

		<?php endif; ?>

		<div class="preview-inner">

			<h2 class="preview-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

			<?php

			// Output the post meta
			koji_the_post_meta( $post->ID, 'preview' ); ?>

		</div><!-- .preview-inner -->

	</div><!-- .preview-wrapper -->

</article>
