<?php get_header(); ?>
			
		<div id="pageHead">
			<?php global $post; if(is_archive() && have_posts()) :

				if (is_category()) : ?>
				<h1><?php single_cat_title(); ?></h1>
				<?php elseif( is_tag() ) : ?>
				<h1><?php single_tag_title(); ?></h1>
				<?php elseif (is_day()) : ?>
				<h1><?php _e('Archive', 'themetrust'); ?> <?php the_time('M j, Y'); ?></h1>
				<?php elseif (is_month()) : ?>
				<h1><?php _e('Archive', 'themetrust'); ?> <?php the_time('F Y'); ?></h1>
				<?php elseif (is_year()) : ?>
				<h1><?php _e('Archive', 'themetrust'); ?> <?php the_time('Y'); ?></h1>
				<?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
				 <h1><?php _e('Archive', 'themetrust'); ?></h1>
				<?php endif; ?>

			<?php endif; ?>
		</div>	
		
	<div id="main" class="clearfix">					 
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
	</div>
		
<?php get_footer(); ?>