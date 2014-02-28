<?php get_header(); ?>

		<div id="pageHead">
			<?php global $post; if(is_archive() && have_posts()) :

				if (is_category()) : ?>
					<h1><?php single_cat_title(); ?></h1>				
					<?php if(strlen(category_description()) > 0) echo category_description(); ?>
				<?php elseif( is_tag() ) : ?>
					<h1><?php single_tag_title(); ?></h1>
				<?php elseif (is_day()) : ?>
					<h1>Archive <?php the_time('M j, Y'); ?></h1>
				<?php elseif (is_month()) : ?>
					<h1>Archive <?php the_time('F Y'); ?></h1>
				<?php elseif (is_year()) : ?>
					<h1>Archive <?php the_time('Y'); ?></h1>
				<?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
					<h1>Archive</h1>
				<?php endif; ?>

			<?php endif; ?>
		</div>
		
						 
		<div id="content" class="twoThirds clearfix">			
			<?php while (have_posts()) : the_post(); ?>			    
				<div <?php post_class(); ?>>					
					<div class="inside">															
						<h1><a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_title(); ?></a></h1>
						<div class="meta clearfix">
							<?php $post_show_author = of_get_option('ttrust_post_show_author'); ?>
							<?php $post_show_date = of_get_option('ttrust_post_show_date'); ?>
							<?php $post_show_category = of_get_option('ttrust_post_show_category'); ?>
							<?php $post_show_comments = of_get_option('ttrust_post_show_comments'); ?>
										
							<?php if($post_show_author || $post_show_date || $post_show_category){ _e('Posted ', 'themetrust'); } ?>					
							<?php if($post_show_author) { _e('by ', 'themetrust'); the_author_posts_link(); }?>
							<?php if($post_show_date) { _e('on', 'themetrust'); ?> <?php the_time( 'M j, Y' ); } ?>
							<?php if($post_show_category) { _e('in', 'themetrust'); ?> <?php the_category(', '); } ?>
							<?php if(($post_show_author || $post_show_date || $post_show_category) && $post_show_comments){ echo " | "; } ?>
							
							<?php if($post_show_comments) : ?>
								<a href="<?php comments_link(); ?>"><?php comments_number(__('No Comments', 'themetrust'), __('One Comment', 'themetrust'), __('% Comments', 'themetrust')); ?></a>
							<?php endif; ?>
						</div>						
						
						<?php if(has_post_thumbnail()) : ?>
							<?php if(of_get_option('ttrust_post_featured_img_size')=="large") : ?>											
				    			<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('ttrust_post_thumb_big', array('class' => 'postThumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>		    	
							<?php else: ?>
								<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('ttrust_post_thumb_small', array('class' => 'postThumb alignleft', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>
							<?php endif; ?>
						<?php endif; ?>															
						
						<?php the_excerpt(); ?>
						<?php more_link(); ?>		
					</div>																				
			    </div>				
			
			<?php endwhile; ?>
			
			<?php get_template_part( 'part-pagination'); ?>
					    	
		</div>		
		<?php get_sidebar(); ?>				
	
		
<?php get_footer(); ?>