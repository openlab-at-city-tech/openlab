<?php

/**
 * Class SBI_HTTP_Request
 *
 * This class with make remote request
 *
 * @since 4.0
 */

namespace InstagramFeed;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class SBI_HTTP_Request
{
	public function __construct()
	{
	}

	/**
	 * Make the HTTP remote request
	 *
	 * @param string     $method
	 * @param string     $url
	 * @param array|null $data
	 *
	 * @return array|WP_Error
	 * @since 4.0
	 */
	public static function request($method, $url, $data = null)
	{
		$args = array(
			'headers' => array(
				'Content-Type' => 'application/json',
			),
		);

		$args = array_merge($args, $data);

		if ('GET' === $method) {
			$request = wp_safe_remote_get($url, $args);
		} elseif ('DELETE' === $method) {
			$args['method'] = 'DELETE';
			$request = wp_safe_remote_request($url, $args);
		} elseif ('PATCH' === $method) {
			$args['method'] = 'PATCH';
			$request = wp_safe_remote_request($url, $args);
		} elseif ('PUT' === $method) {
			$args['method'] = 'PUT';
			$request = wp_safe_remote_request($url, $args);
		} else {
			$args['method'] = 'POST';
			$request = wp_safe_remote_post($url, $args);
		}

		return $request;
	}

	/**
	 * Check if WP_Error returned
	 *
	 * @param array|WP_Error $request
	 *
	 * @return array|WP_Error
	 * @since 4.0
	 */
	public static function is_error($request)
	{
		return is_wp_error($request);
	}

	/**
	 * Get the remote call status code
	 *
	 * @param array|WP_Error $request
	 *
	 * @return array|WP_Error
	 * @since 4.0
	 */
	public static function status($request)
	{
		if (is_wp_error($request)) {
			return;
		}

		return wp_remote_retrieve_response_code($request);
	}

	/**
	 * Get the remote call body data
	 *
	 * @param array|WP_Error $request
	 *
	 * @return array $response
	 * @since 4.0
	 */
	public static function data($request)
	{
		$response = wp_remote_retrieve_body($request);
		return json_decode($response);
	}
}
