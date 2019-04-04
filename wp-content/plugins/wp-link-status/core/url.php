<?php

/**
 * URL class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_URL {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Current Home URL
	 */
	public $home_url;



	/**
	 * Current site host and host URL
	 */
	public $host;
	public $host_url;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct() {

		// Blog home URL
		$this->home_url = rtrim(home_url(), '/');

		// Paranoid mode
		if (false !== ($pos = strpos($this->home_url, '#'))) {
			$this->home_url = mb_substr($this->home_url, 0, $pos);
		}

		// Obtains host URL
		$home_parts = @parse_url($this->home_url);
		if (false !== $home_parts && isset($home_parts['host']) && '' !== $home_parts['host']) {

			// Site host
			$this->host = $home_parts['host'];

			// Check scheme
			$scheme = 'http';
			if (!empty($home_parts['scheme']) && in_array(strtolower($home_parts['scheme']), array('http', 'https'))) {
				$scheme = $home_parts['scheme'];
			}

			// Define host URL
			$this->host_url = $scheme.'://'.$this->host;
		}
	}



	// URL utilities
	// ---------------------------------------------------------------------------------------------------



	/**
	 * URL analysis and fragmentation
	 */
	public function parse($value, $parent_url = false) {

		// Initialize
		$raw = $value;
		$url = trim($raw);
		$fragment = '';
		$absolute = false;
		$relative = false;
		$protorel = false;
		$malformed = false;

		// Extract fragment
		$test_url = $url;
		if (false !== ($pos = strpos($test_url, '#'))) {
			$url = mb_substr($test_url, 0, $pos);
			$fragment = mb_substr($test_url, $pos);
		}

		// Check if begins with double slash
		if ('//' == mb_substr($url, 0, 2)) {

			// Initialize
			$protorel = true;
			$malformed = true;

			// Set full URL
			$full_url = ((0 === stripos($this->home_url, 'https'))? 'https' : 'http').':'.$url;

			// Check scheme and host
			if (false !== ($parts = @parse_url($full_url)) && !empty($parts['scheme']) && !empty($parts['host'])) {

				// No malformed
				$malformed = false;

				// Remove possible fragment
				$parts['fragment'] = null;

				// Final URL
				$url = $this->unparse_url($parts);
			}

		// Check if begins with a single slash (absolute relative)
		} elseif ('/' == mb_substr($url, 0, 1)) {

			// By default
			$malformed = true;

			// Execute parent URL filter
			$parent_url = apply_filters('wplnst_relative_parent_url', $parent_url);

			// Check parent
			if (false !== $parent_url && !is_array($parent_url)) {

				// Check parent URL
				$parent_parts = @parse_url($parent_url);
				if (false !== $parent_parts && isset($parent_parts['host']) && '' !== $parent_parts['host']) {

					// Check scheme
					$scheme = 'http';
					if (!empty($parent_parts['scheme']) && in_array(strtolower($parent_parts['scheme']), array('http', 'https'))) {
						$scheme = $parent_parts['scheme'];
					}

					// Compose full URL
					$url = $scheme.'://'.trim($parent_parts['host'], '/').'/'.ltrim($url, '/');

					// No malfomed
					$malformed = false;

					// Is absolute
					$absolute = true;

					// Extract parts again
					$parts = @parse_url($url);
				}

			// This site
			} else {

				// Set full URL
				$home_url = isset($this->host_url)? $this->host_url : $this->home_url;
				$full_url = rtrim($home_url, '/').$url;

				// Check scheme and host
				if (false !== ($parts = @parse_url($full_url)) && !empty($parts['scheme']) && !empty($parts['host'])) {

					// No malfomed
					$malformed = false;

					// Is absolute
					$absolute = true;

					// Remove possible fragment
					$parts['fragment'] = null;

					// Final URL
					$url = $this->unparse_url($parts);
				}
			}

		// Other
		} else {

			// Split parts
			$parts = @parse_url($url);
			if (false === $parts) {

				// Malformed URL
				$malformed = true;

			// First relative check
			} elseif (empty($parts['scheme'])) {

				// Relative URL
				$relative = true;

				// Execute parent URL filter
				$parent_url = apply_filters('wplnst_relative_parent_url', $parent_url);

				// Try to compose
				if (false !== $parent_url && !is_array($parent_url)) {

					// Absolutize
					$url = $this->absolutize($url, $parent_url);

					// Extract parts again
					$parts = @parse_url($url);

				// This host
				} else {

					// Absolutize
					$url = $this->absolutize($url, $this->home_url);

					// Extract parts again
					$parts = @parse_url($url);
				}
			}
		}

		// Check scheme, host, path and query
		$scheme = isset($parts['scheme'])? $parts['scheme'] : null;
		$host   = isset($parts['host'])?   $parts['host']   : null;
		$path   = isset($parts['path'])?   $parts['path']   : null;
		$query  = isset($parts['query'])?  $parts['query']  : null;

		// Check link scope
		$scope = (isset($host) && isset($scheme))? $this->get_scope($host, $scheme) : null;

		// Check link fragment
		$fragment = isset($fragment)? $fragment : (isset($parts['fragment'])? $parts['fragment'] : null);

		// Done
		return array(
			'url' 		=> $url,
			'raw' 		=> $raw,
			'scheme'	=> $scheme,
			'host'		=> $host,
			'path'		=> $path,
			'query'		=> $query,
			'fragment'	=> $fragment,
			'scope'		=> $scope,
			'spaced'	=> (trim($raw) != $raw),
			'malformed' => $malformed,
			'absolute' 	=> $absolute,
			'protorel'  => $protorel,
			'relative'	=> $relative,
			'nofollow'	=> false,
		);
	}



	/**
	 * Unparse a parsed URL
	 * http://php.net/manual/es/function.parse-url.php#106731
	 */
	public function unparse_url($parsed_url) {
		$scheme   = isset($parsed_url['scheme'])? 	$parsed_url['scheme'].'://' : '';
		$host     = isset($parsed_url['host'])? 	$parsed_url['host'] 		: '';
		$port     = isset($parsed_url['port'])? 	':'.$parsed_url['port'] 	: '';
		$user     = isset($parsed_url['user'])? 	$parsed_url['user'] 		: '';
		$pass     = isset($parsed_url['pass'])? 	':'.$parsed_url['pass'] 	: '';
		$pass     = ($user || $pass)? 				"$pass@" 					: '';
		$path     = isset($parsed_url['path'])? 	$parsed_url['path'] 		: '';
		$query    = isset($parsed_url['query'])? 	'?'.$parsed_url['query'] 	: '';
		$fragment = (isset($parsed_url['fragment']) && '' != $parsed_url['fragment'])? '#'.ltrim($parsed_url['fragment'], '#') : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}



	/**
	 * Absolutize relative URL from a given permalink
	 */
	public function absolutize($relative_url, $permalink) {

		// Parse permalink
		$permalink_parts = @parse_url($permalink);
		if (false === $permalink_parts) {
			return false;
		}

		// Relative with arguments
		if ('?' == mb_substr($relative_url, 0, 1)) {

			// Remove permalink fragment
			if (($pos = strpos($permalink, '#')) > 0) {
				$permalink = mb_substr($permalink, 0, $pos);
			}

			// Concatenation
			return $permalink.$relative_url;

		// Explode
		} else {

			// Initialize
			$path = array();
			$base = empty($permalink_parts['path'])? '/' : $permalink_parts['path'];

			// Check last file
			if ('/' != mb_substr($base, -1)) {
				$base = dirname($base);
			}

			// URL parts
			$parts = explode('/', $relative_url);
			foreach ($parts as $part) {
				if (empty($path) && '..' == $part) {
					$base = dirname($base);
				} elseif ('' !== $part && '.' != $part) {
					$path[] = $part;
				}
			}

			// Compose new path
			$permalink_parts['path'] = rtrim($base, '/').'/'.implode('/', $path).(('/' == mb_substr($relative_url, -1))? '/' : '');

			// Remove permalink parts fragment
			$permalink_parts['fragment'] = null;

			// Done
			return $this->unparse_url($permalink_parts);
		}
	}



	/**
	 * Extract attributes, original RegExp: (\S+)=["']?((?:.(?!["']?\s+(?:\S+)=|[>"']))+.)["']?
	 * http://stackoverflow.com/questions/317053/regular-expression-for-extracting-tag-attributes
	 */
	public function extract_attributes($tag) {
		$attr = array();
		if (preg_match_all('/(\S+)=["'."'".']?((?:.(?!["'."'".']?\s+(?:\S+)=|[>"'."'".']))+.)["'."'".']?/', $tag, $matches, PREG_SET_ORDER) > 0) {
			foreach ($matches as $match) {
				$attr[strtolower($match[1])] = $match[2];
			}
		}
		return $attr;
	}



	/**
	 * Check if URL is allowed for request
	 */
	public function is_crawleable($urlinfo) {
		return ('' !== $urlinfo['url'] && false !== $urlinfo['url'] && !$urlinfo['malformed'] && isset($urlinfo['scheme']) && in_array($urlinfo['scheme'], array('http', 'https')) && isset($urlinfo['host']));
	}



	/**
	 * Check host and scheme and compare from blog host
	 */
	public function get_scope($host, $scheme) {

		// Check scheme
		if (isset($this->host) && in_array(strtolower($scheme), array('http', 'https', 'ftp'))) {
			$test_host = (0 === stripos($host, 'www.'))? mb_substr($host, 4) : $host;
			$test_this_host = (0 === stripos($this->host, 'www.'))? mb_substr($this->host, 4) : $this->host;
			return (strtolower($test_host) == strtolower($test_this_host))? 'internal' : 'external';
		}

		// No valid
		return null;
	}



}