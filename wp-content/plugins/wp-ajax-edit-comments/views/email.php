<?php
global $aecomments;
if (!isset($aecomments)) { //for wp-load.php
	die( 'Access Denied' );
}
if (isset($_GET['cid'])) {
	check_admin_referer('email_' . (int)$_GET['cid']);
}
$commentID = (int)$_GET['cid'];
$postID = (int)$_GET['pid'];
$commentAction = $_GET['action'];
$fromEmail = get_option('admin_email');
$toEmail = strip_tags($_GET['commenter']);
$commentAction = addslashes(preg_replace("/[^a-z0-9]/i", '', strip_tags($commentAction)));
$localization = 'ajaxEdit';
$comment = get_comment($commentID);
$post  = get_post($comment->comment_post_ID);
$user  = get_userdata( $post->post_author );
$email = $user->user_email;

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
AECJS::register_popups_js( 'email' );
wp_print_scripts( array( 'aec_popups' ) );
wp_print_styles( array( 'aeccommenteditor' ) );
do_action('add_wp_ajax_comments_css_editor');
?>
<title>WP Ajax Edit Comments E-mail</title>
</head>
<body class="hidden email">
<div id="comment-options">
<?php
/* Admin nonce */

	wp_nonce_field('wp-ajax-edit-comments_email-comment');
?>
<input type="hidden" id="commentID" value="<?php echo $commentID;?>" />
  <input type="hidden" id="postID" value="<?php echo $postID;?>" />
  <input type="hidden" id="action" value="<?php echo $commentAction;?>" />
  	<table class="form inputs">
    <tbody>
      <tr>
        <td><label for="to"><?php _e('To',$localization); ?></label></td>
        <td><span> : </span><input type="text" size="35" name="to" id="to" value="<?php echo $toEmail; ?>" /></td>
      </tr>
      <tr>
        <td><label for="from"><?php _e('From',$localization); ?></label></td>
        <?php
		
		?>
        <td><span> : </span><select name="from"><option selected="selected" value="<?php echo $fromEmail; ?>"><?php _e('Site Admin',$localization); ?> : <?php echo $fromEmail; ?></option><option value="<?php echo $email; ?>"><?php _e('Post Author',$localization); ?> : <?php echo $email; ?></option></select></td>
      </tr>
      <tr>
        <td><label for="subject"><?php _e('Subject',$localization); ?></label></td>
        <td><span> : </span><input type="text" size="35" name="subject" id="subject" /></td>
      </tr>
    </tbody>
    </table>
    <div id="edit_options"></div>
<div class="form"><textarea style="width: 95%" cols="50" rows="8" name="message" id="message"></textarea></div>

<div class="form" id="buttons">
	<div><input type="button" id="send" name="send" value="<?php _e('Send',$localization); ?>" /></div>
  <div><input type="button" name="cancel" id="cancel" value="<?php _e('Cancel',$localization); ?>" /></div>
</div>
<div id="status"><span id="status_message"></span><span id="close-option">&nbsp;-&nbsp;<a href="#"><?php _e('Close',$localization); ?></a></span></div>
</div> <!-- end comment-options-->
</body>
</html>
