<?php
/**
 * @package Coraline
 * @since Coraline 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<h1 class="page-title"><?php
			printf( __( 'Tag Archives: %s', 'coraline' ), '<span>' . single_tag_title( '', false ) . '</span>' );
		?></h1>

		<?php get_template_part( 'loop', 'tag' ); ?>
	</div><!-- #content -->
</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>