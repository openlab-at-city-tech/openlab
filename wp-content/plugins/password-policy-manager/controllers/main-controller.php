<?php
/**
 * File to include all controller files
 *
 * @package password-policy-manager/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	global $moppm_utility,$moppm_dirname;
	$controller = $moppm_dirname . 'controllers' . DIRECTORY_SEPARATOR;


if ( current_user_can( 'administrator' ) ) {
	$curr_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : null; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading GET parameter from the URL for checking the page name, doesn't require nonce verification.
	if ( ! is_null( $curr_page ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading GET parameter from the URL for checking the page name, doesn't require nonce verification.
		if ( 'moppm_upgrade' === $curr_page ) {
			include $controller . 'moppm-upgrade.php';
		} else {
			include $controller . 'navbar.php';
			echo ' <br> <table class="moppm_main_table" style="width:100%;"><tr><td class="moppm_layout" style="width:74%;">';
			switch ( $curr_page ) {
				case 'moppm_account':
					include $controller . 'account.php';
					break;
				case 'moppm_menu':
					include $controller . 'configuration.php';
					break;
				case 'moppm':
					include $controller . 'password-policy.php';
					break;
				case 'moppm_addons':
					include $controller . 'moppm-addons.php';
					break;
				case 'moppm_reports':
					include $controller . 'moppm-reports.php';
					break;
				case 'moppm_registration_form':
					include $controller . 'moppm-registration-form.php';
					break;
				case 'moppm_advertise':
					include $controller . 'moppm-advertise.php';
					break;

			}
			echo '</td><td></td><td class="moppm_support_layout" style="width:25%;vertical-align:top;">';
			include $controller . 'support.php';
			echo '</td ></tr></table>';

		}
	}
}
