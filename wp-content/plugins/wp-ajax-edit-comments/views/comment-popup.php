<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WP Ajax Edit Comments Popup Box</title>
<?php
global $aecomments;
if (!isset($aecomments)) { //for wp-load.php
	die( 'Access Denied' );
}
load_plugin_textdomain( 'ajaxEdit', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' ); //For language purposes, this doesn't seem to run when a user first leaves a comment.

do_action('aec-popup-box-head');
AECCSS::output_interface_css();
AECJS::register_popups_js( 'comment-popup' );
wp_print_scripts( array( 'aec_popups' ) );
wp_print_styles( array( 'aeccommenteditor' ) );
do_action('add_wp_ajax_comments_css_editor');

?>
</head>
<body id="aec-popup">
<?php
do_action('aec-popup-box');
?>
<p><?php _e("Please type out your comment below.",'ajaxEdit');?></p>
<div id="aec_edit_options"></div>
<textarea name="comment" id="comment" cols="100%" rows="5" tabindex="1"></textarea>
<div class="form" id="buttons"><input type="button" id="close" name="close" value="<?php _e("Save and Return",'ajaxEdit'); ?>" /><input type="button" id="submit" name="submit" value="<?php _e("Submit Comment",'ajaxEdit'); ?>" />
</div><!--end form-->
</body>
</html>
