<?php
if (!defined('ABSPATH')) exit;  // if direct access

class class_breadcrumb_settings
{

	public function __construct()
	{

		add_action('admin_menu', array($this, 'breadcrumb_menu_init'), 12);
	}


	public function breadcrumb_settings()
	{
		include('menu/settings.php');
	}


	public	function breadcrumb_menu_init()
	{

		$breadcrumb_info = get_option('breadcrumb_info');

		$v1_5_39 = isset($breadcrumb_info['v1_5_39']) ? $breadcrumb_info['v1_5_39'] : 'no';


		// add_submenu_page('tools.php', __('Breadcrumb', 'breadcrumb'), __('Breadcrumb', 'breadcrumb'), 'manage_options', 'breadcrumb', array($this, 'dashboard'));


		add_menu_page(__('Breadcrumb', 'breadcrumb'), __('Breadcrumb', 'breadcrumb'), 'manage_options', 'breadcrumb_settings', array($this, 'breadcrumb_settings'), breadcrumb_plugin_url . 'assets/admin/images/right-arrow.png');
	}

	public function data_update()
	{
		include(breadcrumb_plugin_dir . 'includes/menu/data-update.php');
	}

	public function dashboard()
	{
		//include(breadcrumb_plugin_dir . 'includes/menu/dashboard.php');

		//include('menu/dashboard.php');
	}
}

new class_breadcrumb_settings();
