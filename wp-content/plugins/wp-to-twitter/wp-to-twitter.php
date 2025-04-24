<?php
/**
 * XPoster
 *
 * @package     XPoster
 * @author      Joe Dolson
 * @copyright   2008-2025 Joe Dolson
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: XPoster - Share to Bluesky and Mastodon
 * Plugin URI:  https://www.joedolson.com/wp-to-twitter/
 * Description: Posts a status update when you update your WordPress blog or post a link, using your URL shortener. Many options to customize and promote your statuses.
 * Author:      Joe Dolson
 * Author URI:  https://www.joedolson.com
 * Text Domain: wp-to-twitter
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/license/gpl-2.0.txt
 * Version:     5.0.2
 */

/*
	Copyright 2008-2025  Joe Dolson (email : joe@joedolson.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

use WpToTwitter_Vendor\GuzzleHttp\Exception\RequestException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpt_debug = get_option( 'wpt_debug_tweets', false );
define( 'WPT_DEBUG', $wpt_debug );
define( 'WPT_DEBUG_BY_EMAIL', false ); // Email debugging no longer default as of 3.3.0.
define( 'WPT_DEBUG_ADDRESS', get_option( 'admin_email' ) );
define( 'WPT_FROM', 'From: \"' . get_option( 'blogname' ) . '\" <' . get_option( 'admin_email' ) . '>' );

// If current environment tests as staging, enable staging mode.
if ( function_exists( 'wp_get_environment_type' ) ) {
	if ( 'staging' === wp_get_environment_type() && ! defined( 'WPT_STAGING_MODE' ) ) {
		define( 'WPT_STAGING_MODE', true );
	}
}

require_once plugin_dir_path( __FILE__ ) . 'vendor_prefixed/vendor/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/metabox.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/deprecated.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/media.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/post-info.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'wpt-functions.php';
// Service post handlers.
require_once plugin_dir_path( __FILE__ ) . 'services/x/post.php';
require_once plugin_dir_path( __FILE__ ) . 'services/mastodon/post.php';
require_once plugin_dir_path( __FILE__ ) . 'services/bluesky/post.php';
// URL Shortening.
require_once plugin_dir_path( __FILE__ ) . 'wp-to-twitter-shorteners.php';
// Service settings.
require_once plugin_dir_path( __FILE__ ) . 'services/x/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'services/mastodon/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'services/bluesky/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'wp-to-twitter-manager.php';
// Template generation.
require_once plugin_dir_path( __FILE__ ) . 'wpt-truncate.php';
require_once plugin_dir_path( __FILE__ ) . 'wpt-rate-limiting.php';

define( 'XPOSTER_VERSION', '5.0.2' );

register_activation_hook( __FILE__, 'wpt_check_version' );
/**
 * Check whether version requires activation.
 */
function wpt_check_version() {
	$prev_version = ( '' !== get_option( 'wp_to_twitter_version', '' ) ) ? get_option( 'wp_to_twitter_version' ) : '1.0.0';
	if ( version_compare( $prev_version, XPOSTER_VERSION, '<' ) ) {
		xposter_activate();
	}
}

register_deactivation_hook( __FILE__, 'wpt_deactivate' );
/**
 * Deactivate Plugin.
 */
function wpt_deactivate() {
	// Remove rate limits cron if enabled.
	wp_clear_scheduled_hook( 'wptratelimits' );
}

/**
 * Pro version check.
 */
function xposter_pro_check() {
	global $wptp_version;
	$plugin_data = get_plugin_data( __FILE__, false );
	if ( time() < 1743465600 ) {
		$upgrade_now = ' <a href="https://xposterpro.com/awesome/xposter-pro/?discount=XPOSTERPRO-15">' . esc_html__( 'Upgrade Now - 15&#37; Discount until April 1st!', 'wp-to-twitter' ) . '</a>';
	} else {
		$upgrade_now = ' <a href="https://xposterpro.com/awesome/xposter-pro/">' . esc_html__( 'Upgrade Now', 'wp-to-twitter' ) . '</a>';
	}
	if ( $wptp_version && version_compare( $wptp_version, '3.0.0', '<=' ) ) {
		$message = sprintf(
			// Translators: Plugin name, plugin version unsupported.
			__( '%1$s is not compatible with XPoster Pro %2$s or lower; XPoster Pro has been deactivated.', 'wp-to-twitter' ) . $upgrade_now,
			'<strong>' . $plugin_data['Name'] . '</strong>',
			'3.0.0'
		);
		wp_admin_notice(
			$message,
			array(
				'type' => 'error',
			)
		);
		if ( '3.0.0' === $wptp_version ) {
			deactivate_plugins( 'wp-tweets-pro/wpt-pro-functions.php' );
		}
	} elseif ( $wptp_version && version_compare( $wptp_version, '3.4.0', '<=' ) ) {
		$message = sprintf(
			// Translators: Plugin name, plugin version unsupported.
			__( '%1$s has limited compatibility with XPoster Pro %2$s or lower. Some features may not be available.', 'wp-to-twitter' ) . $upgrade_now,
			'<strong>' . $plugin_data['Name'] . '</strong>',
			'3.3.2'
		);
		wp_admin_notice(
			$message,
			array(
				'type' => 'error',
			)
		);
	}
}
add_action( 'admin_notices', 'xposter_pro_check' );

/**
 * Activate XPoster.
 */
function xposter_activate() {
	// If this has never run before, do the initial setup.
	$new_install = '1' === get_option( 'wpt_twitter_setup' ) ? false : true;
	if ( $new_install ) {
		$initial_settings = array(
			'post' => array(
				'post-published-update' => '1',
				'post-published-text'   => 'New post: #title# #url#',
				'post-edited-update'    => '0',
				'post-edited-text'      => 'Post Edited: #title# #url#',
			),
			'page' => array(
				'post-published-update' => '0',
				'post-published-text'   => 'New page: #title# #url#',
				'post-edited-update'    => '0',
				'post-edited-text'      => 'Page edited: #title# #url#',
			),
		);
		update_option( 'wpt_post_types', $initial_settings );
		update_option( 'jd_twit_blogroll', '1' );
		update_option( 'newlink-published-text', 'New link: #title# #url#' );
		update_option( 'jd_shortener', '3' );
		update_option( 'jd_strip_nonan', '0' );
		update_option( 'jd_max_tags', 4 );
		update_option( 'jd_max_characters', 20 );
		$administrator = get_role( 'administrator' );
		if ( is_object( $administrator ) ) {
			// wpt_twitter_oauth is the general permission for editing user accounts.
			$administrator->add_cap( 'wpt_twitter_oauth' );
			$administrator->add_cap( 'wpt_twitter_custom' );
			$administrator->add_cap( 'wpt_twitter_switch' );
			$administrator->add_cap( 'wpt_can_tweet' );
			$administrator->add_cap( 'wpt_tweet_now' );
		}
		$editor = get_role( 'editor' );
		if ( is_object( $editor ) ) {
			$editor->add_cap( 'wpt_can_tweet' );
		}
		$author = get_role( 'author' );
		if ( is_object( $author ) ) {
			$author->add_cap( 'wpt_can_tweet' );
		}
		$contributor = get_role( 'contributor' );
		if ( is_object( $contributor ) ) {
			$contributor->add_cap( 'wpt_can_tweet' );
		}

		update_option( 'jd_post_excerpt', 90 );
		// Use Google Analytics.
		update_option( 'twitter-analytics-campaign', 'twitter' );
		update_option( 'use-twitter-analytics', '0' );
		update_option( 'jd_dynamic_analytics', '0' );
		update_option( 'no-analytics', 1 );
		update_option( 'use_dynamic_analytics', 'category' );
		// Use custom external URLs to point elsewhere.
		update_option( 'jd_twit_custom_url', 'external_link' );
		// Error checking.
		update_option( 'wp_url_failure', '0' );
		// Default publishing options.
		update_option( 'jd_tweet_default', '0' );
		update_option( 'jd_tweet_default_edit', '0' );
		update_option( 'wpt_inline_edits', '0' );
		// Note that default options are set.
		update_option( 'wpt_twitter_setup', '1' );
		update_option( 'jd_keyword_format', '0' );
	}

	$prev_version = get_option( 'wp_to_twitter_version' );
	$upgrade      = version_compare( $prev_version, '3.4.4', '<' );
	if ( $upgrade ) {
		delete_option( 'bitlyapi' );
		delete_option( 'bitlylogin' );
	}

	update_option( 'wp_to_twitter_version', XPOSTER_VERSION );
}

