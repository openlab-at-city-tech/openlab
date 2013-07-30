<?php 
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
?>
<div class="wrap">
<form id="aecadminpanel" method="post" action="<?php echo esc_attr( $_SERVER["REQUEST_URI"] ); ?>">
<?php wp_nonce_field('wp-ajax-edit-comments_admin-options') ?>
<h2>Ajax Edit Comments - <?php _e('Cleanup', 'ajaxEdit');?></h2>
<p><?php _e("Your commentators have edited their comments ", 'ajaxEdit') ?><?php echo number_format(intval($aecomments->get_admin_option( 'number_edits' ))); ?> <?php _e("times", 'ajaxEdit') ?>.</p>

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
<p class="submit">
  <input class='button-primary' type="submit" name="update" value="<?php _e('Update Settings', 'ajaxEdit') ?>" />
</p><!--/submit-->
</form>
</div>

