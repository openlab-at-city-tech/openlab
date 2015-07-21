<?php 
$iid = $this->data->get_uid();
?>
<a id="tfk_back_list" href="<?php echo esc_url(add_query_arg('view', $iid, remove_query_arg(array('hist', 'noheader')))) ?>"><?php _e('Back to task', 'taskfreak') ?></a>
<h2 id="tfk_task_title">
	<a id="tfk_task_link" href="<?php echo esc_url(add_query_arg('view', $iid, tzn_tools::baselink())) ?>">
		<?php echo $this->data->get('title') ?>
	</a>
</h2>
<h3 id="tfk_task_project_name"><?php echo __('In', 'taskfreak').' '.$this->data->get('project')->html('name'); ?></h3>

<h4><?php _e('Task history', 'taskfreak') ?></h4>

<?php if ($this->log->count()): ?>
	<table id="tfk_task_history">
		<tr>
			<th><?php _e('Date', 'taskfreak') ?></th>
			<th><?php _e('User', 'taskfreak') ?></th>
			<th><?php _e('Action', 'taskfreak') ?></th>
		</tr>
		<?php while ($this->log->next()): ?>
		<tr>
			<td><?php echo $this->log->html('log_date') ?></td>
			<td>
				<?php echo $this->log->get('user')->get('display_name') ? $this->log->get('user')->get('display_name') : __('[deleted user]', 'taskfreak') ?>
			 </td>
			<td>
				<?php
					if ($this->log->get('action_code') != '')
						echo _x($this->log->get_status(), 'one task', 'taskfreak');
					else
						echo __('Modified', 'taskfreak').' ('.$this->log->get_info().')';   
				?>
			</td>
		</tr>
		<?php endwhile; ?>
	</table>
<?php endif; ?>
