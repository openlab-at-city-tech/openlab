<?php

/**
 * Admin Scans class
 *
 * @package WP Link Status
 * @subpackage Admin
 */
class WPLNST_Admin_Scans {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Parent object
	 */
	private $admin;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct(&$admin, $menu) {

		// Copy parent
		$this->admin = &$admin;

		// Scans menu
		if ('context' == $menu) {
			$this->scans_context();

		// Check edit scan menu
		} elseif ('edit' == $menu) {
			$this->scans_edit();
		}
	}



	/**
	 * Scans menu
	 */
	private function scans_context() {

		// Check context
		$context = empty($_GET['context'])? false : $_GET['context'];

		// Check scan id parameter
		$scan_id = empty($_GET['scan_id'])? 0 : (int) $_GET['scan_id'];

		// Results context
		if ('results' == $context) {

			// Crawl results
			$this->scans_results($scan_id);

		// Crawling order
		} elseif ('crawler' == $context) {

			// Crawl check
			$this->scans_crawler($scan_id);

		// Delete context
		} elseif ('delete' == $context) {

			// Delete confirm
			$this->scans_delete($scan_id);

		// Edit
		} elseif ('edit' == $context) {

			// Show edit view
			$this->scans_edit($scan_id);

		// Default
		} else {

			// List of scans
			$this->scans_list();
		}
	}



	// Scans list
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Prepare the list of scans
	 */
	private function scans_list() {

		// Retrieve scans
		$scans = $this->admin->get_scans(array(
			'setup_names' 	=> true,
			'order_by'		=> 'FIELD(status, "play", "wait", "queued", "stop", "end"), started_at DESC, enqueued_at ASC, created_at DESC',
			'paged' 		=> empty($_GET['paged'])? 0 : (int) $_GET['paged'],
		));

		// No isolated mode
		$scans->isolated = false;

		// Custom action view
		add_action('wplnst_scans_list_view', array(&$this, 'scans_list_view'));

		// Show admin screen
		$this->admin->screen_view(array(
			'scans' 		=> $scans,
			'wp_action'		=> 'wplnst_scans_list_view',
			'add_item_text'	=> WPLNST_Admin::get_text('scan_new_add'),
			'add_item_url'  => WPLNST_Core_Plugin::get_url_scans_add(),
		));
	}



	/**
	 * Extension view to display the scans list
	 */
	public function scans_list_view($args) {

		// Dependencies
		wplnst_require('views', 'scans');

		// Display table
		$list = new WPLNST_Views_Scans($args['scans']);
		$list->prepare_items();
		$list->display();
	}



