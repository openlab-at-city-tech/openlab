<?php

/**
 * Alive class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Alive {



	// Checks and launch crawler procedures
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Start the check
	 */
	public static function check() {
		self::start(get_class());
	}



	/**
	 * Check running crawler
	 */
	public static function start($source) {

		// Load util file first
		require_once(WPLNST_PATH.'/core/util.php');

		// Version components
		$source::start_version();

		// Load common plugin dependencies
		wplnst_require('core', 'types');

		// Load custom nonce system
		wplnst_require('core', 'nonce/nonce');

		// Check crawl request arguments
		if (!(defined('DOING_AJAX') && DOING_AJAX) || empty($_GET['wplnst_crawler']) || empty($_GET['wplnst_nonce']) || empty($_GET['wplnst_slug'])) {

			// Check notifications
			if (!empty($_GET['wplnst_notify_email']) && 'on' == $_GET['wplnst_notify_email']) {

				// And finally check notify nonce
				if (!empty($_GET['wplnst_notify_nonce']) && self::verify_notify_nonce($_GET['wplnst_notify_nonce'])) {
					add_action('plugins_loaded', array($source, 'notify'));
				}

			// Check cURL needed before
			} elseif (wplnst_is_curl_enabled()) {

				// Activity checks
				self::activity();
			}

		// Check this install crawler slug
		} elseif ($_GET['wplnst_slug'] == self::get_crawler_slug()) {

			/**
			 * From here we are under crawler scope,
			 * based on admin-ajax mode and special URL arguments.
			 */

			// Debug point
 			wplnst_debug('wplnst_slug arg ok', 'alive');

			// Check cURL loaded
			if (wplnst_is_curl_enabled()) {

				// Debug point
				wplnst_debug('curl enabled', 'alive');

				// Check scan integer and nonce argument
				$scan_id = (int) $_GET['wplnst_crawler'];
				if ($scan_id > 0) {

					// Check possible thread
					$thread_id = isset($_GET['wplnst_thread'])? $_GET['wplnst_thread'] : false;

					// Debug point
					wplnst_debug('Valid scan_id '.$scan_id, 'alive');

					// Check scan and nonce
					if (false === ($scan_row = self::verify_crawler($scan_id, $thread_id, $_GET['wplnst_nonce']))) {

						// Debug point
						wplnst_debug('scan '.$scan_id.' - thread '.(empty($thread_id)? 'false' : $thread_id).' - verify crawler error', 'alive');

					// Threads control
					} elseif (false === ($thread_id = self::threading($scan_row, $thread_id))) {

						// Debug point
						wplnst_debug('scan '.$scan_id.' - thread '.$thread_id.' - threading error', 'alive');

					// Ok
					} else {

						// Debug point
						wplnst_debug('scan '.$scan_id.' - thread '.$thread_id.' - instantiate crawler', 'alive');

						// Run instance of crawler
						$source::instantiate_crawler($scan_row, $thread_id);
					}
				}
			}

			// Debug point
			wplnst_debug((empty($scan_row)? '' : 'scan '.$scan_row->scan_id.' - ').'thread '.(empty($thread_id)? '' : $thread_id).' terminate', 'alive');

			// End
			die;
		}
	}



	/**
	 * Specific version components at start
	 */
	protected static function start_version() {

		// Plugin definitions
		wplnst_require('core', 'plugin');
	}



	/**
	 * Start or continue active scan
	 */
	public static function run($scan_id, $hash, $thread_id = false) {

		// Debug point
		wplnst_debug('scan '.$scan_id.' - '.(empty($thread_id)? '' : 'thread '.$thread_id.' ').'run', 'alive');

		// Check salt file
		WPLNST_Core_Nonce::check_salt_file();

		// Load cURL wrapper library
		wplnst_require('core', 'curl');

		// Spawn crawler call
		WPLNST_Core_CURL::spawn(array(
			'CURLOPT_URL' 				=> self::get_crawler_url($scan_id, $hash, $thread_id),
			'CURLOPT_USERAGENT' 		=> wplnst_get_tsetting('user_agent'),
		));
	}



	/**
	 * Check last active scan activity and re-active scan if needed.
	 * This check runs in a normal WP execution, and not under any special URL,
	 * except at the end of a crawler process to check queued scans.
	 */
	public static function activity($skip_check = false) {

		// Skip checks from the end
		if (!$skip_check) {

			// Avoid checks in plugins page
			if (is_admin() && false !== stripos($_SERVER['REQUEST_URI'], '/plugins.php')) {
				return;
			}

			// Preliminary check to avoid constant database queries
			$timestamp = (int) get_option('wplnst_crawler_timestamp');
			if ($timestamp > 0 && time() - $timestamp <= wplnst_get_nsetting('crawler_alive')) {
				return;
			}

			// Reset timer, and exit if this is the first time
			update_option('wplnst_crawler_timestamp', time());
			if (empty($timestamp)) {
				return;
			}
		}

		// Retrieve active scans
		$max_scans = wplnst_get_nsetting('max_scans');
		$scans = self::get_ready_scans('play', 'started_at ASC', $max_scans);

		// Check queued merge
		if (count($scans) < $max_scans) {
			$scans_queued = self::get_ready_scans('queued', 'enqueued_at ASC', $max_scans - count($scans));
			if (!empty($scans_queued)) {
				$scans = array_merge($scans, $scans_queued);
			}
		}

		// Globals
		global $wpdb;

		// Enum active scans
		foreach ($scans as $scan_info) {

			// Check threads cuota
			$threads = @json_decode($scan_info->threads, true);
			if (!empty($threads) && is_array($threads)) {

				// Initialize
				$active = 0;

				// Total timeout and minor correction
				$total_timeout = wplnst_get_nsetting('connect_timeout', $scan_info->connect_timeout) + wplnst_get_nsetting('request_timeout', $scan_info->request_timeout) + wplnst_get_nsetting('extra_timeout');

				// Count active threads
				foreach ($threads as $thread_id => $thread) {
					if ('on' == $thread['status']) {
						if ((time() - (int) $thread['timestamp']) <= $total_timeout) {
							$active++;
						}
					}
				}

				// Check free limits
				if ($active >= wplnst_get_nsetting('max_threads', $scan_info->max_threads)) {
					continue;
				}
			}

			// Check and cast queued to play
			if ('queued' == $scan_info->status) {

				// Configure update
				$update = array('status' => 'play');
				$current_time = current_time('mysql', true);

				// First scan play
				if (empty($scan_info->started_at) || '0000-00-00 00:00:00' == $scan_info->started_at) {
					$update['started_at'] = $current_time;
					$update['stopped_at'] = '0000-00-00 00:00:00';

				// Continue
				} else {

					// Play again
					$update['continued_at'] = $current_time;
				}

				// And update
				$wpdb->update($wpdb->prefix.'wplnst_scans', $update, array('scan_id' => $scan_info->scan_id));
			}

			// Debug point
			wplnst_debug('scan '.$scan_info->scan_id.' launch from activity()', 'alive');

			// New thread
			self::run($scan_info->scan_id, $scan_info->hash);
		}
	}



	// Internal procedures
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Control current thread and check for free threads
	 * This check runs under special URL dedicated to threading
	 */
	private static function threading($scan_row, $current_thread_id) {

		// Globals
		global $wpdb;

		// Check threads array
		$threads = @json_decode($scan_row->threads, true);
		$threads = (empty($threads) || !is_array($threads))? array() : $threads;

		// Debug point
		wplnst_debug('threading thread id '.$current_thread_id, 'alive');

		// Check invalid thread id
		if (!empty($current_thread_id) && !isset($threads[$current_thread_id])) {
			wplnst_debug('no registered thread', 'alive');
			return false;
		}

		// Initialize
		$active = 0;
		$new_thread_id = false;

		// Total timeout and minor correction
		$total_timeout = wplnst_get_nsetting('connect_timeout', $scan_row->connect_timeout) + wplnst_get_nsetting('request_timeout', $scan_row->request_timeout) + wplnst_get_nsetting('extra_timeout');

		// Sum the alive time plus 1 minute grace period
		$total_timeout += 60 + (int) wplnst_get_nsetting('crawler_alive');

		// Max crawler threads allowed
		$max_threads = wplnst_get_nsetting('max_threads', $scan_row->max_threads);

		// Inspect total active threads
		foreach ($threads as $thread_id => $thread) {

			// No checks for current thread
			if (!empty($current_thread_id) && $current_thread_id == $thread_id) {
				$active++;

			// Only active threads
			} elseif ('on' == $thread['status']) {

				// Timestamp limit
				if ((time() - (int) $thread['timestamp']) > $total_timeout) {
					wplnst_debug('inactivated an active thread - timestamp: '.$thread['timestamp'].' - total: '.$total_timeout.' - time: '.time());
					$threads[$thread_id]['status'] = 'off';

				// Active
				} else {
					$active++;
				}
			}
		}

		// No current thread allowed
		if ($active > $max_threads) {
			wplnst_debug('max threads reached - active: '.$active.' - max threads: '.$max_threads);
			if (!empty($current_thread_id)) {
				$threads[$current_thread_id]['status'] = 'off';
			}
			$current_thread_id = false;

		// Check new thread
		} elseif ($active < $max_threads) {
			$active++;
			$new_thread_id = empty($threads)? 1 : (int) max(array_keys($threads)) + 1;
			$threads[$new_thread_id] = array('status' => 'on', 'timestamp' => time());
		}

		// Rebuild current thread
		if (!empty($current_thread_id)) {
			$threads[$current_thread_id] = array('status' => 'on', 'timestamp' => time());
		}

		// Sanitize threads
		$threads_safe = [];
		foreach ($threads as $thread_id => $thread) {
			if (!empty($thread) && is_array($thread) && !empty($thread['status']) && 'on' == $thread['status']) {
				$threads_safe[$thread_id] = $thread;
			}
		}

		// Update threads data
		$wpdb->update($wpdb->prefix.'wplnst_scans', array('threads' => @json_encode($threads_safe)), array('scan_id' => $scan_row->scan_id));

		// Launch new thread
		if (!empty($new_thread_id) && $active < $max_threads) {
			self::run($scan_row->scan_id, $scan_row->hash);
		}

		// Done
		return empty($current_thread_id)? $new_thread_id : $current_thread_id;
	}



	// Active scans info
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Retrieve active scan row identifier, ready state and threads value
	 */
	private static function get_ready_scans($status, $order, $max_scans) {

		// Globals
		global $wpdb;

		// Retrieve scans
		$scans = $wpdb->get_results($wpdb->prepare('SELECT SQL_NO_CACHE scan_id, status, ready, hash, started_at, threads, max_threads, connect_timeout, request_timeout FROM '.$wpdb->prefix.'wplnst_scans WHERE ready = 1 AND status = %s ORDER BY '.esc_sql($order), $status));

		// Check results
		if (empty($scans) || !is_array($scans)) {
			return array();
		}

		// Initialize
		$index = 0;
		$allowed = array();

		// Enum scans
		foreach ($scans as $scan) {

			// Check index
			$index++;
			if ($index > $max_scans) {

				// Queue scan
				if ('play' == $scan->status) {
					$current_time = current_time('mysql', true);
					$wpdb->query($wpdb->prepare('UPDATE '.$wpdb->prefix.'wplnst_scans SET status = "queued", enqueued_at = %s, stopped_at = %s WHERE scan_id = %d', $current_time, $current_time, $scan->scan_id));
				}

				// Next
				continue;
			}

			// Add allowed
			$allowed[] = $scan;
		}

		// Preserve allowed scans
		return $allowed;
	}



	// Crawling related data
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Verify if crawler exists, is ready, and nonce
	 */
	private static function verify_crawler($scan_id, $thread_id, $nonce) {

		// Globals
		global $wpdb;

		// Retrieve and check scan
		$scan_row = $wpdb->get_row($wpdb->prepare('SELECT SQL_NO_CACHE scan_id, status, ready, hash, threads, max_threads, connect_timeout, request_timeout FROM '.$wpdb->prefix.'wplnst_scans WHERE scan_id = %d', $scan_id));
		if (empty($scan_row) || !is_object($scan_row)) {
			return false;
		}

		// Check scan status
		if (empty($scan_row->status) || 'wait' == $scan_row->status || 'end' == $scan_row->status) {
			return false;
		}

		// Check scan ready
		if (empty($scan_row->ready) || 1 != (int) $scan_row->ready) {
			return false;
		}

		// Check nonce and return scan
		return self::verify_crawler_nonce($nonce, $scan_row->hash, $thread_id)? $scan_row : false;
	}



	/**
	 * Load a crawler instance
	 */
	protected static function instantiate_crawler($scan_row, $thread_id) {

		// Load dependencies
		wplnst_require('core', 'crawler');

		// Instance
		WPLNST_Core_Crawler::instantiate(array(
			'scan_id' 		=> $scan_row->scan_id,
			'thread_id' 	=> $thread_id,
			'crawler_url' 	=> self::get_crawler_url($scan_row->scan_id, $scan_row->hash, $thread_id),
		));
	}



	/**
	 * Verify crawler nonce from scan id
	 */
	private static function verify_crawler_nonce($value, $hash, $thread_id = false) {
		return WPLNST_Core_Nonce::verify_nonce($value, 'crawl-scan-'.$hash.(empty($thread_id)? '' : '-thread-'.$thread_id.'-'.self::get_crawler_slug()));
	}



	/**
	 * Create nonce for the crawler
	 */
	private static function get_crawler_nonce($hash, $thread_id = false) {
		return WPLNST_Core_Nonce::create_nonce('crawl-scan-'.$hash.(empty($thread_id)? '' : '-thread-'.$thread_id.'-'.self::get_crawler_slug()));
	}



	/**
	 * Compose current crawler URL
	 */
	public static function get_crawler_url($scan_id, $hash, $thread_id = false) {

		// Default args
		$args = array(
			'wplnst_crawler' => $scan_id,
			'wplnst_nonce' 	 => self::get_crawler_nonce($hash, $thread_id),
			'wplnst_slug' 	 => self::get_crawler_slug(),
		);

		// Check thread arg
		if (!empty($thread_id)) {
			$args['wplnst_thread'] = $thread_id;
		}

		// Compose URL
		return add_query_arg($args, rtrim(admin_url('admin-ajax.php'), '/'));
	}



	/**
	 * Retrieve, check or generate unique crawler slug
	 */
	public static function get_crawler_slug() {

		// Retrieve current slug
		$crawler_slug = get_option('wplnst_crawler_slug');

		// Check valid page slug
		if (empty($crawler_slug) || 16 != strlen($crawler_slug) || !preg_match('/^[a-z0-9]+$/', $crawler_slug)) {

			// Generate new slug
			$crawler_slug = strtolower(WPLNST_Core_Nonce::generate_password(16, false, false));

			// And update
			update_option('wplnst_crawler_slug', $crawler_slug);
		}

		// Done
		return $crawler_slug;
	}



	// Notifications launchers
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Verify crawler nonce from scan id
	 */
	protected static function verify_notify_nonce($value) {
		return WPLNST_Core_Nonce::verify_nonce($value, 'crawl-notify-'.self::get_crawler_slug());
	}



	/**
	 * Create nonce for the notifier
	 */
	public static function get_notify_nonce() {
		return WPLNST_Core_Nonce::create_nonce('crawl-notify-'.self::get_crawler_slug());
	}



	/**
	 * Check pending notifications
	 */
	public static function notify() {

		// Load translations
		WPLNST_Core_Plugin::load_plugin_textdomain();

		// Include and call class
		wplnst_require('core', 'notify');
		WPLNST_Core_Notify::check();

		// End
		die;
	}



}