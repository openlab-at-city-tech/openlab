<?php 
/* Admin Panel Code - Created on April 19, 2008 by Ronald Huereca 
Last modified on May 23, 2010
*/
global $wpdb,$aecomments, $user_email;
if ( !is_a( $aecomments, 'WPrapAjaxEditComments' ) && !current_user_can( 'administrator' ) ) 
	die('');

//Delete security keys 
if (isset($_POST['security_keys'])) {
	if ($_POST['security_keys'] == "true") {
		check_admin_referer('wp-ajax-edit-comments_admin-options');
		$query = "delete from $wpdb->postmeta where left(meta_value, 6) = 'wpAjax'";
		@$wpdb->query( $query );
		$query = "delete from $wpdb->posts where post_type = 'ajax_edit_comments'";
		@$wpdb->query( $query ); 
		?>
			<div class="updated"><p><strong><?php _e('Security keys deleted', 'ajaxEdit') ?></strong></p></div>
		<?php
	}
}

$options = $aecomments->get_all_admin_options(); //global settings
$options['use_rtl'] = 'false';
//Update settings
$updated = false;
if (isset($_POST['update'])) { 
	 check_admin_referer('wp-ajax-edit-comments_admin-options');
	$error = false;
	
	//Validate the comment time entered
	if (isset($_POST['comment_time'])) {
		$commentTimeErrorMessage = '';
		$commentClass = 'error';
		$minutes = absint( $_POST['comment_time'] );
		if( $minutes < 1) {
			$commentTimeErrorMessage = __("Comment time must be greater than one minute.",'ajaxEdit');
			$error = true;
		} else {
			$options['minutes'] = $minutes;
			$updated = true;
		}
		if (!empty($commentTimeErrorMessage)) {
		?>
<div class="<?php echo $commentClass;?>"><p><strong><?php _e($commentTimeErrorMessage, 'ajaxEdit');?></p></strong></div>
		<?php
		}
	}
	
		//Update global settings
		$options['allow_editing'] = $_POST['allow_editing'];
		$options['allow_editing_after_comment'] = $_POST['allow_editing_after_comment'];
		$options['spam_text'] = apply_filters('pre_comment_content',apply_filters('comment_save_pre', $_POST['spam_text']));
		$options['show_timer'] = $_POST['show_timer'];
		$options['show_pages'] = $_POST['show_pages'];
		$options['email_edits'] = $_POST['email_edits'];
		$options['spam_protection'] = $_POST['spam_protection'];
		$options['use_mb_convert'] = $_POST['use_mb_convert'];
		$options['registered_users_edit'] = $_POST['registered_users_edit'];
		$options['registered_users_name_edit'] = $_POST['registered_users_name_edit'];
		$options['registered_users_url_edit'] = $_POST['registered_users_url_edit'];
		$options['registered_users_email_edit'] = $_POST['registered_users_email_edit'];
		$options['allow_email_editing'] = $_POST['allow_email_editing'];
		$options['allow_url_editing'] = $_POST['allow_url_editing'];
		$options['use_rtl'] = "false";
		$options['allow_name_editing'] = $_POST['allow_name_editing'];
		$options['clear_after'] = $_POST['clear_after'];
		$options['javascript_scrolling'] = $_POST['javascript_scrolling'];
		$options['comment_display_top'] = stripslashes_deep(trim($_POST['comment_display_top']));
		$options['icon_display'] = $_POST['icon_display'];
		$options['icon_set'] = $_POST['icon_set'];
		$options['affiliate_text'] = apply_filters('pre_comment_content',apply_filters('comment_save_pre', $_POST['affiliate_text']));
		$options['affiliate_show'] = $_POST['affiliate_show'];
		$options['scripts_in_footer'] = $_POST['scripts_in_footer'];
		$options['compressed_scripts'] = $_POST['compressed_scripts'];
		$options['after_deadline_posts'] = $_POST['after_deadline_posts'];
		$options['after_deadline_popups'] = $_POST['after_deadline_popups'];
		$options['disable_trackbacks'] = $_POST['disable_trackbacks'];
		$options['disable_nofollow'] = $_POST['disable_nofollow'];
		$options['disable_selfpings'] = $_POST['disable_selfpings'];
		$options['delink_content'] = $_POST['delink_content'];
		$options['expand_popups'] = $_POST['expand_popups'];
		$options['expand_posts'] = $_POST['expand_posts'];
		$options['allow_registeredediting'] = $_POST['allow_registeredediting'];
		$options['atdlang'] = $_POST['atdlang'];
		$options['request_deletion_behavior'] = $_POST['request_deletion_behavior'];
		$options['allow_editing_editors'] = $_POST['allow_editing_editors'];
		$options['enable_colorbox'] = $_POST['enable_colorbox'];
		$options['colorbox_width'] = absint( $_POST['colorbox_width'] );
		$options['colorbox_height'] = absint( $_POST['colorbox_height'] );
		//$options['beta_version_notifications'] = $_POST['beta_version_notifications'];
		//Conditions the dropdown values for saving as options
		function aec_dropdown_condition($rowinfo,$postvalue) {
			$postvalue = explode(",", $postvalue);
			$rowinfo['column'] = addslashes(htmlspecialchars($postvalue[0]));
			$rowinfo['position'] =addslashes(htmlspecialchars( $postvalue[1]));
			$rowinfo['enabled'] = addslashes(htmlspecialchars($postvalue[2]));
			return $rowinfo;
		}
		//Conditions the classic values for saving as options
		function aec_classic_condition($rowinfo,$postvalue) {
			$postvalue = explode(",", $postvalue);
			$rowinfo['column'] = addslashes(htmlspecialchars($postvalue[0]));
			$rowinfo['enabled'] = addslashes(htmlspecialchars($postvalue[1]));
			return $rowinfo;
		}
		//Dropdown Menu
		$dropdown = $options['drop_down'];
		foreach ($dropdown as $columns => $info) {
			switch ($info['id']) {
				case "dropdownapprove":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownapprove']);
					continue;
					break;
				case "dropdownmoderate":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownmoderate']);
					continue;
					break;
				case "dropdownspam":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownspam']);
					continue;
					break;
				case "dropdowndelete":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdowndelete']);
					continue;
					break;
				case "dropdowndelink":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdowndelink']);
					continue;
					break;
				case "dropdownmove":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownmove']);
					continue;
					break;
				case "dropdownemail":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownemail']);
					continue;
					break;
				case "dropdownblacklist":
					$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownblacklist']);
					continue;
					break;
			}
		}
		$options['drop_down'] = $dropdown;
		
		//Classic menu
		$classic = $options['classic'];
		foreach ($classic as $info => $value) {
			switch ($value['id']) {
				case "edit":
					$classic[$info] = aec_classic_condition($value,$_POST['edit']);
					continue;
					break;
				case "approve":
					$classic[$info] = aec_classic_condition($value,$_POST['approve']);
					continue;
					break;
				case "moderate":
					$classic[$info] = aec_classic_condition($value,$_POST['moderate']);
					continue;
					break;
				case "spam":
					$classic[$info] = aec_classic_condition($value,$_POST['spam']);
					continue;
					break;
				case "delete":
					$classic[$info] = aec_classic_condition($value,$_POST['delete']);
					continue;
					break;
				case "delink":
					$classic[$info] = aec_classic_condition($value,$_POST['delink']);
					continue;
					break;
				case "move":
					$classic[$info] = aec_classic_condition($value,$_POST['move']);
					continue;
					break;
				case "email":
					$classic[$info] = aec_classic_condition($value,$_POST['email']);
					continue;
					break;
				case "blacklist":
					$classic[$info] = aec_classic_condition($value,$_POST['blacklist']);
					continue;
					break;
			}
		}
		$options['classic'] = $classic;
		//Update user setings
		$author_options['comment_editing'] = $_POST['comment_editing'];
		$author_options['admin_editing'] = $_POST['admin_editing'];
		$updated = true;
	}
	if ($updated && !$error) {
		$aecomments->set_user_option( AECUtility::get_user_email() , $author_options );
		$aecomments->save_admin_options( $options );
	?>
<div class="updated"><p><strong><?php _e('Settings successfully updated.', 'ajaxEdit') ?></strong></p></div>
<?php
}
?>
<div class="wrap">
<form id="aecadminpanel" method="post" action="<?php echo esc_attr( $_SERVER["REQUEST_URI"] ); ?>">
<?php wp_nonce_field('wp-ajax-edit-comments_admin-options') ?>
<h2>Ajax Edit Comments</h2>
<p><?php _e("Your commentators have edited their comments ", 'ajaxEdit') ?><?php echo number_format(intval($options['number_edits'])); ?> <?php _e("times", 'ajaxEdit') ?>.</p>