	// Scan results
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Prepare and display crawling results
	 */
	private function scans_results($scan_id) {

		// Atempt to load scan
		if (false === ($scan = $this->admin->get_scan_by_id($scan_id, true))) {
			return $this->admin->screen_scan_not_found(WPLNST_Admin::get_text('crawler_results'));
		}

		// Prepare scans data
		$scans = (object) array(
			'isolated' 	=> true,
			'rows' 		=> array($scan),
		);

		// Check status param
		$status_code = $status_level = false;
		$status_test = isset($_GET['status'])? $_GET['status'] : false;
		if (false !== $status_test) {
			if (1 == strlen($status_test)) {
				if ('0' == $status_test || in_array($status_test, $scan->status_levels)) {
					$status_level = $status_test;
				}
			} elseif (3 == strlen($status_test)) {
				$status_code = $status_test;
			}
		}

		// Check object type param
		$object_post_type = false;
		$object_type = isset($_GET['otype'])? $_GET['otype'] : false;
		if (false !== $object_type) {
			if (!in_array($object_type, array_keys(WPLNST_Core_Types::get_objects_types()))) {
				if (0 === strpos($object_type, 'posts_') && strlen($object_type) > 6) {
					$object_post_type_test = mb_substr($object_type, 6);
					if (in_array($object_post_type_test, $scan->post_types)) {
						$object_post_type = $object_post_type_test;
					}
				}
				$object_type = false;
			}
		}

		// Check link type param
		$link_type = isset($_GET['ltype'])? $_GET['ltype'] : false;
		if (false !== $link_type && !in_array($link_type, array_keys(WPLNST_Core_Types::get_link_types()))) {
			$link_type = false;
		}

		// Check ignored or not
		$ignored_type = isset($_GET['ig'])? $_GET['ig'] : false;
		if (false !== $ignored_type && !in_array($ignored_type, array_keys(WPLNST_Core_Types::get_ignored_types()))) {
			$ignored_type = false;
		}

		// Check SEO links
		$seo_link_type = isset($_GET['slt'])? $_GET['slt'] : false;
		if (false !== $seo_link_type && !in_array($seo_link_type, array_keys(WPLNST_Core_Types::get_seo_link_types()))) {
			$seo_link_type = false;
		}

		// Check protocol
		$protocol_type = isset($_GET['pt'])? $_GET['pt'] : false;
		if (false !== $protocol_type && !in_array($protocol_type, array_keys(WPLNST_Core_Types::get_protocol_types()))) {
			$protocol_type = false;
		}

		// Check special
		$special_type = isset($_GET['sp'])? $_GET['sp'] : false;
		if (false !== $special_type && !in_array($special_type, array_keys(WPLNST_Core_Types::get_special_types()))) {
			$special_type = false;
		}

		// Check action
		$action_type = isset($_GET['ac'])? $_GET['ac'] : false;
		if (false !== $action_type && !in_array($action_type, array_keys(WPLNST_Core_Types::get_action_types()))) {
			$action_type = false;
		}

		// Check destination
		$dest_type = isset($_GET['dtype'])? $_GET['dtype'] : false;
		if (false !== $dest_type && !in_array($dest_type, array_keys(WPLNST_Core_Types::get_destination_types()))) {
			$dest_type = false;
		}

		// Check order
		$order_type = isset($_GET['or'])? $_GET['or'] : false;
		if (false !== $order_type && !in_array($order_type, array_keys(WPLNST_Core_Types::get_crawl_order())) && !in_array($order_type, array_keys(WPLNST_Core_Types::get_order_types()))) {
			$order_type = false;
		}

		// Check search URL
		$search_url = $search_url_type = false;
		$search_url_test = isset($_GET['surl'])? trim(stripslashes($_GET['surl'])) : false;
		if (false !== $search_url_test && '' !== $search_url_test) {
			$search_url = $search_url_test;
			$search_url_type = isset($_GET['surlt'])? $_GET['surlt'] : false;
			if (false !== $search_url_type && !in_array($search_url_type, array_keys(WPLNST_Core_Types::get_url_search_filters()))) {
				$search_url_type = false;
			}
		}

		// Check search anchor
		$search_anchor = $search_anchor_type = false;
		$search_anchor_test = isset($_GET['sanc'])? trim(stripslashes($_GET['sanc'])) : false;
		if (false !== $search_anchor_test && '' !== $search_anchor_test) {
			$search_anchor = $search_anchor_test;
			$search_anchor_type = isset($_GET['sanct'])? $_GET['sanct'] : false;
			if (false !== $search_anchor_type && !in_array($search_anchor_type, array_keys(WPLNST_Core_Types::get_anchor_search_filters()))) {
				$search_anchor_type = false;
			}
		}

		// Access scan data
		$results = $this->admin->scans->get_scan_results(array(
			'scan_id' 				=> $scan->id,
			'status_code' 			=> $status_code,
			'status_level' 			=> $status_level,
			'object_type'			=> $object_type,
			'object_post_type'		=> $object_post_type,
			'link_type'				=> $link_type,
			'ignored_type'			=> $ignored_type,
			'seo_link_type'			=> $seo_link_type,
			'protocol_type'			=> $protocol_type,
			'special_type'			=> $special_type,
			'action_type'			=> $action_type,
			'dest_type'				=> $dest_type,
			'order_type'			=> $order_type,
			'search_url'			=> $search_url,
			'search_url_type' 		=> $search_url_type,
			'search_anchor'			=> $search_anchor,
			'search_anchor_type' 	=> $search_anchor_type,
			'order_date'			=> $scan->crawl_order,
			'paged' 				=> empty($_GET['paged'])? 0 : (int) $_GET['paged'],
		));

		// Unknown error
		if (false === $results) {
			return $this->admin->screen_unknown_error(WPLNST_Admin::get_text('crawler_results'));
		}

		// Define all results
		$results->is_search = (false !== $object_type) || (false !== $object_post_type) || (false !== $link_type) || (false !== $ignored_type) || (false !== $seo_link_type) || (false !== $protocol_type) || (false !== $special_type) || (false !== $action_type) || (false !== $dest_type) || (false !== $search_url) || (false !== $search_anchor);
		$results->is_all_results = (false === $status_code) && (false === $status_level) && !$results->is_search;

		// Check total data
		if ($results->total_rows > 0 && $results->is_all_results && (!isset($scan->summary['status_total']) || $scan->summary['status_total'] != $results->total_rows)) {

			// Update total
			$scan->summary['status_total'] = $results->total_rows;
			$this->admin->scans->update_scan_summary($scan->id, array('status_total' => $scan->summary['status_total']));
		}

		// Results properties
		$results->scan 			= $scan;
		$results->isolated 		= false;

		// Results filters
		$results->status_code 			= $status_code;
		$results->status_level 			= $status_level;
		$results->object_type 			= $object_type;
		$results->object_post_type 		= $object_post_type;
		$results->link_type 			= $link_type;
		$results->ignored_type 			= $ignored_type;
		$results->seo_link_type			= $seo_link_type;
		$results->protocol_type			= $protocol_type;
		$results->special_type			= $special_type;
		$results->action_type			= $action_type;
		$results->destination_type 		= $dest_type;
		$results->order_type 			= $order_type;
		$results->search_url			= $search_url;
		$results->search_url_type		= $search_url_type;
		$results->search_anchor 		= $search_anchor;
		$results->search_anchor_type 	= $search_anchor_type;

		// Custom action view
		add_action('wplnst_scans_results_view', array(&$this, 'scans_results_view'));

		// Show admin screen
		$this->admin->screen_view(array(
			'scans'			=> $scans,
			'results' 		=> $results,
			'wp_action'		=> 'wplnst_scans_results_view',
			'title'			=> WPLNST_Admin::get_text('crawler_results'),
			'add_item_text'	=> WPLNST_Admin::get_text('scan_new_add'),
			'add_item_url'  => WPLNST_Core_Plugin::get_url_scans_add(),
		));
	}



