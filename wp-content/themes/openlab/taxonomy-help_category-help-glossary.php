<?php
/*
*	Glossary page
*
*/
/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
            <div class="col-sm-18">
		<?php openlab_glossary_cats_loop(); ?>
            </div>
            <?php openlab_bp_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/