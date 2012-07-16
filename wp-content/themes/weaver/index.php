<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 */
?>
<?php get_header(); ?>
	<div id="container" class="container-index-loop">
	<?php
	weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget');
	weaver_put_wvr_widgetarea('top-widget-area','ttw-top-widget','ttw_hide_widg_posts');
	weaver_put_perpage_widgetarea();
	?>
        <div id="content" role="main">
	    <?php
	    /* Run the loop to output the posts.
	     * If you want to overload this in a child theme then include a file
	     * called loop-index.php and that will be used instead.
	     */
	    get_template_part( 'loop', 'index' );
	    ?>
	</div><!-- #content -->
        <?php weaver_put_wvr_widgetarea('bottom-widget-area','ttw-bot-widget','ttw_hide_widg_posts'); ?>
	<?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
        </div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