/**
 * Save error messages for status updates.
 *
 * @param int    $id Post ID.
 * @param int    $auth Current author.
 * @param string $twit Update text.
 * @param string $error Error string from service.
 * @param int    $http_code Http code from service.
 * @param string $ts Current timestamp.
 */
function wpt_save_error( $id, $auth, $twit, $error, $http_code, $ts ) {
	$http_code = (int) $http_code;
	if ( 200 !== $http_code ) {
		add_post_meta(
			$id,
			'_wpt_failed',
			array(
				'author'    => $auth,
				'sentence'  => $twit,
				'error'     => $error,
				'code'      => $http_code,
				'timestamp' => $ts,
			)
		);
	} else {
		if ( '1' === get_option( 'wpt_rate_limiting' ) ) {
			wpt_log_success( $auth, $ts, $id );
		}
	}
}

/**
 * Save a record of a successful status update.
 *
 * @param int    $id Post ID.
 * @param string $twit Status update text.
 * @param int    $http_code HTTP Code returned by query.
 */
function wpt_save_success( $id, $twit, $http_code ) {
	if ( 200 === $http_code ) {
		$jwt = get_post_meta( $id, '_jd_wp_twitter', true );
		if ( ! is_array( $jwt ) ) {
			$jwt = array();
		}
		$jwt[] = urldecode( $twit );
		update_post_meta( $id, '_jd_wp_twitter', $jwt );
	}
}

/**
 * Checks whether XPoster has sent a tweet on this post to this author within the last 30 seconds and blocks duplicates.
 *
 * @param int $id Post ID.
 * @param int $auth Author.
 *
 * @uses filter wpt_recent_tweet_threshold
 * @return boolean true to send status update, false to block.
 */
function wpt_check_recent_tweet( $id, $auth ) {
	if ( ! $id ) {
		return false;
	} else {
		if ( false === $auth ) {
			$transient = get_transient( "_wpt_most_recent_tweet_$id" );
		} else {
			$transient = get_transient( '_wpt_' . $auth . "_most_recent_tweet_$id" );
		}
		if ( $transient ) {
			return true;
		} else {
			/**
			 * Modify the expiration window for recent status updates.
			 * This value does flood control, to prevent a runaway process from sending multiple status updates. Default `30` seconds.
			 *
			 * @hook wpt_recent_tweet_threshold
			 * @param {int} $expire Integer representing seconds. How long the transient will exist.
			 *
			 * @return {int}
			 */
			$expire = apply_filters( 'wpt_recent_tweet_threshold', 30 );
			// if expiration is 0, don't set the transient. We don't want permanent transients.
			if ( 0 !== $expire ) {
				if ( false === $auth ) {
					set_transient( "_wpt_most_recent_tweet_$id", true, $expire );
				} else {
					set_transient( '_wpt_' . $auth . "_most_recent_tweet_$id", true, $expire );
				}
			}
		}
	}

	return false;
}

/**
 * Check whether the current post should be sent to a given service.
 *
 * @param int    $post_ID Post ID.
 * @param string $service Service ID.
 *
 * @return bool
 */
function wpt_service_enabled( $post_ID = false, $service = 'bluesky' ) {
	$omit     = ( $post_ID ) ? get_post_meta( $post_ID, '_wpt_omit_services', true ) : array();
	$omit     = ( $omit && is_array( $omit ) ) ? $omit : array();
	$disabled = get_option( 'wpt_disabled_services', array() );
	$send_to  = true;
	if ( in_array( $service, $omit, true ) || in_array( $service, array_keys( $disabled ), true ) ) {
		if ( $post_ID ) {
			wpt_mail( ucfirst( $service ) . ' is disabled.', wpt_format_error( $disabled ), $post_ID );
		}
		$send_to = false;
	}
	/**
	 * Filter whether a given post should be sent to a specific service.
	 *
	 * @hook wpt_service_enabled
	 *
	 * @param {bool}   $send_to True to send to a service.
	 * @param {int}    $post_ID Post ID. False if checking globally.
	 * @param {string} $service Service ID.
	 *
	 * @return {bool}
	 */
	return apply_filters( 'wpt_service_enabled', $send_to, $post_ID, $service );
}

/**
 * Performs the API post to target services. Alias for wpt_post_to_twitter.
 *
 * @param string       $template Template for status update to be sent to service.
 * @param int          $auth Author ID.
 * @param int          $id Post ID.
 * @param null|boolean $media Whether to upload media attached to the post specified in $id. Default null, using default post settings.
 *
 * @return boolean|array False if blocked, array of statuses if attempted.
 */
function wpt_post_to_service( $template, $auth = false, $id = false, $media = null ) {
	$return = wpt_post_to_twitter( $template, $auth, $id, $media );
	if ( $return && is_array( $return ) ) {
		$info      = array_pop( $return );
		$status    = isset( $info['status'] ) ? $info['status'] : '';
		$notice    = isset( $info['notice'] ) ? $info['notice'] : '';
		$http_code = isset( $info['http'] ) ? $info['http'] : '';
		wpt_save_error( $id, $auth, $status, $notice, $http_code, time() );
		wpt_save_success( $id, $status, $http_code );
	}

	return $return;
}

/**
 * Performs the API post to target services.
 *
 * @param string       $template Template for status update to be sent to service.
 * @param int          $auth Author ID.
 * @param int          $id Post ID.
 * @param null|boolean $media Whether to upload media attached to the post specified in $id. Default null, using default post settings.
 *
 * @return boolean|array False if blocked, array of statuses if attempted.
 */
