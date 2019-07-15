<?php
/**
 * Main Multipage Admin Class.
 *
 * @package Multipage
 * @subpackage CoreAdministration
 * @since 1.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup Multipage Admin.
 *
 * @since 1.4
 *
 */
function mpp_admin() {
	multipage()->admin = new MPP_Admin();
	return;
}