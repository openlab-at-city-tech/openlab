<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

View project

*/

$this->pid = intval($_REQUEST['id']);

// load project
$this->data = new tfk_project();
if ($this->pid) {
	$this->data->set_uid($this->pid);
	if (!$this->data->load()) {
		$this->pid = 0;
	}
}

// TODO, view project details and list users

// TEMPORARY : redirect to page in front office, or list if not possible

if (headers_sent()) {
	$this->call('admin/project_list.php');
} else {
	wp_redirect(add_query_arg(array(
		'mode'		=> 'projects',
		'proj'		=> $this->pid
	), $this->linkfront));
	exit();
}