<?php
/**
 * Send API queries for a post to X.com
 *
 * @category Post from WordPress.
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.xposter.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Upload media to Twitter API.
 *
 * @param object   $connection Twitter connection.
 * @param int|bool $auth Connection context.
 * @param int      $attachment Attachment ID.
 * @param array    $status Array of posting information.
 * @param int      $id Post ID.
 *
 * @return array
 */
function wpt_upload_twitter_media( $connection, $auth, $attachment, $status, $id ) {
	$text = $status['text'];
	if ( $connection ) {
		if ( $attachment ) {
			$attachment_data = wpt_image_binary( $attachment );
			// Return early if fails to fetch image binary.
			if ( ! $attachment_data ) {
				return $status;
			}
			$media_info = $connection->uploadMedia()->upload( $attachment_data );
			$status     = array(
				'text'  => $text,
				'media' => array(
					'media_ids' => array(
						$media_info['media_id_string'],
					),
				),
			);
			// noweh/twitter-api-v2-php doesn't currently support metadata.
			$ct       = wpt_oauth_connection( $auth, '1.1' );
			$media_id = $ct->media(
				'https://upload.twitter.com/1.1/media/metadata/create.json',
				array(
					'auth'       => $auth,
					'media'      => $media_info['media_id_string'],
					'attachment' => $attachment,
				)
			);
			wpt_mail( 'Media Uploaded', "$auth, $media_id, $attachment", $id );
		}
	}
	return $status;
}

/**
 * Post status to Twitter.
 *
 * @param object $connection Connection to Twitter.
 * @param mixed  $auth Main site or specific author ID.
 * @param int    $id Post ID.
 * @param array  $status Array of information sent to Twitter.
 *
 * @return array
 */
function wpt_send_post_to_twitter( $connection, $auth, $id, $status ) {
	/**
	 * Turn on staging mode. Staging mode is automatically turned on if WPT_STAGING_MODE constant is defined.
	 *
	 * @hook wpt_staging_mode
	 * @param {bool}     $staging_mode True to enable staging mode.
	 * @param {int|bool} $auth Current author.
	 * @param {int}      $id Post ID.
	 *
	 * @return {bool}
	 */
	$staging_mode = apply_filters( 'wpt_staging_mode', false, $auth, $id );
	$notice       = '';
	if ( ( defined( 'WPT_STAGING_MODE' ) && true === WPT_STAGING_MODE ) || $staging_mode ) {
		// if in staging mode, we'll behave as if the Tweet succeeded, but not send it.
		$connection = true;
		$http_code  = 200;
		$notice     = __( 'In Staging Mode:', 'wp-to-twitter' ) . ' ' . $status['text'];
		$tweet_id   = false;
	} else {
		/**
		 * Filter the approval to send a Tweet.
		 *
		 * @hook wpt_do_tweet
		 * @param {bool}     $do_tweet Return false to cancel this Tweet.
		 * @param {int|bool} $auth Author.
		 * @param {int}      $id Post ID.
		 * @param {string}   $twit Tweet text.
		 *
		 * @return {bool}
		 */
		$do_tweet = apply_filters( 'wpt_do_tweet', true, $auth, $id, $status['text'] );
		$tweet_id = false;
		$success  = false;
		if ( $do_tweet ) {
			try {
				$return     = $connection->tweet()->create()->performRequest( $status, true );
				$http_code  = 200;
				$success    = true;
				$tweet_id   = $return->data->id;
				$headers    = $return->headers;
				$rate_limit = array(
					'rate-limit'    => $headers['x-rate-limit-remaining'],
					'rate-reset'    => $headers['x-rate-limit-reset'],
					'rate-24'       => $headers['x-app-limit-24hour-limit'],
					'rate-24-reset' => $headers['x-app-limit-24hour-reset'],
				);
				$notice     = __( 'Sent to X.com', 'wp-to-twitter' );
				update_option( 'wpt_app_limit', $rate_limit );
			} catch ( RequestException $e ) {
				// Get Guzzle exception response.
				if ( method_exists( $e, 'getResponse' ) ) {
					$response   = $e->getResponse();
					$headers    = $response->getHeaders();
					$rate_limit = array(
						'rate-limit'    => $headers['x-rate-limit-remaining'],
						'rate-reset'    => $headers['x-rate-limit-reset'],
						'rate-24'       => $headers['x-app-limit-24hour-limit'],
						'rate-24-reset' => $headers['x-app-limit-24hour-reset'],
					);
					update_option( 'wpt_app_limit', $rate_limit );
					$http_code = $response->getStatusCode();
					$notice    = __( 'Request Exception occurred when sending to X.com', 'wp-to-twitter' );
					wpt_mail( 'X RequestException', print_r( $response, 1 ), $id );
				}
			} catch ( Exception $e ) {
				if ( method_exists( $e, 'getMessage' ) ) {
					$error     = json_decode( $e->getMessage() );
					$http_code = $e->getCode();
					$notice    = $error->title . ': ' . $error->detail;
					wpt_mail( 'X Exception', print_r( $error, 1 ), $id );
				} else {
					$http_code = 405;
					$notice    = __( 'Unhandled response', 'wp-to-twitter' );
				}
			}
		} else {
			$http_code = '000';
			$notice    = __( 'XPost Canceled by custom filter.', 'wp-to-twitter' );
		}
	}

	return array(
		'return'   => $success,
		'http'     => $http_code,
		'notice'   => $notice,
		'tweet_id' => $tweet_id,
	);
}
