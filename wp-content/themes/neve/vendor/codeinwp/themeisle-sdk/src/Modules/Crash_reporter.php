<?php
/**
 * The crash reporter model class for ThemeIsle SDK.
 *
 * Captures fatal errors and uncaught exceptions originating from registered
 * ThemeIsle products, stores sanitized aggregates locally and sends them to
 * the tracking endpoint when the logging consent is granted.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2026, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.4.0
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Crash reporter module for ThemeIsle SDK.
 *
 * A single set of PHP handlers serves every registered product: crashes are
 * attributed to a product by path-prefix matching the crashing file against
 * the registered product directories. Crashes that do not originate from a
 * registered product directory are dropped and never stored.
 */
class Crash_Reporter extends Abstract_Module {

	/**
	 * Endpoint where crash reports are sent.
	 */
	const CRASH_ENDPOINT = 'https://api.themeisle.com/tracking/crashes';

	/**
	 * Maximum distinct crash fingerprints stored per product.
	 */
	const MAX_FINGERPRINTS = 15;

	/**
	 * Maximum stored message length. Large enough to preserve the stack trace
	 * text PHP embeds inside uncaught-exception fatal messages.
	 */
	const MAX_MESSAGE_LENGTH = 2000;

	/**
	 * Maximum serialized size of the stored reports, in bytes.
	 */
	const MAX_STORED_BYTES = 16000;

	/**
	 * Message length used for the compact uninstall summary.
	 */
	const SUMMARY_MESSAGE_LENGTH = 200;

	/**
	 * Maximum number of reports included in the compact uninstall summary.
	 */
	const SUMMARY_MAX_REPORTS = 5;

	/**
	 * Send backoff window after a failed delivery, in seconds (12 hours).
	 */
	const BACKOFF_SECONDS = 43200;

	/**
	 * Registered product directories, normalized dir => Product.
	 *
	 * @var array<string, Product>
	 */
	private static $registry = [];

	/**
	 * Whether the PHP handlers have been installed for this request.
	 *
	 * @var bool
	 */
	private static $handlers_registered = false;

	/**
	 * Memory buffer released at shutdown so the handler can run on OOM fatals.
	 *
	 * @var string|null
	 */
	private static $reserved_memory = null;

	/**
	 * Previously registered exception handler, chained after ours.
	 *
	 * @var callable|null
	 */
	private static $previous_exception_handler = null;

	/**
	 * Whether an uncaught exception was already captured by the exception
	 * handler, so the shutdown handler does not record it twice.
	 *
	 * @var bool
	 */
	private static $exception_captured = false;

	/**
	 * Fatal error types captured at shutdown.
	 *
	 * @var int[]
	 */
	private static $fatal_types = [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ];

	/**
	 * Should we load this module for the product?
	 *
	 * @param Product $product Product to check.
	 *
	 * @return bool Should load?
	 */
	public function can_load( $product ) {
		return apply_filters( $product->get_slug() . '_sdk_enable_crash_reporter', true );
	}

	/**
	 * Bootstrap the module for a product.
	 *
	 * @param Product $product Product to load the module for.
	 *
	 * @return Crash_Reporter Module instance.
	 */
	public function load( $product ) {
		$this->product = $product;

		self::register_product( $product );
		self::register_handlers();

		$key = $product->get_key();

		add_action( $key . '_crash_flush', [ $this, 'send_reports' ] );

		$update_action = 'themeisle_sdk_update_' . $product->get_slug();
		add_action( $update_action, [ $this, 'on_product_update' ] );
		if ( did_action( $update_action ) ) {
			$this->on_product_update();
		}

		$this->adopt_sentinel_records();
		$this->maybe_schedule_flush();

		return $this;
	}

	/**
	 * Register a product directory for crash attribution.
	 *
	 * @param Product $product Product to register.
	 */
	private static function register_product( $product ) {
		$dir = self::normalize_path( dirname( $product->get_basefile() ) );
		if ( '' === $dir ) {
			return;
		}
		self::$registry[ rtrim( $dir, '/' ) . '/' ] = $product;
	}

