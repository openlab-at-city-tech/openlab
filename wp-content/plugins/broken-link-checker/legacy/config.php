<?php
/**
 * Load and initialize the plugin's configuration managers
 *
 * @package Broken_Link_Checker
 */

// Load and initialize the plugin's configuration.
require BLC_DIRECTORY_LEGACY . '/includes/config-manager.php';

// Load the appropriate version of the configuration store class based on PHP version.
if ( version_compare( PHP_VERSION, '8.0.0', '<' ) ) {
	require BLC_DIRECTORY_LEGACY . '/includes/config-store.php7.php';
} else {
	require BLC_DIRECTORY_LEGACY . '/includes/config-store.php';
}

// Create the global configuration manager instance.
require BLC_DIRECTORY_LEGACY . '/includes/configuration-registry.php';

/**
 * Initialize configuration registry and register configurations
 *
 * @return void
 */
function blc_init_configuration() {
	if ( ! blcConfigurationRegistry::initialized() ) {
		$blc_configuration_registry = blcConfigurationRegistry::create();
	} else {
		$blc_configuration_registry = blcConfigurationRegistry::get_instance();
	}

	$blc_configuration_registry->register( blc_create_frontend_configuration() );
	$blc_configuration_registry->register( blc_create_admin_configuration() );
	$blc_configuration_registry->register( blc_create_general_configuration() );
	$blc_configuration_registry->register( blc_create_modules_configuration() );

	$blc_configuration_registry->set_default_manager( 'general' );
}

	/**
	 * Create configuration manager for frontend settings
	 *
	 * @return blcConfigurationManager
	 */
function blc_create_frontend_configuration() {
	return new blcConfigurationManager(
		'frontend',
		// Save the plugin frontend configuration into this DB option.
		'wsblc_frontend_options',
		// Initialize default frontend settings.
		array(
			'mark_broken_links'     => true, // Whether to add the broken_link class to broken links in posts.
			'broken_link_css'       => ".broken_link, a.broken_link {\n\ttext-decoration: line-through;\n}",
			'nofollow_broken_links' => false, // Whether to add rel="nofollow" to broken links in posts.
			'mark_removed_links'    => false, // Whether to add the removed_link class when un-linking a link.
			'removed_link_css'      => ".removed_link, a.removed_link {\n\ttext-decoration: line-through;\n}",
			'logging_enabled'       => false,
			'log_file'              => '',
			'installation_complete' => false,
			'current_db_version'    => 0, // The currently set-up version of the plugin's tables.
			'enabled_post_statuses' => array( 'publish' ), // Only check posts that match one of these statuses.
			'active_modules'        => array(), // List of active modules (used during installation to override defaults).
		)
	);
}

	/**
	 * Create configuration manager for admin settings
	 *
	 * @return blcConfigurationManager
	 */
function blc_create_admin_configuration() {
	return new blcConfigurationManager(
		'admin',
		// Save the plugin's scheduling configuration into this DB option.
		'wsblc_admin_options',
		// Initialize default scheduling settings.
		array(
			'autoexpand_widget'            => true, // Autoexpand the Dashboard widget if broken links are detected.
			'dashboard_widget_capability'  => 'edit_others_posts', // Only display the widget to users who have this capability.
			'show_link_count_bubble'       => true, // Display a notification bubble in the menu when broken links are found.
			'table_layout'                 => 'flexible', // The layout of the link table. Possible values : 'classic', 'flexible'.
			'table_compact'                => true, // Compact table mode on/off.
			'table_visible_columns'        => array( 'new-url', 'status', 'used-in', 'new-link-text' ),
			'table_links_per_page'         => 30,
			'table_color_code_status'      => true, // Color-code link status text.
			'installation_flag_cleared_on' => 0,
			'installation_flag_set_on'     => 0,
			'show_link_actions'            => array( 'blc-deredirect-action' => false ), // Visible link actions.
		)
	);
}

	/**
	 * Create configuration manager for general settings
	 *
	 * @return blcConfigurationManager
	 */
