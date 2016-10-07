<?php get_header(); 
get_template_part('breadcrums'); ?>
<div class="container">	
	<div class="row enigma_blog_wrapper">
	<div class="col-md-8">	
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>		
		<?php get_template_part('post','content'); 
		get_template_part('author','intro');
		endwhile; 
		else : 
		get_template_part('nocontent');
		endif;
		weblizar_navigation_posts();
		comments_template( '', true ); ?>
	</div>
	<?php get_sidebar(); ?>	
	</div> <!-- row div end here -->	
</div><!-- container div end here -->
<?php get_footer(); ?>