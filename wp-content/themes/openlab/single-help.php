<?php
/*
Template Name: Help
*/

/**begin layout**/
get_header(); ?>
        <?php openlab_bp_mobile_sidebar('help'); ?>
	<div id="content" class="hfeed row">
            <div class="col-sm-18 col-xs-24 col-help">
		<?php openlab_help_loop(); ?>
            </div>
        <?php openlab_bp_sidebar('help'); ?>
            </div>
<?php get_footer(); 
/**end layout**/
?>
