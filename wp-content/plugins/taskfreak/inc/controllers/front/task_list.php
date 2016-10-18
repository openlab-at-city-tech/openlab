<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

List tasks

*/

$this->baselink = add_query_arg('mode', 'tasks', tzn_tools::baselink());

$this->data = new tfk_item_info();

$sort_params = array('priority', 'deadline_date', 'proximity', 'title', 'name', 'display_name', 'log_date', 'item_status_action_code', 'comment_count', 'file_count');
$sort = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sort_params) ? $_REQUEST['sort'] : 'proximity');
$order = (isset($_REQUEST['ord']) && in_array($_REQUEST['ord'], array('asc', 'desc')) ? $_REQUEST['ord'] : 'ASC');

$this->page = isset($_REQUEST['pg']) && preg_match('/^\d+$/', $_REQUEST['pg']) ? $_REQUEST['pg'] : 1;

if (isset($_REQUEST['npg']) && preg_match('/^\d+$/', $_REQUEST['npg'])) { // useful if JS is disabled
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

// show "new task" or not
$project = new tfk_project();
$this->user_can_post = $project->load_count(array(
		'where' => tfk_user::get_roles_sql('who_post').' AND trashed = 0',
)); 

// show only drafts to their author and to users who have the right to manage the project
if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	$can_see_drafts = '( item_status_action_code <> 0 OR item.author_id = '.$current_user->ID.' )';
} else {
	$can_see_drafts = 'item_status_action_code <> 0';
}

$this->data->load_list(
		array(
				'where'		=> tfk_user::get_roles_sql('who_read').' AND trashed = 0',
				'having'	=> $can_see_drafts
								.($this->filters['filter_task'] == 'all' ? '' : ' AND item_status_action_code = '.$this->filters['filter_task']),				
				'order'		=> $sort.' '.$order.', priority ASC',
				'page_size'	=> $this->page_size,
				'page'		=> $this->page,
				'count' => true,
		)
);

$this->npages = ceil($this->data->total() / $this->page_size); 

$this->prio_size = !empty($this->options['prio_size']);

$this->view('front/task_list.php');
