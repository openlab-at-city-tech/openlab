<style type="text/css">
<?php watupro_resp_table_css(600);?>
</style>
<div class="wrap">
		<h2 class="nav-tab-wrapper">
		<a class='nav-tab' href='admin.php?page=watupro_help&tab=main'><?php _e('Help / User Manual', 'watupro')?></a>
		<a class='nav-tab nav-tab-active' href='admin.php?page=watupro_help&tab=email_log'><?php _e('Raw Email Log', 'watupro')?></a>
	</h2>

	<div class="postbox wp-admin" style="padding:20px;">
		<form method="post">
			<p><label><?php _e('Log date:', 'watupro')?></label> <input type="text" name="date" class="datepicker" value="<?php echo $date?>">
			<?php _e('Filter by receiver email address:', 'watupro');?> <input type="text" name="receiver" value="<?php echo empty($_POST['receiver']) ? '' : $_POST['receiver']?>">
			<input type="submit" value="<?php _e('Show log', 'watupro')?>">
			<br>
			<?php _e('Automatically cleanup old logs after', 'watupro')?> <input type="text" size="4" name="cleanup_days" value="<?php echo $cleanup_raw_log?>"> <?php _e('days', 'watupro')?> <input type="submit" name="cleanup" value="<?php _e('Set Cleanup', 'watupro')?>"> </p>
		</form>		
		
		<?php if(!sizeof($emails)):?>
			<p><?php _e('No emails have been sent on the selected date.', 'watupro')?></p>
		<?php else:?>
			<table class="widefat watupro-table">
				<thead>
					<tr><th><?php _e('Time', 'watupro')?></th><th><?php _e('Sender', 'watupro')?></th><th><?php _e('Receiver', 'watupro')?></th>
	<th><?php _e('Subject', 'watupro')?></th><th><?php _e('Response from the mailing server', 'watupro')?></th>	</tr>
				</thead>
				<tbody>
				<?php foreach($emails as $email):
					$class = ('alternate' == @$class) ? '' : 'alternate';?>
					<tr class="<?php echo $class?>">
						<td><?php echo date('H:i', strtotime($email->datetime))?></td>
						<td><?php echo stripslashes($email->sender)?></td>
						<td><?php echo stripslashes($email->receiver)?></td>
						<td><?php echo stripslashes($email->subject)?></td>
						<td><?php echo $email->status?></td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
		<?php endif;?>
	</div>
</div>	

<script type="text/javascript" >
jQuery(function(){
	jQuery('.datepicker').datepicker({dateFormat: "yy-m-d"});
});
<?php watupro_resp_table_js();?>
</script>