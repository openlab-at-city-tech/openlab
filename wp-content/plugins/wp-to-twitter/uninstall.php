<?php
/**
 * Uninstall XPoster
 *
 * @category Core
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://xposterpro.com
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
} else {
	delete_option( 'wpt_post_types' );
	delete_option( 'jd_twit_remote' );
	delete_option( 'jd_post_excerpt' );

	delete_option( 'comment-published-update' );
	delete_option( 'comment-published-text' );
	delete_option( 'wpt_status_message_last' );
	delete_option( 'wtt_twitter_username' );
	// Su.pr API.
	delete_option( 'suprapi' );

	// Error checking.
	delete_option( 'jd-functions-checked' );
	delete_option( 'wp_twitter_failure' );
	delete_option( 'wp_supr_failure' );
	delete_option( 'wp_url_failure' );
	delete_option( 'wp_bitly_failure' );
	delete_option( 'wpt_curl_error' );

	// Rate Limiting.
	delete_option( 'wpt_rate_limits' );
	delete_option( 'wpt_default_rate_limit' );
	delete_option( 'wpt_rate_limit' );
	delete_option( 'wpt_rate_limiting' );

	// Blogroll options.
	delete_option( 'jd-use-link-title' );
	delete_option( 'jd-use-link-description' );
	delete_option( 'newlink-published-text' );
	delete_option( 'jd_twit_blogroll' );

	// Default publishing options.
	delete_option( 'jd_tweet_default' );
	delete_option( 'jd_tweet_default_edit' );
	delete_option( 'wpt_inline_edits' );

	// Note that default options are set.
	delete_option( 'twitterInitialised' );
	delete_option( 'wpt_twitter_setup' );
	delete_option( 'wp_twitter_failure' );
	delete_option( 'twitterlogin' );
	delete_option( 'twitterpw' );
	delete_option( 'twitterlogin_encrypted' );
	delete_option( 'suprapi' );
	delete_option( 'jd_twit_quickpress' );
	delete_option( 'jd-use-supr' );
	delete_option( 'jd-use-none' );
	delete_option( 'jd-use-wp' );

	// Special Options.
	delete_option( 'jd_twit_prepend' );
	delete_option( 'jd_twit_append' );
	delete_option( 'jd_twit_remote' );
	delete_option( 'twitter-analytics-campaign' );
	delete_option( 'use-twitter-analytics' );
	delete_option( 'jd_twit_custom_url' );
	delete_option( 'jd_shortener' );
	delete_option( 'jd_strip_nonan' );
	delete_option( 'wpt_auto_tweet_allowed' );
	delete_option( 'wpt_tweet_length' );
	delete_option( 'wpt_permit_feed_styles' );

	delete_option( 'jd_individual_twitter_users' );
	delete_option( 'use_tags_as_hashtags' );
	delete_option( 'jd_max_tags' );
	delete_option( 'jd_max_characters' );
	// Bitly Settings.
	delete_option( 'bitlylogin' );
	delete_option( 'jd-use-bitly' );
	delete_option( 'bitlyapi' );

	// twitter compatible api.
	delete_option( 'jd_api_post_status' );
	delete_option( 'app_consumer_key' );
	delete_option( 'app_consumer_secret' );
	delete_option( 'oauth_token' );
	delete_option( 'oauth_token_secret' );

	// dymamic analytics.
	delete_option( 'jd_dynamic_analytics' );
	delete_option( 'use_dynamic_analytics' );
	// category limits.
	delete_option( 'limit_categories' );
	delete_option( 'tweet_categories' );
	// yourls installation.
	delete_option( 'yourlsapi' );
	delete_option( 'yourlspath' );
	delete_option( 'yourlsurl' );
	delete_option( 'yourlslogin' );
	delete_option( 'jd_replace_character' );
	delete_option( 'jd_date_format' );
	delete_option( 'jd_keyword_format' );
	// Version.
	delete_option( 'wp_to_twitter_version' );
	delete_option( 'wpt_authentication_missing' );
	delete_option( 'wpt_http' );
}
