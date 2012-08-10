<?php
/**
 * CubePoints logs display
 */

	
 function cp_show_logs($type='all', $limit=10, $datatables=true){

	global $wpdb;
  
  $q      = '';
  $limitq = '';  
  
	if($type!='all'){
		$uid = (int) $type;
		$q = ' WHERE `uid` = ' . $uid . ' ';
	}
	if($limit>0){
		$limitq = 'LIMIT '.(int) $limit;
	}
	$results = $wpdb->get_results( apply_filters('cp_logs_dbquery', 'SELECT * FROM `'.CP_DB.'` '.$q.'ORDER BY timestamp DESC ') . $limitq);
?>
	
	<table id="cp_logs_table" class="widefat<?php echo $datatables?' datatables':''; ?>">
		<thead><tr><th scope="col"><?php _e('User','cp'); ?></th><th scope="col"><?php _e('Points','cp'); ?></th><th scope="col"><?php _e('Description','cp'); ?></th><th scope="col"><?php _e('Time','cp'); ?></th></tr></thead>
		<tfoot><tr><th scope="col"><?php _e('User','cp'); ?></th><th scope="col"><?php _e('Points','cp'); ?></th><th scope="col"><?php _e('Description','cp'); ?></th><th scope="col"><?php _e('Time','cp'); ?></th></tr></tfoot>
	
		<?php
		foreach($results as $result){
			$user = get_userdata($result->uid);
			$username = $user->user_login;
			$user_nicename = $user->display_name;
		?>
			<tr>
				<td title="<?php echo $user_nicename ?>"><?php echo $username; ?></td>
				<td><?php echo $result->points; ?></td>
				<td><?php do_action('cp_logs_description', $result->type, $result->uid, $result->points, $result->data); ?></td>
				<td title="<?php echo date('Y-m-d H:i:s', $result->timestamp); ?>"><?php echo cp_relativeTime($result->timestamp); ?></td>
			</tr>
		<?php
		}
		?>
	</table>
<?php
	if($datatables){
?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#cp_logs_table').dataTable( {
			"bStateSave": false,
			"bSort": false,
			"aoColumns": [  {},{},{},{ "bSearchable": false } ]
			} );
	} );
	</script>
<?php
	}
}
?>