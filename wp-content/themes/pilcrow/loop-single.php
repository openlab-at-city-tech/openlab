<?php
/**
 * The loop that displays a single post.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop-single.php.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

if ( have_posts() ) : while ( have_posts() ) : the_post();

$format = get_post_format();
if ( false === $format )
	$format = 'standard';
?>

<div id="nav-above" class="navigation">
	<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'pilcrow' ) . '</span> %title' ); ?></div>
	<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'pilcrow' ) . '</span>' ); ?></div>
</div><!-- #nav-above -->

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

	<?php if ( comments_open() ) : ?>
	<div class="jump"><a href="<?php the_permalink(); ?>#comments"><?php _e( '<span class="meta-nav">&darr; </span>Jump to Comments', 'pilcrow' ); ?></a></div>
	<?php endif;

	if ( 'link' != $format ) :
		the_title( '<h1 class="entry-title"><a href="' . get_permalink() . '" rel="bookmark">', '</a></h1>' );
	endif;
	?>

	<div class="entry entry-content">
		<?php
			the_content();
			wp_link_pages( array(
				'before' => '<div class="page-link">' . __( 'Pages:', 'pilcrow' ),
				'after' => '</div>'
			) );
		?>
	</div><!-- .entry-content -->

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

<div id="nav-below" class="navigation">
	<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'pilcrow' ) . '</span> %title' ); ?></div>
	<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'pilcrow' ) . '</span>' ); ?></div>
</div><!-- #nav-below -->

<?php

comments_template();

endwhile; // end of the loop.

endif;
