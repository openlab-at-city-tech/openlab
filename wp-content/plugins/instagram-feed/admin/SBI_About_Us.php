<?php

/**
 * The About Page
 *
 * @since 4.0
 */

namespace InstagramFeed\Admin;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use InstagramFeed\Builder\SBI_Feed_Builder;
use InstagramFeed\SBI_View;

class SBI_About_Us
{
	/**
	 * Admin menu page slug.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	const SLUG = 'sbi-about-us';

	/**
	 * Initializing the class
	 *
	 * @since 4.0
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Determining if the user is viewing the our page, if so, party on.
	 *
	 * @since 4.0
	 */
	public function init()
	{
		if (!is_admin()) {
			return;
		}

		add_action('admin_menu', [$this, 'register_menu']);
	}

	/**
	 * Register Menu.
	 *
	 * @since 4.0
	 */
	public function register_menu()
	{
		$cap = current_user_can('manage_instagram_feed_options') ? 'manage_instagram_feed_options' : 'manage_options';
		$cap = apply_filters('sbi_settings_pages_capability', $cap);

		$about_us = add_submenu_page(
			'sb-instagram-feed',
			__('About Us', 'instagram-feed'),
			__('About Us', 'instagram-feed'),
			$cap,
			self::SLUG,
			[$this, 'about_us'],
			4
		);
		add_action('load-' . $about_us, [$this, 'about_us_enqueue_assets']);
	}

	/**
	 * Enqueue About Us Page CSS & Script.
	 *
	 * Loads only for About Us page
	 *
	 * @since 4.0
	 */
	public function about_us_enqueue_assets()
	{
		if (!get_current_screen()) {
			return;
		}
		$screen = get_current_screen();
		if (!'instagram-feed_page_sbi-about-us' === $screen->id) {
			return;
		}

		wp_enqueue_style(
			'about-style',
			SBI_PLUGIN_URL . 'admin/assets/css/about.css',
			false,
			SBIVER
		);

		wp_enqueue_script(
			'sb-vue',
			SBI_PLUGIN_URL . 'js/vue.min.js',
			null,
			'2.6.12',
			true
		);

		wp_enqueue_script(
			'about-app',
			SBI_PLUGIN_URL . 'admin/assets/js/about.js',
			null,
			SBIVER,
			true
		);

		$sbi_about = $this->page_data();

		wp_localize_script(
			'about-app',
			'sbi_about',
			$sbi_about
		);
	}

