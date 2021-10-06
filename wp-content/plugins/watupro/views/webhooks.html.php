<div class="wrap">
	<h1><?php _e('Manage Webhooks', 'watupro');?></h1>
	
	<p><?php printf(__('Here you can define webhooks which to be notified when a %1$s is completed. Webhooks are most often used with Zapier but can also be useful for integrations to other apps. Learn more about Zapier webhooks <a href="%2$s" target="_blank">here</a>.', 'watupro'), WATUPRO_QUIZ_WORD, 'https://zapier.com/blog/what-are-webhooks/');?></p>
	
	<p><?php printf(__('Learn more about using webhooks in WatuPRO <a href="%s" target="_blank">here</a>.', 'watupro'), 'https://blog.calendarscripts.info/zapier-webhooks-in-watupro/');?></p>
	
	<p><a href="admin.php?page=watupro_webhooks&action=add"><?php _e('Set up a new webhook', 'watupro');?></a></p>
	
	<?php if(count($hooks)):?>
		<table class="widefat">
			<thead>
				<tr><th><?php printf(__('Completing a %s', 'watupro'), WATUPRO_QUIZ_WORD)?></th>
				<th><?php _e('With a grade / result', 'watupro');?></th>
				<th><?php _e('Notifies hook URL', 'watupro');?></th>
				<th><?php _e('View/Edit', 'watupro');?></th>
				<th><?php _e('Delete', 'watupro');?></th></tr>
			</thead>
			<tbody>
				<?php foreach($hooks as $hook):
				if(empty($class)) $class = 'alternate';
				else $class = '';?>
					<tr class="<?php echo $class;?>">
						<td><?php echo stripslashes($hook->quiz_name);?></td>
						<td><?php echo $hook->grade_id ? stripslashes($hook->quiz_name) : __('Any', 'watupro');?></td>
						<td><?php echo $hook->hook_url;?></td>
						<td><a href="admin.php?page=watupro_webhooks&action=edit&id=<?php echo $hook->ID;?>"><?php _e('View/Edit', 'watupro');?></a></td>
						<td><a href="<?php echo wp_nonce_url('admin.php?page=watupro_webhooks&delete=1&id='.$hook->ID, 'delete_hook', 'watupro_hook_nonce')?>" class="delete_link"><?php _e('Delete', 'watupro');?></a></td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>
</div>

<script type="text/javascript" >
jQuery('.delete_link').click(function(){
    return confirm("<?php _e('Are you sure you want to delete the hook?', 'watupro')?>");
});
</script>