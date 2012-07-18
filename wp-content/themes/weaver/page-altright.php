<?php
/**
 * Template Name: Alternative sidebar, right
 *
 * A custom page template with a right sidebar and alternate widget area.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 */
    get_header();
?>
    <div class="right-alt">
      	<?php if (weaver_is_checked_page_opt('ttw_hide_sidebars')) echo("<div id=\"container\" class=\"one-column wvr-altright\">\n");
	else echo("<div id=\"container\" class=\"wvr-altright\">\n");

		get_template_part('pgtpl','container');
?>
	</div><!-- #container altright -->
    </div>
<?php if (!weaver_is_checked_page_opt('ttw_hide_sidebars')) get_sidebar('altright'); ?>
<?php get_footer(); ?>
