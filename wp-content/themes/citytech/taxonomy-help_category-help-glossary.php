<?php
/*
*	Glossary page
*
*/
/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
		<?php openlab_glossary_cats_loop(); ?>
	</div>
    <div id="sidebar" class="sidebar widget-area">
		<?php get_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/