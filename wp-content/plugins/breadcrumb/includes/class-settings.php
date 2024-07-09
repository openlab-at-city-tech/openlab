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




		add_menu_page(__('Breadcrumb', 'breadcrumb'), __('Breadcrumb', 'breadcrumb'), 'manage_options', 'breadcrumb_settings', array($this, 'breadcrumb_settings'), breadcrumb_plugin_url . 'assets/admin/images/right-arrow.png');

		if ($v1_5_39 != 'yes') {
			add_submenu_page('breadcrumb_settings', 'Data Update', 'Data Update', 'manage_options', 'breadcrumb-data-update', array($this, 'data_update'));
		}
	}

	public function data_update()
	{
		include(breadcrumb_plugin_dir . 'includes/menu/data-update.php');
	}
}

new class_breadcrumb_settings();
