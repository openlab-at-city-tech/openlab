<?php
global $aecomments;
if (!isset($aecomments)) { //for wp-load.php
	die( 'Access Denied' );
}
//Check the nonce
if (isset($_GET['cid'])) {
	check_admin_referer('movecomment_' . (int)$_GET['cid']);
}
$commentID = (int)$_GET['cid'];
$postID = (int)$_GET['pid'];
$commentAction = $_GET['action'];
$commentAction = addslashes(preg_replace("/[^a-z0-9]/i", '', strip_tags($commentAction))); 
$comment = get_comment($commentID);

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
AECJS::register_popups_js( 'move-comment' );
wp_print_scripts( array( 'aec_popups', 'jquery-tools-tabs' ) );
wp_print_styles( array( 'aeccommenteditor' ) );
do_action('add_wp_ajax_comments_css_editor');
?>
<title>WP Ajax Edit Comments Move Comment</title>
</head>
<body class="hidden move">
<div id="comment-options">
<?php
/* Admin nonce */
if ( AECCore::is_comment_owner($postID)) {
	wp_nonce_field('wp-ajax-edit-comments_move-comment');
}
?>
<div class="wrap">

	<!-- the tabs -->
	<ul class="tabs" id="flowtabs">
		<li><a href="#1" id="t1"><?php _e("Move by Post", 'ajaxEdit'); ?></a></li>
        <li><a href="#2" id="t2" class=""><?php _e("Move by Title", 'ajaxEdit'); ?></a></li>
         <li><a href="#3" id="t3" class=""><?php _e("Move by ID", 'ajaxEdit'); ?></a></li>
	</ul>
    <div class="panes">
    <div>
 <input type="hidden" id="commentID" value="<?php echo $commentID;?>" />
  <input type="hidden" id="postID" value="<?php echo $postID;?>" />
  <input type="hidden" id="action" value="<?php echo $commentAction;?>" />
  <input type="hidden" id="selectedID" value="0" />
    	<div id="post_loading" class="loading hidden"></div>
    	<div id="post_radio"></div>
    <input type="hidden" id="post_offset" name="post_offset" value="0" />
    <br /><br /><br />
    	<div style="clear: both;"><a class="previous hidden" id="post_previous" href="#"><span class="previcon"></span><?php _e('Previous','ajaxEdit'); ?></a><a class="next hidden" id="post_next" href="#"><?php _e('Next','ajaxEdit'); ?><span class="nexticon"></span></a></div>
  <div class="form" id="post_buttons"><br /> 
    <div><input type="button" id="post_move" name="post_move" disabled="true" value="<?php _e('Move','ajaxEdit'); ?>" /></div>
  </div><!--buttons-->
</div><!--content area1-->
<div>
  	<table class="form inputs">
    <tbody>
      <tr>
      	<?php if ($aecomments->get_admin_option( 'use_rtl' ) == "true") {
				?>
        <td><input type="button" id="title_search" name="title_search" value="<?php _e('Search','ajaxEdit'); ?>" /></td>
        <td>&nbsp;&nbsp;<input type="text" size="25" name="move_title" id="move_title" /><span> : </span></td>
        <td><label for="move_title"><?php _e('Title','ajaxEdit'); ?></label></td>
        <?php 
				} else {
				?>
        <td><label for="move_title"><?php _e('Title','ajaxEdit'); ?></label></td>
        <td><span> : </span><input type="text" size="25" name="move_title" id="move_title" /></td>
        <td>&nbsp;<input type="button" id="title_search" name="title_search" value="<?php _e('Search','ajaxEdit'); ?>" /></td>
        <?php } ?>
      </tr>
    </tbody>
    </table>
    <div id="post_title_loading" class="loading hidden"></div>
    <div id="post_title_radio"></div>
    <div class="form hidden" id="post_title_buttons"><br />
			<div><input type="button" id="post_title_move" name="post_title_move" disabled="true" value="<?php _e('Move','ajaxEdit'); ?>" /></div>
		</div><!--buttons-->
</div><!--content area 2-->
<div>
    <table class="form inputs">
    <tbody>
      <tr>
      	<?php if ($aecomments->get_admin_option( 'use_rtl' ) == "true") {
				?>
        <td><input type="button" id="id_search" name="id_search" value="<?php _e('Search','ajaxEdit'); ?>" /></td>
        <td>&nbsp;&nbsp;<input type="text" size="25" name="post_id" id="post_id" /><span> : </span></td>
        <td><label for="post_id"><?php _e('Post ID','ajaxEdit'); ?></label></td>			
        <?php
				} else {
				?>
        <td><label for="post_id"><?php _e('Post ID','ajaxEdit'); ?></label></td>
        <td><span> : </span><input type="text" size="25" name="post_id" id="post_id" /></td>
        <td>&nbsp;<input type="button" id="id_search" name="id_search" value="<?php _e('Search','ajaxEdit'); ?>" /></td>			
        <?php } ?>
      </tr>
    </tbody>
    </table>
    <div id="post_id_loading" class="loading hidden"></div>
    <div id="post_id_radio"></div>
    <div class="form hidden" id="post_id_buttons">
      <div><input type="button" id="post_id_move" name="post_id_move" disabled="true" value="<?php _e('Move','ajaxEdit'); ?>" /></div>
		</div><!-- end buttons -->
</div><!--content area 3-->
</div><!--end panes-->
</div><!--wrapper-->
<?php 
	$comment = get_comment($commentID);
	if ($comment->comment_approved != "1") {
		?>
    <div>
      <table class="form inputs">
      <tbody>
        <tr>
        <?php
					if ($aecomments->get_admin_option( 'use_rtl' ) == "true") {
					?>
					<td><input id="approved" type="checkbox" name="approved" value="1" checked="checked" /></td>
          <td><span> : </span><label for="approved"><?php _e('Mark as Approved','ajaxEdit'); ?></label></td>
					<?php } else {
					?>
          <td><label for="approved"><?php _e('Mark as Approved','ajaxEdit'); ?></label></td>
          <td><span> : </span><input id="approved" type="checkbox" name="approved" value="1" checked="checked" /></td>
          <?php
					}?>
        </tr>
      </tbody>
      </table>
		</div>   
    <?php
	}
?>
<div id="status"><span id="message"></span><span id="close-option">&nbsp;-&nbsp;<a href="#"><?php _e('Close','ajaxEdit'); ?></a></span></div>
</div><!--end comment options -->
</body>
</html>
