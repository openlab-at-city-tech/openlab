<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu + mount-div for the Extensions React app. Ported from
 * Modula_Admin::register_submenus()/add_extensions_react_root().
 */
class DLM_Extensions_Page {

	public function __construct() {
		add_filter( 'dlm_admin_menu_links', array( $this, 'extensions_pages' ), 30 );
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function extensions_pages( $links ) {
		$links[] = array(
			'page_title' => __( 'Download Monitor Extensions', 'download-monitor' ),
			'menu_title' => '<span style="color:#419CCB;font-weight:bold;">' . __( 'Extensions', 'download-monitor' ) . '</span>',
			'capability' => 'manage_options',
			'menu_slug'  => 'dlm-extensions',
			'function'   => array( $this, 'render_react_app' ),
			'priority'   => 50,
		);

		return $links;
	}

	public function render_react_app() {
		echo '<div id="dlm-extensions-app"></div>';
	}
}
