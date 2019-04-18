<?php

// Load main class
require_once(dirname(__FILE__).'/module.php');

/**
 * Crawler class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Crawler extends WPLNST_Core_Module {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Current scan
	 */
	private $scan;



	/**
	 * Current thread
	 */
	private $thread_id;



	/**
	 * All timeouts
	 */
	private $connect_timeout;
	private $request_timeout;
	private $total_timeout;



	/**
	 * Current pack index
	 */
	private $pack_index = 0;



	/**
	 * Totals content
	 */
	private $total_posts;
	private $total_comments;
	private $total_blogroll;



	/**
	 * Back crawler URL
	 */
	private $crawler_url;



	/**
	 * Content args values
	 */
	private $post_args;
	private $comment_args;
	private $blogroll_args;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates a singleton object
	 */
	public static function instantiate($args = null) {
		return self::get_instance(get_class(), $args);
	}



	/**
	 * Custom constructor
	 */
	protected function on_construct($args = null) {

		// Check scan argument
		$scan_id = empty($args['scan_id'])? 0 : (int) $args['scan_id'];
		if (empty($scan_id)) {
			return;
		}

		// Check thread argument
		$this->thread_id = empty($args['thread_id'])? 0 : (int) $args['thread_id'];
		if (empty($this->thread_id)) {
			return;
		}

		// Crawler URL argument
		$this->crawler_url = empty($args['crawler_url'])? false : $args['crawler_url'];
		if (empty($this->crawler_url)) {
			return;
		}

		// Check scan record
		if (false === ($this->scan = $this->get_scan_by_id($scan_id))) {
			return;
		}

		// Debug point
		$this->debug('__construct');

		// URL object
		$this->load_url_object();

		// All timeouts
		$this->set_timeouts();

		// Incoming POST request data
		if (!empty($_POST['url_id'])) {
			$this->request();
		}

		// Check waiting links before next content data
		if (!$this->inspect()) {
			$this->content();
		}
	}



	// External request data update and quota check
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Connect and request timeout
	 */
	private function set_timeouts() {

		// Local timeouts
		$this->connect_timeout = wplnst_get_nsetting('connect_timeout', $this->scan->threads->connect_timeout);
		$this->request_timeout = wplnst_get_nsetting('request_timeout', $this->scan->threads->request_timeout);

		// Timeouts sum and extra
		$this->total_timeout =  $this->connect_timeout + $this->request_timeout + wplnst_get_nsetting('extra_timeout');
	}



	/**
	 * Result from a external request
	 */
	private function request() {

		// Debug point
		$this->debug('request');

		// Check URL id
		$url_id = (int) $_POST['url_id'];
		if (empty($url_id)) {
			return;
		}

		// Check URL record
		if (false === ($url = $this->scans->get_scan_url(array('id' => $url_id, 'no_cache' => true)))) {
			return;
		}

		// Check URL status record
		if (false === ($rows = $this->scans->get_scan_url_status(array('url_id' => $url_id, 'scan_id' => $this->scan->id, 'no_cache' => true)))) {
			return;
		}

		// Check URL status phase
		if (1 != count($rows) || in_array($rows[0]->phase, array('end', 'discard', 'failed'))) {
			return;
		}

		// Check redirection mode
		$redirection = (!empty($_GET['wplnst_redirection']) && '1' == $_GET['wplnst_redirection']);

		// Analyze request
		wplnst_require('core', 'status');
		$status = new WPLNST_Core_Status($_POST);

		// Check discarded response
		$discard = empty($status->level)? false : (!in_array($status->level, $this->scan->status_levels) && !in_array($status->code, $this->scan->status_codes));

		// Check redirection steps
		$redirect_steps = @json_decode($rows[0]->redirect_steps, true);
		if (empty($redirect_steps) || !is_array($redirect_steps)) {
			$redirect_steps = array();
		}

		// Normal mode
		if (!$this->scan->redir_status || !$redirection) {

			// Check redirection
			$phase_next = 'end';
			$redirect_url_id = 0;
			$redirect_url_status = "";
			$redirect_curl_errno = 0;
			if ($this->scan->redir_status && !$discard && 3 == $status->level && !empty($status->redirect_url)) {

				// Default redirection
				$redirect_url = $status->redirect_url;

				// Analyze
				$urlinfo = $this->urlo->parse($redirect_url, $url->url);
				if ($this->urlo->is_crawleable($urlinfo)) {

					// Copy final URL
					$redirect_url = $urlinfo['url'];

					// Search by main URL
					if (false !== ($redir_url = $this->scans->get_scan_url(array('url' => $redirect_url, 'no_cache' => true)))) {

						// Existing URL
						$phase_next = 'redir';
						$redirect_url_id = $redir_url->url_id;

					// New URL for this scan
					} elseif (false !== ($test_url_id = $this->scans->add_scan_url($urlinfo, $this->scan->id))) {

						// Mark to check
						$phase_next = 'redir';
						$redirect_url_id = $test_url_id;
					}
				}

				// Add possible fragment to save "As is"
				$redirect_url .= $urlinfo['fragment'];
			}

			// Update status data
			$this->scans->update_scan_url_status($url_id, $this->scan->id, array(
				'phase' 	 			=> $discard? 'discard' : $phase_next,
				'request_at'			=> $status->request_at,
				'status_level' 			=> $status->level,
				'status_code' 			=> $status->code,
				'redirect_url'			=> $discard? '' : (isset($redirect_url)? $redirect_url : ''),
				'redirect_url_id' 		=> $redirect_url_id,
				'redirect_url_status' 	=> $redirect_url_status,
				'redirect_curl_errno'	=> $redirect_curl_errno,
				'headers' 				=> $discard? '' : (empty($status->headers)? '' : @json_encode($status->headers)),
				'headers_request'		=> $discard? '' : (empty($status->headers_request)? '' : @json_encode($status->headers_request)),
				'total_time'			=> $discard?  0 : $status->total_time,
				'total_bytes'			=> $discard?  0 : $status->total_bytes,
				'curl_errno'			=> $discard?  0 : $status->curl_errno,
				'requests'				=> $discard?  0 : (int) $rows[0]->requests + 1,
			));

			// Update URL data
			$this->scans->update_scan_url($url_id, array(
				'last_scan_id' 			=> $this->scan->id,
				'last_status_level' 	=> $status->level,
				'last_status_code'  	=> $status->code,
				'last_curl_errno'		=> $status->curl_errno,
				'last_request_at' 		=> $status->request_at,
			));

		// Is Redirection
		} else {

			// Prepare update
			$update = array(
				'phase' 	 			=> 'end',
				'redirect_url_status' 	=> $status->code,
				'redirect_curl_errno'	=> $status->curl_errno,
				'requests'				=> (int) $rows[0]->requests + 1,
			);

			// Check new redirection
			if (empty($curl_errno) && 3 == $status->level && !empty($status->redirect_url) && ($rows[0]->requests + 1) <= wplnst_get_nsetting('max_redirs')) {

				// Check crawleable
				$urlinfo = $this->urlo->parse($status->redirect_url, $url->url);
				if ($this->urlo->is_crawleable($urlinfo)) {

					// Search by main URL
					if (false !== ($redir_url = $this->scans->get_scan_url(array('url' => $urlinfo['url'], 'no_cache' => true)))) {

						// Existing URL
						$redirect_url_id = $redir_url->url_id;

					// New URL for this scan
					} elseif (false !== ($test_url_id = $this->scans->add_scan_url($urlinfo, $this->scan->id))) {

						// Added URL
						$redirect_url_id = $test_url_id;
					}

					// For existing or correctly added URL
					if (!empty($redirect_url_id)) {

						// Copy old steps
						$redirect_steps[] = array('url' => $rows[0]->redirect_url, 'status' => $rows[0]->redirect_url_status);

						// New redir
						$update['phase'] = 'redir';
						$update['redirect_url'] = $urlinfo['url'].$urlinfo['fragment'];
						$update['redirect_url_id'] = $redirect_url_id;
						$update['redirect_url_status'] = "";
						$update['redirect_steps'] = @json_encode($redirection_steps);
					}
				}
			}

			// Update status data
			$this->scans->update_scan_url_status($url_id, $this->scan->id, $update);
		}

		// Status codes
		$this->scans->set_scan_summary_status_codes($this->scan->id, $this->scan->status_levels, $this->scan->status_codes);

		// URLs summary
		$this->scans->set_scan_summary_urls_phases($this->scan->id);
	}



	// Stored links inspection
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Launch new URL`s status checks
	 */
	private function inspect() {

		// Debug point
		$this->debug('inspect');

		// Check if this scan is playing
		if ('play' != $this->scans->get_scan_status($this->scan->id)) {

			// Stopped or ended
			return true;

		// Retrieve next URL
		} elseif (false !== ($url = $this->inspect_wait())) {

			// Check no playing rows flag, and update started datetime
			if (true !== $url && false !== $this->scans->update_scan_url_status($url->url_id, $this->scan->id, array('phase' => 'play', 'started_at' => current_time('mysql', true)))) {

				// Debug point
				$this->debug('inspect request');

				// Check Crawler URL in redirection mode
				$crawler_url = $this->crawler_url;
				if (isset($url->redirection)) {
					$crawler_url = add_query_arg('wplnst_redirection', '1', $crawler_url);
				}

				// Prepare POST fields
				$postfields = array(
					'url' 				=> $url->url,
					'hash'				=> $url->hash,
					'url_id'			=> $url->url_id,
					'connect_timeout' 	=> $this->connect_timeout,
					'request_timeout' 	=> $this->request_timeout,
					'max_download'		=> wplnst_get_nsetting('max_download') * 1024,
					'back_url' 			=> $crawler_url,
					'user_agent'		=> wplnst_get_tsetting('user_agent'),
					'nonce' 			=> WPLNST_Core_Nonce::create_nonce($url->hash),
				);

				// Load cURL wrapper library
				wplnst_require('core', 'curl');

				// Spawn crawler call
				WPLNST_Core_CURL::spawn(array(
					'CURLOPT_URL' 				=> plugins_url('core/requests/http.php', WPLNST_FILE),
					'CURLOPT_USERAGENT' 		=> wplnst_get_tsetting('user_agent'),
					'CURLOPT_POST'				=> true,
					'CURLOPT_POSTFIELDS' 		=> http_build_query($postfields, null, '&'),
					'CURLOPT_HTTPHEADER'		=> array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'),
				));

				// Ends here, it's going out to check status
				return true;
			}

		// Check scan ending from all content
		} elseif ($this->content_end()) {

			// No timeout
			set_time_limit(0);

			// Set "end" scan status
			$this->scans->end_scan($this->scan->id);

			// URLs status codes summary
			$this->scans->set_scan_summary_status_codes($this->scan->id, $this->scan->status_levels, $this->scan->status_codes, true);

			// URLs phases summary
			$this->scans->set_scan_summary_urls_phases($this->scan->id, true);

			// Update posts matched
			if ($this->scan->check_posts) {
				$this->scans->set_scan_summary_objects_match($this->scan->id, 'posts', true);
			}

			// Update posts matched
			if ($this->scan->check_comments) {
				$this->scans->set_scan_summary_objects_match($this->scan->id, 'comments', true);
			}

			// Update blogroll matched
			if ($this->scan->check_blogroll) {
				$this->scans->set_scan_summary_objects_match($this->scan->id, 'blogroll', true);
			}

			// Remove discarded URLS
			$this->scans->remove_scan_discard_urls($this->scan->id);

			// Update real total posts
			if ($this->scan->check_posts) {
				$this->scans->update_scan_trace($this->scan->id, array('total_posts' => $this->scans->get_scan_objects_count($this->scan->id, 'posts')));
			}

			// Remove registered objects
			$this->scans->remove_scan_objects($this->scan->id);

			// Check notifications
			if ($this->scan->notify_default || ($this->scan->notify_address && !empty($this->scan->notify_address_email))) {
				wplnst_require('core', 'notify');
				WPLNST_Core_Notify::completed($this->scan);
			}

			// Check for queued scans
			$this->activity();

			// The end
			return true;
		}

		// No wait or play with timeout
		return false;
	}



	/**
	 * Inspect a waiting url to check
	 */
	private function inspect_wait() {

		// Retrieve next wait
		if (false !== ($url = $this->scans->get_scan_url_waiting($this->scan->id))) {
			return $url;
		}

		// Check playing rows
		if (false === ($rows = $this->scans->get_scan_url_status(array('scan_id' => $this->scan->id, 'phase' => 'play', 'no_cache' => true, 'order' => 'started_at ASC')))) {
			return false;
		}

		// Max requests allowed
		$max_requests = wplnst_get_nsetting('max_requests');

		// Enum playing rows
		foreach ($rows as $row) {

			// Check timeouted
			if (time() - strtotime($row->started_at.' UTC') > $this->total_timeout) {

				// Check max requests
				if ($row->requests >= $max_requests) {

					// Mark as a request phase
					$this->scans->update_scan_url_status($url->url_id, $this->scan->id, array('phase' => 'failed'));

				// Retrieve URL
				} elseif (false !== ($url = $this->scans->get_scan_url(array('id' => $row->url_id, 'no_cache' => true)))) {

					// Update from play to wait
					if (false !== $this->scans->update_scan_url_status($url->url_id, $this->scan->id, array('phase' => 'wait'))) {

						// URLs summary
						$this->scans->set_scan_summary_urls_phases($this->scan->id);

						// Done
						return $url;
					}
				}
			}
		}

		// Playing rows
		return false;
	}



	// Content data extraction procedures
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Initialize post args
	 */
	private function set_post_args() {

		// Check previous
		if (isset($this->post_args)) {
			return;
		}

		// Initialize arguments
		$this->post_args = array();

		// Prepare types and status fragments
		$this->post_args['post_types']  = (1 == count($this->scan->post_types))?  '= "'.esc_sql($this->scan->post_types[0]).'"'  : 'IN ("'.implode('", "', array_map('esc_sql', $this->scan->post_types)).'")';
		$this->post_args['post_status'] = (1 == count($this->scan->post_status))? '= "'.esc_sql($this->scan->post_status[0]).'"' : 'IN ("'.implode('", "', array_map('esc_sql', $this->scan->post_status)).'")';

		// Time scope argument
		$this->post_args['time_scope'] = $this->get_time_scope_arg('post_date_gmt');

		// Check order
		$this->post_args['order_by'] = 'post_date_gmt '.(('asc' == $this->scan->crawl_order)? 'ASC' : 'DESC');

		// Filters arguments
		$this->post_args['content_filters'] = $this->get_content_filters_arg('post_content');
	}



	/**
	 * Initialize comments args
	 */
	private function set_comment_args() {

		// Check previous
		if (isset($this->comment_args)) {
			return;
		}

		// Initialize
		$this->comment_args = array();

		// Comment status
		$comment_types = WPLNST_Core_Types::get_comment_types_values($this->scan->comment_types);
		$this->comment_args['comment_status'] = ' AND comment_approved '.((1 == count($comment_types))? '= "'.esc_sql($comment_types[0]).'"' : 'IN ("'.implode('", "', array_map('esc_sql', $comment_types)).'")');

		// Time scope argument
		$this->comment_args['time_scope'] = $this->get_time_scope_arg('comment_date_gmt');

		// Filters arguments
		$this->comment_args['content_filters'] = $this->get_content_filters_arg('comment_content');

		// Check order
		$this->comment_args['order_by'] = 'comment_date_gmt '.(('asc' == $this->scan->crawl_order)? 'ASC' : 'DESC');
	}



	/**
	 * Initialize blogroll args
	 */
	private function set_blogroll_args() {

		// Check previous
		if (isset($this->blogroll_args)) {
			return;
		}

		// Initialize arguments
		$this->blogroll_args = array();

		// Check order
		$this->blogroll_args['order_by'] = 'link_id '.(('asc' == $this->scan->crawl_order)? 'ASC' : 'DESC');
	}



	/**
	 * Process next post
	 */
	private function content() {

		// Debug point
		$this->debug('content');

		// No script timeout
		set_time_limit(0);

		// Retrieve trace array
		$trace = $this->scans->get_scan_trace($this->scan->id);

		// Initialize process flags
		$process_posts = $process_comments = $process_blogroll = false;


		/* Check content */

		// Check posts
		if ($this->scan->check_posts && empty($trace['populated_posts'])) {
			$process_posts = true;
			$this->content_total('posts');
		}

		// Check comments
		if ($this->scan->check_comments && empty($trace['populated_comments'])) {
			$process_comments = true;
			if (empty($trace['total_comments_check']) || !$process_posts) {
				$this->content_total('comments');
			}
		}

		// Check blogroll
		if ($this->scan->check_blogroll && empty($trace['populated_blogroll'])) {

			// Check links arg, if not update trace
			if (!in_array('links', $this->scan->link_types)) {
				$this->total_blogroll = 0;
				$this->scans->update_scan_trace($this->scan->id, array('populated_blogroll' => true, 'total_blogroll' => 0));

			// Continue
			} else {

				// Mark for process
				$process_blogroll = true;

				// Check first update
				if (empty($trace['total_blogroll_check']) || (!$process_posts && !$process_comments)) {
					$this->content_total('blogroll');
				}
			}
		}


		// Common filters
		add_filter('wplnst_relative_parent_url', array(&$this, 'parent_permalink'));


		/* Processing objects */

		// Process posts
		if ($process_posts && !$this->content_posts()) {
			return;
		}

		// Process comments
		if ($process_comments && !$this->content_comments()) {
			return;
		}

		// Process blogroll
		if ($process_blogroll && !$this->content_blogroll()) {
			return;
		}


		/* External work or run again */

		// More data or restart
		if (!$this->inspect()) {
			$this->restart();
		}
	}



	/**
	 * Check if this is the end of all content types
	 */
	private function content_end() {

		// Initialize
		$the_end = true;

		// Check posts
		if ($this->scan->check_posts) {
			$the_end = (false !== $this->scans->get_scan_trace($this->scan->id, 'populated_posts'));
		}

		// Check comments
		if ($the_end && $this->scan->check_comments) {
			$the_end = (false !== $this->scans->get_scan_trace($this->scan->id, 'populated_comments'));
		}

		// Check blogroll
		if ($the_end && $this->scan->check_blogroll) {
			$the_end = (false !== $this->scans->get_scan_trace($this->scan->id, 'populated_blogroll'));
		}

		// Done
		return $the_end;
	}



	/**
	 * Time scope limits
	 */
	private function get_time_scope_arg($field) {

		// First basic check
		if (empty($this->scan->time_scope) || 'anytime' == $this->scan->time_scope) {
			return '';
		}

		// Check real value
		if (!in_array($this->scan->time_scope, array_keys(WPLNST_Core_Types::get_time_scopes()))) {
			return '';
		}

		// Yesterday
		if ('yesterday' == $this->scan->time_scope) {
			$back_date = ' > "'.gmdate('Y-m-d 00:00:00', strtotime("-1 days")).'"';

		// From one week ago
		} elseif ('7days'  == $this->scan->time_scope) {
			$back_date = ' > "'.gmdate('Y-m-d 00:00:00', strtotime("-7 days")).'"';

		// From 15 days ago
		} elseif ('15days' == $this->scan->time_scope) {
			$back_date = ' > "'.gmdate('Y-m-d 00:00:00', strtotime("-15 days")).'"';

		// From 1 month ago
		} elseif ('month'  == $this->scan->time_scope) {
			$back_date = ' > "'.gmdate('Y-m-d 00:00:00', strtotime("-1 months")).'"';

		// From 3 months ago
		} elseif ('3months' == $this->scan->time_scope) {
			$back_date = ' > "'.gmdate('Y-m-d 00:00:00', strtotime("-3 months")).'"';

		} elseif ('6months' == $this->scan->time_scope) {
			$back_date = ' > "'.gmdate('Y-m-d 00:00:00', strtotime("-6 months")).'"';

		} elseif ('year' == $this->scan->time_scope) {
			$back_date = ' > "'.gmdate('Y-m-d 00:00:00', strtotime("-1 years")).'"';

		} elseif ('custom'  == $this->scan->time_scope) {
			// Pending
		}

		// Check value and compose subquery
		return isset($back_date)? ' AND '.$field.$back_date : '';
	}



	/**
	 * Content filters in query
	 */
	private function get_content_filters_arg($field, $op = ' AND ') {

		// Content matches
		$matches = $this->filter_for_queries();
		if (empty($matches)) {
			return '';
		}

		// Prepare likes
		$likes = array();
		foreach ($matches as $match) {
			$likes[] = $field.' LIKE "%'.addcslashes($match, '_%\\').'%"';
		}

		// Likes fragment
		return $op.((1 == count($likes))? $likes[0] : '('.implode(' OR ', $likes).')');
	}



	/**
	 * Update total posts
	 */
	private function content_total($type, $final = false) {

		// Globals
		global $wpdb;

		// Retrieve trace array
		$trace = $this->scans->get_scan_trace($this->scan->id);

		// Update posts
		if ('posts' == $type) {

			// Debug point
			$this->debug('content_total posts');

			// Check trace value
			$total_posts = empty($trace['total_posts'])? 0 : (int) $trace['total_posts'];

			// Total posts check
			$timestamp = empty($trace['total_posts_check'])? false : (int) $trace['total_posts_check'];
			if (false === $timestamp || (time() - $timestamp) >= wplnst_get_nsetting('total_objects')) {

				// Debug point
				$this->debug('content_total posts update');

				// Mark checked time
				$this->scans->update_scan_trace($this->scan->id, array('total_posts_check' => time()));

				// Set filters
				$this->set_post_args();

				// Prepare query
				$sql = 'SELECT COUNT(*) FROM '.$wpdb->posts.' WHERE post_type '.$this->post_args['post_types'].' AND post_status '.$this->post_args['post_status'].$this->post_args['time_scope'].$this->post_args['content_filters'];

				// Check last date condition
				$last_date_gmt = $this->scans->get_scan_trace($this->scan->id, 'last_post_date_gmt');
				if (empty($last_date_gmt)) {

					// Total posts without restrictions
					$total_posts = (int) $wpdb->get_var($sql);

				// Check
				} else {

					// Sum processed and no-processed
					$where = ' AND post_date_gmt '.(('asc' == $this->scan->crawl_order)? '>=' : '<=').' "'.esc_sql($last_date_gmt).'"';
					$total_posts = (int) $this->scans->get_scan_trace($this->scan->id, 'posts_index') + (int) $wpdb->get_var($sql.$where.' AND ID NOT IN (SELECT object_id FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = '.esc_sql($this->scan->id).' AND object_type = "posts")');
				}

				// Update total posts
				$this->scans->update_scan_trace($this->scan->id, array('total_posts' => $total_posts));
			}

			// Assign results
			$this->total_posts = $total_posts;

		// Update total comments
		} elseif ('comments' == $type) {

			// Debug point
			$this->debug('content_total comments');

			// Check trace value
			$total_comments = empty($trace['total_comments'])? 0 : (int) $trace['total_comments'];

			// Total comments check
			$timestamp = empty($trace['total_comments_check'])? false : (int) $trace['total_comments_check'];
			if (false === $timestamp || (time() - $timestamp) >= wplnst_get_nsetting('total_objects')) {

				// Debug point
				$this->debug('content_total comments update');

				// Mark checked time
				$this->scans->update_scan_trace($this->scan->id, array('total_comments_check' => time()));

				// Set filters
				$this->set_comment_args();

				// Prepare query
				$sql = 'SELECT COUNT(*) FROM '.$wpdb->comments.' WHERE 1 = 1 '.$this->comment_args['comment_status'].$this->comment_args['time_scope'].$this->comment_args['content_filters'];

				// Check last date condition
				$last_date_gmt = $this->scans->get_scan_trace($this->scan->id, 'last_comment_date_gmt');
				if (empty($last_date_gmt)) {

					// Total comments without restrictions
					$total_comments = (int) $wpdb->get_var($sql);

				// Check
				} else {

					// Sum processed and no-processed
					$where = ' AND comment_date_gmt '.(('asc' == $this->scan->crawl_order)? '>=' : '<=').' "'.esc_sql($last_date_gmt).'"';
					$total_comments = (int) $this->scans->get_scan_trace($this->scan->id, 'comments_index') + (int) $wpdb->get_var($sql.$where.' AND comment_ID NOT IN (SELECT object_id FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = '.esc_sql($this->scan->id).' AND object_type = "comments")');
				}

				// Update total comments
				$this->scans->update_scan_trace($this->scan->id, array('total_comments' => $total_comments));
			}

			// Assign results
			$this->total_comments = $total_comments;

		// Update total blogroll links
		} elseif ('blogroll' == $type) {

			// Debug point
			$this->debug('content_total blogroll');

			// Check trace value
			$total_blogroll = empty($trace['total_blogroll'])? 0 : (int) $trace['total_blogroll'];

			// Total blogroll check
			$timestamp = empty($trace['total_blogroll_check'])? false : (int) $trace['total_blogroll_check'];
			if (false === $timestamp || (time() - $timestamp) >= wplnst_get_nsetting('total_objects')) {

				// Debug point
				$this->debug('content_total blogroll update');

				// Mark checked time
				$this->scans->update_scan_trace($this->scan->id, array('total_blogroll_check' => time()));

				// Set filters
				$this->set_blogroll_args();

				// Prepare query
				$sql = 'SELECT COUNT(*) FROM '.$wpdb->links;

				// Check last identifier condition
				$last_id = (int) $this->scans->get_scan_trace($this->scan->id, 'last_blogroll_id');
				if (empty($last_id)) {

					// Total blogroll without restrictions
					$total_blogroll = (int) $wpdb->get_var($sql);

				// Check
				} else {

					// Sum processed and no-processed
					$where = ' WHERE link_id '.(('asc' == $this->scan->crawl_order)? '>' : '<').' '.$last_id;
					$total_blogroll = (int) $this->scans->get_scan_trace($this->scan->id, 'blogroll_index') + (int) $wpdb->get_var($sql.$where.' AND link_id NOT IN (SELECT object_id FROM '.$wpdb->prefix.'wplnst_scans_objects WHERE scan_id = '.esc_sql($this->scan->id).' AND object_type = "blogroll")');
				}

				// Update total blogroll
				$this->scans->update_scan_trace($this->scan->id, array('total_blogroll' => $total_blogroll));
			}

			// Assign results
			$this->total_blogroll = $total_blogroll;
		}
	}



	/**
	 * Obtains and process contents from posts
	 */
	private function content_posts() {

		// Recursion limit
		static $recursion;
		$recursion = isset($recursion)? 1 : $recursion + 1;
		if ($recursion >= wplnst_get_nsetting('recursion_limit')) {
			$this->restart();
			return false;
		}

		// Content max pack
		$this->pack_index++;
		if ($this->pack_index > wplnst_get_nsetting('max_pack')) {
			$this->restart();
			return false;
		}

		// Check next post
		if (false !== ($post = $this->content_posts_next())) {

			// Register object
			if (!$this->scans->register_scan_object($this->scan->id, $post->ID, 'posts', $post->post_date_gmt)) {
				return $this->content_posts();
			}

			// Update last date gmt
			$this->scans->update_scan_trace($this->scan->id, array('last_post_date_gmt' => $post->post_date_gmt));

			// Update posts index
			$this->scans->update_scan_trace($this->scan->id, array('posts_index' => $this->scans->get_scan_objects_count($this->scan->id, 'posts')));

			// Initialize
			$links_added  = array();
			$images_added = array();
			$custom_fields_links_added  = array();
			$custom_fields_images_added = array();

			// Parent permalink reference
			$parent_url = array('post_id' => $post->ID);

			// Process content
			if (!empty($post->post_content)) {

				// Process links
				if (in_array('links', $this->scan->link_types)) {
					$links = $this->extract_links($post->post_content, $parent_url, $post->ID, 'posts', $post->post_type, 'post_content', $post->post_date_gmt);
					if (!empty($links)) {
						$links_added = $this->save($links, $post->ID);
					}
				}

				// Process images
				if (in_array('images', $this->scan->link_types)) {
					$images = $this->extract_images($post->post_content, $parent_url, $post->ID, 'posts', $post->post_type, 'post_content', $post->post_date_gmt);
					if (!empty($images)) {
						$images_added = $this->save($images, $post->ID);
					}
				}
			}

			// Check custom fields
			if (!empty($this->scan->custom_fields)) {

				// Retrieve post custom fields
				$post_metas = $this->scans->get_post_metas($post->ID);
				if (!empty($post_metas) && is_array($post_metas)) {

					// First compare field by key
					foreach ($this->scan->custom_fields as $field) {

						// Check field
						if (empty($field['type']) || !isset($field['name'])) {
							continue;
						}

						// Check existing name
						$name = $field['name'];
						if (isset($post_metas[$name])) {

							// Check custom field content
							$post_meta = $post_metas[$name];
							if (!empty($post_meta) && is_array($post_meta)) {

								// Enum values
								foreach ($post_meta as $meta_id => $value) {

									// Sanitize
									$value = trim($value);
									if (empty($value)) {
										continue;
									}

									// Direct URL
									if ('url' == $field['type']) {

										// Check URL
										$urlinfo = $this->urlo->parse($value, $parent_url);
										if ($this->filter_include_urls($urlinfo['url']) && $this->filter_exclude_urls($urlinfo['url'])) {

											// Create link array
											$link = array_merge($urlinfo, array(
												'anchor' 			=> '',
												'link_type' 		=> 'links',
												'object_id'	 		=> $post->ID,
												'object_type' 		=> 'posts',
												'object_post_type'	=> $post->post_type,
												'object_field'		=> 'custom_field_url_'.$meta_id.'_'.$name,
												'object_date_gmt' 	=> $post->post_date_gmt,
											));

											// Author links
											$custom_fields_links_added = $this->save(array($link), $post->ID);
										}

									// HTML
									} else {

										// Process links
										if (in_array('links', $this->scan->link_types)) {
											$links = $this->extract_links($value, $parent_url, $post->ID, 'posts', $post->post_type, 'custom_field_html_'.$meta_id.'_'.$name, $post->post_date_gmt);
											if (!empty($links)) {
												$custom_fields_links_added = $this->save($links, $post->ID);
											}
										}

										// Process images
										if (in_array('images', $this->scan->link_types)) {
											$images = $this->extract_images($value, $parent_url, $post->ID, 'posts', $post->post_type, 'custom_field_html_'.$meta_id.'_'.$name, $post->post_date_gmt);
											if (!empty($images)) {
												$custom_fields_images_added = $this->save($images, $post->ID);
											}
										}
									}
								}
							}
						}
					}
				}
			}

			// Update stats for summary
			if (!(empty($links_added) && empty($images_added) && empty($custom_fields_links_added) && empty($custom_fields_images_added))) {

				// Update URLs phases
				$this->scans->set_scan_summary_urls_phases($this->scan->id);

				// Update objects matched
				$this->scans->set_scan_summary_objects_match($this->scan->id, 'posts');

				// Totals of content for each URL status added
				$added = array_unique(array_merge($links_added, $images_added, $custom_fields_links_added, $custom_fields_images_added));
				foreach ($added as $url_id) {
					$this->scans->set_scan_url_status_total_content($url_id, $this->scan->id);
				}

				// Check inspect data
				if ($this->inspect()) {
					return false;
				}
			}

		// Empty
		} elseif ($this->scans->get_scan_objects_count($this->scan->id, 'posts') >= $this->total_posts) {

			// Update populated status
			$this->scans->update_scan_trace($this->scan->id, array('populated_posts' => true));

			// Check inspect data
			if ($this->inspect()) {
				return false;
			}

		// Check data to inspect
		} elseif ($this->inspect()) {

			// Working
			return false;
		}

		// End or next post
		return $this->scans->get_scan_trace($this->scan->id, 'populated_posts')? true : $this->content_posts();
	}



	/**
	 * Retrieve next post
	 */
	private function content_posts_next($exceptions = array()) {

		// Globals
		global $wpdb;

		// Initialize
		$where = '1 = 1';

		// Check exceptions
		if (!empty($exceptions)) {
			$where .= ' AND ID NOT IN ('.implode(',', array_map('intval', $exceptions)).')';
		}

		// Check last date condition
		$last_date_gmt = $this->scans->get_scan_trace($this->scan->id, 'last_post_date_gmt');
		if (!empty($last_date_gmt)) {
			$where .= ' AND post_date_gmt '.(('asc' == $this->scan->crawl_order)? '>=' : '<=').' "'.esc_sql($last_date_gmt).'"';
		}

		// Set arguments
		$this->set_post_args();

		// Retrieve next post
		$post = $wpdb->get_row('SELECT ID, post_content, post_date_gmt, post_type FROM '.$wpdb->posts.' WHERE '.$where.' AND post_type '.$this->post_args['post_types'].' AND post_status '.$this->post_args['post_status'].$this->post_args['time_scope'].$this->post_args['content_filters'].' ORDER BY '.$this->post_args['order_by'].' LIMIT 1');

		// Check if post is previously registered
		if (!empty($post) && $this->scans->scan_object_exists($this->scan->id, $post->ID, 'posts')) {

			// Prepare identifiers
			$ids = array($post->ID);

			// Check same value of last date
			if ($post->post_date_gmt == $last_date_gmt) {

				// Check more identifiers with same date
				$existing_ids = $this->scans->get_scan_objects_ids_by_date($this->scan->id, 'posts', $last_date_gmt);
				if (!empty($existing_ids) && is_array($existing_ids)) {
					$ids = array_unique(array_merge($ids, $existing_ids));
				}
			}

			// Add exceptions
			$exceptions = empty($exceptions)? $ids : array_unique(array_merge($exceptions, $ids));

			// Next post with exceptions
			$post = $this->content_posts_next($exceptions);
		}

		// Done
		return empty($post)? false : $post;
	}



	/**
	 * Obtains and process contents from comments
	 */
	private function content_comments() {

		// Recursion limit
		static $recursion;
		$recursion = isset($recursion)? 1 : $recursion + 1;
		if ($recursion >= wplnst_get_nsetting('recursion_limit')) {
			$this->restart();
			return false;
		}

		// Content max pack
		$this->pack_index++;
		if ($this->pack_index > wplnst_get_nsetting('max_pack')) {
			$this->restart();
			return false;
		}

		// Check next comment
		if (false !== ($comment = $this->content_comments_next())) {

			// Register object
			if (!$this->scans->register_scan_object($this->scan->id, $comment->comment_ID, 'comments', $comment->comment_date_gmt)) {
				return $this->content_comments();
			}

			// Update last date gmt
			$this->scans->update_scan_trace($this->scan->id, array('last_comment_date_gmt' => $comment->comment_date_gmt));

			// Update comments index
			$this->scans->update_scan_trace($this->scan->id, array('comments_index' => $this->scans->get_scan_objects_count($this->scan->id, 'comments')));

			// Initialize
			$links_added = array();
			$images_added = array();
			$authors_added = array();

			// Retrieve parent permalink
			$parent_url = array('post_id' => $comment->comment_post_ID);

			// Process content
			if (!empty($comment->comment_content)) {

				// Process links
				if (in_array('links', $this->scan->link_types)) {
					$links = $this->extract_links($comment->comment_content, $parent_url, $comment->comment_ID, 'comments', "", 'comment_content', $comment->comment_date_gmt);
					if (!empty($links)) {
						$links_added = $this->save($links, $comment->comment_post_ID);
					}
				}

				// Process images
				if (in_array('images', $this->scan->link_types)) {
					$images = $this->extract_images($comment->comment_content, $parent_url, $comment->comment_ID, 'comments', "", 'comment_content', $comment->comment_date_gmt);
					if (!empty($images)) {
						$images_added = $this->save($images, $comment->comment_post_ID);
					}
				}
			}

			// Process author URL
			if (!empty($comment->comment_author_url) && in_array('links', $this->scan->link_types)) {

				// Check URL
				$urlinfo = $this->urlo->parse($comment->comment_author_url, $parent_url);
				if ($this->filter_include_urls($urlinfo['url']) && $this->filter_exclude_urls($urlinfo['url'])) {

					// Check anchor
					if ($this->filter_anchor_text($comment->comment_author)) {

						// Create link array
						$link = array_merge($urlinfo, array(
							'anchor' 			=> $comment->comment_author,
							'link_type' 		=> 'links',
							'object_id'	 		=> $comment->comment_ID,
							'object_type' 		=> 'comments',
							'object_post_type'	=> "",
							'object_field'		=> 'comment_author_url',
							'object_date_gmt' 	=> $comment->comment_date_gmt,
						));

						// Author links
						$authors_added = $this->save(array($link), $comment->comment_post_ID);
					}
				}
			}

			// Update stats for summary
			if (!empty($links_added) || !empty($images_added) || !empty($authors_added)) {

				// Update URLs phases
				$this->scans->set_scan_summary_urls_phases($this->scan->id);

				// Update objects matched
				$this->scans->set_scan_summary_objects_match($this->scan->id, 'comments');

				// Totals of content for each URL status added
				$added = array_unique(array_merge($links_added, $images_added, $authors_added));
				foreach ($added as $url_id) {
					$this->scans->set_scan_url_status_total_content($url_id, $this->scan->id);
				}

				// Check inspect data
				if ($this->inspect()) {
					return false;
				}
			}

		// Empty
		} elseif ($this->scans->get_scan_objects_count($this->scan->id, 'comments') >= $this->total_comments) {

			// Update populated status
			$this->scans->update_scan_trace($this->scan->id, array('populated_comments' => true));

			// Check inspect data
			if ($this->inspect()) {
				return false;
			}

		// Check data to inspect
		} elseif ($this->inspect()) {

			// Working
			return false;
		}

		// End or next comment
		return $this->scans->get_scan_trace($this->scan->id, 'populated_comments')? true : $this->content_comments();
	}



	/**
	 * Retrieve next comment
	 */
	private function content_comments_next($exceptions = array()) {

		// Globals
		global $wpdb;

		// Initialize
		$where = '1 = 1';

		// Check exceptions
		if (!empty($exceptions)) {
			$where .= ' AND comment_ID NOT IN ('.implode(',', array_map('intval', $exceptions)).')';
		}

		// Check last date condition
		$last_date_gmt = $this->scans->get_scan_trace($this->scan->id, 'last_comment_date_gmt');
		if (!empty($last_date_gmt)) {
			$where .= ' AND comment_date_gmt '.(('asc' == $this->scan->crawl_order)? '>=' : '<=').' "'.esc_sql($last_date_gmt).'"';
		}

		// Set arguments
		$this->set_comment_args();

		// Retrieve next comment
		$comment = $wpdb->get_row('SELECT comment_ID, comment_post_ID, comment_content, comment_author, comment_author_url, comment_date_gmt FROM '.$wpdb->comments.' WHERE '.$where.' '.$this->comment_args['comment_status'].$this->comment_args['time_scope'].$this->comment_args['content_filters'].' ORDER BY '.$this->comment_args['order_by'].' LIMIT 1');

		// Check if comment id previously registered
		if (!empty($comment) && $this->scans->scan_object_exists($this->scan->id, $comment->comment_ID, 'comments')) {

			// Prepare identifiers
			$ids = array($comment->comment_ID);

			// Check same value of last date
			if ($comment->comment_date_gmt == $last_date_gmt) {

				// Check more identifiers with same date
				$existing_ids = $this->scans->get_scan_objects_ids_by_date($this->scan->id, 'comments', $last_date_gmt);
				if (!empty($existing_ids) && is_array($existing_ids)) {
					$ids = array_unique(array_merge($ids, $existing_ids));
				}
			}

			// Add exceptions
			$exceptions = empty($exceptions)? $ids : array_unique(array_merge($exceptions, $ids));

			// Next comment with exceptions
			$comment = $this->content_comments_next($exceptions);
		}

		// Done
		return empty($comment)? false : $comment;
	}



	/**
	 * Obtains and process links from the blogroll
	 */
	private function content_blogroll() {

		// Recursion limit
		static $recursion;
		$recursion = isset($recursion)? 1 : $recursion + 1;
		if ($recursion >= wplnst_get_nsetting('recursion_limit')) {
			$this->restart();
			return false;
		}

		// Content max pack
		$this->pack_index++;
		if ($this->pack_index > wplnst_get_nsetting('max_pack')) {
			$this->restart();
			return false;
		}

		// Check next blogroll link
		if (false !== ($blogroll_link = $this->content_blogroll_next())) {

			// Register object
			if (!$this->scans->register_scan_object($this->scan->id, $blogroll_link->link_id, 'blogroll')) {
				return $this->content_blogroll();
			}

			// Update last blogroll id
			$this->scans->update_scan_trace($this->scan->id, array('last_blogroll_id' => $blogroll_link->link_id));

			// Update blogroll index
			$this->scans->update_scan_trace($this->scan->id, array('blogroll_index' => $this->scans->get_scan_objects_count($this->scan->id, 'blogroll')));

			// Process link URL
			if (!empty($blogroll_link->link_url)) {

				// Check URL
				$urlinfo = $this->urlo->parse($blogroll_link->link_url);
				if ($this->filter_include_urls($urlinfo['url']) && $this->filter_exclude_urls($urlinfo['url'])) {

					// Check anchor
					if ($this->filter_anchor_text($blogroll_link->link_name)) {

						// Create link array
						$link = array_merge($urlinfo, array(
							'anchor' 			=> $blogroll_link->link_name,
							'link_type' 		=> 'links',
							'object_id'	 		=> $blogroll_link->link_id,
							'object_type' 		=> 'blogroll',
							'object_post_type'	=> "",
							'object_field'		=> 'link_url',
							'object_date_gmt' 	=> '0000-00-00 00:00:00',
						));

						// Add blogroll link
						$blogroll_added = $this->save(array($link));

						// Update URLs phases
						$this->scans->set_scan_summary_urls_phases($this->scan->id);

						// Update objects matched
						$this->scans->set_scan_summary_objects_match($this->scan->id, 'blogroll');

						// Totals of content for each URL status added
						foreach ($blogroll_added as $url_id) {
							$this->scans->set_scan_url_status_total_content($url_id, $this->scan->id);
						}

						// Check inspect data
						if ($this->inspect()) {
							return false;
						}
					}
				}
			}

		// Empty
		} elseif ($this->scans->get_scan_objects_count($this->scan->id, 'blogroll') >= $this->total_blogroll) {

			// Update populated status
			$this->scans->update_scan_trace($this->scan->id, array('populated_blogroll' => true));

			// Check inspect data
			if ($this->inspect()) {
				return false;
			}

		// Check data to inspect
		} elseif ($this->inspect()) {

			// Working
			return false;
		}

		// End or next blogroll link
		return $this->scans->get_scan_trace($this->scan->id, 'populated_blogroll')? true : $this->content_blogroll();
	}



	/**
	 * Retrieve next blogroll link
	 */
	private function content_blogroll_next($exceptions = array()) {

		// Globals
		global $wpdb;

		// Initialize
		$where = '1 = 1';

		// Check exceptions
		if (!empty($exceptions)) {
			$where .= ' AND link_id NOT IN ('.implode(',', array_map('intval', $exceptions)).')';
		}

		// Check last identifier condition
		$last_id = (int) $this->scans->get_scan_trace($this->scan->id, 'last_blogroll_id');
		if (!empty($last_id)) {
			$where .= ' AND link_id '.(('asc' == $this->scan->crawl_order)? '>' : '<').' '.$last_id;
		}

		// Set arguments
		$this->set_blogroll_args();

		// Query next blogroll link
		$bookmark = $wpdb->get_row('SELECT link_id, link_url, link_name FROM '.$wpdb->links.' WHERE '.$where.' ORDER BY '.$this->blogroll_args['order_by'].' LIMIT 1');

		// Check if bookmark is previously registered
		if (!empty($bookmark) && $this->scans->scan_object_exists($this->scan->id, $bookmark->link_id, 'blogroll')) {
			$exceptions[] = $bookmark->link_id;
			$bookmark = $this->content_blogroll_next($exceptions);
		}

		// Done
		return empty($bookmark)? false : $bookmark;
	}



	/**
	 * Extract links from content
	 */
	private function extract_links($content, $parent_url, $object_id, $object_type, $object_post_type, $object_field, $object_date_gmt) {

		// Initialize
		$links = array();

		// Check links
		if (preg_match_all('/(<a[^>]+href=["|\'](.+)["|\'][^>]*>)(.*)<\/a>/isUu', $content, $matches, PREG_SET_ORDER) > 0) {

			// Enum matched links tags
			foreach ($matches as $match) {

				// Skip page anchor
				if ('#' == mb_substr($match[2], 0, 1)) {
					continue;
				}

				// Check anchor
				$anchor = $match[3];
				if (!$this->filter_anchor_text($anchor)) {
					continue;
				}

				// Check URL
				$urlinfo = $this->urlo->parse($match[2], $parent_url);
				if (!$this->filter_include_urls($urlinfo['url']) || !$this->filter_exclude_urls($urlinfo['url'])) {
					continue;
				}

				// Check attributes
				$attributes = $this->urlo->extract_attributes($match[1]);
				if (!$this->filter_html_attributes('a', $attributes)) {
					continue;
				}

				// Check nofollow
				$nofollow = false;
				if (!empty($attributes['rel'])) {
					$values = explode(' ', strtolower(str_replace("\n", ' ', str_replace("\r", ' ', $attributes['rel']))));
					if (in_array('nofollow', $values)) {
						$nofollow = true;
					}
				}

				// Add item
				$links[] = array_merge($urlinfo, array(
					'chunk' 			=> $match[0],
					'anchor' 			=> $anchor,
					'nofollow'			=> $nofollow,
					'attributes' 		=> $attributes,
					'link_type' 		=> 'links',
					'object_id'	 		=> $object_id,
					'object_type' 		=> $object_type,
					'object_post_type'	=> $object_post_type,
					'object_field'		=> $object_field,
					'object_date_gmt' 	=> $object_date_gmt,
				));
			}
		}

		// Done
		return $links;
	}



	/**
	 * Extract images from content
	 */
	private function extract_images($content, $parent_url, $object_id, $object_type, $object_post_type, $object_field, $object_date_gmt) {

		// Initialize
		$images = array();

		// Check links
		if (preg_match_all('/<img[^>]+src=["|\'](.+)["|\'][^>]*>/isUu', $content, $matches, PREG_SET_ORDER) > 0) {

			// Enum matched links tags
			foreach ($matches as $match) {

				// Skip page anchor
				if ('#' == mb_substr($match[1], 0, 1)) {
					continue;
				}

				// Check URL
				$urlinfo = $this->urlo->parse($match[1], $parent_url);
				if (!$this->filter_include_urls($urlinfo['url']) || !$this->filter_exclude_urls($urlinfo['url'])) {
					continue;
				}

				// Check attributes
				$attributes = $this->urlo->extract_attributes($match[0]);
				if (!$this->filter_html_attributes('img', $attributes)) {
					continue;
				}

				// Add item
				$images[] = array_merge($urlinfo, array(
					'chunk' 			=> $match[0],
					'attributes' 		=> $attributes,
					'link_type' 		=> 'images',
					'object_id'	 		=> $object_id,
					'object_type' 		=> $object_type,
					'object_post_type'	=> $object_post_type,
					'object_field'		=> $object_field,
					'object_date_gmt' 	=> $object_date_gmt,
				));
			}
		}

		// Done
		return $images;
	}



	// Filter content
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check if passes anchor text filters
	 */
	private function filter_anchor_text($anchor) {

		// Check filters
		if (empty($this->scan->anchor_filters)) {
			return true;
		}

		// Initialize
		$passed = array();

		// Version without tags
		$anchor_text = strip_tags($anchor);

		// Enum filters
		foreach ($this->scan->anchor_filters as $filter) {

			// Check filter
			if (empty($filter['type']) || !isset($filter['value'])) {
				continue;
			}

			// Check if contains a value
			if ('contains' == $filter['type']) {

				// Continue if passed
				if (isset($passed['contains']) && true === $passed['contains']) {
					continue;
				}

				// Check tag in filter
				$is_filter_tag = (false !== strpos($filter['value'], '<') || false !== strpos($filter['value'], '>'));

				// Test
				$passed['contains'] = (false !== stripos($anchor_text, $filter['value'])) || ($is_filter_tag && false !== stripos($anchor, $filter['value']));

			// Check if not contain a value
			} elseif ('not-contains' == $filter['type']) {

				// Continue if passed
				if (isset($passed['contains']) && false === $passed['contains']) {
					continue;
				}

				// Check tag in filter
				$is_filter_tag = (false !== strpos($filter['value'], '<') || false !== strpos($filter['value'], '>'));

				// Test
				$passed['not-contains'] = (false === stripos($anchor_text, $filter['value'])) || ($is_filter_tag && false === stripos($anchor, $filter['value']));

			// Check if equal to a value
			} elseif ('equal-to' == $filter['type']) {

				// Continue if passed
				if (isset($passed['equal-to']) && true === $passed['equal-to']) {
					continue;
				}

				// Test
				$passed['equal-to'] = ($anchor == $filter['value']);

			// Check if not equal to a value
			} elseif ('not-equal-to' == $filter['type']) {

				// Continue if passed
				if (isset($passed['not-equal-to']) && false === $passed['not-equal-to']) {
					continue;
				}

				// Test
				$passed['not-equal-to'] = ($anchor != $filter['value']);

			// Check if begins with value
			} elseif ('begins-with' == $filter['type']) {

				// Continue if passed
				if (isset($passed['begins-with']) && true === $passed['begins-with']) {
					continue;
				}

				// Test
				$passed['begins-with'] = (0 === stripos($anchor, $filter['value']));

			// Check if ends with value
			} elseif ('ends-by' == $filter['type']) {

				// Continue if passed
				if (isset($passed['ends-by']) && true === $passed['ends-by']) {
					continue;
				}

				// Test
				$passed['ends-by'] = (($temp = strlen($anchor) - strlen($filter['value'])) >= 0 && stripos($anchor, $filter['value'], $temp) !== false);

			// Check if is empty
			} elseif ('empty' == $filter['type']) {

				// Continue if passed
				if (isset($passed['empty'])) {
					continue;
				}

				// Test
				$passed['empty'] = ($anchor === '');
			}
		}

		// Check filters
		$checks = array('contains', 'not-contains', 'equal-to', 'not-equal', 'begins-with', 'ends-by', 'empty');
		foreach ($checks as $check) {
			if (isset($passed[$check]) && false === $passed[$check]) {
				return false;
			}
		}

		// Done
		return true;
	}



	/**
	 * Filter for URL including
	 */
	private function filter_include_urls($url) {

		// Check filters
		if (empty($this->scan->include_urls)) {
			return true;
		}

		// Initialize
		$passed = array();

		// Enum filters
		foreach ($this->scan->include_urls as $filter) {

			// Check filter
			if (empty($filter['type']) || !isset($filter['value'])) {
				continue;
			}

			// Check if match all value
			if ('full-url' == $filter['type']) {

				// Continue if passed
				if (isset($passed['full-url']) && true === $passed['full-url']) {
					continue;
				}

				// Test
				$passed['full-url'] = ($url == $filter['value']);

			// Check if match a value
			} elseif ('matched-string' == $filter['type']) {

				// Continue if passed
				if (isset($passed['matched-string']) && true === $passed['matched-string']) {
					continue;
				}

				// Test
				$passed['matched-string'] = (false !== stripos($url, $filter['value']));

			// Check if is prefixed
			} elseif ('url-prefix' == $filter['type']) {

				// Continue if passed
				if (isset($passed['url-prefix']) && true === $passed['url-prefix']) {
					continue;
				}

				// Test
				$passed['url-prefix'] = (0 === stripos($url, $filter['value']));

			// Check if ends with value
			} elseif ('url-suffix' == $filter['type']) {

				// Continue if passed
				if (isset($passed['url-suffix']) && true === $passed['url-suffix']) {
					continue;
				}

				// Test
				$passed['url-suffix'] = (($temp = strlen($url) - strlen($filter['value'])) >= 0 && stripos($url, $filter['value'], $temp) !== false);
			}
		}

		// Check filters
		$checks = array('full-url', 'matched-string', 'url-prefix', 'url-suffix');
		foreach ($checks as $check) {
			if (isset($passed[$check]) && false === $passed[$check]) {
				return false;
			}
		}

		// Done
		return true;
	}



	/**
	 * Filter for URL excluding
	 */
	private function filter_exclude_urls($url) {

		// Check filters
		if (empty($this->scan->exclude_urls)) {
			return true;
		}

		// Initialize
		$passed = array();

		// Enum filters
		foreach ($this->scan->exclude_urls as $filter) {

			// Check filter
			if (empty($filter['type']) || !isset($filter['value'])) {
				continue;
			}

			// Check if match all value
			if ('full-url' == $filter['type']) {

				// Continue if passed
				if (isset($passed['full-url']) && false === $passed['full-url']) {
					continue;
				}

				// Test
				$passed['full-url'] = ($url != $filter['value']);

			// Check if match a value
			} elseif ('matched-string' == $filter['type']) {

				// Continue if passed
				if (isset($passed['matched-string']) && false === $passed['matched-string']) {
					continue;
				}

				// Test
				$passed['matched-string'] = (false === stripos($url, $filter['value']));

			// Check if is prefixed
			} elseif ('url-prefix' == $filter['type']) {

				// Continue if passed
				if (isset($passed['url-prefix']) && false === $passed['url-prefix']) {
					continue;
				}

				// Test
				$passed['url-prefix'] = (0 !== stripos($url, $filter['value']));

			// Check if ends with value
			} elseif ('url-suffix' == $filter['type']) {

				// Continue if passed
				if (isset($passed['url-suffix']) && false === $passed['url-suffix']) {
					continue;
				}

				// Test
				$passed['url-suffix'] = (($temp = strlen($url) - strlen($filter['value'])) >= 0 && stripos($url, $filter['value'], $temp) === false);
			}
		}

		// Check filters
		$checks = array('full-url', 'matched-string', 'url-prefix', 'url-suffix');
		foreach ($checks as $check) {
			if (isset($passed[$check]) && false === $passed[$check]) {
				return false;
			}
		}

		// Done
		return true;
	}



	/**
	 * Filter based on attributes
	 */
	private function filter_html_attributes($element, $attributes) {

		// Check filters
		if (empty($this->scan->html_attributes)) {
			return true;
		}

		// Initialize
		$passed = array();

		// Isolate attributes
		$att_names = array_keys($attributes);
		$att_values = array_values($attributes);

		// Enum filters
		foreach ($this->scan->html_attributes as $filter) {

			// Check filter
			if (empty($filter['element']) || $element != $filter['element'] || !isset($filter['att']) || empty($filter['having'])) {
				continue;
			}

			// Check if not have an attribute
			if ('not-have' == $filter['having']) {

				// Continue if not passed
				if (isset($passed['not-have']) && false === $passed['not-have']) {
					continue;
				}

				// Test
				$passed['not-have'] = !in_array($filter['att'], $att_names);

			// Contains
			} elseif ('contains' == $filter['op']) {

				// Continue if passed
				if (isset($passed['contains']) && true === $passed['contains']) {
					continue;
				}

				// Need a filter value
				if (!isset($filter['value']) || '' === ''.trim($filter['value'])) {
					$passed['contains'] = false;
					continue;
				}

				// Check att name compatibility
				if (!in_array($filter['att'], $att_names)) {
					$passed['contains'] = false;
					continue;
				}

				// Final test
				$passed['contains'] = in_array(strtolower($filter['value']), $att_values)? $filter['value'] : false;

			// Not contains
			} elseif ('not-contains' == $filter['op']) {

				// Continue if passed
				if (isset($passed['not-contains']) && true === $passed['not-contains']) {
					continue;
				}

				// Need a filter value
				if (!isset($filter['value']) || '' === ''.trim($filter['value'])) {
					$passed['not-contains'] = false;
					continue;
				}

				// Check att name compatibility
				if (!in_array($filter['att'], $att_names)) {
					$passed['not-contains'] = false;
					continue;
				}

				// Final test
				$passed['not-contains'] = !in_array(strtolower($filter['value']), $att_values)? $filter['value'] : false;

			// Equal
			} elseif ('equal' == $filter['op']) {

				// Continue if passed
				if (isset($passed['equal']) && true === $passed['equal']) {
					continue;
				}

				// Test
				$passed['equal'] = in_array($filter['att'], $att_names)? ($attributes[$filter['att']] == (isset($filter['value'])? $filter['value'] : '')) : false;

			// Not equal
			} elseif ('not-equal' == $filter['op']) {

				// Continue if passed
				if (isset($passed['not-equal']) && false === $passed['not-equal']) {
					continue;
				}

				// Test
				$passed['not-equal'] = in_array($filter['att'], $att_names)? ($attributes[$filter['att']] != (isset($filter['value'])? $filter['value'] : '')) : false;

			// Not empty value
			} elseif ('not-empty' == $filter['op']) {

				// Continue if passed
				if (isset($passed['not-empty']) && false === $passed['not-empty']) {
					continue;
				}

				// Test
				$passed['not-empty'] = in_array($filter['att'], $att_names)? ($attributes[$filter['att']] !== '') : false;

			// Empty value
			} elseif ('empty' == $filter['op']) {

				// Continue if passed
				if (isset($passed['empty']) && false === $passed['empty']) {
					continue;
				}

				// Test
				$passed['empty'] = in_array($filter['att'], $att_names)? ($attributes[$filter['att']] === '') : false;
			}
		}

		// Check filters
		$checks = array('not-have', 'contains', 'not-contains', 'equal', 'not-equal', 'not-empty', 'empty');
		foreach ($checks as $check) {
			if (isset($passed[$check]) && false === $passed[$check]) {
				return false;
			}
		}

		// Done
		return true;
	}



	/**
	 * Extract filter content for queries
	 */
	private function filter_for_queries() {


		/* Initialization */

		// Check cache
		static $matches;
		if (isset($matches)) {
			return $matches;
		}
		$matches = array();

		// Check configuration
		if (!$this->scan->filtered_query) {
			return $matches;
		}


		/* Anchor filters */

		// Check anchor filters
		if (!empty($this->scan->anchor_filters)) {

			// Enum anchor filters
			foreach ($this->scan->anchor_filters as $filter) {

				// Check filter
				if (empty($filter['type']) || !isset($filter['value']) || '' === ''.trim($filter['value'])) {
					continue;
				}

				// Match filters
				if (in_array($filter['type'], array('contains', 'equal-to', 'begins-with', 'ends-by'))) {

					// Need this string
					$matches[] = $filter['value'];
				}
			}
		}


		/* Include URLs */

		// Check include filters
		if (!empty($this->scan->include_urls)) {

			// Enum include filters
			foreach ($this->scan->include_urls as $filter) {

				// Check filter
				if (empty($filter['type']) || !isset($filter['value']) || '' === ''.trim($filter['value'])) {
					continue;
				}

				// Match filters
				if (in_array($filter['type'], array('full-url', 'matched-string', 'url-prefix', 'url-suffix'))) {

					// Need this string
					$matches[] = $filter['value'];
				}
			}
		}


		/* HTML attributes */

		// Check attributes filters
		if (!empty($this->scan->html_attributes)) {

			// Enum filters
			foreach ($this->scan->html_attributes as $filter) {

				// Check filter value
				if (!isset($filter['value']) || '' === ''.trim($filter['value'])) {
					continue;
				}

				// Check having and attribute part
				if (empty($filter['having']) || 'have' != $filter['having'] || empty($filter['att']) || '' == 'att'.trim($filter['att'])) {
					continue;
				}

				// Check operation
				if (empty($filter['op']) || !in_array($filter['op'], array('contains', 'equal'))) {
					continue;
				}

				// Add string
				$matches[] = $filter['value'];
			}
		}


		// Done
		return $matches;
	}



	// Data access to store content data
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Save links
	 */
	private function save($links, $parent_post_id = 0) {

		// Initialize
		$statuses  = array();

		// Enum links
		foreach ($links as $link) {

			// Avoid empty URLs
			if ('' === ''.trim($link['url'])) {
				continue;
			}

			// Malformed links tracking
			if ($link['malformed']) {
				if (!$this->scan->malformed) {
					continue;
				}
			}

			// Search by main URL
			if (false !== ($row = $this->scans->get_scan_url(array('url' => $link['url'], 'no_cache' => true)))) {
				$url_id = $row->url_id;

			// Register scan URL
			} elseif (false === ($url_id = $this->scans->add_scan_url($link, $this->scan->id))) {
				continue;
			}

			// Check destination type
			if (isset($link['scope']) && in_array($this->scan->destination_type, array('internal', 'external')) && $link['scope'] != $this->scan->destination_type) {
				continue;
			}

			// Save URL location
			$this->scans->add_scan_url_location($url_id, $this->scan->id, $link);

			// Check our tiny cache
			if (in_array($url_id, $statuses)) {
				continue;
			}

			// Check if exists a previous status identifier
			if (false !== $this->scans->get_scan_url_status(array('url_id' => $url_id, 'scan_id' => $this->scan->id))) {
				$statuses[] = $url_id;
				continue;
			}

			// Check phase
			$phase = $this->urlo->is_crawleable($link)? 'wait' : 'end';

			// Create URL status
			$this->scans->add_scan_url_status($url_id, $this->scan->id, $phase);

			// Status on
			$statuses[] = $url_id;
		}

		// Done
		return $statuses;
	}



	// Utilities functions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Cast relative URLs to absolute
	 */
	public function parent_permalink($parent) {

		// Check array
		if (!is_array($parent) || !isset($parent['post_id'])) {
			return false;
		}

		// Check identifier
		$post_id = (int) $parent['post_id'];
		if (empty($post_id)) {
			return false;
		}

		// Return permalink
		return $this->get_permalink($post_id);
	}



	/**
	 * Retrieve permalink based on WP redirection.
	 * Here is too early to call WP get_permalink function,
	 * so we use the short permalink form to catch the redirection.
	 */
	private function get_permalink($post_id) {

		// Permalinks cache
		static $permalinks;
		if (!isset($permalinks)) {
			$permalinks = array();
		}

		// Check cache
		if (isset($permalinks[$post_id])) {
			return $permalinks[$post_id];
		}

		// Initialize
		$permalinks[$post_id] = false;

		// Load cURL wrapper library
		wplnst_require('core', 'curl');

		// cURL request
		$response = WPLNST_Core_CURL::request(array(
			'CURLOPT_URL'				=> $this->urlo->home_url.'/?p='.$post_id,
			'CURLOPT_HEADER' 			=> true,
			'CURLOPT_NOBODY' 			=> true,
			'CURLOPT_HTTPHEADER' 		=> array('Expect:'),
			'CURLOPT_FOLLOWLOCATION' 	=> false,
			'CURLOPT_RETURNTRANSFER' 	=> true,
			'CURLOPT_FRESH_CONNECT' 	=> true,
			'CURLOPT_CONNECTTIMEOUT' 	=> $this->connect_timeout,
			'CURLOPT_TIMEOUT' 			=> $this->request_timeout,
			'CURLOPT_USERAGENT' 		=> wplnst_get_tsetting('user_agent'),
		));

		// Check response
		if ($response['error']) {
			return false;
		}

		// Check response
		$data = trim($response['data']);
		if (empty($data)) {
			return false;
		}

		// Enum response links
		$data = explode("\n", $data);
		foreach ($data as $line) {

			// Clear line
			$line = trim(preg_replace('/\s+/', ' ', $line));

			// Check redirection in status code
			if (!isset($status_code) && 0 === stripos($line, 'HTTP/')) {
				$line = explode(' ', $line);
				if (count($line) > 1) {
					$status_code = trim($line[1]);
					if ('30' != mb_substr($status_code, 0, 2)) {
						return false;
					}
				}

			// Check Location
			} elseif (isset($status_code) && 0 === stripos($line, 'location:')) {
				$location = trim(mb_substr($line, 9));
				$parts = @parse_url($location);
				if (!empty($parts['scheme']) && !empty($parts['host'])) {
					$permalinks[$post_id] = $location;
				}
			}
		}

		// Done
		return $permalinks[$post_id];
	}



	// Internal debug
	private function debug($message) {
		wplnst_debug('scan '.$this->scan->id.' - thread '.$this->thread_id.' - '.$message, 'crawler');
	}



	/**
	 * Start again the crawler
	 */
	protected function restart() {
		WPLNST_Core_Alive::run($this->scan->id, $this->scan->hash, $this->thread_id);
	}



	/**
	 * Calls alive activity method to check scans activity
	 */
	protected function activity() {
		WPLNST_Core_Alive::activity(true);
	}



}