	/**
	 * Install the PHP handlers, once per request, regardless of how many
	 * products are registered.
	 */
	private static function register_handlers() {
		if ( self::$handlers_registered ) {
			return;
		}
		self::$handlers_registered = true;

		if ( ! defined( 'THEMEISLE_SDK_CRASH_HANDLER' ) ) {
			define( 'THEMEISLE_SDK_CRASH_HANDLER', true );
		}

		self::$reserved_memory            = str_repeat( ' ', 16384 );
		self::$previous_exception_handler = set_exception_handler( [ __CLASS__, 'handle_exception' ] );
		register_shutdown_function( [ __CLASS__, 'handle_shutdown' ] );

		add_filter( 'debug_information', [ __CLASS__, 'add_debug_information' ] );
	}

	/**
	 * Global handler for uncaught exceptions. Captures the crash, then hands
	 * the exception on: to the previously registered handler when there is
	 * one, otherwise back to PHP.
	 *
	 * A handler that returns makes PHP end the request as if nothing failed:
	 * no fatal error is raised or logged and error_get_last() stays empty, so
	 * WordPress never shows its "critical error" page, never enters recovery
	 * mode and does not roll back a broken automatic update. Rethrowing keeps
	 * the request failing exactly as it would without this handler.
	 *
	 * @param \Throwable $throwable Uncaught exception.
	 *
	 * @throws \Throwable The same exception, when no earlier handler exists.
	 */
	public static function handle_exception( $throwable ) {
		try {
			self::$exception_captured = true;
			self::capture( self::normalize_throwable( $throwable ) );
		} catch ( \Exception $e ) {
			self::obs( 'exception_handler_failed' );
		} catch ( \Throwable $e ) {
			self::obs( 'exception_handler_failed' );
		}

		if ( null !== self::$previous_exception_handler && is_callable( self::$previous_exception_handler ) ) {
			call_user_func( self::$previous_exception_handler, $throwable );

			return;
		}

		throw $throwable;
	}

	/**
	 * Shutdown handler. Records supported fatal errors originating from a
	 * registered product directory. Never outputs and never exits.
	 *
	 * @param array{type: int, message: string, file: string, line: int}|null $error Error to examine. Defaults to
	 *                                                                             error_get_last(); tests pass a synthetic one.
	 */
	public static function handle_shutdown( $error = null ) {
		self::$reserved_memory = null;

		if ( null === $error ) {
			$error = error_get_last();
		}
		if ( empty( $error ) || ! isset( $error['type'] ) || ! in_array( (int) $error['type'], self::$fatal_types, true ) ) {
			return;
		}
		if ( self::$exception_captured ) {
			return;
		}

		try {
			self::capture(
				[
					'type'    => (int) $error['type'],
					'message' => isset( $error['message'] ) ? (string) $error['message'] : '',
					'file'    => isset( $error['file'] ) ? (string) $error['file'] : '',
					'line'    => isset( $error['line'] ) ? (int) $error['line'] : 0,
					'trace'   => [],
				]
			);
		} catch ( \Exception $e ) {
			self::obs( 'shutdown_handler_failed' );
		} catch ( \Throwable $e ) {
			self::obs( 'shutdown_handler_failed' );
		}
	}

	/**
	 * Capture pipeline: attribute, sanitize, fingerprint and store one error.
	 *
	 * Public and parameter-driven so tests can inject synthetic errors.
	 *
	 * @param array $error Error data: type, message, file, line, trace.
	 *
	 * @return bool Whether the error was stored.
	 */
	public static function capture( $error ) {
		if ( defined( 'WP_SANDBOX_SCRAPING' ) && WP_SANDBOX_SCRAPING ) {
			return false;
		}

		$file  = self::normalize_path( isset( $error['file'] ) ? $error['file'] : '' );
		$owner = self::find_owner( $file );
		if ( null === $owner ) {
			self::obs( 'no_attribution' );

			return false;
		}

		$message = self::sanitize_text( isset( $error['message'] ) ? $error['message'] : '', self::MAX_MESSAGE_LENGTH );
		$line    = isset( $error['line'] ) ? (int) $error['line'] : 0;
		$type    = isset( $error['type'] ) ? (int) $error['type'] : E_ERROR;
		$trace   = [];
		if ( ! empty( $error['trace'] ) && is_array( $error['trace'] ) ) {
			foreach ( $error['trace'] as $frame ) {
				$trace[] = self::sanitize_frame( $frame );
			}
		}

		$sanitized_file = self::classify_path( $file );
		$report         = [
			'type'            => $type,
			'event_type'      => empty( $error['is_exception'] ) ? 'fatal_error' : 'uncaught_exception',
			'message'         => $message,
			'file'            => $sanitized_file,
			'line'            => $line,
			'trace'           => $trace,
			'in_sdk'          => 0 === strpos( $sanitized_file, 'sdk:' ),
			'request_context' => self::request_context(),
			'product_version' => $owner->get_version(),
			'sdk_version'     => self::get_sdk_version(),
		];

		return self::store_report( $owner, $report );
	}

