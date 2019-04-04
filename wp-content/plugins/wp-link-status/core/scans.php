<?php

/**
 * Scans class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Scans {



	// Retrieving scans data
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Retrieve existing scans
	 */
	public function get_scans($args) {

		// Globals
		global $wpdb;

		// Prepare args
		$args = array_merge(array(
			'setup_rows' 	=> true,
			'setup_names' 	=> false,
			'no_cache' 		=> false,
		), $args);

		// Set vars
		extract($args);

		// Params
		$where = ' WHERE 1 = 1';
		$limit_rows = false;

		// Check scan id
		if (!empty($scan_id)) {
			$where .= ' AND scan_id = '.((int) $scan_id);
		}

		// Check limit
		if (isset($limit)) {
			$check = array_map('intval', array_map('trim', explode(',', $limit)));
			if (1 == count($check)) {
				if ($check[0] > 0) {
					$limit_rows = $check[0];
				}
			} elseif (2 == count($check)) {
				if ($check[0] > 0 || $check[1] > 0) {
					$limit_rows = $check[0].', '.$check[1];
				}
			}
		}

		// Check single row
		if (1 === $limit_rows) {

			// Perform query
			$sql = 'SELECT'.(empty($no_cache)? '' : ' SQL_NO_CACHE').' * FROM '.$wpdb->prefix.'wplnst_scans'.$where.(empty($order_by)? '' : ' ORDER BY '.$order_by).' LIMIT 1';
			$results = (object) array('rows' => $wpdb->get_results($sql));

		// Multiple rows
		} else {

			// Check page
			$paged = empty($paged)? 1 : (int) $paged;
			if (empty($paged)) {
				$paged = 1;
			}

			// Check elements per page
			$per_page = isset($per_page)? (int) $per_page : (int) get_user_option('wplnst_scans_per_page');
			if (empty($per_page)) {
				$per_page = WPLNST_Core_Types::scans_per_page;
			}

			// Execute query
			$results = $this->pagination(array(
				'paged' 	=> $paged,
				'per_page' 	=> $per_page,
				'sql' 		=> 'SELECT $$$fields$$$ FROM '.$wpdb->prefix.'wplnst_scans'.$where,
				'fields' 	=> '*',
				'order_by'	=> (empty($order_by)? '' : $order_by),
				'calc_rows'	=> wplnst_get_bsetting('mysql_calc_rows'),
				'no_cache'	=> false,
			));
		}

		// Check setup
		if (!$setup_rows || empty($results->rows)) {
			return $results;
		}

		// Prepare rows
		$rows = array();
		foreach ($results->rows as $row) {
			$rows[] = $this->setup_row_scan($row, $setup_names);
		}
		$results->rows = $rows;

		// Done
		return $results;
	}



	/**
	 * Return scan by its id
	 */
	public function get_scan_by_id($scan_id, $setup_names = false, $no_cache = false) {

		// Retrieve scans list
		$scans = $this->get_scans(array('scan_id' => $scan_id, 'setup_rows' => true, 'setup_names' => $setup_names, 'no_cache' => $no_cache, 'limit' => 1));

		// Count elements, return first or false
		return (!empty($scans->rows) && is_array($scans->rows) && 1 == count($scans->rows))? $scans->rows[0] : false;
	}



	/**
	 * Setup a scan database row
	 */
	public function setup_row_scan($row, $names = false) {

		// Cache post types
		static $post_types;
		if (!isset($post_types)) {
			$post_types = WPLNST_Core_Types::get_post_types();
		}

		// Cache post types keys
		static $post_types_keys;
		if (!isset($post_types_keys)) {
			$post_types_keys = array_keys($post_types);
		}

		// Cache post status
		static $post_status_keys;
		if (!isset($post_status_keys)) {
			$post_status_keys = array_keys(WPLNST_Core_Types::get_post_status());
		}

		// Cache status level
		static $status_levels;
		if (!isset($status_levels)) {
			$status_levels = WPLNST_Core_Types::get_status_levels();
		}

		// Cache status levels keys
		static $status_levels_keys;
		if (!isset($status_levels_keys)) {
			$status_levels_keys = array_keys($status_levels);
		}

		// Cache status codes
		static $status_codes_raw;
		if (!isset($status_codes_raw)) {
			$status_codes_raw = WPLNST_Core_Types::get_status_codes_raw();
		}

		// Cache status codes keys
		static $status_codes_keys;
		if (!isset($status_codes_keys)) {
			$status_codes_keys = array_keys($status_codes_raw);
		}

		// Scan object
		$scan = new stdClass;
		$scan->row = $row;


		/* scan values */

		// Cast fields
		$scan->id = (int) $row->scan_id;
		$scan->name = $row->name;
		$scan->status = $row->status;
		$scan->ready = (1 == (int) $row->ready);
		$scan->hash = $row->hash;

		// Decode config json field
		$config = @json_decode($row->config, true);

		// General tab
		$scan->destination_type 		= WPLNST_Core_Types::check_array_value($config, 'destination_type', array_keys(WPLNST_Core_Types::get_destination_types()), 'all');
		$scan->time_scope 				= WPLNST_Core_Types::check_array_value($config, 'time_scope', array_keys(WPLNST_Core_Types::get_time_scopes()), 'anytime');
		$scan->link_types				= WPLNST_Core_Types::check_array_value($config, 'link_types', array_keys(WPLNST_Core_Types::get_link_types()), array());
		$scan->crawl_order				= WPLNST_Core_Types::check_array_value($config, 'crawl_order', array_keys(WPLNST_Core_Types::get_crawl_order()), 'desc');
		$scan->redir_status				= WPLNST_Core_Types::check_array_value($config, 'redir_status', true);
		$scan->malformed				= WPLNST_Core_Types::check_array_value($config, 'malformed', true);
		$scan->notify_default 			= WPLNST_Core_Types::check_array_value($config, 'notify_default', true);
		$scan->notify_address 			= WPLNST_Core_Types::check_array_value($config, 'notify_address', true);
		$scan->notify_address_email		= WPLNST_Core_Types::get_array_value($config, 'notify_address_email', '');

		// Content options tab
		$scan->post_types				= (function_exists('did_action') && did_action('init'))? WPLNST_Core_Types::check_array_value($config, 'post_types', $post_types_keys, array()) : ((empty($config['post_types']) || !is_array($config['post_types']))? array() : $config['post_types']);
		$scan->post_status				= WPLNST_Core_Types::check_array_value($config, 'post_status', $post_status_keys, array());
		$scan->check_posts				= (!empty($scan->post_types) && is_array($scan->post_types) && !empty($scan->post_status) && is_array($scan->post_status));
		$scan->comment_types 			= WPLNST_Core_Types::check_array_value($config, 'comment_types', array_keys(WPLNST_Core_Types::get_comment_types()), array());
		$scan->check_comments 			= (!empty($scan->comment_types) && is_array($scan->comment_types));
		$scan->check_blogroll 			= WPLNST_Core_Types::check_array_value($config, 'blogroll', true);

		// Links status tab
		$scan->status_levels 			= WPLNST_Core_Types::check_array_value($config, 'status_levels', $status_levels_keys, array());
		$scan->status_codes				= WPLNST_Core_Types::check_array_value($config, 'status_codes', array_keys(WPLNST_Core_Types::get_status_codes_raw()), array());

		// Filters
		$scan->custom_fields			= WPLNST_Core_Types::check_array_json($config, 'custom_fields');
		$scan->anchor_filters			= WPLNST_Core_Types::check_array_json($config, 'anchor_filters');
		$scan->include_urls				= WPLNST_Core_Types::check_array_json($config, 'include_urls');
		$scan->exclude_urls				= WPLNST_Core_Types::check_array_json($config, 'exclude_urls');
		$scan->html_attributes			= WPLNST_Core_Types::check_array_json($config, 'html_attributes');
		$scan->filtered_query			= WPLNST_Core_Types::check_array_value($config, 'filtered_query', true);


		/* scan config values names */

		if ($names) {

			// Destination and Time scope
			$scan->destination_type_name = WPLNST_Core_Types::get_destination_type_name($scan->destination_type, 'all');
			$scan->time_scope_name 		 = WPLNST_Core_Types::get_time_scope_name($scan->time_scope, 'anytime');

			// Links types
			$scan->link_types_names 	= WPLNST_Core_Types::get_link_types_names($scan->link_types);

			// Crawl order
			$scan->crawl_order_name 	= WPLNST_Core_Types::get_crawl_order_name($scan->crawl_order, 'desc');

			// Post types and status
			$scan->post_types_names 	= WPLNST_Core_Types::get_field_values_names($post_types, $scan->post_types);
			$scan->post_status_names 	= empty($scan->post_status)? array() : array_map('ucfirst', $scan->post_status);

			// A strict mode of post types names
			$scan->post_types_names_strict = array();
			foreach ($scan->post_types as $post_type_value) {
				if (isset($post_types[$post_type_value])) {
					$scan->post_types_names_strict[] = esc_html($post_types[$post_type_value]).' (<code>'.esc_html($post_type_value).'</code>)';
				}
			}

			// Comment types
			if ($scan->check_comments) {
				$scan->comment_types_names 	= WPLNST_Core_Types::get_comment_types_names($scan->comment_types);
				if (1 == count($scan->comment_types_names)) {
					$scan->post_types_names[] = sprintf(__('%s comments', 'wplnst'), $scan->comment_types_names[0]);
				} elseif (2 == count($scan->comment_types_names)) {
					$scan->post_types_names[] = sprintf(__('%s and %s comments', 'wplnst'), $scan->comment_types_names[0], lcfirst($scan->comment_types_names[1]));
				}
			}

			// Check blogroll type
			if ($scan->check_blogroll) {
				$scan->post_types_names[] = __('Blogroll', 'wplnst');
			}

			// Links status combined
			$scan->links_status_names 	= WPLNST_Core_Types::get_links_status_names_combined($scan->status_levels, $scan->status_codes);

			// Links status levels
			$scan->status_levels_names = array();
			foreach ($scan->status_levels as $status_level) {
				if (isset($status_levels[$status_level])) {
					$scan->status_levels_names[] = $status_level.'00s ' .$status_levels[$status_level];
				}
			}

			// Links status codes
			$scan->status_codes_names = array();
			foreach ($scan->status_codes as $status_code) {
				if (isset($status_codes_raw[$status_code])) {
					$scan->status_codes_names[] = $status_code.' ' .$status_codes_raw[$status_code];
				}
			}
		}


		/* Prepare trace values */

		// Decode trace json field
		$trace = @json_decode($row->trace, true);
		if (empty($trace) || !is_array($trace)) {
			$trace = array();
		}
		$scan->trace = $trace;


		/* Prepare scan summary */

		$summary = @json_decode($row->summary, true);
		if (empty($summary) || !is_array($summary)) {
			$summary = array();
		}
		$scan->summary = $summary;


		/* Prepare threads values */

		// Decode threads json field
		$threads = @json_decode($row->threads, true);

		// Create new object
		$scan->threads = new stdClass;

		// Assign object properties
		$scan->threads->current				= (empty($threads) || !is_array($threads))? array() : $threads;
		$scan->threads->max					= (int) $row->max_threads;
		$scan->threads->connect_timeout		= (int) $row->connect_timeout;
		$scan->threads->request_timeout		= (int) $row->request_timeout;

		// Done
		return $scan;
	}



	/**
	 * Remove existing scan data
	 */
	public function delete_scan($scan_id) {

		// Globals
		global $wpdb;

		// Remove from main scans table
		$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_scans WHERE scan_id = %d', $scan_id));

		// Remove from status table
		$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_urls_status WHERE scan_id = %d', $scan_id));

		// Remove from locations table
		$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_urls_locations WHERE scan_id = %d', $scan_id));

		// Remove from locations attributes table
		$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_urls_locations_att WHERE scan_id = %d', $scan_id));

		// Remove from objects table
		$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = %d', $scan_id));
	}



	/**
	 * Number of running scans
	 */
	public function get_scans_play_count() {

		// Globals
		global $wpdb;

		// Perform query
		return (int) $wpdb->get_var('SELECT SQL_NO_CACHE COUNT(*) FROM '.$wpdb->prefix.'wplnst_scans WHERE status = "play"');
	}



	/**
	 * Return if is possible to play a new scan
	 */
	public function can_play_more_scans() {
		return ($this->get_scans_play_count() < wplnst_get_nsetting('max_scans'));
	}



	/**
	 * Check if is ready to start a crawl
	 */
	public static function is_scan_ready($scan) {

		// Initialize
		$result = array();
		$empty_post_types = false;

		// Check link types
		if (empty($scan->link_types) || !is_array($scan->link_types)) {
			$result['link_types'] = __('There is not any <strong>link type</strong> selected, you need to select one or more.', 'wplnst');
		}

		// Check post types
		if (empty($scan->post_types) || !is_array($scan->post_types)) {
			$empty_post_types = true;
		}

		// Check post status
		if (empty($scan->post_status) || !is_array($scan->post_status)) {
			if (!$empty_post_types) {
				$result['post_status'] = __('Need to select any <strong>post status</strong> value for the selected post types.', 'wplnst');
			}
		}

		// Check comments and blogroll
		if (!$scan->check_comments && !$scan->check_blogroll) {
			if ($empty_post_types) {
				$result['post_types'] = __('There is not any kind of <strong>post type</strong>, <strong>comments</strong> or <strong>blogroll</strong> selected.', 'wplnst');
			}
		}

		// Check status levels and status codes
		if ((empty($scan->status_levels) || !is_array($scan->status_levels)) && (empty($scan->status_codes) || !is_array($scan->status_codes))) {
			$result['link_status'] = __('Missing selection of any <strong>links status</strong> level or status code.', 'wplnst');
		}

		// Done
		return empty($result)? true : $result;
	}



	/*
	 * Update scan ready status
	 */
	public function update_scan_ready($scan_id, $ready) {

		// Globals
		global $wpdb;

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('ready' => $ready? 1 : 0), array('scan_id' => $scan_id));
	}



	/**
	 * Remove any stored stopped datetime
	 */
	public function remove_stopped_time($scan_id) {
		// Globals
		global $wpdb;

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('stopped_at' => '0000-00-00 00:00:00'), array('scan_id' => $scan_id));
	}



	/**
	 * Obtains fresh scan trace data
	 */
	public function get_scan_trace($scan_id, $field = false) {

		// Globals
		global $wpdb;

		// Retrieve fresh trace data
		$trace = $wpdb->get_var($wpdb->prepare('SELECT SQL_NO_CACHE trace FROM '.$wpdb->prefix.'wplnst_scans WHERE scan_id = %d', $scan_id));
		$trace = @json_decode($trace, true);
		$trace = (empty($trace) || !is_array($trace))? array() : $trace;

		// Check field or all values
		return (false !== $field)? (isset($trace[$field])? $trace[$field] : false) : $trace;
	}



	/**
	 * Update scan trace values
	 */
	public function update_scan_trace($scan_id, $values) {

		// Globals
		global $wpdb;

		// Merge trace values
		$trace = array_merge($this->get_scan_trace($scan_id), $values);

		// Update trace values
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('trace' => @json_encode($trace)), array('scan_id' => $scan_id));
	}



	/**
	 * Retrieve scan summary
	 */
	public function get_scan_summary($scan_id) {

		// Globals
		global $wpdb;

		// Retrieve fresh summary data
		$summary = $wpdb->get_var($wpdb->prepare('SELECT SQL_NO_CACHE summary FROM '.$wpdb->prefix.'wplnst_scans WHERE scan_id = %d', $scan_id));
		$summary = @json_decode($summary, true);

		// Check data
		return (empty($summary) || !is_array($summary))? array() : $summary;
	}



	/**
	 * Remove scan summary data by prefixed keys
	 */
	public function remove_scan_summary_prefixed($scan_id, $prefixes) {

		// Globals
		global $wpdb;

		// Check value
		if (empty($prefixes)) {
			return;
		}

		// Check array
		if (!is_array($prefixes)) {
			$prefixes = array($prefixes);
		}

		// Current data
		$summary = $this->get_scan_summary($scan_id);
		if (!empty($summary)) {

			// Initialize
			$summary2 = array();

			// Enum summary data
			foreach ($summary as $key => $value) {

				// Check prefixes
				$match = false;
				foreach ($prefixes as $prefix) {
					if (0 === stripos($key, $prefix)) {
						$match = true;
						break;
					}
				}

				// Removed
				if ($match) {
					continue;
				}

				// Copy data
				$summary2[$key] = $value;
			}

			// Check summary update
			if (count($summary) != count($summary2)) {
				$this->update_scan_summary($scan_id, $summary2, false);
			}
		}
	}



	/**
	 * Update scan summary data
	 */
	public function update_scan_summary($scan_id, $values, $merge = true) {

		// Globals
		global $wpdb;

		// Check previous summary
		$summary = $this->get_scan_summary($scan_id);

		// Merge data
		$summary = (empty($summary) || !$merge)? $values : array_merge($summary, $values);

		// Update data
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('summary' => @json_encode($summary)), array('scan_id' => $scan_id));
	}



	/**
	 * Update URL info for summary
	 */
	public function set_scan_summary_status_codes($scan_id, $status_levels, $status_codes, $final = false) {

		// Globals
		global $wpdb;

		// Not the last
		if (!$final) {

			// Check timestamp
			$timestamp = $this->get_scan_trace($scan_id, 'summary_status_codes');
			if (false !== $timestamp && (time() - $timestamp) < wplnst_get_nsetting('summary_status')) {
				return;
			}

			// Update timestamp
			$this->update_scan_trace($scan_id, array('summary_status_codes' => time()));
		}

		// Check levels
		if (!empty($status_levels) && is_array($status_levels)) {
			$where_levels = 's.status_level IN ("0", "'.implode('", "', array_map('esc_sql', $status_levels)).'")';
		}

		// Check codes
		if (!empty($status_codes) && is_array($status_codes)) {
			$where_codes  = 's.status_code IN ("0", "'.implode('", "', array_map('esc_sql', $status_codes)).'")';
		}

		// Compose combined
		if (isset($where_levels) && isset($where_codes)) {
			$where = ' AND ('.$where_levels.' OR '.$where_codes.')';

		// Only levels
		} elseif (isset($where_levels)) {
			$where = ' AND '.$where_levels;

		// Only codes
		} elseif (isset($where_codes)) {
			$where = ' AND '.$where_codes;

		// Error
		} else {
			return;
		}

		// Initialize
		$total = 0;
		$codes = array();
		$levels = array();

		// Retrieve totals of codes for not ignored locations
		$results = $wpdb->get_results($wpdb->prepare('SELECT s.status_code, COUNT(*) total FROM '.$wpdb->prefix.'wplnst_urls_status s RIGHT JOIN '.$wpdb->prefix.'wplnst_urls_locations l ON s.url_id = l.url_id AND s.scan_id = l.scan_id WHERE s.scan_id = %d '.$where.' AND s.phase = "end" AND l.ignored = 0 GROUP BY status_code', $scan_id));

		// Cast data to array
		if (!empty($results) && is_array($results)) {

			// Enum code results
			foreach ($results as $result) {

				// Sum all codes
				$total += (int) $result->total;

				// Total for this code
				$codes[$result->status_code] = (int) $result->total;

				// Total for this level
				$level_key = mb_substr($result->status_code, 0, 1);
				$levels[$level_key] = isset($levels[$level_key])? $levels[$level_key] + $codes[$result->status_code] : $codes[$result->status_code];
			}
		}

		// Prepare to summary
		$summary_keys = array();

		// Prefix code keys
		foreach ($codes as $key => $value) {
			$summary_keys['status_code_'.$key] = $value;
		}

		// Prefix level keys
		foreach ($levels as $key => $value) {
			$summary_keys['status_level_'.$key] = $value;
		}

		// Total results
		$summary_keys['status_total'] = $total;

		// Remove old summary status values
		$this->remove_scan_summary_prefixed($scan_id, array('status_code_', 'status_level_'));

		// Update summary data
		$this->update_scan_summary($scan_id, $summary_keys);
	}



	/**
	 * Update URL info for summary
	 */
	public function set_scan_summary_urls_phases($scan_id, $final = false) {

		// Globals
		global $wpdb;

		// Not the last
		if (!$final) {

			// Check timestamp
			$timestamp = $this->get_scan_trace($scan_id, 'summary_url_phases');
			if (false !== $timestamp && (time() - $timestamp) < wplnst_get_nsetting('summary_phases')) {
				return;
			}

			// Update timestamp
			$this->update_scan_trace($scan_id, array('summary_url_phases' => time()));
		}

		// Initialize
		$phases = array();

		// Retrieve totals of phases
		$results = $wpdb->get_results($wpdb->prepare('SELECT phase, COUNT(*) total FROM '.$wpdb->prefix.'wplnst_urls_status WHERE scan_id = %d GROUP BY phase', $scan_id));

		// Cast data to array
		if (!empty($results) && is_array($results)) {
			foreach ($results as $result) {
				$phases[$result->phase] = (int) $result->total;
			}
		}

		// Merge default values
		$phases = array_merge(array(
			'wait'    => 0,
			'redir'	  => 0,
			'play'	  => 0,
			'end' 	  => 0,
			'discard' => 0,
			'failed'  => 0
		), $phases);

		// Sum redir to wait
		$phases['wait'] += $phases['redir'];

		// New values combined
		$phases['processed'] = $phases['end'] + $phases['discard'] + $phases['failed'];

		// Prefix keys
		$summary_phases = array();
		foreach ($phases as $key => $value) {
			$summary_phases['urls_phase_'.$key] = $value;
		}

		// Update summary data
		$this->update_scan_summary($scan_id, $summary_phases);
	}



	/**
	 * Obtains the total match objects and update summary
	 */
	public function set_scan_summary_objects_match($scan_id, $object_type, $final = false) {

		// Globals
		global $wpdb;

		// Not the last
		if (!$final) {

			// Check timestamp
			$timestamp = $this->get_scan_trace($scan_id, 'summary_objects_match');
			if (false !== $timestamp && (time() - $timestamp) < wplnst_get_nsetting('summary_objects')) {
				return;
			}

			// Update timestamp
			$this->update_scan_trace($scan_id, array('summary_objects_match' => time()));
		}

		// Perform query
		$objects_match = (int) $wpdb->get_var($wpdb->prepare('SELECT COUNT(DISTINCT object_id) FROM '.$wpdb->prefix.'wplnst_urls_locations WHERE scan_id = %d AND object_type = %s', $scan_id, $object_type));

		// Update summary data
		$this->update_scan_summary($scan_id, array('objects_match_'.$object_type => $objects_match));
	}



	/**
	 * Save max threads and timeouts connection options
	 * This works at the end of an scan crawling process
	 */
	public function set_scan_final_threads_options($scan_id, $args) {

		// Globals
		global $wpdb;

		$args = array_merge(array(
			'max_threads' 	  => 0,
			'connect_timeout' => 0,
			'request_timeout' => 0,
		), $args);

		// Prepare update
		$update = array(
			'max_threads' 	  => wplnst_get_nsetting('max_threads', 	  $args['max_threads']),
			'connect_timeout' => wplnst_get_nsetting('connect_timeout', $args['connect_timeout']),
			'request_timeout' => wplnst_get_nsetting('request_timeout', $args['request_timeout']),
		);

		// Update
		return $wpdb->update($wpdb->prefix.'wplnst_scans', $update, array('scan_id' => $scan_id));
	}



	// Scan status queries
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Obtains fresh scan status data
	 */
	public function get_scan_status($scan_id) {

		// Globals
		global $wpdb;

		// Return status
		return $wpdb->get_var($wpdb->prepare('SELECT SQL_NO_CACHE status FROM '.$wpdb->prefix.'wplnst_scans WHERE scan_id = %d', $scan_id));
	}



	/**
	 * Stop any playing scan
	 */
	public function stop_playing_scans() {

		// Globals
		global $wpdb;

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('status' => 'stop'), array('status' => 'play'));
	}



	/**
	 * Queue a given scan
	 */
	public function queue_scan($scan_id) {

		// Globals
		global $wpdb;

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('status' => 'queued', 'enqueued_at' => current_time('mysql', true)), array('scan_id' => $scan_id));
	}



	/**
	 * Unqueue scan, cast to wait
	 */
	public function unqueue_scan($scan_id) {

		// Globals
		global $wpdb;

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('status' => 'wait'), array('scan_id' => $scan_id));
	}



	/**
	 * Play a given scan
	 */
	public function play_scan($scan_id, $continued = false) {

		// Globals
		global $wpdb;

		// Decide field
		$time_field = $continued? 'continued_at' : 'started_at';

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('status' => 'play', $time_field => current_time('mysql', true)), array('scan_id' => $scan_id));
	}



	/**
	 * Stop a given scan
	 */
	public function stop_scan($scan_id) {

		// Globals
		global $wpdb;

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('status' => 'stop', 'stopped_at' => current_time('mysql', true)), array('scan_id' => $scan_id));
	}



	/**
	 * Terminated scan
	 */
	public function end_scan($scan_id) {

		// Globals
		global $wpdb;

		// Perform query
		return $wpdb->update($wpdb->prefix.'wplnst_scans', array('status' => 'end', 'finished_at' => current_time('mysql', true)), array('scan_id' => $scan_id));
	}



	// URLs crawling procedures
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Replacement function of WP API because we need meta_id
	 */
	public function get_post_metas($post_id) {

		// Globals
		global $wpdb;

		// Check keys input
		if (!empty($keys) && !is_array($keys)) {
			$keys = array($keys);
		}

		// Retrieve metas
		$rows = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->postmeta.' WHERE post_id = %d ORDER BY meta_id ASC', $post_id));

		// Check results
		if (!empty($rows) && is_array($rows)) {

			// Initialize
			$metas = array();

			// Enum rrows
			foreach ($rows as $row) {

				// Check meta key
				if (empty($row->meta_key)) {
					continue;
				}

				// Check by name
				if (!isset($metas[$row->meta_key])) {
					$metas[$row->meta_key] = array();
				}

				// Add value
				$metas[$row->meta_key][$row->meta_id] = $row->meta_value;
			}

			// Done
			return $metas;
		}

		// Not found
		return false;
	}



	/**
	 * Register by identifiers before process the object
	 */
	public function register_scan_object($scan_id, $object_id, $object_type, $object_date_gmt = "0000-00-00 00:00:00") {

		// Globals
		global $wpdb;

		// Insert attempt
		return (1 == (int) $wpdb->query($wpdb->prepare('INSERT IGNORE INTO '.$wpdb->prefix.'wplnst_scans_objects SET scan_id = %d, object_id = %d, object_type = %s, object_date_gmt = %s', $scan_id, $object_id, $object_type, $object_date_gmt)));
	}



	/**
	 * Check if exists an scan object
	 */
	public function scan_object_exists($scan_id, $object_id, $object_type) {

		// Globals
		global $wpdb;

		// Number of occurrences
		return (1 == (int) $wpdb->get_var($wpdb->prepare('SELECT SQL_NO_CACHE COUNT(*) FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = %d AND object_id = %d AND object_type = %s', $scan_id, $object_id, $object_type)));
	}



	/**
	 * Retrieve object identifiers based on type and same date
	 */
	public function get_scan_objects_ids_by_date($scan_id, $object_type, $object_date_gmt) {

		// Globals
		global $wpdb;

		// Number of occurrences
		return $wpdb->get_col($wpdb->prepare('SELECT SQL_NO_CACHE object_id FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = %d AND object_type = %s AND object_date_gmt = %s', $scan_id, $object_type, $object_date_gmt));
	}



	/**
	 * Return the amount of registered objects
	 */
	public function get_scan_objects_count($scan_id, $object_type) {

		// Globals
		global $wpdb;

		// Insert attempt
		return (int) $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = %d AND object_type = %s', $scan_id, $object_type));
	}



	/**
	 * Remove all scan registered objects
	 */
	public function remove_scan_objects($scan_id) {

		// Globals
		global $wpdb;

		// Insert attempt
		return (int) $wpdb->get_var($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = %d', $scan_id));
	}



	/**
	 * Retrieve scan URL data
	 */
	public function get_scan_url($args) {

		// Globals
		global $wpdb;

		// Arguments
		extract($args);

		// Check by id
		if (isset($id)) {

			// Check identifier
			$id = (int) $id;
			if (!empty($id)) {
				$row = $wpdb->get_row($wpdb->prepare('SELECT '.(isset($no_cache)? 'SQL_NO_CACHE ' : '').'* FROM '.$wpdb->prefix.'wplnst_urls WHERE url_id = %d', $id));
				return empty($row)? false : $row;
			}

		// By URL string
		} elseif (isset($url)) {

			// Retrieve URLs by hash
			$rows = $wpdb->get_results($wpdb->prepare('SELECT '.(isset($no_cache)? 'SQL_NO_CACHE ' : '').'* FROM '.$wpdb->prefix.'wplnst_urls WHERE hash = %s ORDER BY url_id ASC', $this->get_url_hash($url)));
			if (empty($rows) || !is_array($rows) || 1 != count($rows)) {
				return false;
			}

			// Done
			return $rows[0];
		}

		// Default
		return false;
	}



	/**
	 * Creates a 64 bytes hash to avoid URLs collisions
	 */
	public function get_url_hash($url) {

		// First part
		$hash = md5($url);

		// Another version
		$url2 = array();

		// Special chars
		static $special;
		if (!isset($special)) {
			$special = array(";", "/", "?", ":", "@", "&", "=", "+", "$", ",", "-", "_", ".", "!", "~", "*", "'", "(", ")");
		}

		// Enum URL chars
		$length = mb_strlen($url);
		for ($i = 0; $i < $length; $i++) {
			$char = mb_substr($url, $i, 1);
			if (!in_array($char, $special)) {
				$url2[] = $char;
			}
		}

		// Return combined hash
		return $hash.md5(implode('', array_reverse($url2)));
	}



	/**
	 * Retrieve next URL with next value
	 */
	public function get_scan_url_waiting($scan_id) {

		// Retrieve by wait status
		if (false !== ($rows = $this->get_scan_url_status(array(
			'scan_id' 		=> $scan_id,
			'phase'			=> array('wait', 'redir'),
			'order' 		=> 'created_at ASC',
			'no_cache'		=> true,
			'limit_rows'	=> 1,
		)))) {

			// Retrieve url
			$url = $this->get_scan_url(array('id' => $rows[0]->url_id, 'no_cache' => true));
			if (false !== $url) {

				// Check a redirection
				if ('redir' == $rows[0]->phase) {
					$url->url = $rows[0]->redirect_url;
					$url->redirection = true;
				}
			}

			// Done
			return $url;
		}

		// Default
		return false;
	}



	/**
	 * Add new URL scan
	 */
	public function add_scan_url($link, $scan_id) {

		// Globals
		global $wpdb;

		// Create hash
		$hash = $this->get_url_hash($link['url']);

		// Prepare fields
		$scheme = isset($link['scheme'])? 	mb_substr($link['scheme'], 0, 20)  : '';
		$host   = isset($link['host'])? 	mb_substr($link['host'],   0, 255) : '';
		$path 	= isset($link['path'])? 	mb_substr($link['path'],   0, 255) : '';
		$query  = isset($link['query'])? 	mb_substr($link['query'],  0, 255) : '';
		$scope =  isset($link['scope'])? 	$link['scope'] : '';

		// Add new scan status
		$result = $wpdb->query($wpdb->prepare('INSERT IGNORE INTO '.$wpdb->prefix.'wplnst_urls SET url = %s, hash = %s, scheme = %s, host = %s, path = %s, query = %s, scope = %s, created_at = %s, last_scan_id = %d', $link['url'], $hash, $scheme, $host, $path, $query, $scope, current_time('mysql', true), $scan_id));
		if (!empty($wpdb->insert_id)) {
			return $wpdb->insert_id;

		// Collision?
		} else {

			// Retrieve existing URL record
			$rows = $wpdb->get_results($wpdb->prepare('SELECT url_id, url FROM '.$wpdb->prefix.'wplnst_urls WHERE hash = %s', $hash));
			if (empty($rows) || !is_array($rows) || 1 != count($rows)) {
				return false;
			}

			// Return id only if URLs are the same
			return ($rows[0]->url === $url)? $rows[0]->url_id : false;
		}
	}



	/**
	 * Update URL scan data
	 */
	public function update_scan_url($url_id, $update) {

		// Globals
		global $wpdb;

		// Update play value
		return $wpdb->update($wpdb->prefix.'wplnst_urls', $update, array('url_id' => $url_id));
	}



	/**
	 * Add location for scan url
	 */
	public function add_scan_url_location($url_id, $scan_id, $link) {

		// Globals
		global $wpdb;

		// Add new url
		$wpdb->insert($wpdb->prefix.'wplnst_urls_locations', array(
			'url_id' 			=> $url_id,
			'scan_id' 			=> $scan_id,
			'link_type'			=> $link['link_type'],
			'object_id' 		=> $link['object_id'],
			'object_type' 		=> $link['object_type'],
			'object_post_type' 	=> $link['object_post_type'],
			'object_field' 		=> $link['object_field'],
			'object_date_gmt'	=> $link['object_date_gmt'],
			'detected_at' 		=> current_time('mysql', true),
			'chunk'				=> isset($link['chunk'])? $link['chunk'] : '',
			'anchor'			=> isset($link['anchor'])? $link['anchor'] : '',
			'raw_url'			=> $link['raw'],
			'fragment'			=> $link['fragment'],
			'spaced'			=> $link['spaced']? 	1 : 0,
			'malformed'			=> $link['malformed']? 	1 : 0,
			'absolute'			=> $link['absolute']? 	1 : 0,
			'protorel'			=> $link['protorel']? 	1 : 0,
			'relative'			=> $link['relative']? 	1 : 0,
			'nofollow'			=> $link['nofollow']? 	1 : 0,
		));

		// Result
		return empty($wpdb->insert_id)? false : $wpdb->insert_id;
	}



	/**
	 * Update location fields
	 */
	public function update_scan_url_location($loc_id, $update) {

		// Globals
		global $wpdb;

		// Update
		return $wpdb->update($wpdb->prefix.'wplnst_urls_locations', $update, array('loc_id' => $loc_id));
	}



	/**
	 * Update several locations
	 */
	public function update_scan_url_locations($loc_ids, $update) {
		foreach ($loc_ids as $loc_id) {
			$this->update_scan_url_location($loc_id, $update);
		}
	}



	/**
	 * Update posts fields
	 */
	public function update_scan_post($post_id, $update, $clean_cache = true) {

		// Globals
		global $wpdb;

		// Update
		$result = $wpdb->update($wpdb->posts, $update, array('ID' => $post_id));

		// Check cache
		if ($clean_cache) {
			clean_post_cache($post_id);
		}

		// Done
		return $result;
	}



	/**
	 * Update meta associated to post
	 */
	public function update_scan_post_meta($post_id, $meta_id, $content, $clean_cache = true) {

		// Globals
		global $wpdb;

		// Update
		$result = $wpdb->update($wpdb->postmeta, array('meta_value' => $content), array('meta_id' => $meta_id));

		// Check cache
		if ($clean_cache) {
			clean_post_cache($post_id);
		}

		// Done
		return $result;
	}



	/**
	 * Update comment fields
	 */
	public function update_scan_comment($comment_id, $update, $clean_cache = true) {

		// Globals
		global $wpdb;

		// Update
		$result = $wpdb->update($wpdb->comments, $update, array('comment_ID' => $comment_id));

		// Check cache
		if ($clean_cache) {
			clean_comment_cache($comment_id);
		}

		// Done
		return $result;
	}



	/**
	 * Update bookmark fields
	 */
	public function update_scan_bookmark($link_id, $update, $clean_cache = true) {

		// Globals
		global $wpdb;

		// Update
		$result = $wpdb->update($wpdb->links, $update, array('link_id' => $link_id));

		// Check cache
		if ($clean_cache) {
			clean_bookmark_cache($link_id);
		}

		// Done
		return $result;
	}



	/**
	 * Update total content for a given URL
	 */
	public function set_scan_url_status_total_content($url_id, $scan_id) {

		// Globals
		global $wpdb;

		// Initialize
		$total = array();

		// Retrieve total rows by object type
		$results = $wpdb->get_results($wpdb->prepare('SELECT object_type, COUNT(*) total FROM '.$wpdb->prefix.'wplnst_urls_locations WHERE url_id = %d AND scan_id = %d GROUP BY object_type', $url_id, $scan_id));

		// Cast data to array
		if (!empty($results) && is_array($results)) {
			foreach ($results as $result) {
				$total[$result->object_type] = (int) $result->total;
			}
		}

		// Merge default values
		$total = array_merge(array(
			'posts'    => 0,
			'comments' => 0,
			'blogroll' => 0,
		), $total);

		// Update scan URL status row
		$this->update_scan_url_status($url_id, $scan_id, array('total_posts' => $total['posts'], 'total_comments' => $total['comments'], 'total_blogroll' => $total['blogroll']));
	}



	/**
	 * Retrieve URL status info
	 */
	public function get_scan_url_status($args = array()) {

		// Globals
		global $wpdb;

		// Check arguments
		if (empty($args) || !is_array($args)) {
			return false;
		}

		// Extract parameters
		extract($args);

		// Cast arguments
		$url_id = 	isset($url_id)? 	(int) $url_id  : 0;
		$scan_id = 	isset($scan_id)? 	(int) $scan_id : 0;
		$status = 	isset($status)? 	$status : false;
		$phase = 	isset($phase)? 		$phase  : false;

		// Check identifiers
		if (empty($url_id) && empty($scan_id)) {
			return false;
		}

		// Force cache for an isolated record
		if (!isset($no_cache) && !empty($url_id) && !empty($scan_id)) {
			$no_cache = true;
		}

		// Initialize
		$where = '1 = 1';

		// Check URL id
		if (!empty($url_id)) {
			$where .= ' AND url_id = '.$url_id;
		}

		// Check scan id
		if (!empty($scan_id)) {
			$where .= ' AND scan_id = '.$scan_id;
		}

		// Check status
		if (false !== $status) {
			$where .= ' AND status = "'.esc_sql($status).'"';
		}

		// Check phase
		if (false !== $phase) {
			$where .= ' AND phase'.(is_array($phase)? ' IN ("'.implode('", "', array_map('esc_sql', $phase)).'")' : ' = "'.esc_sql($phase).'"');
		}

		// Order
		$order_by = '';
		if (isset($order)) {
			$order = explode(',', $order);
			foreach ($order as $order_item) {
				$order_item = explode(' ', $order_item);
				if (2 == count($order_item)) {
					if (in_array($order_item[0], array('url_id', 'scan_id', 'status', 'created_at', 'started_at'))) {
						$order_item[1] = strtoupper($order_item[1]);
						if ('ASC' == $order_item[1] || 'DESC' == $order_item[1]) {
							$order_by .= (empty($order_by)?  ' ORDER BY ' : $order_by.', ').$order_item[0].' '.$order_item[1];
						}
					}
				}
			}
		}

		// Limit
		$limit = '';
		if (isset($limit_rows)) {
			$limit_rows = (int) $limit_rows;
			if (!empty($limit_rows)) {
				$limit = ' LIMIT '.(isset($limit_base)? (int) $limit_base.', ' : '').$limit_rows;
			}
		}

		// Perform query
		$rows = $wpdb->get_results('SELECT '.(isset($no_cache)? 'SQL_NO_CACHE ' : '').'* FROM '.$wpdb->prefix.'wplnst_urls_status WHERE '.$where.$order_by.$limit);

		// Done
		return (empty($rows) || !is_array($rows))? false : $rows;
	}



	/**
	 * New URL status record
	 */
	public function new_scan_url_status($url_id, $scan_id, $insert) {

		// Globals
		global $wpdb;

		// Add new scan status
		return $wpdb->query($wpdb->prepare('INSERT IGNORE INTO '.$wpdb->prefix.'wplnst_urls_status SET url_id = %d, scan_id = %d, phase = %s, created_at = %s', $url_id, $scan_id, $phase, current_time('mysql', true)));
	}



	/**
	 * Insert new URL status record
	 */
	public function add_scan_url_status($url_id, $scan_id, $phase) {

		// Globals
		global $wpdb;

		// Add new scan status
		return $wpdb->query($wpdb->prepare('INSERT IGNORE INTO '.$wpdb->prefix.'wplnst_urls_status SET url_id = %d, scan_id = %d, phase = %s, created_at = %s', $url_id, $scan_id, $phase, current_time('mysql', true)));
	}



	/**
	 * Update a URL status phase
	 */
	public function update_scan_url_status($url_id, $scan_id, $update) {

		// Globals
		global $wpdb;

		// Update play value
		return $wpdb->update($wpdb->prefix.'wplnst_urls_status', $update, array('url_id' => $url_id, 'scan_id' => $scan_id));
	}



	/**
	 * Remove URL status record
	 */
	public function remove_scan_url_status($url_id, $scan_id) {

		// Globals
		global $wpdb;

		// Remove record
		return $wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_urls_status WHERE url_id = %d AND scan_id = %d LIMIT 1', $url_id, $scan_id));
	}



	/**
	 * Remove discard URL status and locations
	 */
	public function remove_scan_discard_urls($scan_id) {

		// Globals
		global $wpdb;

		// Remove locations
		$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_urls_locations WHERE scan_id = %d AND url_id IN (SELECT url_id FROM '.$wpdb->prefix.'wplnst_urls_status WHERE scan_id = %d AND phase = "discard")', $scan_id, $scan_id));

		// Remove URL status
		$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wplnst_urls_status WHERE scan_id = %d AND phase = "discard"', $scan_id));
	}



	// Combined results from scans, urls, status and locations
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Retrieve URL request headers
	 */
	public function get_scan_result_headers($scan_id, $url_id) {

		// Globals
		global $wpdb;

		// Retrieve headers
		return $wpdb->get_row($wpdb->prepare('SELECT headers, headers_request, request_at, total_time, total_bytes FROM '.$wpdb->prefix.'wplnst_urls_status WHERE url_id = %d AND scan_id = %d', $url_id, $scan_id));
	}



	/**
	 * Retrieve scan results
	 */
	public function get_scan_results($args) {

		// Globals
		global $wpdb;

		// Check arguments
		if (empty($args) || !is_array($args)) {
			return false;
		}

		// Extract parameters
		extract($args);

		// Check scan id
		$scan_id = empty($scan_id)? 0 : (int) $scan_id;
		if (empty($scan_id)) {
			return false;
		}

		// Check page
		$paged = empty($paged)? 1 : (int) $paged;
		if (empty($paged)) {
			$paged = 1;
		}

		// Check elements per page
		$per_page = isset($per_page)? (int) $per_page : (int) get_user_option('wplnst_scan_results_per_page');
		if (empty($per_page)) {
			$per_page = WPLNST_Core_Types::scans_results_per_page;
		}

		// Prepare fields
		$fields  = 'u.url_id, u.url, u.scheme, u.host, u.scope,';
		$fields .= 's.status_level, s.status_code, s.curl_errno, s.phase, s.redirect_url, s.redirect_steps, s.redirect_url_id, s.redirect_url_status, s.redirect_curl_errno, s.total_posts, s.total_comments, s.total_blogroll, s.total_time, s.total_bytes, s.rechecked, s.requests,';
		$fields .= 'l.loc_id, l.link_type, l.object_id, l.object_type, l.object_field, l.detected_at, l.anchor, l.raw_url, l.fragment, l.spaced, l.malformed, l.absolute, l.protorel, l.relative, l.nofollow, l.ignored, l.unlinked, l.modified, l.anchored, l.attributed';

		// Prepare inner join
		$inner_join = ' INNER JOIN '.$wpdb->prefix.'wplnst_urls_status s ON u.url_id = s.url_id ';

		// Prepare right join
		$right_join = ' RIGHT JOIN '.$wpdb->prefix.'wplnst_urls_locations l ON s.url_id = l.url_id AND s.scan_id = l.scan_id';

		/* Prepare where */

		// Base conditions
		$where = 's.scan_id = '.$scan_id.' AND u.url_id = s.url_id AND s.phase = "end"';

		// Check URL id
		if (!empty($url_id)) {
			$where .= ' AND u.url_id = '.((int) $url_id);

		// Check status level
		} elseif (isset($status_level) && false !== $status_level) {
			$where .= ' AND s.status_level = "'.esc_sql($status_level).'"';

		// Check status code
		} elseif (isset($status_code) && false !== $status_code) {
			$where .= ' AND s.status_code = "'.esc_sql($status_code).'"';
		}

		// Check object type
		if (isset($object_type) && false !== $object_type) {
			$where .= ' AND l.object_type = "'.esc_sql($object_type).'"';
		}

		// Check object post type
		if (isset($object_post_type) && false !== $object_post_type) {
			$where .= ' AND l.object_post_type = "'.esc_sql($object_post_type).'"';
		}

		// Check link type
		if (isset($link_type) && false !== $link_type) {
			$where .= ' AND l.link_type = "'.esc_sql($link_type).'"';
		}

		// Check ignored type
		if (isset($ignored_type) && false !== $ignored_type) {
			if ('oir' == $ignored_type) {
				$where .= ' AND l.ignored = 1';
			}
		} else {
			$where .= ' AND l.ignored = 0';
		}

		// SEO link type
		if (isset($seo_link_type) && false !== $seo_link_type) {
			$where .= ' AND l.nofollow = '.(('nf' == $seo_link_type)? '1' : '0');
		}

		// Protocol type
		if (isset($protocol_type) && false !== $protocol_type) {
			$where .= ('rel' == $protocol_type)? ' AND l.protorel = 1' : ' AND u.scheme = "'.esc_sql($protocol_type).'" AND l.protorel = 0';
		}

		// Special type
		if (isset($special_type) && false !== $special_type) {
			if ('rel' == $special_type) {
				$where .= ' AND l.relative = 1';
			} elseif ('abs' == $special_type) {
				$where .= ' AND l.absolute = 1';
			} elseif ('spa' == $special_type) {
				$where .= ' AND l.spaced = 1';
			} elseif ('mal' == $special_type) {
				$where .= ' AND l.malformed = 1';
			}
		}

		// Action type
		if (isset($action_type) && false !== $action_type) {
			if ('unl' == $action_type) {
				$where .= ' AND l.unlinked = 1';
			} elseif ('mod' == $action_type) {
				$where .= ' AND (l.modified = 1 OR l.anchored = 1 OR l.attributed = 1)';
			} elseif ('umd' == $action_type) {
				$where .= ' AND l.modified = 0 AND l.anchored = 0 AND l.attributed = 0';
			} elseif ('rec' == $action_type) {
				$where .= ' AND s.rechecked = 1';
			}
		}

		// Check destination type
		if (isset($dest_type) && false !== $dest_type && 'all' != $dest_type) {
			$where .= ' AND u.scope = "'.esc_sql($dest_type).'"';
		}

		// Check search URL
		if (isset($search_url) && false !== $search_url && '' !== $search_url) {
			$like = esc_sql(addcslashes($search_url, '_%\\'));
			if ('r' == $search_url_type) {
				$where .= ' AND l.fragment LIKE "%'.$like.'%"';
			} else {
				$op = ('f' == $search_url_type)? ' = "'.esc_sql($search_url).'"' : ' LIKE "'.(('s' == $search_url_type || 'm' == $search_url_type)? '%' : '').$like.(('p' == $search_url_type || 'm' == $search_url_type)? '%' : '').'"';
				$where .= ' AND (u.url'.$op.' OR s.redirect_url'.$op.')';
			}
		}

		// Check search anchor
		if (isset($search_anchor) && false !== $search_anchor && '' !== $search_anchor) {
			$op = ('f' == $search_anchor_type)? ' = "'.esc_sql($search_anchor).'"' : ' LIKE "'.(('s' == $search_anchor_type  || 'm' == $search_anchor_type)? '%' : '').esc_sql(addcslashes($search_anchor, '_%\\')).(('p' == $search_anchor_type || 'm' == $search_anchor_type)? '%' : '').'"';
			$where .= ' AND l.anchor'.$op;
		}

		// Check first order by date
		if (isset($order_type) && false !== $order_type) {

			// Change default order
			if (in_array($order_type, array_keys(WPLNST_Core_Types::get_crawl_order()))) {
				$order_by = 'l.object_date_gmt '.(('asc' == $order_type)? 'ASC' : 'DESC');

			// Orders
			} else {
				if ('dma' == $order_type) {
					$order_by = 'u.host ASC';
				} elseif ('dmd' == $order_type) {
					$order_by = 'u.host DESC';
				} elseif ('dta' == $order_type) {
					$order_by = 's.total_time ASC';
				} elseif ('dtd' == $order_type) {
					$order_by = 's.total_time DESC';
				} elseif ('dsa' == $order_type) {
					$order_by = 's.total_bytes ASC';
				} elseif ('dsd' == $order_type) {
					$order_by = 's.total_bytes DESC';
				}
			}

		// Default order
		} else {
			$order_date = (isset($order_date) && in_array(strtoupper($order_date), array('ASC', 'DESC')))? strtoupper($order_date) : 'DESC';
			$order_by = 'l.object_date_gmt '.$order_date;
		}


		// Done
		return $this->pagination(array(
			'paged' 	=> $paged,
			'per_page' 	=> $per_page,
			'sql' 		=> 'SELECT $$$fields$$$ FROM '.$wpdb->prefix.'wplnst_urls AS u'.$inner_join.$right_join.' WHERE '.$where,
			'fields' 	=> $fields,
			'order_by'	=> $order_by.', l.loc_id ASC',
			'calc_rows'	=> wplnst_get_bsetting('mysql_calc_rows'),
			'no_cache'	=> true,
		));
	}



	/**
	 * Retrieve scan locations
	 */
	public function get_scan_locations($args) {

		// Globals
		global $wpdb;

		// Check arguments
		if (empty($args) || !is_array($args)) {
			return false;
		}

		// Extract parameters
		extract($args);

		// Check scan id
		$scan_id = empty($scan_id)? false : (int) $scan_id;
		if (empty($scan_id)) {
			return false;
		}

		// Check url id
		$url_id = empty($url_id)? false : (is_array($url_id)? array_map('intval', $url_id) : (int) $url_id);
		if (empty($url_id)) {
			return false;
		}

		// URL id where
		$url_id_where = is_array($url_id)? ' IN ('.implode(', ', $url_id).')' : ' = '.$url_id;

		// Others
		$where = '';

		// Check object type
		if (!empty($object_type)) {
			$where .= ' AND object_type = "'.esc_sql($object_type).'"';
		}

		// Check page
		$paged = empty($paged)? 1 : (int) $paged;
		if (empty($paged)) {
			$paged = 1;
		}

		// Check elements per page
		$per_page = isset($per_page)? (int) $per_page : (int) get_user_option('wplnst_scan_locations_per_page');
		if (empty($per_page)) {
			$per_page = 25;
		}

		// Done
		return $this->pagination(array(
			'paged' 	=> $paged,
			'per_page' 	=> $per_page,
			'sql' 		=> $wpdb->prepare('SELECT $$$fields$$$ FROM '.$wpdb->prefix.'wplnst_urls_locations WHERE url_id '.$url_id_where.' AND scan_id = %d '.$where, $scan_id),
			'fields' 	=> '*',
			'order_by'	=> 'detected_at ASC',
			'calc_rows'	=> wplnst_get_bsetting('mysql_calc_rows'),
			'no_cache'	=> false,
		));
	}



	/**
	 * Retrieve single scan location
	 */
	public function get_scan_location_by_id($loc_id) {

		// Global
		global $wpdb;

		// Cast param
		$loc_id = (int) $loc_id;

		// Perform query and return result
		$locations = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wplnst_urls_locations WHERE loc_id = %d', $loc_id));
		return (!empty($locations) && is_array($locations) && 1 == count($locations))? $locations[0] : false;
	}



	/**
	 * Retrieve multiple scan locations
	 */
	public function get_scan_locations_by_ids($loc_ids) {

		// Global
		global $wpdb;

		// Perform query and return result
		$locations = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wplnst_urls_locations WHERE loc_id IN ('.implode(', ', array_map('intval', $loc_ids)).')');
		return (empty($locations) || !is_array($locations))? false : $locations;
	}



	/**
	 * Retrieve array info of location marks
	 */
	public function get_scan_location_marks($scan_id, $loc_id) {

		// Global
		global $wpdb;

		// Default
		$marks = array(
			'url'		=> '',
			'relative' 	=> false,
			'absolute' 	=> false,
			'spaced' 	=> false,
			'malformed' => false,
			'https' 	=> false,
			'protorel' 	=> false,
			'redirs' 	=> false,
			'redirs_count' => '1 redirect',
		);

		// Retrieve row
		$row = $wpdb->get_row($wpdb->prepare('SELECT u.url, u.scheme, l.relative, l.absolute, l.spaced, l.malformed, l.protorel, s.status_level, s.redirect_url_id, s.redirect_url, s.redirect_steps FROM '.$wpdb->prefix.'wplnst_urls AS u, '.$wpdb->prefix.'wplnst_urls_locations AS l, '.$wpdb->prefix.'wplnst_urls_status AS s WHERE l.loc_id = %d AND l.scan_id = %d AND u.url_id = l.url_id AND s.url_id = l.url_id AND s.scan_id = %d LIMIT 1', $loc_id, $scan_id, $scan_id));
		if (!empty($row) && is_object($row)) {

			// Copy URL
			$marks['url'] = esc_html($row->url);

			// Check HTTPS
			$marks['https'] = ('https' == $row->scheme);

			// Link form
			$marks['relative'] 	= (1 == $row->relative);
			$marks['absolute'] 	= (1 == $row->absolute);
			$marks['spaced'] 	= (1 == $row->spaced);
			$marks['malformed'] = (1 == $row->malformed);
			$marks['protorel'] 	= (1 == $row->protorel);

			// Redirs
			$marks['redirs'] = ('3' == $row->status_level && $row->redirect_url_id > 0 && !empty($row->redirect_url));
			if ($marks['redirs']) {
				$redirs_steps = @json_decode($row->redirect_steps, true);
				if (!empty($redirs_steps) && is_array($redirs_steps) && count($redirs_steps) > 1) {
					$marks['redirs_count'] = count($redirs_steps).' redirects';
				}
			}
		}

		// Done
		return $marks;
	}



	/**
	 * Execute and extract pagination data
	 * - paged
	 * - per_page
	 * - sql
	 * - fields
	 * - order_by
	 * - calc_rows
	 * - no_cache
	 */
	public function pagination($args) {

		// Globals
		global $wpdb;

		// Check arguments
		if (empty($args) || !is_array($args)) {
			return false;
		}

		// Extract parameters
		extract($args);

		// Check basic params
		if (empty($sql) || empty($fields)) {
			return false;
		}

		// Totals method and cache
		$calc_rows = !empty($calc_rows) && (true === $calc_rows);
		$no_cache = !empty($no_cache) && (true === $no_cache);

		// Calculating offset
		$paged = empty($paged)? 1 : (int) $paged;
		$per_page = empty($per_page)? 10 : (int) $per_page;
		$offset = (int) (($paged - 1) * $per_page);

		// Default
		$results = array(
			'paged' 		  	=> $paged,
			'per_page' 	  		=> $per_page,
			'total_pages' 		=> 0,
			'rows_page'			=> 0,
			'rows_start'		=> $offset + 1,
			'rows_end'			=> 0,
			'total_rows'  		=> 0,
			'calc_rows'			=> $calc_rows,
			'no_cache'			=> $no_cache,
			'rows' 				=> array(),
		);

		// Timer
		$time_start = microtime(true);

		// Count rows method
		if (!$calc_rows) {

			// Prepare COUNT sql
			$sql_count = str_replace('$$$fields$$$', 'COUNT(*)', $sql);
			if ($no_cache) {
				$sql_count = str_replace('SELECT ', 'SELECT SQL_NO_CACHE ', $sql_count);
			}

			// Obtain totals
			$results['total_rows'] = (int) $wpdb->get_var($sql_count);

		// Calc
		} else {

			// Replace SELECT
			$sql = str_replace('SELECT ', 'SELECT SQL_CALC_FOUND_ROWS ', $sql);
		}

		// Check NO CACHE
		if ($no_cache) {
			$sql = str_replace('SELECT ', 'SELECT SQL_NO_CACHE ', $sql);
		}

		// Set fields
		$sql = str_replace('$$$fields$$$', $fields, $sql);

		// Add ORDER and LIMIT
		$sql .= (empty($order_by)? '' : ' ORDER BY '.$order_by).' LIMIT '.$offset.', '.$per_page;
//echo $sql;
		// Perform query
		$results['rows'] = $wpdb->get_results($sql);

		// Calc rows
		if ($calc_rows) {
			$results['total_rows'] = (int) $wpdb->get_var('SELECT FOUND_ROWS()');
		}

		// End timer
		$results['time'] = microtime(true) - $time_start;
//echo '<br />'.$results['time'].' s';
		// Total pages
		$results['total_pages'] = empty($results['total_rows'])? 0 : ceil($results['total_rows'] / $per_page);

		// Queried rows
		if (!empty($results['rows']) && is_array($results['rows'])) {

			// Total rows in page
			$results['rows_page'] = count($results['rows']);
			$results['rows_end'] = $results['rows_start'] + $results['rows_page'] - 1;
		}

		// Done
		return (object) $results;
	}



}