	/**
	 * Page Data to use in front end
	 *
	 * @return array
	 * @since 4.0
	 */
	public function page_data()
	{
		// get the WordPress's core list of installed plugins
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$license_key = null;
		if (get_option('sbi_license_key')) {
			$license_key = get_option('sbi_license_key');
		}

		$installed_plugins = get_plugins();

		$images_url = SBI_PLUGIN_URL . 'admin/assets/img/about/';

		// check whether the pro or free plugins are installed
		$is_facebook_installed = false;
		$facebook_plugin = 'custom-facebook-feed/custom-facebook-feed.php';
		if (isset($installed_plugins['custom-facebook-feed-pro/custom-facebook-feed.php'])) {
			$is_facebook_installed = true;
			$facebook_plugin = 'custom-facebook-feed-pro/custom-facebook-feed.php';
		} elseif (isset($installed_plugins['custom-facebook-feed/custom-facebook-feed.php'])) {
			$is_facebook_installed = true;
		}

		$is_instagram_installed = false;
		$instagram_plugin = 'instagram-feed/instagram-feed.php';
		if (isset($installed_plugins['instagram-feed-pro/instagram-feed.php'])) {
			$is_instagram_installed = true;
			$instagram_plugin = 'instagram-feed-pro/instagram-feed.php';
		} elseif (isset($installed_plugins['instagram-feed/instagram-feed.php'])) {
			$is_instagram_installed = true;
		}

		$is_twitter_installed = false;
		$twitter_plugin = 'custom-twitter-feeds/custom-twitter-feed.php';
		if (isset($installed_plugins['custom-twitter-feeds-pro/custom-twitter-feed.php'])) {
			$is_twitter_installed = true;
			$twitter_plugin = 'custom-twitter-feeds-pro/custom-twitter-feed.php';
		} elseif (isset($installed_plugins['custom-twitter-feeds/custom-twitter-feed.php'])) {
			$is_twitter_installed = true;
		}

		$is_youtube_installed = false;
		$youtube_plugin = 'feeds-for-youtube/youtube-feed.php';
		if (isset($installed_plugins['youtube-feed-pro/youtube-feed.php'])) {
			$is_youtube_installed = true;
			$youtube_plugin = 'youtube-feed-pro/youtube-feed.php';
		} elseif (isset($installed_plugins['feeds-for-youtube/youtube-feed.php'])) {
			$is_youtube_installed = true;
		}

		$is_tiktok_installed = false;
		$tiktok_plugin = 'feeds-for-tiktok/feeds-for-tiktok.php';
		if (isset($installed_plugins['tiktok-feeds-pro/tiktok-feeds-pro.php'])) {
			$is_tiktok_installed = true;
			$tiktok_plugin = 'tiktok-feeds-pro/tiktok-feeds-pro.php';
		} elseif (isset($installed_plugins['feeds-for-tiktok/feeds-for-tiktok.php'])) {
			$is_tiktok_installed = true;
		}

		$is_reviews_installed = false;
		$reviews_plugin = 'reviews-feed/sb-reviews.php';
		if (
			isset(
				$installed_plugins['reviews-feed-pro/sb-reviews-pro.php']
			)
		) {
			$is_reviews_installed = true;
			$reviews_plugin = 'reviews-feed-pro/sb-reviews-pro.php';
		} elseif (isset($installed_plugins['reviews-feed/sb-reviews.php'])) {
			$is_reviews_installed = true;
		}

		$return = array(
			'admin_url' => admin_url(),
			'supportPageUrl' => admin_url('admin.php?page=sbi-support'),
			'ajax_handler' => admin_url('admin-ajax.php'),
			'links' => SBI_Feed_Builder::get_links_with_utm(),
			'nonce' => wp_create_nonce('sbi-admin'),
			'socialWallLinks' => SBI_Feed_Builder::get_social_wall_links(),
			'socialWallActivated' => is_plugin_active('social-wall/social-wall.php'),
			'genericText' => array(
				'help' => __('Help', 'instagram-feed'),
				'title' => __('About Us', 'instagram-feed'),
				'title2' => __('Our Other Social Media Feed Plugins', 'instagram-feed'),
				'title3' => __('Plugins we recommend', 'instagram-feed'),
				'description2' => sprintf(__('We’re more than just an Instagram plugin! %s Check out our other plugins and add more content to your site.', 'instagram-feed'), '<br>'),
			),
			'aboutBox' => array(
				'atSmashBalloon' => __('At Smash Balloon, we build software that helps you create beautiful responsive social media feeds for your website in minutes.', 'instagram-feed'),
				'weAreOn' => __('We\'re on a mission to make it super simple to add social media feeds in WordPress. No more complicated setup steps, ugly iframe widgets, or negative page speed scores.', 'instagram-feed'),
				'ourPlugins' => __('Our plugins aren\'t just easy to use, but completely customizable, reliable, and fast! Which is why over 1.6 million awesome users, just like you, choose to use them on their site.', 'instagram-feed'),
				'teamAvatar' => SBI_PLUGIN_URL . 'admin/assets/img/team-avatar.png',
				'teamImgAlt' => __('Smash Balloon Team', 'instagram-feed'),
			),
			'pluginsInfo' => array(
				'instagram' => array(
					'plugin' => $instagram_plugin,
					'download_plugin' => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
					'title' => __('Instagram Feed', 'instagram-feed'),
					'description' => __('An elegant way to add your Instagram posts to your website. ', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/insta-icon.svg',
					'installed' => $is_instagram_installed,
					'activated' => is_plugin_active($instagram_plugin),
				),
				'facebook' => array(
					'plugin' => $facebook_plugin,
					'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
					'title' => __('Custom Facebook Feed', 'instagram-feed'),
					'description' => __('Add Facebook posts from your timeline, albums and much more.', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/fb-icon.svg',
					'installed' => $is_facebook_installed,
					'activated' => is_plugin_active($facebook_plugin),
				),
				'twitter' => array(
					'plugin' => $twitter_plugin,
					'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
					'title' => __('Custom Twitter Feeds', 'instagram-feed'),
					'description' => __('A customizable way to display tweets from your Twitter account. ', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/twitter-icon.svg',
					'installed' => $is_twitter_installed,
					'activated' => is_plugin_active($twitter_plugin),
				),
				'youtube' => array(
					'plugin' => $youtube_plugin,
					'download_plugin' => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
					'title' => __('Feeds for YouTube', 'instagram-feed'),
					'description' => __('A simple yet powerful way to display videos from YouTube. ', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/youtube-icon.svg',
					'installed' => $is_youtube_installed,
					'activated' => is_plugin_active($youtube_plugin),
				),
				'tiktok' => array(
					'plugin' => $tiktok_plugin,
					'download_plugin' => 'https://downloads.wordpress.org/plugin/feeds-for-tiktok.zip',
					'title' => __('TikTok Feeds', 'instagram-feed'),
					'description' => __('Display customizable TikTok feeds in WordPress', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/tiktok-icon.svg',
					'installed' => $is_tiktok_installed,
					'activated' => is_plugin_active($tiktok_plugin),
				),
				'reviews' => array(
					'plugin' => $reviews_plugin,
					'download_plugin' => 'https://downloads.wordpress.org/plugin/reviews-feed.zip',
					'title' => __('Reviews Feed', 'instagram-feed'),
					'description' => __('Display reviews from Google, Facebook, Yelp, and more', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/reviews-icon.svg',
					'installed' => $is_reviews_installed,
					'activated' => is_plugin_active($reviews_plugin),
				),
			),
			'proPluginsInfo' => array(
				'social_wall' => array(
					'plugin' => 'social-wall/social-wall.php',
					'title' => __('Social Wall', 'instagram-feed'),
					'description' => __('Combine feeds from all of our plugins into a single wall', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/social-wall-icon.svg',
					'permalink' => sprintf('https://smashballoon.com/social-wall/demo?license_key=%s&upgrade=true&utm_campaign=instagram-free&utm_source=about&utm_medium=social-wall', $license_key),
					'installed' => isset($installed_plugins['social-wall/social-wall.php']) ? true : false,
					'activated' => is_plugin_active('social-wall/social-wall.php'),
				),
				'feed_analytics' => array(
					'plugin' => 'sb-analytics/sb-analytics-pro.php',
					'title' => __('Feed Analytics', 'instagram-feed'),
					'description' => __('Get in depth analytics on all your social feeds in a single place', 'instagram-feed'),
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/feed-analytics-icon.svg',
					'permalink' => sprintf('https://smashballoon.com/feed-analytics?license_key=%s&upgrade=true&utm_campaign=instagram-free&utm_source=about&utm_medium=feed-analytics', $license_key),
					'installed' => isset($installed_plugins['sb-analytics/sb-analytics-pro.php']) ? true : false,
					'activated' => is_plugin_active('sb-analytics/sb-analytics-pro.php'),
				),
			),
			'recommendedPlugins' => array(
				'aioseo' => array(
					'plugin' => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip',
					'title' => __('All in One SEO Pack', 'instagram-feed'),
					'description' => __('The original WordPress SEO plugin and toolkit that improves your website’s search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-seo.png',
					'installed' => isset($installed_plugins['all-in-one-seo-pack/all_in_one_seo_pack.php']) ? true : false,
					'activated' => is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php'),
				),
				'wpforms' => array(
					'plugin' => 'wpforms-lite/wpforms.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/wpforms-lite.zip',
					'title' => __('WPForms', 'instagram-feed'),
					'description' => __('The best drag & drop WordPress form builder. Easily create beautiful contact forms, surveys, payment forms, and more with our 900+ form templates. Trusted by over 6 million websites as the best forms plugin.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-wpforms.png',
					'installed' => isset($installed_plugins['wpforms-lite/wpforms.php']) ? true : false,
					'activated' => is_plugin_active('wpforms-lite/wpforms.php'),
				),
				'monsterinsights' => array(
					'plugin' => 'google-analytics-for-wordpress/googleanalytics.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip',
					'title' => __('MonsterInsights', 'instagram-feed'),
					'description' => __('The leading WordPress analytics plugin that shows you how people find and use your website, so you can make data driven decisions to grow your business. Properly set up Google Analytics without writing code.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-mi.png',
					'installed' => isset($installed_plugins['google-analytics-for-wordpress/googleanalytics.php']) ? true : false,
					'activated' => is_plugin_active('google-analytics-for-wordpress/googleanalytics.php'),
				),
				'optinmonster' => array(
					'plugin' => 'optinmonster/optin-monster-wp-api.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/optinmonster.zip',
					'title' => __('OptinMonster', 'instagram-feed'),
					'description' => __('Instantly get more subscribers, leads, and sales with the #1 conversion optimization toolkit. Create high converting popups, announcement bars, spin a wheel, and more with smart targeting and personalization.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-om.png',
					'installed' => isset($installed_plugins['optinmonster/optin-monster-wp-api.php']) ? true : false,
					'activated' => is_plugin_active('optinmonster/optin-monster-wp-api.php'),
				),
				'wp_mail_smtp' => array(
					'plugin' => 'wp-mail-smtp/wp_mail_smtp.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/wp-mail-smtp.zip',
					'title' => __('WP Mail SMTP', 'instagram-feed'),
					'description' => __('Improve your WordPress email deliverability and make sure that your website emails reach user’s inbox with the #1 SMTP plugin for WordPress. Over 3 million websites use it to fix WordPress email issues.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-smtp.png',
					'installed' => isset($installed_plugins['wp-mail-smtp/wp_mail_smtp.php']) ? true : false,
					'activated' => is_plugin_active('wp-mail-smtp/wp_mail_smtp.php'),
				),
				'rafflepress' => array(
					'plugin' => 'rafflepress/rafflepress.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/rafflepress.zip',
					'title' => __('RafflePress', 'instagram-feed'),
					'description' => __('Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-rp.png',
					'installed' => isset($installed_plugins['rafflepress/rafflepress.php']) ? true : false,
					'activated' => is_plugin_active('rafflepress/rafflepress.php'),
				),
				'seedprod' => array(
					'plugin' => 'coming-soon/coming-soon.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/coming-soon.zip',
					'title' => __('SeedProd Website Builder', 'instagram-feed'),
					'description' => __('The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect a CRM, collect subscribers, and grow an audience. Trusted by 1 million sites.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-seedProd.png',
					'installed' => isset($installed_plugins['coming-soon/coming-soon.php']) ? true : false,
					'activated' => is_plugin_active('coming-soon/coming-soon.php'),
				),
				'pushengage' => array(
					'plugin' => 'pushengage/main.php',
					'download_plugin' => 'https://downloads.wordpress.org/plugin/pushengage.zip',
					'title' => __('PushEngage Web Push Notifications', 'instagram-feed'),
					'description' => __('Connect with your visitors after they leave your website with the leading web push notification software. Over 10,000+ businesses worldwide use PushEngage to send 15 billion notifications each month.', 'instagram-feed'),
					'icon' => $images_url . 'plugin-pushengage.png',
					'installed' => isset($installed_plugins['pushengage/main.php']) ? true : false,
					'activated' => is_plugin_active('pushengage/main.php'),
				)
			),
			'buttons' => array(
				'add' => __('Add', 'instagram-feed'),
				'viewDemo' => __('View Demo', 'instagram-feed'),
				'install' => __('Install', 'instagram-feed'),
				'installed' => __('Installed', 'instagram-feed'),
				'activate' => __('Activate', 'instagram-feed'),
				'deactivate' => __('Deactivate', 'instagram-feed'),
				'open' => __('Open', 'instagram-feed'),
			),
			'icons' => array(
				'plusIcon' => '<svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0832 6.83317H7.08317V11.8332H5.4165V6.83317H0.416504V5.1665H5.4165V0.166504H7.08317V5.1665H12.0832V6.83317Z" fill="white"/></svg>',
				'loaderSVG' => '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>',
				'checkmarkSVG' => '<svg width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.13112 6.88917L11.4951 0.525204L12.9093 1.93942L5.13112 9.71759L0.888482 5.47495L2.3027 4.06074L5.13112 6.88917Z" fill="#8C8F9A"/></svg>',
				'link' => '<svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.333374 9.22668L7.39338 2.16668H3.00004V0.833344H9.66671V7.50001H8.33338V3.10668L1.27337 10.1667L0.333374 9.22668Z" fill="#141B38"/></svg>',
				'installIcon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.213 2.84015C11.2019 2.01003 9.96799 1.49743 8.66634 1.36682V2.71349C9.63967 2.83349 10.5263 3.22015 11.2663 3.79349L12.213 2.84015ZM13.2863 7.33349H14.633C14.4997 5.99349 13.9663 4.77349 13.1597 3.78682L12.2063 4.73349C12.7944 5.48679 13.1676 6.38523 13.2863 7.33349ZM12.2063 11.2668L13.1597 12.2202C13.9887 11.2084 14.5012 9.97482 14.633 8.67349H13.2863C13.1663 9.61938 12.7932 10.5153 12.2063 11.2668ZM8.66634 13.2868V14.6335C10.0063 14.5002 11.2263 13.9668 12.213 13.1602L11.2597 12.2068C10.5263 12.7802 9.63967 13.1668 8.66634 13.2868ZM10.393 7.06015L8.66634 8.78015V4.66682H7.33301V8.78015L5.60634 7.05349L4.66634 8.00015L7.99967 11.3335L11.333 8.00015L10.393 7.06015ZM7.33301 13.2868V14.6335C3.96634 14.3002 1.33301 11.4602 1.33301 8.00015C1.33301 4.54015 3.96634 1.70015 7.33301 1.36682V2.71349C4.69967 3.04015 2.66634 5.28015 2.66634 8.00015C2.66634 10.7202 4.69967 12.9602 7.33301 13.2868Z" fill="#141B38"/></svg>',
			),
		);

		return $return;
	}

	/**
	 * About Us Page View Template
	 *
	 * @since 4.0
	 */
	public function about_us()
	{
		SBI_View::render('about.page');
	}
}
