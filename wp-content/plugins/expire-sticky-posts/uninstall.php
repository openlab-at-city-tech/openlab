<?php
/**
 * Setups our metabox field for the post edit screen
 *
 * @copyright   Copyright (c) 2014, Andy von Dohren
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Remove our option from the database
delete_option( 'pw_esp_prefix' );
