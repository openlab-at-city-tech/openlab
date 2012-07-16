<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

			<div class="post-info">

				<p class="page-title"><a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf('Return to %s', get_the_title( $post->post_parent ) ) ); ?>" rel="gallery"><?php
					/* translators: %s - title of parent post */
					printf('&laquo; %s', get_the_title( $post->post_parent ) );
				?></a></p>
				
				<h1><?php the_title(); ?></h1>
				
				<?php $metadata = wp_get_attachment_metadata(); ?>
				<div class="timestamp"><?php the_time( get_option( 'date_format' ) ); ?>  //<br />
					<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo 'Permalink to full-size image'; ?>"><?php echo $metadata['width']; ?> &times; <?php echo $metadata['height']; ?></a>
				</div> 
				<?php if ( comments_open() ) : ?><div class="comment-bubble"><a href="#comments"><?php comments_number('0', '1', '%'); ?></a></div><?php endif; ?>
				<div class="clearboth"><!-- --></div>

				<?php edit_post_link('Edit this entry', '<p>', '</p>' ); ?>

			</div>


			<div class="post-content image-attachment">
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
				<div class="attachment">
					<p><a href="<?php echo $next_attachment_url;  ?>">
						<?php echo wp_get_attachment_image( $post->ID, array( 460, 9999 ) ); ?>
					</a></p>
				</div>
				
				<?php if ( !empty( $post->post_excerpt ) ) : ?>
				<div class="entry-caption">
					<?php the_excerpt(); ?>
				</div>
				<?php endif; ?>
								
				<?php the_content(); ?>

				<?php wp_link_pages( array('before' => '<p><strong>' . 'Pages:' . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<div class="navigation">
					<div class="alignleft"><?php previous_image_link(false, 'Previous'); ?></div>
					<div class="alignright"><?php next_image_link( false, 'Next'); ?></div>
					<div class="clearboth"><!-- --></div>
				</div>				

			</div>

			<div class="clearboth"><!-- --></div>

		</div>

	<?php comments_template(); ?>

	<!-- <?php trackback_rdf(); ?> -->

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