function wpt_post_to_twitter( $template, $auth = false, $id = false, $media = null ) {
	// If an ID is set but the post is not currently present or published, ignore.
	$return = array();
	if ( $id ) {
		$status = get_post_status( $id );
		if ( ! $status || 'publish' !== $status ) {
			$error = __( 'This post is no longer published or has been deleted', 'wp-to-twitter' );
			wpt_save_error( $id, $auth, $template, $error, '404', time() );
			wpt_set_log( 'wpt_status_message', $id, $error, '404' );

			return false;
		}
	}
	$error = false;
	if ( '1' === get_option( 'wpt_rate_limiting' ) ) {
		// check whether this post needs to be rate limited.
		$continue = wpt_test_rate_limit( $id, $auth );
		if ( ! $continue ) {
			wpt_mail( 'This post was blocked by XPoster rate limiting.', 'Post ID: ' . $id . '; Account: ' . $auth );
			wpt_set_log( 'wpt_status_message', $id, __( 'Status update prevented due to XPoster rate limiting.', 'wp-to-twitter' ), '404' );

			return false;
		}
	}

	$recent = wpt_check_recent_tweet( $id, $auth );
	if ( $recent ) {
		// This is a duplicate after less than 30 seconds, which usually means an extra run of the action.
		// This error used to be logged, but is now exited silently.

		return false;
	}

	$connections    = wpt_check_connections( $auth, true );
	$check_twitter  = $connections['x'];
	$check_mastodon = $connections['mastodon'];
	$check_bluesky  = $connections['bluesky'];
	if ( ! $check_twitter && ! $check_mastodon && ! $check_bluesky ) {
		$error = __( 'This account is not authorized to post to any services.', 'wp-to-twitter' );
		wpt_save_error( $id, $auth, $template, $error, '401', time() );
		wpt_set_log( 'wpt_status_message', $id, $error, '401' );

		return false;
	} // exit silently if not authorized.

	// Check if this update has already been sent to a given service.
	$check = wpt_check_service_history( $id, $auth, $template, $connections );

	// if has media, must have a valid attachment.
	$media      = ( null === $media ) ? wpt_post_with_media( $id ) : $media;
	$attachment = ( $media ) ? wpt_post_attachment( $id ) : false;
	$connection = false;
	if ( $check['x'] && $check_twitter && wpt_service_enabled( $id, 'x' ) ) {
		$text       = $check['x'];
		$status     = array(
			'text' => $text,
		);
		$connection = $check_twitter;
		$status     = wpt_upload_twitter_media( $connection, $auth, $attachment, $status, $id );
		$response   = wpt_send_post_to_twitter( $connection, $auth, $id, $status );
		wpt_post_submit_handler( $connection, $response, $id, $auth, $text );
		$return['xcom'] = $response;
		wpt_mail( 'Share Connection Status: X', "$text, $auth, $id, $media, " . wpt_format_error( $response ), $id );
	}
	if ( $check['mastodon'] && $check_mastodon && wpt_service_enabled( $id, 'mastodon' ) ) {
		$text       = $check['mastodon'];
		$status     = array(
			'text' => $text,
		);
		$connection = $check_mastodon;
		$status     = wpt_upload_mastodon_media( $connection, $auth, $attachment, $status, $id );
		$response   = wpt_send_post_to_mastodon( $connection, $auth, $id, $status );
		wpt_post_submit_handler( $connection, $response, $id, $auth, $text );
		$return['mastodon'] = $response;
		wpt_mail( 'Share Connection Status: Mastodon', "$text, $auth, $id, $media, " . wpt_format_error( $response ), $id );
	}
	if ( $check['bluesky'] && $check_bluesky && wpt_service_enabled( $id, 'bluesky' ) ) {
		$text         = $check['bluesky'];
		$status       = array(
			'text' => $text,
		);
		$connection   = $check_bluesky;
		$request_type = ( wpt_post_with_media( $id ) ) ? 'upload' : 'card';
		$attachment   = ( $attachment ) ? $attachment : wpt_post_attachment( $id );
		$image        = wpt_upload_bluesky_media( $connection, $auth, $attachment, $status, $id, $request_type );
		$response     = wpt_send_post_to_bluesky( $connection, $auth, $id, $status, $image );
		wpt_post_submit_handler( $connection, $response, $id, $auth, $text );
		$return['bluesky'] = $response;
		wpt_mail( 'Share Connection Status: Bluesky', "$text, $auth, $id, $media, " . wpt_format_error( $response ), $id );
	}
	if ( ! empty( $return ) ) {

		return $return;
	} else {
		wpt_set_log( 'wpt_status_message', $id, __( 'This status update has already been sent.', 'wp-to-twitter' ), '404' );

		return false;
	}
}

/**
 * Get the saved custom template for a status update.
 *
 * @param int    $post_ID Post ID.
 * @param string $template Template passed to this post.
 * @param string $service Service posting to.
 *
 * @return string
 */
function wpt_get_custom_template( $post_ID, $template, $service ) {
	$custom_template = get_post_meta( $post_ID, '_wpt_post_template_' . $service, true );
	if ( $custom_template ) {
		$template = $custom_template;
	}

	return $template;
}

/**
 * Check whether a status update has already been sent; expands the passed template to create status update to test.
 *
 * @param int      $post_ID Post ID.
 * @param int|bool $auth Author ID or false.
 * @param string   $template Status template.
 * @param array    $connections Array of active connection information.
 *
 * @return array Array of statuses by service or false, if blocked.
 */
function wpt_check_service_history( $post_ID, $auth, $template, $connections ) {
	$checks = array();
	foreach ( $connections as $service => $connected ) {
		if ( ! $connected || ! wpt_service_enabled( $post_ID, $service ) ) {
			$checks[ $service ] = false;
			continue;
		}
		$template = wpt_get_custom_template( $post_ID, $template, $service );
		$status   = wpt_truncate_status( $template, array(), $post_ID, false, $auth, $service );
		// Get last sent to this service.
		$check = ( ! $auth ) ? get_option( 'wpt_last_' . $service, '' ) : get_user_meta( $auth, 'wpt_last_' . $service, true );
		// prevent duplicate status updates. Checks whether this text has already been sent.
		if ( $check === $status && '' !== $status ) {
			wpt_mail( ucfirst( $service ) . ': Status update identical to previous update', "This Update: $status; Check Update: $check; $auth, $post_ID", $post_ID ); // DEBUG.
			$error = __( 'This status update is identical to another update recently sent to this account.', 'wp-to-twitter' ) . ' ' . __( 'All status updates are expected to be unique.', 'wp-to-twitter' );
			wpt_save_error( $post_ID, $auth, $status, $error, '403-1', time() );
			wpt_set_log( 'wpt_status_message', $post_ID, $error, '403' );
			$status = false;
		} elseif ( '' === $status || ! $status ) {
			wpt_mail( 'Status update check: empty sentence', "$status, $auth, $post_ID", $post_ID ); // DEBUG.
			$error = __( 'This status update was blank and could not be sent to the API.', 'wp-to-twitter' );
			wpt_save_error( $post_ID, $auth, $status, $error, '403-2', time() );
			wpt_set_log( 'wpt_status_message', $post_ID, $error, '403' );
			$status = false;
		}
		$checks[ $service ] = $status;
	}

	return $checks;
}

/**
 * Handle post-sending responses for APIs.
 *
 * @param object   $connection API connection.
 * @param array    $response Array of response data from API.
 * @param int      $id Post ID.
 * @param int|bool $auth Author context.
 * @param string   $twit Posted status text.
 *
 * @return array Array with response info.
 */
function wpt_post_submit_handler( $connection, $response, $id, $auth, $twit ) {
	$return      = $response['return'];
	$http_code   = $response['http'];
	$notice      = $response['notice'];
	$service     = isset( $response['service'] ) ? $response['service'] : false;
	$tweet_id    = ( 'x' === $service ) ? $response['status_id'] : false;
	$mastodon_id = ( 'mastodon' === $service ) ? $response['status_id'] : false;
	$bluesky_id  = ( 'bluesky' === $service ) ? $response['status_id'] : false;
	wpt_mail( "Status Update Response: $http_code / $service", $notice, $id ); // DEBUG.
	// only save last status if successful.
	if ( 200 === $http_code ) {
		$services = array( 'x', 'mastodon', 'bluesky' );
		foreach ( $services as $service ) {
			if ( ! $auth ) {
				update_option( 'wpt_last_' . $service, $twit );
			} else {
				update_user_meta( $auth, 'wpt_last_' . $service, $twit );
			}
		}
	}
	if ( ! $return ) {
		/**
		 * Executes an action after posting a status fails.
		 *
		 * @hook wpt_tweet_failed
		 *
		 * @since 3.6.0
		 *
		 * @param {object} $connection The current OAuth connection.
		 * @param {int}    $id Post ID for status update.
		 * @param {string} $error Error message returned.
		 */
		do_action( 'wpt_tweet_failed', $connection, $id, $notice );
		wpt_set_log( 'wpt_status_message', $id, $notice, $http_code );
	} else {
		/**
		 * Executes an action after a status is posted successfully.
		 *
		 * @hook wpt_tweet_posted
		 *
		 * @param {object} $connection The current OAuth connection.
		 * @param {int}    $id Post ID for status update.
		 */
		do_action( 'wpt_tweet_posted', $connection, $id );
		// Log the Status ID of the first status update on this post.
		$has_tweet_id = get_post_meta( $id, '_wpt_tweet_id', true );
		if ( ! $has_tweet_id && $tweet_id ) {
			update_post_meta( $id, '_wpt_tweet_id', $tweet_id );
		}
		// Log the Status ID of the first Mastodon update on this post.
		$has_mastodon_id = get_post_meta( $id, '_wpt_status_id', true );
		if ( ! $has_mastodon_id && $mastodon_id ) {
			update_post_meta( $id, '_wpt_status_id', $mastodon_id );
		}
		// Log the Status ID of the first Bluesky update on this post.
		$has_bluesky_id = get_post_meta( $id, '_wpt_bluesky_id', true );
		if ( ! $has_bluesky_id && $bluesky_id ) {
			update_post_meta( $id, '_wpt_bluesky_id', $bluesky_id );
		}
		wpt_set_log( 'wpt_status_message', $id, $notice );
	}

	return $response;
}

