<?php
/**
 * The template for displaying image attachments.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

<div id="content-container" class="image-attachment">
	<div id="content" role="main">

	<?php
		/* Run the loop to output the attachment.
		 * If you want to overload this in a child theme then include a file
		 * called loop-attachment.php and that will be used instead.
		 */
		get_template_part( 'loop', 'image' );
	?>

	</div><!-- #content -->
</div><!-- #container -->

<?php get_footer();
