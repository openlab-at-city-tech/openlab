<?php
/*
Template Name: Help
*/
/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed row">
            <div class="col-sm-18">
		<?php openlab_help_cats_loop(); ?>
            </div>
            <?php openlab_bp_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/