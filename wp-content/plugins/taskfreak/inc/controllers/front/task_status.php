<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

Update task status in Ajax

*/

if (!isset($_REQUEST['tfknonce']) || !wp_verify_nonce($_REQUEST['tfknonce'], 'status_changer')) {
	echo '<p class="tfk_err">'.__("Sorry, request forbidden for security reasons.", 'taskfreak').'</p>';
	return;
}

$this->pid = intval($_REQUEST['edit']);

$this->data = new tfk_item();
$this->status = new tfk_item_status();
$this->statuses = $this->status->get_status_list(false, 'one task');

$status_code = isset($_GET['status']) && isset($this->statuses[$_GET['status']]) ? $_GET['status'] : -1;

if ($this->pid) {
	$this->data->set_uid($this->pid);
	if (!$this->data->load()) {
		$this->message = 'ERR '.__('No such item', 'taskfreak');
	} else {
		if (get_current_user_id() != $this->data->get('author_id') // current user must be the author of the task
	            && !$this->data->get('project')->check_access('manage')) { // or must be a manager of the project
			$this->message = 'ERR '.__("You can't change this status", 'taskfreak');
		} else {
			if ($status_code > -1 
				&& ($status_code == $this->data->get_status()->get('action_code')
					|| $this->data->set_status($status_code))
			) {
				$this->message = 'OK  '.$this->statuses[$status_code];
			} else {
				$this->message = 'ERR '.__('Unable to set status', 'taskfreak');
			}
		}
	}
} else {
	$this->message = 'ERR '.__('No such item', 'taskfreak');
}
$this->view('front/status.php');
