<?php /**
*  sign up form template
*
*/

get_header(); ?>

	<div id="content" class="hfeed">
    	<?php openlab_registration_page(); ?>
    </div><!--content-->
    
    <div id="sidebar" class="sidebar widget-area">
	<?php openlab_buddypress_register_actions(); ?>
    </div>
	
<?php get_footer();