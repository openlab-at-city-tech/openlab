<?php
/**
 * TablePress Automatic Periodic Table Import.
 *
 * @package TablePress
 * @subpackage Automatic Periodic Table Import
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the Automatic Periodic Table Import feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_Automatic_Periodic_Table_Import {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Automatic Periodic Table Import configuration option.
	 *
	 * @since 2.0.0
	 * @var TablePress_WP_Option
	 */
	protected static $auto_import_config;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		// Ensure that TablePress' copy of Action Scheduler (or a newer version in another plugin) is loaded on the next page view.
		if ( 'true' !== get_option( 'tablepress_load_action_scheduler', 'false' ) ) {
			// `update_option()` only writes to the database if a value is changed, so this does not incur a performance penalty.
			update_option( 'tablepress_load_action_scheduler', 'true', true );
		}

		// Hook into the action that is periodically executed by Action Scheduler.
		add_action( 'tablepress_automatic_periodic_table_import_action', array( __CLASS__, 'perform_automatic_import' ), 10, 2 );

		/*
		 * Maybe migrate the configuration to the new format and re-schedule the cron jobs.
		 * This action will only be called when a legacy WP Cron hook for it is registered.
		 * This will be unregistered during the migration (which can also be triggered by e.g.
		 * saving the automatic import configuration on the "Import" screen), so that this action
		 * will only be executed once.
		 */
		add_action( 'tablepress_table_auto_import_hook', array( __CLASS__, 'load_configuration' ) );

		// Adjust the Automatic Import configuration when a table is deleted or its ID is changed.
		add_action( 'tablepress_event_deleted_table', array( $this, 'deleted_table_handler' ) );
		add_action( 'tablepress_event_changed_table_id', array( $this, 'changed_table_id_handler' ), 10, 2 );

		if ( is_admin() ) {
			self::init_admin();
		}
	}

	/**
	 * Writes a message to the error log.
	 *
	 * @since 2.2.4
	 *
	 * @param mixed[]|WP_Error|object|string $data Data to log.
	 */
	protected static function log( /* array|WP_Error|object|string */ $data ): void {
		if ( ! defined( 'TABLEPRESS_DEBUG' ) || true !== TABLEPRESS_DEBUG ) {
			return;
		}

		$prefix = 'TablePress # ';

		if ( is_wp_error( $data ) ) {
			error_log( $prefix . TablePress::get_wp_error_string( $data ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		} elseif ( is_array( $data ) || is_object( $data ) ) {
			error_log( $prefix . var_export( $data, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_var_export
		} else {
			error_log( $prefix . $data ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Loads the import configuration, i.e. the list of tables that are to be imported.
	 *
	 * @since 2.0.0
	 */
	public static function load_configuration(): void {
		$params = array(
			'option_name'   => 'tablepress_auto_import_config',
			'default_value' => array(),
		);
		self::$auto_import_config = TablePress::load_class( 'TablePress_WP_Option', 'class-wp_option.php', 'classes', $params );

		/*
		 * Maybe migrate the configuration to the new format and re-schedule the cron jobs.
		 *
		 * Check for string, which is the data type for `schedule` in the previous configuration scheme.
		 * In the new scheme, it would be `null` if no entry exists, or an array if `schedule` is set (it would then be a table).
		 */
		$schedule = self::$auto_import_config->get( 'schedule' );
		if ( is_string( $schedule ) ) {
			// Get global import interval from previously configured schedule.
			$schedules = wp_get_schedules(); // Schedules from WordPress and other plugins.
			// Add custom schedules that the Automatic Periodic Table Import uses previously.
			$schedules['every_minute'] = array(
				'interval' => MINUTE_IN_SECONDS,
			);
			$schedules['quarterhourly'] = array(
				'interval' => 15 * MINUTE_IN_SECONDS,
			);
			$interval = isset( $schedules[ $schedule ] ) ? $schedules[ $schedule ]['interval'] : DAY_IN_SECONDS;

			// Add the previous schedule interval to each table separately and remove the no longer used source key.
			$tables = self::$auto_import_config->get( 'tables', array() );
			foreach ( $tables as &$table ) {
				unset( $table['source'] );
				$table['interval'] = $interval;
			}
			unset( $table ); // Unset use-by-reference parameter of foreach loop.
			// Remove one level of array nesting. The configuration now only has the tables in it.
			self::$auto_import_config->update( $tables );

			wp_unschedule_hook( 'tablepress_table_auto_import_hook' ); // Unschedule the old cron job.
			self::schedule_periodic_actions(); // Schedule the periodic import actions via Action Scheduler.
		}
	}

	/**
	 * Schedules a single periodic import action via Action Scheduler.
	 *
	 * @since 2.3.0
	 *
	 * @param int                         $timestamp Timestamp for the first execution of the action.
	 * @param int|string                  $interval  Interval in seconds or cron schedule for the action to be repeated.
	 * @param array{0: string, 1: string} $args      Data to pass to the action.
	 */
	public static function schedule_single_periodic_action( int $timestamp, /* int|string */ $interval, array $args ): void {
		if ( is_string( $interval ) ) {
			// Schedule the import action with a cron-like schedule.
			as_schedule_cron_action( $timestamp, $interval, 'tablepress_automatic_periodic_table_import_action', $args );
		} else {
			// Schedule the import action with a fixed integer interval.
			as_schedule_recurring_action( $timestamp, $interval, 'tablepress_automatic_periodic_table_import_action', $args );
		}
	}

	/**
	 * Clears and re-schedules the periodic import actions via Action Scheduler.
	 *
	 * @since 2.3.0
	 */
	public static function schedule_periodic_actions(): void {
		as_unschedule_all_actions( 'tablepress_automatic_periodic_table_import_action' );

		$tables = self::$auto_import_config->get();
		foreach ( $tables as $table_id => $table ) {
			if ( $table['active'] ) {
				$table_id = (string) $table_id; // Ensure that the table ID is a string, as it comes from an array key where numeric strings are converted to integers.
				self::schedule_single_periodic_action( time(), $table['interval'], array( $table_id, $table['location'] ) );
			}
		}
	}

	/**
	 * Imports a given import location and replaces an existing table with the new data.
	 *
	 * @since 2.0.0
	 *
	 * @param string $table_id Table ID of the table to replace.
	 * @param string $location Location of the data to import.
	 */
	public static function perform_automatic_import( string $table_id, string $location ): void {
		// Initiate logging.
		if ( defined( 'TABLEPRESS_DEBUG' ) && true === TABLEPRESS_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
			error_reporting( E_ALL ); // Equals WP_DEBUG true.
			// phpcs:ignore WordPress.PHP.IniSet.log_errors_Disallowed
			ini_set( 'log_errors', 1 ); // Equals WP_LOG_ERRORS true.
			// phpcs:ignore WordPress.PHP.IniSet.Risky
			ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' ); // Moves error log file to WP_CONTENT_DIR/debug.log.
		}

		self::log( '#######################################################' );
		self::log( "### Importing table {$table_id} ({$location}) ###" );

		if ( ! TablePress::$model_table->table_exists( $table_id ) ) {
			self::log( 'Start script execution time (WP_START_TIMESTAMP): ' . WP_START_TIMESTAMP . ' s' );
			self::log( "### Table {$table_id} in the automatic import configuration does not exist!" );
			return;
		}

		$start_time = microtime( true );
		$start_memory = memory_get_peak_usage();
		self::log( "Starting periodic automatic table import at: {$start_time} s" );
		self::log( "Start PHP memory: {$start_memory} bytes (limit " . ini_get( 'memory_limit' ) . ')' );

		$import_config = array();
		$import_config['legacy_import'] = false;
		$import_config['type'] = 'replace';
		$import_config['existing_table'] = $table_id;
		$import_config['source'] = str_starts_with( $location, 'http' ) ? 'url' : 'server';
		$import_config[ $import_config['source'] ] = $location; // Store the import URL or server path in either 'url' or 'server'.

		// Use a static variable to keep a reference to the importer, as multiple tables will need to be imported, most likely.
		static $importer = null;
		if ( is_null( $importer ) ) {
			$importer = TablePress::load_class( 'TablePress_Import', 'class-import.php', 'classes' );
		}
		$import = $importer->run( $import_config );

		if ( is_wp_error( $import ) ) {
			$success = false;
			$error = TablePress::get_wp_error_string( $import );
		} elseif ( 0 < count( $import['errors'] ) ) {
			$success = false;
			$wp_error_strings = array();
			foreach ( $import['errors'] as $file ) {
				$wp_error_strings[] = TablePress::get_wp_error_string( $file->error );
			}
			$error = implode( ', ', $wp_error_strings );
		} else {
			$success = true;
			$error = '';
		}

		$import_message = current_time( 'mysql' );
		if ( ! $success ) {
			$import_message = '<strong>' . __( 'Failed', 'tablepress' ) . '</strong> @ ' . $import_message;
		}
		if ( '' !== $error ) {
			$import_message .= '<br><em>' . esc_html( $error ) . '</em>';
		}

		// @phpstan-ignore-next-line
		if ( is_null( self::$auto_import_config ) ) {
			self::load_configuration();
		}

		$tables = self::$auto_import_config->get();
		$tables[ $table_id ]['last_import'] = $import_message;
		self::$auto_import_config->update( $tables );

		$end_time = microtime( true );
		$duration = $end_time - $start_time;
		self::log( "Finished periodic automatic table import at: {$end_time} s, duration {$duration} seconds" );
		$end_memory = memory_get_peak_usage();
		$memory_usage = $end_memory - $start_memory;
		self::log( "Memory usage during the import: {$memory_usage} bytes (total {$end_memory} bytes)" );
	}

	/**
	 * Removes an entry from the Automatic Import configuration after a table was deleted.
	 *
	 * @since 2.3.0
	 *
	 * @param string $table_id ID of the deleted table.
	 */
	public function deleted_table_handler( string $table_id ): void {
		self::load_configuration();
		$tables = self::$auto_import_config->get();
		if ( isset( $tables[ $table_id ] ) ) {
			if ( $tables[ $table_id ]['active'] ) {
				as_unschedule_action( 'tablepress_automatic_periodic_table_import_action', array( $table_id, $tables[ $table_id ]['location'] ) );
			}

			unset( $tables[ $table_id ] );
			self::$auto_import_config->update( $tables );
		}
	}

	/**
	 * Updates an entry in the Automatic Import configuration after a table ID was changed.
	 *
	 * @since 2.3.0
	 *
	 * @param string $new_id New ID of the table.
	 * @param string $old_id Old ID of the table.
	 */
	public function changed_table_id_handler( string $new_id, string $old_id ): void {
		self::load_configuration();
		$tables = self::$auto_import_config->get();
		if ( isset( $tables[ $old_id ] ) ) {
			if ( $tables[ $old_id ]['active'] ) {
				// Get the next timestamp for the scheduled action, so that it can be used for rescheduling, or the current time if it is not scheduled.
				$timestamp = as_next_scheduled_action( 'tablepress_automatic_periodic_table_import_action', array( $old_id, $tables[ $old_id ]['location'] ) );
				if ( false === $timestamp ) {
					$timestamp = time();
				} else {
					// Handle the special case of in-progress actions.
					if ( true === $timestamp ) {
						$timestamp = time();
					}
					as_unschedule_action( 'tablepress_automatic_periodic_table_import_action', array( $old_id, $tables[ $old_id ]['location'] ) );
				}

				self::schedule_single_periodic_action( $timestamp, $tables[ $old_id ]['interval'], array( $new_id, $tables[ $old_id ]['location'] ) );
			}

			$tables[ $new_id ] = $tables[ $old_id ];
			unset( $tables[ $old_id ] );
			self::$auto_import_config->update( $tables );
		}
	}

	/**
	 * Initializes the admin screens of the Automatic Periodic Table Import module.
	 *
	 * @since 2.0.0
	 */
	public static function init_admin(): void {
		if ( current_user_can( 'tablepress_import_tables' ) ) {
			add_filter( 'tablepress_load_file_full_path', array( __CLASS__, 'change_import_view_full_path' ), 10, 3 );
			add_filter( 'tablepress_load_class_name', array( __CLASS__, 'change_view_import_class_name' ) );
			add_filter( 'tablepress_view_data', array( __CLASS__, 'add_automatic_periodic_table_import_view_data' ), 10, 2 );
			add_action( 'wp_ajax_tablepress_import', array( __CLASS__, 'handle_ajax_action_automatic_periodic_import' ) );
		}
	}

	/**
	 * Loads the Automatic Periodic Table Import view class when the TablePress Import view class is loaded.
	 *
	 * @since 2.0.0
	 *
	 * @param string $full_path Full path of the class file.
	 * @param string $file      File name of the class file.
	 * @param string $folder    Folder name of the class file.
	 * @return string Modified full path.
	 */
	public static function change_import_view_full_path( string $full_path, string $file, string $folder ): string {
		if ( 'view-import.php' === $file ) {
			require_once $full_path; // Load desired file first, as we inherit from it in the new $full_path file.
			$full_path = TABLEPRESS_ABSPATH . 'modules/views/view-automatic_periodic_table_import.php';
		}
		return $full_path;
	}

	/**
	 * Changes Import View class name, to load extended view.
	 *
	 * @since 2.0.0
	 *
	 * @param string $class_name Name of the class that shall be loaded.
	 * @return string Changed class name.
	 */
	public static function change_view_import_class_name( string $class_name ): string {
		if ( 'TablePress_Import_View' === $class_name ) {
			$class_name = 'TablePress_Automatic_Periodic_Table_Import_View';
		}
		return $class_name;
	}

	/**
	 * Adds the view data for the Automatic Periodic Table Import view.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data   Data for this screen.
	 * @param string               $action Action for this screen.
	 * @return array<string, mixed> Modified data for this screen.
	 */
	public static function add_automatic_periodic_table_import_view_data( array $data, string $action ): array {
		if ( 'import' !== $action ) {
			return $data;
		}

		self::load_configuration();
		$data['auto_import_tables'] = self::$auto_import_config->get();
		return $data;
	}

	/**
	 * Saves the Automatic Periodic Table Import configuration.
	 *
	 * @since 2.0.0
	 */
	public static function handle_ajax_action_automatic_periodic_import(): void {
		if ( empty( $_POST['tablepress'] ) ) {
			wp_die( '-1' );
		}

		// Check if the submitted nonce matches the generated nonce we created earlier, dies -1 on failure.
		TablePress::check_nonce( 'import', false, '_ajax_nonce', true );

		// Ignore the request if the current user doesn't have sufficient permissions.
		if ( ! current_user_can( 'tablepress_import_tables' ) ) {
			wp_die( '-1' );
		}

		$tables = wp_unslash( $_POST['tablepress'] );
		$tables = json_decode( $tables, true );
		if ( is_null( $tables ) ) {
			wp_die( '-1' );
		}

		$tables = (array) $tables;
		foreach ( $tables as $table_id => $table ) {
			$table_id = (string) $table_id; // Ensure that the table ID is a string, as it comes from an array key where numeric strings are converted to integers.
			$table['active'] = ( isset( $table['active'] ) && $table['active'] );
			if ( ! isset( $table['location'] ) ) {
				$table['location'] = 'https://';
			}
			if ( ! isset( $table['interval'] ) ) {
				$table['interval'] = DAY_IN_SECONDS;
			} elseif ( is_numeric( $table['interval'] ) ) {
				$table['interval'] = absint( $table['interval'] );
				$table['interval'] = max( $table['interval'], MINUTE_IN_SECONDS ); // Minimum interval is 1 minute.
			} elseif ( is_string( $table['interval'] ) ) {
				$table['interval'] = $table['interval']; // Cron schedule. @todo Check format.
			} else {
				$table['interval'] = DAY_IN_SECONDS;
			}
			$table['last_import'] = '-';

			// Only save tables to the configuration that have other than just the default settings.
			if ( $table['active'] || 'https://' !== $table['location'] || DAY_IN_SECONDS !== $table['interval'] ) {
				$tables[ $table_id ] = $table;
			} else {
				unset( $tables[ $table_id ] );
			}
		}

		self::load_configuration();
		self::$auto_import_config->update( $tables );

		self::schedule_periodic_actions(); // Schedule the periodic import actions via Action Scheduler.

		$response = array(
			'success' => true,
			'message' => 'success_save',
		);
		// Buffer all outputs, to prevent errors/warnings being printed that make the JSON invalid.
		$output_buffer = ob_get_clean();
		if ( ! empty( $output_buffer ) ) {
			$response['output_buffer'] = $output_buffer;
		}

		// Send the response.
		wp_send_json( $response );
	}

} // class TablePress_Module_Automatic_Periodic_Table_Import
