<?php
/*
*	Glossary page
*
*/
/**begin layout**/
get_header(); ?>
        <?php openlab_bp_mobile_sidebar('help'); ?>
	<div id="content" class="hfeed">
            <div class="col-sm-18 col-xs-24">
		<?php openlab_glossary_cats_loop(); ?>
            </div>
            <?php openlab_bp_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/