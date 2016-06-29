<?php
class admin_page_class
{	
	static function generate_admin_page()
	{	
		include('admin_page.php');
	}	
	static function add_menu_item()
	{
		add_submenu_page(
		'options-general.php',
		'wp-api',
		'wp-api',
		'manage_options',
		'wp-api',
		'admin_page_class::generate_admin_page'
		 );
	}
}
?>