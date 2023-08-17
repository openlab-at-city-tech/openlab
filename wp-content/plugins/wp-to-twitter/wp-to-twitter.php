<?php
/**
 * WP to Twitter
 *
 * @package     WP to Twitter
 * @author      Joe Dolson
 * @copyright   2008-2023 Joe Dolson
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WP to Twitter
 * Plugin URI:  http://www.joedolson.com/wp-to-twitter/
 * Description: Posts a Tweet when you update your WordPress blog or post a link, using your URL shortener. Many options to customize and promote your Tweets.
 * Author:      Joseph C Dolson
 * Author URI:  http://www.joedolson.com
 * Text Domain: wp-to-twitter
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/license/gpl-2.0.txt
 * Domain Path: lang
 * Version:     3.7.0
 */

/*
	Copyright 2008-2023  Joe Dolson (email : joe@joedolson.com)

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

require_once( plugin_dir_path( __FILE__ ) . 'wpt-functions.php' );
require_once( plugin_dir_path( __FILE__ ) . 'wp-to-twitter-users.php' );
require_once( plugin_dir_path( __FILE__ ) . 'wp-to-twitter-oauth.php' );
require_once( plugin_dir_path( __FILE__ ) . 'wp-to-twitter-shorteners.php' );
require_once( plugin_dir_path( __FILE__ ) . 'wp-to-twitter-manager.php' );
require_once( plugin_dir_path( __FILE__ ) . 'wpt-truncate.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/class-wpt-twitterfeed.php' );
require_once( plugin_dir_path( __FILE__ ) . 'wpt-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'wpt-rate-limiting.php' );

global $wpt_version;
$wpt_version = '3.7.0';

add_action( 'init', 'wpt_load_textdomain' );
/**
 * Set up text domain for WP to Twitter.
 */
function wpt_load_textdomain() {
	load_plugin_textdomain( 'wp-to-twitter' );
}

add_action( 'widgets_init', 'wpt_register_widgets' );
/**
 * Register WP to Twitter Widgets
 */
function wpt_register_widgets() {
	register_widget( 'WPT_Latest_Tweets_Widget' );
	register_widget( 'WPT_Search_Tweets_Widget' );
}

/**
 * Check for OAuth configuration
 *
 * @param mixed int/boolean $auth Which account to check.
 *
 * @return boolean Whether authorized.
 */
function wpt_check_oauth( $auth = false ) {
	if ( ! function_exists( 'wtt_oauth_test' ) ) {
		$oauth = false;
	} else {
		$oauth = wtt_oauth_test( $auth );
	}

	return $oauth;
}

/**
 * Check whether version requires activation.
 */
function wpt_check_version() {
	global $wpt_version;
	$prev_version = ( '' !== get_option( 'wp_to_twitter_version', '' ) ) ? get_option( 'wp_to_twitter_version' ) : '1.0.0';
	if ( version_compare( $prev_version, $wpt_version, '<' ) ) {
		wptotwitter_activate();
	}
}

/**
 * Activate WP to Twitter.
 */
function wptotwitter_activate() {
	// If this has never run before, do the initial setup.
	$new_install = ( '1' === get_option( 'wpt_twitter_setup' ) || '1' === get_option( 'twitterInitialised' ) ) ? false : true;
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
		update_option( 'jd_shortener', '1' );
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

		update_option( 'jd_post_excerpt', 30 );
		// Use Google Analytics with Twitter.
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

	global $wpt_version;
	$prev_version  = get_option( 'wp_to_twitter_version' );
	$administrator = get_role( 'administrator' );
	$upgrade       = version_compare( $prev_version, '2.9.0', '<' );
	if ( $upgrade ) {
		$administrator->add_cap( 'wpt_tweet_now' );
	}
	$upgrade = version_compare( $prev_version, '3.4.4', '<' );
	if ( $upgrade ) {
		delete_option( 'bitlyapi' );
		delete_option( 'bitlylogin' );
	}

	update_option( 'wp_to_twitter_version', $wpt_version );
}

/**
 * Function checks for an alternate URL to be Tweeted. Contribution by Bill Berry.
 *
 * @param int $post_ID Post ID.
 *
 * @return Link to use for this URL.
 */
function wpt_link( $post_ID ) {
	$ex_link       = false;
	$external_link = get_option( 'jd_twit_custom_url', '' );
	$permalink     = get_permalink( $post_ID );
	if ( '' !== $external_link ) {
		$ex_link = get_post_meta( $post_ID, $external_link, true );
	}

	return ( $ex_link ) ? $ex_link : $permalink;
}

/**
 * Save error messages for Tweets.
 *
 * @param int    $id Post ID.
 * @param int    $auth Current author.
 * @param string $twit Tweet text.
 * @param string $error Error string from Twitter.
 * @param int    $http_code Http code from Tiwtter.
 * @param string $ts Current timestamp.
 */
