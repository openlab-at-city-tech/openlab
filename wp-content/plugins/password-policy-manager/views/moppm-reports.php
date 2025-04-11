<?php
/**
 * File to display the reports.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_print_scripts( 'moppm_admin_datatable_script' );
?>
<div> 
<div class="moppm_inactive">
<div id="main_class" style="display: flex;">
<div id="moppm_report" class="moppm_main_table">
				<table style="width:100%">
					<tbody>
						<tr class="moppm-header">
							<td class="moppm_1click_text1"><?php esc_html_e( ' Users Login Report', 'password-policy-manager' ); ?></td>
							<td><input type="button" value="Clear All"  id="moppm_clear_all" class="button button-primary button-large" style="width: 90px;"></td>
						</tr>
						<tr>
							<td class="moppm_enable_disable_report"> 
								<label><?php esc_html_e( 'Enable Report Entry', 'password-policy-manager' ); ?></label>  
								<label class="moppm_switch" >   
									<input type="checkbox"  id="moppm_enable_disable_report" name="moppm_enable_disable_report">    
									<span class="moppm_switch_slider moppm_switch_round"></span>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
				<hr>
				<table id="moppm_users" class="display" style="width:100%">
				<thead><tr class='moppm_not_bold'><th>User ID</th><th>User Email</th><th>Last Log in Time</th><th>Last Password Change</th><th>Action</th></tr></thead>
				<tbody>
		<?php
				global $wpdb;
				global $moppm_db_queries;
				$result = $moppm_db_queries->moppm_get_report_list();
				global $results;
				$disabled = '';
		foreach ( $result as $results ) {
			echo "<tr class='moppm_not_bold' id =" . esc_attr( $results->id ) . '><td>' . esc_html( $results->id ) . '</td><td>' . esc_html( $results->user_email ) . '</td><td>' . esc_html( $results->Login_time ) . '</td><td>' . esc_html( $results->Logout_time ) . '</td><td>  <a style="cursor:pointer;" onclick=removefromlist(' . esc_js( $results->id ) . ')>Remove</a></td></tr>'; //phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Object property name coming from database , so can't change it to snakecase format
		}
		?>
					</tbody>
					</table>
					<script type="text/javascript">
						jQuery("#moppm_users").DataTable({
						"order": [[ 3, "desc" ]]
						});
					</script>
			</div>	
	</div>
	</div>

<div id="moppm_inactive_user" class="moppm_inactive">
				<table style="width:100%">
				<tbody>
				<tr class="moppm-header">
					<td class="moppm_1click_text1">Inactive Users Report <?php echo '  <a href="' . esc_url_raw( $upgrade_url ) . '" style="color: red;font-size:14px;text-decoration: none !important;">'; ?>[ UPGRADE ]</a></td>
					<td ><input type="button" value="Remove All"  id="moppm_clear_all_inactive" class="button button-primary button-large"></td>
				</tr>
				</tbody>
				</table>
				<span class="moppm_premium_instruction" id="moppm_report_error"></span>
				<table id="moppm_inactive_users" class="display" style="width:100%">
				<thead><tr><th>User ID&emsp;&emsp;</th><th>User Email&emsp;&emsp;</th><th>Status&emsp;&emsp;</th><th>Action&emsp;&emsp;</th></tr></thead>
				<tbody>
			<?php
				$meta_key = 'moppm_inactive_user_is_block';
				$users    = get_users();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					if ( get_user_meta( $user->ID, $meta_key ) ) {
						echo "<tr class='moppm_not_bold' id =" . esc_attr( $user->ID ) . '><td>' . esc_html( $user->ID ) . '</td><td>' . esc_html( $user->user_email ) . '</td><td>Locked</td><td>  <a onclick=removefrominactivelist(' . esc_js( $user->ID ) . ')>Remove</a></td></tr>';
					}
				}
			}
			?>
				</tbody>
				</table>
				<script type="text/javascript">
						jQuery("#moppm_inactive_users").DataTable({
						"order": [[ 3, "desc" ]]
						});
					</script>
			</div>  
<script type="text/javascript">

jQuery("#moppm_clear_all_inactive").click(function(e){   
Moppm_error_msg("This feature is available in premium plugins.");
});
function removefromlist(id){
	var nonce = '<?php echo esc_js( wp_create_nonce( 'moppm_remove_Nonce' ) ); ?>';
	user_value = id;
	if(user_value != '')
	{
		var data = {
		'action'					: 'moppm_ajax',
		'option' 					: 'moppm_report_remove', 
		'user_value'				:  user_value,
		'nonce'						:  nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
				var response = response.replace(/\s+/g,' ').trim();
				if(response == 'UNKNOWN_ERROR')
				{
			Moppm_error_msg(" Unknown Error occured while removing the user.");
				}
				else
				{
			Moppm_success_msg("User detail is removed from list successfully.");
			jQuery('#'+id).hide();      
				}
		});
	}
}

jQuery("#moppm_clear_all").click(function()
		{
			jQuery("#moppm_clear_all").attr('disabled','disabled');
			var nonce = '<?php echo esc_js( wp_create_nonce( 'moppm_clear_nonce' ) ); ?>'; 
					var data = {
								'action'                            :  'moppm_ajax',
								'option'                            :  'moppm_clear_button',
								'nonce'                             :   nonce
							};
						jQuery.post(ajaxurl, data, function(response) 
						{
							jQuery("#moppm_clear_all").removeAttr('disabled');
							var response = response.replace(/\s+/g,' ').trim(); 
							if(response == 'ERROR')
								Moppm_error_msg('Please click again.');
							else{
								Moppm_success_msg('Your report list is clear.');
								window.location.reload(); 
							}

						});
});
function moppmrefreshListTable(html)
{
	jQuery('#moppm_users').html(html);
}
</script>
<script>
var moppm_enable_disable_report = "<?php echo esc_js( get_site_option( 'moppm_enable_disable_report' ) ); ?>";
			if(moppm_enable_disable_report == 'on')
				{
					jQuery('#moppm_enable_disable_report').prop("checked",true);   
				}
				else
				{
					jQuery('#moppm_enable_disable_report').prop("checked",false);
				}
jQuery("#moppm_enable_disable_report").click(function()
{
	var moppm_enable_disable_report = jQuery("input[name='moppm_enable_disable_report']:checked").val();

	var nonce = '<?php echo esc_js( wp_create_nonce( 'moppm_enable_disable_report' ) ); ?>'; 
					var data = {
								'action'                            :  'moppm_ajax',
								'option'                            :  'moppm_enable_disable_report',
								'moppm_enable_disable_report'       :  moppm_enable_disable_report,
								'nonce'                             :   nonce
							};
							jQuery.post(ajaxurl, data, function(response) 
						{
							var response = response.replace(/\s+/g,' ').trim();
							if (response == "true"){
								Moppm_success_msg("Your login report is now enabled.");
							}
							else{
									Moppm_error_msg("Your login report is now disabled.");
								}
						});
});
</script>
