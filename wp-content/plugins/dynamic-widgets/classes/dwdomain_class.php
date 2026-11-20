<?php
/**
 * DWDomain class - parsing and matching for the Domain module
 *
 * Kept free of WordPress calls so it can be unit tested (tests/test-domain-logic.php).
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DWDomain {

		/**
		 * DWDomain::parse() Turn the textarea value (one domain per line) into a list of domains
		 *
		 * @param string $raw
		 * @return array
		 */
		public static function parse($raw) {
			$domains = array();

			$lines = explode("\n", str_replace("\r", "", trim($raw)));
			foreach ( $lines as $line ) {
				$domain = strtolower(trim($line));
				if ( $domain !== '' ) {
					$domains[] = $domain;
				}
			}

			return $domains;
		}

		/**
		 * DWDomain::matches() Does the current host name match one of the listed domains?
		 * Case insensitive; a port in the host name (HTTP_HOST "example.com:8080") is ignored
		 * unless the listed domain contains that port itself.
		 *
		 * @param string|null $hostname
		 * @param array $domains
		 * @return boolean
		 */
		public static function matches($hostname, $domains) {
			if ( empty($hostname) || ! is_array($domains) ) {
				return FALSE;
			}

			$host = strtolower(trim($hostname));
			$host_no_port = preg_replace('/:\d+$/', '', $host);

			foreach ( $domains as $d ) {
				$d = strtolower(trim($d));
				if ( $d !== '' && ($d === $host || $d === $host_no_port) ) {
					return TRUE;
				}
			}

			return FALSE;
		}
	}
?>
