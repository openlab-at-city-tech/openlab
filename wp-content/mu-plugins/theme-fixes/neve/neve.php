<?php

/**
 * Remove 'neve_preview_hook' item from the toolbar.
 */
function remove_neve_preview_hook() {
	global $wp_admin_bar;

	// Check if the 'neve_preview_hook' item exists in the toolbar
	if ( $wp_admin_bar->get_node( 'neve_preview_hook' ) ) {
		// Remove the 'neve_preview_hook' item
		$wp_admin_bar->remove_node( 'neve_preview_hook' );
	}
}
add_action( 'admin_bar_menu', 'remove_neve_preview_hook', 999 );
