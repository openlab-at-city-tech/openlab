<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

/* Display navigation to next/previous pages when applicable */

if ( $wp_query->max_num_pages > 1 ) :
?>
<div id="nav-above" class="navigation">
	<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'pilcrow' ) ); ?></div>
	<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'pilcrow' ) ); ?></div>
</div><!-- #nav-above -->
<?php
endif;

/* If there are no posts to display, such as an empty archive page */
if ( ! have_posts() ) : ?>
<div id="post-0" class="post error404 not-found">
	<h1 class="entry-title"><?php _e( 'Not Found', 'pilcrow' ); ?></h1>
	<div class="entry entry-content">
		<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'pilcrow' ); ?></p>
		<?php get_search_form(); ?>
	</div><!-- .entry-content -->
</div><!-- #post-0 -->
<?php
endif;

/* Start the Loop.
 *
 * In Pilcrow we use the same loop in multiple contexts.
 * It is broken into three main parts: when we're displaying
 * posts that are in the gallery category, when we're displaying
 * posts in the asides category, and finally all other posts.
 *
 * Additionally, we sometimes check for whether we are on an
 * archive page, a search page, etc., allowing for small differences
 * in the loop on each template without actually duplicating
 * the rest of the loop that is shared.
 *
 * Without further ado, the loop:
 */
while ( have_posts() ) : the_post();

$format = get_post_format();
if ( false === $format )
	$format = 'standard';
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-meta">

		<?php
			if ( 'standard' == $format ) :
				if ( is_multi_author() ) :
					printf( __( '<span class="by-author"><span class="sep">by</span> <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span> | </span>', 'pilcrow' ),
						esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
						esc_attr( sprintf( __( 'View all posts by %s', 'pilcrow' ), get_the_author_meta( 'display_name' ) ) ),
						esc_attr( get_the_author_meta( 'display_name' ) )
					);
				endif;

				the_date(); ?> &middot; <?php the_time();
			else :
		?>
		<span class="entry-format">
			<a class="entry-format-link" href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'pilcrow' ), get_post_format_string( get_post_format() ) ) ); ?>"><?php echo get_post_format_string( get_post_format() ); ?></a>
		</span>
		<?php
			endif;

			edit_post_link( __( 'Edit', 'pilcrow' ), '<span class="edit-link"> | ', '</span>' );
		?>
	</div><!-- .entry-meta -->

	<?php
		if ( 'link' != $format ) :
			the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( is_search() ) : // Only display excerpts in search.
	?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'pilcrow' ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'pilcrow' ), 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	<?php endif; // is_search ?>

	<div class="entry-links">
		<p class="comment-number"><?php comments_popup_link( __( 'Leave a Comment', 'pilcrow' ), __( '1 Comment', 'pilcrow' ), __( '% Comments', 'pilcrow' ) ); ?></p>

		<?php
			if ( 'standard' != $format ) :
				if ( is_multi_author() ) :
					printf( __( '<span class="by-author"><span class="sep">by</span> <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span> | </span>', 'pilcrow' ),
						esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
						esc_attr( sprintf( __( 'View all posts by %s', 'pilcrow' ), get_the_author_meta( 'display_name' ) ) ),
						esc_attr( get_the_author_meta( 'display_name' ) )
					);
				endif;
		?>
			<a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a> &middot; <?php the_time(); ?>
		<?php else : ?>
			<p class="entry-categories tagged"><?php printf( __( 'Filed under %s', 'pilcrow' ), get_the_category_list( ', ' ) ); ?></p>
			<p class="entry-tags tagged"><?php the_tags( __( 'Tagged as', 'pilcrow' ).' ', ', ', '<br />' ); ?></p>
		<?php endif; ?>
	</div><!-- .entry-links -->

</div><!-- #post-## -->

<?php comments_template(); ?>

<?php endwhile; // End the loop. Whew.

/* Display navigation to next/previous pages when applicable */
if ( $wp_query->max_num_pages > 1 ) :
?>
<div id="nav-below" class="navigation">
	<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'pilcrow' ) ); ?></div>
	<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'pilcrow' ) ); ?></div>
</div><!-- #nav-below -->
<?php
endif;
