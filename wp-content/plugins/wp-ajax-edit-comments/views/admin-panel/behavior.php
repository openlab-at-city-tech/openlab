<?php 
global $wpdb,$aecomments, $user_email;
if ( !is_a( $aecomments, 'WPrapAjaxEditComments' ) && !current_user_can( 'administrator' ) ) 
	die('');

$options = $aecomments->get_all_admin_options(); //global settings
$all_post_types = $aecomments->get_all_post_types(); //post types array

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
	$options['allow_editing_after_comment'] = $_POST['allow_editing_after_comment'];
	$options['spam_text'] = apply_filters('pre_comment_content',apply_filters('comment_save_pre', $_POST['spam_text']));
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
	$options['affiliate_text'] = apply_filters('pre_comment_content',apply_filters('comment_save_pre', $_POST['affiliate_text']));
	$options['affiliate_show'] = $_POST['affiliate_show'];
	$options['scripts_in_footer'] = $_POST['scripts_in_footer'];
	$options['scripts_on_archive'] = $_POST['scripts_on_archive'];
	$options['allowed_archives'] = $_POST['allowed_archives'];
	$options['compressed_scripts'] = $_POST['compressed_scripts'];
	$options['after_deadline_posts'] = $_POST['after_deadline_posts'];
	$options['after_deadline_popups'] = $_POST['after_deadline_popups'];
	$options['disable_trackbacks'] = $_POST['disable_trackbacks'];
	$options['disable_nofollow'] = $_POST['disable_nofollow'];
	$options['disable_selfpings'] = $_POST['disable_selfpings'];
	$options['delink_content'] = $_POST['delink_content'];
	$options['expand_popups'] = $_POST['expand_popups'];
	$options['expand_posts'] = $_POST['expand_posts'];
	$options['atdlang'] = $_POST['atdlang'];
	$options['request_deletion_behavior'] = $_POST['request_deletion_behavior'];
	$updated = true;
}
if ($updated && !$error) {
	$aecomments->save_admin_options( $options );
	?>
<div class="updated"><p><strong><?php _e('Settings successfully updated.', 'ajaxEdit') ?></strong></p></div>
<?php
}
?>
<div class="wrap">
<form id="aecadminpanel" method="post" action="<?php echo esc_attr( $_SERVER["REQUEST_URI"] ); ?>">
<?php wp_nonce_field('wp-ajax-edit-comments_admin-options') ?>
<h2>Ajax Edit Comments - <?php _e('Behavior', 'ajaxEdit');?></h2>
<p><?php _e("Your commentators have edited their comments ", 'ajaxEdit') ?><?php echo number_format(intval($options['number_edits'])); ?> <?php _e("times", 'ajaxEdit') ?>.</p>

<h3><?php _e('General', 'ajaxEdit') ?></h3>

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

<h3><?php _e('Advertising Options', 'ajaxEdit') ?></h3>

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

<h3><?php _e('Anonymous Users', 'ajaxEdit') ?></h3>

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

<h3><?php _e('Registered Users', 'ajaxEdit') ?></h3>

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

<h3><?php _e('Other Features', 'ajaxEdit') ?></h3>

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
    <p><label for="scripts_in_footer_yes"><input type="radio" id="scripts_in_footer_yes" name="scripts_in_footer" value="true" <?php if ($options['scripts_in_footer'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="scripts_in_footer_no"><input type="radio" id="scripts_in_footer_no" name="scripts_in_footer" value="false" <?php if ($options['scripts_in_footer'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label>
    </p>
    <p><strong><?php _e('Use Compressed JavaScript Files?', 'ajaxEdit') ?></strong></p>
    <p><label for="compressed_scripts_yes"><input type="radio" id="compressed_scripts_yes" name="compressed_scripts" value="true" <?php if ($options['compressed_scripts'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="compressed_scripts_no"><input type="radio" id="compressed_scripts_no" name="compressed_scripts" value="false" <?php if ($options['compressed_scripts'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>     
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Archives','ajaxEdit'); ?></th>
    <td>
    <p><strong><?php _e('Load Scripts on archives?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('This option should only be used if you have commenting enabled in your theme for archive pages', 'ajaxEdit') ?></p>
    <p><label for="scripts_on_archive_yes"><input type="radio" id="scripts_on_archive_yes" name="scripts_on_archive" value="true" <?php if ($options['scripts_on_archive'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="scripts_on_archive_no"><input type="radio" id="scripts_on_archive_no" name="scripts_on_archive" value="false" <?php if ($options['scripts_on_archive'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label>
    </p>
    <p><strong><?php _e('Select post type archives?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('This option will only take effect if "Load on archives?" is enabled. (Hold ctrl to select multiple)', 'ajaxEdit') ?></p>
    <select name="allowed_archives[]" multiple="true">
        <?php foreach ($all_post_types as $post_type) : ?>
            <option value="<?php echo $post_type; ?>" <?php if (in_array($post_type, $options['allowed_archives'])) { echo('selected="selected"'); }?>><?php echo ucfirst($post_type); ?></option>
        <?php endforeach; ?>
    </select>
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

<p class="submit">
  <input class='button-primary' type="submit" name="update" value="<?php _e('Update Settings', 'ajaxEdit') ?>" />
</p><!--/submit-->
</form>
</div><!-- .wrap -->

