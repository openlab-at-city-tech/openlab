<?php if ($this->user_can_post): ?>
<a id="tfk_new_task" href="<?php echo esc_url(add_query_arg('edit', '')) ?>"><?php _e('New Task', 'taskfreak') ?></a>
<?php endif; ?>
<ul id="tfk_subfilters">
<?php echo tfk_item_status::list_links($this->baselink, 'filter_task', $this->filters['filter_task'], ($this->data->count() ? $this->data->total() : null)); ?>
</ul>
<?php
if ($this->data->count()) {
?>
<div id="tfk_task_count">
	<?php echo $this->data->total().' '._n('task', 'tasks', $this->data->total(), 'taskfreak') ?>
</div>
<div id="tfk_sort_criteria">
	<label for="tfk_sort_criteria"><?php _e('Sort by:', 'taskfreak') ?></label>
	<ul>
	<?php foreach (array(
			array('proximity', __('Deadline proximity', 'taskfreak'), 'asc'),
			array('deadline_date', __('Deadline date', 'taskfreak'), 'desc'),
			array('priority', __('Priority', 'taskfreak'), 'asc'),
			array('title', __('Title', 'taskfreak'), 'asc'),
			array('display_name', __('Assignee', 'taskfreak'), 'asc'),
			array('log_date', __('Modification date', 'taskfreak'), 'desc'),
			array('item_status_action_code', __('Status', 'taskfreak'), 'desc'),
	) as $sort): ?>
		<li<?php echo (isset($_REQUEST['sort']) && $_REQUEST['sort'] == $sort[0]
						&& isset($_REQUEST['ord']) && $_REQUEST['ord'] == $sort[2]	? ' class="tfk_selected_order"' : '') ?>>
			<a href="<?php echo esc_url(remove_query_arg('pg', add_query_arg(array('sort' => $sort[0], 'ord' => $sort[2])))) ?>"><?php echo $sort[1] ?></a>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
<ol id="tfk_tasksheet">
	<?php	
	while ($this->data->next()):
		$iid = $this->data->get_uid();
		$user_id = $this->data->get('user_id');
		$user =  get_userdata($user_id);
		$last_mod_user = get_userdata($this->data->get('item_status_user_id'));
		$deadline_date = $this->data->html('deadline_date');
		?>
		<li id="tfk_row-<?php echo $iid; ?>" class="tfk_pr<?php
			echo $this->data->get('priority').' ';
			if ($this->data->get('item_status')->get('action_code') != 60 && $deadline_date) {
		 		if ($this->data->get('deadline_date') < date("Y-m-d H:i:s", time()-24*60*60)) {
					echo "tfk_late";
				} elseif ($this->data->get('deadline_date') < date("Y-m-d H:i:s", time()+7*24*60*60)) { // TODO 7 days = urgent => setting ?
					echo "tfk_urg";
				}
			} else {
				echo "tfk_none";
			}
			?>">
			<ul>
				<li class="tfk_col1 tfk_avatar_<?php echo $this->options['avatar_size'] ?>"
					title="<?php echo $user_id ? 
										__('Assigned to', 'taskfreak').' '.($user ? $user->display_name : __('[deleted user]', 'taskfreak'))
										: __('Unassigned', 'taskfreak')?>">
					<?php echo get_avatar($user_id, $this->options['avatar_size']); ?>
				</li>
				<li class="tfk_col2">
					<ul>
						<?php 
						$proximity = $this->data->get('item_status')->get('action_code') == 60 ? _x('Closed', 'one task', 'taskfreak') : $this->data->get_deadline_proximity();
						if ($this->options['proximity']):
						?>
						<li class="tfk_prox" style="width: <?php echo $this->data->get_deadline_proximity_bar() ?>px" title="<?php echo $proximity ?>">
							<?php echo $proximity ?>
						</li>
						<?php endif; ?>
						<li class="tfk_desc<?php if ($this->prio_size) echo " tfk_size" ?>">
							<a	href="<?php echo esc_url(add_query_arg('view', $iid)).'#tfk_task_title'; ?>"
								title="<?php echo $this->data->get_description_extract(); ?>">
								<?php echo $this->data->html('title'); ?>
							</a>
						</li>
						<li class="tfk_prj"><?php echo $this->data->get('project')->html('name'); ?></li>
						<li class="tfk_pri"><span class="tfk_hid"><?php _e('Prio.:', 'taskfreak') ?> </span><?php echo $this->data->get('priority'); ?></li>
						<li class="tfk_usr<?php echo $user_id ? '' : ' unassigned' ?>"><?php echo $user_id ? ($user ? $user->display_name : __('[deleted user]', 'taskfreak')) : __('Unassigned', 'taskfreak') ; ?></li>
						<li class="tfk_upd"><?php 
							if ($this->data->get('item_status_info') == 'creation') {
								echo __('Add.: ', 'taskfreak')
										.$this->data->html('creation_date');
							} else {
								echo _e('Mod.: ', 'taskfreak')
										.$this->data->html('item_status_date');
							}
							echo __(' by ', 'taskfreak')	.($last_mod_user ? $last_mod_user->display_name : __('[deleted user]', 'taskfreak')); ?></li>
					</ul>
				</li>
				<li class="tfk_col3">
					<ul>
						<li class="tfk_com">
							<a title="<?php echo $this->data->get('comment_count').' '.($this->data->get('comment_count') ? _n('comment', 'comments', $this->data->get('comment_count'), 'taskfreak') : __('comment', 'taskfreak')) ?>" 
								href="<?php echo esc_url(add_query_arg('view', $iid)).'#tfk_comments' ?>">
								<?php echo $this->data->get('comment_count') ?>
							</a>
							<span class="tfk_hid">
								<?php echo $this->data->get('comment_count') ? _n('comment', 'comments', $this->data->get('comment_count'), 'taskfreak') : __('comment', 'taskfreak') ?>
							</span>
						</li>
						<?php if ($this->data->value('file_count')): ?>
						<li class="tfk_atch" title="<?php echo $this->data->get_attachments() ?>">
							<a title="<?php echo $this->data->get_attachments() ?>" href="<?php echo esc_url(add_query_arg('view', $iid)).'#tfk_uploaded_files' ?>">
								<?php echo $this->data->get('file_count') ?>
							</a>
							<span class="tfk_hid"><?php _e('Attached files', 'taskfreak') ?></span>
						</li>
						<?php endif; ?>
					</ul>
				</li>
				<li class="tfk_col4" id="tfk_col4-<?php echo $iid ?>">
					<ul>
						<li class="tfk_due" 
							title="<?php echo ($deadline_date ? $deadline_date.' — '.$proximity : __('Undefined Deadline', 'taskfreak')) ?>">
							<span class="tfk_hid"><?php _e('Deadline:', 'taskfreak') ?></span>
							<?php echo $deadline_date ? $deadline_date : '—'; ?>
						</li>
						<li class="tfk_sts">
							<span class="tfk_hid"><?php _e('Change Status:', 'taskfreak') ?></span>
							<?php $nonce = wp_create_nonce('status_changer') ?>
							<a id="tfk_sts1-<?php echo $iid ?>" 
								class="tfk_sts<?php echo $this->data->get('item_status')->get('action_code') > 0 ? 1 : 0 ?>" 
								href="<?php echo esc_url(add_query_arg(array('edit' => $iid, 'status' => 20, 'tfknonce' => $nonce))); ?>" 
								title="<?php _e('Click to mark as In Progress', 'taskfreak') ?>">
								<?php _ex('In Progress', 'one task', 'taskfreak') ?>
							</a><a id="tfk_sts2-<?php echo $iid ?>" 
								class="tfk_sts<?php echo $this->data->get('item_status')->get('action_code') > 20 ? 1 : 0 ?>" 
								href="<?php echo esc_url(add_query_arg(array('edit' => $iid, 'status' => 30, 'tfknonce' => $nonce))); ?>" 
								title="<?php _e('Click to mark as Suspended', 'taskfreak') ?>">
								<?php _ex('Suspended', 'one task', 'taskfreak') ?>
							</a><a id="tfk_sts3-<?php echo $iid ?>" 
								class="tfk_sts<?php echo $this->data->get('item_status')->get('action_code') > 30 ? 1 : 0 ?>" 
								href="<?php echo esc_url(add_query_arg(array('edit' => $iid, 'status' => 60, 'tfknonce' => $nonce))); ?>" 
								title="<?php _e('Click to mark as Closed', 'taskfreak') ?>">
								<?php _ex('Closed', 'one task', 'taskfreak') ?>
							</a>
							<span class="tfk_hid"><?php _e('Current Status: ', 'taskfreak') ?></span><span class="tfk_sts_lbl"><?php _ex($this->data->get('item_status')->get_status(), 'one task', 'taskfreak') ?></span>
						</li>
					</ul>
				</li>
			</ul>
		</li>
		<?php
	endwhile;
	?>
</ol>

<div id="tfk_pager">
	<label for="tfk_pager"><?php _e('Page:', 'taskfreak') ?></label>
	<ul>
	<?php for ($p = 1 ; $p <= $this->npages ; $p++): ?>
		<li<?php echo $this->page == $p ? ' class="tfk_current_page"' : '' ?>>
			<a href="<?php echo esc_url(add_query_arg('pg', $p)) ?>"><?php echo $p ?></a>
		</li>	
	<?php endfor; ?>
	</ul>
</div>

<div id="tfk_page_size">
	<label for="tfk_page_size"><?php _e('Results per page:', 'taskfreak') ?></label>
	<ul>
	<?php foreach (array(5, 10, 20, 50, 100) as $npg): ?>
		<li<?php echo $this->page_size == $npg ? ' class="tfk_selected_page_size"' : '' ?>>
			<a href="<?php echo esc_url(remove_query_arg('pg', add_query_arg('npg', $npg))) ?>"><?php echo $npg ?></a>
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<?php
} else {
?>
<p>
	<?php _e('No item found','taskfreak') ?>
</p>
<?php
}
?>