	/**
	 * Extension view to display the scans results list
	 */
	public function scans_results_view($args) {

		// Scan results row actions
		add_filter('wplnst_results_actions_url', 	array(&$this, 'scans_results_actions_url'),    10, 2);
		add_filter('wplnst_results_actions_status', array(&$this, 'scans_results_actions_status'), 10, 2);
		add_filter('wplnst_results_actions_anchor', array(&$this, 'scans_results_actions_anchor'), 10, 2);

		// Display an isolated scan
		if (isset($args['scans'])) {
			$this->scans_list_view(array('scans' => $args['scans']));
		}

		// Handle the results view callback
		add_action('wplnst_scans_results_view_display', array(&$this, 'scans_results_view_display'));

		// Results list table
		$this->scans_results_views_table($args);
	}



	/**
	 * Show a list table for scan results
	 */
	protected function scans_results_views_table($args) {
		wplnst_require('views', 'scans-results');
		$list = new WPLNST_Views_Scans_Results($args['results']);
		$list->prepare_items();
		$list->display();
	}



	/**
	 * Reserved for extra element before display results table
	*/
	public function scans_results_view_display() {}



	/**
	 * Define column URL row actions
	 */
	public function scans_results_actions_url($actions, $item) {

		// Check unlinked
		if ($item['unlinked']) {
			return false;
		}

		// Check actions var
		if (!isset($actions) || !is_array($actions)) {
			$actions = array();
		}

		// More actions
		$actions = apply_filters('wplnst_results_actions_url_extended', $actions, $item);

		// Visit URL
		$actions['wplnst-action-url-visit'] = '<a href="'.esc_url($item['url']).'" target="_blank">'.__('Visit', 'wplnst').'</a>';

		// Done
		return $actions;
	}



