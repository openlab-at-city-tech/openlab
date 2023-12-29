<?php
/**
 * The Read meter Sub-menu Display.
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

/**
 * Add submenu of Global settings Page to admin menu.
 *
 * @since  1.0.0
 * @return void
 */
function bsf_rt_settings_page() {
	add_submenu_page(
		'options-general.php',
		'Read Meter',
		'Read Meter',
		'manage_options',
		'bsf_rt',
		'bsf_rt_page_html'
	);
}
add_action( 'admin_menu', 'bsf_rt_settings_page' );

/**
 * Main Frontpage.
 *
 * @since  1.0.0
 * @return void
 */
function bsf_rt_page_html() {
	require_once BSF_RT_ABSPATH . 'includes/bsf-rt-main-frontend.php';
}
