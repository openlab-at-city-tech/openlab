<?php

/** Backup and Restore Module */

cp_module_register(__('Backup and Restore', 'cp') , 'backup' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('Backup and restore the points of your users. Exports points in JSON or CSV format.', 'cp'), 1);

if(cp_module_activated('backup')){

	add_action( 'wp_ajax_cp_module_backup_down', 'wp_ajax_cp_module_backup_down' );
	function wp_ajax_cp_module_backup_down() {
		
		if( ! current_user_can('manage_options') ){
			$response = json_encode( array() );
			echo $response;
			exit;
		}

		global $wpdb;
		$data = $wpdb->get_results('SELECT '.$wpdb->base_prefix.'users.id, '.$wpdb->base_prefix.'users.user_login, '.$wpdb->base_prefix.'users.user_email, '.$wpdb->base_prefix.'usermeta.meta_value 
			FROM `'.$wpdb->base_prefix.'users` 
			LEFT JOIN `'.$wpdb->base_prefix.'usermeta` ON '.$wpdb->base_prefix.'users.id = '.$wpdb->base_prefix.'usermeta.user_id 
			AND '.$wpdb->base_prefix.'usermeta.meta_key=\''.'cpoints'.'\''.$extraquery.' 
			ORDER BY '.$wpdb->base_prefix.'users.id ASC'
			. $limit . ';'
			,ARRAY_N);
			
		foreach($data as $x=>$y){
			$data[$x][3] = (int) $data[$x][3];
		}
		
		switch($_GET['fmt']){

			default:
			case 'json':	
				header( "Content-Type: application/json" );
				header('Content-Disposition: attachment; filename="'.__('backup', 'cp').'_'.time().'.json"');
				echo json_encode($data);
				exit;
				break;
				
			case 'csv':
				header( "Content-Type: application/csv" );
				header('Content-Disposition: attachment; filename="'.__('backup', 'cp').'_'.time().'.csv"');
				foreach($data as $x=>$y){
					$data[$x] = implode(",",$y);
				}
				echo implode("\n", $data);
				break;

		}

		exit;
		
	}

	function cp_module_backup_data_add_admin_page(){
		add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('Backup and Restore','cp'), __('Backup &amp; Restore','cp'), 'manage_options', 'cp_modules_backup_admin', 'cp_modules_backup_admin');
	}
	add_action('cp_admin_pages','cp_module_backup_data_add_admin_page');

	function cp_modules_backup_admin(){

	// handles form submissions
	if ($_POST['cp_module_backup_down_form_submit'] == 'Y') {
		update_option('cp_module_backup_lastbackup', time());
		echo '<div class="updated"><p><strong>'.__('Your backup has been generated','cp').'...</strong></p></div>';
		echo '<script type="text/javascript">jQuery(document).ready(function() { location.href="'. get_bloginfo('url').'/wp-admin/admin-ajax.php?action=cp_module_backup_down&fmt='.$_POST['cp_module_backup_down_form_format'].'"; });</script>';
	}
	if ($_POST['cp_module_backup_up_form_submit'] == 'Y') {
		switch($_FILES['cp_module_backup_up_form_upload']['error']){
			case 0:
				$handle = fopen($_FILES['cp_module_backup_up_form_upload']['tmp_name'], "r");
				$data = fread($handle, filesize($_FILES['cp_module_backup_up_form_upload']['tmp_name']));
				fclose($handle);
				// try json
				$json = json_decode($data);
				if($json!=null){
					$data = $json;
				}
				else{
					// try csv
					$lines = explode("\n",str_replace("\r","",$data));
					foreach($lines as $n=>$line){
						$csv[] = explode(",", $line);
					}
					$data = $csv;
				}
				$datap = array();
				foreach($data as $d){
					if( is_numeric($d[0]) && validate_username($d[1]) && is_email($d[2]) && is_numeric($d[3]) && $d[3]>=0 ){
						$datap[] = array($d[0],$d[1],$d[2],$d[3]);
					}
				}
				if(count($datap)>0){
					// valid data
					$users_matched = 0;
					$users_updated = 0;
					foreach($datap as $d){
						switch($_POST['cp_module_backup_up_form_match']){
							default:
							case 'id':
								$u = get_user_by('id', $d[0]);
								break;

							case 'login':
								$u = get_user_by('login', $d[1]);
								break;

							case 'email':
								$u = get_user_by('email', $d[2]);
								break;
						}
						if($u){
							$uid = $u->ID;
							$curr_points = $u->cpoints;
							if((int)$curr_points != $d[3]){
								cp_updatePoints($uid, $d[3]);
								$users_updated++;
							}
							$users_matched++;
						}
					}
					echo '<div class="updated"><p><strong>'.__('The backup file has been restored!','cp').'</strong>';
					echo '<div style="font-size:11px;">';
					$users = count_users();
					echo '<strong>'.__('Backup file','cp').':</strong> '. basename($_FILES['cp_module_backup_up_form_upload']['name']);
					echo '<br /><strong>'.__('Total users in blog','cp').':</strong> '. $users['total_users'];
					echo '<br /><strong>'.__('Users in backup file','cp').':</strong> '.count($datap);
					echo '<br /><strong>'.__('Users matched','cp').':</strong> '.$users_matched;
					echo '<br /><strong>'.__('Users altered','cp').':</strong> '.$users_updated;
					echo '</div>';
					echo '</p></div>';
					update_option('cp_module_backup_lastrestore', time());
				}
				else{
					echo '<div class="error"><p><strong>'.__('The file you have uploaded is not a valid backup file','cp').'...</strong></p></div>';
				}
				break;
			case 1:
			case 2:
				echo '<div class="error"><p><strong>'.__('The file you uploaded exceeds the maximum file size allowed','cp').'...</strong></p></div>';
				break;
			case 4:
				echo '<div class="error"><p><strong>'.__('Please select a file to restore','cp').'...</strong></p></div>';
				break;
			default:
				echo '<div class="error"><p><strong>'.__('An error occured while uploading the backup file','cp').'...</strong></p></div>';
				break;
		}
		
	}
		
	?>
	
	<div class="wrap">
		<h2>CubePoints - <?php _e('Backup &amp; Restore', 'cp'); ?></h2>
		

		<h3><?php _e('Backup Points','cp'); ?></h3>		

		<form name="cp_module_backup_down_form" method="post">
			<input type="hidden" name="cp_module_backup_down_form_submit" value="Y" />
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Last backup'); ?>:</label></th>
					<td valign="middle"><?php echo get_option('cp_module_backup_lastbackup')==null?'<i>('.__('none', 'cp').')</i>':date_i18n("j F Y, h:i A", get_option('cp_module_backup_lastbackup') + get_option( 'gmt_offset' ) * 3600); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Number of users'); ?>:</label></th>
					<td valign="middle"><?php $users = count_users(); echo $users['total_users']; ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cp_module_backup_down_form_format"><?php _e('Format', 'cp'); ?>:</label></th>
					<td valign="middle">
						<select id="cp_module_backup_down_form_format" name="cp_module_backup_down_form_format" style="width:200px;">
							<option value="json"<?php echo $_POST['cp_module_backup_down_form_format']=='json'?' selected':''; ?>>JSON</option>
							<option value="csv"<?php echo $_POST['cp_module_backup_down_form_format']=='csv'?' selected':''; ?>>CSV</option>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Download Backup','cp'); ?> &raquo;" />
			</p>

		</form>

		<h3><?php _e('Restore Backup','cp'); ?></h3>		

		<form name="cp_module_backup_up_form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="cp_module_backup_up_form_submit" value="Y" />
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Last restore'); ?>:</label></th>
					<td valign="middle"><?php echo get_option('cp_module_backup_lastrestore')==null?'<i>('.__('none', 'cp').')</i>':date_i18n("j F Y, h:i A", get_option('cp_module_backup_lastrestore') + get_option( 'gmt_offset' ) * 3600); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cp_module_backup_up_form_upload"><?php _e('Upload', 'cp'); ?>:</label></th>
					<td valign="middle">
						<input type="file" id="cp_module_backup_up_form_upload" name="cp_module_backup_up_form_upload" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cp_module_backup_up_form_match"><?php _e('Match', 'cp'); ?>:</label></th>
					<td valign="middle">
						<select id="cp_module_backup_up_form_match" name="cp_module_backup_up_form_match" style="width:200px;">
							<option value="id"<?php echo $_POST['cp_module_backup_up_form_match']=='id'?' selected':''; ?>><?php _e('User ID', 'cp'); ?></option>
							<option value="login"<?php echo $_POST['cp_module_backup_up_form_match']=='login'?' selected':''; ?>><?php _e('Username', 'cp'); ?></option>
							<option value="email"<?php echo $_POST['cp_module_backup_up_form_match']=='email'?' selected':''; ?>><?php _e('Email Address', 'cp'); ?></option>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Restore from Backup','cp'); ?> &raquo;" />
			</p>

		</form>
		
	</div>
	<?php
	}
	
}
?>