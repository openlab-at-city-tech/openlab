<?php
/**
 *  Template for displaying gallery excerpt
 */
?>
        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h5 class="entry-format"><?php _e( 'Gallery', WEAVER_TRANS ); ?></h5>
	<h2 class="entry-title"><?php weaver_post_title(); ?></h2>
	<?php weaver_posted_on('blog'); ?>
	    <div class="entry-content">
		<?php
		$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );
		if ( $images ) {
			$total_images = count( $images );
			$image = array_shift( $images );
			$image_img_tag = wp_get_attachment_image( $image->ID, 'thumbnail' );
			?>
			<div class="gallery-thumb">
			<a class="size-thumbnail" href="<?php the_permalink(); ?>"><?php echo $image_img_tag; ?></a>
			</div> <!-- .gallery-thumb -->
			<p><em><?php printf( __( 'This gallery contains <a %1$s>%2$s photos</a>.', WEAVER_TRANS ),
				'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', WEAVER_TRANS ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
				$total_images
				); ?></em></p>
		<?php } ?>
			<?php the_excerpt(); ?>
	    </div><!-- .entry-content -->
		<?php weaver_posted_in('blog'); ?>
	</div><!-- #post-## -->
