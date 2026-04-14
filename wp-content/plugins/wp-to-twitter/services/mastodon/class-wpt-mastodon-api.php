<?php
/**
 * Mastodon access class.
 *
 * @category OAuth
 * @package  XPoster
 * @author   https://github.com/Eleirbag89, documented and adapted to WP code style.
 * @license  GPLv3
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * OAuth / Wpt_Mastodon_Api
 *
 * A simple library to send status updates to Mastodon instances.
 *
 * @author eleirbag89
 * @version 0.1
 * @link https://github.com/Eleirbag89/MastodonBotPHP
 */
class Wpt_Mastodon_Api {
	/**
	 * Access token for Mastodon instance.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * URL for instance root.
	 *
	 * @var string
	 */
	private $instance_url;

	/**
	 * Construct.
	 *
	 * @param string $token Access token for Mastodon instance.
	 * @param string $instance_url URL to instance root.
	 */
	public function __construct( $token, $instance_url ) {
		$this->token        = $token;
		$this->instance_url = $instance_url;
	}

	/**
	 * Post a status to the mastodon status endpoint.
	 *
	 * @param array $status Array posted to Mastodon. [status,visibility,language,media_ids="[]"].
	 *
	 * @return array Mastodon response.
	 */
	public function post_status( $status ) {
		return $this->call_api( '/api/v1/statuses', 'POST', $status );
	}

	/**
	 * Post a media attachment to the mastodon status endpoint.
	 *
	 * @param array $media Array of media data posted to Mastodon. [file,description].
	 *
	 * @return array Mastodon response.
	 */
	public function upload_media( $media ) {
		return $this->call_api( '/api/v1/media', 'POST', $media );
	}

	/**
	 * Verify account credentials
	 *
	 * @return array Mastodon response.
	 */
	public function verify() {
		return $this->call_api( '/api/v1/accounts/verify_credentials', 'GET', array() );
	}

	/**
	 * Post to the API endpoint.
	 *
	 * @param string $endpoint REST API path.
	 * @param string $method query method. GET, POST, etc.
	 * @param array  $data Data being posted.
	 *
	 * @return array Mastodon response or error.
	 */
	public function call_api( $endpoint, $method, $data ) {
		$content_type_boundary = '';
		$body                  = '';
		if ( '/api/v1/media' === $endpoint ) {
			$boundary = md5( time() );
			$eol      = "\r\n";
			$filedata = $data['file'];
			$name     = $filedata['name'];
			$file     = $filedata['file'];
			$mime     = $filedata['mime'];
			$alt      = $data['description'];

			$body = '--' . $boundary . $eol;
			if ( $alt ) {
				$body .= 'Content-Disposition: form-data; name="description";' . $eol . $eol;
				$body .= $alt . $eol;
				$body .= '--' . $boundary . $eol;
			}
			$body .= 'Content-Disposition: form-data; name="file"; filename="' . $name . '"' . $eol;
			$body .= 'Content-Type: ' . $mime . $eol . $eol;
			$body .= $file . $eol;
			$body .= '--' . $boundary . '--';

			$data                  = $body;
			$content_type_boundary = "; boundary=$boundary";
		}
		$headers = array(
			'Authorization' => 'Bearer ' . $this->token,
			'Content-Type'  => 'multipart/form-data' . $content_type_boundary,
		);

		$reply = wp_remote_post(
			$this->instance_url . $endpoint,
			array(
				'method'  => $method,
				'headers' => $headers,
				'body'    => $data,
			)
		);

		if ( is_wp_error( $reply ) ) {
			$error = array(
				'ok'         => false,
				'error_code' => $reply->get_error_code(),
				'error'      => $reply->get_error_message(),
			);
			return $error;
		}

		return json_decode( wp_remote_retrieve_body( $reply ), true );
	}
}
