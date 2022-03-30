<?php

// Zephyr helper and resuable functions

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Core\Utillities;
use Inc\ZephyrProjectManager;
use Inc\Core\Tasks;
use Inc\Zephyr;

function zpm_add_scheduled_events() {
	add_action( 'zpm_daily_processes', 'zpm_daily_scheduled_tasks' );
	date_default_timezone_set('UTC');
	$time = strtotime('00:00:00');
	$recurrence = 'daily';
	$hook = 'zpm_daily_processes';
	if ( !wp_next_scheduled( $hook ) ) {
		wp_schedule_event( $time, $recurrence, $hook);
	}
}

function zpm_daily_scheduled_tasks() {
	$manager = ZephyrProjectManager();
	$tasks = $manager::get_tasks();

	foreach ($tasks as $task) {
		Tasks::recur_task( $task );
	}
}

function zpm_array_to_comma_string( $array ) {
	$string = '';

	if ( is_array( $array ) ) {
		$string = '';
		foreach ($array as $key => $value) {
			$string .= $value;

			if ($key !== ( sizeof( $array ) - 1 ) ) {
				$string .= ',';
			}
		}
	}

	return $string;
}

function zpm_get_colors() {
	$general_settings = Utillities::general_settings();
	$primary = $general_settings['primary_color'];
	$primary_light = $general_settings['primary_color_light'];
	$primary_shifted = Utillities::adjust_brightness( $primary, -40 );
	$primary_dark = $general_settings['primary_color_dark'];
	$primary_dark_adjust = Utillities::adjust_brightness( $primary, -40 );

	$colors = array(
		'primary' => $primary,
		'primary_light' => $primary_light,
		'primary_dark' => $primary_shifted,
		'secondary' => $primary_dark,
		'secondary_dark' => $primary_dark_adjust
	);

	return $colors;
}

function zpm_get_primary_color() {
	$colors = zpm_get_colors();
	$primary = $colors['primary'];
	if (function_exists('zpm_get_primary_frontend_color')) {
		$primary = zpm_get_primary_frontend_color();
	}
	return $primary;
}

function zpm_user_has_role( $user_id, $role ) {
	$u = new WP_User( $user_id );

	if ( in_array($role, (array) $u->roles) ) {
		return true;
	}

	return false;
}

function zpm_get_pages() {
	$zpm_pages = array(
		'zephyr_project_manager',
		'zephyr_project_manager_tasks',
		'zephyr_project_manager_files',
		'zephyr_project_manager_activity',
		'zephyr_project_manager_progress',
		'zephyr_project_manager_calendar',
		'zephyr_project_manager_settings',
		'zephyr_project_manager_projects',
		'zephyr_project_manager_categories',
		'zephyr_project_manager_teams_members',
		'zephyr_project_manager_asana',
		'zephyr_project_manager_reports',
		'zephyr_project_manager_custom_fields',
		'zephyr_project_manager_purchase_premium',
		'zephyr_project_manager_asana_settings',
		'zephyr_project_manager_devices',
		'zephyr_project_manager_help',
		'zephyr_project_manager_extensions',
		'zephyr_project_manager_milestones',
		'zephyr_project_manager_kanban'
	);

	$zpm_pages = apply_filters( 'zpm_hide_notice_pages', $zpm_pages );
	return $zpm_pages;
}

function isZephyrPage() {
	$pages = zpm_get_pages();

	if (isset($_REQUEST['page'])) {
		if (in_array($_REQUEST['page'], $pages)) {
			return true;
		}
	}
	
	return false;
}

function zpmIsProjectsPage() {

	if (isset($_REQUEST['page'])) {
		if ($_REQUEST['page'] == 'zephyr_project_manager_projects') {
			return true;
		}
	}
	
	return false;
}

function zpmIsTasksPage() {

	if (isset($_REQUEST['page'])) {
		if ($_REQUEST['page'] == 'zephyr_project_manager_tasks') {
			return true;
		}
	}
	
	return false;
}

function ZephyrProjectManager() {
	return ZephyrProjectManager::get_instance();
}

function zpm_get_attachment_types() {
	$attachment_types = [
		'attachment'
	];
	$attachment_types = apply_filters( 'zpm_attachment_types', $attachment_types );
	return $attachment_types;
}

function zpm_get_company_name() {
	return apply_filters( 'zpm_company_name', 'Zephyr' );
}

function zpm_is_frontend() {
	if (!is_admin() || (isset($_REQUEST['frontend']) && $_REQUEST['frontend'] == true)) {
		return true;
	}
	return false;
}

function zpm_get_extensions() {
	$extensions = [
		[
			'link' => 'https://zephyr-one.com/purchase-pro/',
			'title' => 'Zephyr Project Manager Pro',
			'description' => 'Zephyr Project Manager Pro contains many new features to help with your projects including Kanban Boards, Gantt Charts, Reporting, Customizable Frontend Project Manager, Asana Integration, Custom Fields, Templates and many more useful features.
Some features include:
- Kanban style rojects
- Gantt style projects
- Customizable Frontend Project Manager
- Custom Fields
- Templates
- Asana Integration
- Reporting
- And much more...',
			'color' => '#137cc6',
			'installed' => apply_filters( 'zpm_pro_installed', false )
		],
		[
			'link' => 'https://zephyr-one.com/woocommerce-integration/',
			'title' => 'Zephyr - WooCommerce Integration',
			'description' => 'Implement Zephyr Project Manager into your WooCommerce workflow to automatically create tasks and projects when orders are placed or payments are made and simplify your workflow while keeping everything organized and on track. Some key features include:
- Choose when to create the project - when order is placed or only after payment is received
- Choose whether to create tasks when multiple items are purchased in a product
- Select agent to automatically assign the projects to
- And more...',
			'color' => '#9b5c8f',
			'installed' => apply_filters( 'zpm_woocommerce_installed', false )
		],
		[
			'link' => 'https://zephyr-one.com/google-integration/',
			'title' => 'Zephyr - Google Integration',
			'description' => 'Sync your tasks with Google Calendar and integrate with other Google products to keep everything synced and integrated to improve productivity.
Some features include:
- Google Calendar integration
- Sync your Zephyr tasks with any of your Google Calendars
- Sync different projects to different calendars
- Any changes in Zephyr are automatically updated on your Google Calendar and newly created tasks are added to the calendar instantly as well
- More Google features coming soon...',
			'color' => '#4DAA53',
			'installed' => apply_filters( 'zpm_google_installed', false )
		]
	];
	return $extensions;
}

function zpm_get_version() {
	$version = Zephyr::getPluginVersion();
	return $version;
}

function zpm_is_single_project() {
	if (isset($_GET['project']) || isset($_POST['project_id']) || isset($_REQUEST['project_id'])) {
		return true;
	} else {
		return false;
	}
}

function zpm_is_image($url) {
   $size = getimagesize($url);
   return (strtolower(substr($size['mime'], 0, 5)) == 'image' ? true : false);  
}

function zpm_get_current_task_id() {
	if (isset($_GET['task_id'])) {
		return $_GET['task_id'];
	}
	if (isset($_POST['task_id'])) {
		return $_POST['task_id'];
	}
	if (isset($_GET['action']) && isset($_GET['id'])) {
		if ($_GET['action'] == 'task') {
			return $_GET['id'];
		}
	}
	return -1;
}

function zpm_move_array_element(&$array, $a, $b) {
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
}

function zpmIsPro() {
	if (class_exists('Inc\\ZephyrProjectManager\\Plugin')) {
		return true;
	}

	return false;
}