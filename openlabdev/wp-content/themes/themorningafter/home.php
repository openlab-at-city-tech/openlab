<?php
	get_header();
	global $woo_options;
	get_template_part( 'top-banner' );
?> 
        <div id="home_content" class="column span-14">
        
            <div id="home_left" class="column span-7 first">
         
				<?php 
				while ( have_posts() ) { the_post();

				$do_not_duplicate = $post->ID; ?>
        
				<div id="latest_post">
					<h3 class="mast"><?php _e( 'Latest Post', 'woothemes' ); ?></h3>
					
					<div id="latest_post_image">
					<?php woo_image( 'width=470&height=210' ); ?>
					</div>
					
					<h3 class="latest_post_title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'woothemes' );?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					
					<p><?php if ( $woo_options['woo_post_content_home'] == 'true' ) { the_content(); } else { the_excerpt(); } ?></p>
					
					<div class="latest_post_meta">
						<span class="latest_read_on"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php _e( 'Continue Reading', 'woothemes' ); ?></a></span>
						<span class="latest_comments"><?php comments_popup_link( __( 'Post a comment', 'woothemes' ), __( 'One comment', 'woothemes' ), '% comments', '', __( 'Comments off', 'woothemes' ) ); ?></span>						
						<?php $cat = get_the_category(); $cat = $cat[0]; ?>
						<span class="latest_category"><a href="<?php echo get_category_link( $cat->cat_ID );?>"><?php echo $cat->cat_name; ?></a></span>
					</div>
				</div>
				
				<?php 
				break;
					} // End WHILE Loop
				?>
				
				
				<div id="home_featured">
				
					<?php
                    $limit = $woo_options['woo_featured_limit'];
                    
                    if ($limit > 0) {
					?>
				
					<h3 class="home_featured"><?php echo $woo_options['woo_featured_heading']; ?></h3>
					
					<?php 
					$feat_id = get_cat_id($woo_options['woo_featured_category']);           
					$limit = $woo_options['woo_featured_limit'];
					$the_query = new WP_Query( 'cat=' . $feat_id . '&showposts=' . $limit );
			
					while ( $the_query->have_posts() ) { $the_query->the_post();

					$do_not_duplicate = $post->ID; ?>
					
					<div <?php post_class( 'feat_content' ); ?>>
					
						<?php woo_image( 'width=65&height=65' ); ?>
						
						<div class="feat_title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'woothemes' );?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>
						
						<div class="feat_exc">						
							<?php if ( $woo_options['woo_post_content_home'] == 'true' ) { the_content(); } else { the_excerpt(); } ?>
						</div>
											
					</div>
					
					<?php
							} // End WHILE Loop
						} // End IF Statement
					?>
						
				</div>
				<?php 
				$limit = $woo_options['woo_updates_limit'];
				$the_query = new WP_Query( 'post_type=updates&showposts=' . $limit ); 
				
				if( $the_query->have_posts() ) {
				?>
				
				<div id="home_asides">
				
					<h3 class="mast"><?php echo $woo_options['woo_updates_heading']; ?></h3>
					
					<ul class="arrow">
						
						<?php 
						while ( $the_query->have_posts() ) { $the_query->the_post();

						$do_not_duplicate = $post->ID; ?>
						
						<li><?php echo strip_tags( get_the_content(), '<a>' ); ?> <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to','woothemes' ); ?> <?php the_title_attribute(); ?>">#</a></li>
						
						<?php } // End WHILE Loop ?>
					</ul>
				</div>
				<?php } ?>           
            </div>            
            
            <div id="home_right" class="column span-7 last">
            	<?php if( ! empty( $woo_options['woo_home_heading'] ) && ! empty( $woo_options['woo_home_text'] ) ) { ?>
            	<div id="home_about">
					
					<?php if ( ! empty( $woo_options['woo_home_heading'] ) ) { ?><h3 class="mast3"><?php echo stripslashes( $woo_options['woo_home_heading'] ); ?></h3><?php } ?>
					<?php if (  !empty( $woo_options['woo_home_text'] ) ) { ?><p><?php echo stripslashes( $woo_options['woo_home_text'] ); ?><p><?php } ?>		
							
				</div>
				<?php } ?>
					
				<div class="column span-4 first">
					<?php if ( ! function_exists( 'woo_sidebar' ) || ! woo_sidebar( 'middle_sidebar' ) ) { /* Silence is golden. */ } ?>
            	</div>
            
                <?php get_sidebar(); ?>         
            	
            
            </div>
        
        </div>    
        
<?php get_footer(); ?> 