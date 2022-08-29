<?php 
namespace ElementsKit_Lite\Modules\Dynamic_Content;

defined( 'ABSPATH' ) || exit;

class Init {

	public static function get_url() {
		return \ElementsKit_Lite::module_url() . 'dynamic-content/';
	}
	public static function get_dir() {
		return \ElementsKit_Lite::module_dir() . 'dynamic-content/';
	}

	public function __construct() {

		// Includes necessary files
		$this->include_files();
	}

	private function include_files() {
		// Controls_Manager
		include_once self::get_dir() . 'cpt.php';
		include_once self::get_dir() . 'cpt-api.php';
	}
}
