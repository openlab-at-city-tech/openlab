<?php
/**
 * Send API queries for a post to Bluesky.
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

require_once plugin_dir_path( __FILE__ ) . 'class-wpt-bluesky-api.php';

/**
 * Upload media to Bluesky API.
 *
 * @param object   $connection Bluesky connection.
 * @param int|bool $auth Connection context.
 * @param int      $attachment Attachment ID.
 * @param array    $status Array of posting information.
 * @param int      $id Post ID.
 * @param string   $request_type Whether an upload or card should be added to Bluesky request.
 *
 * @return array Image blob array to be posted with status.
 */
function wpt_upload_bluesky_media( $connection, $auth, $attachment, $status, $id, $request_type = 'upload' ) {
	$request = array();
	if ( $connection ) {
		$card = ( function_exists( 'wpt_card_data' ) ) ? wpt_card_data( $id, 'og' ) : false;
		if ( ! $card ) {
			// If there's no card data, return early.
			return $request;
		}
		if ( $attachment ) {
			$allowed = wpt_check_mime_type( $attachment, 'bluesky' );
			if ( ! $allowed ) {
				wpt_mail( 'Media upload mime type not accepted by Bluesky', get_post_mime_type( $attachment ), $id );

				return $request;
			}
			$alt_text = get_post_meta( $attachment, '_wp_attachment_image_alt', true );
			/**
			 * Add alt attributes to uploaded images.
			 *
			 * @hook wpt_uploaded_image_alt
			 *
			 * @param {string} $alt_text Text stored in media library as alt.
			 * @param {int}    $attachment Attachment ID.
			 *
			 * @return {string}
			 */
			$alt_text   = apply_filters( 'wpt_uploaded_image_alt', $alt_text, $attachment );
			$path       = wpt_attachment_path( $attachment, 'large' );
			$image_data = wp_get_attachment_image_src( $attachment, 'large' );
			$ratio      = array(
				'width'  => $image_data[1],
				'height' => $image_data[2],
			);
			$mimetype   = mime_content_type( $path );
			$mimetypes  = array( 'image/png', 'image/jpeg', 'image/webp' );
			$size       = filesize( $path );
			// Return without attempting if fails to fetch image object.
			if ( ! ( in_array( $mimetype, $mimetypes, true ) && (int) $size < 1000000 ) ) {
				return $request;
			}
			$attachment_data = wpt_image_binary( $attachment, $id, 'bluesky' );
			if ( ! $attachment_data ) {
				return $request;
			}
			$request = array(
				'data'         => $attachment_data,
				'content-type' => $mimetype,
			);
			$blob    = $connection->call_api( 'https://bsky.social/xrpc/com.atproto.repo.uploadBlob', $request );
			if ( 'upload' === $request_type ) {
				$request = array(
					'$type'  => 'app.bsky.embed.images',
					'images' => array(
						array(
							'alt'         => $alt_text,
							'image'       => $blob['blob'],
							'aspectRatio' => $ratio,
						),
					),
				);
			} else {
				$card    = wpt_card_data( $id, 'og' );
				$request = array(
					'$type'    => 'app.bsky.embed.external',
					'external' => array(
						'uri'         => get_the_permalink( $id ),
						'title'       => $card['title'],
						'description' => $card['description'],
						'thumb'       => $blob['blob'],
					),
				);
			}
			wpt_mail( 'Media Uploaded (Bluesky)', "$auth, $attachment" . PHP_EOL . wpt_format_error( $blob ), $id );
		}
		if ( ! $attachment && 'card' === $request_type ) {
			$request = array(
				'$type'    => 'app.bsky.embed.external',
				'external' => array(
					'uri'         => get_the_permalink( $id ),
					'title'       => $card['title'],
					'description' => $card['description'],
				),
			);
			wpt_mail( 'Bluesky Card without media', "$auth, $attachment" . PHP_EOL . wpt_format_error( $request ), $id );

		}
	}

	return $request;
}

/**
 * Post status to Bluesky.
 *
 * @param object $connection Connection to Bluesky.
 * @param mixed  $auth Main site or specific author ID.
 * @param int    $id Post ID.
 * @param array  $status Array of information sent to Bluesky.
 * @param array  $image Array of image data to add to Bluesky post.
 *
 * @return array
 */
