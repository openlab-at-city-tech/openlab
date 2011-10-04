<?php get_header(); ?>        
<?php global $woo_options; ?>      
		<div id="topbanner" class="column span-14" style="background-image:url(<?php header_image(); ?>)">
            <div class="pagetitle">
                <?php echo $woo_options['woo_pageheading_prefix'] . stripslashes( $woo_options['woo_pageheading_archives'] ); ?>
            </div>
        </div>
        
        
        <div id="arch_content" class="column span-14">
        
        <?php if ( have_posts() ) { ?>
        
        	<div class="column span-3 first">
        	
        		<?php if ( is_category() ) { ?>
        	
            	<h2 class="archive_name"><?php echo single_cat_title(); ?></h2>        
            	
            	<div class="archive_meta">
            	
            		<div class="archive_feed">
            			<?php $cat_obj = $wp_query->get_queried_object(); $cat_id = $cat_obj->cat_ID; echo '<a href="' . get_category_feed_link( $cat, '' ) . '">RSS feed for this section</a>'; ?>            			
            		</div>

            		<?php $cat_count = $cat_obj->category_count; ?>
            		<div class="archive_number">
            			<?php _e( 'This category contains', 'woothemes' ); ?> <?php echo $cat_count . " " . ( $cat_count==1? __( 'post', 'woothemes' ): __( 'posts', 'woothemes' ) ); ?>
            		</div>           		
            	
            	</div>

				<?php } elseif ( is_tag() ) { ?>
        	
            	<h2 class="archive_name"><?php single_tag_title(); ?></h2>        
            	
            	<div class="archive_meta">
            	
            		<div class="archive_number">
            			<?php _e( 'This tag is associated with', 'woothemes' ); ?> <?php $tag = $wp_query->get_queried_object(); echo $tag->count; ?> <?php _e( 'posts','woothemes' ); ?>
            		</div>           		
            	
            	</div>
            	
				<?php } elseif ( is_day() ) { ?>
				<h2 class="archive_name"><?php _e('Archive for','woothemes'); ?> <?php the_time( 'F jS, Y' ); ?></h2>

				<?php } elseif ( is_month() ) { ?>
				<h2 class="archive_name"><?php _e('Archive for','woothemes'); ?> <?php the_time( 'F, Y' ); ?></h2>

				<?php } elseif ( is_year() ) { ?>
				<h2 class="archive_name"><?php _e('Archive for','woothemes'); ?> <?php the_time( 'Y' ); ?></h2>
				
				<?php } ?>
				
            </div>
            
                        
            <div class="column span-8">
            
            <?php while ( have_posts() ) { the_post(); ?>
            
            	<div <?php post_class( 'archive_post_block' ); ?>>
            		<h3 class="archive_title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php echo __( 'Permanent Link to', 'woothemes' ); the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
            		
            		<div class="archive_post_meta"><?php _e('By','woothemes');?> <?php the_author_posts_link(); ?> <span class="dot">&sdot;</span> <?php the_time( get_option( 'date_format' ) ); ?> <span class="dot">&sdot;</span> <a href="<?php comments_link(); ?>"><?php comments_number( __( 'Post a comment', 'woothemes' ), __( 'One comment', 'woothemes' ),'% comments' ); ?></a></div>
            		
            		<?php if ( $woo_options['woo_post_content_archives'] == 'true' ) { the_content(); } else { the_excerpt(); } ?>
            	</div>
            	
            	<?php } // End WHILE Loop ?>

				<div class="navigation">
					<p><?php next_posts_link( '&laquo;'. __( 'Previous', 'woothemes' ) ); ?> &#8212; <?php previous_posts_link( __( 'Next', 'woothemes' ) . ' &raquo;' ); ?></p>
				</div>

				<?php
				} else {
					printf( __( '<p>Lost? Go back to the <a href="%s">home page</a></p>', 'woothemes' ), get_home_url() );
				}
				?>
            	
            </div>
            
            <?php get_sidebar(); ?>
        
        </div>
        
<?php get_footer(); ?>
