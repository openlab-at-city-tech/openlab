<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php get_header(); ?>

	<div class="span-24 last">
	<div class="content">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php the_title(); ?></h2>
			<div class="entry">
<?php
	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
	 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
	 */
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID )
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
				<p class="image-attachment"><a href="<?php echo $next_attachment_url; ?>">
					<?php echo wp_get_attachment_image( $post->ID, array( 949, 9999 ) ); ?>
				</a></p>

				<div class="clear"></div>

								<div class="caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the "caption" ?></div>

				<?php the_content( __( '<p class="serif">Read the rest of this entry &raquo;</p>', 'modularity' ) ); ?>

				<div class="navigation">
					<div class="alignleft"><?php previous_image_link() ?></div>
					<div class="alignright"><?php next_image_link() ?></div>
				</div>
				<div class="clear"></div>

				<p class="postmetadata alt">
					<small>
						<?php
							$metadata = wp_get_attachment_metadata();
							printf( __( '<span class="meta-prep meta-prep-entry-date">Published </span> <span class="entry-date"><abbr class="published" title="%1$s">%2$s</abbr></span>  at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%7$s</a>', 'modularity'),
								esc_attr( get_the_time() ),
								get_the_date(),
								wp_get_attachment_url(),
								$metadata['width'],
								$metadata['height'],
								get_permalink( $post->post_parent ),
								get_the_title( $post->post_parent )
							);
						?>
						<?php edit_post_link( __( 'Edit', 'modularity' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
					</small>
				</p>

			</div>

		</div>

	<?php comments_template(); ?>

	</div>
	</div>

	<?php endwhile; else: ?>

		<p><?php _e( 'Sorry, no attachments matched your criteria.', 'modularity' ); ?></p>

<?php endif; ?>

<!-- Begin Footer -->
<?php get_footer(); ?>