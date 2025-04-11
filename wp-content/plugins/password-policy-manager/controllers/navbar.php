<?php
/**
 * File to include navbar.php
 *
 * @package password-policy-manager/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $moppm_utility,$moppm_dirname;
$logo_url          = plugin_dir_url( dirname( __FILE__ ) ) . 'includes/images/miniorange_logo.png';
$active_tab        = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading GET parameter from the URL for checking the page name, doesn't require nonce verification.
$configuration_url = isset( $_SERVER['REQUEST_URI'] ) ? add_query_arg( array( 'page' => 'moppm' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
$addon_url         = isset( $_SERVER['REQUEST_URI'] ) ? add_query_arg( array( 'page' => 'moppm_addons' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
$report_url        = isset( $_SERVER['REQUEST_URI'] ) ? add_query_arg( array( 'page' => 'moppm_reports' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
$registration_url  = isset( $_SERVER['REQUEST_URI'] ) ? add_query_arg( array( 'page' => 'moppm_registration_form' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
$upgrade_url       = isset( $_SERVER['REQUEST_URI'] ) ? add_query_arg( array( 'page' => 'moppm_upgrade' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
$account_url       = isset( $_SERVER['REQUEST_URI'] ) ? add_query_arg( array( 'page' => 'moppm_account' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
$advertise_url     = isset( $_SERVER['REQUEST_URI'] ) ? add_query_arg( array( 'page' => 'moppm_advertise' ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
require_once $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'navbar.php';