/**
 * Get text error message from HTTP code.
 *
 * @param int      $http_code HTTP returned.
 * @param string   $notice Any already generated notification message.
 * @param int|bool $auth Current authentication context.
 *
 * @return array
 */
function wpt_get_response_message( $http_code, $notice, $auth ) {
	$return = false;
	switch ( $http_code ) {
		case '000':
			$error = '';
			break;
		case 100:
			$error = __( '100 Continue: X received the header of your submission, but your server did not follow through by sending the body of the data.', 'wp-to-twitter' );
			break;
		case 200:
			$return = true;
			$error  = __( '200 OK: Success!', 'wp-to-twitter' );
			update_option( 'wpt_authentication_missing', false );
			break;
		case 304:
			$error = __( '304 Not Modified: There was no new data to return', 'wp-to-twitter' );
			break;
		case 400:
			// Translators: Error description from X.com.
			$error = sprintf( __( '400: %s', 'wp-to-twitter' ), $notice );
			break;
		case 401:
			// Translators: Error description from X.com.
			$error = sprintf( __( '401: %s', 'wp-to-twitter' ), $notice );
			update_option( 'wpt_authentication_missing', "$auth" );
			break;
		case 403:
			// Translators: Error description from X.com.
			$error = sprintf( __( '403: %s', 'wp-to-twitter' ), $notice );
			break;
		case 404:
			// Translators: Error description from X.com.
			$error = sprintf( __( '404: %s', 'wp-to-twitter' ), $notice );
			break;
		case 406:
			// Translators: Error description from X.com.
			$error = sprintf( __( '406: %s', 'wp-to-twitter' ), $notice );
			break;
		case 422:
			// Translators: Error description from X.com.
			$error = sprintf( __( '422: %s', 'wp-to-twitter' ), $notice );
			break;
		case 429:
			// Translators: Error description from X.com.
			$error = sprintf( __( '429: %s', 'wp-to-twitter' ), $notice );
			break;
		case 500:
			$error = __( '500 Internal Server Error: Something is broken at X.com.', 'wp-to-twitter' );
			break;
		case 502:
			$error = __( '502 Bad Gateway: X.com is down or being upgraded.', 'wp-to-twitter' );
			break;
		case 503:
			$error = __( '503 Service Unavailable: The X.com servers are up, but overloaded with requests - Please try again later.', 'wp-to-twitter' );
			break;
		case 504:
			$error = __( "504 Gateway Timeout: The X.com servers are up, but the request couldn't be serviced due to some failure within our stack. Try again later.", 'wp-to-twitter' );
			break;
		default:
			// Translators: http code.
			$error = sprintf( __( '<strong>Code %s</strong>: X.com did not return a recognized response code.', 'wp-to-twitter' ), $http_code );
			break;
	}
	return array(
		'error'  => $error,
		'return' => $return,
	);
}

/**
 * For servers without PEAR normalize installed, approximates normalization. With normalizer, executes normalization on string.
 *
 * @param string $text Text to normalize.
 *
 * @return string Normalized text.
 */
function wpt_normalize( $text ) {
	if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) && function_exists( 'normalizer_normalize' ) ) {
		if ( normalizer_is_normalized( $text ) ) {
			return $text;
		}

		return normalizer_normalize( $text );
	} else {
		$normalizer = new WPT_Normalizer();
		if ( $normalizer->is_normalized( $text ) ) {
			return $text;
		}

		return $normalizer->normalize( $text );
	}
}

/**
 * Test URL to see if is pointing to https location.
 *
 * @param string $url URL to check for https.
 *
 * @return boolean
 */
