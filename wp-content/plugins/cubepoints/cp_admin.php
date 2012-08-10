<?php
/**
 * CubePoints admin pages
 */

/** Admin pages */
function cp_admin() {
	add_menu_page('CubePoints', 'CubePoints', 'manage_options', 'cp_admin_manage', 'cp_admin_manage');
	add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('Manage','cp'), __('Manage','cp'), 'manage_options', 'cp_admin_manage', 'cp_admin_manage');
	add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('Award Points','cp'), __('Add Points','cp'), 'manage_options', 'cp_admin_add_points', 'cp_admin_add_points');
	add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('Configure','cp'), __('Configure','cp'), 'manage_options', 'cp_admin_config', 'cp_admin_config');
	add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('Logs','cp'), __('Logs','cp'), 'manage_options', 'cp_admin_logs', 'cp_admin_logs');
	do_action('cp_admin_pages');
	add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('Modules','cp'), __('Modules','cp'), 'manage_options', 'cp_admin_modules', 'cp_admin_modules');
}

/** Include admin pages */
	require_once('cp_admin_manage.php');
	require_once('cp_admin_add_points.php');
	require_once('cp_admin_config.php');
	require_once('cp_admin_logs.php');
	require_once('cp_admin_modules.php');

/** Hook for admin pages */
add_action('admin_menu', 'cp_admin');

add_action('admin_enqueue_scripts', 'cp_admin_register_scripts');
function cp_admin_register_scripts() {
	/** Register datatables script and stylesheet for admin pages */
	wp_register_script('datatables',
		   CP_PATH . 'assets/datatables/js/jquery.dataTables.min.js',
		   array('jquery'),
		   '1.7.4' );
	wp_register_style('datatables', CP_PATH . 'assets/datatables/css/style.css');
	
	/** Register autocomplete script and stylesheet for admin pages */
	wp_register_script('autocomplete',
		   CP_PATH . 'assets/autocomplete/jquery.autocomplete.js',
		   array('jquery'),
		   '3.2.2' );
	wp_register_style('autocomplete', CP_PATH . 'assets/autocomplete/jquery.autocomplete.css');
}

/** Enqueue datatables */
function cp_datatables_script(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('datatables');
}
function cp_datatables_style(){
	wp_enqueue_style('datatables');
}
add_action('admin_print_scripts-toplevel_page_cp_admin_manage', 'cp_datatables_script');
add_action('admin_print_styles-toplevel_page_cp_admin_manage', 'cp_datatables_style');
add_action('admin_print_scripts-cubepoints_page_cp_admin_logs', 'cp_datatables_script');
add_action('admin_print_styles-cubepoints_page_cp_admin_logs', 'cp_datatables_style');
add_action('admin_print_scripts-cubepoints_page_cp_admin_modules', 'cp_datatables_script');
add_action('admin_print_styles-cubepoints_page_cp_admin_modules', 'cp_datatables_style');

/** Enqueue autocomplete */
function cp_autocomplete_script(){
	wp_enqueue_script('autocomplete');
}
function cp_autocomplete_style(){
	wp_enqueue_style('autocomplete');
}
add_action('admin_print_scripts-cubepoints_page_cp_admin_add_points', 'cp_autocomplete_script');
add_action('admin_print_styles-cubepoints_page_cp_admin_add_points', 'cp_autocomplete_style');

?>