<div class="wrap">

	<!-- the tabs -->
	<ul class="tabs" id="flowtabs">
		<li><a href="#1" id="t1"><?php _e('Behavior', 'ajaxEdit');?></a></li>
        <li><a href="#2" id="t2"  class=""><?php _e('Appearance', 'ajaxEdit');?></a></li>
        <li><a href="#3" id="t3" class=""><?php _e('Permissions', 'ajaxEdit');?></a></li>
        <li><a href="#4" id="t4" class=""><?php _e('Cleanup', 'ajaxEdit');?></a></li> 
	</ul>

	<!-- tab "panes" -->
	<div class="pane" style="display: block;">
		<!-- the tabs -->
<ul class="tabs">
	<li><a href="#1-1" class="current"><?php _e('General', 'ajaxEdit') ?></a></li>
	<li><a href="#1-2" class=""><?php _e('Advertising Options', 'ajaxEdit') ?></a></li>
	<li><a href="#1-3" class=""><?php _e('Anonymous Users', 'ajaxEdit') ?></a></li> 
    <li><a href="#1-4" class=""><?php _e('Registered Users', 'ajaxEdit') ?></a></li> 
    <li><a href="#1-5" class=""><?php _e('Other Features', 'ajaxEdit') ?></a></li> 
</ul>

<!-- tab "panes" -->
<div class="pane" style="display: block;">
	<table class="form-table">
	<tbody>
  	
  	<tr valign="top">
      <th scope="row"><?php _e('Set comment time (minutes):', 'ajaxEdit') ?></th>
      <td><input type="text" name="comment_time" value="<?php echo $options['minutes'] ?>" id="comment_time"/></td>
    </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Spam notification text:', 'ajaxEdit') ?></th>
    <td>
    <p><?php _e('Please limit to one line if possible since this text will show up when editing the comment or author (Tags allowed: em, a, strong, blockquote):', 'ajaxEdit') ?></p>
    <p><textarea cols="100" rows="3" name="spam_text" id="spam_text"><?php _e(stripslashes(apply_filters('comment_edit_save', $options['spam_text'])), 'ajaxEdit')?></textarea></p>
    </td>
  </tr>
 </tbody>
