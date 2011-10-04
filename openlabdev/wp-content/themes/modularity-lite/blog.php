<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<div class="span-<?php modularity_sidebar_class(); ?>">
<h3 class="sub"><?php _e( 'Latest', 'modularity' ); ?></h3>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<div class="content">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<div class="entry">
						<?php the_content( __( 'Read the rest of this page &raquo;', 'modularity' ) ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'modularity' ), 'after' => '</div>' ) ); ?>
					</div>
					<div class="clear"></div>
					<p class="postmetadata alt quiet">
						<?php
							$tag_list = get_the_tag_list( '| Tags: ', ', ' );
							printf( __( '%1$s | Categories: %2$s %3$s | ', 'modularity' ),
								get_the_time( get_option( 'date_format' ) ),
								get_the_category_list( ', ' ),
								$tag_list
							);
						?>
						<?php comments_popup_link( __( 'Leave A Comment &#187;', 'modularity' ), __( '1 Comment &#187;', 'modularity' ), __( '% Comments &#187;', 'modularity' ) ); ?>
						<?php edit_post_link( __( ' Edit', 'modularity'), '| ', '' ); ?>
					</p>
				</div>
			</div>
		<?php endwhile; ?>
			
		<div class="clear"></div>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Entries', 'modularity' ) ); ?></div>
			<div class="alignright"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'modularity' ) ); ?></div>
		</div>

	<?php else : ?>

		<h2 class="center"><?php _e( 'Not Found', 'modularity' ); ?></h2>
		<p class="center"><?php _e( 'Sorry, but you are looking for something that isn&rsquo;t here.', 'modularity' ); ?></p>
		<?php get_search_form(); ?>

	<?php endif; ?>
</div>

<?php get_sidebar(); ?>
</div>
<div class="double-border"></div>