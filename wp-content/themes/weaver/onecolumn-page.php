<?php
/**
 * Template Name: One column, no sidebar
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 */

get_header(); ?>
      	<div id="container" class="one-column">
<?php
	get_template_part('pgtpl','container');
?>
	</div><!-- #container -->
<?php get_footer(); ?>
