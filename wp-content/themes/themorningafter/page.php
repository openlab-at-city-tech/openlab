<?php
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
?>
        <div id="post_content" class="column span-14">
        
        <?php if ( have_posts() ) { ?>
        <?php while ( have_posts() ) { the_post(); ?>
        
        	<div class="column span-11 first">
        		            	
            	<?php the_content( '<p>' . __( 'Continue reading this post','woothemes' ) . '</p>' ); ?>

				<?php wp_link_pages( array('before' => '<p><strong>' . __( 'Pages','woothemes' ) . ':</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>
				
				<?php edit_post_link( __( 'Edit this entry.', 'woothemes' ),'<p>','</p>' ); ?>				
            	
				<?php
					$comm = get_option( 'woo_comments' );
					if ( 'open' == $post->comment_status && ($comm == 'page' || $comm == 'both' ) ) {
						comments_template( '', true );
					}
				?>

            </div>
            
        <?php
        		}
        	} // End IF Statement
        ?>    
            
            <?php get_sidebar(); ?>     

        </div>   <!-- start home_content -->
<?php get_footer(); ?>