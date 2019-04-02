<?php

/**
 * Admin Scans Submit class
 *
 * @package WP Link Status
 * @subpackage Admin
 */
class WPLNST_Admin_Scans_Submit {



	/**
	 * Notice updated and error
	 */
	public $notice_error = false;
	public $notice_warning = false;
	public $notice_success = false;
	public $notice_crawler = false;



	/**
	 * Handle submit in the constructor
	 */
	public function __construct($scans) {

		// Globals
		global $wpdb;

		// Check scan identifier
		$scan_id = isset($_POST['scan_id'])? (int) $_POST['scan_id'] : false;
		if (false === $scan_id || (isset($_GET['scan_id']) && $scan_id != (int) $_GET['scan_id'])) {
			$this->notice_error = WPLNST_Admin::get_text('invalid_data');
			return;
		}

		// Check existing scan
		if ($scan_id > 0 && false === ($scan = $scans->get_scan_by_id($scan_id))) {
			$this->notice_error = WPLNST_Admin::get_text('scan_not_found');
			return;
		}

		// Check submitted nonce
		$nonce = isset($_POST['scan_edit_nonce'])? $_POST['scan_edit_nonce'] : false;
		if (false === $nonce || !wp_verify_nonce($nonce, 'scan-edit-'.(empty($scan)? '0' : $scan->hash))) {
			$this->notice_error = WPLNST_Admin::get_text('invalid_nonce');
			return;
		}

		// Collect data
		$max_threads = isset($_POST['tx-threads'])? (int) $_POST['tx-threads'] : 0;
		$connect_timeout = isset($_POST['tx-connect-timeout'])? (int) $_POST['tx-connect-timeout'] : 0;
		$request_timeout = isset($_POST['tx-request-timeout'])? (int) $_POST['tx-request-timeout'] : 0;

		// Update data array
		$updates = array(
			'modified_by' 		=> get_current_user_id(),
			'modified_at' 		=> current_time('mysql', true),
			'name' 				=> isset($_POST['tx-name'])? substr(trim(stripslashes($_POST['tx-name'])), 0, 255) : '',
			'max_threads' 		=> $max_threads,
			'connect_timeout' 	=> $connect_timeout,
			'request_timeout' 	=> $request_timeout,
		);


		// Initialize
		$config = array();


		/* Notifications */

		// Empty of not completed
		if (empty($scan) || 'end' != $scan->status) {

			// Check config for existing scan
			if (!empty($scan) && 'wait' != $scan->status) {
				$config = @json_decode($scan->row->config, true);
				if (empty($config) || !is_array($config)) {
					$config = array();
				}
			}

			// Set e-mail settings
			$config['notify_default']		= WPLNST_Core_Types::check_post_value('ck-notify-default', 'on', false);
			$config['notify_address']		= WPLNST_Core_Types::check_post_value('ck-notify-address', 'on', false);
			$config['notify_address_email']	= isset($_POST['tx-notify-address-email'])? substr(trim(stripslashes($_POST['tx-notify-address-email'])), 0, 255) : '';
		}


		/* Editable scan */

		// Editable fields
		if (empty($scan) || 'wait' == $scan->status) {

			// General tab
			$config['destination_type'] 	= WPLNST_Core_Types::check_post_value('sl-destination-type', array_keys(WPLNST_Core_Types::get_destination_types()), 'all');
			$config['time_scope'] 			= WPLNST_Core_Types::check_post_value('sl-time-scope', array_keys(WPLNST_Core_Types::get_time_scopes()), 'anytime');
			$config['link_types']			= WPLNST_Core_Types::check_post_value('ck-link-type', array_keys(WPLNST_Core_Types::get_link_types()), array());
			$config['crawl_order'] 			= WPLNST_Core_Types::check_post_value('sl-crawl-order', array_keys(WPLNST_Core_Types::get_crawl_order()), 'asc');
			$config['redir_status']			= WPLNST_Core_Types::check_post_value('ck-redir-status', 'on', false);
			$config['malformed']			= WPLNST_Core_Types::check_post_value('ck-malformed-links', 'on', false);

			// Content options tab
			$config['post_types']			= WPLNST_Core_Types::check_post_value('ck-post-type', array_keys(WPLNST_Core_Types::get_post_types()), array());
			$config['post_status']			= WPLNST_Core_Types::check_post_value('ck-post-status', array_keys(WPLNST_Core_Types::get_post_status()), array());
			$config['comment_types'] 		= WPLNST_Core_Types::check_post_value('ck-comment-type', array_keys(WPLNST_Core_Types::get_comment_types()), array());
			$config['blogroll'] 			= WPLNST_Core_Types::check_post_value('ck-blogroll', 'on', false);

			// Links status tab
			$config['status_levels'] 		= WPLNST_Core_Types::check_post_value('ck-status-level', array_keys(WPLNST_Core_Types::get_status_levels()), array());
			$config['status_codes']			= WPLNST_Core_Types::check_post_value('ck-status-code', array_keys(WPLNST_Core_Types::get_status_codes_raw()), array());

			// Filters
			$config['custom_fields']		= WPLNST_Core_Types::check_post_elist('scan_custom_fields');
			$config['anchor_filters']		= WPLNST_Core_Types::check_post_elist('scan_anchor_filters');
			$config['include_urls']			= WPLNST_Core_Types::check_post_elist('scan_include_urls');
			$config['exclude_urls']			= WPLNST_Core_Types::check_post_elist('scan_exclude_urls');
			$config['html_attributes']		= WPLNST_Core_Types::check_post_elist('scan_html_attributes');
			$config['filtered_query']		= WPLNST_Core_Types::check_post_value('ck-filtered-query', 'on', false);
		}

		// Add to update
		if (!empty($config)) {
			$updates['config'] = @json_encode($config);
		}


		/* Save and run */

		// Check run attempt
		$do_play = (isset($_POST['scan_run']) && 1 == (int) $_POST['scan_run']);

		// Abort when cURL is not enabled
		if ($do_play && !wplnst_is_curl_enabled()) {
			$do_play = false;
		}

		// Check new scan
		if (empty($scan_id)) {

			// Create unique random hash associated with the scan
			$hash = md5(rand(0, 9999).microtime().rand(0, 9999));

			// Add scan and redirect
			$wpdb->insert($wpdb->prefix.'wplnst_scans', array_merge($updates, array('status' => 'wait', 'hash' => $hash, 'created_at' => current_time('mysql', true))));

			// New identifier
			$insert_id = (int) $wpdb->insert_id;

			// Check error
			if (empty($insert_id)) {

				// Message error
				$this->notice_error = __('Something went wrong adding the new scan. Please <a href="javascript:history.back();">go back</a> and attempt again to submit form.');

			// Scan added
			} else {

				// Run scan
				if ($do_play) {

					// Check max scans running
					if (!$scans->can_play_more_scans()) {

						// Maximum reached
						$started = 'max_scans';

						// Queue scan
						$scans->queue_scan($insert_id);

					// Retrieve scan
					} elseif (false === ($scan = $scans->get_scan_by_id($insert_id))) {

						// Can`t start scan
						$started = 'error';

					// Check ready
					} elseif (true === $scans->is_scan_ready($scan)) {

						// Update scan ready
						$scans->update_scan_ready($scan->id, true);

						// Attempt to run scan
						if (false === $scans->play_scan($scan->id)) {

							// Can`t start scan
							$started = 'error';

						// Running
						} else {

							// Done
							$started = 'on';

							// Save default threads values
							$scans->set_scan_final_threads_options($scan->id, array(
								'max_threads' 	  => $max_threads,
								'connect_timeout' => $connect_timeout,
								'request_timeout' => $request_timeout,
							));

							// Attemp to run scan
							WPLNST_Core_Alive::run($scan->id, $hash);
						}
					}
				}

				// Redirect to scan edit permalink
				wp_redirect(WPLNST_Core_Plugin::get_url_scans_edit($insert_id, true, $do_play? $started : false));

				// End
				die;
			}

		// Update scan
		} else {

			// Update threads values if scan is in play mode
			if ('play' == $scan->status) {
				$updates['max_threads'] 	= wplnst_get_nsetting('max_threads', $max_threads);
				$updates['connect_timeout'] = wplnst_get_nsetting('connect_timeout', $connect_timeout);
				$updates['request_timeout'] = wplnst_get_nsetting('request_timeout', $request_timeout);
			}

			// Update scan data
			$rows = $wpdb->update($wpdb->prefix.'wplnst_scans', $updates, array('scan_id' => $scan->id));
			if (empty($rows) || false === ($scan = $scans->get_scan_by_id($scan->id))) {

				// Message error
				$this->notice_error = __('Something went wrong updating the scan data. Please <a href="javascript:history.back();">go back</a> and attempt again to save data.', 'wplnst');

			// Done
			} else {

				// Update message
				$this->notice_success = __('Scan updated successfully.', 'wplnst');

				// Check if run the crawler, only for waiting scans
				if ($do_play && 'wait' == $scan->status) {

					// Check max scans running
					if (!$scans->can_play_more_scans()) {

						// Maximum reached
						$this->notice_warning = WPLNST_Admin::get_text('max_scans');

						// Queue scan
						$scans->queue_scan($scan->id);

					// Set to play
					} elseif (false === $scans->play_scan($scan->id)) {

						// Crawler not started
						$this->notice_warning = sprintf(__('Something went wrong trying to start the crawler.'));

					// Done
					} else {

						// Save default threads values
						$scans->set_scan_final_threads_options($scan->id, array(
							'max_threads' 	  => $max_threads,
							'connect_timeout' => $connect_timeout,
							'request_timeout' => $request_timeout,
						));

						// Run scan
						WPLNST_Core_Alive::run($scan->id, $scan->hash);

						// Update message
						$this->notice_crawler = sprintf(__('The crawler for this scan is running. You can see its data in the <a href="%s">crawler results page</a>.', 'wplnst'), esc_url(WPLNST_Core_Plugin::get_url_scans_results($scan->id)));
					}

				// Not play
				} elseif ('wait' == $scan->status && $scans->can_play_more_scans() && true === $scans->is_scan_ready($scan)) {

					// Start crawler reminder
					$start_url = esc_url(WPLNST_Core_Plugin::get_url_scans_crawler($scan->id, 'on', $scan->hash));
					$this->notice_success .= ' '.sprintf(__('The crawler for this scan is <strong>not started</strong>, you can <a href="%s">start the crawler</a> now.', 'wplnst'), $start_url);
				}
			}
		}
	}



}