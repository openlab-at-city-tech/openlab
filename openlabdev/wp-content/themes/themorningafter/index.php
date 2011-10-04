<?php
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
	
	$blog_name = get_bloginfo( 'name' );
?>        
        <div id="arch_content" class="column span-14">
        
        <?php if ( have_posts() ) { ?>
        
        	<div class="column span-3 first">        
            	<h2 class="archive_name"><?php echo $blog_name; ?></h2>        
            	
            	<div class="archive_meta">
            	
            		<div class="archive_feed">
            			<a href="<?php bloginfo( 'rss2_url' ); ?>"><?php _e( 'RSS feed for','woothemes' );?> <?php echo $blog_name; ?></a>		
            		</div>
            	
            	</div>
            </div>           
                        
            <div class="column span-8">
            
            <?php while ( have_posts() ) { the_post(); ?>
            
            	<div <?php post_class( 'archive_post_block' ); ?>>
            		<h3 class="archive_title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'woothemes' );?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
            		
            		<div class="archive_post_meta"><?php _e( 'By', 'woothemes' ); ?> <?php the_author_posts_link(); ?> <span class="dot">&sdot;</span> <?php the_time( get_option( 'date_format' ) ); ?> <span class="dot">&sdot;</span> <a href="<?php comments_link(); ?>"><?php comments_number( __( 'Post a comment', 'woothemes' ), __( 'One comment', 'woothemes' ),'% comments' ); ?></a></div>
            		
            		<?php the_excerpt(); ?>
            	</div>
            	
            	<?php } // End WHILE Loop ?>

				<div class="navigation">
					<p><?php next_posts_link( __( '&laquo; Previous', 'woothemes' ) ); ?> &nbsp; <?php previous_posts_link( __( 'Next &raquo;', 'woothemes' ) ); ?></p>
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