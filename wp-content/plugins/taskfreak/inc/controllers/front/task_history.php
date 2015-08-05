<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

View task history (called by Ajax if JS is enabled)

*/

$this->pid = intval($_REQUEST['hist']);

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

$this->log = new tfk_item_status();
$this->log->load_list(array('where' => 'item_id = '.$this->pid.' AND comment_id = 0'));

$this->view('front/task_history.php');