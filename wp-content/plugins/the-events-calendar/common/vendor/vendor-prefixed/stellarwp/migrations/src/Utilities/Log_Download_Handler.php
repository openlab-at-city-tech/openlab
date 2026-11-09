<?php
/**
 * Migration Log Download Handler.
 *
 * Handles CSV download of migration execution logs via admin-post.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Utilities
 */

namespace TEC\Common\StellarWP\Migrations\Utilities;

use DateTimeInterface;
use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Enums\Log_Type;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Logs;
use TEC\Common\StellarWP\Migrations\Models\Execution;

/**
 * Migration Log Download Handler.
 *
 * @since 0.0.1
 */
class Log_Download_Handler {

	/**
	 * Action name suffix for the file download. Full action uses hook prefix.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private static $action_suffix = 'log_download';

	/**
	 * Returns the full admin-post action name for the log download.
	 *
	 * @since 0.0.1
	 *
	 * @return string The action name.
	 */
	public static function get_action_name(): string {
		return 'stellarwp_migrations_' . Config::get_hook_prefix() . '_' . self::$action_suffix;
	}

	/**
	 * Downloads the migration log file based on query parameters.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function download(): void {
		$migration_execution_id = Cast::to_int( filter_input( INPUT_GET, 'migration_execution_id', FILTER_SANITIZE_NUMBER_INT ) );
		$raw_nonce              = filter_input( INPUT_GET, 'nonce', FILTER_SANITIZE_SPECIAL_CHARS );
		$nonce                  = sanitize_text_field( Cast::to_string( $raw_nonce ?? '' ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to download this file.', 'stellarwp-migrations' ) );
		}

		if ( ! $migration_execution_id ) {
			wp_die( esc_html__( 'Invalid migration execution ID.', 'stellarwp-migrations' ) );
		}

		if ( ! wp_verify_nonce( $nonce, static::get_action_name() . $migration_execution_id ) ) {
			wp_die( esc_html__( 'URL expired. Please refresh the page and try again.', 'stellarwp-migrations' ) );
		}

		$execution = Migration_Executions::get_by_id( $migration_execution_id );
		if ( ! $execution || ! $execution instanceof Execution ) {
			wp_die( esc_html__( 'Execution not found.', 'stellarwp-migrations' ) );
		}

		$filename = self::get_filename( $migration_execution_id );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );

		if ( ob_get_level() ) {
			ob_end_clean();
		}

		set_time_limit( 0 );

		self::stream_logs_to_output( $migration_execution_id );

		self::do_exit();
	}

	/**
	 * Returns the download URL for a migration execution log.
	 *
	 * @since 0.0.1
	 *
	 * @param int $migration_execution_id The migration execution ID.
	 *
	 * @return string The download URL.
	 */
	public static function get_download_url( int $migration_execution_id ): string {
		$nonce = wp_create_nonce( self::get_action_name() . $migration_execution_id );

		return add_query_arg(
			[
				'action'                 => self::get_action_name(),
				'migration_execution_id' => $migration_execution_id,
				'nonce'                  => $nonce,
			],
			admin_url( 'admin-post.php' )
		);
	}

	/**
	 * Wrapper for exit() so we can mock it in tests.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private static function do_exit(): void {
		exit;
	}

	/**
	 * Returns the batch size for streaming logs from the database.
	 *
	 * @since 0.0.1
	 *
	 * @return int The batch size.
	 */
	private static function get_batch_size(): int {
		$prefix = Config::get_hook_prefix();

		/**
		 * Filters the batch size for streaming logs from the database.
		 *
		 * @since 0.0.1
		 *
		 * @param int $batch_size The batch size. Default is 500.
		 *
		 * @return int The filtered batch size.
		 */
		return Cast::to_int( apply_filters( "stellarwp_migrations_{$prefix}_log_download_batch_size", 500 ) );
	}

	/**
	 * Returns the CSV separator for the download file.
	 *
	 * @since 0.0.1
	 *
	 * @return string The CSV separator.
	 */
	private static function get_csv_separator(): string {
		$prefix = Config::get_hook_prefix();

		/**
		 * Filters the CSV separator for the download file.
		 *
		 * @since 0.0.1
		 *
		 * @param string $csv_separator The CSV separator. Default is ';'.
		 *
		 * @return string The filtered CSV separator.
		 */
		return (string) apply_filters( "stellarwp_migrations_{$prefix}_log_download_csv_separator", ';' );
	}

