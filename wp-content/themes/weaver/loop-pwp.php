<?php
/**
 * The loop that displays posts for the Page with Posts templates
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
 */
?>
<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) { ?>
	<div id="nav-above" class="navigation">
	<?php if ( function_exists ('wp_pagenavi')) {
		wp_pagenavi(); }
	else { ?>

	    <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', WEAVER_TRANS ) ); ?></div>
	    <div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', WEAVER_TRANS ) ); ?></div>
	<?php } ?>
	</div><!-- #nav-above -->
<?php } ?>


<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', WEAVER_TRANS ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', WEAVER_TRANS ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>

<?php
	/* Start the Loop.
	 */
	?>
<?php while ( have_posts() ) {
	the_post();
	global $weaver_cur_post_id;
	$weaver_cur_post_id = get_the_ID();
	weaver_per_post_style();

	if (!is_sticky() || !weaver_is_checked_page_opt('ttw_hide_sticky')) {
	    if (weaver_show_post_format($post->ID)) {
		continue;
	    }
	?>

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	    <h2 class="entry-title"><?php weaver_post_title(); ?></h2>

	    <?php if (!weaver_is_checked_page_opt('ttw_hide_pp_infotop')) weaver_posted_on('blog');

	    global $weaver_loop;
	    if ($weaver_loop == 'full') {	?>
	    <div class="entry-content">
		<?php weaver_the_content_featured(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', WEAVER_TRANS ), 'after' => '</div>' ) ); ?>

	    </div><!-- .entry-content -->
	<?php } elseif ($weaver_loop == 'excerpt') { ?>
		<div class="entry-summary">
		<?php weaver_the_excerpt_featured(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', WEAVER_TRANS ), 'after' => '</div>' ) ); ?>
	    </div><!-- .entry-summary -->
	<?php } else /* title */ { ?>
	<?php }

	if (!weaver_is_checked_page_opt('ttw_hide_pp_infobot')) weaver_posted_in('blog');
	else echo('<div style="clear:both;"></div>'); ?>
	</div><!-- #post-## -->

	<?php comments_template( '', true );
	?>

<?php	}
} // End the loop. ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) { ?>
	<div id="nav-below" class="navigation">
	<?php if ( function_exists ('wp_pagenavi')) {
		wp_pagenavi(); }
	else { ?>

	    <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', WEAVER_TRANS ) ); ?></div>
	    <div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', WEAVER_TRANS ) ); ?></div>
	<?php } ?>
	</div><!-- #nav-below -->
<?php } ?>
