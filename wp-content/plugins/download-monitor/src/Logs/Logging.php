<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Logging class.
 */
class DLM_Logging {

	/**
	 * Holds the class object.
	 *
	 * @since 4.6.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Class constructor
	 * 
	 * @since 4.6.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_log_dlm_xhr_download', array( $this, 'xhr_log_download' ), 15 );
		add_action( 'wp_ajax_nopriv_log_dlm_xhr_download', array( $this, 'xhr_log_download' ), 15 );
		add_filter( 'dlm_log_entries', array( $this, 'log_entries' ), 15, 2 );
		add_filter( 'dlm_log_values', array( $this, 'log_values' ), 15, 1 );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return object The DLM_Logging object.
	 * @since 4.6.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_Logging ) ) {
			self::$instance = new DLM_Logging();
		}

		return self::$instance;

	}

	/**
	 * Check if logging is enabled.
	 * Modified in version 4.5.0
	 *
	 * @return bool
	 *
	 * @since 4.5.0
	 */
	public static function is_logging_enabled() {

		return apply_filters( 'dlm_enable_reports', true );
	}

	/**
	 * Get the type of IP logging that is configured in settings
	 *
	 * @return string
	 */
	public function get_ip_logging_type() {
		$type = get_option( 'dlm_logging_ip_type', 'full' );
		if ( empty( $type ) ) {
			$type = 'full';
		}

		return $type;
	}

	/**
	 * Check if user agent logging is enabled.
	 *
	 * @return mixed|null
	 *
	 * @since 4.9.6
	 */
	public function is_ua_logging_enabled() {
		/**
		 * Hook to disable logging of user agents
		 *
		 * @hook  dlm_logging_user_agent
		 *
		 * @since 4.9.6
		 */
		return apply_filters( 'dlm_logging_user_agent', true );
	}

	/**
	 * Check if 'dlm_count_unique_ips' is enabled
	 *
	 * @return bool
	 */
	public function is_count_unique_ips_only() {
		return ( '1' === get_option( 'dlm_count_unique_ips', 0 ) && 'full' === get_option( 'dlm_logging_ip_type', 'full' ) );
	}

	/**
	 * Check if visitor has downloaded version
	 *
	 * @param DLM_Download_Version $version
	 *
	 * @return bool
	 */
	public function has_ip_downloaded_version( $version ) {
		global $wpdb;

		return ( absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->download_log} WHERE `version_id` = %d AND `user_ip` = %s AND `download_status` IN ('completed','redirected')", $version->get_id(), DLM_Utils::get_visitor_ip() ) ) ) > 0 );
	}

	/**
	 * Check if visitor has downloaded version
	 *
	 * @param mixed $version Version object or version ID.
	 *
	 * @return bool
	 * @since 4.6.0
	 */
	public function has_uuid_downloaded_version( $version ) {
		global $wpdb;
		// Check if version ID is passed or object
		if ( is_int( $version ) ) {
			$version_id = $version;
		} else {
			$version_id = $version->get_id();
		}

		return ( absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->download_log} WHERE `version_id` = %d AND `uuid` = %s AND `download_status` IN ('completed','redirected')", $version_id, DLM_Utils::get_visitor_uuid() ) ) ) > 0 );
	}

	/**
	 * @param int $download_id
	 * @param int $version_id
	 *
	 * @return string
	 */
	public function generate_xhr_log_token( $download_id, $version_id ) {
		$token = bin2hex( random_bytes( 32 ) );

		set_transient(
			'dlm_xhr_log_' . $token,
			array(
				'download_id' => absint( $download_id ),
				'version_id'  => absint( $version_id ),
			),
			apply_filters( 'dlm_xhr_log_token_ttl', 30 * MINUTE_IN_SECONDS )
		);

		return $token;
	}

