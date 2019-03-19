<?php

/**
 * Module class
 *
 * @package WP Link Status
 * @subpackage Core
 */
abstract class WPLNST_Core_Module {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Scans object
	 */
	public $scans;



	/**
	 * URL object
	 */
	public $url;



	/**
	 * Singleton objects
	 */
	private static $instances = array();



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Private constructor, call plugin custom method
	 */
	private function __construct($args = null) {
		$this->on_construct($args);
	}



	/**
	 * Creates a singleton object instance
	 * Avoided use of get_called_class for PHP < 5.3 compatibility
	 */
	protected static function get_instance($classname, $args = null) {
		if (!isset(self::$instances[$classname])) {
			self::$instances[$classname] = new $classname($args);
		}
		return self::$instances[$classname];
	}



	/**
	 * Do nothing, to override
	 */
	protected function on_construct($args = null) {}



	// Plugins objects load
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Return scans object
	 */
	public function load_scans_object() {
		if (!isset($this->scans)) {
			wplnst_require('core', 'scans');
			$this->scans = new WPLNST_Core_Scans();
		}
	}



	/**
	 * Return URL object
	 */
	public function load_url_object() {
		if (!isset($this->urlo)) {
			wplnst_require('core', 'url');
			$this->urlo = new WPLNST_Core_URL();
		}
	}



	// Wrapper methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Wrapper method of WPLNST_Core_Scans class
	 */
	public function get_scans($args) {
		$this->load_scans_object();
		return $this->scans->get_scans($args);
	}



	/**
	 * Wrapper method of WPLNST_Core_Scans class
	 */
	public function get_scan_by_id($scan_id, $setup_names = false, $no_cache = false) {
		$this->load_scans_object();
		return $this->scans->get_scan_by_id($scan_id, $setup_names, $no_cache);
	}



	// AJAX wrappers
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check and initialize ajax respose
	 */
	protected static function check_ajax_submit(&$response, $capability, $nonce = null) {

		// Check default output
		if (!isset($response)) {
			$response = self::default_ajax_response($nonce);
		}

		// Check user capabilities
		if (!current_user_can($capability)) {
			$response['status'] = 'error';
			$response['reason'] = __('Sorry, current user can`t perform this action', 'wplnst');
			return false;

		// Check if submitted nonce matches with the generated nonce we created earlier
		} elseif (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], isset($nonce)? $nonce : __FILE__)) {
			$response['status'] = 'error';
			$response['reason'] = __('Sorry, security verification error. Please reload this page and try again.', 'wplnst');
			return false;
		}

		// Submit Ok
		return true;
	}



	/**
	 * Return array of ajax response
	 */
	protected static function default_ajax_response($nonce = null) {
		return array(
			'status' => 'ok',
			'reason' => '',
			'nonce' => 	(isset($nonce) && false === $nonce)? '' : wp_create_nonce(isset($nonce)? $nonce : __FILE__),
			'data' => 	array(),
		);
	}



	/**
	 * Custom error ajax response
	 */
	protected static function error_ajax_response($reason, $nonce = false) {
		$response = self::default_ajax_response($nonce);
		$response['status'] = 'error';
		$response['reason'] = $reason;
		self::output_ajax_response($response);
	}



	/**
	 * Output AJAX in JSON format and exit
	 */
	protected static function output_ajax_response($response) {
		@header('Content-Type: application/json');
		die(@json_encode($response));
	}



}