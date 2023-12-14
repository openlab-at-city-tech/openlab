<?php
/**
 * BP Classic Nouveau Functions.
 *
 * @package bp-classic\inc\nouveau
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unregister the Legacy Notices widget.
 *
 * @since 1.0.0
 */
function bp_classic_nouveau_unregister_notices_widget() {
	unregister_widget( 'BP_Classic_Messages_Sitewide_Notices_Widget' );
}

/**
 * At the `bp_init` time, the BuddyPress Component global variables are not fully set.
 *
 * @since 1.0.0
 */
function bp_classic_setup_nouveau() {
	// Register the Template pack widgets if needed.
	if ( bp_classic_retain_legacy_widgets() ) {
		add_action( 'bp_widgets_init', array( 'BP_Classic_Templates_Nouveau_Object_Nav_Widget', 'register_widget' ) );

		if ( bp_is_active( 'activity' ) ) {
			add_action( 'bp_widgets_init', array( 'BP_Classic_Templates_Nouveau_Latest_Activities', 'register_widget' ) );
		}

		if ( bp_is_active( 'messages' ) ) {
			// Notices.
			add_action( 'widgets_init', 'bp_classic_nouveau_unregister_notices_widget' );
		}
	}
}
add_action( 'bp_after_setup_theme', 'bp_classic_setup_nouveau', 5 );