	/**
	 * Define column Status row actions
	 */
	public function scans_results_actions_status($actions, $item) {

		// Check unlinked
		if ($item['unlinked']) {
			return false;
		}

		// Check actions var
		if (!isset($actions) || !is_array($actions)) {
			$actions = array();
		}

		// More actions
		$actions = apply_filters('wplnst_results_actions_status_extended', $actions, $item);

		// Done
		return $actions;
	}



	/**
	 * Define column Anchor row actions
	 */
	public function scans_results_actions_anchor($actions, $item) {

		// Check unlinked
		if ($item['unlinked']) {
			return false;
		}

		// Check actions var
		if (!isset($actions) || !is_array($actions)) {
			$actions = array();
		}

		// More actions
		$actions = apply_filters('wplnst_results_actions_anchor_extended', $actions, $item);

		// Done
		return $actions;
	}



	// Scan crawler
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Attemp to start or stop a crawler
	 */
	private function scans_crawler($scan_id) {

		// Atempt to load scan
		if (false === ($scan = $this->admin->get_scan_by_id($scan_id, true))) {
			return $this->admin->screen_scan_not_found(WPLNST_Admin::get_text('crawler_action'));
		}

		// Check valid operation
		$operation = empty($_GET['operation'])? false : (in_array($_GET['operation'], array('on', 'off'))? $_GET['operation'] : false);
		if (false === $operation) {
			return $this->admin->screen_invalid_data(WPLNST_Admin::get_text('crawler_action'));
		}

		// Initialize
		$notice_error = false;
		$notice_warning = false;
		$notice_success = false;

		// Check nonce
		if (empty($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'scan-crawler-'.$scan->hash)) {

			// Not valid data
			return $this->admin->screen_invalid_nonce(WPLNST_Admin::get_text('crawler_action'));

		// Check if is playing right now
		} elseif ('on' == $operation && 'play' == $scan->status) {

			// Already started
			$notice_warning = __('The crawling process for this scan is already started.', 'wplnst');

		// Check if is stopped right now
		} elseif ('off' == $operation && in_array($scan->status, array('stop', 'wait'))) {

			// Already started
			$notice_warning = __('The crawling process for this scan is already stopped.', 'wplnst');

		// Avoid ended scans
		} elseif ('end' == $scan->status) {

			// Not available scan
			$notice_warning = __('This scan was completed and it is not possible to start again.', 'wplnst');

		// Check submit form
		} else {

			// Stop current scan
			if ('off' == $operation) {

				// Remove queued flag if never started
				if ('queued' == $scan->status && (empty($scan->row->started_at) || '0000-00-00 00:00:00' == $scan->row->started_at)) {

					// Start current scan
					if (!$this->admin->scans->unqueue_scan($scan->id)) {

						// Something failed
						$notice_error = __('Something went wrong and the unqueue process was failed.', 'wplnst');

					// Done
					} else {

						// Updated
						$notice_success = __('The crawler for this scan is back to the wait mode.', 'wplnst');
					}

				// Normal mode
				} else {

					// Start current scan
					if (!$this->admin->scans->stop_scan($scan->id)) {

						// Something failed
						$notice_error = __('Something went wront and the crawler stop was failed.', 'wplnst');

					// Done
					} else {

						// Updated
						$notice_success = sprintf(__('The crawler for this scan is stopped. You can see its collected data in the <a href="%s">crawler results page</a>.', 'wplnst'), esc_url(WPLNST_Core_Plugin::get_url_scans_results($scan_id)));
					}
				}

			// Run when cURL enabled
			} elseif (wplnst_is_curl_enabled()) {

				// Check max scans running
				if (!$this->admin->scans->can_play_more_scans()) {

					// Max scans allowed
					$notice_error = WPLNST_Admin::get_text('max_scans');

					// Queue scan
					$this->admin->scans->queue_scan($scan->id);

				// Check scan ready
				} elseif (!$scan->ready) {

					// Something failed
					$notice_error = sprintf(__('You need to complete some critical values before start the crawler, please <a href="%s">edit this scan</a>.', 'wplnst'), WPLNST_Core_Plugin::get_url_scans_edit($scan->id));

				// Start attempt
				} else {

					// Not continued
					$continued = false;

					// Register stopped time
					if ('stop' == $scan->status || 'queued' == $scan->status) {

						// Needed to be started before
						if (empty($scan->row->started_at) || '0000-00-00 00:00:00' == $scan->row->started_at) {

							// Remove stopped time
							$this->admin->scans->remove_stopped_time($scan->id);
							$this->admin->scans->update_scan_summary($scan->id, array('time_stopped' => 0));

						// Check stopped time
						} elseif (!empty($scan->row->stopped_at) && '0000-00-00 00:00:00' != $scan->row->stopped_at) {

							// Continued
							$continued = true;

							// Calculate stopped time
							$time_stopped = time() - strtotime($scan->row->stopped_at.' UTC');
							$summary = $this->admin->scans->get_scan_summary($scan->id);
							$time_stopped += isset($summary['time_stopped'])? (int) $summary['time_stopped'] : 0;
						}
					}

					// Start current scan
					if (!$this->admin->scans->play_scan($scan->id, $continued)) {

						// Something failed
						$notice_error = __('Something went wront and the crawler start was failed.', 'wplnst');

					// Done
					} else {

						// Check salt file
						if (empty($notice_warning) && !WPLNST_Core_Nonce::check_salt_file()) {
							$notice_warning = WPLNST_Admin::get_text('no_salt');
						}

						// Update stopped time
						if (isset($time_stopped)) {
							$this->admin->scans->update_scan_summary($scan->id, array('time_stopped' => $time_stopped));
						}

						// Save default threads values
						$this->admin->scans->set_scan_final_threads_options($scan->id, array(
							'max_threads' 	  => $scan->threads->max,
							'connect_timeout' => $scan->threads->connect_timeout,
							'request_timeout' => $scan->threads->request_timeout,
						));

						// Start process now if are not running scans
						$this->scans_crawler_run($scan->id, $scan->hash);

						// Updated
						$notice_success = sprintf(__('The crawler is running, you can see its data in the <a href="%s">crawler results page</a>.', 'wplnst'), esc_url(WPLNST_Core_Plugin::get_url_scans_results($scan_id)));
					}
				}
			}
		}

		// Reload scan
		if (false === ($scan = $this->admin->get_scan_by_id($scan_id, true, true))) {
			return $this->admin->screen_scan_not_found(WPLNST_Admin::get_text('crawler_action'));
		}

		// Prepare scans data
		$scans = (object) array(
			'isolated' 	=> true,
			'rows' 		=> array($scan),
		);

		// Custom action view
		add_action('wplnst_scans_crawler_view', array(&$this, 'scans_list_view'));

		// Screen showing the start crawling process
		$this->admin->screen_view(array(
			'scans' 			=> $scans,
			'title'				=> WPLNST_Admin::get_text('crawler_action'),
			'notice_error' 		=> $notice_error,
			'notice_warning' 	=> $notice_warning,
			'notice_success' 	=> $notice_success,
			'wp_action'			=> 'wplnst_scans_crawler_view',
			'add_item_text'		=> WPLNST_Admin::get_text('scan_new_add'),
			'add_item_url'  	=> WPLNST_Core_Plugin::get_url_scans_add(),
		));

		// Check for queued scans
		WPLNST_Core_Alive::activity(true);
	}



