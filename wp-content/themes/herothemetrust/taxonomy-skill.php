<?php get_header(); ?>

<?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' )); ?>
<div id="pageHead">
	<h1><?php echo $term->name; ?></h1>			
	<?php if(strlen($term->description) > 0) echo '<p>'.$term->description.'</p>'; ?>
</div>
<div id="content" class="fullProjects clearfix full grid">	
<div id="projects">		
	<div class="thumbs masonry">
	<?php query_posts( 'skill='.$term->slug.'&post_type=project&posts_per_page=200' ); ?>			
	<?php  while (have_posts()) : the_post(); ?>		
		<?php get_template_part( 'part-project-thumb'); ?>	
	<?php endwhile; ?>
	</div>	
</div>
</div>

<?php get_footer(); ?>