function wpt_saves_error( $id, $auth, $twit, $error, $http_code, $ts ) {
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
 * Checks whether WP to Twitter has sent a tweet on this post to this author within the last 30 seconds and blocks duplicates.
 *
 * @param int $id Post ID.
 * @param int $auth Author.
 *
 * @uses filter wpt_recent_tweet_threshold
 * @return boolean true to send Tweet, false to block.
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
			 * Modify the expiration window for recent Tweets. This value does flood control, to prevent a runaway process from sending multiple Tweets. Default `30` seconds.
			 *
			 * @hook wpt_recent_tweet_threshold
			 * @param {int} $expire Integer representing seconds. How long the transient will exist.
			 *
			 * @return {int}
			 */
			$expire = apply_filters( 'wpt_recent_tweet_threshold', 30 );
			// if expiration is 0, don't set the transient. We don't want permanent transients.
			if ( 0 !== $expire ) {
				wpt_mail( 'Tweet transient set', "$expire / $auth / $id", $id );
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
 * Performs the API post to Twitter
 *
 * @param string  $twit Text of Tweet to be sent to Twitter.
 * @param int     $auth Author ID.
 * @param int     $id Post ID.
 * @param boolean $media Whether to upload media attached to the post specified in $id.
 *
 * @return boolean Success of query.
 */
function wpt_post_to_twitter( $twit, $auth = false, $id = false, $media = false ) {
	$http_code = 0;
	// If an ID is set but the post is not currently present or published, ignore.
	if ( $id ) {
		$status = get_post_status( $id );
		if ( ! $status || 'publish' !== $status ) {
			$error = __( 'This post is no longer published or has been deleted', 'wp-to-twitter' );
			wpt_saves_error( $id, $auth, $twit, $error, '404', time() );
			wpt_set_log( 'wpt_status_message', $id, $error );

			return false;
		}
	}
	$error = false;
	if ( '1' === get_option( 'wpt_rate_limiting' ) ) {
		// check whether this post needs to be rate limited.
		$continue = wpt_test_rate_limit( $id, $auth );
		if ( ! $continue ) {
			wpt_mail( 'This post was blocked by WP to Twitter rate limiting.', 'Post ID: ' . $id . '; Account: ' . $auth );

			return false;
		}
	}

	$recent = wpt_check_recent_tweet( $id, $auth );
	if ( $recent ) {
		wpt_mail( 'This post was just Tweeted, and this is a duplicate.', 'Post ID: ' . $id . '; Account: ' . $auth );

		return false;
	}

	if ( ! wpt_check_oauth( $auth ) ) {
		$error = __( 'This account is not authorized to post to Twitter.', 'wp-to-twitter' );
		wpt_saves_error( $id, $auth, $twit, $error, '401', time() );
		wpt_set_log( 'wpt_status_message', $id, $error );
		wpt_mail( 'Account not authorized with Twitter', 'Post ID: ' . $id );

		return false;
	} // exit silently if not authorized.

	$check = ( ! $auth ) ? get_option( 'jd_last_tweet', '' ) : get_user_meta( $auth, 'wpt_last_tweet', true ); // get user's last tweet.
	// prevent duplicate Tweets.
	if ( $check === $twit && '' !== $twit ) {
		wpt_mail( 'Matched: tweet identical', "This Tweet: $twit; Check Tweet: $check; $auth, $id, $media", $id ); // DEBUG.
		$error = __( 'This tweet is identical to another Tweet recently sent to this account.', 'wp-to-twitter' ) . ' ' . __( 'Twitter requires all Tweets to be unique.', 'wp-to-twitter' );
		wpt_saves_error( $id, $auth, $twit, $error, '403-1', time() );
		wpt_set_log( 'wpt_status_message', $id, $error );

		return false;
	} elseif ( '' === $twit || ! $twit ) {
		wpt_mail( 'Tweet check: empty sentence', "$twit, $auth, $id, $media", $id ); // DEBUG.
		$error = __( 'This tweet was blank and could not be sent to Twitter.', 'wp-to-twitter' );
		wpt_saves_error( $id, $auth, $twit, $error, '403-2', time() );
		wpt_set_log( 'wpt_status_message', $id, $error );

		return false;
	} else {
		$media_id = false;
		// must be designated as media and have a valid attachment.
		$attachment = ( $media ) ? wpt_post_attachment( $id ) : false;
		if ( $attachment ) {
			wpt_mail( 'Post has upload', "$auth, $attachment", $id );
			$meta = wp_get_attachment_metadata( $attachment );
			if ( ! isset( $meta['width'], $meta['height'] ) ) {
				wpt_mail( "Image Data Does not Exist for #$attachment", print_r( $meta, 1 ), $id );
				$attachment = false;
			}
		}
		$api        = 'https://api.twitter.com/1.1/statuses/update.json';
		$api2       = 'https://api.twitter.com/2/tweets'; // For testing.
		$upload_api = 'https://upload.twitter.com/1.1/media/upload.json';
		$status     = array(
			'status'           => $twit,
			'source'           => 'wp-to-twitter',
			'include_entities' => 'true',
		);

		if ( wtt_oauth_test( $auth ) ) {
			$connection = wpt_oauth_connection( $auth );
			if ( $connection ) {
				if ( $media && $attachment && ! $media_id ) {
					$media_id = $connection->media(
						$upload_api,
						array(
							'auth'  => $auth,
							'media' => $attachment,
						)
					);
					wpt_mail( 'Media Uploaded', "$auth, $media_id, $attachment", $id );
					if ( $media_id ) {
						$status['media_ids'] = $media_id;

					}
				}
			}
		}
		if ( empty( $connection ) ) {
			$connection = array( 'connection' => 'undefined' );
		} else {

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
				$do_tweet = apply_filters( 'wpt_do_tweet', true, $auth, $id, $twit );
				if ( $do_tweet ) {
					$connection->post( $api, $status );
					$http_code = ( $connection ) ? (int) $connection->http_code : 'failed';
					$notice    = '';
				} else {
					$http_code = '000';
					$notice    = __( 'Tweet Canceled by custom filter.', 'wp-to-twitter' );
				}
			}
		}
		wpt_mail( 'Twitter Connection', print_r( $connection, 1 ) . " - $twit, $auth, $id, $media", $id );
		if ( $connection ) {
			if ( isset( $connection->http_header['x-access-level'] ) && 'read' === $connection->http_header['x-access-level'] ) {
				// Translators: Twitter App editing URL.
				$supplement = sprintf( __( 'Your Twitter application does not have read and write permissions. Go to <a href="%s">your Twitter apps</a> to modify these settings.', 'wp-to-twitter' ), 'https://developer.twitter.com/en/portal/projects-and-apps' );
			} else {
				$supplement = '';
			}
			$return = false;
			switch ( $http_code ) {
				case '000':
					$error = '';
					break;
				case 100:
					$error = __( '100 Continue: Twitter received the header of your submission, but your server did not follow through by sending the body of the data.', 'wp-to-twitter' );
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
					$error = __( '400 Bad Request: The request was invalid. This is the status code returned during rate limiting.', 'wp-to-twitter' );
					break;
				case 401:
					$error = __( '401 Unauthorized: Authentication credentials were missing or incorrect.', 'wp-to-twitter' );
					update_option( 'wpt_authentication_missing', "$auth" );
					break;
				case 403:
					$error = __( '403 Forbidden: The request is understood, but has been refused by Twitter.', 'wp-to-twitter' );
					break;
				case 404:
					$error = __( '404 Not Found: The URI requested is invalid or the resource requested does not exist.', 'wp-to-twitter' );
					break;
				case 406:
					$error = __( '406 Not Acceptable: Invalid Format Specified.', 'wp-to-twitter' );
					break;
				case 422:
					$error = __( '422 Unprocessable Entity: The image uploaded could not be processed.', 'wp-to-twitter' );
					break;
				case 429:
					$error = __( '429 Too Many Requests: You have exceeded your rate limits.', 'wp-to-twitter' );
					break;
				case 500:
					$error = __( '500 Internal Server Error: Something is broken at Twitter.', 'wp-to-twitter' );
					break;
				case 502:
					$error = __( '502 Bad Gateway: Twitter is down or being upgraded.', 'wp-to-twitter' );
					break;
				case 503:
					$error = __( '503 Service Unavailable: The Twitter servers are up, but overloaded with requests - Please try again later.', 'wp-to-twitter' );
					break;
				case 504:
					$error = __( "504 Gateway Timeout: The Twitter servers are up, but the request couldn't be serviced due to some failure within our stack. Try again later.", 'wp-to-twitter' );
					break;
				default:
					// Translators: http code.
					$error = sprintf( __( '<strong>Code %s</strong>: Twitter did not return a recognized response code.', 'wp-to-twitter' ), $http_code );
					break;
			}
			$body             = $connection->body;
			$error_code       = ( 200 !== $http_code ) ? $body->errors[0]->code : '';
			$error_message    = ( 200 !== $http_code ) ? $body->errors[0]->message : '';
			$error_supplement = ( '' !== (string) $error_code ) ? ' (Error Code: ' . $error_code . ': ' . $error_message . ')' : '';
			$error           .= ( '' !== $supplement ) ? " $supplement" : '';
			$error           .= $error_supplement;
			wpt_mail( "Twitter Response: $http_code", "$error", $id ); // DEBUG.
			// only save last Tweet if successful.
			if ( 200 === $http_code ) {
				if ( ! $auth ) {
					update_option( 'jd_last_tweet', $twit );
				} else {
					update_user_meta( $auth, 'wpt_last_tweet', $twit );
				}
			}
			wpt_saves_error( $id, $auth, $twit, $error, $http_code, time() );
			if ( 200 === $http_code ) {
				$jwt = get_post_meta( $id, '_jd_wp_twitter', true );
				if ( ! is_array( $jwt ) ) {
					$jwt = array();
				}
				$jwt[] = urldecode( $twit );
				if ( empty( $_POST ) ) {
					$_POST = array();
				}
				$_POST['_jd_wp_twitter'] = $jwt;
				update_post_meta( $id, '_jd_wp_twitter', $jwt );
			}
			if ( ! $return ) {
				/**
				 * Executes an action after posting a Tweet fails.
				 *
				 * @hook wpt_tweet_failed
				 *
				 * @since 3.6.0
				 *
				 * @param {object} $connection The current OAuth connection.
				 * @param {int}    $id Post ID for Tweeted post.
				 * @param {string} $error Error message returned.
				 */
				do_action( 'wpt_tweet_failed', $connection, $id, $error );
				wpt_set_log( 'wpt_status_message', $id, $error );
			} else {
				/**
				 * Executes an action after a Tweet is posted successfully.
				 *
				 * @hook wpt_tweet_posted
				 *
				 * @param {object} $connection The current OAuth connection.
				 * @param {int}    $id Post ID for Tweeted post.
				 */
				do_action( 'wpt_tweet_posted', $connection, $id );
				// Log the Tweet ID of the first Tweet for this post.
				$has_tweet_id = get_post_meta( $id, '_wpt_tweet_id', true );
				if ( ! $has_tweet_id ) {
					update_post_meta( $id, '_wpt_tweet_id', $connection->body->id );
				}
				wpt_set_log( 'wpt_status_message', $id, $notice . __( 'Tweet sent successfully.', 'wp-to-twitter' ) );
			}

			return $return;
		} else {
			wpt_set_log( 'wpt_status_message', $id, __( 'No Twitter OAuth connection found.', 'wp-to-twitter' ) );

			return false;
		}
	}
}

/**
 * For servers without PEAR normalize installed, approximates normalization. With normalizer, executes normalization on string.
 *
 * @param string $string Text to normalize.
 *
 * @return string Normalized text.
 */
function wpt_normalize( $string ) {
	if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) && function_exists( 'normalizer_normalize' ) ) {
		if ( normalizer_is_normalized( $string ) ) {
			return $string;
		}

		return normalizer_normalize( $string );
	} else {
		$normalizer = new WPT_Normalizer();
		if ( $normalizer->is_normalized( $string ) ) {
			return $string;
		}

		return $normalizer->normalize( $string );
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
 * Builds array of post info for use in Tweet functions.
 *
 * @param integer $post_ID Post ID.
 *
 * @return array Post data used in Tweet functions.
 */
function wpt_post_info( $post_ID ) {
	$encoding = get_option( 'blog_charset', '' );
	if ( '' === $encoding ) {
		$encoding = 'UTF-8';
	}
	$post         = get_post( $post_ID );
	$category_ids = false;
	$values       = array();
	$values['id'] = $post_ID;
	// get post author.
	$values['postinfo']      = $post;
	$values['postContent']   = $post->post_content;
	$values['authId']        = $post->post_author;
	$postdate                = $post->post_date;
	$dateformat              = ( '' === get_option( 'jd_date_format', '' ) ) ? get_option( 'date_format' ) : get_option( 'jd_date_format' );
	$thisdate                = mysql2date( $dateformat, $postdate );
	$altdate                 = mysql2date( 'Y-m-d H:i:s', $postdate );
	$values['_postDate']     = $altdate;
	$values['postDate']      = $thisdate;
	$moddate                 = $post->post_modified;
	$values['_postModified'] = mysql2date( 'Y-m-d H:i:s', $moddate );
	$values['postModified']  = mysql2date( $dateformat, $moddate );
	// get first category.
	$category   = null;
	$cat_desc   = null;
	$categories = get_the_category( $post_ID );
	$cats       = array();
	$cat_descs  = array();
	if ( is_array( $categories ) ) {
		if ( count( $categories ) > 0 ) {
			$category = $categories[0]->cat_name;
			$cat_desc = $categories[0]->description;
		}
		foreach ( $categories as $cat ) {
			$category_ids[] = $cat->term_id;
			$cats[]         = $cat->cat_name;
			$cat_descs[]    = $cat->description;
		}
		/**
		 * Filter the space separated list of category names in #cats#.
		 *
		 * @hook wpt_twitter_category_names
		 * @param {array} $cats Array of category names attached to this Tweet.
		 *
		 * @return {array}
		 */
		$cat_names = implode( ' ', apply_filters( 'wpt_twitter_category_names', $cats ) );
		/**
		 * Filter the space separated list of category descriptions in #cat_descs#.
		 *
		 * @hook wpt_twitter_category_descs
		 * @param {array} $cats Array of category descriptions attached to this Tweet.
		 *
		 * @return {array}
		 */
		$cat_descs = implode( ' ', apply_filters( 'wpt_twitter_category_descs', $cat_descs ) );
	} else {
		$category     = '';
		$cat_desc     = '';
		$category_ids = array();
	}
	$values['cats']        = $cat_names;
	$values['cat_descs']   = $cat_descs;
	$values['categoryIds'] = $category_ids;
	$values['category']    = html_entity_decode( $category, ENT_COMPAT, $encoding );
	$values['cat_desc']    = html_entity_decode( $cat_desc, ENT_COMPAT, $encoding );
	$excerpt_length        = get_option( 'jd_post_excerpt' );
	$post_excerpt          = ( '' === trim( $post->post_excerpt ) ) ? mb_substr( strip_tags( strip_shortcodes( $post->post_content ) ), 0, $excerpt_length ) : mb_substr( strip_tags( strip_shortcodes( $post->post_excerpt ) ), 0, $excerpt_length );
	$values['postExcerpt'] = html_entity_decode( $post_excerpt, ENT_COMPAT, $encoding );
	$thisposttitle         = $post->post_title;
	if ( '' === $thisposttitle && isset( $_POST['title'] ) ) {
		$thisposttitle = wp_kses_post( $_POST['title'] );
	}
	$thisposttitle = strip_tags( apply_filters( 'the_title', stripcslashes( $thisposttitle ), $post_ID ) );
	// These are common sequences that may not be fixed by html_entity_decode due to double encoding.
	$search               = array( '&apos;', '&#039;', '&quot;', '&#034;', '&amp;', '&#038;' );
	$replace              = array( "'", "'", '"', '"', '&', '&' );
	$thisposttitle        = str_replace( $search, $replace, $thisposttitle );
	$values['postTitle']  = html_entity_decode( $thisposttitle, ENT_QUOTES, $encoding );
	$values['postLink']   = wpt_link( $post_ID );
	$values['blogTitle']  = get_bloginfo( 'name' );
	$values['shortUrl']   = wpt_short_url( $post_ID );
	$values['postStatus'] = $post->post_status;
	$values['postType']   = $post->post_type;
	/**
	 * Filters post array to insert custom data that can be used in Tweet process.
	 *
	 * @param array   $values Existing values.
	 * @param integer $post_ID Post ID.
	 * @return array  $values
	 */
	$values = apply_filters( 'wpt_post_info', $values, $post_ID );

	return $values;
}

/**
 * Retrieve stored short URL.
 *
 * @param int $post_id Post ID.
 *
 * @return mixed
 */
function wpt_short_url( $post_id ) {
	global $post_ID;
	if ( ! $post_id ) {
		$post_id = $post_ID;
	}
	$use_urls = ( get_option( 'wpt_use_stored_urls' ) === 'false' ) ? false : true;
	$short    = ( $use_urls ) ? get_post_meta( $post_id, '_wpt_short_url', true ) : false;
	$short    = ( '' === $short ) ? false : $short;

	return $short;
}

/**
 * Identify whether a post should be uploading media. Test settings and verify whether post has images that can be uploaded.
 *
 * @param int   $post_ID Post ID.
 * @param array $post_info Array of post data.
 *
 * @return boolean
 */
function wpt_post_with_media( $post_ID, $post_info = array() ) {
	$return = false;
	if ( ! function_exists( 'wpt_pro_exists' ) ) {
		return $return;
	}
	if ( isset( $post_info['wpt_image'] ) && 1 === (int) $post_info['wpt_image'] ) {
		// Post settings win over filters.
		return $return;
	}
	if ( ! get_option( 'wpt_media' ) ) {
		// Don't return immediately, this needs to be overrideable for posts.
		$return = false;
	} else {
		if ( has_post_thumbnail( $post_ID ) || wpt_post_attachment( $post_ID ) ) {
			$return = true;
		}
	}
	/**
	 * Filter whether this post should upload media.
	 *
	 * @hook wpt_upload_media
	 * @param {bool} $upload True to allow this post to upload media.
	 * @param {int}  $post_ID Post ID.
	 *
	 * @return {bool}
	 */
	return apply_filters( 'wpt_upload_media', $return, $post_ID );
}

/**
 * This function is no longer in use, but the filter within it is.
 *
 * @param string $post_type Type of post.
 * @param array  $post_info Post info.
 * @param int    $post_ID Post ID.
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
 * Set up a Tweet to be sent.
 *
 * @param int     $post_ID Post ID.
 * @param string  $type Publishing context: instant, future, xmlrpc.
 * @param object  $post Post object.
 * @param boolean $updated True if updated, false if inserted.
 * @param object  $post_before The post prior to this update, or null for new posts.
 *
 * @return int $post_ID
 */
function wpt_tweet( $post_ID, $type = 'instant', $post = null, $updated = null, $post_before = null ) {
	if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
		return $post_ID;
	}
	wpt_check_version();
	$tweet_this     = get_post_meta( $post_ID, '_jd_tweet_this', true );
	$newpost        = false;
	$oldpost        = false;
	$is_inline_edit = false;
	$sentence       = '';
	$template       = '';
	$nptext         = '';
	if ( '1' !== get_option( 'wpt_inline_edits' ) ) {
		if ( isset( $_POST['_inline_edit'] ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return false;
		}
	} else {
		if ( isset( $_POST['_inline_edit'] ) || isset( $_REQUEST['bulk_edit'] ) ) {
			$is_inline_edit = true;
		}
	}
	if ( '0' === get_option( 'jd_tweet_default' ) ) {
		$default      = ( 'no' !== $tweet_this ) ? true : false;
		$text_default = 'no';
	} else {
		$default      = ( 'yes' === $tweet_this ) ? true : false;
		$text_default = 'yes';
	}
	wpt_mail( '1: Tweet Status', "Should tweet: $tweet_this; Setting: $text_default; Publication method: $type", $post_ID ); // DEBUG.
	if ( $default ) { // default switch: depend on default settings.
		$post_info = wpt_post_info( $post_ID );
		$media     = wpt_post_with_media( $post_ID, $post_info );
		if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() ) {
			$auth = ( 'false' === get_option( 'wpt_cotweet_lock' ) || ! get_option( 'wpt_cotweet_lock' ) ) ? $post_info['authId'] : get_option( 'wpt_cotweet_lock' );
		} else {
			$auth = $post_info['authId'];
		}
		$debug_post_info = $post_info;
		unset( $debug_post_info['post_content'] );
		unset( $debug_post_info['postContent'] );
		wpt_mail( '2: WP to Twitter Post Info (post content omitted)', print_r( $debug_post_info, 1 ), $post_ID ); // DEBUG.
		if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() && function_exists( 'wpt_filter_post_info' ) ) {
			$filter = wpt_filter_post_info( $post_info );
			if ( true === $filter ) {
				wpt_mail( '3: Post blocked by WP Tweets Pro custom filters', 'No additional data available', $post_ID );

				return false;
			}
		}
		/**
		 * Return true to ignore this post based on POST data. Default false.
		 *
		 * @hook wpt_filter_post_data
		 * @param {bool} $filter True if this post should not be Tweeted.
		 * @param {array} $post POST global.
		 *
		 * @return {bool}
		 */
		$filter = apply_filters( 'wpt_filter_post_data', false, $_POST );
		if ( $filter ) {
			return false;
		}
		$post_type = $post_info['postType'];
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
		$post_type_settings = get_option( 'wpt_post_types' );
		$post_types         = array_keys( $post_type_settings );
		if ( in_array( $post_type, $post_types, true ) ) {
			// identify whether limited by category/taxonomy.
			$continue = wpt_category_limit( $post_type, $post_info, $post_ID );
			if ( false === $continue ) {
				wpt_mail( '4b: WP Tweets Pro: Limited by term filters', 'This post was rejected by a taxonomy/term filter', $post_ID );
				return false;
			}
			// create Tweet and ID whether current action is edit or new.
			$ct = get_post_meta( $post_ID, '_jd_twitter', true );
			if ( isset( $_POST['_jd_twitter'] ) && '' !== trim( $_POST['_jd_twitter'] ) ) {
				$ct = sanitize_textarea_field( $_POST['_jd_twitter'] );
			}
			$custom_tweet = ( '' !== $ct ) ? stripcslashes( trim( $ct ) ) : '';
			// if ops is set and equals 'publish', this is being edited. Otherwise, it's a new post.
			if ( 0 === $new || true === $is_inline_edit ) {
				// if this is an old post and editing updates are enabled.
				if ( '1' === get_option( 'jd_tweet_default_edit' ) ) {
					$tweet_this = apply_filters( 'wpt_tweet_this_edit', $tweet_this, $_POST );
					if ( 'yes' !== $tweet_this ) {
						return false;
					}
				}
				wpt_mail( '4b: Post action is edit', 'This event was a post edit action, not a post publication.' . "\n" . 'Modified Date: ' . $post_info['_postModified'] . "\n\n" . 'Publication date:' . $post_info['_postDate'], $post_ID ); // DEBUG.
				if ( '1' === (string) $post_type_settings[ $post_type ]['post-edited-update'] ) {
					$nptext  = stripcslashes( $post_type_settings[ $post_type ]['post-edited-text'] );
					$oldpost = true;
				}
			} else {
				wpt_mail( '4c: Post action is publish', 'This event was a post publish action.' . "\n" . 'Modified Date: ' . $post_info['_postModified'] . "\n\n" . 'Publication date:' . $post_info['_postDate'], $post_ID ); // DEBUG.
				if ( '1' === (string) $post_type_settings[ $post_type ]['post-published-update'] ) {
					$nptext  = stripcslashes( $post_type_settings[ $post_type ]['post-published-text'] );
					$newpost = true;
				}
			}
			if ( $newpost || $oldpost ) {
				$template = ( '' !== $custom_tweet ) ? $custom_tweet : $nptext;
				$sentence = jd_truncate_tweet( $template, $post_info, $post_ID );
				wpt_mail( '5: Tweet Template Processed', "Template: $template; Tweet: $sentence", $post_ID ); // DEBUG.
				if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() ) {
					$sentence2 = jd_truncate_tweet( $template, $post_info, $post_ID, false, $auth );
				}
			}
			if ( '' !== $sentence ) {
				// WPT PRO.
				if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() ) {
					$wpt_selected_users = $post_info['wpt_authorized_users'];
					// set up basic author/main account values.
					$auth_verified = wtt_oauth_test( $auth, 'verify' );
					if ( empty( $wpt_selected_users ) && '1' === get_option( 'jd_individual_twitter_users' ) ) {
						$wpt_selected_users = ( $auth_verified ) ? array( $auth ) : array( false );
					}
					if ( 1 === (int) $post_info['wpt_cotweet'] || '1' !== get_option( 'jd_individual_twitter_users' ) || in_array( 'main', $wpt_selected_users, true ) ) {
						$wpt_selected_users['main'] = false;
					}
					// filter selected users before using.
					$wpt_selected_users = apply_filters( 'wpt_filter_users', $wpt_selected_users, $post_info );
					if ( '0' === (string) $post_info['wpt_delay_tweet'] || '' === $post_info['wpt_delay_tweet'] || 'on' === $post_info['wpt_no_delay'] ) {
						foreach ( $wpt_selected_users as $acct ) {
							if ( wtt_oauth_test( $acct, 'verify' ) ) {
								wpt_post_to_twitter( $sentence2, $acct, $post_ID, $media );
							}
						}
					} else {
						foreach ( $wpt_selected_users as $acct ) {
							$acct   = ( 'main' === $acct ) ? false : $acct;
							$offset = ( $auth !== $acct ) ? apply_filters( 'wpt_random_delay', rand( 60, 480 ) ) : 0;
							if ( wtt_oauth_test( $acct, 'verify' ) ) {
								$time = apply_filters( 'wpt_schedule_delay', ( (int) $post_info['wpt_delay_tweet'] ) * 60, $acct );

								/**
								 * Render the template of a scheduled Tweet only at the time it's sent.
								 *
								 * @hook wpt_postpone_rendering
								 * @param {bool} $postpone True to postpone rendering.
								 *
								 * @return {bool}
								 */
								$postpone_rendering = apply_filters( 'wpt_postpone_rendering', get_option( 'wpt_postpone_rendering', 'false' ) );
								if ( 'false' !== $postpone_rendering ) {
									$sentence = $template;
								}
								wp_schedule_single_event(
									time() + $time + $offset,
									'wpt_schedule_tweet_action',
									array(
										'id'       => $acct,
										'sentence' => $sentence,
										'rt'       => 0,
										'post_id'  => $post_ID,
									)
								);
								if ( WPT_DEBUG ) {
									$author_id = ( $acct ) ? "#$acct" : 'Main';
									wpt_mail(
										"7a: Tweet Scheduled for author: $author_id",
										print_r(
											array(
												'id'       => $acct,
												'sentence' => $sentence,
												'rt'       => 0,
												'post_id'  => $post_ID,
												'timestamp' => time() + $time + $offset . ', ' . gmdate( 'Y-m-d H:i:s', time() + $time + $offset ),
												'current_time' => time() . ', ' . gmdate( 'Y-m-d H:i:s', time() ),
												'timezone' => get_option( 'gmt_offset' ),
												'users'    => $wpt_selected_users,
											),
											1
										),
										$post_ID
									);
									// DEBUG.
								}
							}
						}
					}
					// This cycle handles scheduling the automatic retweets.
					if ( 0 !== (int) $post_info['wpt_retweet_after'] && 'on' !== $post_info['wpt_no_repost'] ) {
						$repeat = $post_info['wpt_retweet_repeat'];
						$first  = true;
						foreach ( $wpt_selected_users as $acct ) {
							if ( wtt_oauth_test( $acct, 'verify' ) ) {
								for ( $i = 1; $i <= $repeat; $i ++ ) {
									$continue = apply_filters( 'wpt_allow_reposts', true, $i, $post_ID, $acct );
									if ( $continue ) {
										$retweet = apply_filters( 'wpt_set_retweet_text', $template, $i, $post_ID );

										/**
										 * Render the template of a scheduled Tweet only at the time it's sent.
										 *
										 * @hook wpt_postpone_rendering
										 * @param {bool} $postpone True to postpone rendering.
										 *
										 * @return {bool}
										 */
										$postpone_rendering = apply_filters( 'wpt_postpone_rendering', get_option( 'wpt_postpone_rendering', 'false' ) );
										if ( 'false' !== $postpone_rendering ) {
											$retweet = $retweet;
										} else {
											$retweet = jd_truncate_tweet( $retweet, $post_info, $post_ID, true, $acct );
										}
										if ( '' === $retweet ) {
											// If a filter sets this value to empty, exit without scheduling.
											return $post_ID;
										}
										// add original delay to schedule.
										$delay = ( isset( $post_info['wpt_delay_tweet'] ) ) ? ( (int) $post_info['wpt_delay_tweet'] ) * 60 : 0;
										// Don't delay the first Tweet of the group.
										$offset = ( true === $first ) ? 0 : rand( 60, 240 ); // delay each co-tweet by 1-4 minutes.
										$time   = apply_filters( 'wpt_schedule_retweet', ( $post_info['wpt_retweet_after'] ) * ( 60 * 60 ) * $i, $acct, $i, $post_info );
										wp_schedule_single_event(
											time() + $time + $offset + $delay,
											'wpt_schedule_tweet_action',
											array(
												'id'       => $acct,
												'sentence' => $retweet,
												'rt'       => $i,
												'post_id'  => $post_ID,
											)
										);
										if ( WPT_DEBUG ) {
											if ( $acct ) {
												$author_id = "#$acct";
											} else {
												$author_id = 'Main';
											}
											wpt_mail(
												"7b: Retweet Scheduled for author $author_id",
												print_r(
													array(
														'id'         => $acct,
														'sentence'   => array( $retweet, $i, $post_ID ),
														'timestamp'  => time() + $time + $offset + $delay,
														'time'       => array( $time, $offset, $delay, get_option( 'gmt_offset' ), time() ),
														'timestring' => gmdate( 'Y-m-d H:i:s', time() + $time + $offset + $delay ),
														'current_ts' => gmdate( 'Y-m-d H:i:s', time() ),
													),
													1
												),
												$post_ID
											); // DEBUG.
										}
										$tweet_limit = (int) apply_filters( 'wpt_tweet_repeat_limit', 4, $post_ID );
										if ( $i === $tweet_limit ) {
											break;
										}
									}
								}
							}
							$first = false;
						}
					}
				} else {
					wpt_post_to_twitter( $sentence, false, $post_ID, $media );
				}
				// END WPT PRO.
			}
		}
	}

	return $post_ID;
}

