<?php

/* Template Name: Full Width Template */

get_header(); ?>

<div class="wrapper section-inner group">						

	<div class="content full-width">

		<div class="posts">
												        
			<?php 
			
			if ( have_posts() ) : 
				
				while ( have_posts() ) : the_post(); 

					get_template_part( 'content', get_post_type() );

				endwhile;
			
			endif; 
			
			?>
			
		</div><!-- .posts -->
	
	</div><!-- .content -->
	
</div><!-- .wrapper section-inner -->
								
<?php get_footer(); ?>