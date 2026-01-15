<?php //phpcs:ignore
/**
 * Includes necessary files
 *
 * @package Patterns_Docs
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once trailingslashit( __DIR__ ) . 'class-api.php';
require_once trailingslashit( __DIR__ ) . 'class-api-install-plugin.php';
require_once trailingslashit( __DIR__ ) . 'class-api-settings.php';
