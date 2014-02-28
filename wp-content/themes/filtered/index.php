<?php get_header(); ?>	
		<div id="main" class="clearfix">
			<?php $ttrust_posts_on_home = of_get_option('ttrust_posts_on_home'); ?>
			<?php if($ttrust_posts_on_home) : ?>				
			<div id="content" class="twoThird clearfix">				
				<?php $c=0; $post_count = $wp_query->post_count; ?>	
				<?php while (have_posts()) : the_post(); ?>
				<?php $c++; ?>
				<?php $postClass = has_post_thumbnail() ? $postClass = "withThumb" :  $postClass = ""; ?>				
				<?php $postClass .= " clearfix"; ?>
				    <div <?php post_class($postClass); ?>>
						<?php if(has_post_thumbnail()) : ?>												
					    		<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('ttrust_small', array('class' => 'postThumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>			    	
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
			
			<?php else : ?>	
				<div id="content" class="full clearfix">							 
					<?php  include( TEMPLATEPATH . '/includes/projects-home.php'); ?>
				</div>
			
			<?php endif; ?>
			
		</div>
	
<?php get_footer(); ?>