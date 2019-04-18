<?php /* Template Name: Contact */ ?>

<?php get_header(); ?>

<div class="page-content">
	<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'content-page' );
	endwhile;
	?>
</div>

<?php get_sidebar( 'contact' ); ?>
<?php get_footer(); ?>