/**
 *  Send Tweets on links in link manager. Only active if Link plug-in is installed.
 *
 * @param integer $link_id Database ID for link.
 *
 * @return mixed boolean/integer link ID if successful, false if failure.
 */
function wpt_twit_link( $link_id ) {
	wpt_check_version();
	$thislinkprivate = sanitize_text_field( $_POST['link_visible'] );
	if ( 'N' !== $thislinkprivate ) {
		$thislinkname        = stripslashes( sanitize_text_field( $_POST['link_name'] ) );
		$thispostlink        = sanitize_text_field( $_POST['link_url'] );
		$thislinkdescription = stripcslashes( sanitize_textarea_field( $_POST['link_description'] ) );
		$sentence            = stripcslashes( get_option( 'newlink-published-text' ) );
		$sentence            = str_ireplace( '#title#', $thislinkname, $sentence );
		$sentence            = str_ireplace( '#description#', $thislinkdescription, $sentence );

		if ( mb_strlen( $sentence ) > 118 ) {
			$sentence = mb_substr( $sentence, 0, 114 ) . '...';
		}

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
			wpt_post_to_twitter( $sentence, false, $link_id );
		}

		return $link_id;
	} else {
		return false;
	}
}

/**
 * Generate hash tags from tags set on post.
 *
 * @param int $post_ID Post ID.
 *
 * @return string $hashtags Hashtags in format needed for Tweet.
 */
