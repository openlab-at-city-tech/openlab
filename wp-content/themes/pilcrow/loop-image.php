<?php
/**
 * The loop that displays an attachment.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop-image.php.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

while ( have_posts() ) :
	the_post();

	$format   = empty( $post->post_parent ) ? __( 'Published %1$s', 'pilcrow' ) :  __( 'Published %1$s in %2$s', 'pilcrow' );
	$metadata = wp_get_attachment_metadata();
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-meta">
		<?php
			printf( $format,
				sprintf( '<span class="entry-date"><abbr class="published" title="%1$s">%2$s</abbr></span>',
					esc_attr( get_the_time() ),
					get_the_date()
				),
				sprintf( '<a href="%1$s" title="%2$s" rel="gallery">%3$s</a>',
					get_permalink( $post->post_parent ),
					esc_attr( sprintf( __( 'Return to %s', 'pilcrow' ), get_the_title( $post->post_parent ) ) ),
					get_the_title( $post->post_parent )
				)
			);

			echo ' <span class="meta-sep">|</span> ';

			printf( __( 'Full size is %s pixels', 'pilcrow' ),
				sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
					wp_get_attachment_url(),
					esc_attr( __( 'Link to full-size image', 'pilcrow' ) ),
					$metadata['width'],
					$metadata['height']
				)
			);

			edit_post_link( __( 'Edit', 'pilcrow' ), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>' );
		?>
	</div><!-- .entry-meta -->

	<div id="image-navigation">
		<span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'pilcrow' ) ); ?></span>
		<span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'pilcrow' ) ); ?></span>
	</div><!-- #image-navigation -->

	<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>

	<div class="entry entry-content">
		<div class="entry-attachment">

			<p class="attachment">
				<?php pilcrow_the_attached_image(); ?>
			</p>

		</div><!-- .entry-attachment -->
		<div class="entry-caption"><?php the_excerpt(); ?></div>

		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'pilcrow' ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'pilcrow' ), 'after' => '</div>' ) ); ?>

	</div><!-- .entry-content -->
</div><!-- #post-## -->

<?php comments_template(); ?>

<?php endwhile; // end of the loop.
