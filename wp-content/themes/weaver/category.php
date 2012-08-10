<?php
/**
 * The template for displaying Category Archive pages.
 */
	get_header();
	if (weaver_getopt('ttw_hide_special_post_sidebars'))
	    echo('<div id="container" class="one-column container-category category-page">' . "\n");
	else
	    echo('<div id="container" class="container-category category-page">' . "\n");

	weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget');
	weaver_put_wvr_widgetarea('postpages-widget-area','ttw-top-widget','ttw_hide_special_posts'); ?>

	    <div id="content" role="main">

		<?php
		printf('<h1 id ="category-title-%s" class="page-title category-title">',strtolower(str_replace(' ','-',single_cat_title( '', false )))); echo("\n");
		printf( __( 'Category Archives: %s', WEAVER_TRANS ), '<span>' . single_cat_title( '', false ) . '</span></h1>' );

		$category_description = category_description();
		if ( ! empty( $category_description ) )
			echo '<div class="archive-meta">' . $category_description . '</div>';

		/* Run the loop for the category page to output the posts.
		 * If you want to overload this in a child theme then include a file
		 * called loop-category.php and that will be used instead.
		 */
		get_template_part( 'loop', 'category' );
		?>

	    </div><!-- #content -->
	    <?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
	</div><!-- #container -->

<?php if (!weaver_getopt('ttw_hide_special_post_sidebars')) get_sidebar(); ?>
<?php get_footer(); ?>
