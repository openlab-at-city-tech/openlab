<?php
/**
 *  Template for displaying non-post-format-specific posts
 */
?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h5 class="entry-format"><?php _e( 'Aside', WEAVER_TRANS ); ?></h5>

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

	    <?php weaver_posted_on_code(); ?>
	</div><!-- #post-## -->