	/**
	 * Wrapper function to run scan from Alive class
	 */
	protected function scans_crawler_run($scan_id, $hash) {
		WPLNST_Core_Alive::run($scan_id, $hash);
	}



	// Scan delete
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Attemp to remove a scan
	 */
	private function scans_delete($scan_id) {

		// Initialize
		$notice_success = false;
		$notice_warning = false;

		// Check multiple scans
		if (isset($_GET['scan_id']) && '-' == mb_substr($_GET['scan_id'], 0, 1)) {

			// Check nonce
			if (empty($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'bulk-scans-delete')) {
				return $this->admin->screen_invalid_nonce(WPLNST_Admin::get_text('scan_delete'));
			}

			// Check confirmation
			if (empty($_GET['confirm']) || 'on' != $_GET['confirm']) {

				// Confirm message
				$notice_warning = sprintf(__('Sorry, we need a confirmation action. Please click here to <a href="%s" class="wplnst-scan-delete" data-confirm="%s">delete scan</a>', 'wplnst'), esc_url(WPLNST_Core_Plugin::get_url_scans_delete($_GET['scan_id'], 'bulk-scans-delete')), esc_attr(WPLNST_Admin::get_text('scan_delete_confirm')));

			// Done
			} else {

				// Timeouts
				set_time_limit(0);

				// Initialize
				$scans = array();

				// Extract identifiers
				$ids = array_map('intval', explode('-', trim($_GET['scan_id'], '-')));
				foreach ($ids as $scan_id) {
					if (!empty($scan_id) && false !== ($scan = $this->admin->get_scan_by_id($scan_id))) {
						$scans[] = $scan_id;
					}
				}

				// Check data
				if (empty($scans)) {
					return $this->admin->screen_invalid_data(WPLNST_Admin::get_text('scan_delete'));
				}

				// Enum and remove
				foreach ($scans as $scan_id) {
					$this->admin->scans->delete_scan($scan_id);
				}

				// Success message
				$notice_success = __('The scans have been removed.', 'wplnst');
			}

		// Single
		} else {

			// Atempt to load scan
			if (false === ($scan = $this->admin->get_scan_by_id($scan_id))) {
				return $this->admin->screen_scan_not_found(WPLNST_Admin::get_text('scan_delete'));
			}

			// Check nonce
			if (empty($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], $scan->hash)) {
				return $this->admin->screen_invalid_nonce(WPLNST_Admin::get_text('scan_delete'));
			}

			// Check confirmation
			if (empty($_GET['confirm']) || 'on' != $_GET['confirm']) {

				// Confirm message
				$notice_warning = sprintf(__('Sorry, we need a confirmation action. Please click here to <a href="%s" class="wplnst-scan-delete-isolated" data-confirm-delete="%s">delete scan</a>', 'wplnst'), esc_url(WPLNST_Core_Plugin::get_url_scans_delete($scan->id, $scan->hash)), esc_attr(WPLNST_Admin::get_text('scan_delete_confirm')));

			// Done
			} else {

				// Remove scan
				$this->admin->scans->delete_scan($scan_id);

				// Success message
				$notice_success = __('The scan has been removed.', 'wplnst');
			}
		}