function wpt_generate_hash_tags( $post_ID ) {
	$hashtags       = '';
	$term_meta      = false;
	$t_id           = false;
	$max_tags       = get_option( 'jd_max_tags', '3' );
	$max_characters = get_option( 'jd_max_characters', '20' );
	$max_characters = ( '0' === $max_characters || '' === $max_characters ) ? 100 : $max_characters + 1;
	if ( '0' === $max_tags || '' === $max_tags ) {
		$max_tags = 100;
	}
	$use_cats = ( '1' === get_option( 'wpt_use_cats' ) ) ? true : false;
	$tags     = ( true === $use_cats ) ? wp_get_post_categories( $post_ID, array( 'fields' => 'all' ) ) : get_the_tags( $post_ID );
	/**
	 * Change the taxonomy used by default to generate post tags. Array of terms attached to post.
	 *
	 * @hook wpt_hash_source
	 * @param {array} $tags Array of post terms.
	 * @param {int}   $post_ID Post ID.
	 *
	 * @return {array}
	 */
	$tags = apply_filters( 'wpt_hash_source', $tags, $post_ID );
	if ( $tags && count( $tags ) > 0 ) {
		$i = 1;
		foreach ( $tags as $value ) {
			if ( function_exists( 'wpt_pro_exists' ) ) {
				$t_id      = $value->term_id;
				$term_meta = get_option( "wpt_taxonomy_$t_id" );
			}
			$source = get_option( 'wpt_tag_source' );
			if ( 'slug' === $source ) {
				// If the tag has an '@' symbol as the first character, assume it is a mention unless set.
				if ( 0 === stripos( $value->name, '@' ) && ! $term_meta ) {
					$term_meta = 5;
				}
				$tag = $value->slug;
			} else {
				$tag = $value->name;
				// If the tag has an '@' symbol as the first character, assume it is a mention unless set.
				if ( 0 === stripos( $value->name, '@' ) && ! $term_meta ) {
					$term_meta = 4;
				}
			}
			$strip   = get_option( 'jd_strip_nonan' );
			$search  = '/[^\p{L}\p{N}\s]/u';
			$replace = get_option( 'jd_replace_character' );
			$replace = ( '[ ]' === $replace || '' === $replace ) ? '' : $replace;
			if ( false !== strpos( $tag, ' ' ) ) {
				// If multiple words, camelcase tag.
				$tag = ucwords( $tag );
			}
			$tag = str_ireplace( ' ', $replace, trim( $tag ) );
			$tag = preg_replace( '/[\/]/', $replace, $tag ); // remove forward slashes.
			$tag = ( '1' === $strip ) ? preg_replace( $search, $replace, $tag ) : $tag;

			switch ( $term_meta ) {
				case 1:
					$newtag = "#$tag";
					break;
				case 2:
					$newtag = "$$tag";
					break;
				case 3:
					$newtag = '';
					break;
				case 4:
					$newtag = $tag;
					break;
				case 5:
					$newtag = "@$tag";
					break;
				default:
					/**
					 * Change the default tag character. Default '#'.
					 *
					 * @hook wpt_tag_default
					 * @param {string} $char Character used to convert tags into hashtags.
					 * @param {int}    $t_id Term ID.
					 *
					 * @return {string}
					 */
					$newtag = apply_filters( 'wpt_tag_default', '#', $t_id ) . $tag;
			}
			if ( mb_strlen( $newtag ) > 2 && ( mb_strlen( $newtag ) <= $max_characters ) && ( $i <= $max_tags ) ) {
				$hashtags .= "$newtag ";
				$i ++;
			}
		}
	}
	$hashtags = trim( $hashtags );
	if ( mb_strlen( $hashtags ) <= 1 ) {
		$hashtags = '';
	}

	return $hashtags;
}