	/**
	 * Store a sanitized report into the product crash option, aggregating by
	 * fingerprint.
	 *
	 * @param Product $product Owning product.
	 * @param array   $report  Sanitized report.
	 *
	 * @return bool Whether the report was persisted.
	 */
	private static function store_report( $product, $report ) {
		$key  = $product->get_key();
		$data = self::read_data( $key );

		$fingerprint = self::fingerprint( $report );
		$now         = time();

		if ( isset( $data['reports'][ $fingerprint ] ) ) {
			$data['reports'][ $fingerprint ]['count']     = (int) $data['reports'][ $fingerprint ]['count'] + 1;
			$data['reports'][ $fingerprint ]['last_seen'] = $now;
		} else {
			if ( count( $data['reports'] ) >= self::MAX_FINGERPRINTS ) {
				$data['reports'] = self::evict_lowest( $data['reports'] );
			}
			$report['fingerprint'] = $fingerprint;
			$report['count']       = 1;
			$report['first_seen']  = $now;
			$report['last_seen']   = $now;
			if ( ! empty( $data['meta']['last_update'] ) ) {
				$report['time_since_update'] = $now - (int) $data['meta']['last_update'];
			}
			$data['reports'][ $fingerprint ] = $report;
		}

		$data['reports'] = self::enforce_size_cap( $data['reports'] );

		$stored = self::write_data( $key, $data );
		if ( $stored ) {
			self::schedule_flush( $product );
		}

		return $stored;
	}

	/**
	 * Evict the report with the lowest count, oldest last-seen on ties.
	 *
	 * @param array $reports Stored reports keyed by fingerprint.
	 *
	 * @return array Reports with one entry removed.
	 */
	private static function evict_lowest( $reports ) {
		$evict_key = null;
		foreach ( $reports as $fingerprint => $report ) {
			if ( null === $evict_key ) {
				$evict_key = $fingerprint;
				continue;
			}
			$candidate = $reports[ $evict_key ];
			if ( $report['count'] < $candidate['count'] || ( $report['count'] === $candidate['count'] && $report['last_seen'] < $candidate['last_seen'] ) ) {
				$evict_key = $fingerprint;
			}
		}
		if ( null !== $evict_key ) {
			unset( $reports[ $evict_key ] );
			self::obs( 'cap_evicted' );
		}

		return $reports;
	}

	/**
	 * Keep the serialized reports under the size cap: first trim traces and
	 * messages, then drop the lowest-value reports.
	 *
	 * @param array $reports Stored reports keyed by fingerprint.
	 *
	 * @return array Reports fitting the size cap.
	 */
	private static function enforce_size_cap( $reports ) {
		if ( strlen( (string) wp_json_encode( $reports ) ) <= self::MAX_STORED_BYTES ) {
			return $reports;
		}

		foreach ( $reports as $fingerprint => $report ) {
			if ( ! empty( $report['trace'] ) && count( $report['trace'] ) > 5 ) {
				$trimmed_marker                   = [
					'file'     => '[trimmed]',
					'line'     => 0,
					'function' => '',
				];
				$reports[ $fingerprint ]['trace'] = array_merge(
					array_slice( $report['trace'], 0, 3 ),
					[ $trimmed_marker ],
					array_slice( $report['trace'], - 2 )
				);
			}
			$reports[ $fingerprint ]['message'] = substr( (string) $report['message'], 0, 500 );
		}
		self::obs( 'payload_trimmed' );

		$total = count( $reports );
		$size  = strlen( (string) wp_json_encode( $reports ) );
		while ( $total > 1 && $size > self::MAX_STORED_BYTES ) {
			$reports = self::evict_lowest( $reports );
			$total   = count( $reports );
			$size    = strlen( (string) wp_json_encode( $reports ) );
		}

		return $reports;
	}