function wpt_send_post_to_bluesky( $connection, $auth, $id, $status, $image ) {
	$notice = '';
	/**
	 * Turn on staging mode. Staging mode is automatically turned on if WPT_STAGING_MODE constant is defined.
	 *
	 * @hook wpt_staging_mode
	 * @param {bool}     $staging_mode True to enable staging mode.
	 * @param {int|bool} $auth Current author.
	 * @param {int}      $id Post ID.
	 * @param {string}   $service Service being put into staging.
	 *
	 * @return {bool}
	 */
	$staging_mode = apply_filters( 'wpt_staging_mode', false, $auth, $id, 'bluesky' );
	$status_text  = $status['text'];
	if ( ( defined( 'WPT_STAGING_MODE' ) && true === WPT_STAGING_MODE ) || $staging_mode ) {
		// if in staging mode, we'll behave as if the update succeeded, but not send it.
		$connection = true;
		$success    = true;
		$http_code  = 200;
		$notice     = __( 'In Staging Mode:', 'wp-to-twitter' ) . ' ' . $status['text'];
		$status_id  = false;
	} else {
		/**
		 * Filter the approval to send a Bluesky post.
		 *
		 * @hook wpt_do_bluesky_post
		 * @param {bool}     $do_post Return false to cancel this post.
		 * @param {int|bool} $auth Author.
		 * @param {int}      $id Post ID.
		 * @param {string}   $text Status update text.
		 *
		 * @return {bool}
		 */
		$do_post   = apply_filters( 'wpt_do_bluesky_post', true, $auth, $id, $status['text'] );
		$status_id = false;
		$success   = false;
		// Change status array to Bluesky expectation.
		$status = array(
			'type'      => 'app.bsky.feed.post',
			'text'      => $status_text,
			'createdAt' => gmdate( DATE_ATOM ),
		);
		if ( ! empty( $image ) ) {
			$status['embed'] = $image;
		}
		/**
		 * Filter status array for Bluesky.
		 *
		 * @hook wpt_filter_bluesky_status
		 *
		 * @param {array}    $status Array of parameters sent to Bluesky.
		 * @param {int}      $post Post ID being tweeted.
		 * @param {int|bool} $auth Authoring context.
		 *
		 * @return {array}
		 */
		$status = apply_filters( 'wpt_filter_bluesky_status', $status, $id, $auth );
		if ( $do_post ) {
			$return = $connection->post_status( $status );
			if ( isset( $return['cid'] ) ) {
				$success   = true;
				$http_code = 200;
				$status_id = $return['cid'];
				$notice   .= __( 'Sent to Bluesky.', 'wp-to-twitter' );
			} else {
				$http_code = 401;
				$notice   .= __( 'Bluesky status update failed.', 'wp-to-twitter' );
			}
		} else {
			$http_code = '000';
			$notice   .= __( 'Bluesky status update cancelled by custom filter.', 'wp-to-twitter' );
		}
	}

	return array(
		'return'    => $success,
		'http'      => $http_code,
		'notice'    => $notice,
		'status_id' => $status_id,
		'status'    => $status_text,
		'service'   => 'bluesky',
	);
}

/**
 * Establish a client to Bluesky.
 *
 * @param mixed int|boolean $auth Current author context.
 * @param array             $verify Array of credentials to validate.
 *
 * @return mixed array or false
 */
function wpt_bluesky_connection( $auth = false, $verify = false ) {
	if ( ! empty( $verify ) ) {
		$password = $verify['password'];
		$user     = $verify['identifier'];
	} else {
		if ( ! $auth ) {
			$password = get_option( 'wpt_bluesky_token' );
			$user     = get_option( 'wpt_bluesky_username' );
		} else {
			$password = get_user_meta( $auth, 'wpt_bluesky_token', true );
			$user     = get_user_meta( $auth, 'wpt_bluesky_username', true );
		}
	}
	$bluesky = false;
	if ( $password && $user ) {
		$bluesky = new Wpt_Bluesky_Api( $user, $password );
		if ( $verify ) {
			$verify = $bluesky->verify();

			return $verify;
		}
	}

	return $bluesky;
}