add_action( 'admin_menu', 'wpt_add_twitter_outer_box' );
/**
 * Set up post meta box.
 */
function wpt_add_twitter_outer_box() {
	wpt_check_version();
	// add Twitter panel to post types where it's enabled.
	$wpt_post_types = get_option( 'wpt_post_types' );
	if ( is_array( $wpt_post_types ) ) {
		foreach ( $wpt_post_types as $key => $value ) {
			if ( '1' === (string) $value['post-published-update'] || '1' === (string) $value['post-edited-update'] ) {
				if ( current_user_can( 'wpt_can_tweet' ) ) {
					add_meta_box( 'wp2t', 'WP to Twitter', 'wpt_add_twitter_inner_box', $key, 'side' );
				}
			}
		}
	}
}

add_action( 'admin_menu', 'wpt_add_twitter_debug_box' );
/**
 * Set up post meta box.
 */
function wpt_add_twitter_debug_box() {
	if ( WPT_DEBUG && current_user_can( 'manage_options' ) ) {
		wpt_check_version();
		// add Twitter panel to post types where it's enabled.
		$wpt_post_types = wpt_allowed_post_types();
		foreach ( $wpt_post_types as $type ) {
			add_meta_box( 'wp2t-debug', 'WP to Twitter Debugging', 'wpt_show_debug', $type, 'advanced' );
		}
	}
}

/**
 * Print post meta box
 *
 * @param  object $post Post object.
 */
