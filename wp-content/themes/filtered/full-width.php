<?php /*
Template Name: Full Width
*/ ?>
<?php get_header(); ?>		
	<?php if(!is_front_page()):?>
	<div id="pageHead">
		<h1><?php the_title(); ?></h1>
	</div>
	<?php endif; ?>
	<div id="main" class="clearfix page full">					 
		<div id="content" class="clearfix">
			<?php while (have_posts()) : the_post(); ?>			    
				<div <?php post_class('clearfix'); ?>>						
					<?php the_content(); ?>				
				</div>				
				<?php comments_template('', true); ?>			
			<?php endwhile; ?>					    	
		</div>
	</div>		
<?php get_footer(); ?>