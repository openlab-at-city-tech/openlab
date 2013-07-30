<?php
/**
 * @package Coraline
 * @since Coraline 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<h1 class="page-title"><?php
			printf( __( 'Category Archives: %s', 'coraline' ), '<span>' . single_cat_title( '', false ) . '</span>' );
		?></h1>
		<?php
			$category_description = category_description();
			if ( ! empty( $category_description ) )
				printf( '<div class="archive-meta">%s</div>', $category_description );

			get_template_part( 'loop', 'category' );
		?>

	</div><!-- #content -->
</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>