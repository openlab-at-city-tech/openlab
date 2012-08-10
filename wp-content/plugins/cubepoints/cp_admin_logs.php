<?php
/**
 * CubePoints admin page: logs
 */

function cp_admin_logs()
{
?>

	<div class="wrap">
		<h2>CubePoints - <?php _e('Logs', 'cp'); ?></h2>
		<?php _e('View recent point transactions.', 'cp'); ?><br /><br />
		<?php cp_show_logs('all', apply_filters('cp_admin_logs_limit', 0 ) , true); ?>
	</div>
	
	<?php do_action('cp_admin_logs'); ?>
	
	<?php
}
?>