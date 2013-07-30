<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<h1 class="page-title archive-head">
			<?php printf( __( 'Tag Archives: %s', 'pilcrow' ), '<span>' . single_tag_title( '', false ) . '</span>' ); ?>
		</h1>

		<?php
			/* Run the loop for the tag archive to output the posts
			 * If you want to overload this in a child theme then include a file
			 * called loop-tag.php and that will be used instead.
			 */
			get_template_part( 'loop', 'tag' );
		?>
	</div><!-- #content -->
</div><!-- #content-container -->

<?php
get_sidebar();
get_footer();
