<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

    get_header();
    if (weaver_getopt('ttw_hide_special_post_sidebars'))
	echo('<div id="container" class="one-column container-search">' . "\n");
    else
	echo('<div id="container" class="container-search">' . "\n");
    weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget');
    weaver_put_wvr_widgetarea('postpages-widget-area','ttw-top-widget','ttw_hide_special_posts'); ?>
	<div id="content" role="main">

<?php if ( have_posts() ) : ?>
	<h1 class="page-title search-results"><?php printf( __( 'Search Results for: %s', WEAVER_TRANS ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		<?php
		/* Run the loop for the search to output the results.
		 * If you want to overload this in a child theme then include a file
		 * called loop-search.php and that will be used instead.
		 */
		get_template_part( 'loop', 'search' );
		?>
<?php else : ?>
		<div id="post-0" class="post no-results not-found">
			<h2 class="entry-title"><?php _e( 'Nothing Found', WEAVER_TRANS ); ?></h2>
			<div class="entry-content">
				<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', WEAVER_TRANS ); ?></p>
				<?php get_search_form(); ?>
			</div><!-- .entry-content -->
		</div><!-- #post-0 -->
<?php endif; ?>
	</div><!-- #content -->
	<?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
    </div><!-- #container -->

<?php if (!weaver_getopt('ttw_hide_special_post_sidebars')) get_sidebar(); ?>
<?php get_footer(); ?>
