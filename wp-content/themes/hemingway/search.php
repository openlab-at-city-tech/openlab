<?php get_header(); ?>

	<div class="wrapper section-inner">
	
		<div class="content left">

		<?php if ( have_posts() ) : ?>
		
			<div class="page-title">
			
				<h4>
			
					<?php _e( 'Search results:', 'hemingway'); echo ' "' . get_search_query() . '"'; ?>
				
					<?php
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					
					if ( "1" < $wp_query->max_num_pages ) : ?>
					
						<span><?php printf( __('(page %s of %s)', 'hemingway'), $paged, $wp_query->max_num_pages ); ?></span>
					
					<?php endif; ?>
				
				</h4>
				
			</div>
					
			<div class="posts">
	
				<?php while ( have_posts() ) : the_post(); ?>
				
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								
						<?php get_template_part( 'content', get_post_format() ); ?>						
					
					</div> <!-- /post -->
					
				<?php endwhile; ?>
							
			</div> <!-- /posts -->
			
			<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			
				<div class="post-nav archive-nav">
				
					<?php echo get_next_posts_link( __('&laquo; Older<span> posts</span>', 'hemingway')); ?>
								
					<?php echo get_previous_posts_link( __('Newer<span> posts</span> &raquo;', 'hemingway')); ?>
					
					<div class="clear"></div>
					
				</div>
				
			<?php endif; ?>
	
		<?php else : ?>
			
			<div class="posts">
			
				<div class="page-title">
			
					<h4>
				
						<?php echo _e( 'Search results:', 'hemingway') . ' "' . get_search_query() . '"'; ?>
					
						<?php
						$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
						
						if ( "1" < $wp_query->max_num_pages ) : ?>
						
							<span><?php printf( __('(page %s of %s)', 'hemingway'), $paged, $wp_query->max_num_pages ); ?></span>
						
						<?php endif; ?>
						
					</h4>
					
				</div>
				
				<div class="clear"></div>
			
				<div class="post">
				
					<div class="content-inner">
				
						<div class="post-content">
						
							<p><?php _e('No results. Try again, would you kindly?', 'hemingway'); ?></p>
							
							<?php get_search_form(); ?>
						
						</div> <!-- /post-content -->
					
					</div> <!-- /content-inner -->
					
					<div class="clear"></div>
				
				</div> <!-- /post -->
			
			</div> <!-- /posts -->
		
		<?php endif; ?>
		
		</div> <!-- /content -->
		
		<?php get_sidebar(); ?>
		
		<div class="clear"></div>
		
	</div> <!-- /wrapper -->
		
<?php get_footer(); ?>