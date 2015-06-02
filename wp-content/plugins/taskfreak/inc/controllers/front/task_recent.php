<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

List recently updated tasks

*/

$this->baselink = add_query_arg('mode', 'recent', tzn_tools::baselink());

$this->log = new tfk_item_status_info();

switch ($this->filters['filter_recent']) {
	case 'tasks':
		$filter = 'log.item_id <> 0 AND log.comment_id = 0'; 
		break;
	case 'projects':
		$filter = 'log.project_id <> 0'; 
		break;
	case 'comments':
		$filter = 'log.comment_id <> 0';
		break;
	default:
		$filter = '';
}

if ($this->options['number_updates']) {
	$date_delta = $this->options['number_updates'];
} else {
	$date_delta = 7;
}

$this->log->load_list(
		array(
			'where' => 'DATEDIFF(NOW(), log_date) < '.$date_delta.
						($filter ? ' AND '.$filter : ''),
		)
);

$this->view('front/recent.php');