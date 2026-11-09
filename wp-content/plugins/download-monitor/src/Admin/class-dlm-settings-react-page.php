<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu + mount-div for the React Settings page.
 */
class DLM_Settings_React_Page {

	public function __construct() {
		add_filter( 'dlm_admin_menu_links', array( $this, 'settings_pages' ), 20 );
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function settings_pages( $links ) {
		$links[] = array(
			'page_title' => __( 'Settings', 'download-monitor' ),
			'menu_title' => __( 'Settings', 'download-monitor' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'download-monitor-settings',
			'function'   => array( $this, 'render_react_app' ),
			'priority'   => 20,
		);

		return $links;
	}

	public function render_react_app() {
		echo '<div id="dlm-settings-app"></div>';
	}
}
