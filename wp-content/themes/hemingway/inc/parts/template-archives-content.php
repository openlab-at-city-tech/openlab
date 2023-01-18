<div class="archive-box">

	<h3><?php _e( 'Last 30 Posts', 'hemingway' ); ?></h3>
				
	<ul>
		<?php 
		
		$archive_posts = get_posts( array(
			'post_status'		=> 'publish',
			'posts_per_page'	=> 30,
		) );

		foreach ( $archive_posts as $archive_post ) : ?>
			<li>
				<a href="<?php echo get_the_permalink( $archive_post->ID ); ?>">
					<?php echo get_the_title( $archive_post->ID );?> 
					<span>(<?php echo get_the_time( get_option( 'date_format' ), $archive_post->ID ); ?>)</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	
	<h3><?php _e( 'Archives by Categories', 'hemingway' ); ?></h3>
	
	<ul>
		<?php wp_list_categories( 'title_li=' ); ?>
	</ul>
	
	<h3><?php _e( 'Archives by Tags', 'hemingway' ); ?></h3>
	
	<ul>
		<?php 
		
		$tags = get_tags();
		
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				/* Translators: %s = The name of the tag */
				echo '<li><a href="' . get_tag_link( $tag->term_id ) . '" title="' . sprintf( __( "View all posts in %s", 'hemingway' ), $tag->name ) . '" ' . '>' . $tag->name.'</a></li> ';
			}
		}
		
		?>
	</ul>

	<h3><?php _e( 'Contributors', 'hemingway' ); ?></h3>
	
	<ul>
		<?php wp_list_authors(); ?> 
	</ul>
	
	<h3><?php _e( 'Archives by Year', 'hemingway' ); ?></h3>
	
	<ul>
		<?php wp_get_archives( 'type=yearly' ); ?>
	</ul>
	
	<h3><?php _e( 'Archives by Month', 'hemingway' ); ?></h3>
	
	<ul>
		<?php wp_get_archives( 'type=monthly' ); ?>
	</ul>

	<h3><?php _e( 'Archives by Day', 'hemingway' ); ?></h3>
	
	<ul>
		<?php wp_get_archives( 'type=daily' ); ?>
	</ul>

</div><!-- .archive-box -->