<?php
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
?>
        <div id="post_content" <?php post_class( 'column span-14' ); ?>>
        
        <?php if ( have_posts() ) { ?>	
        <?php while ( have_posts() ) { the_post(); ?>
        
        	<div class="column span-11 first">
        		<h2 class="post_cat"><?php $cat = get_the_category(); $cat = $cat[0]; echo $cat->cat_name; ?></h2>
        		
            	<h2 class="post_name" id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2>
            	
				<?php echo woo_get_embed( 'embed','700','420' ); ?>
            	<div class="post_meta">
            		<?php _e( 'By','woothemes' );?> <?php the_author_posts_link(); ?> <span class="dot">&sdot;</span> <?php the_time( get_option( 'date_format' ) ); ?> <span class="dot">&sdot;</span> <?php if( function_exists( 'wp_email' ) ) { ?> <?php email_link(); ?> <span class="dot">&sdot;</span> <?php } ?> <?php if( function_exists( 'wp_print' ) ) { ?> <?php print_link(); ?> <span class="dot">&sdot;</span> <?php } ?> <a href="#comments"><?php _e( 'Post a comment','woothemes' ); ?></a>
            	</div>

				<div class="post_meta">
            		<?php the_tags( '<span class="filedunder"><strong>' . __( 'Filed Under','woothemes' ) . '</strong></span> &nbsp;', ', ', '' ); ?>
            	</div>
            	
				<div class="post_text">

            		<?php the_content('<p>'.__('Continue reading this post','woothemes').'</p>'); ?>
					<?php wp_link_pages( array( 'before' => '<p><strong>' . __( 'Pages','woothemes' ) . ':</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>
					<?php edit_post_link( __( 'Edit this entry.', 'woothemes' ),'<p>','</p>' ); ?>	

				</div>
				<?php
					$comm = get_option( 'woo_comments' );
					if ( 'open' == $post->comment_status && ($comm == 'post' || $comm == 'both' ) ) {
						comments_template( '', true );
					}
				?>
            </div>
            
        <?php
        		} // End WHILE Loop
			} else {
				printf( __( '<p>Lost? Go back to the <a href="%s">home page</a></p>', 'woothemes' ), get_home_url() );
			}
		?>    
        <?php get_sidebar(); ?>     
        
        </div>
                
<?php get_footer(); ?>