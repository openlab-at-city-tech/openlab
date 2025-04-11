<?php
/**
 * This file contains Constants.
 *
 * @package    password-policy-manager/helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MOPPM_Constants' ) ) {
	/**
	 * Constants class
	 */
	class MOPPM_Constants {

		const HOST_NAME            = 'https://login.xecurify.com';
		const DEFAULT_CUSTOMER_KEY = '16555';
		const DEFAULT_API_KEY      = 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';
		const DB_VERSION           = 3;
		/**
		 * Contruct function
		 */
		public function __construct() {
			$this->define_global();
		}
		/**
		 * Function to define global variables.
		 *
		 * @return void
		 */
		public function define_global() {
			global $moppm_db_queries,$moppm_utility,$moppm_dirname;
			$moppm_db_queries = new MOPPM_DATABASE();
			$moppm_dirname    = plugin_dir_path( dirname( __FILE__ ) );
		}
	}
}new MOPPM_Constants();