		// Custom action view
		add_action('wplnst_scans_delete_view', array(&$this, 'scans_delete_view'));

		// Show admin screen
		$this->admin->screen_view(array(
			'title' 			=> WPLNST_Admin::get_text('scan_delete'),
			'notice_success' 	=> $notice_success,
			'notice_warning' 	=> $notice_warning,
			'wp_action'			=> 'wplnst_scans_delete_view',
			'add_item_text'		=> WPLNST_Admin::get_text('scan_new_add'),
			'add_item_url'  	=> WPLNST_Core_Plugin::get_url_scans_add(),
		));

		// Check for queued scans
		WPLNST_Core_Alive::activity(true);
	}



	/**
	 * Extension view when scan deleted
	 */
	public function scans_delete_view($args) {

		// Back to the scans screen
		echo '<p>&nbsp;&nbsp; &laquo; <a href="'.WPLNST_Core_Plugin::get_url_scans().'">'.__('Back to the scans list', 'wplnst').'</a></p>';
	}



	// New scan or edit
	// ---------------------------------------------------------------------------------------------------



	/**
	 * New or edit scan
	 */
	private function scans_edit($scan_id = 0) {

		// Scans library
		$this->admin->load_scans_object();

		// Notice initialization
		$notice_error 	= isset($this->admin->scan_submit)? $this->admin->scan_submit->notice_error   : false;
		$notice_warning = isset($this->admin->scan_submit)? $this->admin->scan_submit->notice_warning : false;
		$notice_success = isset($this->admin->scan_submit)? $this->admin->scan_submit->notice_success : false;
		$notice_crawler = isset($this->admin->scan_submit)? $this->admin->scan_submit->notice_crawler : false;

		// Check submit error
		if ($notice_error) {
			return $this->admin->screen_view(array(
				'title' => WPLNST_Admin::get_text('scan_edit'),
				'notice_error' => $notice_error,
			));
		}

		// Check existing scan
		if (!empty($scan_id)) {

			// Atempt to load scan
			if (false === ($scan = $this->admin->get_scan_by_id($scan_id, true))) {
				return $this->admin->screen_scan_not_found(WPLNST_Admin::get_text('scan_edit'));
			}

			// Check update
			if (!$notice_success && !empty($_GET['updated']) && 'on' == $_GET['updated']) {

				// New scan created
				$notice_success = __('New scan added successfully.', 'wplnst');

				// Check started argument
				if (!empty($_GET['started'])) {

					// Check max scans
					if ('max_scans' == $_GET['started']) {

						// Warning message
						$notice_warning = WPLNST_Admin::get_text('max_scans');

					// Check error
					} elseif ('error' == $_GET['started']) {

						// Warning message
						$notice_warning = sprintf(__('Something went wrong trying to start the crawler for this new scan.'));

					// Scan running
					} elseif ('on' == $_GET['started']) {

						// New crawler running
						$notice_crawler = sprintf(__('The crawler for this new scan is running. You can see its data in the <a href="%s">crawler results page</a>.', 'wplnst'), esc_url(WPLNST_Core_Plugin::get_url_scans_results($scan_id)));
					}

				// Not started
				} elseif ('wait' == $scan->status && $this->admin->scans->can_play_more_scans() && true === $this->admin->scans->is_scan_ready($scan)) {

					// Invite to start the crawler
					$start_url = esc_url(WPLNST_Core_Plugin::get_url_scans_crawler($scan->id, 'on', $scan->hash));
					$notice_success .= ' '.sprintf(__('From now on you can <a href="%s">start the crawler</a>.', 'wplnst'), $start_url);
				}
			}

			// Check salt file
			if ('play' == $scan->status && empty($notice_warning) && !WPLNST_Core_Nonce::check_salt_file()) {
				$notice_warning = WPLNST_Admin::get_text('no_salt');
			}
		}

		// Default values
		if (empty($scan)) {

			// Prepare scan object
			$scan = (object) array(
				'id' 					=> 0,
				'name' 					=> '',
				'status'				=> 'wait',
				'destination_type' 		=> 'all',
				'time_scope'			=> 'anytime',
				'link_types' 			=> array('links', 'images'),
				'crawl_order'			=> 'desc',
				'redir_status'			=> true,
				'malformed'				=> true,
				'notify_default'		=> true,
				'notify_address' 		=> false,
				'notify_address_email' 	=> '',
				'post_types' 			=> array('post', 'page'),
				'post_status' 			=> array('publish'),
				'comment_types'			=> array(),
				'check_comments'		=> false,
				'check_blogroll'		=> false,
				'status_levels'			=> array('2', '3', '4', '5'),
				'status_codes'			=> array(),
				'custom_fields'			=> array(),
				'anchor_filters'		=> array(),
				'include_urls'			=> array(),
				'exclude_urls'			=> array(),
				'html_attributes'		=> array(),
				'filtered_query'		=> true,
				'threads'				=> 0,
				'connect_timeout'		=> 0,
				'request_timeout'		=> 0,
			);

		// Check scan
		} else {

			// Analyze data
			$ready = $this->admin->scans->is_scan_ready($scan);
			$is_ready = (true === $ready);

			// Prepare notifications
			if (!$is_ready) {
				$notice_ready = $ready;
			}

			// Check ready update
			if ($scan->ready != $is_ready) {
				$this->admin->scans->update_scan_ready($scan->id, $is_ready);
			}
		}

		// Custom action view
		add_action('wplnst_scans_edit_view', array(&$this, 'scans_edit_view'));

		// Display page
		$this->admin->screen_view(array(
			'scan' 						=> $scan,
			'wp_action'					=> 'wplnst_scans_edit_view',
			'destination_types'			=> WPLNST_Core_Types::get_destination_types(),
			'time_scopes'				=> WPLNST_Core_Types::get_time_scopes(),
			'link_types' 				=> WPLNST_Core_Types::get_link_types(),
			'crawl_order'				=> WPLNST_Core_Types::get_crawl_order(),
			'custom_fields'				=> WPLNST_Core_Types::get_custom_fields(),
			'anchor_filters'			=> WPLNST_Core_Types::get_anchor_filters(),
			'url_filters'				=> WPLNST_Core_Types::get_url_filters(),
			'html_attributes_having'	=> WPLNST_Core_Types::get_html_attributes_having(),
			'html_attributes_operators'	=> WPLNST_Core_Types::get_html_attributes_operators(),
			'post_types' 				=> WPLNST_Core_Types::get_post_types(),
			'post_status' 				=> WPLNST_Core_Types::get_post_status(),
			'comment_types'				=> WPLNST_Core_Types::get_comment_types(),
			'status_levels'				=> WPLNST_Core_Types::get_status_levels(),
			'status_codes' 				=> WPLNST_Core_Types::get_status_codes(),
			'nonce' 					=> wp_create_nonce('scan-edit-'.(empty($scan->id)? '0' : $scan->hash)),
			'action'					=> ($scan->id > 0)? WPLNST_Core_Plugin::get_url_scans_edit($scan->id) : WPLNST_Core_Plugin::get_url_scans_add(),
			'title'						=> ($scan->id > 0)? WPLNST_Admin::get_text('scan_edit') : WPLNST_Admin::get_text('scan_new'),
			'more_scans'				=> $this->admin->scans->can_play_more_scans(),
			'notice_success'			=> $notice_success,
			'notice_crawler'			=> $notice_crawler,
			'notice_warning'			=> $notice_warning,
			'notice_ready'				=> isset($notice_ready)? $notice_ready : false,
			'default_max_threads'		=> wplnst_get_nsetting('max_threads'),
			'default_connect_timeout' 	=> wplnst_get_nsetting('connect_timeout'),
			'default_request_timeout' 	=> wplnst_get_nsetting('request_timeout'),
			'add_item_text'				=> ($scan->id > 0 && 'wait' != $scan->status)? WPLNST_Admin::get_text('scan_new_add') : '',
			'add_item_url'  			=> ($scan->id > 0 && 'wait' != $scan->status)? WPLNST_Core_Plugin::get_url_scans_add() : '',
		));
	}



	/**
	 * Extension view for the edit scan screen
	 */
	public function scans_edit_view($args) {
		wplnst_require('views', 'scans-edit');
		WPLNST_Views_Scans_Edit::view($args);
	}



}