function wpt_add_twitter_inner_box( $post ) {
	$nonce = wp_create_nonce( 'wp-to-twitter-nonce' );
	?>
	<div>
		<input type="hidden" name="wp_to_twitter_nonce" value="<?php echo $nonce; ?>">
		<input type="hidden" name="wp_to_twitter_meta" value="true">
	</div>
	<?php
	if ( current_user_can( 'wpt_can_tweet' ) ) {
		$is_pro = ( function_exists( 'wpt_pro_exists' ) ) ? 'pro' : 'free';
		?>
		<div class='wp-to-twitter <?php echo $is_pro; ?>'>
		<?php
		$tweet_status = '';
		$options      = get_option( 'wpt_post_types' );
		$type         = $post->post_type;
		$status       = $post->post_status;
		$post_id      = $post->ID;
		$tweet_this   = get_post_meta( $post_id, '_jd_tweet_this', true );
		if ( ! $tweet_this ) {
			$tweet_this = ( '1' === get_option( 'jd_tweet_default' ) ) ? 'no' : 'yes';
		}
		if ( isset( $_GET['action'] ) && 'edit' === $_GET['action'] && '1' === get_option( 'jd_tweet_default_edit' ) && 'publish' === $status ) {
			$tweet_this = 'no';
		}
		if ( isset( $_REQUEST['message'] ) && '10' !== $_REQUEST['message'] ) {
			// don't display when draft is updated or if no message.
			if ( ! ( ( '1' === $_REQUEST['message'] ) && ( 'publish' === $status && '1' !== $options[ $type ]['post-edited-update'] ) ) && 'no' !== $tweet_this ) {
				$log   = wpt_get_log( 'wpt_status_message', $post_id );
				$class = ( __( 'Tweet sent successfully.', 'wp-to-twitter' ) !== $log ) ? 'error' : 'updated';
				if ( '' !== trim( $log ) ) {
					echo "<div class='$class'><p>$log</p></div>";
				}
			}
		}
		$tweet = esc_attr( stripcslashes( get_post_meta( $post_id, '_jd_twitter', true ) ) );
		$tweet = apply_filters( 'wpt_user_text', $tweet, $status );
		// Formulate Template display.
		$template = ( 'publish' === $status ) ? $options[ $type ]['post-edited-text'] : $options[ $type ]['post-published-text'];
		$expanded = $template;
		if ( '' !== get_option( 'jd_twit_prepend', '' ) ) {
			$expanded = '<em>' . stripslashes( get_option( 'jd_twit_prepend' ) ) . '</em> ' . $expanded;
		}
		if ( '' !== get_option( 'jd_twit_append', '' ) ) {
			$expanded = $expanded . ' <em>' . stripslashes( get_option( 'jd_twit_append' ) ) . '</em>';
		}
		if ( 'publish' === $status && '1' !== $options[ $type ]['post-edited-update'] ) {
			// Translators: post type.
			$tweet_status = sprintf( __( '%s will not be Tweeted on update.', 'wp-to-twitter' ), ucfirst( $type ) );
		}
		if ( 'publish' === $status && ( current_user_can( 'wpt_tweet_now' ) || current_user_can( 'manage_options' ) ) ) {
			?>
			<div class='tweet-buttons'>
				<button type='button' class='tweet button-primary' data-action='tweet'><span class='dashicons dashicons-twitter' aria-hidden='true'></span><?php _e( 'Tweet Now', 'wp-to-twitter' ); ?></button>
			<?php
			if ( function_exists( 'wpt_pro_exists' ) && wpt_pro_exists() ) {
				?>
			<button type='button' class='tweet schedule button-secondary' data-action='schedule' disabled><?php _e( 'Schedule', 'wp-to-twitter' ); ?></button>
			<button type='button' class='time button-secondary'>
				<span class="dashicons dashicons-clock" aria-hidden="true"></span><span class="screen-reader-text"><?php _e( 'Set Date/Time', 'wp-to-twitter' ); ?></span>
			</button>
			<div id="wpt_set_tweet_time">
				<?php
				$datavalue = gmdate( 'Y-m-d', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				$timevalue = date_i18n( 'h:s a', current_time( 'timestamp' ) + 3600 ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				?>
				<label for='wpt_date'><?php _e( 'Date', 'wp-to-twitter' ); ?></label>
				<input type='date' value='' class='wpt_date date' name='wpt_datetime' id='wpt_date' data-value='<?php echo $datavalue; ?>' /><br/>
				<label for='wpt_time'><?php _e( 'Time', 'wp-to-twitter' ); ?></label>
				<input type='text' value='<?php echo $timevalue; ?>' class='wpt_time time' name='wpt_datetime' id='wpt_time'/>
			</div>
				<?php
			}
			?>
				<div class='wpt_log' aria-live='assertive'></div>
			</div>
			<?php
		}
		if ( current_user_can( 'wpt_twitter_custom' ) || current_user_can( 'manage_options' ) ) {
			?>
			<p class='jtw'>
				<label for="wpt_custom_tweet"><?php _e( 'Custom Twitter Post', 'wp-to-twitter' ); ?></label><br/>
				<textarea class="wpt_tweet_box widefat" name="_jd_twitter" id="wpt_custom_tweet" rows="2" cols="60"><?php echo esc_attr( $tweet ); ?></textarea>
				<?php echo apply_filters( 'wpt_custom_box', '', $tweet, $post_id ); ?>
			</p>
			<p class='wpt-template'>
				<?php _e( 'Template:', 'wp-to-twitter' ); ?> <code><?php echo stripcslashes( $expanded ); ?></code>
				<?php echo apply_filters( 'wpt_template_block', '', $expanded, $post_id ); ?>
			</p>
			<?php
			echo apply_filters( 'wpt_custom_retweet_fields', '', $post_id );
			if ( get_option( 'jd_keyword_format' ) === '2' ) {
				$custom_keyword = get_post_meta( $post_id, '_yourls_keyword', true );
				echo "<label for='yourls_keyword'>" . __( 'YOURLS Custom Keyword', 'wp-to-twitter' ) . "</label> <input type='text' name='_yourls_keyword' id='yourls_keyword' value='$custom_keyword' />";
			}
		} else {
			?>
			<input type="hidden" name='_jd_twitter' value='<?php echo esc_attr( $tweet ); ?>' />
			<p class='wpt-template'>
				<?php _e( 'Template:', 'wp-to-twitter' ); ?> <code><?php echo stripcslashes( $expanded ); ?></code>
				<?php echo apply_filters( 'wpt_template_block', '', $expanded, $post_id ); ?>
			</p>
			<?php
		}
		if ( current_user_can( 'wpt_twitter_switch' ) || current_user_can( 'manage_options' ) ) {
			// "no" means 'Don't Tweet' (is checked)
			$nochecked  = ( 'no' === $tweet_this ) ? ' checked="checked"' : '';
			$yeschecked = ( 'yes' === $tweet_this ) ? ' checked="checked"' : '';
			?>
		<p class='toggle-btn-group'>
			<input type="radio" name="_jd_tweet_this" value="no" id="jtn"<?php echo $nochecked; ?> /><label for="jtn"><?php _e( "Don't Tweet", 'wp-to-twitter' ); ?></label>
			<input type="radio" name="_jd_tweet_this" value="yes" id="jty"<?php echo $yeschecked; ?> /><label for="jty"><?php _e( 'Tweet', 'wp-to-twitter' ); ?></label>
		</p>
			<?php
		} else {
			?>
		<input type='hidden' name='_jd_tweet_this' value='<?php echo $tweet_this; ?>'/>
			<?php
		}
		?>
		<div class='wpt-options'>
			<?php
			if ( 'pro' === $is_pro ) {
				$pro_active  = " class='active'";
				$free_active = '';
			} else {
				$free_active = " class='active'";
				$pro_active  = '';
			}
			?>
			<ul class='tabs' role="tablist">
				<li><a href='#custom' aria-controls="custom" role="tab" id="tab_custom"><?php _e( 'Options', 'wp-to-twitter' ); ?></a></li>
				<li><a href='#notes'<?php echo $free_active; ?> aria-controls="notes" role="tab" id="tab_notes"><?php _e( 'Help', 'wp-to-twitter' ); ?></a></li>
			</ul>
			<div class='wptab' id='custom' aria-labelledby='tab_custom' role='tabpanel'>
			<?php
			if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() && ( current_user_can( 'wpt_twitter_custom' ) || current_user_can( 'manage_options' ) ) ) {
				wpt_schedule_values( $post_id );
				do_action( 'wpt_custom_tab', $post_id, 'visible' );
				// WPT PRO OPTIONS.
				if ( current_user_can( 'edit_others_posts' ) ) {
					if ( '1' === get_option( 'jd_individual_twitter_users' ) ) {
						$selected = ( get_post_meta( $post_id, '_wpt_authorized_users', true ) ) ? get_post_meta( $post_id, '_wpt_authorized_users', true ) : array();
						if ( function_exists( 'wpt_authorized_users' ) ) {
							echo wpt_authorized_users( $selected );
							do_action( 'wpt_authors_tab', $post_id, $selected );
						}
					}
				}
			}
			// WPT PRO.
			if ( ! current_user_can( 'wpt_twitter_custom' ) && ! current_user_can( 'manage_options' ) ) {
				?>
				<p><?php _e( 'Customizing WP to Twitter options is not allowed for your user role.', 'wp-to-twitter' ); ?></p>
				<?php
				if ( function_exists( 'wpt_pro_exists' ) && wpt_pro_exists() === true ) {
					wpt_schedule_values( $post_id, 'hidden' );
					do_action( 'wpt_custom_tab', $post_id, 'hidden' );
				}
			}
			?>
			</div>
			<div class='wptab' id='notes' aria-labelledby='tab_notes' role='tabpanel'>
				<p>
				<?php
				_e( 'Template Tags:<br /><code>#url#</code>, <code>#title#</code>, <code>#post#</code>, <code>#category#</code>, <code>#categories#</code>, <code>#date#</code>, <code>#modified#</code>, <code>#author#</code>, <code>#account#</code>, <code>#tags#</code>, <code>#blog#</code>, <code>#longurl#</code>.', 'wp-to-twitter' );
				do_action( 'wpt_notes_tab', $post_id );
				?>
				</p>
			</div>
		</div>
		<?php wpt_show_tweets( $post_id ); ?>
		<p class="wpt-support">
		<?php
		if ( function_exists( 'wpt_pro_exists' ) ) {
			?>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'support', admin_url( 'admin.php?page=wp-tweets-pro' ) ) ); ?>#get-support"><?php _e( 'Get Support', 'wp-to-twitter' ); ?></a> &raquo;
			<?php
		}
		?>
		</p>
		<?php
		if ( '' !== $tweet_status ) {
			echo "<p class='disabled'>$tweet_status</p>";
		}
		?>
		</div>
		<?php
	} else {
		// permissions: this user isn't allowed to Tweet.
		_e( 'Your role does not have the ability to Post Tweets from this site.', 'wp-to-twitter' );
		?>
		<input type='hidden' name='_jd_tweet_this' value='no'/>
		<?php
	}
}

/**
 * Format history of Tweets attempted on current post.
 *
 * @param array $post_id Post ID to fetch Tweets on.
 */
function wpt_show_tweets( $post_id ) {
	$previous_tweets = get_post_meta( $post_id, '_jd_wp_twitter', true );
	$failed_tweets   = get_post_meta( $post_id, '_wpt_failed' );

	if ( ! is_array( $previous_tweets ) && '' !== $previous_tweets ) {
		$previous_tweets = array( 0 => $previous_tweets );
	}
	if ( ! empty( $previous_tweets ) || ! empty( $failed_tweets ) ) {
		?>
	<p class='panel-toggle'>
		<a href='#wpt_tweet_history' class='history-toggle'><span class='dashicons dashicons-plus' aria-hidden="true"></span><?php _e( 'View Tweet History', 'wp-to-twitter' ); ?></a>
	</p>
	<div class='history'>
	<p class='error'><em><?php _e( 'Previous Tweets', 'wp-to-twitter' ); ?>:</em></p>
	<ul>
		<?php
		$has_history   = false;
		$hidden_fields = '';
		if ( is_array( $previous_tweets ) ) {
			foreach ( $previous_tweets as $previous_tweet ) {
				if ( '' !== $previous_tweet ) {
					$has_history    = true;
					$hidden_fields .= "<input type='hidden' name='_jd_wp_twitter[]' value='" . esc_attr( $previous_tweet ) . "' />";
					echo "<li>$previous_tweet <a href='http://twitter.com/intent/tweet?text=" . urlencode( $previous_tweet ) . "'>Retweet this</a></li>";
				}
			}
		}
		?>
	</ul>
		<?php
		$list       = false;
		$error_list = '';
		if ( is_array( $failed_tweets ) ) {
			foreach ( $failed_tweets as $failed_tweet ) {
				if ( ! empty( $failed_tweet ) ) {
					$ft          = $failed_tweet['sentence'];
					$reason      = $failed_tweet['code'];
					$error       = $failed_tweet['error'];
					$list        = true;
					$error_list .= "<li> <code>Error: $reason</code> $ft <a href='http://twitter.com/intent/tweet?text=" . urlencode( $ft ) . "'>Tweet this</a><br /><em>$error</em></li>";
				}
			}
			if ( true === $list ) {
				echo "<p class='error'><em>" . __( 'Failed Tweets', 'wp-to-twitter' ) . ":</em></p>
				<ul>$error_list</ul>";
			}
		}
		echo '<div>' . $hidden_fields . '</div>';
		if ( $has_history || $list ) {
			echo "<p><input type='checkbox' name='wpt_clear_history' id='wptch' value='clear' /> <label for='wptch'>" . __( 'Delete Tweet History', 'wp-to-twitter' ) . '</label></p>';
		}
		?>
	</div>
		<?php
	}
}

add_action( 'admin_enqueue_scripts', 'wpt_admin_scripts', 10, 1 );
/**
 * Enqueue admin scripts for WP to Twitter and WP Tweets PRO.
 */
