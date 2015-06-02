<?php 
$iid = $this->data->get_uid();
$assignee_id = $this->data->get('user_id');
$assignee =  get_userdata($assignee_id);
$author_id = $this->data->get('author_id');
$author = get_userdata($this->data->get('author_id'));
$last_mod_user = get_userdata($this->data->get('item_status_user_id'));
$status_action_code = $this->data->get_status()->get('action_code');
?>
<a id="tfk_back_list" href="<?php echo esc_url(remove_query_arg(array('view', 'noheader'))).'#tfk_row-'.$iid ?>"><?php _e('Back to list', 'taskfreak') ?></a>
<h2 id="tfk_task_title">
	<a id="tfk_task_link" href="<?php echo esc_url(add_query_arg('view', $iid, tzn_tools::baselink())) ?>">
		<?php echo $this->data->get('title') ?>
	</a>
</h2>
<h3 id="tfk_task_project_name"><?php echo __('In', 'taskfreak').' '.$this->data->get('project')->html('name') ?></h3>
<?php echo $this->comment->errors['global_errors'] ?>
<div id="tfk_task_edit_comment">
	<?php if ($this->user_can_edit_task): ?>
	<a id="tfk_task_edit" href="<?php echo esc_url(remove_query_arg(array('view','noheader'), add_query_arg('edit', $iid))) ?>#tfk_edit_task_form">
		<?php _e('Edit', 'taskfreak') ?>
	</a>
	<?php endif; ?>
	<?php if ($this->user_can_comment_project): ?>
	<a href="#tfk_comment_form"><?php _e('Comment', 'taskfreak') ?></a>
	<?php endif; ?>
</div>
<span id="tfk_task_author_pic" 
		class="tfk_avatar_<?php echo $this->options['avatar_size'] ?>" 
		title="<?php echo __('Task created by', 'taskfreak').' '.($author ? $author->display_name : __('[deleted user]', 'taskfreak')) ?>">
	<?php echo get_avatar(($author_id ? $author_id : 0), $this->options['avatar_size']); ?>
</span>
<p class="tfk_task_head"><?php echo __('By', 'taskfreak').' '.($author ? $author->display_name : __('[deleted user]', 'taskfreak')) ?></p>
<p class="tfk_task_head"><?php echo __('On', 'taskfreak').' '.$this->data->html('creation_date') ?></p>
<div id="tfk_task_details" class="tfk_pr<?php echo $this->data->get('priority') ?>">
	<p><span><?php _e('Priority:', 'taskfreak') ?></span> <span class="tfk_pri tfk_pr<?php echo $this->data->get('priority') ?>"><?php echo $this->data->get('priority') ?></span></p>
	<p><span><?php _e('Deadline:', 'taskfreak') ?></span> <span><?php echo $this->data->html('deadline_date') ? $this->data->html('deadline_date') : '&mdash;' ?></span></p>
	<p><span><?php _e('Assigned To:', 'taskfreak') ?></span> <span><?php echo $assignee_id ? ($assignee ? $assignee->display_name : __('[deleted user]', 'taskfreak')) : __('Unassigned', 'taskfreak'); ?></span></p>
	<p class="tfk_sts">
		<span class="tfk_hid"><?php _e('Change Status:', 'taskfreak') ?></span>
		<?php $nonce = wp_create_nonce('status_changer') ?>
		<a id="tfk_sts1-<?php echo $iid ?>" 
			class="tfk_sts<?php echo $status_action_code > 0 ? 1 : 0 ?>" 
			href="<?php echo esc_url(remove_query_arg('view', add_query_arg(array('edit' => $iid, 'status' => 20, 'tfknonce' => $nonce)))) ?>" 
			title="<?php _e('Click to mark as In Progress', 'taskfreak') ?>">
			<?php _ex('In Progress', 'one task', 'taskfreak') ?>
		</a><a id="tfk_sts2-<?php echo $iid ?>" 
			class="tfk_sts<?php echo $status_action_code > 20 ? 1 : 0 ?>" 
			href="<?php echo esc_url(remove_query_arg('view', add_query_arg(array('edit' => $iid, 'status' => 30, 'tfknonce' => $nonce)))) ?>" 
			title="<?php _e('Click to mark as Suspended', 'taskfreak') ?>">
			<?php _ex('Suspended', 'one task', 'taskfreak') ?>
		</a><a id="tfk_sts3-<?php echo $iid ?>" 
			class="tfk_sts<?php echo $status_action_code > 30 ? 1 : 0 ?>" 
			href="<?php echo esc_url(remove_query_arg('view', add_query_arg(array('edit' => $iid, 'status' => 60, 'tfknonce' => $nonce)))) ?>" 
			title="<?php _e('Click to mark as Closed', 'taskfreak') ?>">
			<?php _ex('Closed', 'one task', 'taskfreak') ?>
		</a>
		<span class="tfk_hid"><?php _e('Current Status: ', 'taskfreak') ?></span><span class="tfk_sts_lbl"><?php _ex($this->data->get_status()->get_status(), 'one task', 'taskfreak') ?></span>
	</p>
	<p id="tfk_task_hist_cntr">
		<a id="tfk_task_history_toggle" 
            class="tfk_task_history_hidden"
            href="<?php echo esc_url(add_query_arg('hist', $iid, tzn_tools::baselink())) ?>" 
            title="<?php _e('Task history', 'taskfreak') ?>"><?php _e('Task history', 'taskfreak') ?></a>
	</p>
