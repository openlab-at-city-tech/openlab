<?php
/*
*	Glossary page
*
*/
/**begin layout**/
get_header(); ?>
	<div id="content" class="hfeed">
            <?php openlab_bp_mobile_sidebar('help'); ?>
            <div class="col-sm-18 col-xs-24">
                <div class="content-wrapper">
                    <?php openlab_glossary_cats_loop(); ?>
                </div>
            </div>
            <?php openlab_bp_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/