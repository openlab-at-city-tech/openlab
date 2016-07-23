<?php
/**
 * @package Coraline
 * @since Coraline 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

	<?php if ( have_posts() ) the_post(); ?>

		<h1 class="page-title author"><?php printf( __( 'Author Archives: %s', 'coraline' ), "<span class='vcard'><a class='url fn n' href='" . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></h1>

	<?php
		rewind_posts();
		get_template_part( 'loop', 'author' );
	?>
	</div><!-- #content -->
</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>