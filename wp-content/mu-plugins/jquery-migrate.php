<?php

namespace CAC\JQueryMigrate;

const CUTOFF_TIMESTAMP = 1628776800; // 2021-08-12 14:00:00

add_action(
	'plugins_loaded',
	function() {
		if ( bp_is_root_blog() ) {
			return;
		}

		$current_site = get_site();

		if ( strtotime( $current_site->registered ) > CUTOFF_TIMESTAMP ) {
			return;
		}

		include_once WP_PLUGIN_DIR . '/enable-jquery-migrate-helper/class-jquery-migrate-helper.php';
		\jQuery_Migrate_Helper::init_actions();

		remove_action( 'admin_notices', [ 'jQuery_Migrate_Helper', 'admin_notices' ] );

		if ( current_user_can( 'manage_network_options' ) ) {
			return;
		}

		add_action(
			'admin_bar_menu',
			function( $wp_admin_bar ) {
				$wp_admin_bar->remove_menu( 'enable-jquery-migrate-helper' );
			},
			200
		);

		add_action(
			'admin_menu',
			function() {
				remove_submenu_page( 'tools.php', 'jqmh' );
			},
			20
		);

		// Suppress weekly reminders.
		remove_action( 'enable_jquery_migrate_helper_notification', [ 'jQuery_Migrate_Helper', 'scheduled_event_handler' ] );
	},
	20
);

// 'Live deprecations' should be set to 'no'.
add_filter( 'pre_option__jquery_migrate_public_deprecation_logging', function() { return 'yes'; } );

// 'Public deprecation logging' should be set to 'yes'.
add_filter( 'pre_option__jquery_migrate_deprecations_dismissed_notice', function() { return CUTOFF_TIMESTAMP; } );

// 'Downgrade' emails should go to network admin instead of site admin.
// We hook to 'jqmh_email_message' for just-in-time filtering.
add_filter(
	'jqmh_email_message',
	function( $message, $template ) {
		add_filter(
			'wp_mail',
			function( $args ) {
				$args['to'] = 'boone@gorg.es';
				return $args;
			}
		);

		return $message;
	},
	10,
	2
);
