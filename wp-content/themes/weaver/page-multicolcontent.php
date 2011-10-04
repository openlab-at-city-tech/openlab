<?php
/**
 * Template Name: 2 Col Content (split w/ &lt;!--more--&gt;)
 *
 * A custom page template with a right sidebar and alternate widget area.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 */
?>
<?php
    get_header();

 if (weaver_is_checked_page_opt('ttw_hide_sidebars')) echo("<div id=\"container\" class=\"one-column wvr-twocolumn\">\n");
	else echo("<div id=\"container\" class=\"wvr-twocolumn\">\n");
	weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget');
	weaver_put_wvr_widgetarea('top-widget-area','ttw-top-widget','ttw_hide_widg_pages');
	weaver_put_perpage_widgetarea();
?>
    <div id="content">
	<?php
	if ( have_posts() ) {
	the_post(); ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	    <?php if ( is_front_page() ) {
		weaver_put_page_title('h2');
		} else {
		    weaver_put_page_title('h1');
	    } ?>
		<div class="entry-content">
		    <?php $content = get_the_content('',FALSE,''); //arguments remove 'more' text
			echo weaver_multi_col($content);
			echo ("<div class=\"clear-cols\"></div>");
			wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', WEAVER_TRANS ), 'after' => '</div>' ) );
			edit_post_link( __( 'Edit', WEAVER_TRANS ), '<span class="edit-link">', '</span>' );
		    ?>
		</div><!-- .entry-content -->
	</div><!-- #post-<?php the_ID(); ?> -->
<?php 	comments_template( '', true );
	}
?>
    </div><!-- #content -->
    <?php weaver_put_wvr_widgetarea('bottom-widget-area','ttw-bot-widget','ttw_hide_widg_pages'); ?>
    <?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
</div><!-- #container -->
<?php if (!weaver_is_checked_page_opt('ttw_hide_sidebars')) get_sidebar(); ?>
<?php get_footer(); ?>
