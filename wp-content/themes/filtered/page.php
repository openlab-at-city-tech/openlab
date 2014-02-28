<?php get_header(); ?>	
	<?php if(!is_front_page()):?>
	<div id="pageHead">
		<h1><?php the_title(); ?></h1>
	</div>
	<?php endif; ?>
	<div id="main" class="clearfix">			 
		<div id="content" class="twoThird clearfix">
			<?php while (have_posts()) : the_post(); ?>			    
			    <div <?php post_class('clearfix'); ?>>						
					<?php the_content(); ?>				
				</div>				
				<?php comments_template('', true); ?>			
			<?php endwhile; ?>					    	
		</div>		
		<?php get_sidebar(); ?>
	</div>	
<?php get_footer(); ?>
