<?php

if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Class SB_Instagram_Oembed
 *
 * Replaces the native WordPress functionality for Instagram oembed
 * to allow authenticated oembeds
 *
 * @since 2.5/5.8
 *
 * @package instagram-feed-free
 */
class SB_Instagram_Oembed // phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
{
	/**
	 * SB_Instagram_Oembed constructor.
	 *
	 * If an account has been connected, hooks are added
	 * to change how Instagram links are handled for oembeds
	 *
	 * @since 2.5/5.8
	 */
	public function __construct()
	{
		if (self::can_do_oembed()) {
			if (self::can_check_for_old_oembeds()) {
				add_action('init', array('SB_Instagram_Oembed', 'clear_checks'));
			}
			add_filter('oembed_providers', array('SB_Instagram_Oembed', 'oembed_providers'), 10, 1);
			add_filter('oembed_fetch_url', array('SB_Instagram_Oembed', 'oembed_set_fetch_url'), 10, 3);
			add_filter('oembed_result', array('SB_Instagram_Oembed', 'oembed_result'), 10, 3);
		}
		if (self::should_extend_ttl()) {
			add_filter('oembed_ttl', array('SB_Instagram_Oembed', 'oembed_ttl'), 10, 4);
		}
	}

	/**
	 * Check to make sure there is a connected account to
	 * enable authenticated oembeds
	 *
	 * @return bool
	 *
	 * @since 2.5/5.8
	 */
	public static function can_do_oembed()
	{
		$oembed_token_settings = get_option('sbi_oembed_token', array());

		if (isset($oembed_token_settings['disabled']) && $oembed_token_settings['disabled']) {
			return false;
		}

		$access_token = self::last_access_token();
		if (!$access_token) {
			return false;
		}

		return true;
	}

	/**
	 * Any access token will work for oembeds so the most recently connected account's
	 * access token is returned
	 *
	 * @return bool|string
	 *
	 * @since 2.5/5.8
	 */
	public static function last_access_token()
	{
		$oembed_token_settings = get_option('sbi_oembed_token', array());
		$will_expire = self::oembed_access_token_will_expire();
		if (
			!empty($oembed_token_settings['access_token'])
			&& (!$will_expire || $will_expire > time())
		) {
			return sbi_maybe_clean($oembed_token_settings['access_token']);
		} else {
			$if_database_settings = sbi_get_database_settings();

			if (isset($if_database_settings['connected_accounts'])) {
				$connected_accounts = $if_database_settings['connected_accounts'];
				foreach ($connected_accounts as $connected_account) {
					if (empty($oembed_token_settings['access_token']) && isset($connected_account['type']) && $connected_account['type'] === 'business') {
						$oembed_token_settings['access_token'] = $connected_account['access_token'];
					}
				}
			}

			if (!empty($oembed_token_settings['access_token'])) {
				return sbi_maybe_clean($oembed_token_settings['access_token']);
			}

			if (class_exists('CFF_Oembed')) {
				$cff_oembed_token_settings = get_option('cff_oembed_token', array());
				if (!empty($cff_oembed_token_settings['access_token'])) {
					return $cff_oembed_token_settings['access_token'];
				}
			}
		}

		return false;
	}

	/**
	 * Access tokens created from FB accounts not connected to an
	 * FB page expire after 60 days.
	 *
	 * @return bool|int
	 */
	public static function oembed_access_token_will_expire()
	{
		$oembed_token_settings = get_option('sbi_oembed_token', array());

		$will_expire = false;
		if (isset($oembed_token_settings['expiration_date']) && (int)$oembed_token_settings['expiration_date'] > 0) {
			$will_expire = (int)$oembed_token_settings['expiration_date'];
		}

		return $will_expire;
	}

	/**
	 * Checking for old oembeds makes permanent changes to posts
	 * so we want the user to turn it off and on
	 *
	 * @return bool
	 *
	 * @since 2.5/5.8
	 */
	public static function can_check_for_old_oembeds()
	{
		$sbi_statuses = get_option('sbi_statuses', array());
		return !isset($sbi_statuses['clear_old_oembed_checks']);
	}

	/**
	 * The "time to live" for Instagram oEmbeds is extended if the access token expires.
	 * Even if new oEmbeds will not use the Instagram Feed system due to an expired token
	 * the time to live should continue to be extended.
	 *
	 * @return bool
	 *
	 * @since 2.5/5.8
	 */
	public static function should_extend_ttl()
	{
		$oembed_token_settings = get_option('sbi_oembed_token', array());

		if (isset($oembed_token_settings['disabled']) && $oembed_token_settings['disabled']) {
			return false;
		}

		$will_expire = self::oembed_access_token_will_expire();
		if ($will_expire) {
			return true;
		}

		return false;
	}

