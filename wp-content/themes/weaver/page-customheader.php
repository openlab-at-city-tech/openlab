<?php
/**
 * Template Name: Custom Header (see Adv Opts admin)
 *
 * A custom page template with a right sidebar and alternate widget area.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 */
?>
<?php
    get_header('custom');
    if (weaver_is_checked_page_opt('ttw_hide_sidebars')) echo("<div id=\"container\" class=\"one-column wvr-customheader\">\n");
    else echo("<div id=\"container\" class=\"wvr-customheader\">\n");

    get_template_part('pgtpl','container');
?>
	</div><!-- #container -->
<?php if (!weaver_is_checked_page_opt('ttw_hide_sidebars')) get_sidebar(); ?>
<?php get_footer(); ?>