	/**
	 * Build the dedup fingerprint of a report.
	 *
	 * Numbers are normalized out of the message component so variable parts
	 * (memory sizes, ids) do not break aggregation.
	 *
	 * @param array $report Sanitized report.
	 *
	 * @return string Fingerprint hash.
	 */
	private static function fingerprint( $report ) {
		$message_part = preg_replace( '/\d+/', 'N', substr( (string) $report['message'], 0, 200 ) );

		return md5(
			implode(
				'|',
				[
					$report['type'],
					$message_part,
					$report['file'],
					$report['line'],
					$report['product_version'],
				]
			)
		);
	}

	/**
	 * Find the registered product owning a file path, longest prefix wins.
	 *
	 * @param string $file Normalized absolute file path.
	 *
	 * @return Product|null Owning product or null when the file is not ours.
	 */
	private static function find_owner( $file ) {
		if ( '' === $file ) {
			return null;
		}
		$owner      = null;
		$owner_size = 0;
		foreach ( self::$registry as $dir => $product ) {
			if ( 0 === strpos( $file, $dir ) && strlen( $dir ) > $owner_size ) {
				$owner      = $product;
				$owner_size = strlen( $dir );
			}
		}

		return $owner;
	}

	/**
	 * Rewrite an absolute path into a privacy-safe classified path.
	 *
	 * @param string $path Absolute path.
	 *
	 * @return string Classified path, e.g. `product:inc/file.php`.
	 */
	private static function classify_path( $path ) {
		$path = self::normalize_path( $path );
		if ( '' === $path ) {
			return '';
		}

		// The longest matching root wins, so the most specific classification
		// applies regardless of how the roots are nested into each other
		// (in production the SDK lives inside a product directory).
		$roots = [
			[ rtrim( self::normalize_path( dirname( dirname( __DIR__ ) ) ), '/' ) . '/', 'sdk:' ],
		];
		foreach ( self::$registry as $dir => $product ) {
			$roots[] = [ $dir, 'product:' ];
		}
		if ( defined( 'WP_PLUGIN_DIR' ) ) {
			$roots[] = [ rtrim( self::normalize_path( WP_PLUGIN_DIR ), '/' ) . '/', 'plugin:' ];
		}
		if ( function_exists( 'get_theme_root' ) ) {
			$roots[] = [ rtrim( self::normalize_path( get_theme_root() ), '/' ) . '/', 'theme:' ];
		}
		if ( defined( 'ABSPATH' ) ) {
			$roots[] = [ rtrim( self::normalize_path( ABSPATH ), '/' ) . '/', 'wp:' ];
		}

		$best_root  = '';
		$best_label = null;
		foreach ( $roots as $root ) {
			if ( strlen( $root[0] ) > strlen( $best_root ) && 0 === strpos( $path, $root[0] ) ) {
				$best_root  = $root[0];
				$best_label = $root[1];
			}
		}
		if ( null !== $best_label ) {
			return $best_label . substr( $path, strlen( $best_root ) );
		}

		return basename( $path );
	}

	/**
	 * Sanitize a single trace frame: classified path, line and callable name,
	 * arguments always dropped.
	 *
	 * @param array $frame Raw trace frame.
	 *
	 * @return array Sanitized frame.
	 */
	private static function sanitize_frame( $frame ) {
		$function = isset( $frame['function'] ) ? (string) $frame['function'] : '';
		if ( isset( $frame['class'] ) ) {
			$function = $frame['class'] . ( isset( $frame['type'] ) ? $frame['type'] : '::' ) . $function;
		}

		return [
			'file'     => isset( $frame['file'] ) ? self::classify_path( $frame['file'] ) : '[internal]',
			'line'     => isset( $frame['line'] ) ? (int) $frame['line'] : 0,
			'function' => $function,
		];
	}

