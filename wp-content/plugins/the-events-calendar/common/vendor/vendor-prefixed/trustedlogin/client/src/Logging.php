<?php
/**
 * Class Logging
 *
 * @package TEC\Common\TrustedLogin\Client
 *
 * @copyright 2021 Katz Web Services, Inc.
 */

namespace TEC\Common\TrustedLogin;

use WP_Filesystem_Base;
use WP_Filesystem;

/**
 * Handles all logging for the client.
 */
class Logging {

	/**
	 * Path to logging directory (inside the WP Uploads base dir)
	 */
	const DIRECTORY_PATH = 'trustedlogin-logs/';

	/**
	 * Namespace for the vendor.
	 *
	 * @var string
	 */
	private $ns;

	/**
	 * Config object.
	 *
	 * @var Config
	 */
	private $config = null;

	/**
	 * Whether logging is enabled. Can be overridden by a filter.
	 *
	 * @var bool $logging_enabled
	 */
	private $logging_enabled = false;

	/**
	 * KLogger instance.
	 *
	 * @var Logger|null|false Null: not instantiated; False: failed to instantiate.
	 */
	private $klogger = null;

	/**
	 * Logger constructor.
	 *
	 * @param Config $config Config object.
	 */
	public function __construct( Config $config ) {

		$this->config = $config;

		$this->ns = $config->ns();

		$this->logging_enabled = (bool) $config->get_setting( 'logging/enabled', false );
	}

	/**
	 * Attempts to initialize KLogger logging
	 *
	 * @param Config $config Config object.
	 *
	 * @return false|Logger
	 */
	private function setup_klogger( $config ) {

		$logging_directory = $this->setup_logging_directory( $config );

		// Directory cannot be found or created. Cannot log.
		if ( ! $logging_directory ) {
			return false;
		}

		try {
			$datetime = new \DateTime( '@' . time() );

			// Filename hash changes every day, make it harder to guess.
			$filename_hash_data = $this->ns . home_url( '/' ) . $datetime->format( 'z' );

			$default_options = array(
				'extension'      => 'log',
				'dateFormat'     => 'Y-m-d G:i:s.u',
				'filename'       => sprintf( 'client-debug-%s-%s', $datetime->format( 'Y-m-d' ), \hash( 'sha256', $filename_hash_data ) ),
				'flushFrequency' => false,
				'logFormat'      => false,
				'appendContext'  => true,
			);

			$settings_options = $config->get_setting( 'logging/options', $default_options );

			$options = wp_parse_args( $settings_options, $default_options );

			$klogger = new Logger(
				$logging_directory,
				$config->get_setting( 'logging/threshold', 'notice' ),
				$options
			);
		} catch ( \RuntimeException $exception ) {
			$this->log( 'Could not initialize KLogger: ' . $exception->getMessage(), __METHOD__, 'error' );

			return false;
		} catch ( \Exception $exception ) {
			$this->log( 'DateTime could not be created: ' . $exception->getMessage(), __METHOD__, 'error' );

			return false;
		}

		return $klogger;
	}

	/**
	 * Returns the directory to use for logging. Creates one if it doesn't exist.
	 *
	 * @param \TEC\Common\TrustedLogin\Config $config Configuration object.
	 *
	 * @since 1.8.0
	 *
	 * @return bool|string Directory path to logging. False if logging directory cannot be found, created, or written to.
	 */
	private function setup_logging_directory( $config ) {

		$logging_directory = $config->get_setting( 'logging/directory', '' );

		if ( empty( $logging_directory ) ) {
			$logging_directory = $this->maybe_make_logging_directory();
		}

		// Directory cannot be found or created. Cannot log.
		if ( ! $logging_directory ) {
			return false;
		}

		// Directory cannot be written to. Cannot log.
		if ( ! $this->check_directory( $logging_directory ) ) {
			return false;
		}

		// Protect directory from being browsed by adding index.html.
		$this->prevent_directory_browsing( $logging_directory );

		return $logging_directory;
	}

	/**
	 * Returns the full path to the log file
	 *
	 * @since 1.5.0
	 *
	 * @return null|string Path to log file, if exists; null if not instantiated.
	 */
	public function get_log_file_path() {

		if ( ! $this->klogger instanceof Logger ) {
			return null;
		}

		return $this->klogger->getLogFilePath();
	}

	/**
	 * Checks whether a path exists and is writable
	 *
	 * @param string $dirpath Path to directory.
	 *
	 * @return bool|string If exists and writable, returns original string. Otherwise, returns false.
	 */
	private function check_directory( $dirpath ) {

		$dirpath     = (string) $dirpath;
		$file_exists = file_exists( $dirpath );
		$is_writable = wp_is_writable( $dirpath );

		// If the configured setting path exists and is writeable, use it.
		if ( $file_exists && $is_writable ) {
			return $dirpath;
		}

		// Otherwise, try and log default errors.
		if ( ! $file_exists ) {
			$this->log( 'The defined logging directory does not exist: ' . $dirpath, __METHOD__, 'error' );
		}

		if ( ! $is_writable ) {
			$this->log( 'The defined logging directory exists but could not be written to: ' . $dirpath, __METHOD__, 'error' );
		}

		return false;
	}