</table>
</div>
<div class="pane" style="display: none;">
<table class="form-table">
	<tbody>
    <tr valign="top">
  	<th scope="row"><?php _e('Advertising Text:', 'ajaxEdit') ?></th>
    <td>
    <p><?php _e('This text will show when an anonymous commenter sees the editing options.  WordPress shortcodes may be used here.', 'ajaxEdit' ); ?></p>
    <p><textarea cols="100" rows="3" name="affiliate_text" id="affiliate_text"><?php _e(stripslashes(apply_filters('comment_edit_save', $options['affiliate_text'])), 'ajaxEdit')?></textarea></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Advertising Options', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Enable comment advertising?', 'ajaxEdit') ?></strong></p><p><?php _e('Selecting "Yes" will show the "Advertising Text" above the editing options for each anonymous comment posted.', 'ajaxEdit') ?></p>
    <p><label for="affiliate_show_yes"><input type="radio" id="affiliate_show_yes" name="affiliate_show" value="true" <?php if ($options['affiliate_show'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="affiliate_show_no"><input type="radio" id="affiliate_show_no" name="affiliate_show" value="false" <?php if ($options['affiliate_show'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
</tbody>
</table>
</div>
<div class="pane" style="display: none;">
<table class="form-table">
	<tbody>
  <tr valign="top">
  	 <td>
    <p><strong><?php _e('Allow editing after additional comments have been posted?', 'ajaxEdit');?></strong></p><p><?php _e('Selecting "No" will prevent users from editing their comments if another comment has been made on a post.', 'ajaxEdit') ?></p>
    <p><label for="allow_editing_after_comment_yes"><input type="radio" id="allow_editing_after_comment_yes" name="allow_editing_after_comment" value="true" <?php if ($options['allow_editing_after_comment'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow_editing_after_comment_no"><input type="radio" id="allow_editing_after_comment_no" name="allow_editing_after_comment" value="false" <?php if ($options['allow_editing_after_comment'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
     <p><strong><?php _e('Allow Users to Edit Their Name?', 'ajaxEdit');?></strong></p><p><?php _e('Selecting "No" will turn off editing of Names', 'ajaxEdit') ?></p>
    <p><label for="allow_name_editing_yes"><input type="radio" id="allow_name_editing_yes" name="allow_name_editing" value="true" <?php if ($options['allow_name_editing'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow_name_editing_no"><input type="radio" id="allow_name_editing_no" name="allow_name_editing" value="false" <?php if ($options['allow_name_editing'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
     <p><strong><?php _e('Allow Users to Edit Their E-mail Addresses?', 'ajaxEdit');?></strong></p><p><?php _e('Selecting "No" will turn off editing of e-mail addresses.  One of the reasons you may want this on is for users with Avatars.', 'ajaxEdit') ?></p>
    <p><label for="allow_email_editing_yes"><input type="radio" id="allow_email_editing_yes" name="allow_email_editing" value="true" <?php if ($options['allow_email_editing'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow_email_editing_no"><input type="radio" id="allow_email_editing_no" name="allow_email_editing" value="false" <?php if ($options['allow_email_editing'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
     <p><strong><?php _e('Allow Users to Edit Their URLs?', 'ajaxEdit');?></strong></p><p><?php _e('Selecting "No" will turn off editing of URLs', 'ajaxEdit') ?></p>
    <p><label for="allow_url_editing_yes"><input type="radio" id="allow_url_editing_yes" name="allow_url_editing" value="true" <?php if ($options['allow_url_editing'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow_url_editing_no"><input type="radio" id="allow_url_editing_no" name="allow_url_editing" value="false" <?php if ($options['allow_url_editing'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    <p><strong><?php _e('Request Deletion Behavior', 'ajaxEdit');?></strong></p><p><?php _e('Determine if you want users to request deletion (comments are sent to moderation with admin notification) or delete comments (comments are deleted immediately with no admin notification).  Select Off if you would like to disable this feature.', 'ajaxEdit') ?></p>
    <p><label for="request_deletion_behavior_request"><input type="radio" id="request_deletion_behavior_request" name="request_deletion_behavior" value="request" <?php if ($options['request_deletion_behavior'] == "request") { echo('checked="checked"'); }?> /> <?php _e('Request Deletion','ajaxEdit'); ?></label><br /><label for="request_deletion_behavior_delete"><input type="radio" id="request_deletion_behavior_delete" name="request_deletion_behavior" value="delete" <?php if ($options['request_deletion_behavior'] == "delete") { echo('checked="checked"'); }?>/> <?php _e('Delete','ajaxEdit'); ?></label><br /><label for="request_deletion_behavior_none"><input type="radio" id="request_deletion_behavior_none" name="request_deletion_behavior" value="none" <?php if ($options['request_deletion_behavior'] == "none") { echo('checked="checked"'); }?>/> <?php _e('Off','ajaxEdit'); ?></label></p>
    </td>
  </tr>
 </tbody>
</table>
</div>
<div class="pane" style="display: none;">
<table class="form-table">
	<tbody>
  <tr valign="top">
    <td>
    <p><strong><?php _e('Allow Registered Users to Edit Comments Indefinitely?', 'ajaxEdit'); ?></strong></p>
    		<p><?php _e('Selecting "Yes" will allow users registered on your website to edit comments without a time limit.', 'ajaxEdit');?></p>
        <p><label for="registered_users_edit_yes"><input type="radio" id="registered_users_edit_yes" name="registered_users_edit" value="true" <?php if ($options['registered_users_edit'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="registered_users_edit_no"><input type="radio" id="registered_users_edit_no" name="registered_users_edit" value="false" <?php if ($options['registered_users_edit'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    <p><strong><?php _e('Allow Registered Users to Edit Their Name?', 'ajaxEdit'); ?></strong></p>
    		<p><?php _e('Selecting "Yes" will allow users registered on your website to edit their names.  This can prevent issues if a user wishes to impersonate others.', 'ajaxEdit');?></p>
        <p><label for="registered_users_name_edit_yes"><input type="radio" id="registered_users_name_edit_yes" name="registered_users_name_edit" value="true" <?php if ($options['registered_users_name_edit'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="registered_users_name_edit_no"><input type="radio" id="registered_users_name_edit_no" name="registered_users_name_edit" value="false" <?php if ($options['registered_users_name_edit'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
<p><strong><?php _e('Allow Registered Users to Edit Their E-mail Address?', 'ajaxEdit'); ?></strong></p>
    		<p><?php _e('Selecting "Yes" will allow users registered on your website to edit their e-mail address.', 'ajaxEdit');?></p>
        <p><label for="registered_users_email_edit_yes"><input type="radio" id="registered_users_email_edit_yes" name="registered_users_email_edit" value="true" <?php if ($options['registered_users_email_edit'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="registered_users_email_edit_no"><input type="radio" id="registered_users_email_edit_no" name="registered_users_email_edit" value="false" <?php if ($options['registered_users_email_edit'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
 <p><strong><?php _e('Allow Registered Users to Edit Their URL?', 'ajaxEdit'); ?></strong></p>
    		<p><?php _e('Selecting "Yes" will allow users registered on your website to edit their URL.', 'ajaxEdit');?></p>
        <p><label for="registered_users_url_edit_yes"><input type="radio" id="registered_users_url_edit_yes" name="registered_users_url_edit" value="true" <?php if ($options['registered_users_url_edit'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="registered_users_url_edit_no"><input type="radio" id="registered_users_url_edit_no" name="registered_users_url_edit" value="false" <?php if ($options['registered_users_url_edit'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
	</tr>  
 </tbody>
</table>
</div>

<div class="pane" style="display: none;">
<table class="form-table">
	<tbody>
  
  <tr valign="top">
  	<th scope="row"><?php _e('Edit E-mails', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Allow Edit E-mails?', 'ajaxEdit') ?></strong></p><p>  <?php _e('Selecting "Yes" will send you an email each time someone edits their comment.', 'ajaxEdit') ?></p>
    <p><label for="email_edits_yes"><input type="radio" id="email_edits_yes" name="email_edits" value="true" <?php if ($options['email_edits'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="email_edits_no"><input type="radio" id="email_edits_no" name="email_edits" value="false" <?php if ($options['email_edits'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Spam Protection','ajaxEdit'); ?></th>
    <td>
    <p><label for="wpAJAXAkismet"><input type="radio" id="wpAJAXAkismet" name="spam_protection" value="akismet" <?php if ($options['spam_protection'] == "akismet") { echo('checked="checked"'); }?> /> <?php _e('Akismet','ajaxEdit'); ?></label><br /><label for="wpAJAXDefensio"><input type="radio" id="wpAJAXDefensio" name="spam_protection" value="defensio" <?php if ($options['spam_protection'] == "defensio") { echo('checked="checked"'); }?>/> <?php _e('Defensio','ajaxEdit'); ?></label><br /><label for="wpAJAXNoSpam"><input type="radio" id="wpAJAXNoSpam" name="spam_protection" value="none" <?php if ($options['spam_protection'] == "none") { echo('checked="checked"'); }?>/> <?php _e('None','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Performance','ajaxEdit'); ?></th>
    <td>
    <p><strong><?php _e('Load Scripts in Footer? (Requires WordPress 2.8 or above and theme compatibility)', 'ajaxEdit') ?></strong></p>
    <p><label for="scripts_in_footer_yes"><input type="radio" id="scripts_in_footer_yes" name="scripts_in_footer" value="true" <?php if ($options['scripts_in_footer'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="scripts_in_footer_no"><input type="radio" id="scripts_in_footer_no" name="scripts_in_footer" value="false" <?php if ($options['scripts_in_footer'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
    <p><strong><?php _e('Use Compressed JavaScript Files?', 'ajaxEdit') ?></strong></p>
    <p><label for="compressed_scripts_yes"><input type="radio" id="compressed_scripts_yes" name="compressed_scripts" value="true" <?php if ($options['compressed_scripts'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="compressed_scripts_no"><input type="radio" id="compressed_scripts_no" name="compressed_scripts" value="false" <?php if ($options['compressed_scripts'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>     
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Character Encoding','ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Enable mb_convert_encoding?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('Some servers do not have this installed.  If you disable this option, be sure to test out various characters.  The mb_convert_encoding function is necessary to convert from UTF-8 to various charsets.', 'ajaxEdit') ?></p>
    <p><label for="use_mb_convert_yes"><input type="radio" id="use_mb_convert_yes" name="use_mb_convert" value="true" <?php if ($options['use_mb_convert'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="use_mb_convert_no"><input type="radio" id="use_mb_convert_no" name="use_mb_convert" value="false" <?php if ($options['use_mb_convert'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Spell Check','ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Select After the Deadline Language', 'ajaxEdit') ?></strong></p>
    <select name="atdlang">
	<option value="en" <?php if ($options['atdlang'] == "en") { echo('selected="selected"'); }?>>English</option>
    <option value="nl" <?php if ($options['atdlang'] == "nl") { echo('selected="selected"'); }?>>Dutch</option>
    <option value="fr" <?php if ($options['atdlang'] == "fr") { echo('selected="selected"'); }?>>French</option>
    <option value="de" <?php if ($options['atdlang'] == "de") { echo('selected="selected"'); }?>>German</option>
    <option value="id" <?php if ($options['atdlang'] == "id") { echo('selected="selected"'); }?>>Indonesian</option>
    <option value="it" <?php if ($options['atdlang'] == "it") { echo('selected="selected"'); }?>>Italian</option>
    <option value="pl" <?php if ($options['atdlang'] == "pl") { echo('selected="selected"'); }?>>Polish</option>
    <option value="pt" <?php if ($options['atdlang'] == "pt") { echo('selected="selected"'); }?>>Portuguese</option>
    <option value="es" <?php if ($options['atdlang'] == "es") { echo('selected="selected"'); }?>>Spanish</option>
    <option value="ru" <?php if ($options['atdlang'] == "ru") { echo('selected="selected"'); }?>>Russian</option>
</select>
    <p><strong><?php _e('Disable the After the Deadline Spellchecker on Posts?', 'ajaxEdit') ?></strong></p>
<p><label for="after_deadline_posts_no"><input type="radio" id="after_deadline_posts_no" name="after_deadline_posts" value="false" <?php if ($options['after_deadline_posts'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="after_deadline_posts_yes"><input type="radio" id="after_deadline_posts_yes" name="after_deadline_posts" value="true" <?php if ($options['after_deadline_posts'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
<p><strong><?php _e('Disable the After the Deadline Spellchecker on the Popups?', 'ajaxEdit') ?></strong></p>
<p><label for="after_deadline_popups_no"><input type="radio" id="after_deadline_popups_no" name="after_deadline_popups" value="false" <?php if ($options['after_deadline_popups'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="after_deadline_popups_yes"><input type="radio" id="after_deadline_popups_yes" name="after_deadline_popups" value="true" <?php if ($options['after_deadline_popups'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Expand Options','ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Disable the Expand Option on Posts?', 'ajaxEdit') ?></strong></p>
<p><label for="expand_posts_no"><input type="radio" id="expand_posts_no" name="expand_posts" value="false" <?php if ($options['expand_posts'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="expand_posts_yes"><input type="radio" id="expand_posts_yes" name="expand_posts" value="true" <?php if ($options['expand_posts'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
<p><strong><?php _e('Disable the Expand Option on the Popups?', 'ajaxEdit') ?></strong></p>
<p><label for="expand_popups_no"><input type="radio" id="expand_popups_no" name="expand_popups" value="false" <?php if ($options['expand_popups'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="expand_popups_yes"><input type="radio" id="expand_popups_yes" name="expand_popups" value="true" <?php if ($options['expand_popups'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Disable Trackbacks','ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Disable trackbacks from showing?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('Enabling this option will prevent trackbacks from showing up on your blog.  Please note that this option will not prevent your site from receiving trackbacks.', 'ajaxEdit') ?></p>
    <p><label for="disable_trackbacks_yes"><input type="radio" id="disable_trackbacks_yes" name="disable_trackbacks" value="true" <?php if ($options['disable_trackbacks'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="disable_trackbacks_no"><input type="radio" id="disable_trackbacks_no" name="disable_trackbacks" value="false" <?php if ($options['disable_trackbacks'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Disable No-follow','ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Disable no-follow in comment links?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('Enabling this option will remove no-follow from comment and author links.', 'ajaxEdit') ?></p>
    <p><label for="disable_nofollow_yes"><input type="radio" id="disable_nofollow_yes" name="disable_nofollow" value="true" <?php if ($options['disable_nofollow'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="disable_nofollow_no"><input type="radio" id="disable_nofollow_no" name="disable_nofollow" value="false" <?php if ($options['disable_nofollow'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Disable Self Pings','ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Disable self-pings to your site?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('Enabling this option will prevent your site from pinging itself.', 'ajaxEdit') ?></p>
    <p><label for="disable_selfpings_yes"><input type="radio" id="disable_selfpings_yes" name="disable_selfpings" value="true" <?php if ($options['disable_selfpings'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="disable_selfpings_no"><input type="radio" id="disable_selfpings_no" name="disable_selfpings" value="false" <?php if ($options['disable_selfpings'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('De-link Behavior','ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Choose how the de-link function behaves.', 'ajaxEdit') ?></strong></p>
    <p><?php _e('You can choose to remove links from the comment content and comment author URL, or just the comment author URL.', 'ajaxEdit') ?></p>
    <p><label for="delink_content_yes"><input type="radio" id="delink_content_yes" name="delink_content" value="true" <?php if ($options['delink_content'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Content and comment author URL.','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="delink_content_no"><input type="radio" id="delink_content_no" name="delink_content" value="false" <?php if ($options['delink_content'] == "false") { echo('checked="checked"'); }?>/> <?php _e('Comment author URL.','ajaxEdit'); ?></label></p>
    </td>
  </tr>
 </tbody>
</table>
</div>
	</div>
     <!--appearance-->
	<div class="pane" style="display: none;">
        <ul class="tabs">
            <li><a href="#2-1" class=""><?php _e('Display', 'ajaxEdit') ?></a></li>
            <li><a href="#2-3" class=""><?php _e('Icons', 'ajaxEdit') ?></a></li> 
            <li><a href="#2-4" class=""><?php _e('Colorbox', 'ajaxEdit') ?></a></li> 
        </ul>
        <!--appearance/display-->
        <div class="pane" style="display:none;">
        <table class="form-table">
	<tbody>
  <tr valign="top">
  	<th scope="row"><?php _e('Countdown Timer', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Show a Countdown Timer?', 'ajaxEdit') ?></strong></p><p><?php _e('Selecting "No" will turn off the countdown timer for non-admin commentators.', 'ajaxEdit') ?></p>
    <p><label for="show_timer_yes"><input type="radio" id="show_timer_yes" name="show_timer" value="true" <?php if ($options['show_timer'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="show_timer_no"><input type="radio" id="show_timer_no" name="show_timer" value="false" <?php if ($options['show_timer'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Pages', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Display on pages?', 'ajaxEdit') ?></strong></p><p><?php _e('Selecting "No" will turn off comment editing on pages.', 'ajaxEdit') ?></p>
    <p><label for="show_pages_yes"><input type="radio" id="show_pages_yes" name="show_pages" value="true" <?php if ($options['show_pages'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="show_pages_no"><input type="radio" id="show_pages_no" name="show_pages" value="false" <?php if ($options['show_pages'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  <th scope="row"><?php _e('Clearfix', 'ajaxEdit'); ?></th>
  <td>
  <p><strong><?php _e('Turn Off clearfix:after?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('The clearfix is enabled by default for maximum compatibility with themes.', 'ajaxEdit') ?></p>
<p><label for="clear_after_yes"><input type="radio" id="clear_after_yes" name="clear_after" value="false" <?php if ($options['clear_after'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="clear_after_no"><input type="radio" id="clear_after_no" name="clear_after" value="true" <?php if ($options['clear_after'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('JavaScript Scrolling', 'ajaxEdit'); ?></th>
<td>
<p><strong><?php _e('Turn Off Admin JavaScript Scrolling?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('The plugin tries to correct incorrect offsets on a post if you are admin.', 'ajaxEdit') ?></p>
<p><label for="javascript_scrolling_yes"><input type="radio" id="javascript_scrolling_yes" name="javascript_scrolling" value="false" <?php if ($options['javascript_scrolling'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="javascript_scrolling_no"><input type="radio" id="javascript_scrolling_no" name="javascript_scrolling" value="true" <?php if ($options['javascript_scrolling'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Edit Interface Location', 'ajaxEdit'); ?></th>
<td>
    <p><strong><?php _e('Comment Edit Interface On Bottom?', 'ajaxEdit') ?></strong></p>
<p><label for="comment_display_top_no"><input type="radio" id="comment_display_top_no" name="comment_display_top" value="false" <?php if ($options['comment_display_top'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="comment_display_top_yes"><input type="radio" id="comment_display_top_yes" name="comment_display_top" value="true" <?php if ($options['comment_display_top'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
</td>
</tr>
  </tbody>
  </table>
        </div>
        <!--/appearance/display-->        
<!--appearance/icons-->
<div class="pane" style="display: none;">
<table class="form-table">
	<tbody>
    <tr valign="top">
    <th scope="row"><?php _e('Icon Display', 'ajaxEdit') ?></th>
    <td>
    <p><?php _e('Select an option below to determine how the icons are displayed on your website.', 'ajaxEdit') ?></p>
<select name="icon_display">
	<option value="noicons" <?php if ($options['icon_display'] == "noicons") { echo('selected="selected"'); }?>><?php _e('Text Only','ajaxEdit');?></option>
  <option value="classic" <?php if ($options['icon_display'] == "classic") { echo('selected="selected"'); }?>><?php _e('Classic','ajaxEdit');?></option>
  <option value="dropdown" <?php if ($options['icon_display'] == "dropdown") { echo('selected="selected"'); }?>><?php _e('Dropdown','ajaxEdit');?></option>
  <option value="iconsonly" <?php if ($options['icon_display'] == "iconsonly") { echo('selected="selected"'); }?>><?php _e('Icons Only','ajaxEdit');?></option>
</select>
</td>
<tr valign="top">
<th scope="row"><?php _e('Icon Set', 'ajaxEdit') ?></th>
<td>
   <p><?php _e('Select an option below to display the icon set on your website.', 'ajaxEdit') ?></p>
    <?php 
		// Files in wp-content/plugins directory
		$path = $aecomments->get_plugin_dir( "/images/themes" );
		if ( is_dir( $path ) ) {
			$themedir = @ opendir($path);
			echo "<select name='icon_set'>";
			while (($file = readdir( $themedir ) ) !== false ) {
				
				if (is_dir($path.'/'.$file ) && substr_count($file, '.') == 0) {
					$selected = '';
					if ($file == $options['icon_set']) {
						$selected = "selected";
					}
					echo "<option value='$file' $selected>$file</option>";
				}
			}
			echo "</select>";
		} //end is_dir $path
		?>
        <div id="iconpreview"><img src='<?php echo $aecomments->get_plugin_url( "/images/themes/" . $options['icon_set'] . "/sprite.png" );?>' alt="Icon Preview" /><input type="hidden" name="iconpreviewurl" value="<?php echo $aecomments->get_plugin_url('/images/themes/');?>" /></div>
    </td></tr>
    <tr valign="top">
    <th scope="row"><?php _e('Icon Drop Down Menu', 'ajaxEdit') ?></th>
    <td>
    	<p><?php _e('Drag and drop between lists to adjust the icon order.  Click on the image to disable or enable the option.', 'ajaxEdit') ?></p>
        <table>
            <tr valign="top">
            <?php 
			//DROP DOWN STUFF
			//Sort the columns for the dropdown
			function aec_position_order($a, $b) {
				return strcmp($a['position'], $b['position']);
			}
			function aec_column_order($a, $b) {
				return strcmp($a['column'], $b['column']);
			}
			$lis = array();
			$dropdown = $options['drop_down'];
			//Create the array columns
			$columns = array('column0'=>array(),'column1'=>array(), 'column2'=> array());
			foreach ($dropdown as $items => $item) {
				switch ($item['column']) {
					case "0":
						$columns['column0'][sizeof($columns['column0'])] = $item;
						break;
					case "1":
						$columns['column1'][sizeof($columns['column1'])] = $item;
						break;
					case "2":
						$columns['column2'][sizeof($columns['column2'])] = $item;
						break;
				}	
			}
			$lis = array();
			foreach ($columns as $column) {
				usort($column, 'aec_position_order');
				//Build the LIs
				$li = '';
				foreach ($column as $info) {
					$li .= "<li class='sortable' id='" . $info['id'] . "'><span class='dropdown ";
					if ($info['enabled'] == '1') { $li .= "enabled"; } else { $li .= "disabled";}
					$li .= "' id='" . $info['id'] . "'></span>";
					$li .= __($info['text'], 'ajaxEdit');
					$li .= "<input type='hidden' name='" . $info['id'] . "' value='" . $info['column'] . "," . $info['position'] . "," . $info['enabled'] . "' />";
					$li .= "</li>";
				}
				$lis[sizeof($lis)] = $li;
			}
			//Output
			for ($i = 0; $i < sizeof($lis); $i++) {
				echo "<td id='sort$i'>";
				echo "<ul id='sort$i" . "ul' class='connectedSortable'>";
				echo $lis[$i];
				echo "</ul>";
				echo "</td>";
			}
			?>
            </tr>
        </table>
    </td>
    </tr>
    <tr valign="top">
    <th scope="row"><?php _e('Classic and Icons Only Vew', 'ajaxEdit') ?></th>
    	<td>
    	<p><?php _e('Drag and drop to adjust the icon order.  Click on the image to disable or enable the option.', 'ajaxEdit') ?></p>
        <table>
            <tr valign="top">
            <?php 
			$classic = $options['classic'];
			//Create the array columns
			$columns = array();
			foreach ($classic as $items => $item) {
				$columns[sizeof($columns)] = $item;
			}
			
			$items = '';
			usort($columns, 'aec_column_order');
			foreach ($columns as $column) {
				//Build the LIs
				$items .= "<li class='sortableclassic' id='" . $column['id'] . "'><span class='classic ";
				if ($column['enabled'] == '1') { $items .= "enabled"; } else { $items .= "disabled";}
				$items .= "' id='" . $column['id'] . "'></span>";
				$items .= __($column['text'], 'ajaxEdit');
				$items .= "<input type='hidden' name='" . $column['id'] . "' value='" . $column['column'] . "," . $column['enabled'] . "' />";
				$items .= "</li>";
			}
			echo "<td id='sortclassic'>";
			echo "<ul id='sortclassicul' >";
			echo $items;
			echo "</ul>";
			echo "</td>";
			?>
            </tr>
        </table>
 		</td>
    </tr>
    </tbody>
</table>
</div><!--/appearance/icons-->
<!--appearance/colorbox-->
<div class="pane" style="display: none;">
<p><?php printf( __( '%s is a lightbox script that is used for the various pop-ups within this plugin.', 'ajaxEdit' ), sprintf( "<a href='%s'>Colorbox</a>", esc_url( 'http://colorpowered.com/colorbox/' ) ) ); ?></p>
<table class="form-table">
	<tbody>
     <tr valign='top'>
   <th scope='row'><?php _e('Enable Colorbox on the front-end?', 'ajaxEdit'); ?></th>
   	<td>
    <p><?php printf( __('Disable this option if you would like to use another Colorbox WordPress plugin such as %s or %s.', 'ajaxEdit' ), sprintf( "<a href='%s'>jQuery Colorbox</a>", esc_url( 'http://wordpress.org/extend/plugins/jquery-colorbox/' ) ), sprintf( "<a href='%s'>Lightbox Plus</a>", esc_url( 'http://wordpress.org/extend/plugins/lightbox-plus/' ) ) );?></p>
    <p><label for="enable_colorbox_yes"><input type="radio" id="enable_colorbox_yes" name="enable_colorbox" value="true" <?php checked( $options[ 'enable_colorbox' ], 'true' ); ?>  /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enable_colorbox_no"><input type="radio" id="enable_colorbox_no" name="enable_colorbox" value="false" <?php checked( $options[ 'enable_colorbox' ], 'false' ); ?>  /> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
    </tr>
       <tr valign='top'>
   <th scope='row'><label for="colorbox_width"><?php _e('Set the Colorbox Width', 'ajaxEdit'); ?></label></th>
   	<td>
    <p><input type="text" size="30" value="<?php echo esc_attr( absint( $options['colorbox_width'] ) ); ?>" name="colorbox_width" id="colorbox_width" /></p>
    </td>
    </tr>
         <tr valign='top'>
   <th scope='row'><label for="colorbox_height"><?php _e('Set the Colorbox Height', 'ajaxEdit'); ?></label></th>
   	<td>
    <p><input type="text" size="30" value="<?php echo esc_attr( absint( $options['colorbox_height'] ) ); ?>" name="colorbox_height" id="colorbox_height" /></p>
    </td>
    </tr>
    
  </tbody>
</table>
</div><!--/appearance/colorbox-->
    
    </div><!--/appearance-->
	 <!--editing options-->
	<div class="pane" style="display: none;">
	<?php
$comment = $aecomments->get_user_option( 'comment_editing' );
$adminEdits = $aecomments->get_user_option( 'admin_editing' );
?>
<table class="form-table">
	<tbody>
     <tr valign='top'>
   <th scope='row'><?php _e('Registered Comment Editing', 'ajaxEdit'); ?></th>
   	<td>
    <p><strong><?php _e('Allow Registered Users to Edit Their Own Comments?', 'ajaxEdit');?></strong></p><p><?php _e('Selecting "No" will turn off comment editing for registered users.', 'ajaxEdit') ?></p>
    <p><label for="allow_registeredediting_yes"><input type="radio" id="allow_registeredediting_yes" name="allow_registeredediting" value="true" <?php if ($options['allow_registeredediting'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow_registeredediting_no"><input type="radio" id="allow_registeredediting_no" name="allow_registeredediting" value="false" <?php if ($options['allow_registeredediting'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
    </tr>
    <tr valign='top'>
   	<th scope='row'><?php _e("Anonymous Commenter Editing", 'ajaxEdit'); ?></th>
    <td>
    <p><strong><?php _e('Allow Anonymous Users to Edit Their Own Comments?', 'ajaxEdit');?></strong></p><p><?php _e('Selecting "No" will turn off comment editing for anonymous users.', 'ajaxEdit') ?></p>
    <p><label for="allow_editing_yes"><input type="radio" id="allow_editing_yes" name="allow_editing" value="true" <?php if ($options['allow_editing'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow_editing_no"><input type="radio" id="allow_editing_no" name="allow_editing" value="false" <?php if ($options['allow_editing'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Admin Users (Admin Panel)', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Turn Off Comment Editing in Admin Panel?', 'ajaxEdit') ?></strong></p>
<p><?php _e('Selecting "Yes" will disable comment editing in the Admin Comments Panel.', 'ajaxEdit') ?></p>
<p><label for="admin_editing_yes"><input type="radio" id="admin_editing_yes" name="admin_editing" value="true" <?php if ($adminEdits == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="admin_editing_no"><input type="radio" id="admin_editing_no" name="admin_editing" value="false" <?php if ($adminEdits == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Admin Users (Posts)', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Turn On Comment Editing?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('Selecting "Yes" will enable your ability to edit a user\'s comment.  Selecting "No" will disable your ability to edit comments on a post', 'ajaxEdit') ?></p>
<p><label for="comment_editing_yes"><input type="radio" id="comment_editing_yes" name="comment_editing" value="true" <?php if ($comment == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="comment_editing_no"><input type="radio" id="comment_editing_no" name="comment_editing" value="false" <?php if ($comment == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Editors', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Turn On Comment Editing?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('Selecting "Yes" will allow Editors to see the AEC edit options on a post or in the admin panel.', 'ajaxEdit') ?></p>
<p><label for="allow_editing_editors_yes"><input type="radio" id="allow_editing_editors_yes" name="allow_editing_editors" value="true" <?php if ($options['allow_editing_editors'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow_editing_editors_no"><input type="radio" id="allow_editing_editors_no" name="allow_editing_editors" value="false" <?php if ($options['allow_editing_editors'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
    </td>
  </tr>
  </tbody>
</table>
    
    </div><!--/editing options-->
	<!--cleanup-->
	<div class="pane" style="display: none;">
		<table class="form-table">
	<tbody>
  <tr valign="top">
  	<th scope="row"><?php _e('Delete all security keys', 'ajaxEdit') ?></th>
    <td>
    <p><?php _e("Each time a user leaves a comment, a security key is stored as a custom key.  Periodically you may want to delete this information.  Please backup your database first.", 'ajaxEdit') ?></p>
    <p><label for="security_keys_yes"><input type="radio" id="security_keys_yes" name="security_keys" value="true" /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="security_keys_no"><input type="radio" id="security_keys_no" name="security_keys" value="false" checked="checked"/> <?php _e('No', 'ajaxEdit') ?></label></p>
		</td>
	</tr>
  </tbody>
</table>  
	</div><!--/cleanup-->
<p class="submit">
  <input class='button-primary' type="submit" name="update" value="<?php _e('Update Settings', 'ajaxEdit') ?>" />
</p><!--/submit-->
</div>

