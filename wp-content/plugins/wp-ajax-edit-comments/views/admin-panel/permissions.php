<?php 
global $wpdb,$aecomments, $user_email;
if ( !is_a( $aecomments, 'WPrapAjaxEditComments' ) && !current_user_can( 'administrator' ) ) 
	die('');

$options = $aecomments->get_all_admin_options(); //global settings

//Update settings
$updated = false;
if (isset($_POST['update'])) { 
	 check_admin_referer('wp-ajax-edit-comments_admin-options');
	$error = false;
	
	//Update global settings
	$options[ 'allow_registeredediting' ] = $_POST[ 'allow_registeredediting' ];
	$options[ 'allow_editing' ] = $_POST[ 'allow_editing' ];
	$options[ 'allow_editing_editors' ] = $_POST[ 'allow_editing_editors' ];
	
	
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
<h2>Ajax Edit Comments - <?php _e('Permissions', 'ajaxEdit');?></h2>
<p><?php _e("Your commentators have edited their comments ", 'ajaxEdit') ?><?php echo number_format(intval($options['number_edits'])); ?> <?php _e("times", 'ajaxEdit') ?>.</p>

<div class="wrap">

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
<p class="submit">
  <input class='button-primary' type="submit" name="update" value="<?php _e('Update Settings', 'ajaxEdit') ?>" />
</p><!--/submit-->
</form>
</div>

