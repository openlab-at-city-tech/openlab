<?php
/* pgtpl-content - content for "normal" pages
   this goes in <container> div */

    weaver_put_wvr_widgetarea( 'sitewide-top-widget-area','ttw-site-top-widget');
    weaver_put_wvr_widgetarea( 'top-widget-area','ttw-top-widget','ttw_hide_widg_pages');
    weaver_put_perpage_widgetarea();
?>
	<div id="content">
<?php if ( have_posts() ) {
	the_post(); ?>
	    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php 		if ( is_front_page() ) {
		    weaver_put_page_title('h2');
		} else {
		    weaver_put_page_title('h1');
		}
?>
		    <div class="entry-content">
<?php
	weaver_page_content();
	echo ("<div class=\"clear-cols\"></div>");
	wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', WEAVER_TRANS ), 'after' => '</div>' ) );
	edit_post_link( __( 'Edit', WEAVER_TRANS ), '<span class="edit-link">', '</span>' );
?>
		    </div><!-- .entry-content -->
	    </div><!-- #post-<?php the_ID(); ?> -->
<?php 	    comments_template( '', true ); ?>
<?php } // end of the loop ?>
	</div><!-- #content -->
<?php	weaver_put_wvr_widgetarea( 'bottom-widget-area','ttw-bot-widget','ttw_hide_widg_pages');
    weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget');
?>
