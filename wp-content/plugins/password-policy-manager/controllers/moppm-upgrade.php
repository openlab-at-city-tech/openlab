<?php
/**
 * File to include moppm-upgrade.php file
 *
 * @package password-policy-manager/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $moppm_dirname;
require_once $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'moppm-upgrade.php';
update_site_option( 'moppm_pricing_page_visitor', time() );
