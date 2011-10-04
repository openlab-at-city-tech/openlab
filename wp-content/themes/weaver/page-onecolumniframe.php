<?php
/**
 * Template Name: One column, iframe full width
 *
 * A custom page template without sidebar styled for iframes.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 */

get_header(); ?>
      	<div id="container" class="one-column-iframe">
	<?php
	get_template_part('pgtpl','container');
	?>
	</div><!-- #container -->
<?php get_footer(); ?>
