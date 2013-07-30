<?php 
global $aecomments;
if (!isset($aecomments)) { //for wp-load.php
	die( 'Access Denied' );
}
//Check the nonce
if (isset($_GET['cid'])) {
	check_admin_referer('blacklist_' . (int)$_GET['cid']);
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
AECJS::register_popups_js( 'blacklist-comment' );
wp_print_scripts( array( 'aec_popups', 'jquery-tools-tabs' ) );
wp_print_styles( array( 'aeccommenteditor' ) );
do_action('add_wp_ajax_comments_css_editor');
?>
<title>WP Ajax Edit Comments Blacklist Comment</title>
</head>
<body class="hidden blacklist">
<?php 
$comment = get_comment(intval($_GET['cid']));
?>
<?php 
/* Admin nonce */
	wp_nonce_field('wp-ajax-edit-comments_blacklist-comment');
?>
<div><input type="hidden" id="commentID" value="<?php echo $commentID;?>" />
  <input type="hidden" id="postID" value="<?php echo $postID;?>" />
  <input type="hidden" id="action" value="<?php echo $commentAction;?>" />
</div>
   <div id="comment-options">
    <div class="wrap">

	<!-- the tabs -->
	<ul class="tabs" id="flowtabs">
		<li><a href="#1" id="t1"><?php _e("Main", $localization); ?></a></li>
        <li><a href="#2" id="t2" class=""><?php _e("Advanced", $localization); ?></a></li>
	</ul>
    <div class="panes">
    <div>
  <p><?php _e("Select from the options below to add to your comment blacklist.", $localization); ?></p>
  	<table class="form inputs">
    <tbody>
    	<tr>
        <td><input type="checkbox" name="blacklist[]" id="name" value="name" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="name"><?php _e('Name',$localization); ?><em><small class="name<?php echo $commentID;?>"> (<?php echo $comment->comment_author ?>)</small></em></label></td>
      </tr>
      <?php
				if ( $comment->comment_author_url != '' && $comment->comment_author_url != 'http://' ) {
			?>
      <tr>
        <td><input type="checkbox" name="blacklist[]" id="url" value="url" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="url"><?php _e('URL',$localization); ?><em><small class="url<?php echo $commentID;?>"> (<?php echo $comment->comment_author_url ?>)</small></em></label></td>
      </tr>
      <?php
				}
			?>
      <tr>
        <td><input type="checkbox" name="blacklist[]" id="email" value="email" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="email"><?php _e('E-mail Address',$localization); ?><em><small class="email<?php echo $commentID;?>"> (<?php echo $comment->comment_author_email ?>)</small></em></label></td>
      </tr>
      <tr>
        <td><input type="checkbox" name="blacklist[]" id="ip" value="ip" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="ip"><?php _e('IP Address',$localization); ?><em><small class="ip<?php echo $commentID;?>"> (<?php echo $comment->comment_author_IP ?>)</small></em></label></td>
      </tr>
    </tbody>
    </table>
    </div><!--content area 1-->
	<div>
    <h3><?php _e("Spam Matching Comments", $localization); ?></h3>
  <p><?php _e(" Example: Selecting 'email' and 'name' will spam all comments that match both the name and e-mail address.", $localization);?><?php _e("Please be careful with this feature.  There is no undo function.", $localization);?></p>
  	<table class="form inputs">
    <tbody>
    	<tr>
        <td><input type="checkbox" name="spam[]" id="spamname" value="spamname" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="spamname"><?php _e('Name',$localization); ?><em><small class="name<?php echo $commentID;?>"> (<?php echo $comment->comment_author ?></small>)</em></label></td>
      </tr>
      <?php
				if ( $comment->comment_author_url != '' && $comment->comment_author_url != 'http://' ) {
			?>
      <tr>
        <td><input type="checkbox" name="blacklist[]" id="spamurl" value="spamurl" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="spamurl"><?php _e('URL',$localization); ?><em><small class="spamurl<?php echo $commentID;?>"> (<?php echo $comment->comment_author_url ?>)</small></em></label></td>
      </tr>
      <?php
				}
			?>
      <tr>
        <td><input type="checkbox" name="spam[]" id="spamemail" value="spamemail" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="spamemail"><?php _e('E-mail Address',$localization); ?><em><small class="email<?php echo $commentID;?>"> (<?php echo $comment->comment_author_email ?>)</small></em></label></td>
      </tr>
      <tr>
        <td><input type="checkbox" name="spam[]" id="spamip" value="spamip" /></td>
        <td>&nbsp;&nbsp;&nbsp;<label for="spamip"><?php _e('IP Address',$localization); ?><em><small class="ip<?php echo $commentID;?>"> (<?php echo $comment->comment_author_IP ?>)</small></em></label></td>
      </tr>
    </tbody>
    </table>
    </div><!--content area 2-->
    </div><!-- end panes -->
    </div><!--wrapper-->
<div class="form" id="buttons">
	<div><input type="button" id="send-request" name="send-request" value="<?php _e('Blacklist',$localization); ?>" /></div>
  <div><input type="button" name="cancel" id="cancel" value="<?php _e('Cancel',$localization); ?>" /></div>
</div>
<div id="status"><span id="message"></span><span id="close-option">&nbsp;-&nbsp;<a href="#"><?php _e('Close',$localization); ?></a></span></div>
</div><!--end comment options -->
</body>
</html>
