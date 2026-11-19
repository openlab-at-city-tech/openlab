<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains API specific helper methods.
 *
 * @since 1.0.0
 */
trait Api {
	/**
	 * Request the remote URL via wp_remote_post and return a json decoded response.
	 *
	 * @since 1.0.0
	 *
	 * @param  array       $body    The content to retrieve from the remote URL.
	 * @param  array       $headers The headers to send to the remote URL.
	 * @return string|null          JSON decoded response on success, false on failure.
	 */
	public function sendRequest( $url, $body = [], $headers = [] ) {
		$body = wp_json_encode( $body );

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			[
				'Content-Type' => 'application/json'
			]
		);

		// Setup variable for wp_remote_post.
		$requestArgs = [
			'headers' => $headers,
			'body'    => $body,
			'timeout' => 20
		];

		// Perform the query and retrieve the response.
		$response     = $this->wpRemotePost( $url, $requestArgs );
		$responseBody = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( ! $responseBody ) {
			return null;
		}

		// Return the json decoded content.
		return json_decode( $responseBody );
	}

	/**
	 * Sends a request using wp_remote_post.
	 *
	 * @since 1.0.0
	 *
	 * @param  string          $url  The URL to send the request to.
	 * @param  array           $args The args to use in the request.
	 * @return array|\WP_Error       The response as an array or WP_Error on failure.
	 */
	public function wpRemotePost( $url, $args = [] ) {
		return wp_remote_post( $url, array_replace_recursive( $this->getWpApiRequestDefaults(), $args ) );
	}

	/**
	 * Sends a request using wp_remote_get.
	 *
	 * @since 1.0.0
	 *
	 * @param  string          $url  The URL to send the request to.
	 * @param  array           $args The args to use in the request.
	 * @return array|\WP_Error       The response as an array or WP_Error on failure.
	 */
	public function wpRemoteGet( $url, $args = [] ) {
		return wp_remote_get( $url, array_replace_recursive( $this->getWpApiRequestDefaults(), $args ) );
	}

	/**
	 * Default arguments for wp_remote_get and wp_remote_post.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of default arguments for the request.
	 */
	private function getWpApiRequestDefaults() {
		return [
			'timeout'    => 10,
			'headers'    => aioseoTableOfContents()->helpers->getApiHeaders(),
			'user-agent' => aioseoTableOfContents()->helpers->getApiUserAgent()
		];
	}

	/**
	 * Returns the headers for internal API requests.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of headers.
	 */
	private function getApiHeaders() {
		return [];
	}

	/**
	 * Returns the User Agent for internal API requests.
	 *
	 * @since 1.0.0
	 *
	 * @return string The User Agent.
	 */
	private function getApiUserAgent() {
		return 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ) . '; AIOSEO/TableOfContents/' . AIOSEO_TABLE_OF_CONTENTS_VERSION;
	}
}