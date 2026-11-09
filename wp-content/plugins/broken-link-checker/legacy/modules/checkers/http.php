<?php

/*
Plugin Name: Basic HTTP
Description: Check all links that have the HTTP/HTTPS protocol.
Version: 1.0
Author: Janis Elsts

ModuleID: http
ModuleCategory: checker
ModuleContext: on-demand
ModuleLazyInit: true
ModuleClassName: blcHttpChecker
ModulePriority: -1
*/

require_once BLC_DIRECTORY_LEGACY . '/includes/token-bucket.php';

// TODO: Rewrite sub-classes as transports, not stand-alone checkers
class blcHttpChecker extends blcChecker {
	/* @var blcChecker */
	var $implementation = null;

	/** @var  blcTokenBucketList */
	private $token_bucket_list;

	function init() {
		parent::init();

		$conf                    = blc_get_configuration();
		$this->token_bucket_list = new blcTokenBucketList(
			$conf->get( 'http_throttle_rate', 3 ),
			$conf->get( 'http_throttle_period', 15 ),
			$conf->get( 'http_throttle_min_interval', 2 )
		);

		if ( apply_filters( 'wpmudev_blc_local_use_curl', function_exists( 'curl_init' ) || is_callable( 'curl_init' ) )  ) {
			$this->implementation = new blcCurlHttp(
				$this->module_id,
				$this->cached_header,
				$this->plugin_conf,
				$this->module_manager
			);
		} else {
			// try and use wp request method
			$this->implementation = new blcWPHttp(
				$this->module_id,
				$this->cached_header,
				$this->plugin_conf,
				$this->module_manager
			);
		}
	}

	function can_check( $url, $parsed ) {
		if ( isset( $this->implementation ) ) {
			return $this->implementation->can_check( $url, $parsed );
		} else {
			return false;
		}
	}

	function check( $url, $use_get = false ) {
		global $blclog;

		// Throttle requests based on the domain name.
		$domain = @parse_url( $url, PHP_URL_HOST );
		if ( $domain ) {
			$this->token_bucket_list->takeToken( $domain );
		}

		$blclog->debug( 'HTTP module checking "' . $url . '"' );
		return $this->implementation->check( $url, $use_get );
	}
}


/**
 * Base class for checkers that deal with HTTP(S) URLs.
 *
 * @package Broken Link Checker
 * @access public
 */
class blcHttpCheckerBase extends blcChecker {

	function clean_url( $url ) {
		$url = html_entity_decode( $url );

		$ltrm = preg_quote( json_decode( '"\u200E"' ), '/' );
		$url  = preg_replace(
			array(
				'/([\?&]PHPSESSID=\w+)$/i', // remove session ID
				'/(#[^\/]*)$/',             // and anchors/fragments
				'/&amp;/',                  // convert improper HTML entities
				'/([\?&]sid=\w+)$/i',       // remove another flavour of session ID
				'/' . $ltrm . '/',          // remove Left-to-Right marks that can show up when copying from Word.
			),
			array( '', '', '&', '', '' ),
			$url
		);
		$url  = trim( $url );

		return $url;
	}

	function is_error_code( $http_code ) {
		/*
		"Good" response codes are anything in the 2XX range (e.g "200 OK") and redirects  - the 3XX range.
		  HTTP 401 Unauthorized is a special case that is considered OK as well. Other errors - the 4XX range -
		  are treated as such. */
		$good_code = ( ( $http_code >= 200 ) && ( $http_code < 400 ) ) || ( 401 === $http_code );
		return ! $good_code;
	}

	/**
	 * This checker only accepts HTTP(s) links.
	 *
	 * @param string     $url
	 * @param array|bool $parsed
	 * @return bool
	 */
	function can_check( $url, $parsed ) {
		if ( ! isset( $parsed['scheme'] ) ) {
			return false;
		}

		return in_array( strtolower( $parsed['scheme'] ), array( 'http', 'https' ) );
	}

	/**
	 * Takes an URL and replaces spaces and some other non-alphanumeric characters with their urlencoded equivalents.
	 *
	 * @param string $url
	 * @return string
	 */
	function urlencodefix( $url ) {
		// TODO: Remove/fix this. Probably not a good idea to "fix" invalid URLs like that.
		return preg_replace_callback(
			'|[^a-z0-9\+\-\/\\#:.,;=?!&%@()$\|*~_]|i',
			function( $str ) {
				return rawurlencode( $str[0] );
			},
			$url
		);
	}

