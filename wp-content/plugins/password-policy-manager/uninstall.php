<?php
/**
 * File for deleting plugin related options and database tables.
 *
 * @package    password-policy-manager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

	delete_site_option( 'moppm_expiration_time' );
	delete_site_option( 'moppm_Numeric_digit' );
	delete_site_option( 'moppm_letter' );
	delete_site_option( 'moppm_digit' );
	delete_site_option( 'moppm_special_char' );
	delete_site_option( 'Moppm_enable_disable_ppm' );
	delete_site_option( 'moppm_enable_disable_report' );
	delete_site_option( 'moppm_planname' );
	delete_site_option( 'moppm_plantype' );
	delete_site_option( 'moppm_disable_forget' );
	delete_site_option( 'moppm_activated_time' );
	delete_site_option( 'moppm_dbversion' );
	delete_site_option( 'moppm_first_reset' );
	// drop custom db tables.
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}moppm_user_login_info" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery , WordPress.DB.DirectDatabaseQuery.NoCaching , WordPress.DB.DirectDatabaseQuery.SchemaChange -- droping the database table on uninstallation of the plugin, wpdb required here and catching is not required here.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}moppm_user_report_table" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery , WordPress.DB.DirectDatabaseQuery.NoCaching , WordPress.DB.DirectDatabaseQuery.SchemaChange -- droping the database table on uninstallation of the plugin, wpdb required here and catching is not required here.
	$users = get_users();
if ( ! empty( $users ) ) {
	foreach ( $users as $user ) {
		delete_user_meta( $user->ID, 'moppm_points' );
		delete_user_meta( $user->ID, 'moppm_pass_score' );
		delete_user_meta( $user->ID, 'moppm_first_reset' );
	}
}
	$moppm_attempt = base64_encode( 'moppm_no_of_attempt' ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- not used for obfuscation.
	delete_site_option( $moppm_attempt );
	delete_site_option( 'moppm_enable_disable_expiry' );

