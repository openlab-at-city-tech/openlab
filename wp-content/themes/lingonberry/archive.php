<?php get_header(); ?>

<div class="content section-inner">
			
		<div class="posts">
		
			<div class="page-title">
	
				<h4><?php if ( is_day() ) : ?>
					<?php printf( __( 'Date: %s', 'lingonberry' ), '' . get_the_date() . '' ); ?>
				<?php elseif ( is_month() ) : ?>
					<?php printf( __( 'Month: %s', 'lingonberry' ), '' . get_the_date( _x( 'F Y', 'F = Month, Y = Year', 'lingonberry' ) ) ); ?>
				<?php elseif ( is_year() ) : ?>
					<?php printf( __( 'Year: %s', 'lingonberry' ), '' . get_the_date( _x( 'Y', 'Y = Year', 'lingonberry' ) ) ); ?>
				<?php elseif ( is_category() ) : ?>
					<?php printf( __( 'Category: %s', 'lingonberry' ), '' . single_cat_title( '', false ) . '' ); ?>
				<?php elseif ( is_tag() ) : ?>
					<?php printf( __( 'Tag: %s', 'lingonberry' ), '' . single_tag_title( '', false ) . '' ); ?>
				<?php elseif ( is_author() ) : ?>
					<?php $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author)); ?>
					<?php printf( __( 'Author: %s', 'lingonberry' ), $curauth->display_name ); ?>
				<?php else : ?>
					<?php _e( 'Archive', 'lingonberry' ); ?>
				<?php endif;
				
				$paged = get_query_var('paged') ? get_query_var('paged') : 1;
				
				if ( "1" < $wp_query->max_num_pages ) : ?>
				
					<span><?php printf( __( '(page %1$s of %2$s)', 'lingonberry' ), $paged, $wp_query->max_num_pages ); ?></span>
				
				<?php endif; ?></h4>
				
				<?php
				$tag_description = tag_description();
				if ( ! empty( $tag_description ) ) {
					echo apply_filters( 'tag_archive_meta', '<div class="tag-archive-meta">' . $tag_description . '</div>' );
				}
				?>
				
			</div><!-- .page-title -->
			
			<div class="clear"></div>
	
			<?php if ( have_posts() ) : ?>
					
				<?php while ( have_posts() ) : the_post(); ?>
				
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
						<?php get_template_part( 'content', get_post_format() ); ?>
						
						<div class="clear"></div>
						
					</div><!-- .post -->
					
				<?php endwhile; ?>
							
		</div><!-- .posts -->
					
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		
			<div class="post-nav archive-nav">
			
				<?php echo get_next_posts_link( __( 'Older', 'lingonberry' ) . '<span>' . __( 'posts', 'lingonberry' ) . '</span>' ); ?>
							
				<?php echo get_previous_posts_link( __( 'Newer', 'lingonberry' ) . '<span>' . __( 'posts', 'lingonberry' ) . '</span>' ); ?>
				
				<div class="clear"></div>
				
			</div><!-- .post-nav archive-nav -->
			
			<div class="clear"></div>
			
		<?php endif; ?>
				
	<?php endif; ?>

</div><!-- .content -->

<?php get_footer(); ?>