	/**
	 * IP ranges a link check must never be allowed to reach.
	 *
	 * wp_http_validate_url() performs a similar check, but the set of ranges it
	 * covers has grown over time: the IANA special-purpose ranges (link-local,
	 * CGNAT, TEST-NET, multicast, reserved) were added to WordPress core
	 * comparatively recently, while this plugin still supports considerably
	 * older versions. Delegating the whole boundary to core would therefore
	 * leave cloud metadata endpoints such as 169.254.169.254 reachable on the
	 * older versions we support, so the ranges are restated here and enforced
	 * by the plugin regardless of which core version happens to be running.
	 *
	 * @var string[]
	 */
	const BLOCKED_IP_RANGES = array(
		// IPv4.
		'0.0.0.0/8',        // "This network".
		'10.0.0.0/8',       // Private.
		'100.64.0.0/10',    // Carrier-grade NAT.
		'127.0.0.0/8',      // Loopback.
		'169.254.0.0/16',   // Link-local, includes the cloud metadata endpoints.
		'172.16.0.0/12',    // Private.
		'192.0.0.0/24',     // IETF protocol assignments.
		'192.0.2.0/24',     // TEST-NET-1.
		'192.88.99.0/24',   // 6to4 relay anycast.
		'192.168.0.0/16',   // Private.
		'198.18.0.0/15',    // Benchmarking.
		'198.51.100.0/24',  // TEST-NET-2.
		'203.0.113.0/24',   // TEST-NET-3.
		'224.0.0.0/4',      // Multicast.
		'240.0.0.0/4',      // Reserved, includes the 255.255.255.255 broadcast address.

		// IPv6.
		'::/128',           // Unspecified.
		'::1/128',          // Loopback.
		'::ffff:0:0/96',    // IPv4-mapped.
		'64:ff9b::/96',     // IPv4/IPv6 translation.
		'100::/64',         // Discard-only.
		'2001:db8::/32',    // Documentation.
		'fc00::/7',         // Unique local.
		'fe80::/10',        // Link-local.
		'ff00::/8',         // Multicast.
	);

	/**
	 * Determine whether an IP address falls inside a CIDR range.
	 *
	 * Compares the packed representations so that both address families are
	 * handled without the caller having to know which one it is looking at.
	 *
	 * @param string $ip   IP address in presentation format.
	 * @param string $cidr Range in CIDR notation.
	 * @return bool
	 */
	protected function ip_in_cidr( $ip, $cidr ) {
		if ( false === strpos( $cidr, '/' ) ) {
			return false;
		}

		list( $subnet, $prefix ) = explode( '/', $cidr, 2 );

		$packed_ip     = @inet_pton( $ip );
		$packed_subnet = @inet_pton( $subnet );

		// Differing lengths mean the two belong to different address families.
		if ( false === $packed_ip || false === $packed_subnet || strlen( $packed_ip ) !== strlen( $packed_subnet ) ) {
			return false;
		}

		$prefix = (int) $prefix;
		if ( $prefix < 0 || $prefix > strlen( $packed_ip ) * 8 ) {
			return false;
		}

		$whole_bytes    = intdiv( $prefix, 8 );
		$remaining_bits = $prefix % 8;

		if ( $whole_bytes > 0 && 0 !== strncmp( $packed_ip, $packed_subnet, $whole_bytes ) ) {
			return false;
		}

		if ( 0 === $remaining_bits ) {
			return true;
		}

		$mask = chr( ( 0xFF << ( 8 - $remaining_bits ) ) & 0xFF );

		return ( $packed_ip[ $whole_bytes ] & $mask ) === ( $packed_subnet[ $whole_bytes ] & $mask );
	}

	/**
	 * Determine whether an IP address is one the plugin must not connect to.
	 *
	 * @param string $ip IP address in presentation format.
	 * @return bool
	 */
	public function is_blocked_ip( $ip ) {
		// Anything that will not parse as an address is not something we can vouch for.
		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return true;
		}

