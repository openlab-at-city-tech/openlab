<?php

/**
 * Prevent Sliding Door from showing plugin installation notice.
 */
function openlab_remove_sliding_door_plugin_installation_notice() {
	if ( 'sliding-door' === get_template() ) {
		remove_action( 'tgmpa_register', 'my_theme_register_required_plugins' );
	}
}
add_action( 'after_setup_theme', 'openlab_remove_sliding_door_plugin_installation_notice', 100 );

/**
 * Sliding Door requires the Page Links To plugin.
 */
function openlab_activate_page_links_to_on_sliding_door() {
	if ( 'sliding-door' !== get_template() ) {
		return;
	}

	if ( ! is_admin() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	if ( ! is_plugin_active( 'page-links-to/page-links-to.php' ) ) {
		activate_plugin( 'page-links-to/page-links-to.php' );
	}
}
add_action( 'after_setup_theme', 'openlab_activate_page_links_to_on_sliding_door', 50 );
