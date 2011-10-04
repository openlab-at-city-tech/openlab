<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php get_header(); ?>

<div class="span-<?php modularity_sidebar_class(); ?>">

<div class="content">
<?php if (have_posts()) : ?>

	<h2><?php printf( __( 'Search Results for: %s' ), '<span>' . get_search_query() . '</span>'); ?></h2>

	<?php while (have_posts()) : the_post(); ?>
		
	<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<h6><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'modularity' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title() ?></a></h6>
	
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'modularity' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_post_thumbnail(); ?></a>
		<?php endif; ?>
	
		<?php the_excerpt(); ?>
	
		<p class="postmetadata alt quiet">
			<?php
				$tag_list = get_the_tag_list( '| Tags: ', ', ' );
				printf( __( '%1$s | Categories: %2$s %3$s | ', 'modularity' ),
				get_the_time( get_option( 'date_format' ) ),
				get_the_category_list( ', ' ),
				$tag_list
			); ?>
			<?php comments_popup_link( __( 'Leave A Comment &#187;', 'modularity' ), __( '1 Comment &#187;', 'modularity' ), __( '% Comments &#187;', 'modularity' ) ); ?>
			<?php edit_post_link( __( ' Edit', 'modularity'), '| ', '' ); ?>
		</p>
	</div>
	
	<div class="clear"></div>
	
	<?php endwhile; ?>

<div class="clear"></div>

	<div class="navigation">
		<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Entries', 'modularity' ) ); ?></div>
		<div class="alignright"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'modularity' ) ) ?></div>
	</div>

<?php else : ?>

	<h2><?php _e( 'No posts found. Try a different search?', 'modularity' ); ?></h2>
	<?php get_search_form(); ?>

<?php endif; ?>

</div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>