<?php
/**
 * CubePoints admin page: configure
 */

function cp_admin_config()
{
	// handles form submissions
 	if ( isset($_POST['cp_admin_form_submit']) && $_POST['cp_admin_form_submit'] == 'Y' ) {
		$cp_topfilter = explode(',',str_replace(array("\n","\r"),'',$_POST['cp_topfilter']));
		if(cp_topfilter==''){
			$cp_topfilter=array();
		}
		else{
			foreach($cp_topfilter as $x=>$y){
				$cp_topfilter[$x]=trim($y);
			}
			$cp_topfilter=array_unique($cp_topfilter);
			$cp_topfilter=array_filter($cp_topfilter, 'strlen');
		}
		$cp_comment_points = (int)$_POST['cp_comment_points'];
		$cp_del_comment_points = (int)$_POST['cp_del_comment_points'];
		$cp_post_points = (int)$_POST['cp_post_points'];
		$cp_reg_points = (int)$_POST['cp_reg_points'];
		$cp_prefix = $_POST['cp_prefix'];
		$cp_suffix = $_POST['cp_suffix'];
		$cp_about_posts = (bool)$_POST['cp_about_posts'];
		$cp_about_comments = (bool)$_POST['cp_about_comments'];
		$cp_donation = (bool)$_POST['cp_donation'];
		update_option('cp_comment_points', $cp_comment_points);
		update_option('cp_del_comment_points', $cp_del_comment_points);
		update_option('cp_post_points', $cp_post_points);
		update_option('cp_reg_points', $cp_reg_points);
		update_option('cp_prefix', $cp_prefix);
		update_option('cp_suffix', $cp_suffix);
		update_option('cp_donation', $cp_donation);
		update_option('cp_topfilter', $cp_topfilter);
		
		// hook for modules to process submitted data
		do_action('cp_config_process');

		echo '<div class="updated"><p><strong>'.__('Settings Updated','cp').'</strong></p></div>';
  	}
  	
	// prepares data for use in form
	if(count(get_option('cp_topfilter'))>0){
		$cp_topfilter_text = implode(", ",(array)get_option('cp_topfilter'));
	} else {
		$cp_topfilter_text = '';
	}
	if (get_option('cp_donation')) {
		  $cp_donation_checked = " checked='checked'";
	} else {
		  $cp_donation_checked = "";
	}
	if (get_option('cp_mypoints')) {
		$cp_mypoints_checked = " checked='checked'";
	} else {
		$cp_mypoints_checked = "";
	}
	
?>

	<div class="wrap">
		<h2>CubePoints - <?php _e('Configure', 'cp'); ?></h2>
		<?php _e('Configure CubePoints to your liking!', 'cp'); ?><br /><br />
	
		<form name="cp_admin_form" method="post">
			<input type="hidden" name="cp_admin_form_submit" value="Y" />

		<h3><?php _e('General Settings','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_prefix"><?php _e('Prefix for display of points', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_prefix" name="cp_prefix" value="<?php echo get_option('cp_prefix'); ?>" size="30" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cp_suffix"><?php _e('Suffix for display of points', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_suffix" name="cp_suffix" value="<?php echo get_option('cp_suffix'); ?>" size="30" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label title="<?php _e('Separate usernames using &quot;,&quot;', 'cp'); ?>" for="cp_topfilter"><?php _e('Hide the following users from list of top users','cp'); ?>:</label></th>
				<td valign="middle"><textarea id="cp_topfilter" name="cp_topfilter"><?php echo $cp_topfilter_text; ?></textarea></td>
			</tr>
			<?php do_action('cp_config_form_general'); ?>
		</table>
		<br />
		<h3><?php _e('Point Settings','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_comment_points"><?php _e('Points for each comment', 'cp'); ?>:</label>
				</th>
				<td valign="middle" width="190"><input type="text" id="cp_comment_points" name="cp_comment_points" value="<?php echo get_option('cp_comment_points'); ?>" size="30" /></td>
				<td><input type="button" onclick="document.getElementById('cp_comment_points').value='0'" value="<?php _e('Do not add points for comments', 'cp'); ?>" class="button" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cp_comment_points"><?php _e('Points subtracted for each comment deleted','cp'); ?>:</label>
				</th>
				<td valign="middle"><input type="text" id="cp_del_comment_points" name="cp_del_comment_points" value="<?php echo get_option('cp_del_comment_points'); ?>" size="30" /></td>
				<td><input type="button" onclick="document.getElementById('cp_del_comment_points').value='0'" value="<?php _e('Do not subtract points on comment deletion','cp'); ?>" class="button" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cp_comment_points"><?php _e('Points for each post','cp'); ?>:</label>
				</th>
				<td valign="middle"><input type="text" id="cp_post_points" name="cp_post_points" value="<?php echo get_option('cp_post_points'); ?>" size="30" /></td>
				<td><input type="button" onclick="document.getElementById('cp_post_points').value='0'" value="<?php _e('Do not add points for new posts','cp'); ?>" class="button" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cp_comment_points"><?php _e('Points for new members','cp'); ?>:</label>
				</th>
				<td valign="middle"><input type="text" id="cp_reg_points" name="cp_reg_points" value="<?php echo get_option('cp_reg_points'); ?>" size="30" /></td>
				<td><input type="button" onclick="document.getElementById('cp_reg_points').value='0'" value="<?php _e('Do not add points for new registrations','cp'); ?>" class="button" /></td>
			</tr>
			<?php do_action('cp_config_form_points'); ?>
		</table>
		
		<?php do_action('cp_config_form'); ?>
		
		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options','cp'); ?>" />
		</p>
	
	</form>
	
	</div>

	<?php do_action('cp_admin_config'); ?>
	
<?php
}
?>