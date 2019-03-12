<?php

/**
 * Types class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Types {



	// Constants
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Post types avoided
	 */
	const post_types_avoid = 'attachment, nav_menu_item, revision';



	/**
	 * Post status allowed
	 */
	const post_status_allow = 'publish, future, draft, pending, private, trash';



	/**
	 * Default scans and results per page
	 */
	const scans_per_page = 5;
	const scans_results_per_page = 25;



	// Data types
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Post types allowed
	 */
	public static function get_post_types($output = 'keys-names') {

		// Current avoid post types
		$avoid_post_types = apply_filters('wplnst_avoid_post_types', array_map('trim', explode(',', self::post_types_avoid)));
		if (empty($avoid_post_types) || !is_array($avoid_post_types)) {
			return false;
		}

		// Compute allowed post types
		$post_types = get_post_types(array(), 'objects');
		$allowed_post_types = array_diff_key($post_types, array_fill_keys($avoid_post_types, true));

		// Return key-name values
		if ('keys-names' == $output) {
			$keys_names = array();
			foreach ($allowed_post_types as $key => $post_type) {
				$keys_names[$key] = $post_type->labels->name;
			}
			return $keys_names;

		// Return keys
		} elseif ('keys' == $output) {
			return array_keys($allowed_post_types);

		// Return names
		} elseif ('names' == $output) {
			$names = array();
			foreach ($allowed_post_types as $key => $post_type) {
				$names[] = $post_type->labels->name;
			}
			return $names;
		}

		// Default
		return $allowed_post_types;
	}



	/**
	 * Post status allowed
	 */
	public static function get_post_status() {
		$post_status_allowed = array_map('trim', explode(',', self::post_status_allow));
		$post_status = apply_filters('wplnst_allow_post_status', $post_status_allowed);
		return (empty($post_status) && !is_array($post_status))? false : array_intersect($post_status, $post_status_allowed);
	}



	/**
	 * Return an array of tipified objects
	 */
	public static function get_objects_types() {
		return array(
			'posts' 	=> __('Entries',  'wplnst'),
			'comments' 	=> __('Comments', 'wplnst'),
			'blogroll' 	=> __('Blogroll', 'wplnst'),
		);
	}



	/**
	 * Return an array of possible scan status
	 */
	public static function get_scan_statuses() {
		return array(
			'wait' 		=> __('Waiting', 	'wplnst'),
			'queued' 	=> __('Queued',  	'wplnst'),
			'play'		=> __('Running', 	'wplnst'),
			'stop'		=> __('Stopped', 	'wplnst'),
			'end'		=> __('Completed', 	'wplnst'),
		);
	}



	/**
	 * Return link destination types
	 */
	public static function get_destination_types() {
		return array(
			'all' 		=> __('All URLs', 		'wplnst'),
			'internal' 	=> __('Internal URLs', 	'wplnst'),
			'external' 	=> __('External URLs', 	'wplnst'),
		);
	}



	/**
	 * Return available time scopes
	 */
	public static function get_time_scopes() {
		return array(
			'anytime' 	=> __('Anytime content', 'wplnst'),
			'yesterday' => __('From yesterday',  'wplnst'),
			'7days' 	=> __('Last 7 days', 	 'wplnst'),
			'15days' 	=> __('Last 15 days', 	 'wplnst'),
			'month' 	=> __('One month', 		 'wplnst'),
			'3months'	=> __('Last 3 months', 	 'wplnst'),
			'6months'	=> __('Last 6 months', 	 'wplnst'),
			'year'		=> __('One year', 		 'wplnst'),
			/* 'custom' => 'Custom' */
		);
	}



	/**
	 * Return link types
	 */
	public static function get_link_types() {
		return array(
			'links'  => __('Links',  'wplnst'),
			'images' => __('Images', 'wplnst'),
		);
	}



	/**
	 * Return crawl order values
	 */
	public static function get_crawl_order() {
		return array(
			'desc' 	=> __('Most recent content',  'wplnst'),
			'asc' 	=> __('Oldest content first', 'wplnst'),
		);
	}



	/**
	 * Return custom fields types array
	 */
	public static function get_custom_fields() {
		return array(
			'url' 	=> 'URL',
			'html'	=> 'HTML',
		);
	}



	/**
	 * Return allowed comment types
	 */
	public static function get_comment_types() {
		return array(
			'approved' 	=> __('Approved', 'wplnst'),
			'pending'	=> __('Pending',  'wplnst'),
		);
	}



	/**
	 * Return database values for comment types keys
	 */
	public static function get_comment_types_values($comment_types) {
		$comment_types_values = array();
		$eq = array('approved' 	=> '1', 'pending' => '0');
		foreach ($comment_types as $comment_type) {
			if (isset($eq[$comment_type])) {
				$comment_types_values[] = $eq[$comment_type];
			}
		}
		return $comment_types_values;
	}



	/**
	 * Return anchor filters types array
	 */
	public static function get_anchor_filters() {
		return array(
			'contains' 		=> __('Contains', 		'wplnst'),
			'not-contains' 	=> __('Not contains', 	'wplnst'),
			'equal-to' 		=> __('Is equal to', 	'wplnst'),
			'not-equal-to' 	=> __('Not equal to', 	'wplnst'),
			'begins-with' 	=> __('Starts with', 	'wplnst'),
			'ends-by' 		=> __('Ends by', 		'wplnst'),
			'empty' 		=> __('Empty', 			'wplnst'),
		);
	}



	/**
	 * Return filters for Anchor text search
	 */
	public static function get_anchor_search_filters() {
		return array(
			'm' => __('Matched string', 'wplnst'),
			'p' => __('Starts with', 	'wplnst'),
			's' => __('Ends by', 		'wplnst'),
			'f' => __('Full anchor', 	'wplnst'),
		);
	}



	/**
	 * Return filters for URL inclusion and exclusion
	 */
	public static function get_url_filters() {
		return array(
			'matched-string' => __('Matched string', 'wplnst'),
			'url-prefix' 	 => __('URL prefix', 	 'wplnst'),
			'url-suffix' 	 => __('URL suffix', 	 'wplnst'),
			'full-url' 		 => __('Full URL', 		 'wplnst'),
		);
	}



	/**
	 * Return filters for URL search
	 */
	public static function get_url_search_filters() {
		return array(
			'm' => __('Matched string', 'wplnst'),
			'p' => __('URL prefix', 	'wplnst'),
			's' => __('URL suffix', 	'wplnst'),
			'r' => __('URL fragment #', 'wplnst'),
			'f' => __('Full URL', 		'wplnst'),
		);
	}



	/**
	 * Return array of HTML elements having options
	 */
	public static function get_html_attributes_having() {
		return array(
			'have' 		=> __('Has', 	  'wplnst'),
			'not-have' 	=> __('Not have', 'wplnst'),
		);
	}



	/**
	 * Return array of HTML elements operation
	 */
	public static function get_html_attributes_operators() {
		return array(
			'contains' 		=> __('Contains', 		'wplnst'),
			'not-contains' 	=> __('Not contains', 	'wplnst'),
			'equal' 		=> __('Is equal to', 	'wplnst'),
			'not-equal' 	=> __('Not equal to', 	'wplnst'),
			'not-empty' 	=> __('Not empty', 		'wplnst'),
			'empty' 		=> __('Empty', 			'wplnst'),
		);
	}



	/**
	 * All status levels
	 */
	public static function get_status_levels() {
		return array(
			'2' => __('Success', 		'wplnst'),
			'3' => __('Redirections', 	'wplnst'),
			'4' => __('Errors', 		'wplnst'),
			'5' => __('Server Error', 	'wplnst'),
		);
	}



	/**
	 * All status codes
	 */
	public static function get_status_codes() {
		return array(
			'2' => array(
				'200' => __('OK', 								'wplnst'),
				'201' => __('Created', 							'wplnst'),
				'202' => __('Accepted', 						'wplnst'),
				'203' => __('Non-Authoritative Information', 	'wplnst'),
				'204' => __('No Content', 						'wplnst'),
				'205' => __('Reset Content', 					'wplnst'),
				'206' => __('Partial Content', 					'wplnst'),
			),
			'3' => array(
				'300' => __('Multiple Choices', 				'wplnst'),
				'301' => __('Moved Permanently', 				'wplnst'),
				'302' => __('Found', 							'wplnst'),
				'303' => __('See Other', 						'wplnst'),
				'304' => __('Not Modified', 					'wplnst'),
				'305' => __('Use Proxy', 						'wplnst'),
				'307' => __('Temporary Redirect', 				'wplnst'),
			),
			'4' => array(
				'400' => __('Bad Request', 						'wplnst'),
				'401' => __('Unauthorized', 					'wplnst'),
				'402' => __('Payment Required', 				'wplnst'),
				'403' => __('Forbidden', 						'wplnst'),
				'404' => __('Not Found', 						'wplnst'),
				'405' => __('Method Not Allowed', 				'wplnst'),
				'406' => __('Not Acceptable', 					'wplnst'),
				'407' => __('Proxy Authentication Required', 	'wplnst'),
				'408' => __('Request Timeout', 					'wplnst'),
				'409' => __('Conflict', 						'wplnst'),
				'410' => __('Gone', 							'wplnst'),
				'411' => __('Length Required', 					'wplnst'),
				'412' => __('Precondition Failed', 				'wplnst'),
				'413' => __('Request Entity Too Large', 		'wplnst'),
				'414' => __('Request-URI Too Long', 			'wplnst'),
				'415' => __('Unsupported Media Type', 			'wplnst'),
				'416' => __('Requested Range Not Satisfiable', 	'wplnst'),
				'417' => __('Expectation Failed', 				'wplnst'),
			),
			'5' => array(
				'500' => __('Internal Server Error', 			'wplnst'),
				'501' => __('Not Implemented', 					'wplnst'),
				'502' => __('Bad Gateway', 						'wplnst'),
				'503' => __('Service Unavailable', 				'wplnst'),
				'504' => __('Gateway Timeout', 					'wplnst'),
				'505' => __('HTTP Version Not Supported', 		'wplnst'),
			)
		);
	}



	/**
	 * Return only codes in one-dimensional array
	 */
	public static function get_status_codes_raw() {
		$raw = array();
		$codes = self::get_status_codes();
		foreach ($codes as $level => $status) {
			foreach ($status as $code => $description) {
				$raw[$code] = $description;
			}
		}
		return $raw;
	}



	/**
	 * Return SEO link types
	 */
	public static function get_seo_link_types() {
		return array(
			'nf' => __('NoFollow', 'wplnst'),
			'df' => __('DoFollow', 'wplnst'),
		);
	}



	/**
	 * Return protocol filters
	 */
	public static function get_protocol_types() {
		return array(
			'http'  => 'HTTP',
			'https' => 'HTTPS',
			'rel'	=> 'Relative //',
		);
	}



	/**
	 * Return special types of URLs
	 */
	public static function get_special_types() {
		return array(
			'rel' => __('Relative',  'wplnst'),
			'abs' => __('Absolute',  'wplnst'),
			'spa' => __('Spaced', 	 'wplnst'),
			'mal' => __('Malformed', 'wplnst'),
		);
	}



	/**
	 * Return actions performed
	 */
	public static function get_action_types() {
		return array(
			'unl' => __('Unlinked', 	'wplnst'),
			'mod' => __('Modified', 	'wplnst'),
			'umd' => __('Unmodified', 	'wplnst'),
			'rec' => __('Rechecked', 	'wplnst'),
		);
	}



	/**
	 * Return filter for ignored results
	 */
	public static function get_ignored_types() {
		return array(
			'oir' => __('Only ignored results', 	'wplnst'),
			'ian' => __('Ignored and not ignored', 	'wplnst'),
		);
	}



	/**
	 * Return allowed order by
	 */
	public static function get_order_types() {
		return array(
			'dma' => __('Domain name ASC',    'wplnst'),
			'dmd' => __('Domain name DESC',   'wplnst'),
			'dta' => __('Download time ASC',  'wplnst'),
			'dtd' => __('Download time DESC', 'wplnst'),
			'dsa' => __('Download size ASC',  'wplnst'),
			'dsd' => __('Download size DESC', 'wplnst'),
		);
	}



	// Selected types name
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Resolve Destination Type name from value
	 */
	public static function get_destination_type_name($value, $default = false) {
		return self::get_field_value_name(self::get_destination_types(), $value, $default);
	}



	/**
	 * Resolve Time Scope name from value
	 */
	public static function get_time_scope_name($value, $default = false) {
		return self::get_field_value_name(self::get_time_scopes(), $value, $default);
	}



	/**
	 * Resolve Links Types from values
	 */
	public static function get_link_types_names($value, $default = false) {
		return self::get_field_values_names(self::get_link_types(), $value, $default);
	}



	/**
	 * Resolve Crawl Order name from value
	 */
	public static function get_crawl_order_name($value, $default = false) {
		return self::get_field_value_name(self::get_crawl_order(), $value, $default);
	}



	/**
	 * Resolve post types from values
	 */
	public static function get_post_types_names($value, $default = false) {
		return self::get_field_values_names(self::get_post_types(), $value, $default);
	}



	/**
	 * Resolve post status from values
	 */
	public static function get_post_status_names($value, $default = false) {
		return self::get_field_values_names(self::get_post_status(), $value, $default);
	}



	/**
	 * Resolve Comments Types from values
	 */
	public static function get_comment_types_names($value, $default = false) {
		return self::get_field_values_names(self::get_comment_types(), $value, $default);
	}



	/**
	 * Resolve links level status
	 */
	public static function get_links_status_levels_names($value, $default = false) {
		return self::get_field_values_names(self::get_status_levels(), $value, $default);
	}



	/**
	 * Resolve links codes status
	 */
	public static function get_links_status_codes_names($value, $default = false) {
		return self::get_field_values_names(self::get_status_codes_raw(), $value, $default);
	}



	/**
	 * Resolve names, combined with levels and codes
	 */
	public static function get_links_status_names_combined($status_levels_values, $status_codes_values) {

		// Initialize
		$names = array();

		// Resolve levels
		$status_codes = self::get_status_codes();
		$status_levels = self::get_status_levels();
		foreach ($status_levels as $key => $level_name) {
			if (in_array($key, $status_levels_values)) {
				$names[] = $key.'00s '.$level_name;
			}
			if (isset($status_codes[$key])) {
				foreach ($status_codes[$key] as $code => $code_name) {
					if (in_array($code, $status_codes_values)) {
						$names[] = $code.' '.$code_name;
					}
				}
			}
		}

		// Donde
		return $names;
	}



	/**
	 * Resolve a name from key value
	 */
	public static function get_field_value_name($array, $value, $default = false) {

		// Check name value
		if (isset($array[$value])) {
			return $array[$value];
		}

		// Check default value
		if (false !== $default && isset($array[$default])) {
			return $array[$default];
		}

		// Nothing
		return '';
	}



	/**
	 * Resolve an array of names from an array of key values
	 */
	public static function get_field_values_names($array, $values, $default = false) {

		// Initialize
		$names = array();

		// Enum values
		foreach ($values as $key) {
			if (isset($array[$key])) {
				$names[] = $array[$key];
			}
		}

		// Check default for empty names
		if (empty($names) && false !== $default) {
			if (!is_array($default)) {
				if (isset($array[$default])) {
					$names[] = $array[$default];
				}
			} else {
				foreach ($default as $key) {
					if (isset($array[$key])) {
						$names[] = $array[$key];
					}
				}
			}
		}

		// Done
		return $names;
	}



	// Validation
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check submit POST value
	 */
	public static function check_post_value($param, $allowed, $default = null) {
		return (!empty($_POST) && is_array($_POST) && isset($_POST[$param]))? self::check_allowed_value($_POST[$param], $allowed, $default) : $default;
	}



	/**
	 * Check submitted elist and rearrange indexes
	 */
	public static function check_post_elist($param, $default = array()) {

		// Decode value
		$elist = isset($_POST[$param])? @json_decode(stripslashes($_POST[$param]), true) : false;

		// Check decoding
		if (empty($elist) || !is_array($elist)) {
			return $default;
		}

		// Arrange index
		$index = -1;
		foreach ($elist as &$value) {
			if (is_array($value)) {
				$value['index'] = ++$index;
			}
		}

		// Done
		return @json_encode($elist);
	}



	/**
	 * Safe retrieve array value
	 */
	public static function get_array_value($array, $param, $default = null) {
		return (!empty($array) && is_array($array) && isset($array[$param]))? $array[$param] : $default;
	}



	/**
	 * Check array value
	 */
	public static function check_array_value($array, $param, $allowed, $default = null) {
		return (!empty($array) && is_array($array) && isset($array[$param]))? self::check_allowed_value($array[$param], $allowed, $default) : $default;
	}



	/**
	 * Check numeric array value
	 */
	public static function check_array_numeric_value($array, $param, $default = 0) {
		return empty($array[$param])? $default : (int) $array[$param];
	}



	/**
	 * Check a json array value
	 */
	public static function check_array_json($array, $param, $default = array()) {
		$value = (!empty($array) && is_array($array) && isset($array[$param]))? @json_decode($array[$param], true) : array();
		return (empty($value) || !is_array($value))? $default : $value;
	}



	/**
	 * Check a value in array
	 */
	public static function check_allowed_value($test, $allowed, $default = null) {

		// Is an array
		if (is_array($allowed)) {

			// Initialize
			$value = $default;

			// Source value as an array
			if (is_array($test)) {
				$value = array();
				foreach ($test as $key => $name) {
					$sel = ('on' == $name)? $key : $name;
					if (in_array($sel, $allowed)) {
						$value[] = $sel;
					}
				}

			// Single value in allowed array
			} elseif (in_array($test, $allowed)) {
				$value = $test;
			}

		// Result value
		} else {

			// Comparison value
			$value = ($test === $allowed);
		}

		// Done
		return $value;
	}



}