<?php

/**
 * Customizations for editoria11y-accessibility-checker.
 */

/**
 * Database schema correction.
 *
 * Various situations (cloning, bad installation) can result in missing tables.
 */
add_action(
	'init',
	function() {
		if ( ! class_exists( 'Editoria11y' ) ) {
			return;
		}

		// Only run for logged-in users.
		if ( ! is_user_logged_in() ) {
			return;
		}

		global $wpdb;

		$table_urls       = $wpdb->prefix . 'ed11y_urls';
		$table_results    = $wpdb->prefix . 'ed11y_results';
		$table_dismissals = $wpdb->prefix . 'ed11y_dismissals';

		// Check for the existence of the tables.
		$suppress = $wpdb->suppress_errors();

		$urls_describe       = $wpdb->get_results( "DESCRIBE $table_urls" );
		$results_describe    = $wpdb->get_results( "DESCRIBE $table_results" );
		$dismissals_describe = $wpdb->get_results( "DESCRIBE $table_dismissals" );

		$wpdb->suppress_errors( $suppress );

		if ( ! empty( $urls_describe ) && ! empty( $results_describe ) && ! empty( $dismissals_describe ) ) {
			return;
		}

		delete_option( 'editoria11y_db_version' );
		Editoria11y::check_tables();
	}
);