function wpt_is_ssl( $url ) {
	if ( stripos( $url, 'https' ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * This function is no longer in use, but the filter within it is.
 *
 * @param string $post_type Type of post.
 * @param array  $post_info Post info.
 * @param int    $post_ID Post ID.
 *
 * @return bool False to block this from posting.
 */
function wpt_category_limit( $post_type, $post_info, $post_ID ) {
	$continue = true;
	$args     = array(
		'type' => $post_type,
		'info' => $post_info,
		'id'   => $post_ID,
	);

	return apply_filters( 'wpt_filter_terms', $continue, $args );
}

/**
 * Process bulk edited posts if permitted.
 *
 * @param array $updated Array of updated post IDs.
 */
function wpt_bulk_edit_posts( $updated ) {
	if ( '1' === get_option( 'wpt_inline_edits' ) ) {
		foreach ( $updated as $post_ID ) {
			wpt_post_update( $post_ID, 'instant' );
		}
	}
}
add_action( 'bulk_edit_posts', 'wpt_bulk_edit_posts' );

/**
 * Set up a status update to be sent.
 *
 * @param int     $post_ID Post ID.
 * @param string  $type Publishing context: instant, future, xmlrpc.
 * @param object  $post Post object.
 * @param boolean $updated True if updated, false if inserted.
 * @param object  $post_before The post prior to this update, or null for new posts.
 *
 * @return int $post_ID
 */
function wpt_post_update( $post_ID, $type = 'instant', $post = null, $updated = null, $post_before = null ) {
	if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
		return $post_ID;
	}
	$post_this = get_post_meta( $post_ID, '_wpt_post_this', true );
	$newpost   = false;
	$oldpost   = false;
	$template  = '';
	$nptext    = '';
	if ( '0' === get_option( 'jd_tweet_default' ) ) {
		// If post this value is not set or equals 'yes'.
		$send_update  = ( 'no' !== $post_this ) ? true : false;
		$text_default = 'no';
	} else {
		// If post this is set and is equal to yes.
		$send_update  = ( 'yes' === $post_this ) ? true : false;
		$text_default = 'yes';
	}
	wpt_mail( '1: Status Update should send: ' . $post_this, "Default: $text_default; Publication method: $type", $post_ID ); // DEBUG.
	if ( $send_update ) {
		$post_info       = wpt_post_info( $post_ID );
		$debug_post_info = $post_info;
		unset( $debug_post_info['post_content'] );
		unset( $debug_post_info['postContent'] );
		wpt_mail( '2: XPoster Post Info (post content omitted)', wpt_format_error( $debug_post_info ), $post_ID ); // DEBUG.
		/**
		 * Apply filters against this post to determine whether it should be allowed to be sent.
		 *
		 * @hook wpt_should_block_status
		 *
		 * @param {bool}  $filter Always false by default.
		 * @param {array} $post_info Array of post data.
		 *
		 * @return {bool} True to block post.
		 */
		$filter = apply_filters( 'wpt_should_block_status', false, $post_info );
		if ( true === $filter ) {
			wpt_mail( '3: Post blocked by XPoster Pro custom filters', 'No additional data available', $post_ID );

			return false;
		}
		/**
		 * Return true to block this post based on POST data. Default false.
		 *
		 * @hook wpt_filter_post_data
		 *
		 * @param {bool} $filter True if this post should not have a status update sent.
		 * @param {array} $post POST global.
		 *
		 * @return {bool}
		 */
		$filter = apply_filters( 'wpt_filter_post_data', false, $_POST );
		if ( $filter ) {
			return false;
		}
		if ( 'future' === $type || 'future' === get_post_meta( $post_ID, 'wpt_publishing', true ) ) {
			$new = 1; // if this is a future action, then it should be published regardless of relationship.
			wpt_mail( '4a: Post is a scheduled post', 'See Post Info data', $post_ID );
			delete_post_meta( $post_ID, 'wpt_publishing' );
		} else {
			// if the post modified date and the post date are the same, this is new.
			// true if first date before or equal to last date.
			$new = wpt_post_is_new( $post_info['_postModified'], $post_info['_postDate'] );
		}
		// post is not previously published but has been backdated.
		// (post date is edited, but save option is 'publish').
		if ( 0 === $new && ( isset( $_POST['edit_date'] ) && '1' === $_POST['edit_date'] && ! isset( $_POST['save'] ) ) ) {
			$new = 1;
		}
		// can't catch posts that were set to a past date as a draft, then published.
		$post_type          = $post_info['postType'];
		$post_type_settings = get_option( 'wpt_post_types' );
		if ( wpt_allowed_post_types( $post_type ) ) {
			// identify whether limited by category/taxonomy.
			$continue = wpt_category_limit( $post_type, $post_info, $post_ID );
			if ( false === $continue ) {
				wpt_mail( '4b: XPoster Pro: Limited by term filters', 'This post was limited by a taxonomy/term filter', $post_ID );
				return false;
			}
			// create status update and ID whether current action is edit or new.
			$ct = get_post_meta( $post_ID, '_jd_twitter', true );
			if ( isset( $_POST['_jd_twitter'] ) && ! empty( $_POST['_jd_twitter'] ) ) {
				$ct = sanitize_textarea_field( wp_unslash( $_POST['_jd_twitter'] ) );
			}
			$custom_tweet = ( '' !== $ct ) ? stripcslashes( trim( $ct ) ) : '';
			// if ops is set and equals 'publish', this is being edited. Otherwise, it's a new post.
			if ( 0 === $new ) {
				// if this is an old post and editing updates are enabled.
				if ( '1' === get_option( 'jd_tweet_default_edit' ) ) {
					/**
					 * Filter whether a post defaults to send updates on edit.
					 *
					 * @hook wpt_tweet_this_edit
					 *
					 * @param {string} $post_this 'yes' or 'no'.
					 * @param {array}  $_POST POST global.
					 * @param {int}    $post_ID Post ID.
					 *
					 * @return {string} 'yes' to continue with posting.
					 */
					$post_this = apply_filters( 'wpt_tweet_this_edit', $post_this, $_POST, $post_ID );
					if ( 'yes' !== $post_this ) {
						return false;
					}
				}
				wpt_mail( '4b: Post action is edit', 'This event was a post edit action.' . "\n" . 'Modified Date: ' . $post_info['_postModified'] . "\n\n" . 'Publication date:' . $post_info['_postDate'], $post_ID ); // DEBUG.
				if ( '1' === (string) $post_type_settings[ $post_type ]['post-edited-update'] || $post_this ) {
					$nptext = stripcslashes( $post_type_settings[ $post_type ]['post-edited-text'] );
					if ( ! $nptext ) {
						wpt_mail( '4b: Edited post template is empty.', 'Post Type: ' . $post_type, $post_ID ); // DEBUG.
					}

					$oldpost = true;
				}
			} else {
				wpt_mail( '4c: Post action is publish', 'This event was a post publish action.' . "\n" . 'Modified Date: ' . $post_info['_postModified'] . "\n\n" . 'Publication date:' . $post_info['_postDate'], $post_ID ); // DEBUG.
				if ( '1' === (string) $post_type_settings[ $post_type ]['post-published-update'] || $post_this ) {
					$nptext = stripcslashes( $post_type_settings[ $post_type ]['post-published-text'] );
					if ( ! $nptext ) {
						wpt_mail( '4c: Published post template is empty.', 'Post Type: ' . $post_type, $post_ID ); // DEBUG.
					}

					$newpost = true;
				}
			}
			if ( $newpost || $oldpost ) {
				$template = ( '' !== $custom_tweet ) ? $custom_tweet : $nptext;
			}
			if ( '' !== $template ) {
				/**
				 * Execute an action when a status update is executed.
				 *
				 * @hook wpt_post_to_service
				 *
				 * @param {int}      $post_ID Post ID.
				 * @param {array}    $post_info Array of post info for templates.
				 * @param {string}   $template Template in use.
				 * @param {bool}     $media Whether media should be included.
				 */
				do_action( 'wpt_post_to_service', $post_ID, $post_info, $template );
				wpt_post_to_service( $template, false, $post_ID );
			}
		}
	}

	return $post_ID;
}

/**
 *  Send updates on links in link manager. Only active if Link plug-in is installed.
 *
 * @param integer $link_id Database ID for link.
 *
 * @return mixed boolean/integer link ID if successful, false if failure.
 */
function wpt_post_update_link( $link_id ) {
	$bookmark        = get_bookmark( $link_id );
	$thislinkprivate = $bookmark->link_visible;
	if ( 'N' !== $thislinkprivate ) {
		$thislinkname        = $bookmark->link_name;
		$thispostlink        = $bookmark->link_url;
		$thislinkdescription = $bookmark->link_description;
		$sentence            = stripcslashes( get_option( 'newlink-published-text' ) );
		$sentence            = str_ireplace( '#title#', $thislinkname, $sentence );
		$sentence            = str_ireplace( '#description#', $thislinkdescription, $sentence );

		/**
		 * Customize the URL shortening of a link in the link manager.
		 *
		 * @hook wptt_shorten_link
		 *
		 * @param {string} $thispostlink The passed bookmark link.
		 * @param {string} $thislinkname The provided link title.
		 * @param {bool}   $post_ID False, because links don't have post IDs.
		 * @param {bool}   $test 'link' to indicate a link is being shortened.
		 *
		 * @return {string}
		 */
		$shrink = apply_filters( 'wptt_shorten_link', $thispostlink, $thislinkname, false, 'link' );
		if ( false === stripos( $sentence, '#url#' ) ) {
			$sentence = $sentence . ' ' . $shrink;
		} else {
			$sentence = str_ireplace( '#url#', $shrink, $sentence );
		}

		if ( false === stripos( $sentence, '#longurl#' ) ) {
			$sentence = $sentence . ' ' . $thispostlink;
		} else {
			$sentence = str_ireplace( '#longurl#', $thispostlink, $sentence );
		}

		if ( '' !== $sentence ) {
			wpt_post_to_service( $sentence, false, $link_id );
		}

		return $link_id;
	} else {
		return false;
	}
}

add_action( 'admin_menu', 'wpt_add_twitter_debug_box' );
/**
 * Set up post meta box.
 */
function wpt_add_twitter_debug_box() {
	if ( WPT_DEBUG && current_user_can( 'manage_options' ) ) {
		// add X.com panel to post types where it's enabled.
		$wpt_post_types = wpt_allowed_post_types();
		foreach ( $wpt_post_types as $type ) {
			add_meta_box( 'wp2t-debug', 'XPoster Debugging', 'wpt_show_debug', $type, 'advanced' );
		}
	}
}

add_action( 'admin_enqueue_scripts', 'wpt_admin_scripts', 10, 1 );
/**
 * Enqueue admin scripts for XPoster and XPoster PRO.
 */
function wpt_admin_scripts() {
	global $current_screen;
	$wpt_version   = XPOSTER_VERSION;
	$charcount_url = 'js/charcount.min.js';
	$ajax_url      = 'js/ajax.min.js';
	$base_url      = 'js/base.min.js';
	$tabs_url      = 'js/tabs.min.js';
	if ( SCRIPT_DEBUG ) {
		$wpt_version  .= '-' . wp_rand( 10000, 99999 );
		$charcount_url = 'js/charcount.js';
		$ajax_url      = 'js/ajax.js';
		$base_url      = 'js/base.js';
		$tabs_url      = 'js/tabs.js';
	}
	wp_register_script( 'wpt.charcount', plugins_url( $charcount_url, __FILE__ ), array( 'jquery' ), $wpt_version, true );
	if ( 'post' === $current_screen->base || 'xposter-pro_page_wp-to-twitter-schedule' === $current_screen->base ) {
		wp_enqueue_script( 'wpt.charcount' );
		wp_register_style( 'wpt-post-styles', plugins_url( 'css/post-styles.css', __FILE__ ), array(), $wpt_version );
		wp_enqueue_style( 'wpt-post-styles' );
		$config = wpt_max_length( false );
		// add one; character count starts from 1.
		if ( 'post' === $current_screen->base ) {
			$allowed = $config['base_length'] - mb_strlen( stripslashes( get_option( 'jd_twit_prepend' ) . get_option( 'jd_twit_append' ) ) ) + 1;
		} else {
			$allowed = $config['base_length'] + 1;
		}
		wp_register_script( 'wpt-base-js', plugins_url( $base_url, __FILE__ ), array( 'jquery', 'wpt.charcount' ), $wpt_version, true );
		wp_enqueue_script( 'wpt-base-js' );
		wp_localize_script(
			'wpt-base-js',
			'wptSettings',
			array(
				'allowed'        => $allowed,
				'x_limit'        => $config['x'],
				'mastodon_limit' => $config['mastodon'],
				'bluesky_limit'  => $config['bluesky'],
				'is_ssl'         => ( wpt_is_ssl( home_url() ) ) ? 'true' : 'false',
				'text'           => __( 'Characters left: ', 'wp-to-twitter' ),
				'updated'        => __( 'Custom status template updated', 'wp-to-twitter' ),
			)
		);
	}
	if ( 'post' === $current_screen->base && ( current_user_can( 'wpt_tweet_now' ) || current_user_can( 'manage_options' ) ) ) {
		global $post;
		// AJAX posting is only possible on published posts.
		if ( 'publish' === $post->post_status ) {
			wp_enqueue_script( 'wpt.ajax', plugins_url( $ajax_url, __FILE__ ), array( 'jquery' ), $wpt_version, true );
			wp_localize_script(
				'wpt.ajax',
				'wpt_data',
				array(
					'post_ID'  => $post->ID,
					'action'   => 'wpt_post_update',
					'security' => wp_create_nonce( 'wpt-tweet-nonce' ),
				)
			);
		}
	}
	if ( 'toplevel_page_wp-tweets-pro' === $current_screen->id ) {
		wp_enqueue_script( 'wpt.tabs', plugins_url( $tabs_url, __FILE__ ), array( 'jquery' ), $wpt_version, true );
		wp_localize_script(
			'wpt.tabs',
			'wpt',
			array(
				'firstItem' => 'wpt_post',
				'firstPerm' => 'wpt_editor',
			)
		);
		wp_enqueue_script( 'dashboard' );
	}
}

/**
 * Post the Custom Update & custom Update data into the post meta table
 *
 * @param integer $id Post ID.
 * @param object  $post Post object.
 *
 * @return bool
 */
function wpt_save_post( $id, $post ) {
	if ( empty( $_POST ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id ) || isset( $_POST['_inline_edit'] ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! wpt_in_post_type( $id ) ) {
		return $id;
	}
	if ( isset( $_POST['wp_to_twitter_meta'] ) ) {
		$nonce = ( isset( $_POST['wp_to_twitter_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wp_to_twitter_nonce'] ) ) : false;
		if ( ! ( $nonce && wp_verify_nonce( $nonce, 'wp-to-twitter-nonce' ) ) ) {
			wp_die( 'XPoster: Security check failed' );
		}
		if ( isset( $_POST['_yourls_keyword'] ) ) {
			$yourls = sanitize_text_field( wp_unslash( $_POST['_yourls_keyword'] ) );
			update_post_meta( $id, '_yourls_keyword', $yourls );
		}
		if ( isset( $_POST['_jd_twitter'] ) && '' !== $_POST['_jd_twitter'] ) {
			$twitter = sanitize_textarea_field( wp_unslash( $_POST['_jd_twitter'] ) );
			update_post_meta( $id, '_jd_twitter', $twitter );
		} elseif ( isset( $_POST['_jd_twitter'] ) && '' === $_POST['_jd_twitter'] ) {
			delete_post_meta( $id, '_jd_twitter' );
		}
		if ( isset( $_POST['_wpt_post_template_x'] ) && '' !== $_POST['_wpt_post_template_x'] ) {
			$template = sanitize_textarea_field( wp_unslash( $_POST['_wpt_post_template_x'] ) );
			update_post_meta( $id, '_wpt_post_template_x', $template );
		} elseif ( isset( $_POST['_wpt_post_template_x'] ) && '' === $_POST['_wpt_post_template_x'] ) {
			delete_post_meta( $id, '_wpt_post_template_x' );
		}
		if ( isset( $_POST['_wpt_post_template_bluesky'] ) && '' !== $_POST['_wpt_post_template_bluesky'] ) {
			$template = sanitize_textarea_field( wp_unslash( $_POST['_wpt_post_template_bluesky'] ) );
			update_post_meta( $id, '_wpt_post_template_bluesky', $template );
		} elseif ( isset( $_POST['_wpt_post_template_bluesky'] ) && '' === $_POST['_wpt_post_template_bluesky'] ) {
			delete_post_meta( $id, '_wpt_post_template_bluesky' );
		}
		if ( isset( $_POST['_wpt_post_template_mastodon'] ) && '' !== $_POST['_wpt_post_template_mastodon'] ) {
			$template = sanitize_textarea_field( wp_unslash( $_POST['_wpt_post_template_mastodon'] ) );
			update_post_meta( $id, '_wpt_post_template_mastodon', $template );
		} elseif ( isset( $_POST['_wpt_post_template_mastodon'] ) && '' === $_POST['_wpt_post_template_mastodon'] ) {
			delete_post_meta( $id, '_wpt_post_template_mastodon' );
		}
		if ( isset( $_POST['_jd_wp_twitter'] ) && '' !== $_POST['_jd_wp_twitter'] ) {
			$wp_twitter = sanitize_textarea_field( wp_unslash( $_POST['_jd_wp_twitter'] ) );
			update_post_meta( $id, '_jd_wp_twitter', $wp_twitter );
		}
		if ( isset( $_POST['_wpt_post_this'] ) ) {
			$post_this = ( 'no' === $_POST['_wpt_post_this'] ) ? 'no' : 'yes';
			update_post_meta( $id, '_wpt_post_this', $post_this );
		} else {
			$post_default = ( '1' === get_option( 'jd_tweet_default' ) ) ? 'no' : 'yes';
			update_post_meta( $id, '_wpt_post_this', $post_default );
		}
		$omit_services = ( isset( $_POST['_wpt_omit_services'] ) ) ? $_POST['_wpt_omit_services'] : array();
		$services      = wpt_check_connections( false, true );
		$omit          = array();
		// The interface has you choose what you want; the DB represents what's omitted.
		foreach ( array_keys( $services ) as $service ) {
			if ( ! in_array( $service, $omit_services, true ) ) {
				$omit[] = $service;
			}
		}
		update_post_meta( $id, '_wpt_omit_services', $omit );
		if ( isset( $_POST['wpt_clear_history'] ) && 'clear' === $_POST['wpt_clear_history'] ) {
			delete_post_meta( $id, '_wpt_failed' );
			delete_post_meta( $id, '_jd_wp_twitter' );
			delete_post_meta( $id, '_wpt_short_url' );
			delete_post_meta( $id, '_wp_jd_twitter' );
		}
		/**
		 * Runs when post data is inserted.
		 *
		 * @hook wpt_insert_post
		 *
		 * @param {array} $_POST Unaltered POST data.
		 * @param {int}   $id Post ID
		 */
		do_action( 'wpt_insert_post', $_POST, $id );
		// only send debug data if post meta is updated.
		wpt_mail( 'Post Meta Processed', 'XPoster post meta was updated' . "\n\n" . wpt_format_error( map_deep( $_POST, 'sanitize_textarea_field' ) ), $id ); // DEBUG.

		if ( isset( $_POST['wpt-delete-debug'] ) && 'true' === $_POST['wpt-delete-debug'] ) {
			delete_post_meta( $id, '_wpt_debug_log' );
		}
		if ( isset( $_POST['wpt-delete-all-debug'] ) && 'true' === $_POST['wpt-delete-all-debug'] ) {
			delete_post_meta_by_key( '_wpt_debug_log' );
		}
	}
	return $id;
}

add_action( 'init', 'wpt_old_admin_redirect' );
/**
 * Send links to old admin to new admin page
 */
function wpt_old_admin_redirect() {
	if ( is_admin() && isset( $_GET['page'] ) && 'wp-to-twitter/wp-to-twitter.php' === $_GET['page'] ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( admin_url( 'admin.php?page=wp-tweets-pro' ) );
		exit;
	}
}

add_action( 'admin_menu', 'wpt_admin_page' );
/**
 * Add the administrative settings to the "Settings" menu.
 */
function wpt_admin_page() {
	if ( function_exists( 'add_menu_page' ) && ! function_exists( 'wpt_pro_functions' ) ) {
		add_menu_page( 'XPoster', 'XPoster', 'manage_options', 'wp-tweets-pro', 'wpt_update_settings', 'dashicons-share' );
	}
}

add_action( 'admin_head', 'wpt_admin_style' );
/**
 * Add stylesheets to XPoster pages.
 */
function wpt_admin_style() {
	$wpt_version = XPOSTER_VERSION;
	if ( SCRIPT_DEBUG ) {
		$wpt_version .= '-' . wp_rand( 10000, 99999 );
	}
	global $current_screen;
	$enqueues = array(
		'toplevel_page_wp-tweets-pro',
		'xposter-pro_page_wp-to-twitter-schedule',
		'xposter-pro_page_wp-to-twitter-tweets',
		'xposter-pro_page_wp-to-twitter-errors',
		'profile',
		'user-edit',
	);
	if ( in_array( $current_screen->base, $enqueues, true ) ) {
		wp_enqueue_style( 'wpt-styles', plugins_url( 'css/styles.css', __FILE__ ), array(), $wpt_version );
	}
}

/**
 * Add XPoster links to plug-in information.
 *
 * @param array  $links Array of links.
 * @param string $file Current file name.
 *
 * @return link new array.
 */
function wpt_plugin_action( $links, $file ) {
	if ( plugin_basename( __DIR__ . '/wp-to-twitter.php' ) === $file || 'wp-to-twitter/init.php' === $file ) {
		$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
		$links[]   = "<a href='$admin_url'>" . __( 'XPoster Settings', 'wp-to-twitter' ) . '</a>';
		if ( ! function_exists( 'wpt_pro_exists' ) ) {
			$links[] = "<strong><a href='https://xposterpro.com/awesome/xposter-pro/'>Get XPoster Pro</a></strong>";
		}
	}

	return $links;
}
add_filter( 'plugin_action_links', 'wpt_plugin_action', 10, 2 );

/**
 * Parse plugin update info to display in update list.
 */
function wpt_plugin_update_message() {
	define( 'WPT_PLUGIN_README_URL', 'http://svn.wp-plugins.org/wp-to-twitter/trunk/readme.txt' );
	$response = wp_remote_get( WPT_PLUGIN_README_URL, array( 'user-agent' => 'WordPress/XPoster ' . XPOSTER_VERSION . '; ' . get_bloginfo( 'url' ) ) );
	if ( ! is_wp_error( $response ) || is_array( $response ) ) {
		$data   = $response['body'];
		$bits   = explode( '== Upgrade Notice ==', $data );
		$notice = trim( str_replace( '* ', '', nl2br( trim( $bits[1] ) ) ) );
		if ( $notice ) {
			?>
			</div><div id="wpt-upgrade" class="notice inline notice-warning"><ul><li><strong style="color:#c22;">Upgrade Notes:</strong> ' . esc_html( str_replace( '* ', '', nl2br( trim( $bits[1] ) ) ) ) . '</li></ul>
			<?php
		}
	}
}
add_action( 'in_plugin_update_message-wp-to-twitter/wp-to-twitter.php', 'wpt_plugin_update_message' );

if ( '1' === get_option( 'jd_twit_blogroll' ) ) {
	add_action( 'add_link', 'wpt_post_update_link' );
}

if ( function_exists( 'wp_after_insert_post' ) ) {
	/**
	 * Use the `wp_after_insert_post` action to run Updates.
	 *
	 * @since WordPress 5.6
	 */
	add_action( 'wp_after_insert_post', 'wpt_save_post', 10, 2 );
	add_action( 'wp_after_insert_post', 'wpt_do_post_update', 15, 4 );
} else {
	add_action( 'save_post', 'wpt_save_post', 10, 2 );
	add_action( 'save_post', 'wpt_do_post_update', 15 );
}
/**
 * Check whether a given post is in an allowed post type and has an update template configured.
 *
 * @param integer $id Post ID.
 *
 * @return boolean True if post is allowed, false otherwise.
 */
function wpt_in_post_type( $id ) {
	$post_types = wpt_allowed_post_types();
	$type       = get_post_type( $id );
	if ( in_array( $type, $post_types, true ) ) {
		return true;
	}
	if ( WPT_DEBUG ) {
		wpt_mail( '0: Not an updated post type', 'This post type is not enabled for status updates: ' . $type, $id );
	}

	return false;
}

/**
 * Get array of post types that can be updated.
 *
 * @param string|bool $post_type Name of post type to check if a specific type is allowed or false to return array.
 *
 * @return array|bool
 */
function wpt_allowed_post_types( $post_type = false ) {
	$post_type_settings = get_option( 'wpt_post_types' );
	$post_types         = array_keys( $post_type_settings );
	if ( $post_type ) {
		return in_array( $post_type, $post_types, true ) ? true : false;
	}
	$allowed_types = array();
	if ( is_array( $post_type_settings ) && ! empty( $post_type_settings ) ) {
		foreach ( $post_type_settings as $type => $settings ) {
			if ( '1' === (string) $settings['post-edited-update'] || '1' === (string) $settings['post-published-update'] ) {
				$allowed_types[] = $type;
			}
		}
	}

	/**
	 * Return array of post types that can be sent as status updates.
	 *
	 * @hook wpt_allowed_post_types
	 * @param {array} $types Array of post type names enabled for status updates either when editing or publishing.
	 * @param {array} $post_type_settings Multidimensional array of post types and post type settings.
	 *
	 * @return {array}
	 */
	return apply_filters( 'wpt_allowed_post_types', $allowed_types, $post_type_settings );
}

add_action( 'future_to_publish', 'wpt_future_to_publish', 16 );
/**
 * Handle updating posts scheduled for the future.
 *
 * @param object $post Post object.
 */
function wpt_future_to_publish( $post ) {
	$id = $post->ID;
	if ( wp_is_post_autosave( $id ) || wp_is_post_revision( $id ) || ! wpt_in_post_type( $id ) ) {
		return;
	}
	wpt_mail( 'Transitioning future to publish', $id );
	wpt_post_update_future( $id );
}

/**
 * Check whether autotweeting has been allowed.
 *
 * @param int $post_id Post ID.
 *
 * @return bool
 */
function wpt_auto_tweet_allowed( $post_id ) {
	$state  = get_option( 'wpt_auto_tweet_allowed', '0' );
	$return = ( '0' !== $state ) ? true : false;

	/**
	 * Return true if auto tweeting of old posts is enabled.
	 *
	 * @hook wpt_auto_tweet_allowed
	 * @param {bool} $return true if enabled.
	 * @param {int}  $post_id Post ID.
	 *
	 * @return {bool}
	 */
	return apply_filters( 'wpt_auto_tweet_allowed', $return, $post_id );
}

/**
 * Handle updating posts published directly. As of 12/10/2020, supports new wp_after_insert_post to improve support when used with block editor.
 *
 * @param int     $id Post ID.
 * @param object  $post Post object.
 * @param boolean $updated True if updated, false if inserted.
 * @param object  $post_before The post prior to this update, or null for new posts.
 */
function wpt_do_post_update( $id, $post = null, $updated = null, $post_before = null ) {
	if ( ( empty( $_POST ) && ! wpt_auto_tweet_allowed( $id ) ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id ) || isset( $_POST['_inline_edit'] ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! wpt_auto_tweet_allowed( $id ) ) || ! wpt_in_post_type( $id ) ) {
		return $id;
	}

	$post = ( null === $post ) ? get_post( $id ) : $post;
	if ( 'publish' !== $post->post_status ) {
		return $id;
	}
	wpt_post_update_instant( $id, $post, $updated, $post_before );
}
add_action( 'xmlrpc_publish_post', 'wpt_post_update_xmlrpc' );
add_action( 'publish_phone', 'wpt_post_update_xmlrpc' );

/**
 * For future posts, check transients to see whether this post has already been published. Prevents duplicate status update attempts in older versions of WP.
 *
 * @param integer $id Post ID.
 */
function wpt_post_update_future( $id ) {
	set_transient( '_wpt_post_update_future', $id, 10 );
	// instant action has already run for this post.
	// prevent running actions twice (need both for older WP).
	if ( get_transient( '_wpt_post_update_instant' ) && (int) get_transient( '_wpt_twit_instant' ) === $id ) {
		delete_transient( '_wpt_post_update_instant' );

		return;
	}

	wpt_post_update( $id, 'future' );
}

/**
 * For immediate posts, check transients to see whether this post has already been published. Prevents duplicate status update attempts in older versions of WP or cases where a future action is being run after the initial action.
 *
 * @param int     $id Post ID.
 * @param object  $post Post object.
 * @param boolean $updated True if updated, false if inserted.
 * @param object  $post_before The post prior to this update, or null for new posts.
 */
function wpt_post_update_instant( $id, $post, $updated, $post_before ) {
	set_transient( '_wpt_twit_instant', $id, 10 );
	// future action has already run for this post.
	if ( get_transient( '_wpt_twit_future' ) && (int) get_transient( '_wpt_twit_future' ) === $id ) {
		delete_transient( '_wpt_twit_future' );

		return;
	}
	// xmlrpc action has already run for this post.
	if ( get_transient( '_wpt_twit_xmlrpc' ) && (int) get_transient( '_wpt_twit_xmlrpc' ) === $id ) {
		delete_transient( '_wpt_twit_xmlrpc' );

		return;
	}

	wpt_post_update( $id, 'instant', $post, $updated, $post_before );
}

/**
 * Status updates on XMLRPC posts.
 *
 * @param integer $id Post ID.
 *
 * @return post ID.
 */
function wpt_post_update_xmlrpc( $id ) {
	set_transient( '_wpt_twit_xmlrpc', $id, 10 );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $id ) || ! wpt_in_post_type( $id ) ) {
		return $id;
	}
	wpt_mail( 'Status update sent on XMLRPC published post', $id );
	wpt_post_update( $id, 'xmlrpc' );
	return $id;
}

add_action( 'admin_notices', 'wpt_debugging_enabled', 10 );
/**
 * Show notice if X.com debugging is enabled.
 */
function wpt_debugging_enabled() {
	if ( current_user_can( 'manage_options' ) && WPT_DEBUG ) {
		$message = __( '<strong>XPoster</strong> debugging is enabled. Remember to disable debugging when you are finished.', 'wp-to-twitter' );
		wp_admin_notice(
			$message,
			array(
				'type' => 'error',
			)
		);
	}
}

add_action( 'admin_notices', 'wpt_needs_bearer_token', 10 );
/**
 * Notify users if they need to add a bearer token for XPoster.
 */
function wpt_needs_bearer_token() {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'wpt_twitter_oauth' ) ) {
		$screen = get_current_screen();
		if ( 'profile' === $screen->id && get_option( 'jd_individual_twitter_users' ) ) {
			$auth       = wp_get_current_user()->ID;
			$authorized = wtt_oauth_test( $auth );
			$bt         = get_user_meta( $auth, 'bearer_token', true );
		} else {
			$auth       = false;
			$authorized = wtt_oauth_test();
			$bt         = get_option( 'bearer_token', '' );
		}
		if ( ! $bt && $authorized ) {
			if ( $auth && get_option( 'jd_individual_twitter_users' ) ) {
				$message = __( '<strong>XPoster</strong> needs a Bearer Token added to your profile settings to support the X.com API.', 'wp-to-twitter' );
				wp_admin_notice(
					$message,
					array(
						'type' => 'error',
					)
				);
			} elseif ( current_user_can( 'manage_options' ) ) {
				// Translators: URL to connection settings.
				$message = sprintf( __( '<strong>XPoster</strong> needs a Bearer Token added to the <a href="%s">connection settings</a> to support the X.com API.', 'wp-to-twitter' ), admin_url( 'admin.php?page=wp-tweets-pro&tab=connection' ) );
				wp_admin_notice(
					$message,
					array(
						'type' => 'error',
					)
				);
			}
		}
	}
}

