<?php 
/* Admin Panel Support Code - Created on May 23, 2010 by Ronald Huereca 
Last modified on May 23, 2010
*/

global $wpdb,$aecomments, $user_email;
if ( !is_a( $aecomments, 'WPrapAjaxEditComments' ) && !current_user_can( 'administrator' ) ) 
	die('');
 //global settings
			
?>
<div class="wrap">
<h2>Ajax Edit Comments <?php _e('Support', 'ajaxEdit'); ?></h2>


<!-- the tabs -->
	<ul class="tabs" id="flowtabs">
        <li><a href="#1" id="t1"  class=""><?php _e('Support Forums', 'ajaxEdit');?></a></li>
        <li><a href="#2" id="t2" class=""><?php _e('Documentation', 'ajaxEdit');?></a></li>
	</ul>
   
<div class='pane' style="display: block;">
<p><?php printf(__("To receive help from PluginBuddy support and the PluginBuddy community, please consider leaving a support request via our %sSupport Forums%s", 'ajaxEdit'),"<a href='http://www.ithemes.com/support/ajax-edit-comments'>","</a>"); ?>.</p>
</div><!--contentarea2-->
<div class='pane' style="display: none;">
<p><?php printf(__("For in-depth documenation, including feature descriptions and how-to videos, please visit our %sDocumentation Section%s", 'ajaxEdit'),"<a href='http://ithemes.com/codex/page/Ajax-Edit-Comments'>","</a>"); ?>.</p></div><!--contentarea3-->
</div><!--/wrap-->
<ul class="tabs" id="flowtabs">
</ul>