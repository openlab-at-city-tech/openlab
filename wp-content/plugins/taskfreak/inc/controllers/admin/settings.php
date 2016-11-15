<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

TFWP settings
	
*/

$this->options = get_option('tfk_options');

// for compat with versions prior to 1.1.0
if (!isset($this->options['prio_size']))
    $this->options['prio_size'] = 0;

$this->message = '';

if (isset($_POST['opt_save']) && check_admin_referer('tfk_settings')) {
	// save options
	$this->options['tfk_all'] = $_POST['tfk_all'];
	$this->options['tasks_per_page'] = $_POST['tasks_per_page'];
	$this->options['number_updates'] = $_POST['number_updates'];
	$this->options['avatar_size'] = $_POST['avatar_size'];
	$this->options['format_date'] = $_POST['format_date'];
	$this->options['format_time'] = $_POST['format_time'];
	$this->options['proximity'] = intval($_POST['proximity']);
	$this->options['prio_size'] = intval($_POST['prio_size']);
	$this->options['access_read'] = $_POST['access_read'];
	$this->options['access_comment'] = $_POST['access_comment'];
	$this->options['access_post'] = $_POST['access_post'];
	$this->options['access_manage'] = $_POST['access_manage'];
	$this->options['comment_upload'] = $_POST['comment_upload'];
	
	if (update_option('tfk_options', $this->options)) {
		$this->message = __('Changes saved', 'taskfreak');
	} else {
		$this->message = __('No changes saved', 'taskfreak');
	}
	
}

$this->view('admin/settings.php');
