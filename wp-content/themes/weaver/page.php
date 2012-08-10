<?php
/**
 * The template used to display all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 */
?>
<?php get_header(); ?>

	<?php if (weaver_is_checked_page_opt('ttw_hide_sidebars')) echo("<div id=\"container\" class=\"one-column container-page\">\n");
	else echo("<div id=\"container\" class=\"container-page\">\n");
	
	get_template_part('pgtpl','container');
?>
	</div><!-- #container -->
<?php if (!weaver_is_checked_page_opt('ttw_hide_sidebars')) get_sidebar(); ?>
<?php get_footer(); ?>