/**
 * Check connections.
 *
 * @param int|bool $auth User ID or false to check primary connection.
 * @param bool     $get_connections True to return an array with the valid connections. Default false.
 *
 * @return bool|array
 */
function wpt_check_connections( $auth = false, $get_connections = false ) {
	$connected = false;
	if ( ! $get_connections ) {
		$connected = ( wtt_oauth_test( $auth, 'verify' ) || wpt_mastodon_connection( $auth ) || wpt_bluesky_connection( $auth ) ) ? true : false;
	} else {
		$connected = array(
			'x'        => wpt_oauth_connection( $auth ),
			'mastodon' => wpt_mastodon_connection( $auth ),
			'bluesky'  => wpt_bluesky_connection( $auth ),
		);
	}

	return $connected;
}

/**
 * Dismiss the missing connection notice.
 */
function wpt_dismiss_connection() {
	global $current_screen;
	if ( $current_screen && 'toplevel_page_wp-tweets-pro' === $current_screen->id ) {
		$nonce   = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : false;
		$verify  = wp_verify_nonce( $nonce, 'wpt-dismiss' );
		$dismiss = isset( $_GET['dismiss'] ) && 'connection' === $_GET['dismiss'] ? true : false;
		if ( $verify && $dismiss ) {
			update_option( 'wpt_ignore_connection', 'true' );
		}
	}
}
add_action( 'admin_init', 'wpt_dismiss_connection' );

