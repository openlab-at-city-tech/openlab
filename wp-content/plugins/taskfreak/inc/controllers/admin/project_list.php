<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

List projects

*/
// security : only author and admin are allowed to see draft and trashed

$this->filter = 'all';
$where = 'trashed=0';

if (!$this->is_manager) {
	// non admin can not list drafts
	$where .= " AND project_status.action_code<>'0'";
}

if (isset($_REQUEST['filter'])) {

	$this->filter = $_REQUEST['filter'];
	
	// check filter
	switch ($_REQUEST['filter']) {
		case 'trash':
			if ($this->is_manager) {
				$where = 'trashed=1';
				break;
			}
		case 'draft':
			if ($this->is_manager) {
				$where .= " AND project_status.action_code='0'";
				break;
			}
		default:
			if ($f = intval($_REQUEST['filter'])) {
				$where .= " AND project_status.action_code='$f'";
			} else {
				$this->filter = 'all'; // all or invalid
			}
			break;
	}
}

$where .= ' AND '.tfk_user::get_roles_sql('who_read');

$this->data = new tfk_project_info();
$this->data->load_list(
	array(
		'where'		=> $where,
		'order'		=> 'project_status_action_code ASC, project_id DESC',
		'page_size'	=> 10,
		'page'		=> empty($_REQUEST['pg'])?1:$_REQUEST['pg']
	)
);

wp_register_style( 'tznadmincss', plugins_url('css/admin.css', TFK_ROOT_FILE));
wp_enqueue_style( 'tznadmincss' );

$this->view('admin/project_list.php');