<?php get_header(); ?>

<div class="wrapper section-inner group">

	<div class="content left">

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
	
	<?php get_sidebar(); ?>
		
</div><!-- .wrapper -->
		
<?php get_footer(); ?>