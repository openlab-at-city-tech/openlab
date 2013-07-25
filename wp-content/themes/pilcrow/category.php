<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<h1 class="page-title archive-head">
			<?php printf( __( 'Category Archives: %s', 'pilcrow' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?>
		</h1>

		<?php
			$category_description = category_description();
			if ( ! empty( $category_description ) )
				printf( '<div class="archive-meta">%s</div>', $category_description );

			/* Run the loop for the category page to output the posts.
			 * If you want to overload this in a child theme then include a file
			 * called loop-category.php and that will be used instead.
			 */
			get_template_part( 'loop', 'category' );
		?>

	</div><!-- #content -->
</div><!-- #content-container -->

<?php
get_sidebar();
get_footer();
