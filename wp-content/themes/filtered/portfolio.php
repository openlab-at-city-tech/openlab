<?php /*
Template Name: Portfolio
*/ ?>
<?php get_header(); ?>		
	<div id="pageHead">
		<h1><?php the_title(); ?></h1>
	</div>
	<div id="main" class="clearfix page full">					 
		<div id="content" class="clearfix">
			<?php while (have_posts()) : the_post(); ?>			    
				<div class="post">						
					<?php the_content(); ?>				
				</div>						
			<?php endwhile; ?>
			
			<?php  include( TEMPLATEPATH . '/includes/projects-page.php'); ?>
								    	
		</div>
	</div>		
<?php get_footer(); ?>