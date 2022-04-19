<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Base\BaseController;

class SettingsLinks {

	public function register() {
		add_filter( 'plugin_action_links_' . ZPM_PLUGIN, array( $this, 'settings_links' ) );
	}

	public function settings_links( $links ) {
		$settings_link = sprintf( __( '%s Settings %s', 'zephyr-project-manager' ), '<a href="admin.php?page=zephyr_project_manager">', '</a>' );
		$purchase_link = sprintf( __( '%s Purchase Premium %s', 'zephyr-project-manager' ), '<a href="https://zephyr-one.com/purchase-pro">', '</a>' );
		array_push( $links, $settings_link );
		if (!BaseController::is_pro()) {
			array_push( $links, $purchase_link );
		}
		return $links;
	}
}