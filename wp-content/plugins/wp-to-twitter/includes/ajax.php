<?php
/**
 * AJAX action runners.
 *
 * @category AJAX
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

add_action( 'wp_ajax_wpt_post_update', 'wpt_ajax_tweet' );
/**
 * Handle updates sent via Ajax Update Now/Schedule Update buttons.
 */
function wpt_ajax_tweet() {
	if ( ! check_ajax_referer( 'wpt-tweet-nonce', 'security', false ) ) {
		wp_die( esc_html__( 'XPoster: Invalid Security Check', 'wp-to-twitter' ) );
	}
	$action = ( isset( $_REQUEST['tweet_action'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['tweet_action'] ) ) : false;
	if ( ! $action ) {
		return;
	}
	$authors      = ( isset( $_REQUEST['tweet_auth'] ) && null !== $_REQUEST['tweet_auth'] ) ? map_deep( wp_unslash( $_REQUEST['tweet_auth'] ), 'sanitize_text_field' ) : false;
	$upload       = ( isset( $_REQUEST['tweet_upload'] ) && null !== $_REQUEST['tweet_upload'] ) ? (int) $_REQUEST['tweet_upload'] : '1';
	$current_user = wp_get_current_user();
	if ( function_exists( 'wpt_pro_exists' ) && wpt_pro_exists() ) {
		$acct     = $current_user->ID;
		$verified = wpt_check_connections( $acct );
		if ( $verified ) {
			$auth = $current_user->ID;
		} else {
			$auth = false;
		}
	} else {
		$auth = false;
	}
	$authors = ( is_array( $authors ) && ! empty( $authors ) ) ? $authors : array( $auth );

	if ( current_user_can( 'wpt_can_tweet' ) ) {
		$options  = get_option( 'wpt_post_types' );
		$post_ID  = isset( $_REQUEST['tweet_post_id'] ) ? intval( $_REQUEST['tweet_post_id'] ) : false;
		$image_id = ( isset( $_REQUEST['image_id'] ) && null !== $_REQUEST['image_id'] ) ? (int) $_REQUEST['image_id'] : false;
		if ( $image_id ) {
			update_post_meta( $post_ID, '_wpt_custom_image', $image_id );
		}
		$omitted  = ( isset( $_REQUEST['omit'] ) ) ? map_deep( wp_unslash( $_REQUEST['omit'] ), 'sanitize_text_field' ) : array();
		$services = wpt_check_connections( false, true );
		// The interface has you choose what you want; the DB represents what's omitted.
		foreach ( array_keys( $services ) as $service ) {
			if ( ! in_array( $service, $omitted, true ) ) {
				$omit[] = $service;
			}
		}
		update_post_meta( $post_ID, '_wpt_omit_services', $omit );
		$type           = get_post_type( $post_ID );
		$default        = ( isset( $options[ $type ]['post-edited-text'] ) ) ? $options[ $type ]['post-edited-text'] : '';
		$sentence       = ( isset( $_REQUEST['tweet_text'] ) && ! empty( $_REQUEST['tweet_text'] ) ) ? sanitize_textarea_field( wp_unslash( $_REQUEST['tweet_text'] ) ) : $default;
		$schedule       = ( isset( $_REQUEST['tweet_schedule'] ) ) ? strtotime( sanitize_text_field( wp_unslash( $_REQUEST['tweet_schedule'] ) ) ) : wp_rand( 60, 240 );
		$print_schedule = date_i18n( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), $schedule );
		$offset         = ( 60 * 60 * get_option( 'gmt_offset' ) );
		$schedule       = $schedule - $offset;
		$media          = ( '1' === $upload ) ? false : true; // this is correct; the boolean logic is reversed. Blah.

		if ( isset( $_REQUEST['x_text'] ) && ! empty( $_REQUEST['x_text'] ) ) {
			$template = sanitize_textarea_field( wp_unslash( $_REQUEST['x_text'] ) );
			update_post_meta( $post_ID, '_wpt_post_template_x', $template );
		}

		if ( isset( $_REQUEST['mastodon_text'] ) && ! empty( $_REQUEST['mastodon_text'] ) ) {
			$template = sanitize_textarea_field( wp_unslash( $_REQUEST['mastodon_text'] ) );
			update_post_meta( $post_ID, '_wpt_post_template_mastodon', $template );
		}

		if ( isset( $_REQUEST['bluesky_text'] ) && ! empty( $_REQUEST['bluesky_text'] ) ) {
			$template = sanitize_textarea_field( wp_unslash( $_REQUEST['bluesky_text'] ) );
			update_post_meta( $post_ID, '_wpt_post_template_bluesky', $template );
		}

		foreach ( $authors as $auth ) {
			$auth = ( 'main' === $auth ) ? false : $auth;
			switch ( $action ) {
				case 'tweet':
					wpt_post_to_service( $sentence, $auth, $post_ID, $media );
					break;
				case 'schedule':
					wp_schedule_single_event(
						$schedule,
						'wpt_schedule_tweet_action',
						array(
							'id'       => $auth,
							'sentence' => $sentence,
							'rt'       => 0,
							'post_id'  => $post_ID,
						)
					);
					break;
			}
			$log     = wpt_get_log( 'wpt_status_message', $post_ID );
			$message = is_array( $log ) ? $log['message'] : $log;
			// Translators: Full text of Update, time scheduled for.
			$return = ( 'tweet' === $action ) ? $message : sprintf( __( 'Update scheduled: %1$s for %2$s', 'wp-to-twitter' ), '"' . $sentence . '"', $print_schedule );
			echo esc_html( $return );
			if ( count( $authors ) > 1 ) {
				echo '<br />';
			}
		}
	} else {
		esc_html_e( 'You are not authorized to perform this action', 'wp-to-twitter' );
	}
	die;
}