	/**
	 * @param string $token
	 * @param int    $download_id
	 * @param int    $version_id
	 *
	 * @return bool
	 */
	private function consume_xhr_log_token( $token, $download_id, $version_id ) {
		$key  = 'dlm_xhr_log_' . $token;
		$data = get_transient( $key );
		delete_transient( $key );

		if ( ! is_array( $data ) || ! isset( $data['download_id'], $data['version_id'] ) ) {
			return false;
		}

		return absint( $data['download_id'] ) === absint( $download_id )
			&& absint( $data['version_id'] ) === absint( $version_id );
	}

	/**
	 * Log the XHR download
	 *
	 * @return void
	 */
	public function xhr_log_download() {
		if ( ! isset( $_POST['download_id'] ) || ! isset( $_POST['version_id'] ) ) {
			if ( WP_DLM::dlm_x_sendfile() ) {
				wp_send_json_error( 'Missing download_id or version_id. X-Sendfile is enabled, so this is a problem.' );
			}
			wp_send_json_error( 'Missing download_id or version_id' );
		}

		check_ajax_referer( 'dlm_ajax_nonce', 'nonce' );

		$download_id = absint( $_POST['download_id'] );
		$version_id  = absint( $_POST['version_id'] );

		if ( ! isset( $_POST['log_token'] )
			|| ! $this->consume_xhr_log_token( sanitize_text_field( wp_unslash( $_POST['log_token'] ) ), $download_id, $version_id )
		) {
			wp_send_json_error( 'Invalid or expired log token' );
		}

		// Let's make sure the DLM_DOING_XHR is defined
		if ( ! defined( 'DLM_DOING_XHR' ) ) {
			define( 'DLM_DOING_XHR', true );
		}

		$status           = sanitize_text_field( wp_unslash( $_POST['status'] ) );
		$allowed_statuses = apply_filters( 'dlm_xhr_log_allowed_statuses', array( 'completed', 'redirected', 'failed' ) );

		if ( ! in_array( $status, $allowed_statuses, true ) ) {
			wp_send_json_error( 'Invalid status' );
		}

		$cookie      = 'true' === $_POST['cookie'];
		$current_url = ( isset( $_POST['currentURL'] ) ) ? esc_url_raw( $_POST['currentURL'] ) : '-';
		// Set our objects
		try {
			$download = download_monitor()->service( 'download_repository' )->retrieve_single( $download_id );
		} catch ( \Exception $e ) {
			die();
		}

		try {
			$version = download_monitor()->service( 'version_repository' )->retrieve_single( $version_id );
		} catch ( \Exception $e ) {
			die();
		}

		$download->set_version( $version );
		// Truly log the corresponding status
		$this->log( $download, $version, $status, $cookie, $current_url );

		if ( 'completed' !== $status ) {
			die();
		}

		try {
			$download = download_monitor()->service( 'download_repository' )->retrieve_single( $download_id );
		} catch ( \Exception $e ) {
			die();
		}

		$count    = $download->get_download_count();
		$response = array( 'download_count' => $count );
		$formats  = isset( $_POST['count_formats'] ) ? array_filter( array_map( 'sanitize_key', (array) $_POST['count_formats'] ) ) : array();

		if ( $formats ) {
			$response['counts'] = array();

			foreach ( $formats as $format ) {
				$response['counts'][ $format ] = $this->format_download_count_text( $format, $count );
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Translate a download count into the requested display format.
	 *
	 * @param string $format
	 * @param int    $count
	 *
	 * @return string
	 */
	private function format_download_count_text( $format, $count ) {
		switch ( $format ) {
			case 'standard':
				return sprintf( _n( '%d download', '%d downloads', $count, 'download-monitor' ), $count );

			case 'times':
				return sprintf( _n( 'Downloaded %d time', 'Downloaded %d times', $count, 'download-monitor' ), $count );

			case 'times-value':
				return sprintf( _n( '%d time', '%d times', $count, 'download-monitor' ), $count );
		}

		/**
		 * Filters the display text for a download count format that isn't
		 * one of the built-in ones above. Defaults to the raw count.
		 *
		 * @hook dlm_xhr_download_count_format_text
		 *
		 * @param int    $count  The raw download count.
		 * @param string $format The requested format key.
		 */
		return apply_filters( 'dlm_xhr_download_count_format_text', $count, $format );
	}

	/**
	 * Create a log if logging is enabled
	 *
	 * @param string $type
	 * @param string $status
	 * @param string $message
	 * @param DLM_Download $download
	 * @param DLM_Download_Version $version
	 * @param bool $cookie
	 * @param string $url
	 */
	public function log( $download, $version, $status = 'completed', $cookie = true, $url = '-' ) {

		$cookie_manager = DLM_Cookie_Manager::get_instance();
		// setup new log item object.
		if ( false === $cookie_manager->check_cookie_meta( 'wp_dlm_downloading', $download->get_id() ) ) {
			$ip       = DLM_Utils::get_visitor_ip();
			$log_item = new DLM_Log_Item();
			$log_item->set_user_id( absint( get_current_user_id() ) );
			$log_item->set_download_id( absint( $download->get_id() ) );
			$log_item->set_user_ip( $ip );
			$log_item->set_user_uuid( $ip );
			$log_item->set_user_agent( DLM_Utils::get_visitor_ua() );
			$log_item->set_version_id( absint( $version->get_id() ) );
			$log_item->set_version( $version->get_version() );
			$log_item->set_download_status( $status );
			$log_item->set_current_url( $url );

			if ( $cookie ) {
				$cookie_manager->set_cookie( $download, array( 'meta' => array( array( 'wp_dlm_downloading' => $download->get_id() ) ) ) );
			}
			// persist log item.
			download_monitor()->service( 'log_item_repository' )->persist( $log_item ); 
		}
	}

	/**
	 * Failproof way to log a download if DB upgrade did not proceed correctly
	 *
	 * @param $entries
	 * @param $item
	 *
	 * @return void
	 * @since 40.6.3
	 */
	public function log_entries( $entries, $item ) {
		global $wpdb;

		if ( isset( $wpdb->download_log ) && DLM_Utils::column_checker( $wpdb->download_log, 'uuid' ) ) {
			$entries['uuid'] = $item->get_user_uuid();
		}

		if ( isset( $wpdb->download_log ) && DLM_Utils::column_checker( $wpdb->download_log, 'download_location' ) ) {
			$entries['download_location'] = $item->get_current_url();
		}

		if ( isset( $wpdb->download_log ) && DLM_Utils::column_checker( $wpdb->download_log, 'download_category' ) ) {
			$entries['download_category'] = $item->get_download_categories( $item->get_download_id() );
		}

		return $entries;
	}

	/**
	 * Failproof way to log a download if DB upgrade did not proceed correctly
	 *
	 * @param $entries
	 * @param $item
	 *
	 * @return void
	 * @since 4.6.3
	 */
	public function log_values( $entries ) {
		global $wpdb;

		if ( isset( $wpdb->download_log ) && DLM_Utils::column_checker( $wpdb->download_log, 'uuid' ) ) {
			$entries[] = '%s';
		}

		if ( isset( $wpdb->download_log ) && DLM_Utils::column_checker( $wpdb->download_log, 'download_location' ) ) {
			$entries[] = '%s';
		}

		if ( isset( $wpdb->download_log ) && DLM_Utils::column_checker( $wpdb->download_log, 'download_category' ) ) {
			$entries[] = '%s';
		}

		return $entries;
	}

	/**
	 * Check if admin log should be ignored
	 *
	 * @return bool
	 * @since 4.9.6
	 */
	public static function ignore_admin_log() {
		/**
		 * Hook to disable logging of admin hits
		 *
		 * @hook  dlm_log_ignore_admin_download_count
		 *
		 * @since 4.9.6
		 */
		if ( apply_filters( 'dlm_log_ignore_admin_download_count', true ) && is_user_logged_in() && in_array( 'administrator', wp_get_current_user()->roles, true ) ) {
			return true;
		}

		return false;
	}
}

