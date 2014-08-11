<?php
/*
Template Name: Help
*/
/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
		<?php openlab_help_cats_loop(); ?>
	</div>
        <?php openlab_bp_sidebar('help'); ?>
<?php get_footer(); 
/**end layout**/