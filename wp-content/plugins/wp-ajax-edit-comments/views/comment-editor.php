<?php
function aec_touch_time( $edit = 1, $for_post = 1, $tab_index = 0, $commentID = 0) {
	global $wp_locale, $post, $aecomments;
	$multi = 0;
	$comment = get_comment($commentID);

	if ( $for_post )
		$edit = ! ( in_array($post->post_status, array('draft', 'pending') ) && (!$post->post_date_gmt || '0000-00-00 00:00:00' == $post->post_date_gmt ) );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	// echo '<label for="timestamp" style="display: block;"><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp"'.$tab_index_attribute.' /> '.__( 'Edit timestamp' ).'</label><br />';

	$time_adj = current_time('timestamp');
	$post_date = ($for_post) ? $post->post_date : $comment->comment_date;
	$jj = ($edit) ? mysql2date( 'd', $post_date, false ) : gmdate( 'd', $time_adj );
	$mm = ($edit) ? mysql2date( 'm', $post_date, false ) : gmdate( 'm', $time_adj );
	$aa = ($edit) ? mysql2date( 'Y', $post_date, false ) : gmdate( 'Y', $time_adj );
	$hh = ($edit) ? mysql2date( 'H', $post_date, false ) : gmdate( 'H', $time_adj );
	$mn = ($edit) ? mysql2date( 'i', $post_date, false ) : gmdate( 'i', $time_adj );
	$ss = ($edit) ? mysql2date( 's', $post_date, false ) : gmdate( 's', $time_adj );

	$cur_jj = gmdate( 'd', $time_adj );
	$cur_mm = gmdate( 'm', $time_adj );
	$cur_aa = gmdate( 'Y', $time_adj );
	$cur_hh = gmdate( 'H', $time_adj );
	$cur_mn = gmdate( 'i', $time_adj );

	$month = "<select " . ( $multi ? '' : 'id="mm" ' ) . "name=\"mm\"$tab_index_attribute>\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
		if ( $i == $mm )
			$month .= ' selected="selected"';
		$month .= '>' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
	}
	$month .= '</select>';

	$day = '<input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
	$year = '<input type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" />';
	$hour = '<input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
	$minute = '<input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';

	echo '<div class="timestamp-wrap">';
	/* translators: 1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input */
	printf(__('%1$s%2$s, %3$s @ %4$s : %5$s', 'ajaxEdit' ), $month, $day, $year, $hour, $minute);

	echo '</div><input type="hidden" id="ss" name="ss" value="' . $ss . '" />';

	if ( $multi ) return;

	echo "\n\n";
	foreach ( array('mm', 'jj', 'aa', 'hh', 'mn') as $timeunit ) {
		echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $$timeunit . '" />' . "\n";
		$cur_timeunit = 'cur_' . $timeunit;
		echo '<input type="hidden" id="'. $cur_timeunit . '" name="'. $cur_timeunit . '" value="' . $$cur_timeunit . '" />' . "\n";
	}
} //aec_touch_time
global $aecomments;
if (!isset($aecomments)) { //for wp-load.php
	die( 'Access Denied' );
}
//Check the nonce
check_admin_referer('editcomment_' . (int)$_GET['cid']);
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
AECJS::register_popups_js( 'comment-editor' );
wp_print_scripts( array( 'aec_popups', 'jquery-tools-tabs' ) );
wp_print_styles( array( 'aeccommenteditor' ) );
do_action('add_wp_ajax_comments_css_editor');
?>
<title>WP Ajax Edit Comments Comment Editor</title>
</head>
<body class="hidden editor">

<div id="container">
<?php
if ( AECCore::is_comment_owner() ) : 
?>
<div class="wrap">

	<!-- the tabs -->
	<ul class="tabs" id="flowtabs">
		<li><a href="#1" id="t1"><?php _e("Main", "ajaxEdit"); ?></a></li>
        <li><a href="#2" id="t2" class=""><?php _e("Advanced", "ajaxEdit"); ?></a></li>
	</ul>
    <div class="panes">
    <div>
<?php endif; ?>
<?php
/* Admin nonce */
	wp_nonce_field('wp-ajax-edit-comments_save-comment');
?>
<div><input type="hidden" id="commentID" value="<?php echo $commentID;?>" />
  <input type="hidden" id="postID" value="<?php echo $postID;?>" />
  <input type="hidden" id="action" value="<?php echo $commentAction;?>" /></div>
