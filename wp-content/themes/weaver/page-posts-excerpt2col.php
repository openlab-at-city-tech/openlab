<?php
/**
 * Template Name: Page with Posts (excerpts - 2 cols)
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 */
?>
<?php
	global $wp_query, $weaver_loop;

	get_header();
	$paged = weaver_get_page();

	$temp = $wp_query;  // assign original query to temp variable for later use

	if (weaver_is_checked_page_opt('ttw_hide_sidebars')) echo("<div id=\"container\" class=\"one-column page-with-posts page-with-posts-excerpt2\">\n");
	else echo("<div id=\"container\" class=\"page-with-posts page-with-posts-excerpt2\">\n");

	weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget');
	weaver_put_wvr_widgetarea('top-widget-area','ttw-top-widget','ttw_hide_widg_posts');
	weaver_put_perpage_widgetarea();
	?>
        <div id="content">
<?php
	// First, put any content from the static page (code derived from page.php template)
	if (have_posts()) {
	the_post();
	if ($paged == 1) {	// only show on the first page
	    ob_start();
	    if ( is_front_page() ) {
		weaver_put_page_title('h2');
	    } else {
		weaver_put_page_title('h1');
	    }
	    weaver_page_content();	// get the page content, but don't display
	    $page_content = ob_get_clean();	// get the output

	    // now, behave differently if empty.

	    if (strlen($page_content) > 0) {
?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
		    <?php echo("$page_content"); ?>
		    <div style="clear:both;"></div>
		</div><!-- .entry-content -->
		<?php edit_post_link( __( 'Edit', WEAVER_TRANS ), '<span class="edit-link">', '</span>' ); // put here... ?>
	</div><!-- #post-<?php the_ID(); ?> -->
<?php
	    }
	}

	$args = array(
		'orderby' => 'date',
		'order' => 'DESC',
		'paged' => $paged
	);
	$args = weaver_setup_post_args($args);	// setup custom fields for this page

	$wp_query = new WP_Query($args);

	$weaver_loop = 'excerpt';

	get_template_part('loop','twocol');
?>

<?php
	$wp_query = $temp;
	// It might be nice if the edit tag for the page were to be displayed here, but it
	// doesn't seem to work right.
	}
?>
	</div><!-- #content -->
        <?php weaver_put_wvr_widgetarea('bottom-widget-area','ttw-bot-widget','ttw_hide_widg_posts'); ?>
	<?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
    </div><!-- #container -->
<?php

	if (!weaver_is_checked_page_opt('ttw_hide_sidebars')) get_sidebar();
	get_footer();
?>