	/**
	 * Filters the WordPress list of oembed providers to
	 * change what url is used for remote requests for the
	 * oembed data
	 *
	 * @param array $providers WordPress' array of oEmbed providers.
	 *
	 * @return array
	 *
	 * @since 2.5/5.8
	 */
	public static function oembed_providers($providers)
	{
		$oembed_url = self::oembed_url();
		if ($oembed_url) {
			$providers['#https?://(www\.)?instagr(\.am|am\.com)/(p|tv|reel)/.*#i'] = array($oembed_url, true);
			// for WP 4.9.
			$providers['#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i'] = array($oembed_url, true);
		}

		return $providers;
	}

	/**
	 * Depending on whether a business or personal account is connected,
	 * a different oembed endpoint is used
	 *
	 * @return string
	 *
	 * @since 2.5/5.8
	 */
	public static function oembed_url()
	{
		return 'https://graph.facebook.com/instagram_oembed';
	}

	/**
	 * Add the access token from a connected account to make an authenticated
	 * call to get oembed data from Instagram
	 *
	 * @param string $provider oEmbed provider URL.
	 * @param string $url Instagram permalink.
	 * @param array  $args Additional arguments.
	 *
	 * @return string
	 *
	 * @since 2.5/5.8
	 */
	public static function oembed_set_fetch_url($provider, $url, $args)
	{
		$access_token = self::last_access_token();
		if (!$access_token) {
			return $provider;
		}

		if (strpos($provider, 'instagram_oembed') !== false) {
			if (strpos($url, '?') !== false) {
				$exploded = explode('?', $url);
				if (isset($exploded[1])) {
					$provider = str_replace(urlencode('?' . $exploded[1]), '', $provider);
				}
			}
			$provider = add_query_arg('access_token', $access_token, $provider);
		}

		return $provider;
	}

	/**
	 * New oembeds are wrapped in a div for easy detection of older oembeds
	 * that will need to be updated
	 *
	 * @param string $html the HTML used for the oEmbed.
	 * @param string $url the URL used for the oEmbed.
	 * @param array  $args additional args.
	 *
	 * @return string
	 *
	 * @since 2.5/5.8
	 */
	public static function oembed_result($html, $url, $args)
	{
		if (preg_match('#https?://(www\.)?instagr(\.am|am\.com)/(p|tv|reel)/.*#i', $url) === 1) {
			if (strpos($html, 'class="instagram-media"') !== false) {
				$exchanged = str_replace('class="instagram-media"', 'class="instagram-media sbi-embed"', $html);
				$html = '<div class="sbi-embed-wrap">' . $exchanged . '</div>';
			}
		}

		return $html;
	}

	/**
	 * Extend the "time to live" for oEmbeds created with access tokens that expire
	 *
	 * @param int    $ttl time to live.
	 * @param string $url oEmbed url.
	 * @param string $attr additional attributes.
	 * @param int    $post_ID ID of the post that contains the oEmbed URL.
	 *
	 * @return float|int
	 *
	 * @since 2.5/5.8
	 */
	public static function oembed_ttl($ttl, $url, $attr, $post_ID)
	{
		if (preg_match('#https?://(www\.)?instagr(\.am|am\.com)/(p|tv|reel)/.*#i', $url) === 1) {
			$ttl = 30 * YEAR_IN_SECONDS;
		}

		return $ttl;
	}

	/**
	 * Loop through post meta data and if it's an oembed and has content
	 * that looks like an Instagram oembed, delete it
	 *
	 * @param int $post_ID the ID of the post containing the oEmbed.
	 *
	 * @return int number of old oembed caches found
	 *
	 * @since 2.5/5.8
	 */
	public static function delete_instagram_oembed_caches($post_ID)
	{
		$post_metas = get_post_meta($post_ID);
		if (empty($post_metas)) {
			return 0;
		}

		$total_found = 0;
		foreach ($post_metas as $post_meta_key => $post_meta_value) {
			if (
				'_oembed_' === substr($post_meta_key, 0, 8)
				&& strpos($post_meta_value[0], 'class="instagram-media"') !== false
				&& strpos($post_meta_value[0], 'sbi-embed-wrap') === false
			) {
					++$total_found;
					delete_post_meta($post_ID, $post_meta_key);
				if ('_oembed_time_' !== substr($post_meta_key, 0, 13)) {
					delete_post_meta($post_ID, str_replace('_oembed_', '_oembed_time_', $post_meta_key));
				}
			}
		}

		return $total_found;
	}

	/**
	 * Used for clearing the oembed update check flag for all posts
	 *
	 * @since 2.5/5.8
	 */
	public static function clear_checks()
	{
		global $wpdb;
		$table_name = esc_sql($wpdb->prefix . 'postmeta');
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			"DELETE
			FROM $table_name
			WHERE meta_key = '_sbi_oembed_done_checking';"
		);

		$sbi_statuses = get_option('sbi_statuses', array());
		$sbi_statuses['clear_old_oembed_checks'] = true;
		update_option('sbi_statuses', $sbi_statuses);
	}
}

/**
 * Start the oEmbed adaptation
 *
 * @return SB_Instagram_Oembed
 */
function sbiOembedInit()
{
	return new SB_Instagram_Oembed();
}

sbiOembedInit();
