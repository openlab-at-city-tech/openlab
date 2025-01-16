<?php

/**
 * Activate this plugin i.e. setup tables, data etc.
 * NOT invoked on plugin updates
 *
 * @param bool $network_wide - If the plugin is being network-activated
 */
function epkb_activate_plugin( $network_wide=false ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			epkb_get_instance()->kb_config_obj->reset_cache();
			epkb_activate_plugin_do();
			restore_current_blog();
		}
	} else {
		epkb_activate_plugin_do();
	}
}
register_activation_hook( Echo_Knowledge_Base::$plugin_file, 'epkb_activate_plugin' );

function epkb_activate_plugin_do() {

	// true if the plugin was activated for the first time since installation
	$plugin_version = get_option( 'epkb_version' );
	if ( empty( $plugin_version ) ) {

		set_transient( '_epkb_plugin_installed', true, 3600 );
		EPKB_Core_Utilities::add_kb_flag( 'epkb_run_setup' );

		// prepare KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_config['upgrade_plugin_version'] = Echo_Knowledge_Base::$version;   // TODO 2025 remove and in the specs
		$kb_config['first_plugin_version'] = Echo_Knowledge_Base::$version;     // TODO 2025 remove and in the specs
		epkb_get_instance()->kb_config_obj->update_kb_configuration( EPKB_KB_Config_DB::DEFAULT_KB_ID, $kb_config );

		// update KB versions
		EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version );
	}

	set_transient( '_epkb_plugin_activated', true, 3600 );

	// Clear permalinks
	update_option( 'epkb_flush_rewrite_rules', true );
	set_transient( '_epkb_faqs_flush_rewrite_rules', true, 3600 );
	flush_rewrite_rules( false );
}

/**
 * User deactivates this plugin so refresh the permalinks
 */
function epkb_deactivation() {

	// Clear the permalinks to remove our post type's rules
	flush_rewrite_rules( false );

}
register_deactivation_hook( Echo_Knowledge_Base::$plugin_file, 'epkb_deactivation' );
