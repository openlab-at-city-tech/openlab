<?php
/**
 * Welcome class.
 *
 * @since 1.8.1
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

namespace Imagely\NGG\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Imagely\NGG\Util\Installer_Skin;

/**
 * Welcome Class
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team <support@enviragallery.com>
 */
class About {

	/**
	 * Envira Welcome Pages.
	 *
	 * @var array
	 */
	public $pages = [
		'nextgen-about-us',
	];

	/**
	 * Holds the submenu pagehook.
	 *
	 * @since 1.7.0
	 *
	 * @var string`
	 */
	public $hook;

	/**
	 * Helper method for installed plugins
	 *
	 * @since 1.7.0
	 *
	 * @var array
	 */
	public $installed_plugins;

	/**
	 * Class Hooks
	 *
	 * @since 1.8.7
	 *
	 * @return void
	 */
	public function hooks() {

		// Add custom addons submenu.
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 15 );

		// Add scripts and styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

		// Misc.
		add_action( 'admin_print_scripts', [ $this, 'disable_admin_notices' ] );

		// Ajax.
		add_action( 'wp_ajax_nextgen_install_am_plugin', [ $this, 'install_am_plugin' ] );
		add_action( 'wp_ajax_nextgen_deactivate_am_plugin', [ $this, 'deactivate_am_plugin' ] );
		add_action( 'wp_ajax_nextgen_activate_am_plugin', [ $this, 'activate_am_plugin' ] );
	}


	/**
	 * Register and enqueue addons page specific JS.
	 *
	 * @since 1.5.0
	 */
	public function enqueue_admin_scripts() {
		global $current_screen;

		if ( ! empty( $current_screen->base ) && strpos( $current_screen->base, 'nextgen-gallery_page' ) !== false ) {

			wp_register_script( NGG_PLUGIN_SLUG . '-about-script', plugins_url( 'assets/js/min/about-min.js', NGG_PLUGIN_FILE ), [ 'jquery' ], NGG_PLUGIN_VERSION, true );
			wp_enqueue_script( NGG_PLUGIN_SLUG . '-about-script' );
			wp_localize_script(
				NGG_PLUGIN_SLUG . '-about-script',
				'nextgen_about',
				[
					'ajax'             => admin_url( 'admin-ajax.php' ),
					'activate_nonce'   => wp_create_nonce( 'nextgen-activate-partner' ),
					'deactivate_nonce' => wp_create_nonce( 'nextgen-deactivate-partner' ),
					'install_nonce'    => wp_create_nonce( 'nextgen-install-partner' ),
					'active'           => __( 'Status: Active', 'nggallery' ),
					'activate'         => __( 'Activate', 'nggallery' ),
					'activating'       => __( 'Activating...', 'nggallery' ),
					'deactivate'       => __( 'Deactivate', 'nggallery' ),
					'deactivating'     => __( 'Deactivating...', 'nggallery' ),
					'inactive'         => __( 'Status: Inactive', 'nggallery' ),
					'install'          => __( 'Install', 'nggallery' ),
					'installing'       => __( 'Installing...', 'nggallery' ),
					'proceed'          => __( 'Proceed', 'nggallery' ),
				]
			);
		}
	}

	/**
	 * Register and enqueue addons page specific CSS.
	 *
	 * @since 1.8.1
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {

		global $current_screen;

		if ( ! empty( $current_screen->base ) && strpos( $current_screen->base, 'nextgen-gallery_page' ) !== false ) {

			wp_register_style( NGG_PLUGIN_SLUG . '-about-style', plugins_url( 'assets/css/about.css', NGG_PLUGIN_FILE ), [], NGG_PLUGIN_VERSION );
			wp_enqueue_style( NGG_PLUGIN_SLUG . '-about-style' );

		}

		// Run a hook to load in custom styles.
		do_action( 'nextgen_about_styles' );
	}

	/**
	 * Making page as clean as possible
	 *
	 * @since 1.8.1
	 *
	 * @return void
	 */
	public function disable_admin_notices() {

		global $wp_filter;

		global $current_screen;

		if ( ! empty( $current_screen->base ) && strpos( $current_screen->base, 'nextgen-gallery_page' ) !== false ) {

			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}
	}

	/**
	 * Helper Method to get AM Plugins
	 *
	 * @since 1.8.7
	 *
	 * @return array
	 */
	public function get_am_plugins() {

		$images_url = trailingslashit( NGG_PLUGIN_URI . 'assets/images/about' );
		$plugins    = [
			'optinmonster'                                 => [
				'icon'        => $images_url . 'plugin-om.png',
				'name'        => esc_html__( 'OptinMonster', 'nggallery' ),
				'description' => esc_html__( 'Instantly get more subscribers, leads, and sales with the #1 conversion optimization toolkit. Create high converting popups, announcement bars, spin a wheel, and more with smart targeting and personalization.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/optinmonster/',
				'url'         => 'https://downloads.wordpress.org/plugin/optinmonster.zip',
				'basename'    => 'optinmonster/optin-monster-wp-api.php',
			],
			'google-analytics-for-wordpress'               => [
				'icon'        => $images_url . 'plugin-mi.png',
				'name'        => esc_html__( 'MonsterInsights', 'nggallery' ),
				'description' => esc_html__( 'The leading WordPress analytics plugin that shows you how people find and use your website, so you can make data driven decisions to grow your business. Properly set up Google Analytics without writing code.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/google-analytics-for-wordpress/',
				'url'         => 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip',
				'basename'    => 'google-analytics-for-wordpress/googleanalytics.php',
				'pro'         => [
					'plug'        => 'google-analytics-premium/googleanalytics-premium.php',
					'icon'        => $images_url . 'plugin-mi.png',
					'name'        => esc_html__( 'MonsterInsights Pro', 'nggallery' ),
					'description' => esc_html__( 'The leading WordPress analytics plugin that shows you how people find and use your website, so you can make data driven decisions to grow your business. Properly set up Google Analytics without writing code.', 'nggallery' ),
					'url'         => 'https://www.monsterinsights.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'wp-mail-smtp/wp_mail_smtp.php'                => [
				'icon'        => $images_url . 'plugin-smtp.png',
				'name'        => esc_html__( 'WP Mail SMTP', 'nggallery' ),
				'description' => esc_html__( "Improve your WordPress email deliverability and make sure that your website emails reach user's inbox with the #1 SMTP plugin for WordPress. Over 3 million websites use it to fix WordPress email issues.", 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/wp-mail-smtp/',
				'url'         => 'https://downloads.wordpress.org/plugin/wp-mail-smtp.zip',
				'basename'    => 'wp-mail-smtp/wp_mail_smtp.php',
				'pro'         => [
					'plug'        => 'wp-mail-smtp-pro/wp_mail_smtp.php',
					'icon'        => $images_url . 'plugin-smtp.png',
					'name'        => esc_html__( 'WP Mail SMTP Pro', 'nggallery' ),
					'description' => esc_html__( "Improve your WordPress email deliverability and make sure that your website emails reach user's inbox with the #1 SMTP plugin for WordPress. Over 3 million websites use it to fix WordPress email issues.", 'nggallery' ),
					'url'         => 'https://wpmailsmtp.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'all-in-one-seo-pack/all_in_one_seo_pack.php'  => [
				'icon'        => $images_url . 'plugin-aioseo.png',
				'name'        => esc_html__( 'AIOSEO', 'nggallery' ),
				'description' => esc_html__( "The original WordPress SEO plugin and toolkit that improves your website's search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.", 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/all-in-one-seo-pack/',
				'url'         => 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip',
				'basename'    => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
				'pro'         => [
					'plug'        => 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php',
					'icon'        => $images_url . 'plugin-aioseo.png',
					'name'        => esc_html__( 'AIOSEO Pro', 'nggallery' ),
					'description' => esc_html__( "The original WordPress SEO plugin and toolkit that improves your website's search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.", 'nggallery' ),
					'url'         => 'https://aioseo.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'coming-soon/coming-soon.php'                  => [
				'icon'        => $images_url . 'plugin-seedprod.png',
				'name'        => esc_html__( 'SeedProd', 'nggallery' ),
				'description' => esc_html__( 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/coming-soon/',
				'url'         => 'https://downloads.wordpress.org/plugin/coming-soon.zip',
				'basename'    => 'coming-soon/coming-soon.php',
				'pro'         => [
					'plug'        => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php',
					'icon'        => $images_url . 'plugin-seedprod.png',
					'name'        => esc_html__( 'SeedProd Pro', 'nggallery' ),
					'description' => esc_html__( 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.', 'nggallery' ),
					'url'         => 'https://www.seedprod.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'rafflepress/rafflepress.php'                  => [
				'icon'        => $images_url . 'plugin-rp.png',
				'name'        => esc_html__( 'RafflePress', 'nggallery' ),
				'description' => esc_html__( 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/rafflepress/',
				'url'         => 'https://downloads.wordpress.org/plugin/rafflepress.zip',
				'basename'    => 'rafflepress/rafflepress.php',
				'pro'         => [
					'plug'        => 'rafflepress-pro/rafflepress-pro.php',
					'icon'        => $images_url . 'plugin-rp.png',
					'name'        => esc_html__( 'RafflePress Pro', 'nggallery' ),
					'description' => esc_html__( 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'nggallery' ),
					'url'         => 'https://rafflepress.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'pushengage/main.php'                          => [
				'icon'        => $images_url . 'plugin-pushengage.png',
				'name'        => esc_html__( 'PushEngage', 'nggallery' ),
				'description' => esc_html__( 'Connect with your visitors after they leave your website with the leading web push notification software. Over 10,000+ businesses worldwide use PushEngage to send 15 billion notifications each month.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/pushengage/',
				'url'         => 'https://downloads.wordpress.org/plugin/pushengage.zip',
				'basename'    => 'pushengage/main.php',
			],

			'instagram-feed/instagram-feed.php'            => [
				'icon'        => $images_url . 'plugin-sb-instagram.png',
				'name'        => esc_html__( 'Smash Balloon Instagram Feeds', 'nggallery' ),
				'description' => esc_html__( 'Easily display Instagram content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/instagram-feed/',
				'url'         => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
				'basename'    => 'instagram-feed/instagram-feed.php',
				'pro'         => [
					'plug'        => 'instagram-feed-pro/instagram-feed.php',
					'icon'        => $images_url . 'plugin-sb-instagram.png',
					'name'        => esc_html__( 'Smash Balloon Instagram Feeds Pro', 'nggallery' ),
					'description' => esc_html__( 'Easily display Instagram content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'nggallery' ),
					'url'         => 'https://smashballoon.com/instagram-feed/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'custom-facebook-feed/custom-facebook-feed.php' => [
				'icon'        => $images_url . 'plugin-sb-fb.png',
				'name'        => esc_html__( 'Smash Balloon Facebook Feeds', 'nggallery' ),
				'description' => esc_html__( 'Easily display Facebook content on your WordPress site without writing any code. Comes with multiple templates, ability to embed albums, group content, reviews, live videos, comments, and reactions.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/custom-facebook-feed/',
				'url'         => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
				'basename'    => 'custom-facebook-feed/custom-facebook-feed.php',
				'pro'         => [
					'plug'        => 'custom-facebook-feed-pro/custom-facebook-feed.php',
					'icon'        => $images_url . 'plugin-sb-fb.png',
					'name'        => esc_html__( 'Smash Balloon Facebook Feeds Pro', 'nggallery' ),
					'description' => esc_html__( 'Easily display Facebook content on your WordPress site without writing any code. Comes with multiple templates, ability to embed albums, group content, reviews, live videos, comments, and reactions.', 'nggallery' ),
					'url'         => 'https://smashballoon.com/custom-facebook-feed/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'feeds-for-youtube/youtube-feed.php'           => [
				'icon'        => $images_url . 'plugin-sb-youtube.png',
				'name'        => esc_html__( 'Smash Balloon YouTube Feeds', 'nggallery' ),
				'description' => esc_html__( 'Easily display YouTube videos on your WordPress site without writing any code. Comes with multiple layouts, ability to embed live streams, video filtering, ability to combine multiple channel videos, and more.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/feeds-for-youtube/',
				'url'         => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
				'basename'    => 'feeds-for-youtube/youtube-feed.php',
				'pro'         => [
					'plug'        => 'youtube-feed-pro/youtube-feed.php',
					'icon'        => $images_url . 'plugin-sb-youtube.png',
					'name'        => esc_html__( 'Smash Balloon YouTube Feeds Pro', 'nggallery' ),
					'description' => esc_html__( 'Easily display YouTube videos on your WordPress site without writing any code. Comes with multiple layouts, ability to embed live streams, video filtering, ability to combine multiple channel videos, and more.', 'nggallery' ),
					'url'         => 'https://smashballoon.com/youtube-feed/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'custom-twitter-feeds/custom-twitter-feed.php' => [
				'icon'        => $images_url . 'plugin-sb-twitter.png',
				'name'        => esc_html__( 'Smash Balloon Twitter Feeds', 'nggallery' ),
				'description' => esc_html__( 'Easily display Twitter content in WordPress without writing any code. Comes with multiple layouts, ability to combine multiple Twitter feeds, Twitter card support, tweet moderation, and more.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/custom-twitter-feeds/',
				'url'         => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
				'basename'    => 'custom-twitter-feeds/custom-twitter-feed.php',
				'pro'         => [
					'plug'        => 'custom-twitter-feeds-pro/custom-twitter-feed.php',
					'icon'        => $images_url . 'plugin-sb-twitter.png',
					'name'        => esc_html__( 'Smash Balloon Twitter Feeds Pro', 'nggallery' ),
					'description' => esc_html__( 'Easily display Twitter content in WordPress without writing any code. Comes with multiple layouts, ability to combine multiple Twitter feeds, Twitter card support, tweet moderation, and more.', 'nggallery' ),
					'url'         => 'https://smashballoon.com/custom-twitter-feeds/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'trustpulse-api/trustpulse.php'                => [
				'icon'        => $images_url . 'plugin-trustpulse.png',
				'name'        => esc_html__( 'TrustPulse', 'nggallery' ),
				'description' => esc_html__( 'Boost your sales and conversions by up to 15% with real-time social proof notifications. TrustPulse helps you show live user activity and purchases to help convince other users to purchase.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/trustpulse-api/',
				'url'         => 'https://downloads.wordpress.org/plugin/trustpulse-api.zip',
				'basename'    => 'trustpulse-api/trustpulse.php',
			],
			'searchwp/index.php'                           => [
				'icon'        => $images_url . 'plugin-searchwp.png',
				'name'        => esc_html__( 'SearchWP', 'nggallery' ),
				'description' => esc_html__( 'The most advanced WordPress search plugin. Customize your WordPress search algorithm, reorder search results, track search metrics, and everything you need to leverage search to grow your business.', 'nggallery' ),
				'wporg'       => false,
				'url'         => 'https://searchwp.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
				'act'         => 'go-to-url',
			],
			'affiliate-wp/affiliate-wp.php'                => [
				'icon'        => $images_url . 'plugin-affwp.png',
				'name'        => esc_html__( 'AffiliateWP', 'nggallery' ),
				'description' => esc_html__( 'The #1 affiliate management plugin for WordPress. Easily create an affiliate program for your eCommerce store or membership site within minutes and start growing your sales with the power of referral marketing.', 'nggallery' ),
				'wporg'       => false,
				'url'         => 'https://affiliatewp.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
				'act'         => 'go-to-url',
			],
			'stripe/stripe-checkout.php'                   => [
				'icon'        => $images_url . 'plugin-wp-simple-pay.png',
				'name'        => esc_html__( 'WP Simple Pay', 'nggallery' ),
				'description' => esc_html__( 'The #1 Stripe payments plugin for WordPress. Start accepting one-time and recurring payments on your WordPress site without setting up a shopping cart. No code required.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/stripe/',
				'url'         => 'https://downloads.wordpress.org/plugin/stripe.zip',
				'basename'    => 'stripe/stripe-checkout.php',
				'pro'         => [
					'plug'        => 'wp-simple-pay-pro-3/simple-pay.php',
					'icon'        => $images_url . 'plugin-wp-simple-pay.png',
					'name'        => esc_html__( 'WP Simple Pay Pro', 'nggallery' ),
					'description' => esc_html__( 'The #1 Stripe payments plugin for WordPress. Start accepting one-time and recurring payments on your WordPress site without setting up a shopping cart. No code required.', 'nggallery' ),
					'url'         => 'https://wpsimplepay.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],

			'easy-digital-downloads/easy-digital-downloads.php' => [
				'icon'        => $images_url . 'plugin-edd.png',
				'name'        => esc_html__( 'Easy Digital Downloads', 'nggallery' ),
				'description' => esc_html__( 'The best WordPress eCommerce plugin for selling digital downloads. Start selling eBooks, software, music, digital art, and more within minutes. Accept payments, manage subscriptions, advanced access control, and more.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/easy-digital-downloads/',
				'url'         => 'https://downloads.wordpress.org/plugin/easy-digital-downloads.zip',
				'basename'    => 'easy-digital-downloads/easy-digital-downloads.php',
			],

			'sugar-calendar-lite/sugar-calendar-lite.php'  => [
				'icon'        => $images_url . 'plugin-sugarcalendar.png',
				'name'        => esc_html__( 'Sugar Calendar', 'nggallery' ),
				'description' => esc_html__( 'A simple & powerful event calendar plugin for WordPress that comes with all the event management features including payments, scheduling, timezones, ticketing, recurring events, and more.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/sugar-calendar-lite/',
				'url'         => 'https://downloads.wordpress.org/plugin/sugar-calendar-lite.zip',
				'basename'    => 'sugar-calendar-lite/sugar-calendar-lite.php',
				'pro'         => [
					'plug'        => 'sugar-calendar/sugar-calendar.php',
					'icon'        => $images_url . 'plugin-sugarcalendar.png',
					'name'        => esc_html__( 'Sugar Calendar Pro', 'nggallery' ),
					'description' => esc_html__( 'A simple & powerful event calendar plugin for WordPress that comes with all the event management features including payments, scheduling, timezones, ticketing, recurring events, and more.', 'nggallery' ),
					'url'         => 'https://sugarcalendar.com/?utm_source=enviragallerylite&utm_medium=link&utm_campaign=About%20Envira',
					'act'         => 'go-to-url',
				],
			],
			'charitable/charitable.php'                    => [
				'icon'        => $images_url . 'plugin-charitable.png',
				'name'        => esc_html__( 'WP Charitable', 'nggallery' ),
				'description' => esc_html__( 'Top-rated WordPress donation and fundraising plugin. Over 10,000+ non-profit organizations and website owners use Charitable to create fundraising campaigns and raise more money online.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/charitable/',
				'url'         => 'https://downloads.wordpress.org/plugin/charitable.zip',
				'basename'    => 'charitable/charitable.php',
			],
			'insert-headers-and-footers/ihaf.php'          => [
				'icon'        => $images_url . 'plugin-wpcode.png',
				'name'        => esc_html__( 'WPCode', 'nggallery' ),
				'description' => esc_html__( 'Future proof your WordPress customizations with the most popular code snippet management plugin for WordPress. Trusted by over 1,500,000+ websites for easily adding code to WordPress right from the admin area.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/insert-headers-and-footers/',
				'url'         => 'https://downloads.wordpress.org/plugin/insert-headers-and-footers.zip',
				'basename'    => 'insert-headers-and-footers/ihaf.php',
			],
			'duplicator/duplicator.php'                    => [
				'icon'        => $images_url . 'plugin-duplicator.png',
				'name'        => esc_html__( 'Duplicator', 'nggallery' ),
				'description' => esc_html__( 'Leading WordPress backup & site migration plugin. Over 1,500,000+ smart website owners use Duplicator to make reliable and secure WordPress backups to protect their websites. It also makes website migration really easy.', 'nggallery' ),
				'wporg'       => 'https://wordpress.org/plugins/duplicator/',
				'url'         => 'https://downloads.wordpress.org/plugin/duplicator.zip',
				'basename'    => 'duplicator/duplicator.php',
			],
			'soliloquy'                                    => [
				'icon'        => $images_url . 'soliloquy.png',
				'name'        => esc_html__( 'Slider by Soliloquy – Responsive Image Slider for WordPress', 'nggallery' ),
				'description' => esc_html__( 'The best WordPress slider plugin. Drag & Drop responsive slider builder that helps you create a beautiful image slideshows with just a few clicks.', 'nggallery' ),
				'url'         => 'https://downloads.wordpress.org/plugin/soliloquy-lite.zip',
				'basename'    => 'soliloquy-lite/soliloquy-lite.php',
			],
		];

		return $plugins;
	}

	/**
	 * Register the Welcome submenu item for Envira.
	 *
	 * @since 1.8.1
	 *
	 * @return void
	 */
	public function admin_menu() {

		global $submenu;

		$whitelabel = apply_filters( 'nextgen_whitelabel', false ) ? '' : esc_html__( 'NextGen Gallery ', 'nggallery' );

		// Register the submenus.
		add_submenu_page(
			NGGFOLDER,
			$whitelabel . esc_html__( 'About Us', 'nggallery' ),
			'<span style="color:#FFA500"> ' . esc_html__( 'About Us', 'nggallery' ) . '</span>',
			apply_filters( 'nextgen_menu_cap', 'manage_options' ),
			NGG_PLUGIN_SLUG . '-about-us',
			[ $this, 'about_page' ]
		);
	}

	/**
	 * Output tab navigation
	 *
	 * @since 2.2.0
	 *
	 * @param string $tab Tab to highlight as active.
	 */
	public static function tab_navigation( $tab = 'whats_new' ) {
		?>

		<ul class="nextgen-nav-tab-wrapper">
			<li>
			<a class="nextgen-nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'nextgen-gallery-about-us' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				nextgen-nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							[
								'post_type' => 'envira',
								'page'      => 'nextgen-about-us',
							],
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'About Us', 'nggallery' ); ?>
			</a>
			</li>

		</ul>

		<?php
	}

	/**
	 * Output the about screen.
	 *
	 * @since 1.8.5
	 */
	public function about_page() {

		self::tab_navigation( __METHOD__ );
		?>
		<div class="nextgen-welcome-wrap nextgen-about">
			<div class="nextgen-panel nextgen-lite-about-panel">
				<div class="content">
					<h3><?php esc_html_e( 'Hello and welcome to NextGEN Gallery, the most beginner-friendly WordPress Gallery Plugin. At NextGEN Gallery, we build software that helps you create beautiful galleries in minutes.', 'nggallery' ); ?></h3>
					<p><?php esc_html_e( 'Over the years, we found that most WordPress gallery plugins were bloated, buggy, slow, and very hard to use. So, we started with a simple goal: build a WordPress gallery system that’s both easy and powerful.', 'nggallery' ); ?></p>
					<p><?php esc_html_e( 'Our goal is to provide the easiest way to create beautiful galleries.', 'nggallery' ); ?></p>
					<p><?php esc_html_e( 'NextGEN Gallery is brought to you by the same team that’s behind the largest WordPress resource site, WPBeginner, the most popular lead-generation software, OptinMonster, the best WordPress analytics plugin, MonsterInsights, and more!', 'nggallery' ); ?></p>
					<p><?php esc_html_e( 'Yup, we know a thing or two about building awesome products that customers love.', 'nggallery' ); ?></p>
				</div>
				<div class="image">
					<img src="<?php echo esc_url( trailingslashit( NGG_PLUGIN_URI ) . 'assets/images/about/team.jpg' ); ?> ">
				</div>
			</div>

			<div class="nextgen-am-plugins-wrap">
				<?php
				foreach ( $this->get_am_plugins() as $partner ) :

					$this->get_plugin_card( $partner );

				endforeach;
				?>
			</div>

		</div> <!-- wrap -->

		<?php
	}

	/**
	 * Helper method to get plugin card
	 *
	 * @param mixed $plugin False or plugin data array.
	 * @return void
	 */
	public function get_plugin_card( $plugin = false ) {

		if ( ! $plugin ) {
			return;
		}
		$this->installed_plugins = get_plugins();

		if ( ( isset( $plugin['basename'] ) && ! isset( $this->installed_plugins[ $plugin['basename'] ] ) ) || isset( $plugin['act'] ) ) {
			?>
			<div class="nextgen-am-plugins">
				<div class="nextgen-am-plugins-main">
					<div>
						<img src="<?php echo esc_attr( $plugin['icon'] ); ?>" width="64px" />
					</div>
					<div>
						<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
						<p class="nextgen-am-plugins-excerpt"><?php echo esc_html( $plugin['description'] ); ?></p>
					</div>
				</div>
					<div class="nextgen-am-plugins-footer">
					<div class="nextgen-am-plugins-status">Status:&nbsp;<span>Not Installed</span></div>
						<div class="nextgen-am-plugins-install-wrap">
							<span class="spinner nextgen-am-plugins-spinner"></span>
							<?php if ( isset( $plugin['basename'] ) ) : ?>
								<a href="#" class="button nextgen-primary-button nextgen-am-plugins-install" data-url="<?php echo esc_url( $plugin['url'] ); ?>" data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>">Install Plugin</a>
							<?php else : ?>
								<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button nextgen-primary-button" data-url="<?php echo esc_url( $plugin['url'] ); ?>" >Install Plugin</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php
		} elseif ( isset( $plugin['basename'] ) && is_plugin_active( $plugin['basename'] ) ) {
			?>
							<div class="nextgen-am-plugins">
							<div class="nextgen-am-plugins-main">
								<div>
									<img src="<?php echo esc_attr( $plugin['icon'] ); ?>" width="64px" />
								</div>
								<div>
									<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
								<p class="nextgen-am-plugins-excerpt"><?php echo esc_html( $plugin['description'] ); ?></p>
								</div>
							</div>
								<div class="nextgen-am-plugins-footer">
							<div class="nextgen-am-plugins-status">Status:&nbsp;<span>Active</span></div>
								<div class="nextgen-am-plugins-install-wrap">
								<span class="spinner nextgen-am-plugins-spinner"></span>
							<?php if ( isset( $plugin['basename'] ) ) : ?>
								<a href="#" target="_blank" class="button nextgen-secondary-button nextgen-am-plugins-deactivate" data-url="<?php echo esc_url( $plugin['url'] ); ?>" data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>">Deactivate</a>
							<?php else : ?>
								<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button nextgen-secondary nextgen-am-plugins-deactivate" data-url="<?php echo esc_url( $plugin['url'] ); ?>">Activate</a>
							<?php endif; ?>
						</div>
				</div>
						</div>
			<?php } else { ?>
				<div class="nextgen-am-plugins">
							<div class="nextgen-am-plugins-main">
								<div>
									<img src="<?php echo esc_attr( $plugin['icon'] ); ?>" width="64px" />
								</div>
								<div>
									<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
								<p class="nextgen-am-plugins-excerpt"><?php echo esc_html( $plugin['description'] ); ?></p>
								</div>
							</div>
							<div class="nextgen-am-plugins-footer">
							<div class="nextgen-am-plugins-status">Status:&nbsp;<span>Inactive</span></div>
							<div class="nextgen-am-plugins-install-wrap">
							<span class="spinner nextgen-am-plugins-spinner"></span>

							<?php if ( isset( $plugin['basename'] ) ) : ?>
							<a href="#" target="_blank" class="button nextgen-primary-button nextgen-am-plugins-activate" data-url="<?php echo esc_url( $plugin['url'] ); ?>" data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>">Activate</a>
							<?php else : ?>
								<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button nextgen-primary-button nextgen-am-plugins-activate" data-url="<?php echo esc_url( $plugin['url'] ); ?>">Activate</a>
							<?php endif; ?>
						</div>
				</div>
						</div>
				<?php

			}
	}

	/**
	 * Helper method to activate partner
	 *
	 * @since 1.90
	 *
	 * @return void
	 */
	public function activate_am_plugin() {
		// Run a security check first.
		check_admin_referer( 'nextgen-activate-partner', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You are not allowed to activate plugins.', 'nggallery' ) ] );
		}

		// Activate the addon.
		if ( isset( $_POST['basename'] ) ) {
			$activate = activate_plugin( sanitize_text_field( wp_unslash( $_POST['basename'] ) ) );

			if ( is_wp_error( $activate ) ) {
				echo wp_json_encode( [ 'error' => $activate->get_error_message() ] );
				die;
			}
		}

		echo wp_json_encode( true );
		die;
	}


	/**
	 * Helper method to deactivate partner
	 *
	 * @since 1.90
	 *
	 * @return void
	 */
	public function deactivate_am_plugin() {
		// Run a security check first.
		check_admin_referer( 'nextgen-deactivate-partner', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You are not allowed to deactivate plugins.', 'nggallery' ) ] );
		}

		// Deactivate the addon.
		if ( isset( $_POST['basename'] ) ) {
			$deactivate = deactivate_plugins( sanitize_text_field( wp_unslash( $_POST['basename'] ) ) );
		}

		echo wp_json_encode( true );
		die;
	}

	/**
	 * Helper method to install partner
	 *
	 * @since 1.90
	 *
	 * @return void
	 */
	public function install_am_plugin() {

		check_admin_referer( 'nextgen-install-partner', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You are not allowed to install plugins.', 'nggallery' ) ] );
		}

		// Install the addon.
		if ( isset( $_POST['download_url'] ) ) {

			$download_url = esc_url_raw( wp_unslash( $_POST['download_url'] ) );
			global $hook_suffix;

			// Set the current screen to avoid undefined notices.
			set_current_screen();

			$method = '';
			$url    = esc_url_raw( admin_url( 'admin.php?page=nextgen-gallery-about-us' ) );

			// Start output bufferring to catch the filesystem form if credentials are needed.
			ob_start();
			$creds = request_filesystem_credentials( $url, $method, false, false, null );
			if ( false === $creds ) {
				$form = ob_get_clean();
				echo wp_json_encode( [ 'form' => $form ] );
				die;
			}

			// If we are not authenticated, make it happen now.
			if ( ! WP_Filesystem( $creds ) ) {
				ob_start();
				request_filesystem_credentials( $url, $method, true, false, null );
				$form = ob_get_clean();
				echo wp_json_encode( [ 'form' => $form ] );
				die;
			}

			// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			// Create the plugin upgrader with our custom skin.
			$skin      = new \Imagely\NGG\Util\Installer_Skin();
			$installer = new \Plugin_Upgrader( $skin );
			$installer->install( $download_url );

			// Flush the cache and return the newly installed plugin basename.
			wp_cache_flush();

			if ( $installer->plugin_info() ) {
				$plugin_basename = $installer->plugin_info();

				$active = activate_plugin( $plugin_basename, false, false, true );

				wp_send_json_success( [ 'plugin' => $plugin_basename ] );

				die();
			}
		}

		// Send back a response.
		echo wp_json_encode( true );
		die;
	}
}