		foreach ( self::BLOCKED_IP_RANGES as $range ) {
			if ( $this->ip_in_cidr( $ip, $range ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Resolve a host name to every address it currently answers with.
	 *
	 * Both address families are looked up. wp_http_validate_url() consults only
	 * gethostbyname(), which is IPv4-only, so a host answering with an innocuous
	 * A record and a loopback AAAA record would otherwise pass validation and
	 * then be reached over IPv6.
	 *
	 * @param string $host Host name, or an IP address literal.
	 * @return string[] Addresses in presentation format, empty when the host does not resolve.
	 */
	protected function resolve_host( $host ) {
		if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
			return array( $host );
		}

		$addresses = array();

		foreach ( $this->lookup_dns_records( $host ) as $record ) {
			if ( ! empty( $record['ip'] ) ) {
				$addresses[] = $record['ip'];
			} elseif ( ! empty( $record['ipv6'] ) ) {
				$addresses[] = $record['ipv6'];
			}
		}

		// Always add the answer the operating system gives as well, and not only
		// when the DNS lookup comes back empty. It is the lookup cURL and core
		// perform, so it can disagree with dns_get_record(): a resolver may answer
		// for a name the system reads as an address literal. Every address has to
		// be cleared, so the union can only make the guard stricter.
		$resolved = @gethostbyname( $host );
		if ( ! empty( $resolved ) && $resolved !== $host ) {
			$addresses[] = $resolved;
		}

		return array_values( array_unique( $addresses ) );
	}

	/**
	 * Look up a host's A and AAAA records.
	 *
	 * Kept apart from resolve_host() so a test can stand in for the resolver.
	 *
	 * @param string $host Host name.
	 * @return array[] DNS records, empty when the lookup fails or is unavailable.
	 */
	protected function lookup_dns_records( $host ) {
		$records = @dns_get_record( $host, DNS_A | DNS_AAAA );

		return is_array( $records ) ? $records : array();
	}

	/**
	 * Determine whether a host is spelled like an IPv4 address without being one.
	 *
	 * Octal (0177.0.0.1), hexadecimal (0x7f.0.0.1), decimal (2130706433) and short
	 * (127.1) forms do not parse as an address, yet cURL and the system resolver
	 * read them as one. Which address they mean is decided by whichever lookup
	 * answers, so they are refused outright and never resolved.
	 *
	 * @param string $host Host taken from the URL.
	 * @return bool
	 */
	protected function is_non_canonical_ip_host( $host ) {
		if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
			return false;
		}

		return (bool) preg_match( '/^(0x[0-9a-f]*|[0-9]+)(\.(0x[0-9a-f]*|[0-9]+)){0,3}$/i', $host );
	}

	/**
	 * Determine whether the plugin is allowed to request a URL.
	 *
	 * This has to be applied to every URL the checker is about to request,
	 * each redirect target included, and not only to the URL a check started
	 * with.
	 *
	 * @param string $url URL to test.
	 * @return bool
	 */
	public function is_safe_url( $url ) {
		if ( ! wp_http_validate_url( $url ) ) {
			return false;
		}

		$host = @parse_url( $url, PHP_URL_HOST );
		if ( empty( $host ) ) {
			return false;
		}

		$host = trim( $host, '.' );

		// Links pointing back at the site itself are legitimate even when the site
		// is served from a private address, which is the case on a great many
		// intranet and staging installs. Core makes the same exemption, and the
		// port allow-list still applies to these URLs.
		$home = @parse_url( get_option( 'home' ), PHP_URL_HOST );
		if ( ! empty( $home ) && strtolower( $home ) === strtolower( $host ) ) {
			return true;
		}

		if ( $this->is_non_canonical_ip_host( $host ) ) {
			return false;
		}

		$addresses = $this->resolve_host( $host );

		// A host we cannot resolve is a host we cannot clear.
		if ( empty( $addresses ) ) {
			return false;
		}

		foreach ( $addresses as $address ) {
			if ( $this->is_blocked_ip( $address ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Normalise a URL and clear it for a request.
	 *
	 * Every HTTP request the plugin makes has to come through here first - both
	 * the URL a check starts with and each redirect target it walks to. The order
	 * matters: clean_url() runs html_entity_decode(), so validating ahead of it
	 * would leave the string that was checked free to disagree with the string
	 * that is ultimately requested.
	 *
	 * @param string $url URL to prepare.
	 * @return string The URL to request, or an empty string when it must not be
	 *                requested at all.
	 */
	protected function prepare_url( $url ) {
		$url = $this->clean_url( $url );

		/*
		 * wp_http_validate_url() runs wp_kses_bad_protocol() itself and refuses
		 * any URL whose protocol had to be stripped, so a javascript:, file: or
		 * gopher: address comes back from it as false.
		 *
		 * Running the protocol filter here first would be worse than redundant.
		 * wp_kses_bad_protocol() strips the offending protocol rather than
		 * rejecting the string, so passing its output on would hand a schemeless
		 * URL - a different URL from the one that was supplied - to the guard
		 * below, which would then have nothing left to object to.
		 */
		$url = wp_http_validate_url( $url );

		if ( empty( $url ) || ! $this->is_safe_url( $url ) ) {
			return '';
		}

		return $url;
	}

}

class blcCurlHttp extends blcHttpCheckerBase {

	var $last_headers = '';

	function check( $url, $use_get = false ) {
		global $blclog;
		$blclog->info( __CLASS__ . ' Checking link', $url );

		$log                = '';
		$this->last_headers = '';
		$result             = array(
			'broken'  => false,
			'timeout' => false,
			'warning' => false,
		);

		/*
		 * Normalise first and validate afterwards, so the string that gets checked
		 * is the string that ultimately gets requested. clean_url() runs
		 * html_entity_decode(), so validating ahead of it would leave the two free
		 * to disagree about where the URL actually points.
		 */
		$url = $this->prepare_url( $url );

		if ( empty( $url ) ) {
			$blclog->error( __CLASS__ . ' Invalid URL:', $url );

			$result = array(
				'warning'     => true,
				'log'         => "Invalid URL.\nURL fails to pass validation for safe use in the HTTP API.",
				'status_text' => __( 'Invalid URL', 'broken-link-checker' ),
				'error_code'  => 'invalid_url',
				'status_code' => BLC_LINK_STATUS_WARNING,
			);

			return $result;
		}

		$blclog->debug( __CLASS__ . ' Clean URL:', $url );

		// Get the BLC configuration. It's used below to set the right timeout values and such.
		$conf = blc_get_configuration();

		$nobody = ! $use_get; // Whether to send a HEAD request (the default) or a GET request

		/*
		 * Walk the redirect chain here rather than handing it to cURL, so that every
		 * hop can be validated before it is requested. CURLOPT_FOLLOWLOCATION was
		 * used previously, which meant only the URL the check started with was ever
		 * validated: a Location header pointing at a loopback, private or link-local
		 * address was followed without question.
		 *
		 * Driving the chain ourselves also means redirects are now followed on hosts
		 * with open_basedir enabled, where cURL refuses to follow them at all.
		 */
		$max_redirects  = 10;
		$redirect_count = 0;
		$request_url    = $url;
		$final_url      = $url;
		$blocked_url    = '';

		// Durations are accumulated across every hop, because cURL used to report
		// the time for the whole chain and the timeout heuristic below is tuned
		// against that.
		$total_duration          = 0;
		$total_measured_duration = 0;

		while ( true ) {
			$response  = $this->perform_request( $request_url, $nobody, $conf, $use_get );
			$final_url = $request_url;
			$http_code = intval( $response['info']['http_code'] );

			$total_duration          += empty( $response['info']['total_time'] ) ? 0 : $response['info']['total_time'];
			$total_measured_duration += $response['measured_duration'];

			// Anything that is not a redirect is the end of the chain.
			if ( $http_code < 300 || $http_code >= 400 ) {
				break;
			}

			// With redirect following disabled, cURL resolves the target for us.
			$next_url = empty( $response['info']['redirect_url'] ) ? '' : $response['info']['redirect_url'];
			if ( '' === $next_url ) {
				break;
			}

			if ( $redirect_count >= $max_redirects ) {
				$log .= sprintf( "[Warning] Stopped following redirects after %d hops.\n", $max_redirects );
				break;
			}

			if ( ! $this->is_safe_url( $next_url ) ) {
				$blocked_url = $next_url;
				break;
			}

			$request_url = $next_url;
			++$redirect_count;
		}

		$content                   = $response['content'];
		$info                      = $response['info'];
		$measured_request_duration = $total_measured_duration;

		// Store the results
		$result['http_code']        = intval( $info['http_code'] );
		$result['final_url']        = $final_url;
		$result['request_duration'] = $total_duration;
		$result['redirect_count']   = $redirect_count;

		/*
		 * The chain ended at a target we are not allowed to request. Report the link
		 * as unverified instead of following it, and deliberately do not expose the
		 * blocked target: a non-zero redirect_count paired with a final_url is what
		 * the "Fix redirect" action rewrites post content to, and we have not
		 * established where this chain actually ends. Only the host is logged, and
		 * it is escaped at write time like the rest of the log.
		 */
		if ( '' !== $blocked_url ) {
			$blocked_host = @parse_url( $blocked_url, PHP_URL_HOST );

			$result['final_url']      = $url;
			$result['redirect_count'] = 0;
			$result['broken']         = false;
			$result['warning']        = true;
			$result['status_code']    = BLC_LINK_STATUS_WARNING;
			$result['status_text']    = __( 'Unsafe Redirect', 'broken-link-checker' );
			$result['error_code']     = 'unsafe_redirect';

			$log .= sprintf(
				/* translators: %s: host name of the redirect target that was refused. */
				__( 'Refused to follow a redirect to "%s" because it resolves to a private, loopback or otherwise reserved address.', 'broken-link-checker' ),
				esc_html( (string) $blocked_host )
			) . "\n";

			$blclog->error( __CLASS__ . ' Refused an unsafe redirect for:', $url );

			$result['log'] = $log;

			$result['result_hash'] = implode(
				'|',
				array(
					$result['http_code'],
					'0',
					'0',
					blcLink::remove_query_string( $result['final_url'] ),
				)
			);

			return $result;
		}

		// CURL doesn't return a request duration when a timeout happens, so we measure it ourselves.
		// It is useful to see how long the plugin waited for the server to respond before assuming it timed out.
		if ( empty( $result['request_duration'] ) ) {
			$result['request_duration'] = $measured_request_duration;
		}

		// Preserve the final destination's HTTP code for broken-link detection.
		// $result['http_code'] may be overridden below with the redirect code for display.
		$final_http_code = $result['http_code'];

		// If at least one redirect occurred, extract the first redirect status code
		// from the accumulated response headers so the Status column displays 301/302.
		if ( $result['redirect_count'] > 0 ) {
			if ( preg_match( '/^HTTP\/\S+\s+(\d+)/m', $this->last_headers, $matches ) ) {
				$redirect_code = intval( $matches[1] );
				if ( $redirect_code >= 300 && $redirect_code < 400 ) {
					$result['http_code'] = $redirect_code;
				}
			}
		}

		// Determine if the link counts as "broken"
		if ( 0 === absint( $final_http_code ) ) {
			$result['broken'] = true;

			$error_code = $response['errno'];
			$log       .= sprintf( "%s [Error #%d]\n", $response['error'], $error_code );

			// We only handle a couple of CURL error codes; most are highly esoteric.
			// libcurl "CURLE_" constants can't be used here because some of them have
			// different names or values in PHP.
			switch ( $error_code ) {
				case 6: // CURLE_COULDNT_RESOLVE_HOST
					$result['status_code'] = BLC_LINK_STATUS_WARNING;
					$result['status_text'] = __( 'Server Not Found', 'broken-link-checker' );
					$result['error_code']  = 'couldnt_resolve_host';
					break;

				case 28: // CURLE_OPERATION_TIMEDOUT
					$result['timeout'] = true;
					break;

				case 7: // CURLE_COULDNT_CONNECT
					// More often than not, this error code indicates that the connection attempt
					// timed out. This heuristic tries to distinguish between connections that fail
					// due to timeouts and those that fail due to other causes.
					if ( $result['request_duration'] >= 0.9 * $conf->options['timeout'] ) {
						$result['timeout'] = true;
					} else {
						$result['status_code'] = BLC_LINK_STATUS_WARNING;
						$result['status_text'] = __( 'Connection Failed', 'broken-link-checker' );
						$result['error_code']  = 'connection_failed';
					}
					break;

				default:
					$result['status_code'] = BLC_LINK_STATUS_WARNING;
					$result['status_text'] = __( 'Unknown Error', 'broken-link-checker' );
			}
		} elseif ( 999 === $final_http_code ) {
			$result['status_code'] = BLC_LINK_STATUS_WARNING;
			$result['status_text'] = __( 'Unknown Error', 'broken-link-checker' );
			$result['warning']     = true;
		} else {
			$result['broken'] = $this->is_error_code( $final_http_code );
		}

		$blclog->info(
			sprintf(
				'HTTP response: %d, duration: %.2f seconds, status text: "%s"',
				$result['http_code'],
				$result['request_duration'],
				isset( $result['status_text'] ) ? $result['status_text'] : 'N/A'
			)
		);

		$use_get = apply_filters( 'blc_use_get_checker', false, $result );

		if ( $nobody && ! $result['timeout'] && ! $use_get && ( $result['broken'] || $result['redirect_count'] == 1 ) ) {
			// The site in question might be expecting GET instead of HEAD, so lets retry the request
			// using the GET verb...but not in cases of timeout, or where we've already done it.
			return $this->check( $url, true );

			// Note : normally a server that doesn't allow HEAD requests on a specific resource *should*
			// return "405 Method Not Allowed". Unfortunately, there are sites that return 404 or
			// another, even more general, error code instead. So just checking for 405 wouldn't be enough.
		}

		// A redirect we did not follow - one with no usable Location header, or one
		// that hit the hop limit - still reads as a redirect in the Status column.
		if ( ( 0 === absint( $result['redirect_count'] ) ) && ( in_array( $result['http_code'], array( 301, 302, 303, 307 ) ) ) ) {
			$result['redirect_count'] = 1;
		}

		// Build the log from HTTP code and headers.
		$log .= '=== ';
		if ( $result['http_code'] ) {
			$log .= sprintf( __( 'HTTP code : %d', 'broken-link-checker' ), $result['http_code'] );
		} else {
			$log .= __( '(No response)', 'broken-link-checker' );
		}
		$log .= " ===\n\n";

		$log .= "Response headers\n" . str_repeat( '=', 16 ) . "\n";
		$log .= htmlentities( $this->last_headers );

		if ( isset( $info['request_header'] ) ) {
			$log .= "Request headers\n" . str_repeat( '=', 16 ) . "\n";
			$log .= htmlentities( $info['request_header'] );
		}

		if ( ! $nobody && ( false !== $content ) && $result['broken'] ) {
			$log .= "Response HTML\n" . str_repeat( '=', 16 ) . "\n";
			$log .= htmlentities( substr( $content, 0, 2048 ) );
		}

		if ( ! empty( $result['broken'] ) && ! empty( $result['timeout'] ) ) {
			$log .= "\n(" . __( "Most likely the connection timed out or the domain doesn't exist.", 'broken-link-checker' ) . ')';
		}

		$result['log'] = $log;

		// The hash should contain info about all pieces of data that pertain to determining if the
		// link is working.
		$result['result_hash'] = implode(
			'|',
			array(
				$result['http_code'],
				! empty( $result['broken'] ) ? 'broken' : '0',
				! empty( $result['timeout'] ) ? 'timeout' : '0',
				blcLink::remove_query_string( $result['final_url'] ),
			)
		);

		return $result;
	}

	/**
	 * Pin the transport options that must not be negotiable.
	 *
	 * These are applied after the 'broken-link-checker-curl-options' filter on
	 * purpose. That filter is public, and anything switching redirect following
	 * back on through it would hand the redirect chain to cURL again and quietly
	 * undo the per-hop validation check() performs.
	 *
	 * @param resource|\CurlHandle $ch cURL handle.
	 */
	protected function apply_transport_restrictions( $ch ) {
		// check() walks the redirect chain itself, clearing every hop before it is
		// requested. cURL must never follow one on its own.
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 0 );

		/*
		 * Hold the transport to the two schemes this checker claims to handle.
		 * CURLOPT_PROTOCOLS is deprecated in newer libcurl builds in favour of the
		 * string form, so use whichever one the running version provides.
		 */
		if ( defined( 'CURLOPT_PROTOCOLS_STR' ) ) {
			curl_setopt( $ch, CURLOPT_PROTOCOLS_STR, 'http,https' );
		} elseif ( defined( 'CURLPROTO_HTTP' ) && defined( 'CURLPROTO_HTTPS' ) ) {
			curl_setopt( $ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS );
		}
	}

	/**
	 * Perform a single HTTP request, without following redirects.
	 *
	 * Redirect following is deliberately left off. check() drives the redirect
	 * chain itself so every hop can be validated before it is requested, which
	 * is what stops an attacker-supplied Location header from reaching hosts the
	 * plugin is not allowed to talk to.
	 *
	 * @param string $url     URL to request. Must already have been cleared by is_safe_url().
	 * @param bool   $nobody  Whether to send a HEAD request rather than a GET.
	 * @param object $conf    Plugin configuration.
	 * @param bool   $use_get Whether this is the GET retry. Passed to the user agent filter.
	 * @return array {
	 *     @type string|false $content           Response body, or false when the request failed.
	 *     @type array        $info              curl_getinfo() output.
	 *     @type int          $errno             cURL error number.
	 *     @type string       $error             cURL error message.
	 *     @type float        $measured_duration Wall-clock duration of the request.
	 * }
	 */
	protected function perform_request( $url, $nobody, $conf, $use_get ) {
		// Init curl.
		$ch              = curl_init();
		$request_headers = array();
		curl_setopt( $ch, CURLOPT_URL, $this->urlencodefix( $url ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		// Masquerade as a recent version of Chrome
		//$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.34 Safari/537.36';
		// Use a custom user agent string.
		$ua = apply_filters( 'wpmudev_blc_local_ua', 'WPMU DEV Broken Link Checker Local Engine', $url, $use_get );
		curl_setopt( $ch, CURLOPT_USERAGENT, $ua );

		// Close the connection after the request (disables keep-alive). The plugin rate-limits requests,
		// so it's likely we'd overrun the keep-alive timeout anyway.
		curl_setopt( $ch, CURLOPT_FORBID_REUSE, true );
		$request_headers[] = 'Connection: close';

		// Add a semi-plausible referer header to avoid tripping up some bot traps
		curl_setopt( $ch, CURLOPT_REFERER, home_url() );

		// Set the timeout
		curl_setopt( $ch, CURLOPT_TIMEOUT, $conf->options['timeout'] );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $conf->options['timeout'] );

		// Set the proxy configuration. The user can provide this in wp-config.php
		if ( defined( 'WP_PROXY_HOST' ) ) {
			curl_setopt( $ch, CURLOPT_PROXY, WP_PROXY_HOST );
		}
		if ( defined( 'WP_PROXY_PORT' ) ) {
			curl_setopt( $ch, CURLOPT_PROXYPORT, WP_PROXY_PORT );
		}
		if ( defined( 'WP_PROXY_USERNAME' ) ) {
			$auth = WP_PROXY_USERNAME;
			if ( defined( 'WP_PROXY_PASSWORD' ) ) {
				$auth .= ':' . WP_PROXY_PASSWORD;
			}
			curl_setopt( $ch, CURLOPT_PROXYUSERPWD, $auth );
		}

		// Make CURL return a valid result even if it gets a 404 or other error.
		curl_setopt( $ch, CURLOPT_FAILONERROR, false );

		$parts = @parse_url( $url );
		if ( ! empty( $parts['scheme'] ) && 'https' === $parts['scheme'] ) {
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // Required to make HTTPS URLs work.
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		}

		if ( $nobody ) {
			// If possible, use HEAD requests for speed.
			curl_setopt( $ch, CURLOPT_NOBODY, true );
		} else {
			// If we must use GET at least limit the amount of downloaded data.
			$request_headers[] = 'Range: bytes=0-2048'; // 2 KB
		}

		// Set request headers.
		if ( ! empty( $request_headers ) ) {
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $request_headers );
		}

		// Register a callback function which will process the HTTP header(s).
		curl_setopt( $ch, CURLOPT_HEADERFUNCTION, array( $this, 'read_header' ) );

		// Record request headers.
		if ( defined( 'CURLINFO_HEADER_OUT' ) ) {
			curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
		}

		if (  apply_filters( 'wpmudev_blc_local_accept_encoding_header', true ) ) {
			curl_setopt( $ch, CURLOPT_ENCODING, '' );
		}

		// Apply filter for additional options
		curl_setopt_array( $ch, apply_filters( 'broken-link-checker-curl-options', array() ) );

		// Applied last, after the public filter above, so the boundary cannot be
		// negotiated away by third-party code.
		$this->apply_transport_restrictions( $ch );

		// Execute the request
		$start_time                = microtime_float();
		$content                   = curl_exec( $ch );
		$measured_request_duration = microtime_float() - $start_time;

		$info  = curl_getinfo( $ch );
		$errno = curl_errno( $ch );
		$error = curl_error( $ch );

		// Apply filter before curl closes. This now runs once per hop rather than
		// once per check, because each hop is a request in its own right.
		apply_filters( 'broken-link-checker-curl-before-close', $ch, $content, $this->last_headers );

		curl_close( $ch );

		return array(
			'content'           => $content,
			'info'              => $info,
			'errno'             => $errno,
			'error'             => $error,
			'measured_duration' => $measured_request_duration,
		);
	}

	function read_header( /** @noinspection PhpUnusedParameterInspection */ $ch, $header ) {
		$this->last_headers .= $header;
		return strlen( $header );
	}

}

class blcWPHttp extends blcHttpCheckerBase {

	/**
	 * Check a URL with the WordPress HTTP API.
	 *
	 * This is the fallback transport, used when cURL is unavailable or has been
	 * switched off. It has to hold the same destination boundary as the cURL
	 * checker: every address it is about to request, redirect targets included,
	 * is cleared by is_safe_url() first.
	 *
	 * wp_http_validate_url() is not a boundary worth delegating to. The set of
	 * ranges it rejects has been extended across releases and backports, so what
	 * it catches depends on the WordPress version in use, and it resolves only a
	 * host's IPv4 addresses. On a version whose core does not reject the
	 * link-local range, this path requested the cloud metadata endpoint
	 * (169.254.169.254) and recorded the result.
	 *
	 * @param string $url URL to check.
	 * @return array
	 */
	function check( $url ) {

		// Note : Snoopy doesn't work too well with HTTPS URLs.

		$result = array(
			'broken'  => false,
			'timeout' => false,
		);
		$log    = '';

		// Get the timeout setting from the BLC configuration.
		$conf    = blc_get_configuration();
		$timeout = $conf->options['timeout'];

		global $blclog;

		/*
		 * Normalise first and validate afterwards, so the string that gets
		 * checked is the string that ultimately gets requested. clean_url() runs
		 * html_entity_decode(), so validating ahead of it would leave the two
		 * free to disagree about where the URL actually points.
		 */
		$url = $this->prepare_url( $url );

		if ( empty( $url ) ) {
			$blclog->error( __CLASS__ . ' Invalid URL:', $url );

			$result = array(
				'warning'     => true,
				'log'         => "Invalid URL.\nURL fails to pass validation for safe use in the HTTP API.",
				'status_text' => __( 'Invalid URL', 'broken-link-checker' ),
				'error_code'  => 'invalid_url',
				'status_code' => BLC_LINK_STATUS_WARNING,
			);

			return $result;
		}

		$start_time = microtime_float();

		// Fetch the URL with WP_Http. Try HEAD first to avoid downloading the body.
		$request_args = array(
			'timeout'     => $timeout,
			'user-agent'  => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)', // masquerade as IE 7
			'aa'          => 1024 * 5,
			/*
			 * Walk the redirect chain here rather than letting the HTTP API follow
			 * it, so every hop can be cleared before it is requested. With core
			 * following redirects, only the URL the check started with was ever
			 * validated: a Location header pointing at a loopback, private or
			 * link-local address was followed without question. Note that core only
			 * disables following for the HEAD request itself - the GET retry below
			 * would otherwise follow the chain on a checker's behalf.
			 */
			'redirection' => 0,
		);

		$max_redirects  = 10;
		$redirect_count = 0;
		$request_url    = $url;
		$final_url      = $url;
		$blocked_url    = '';
		$request        = null;
		$http_code      = 0;

		while ( true ) {
			$request   = wp_safe_remote_head( $this->urlencodefix( $request_url ), $request_args );
			$http_code = is_wp_error( $request ) ? 0 : (int) wp_remote_retrieve_response_code( $request );

			// Fall back to GET if the server rejected HEAD (405 Method Not Allowed) or returned nothing.
			if ( in_array( $http_code, array( 0, 405 ), true ) ) {
				$request   = wp_safe_remote_get( $this->urlencodefix( $request_url ), $request_args );
				$http_code = is_wp_error( $request ) ? 0 : (int) wp_remote_retrieve_response_code( $request );
			}

			$final_url = $request_url;

			// Anything that is not a redirect is the end of the chain.
			if ( $http_code < 300 || $http_code >= 400 ) {
				break;
			}

			$location = is_wp_error( $request ) ? '' : wp_remote_retrieve_header( $request, 'location' );
			if ( is_array( $location ) ) {
				// A server sending more than one Location header does not get to
				// pick which one is followed; take the first, as browsers do.
				$location = reset( $location );
			}
			$location = (string) $location;

			if ( '' === $location ) {
				break;
			}

			if ( $redirect_count >= $max_redirects ) {
				$log .= sprintf( "[Warning] Stopped following redirects after %d hops.\n", $max_redirects );
				break;
			}

			$next_url = WP_Http::make_absolute_url( $location, $request_url );

			// The same guard the starting URL went through, applied to every hop.
			if ( ! $this->is_safe_url( $next_url ) ) {
				$blocked_url = $next_url;
				break;
			}

			$request_url = $next_url;
			++$redirect_count;
		}

		// request timeout results in WP ERROR
		if ( is_wp_error( $request ) ) {
			$result['http_code'] = 0;
			$result['timeout']   = true;
			$result['message']   = $request->get_error_message();
		} else {
			$result['http_code'] = wp_remote_retrieve_response_code( $request ); // HTTP status code
			$result['message']   = wp_remote_retrieve_response_message( $request );
		}

		/*
		 * The chain ended at a target we are not allowed to request. Report the
		 * link as unverified instead of following it, and deliberately do not
		 * expose the blocked target: a non-zero redirect_count paired with a
		 * final_url is what the "Fix redirect" action rewrites post content to,
		 * and we have not established where this chain actually ends. Only the
		 * host is logged, and it is escaped at write time like the rest of the
		 * log.
		 */
		if ( '' !== $blocked_url ) {
			$blocked_host = @parse_url( $blocked_url, PHP_URL_HOST );

			$result['final_url']      = $url;
			$result['redirect_count'] = 0;
			$result['broken']         = false;
			$result['timeout']        = false;
			$result['warning']        = true;
			$result['status_code']    = BLC_LINK_STATUS_WARNING;
			$result['status_text']    = __( 'Unsafe Redirect', 'broken-link-checker' );
			$result['error_code']     = 'unsafe_redirect';

			$log .= sprintf(
				/* translators: %s: host name of the redirect target that was refused. */
				__( 'Refused to follow a redirect to "%s" because it resolves to a private, loopback or otherwise reserved address.', 'broken-link-checker' ),
				esc_html( (string) $blocked_host )
			) . "\n";

			$blclog->error( __CLASS__ . ' Refused an unsafe redirect for:', $url );

			$result['log'] = $log;

			$result['result_hash'] = implode(
				'|',
				array(
					$result['http_code'],
					'0',
					'0',
					blcLink::remove_query_string( $result['final_url'] ),
				)
			);

			return $result;
		}

		// Build the log
		$log .= '=== ';
		if ( $result['http_code'] ) {
			$log .= sprintf( __( 'HTTP code : %d', 'broken-link-checker' ), $result['http_code'] );
		} else {
			$log .= __( '(No response)', 'broken-link-checker' );
		}
		$log .= " ===\n\n";

		if ( $result['message'] ) {
			$log .= esc_html( $result['message'] ) . "\n";
		}

		if ( is_wp_error( $request ) ) {
			$log              .= __( 'Request timed out.', 'broken-link-checker' ) . "\n";
			$result['timeout'] = true;
		}

		// Determine if the link counts as "broken"
		$result['broken'] = $this->is_error_code( $result['http_code'] ) || $result['timeout'];

		$log          .= '<em>(' . __( 'Using WP HTTP', 'broken-link-checker' ) . ')</em>';
		$result['log'] = $log;

		/*
		 * A redirect we did not follow - one with no usable Location header, or one
		 * that hit the hop limit - still reads as a redirect in the Status column,
		 * the same way the cURL checker treats that case.
		 */
		if ( ( 0 === absint( $redirect_count ) ) && ( in_array( $result['http_code'], array( 301, 302, 303, 307 ) ) ) ) {
			$redirect_count = 1;
		}

		$result['final_url']      = $final_url;
		$result['redirect_count'] = $redirect_count;

		// The hash should contain info about all pieces of data that pertain to determining if the
		// link is working.
		$result['result_hash'] = implode(
			'|',
			array(
				$result['http_code'],
				$result['broken'] ? 'broken' : '0',
				$result['timeout'] ? 'timeout' : '0',
				blcLink::remove_query_string( $result['final_url'] ),
			)
		);

		return $result;
	}

}