function wpt_admin_scripts() {
	global $current_screen, $wpt_version;
	if ( 'post' === $current_screen->base || 'wp-tweets-pro_page_wp-to-twitter-schedule' === $current_screen->id ) {
		wp_enqueue_script( 'charCount', plugins_url( 'wp-to-twitter/js/jquery.charcount.js' ), array( 'jquery' ), $wpt_version );
	}
	if ( 'post' === $current_screen->base && isset( $_GET['post'] ) && ( current_user_can( 'wpt_tweet_now' ) || current_user_can( 'manage_options' ) ) ) {
		wp_enqueue_script( 'wpt.ajax', plugins_url( 'js/ajax.js', __FILE__ ), array( 'jquery' ), $wpt_version );
		wp_localize_script(
			'wpt.ajax',
			'wpt_data',
			array(
				'post_ID'  => (int) $_GET['post'],
				'action'   => 'wpt_tweet',
				'security' => wp_create_nonce( 'wpt-tweet-nonce' ),
			)
		);
	}
	if ( 'settings_page_wp-to-twitter/wp-to-twitter' === $current_screen->id || 'toplevel_page_wp-tweets-pro' === $current_screen->id ) {
		wp_enqueue_script( 'wpt.tabs', plugins_url( 'js/tabs.js', __FILE__ ), array( 'jquery' ), $wpt_version );
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

add_action( 'wp_ajax_wpt_tweet', 'wpt_ajax_tweet' );
/**
 * Handle Tweets sent via Ajax Tweet Now/Schedule Tweet buttons.
 */
function wpt_ajax_tweet() {
	if ( ! check_ajax_referer( 'wpt-tweet-nonce', 'security', false ) ) {
		echo 'Invalid Security Check';
		die;
	}
	$action       = ( 'tweet' === $_REQUEST['tweet_action'] ) ? 'tweet' : 'schedule';
	$authors      = ( isset( $_REQUEST['tweet_auth'] ) && null !== $_REQUEST['tweet_auth'] ) ? map_deep( $_REQUEST['tweet_auth'], 'sanitize_text_field' ) : false;
	$upload       = ( isset( $_REQUEST['tweet_upload'] ) && null !== $_REQUEST['tweet_upload'] ) ? (int) $_REQUEST['tweet_upload'] : '1';
	$current_user = wp_get_current_user();
	if ( function_exists( 'wpt_pro_exists' ) && wpt_pro_exists() ) {
		if ( wtt_oauth_test( $current_user->ID, 'verify' ) ) {
			$auth    = $current_user->ID;
			$user_ID = $current_user->ID;
		} else {
			$auth    = false;
			$user_ID = $current_user->ID;
		}
	} else {
		$auth    = false;
		$user_ID = $current_user->ID;
	}

	$authors = ( is_array( $authors ) && ! empty( $authors ) ) ? $authors : array( $auth );

	if ( current_user_can( 'wpt_can_tweet' ) ) {
		$options        = get_option( 'wpt_post_types' );
		$post_ID        = intval( $_REQUEST['tweet_post_id'] );
		$type           = get_post_type( $post_ID );
		$default        = ( isset( $options[ $type ]['post-edited-text'] ) ) ? $options[ $type ]['post-edited-text'] : '';
		$sentence       = ( isset( $_REQUEST['tweet_text'] ) && '' !== trim( $_REQUEST['tweet_text'] ) ) ? $_REQUEST['tweet_text'] : $default;
		$sentence       = stripcslashes( trim( $sentence ) );
		$post_info      = wpt_post_info( $post_ID );
		$sentence       = jd_truncate_tweet( $sentence, $post_info, $post_ID, false, $user_ID );
		$schedule       = ( isset( $_REQUEST['tweet_schedule'] ) ) ? strtotime( $_REQUEST['tweet_schedule'] ) : rand( 60, 240 );
		$print_schedule = date_i18n( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), $schedule );
		$offset         = ( 60 * 60 * get_option( 'gmt_offset' ) );
		$schedule       = $schedule - $offset;
		$media          = ( '1' === $upload ) ? false : true; // this is correct; the boolean logic is reversed. Blah.

		foreach ( $authors as $auth ) {

			$auth = ( 'main' === $auth ) ? false : $auth;

			switch ( $action ) {
				case 'tweet':
					wpt_post_to_twitter( $sentence, $auth, $post_ID, $media );
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
			// Translators: Full text of Tweet, time scheduled for.
			$return = ( 'tweet' === $action ) ? wpt_get_log( 'wpt_status_message', $post_ID ) : sprintf( __( 'Tweet scheduled: %1$s for %2$s', 'wp-to-twitter' ), '"' . $sentence . '"', $print_schedule );
			echo $return;
			if ( count( $authors ) > 1 ) {
				echo '<br />';
			}
		}
	} else {
		echo __( 'You are not authorized to perform this action', 'wp-to-twitter' );
	}
	die;
}

add_action( 'admin_head', 'wpt_admin_script' );
/**
 * Print scripts to WP Tweets PRO pages.
 */
function wpt_admin_script() {
	global $current_screen;
	if ( 'post' === $current_screen->base || 'wp-tweets-pro_page_wp-to-twitter-schedule' === $current_screen->id ) {
		wp_register_style( 'wpt-post-styles', plugins_url( 'css/post-styles.css', __FILE__ ) );
		wp_enqueue_style( 'wpt-post-styles' );
		$config = wpt_max_length();
		// add one; character count starts from 1.
		if ( 'post' === $current_screen->base ) {
			$allowed = $config['base_length'] - mb_strlen( stripslashes( get_option( 'jd_twit_prepend' ) . get_option( 'jd_twit_append' ) ) ) + 1;
		} else {
			$allowed = $config['base_length'] + 1;
		}
		if ( function_exists( 'wpt_pro_exists' ) ) {
			$first = '#custom';
		} else {
			$first = '#notes';
		}
		wp_register_script( 'wpt-base-js', plugins_url( 'js/base.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'wpt-base-js' );
		wp_localize_script(
			'wpt-base-js',
			'wptSettings',
			array(
				'allowed' => $allowed,
				'first'   => $first,
				'is_ssl'  => ( wpt_is_ssl( home_url() ) ) ? 'true' : 'false',
				'text'    => __( 'Characters left: ', 'wp-to-twitter' ),
			)
		);
		echo "
<style type='text/css'>
#wp2t h3 span, #wp2t h2 span { padding-left: 30px; background: url(" . plugins_url( 'wp-to-twitter/images/twitter-bird-light-bgs.png' ) . ') left 50% no-repeat; }
</style>';
	}
}

/**
 * Post the Custom Tweet & custom Tweet data into the post meta table
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
		$nonce = ( isset( $_POST['wp_to_twitter_nonce'] ) ) ? $_POST['wp_to_twitter_nonce'] : false;
		if ( ! ( $nonce && wp_verify_nonce( $nonce, 'wp-to-twitter-nonce' ) ) ) {
			wp_die( 'WP to Twitter: Security check failed' );
		}
		if ( isset( $_POST['_yourls_keyword'] ) ) {
			$yourls = sanitize_text_field( $_POST['_yourls_keyword'] );
			$update = update_post_meta( $id, '_yourls_keyword', $yourls );
		}
		if ( isset( $_POST['_jd_twitter'] ) && '' !== $_POST['_jd_twitter'] ) {
			$twitter = sanitize_textarea_field( $_POST['_jd_twitter'] );
			$update  = update_post_meta( $id, '_jd_twitter', $twitter );
		} elseif ( isset( $_POST['_jd_twitter'] ) && '' === $_POST['_jd_twitter'] ) {
			delete_post_meta( $id, '_jd_twitter' );
		}
		if ( isset( $_POST['_jd_wp_twitter'] ) && '' !== $_POST['_jd_wp_twitter'] ) {
			$wp_twitter = sanitize_textarea_field( $_POST['_jd_wp_twitter'] );
			$update     = update_post_meta( $id, '_jd_wp_twitter', $wp_twitter );
		}
		if ( isset( $_POST['_jd_tweet_this'] ) ) {
			$tweet_this = ( 'no' === $_POST['_jd_tweet_this'] ) ? 'no' : 'yes';
			$update     = update_post_meta( $id, '_jd_tweet_this', $tweet_this );
		} else {
			$tweet_default = ( '1' === get_option( 'jd_tweet_default' ) ) ? 'no' : 'yes';
			$update        = update_post_meta( $id, '_jd_tweet_this', $tweet_default );
		}
		if ( isset( $_POST['wpt_clear_history'] ) && 'clear' === $_POST['wpt_clear_history'] ) {
			delete_post_meta( $id, '_wpt_failed' );
			delete_post_meta( $id, '_jd_wp_twitter' );
			delete_post_meta( $id, '_wpt_short_url' );
			delete_post_meta( $id, '_wp_jd_twitter' );
		}
		// WPT PRO.
		$update = apply_filters( 'wpt_insert_post', $_POST, $id );
		// WPT PRO.
		// only send debug data if post meta is updated.
		wpt_mail( 'Post Meta Processed', 'WP to Twitter post meta was updated' . "\n\n" . print_r( map_deep( $_POST, 'sanitize_textarea_field' ), 1 ), $id ); // DEBUG.

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
	if ( is_admin() && isset( $_GET['page'] ) && 'wp-to-twitter/wp-to-twitter.php' === $_GET['page'] ) {
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
		$icon_path = plugins_url( 'images/icon.png', __FILE__ );
		$page      = add_menu_page( __( 'WP to Twitter', 'wp-to-twitter' ), __( 'WP to Twitter', 'wp-to-twitter' ), 'manage_options', 'wp-tweets-pro', 'wpt_update_settings', $icon_path );
	}
}

add_action( 'admin_head', 'wpt_admin_style' );
/**
 * Add stylesheets to WP to Twitter pages.
 */
function wpt_admin_style() {
	global $wpt_version;
	if ( isset( $_GET['page'] ) && ( 'wp-to-twitter' === $_GET['page'] || 'wp-tweets-pro' === $_GET['page'] || 'wp-to-twitter-schedule' === $_GET['page'] || 'wp-to-twitter-tweets' === $_GET['page'] || 'wp-to-twitter-errors' === $_GET['page'] ) ) {
		wp_enqueue_style( 'wpt-styles', plugins_url( 'css/styles.css', __FILE__ ), array(), $wpt_version );
	}
}

/**
 * Add WP to Twitter links to plug-in information.
 *
 * @param array  $links Array of links.
 * @param string $file Current file name.
 *
 * @return link new array.
 */
function wpt_plugin_action( $links, $file ) {
	if ( plugin_basename( dirname( __FILE__ ) . '/wp-to-twitter.php' ) === $file ) {
		$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
		$links[]   = "<a href='$admin_url'>" . __( 'WP to Twitter Settings', 'wp-to-twitter' ) . '</a>';
	}

	return $links;
}

// Add Plugin Actions to WordPress.
add_filter( 'plugin_action_links', 'wpt_plugin_action', 10, 2 );
add_action( 'in_plugin_update_message-wp-to-twitter/wp-to-twitter.php', 'wpt_plugin_update_message' );
/**
 * Parse plugin update info to display in update list.
 */
function wpt_plugin_update_message() {
	global $wpt_version;
	$note = '';
	define( 'WPT_PLUGIN_README_URL', 'http://svn.wp-plugins.org/wp-to-twitter/trunk/readme.txt' );
	$response = wp_remote_get( WPT_PLUGIN_README_URL, array( 'user-agent' => 'WordPress/WP to Twitter' . $wpt_version . '; ' . get_bloginfo( 'url' ) ) );
	if ( ! is_wp_error( $response ) || is_array( $response ) ) {
		$data = $response['body'];
		$bits = explode( '== Upgrade Notice ==', $data );
		$note = '</div><div id="wpt-upgrade" class="notice inline notice-warning"><ul><li><strong style="color:#c22;">Upgrade Notes:</strong> ' . str_replace( '* ', '', nl2br( trim( $bits[1] ) ) ) . '</li></ul>';
	}

	echo $note;
}

if ( '1' === get_option( 'jd_twit_blogroll' ) ) {
	add_action( 'add_link', 'wpt_twit_link' );
}

if ( function_exists( 'wp_after_insert_post' ) ) {
	/**
	 * Use the `wp_after_insert_post` action to run Tweets.
	 *
	 * @since WordPress 5.6
	 */
	add_action( 'wp_after_insert_post', 'wpt_save_post', 10, 2 );
	add_action( 'wp_after_insert_post', 'wpt_twit', 15, 4 );
} else {
	add_action( 'save_post', 'wpt_save_post', 10, 2 );
	add_action( 'save_post', 'wpt_twit', 15 );
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
		wpt_mail( '0: Not a Tweeted post type', 'This post type is not enabled for Tweeting: ' . $type, $id );
	}

	return false;
}

/**
 * Get array of post types that can be Tweeted.
 *
 * @return array
 */
function wpt_allowed_post_types() {
	$post_type_settings = get_option( 'wpt_post_types' );
	$allowed_types      = array();
	if ( is_array( $post_type_settings ) && ! empty( $post_type_settings ) ) {
		foreach ( $post_type_settings as $type => $settings ) {
			if ( '1' === (string) $settings['post-edited-update'] || '1' === (string) $settings['post-published-update'] ) {
				$allowed_types[] = $type;
			}
		}
	}

	/**
	 * Return array of post types that can be sent as Tweets.
	 *
	 * @hook wpt_allowed_post_types
	 * @param {array} $types Array of post type names enabled for Tweets either when editing or publishing.
	 * @param {array} $post_type_settings Multidimensional array of post types and post type settings.
	 *
	 * @return {array}
	 */
	return apply_filters( 'wpt_allowed_post_types', $allowed_types, $post_type_settings );
}

add_action( 'future_to_publish', 'wpt_future_to_publish', 16 );
/**
 * Handle Tweeting posts scheduled for the future.
 *
 * @param object $post Post object.
 */
function wpt_future_to_publish( $post ) {
	$id = $post->ID;
	if ( wp_is_post_autosave( $id ) || wp_is_post_revision( $id ) || ! wpt_in_post_type( $id ) ) {
		return;
	}
	wpt_mail( 'Transitioning future to publish', $id );
	wpt_twit_future( $id );
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
 * Handle Tweeting posts published directly. As of 12/10/2020, supports new wp_after_insert_post to improve support when used with block editor.
 *
 * @param int     $id Post ID.
 * @param object  $post Post object.
 * @param boolean $updated True if updated, false if inserted.
 * @param object  $post_before The post prior to this update, or null for new posts.
 */
function wpt_twit( $id, $post = null, $updated = null, $post_before = null ) {
	if ( ( empty( $_POST ) && ! wpt_auto_tweet_allowed( $id ) ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id ) || isset( $_POST['_inline_edit'] ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! wpt_auto_tweet_allowed( $id ) ) || ! wpt_in_post_type( $id ) ) {
		return $id;
	}

	$post = ( null === $post ) ? get_post( $id ) : $post;
	if ( 'publish' !== $post->post_status ) {
		return $id;
	}
	// is there any reason to accept any other status?
	wpt_mail( 'Tweeting published post', $id );
	wpt_twit_instant( $id, $post, $updated, $post_before );
}

add_action( 'xmlrpc_publish_post', 'wpt_twit_xmlrpc' );
add_action( 'publish_phone', 'wpt_twit_xmlrpc' );

/**
 * For future posts, check transients to see whether this post has already been published. Prevents duplicate Tweet attempts in older versions of WP.
 *
 * @param integer $id Post ID.
 */
function wpt_twit_future( $id ) {
	set_transient( '_wpt_twit_future', $id, 10 );
	// instant action has already run for this post.
	// prevent running actions twice (need both for older WP).
	if ( get_transient( '_wpt_twit_instant' ) && (int) get_transient( '_wpt_twit_instant' ) === $id ) {
		delete_transient( '_wpt_twit_instant' );

		return;
	}

	wpt_tweet( $id, 'future' );
}

/**
 * For immediate posts, check transients to see whether this post has already been published. Prevents duplicate Tweet attempts in older versions of WP or cases where a future action is being run after the initial action.
 *
 * @param int     $id Post ID.
 * @param object  $post Post object.
 * @param boolean $updated True if updated, false if inserted.
 * @param object  $post_before The post prior to this update, or null for new posts.
 */
function wpt_twit_instant( $id, $post, $updated, $post_before ) {
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
	wpt_tweet( $id, 'instant', $post, $updated, $post_before );
}

/**
 * Tweet XMLRPC posts.
 *
 * @param integer $id Post ID.
 *
 * @return post ID.
 */
function wpt_twit_xmlrpc( $id ) {
	set_transient( '_wpt_twit_xmlrpc', $id, 10 );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $id ) || ! wpt_in_post_type( $id ) ) {
		return $id;
	}
	wpt_mail( 'Tweeting XMLRPC published post', $id );
	wpt_tweet( $id, 'xmlrpc' );
	return $id;
}

add_action( 'admin_notices', 'wpt_debugging_enabled', 10 );
/**
 * Show notice is Twitter debugging is enabled.
 */
function wpt_debugging_enabled() {
	if ( current_user_can( 'manage_options' ) && WPT_DEBUG ) {
		echo "<div class='notice error important'><p>" . __( '<strong>WP to Twitter</strong> debugging is enabled. Remember to disable debugging when you are finished.', 'wp-to-twitter' ) . '</p></div>';
	}
}

add_action( 'wp_enqueue_scripts', 'wpt_stylesheet' );
/**
 * Enqueue front-end styles for Twitter Feed widget if enabled.
 */
function wpt_stylesheet() {
	/**
	 * Disable WP to Twitter feeds stylesheet. Styles the Twitter Feed widgets.
	 *
	 * @hook wpt_enqueue_feed_styles
	 * @param {bool} $apply False to disable.
	 *
	 * @return {bool}
	 */
	$apply = apply_filters( 'wpt_enqueue_feed_styles', true );
	if ( $apply ) {
		/**
		 * Replace the WP to Twitter feeds stylesheet.
		 *
		 * @hook wpt_feed_stylesheet
		 * @param {string} $file URL to stylesheet.
		 *
		 * @return {string}
		 */
		$file = apply_filters( 'wpt_feed_stylesheet', plugins_url( 'css/twitter-feed.css', __FILE__ ) );
		wp_register_style( 'wpt-twitter-feed', $file );
		wp_enqueue_style( 'wpt-twitter-feed' );
	}
}

add_filter( 'wpt_enqueue_feed_styles', 'wpt_permit_feed_styles' );
/**
 * Check whether Twitter Feed styles are enabled.
 *
 * @param boolean $value true if permitted.
 *
 * @return boolean $value False if settings disable styles.
 */
function wpt_permit_feed_styles( $value ) {
	if ( '1' === get_option( 'wpt_permit_feed_styles' ) ) {
		$value = false;
	}

	return $value;
}