function blc_create_general_configuration() {
	return new blcConfigurationManager(
		'general',
		// Save the plugin's general configuration into this DB option.
		'wsblc_general_options',
		// Initialize default general settings.
		array(
			'send_email_notifications'         => true, // Whether to send the admin email notifications about broken links.
			'send_authors_email_notifications' => false, // Whether to send post authors notifications about broken links in their posts.
			'suggestions_enabled'              => true, // Whether to suggest alternative URLs for broken links.
			'warnings_enabled'                 => true, // Try to automatically detect temporary problems and false positives, and report them as "Warnings" instead of broken links.
			'enable_load_limit'                => true, // Enable/disable load monitoring.
			'exclusion_list'                   => array(), // Links that contain a substring listed in this array won't be checked.
			'need_resynch'                     => false, // [Internal flag] True if there are unparsed items.
			'max_execution_time'               => 7 * 60, // (in seconds) How long the worker instance may run, at most.
			'check_threshold'                  => 72,  // (in hours) Check each link every 72 hours.
			'recheck_count'                    => 3, // How many times a broken link should be re-checked.
			'run_in_dashboard'                 => true, // Run the link checker algo. continuously while the Dashboard is open.
			'run_via_cron'                     => true, // Run it hourly via WordPress pseudo-cron.
			'timeout'                          => 30, // (in seconds) Links that take longer than this to respond will be treated as broken.
			'server_load_limit'                => null, // Stop parsing stuff & checking links if the 1-minute load average goes over this value. Only works on Linux servers. 0 = no limit.
			'failure_duration_threshold'       => 3, // (days) Assume a link is permanently broken if it still hasn't recovered after this many days.
			'incorrect_path'                   => false,
			'blc_post_modified'                => '',
			'youtube_api_key'                  => '',
			'last_notification_sent'           => 0, // When the last email notification was sent (Unix timestamp).
			'notification_email_address'       => '', // If set, send email notifications to this address instead of the admin.
			'notification_schedule'            => apply_filters( 'blc_notification_schedule_filter', 'daily' ), // How often (at most) notifications will be sent. Possible values : 'daily', 'weekly'. There is no option for this so we've added a fitler for it.
			'custom_fields'                    => array(), // List of custom fields that can contain URLs and should be checked.
			'acf_fields'                       => array(), // List of custom fields that can contain URLs and should be checked.
			'highlight_permanent_failures'     => false, // Highlight links that have appear to be permanently broken (in Tools -> Broken Links).
			'clear_log_on'                     => '',
			'custom_log_file_enabled'          => false,
		)
	);
}

/**
 * Create configuration manager for modules settings
 *
 * @return blcConfigurationManager
 */
function blc_create_modules_configuration() {
	return new blcConfigurationManager(
		'modules',
		// Save the plugin's modules configuration into this DB option.
		'wsblc_modules_options',
		// Initialize default modules settings.
		array(
			'all_modules'    => array(),
		)
	);
}

/**
 * Get the global configuration registry instance
 *
 * @return blcConfigurationRegistry
 */
function blc_get_configuration() {
	$blc_configuration_registry = blcConfigurationRegistry::get_instance();
	return $blc_configuration_registry;
}

/**
 * Check if the configuration registry has been initialized
 *
 * @return bool
 */
function blc_config_initialized() {
	return blcConfigurationRegistry::initialized();
}

/**
 * Migrate configuration data from old option to new configuration managers
 *
 * @return void
 */
function blc_maybe_migrate_configuration() {

	$migrated = get_option( 'blc_configuration_migrated', false );
	if ( $migrated ) {
		return false;
	}

	if ( ! blc_config_initialized() ) {
		blc_init_configuration();
	}

	$old_option = get_option( 'wsblc_options', false );
	if ( false === $old_option ) {
		return false;
	}

	if ( ! is_string( $old_option ) ) {
		return false;
	}

	$old_data = json_decode( $old_option, true );
	if ( null === $old_data ) {
		return;
	}

	$config   = blc_get_configuration();
	$managers = $config->options->get_managers();

	foreach ( $managers as $manager ) {
		$manager->load_options();
		if ( $manager->db_option_loaded ) {
			// Don't overwrite existing options.
			continue;
		}

		$available_settings = $manager->get_available_settings();

		foreach ( $available_settings as $setting ) {
			if ( 'active_modules' === $setting ) {
				continue;
			}

			if ( ! array_key_exists( $setting, $old_data ) ) {
				continue;
			}

			$manager->set( $setting, $old_data[ $setting ] );
			unset( $old_data[ $setting ] );
		}

		if ( $manager->is_modified() ) {
			$manager->save_options();
		}
	}

	if ( isset( $old_data['active_modules'] ) ) {
		$frontend_manager = $config->options->get_manager( 'frontend' );
		if ( $frontend_manager instanceof blcConfigurationManager ) {
			$frontend_manager->set( 'active_modules', array_keys( $old_data['active_modules'] ) );
			$frontend_manager->save_options();
		}

		$modules_manager = $config->options->get_manager( 'modules' );
		if ( $modules_manager instanceof blcConfigurationManager ) {
			$modules_manager->set( 'all_modules', $old_data['active_modules'] );
			$modules_manager->save_options();
		}

		unset( $old_data['active_modules'] );
	}

	if ( ! empty( $old_data ) ) {
		$default_manager = $config->options->get_default_manager();
		$default_manager->add_options( $old_data );
		$default_manager->save_options();
	}

	update_option( 'blc_configuration_migrated', 1 );

	return true;
}