	/**
	 * Redact secrets, personal data and server paths from free text.
	 *
	 * @param string $text       Text to sanitize.
	 * @param int    $max_length Maximum length to keep.
	 *
	 * @return string Sanitized text.
	 */
	private static function sanitize_text( $text, $max_length ) {
		$text = (string) $text;

		// Rewrite known roots first so embedded stack traces stay readable.
		$sdk_root = rtrim( self::normalize_path( dirname( dirname( __DIR__ ) ) ), '/' ) . '/';
		$text     = str_replace( [ $sdk_root, str_replace( '/', '\\', $sdk_root ) ], 'sdk:/', $text );
		foreach ( self::$registry as $dir => $product ) {
			$text = str_replace( [ $dir, str_replace( '/', '\\', $dir ) ], 'product:/', $text );
		}
		if ( defined( 'ABSPATH' ) ) {
			$abspath = rtrim( self::normalize_path( ABSPATH ), '/' ) . '/';
			$text    = str_replace( [ $abspath, str_replace( '/', '\\', $abspath ) ], 'wp:/', $text );
		}

		$replacements = [
			// Emails.
			'/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}/' => '[email]',
			// Bearer tokens.
			'/Bearer\s+[A-Za-z0-9\-._~+\/=]{8,}/i'       => 'Bearer [token]',
			// JWT-looking tokens.
			'/eyJ[A-Za-z0-9_-]{8,}\.[A-Za-z0-9._-]{8,}/' => '[token]',
			// Long hex strings (hashes, keys).
			'/\b[a-fA-F0-9]{32,}\b/'                     => '[token]',
			// Long base64-looking strings.
			'/\b[A-Za-z0-9+\/=]{40,}\b/'                 => '[token]',
			// Remaining absolute paths, keep the basename for readability.
			'~(?:[A-Za-z]:)?[/\\\\](?:[^\s\'"():*?]+[/\\\\])+([^\s\'"():*?/\\\\]+)~' => '.../$1',
		];
		$text         = (string) preg_replace( array_keys( $replacements ), array_values( $replacements ), $text );

		return substr( $text, 0, $max_length );
	}

	/**
	 * Normalize a path to forward slashes.
	 *
	 * @param string $path Path to normalize.
	 *
	 * @return string Normalized path.
	 */
	private static function normalize_path( $path ) {
		$path = (string) $path;
		if ( function_exists( 'wp_normalize_path' ) ) {
			return wp_normalize_path( $path );
		}

		return str_replace( '\\', '/', $path );
	}

	/**
	 * Normalize a throwable into the capture error shape.
	 *
	 * @param \Throwable $throwable Uncaught throwable.
	 *
	 * @return array Error data.
	 */
	private static function normalize_throwable( $throwable ) {
		$trace = $throwable->getTrace();

		$previous = $throwable->getPrevious();
		$depth    = 0;
		while ( null !== $previous && $depth < 2 ) {
			$trace[]  = [
				'file'     => $previous->getFile(),
				'line'     => $previous->getLine(),
				'function' => '[caused by] ' . get_class( $previous ),
			];
			$previous = $previous->getPrevious();
			$depth ++;
		}

		return [
			'type'         => ( $throwable instanceof \ParseError ) ? E_PARSE : E_ERROR,
			'message'      => get_class( $throwable ) . ': ' . $throwable->getMessage(),
			'file'         => $throwable->getFile(),
			'line'         => $throwable->getLine(),
			'trace'        => $trace,
			'is_exception' => true,
		];
	}

	/**
	 * Detect the request context the crash happened in.
	 *
	 * @return string One of cli, cron, ajax, rest, admin, frontend.
	 */
	private static function request_context() {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return 'cli';
		}
		if ( function_exists( 'wp_doing_cron' ) ? wp_doing_cron() : ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			return 'cron';
		}
		if ( function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return 'ajax';
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return 'rest';
		}
		if ( function_exists( 'is_admin' ) && is_admin() ) {
			return 'admin';
		}

