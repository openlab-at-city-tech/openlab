<?php
/*
* Help tags template
*
*/

/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
		<?php openlab_help_tags_loop(); ?>
	</div>
        <?php openlab_bp_sidebar('help'); ?>
<?php get_footer(); 
/**end layout**/