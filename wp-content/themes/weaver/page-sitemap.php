<?php
/**
 * Template Name: Sitemap
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 */
?>
<?php
	global $wp_query;
	get_header();
?>
    <div id="container" class="container-wvr-sitemap">
	<?php weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget'); ?>
        <?php weaver_put_wvr_widgetarea('top-widget-area','ttw-top-widget','ttw_hide_widg_posts');
	weaver_put_perpage_widgetarea();
	?>
        <div id="content">
<?php
	// First, put any content from the static page (code derived from page.php template)
	if ( have_posts() ) {
	the_post();
	ob_start();
	weaver_page_content();	// get the page content, but don't display
	$page_content = ob_get_clean();	// get the output

	// now, behave differently if empty.

	if (strlen($page_content) > 0) {
?>
	<div id="post-<?php the_ID(); ?>"<?php post_class(); ?>>
	<?php weaver_put_page_title('h2'); ?>
		<div class="entry-content">
		    <?php echo("$page_content"); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-<?php the_ID(); ?> -->
<?php
	    }
	else {
	    weaver_put_page_title('h2','style="margin-bottom:10px;"');
	}


	echo("<div id=\"wvr-sitemap\">\n");
	echo("<h3>" . __('Pages', WEAVER_TRANS) . "</h3><ul class='xoxo sitemap-pages'>\n");
	wp_list_pages(array('title_li' => false));
	echo("</ul>\n");

	echo("<h3>" . __('Posts', WEAVER_TRANS) . "</h3><ul class='xoxo sitemap-pages-month'>\n");
	wp_get_archives(array('type' => 'monthly', 'show_post_count' => true));
	echo("</ul>\n");

	if (!weaver_getopt('ttw_post_hide_cats')) {
	echo("<h3>" . __('Categories', WEAVER_TRANS) . "</h3><ul class='xoxo sitemap-categories'>\n");
	 wp_list_categories(array('show_count' => true, 'use_desc_for_title' => true, 'title_li' => false));
	echo("</ul>\n");

	// If you want to show authors, simply uncomment the next 3 lines
	// echo("<h3>" . __('Authors') ."</h3><ul class='xoxo sitemap-authors'>\n");
	// wp_list_authors(array('exclude_admin' => false, 'optioncount' => true, 'title_li' => false));
	// echo("</ul>\n");

	echo("<h3>" . __('Tag Cloud', WEAVER_TRANS) . "</h3><ul class='xoxo sitemap-tag'>\n");
	wp_tag_cloud(array('number' => 0));
	echo("</ul>\n");
	}
	echo("</div><!-- wvr-sitemap -->\n");
?>
	<?php edit_post_link( __( 'Edit', WEAVER_TRANS ), '<span class="edit-link">', '</span>' ); // put here...
	} // end of loop ?>
	</div><!-- #content -->
        <?php weaver_put_wvr_widgetarea('bottom-widget-area','ttw-bot-widget','ttw_hide_widg_posts'); ?>
	<?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
    </div><!-- #container -->
<?php
	get_sidebar();
	get_footer();
?>
