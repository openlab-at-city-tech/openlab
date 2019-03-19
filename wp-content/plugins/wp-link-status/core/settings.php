<?php

/**
 * Settings class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Settings {



	/**
	 * Return a numeric setting
	 */
	public static function get_nsetting($name, $value = 0) {

		// Load settings
		static $settings;
		if (!isset($settings)) {
			$settings = self::get_default_nsettings();
		}

		// Check available
		if (!isset($settings[$name])) {
			return false;
		}

		// Check boundary
		if ('min' === $value || 'max' === $value) {
			return $settings[$name][$value];
		}

		// Check input value
		$value = (int) $value;
		if (empty($value)) {
			$value = (int) get_option('wplnst_'.$name);
		}

		// Check return value
		$setting = $settings[$name];
		return (empty($value) || $value < $setting['min'] || $value > $setting['max'])? $setting['default'] : $value;
	}



	/**
	 * Return an array of default numeric settings
	 */
	private static function get_default_nsettings() {
		return array(
			'max_threads' 		=> array('min' => 1, 	'max' => 999, 	'default' => 1),
			'max_scans'			=> array('min' => 1, 	'max' => 999, 	'default' => 1),
			'max_pack'			=> array('min' => 1, 	'max' => 999, 	'default' => 25),
			'max_requests'		=> array('min' => 1, 	'max' => 999, 	'default' => 3),
			'max_redirs'		=> array('min' => 1, 	'max' => 999, 	'default' => 5),
			'max_download'		=> array('min' => 32, 	'max' => 10240, 'default' => 2048),
			'connect_timeout' 	=> array('min' => 1, 	'max' => 999, 	'default' => 10),
			'request_timeout' 	=> array('min' => 1, 	'max' => 999, 	'default' => 30),
			'extra_timeout' 	=> array('min' => 5, 	'max' => 999, 	'default' => 5),
			'crawler_alive'		=> array('min' => 3, 	'max' => 999, 	'default' => 30),
			'total_objects'		=> array('min' => 30, 	'max' => 300, 	'default' => 120),
			'summary_status'	=> array('min' => 10, 	'max' => 999, 	'default' => 30),
			'summary_phases'	=> array('min' => 10, 	'max' => 999, 	'default' => 30),
			'summary_objects'	=> array('min' => 10, 	'max' => 999, 	'default' => 30),
			'recursion_limit' 	=> array('min' => 10, 	'max' => 99999, 'default' => 99),
		);
	}



	/**
	 * Return a boolean setting
	 */
	public static function get_bsetting($name, $use_default_if_empty = true) {

		// Check stored value
		$value = ''.get_option('wplnst_'.$name);
		if ('' !== $value || !$use_default_if_empty) {
			return ('on' == $value);
		}

		// Load settings
		static $settings;
		if (!isset($settings)) {
			$settings = self::get_default_bsettings();
		}

		// Return default setting or false
		return isset($settings[$name])? ('on' == $settings[$name]) : false;
	}



	/**
	 * Return an array of default boolean settings
	 */
	private static function get_default_bsettings() {
		return array(
			'mysql_calc_rows' => 'off',
			'uninstall_data'  => 'off',
		);
	}



	/**
	 * Return a text setting
	 */
	public static function get_tsetting($name, $use_default_if_empty = true) {

		// Check stored value
		$value = ''.get_option('wplnst_'.$name);
		if ('' !== $value || !$use_default_if_empty) {
			return $value;
		}

		// Load settings
		static $settings;
		if (!isset($settings)) {
			$settings = self::get_default_tsettings();
		}

		// Return default setting or false
		return isset($settings[$name])? $settings[$name] : false;
	}



	/**
	 * Return an array of default string settings
	 */
	private static function get_default_tsettings() {
		return array(
			'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0',
		);
	}



	/**
	 * Remove all plugin options
	 */
	public static function delete_all_options() {

		// Collect numeric, text, and crawler options
		$options = array_merge(
			array_keys(self::get_default_nsettings()),
			array_keys(self::get_default_tsettings()),
			array_keys(self::get_default_bsettings()),
			self::get_crawler_options_names()
		);

		// Remove all plugin settings
		foreach ($options as $name) {
			delete_option('wplnst_'.$name);
		}
	}



	/**
	 * Remove custom options
	 */
	public static function delete_crawler_options() {
		$options = self::get_crawler_options_names();
		foreach ($options as $name) {
			delete_option('wplnst_'.$name);
		}
	}



	/**
	 * Retrive custom options names
	 */
	private static function get_crawler_options_names() {
		return array('crawler_timestamp', 'crawler_slug', 'crawler_notifications');
	}



}