<?php if (AECCore::can_edit_options($commentID, $postID)): 
?>
  	<table class="form inputs">
    <tbody>
    	<?php if (AECCore::can_edit_name($commentID, $postID)): ?>
      <tr>
        <td><label for="name"><?php _e('Name',"ajaxEdit"); ?></label></td>
        <td><span> : </span><input type="text" size="35" name="name" id="name" /></td>
      </tr>
      <?php endif;?>
      <?php if (AECCore::can_edit_email($commentID, $postID)): ?>
      <tr>
        <td><label for="e-mail"><?php _e('E-mail',"ajaxEdit"); ?></label></td>
        <td><span> : </span><input type="text" size="35" name="e-mail" id="e-mail" /></td>
      </tr>
      <?php endif;?>
      <?php if (AECCore::can_edit_url($commentID, $postID)): ?>
      <tr>
        <td><label for="URL"><?php _e('URL',"ajaxEdit"); ?></label></td>
        <td><span> : </span><input type="text" size="35" name="URL" id="URL" /></td>
      </tr>
      <?php endif;?>
    </tbody>
    </table>
    <table><tbody>
    <?php do_action('wp_ajax_comments_editor'); ?>
    </tbody></table>
<?php endif; ?>
<div id="edit_options"></div>
<div class="form"><textarea cols="50" rows="8" name="comment" id="comment">&nbsp;</textarea></div> <!--form-->
<?php
if ( AECCore::is_comment_owner() ) : 
?>
</div><!--content area 1-->
<div>
        <div id="comment-options">
<?php
        
				// translators: Publish box date formt, see http://php.net/date
				$datef = __( 'M j, Y @ G:i' );
				$stamp = __('Submitted on: <b>%1$s</b>');
				$date = date_i18n( $datef, strtotime( $comment->comment_date ) );
?>			<h3><?php _e('Adjust Comment Time',"ajaxEdit"); ?></h3>
				<div><span id="timestamp"><?php printf($stamp, $date); ?></span><div><?php aec_touch_time(1, 0, 5, $commentID); ?></div><br /></div>
        <h3><?php _e('Adjust Comment Status', "ajaxEdit"); ?></h3>
        <?php 
				//todo - replace with wordpress checked() function
				function aec_checked( $checked, $current) {
					if ( $checked == $current)
						echo ' checked="checked"';
				}
				?>
        <div class="misc-pub-section" id="comment-status-radio">
        <?php 
				if ($aecomments->get_admin_option( 'use_rtl' ) == "true") {
				?>
        	<label class="approved"><?php echo _e('Approved',"ajaxEdit") ?><input type="radio"<?php aec_checked( $comment->comment_approved, '1' ); ?> name="comment_status" value="1" /></label><br />
          <label class="waiting"><?php echo _e('Pending', "ajaxEdit") ?><input type="radio"<?php aec_checked( $comment->comment_approved, '0' ); ?> name="comment_status" value="0" /></label><br />
          <label class="spam"><?php echo _e('Spam', "ajaxEdit"); ?><input type="radio"<?php aec_checked( $comment->comment_approved, 'spam' ); ?> name="comment_status" value="spam" /></label><br />
          <label class="trash"><?php echo _e('Trash',"ajaxEdit") ?><input type="radio"<?php aec_checked( $comment->comment_approved, 'trash' ); ?> name="comment_status" value="trash" /></label>
        <?php
				} else {
				?>
          <label class="approved"><input type="radio"<?php aec_checked( $comment->comment_approved, '1' ); ?> name="comment_status" value="1" /><?php echo _e('Approved',"ajaxEdit") ?></label><br />
          <label class="waiting"><input type="radio"<?php aec_checked( $comment->comment_approved, '0' ); ?> name="comment_status" value="0" /><?php echo _e('Pending', "ajaxEdit") ?></label><br />
          <label class="spam"><input type="radio"<?php aec_checked( $comment->comment_approved, 'spam' ); ?> name="comment_status" value="spam" /><?php echo _e('Spam', "ajaxEdit"); ?></label><br />
          <label class="trash"><input type="radio"<?php aec_checked( $comment->comment_approved, 'trash' ); ?> name="comment_status" value="trash" /><?php echo _e('Trash', "ajaxEdit"); ?></label>
				<?php } ?>
				</div>
        </div><!-- end comment options-->
</div> <!--content area 2-->
</div><!--panes-->
</div> <!--wrapper -->
<?php endif; ?>
<div class="form" id="buttons">
	<div><input type="button" id="save" name="save" disabled="true" value="<?php _e('Save',"ajaxEdit"); ?>" /></div>
  <div><input type="button" name="cancel" id="cancel" disabled="true" value="<?php _e('Cancel',"ajaxEdit"); ?>" /></div>
  <div id="timer<?php echo $commentID ?>"></div>
</div>
<div id="status"><span id="message"></span><span id="close-option">&nbsp;-&nbsp;<a href="#"><?php _e('Close',"ajaxEdit"); ?></a></span></div>
</div> <!-- end container-->
</body>
</html>
