<?php
/*
Template Name: Help
*/

/**begin layout**/
get_header(); ?>
	<div id="content" class="hfeed row">
            <div class="col-sm-18 col-xs-24 col-help">
                <div id="openlab-main-content" class="content-wrapper">
                    <?php openlab_help_loop(); ?>
                </div>
            </div>
        <?php openlab_bp_sidebar('help', false, ' mobile-enabled'); ?>
            </div>
<?php get_footer();
/**end layout**/
?>
