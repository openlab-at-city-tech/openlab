<?php get_header(); ?>

<main class="wrapper section-inner group" id="site-content">

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
		
</main><!-- .wrapper -->
		
<?php get_footer(); ?>