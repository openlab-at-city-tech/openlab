<?php get_header(); ?>
	<div id="pageHead">
		<h1><?php _e('Search Results', 'themetrust'); ?></h1>
	</div>
	<div id="main" class="clearfix">				 
	<div id="content" class="twoThird clearfix">
	<?php if ( have_posts() ) : ?>
		<?php $c=0; $post_count = $wp_query->post_count; ?>	
		<?php while (have_posts()) : the_post(); ?>
			<?php $c++; ?>
			<?php $postClass = has_post_thumbnail() ? $postClass = "withThumb" :  $postClass = ""; ?>			
			<div class="post clearfix <?php echo $postClass; ?>">										
									
				<?php if(has_post_thumbnail()) : ?>												
					<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('ttrust_small', array('class' => 'postThumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>			    	
				<?php endif; ?>																	
				<div class="inside">
					<h1><a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_title(); ?></a></h1>	
					<?php the_excerpt('',TRUE); ?>
				</div>																									
			</div>				
			
		<?php endwhile; ?>
		<?php include( TEMPLATEPATH . '/includes/pagination.php'); ?>
	<?php else : ?>	
		
		<div class="post clearfix">																			
			<div class="inside">					
				<h2><?php _e('No Matches Found', 'themetrust'); ?></h2>
				<p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'themetrust'); ?></p>
			</div>																									
		</div>
		
	<?php endif; ?>					    	
	</div>
		
	<?php get_sidebar(); ?>	
	</div>				
	
<?php get_footer(); ?>