<div class="post-meta">
	
	<span class="post-date"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_time(get_option('date_format')); ?></a></span>
	
	<?php if( is_sticky() && !has_post_thumbnail() ) { ?> 
		
		<span class="date-sep"> / </span>
	
		<?php _e('Sticky', 'hemingway'); ?>
	
	<?php } ?>
	
	<?php if ( current_user_can( 'manage_options' ) ) { ?>
	
		<span class="date-sep"> / </span>
					
		<?php edit_post_link(__('Edit', 'hemingway')); ?>
	
	<?php } ?>
							
</div>

<div class="post-content">
	    		            			            	                                                                                            
	<?php the_content(); ?>
			
	<?php wp_link_pages(); ?>
				        
</div> <!-- /post-content -->
            
<div class="clear"></div>