	/**
	 * Returns the directory to use for logging if not defined by Config. Creates one if it doesn't exist.
	 *
	 * Note: Created directories are protected by an index.html file to prevent browsing.
	 *
	 * @return false|string Directory path, if exists; False if failure.
	 */
	private function maybe_make_logging_directory() {

		$upload_dir = wp_upload_dir();

		$log_dir = trailingslashit( $upload_dir['basedir'] ) . self::DIRECTORY_PATH;

		// Directory exists; return early.

		if ( file_exists( $log_dir ) ) {
			return $log_dir;
		}

		// Create the folder using wp_mkdir_p() instead of relying on KLogger.
		$folder_created = wp_mkdir_p( $log_dir );

		// Something went wrong mapping the directory.
		if ( ! $folder_created ) {
			$this->log( 'The log directory could not be created: ' . $log_dir, __METHOD__, 'error' );
			return false;
		}

		return $log_dir;
	}

	/**
	 * Prevent browsing a directory by adding an index.html file to it
	 *
	 * Code inspired by @see wp_privacy_generate_personal_data_export_file()
	 *
	 * @param string $dirpath Path to directory to protect (in this case, logging).
	 *
	 * @return bool True: File exists or was created; False: file could not be created.
	 */
	private function prevent_directory_browsing( $dirpath ) {
		// phpcs:disable Generic.Commenting.DocComment.MissingShort
		/** @global WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! $wp_filesystem instanceof WP_Filesystem_Base ) {
			$this->log( 'Unable to initialize WP_Filesystem.', __METHOD__, 'error' );
			return false;
		}

		// Protect export folder from browsing.
		$index_pathname = $dirpath . 'index.html';

		if ( $wp_filesystem->exists( $index_pathname ) ) {
			return true;
		}

		$file_content = '<!-- Silence is golden. TrustedLogin is also pretty great. Learn more: https://www.trustedlogin.com/about/easy-and-safe/ -->';

		$file_was_saved = $wp_filesystem->put_contents( $index_pathname, $file_content );

		if ( ! $file_was_saved ) {
			$this->log( 'Unable to protect directory from browsing.', __METHOD__, 'error' );
			return false;
		}

		return true;
	}

	/**
	 * Returns whether logging is enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {

		$is_enabled = ! empty( $this->logging_enabled );

		/**
		 * Filter: Whether debug logging is enabled in TrustedLogin Client
		 *
		 * @since 1.0.0
		 *
		 * @param bool $debug_mode Default: false
		 */
		$is_enabled = apply_filters( 'trustedlogin/' . $this->ns . '/logging/enabled', $is_enabled );

		return (bool) $is_enabled;
	}

	/**
	 * Log a message using KLogger or error_log().
	 *
	 * @see https://github.com/php-fig/log/blob/master/Psr/Log/LogLevel.php for log levels
	 *
	 * @param string|\WP_Error           $message Message or error to log. If a WP_Error is passed, $data is ignored.
	 * @param string                     $method Method where the log was called.
	 * @param string                     $level PSR-3 log level.
	 * @param \WP_Error|\Exception|mixed $data Optional. Error data. Ignored if $message is WP_Error.
	 */
	public function log( $message = '', $method = '', $level = 'debug', $data = array() ) {

		if ( ! $this->is_enabled() ) {
			return;
		}

		$levels = array( 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug' );

		if ( ! in_array( $level, $levels, true ) ) {
			$this->log( sprintf( 'Invalid level passed by %s method: %s', $method, $level ), __METHOD__, 'error' );

			$level = 'debug'; // Continue processing original log.
		}

		$log_message = $message;

		if ( is_wp_error( $log_message ) ) {
			$data        = $log_message; // Store WP_Error as extra data.
			$log_message = ''; // The message will be constructed below.
		}

		if ( ! is_string( $log_message ) ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$log_message = print_r( $log_message, true );
		}

		if ( is_wp_error( $data ) ) {
			$log_message .= sprintf( '[%s] %s', $data->get_error_code(), $data->get_error_message() );
		}

		if ( $data instanceof \Exception ) {
			$log_message .= sprintf( '[%s] %s', $data->getCode(), $data->getMessage() );
		}

		// Keep PSR-4 compatible.
		if ( $data && ! is_array( $data ) ) {
			$data = array( $data );
		}

		do_action( 'trustedlogin/' . $this->ns . '/logging/log', $message, $method, $level, $data );
		do_action( 'trustedlogin/' . $this->ns . '/logging/log_' . $level, $message, $method, $data );

		// If logging is in place, don't use the error_log.
		if ( has_action( 'trustedlogin/' . $this->ns . '/logging/log' ) || has_action( 'trustedlogin/' . $this->ns . '/logging/log_' . $level ) ) {
			return;
		}

		// Set up klogger, creating the logging file/directory if it doesn't already exist.
		$this->klogger = $this->setup_klogger( $this->config );

		// The logger class didn't load. Rely on WordPress logging, if enabled.
		if ( ! $this->klogger ) {
			$wp_debug     = defined( 'WP_DEBUG' ) && WP_DEBUG;
			$wp_debug_log = defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;

			// If WP_DEBUG and WP_DEBUG_LOG are enabled, log errors to that file.
			if ( $wp_debug && $wp_debug_log ) {
				if ( ! empty( $data ) ) {
					// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$log_message .= ' Error data: ' . print_r( $data, true );
				}

				// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( $method . ' (' . $level . '): ' . $log_message );
			}

			return;
		}

		$this->klogger->{$level}( $log_message, (array) $data );
	}
}
