<?php echo $this->data->errors['global_errors'] ?>
<?php $iid = $this->data->value('item_id') ?>
<form name="my_form" action="<?php echo esc_url(add_query_arg('noheader', 1)); ?>" enctype="multipart/form-data" method="post" id="tfk_edit_task_form">
	<input type="hidden" name="edit" value="<?php echo $iid; ?>">
	
	<fieldset>
		
		<a id="tfk_back_list" href="<?php echo esc_url(remove_query_arg(array('edit', 'noheader'))).'#tfk_row-'.$iid ?>"><?php _e('Back to list', 'taskfreak') ?></a>
		<h3><?php echo $this->pid ? __('Edit Task', 'taskfreak') : __('New Task', 'taskfreak') ?></h3>
		
		<ol>
			<li>
				<label for="tfk_title"><?php _e('Title', 'taskfreak') ?></label>
				<input type="text" name="title" id="tfk_title" value="<?php echo $this->data->value('title'); ?>">
				<?php echo $this->data->errors['title'] ?>
			</li>
			<li>
				<label for="tfk_project"><?php _e('Project', 'taskfreak') ?></label>
				<?php if ($this->projects->count()): ?>
					<select name="project_id" id="tfk_project" data-ajax="<?php echo esc_url(add_query_arg('js', 1, tzn_tools::baselink())) ?>">
						<?php while ($obj = $this->projects->next(true)):?>
							<option value="<?php echo $obj->get_uid(); ?>"<?php 
								if ($this->pid && $this->data->get('project')->value('project_id') == $obj->get_uid()
									|| !$this->pid && $this->current_project == $obj->get_uid()) { 
									echo " selected"; 
								}
								?>><?php echo (strlen($obj->get('name')) > 60 ? substr($obj->get('name'), 0, 60).'…' : $obj->get('name')) ?></option>
						<?php endwhile; ?>
					</select>
				<?php else: ?>
					<?php _e('Create a project first, please', 'taskfreak'); ?>
				<?php endif; ?>
				<?php echo $this->data->errors['project'] ?>
			</li>
			<li>
				<label for="tfk_priority"><?php _e('Priority', 'taskfreak') ?></label>
				<div id="tfk_select_prio_color">&nbsp;</div>
				<select id="tfk_select_prio" name="priority" id="tfk_priority">
					<?php for ($p = 1; $p < 4; $p++): ?>
					<option value="<?php echo $p ?>"<?php if ($this->data->value('priority') == $p) { echo " selected"; } ?>><?php echo $p ?></option>
					<?php endfor; ?>
				</select>
				<?php echo $this->data->errors['priority'] ?>
			</li>
			<li>
				<label for="tfk_deadline_date"><?php _e('Deadline', 'taskfreak') ?></label>
				<input type="text" name="deadline_date" id="tfk_deadline_date" 
					value="<?php echo ($this->pid && $this->data->value('deadline_date')) ? tzn_tools::format_date($this->data->value('deadline_date'), $this->options['format_date']) : '' ?>">
				<img id="tfk_cal_btn" 
					src="<?php echo plugins_url('taskfreak') ?>/img/calendar.png" 
					alt="<?php _e('Calendar', 'taskfreak') ?>" 
					title="<?php _e('Click to show calendar', 'taskfreak') ?>"
					width="28"
					height="28">
				<!-- span id="tfk_cal_btn" type="button"><?php _e('Calendar', 'taskfreak') ?></span-->
				<?php echo $this->data->errors['deadline_date'] ?>
			</li>
			<li>
				<label for="tfk_user_id"><?php _e('Assigned to', 'taskfreak') ?></label>
				<select name="user_id" id="tfk_user_id">
					<option value="">&mdash;</option>
					<?php foreach ($this->users as $user): ?>
					<option value="<?php echo $user->ID ?>"<?php if ($this->pid && $this->data->get('user_id') == $user->ID) { echo " selected"; } ?>><?php echo $user->display_name ?></option>
					<?php endforeach; ?>
				</select>
				<?php echo $this->data->errors['user_id'] ?>
			</li>
			<li>
				<label for="tfk_status"><?php _e('Status', 'taskfreak') ?></label>
				<select name="status" id="tfk_status">
					<?php foreach ($this->status->get_status_list(true, 'one task') as $status => $status_lbl): ?>
					<option value="<?php echo $status ?>"
					<?php if ((isset($_GET['status']) && $_GET['status'] == $status) 
								|| ($this->pid && !isset($_GET['status']) && $this->data->get_status()->get('action_code') == $status)) { 
									echo " selected"; } ?>>
						<?php echo $status_lbl ?>
					</option>
					<?php endforeach; ?>
				</select>
				<?php echo $this->data->errors['status'] ?>
			</li>
		</ol>
	
		<?php
		wp_editor($this->data->value('description'), 'description', array(
			'media_buttons'=> false, // no media button
	        'wpautop'	=> false, // no <p>
	        'teeny'	=> true, // minimal editor
	        'dfw' => false,
	        // 'tabfocus_elements' => 'sample-permalink,post-preview',
	        'editor_height' => 360
	    ));
		?>
		<?php echo $this->data->errors['description'] ?>
		<p id="tfk_add_file"><?php _e('Add a File:', 'taskfreak') ?></p>
		<ul>
			<li id="tfk_file_1"><input type="file" name="tfk_file_1"><a href="#" class="tfk_file_more"><?php _e('more', 'taskfreak') ?></a></li>
			<li id="tfk_file_2"><input type="file" name="tfk_file_2"><a href="#" class="tfk_file_more"><?php _e('more', 'taskfreak') ?></a></li>
			<li id="tfk_file_3"><input type="file" name="tfk_file_3"><a href="#" class="tfk_file_more"><?php _e('more', 'taskfreak') ?></a></li>
			<li id="tfk_file_4"><input type="file" name="tfk_file_4"><a href="#" class="tfk_file_more"><?php _e('more', 'taskfreak') ?></a></li>
			<li id="tfk_file_5"><input type="file" name="tfk_file_5"></li>
		</ul>
		<?php echo $this->data->errors['file'] ?>
	
		<?php if ($this->file->count()):
				$wp_upload_dir = wp_upload_dir(); ?>
			<p><?php _e('Attached Files:', 'taskfreak') ?></p>
			<ol id="tfk_uploaded_files">
			<?php while ($this->file->next()) : ?>
				<li>
					<a href="<?php echo $wp_upload_dir['url'].'/'.$this->file->get('file_name') ?>"
						rel="external">
						<?php echo $this->file->get('file_title') ?>
					</a>
				</li>
			<?php endwhile; ?>
			</ol>
		<?php endif; ?>
		</fieldset>
	<button type="submit"><?php _e('Save Task', 'taskfreak') ?></button>

</form>