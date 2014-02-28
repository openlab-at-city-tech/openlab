<?php get_header(); ?>
			
		<div id="pageHead">
			<?php global $wp_query; $current_author = $wp_query->get_queried_object(); ?>
			<h1><?php _e('Author:', 'themetrust'); ?> <?php echo $current_author->display_name; ?></h1>
		</div>	
		
		<div id="main" class="clearfix">					 
		<div id="content" class="twoThird clearfix">
			<?php $c=0; $post_count = $wp_query->post_count; ?>	
			<?php while (have_posts()) : the_post(); ?>
			<?php $c++; ?>
			<?php $postClass = has_post_thumbnail() ? $postClass = "withThumb" :  $postClass = ""; ?>			
			    
			    <div class="post clearfix <?php echo $postClass; ?>">
					<?php if(has_post_thumbnail()) : ?>												
				    		<?php the_post_thumbnail('ttrust_small', array('class' => 'postThumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?>			    	
					<?php endif; ?>
					
					<div class="inside">					
						<h1><a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_title(); ?></a></h1>
						<div class="meta clearfix">						
							<?php _e('Posted by', 'themetrust'); ?> <?php the_author_posts_link(); ?> <?php _e('on', 'themetrust'); ?> <?php the_time( 'M j, Y' ) ?> in <?php the_category(', ') ?><?php if(have_comments() || 'open' == $post->comment_status) : ?> | <a href="<?php comments_link(); ?>"><?php comments_number(__('No Comments', 'themetrust'), __('One Comment', 'themetrust'), __('% Comments', 'themetrust')); ?></a><?php endif; ?>
						</div>																	
						<?php the_excerpt(); ?>
						<?php more_link(); ?>						
					</div>																				
			    </div>				
			
			<?php endwhile; ?>
			
			<?php include( TEMPLATEPATH . '/includes/pagination.php'); ?>
					    	
		</div>		
		<?php get_sidebar(); ?>				
	</div>	
<?php get_footer(); ?>
