<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

View task details

*/

$this->pid = intval($_REQUEST['view']);

$this->data = new tfk_item();

if ($this->pid) {
	$this->data->set_uid($this->pid);
	if (!$this->data->load()) {
		echo '<p class="tfk_err">'.__('Sorry, item not found', 'taskfreak').'</p>';
		return;
	}
} else {
	echo '<p class="tfk_err">'.__('Missing "view" parameter', 'taskfreak').'</p>';
	return;
}

$this->user_can_comment_project = $this->data->get('project')->check_access('comment');
$this->user_can_edit_task = get_current_user_id() == $this->data->get('author_id') // must be the author of the task 
							|| $this->data->get('project')->check_access('manage'); // or a manager of the project

if (($this->data->get_status()->get('action_code') == 0 && get_current_user_id() != $this->data->get('author_id')) 
	|| !$this->data->get('project')->check_access('read')) {
	echo '<p class="tfk_err">'.__("Sorry, you can't read this task. Please contact an admin.", 'taskfreak').'</p>';
	return;
}

$this->comment = new tfk_item_comment();

if (isset($_POST['edit'])) {
	// Submit data, check and save
	
	// we have to do this because of http://codex.wordpress.org/Function_Reference/stripslashes_deep#Notes
	$_POST = stripslashes_deep($_POST);
	
	$this->comment->set_auto($_POST);
	
	if (!$this->user_can_comment_project) {
		echo '<p class="tfk_err">'.__("Sorry, you can't comment tasks in this project. Please contact an admin.", 'taskfreak').'</p>';
		return;
	} elseif ($this->comment->check()) {
		$this->comment->set('body', $_POST['body'], 'HTM');
		$this->comment->set('item_id', $this->pid);
		$this->comment->set('post_date', 'NOW');
		$this->comment->set('user_id', get_current_user_id());
		$this->comment->insert();
		$log = new tfk_item_status();
		$log->set('log_date', 'NOW');
		$log->set('item_id', $this->pid);
		$log->set_object('user', get_current_user_id());
		$log->set('comment_id', $this->comment->get_uid());
		$log->save();

		$this->comment->errors['file'] = '';
		if ($this->options['comment_upload']) {
			$wp_upload_dir = wp_upload_dir();
			for ($i = 1; $i <= 3 ; $i++) { // up to 3 uploads per comment
				if (!empty($_FILES["tfk_file_$i"]['name'])) {
					$file = new tfk_item_file();
					if ($file->upload("tfk_file_$i")) {
						$file->set('item_id', $this->pid);
						$file->set('user_id', get_current_user_id());
						$file->set('file_tags', 'comment');
						$file->save();
						$this->comment->set('body', $this->comment->get('body').
								'<p>'.__('Uploaded file: ', 'taskfreak')
								.'<a href="'.$wp_upload_dir['url'].'/'.$file->get('file_name').'" rel="external">'
								.$file->get('file_title')
								.'</a>'
								.'</p>');
						$this->comment->update();
					} else {
						$this->comment->errors['file'] .= '<p class="tfk_err">'.$file->error.'</p>';
					}
				} // else no file, do nothing
			}
		}
		
		if (!$this->comment->errors['file']) {
			if (headers_sent()) {
				$this->data->load();
				$this->comment->errors['global_errors'] =
					'<p class="tfk_ok">'
						.'<a id="tfk_back_list" href="'.esc_url(remove_query_arg('noheader')).'#tfk_comment_'.$this->comment->get_uid().'">'
							.__('See comment', 'taskfreak')
						.'</a>'
						.__('Comment saved.', 'taskfreak')
					.'</p>';
			} else {
				wp_redirect(remove_query_arg('noheader', add_query_arg('view', $this->pid)).'#tfk_comment_'.$this->comment->get_uid());
				exit();
			}
		}
	}
} elseif (preg_match('/noheader=1/', $_SERVER['REQUEST_URI'])) {
	echo '<p class="tfk_err">'.__('Upload failed. Check file size.', 'taskfreak').'</p>';
	return;
}

$this->comment->load_list(array('where' => 'item_id = '.$this->pid));

$this->file = new tfk_item_file();
$this->file->load_list(array('where' => 'item_id = '.$this->pid.' and file_tags = "task"'));

$this->view('front/task.php');