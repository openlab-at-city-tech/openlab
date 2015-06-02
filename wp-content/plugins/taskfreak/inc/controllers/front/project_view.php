<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

View Project and list tasks

*/

$this->pid = intval($_REQUEST['proj']);

$this->baselink = add_query_arg(array('mode' => 'projects', 'proj' => $this->pid), tzn_tools::baselink());

$sort_params = array('priority', 'deadline_date', 'title', 'name', 'display_name', 'log_date', 'item_status_action_code', 'comment_count', 'file_count');
$sort = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sort_params) ? $_REQUEST['sort'] : 'proximity');
$order = (isset($_REQUEST['ord']) && in_array($_REQUEST['ord'], array('asc', 'desc')) ? $_REQUEST['ord'] : 'ASC');

$this->page = isset($_REQUEST['pg']) && preg_match('/^\d+$/', $_REQUEST['pg']) ? $_REQUEST['pg'] : 1;

if (isset($_REQUEST['npg']) && preg_match('/^\d+$/', $_REQUEST['npg'])) {
	if (!headers_sent()) {
		setcookie('tfk_page_size', $_REQUEST['npg']);
	}
	$this->page_size = $_REQUEST['npg'];
} elseif (isset($_COOKIE['tfk_page_size']) && preg_match('/^\d+$/', $_COOKIE['tfk_page_size'])) {
	$this->page_size = $_COOKIE['tfk_page_size'];
} elseif ($this->options['tasks_per_page']) {
	$this->page_size = $this->options['tasks_per_page'];
} else {
	$this->page_size = 5;
}

// load project
$this->project = new tfk_project();
$this->project->set_uid($this->pid);
if (!$this->pid || !$this->project->load()) {
	echo '<p class="tfk_err">'.__("No such project.", 'taskfreak').'</p>';
	return;
} else {
	// check if user has access
	if (!$this->project->check_access('read')) {
		echo '<p class="tfk_err">'.__("Sorry, you can't access this project. Please contact an admin.", 'taskfreak').'</p>';
		return;
	}
}

$this->user_can_post = $this->project->check_access('post');

// show only drafts to their author and to users who have the right to manage the project
if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	$can_see_drafts = '( item_status_action_code <> 0 OR item.author_id = '.$current_user->ID.' )';
} else {
	$can_see_drafts = 'item_status_action_code <> 0';
}

// load tasks
$this->data = new tfk_item_info();
$this->data->load_list(
		array(
				'where'		=> 'item.project_id = '.$this->pid.' AND trashed = 0',
				'having'	=> $can_see_drafts
								.($this->filters['filter_task'] == 'all' ? '' : ' AND item_status_action_code = '.$this->filters['filter_task']),
				'order'		=> $sort.' '.$order,
				'page_size'	=> $this->page_size,
				'page'		=> $this->page
	)
);

$this->npages = ceil($this->data->total() / $this->page_size);

$this->view('front/project_view.php');