	/**
	 * Streams logs from the database directly to output in batches.
	 *
	 * @since 0.0.1
	 *
	 * @param int $migration_execution_id The migration execution ID.
	 *
	 * @return void
	 */
	private static function stream_logs_to_output( int $migration_execution_id ): void {
		$separator = self::get_csv_separator();
		// phpcs:ignore WordPressVIPMinimum.Performance.FilesystemWrites.FileSystemWrites -- Streaming to response output, not filesystem.
		$handle = fopen( 'php://output', 'w' );

		if ( $handle === false ) {
			return;
		}

		$headers = array_map(
			static function ( $header ) use ( $separator ) {
				return self::sanitize_csv_value( (string) $header, $separator );
			},
			self::get_headers()
		);

		fputcsv( $handle, $headers, $separator ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fputcsv -- Stream to response output, not filesystem.

		$batch_size = self::get_batch_size();
		$offset     = 0;
		$has_more   = true;

		while ( $has_more ) {
			$arguments = [
				'offset'                 => $offset,
				'orderby'                => 'created_at',
				'order'                  => 'DESC',
				'query_operator'         => 'AND',
				'migration_execution_id' => [
					'column'   => 'migration_execution_id',
					'value'    => $migration_execution_id,
					'operator' => '=',
				],
			];

			/** @var array<int|string, array<string, mixed>>|null $log_entries */
			$log_entries = Migration_Logs::paginate( $arguments, $batch_size );

			if ( empty( $log_entries ) || count( $log_entries ) < $batch_size ) {
				$has_more = false;
			}

			$prefix = Config::get_hook_prefix();

			/**
			 * Fires before streaming a batch of log entries.
			 *
			 * @since 0.0.1
			 *
			 * @param array<int|string, array<string, mixed>>|null $log_entries            The log entries in the batch. Null if none.
			 * @param int                                          $migration_execution_id The migration execution ID.
			 * @param int                                          $offset                 The current offset.
			 * @param int                                          $batch_size             The batch size.
			 *
			 * @return void
			 */
			do_action(
				"stellarwp_migrations_{$prefix}_log_download_stream_before",
				$log_entries,
				$migration_execution_id,
				$offset,
				$batch_size
			);

			if ( ! empty( $log_entries ) ) {
				/** @var array<string, mixed> $log_entry */
				foreach ( $log_entries as $log_entry ) {
					if ( ! is_array( $log_entry ) ) {
						continue;
					}

					fputcsv( $handle, self::log_entry_to_row( $log_entry, $separator ), $separator ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fputcsv -- Stream to response output, not filesystem.
				}
			}

			/**
			 * Fires after streaming a batch of log entries.
			 *
			 * @since 0.0.1
			 *
			 * @param array<int|string, array<string, mixed>>|null $log_entries            The log entries in the batch. Null if none.
			 * @param int                                          $migration_execution_id The migration execution ID.
			 * @param int                                          $offset                 The current offset.
			 * @param int                                          $batch_size             The batch size.
			 *
			 * @return void
			 */
			do_action(
				"stellarwp_migrations_{$prefix}_log_download_stream_after",
				$log_entries,
				$migration_execution_id,
				$offset,
				$batch_size
			);

			flush();
			$offset += $batch_size;
		}

		fclose( $handle );
	}

	/**
	 * Sanitizes a value for safe CSV output by removing the separator and newlines.
	 *
	 * Prevents the CSV from being broken when message or data contain the delimiter.
	 *
	 * @since 0.0.1
	 *
	 * @param string $value     The raw value.
	 * @param string $separator The CSV separator in use.
	 *
	 * @return string The sanitized value.
	 */
	private static function sanitize_csv_value( string $value, string $separator ): string {
		if ( $value === '' ) {
			return '';
		}

		$sanitized = str_replace( [ $separator, "\r\n", "\r", "\n" ], ' ', $value );

		$prefix = Config::get_hook_prefix();

		/**
		 * Filters the sanitized CSV value before writing.
		 *
		 * @since 0.0.1
		 *
		 * @param string $sanitized The value after separator/newline replacement.
		 * @param string $value     The original value.
		 * @param string $separator The CSV separator.
		 *
		 * @return string The final value to write.
		 */
		return (string) apply_filters(
			"stellarwp_migrations_{$prefix}_log_download_sanitize_csv_value",
			$sanitized,
			$value,
			$separator
		);
	}

	/**
	 * Converts a log entry array to a CSV row (flat array of strings).
	 *
	 * Row length and order must match the filtered headers from get_headers() when
	 * using the log_download_row filter so columns align correctly.
	 *
	 * @since 0.0.1
	 *
	 * @param array<string, mixed> $log_entry The log entry from the table.
	 * @param string               $separator The CSV separator (used to sanitize values).
	 *
	 * @return array<int, string> The row values for CSV.
	 */
	private static function log_entry_to_row( array $log_entry, string $separator ): array {
		$id         = $log_entry['id'] ?? '';
		$exec_id    = $log_entry['migration_execution_id'] ?? '';
		$created_at = $log_entry['created_at'] ?? '';
		$type       = $log_entry['type'] ?? '';
		$message    = $log_entry['message'] ?? '';
		$data       = $log_entry['data'] ?? [];

		if ( $created_at instanceof DateTimeInterface ) {
			$created_at = $created_at->format( 'Y-m-d H:i:s' );
		}

		if ( $type instanceof Log_Type ) {
			$type = $type->getValue();
		}

		$data_str = is_array( $data ) && $data !== []
			? wp_json_encode( $data )
			: '';

		$row = [
			self::sanitize_csv_value( Cast::to_string( $id ), $separator ),
			self::sanitize_csv_value( Cast::to_string( $exec_id ), $separator ),
			self::sanitize_csv_value( Cast::to_string( $created_at ), $separator ),
			self::sanitize_csv_value( Cast::to_string( $type ), $separator ),
			self::sanitize_csv_value( Cast::to_string( $message ), $separator ),
			self::sanitize_csv_value( Cast::to_string( $data_str ), $separator ),
		];

		$prefix = Config::get_hook_prefix();

		/**
		 * Filters the row data for each log entry in the migration log download.
		 *
		 * Use this filter when customizing headers via log_download_headers so that
		 * the number and order of row values match the headers.
		 *
		 * @since 0.0.1
		 *
		 * @param array<int, string>   $row       The row values for CSV (same length/order as headers).
		 * @param array<string, mixed> $log_entry The raw log entry from the table.
		 * @param string               $separator The CSV separator in use.
		 *
		 * @return array<int, string> The filtered row values.
		 */
		return (array) apply_filters(
			"stellarwp_migrations_{$prefix}_log_download_row",
			$row,
			$log_entry,
			$separator
		);
	}

	/**
	 * Returns the headers for the migration log download.
	 *
	 * When filtering headers, also filter row data via log_download_row so column
	 * count and order stay in sync.
	 *
	 * @since 0.0.1
	 *
	 * @return array<string> The header row.
	 */
	private static function get_headers(): array {
		$headers = [
			_x( 'ID', 'Migration log download header', 'stellarwp-migrations' ),
			_x( 'Migration Execution ID', 'Migration log download header', 'stellarwp-migrations' ),
			_x( 'Date GMT', 'Migration log download header', 'stellarwp-migrations' ),
			_x( 'Type', 'Migration log download header', 'stellarwp-migrations' ),
			_x( 'Message', 'Migration log download header', 'stellarwp-migrations' ),
			_x( 'Data', 'Migration log download header', 'stellarwp-migrations' ),
		];

		$prefix = Config::get_hook_prefix();

		/**
		 * Filters the headers for the migration log download.
		 *
		 * @since 0.0.1
		 *
		 * @param array<string> $headers The headers.
		 *
		 * @return array<string> The filtered headers.
		 */
		return (array) apply_filters( "stellarwp_migrations_{$prefix}_log_download_headers", $headers );
	}

	/**
	 * Returns the filename for the migration log download.
	 *
	 * @since 0.0.1
	 *
	 * @param int $migration_execution_id The migration execution ID.
	 *
	 * @return string The filename.
	 */
	private static function get_filename( int $migration_execution_id ): string {
		$default = sprintf(
			'migration-execution-%d-logs-%s.csv',
			$migration_execution_id,
			gmdate( 'Y-m-d-His' )
		);

		$prefix = Config::get_hook_prefix();

		/**
		 * Filters the filename for the migration log download.
		 *
		 * @since 0.0.1
		 *
		 * @param string $filename The filename.
		 *
		 * @return string The filtered filename.
		 */
		return (string) apply_filters( "stellarwp_migrations_{$prefix}_log_download_filename", $default );
	}
}
