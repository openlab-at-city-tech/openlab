<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php get_header(); ?>

<?php if ( is_home() && ! is_paged() ) : /* Show the welcome box and slideshow only on first page.  Makes for better pagination. */ ?>

	<?php
		$options = get_option( 'modularity_theme_options' );
		if ( $options['welcome_content'] != '' ) :
	?>
	
	<div class="welcomebox entry">
		<?php if ( $options['welcome_title'] != '' ) echo '<p>' . stripslashes( $options['welcome_title'] ) . '</p>'  ?>
		<div id="welcome-content"><?php echo stripslashes( $options['welcome_content'] ); ?></div>
	</div><!--end welcome-box-->
	
	<?php endif; ?>
	
	<?php if ( $options['slideshow'] != 0 ) : ?>
	<div id="slideshow">
	<?php	
	// Start a new query that looks at all the posts on the home page.
	$mod_slide = new WP_Query();
	$mod_slide->query( 'order=DESC&caller_get_posts=1' );

	while ($mod_slide->have_posts()) : $mod_slide->the_post();
		
		// Check to see if the posts have attachments.
		$attachments = get_children(array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order'));
		if ( empty( $attachments ) ) 
			continue;
		
		// Set our attachment variable.
		$first_attachment = '';
		
		// Awesome! We have attachments! Let's grab the first one that's 950px x 425px.
		foreach ( $attachments as $attachment ) {
			$image = wp_get_attachment_image_src( $attachment->ID, 'modularity-slideshow' );

			if ( $image[1] >= 950 && $image[2] >= 425 ) {
				$first_attachment = $attachment;
				break;
			}
		}	
		
		// Cool. Now, if we actually have an attachment, let's pop it into our slideshow.
		if ( $first_attachment != '' ) { ?>
			<div class="slide"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo wp_get_attachment_image($first_attachment->ID, 'modularity-slideshow' ); ?></a></div>
		<?php }
			
	endwhile;
	?>
	</div><!-- end slideshow -->
	<?php endif; ?>
	
<?php endif; /* End Better Pagination */ ?>

<?php get_template_part( 'blog' ); ?>

<?php get_footer(); ?>