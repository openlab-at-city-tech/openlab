<?php
/**
 * @package Coraline
 * @since Coraline 1.0
 */

get_header(); ?>

		<div id="content-container" class="image-attachment">
			<div id="content" role="main">

			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

					<div class="entry-meta">
						<?php
							if ( ! empty( $post->post_parent ) ) :

								$metadata = wp_get_attachment_metadata();
								printf( __( '<span class="meta-prep meta-prep-entry-date">Published </span> <span class="entry-date"><abbr class="published" title="%1$s">%2$s</abbr></span>  at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%7$s</a>', 'coraline' ),
									esc_attr( get_the_time() ),
									esc_html( get_the_date() ),
									wp_get_attachment_url(),
									$metadata['width'],
									$metadata['height'],
									esc_url( get_permalink( $post->post_parent ) ),
									esc_attr( get_the_title( $post->post_parent ) )
								);

								edit_post_link( __( 'Edit', 'coraline' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' );

							else :

							$metadata = wp_get_attachment_metadata();
							printf( __( '<span class="meta-prep meta-prep-entry-date">Uploaded </span> at <a href="%1$s" title="Link to full-size image">%2$s &times; %3$s</a>', 'coraline' ),
								wp_get_attachment_url(),
								$metadata['width'],
								$metadata['height']
							);
						?>
							<?php edit_post_link( __( 'Edit', 'coraline' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
						<?php endif; ?>
					</div><!-- .entry-meta -->

					<div id="image-navigation" class="entry-meta">
						<span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'coraline' ) ); ?></span>
						<span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'coraline' ) ); ?></span>
					</div><!-- #image-navigation -->

					<div class="entry-content">

						<div class="entry-attachment">
							<div class="attachment">
<?php
	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
	 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
	 */
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == get_the_ID() )
			break;
	}
	$k++;
	// If there is more than 1 attachment in a gallery
	if ( count( $attachments ) > 1 ) {
		if ( isset( $attachments[ $k ] ) )
			// get the URL of the next image attachment
			$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
		else
			// or get the URL of the first image attachment
			$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
	} else {
		// or, if there's only 1 image, get the URL of the image
		$next_attachment_url = wp_get_attachment_url();
	}
?>
								<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
								echo wp_get_attachment_image( get_the_ID(), 'coraline-image-template' );
								?></a>
							</div><!-- .attachment -->

							<?php if ( has_excerpt() ) : ?>
							<div class="entry-caption">
								<?php the_excerpt(); ?>
							</div>
							<?php endif; ?>
						</div><!-- .entry-attachment -->

						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'coraline' ), 'after' => '</div>' ) ); ?>

					</div><!-- .entry-content -->

					<div class="entry-info">
						<?php coraline_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'coraline' ), ' <span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-info -->
				</div><!-- #post-## -->

				<?php comments_template(); ?>

			<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>