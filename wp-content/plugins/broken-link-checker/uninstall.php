<?php
/**
 * @author Janis Elsts
 * @copyright 2010
 *
 * The terrifying uninstallation script.
 */

if ( defined( 'ABSPATH' ) && defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	// Remove V2 options.
	delete_option( 'blc_settings' );
	delete_option( 'wpmudev_blc_reviewed' );

	// Remove the plugin's settings & installation log.
	delete_option( 'wsblc_options' );
	delete_option( 'blc_activation_enabled' );
	delete_option( 'blc_installation_log' );

	// Remove the database tables.
	$mywpdb = $GLOBALS['wpdb'];
	if ( isset( $mywpdb ) ) {
		// EXTERMINATE!
		$mywpdb->query( "DROP TABLE IF EXISTS {$mywpdb->prefix}blc_linkdata, {$mywpdb->prefix}blc_postdata, {$mywpdb->prefix}blc_instances, {$mywpdb->prefix}blc_links, {$mywpdb->prefix}blc_synch, {$mywpdb->prefix}blc_filters" );
	}
}
