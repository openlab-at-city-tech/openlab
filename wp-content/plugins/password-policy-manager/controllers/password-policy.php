<?php
/**
 * File to include password-policy.php file
 *
 * @package password-policy-manager/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $moppm_dirname;
require_once $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'password-policy.php';
