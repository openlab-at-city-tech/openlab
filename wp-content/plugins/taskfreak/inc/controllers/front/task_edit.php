<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

Edit tasks

*/

if (!is_user_logged_in()) {
	echo '<p class="tfk_err">'.__("Sorry, you can't edit tasks when logged out.", 'taskfreak').'</p>';
	return;
}

$this->pid = intval($_REQUEST['edit']);

$this->data = new tfk_item();
if ($this->pid) {
	$this->data->set_uid($this->pid);
	if (!$this->data->load()) {
		$this->pid = 0;
		$this->data->set_uid(0); // prevent user from setting id
	}
}

// if we hit "new task" in a project's task list, then...
if (!$this->pid && isset($_REQUEST['proj']) && preg_match('/^\d+$/', $_REQUEST['proj'])) {
	$this->current_project = $_REQUEST['proj'];
} else {
	$this->current_project = '';
}

if ($this->pid != 0
		&& get_current_user_id() != $this->data->get('author_id') // current user must be the author of the task
		&& !$this->data->get('project')->check_access('manage')) { // or must be a manager of the project
	echo '<p class="tfk_err">'.__("Sorry, you can't edit this task. Please contact an admin.", 'taskfreak').'</p>';
	return;
}

// --- SUBMIT ---------------------------

if (isset($_POST['edit'])) {

	// Submit data, check and save

	// we have to do this because of http://codex.wordpress.org/Function_Reference/stripslashes_deep#Notes
	$_POST = stripslashes_deep($_POST);
	
	if ($this->pid) { // prepare log info (if data check fails, it won't be saved)
		$update_log = new tfk_item_status();
		$update_log->set('log_date', 'NOW');
		$update_log->set('item_id', $this->pid);
		$update_log->set_object('user', get_current_user_id());
		$update_log_info = array();
		foreach (array('title', 'priority', 'user_id', 'description') as $prop) {
			if (!empty($_POST[$prop]) && $_POST[$prop] != $this->data->get($prop))
				$update_log_info[] = $prop;
		}
		if (!empty($_POST['project_id']) 
					&& $_POST['project_id'] != $this->data->get('project')->get_uid())
			$update_log_info[] = 'project';
		if (!empty($_POST['deadline_date']) 
					&& $_POST['deadline_date'] != $this->data->html('deadline_date'))
			$update_log_info[] = 'deadline';
	}
	
	$this->data->set_auto($_POST);

	if (!$this->data->get('project')->get_uid() || !$this->data->get('project')->load()) {
		$this->data->errors['project'] = '<p class="tfk_err">'.__("Unavailable project", 'taskfreak').'</p>';
	} elseif (!$this->data->get('project')->check_access('post')) {
		$this->data->errors['project'] = '<p class="tfk_err">'.__("Sorry, you can't post in this project. Please contact an admin.", 'taskfreak').'</p>';
	} elseif ($this->data->get('user_id') 
				&& ! $this->data->get('project')->check_access('read', $this->data->get('user_id'))) {
		$this->data->errors['user_id'] = '<p class="tfk_err">'.__("Please select a user who has access to this project", 'taskfreak').'</p>';
	} elseif ($this->data->check()) {
		if ($this->pid) {
			$this->data->update();
			if ($_POST['status'] != $this->data->get_status()->get('action_code')) {
				$this->data->set_status($_POST['status']);
			}
		} else {
			$this->data->set('author_id', get_current_user_id());
			$this->data->set('creation_date', 'NOW');
			$this->pid = $this->data->insert();
			$this->data->set_status($_POST['status'], null, 'creation');
		}
		
		$this->data->errors['file'] = '';
		$wp_upload_dir = wp_upload_dir();
		$saved_files = 0;
		for ($i = 1; $i <= 5 ; $i++) { // up to 5 uploads per edit
			if (!empty($_FILES["tfk_file_$i"]['name'])) {
				$file = new tfk_item_file();
				if ($file->upload("tfk_file_$i")) {
					$file->set('item_id', $this->pid);
					$file->set('user_id', get_current_user_id());
					$file->set('file_tags', 'task');
					$file->save();
					$saved_files++;
				} else {
					$this->data->errors['file'] .= '<p class="tfk_err">'.$file->error.'</p>';
				}
			} // else no file, do nothing
		}
		
		if (isset($update_log)) {
			if ($saved_files) {
				$update_log_info[] = $saved_files.' file(s)';
			}
			if (!empty($update_log_info)) {
				$update_log->set('info', implode(',', $update_log_info));
				$update_log->save();
			}
		}
		
		if (!$this->data->errors['file']) {
			if (headers_sent()) {
				$this->data->load();
				$this->data->errors['global_errors'] = 
					'<p class="tfk_ok">'.__('Changes saved.', 'taskfreak').'</p>';
			} else {
				wp_redirect(remove_query_arg(array('edit', 'noheader'), add_query_arg('view', $this->pid)."#tfk_task_title"));
				exit();
			}
		}
	}
} elseif (preg_match('/noheader=1/', $_SERVER['REQUEST_URI'])) {
	echo '<p class="tfk_err">'.__('Upload failed. Check file size.', 'taskfreak').'</p>';
	return;
}

// --- DISPLAY FORM (prepare data) -----

// load attachments
$this->file = new tfk_item_file();
$this->file->load_list(array('where' => 'item_id = '.$this->pid.' AND file_tags = "task"'));

// load projects
$this->projects = new tfk_project_info();
$this->projects->load_list(array(
			'where' => tfk_user::get_roles_sql('who_post').' AND trashed = 0 ',
			'having' => 'project_status_action_code IN (20, 30)',
			'order'	=> 'name ASC',
));

// load users
$this->users = get_users();

// for status list
$this->status = new tfk_item_status();

$this->view('front/edit.php');
