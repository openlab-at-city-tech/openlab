<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Api\Callbacks;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Core\Projects;
use Inc\Core\Categories;
use Inc\Api\ColorPickerApi;
use Inc\Base\BaseController;

class AdminCallbacks extends Projects {

	public function adminDashboard() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/dashboard.php' );
	}

	public function adminProjects() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/projects.php' );
	}

	public function adminTasks() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/tasks.php' );
	}

	public function adminTeamsMembers() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/teams_and_members.php' );
	}

	public function adminCalendar() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/calendar.php' );
	}

	public function adminFiles() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/files.php' );
	}

	public function adminActivity() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/activity.php' );
	}

	public function adminProgress() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/progress.php' );
	}

	public function adminCategories() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/categories.php' );
	}

	public function adminSettings() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/settings.php' );
	}

	public function purchase_premium() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/premium.php' );
	}

	public function devicesPage() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/devices.php' );
	}

	public function extensionPage() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/extensions.php' );
	}

	public function ganttPage() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/gantt.php' );
	}

	public function help_page() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/help.php' );
	}

	/**
	* Loads the template file for the ZPM header
	* @param string $page_title The main title for the page
	* @param string $quickbutton_class A custom class for the QuickAction button
	*/
	public function get_header( $page_title = null, $quickbutton_class = '' ) {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/page_header.php' );
	}

	/**
	* Loads the template file for the ZPM header
	* @param string $page_title The main title for the page
	* @param string $quickbutton_class A custom class for the QuickAction button
	*/
	public function get_footer() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/page_footer.php' );
	}

	/**
	* Checks if Pro version is installed
	*/
	public static function is_pro() {
		if (class_exists('Inc\\ZephyrProjectManager\\Plugin')) {
			return true;
		}

		return false;
	}
}