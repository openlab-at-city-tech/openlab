<?php
/*
Template Name: Help
*/

/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed row">
            <div class="col-sm-9">
		<?php openlab_help_loop(); ?>
            </div>
    <div id="sidebar" class="sidebar widget-area col-sm-3">
		<?php get_sidebar('help'); ?>
	</div>
            </div>
<?php get_footer(); 
/**end layout**/
?>
