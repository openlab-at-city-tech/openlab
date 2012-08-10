<?php
/**
 * The Template for displaying all single posts.
 */

    get_header();

    if (weaver_getopt('ttw_hide_single_sidebars') || weaver_is_checked_page_opt('ttw_hide_sidebars'))
	echo("<div id=\"container\" class=\"one-column single-page\">\n");
    else
	echo("<div id=\"container\" class=\"single-page\">\n"); ?>

    <?php weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget'); ?>
    <?php weaver_put_wvr_widgetarea('postpages-widget-area','ttw-top-widget','ttw_hide_special_posts'); ?>
	    <div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post();
	global $weaver_cur_post_id;
	$weaver_cur_post_id = get_the_ID();
	weaver_per_post_style();
?>

		<div id="nav-above" class="navigation">
		    <div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', WEAVER_TRANS ) . '</span> %title' ); ?></div>
		    <div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', WEAVER_TRANS ) . '</span>' ); ?></div>
		</div><!-- #nav-above -->

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		    <h2 class="entry-title"><?php weaver_post_title('single'); ?></h2>

			<?php weaver_posted_on('single'); ?>

			<div class="entry-content">
			    <?php weaver_the_content_featured_single(); ?>
			    <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', WEAVER_TRANS ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->

<?php if ( !weaver_getopt('ttw_hide_author_bio') && get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
		<div id="entry-author-info">
		    <div id="author-avatar">
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'weaver_author_bio_avatar_size', 60 ) ); ?>
		    </div><!-- #author-avatar -->
		    <div id="author-description">
			<h2><?php printf( esc_attr__( 'About %s', WEAVER_TRANS ), get_the_author() ); ?></h2>
			<?php the_author_meta( 'description' ); ?>
			<div id="author-link">
			    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
				<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', WEAVER_TRANS ), get_the_author() ); ?>
			    </a>
			</div><!-- #author-link	-->
		    </div><!-- #author-description -->
		</div><!-- #entry-author-info -->
<?php endif; ?>

		<div class="entry-utility">
		    <?php weaver_posted_in('single'); ?>
		</div><!-- .entry-utility -->
		</div><!-- #post-## -->

		<div id="nav-below" class="navigation">
		    <div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', WEAVER_TRANS ) . '</span> %title' ); ?></div>
		    <div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', WEAVER_TRANS ) . '</span>' ); ?></div>
		</div><!-- #nav-below -->

		<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>

	    </div><!-- #content -->
	    <?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
	</div><!-- #container -->

<?php if (!weaver_getopt('ttw_hide_single_sidebars') && !weaver_is_checked_page_opt('ttw_hide_sidebars')) get_sidebar(); ?>
<?php get_footer(); ?>