		return 'frontend';
	}

	/**
	 * Read the crash data option of a product.
	 *
	 * @param string $key Product key.
	 *
	 * @return array Crash data with reports and meta keys.
	 */
	private static function read_data( $key ) {
		$data = get_option( $key . '_crash_data', [] );
		if ( ! is_array( $data ) ) {
			$data = [];
		}
		if ( ! isset( $data['reports'] ) || ! is_array( $data['reports'] ) ) {
			$data['reports'] = [];
		}
		if ( ! isset( $data['meta'] ) || ! is_array( $data['meta'] ) ) {
			$data['meta'] = [];
		}

		return $data;
	}

	/**
	 * Persist the crash data option of a product, never autoloaded.
	 *
	 * @param string $key  Product key.
	 * @param array  $data Crash data.
	 *
	 * @return bool Whether the write did not fail.
	 */
	private static function write_data( $key, $data ) {
		try {
			$option = $key . '_crash_data';
			if ( false === get_option( $option, false ) ) {
				return add_option( $option, $data, '', 'no' );
			}
			update_option( $option, $data );

			return true;
		} catch ( \Exception $e ) {
			self::obs( 'store_failed' );
		} catch ( \Throwable $e ) {
			self::obs( 'store_failed' );
		}

		return false;
	}

	/**
	 * Schedule the flush event for a product when consent is granted and no
	 * event or backoff is pending. Randomized 1-6h jitter avoids synchronized
	 * fleet-wide bursts.
	 *
	 * @param Product $product Product to schedule for.
	 */
	private static function schedule_flush( $product ) {
		if ( ! function_exists( 'wp_next_scheduled' ) || ! function_exists( 'wp_schedule_single_event' ) ) {
			return;
		}
		if ( ! self::is_consent_given( $product ) ) {
			return;
		}
		$key = $product->get_key();
		if ( false !== get_transient( $key . '_crash_backoff' ) ) {
			self::obs( 'backoff' );

			return;
		}
		$action_key = $key . '_crash_flush';
		if ( ! wp_next_scheduled( $action_key ) ) {
			wp_schedule_single_event( time() + ( wp_rand( 1, 6 ) * HOUR_IN_SECONDS ), $action_key );
		}
	}

	/**
	 * Check the logging consent for a product, using the Logger semantics.
	 *
	 * @param Product $product Product to check.
	 *
	 * @return bool Consent granted?
	 */
	private static function is_consent_given( $product ) {
		if ( ! class_exists( 'ThemeisleSDK\Modules\Logger' ) ) {
			return false;
		}

		return Logger::is_logging_active( $product );
	}

	/**
	 * Re-arm the flush schedule on normal loads when reports are pending.
	 */
	private function maybe_schedule_flush() {
		$data = self::read_data( $this->product->get_key() );
		if ( empty( $data['reports'] ) ) {
			return;
		}
		self::schedule_flush( $this->product );
	}

	/**
	 * Send the stored reports of the product to the crash endpoint. Clears the
	 * store on success, sets a backoff window on failure.
	 */
	public function send_reports() {
		$key  = $this->product->get_key();
		$data = self::read_data( $key );
		if ( empty( $data['reports'] ) ) {
			return;
		}
		if ( ! self::is_consent_given( $this->product ) ) {
			return;
		}
		if ( false !== get_transient( $key . '_crash_backoff' ) ) {
			return;
		}

		global $wp_version;
		$body = apply_filters(
			'themeisle_sdk_crash_report_data',
			[
				'site'        => get_site_url(),
				'slug'        => $this->product->get_slug(),
				'version'     => $this->product->get_version(),
				'wp_version'  => $wp_version,
				'php_version' => PHP_VERSION,
				'sdk_version' => self::get_sdk_version(),
				'locale'      => get_locale(),
				'license'     => apply_filters( $key . '_license_status', '' ),
				'reports'     => wp_json_encode( array_values( $data['reports'] ) ),
			],
			$this->product
		);

		$response = wp_remote_post(
			self::CRASH_ENDPOINT,
			[
				'timeout' => 3, //phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
				'body'    => $body,
			]
		);

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( ! is_wp_error( $response ) && $code >= 200 && $code < 300 ) {
			$data['reports'] = [];
			self::write_data( $key, $data );

			return;
		}

		set_transient( $key . '_crash_backoff', true, self::BACKOFF_SECONDS );
		self::obs( 'send_failed' );
	}

	/**
	 * Product version change handler: record the update time and lift the send
	 * backoff so a fixed release reports its health immediately.
	 */
	public function on_product_update() {
		$key = $this->product->get_key();
		delete_transient( $key . '_crash_backoff' );

		$data                        = self::read_data( $key );
		$data['meta']['last_update'] = time();
		self::write_data( $key, $data );
	}

	/**
	 * Adopt raw records left by the load.php early sentinel: run them through
	 * the full sanitize/fingerprint pipeline and drop the raw entries.
	 */
	private function adopt_sentinel_records() {
		$key  = $this->product->get_key();
		$data = self::read_data( $key );
		if ( empty( $data['raw'] ) || ! is_array( $data['raw'] ) ) {
			return;
		}

		$raw = $data['raw'];
		unset( $data['raw'] );
		self::write_data( $key, $data );

		foreach ( $raw as $record ) {
			if ( ! is_array( $record ) ) {
				continue;
			}
			self::capture(
				[
					'type'    => isset( $record['type'] ) ? (int) $record['type'] : E_ERROR,
					'message' => isset( $record['message'] ) ? (string) $record['message'] : '',
					'file'    => isset( $record['file'] ) ? (string) $record['file'] : '',
					'line'    => isset( $record['line'] ) ? (int) $record['line'] : 0,
					'trace'   => [],
				]
			);
		}
	}

	/**
	 * Compact crash summary attached to the uninstall feedback call:
	 * top reports by count, short messages, product-only frames.
	 *
	 * @param Product $product Product to summarize.
	 *
	 * @return array Compact summary, empty when there are no reports.
	 */
	public static function get_uninstall_summary( $product ) {
		$data = self::read_data( $product->get_key() );
		if ( empty( $data['reports'] ) ) {
			return [];
		}

		$reports = array_values( $data['reports'] );
		usort(
			$reports,
			function ( $a, $b ) {
				return (int) $b['count'] - (int) $a['count'];
			}
		);
		$reports = array_slice( $reports, 0, self::SUMMARY_MAX_REPORTS );

		$summary = [];
		foreach ( $reports as $report ) {
			$frames = [];
			foreach ( (array) $report['trace'] as $frame ) {
				if ( isset( $frame['file'] ) && ( 0 === strpos( (string) $frame['file'], 'product:' ) || 0 === strpos( (string) $frame['file'], 'sdk:' ) ) ) {
					$frames[] = $frame;
				}
			}
			$summary[] = [
				'fingerprint'     => isset( $report['fingerprint'] ) ? $report['fingerprint'] : '',
				'type'            => $report['type'],
				'event_type'      => isset( $report['event_type'] ) ? $report['event_type'] : 'fatal_error',
				'message'         => substr( (string) $report['message'], 0, self::SUMMARY_MESSAGE_LENGTH ),
				'file'            => $report['file'],
				'line'            => $report['line'],
				'trace'           => $frames,
				'count'           => $report['count'],
				'first_seen'      => $report['first_seen'],
				'last_seen'       => $report['last_seen'],
				'product_version' => $report['product_version'],
			];
		}

		return $summary;
	}

	/**
	 * Short-form Site Health section: one row per product having stored
	 * crashes, no messages and no traces to keep the page lean.
	 *
	 * @param array $info Debug information sections.
	 *
	 * @return array Debug information sections.
	 */
	public static function add_debug_information( $info ) {
		$fields = [];
		foreach ( self::$registry as $product ) {
			$data = self::read_data( $product->get_key() );
			if ( empty( $data['reports'] ) ) {
				continue;
			}
			$total = 0;
			$top   = null;
			$last  = 0;
			foreach ( $data['reports'] as $report ) {
				$total += (int) $report['count'];
				$last   = max( $last, (int) $report['last_seen'] );
				if ( null === $top || $report['count'] > $top['count'] ) {
					$top = $report;
				}
			}
			$fields[ $product->get_key() ] = [
				'label' => $product->get_friendly_name(),
				'value' => sprintf(
					'%d distinct / %d total, last on %s, top: type %d @ %s:%d ×%d',
					count( $data['reports'] ),
					$total,
					gmdate( 'Y-m-d H:i', $last ),
					$top['type'],
					$top['file'],
					$top['line'],
					$top['count']
				),
			];
		}

		if ( empty( $fields ) ) {
			return $info;
		}

		$info['themeisle-sdk-crash-reports'] = [
			'label'       => 'ThemeIsle SDK Crash Reports',
			'description' => 'Locally stored crash summaries for ThemeIsle products. Full detail lives in the per-product crash data option.',
			'fields'      => $fields,
		];

		return $info;
	}

	/**
	 * Current SDK version, from the loader globals.
	 *
	 * @return string SDK version.
	 */
	private static function get_sdk_version() {
		global $themeisle_sdk_max_version;

		return empty( $themeisle_sdk_max_version ) ? '' : (string) $themeisle_sdk_max_version;
	}

	/**
	 * Observability breadcrumb for debugging the reporter itself in the field.
	 *
	 * @param string $reason Drop or failure reason.
	 */
	private static function obs( $reason ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[TISDK_CRASH] ' . $reason ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Reset the static state. Test helper only.
	 */
	public static function reset() {
		self::$registry           = [];
		self::$exception_captured = false;
	}
}
