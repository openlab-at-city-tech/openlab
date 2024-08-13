<?php
/**
 * Send API queries for a post to Mastodon instance.
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

require_once plugin_dir_path( __FILE__ ) . 'classes/class-wpt-mastodon-api.php';

/**
 * Upload media to Mastodon API.
 *
 * @param object   $connection Mastodon connection.
 * @param int|bool $auth Connection context.
 * @param int      $attachment Attachment ID.
 * @param array    $status Array of posting information.
 * @param int      $id Post ID.
 *
 * @return array
 */
function wpt_upload_mastodon_media( $connection, $auth, $attachment, $status, $id ) {
	if ( $connection ) {
		if ( $attachment ) {
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
			$alt_text        = apply_filters( 'wpt_uploaded_image_alt', $alt_text, $attachment );
			$attachment_data = wpt_image_binary( $attachment, 'mastodon' );
			// Return without attempting if fails to fetch image object.
			if ( ! $attachment_data ) {
				return $status;
			}
			$request = array(
				'file'        => $attachment_data,
				'description' => $alt_text,
			);

			$response              = $connection->upload_media( $request );
			$media_id              = $response['id'];
			$status['media_ids[]'] = $media_id;

			wpt_mail( 'Media Uploaded', "$auth, $media_id, $attachment", $id );
		}
	}

	return $status;
}

/**
 * Post status to Mastodon.
 *
 * @param object $connection Connection to Mastodon.
 * @param mixed  $auth Main site or specific author ID.
 * @param int    $id Post ID.
 * @param array  $status Array of information sent to Mastodon.
 *
 * @return array
 */
function wpt_send_post_to_mastodon( $connection, $auth, $id, $status ) {
	$notice = '';
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
	if ( ( defined( 'WPT_STAGING_MODE' ) && true === WPT_STAGING_MODE ) || $staging_mode ) {
		// if in staging mode, we'll behave as if the Tweet succeeded, but not send it.
		$connection = true;
		$http_code  = 200;
		$notice     = __( 'In Staging Mode:', 'wp-to-twitter' ) . ' ';
		$status_id  = false;
	} else {
		/**
		 * Filter the approval to send a Mastodon Toot.
		 *
		 * @hook wpt_do_toot
		 * @param {bool}     $do_toot Return false to cancel this Toot.
		 * @param {int|bool} $auth Author.
		 * @param {int}      $id Post ID.
		 * @param {string}   $text Status update text.
		 *
		 * @return {bool}
		 */
		$do_post   = apply_filters( 'wpt_do_toot', true, $auth, $id, $status['text'] );
		$status_id = false;
		$success   = false;
		// Change status array to Mastodon expectation.
		$status['status'] = $status['text'];
		unset( $status['text'] );
		/**
		 * Filter status array for Mastodon.
		 *
		 * @hook wpt_filter_mastodon_status
		 *
		 * @param {array}    $status Array of parameters sent to Mastodon.
		 * @param {int}      $post Post ID being tweeted.
		 * @param {int|bool} $auth Authoring context.
		 *
		 * @return {array}
		 */
		$status = apply_filters( 'wpt_filter_mastodon_status', $status, $id, $auth );
		if ( $do_post ) {
			$return = $connection->post_status( $status );
			if ( isset( $return['id'] ) ) {
				$success   = true;
				$http_code = 200;
				$status_id = $return['id'];
				$notice   .= __( 'Sent to Mastodon.', 'wp-to-twitter' );
			} else {
				$http_code = 401;
				$notice   .= __( 'Mastodon status update failed.', 'wp-to-twitter' );
			}
		} else {
			$http_code = '000';
			$notice   .= __( 'Mastodon status update cancelled by custom filter.', 'wp-to-twitter' );
		}
	}

	return array(
		'return'    => $success,
		'http'      => $http_code,
		'notice'    => $notice,
		'status_id' => $status_id,
	);
}

/**
 * Establish an OAuth client to Mastodon.
 *
 * @param mixed int|boolean $auth Current author context.
 * @param array             $verify Array of credentials to validate.
 *
 * @return mixed $mastodon or false
 */
function wpt_mastodon_connection( $auth = false, $verify = false ) {
	if ( ! empty( $verify ) ) {
		$token    = $verify['token'];
		$instance = $verify['instance'];
	} else {
		if ( ! $auth ) {
			$token    = get_option( 'wpt_mastodon_token' );
			$instance = get_option( 'wpt_mastodon_instance' );
		} else {
			$token    = get_user_meta( $auth, 'wpt_mastodon_token', true );
			$instance = get_user_meta( $auth, 'wpt_mastodon_instance', true );
		}
	}
	$mastodon = false;
	if ( $token && $instance ) {
		$mastodon = new Wpt_Mastodon_Api( $token, $instance );
		if ( $verify ) {
			$verify = $mastodon->verify();

			return $verify;
		}
	}

	return $mastodon;
}
