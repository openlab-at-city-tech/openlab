<?php 

$themeoptions_display_post_featured_image 	= esc_attr(get_theme_mod( 'theme-display-post-featured-image', 0 ));
if ( $themeoptions_display_post_featured_image != 1 ) {
	return;
}

$attachments_num = 0;
if ( has_post_thumbnail( $post->ID ) ) {
	$featured_image_id = get_post_thumbnail_id($post->ID);

	unset($attachments,$attachments_num);
	
	$args = array(
		'include'    => $featured_image_id,
		'post_type'      => 'attachment',
		'post_mime_type' => 'image'
	);
	
	$attachments = get_posts($args);
	$attachments_num = count($attachments);

}

$i = 0;

if ( $attachments_num == 1 ) { ?>

	<div id="site-section-slideshow" class="site-section-slideshow-withimage">
		<ul class="site-slideshow-list academia-slideshow">
		<?php
		foreach ($attachments as $attachment) {
			$i++;
			$large_image_url = wp_get_attachment_image_src( $attachment->ID, 'thumb-academia-slideshow');
			?>
			<li class="site-slideshow-item">
				<div class="slideshow-hero-wrapper"<?php if ( isset($large_image_url) ) { echo ' style="background-image: url( ' . esc_url($large_image_url[0]) . ');"'; } ?>>
				</div><!-- .slideshow-hero-wrapper -->
			</li><!-- .site-slideshow-item -->
		<?php } // foreach ?>
		</ul><!-- .site-slideshow-list .academia-slideshow -->
	</div><!-- #site-section-slideshow -->

<?php } ?>