/**
 * Display notices if update services are not connected.
 */
function wpt_needs_connection() {
	global $current_screen;
	if ( 'toplevel_page_wp-tweets-pro' === $current_screen->id && ! 'true' === get_option( 'wpt_ignore_connection' ) ) {
		$message  = '';
		$mastodon = wpt_mastodon_connection();
		$x        = wpt_check_oauth();
		$bluesky  = wpt_bluesky_connection();
		// show notification to authenticate with Mastodon.
		if ( ! $mastodon ) {
			$admin_url = admin_url( 'admin.php?page=wp-tweets-pro&tab=mastodon' );
			// Translators: Settings page to authenticate Mastodon.
			$message .= '<li>' . sprintf( __( "Mastodon requires authentication. <a href='%s'>Update your Mastodon settings</a> to enable XPoster to send updates to Mastodon.", 'wp-to-twitter' ), $admin_url ) . '</li>';
		}
		// show notification to authenticate with OAuth.
		if ( ! $x ) {
			$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
			// Translators: Settings page to authenticate X.com.
			$message .= '<li>' . sprintf( __( "X.com requires authentication by OAuth. <a href='%s'>Update your X settings</a> to enable XPoster to send updates to X.com.", 'wp-to-twitter' ), $admin_url ) . '</li>';
		}
		// show notification to authenticate with Bluesky.
		if ( ! $bluesky ) {
			$admin_url = admin_url( 'admin.php?page=wp-tweets-pro&tab=bluesky' );
			// Translators: Settings page to authenticate Bluesky.
			$message .= '<li>' . sprintf( __( "Bluesky requires authentication. <a href='%s'>Update your Bluesky settings</a> to enable XPoster to send updates to Bluesky.", 'wp-to-twitter' ), $admin_url ) . '</li>';
		}
		$message        = ( $message ) ? '<ul>' . $message . '</ul>' : '';
		$is_dismissible = '';
		$class          = 'xposter-connection';
		if ( $x || $mastodon || $bluesky ) {
			$class          = 'xposter-connection dismissible';
			$args           = array(
				'dismiss'  => 'connection',
				'_wpnonce' => wp_create_nonce( 'wpt_dismiss' ),
			);
			$dismiss_url    = add_query_arg( $args, admin_url( 'admin.php?page=wp-tweets-pro' ) );
			$is_dismissible = ' <a href="' . esc_url( $dismiss_url ) . '" class="button button-secondary">' . __( 'Ignore', 'wp-to-twitter' ) . '</a>';
		}
		if ( $message ) {
			wp_admin_notice(
				"$message $is_dismissible",
				array(
					'type'               => 'error',
					'additional_classes' => array( $class ),
				)
			);
		}
	}
}
add_action( 'admin_notices', 'wpt_needs_connection' );

/**
 * Get service SVG.
 *
 * @param string $service Service name.
 *
 * @return string Url.
 */
function wpt_get_svg( $service ) {
	return plugins_url( 'images/' . $service . '.svg', __FILE__ );
}
