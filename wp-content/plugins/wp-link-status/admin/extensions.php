<?php

/**
 * Admin Extensions class
 *
 * @package WP Link Status
 * @subpackage Admin
 */
class WPLNST_Admin_Extensions {



	/**
	 * Constructor
	 */
	public function __construct(&$admin) {

		// Custom action view
		add_action('wplnst_view_extensions', array(&$this, 'view_extensions'));

		// Show settings screen
		$admin->screen_view(array(
			'title' 	=> __('Extensions', 'wplnst'),
			'wp_action'	=> 'wplnst_view_extensions',
			'action' 	=> WPLNST_Core_Plugin::get_url_extensions(),
		));
	}



	/**
	 * Extension view for settings page
	 */
	public function view_extensions($args) {
		wplnst_require('views', 'extensions');
		WPLNST_Views_Extensions::view($args);
	}



}