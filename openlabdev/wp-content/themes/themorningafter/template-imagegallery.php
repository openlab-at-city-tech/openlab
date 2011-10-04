<?php
/*
Template Name: Image Gallery
*/
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
?>   
        <div id="post_content" <?php post_class( 'column span-14 first' ); ?>>
        
        	<div class="column span-11 first">
                                                                            
            <div class="post">

				<div class="entry">
                <?php query_posts( 'showposts=60' ); ?>
                <?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>				
                    <?php $wp_query->is_home = false; ?>
                    <?php woo_get_image( 'image',100,100,'thumbnail alignleft' ); ?>
                <?php
                		}
                	}
                ?>	
                </div>

            </div>
            <div class="fix"></div>                
                                                            
		</div>
		
        <?php get_sidebar(); ?>

    </div>
		
<?php get_footer(); ?>