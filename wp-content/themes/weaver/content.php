<?php
/**
 *  Template for displaying non-post-format-specific posts
 */
?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	    <h2 class="entry-title"><?php weaver_post_title(); ?></h2>

	    <?php weaver_posted_on('blog'); ?>

	<?php if ( is_archive() || is_search() ) { // Only display excerpts for archives and search. ?>
	    <div class="entry-summary">
		<?php weaver_the_excerpt_featured(); ?>
	    </div><!-- .entry-summary -->
	<?php } else { ?>
	    <div class="entry-content">
		<?php weaver_the_content_featured(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', WEAVER_TRANS ), 'after' => '</div>' ) ); ?>

	    </div><!-- .entry-content -->
	<?php } ?>

	    <?php weaver_posted_in('blog'); ?>
	</div><!-- #post-## -->
