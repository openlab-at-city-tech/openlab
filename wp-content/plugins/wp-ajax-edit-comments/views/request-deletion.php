<?php 
global $aecomments;
if (!isset($aecomments)) { //for wp-load.php
	die( 'Access Denied' );
}
//Check the nonce
if (isset($_GET['cid'])) {
	check_admin_referer('requestdeletion_' . (int)$_GET['cid']);
}
$commentID = (int)$_GET['cid'];
$postID = (int)$_GET['pid'];
$commentAction = $_GET['action'];
$commentAction = addslashes(preg_replace("/[^a-z0-9]/i", '', strip_tags($commentAction))); 

$localization = 'ajaxEdit';
$min = '';
if ($aecomments->get_admin_option( 'compressed_scripts' ) == 'true') {
	$min = ".min";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
AECCSS::output_interface_css();
AECJS::register_popups_js( 'request-deletion' );
wp_print_scripts( array( 'aec_popups' ) );
wp_print_styles( array( 'aeccommenteditor' ) );
do_action('add_wp_ajax_comments_css_editor');
?>
<title>WP Ajax Edit Comments Request Deletion</title>
</head>
<body class="request-deletion">
<div id="comment-options">
<h3><?php _e("Request Deletion", 'ajaxEdit'); ?></h3>
<?php 
/* Admin nonce */
	wp_nonce_field('requestdeletion_' . $commentID);
?>
<div><input type="hidden" id="commentID" value="<?php echo $commentID;?>" />
  <input type="hidden" id="postID" value="<?php echo $postID;?>" />
  <input type="hidden" id="action" value="<?php echo $commentAction;?>" /></div>
  	<table class="form inputs">
    <tbody>
    	<tr>
      <td>
      <?php _e('Please explain why the comment should be deleted.  After sending the request, your comment will be marked as moderated and will no longer be viewable publicly.','ajaxEdit'); ?>
      </td>
      <tr>
        <td><textarea style="width: 95%" cols="70" rows="8" name="deletion-reason" id="deletion-reason"></textarea></td>
      </tr>
    </tbody>
    </table>
<div class="form" id="buttons">
	<div><input type="button" id="send-request" name="send-request" value="<?php _e('Send Request','ajaxEdit'); ?>" /></div>
  <div><input type="button" name="cancel" id="cancel" value="<?php _e('Cancel','ajaxEdit'); ?>" /></div>
</div>
<div id="status"><span id="message"></span><span id="close-option">&nbsp;-&nbsp;<a href="#"><?php _e('Close','ajaxEdit'); ?></a></span></div>
</div> <!-- end comment options-->
</body>
</html>
