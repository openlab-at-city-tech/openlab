<?php get_header(); ?>		
<?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>
	<div id="pageHead">
		<h1><?php echo $term->name; ?></h1>
	</div>
	<div id="main" class="clearfix page full">					 
		<div id="content" class="clearfix">
			<?php $term_description = $term->description; ?>
			<?php if($term_description) : ?>
				<p><?php echo $term_description; ?></p>
			<?php endif; ?>
			<?php
			$args = array(			  
				$term->taxonomy => $term->slug,			
			  	'post_type' => 'projects',
			  	'posts_per_page' => 200			
				);
			query_posts($args);
			?>						
			<?php  include( TEMPLATEPATH . '/includes/project-grid.php'); ?>
								    	
		</div>
	</div>		
<?php get_footer(); ?>