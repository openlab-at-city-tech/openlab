<?php
/*
Template Name: Archives
*/
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
?>
        <div id="post_content" <?php post_class( 'column span-14 first' ); ?>>
        
        	<div class="column span-11 first">
                           
             <div class="entry">
			    
				    <h3><?php _e( 'The Last 30 Posts', 'woothemes' ); ?></h3>
																	  
				    <ul>											  
				        <?php query_posts( 'showposts=30' ); ?>		  
				        <?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>
				            <?php $wp_query->is_home = false; ?>	  
				            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> - <?php the_time( get_option( 'date_format' ) ); ?> - <?php echo $post->comment_count; ?> <?php _e( 'comments', 'woothemes' ); ?></li>
				        <?php
				        		}
				        	}
				        ?>					  
				    </ul>											  
																	  
				    <h3><?php _e( 'Categories', 'woothemes' ); ?></h3>	  
																	  
				    <ul>											  
				        <?php wp_list_categories( 'title_li=&hierarchical=0&show_count=1' ); ?>	
				    </ul>											  
				     												  
				    <h3><?php _e( 'Monthly Archives', 'woothemes' ); ?></h3>
																	  
				    <ul>											  
				        <?php wp_get_archives( 'type=monthly&show_post_count=1' ); ?>	
				    </ul>

			</div>
			
		</div>
			
        <?php get_sidebar(); ?>
        
        </div>
      
<?php get_footer(); ?>