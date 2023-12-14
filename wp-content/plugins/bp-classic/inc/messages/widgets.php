<?php
/**
 * BP Classic Messages Widget Functions.
 *
 * @package bp-classic\inc\messages
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Sitewide Notices Legacy Widget.
 *
 * @since 1.0.0
 */
function bp_classic_messages_register_sitewide_notices_widget() {
	register_widget( 'BP_Classic_Messages_Sitewide_Notices_Widget' );
}

/**
 * Register widgets for the Messages component.
 *
 * @since 1.0.0
 */
function bp_classic_messages_register_widgets() {
	add_action( 'widgets_init', 'bp_classic_messages_register_sitewide_notices_widget' );
}
add_action( 'bp_register_widgets', 'bp_classic_messages_register_widgets' );
