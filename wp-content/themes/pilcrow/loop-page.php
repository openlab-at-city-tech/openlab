<?php
/**
 * The loop that displays a page.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop-page.php.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

if ( have_posts() ) : while ( have_posts() ) : the_post();
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

	<div class="entry entry-content">
		<?php
			the_content();
			wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'pilcrow' ), 'after' => '</div>' ) );
		?>
	</div><!-- .entry-content -->

	<?php edit_post_link( __( 'Edit', 'pilcrow' ), '<div class="entry-links"><span class="edit-link">', '</span></div>' ); ?>
</div><!-- #post-## -->

<?php
comments_template();

endwhile; // end of the loop.

endif; // have_posts
