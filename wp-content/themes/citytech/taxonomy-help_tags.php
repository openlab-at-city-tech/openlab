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
    <div id="sidebar" class="sidebar widget-area">
		<?php get_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/