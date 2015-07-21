<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

Create or edit project

*/

$this->pid = intval($_REQUEST['id']);

$this->saveok = false;
$this->saverror = false;

// load project
$this->data = new tfk_project();
if ($this->pid) {
	$this->data->set_uid($this->pid);
	if (!$this->data->load()) {
		$this->pid = 0;
	}
}

if (!$this->pid) {
	// create new project, set defaults
	$this->data->init_rights($this->options);
	
}

// modify project (on submit)
if (!empty($_REQUEST['action'])) {

	// check user right (admin and editors only)
	if (!$this->is_manager) {
		die('Security: only Administrators and Editors can do that');
	}

	// check nonce
	check_admin_referer('tfk_project_'.$_REQUEST['action']);

	if ($_REQUEST['action'] == 'trash') {
		// move to trash
		$this->data->set('trashed',1);
		$this->data->update();
		wp_redirect(admin_url().'admin.php?page=taskfreak_projects');
		exit;
	}
	if ($_REQUEST['action'] == 'restore') {
		// move to trash
		$this->data->set('trashed',0);
		$this->data->update();
		wp_redirect(admin_url().'admin.php?page=taskfreak_projects&filter=trash');
		exit;
	}
	if ($_REQUEST['action'] == 'delete') {
		// delete permanently
		$this->data->delete();
		wp_redirect(admin_url().'admin.php?page=taskfreak_projects&filter=trash');
		exit;
	}
	
	if ($_POST['action'] == 'save') {
		// clean POST values
		$_POST = stripslashes_deep($_POST);
		// set object properties
		$this->data->set_auto($_POST);
		// check 'n save
		if ($this->data->check()) {
			if ($this->pid) {
				$this->data->update();
				if ($_POST['project_status_new'] != $_POST['project_status_old']) {
					$this->data->set_status($_POST['project_status_new']);
				}
			} else {
				$this->data->set('creation_date', 'NOW');
				$this->pid = $this->data->insert();
				$this->data->set_status($_POST['project_status_new'], null, 'creation');
			}
			
			// notify user : project saved
			$this->saveok = true;
		} else {
			// notify user : error on form
			$this->saverror = true;
		}
	}
}

// load status history
$this->status = new tfk_project_status();
if ($this->pid) {
	$this->status->load_list(array(
		'where' => 'project_id='.$this->pid,
		'order'	=> 'log_date ASC'
	));
}

//  No need to redirect, show headers now
if (isset($_REQUEST['noheader'])) {
    require_once(ABSPATH . 'wp-admin/admin-header.php');
}

$this->baselink = add_query_arg('id',($this->pid?$this->pid:'new'), $this->linkadmin);

// prepare view
add_thickbox();

wp_enqueue_script('post');		

wp_register_script('tznadminjs', plugins_url( '/js/admin.js' , TFK_ROOT_FILE), array('jquery') );
wp_enqueue_script('tznadminjs');

wp_register_style( 'tznadmincss', plugins_url('css/admin.css', TFK_ROOT_FILE));
wp_enqueue_style( 'tznadmincss' );

if ( function_exists('wp_is_mobile') && wp_is_mobile() ) {
	wp_enqueue_script( 'jquery-touch-punch' );
}
$this->view('admin/project_edit.php');