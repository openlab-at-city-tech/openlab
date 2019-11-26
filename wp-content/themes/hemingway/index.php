<?php get_header(); ?>

<div class="wrapper section-inner">

	<div class="content left">
		
		<div class="posts">

			<?php
		
			$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

			$archive_title = '';
			$archive_subtitle = '';

			if ( is_archive() ) {
				$archive_title = get_the_archive_title();
			} elseif ( is_search() ) {
				$archive_title = sprintf( _x( 'Search results: "%s"', 'Variable: search query text', 'hemingway' ), get_search_query() );
			} elseif ( $paged > 1 ) {
				$archive_title = sprintf( __( 'Page %1$s of %2$s', 'hemingway' ), $paged, $wp_query->max_num_pages );
			}

			if ( ( is_archive() || is_search() ) && 1 < $wp_query->max_num_pages ) {
				$archive_subtitle = sprintf( __( '(page %1$s of %2$s)', 'hemingway' ), $paged, $wp_query->max_num_pages );
			}

			if ( $archive_title ) : ?>

				<div class="page-title">

					<h4>
						<?php 
						echo $archive_title;
						
						if ( $archive_subtitle ) {
							echo ' <span>' . $archive_subtitle . '</span>';
						} 
						?>
						
					</h4>
					
				</div><!-- .page-title -->

				<?php 
			endif;

			if ( have_posts() ) : 
			
				while ( have_posts() ) : the_post();
				
					get_template_part( 'content', get_post_format() );
					
				endwhile;
	
			elseif ( is_search() ) : ?>

				<div class="post">
				
					<div class="content-inner">
				
						<div class="post-content">
						
							<p><?php _e( 'No results. Try again, would you kindly?', 'hemingway' ); ?></p>
							
							<?php get_search_form(); ?>
						
						</div><!-- .post-content -->
					
					</div><!-- .content-inner -->
					
					<div class="clear"></div>
				
				</div><!-- .post -->
			
			<?php endif; ?>

		</div><!-- .posts -->
		
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		
			<div class="post-nav archive-nav">
						
				<?php echo get_next_posts_link( __( '&laquo; Older<span> posts</span>', 'hemingway' ) ); ?>
							
				<?php echo get_previous_posts_link( __( 'Newer<span> posts</span> &raquo;', 'hemingway' ) ); ?>
				
				<div class="clear"></div>
				
			</div><!-- .post-nav.archive-nav -->
		
		<?php endif; ?>
			
	</div><!-- .content.left -->
		
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div><!-- .wrapper -->
	              	        
<?php get_footer(); ?>