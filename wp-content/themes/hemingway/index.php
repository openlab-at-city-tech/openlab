<?php get_header(); ?>

<div class="wrapper section-inner">

	<div class="content left">
																		                    
		<?php if (have_posts()) : ?>
		
			<div class="posts">
		
				<?php
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$total_post_count = wp_count_posts();
				$published_post_count = $total_post_count->publish;
				$total_pages = ceil( $published_post_count / $posts_per_page );
				
				if ( "1" < $paged ) : ?>
				
					<div class="page-title">
					
						<h4><?php printf( __('Page %s of %s', 'hemingway'), $paged, $wp_query->max_num_pages ); ?></h4>
						
					</div>
					
					<div class="clear"></div>
				
				<?php endif; ?>
					
		    	<?php while (have_posts()) : the_post(); ?>
		    	
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		    	
			    		<?php get_template_part( 'content', get_post_format() ); ?>
			    				    		
		    		</div> <!-- /post -->
		    			        		            
		        <?php endwhile; ?>
	        	                    
			<?php endif; ?>
			
		</div> <!-- /posts -->
		
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		
			<div class="post-nav archive-nav">
						
				<?php echo get_next_posts_link( __('&laquo; Older<span> posts</span>', 'hemingway')); ?>
							
				<?php echo get_previous_posts_link( __('Newer<span> posts</span> &raquo;', 'hemingway')); ?>
				
				<div class="clear"></div>
				
			</div> <!-- /post-nav archive-nav -->
		
		<?php endif; ?>
			
	</div> <!-- /content.left -->
		
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div> <!-- /wrapper -->
	              	        
<?php get_footer(); ?>