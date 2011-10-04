<?php
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
?>
        <div id="arch_content" class="column span-14">
        
           	<div class="column span-3 first">        
            	<h2 class="archive_name"><?php bloginfo( 'name' ); ?></h2>        
            	
            	<div class="archive_meta">
            	
            		<div class="archive_feed">
            			<a href="<?php $GLOBALS['feedurl'] = $woo_options['woo_feed_url']; if ( ! empty( $feedurl ) ) { echo $feedurl; } else { echo get_bloginfo_rss( 'rss2_url' ); } ?>"><?php _e( 'RSS feed for', 'woothemes' ); ?> <?php bloginfo( 'name' ); ?></a>		
            		</div>
            	
            	</div>
            </div>
            
                        
            <div class="column span-8">
            
            	<p><strong><?php _e( 'Oops!', 'woothemes' ); ?></strong></p>
            
            	<p><?php _e( "Looks like the page you're looking for has been moved or had its name changed. Or maybe it's just fate. You could use the search box in the header to search for what you're looking for, or begin again from the", "woothemes" ); ?> <a href="<?php echo home_url( '/' ); ?>/"><?php _e( 'home page', 'woothemes' ); ?></a>.
            	
            </div>
            
            <?php get_sidebar(); ?>
        
        </div>
        
        
<?php get_footer(); ?>