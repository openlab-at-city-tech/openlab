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

	<?php if ( ! is_page_template( array( 'template-fullwidth.php', 'template-nosidebar.php' ) ) ) : ?>
	
		<?php get_sidebar(); ?>

	<?php endif; ?>
		
</main><!-- .wrapper -->
		
<?php get_footer(); ?>