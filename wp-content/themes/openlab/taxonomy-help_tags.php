<?php
/*
* Help tags template
*
*/

/**begin layout**/
get_header(); ?>
	<div id="content" class="hfeed">
            <?php openlab_bp_mobile_sidebar('help'); ?>
            <div class="col-sm-18 col-xs-24">
                <div id="openlab-main-content" class="content-wrapper">
                    <?php openlab_help_tags_loop(); ?>
                </div>
            </div>
            <?php openlab_bp_sidebar('help', false, ' mobile-enabled'); ?>
	</div>
<?php get_footer(); 
/**end layout**/