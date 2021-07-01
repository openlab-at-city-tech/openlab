<?php

/*
 * Appended to the_content of the "Archive template" page template with lingonberry_the_content().
*/

?>

<div class="archive-box">
					
	<div class="archive-col">

		<?php

		$archive_30 = get_posts( array( 
			'post_status'		=> 'publish',
			'post_type'			=> 'post',
			'posts_per_page' 	=> 30,
		) );

		if ( $archive_30 ) : 
			?>
						
			<h3><?php _e( 'Last 30 Posts', 'lingonberry' ) ?></h3>
						
			<ul>
				<?php foreach ( $archive_30 as $post ) : ?>
					<li>
						<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo get_the_title( $post->ID );?> <span>(<?php echo get_the_time( get_option( 'date_format' ), $post->ID ); ?>)</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php
		endif;
		?>
		
		<h3><?php _e( 'Archives by Categories', 'lingonberry' ); ?></h3>
		
		<ul>
			<?php 
			wp_list_categories( array( 
				'title_li' 	=> '' 
			) ); 
			?>
		</ul>

		<?php

		$tags_list = wp_list_categories( array( 
			'echo'		=> false,
			'taxonomy'	=> 'post_tag',
			'title_li' 	=> '' 
		) ); 

		if ( $tags_list ) : 
			?>
		
			<h3><?php _e( 'Archives by Tags', 'lingonberry') ?></h3>
			
			<ul>
				<?php echo $tags_list; ?>
			</ul>

			<?php
		endif;
		?>
	
	</div><!-- .archive-col -->
	
	<div class="archive-col">
	
		<h3><?php _e( 'Contributors', 'lingonberry' ); ?></h3>
		
		<ul>
			<?php wp_list_authors(); ?> 
		</ul>
		
		<h3><?php _e( 'Archives by Year', 'lingonberry' ); ?></h3>
		
		<ul>
			<?php wp_get_archives( 'type=yearly' ); ?>
		</ul>
		
		<h3><?php _e( 'Archives by Month', 'lingonberry' ); ?></h3>
		
		<ul>
			<?php wp_get_archives( 'type=monthly' ); ?>
		</ul>
	
		<h3><?php _e( 'Archives by Day', 'lingonberry' ); ?></h3>
		
		<ul>
			<?php wp_get_archives( 'type=daily' ); ?>
		</ul>
	
	</div><!-- .archive-col -->

</div><!-- .archive-box -->