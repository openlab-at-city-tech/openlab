<?php get_header(); ?>

<div class="page-content">
	<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'content-portfolio' );

		rain_project_navigation();
	endwhile;
	?>
</div>

<?php get_footer(); ?>