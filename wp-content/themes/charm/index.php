<?php get_header(); ?>

<div class="page-content">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
			get_template_part( 'content' );
		endwhile;

		rain_posts_pagination();
	else :
		get_template_part( 'content-none' );
	endif;
	?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>