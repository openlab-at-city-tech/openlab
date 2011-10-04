<?php
/*
Template Name: Full Width
*/
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
?>    
        <div id="post_content" <?php post_class( 'column span-14 first' ); ?>>
            
            <?php if ( have_posts() ) { $count = 0; ?>
            <?php while ( have_posts() ) { the_post(); $count++; ?>
            
        	<div class="column">
           
                <div class="entry">
	               	<?php the_content(); ?>
	          	</div>
             
             </div>
                                                    
        	<?php
	        		} // End WHILE Loop
				} else {
					printf( __( '<p>Lost? Go back to the <a href="%s">home page</a></p>', 'woothemes' ), get_home_url() );
				}
			?>  
            

        
        </div>         
<?php get_footer(); ?>