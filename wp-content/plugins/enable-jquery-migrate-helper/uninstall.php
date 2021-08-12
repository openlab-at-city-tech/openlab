<?php
/**
 * jQuery_Migrate_Helper uninstall methods
 */

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'jqmh_logs' );
delete_option( 'jqmh_last_log_time' );
delete_option( '_jquery_migrate_dismissed_notice' );
delete_option( '_jquery_migrate_downgrade_version' );
delete_option( '_jquery_migrate_modern_deprecations' );
delete_option( '_jquery_migrate_public_deprecation_logging' );
delete_option( '_jquery_migrate_deprecations_dismissed_notice' );
delete_option( '_jquery_migrate_previous_deprecations_dismissed_notice' );
delete_option( '_jquery_migrate_has_auto_downgraded' );

if ( wp_next_scheduled( 'enable_jquery_migrate_helper_notification' ) ) {
	wp_clear_scheduled_hook( 'enable_jquery_migrate_helper_notification' );
}