</div>
<table id="tfk_task_history"></table>
<div id="tfk_task_description"><?php echo $this->data->get('description') ?></div>
<?php if ($this->file->count()): 
		$wp_upload_dir = wp_upload_dir(); ?>
	<h3><?php _e('Attached files', 'taskfreak') ?></h3>
	<ol id="tfk_uploaded_files">
	<?php while ($this->file->next()): ?>
		<li>
			<a href="<?php echo $wp_upload_dir['url'].'/'.$this->file->get('file_name') ?>" rel="external">
				<?php echo $this->file->get('file_title') ?>
			</a>
		</li>
	<?php endwhile; ?>
	</ol>
<?php endif; ?>
<h3 id="tfk_comments"><?php echo $this->comment->count().' '._n('comment', 'comments', $this->comment->count() > 1 ? 2 : 1, 'taskfreak') ?></h3>
<ul>
<?php while ($this->comment->next()): ?>
	<li class="tfk_comment" id="tfk_comment_<?php echo $this->comment->get_uid() ?>">
		<div class="tfk_comm_user tfk_avatar_<?php echo $this->options['avatar_size'] ?>">
			<?php 
				$comment_user_id = $this->comment->get('user_id');
				$comment_user = get_userdata($comment_user_id);
				echo get_avatar($comment_user_id ? $comment_user_id : 0, $this->options['avatar_size']);
			?>
		</div>
		<div>
			<a class="tfk_comm_lnk" href="<?php echo esc_url(add_query_arg('view', $iid, tzn_tools::baselink())) ?>#tfk_comment_<?php echo $this->comment->get_uid() ?>">#</a>
			<?php echo $comment_user ? $comment_user->display_name : _e('[deleted user]', 'taskfreak') ?>
			<div class="tfk_comm_date"><?php echo $this->comment->html('post_date') ?></div>
			<div class="tfk_comm_body"><?php echo $this->comment->get('body') ?></div>
		</div>
	</li>
<?php endwhile; ?>
</ul>
<?php if ($this->user_can_comment_project): ?>
<form id="tfk_comment_form" action="<?php echo esc_url(add_query_arg('noheader', 1)) ?>#tfk_task_title" enctype="multipart/form-data" method="post">
	<input type="hidden" name="edit" value="<?php echo $this->data->value('item_id'); ?>">
	<div>
		<p id="tfk_add_comment"><?php _e('Add a Comment:', 'taskfreak') ?></p>
		<?php
		wp_editor('', 'body', array(
			'media_buttons'=> false, // no media button
	        'wpautop'	=> false, // no <p>
	        'teeny'	=> true, // minimal editor
	        'dfw' => false,
	        // 'tabfocus_elements' => 'sample-permalink,post-preview',
	        'editor_height' => 360
	    ));
		?>
		<?php echo $this->comment->errors['body'] ?>
	</div>
	<?php if ($this->options['comment_upload']): ?>
	<div>
		<p id="tfk_add_file"><?php _e('Add a File:', 'taskfreak') ?></p>
		<ul>
			<li id="tfk_file_1"><input type="file" name="tfk_file_1"><a href="#" class="tfk_file_more"><?php _e('more', 'taskfreak') ?></a></li>
			<li id="tfk_file_2"><input type="file" name="tfk_file_2"><a href="#" class="tfk_file_more"><?php _e('more', 'taskfreak') ?></a></li>
			<li id="tfk_file_3"><input type="file" name="tfk_file_3"></li>
		</ul>
		<?php echo $this->comment->errors['file'] ?>
	</div>
	<?php endif; ?>
	<button type="submit"><?php _e('Save Comment', 'taskfreak') ?></button>
</form>
<?php endif; ?>
