<?php
/**
 * The main loader class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Loader
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK;

use ThemeisleSDK\Common\Module_Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Singleton loader for ThemeIsle SDK.
 */
final class Loader {
	/**
	 * Singleton instance.
	 *
	 * @var Loader instance The singleton instance
	 */
	private static $instance;
	/**
	 * Current loader version.
	 *
	 * @var string $version The class version.
	 */
	private static $version = '2.0.0';
	/**
	 * Holds registered products.
	 *
	 * @var array<Product> The products which use the SDK.
	 */
	private static $products = [];
	/**
	 * Holds available modules to load.
	 *
	 * @var array The modules which SDK will be using.
	 */
	private static $available_modules = [
		'script_loader',
		'dashboard_widget',
		'rollback',
		'uninstall_feedback',
		'licenser',
		'logger',
		'translate',
		'translations',
		'review',
		'recommendation',
		'notification',
		'promotions',
		'welcome',
		'compatibilities',
		'about_us',
		'announcements',
		'featured_plugins',
		'float_widget',
	];
	/**
	 * Holds the labels for the modules.
	 *
	 * @var array The labels for the modules.
	 */
	public static $labels = [
		'announcements'    => [
			'hurry_up'    => 'Hurry up! Only %s left.',
			'sale_live'   => 'Themeisle Black Friday Sale is Live!',
			'learn_more'  => 'Learn more',
			'max_savings' => 'Enjoy Maximum Savings on %s',
		],
		'compatibilities'  => [
			'notice'        => '%s requires a newer version of %s. Please %supdate%s %s %s to the latest version.',
			'notice2'       => '%s update requires a newer version of %s. Please %supdate%s %s %s.',
			'notice_theme'  => '%1$sWarning:%2$s This theme has not been tested with your current version of %1$s%3$s%2$s. Please update %3$s plugin.',
			'notice_plugin' => '%1$sWarning:%2$s This plugin has not been tested with your current version of %1$s%3$s%2$s. Please update %3$s %4$s.',
			'theme'         => 'theme',
			'plugin'        => 'plugin',
		],
		'dashboard_widget' => [
			'title'   => 'WordPress Guides/Tutorials',
			'popular' => 'Popular %s',
			'install' => 'Install',
			'powered' => 'Powered by %s',
		],
		'licenser'         => [
			'activate'            => 'Activate',
			'invalid_msg'         => 'Invalid license.',
			'error_notice'        => 'ERROR: Failed to connect to the license service. Please try again later. Reason: %s',
			'error_notice2'       => 'ERROR: Failed to validate license. Please try again in one minute.',
			'error_invalid'       => 'ERROR: Invalid license provided.',
			'update_license'      => 'Updating this theme will lose any customizations you have made. Cancel to stop, OK to update.',
			'invalid_msg'         => 'Invalid license.',
			'already_active'      => 'License is already active.',
			'notice_update'       => '%1$s is available. %2$sCheck out what\'s%3$s new or %4$supdate now%3$s.',
			'not_active'          => 'License not active.',
			'deactivate'          => 'Deactivate',
			'renew_cta'           => 'Renew license to update',
			'autoactivate_notice' => '%s has been successfully activated using %s license !',
			'valid'               => 'Valid',
			'invalid'             => 'Invalid',
			'notice'              => 'Enter your license from %s purchase history in order to get %s updates',
			'expired'             => 'Your %s\'s License Key has expired. In order to continue receiving support and software updates you must  %srenew%s your license key.',

			'inactive'            => 'In order to benefit from updates and support for %s, please add your license code from your  %spurchase history%s and validate it %shere%s.',
			'no_activations'      => 'No more activations left for %s. You need to upgrade your plan in order to use %s on more websites. If you need assistance, please get in touch with %s staff.',
		],
		'promotions'       => [
			'recommended'     => 'Recommended by %s',
			'installActivate' => 'Install & Activate',
			'preview'         => 'Preview',
			'installing'      => 'Installing',
			'activating'      => 'Activating',
			'connecting'      => 'Connecting to API',
			'learnmore'       => 'Learn More',
			'activate'        => 'Activate',
			'all_set'         => 'Awesome! You are all set!',
			'woo'             => [
				'title'        => 'More extensions from Themeisle',
				'title2'       => 'Recommended extensions',
				'cta_install'  => 'Install',
				'learn_more'   => 'Learn More',
				'dismiss'      => 'Dismiss this suggestion',
				'ppom_title'   => 'Product Add-Ons',
				'ppom_desc'    => 'Add extra custom fields & add-ons on your product pages, like sizes, colors & more.',
				'spark_title1' => 'Wishlist',
				'spark_title2' => 'Multi-Announcement Bars',
				'spark_title3' => 'Advanced Product Review',
				'spark_desc1'  => 'Loyalize your customers by allowing them to save their favorite products.',
				'spark_desc2'  => 'Add a top notification bar on your website to highlight the latest products, offers, or upcoming events.',
				'spark_desc3'  => 'Enable an advanced review section, enlarging the basic review options with lots of capabilities.',
			],
			'optimole'        => [
				'installOptimole' => 'Install Optimole',
				'gotodash'        => 'Go to Optimole dashboard',
				'dismisscta'      => 'Dismiss this notice.',
				'message1'        => 'Increase this page speed and SEO ranking by optimizing images with Optimole.',
				'message3'        => 'Save your server space by storing images to Optimole and deliver them optimized from 400 locations around the globe. Unlimited images, Unlimited traffic.',
				'message4'        => 'This image looks to be too large and would affect your site speed, we recommend you to install Optimole to optimize your images.',
				'message2'        => 'Leverage Optimole\'s full integration with Elementor to automatically lazyload, resize, compress to AVIF/WebP and deliver from 400 locations around the globe!',
			],
			'redirectionCF7'  => [
				'gotodash'   => 'Go to Contact Forms',
				'dismisscta' => 'Dismiss this notice.',
				'gst'        => 'Get Started Free',
				'message'    => 'Add URL redirects, spam protection, execute JavaScript after submissions, and more with the Redirection for CF7 free plugin.',
			],
		],
		'welcome'          => [
			'ctan'    => 'No, thanks.',
			'ctay'    => 'Upgrade Now!',
			'message' => '<p>You\'ve been using <b>{product}</b> for 7 days now and we appreciate your loyalty! We also want to make sure you\'re getting the most out of our product. That\'s why we\'re offering you a special deal - upgrade to <b>{pro_product}</b> in the next 5 days and receive a discount of <b>up to 30%</b>. <a href="{cta_link}" target="_blank">Upgrade now</a> and unlock all the amazing features of <b>{pro_product}</b>!</p>',
		],
		'uninstall'        => [
			'heading_plugin' => 'What\'s wrong?',
			'heading_theme'  => 'What does not work for you in {theme}?',
			'submit'         => 'Submit',
			'cta_info'       => 'What info do we collect?',
			'button_submit'  => 'Submit &amp; Deactivate',
			'button_cancel'  => 'Skip &amp; Deactivate',
			'disclosure'     => [
				'title'   => 'Below is a detailed view of all data that Themeisle will receive if you fill in this survey. No email address or IP addresses are transmitted after you submit the survey.',
				'version' => '%s %s version %s %s %s %s',
				'website' => '%sCurrent website:%s %s %s %s',
				'usage'   => '%sUsage time:%s %s %s%s',
				'reason'  => '%s Uninstall reason %s %s Selected reason from the above survey %s ',
			],

			'options'        => [
				'id3'   => [
					'title'       => 'I found a better plugin',
					'placeholder' => 'What\'s the plugin\'s name?',
				],
				'id4'   => [

					'title'       => 'I could not get the plugin to work',
					'placeholder' => 'What problem are you experiencing?',
				],
				'id5'   => [

					'title'       => 'I no longer need the plugin',
					'placeholder' => 'If you could improve one thing about our product, what would it be?',
				],
				'id6'   => [
					'title'       => 'It\'s a temporary deactivation. I\'m just debugging an issue.',
					'placeholder' => 'What problem are you experiencing?',
				],
				'id7'   => [
					'title' => 'I don\'t know how to make it look like demo',
				],
				'id8'   => [

					'placeholder' => 'What option is missing?',
					'title'       => 'It lacks options',
				],
				'id9'   => [
					'title'       => 'Is not working with a plugin that I need',
					'placeholder' => 'What is the name of the plugin',
				],
				'id10'  => [
					'title' => 'I want to try a new design, I don\'t like {theme} style',
				],
				'id999' => [
					'title'       => 'Other',
					'placeholder' => 'What can we do better?',
				],
			],
		],
		'review'           => [
			'notice' => '<p>Hey, it\'s great to see you have <b>{product}</b> active for a few days now. How is everything going? If you can spare a few moments to rate it on WordPress.org it would help us a lot (and boost my motivation). Cheers! <br/> <br/>~ {developer}, developer of {product}</p>',
			'ctay'   => 'Ok, I will gladly help.',
			'ctan'   => 'No, thanks.',

		],
		'rollback'         => [
			'cta' => 'Rollback to v%s',
		],
		'logger'           => [
			'notice' => 'Do you enjoy <b>{product}</b>? Become a contributor by opting in to our anonymous data tracking. We guarantee no sensitive data is collected.',
			'cta_y'  => 'Sure, I would love to help.',
			'cta_n'  => 'No, thanks.',
		],
		'about_us'         => [
			'title'            => 'About Us',
			'heroHeader'       => 'Our Story',
			'heroTextFirst'    => 'Themeisle was founded in 2012 by a group of passionate developers who wanted to create beautiful and functional WordPress themes and plugins. Since then, we have grown into a team of over 20 dedicated professionals who are committed to delivering the best possible products to our customers.',
			'heroTextSecond'   => 'At Themeisle, we offer a wide range of WordPress themes and plugins that are designed to meet the needs of both beginners and advanced users. Our products are feature-rich, easy to use, and are designed to help you create beautiful and functional websites.',
			'teamImageCaption' => 'Our team in WCEU2022 in Portugal',
			'newsHeading'      => 'Stay connected for news & updates!',
			'emailPlaceholder' => 'Your email address',
			'signMeUp'         => 'Sign me up',
			'installNow'       => 'Install Now',
			'activate'         => 'Activate',
			'learnMore'        => 'Learn More',
			'installed'        => 'Installed',
			'notInstalled'     => 'Not Installed',
			'active'           => 'Active',
			'others'           => [
				'optimole_desc'   => 'Optimole is an image optimization service that automatically optimizes your images and serves them to your visitors via a global CDN, making your website lighter, faster and helping you reduce your bandwidth usage.',
				'neve_desc'       => 'A fast, lightweight, customizable WordPress theme offering responsive design, speed, and flexibility for various website types.',
				'landingkit_desc' => 'Turn WordPress into a landing page powerhouse with Landing Kit, map domains to pages or any other published resource.',
				'sparks_desc'     => 'Extend your store functionality with 8 ultra-performant features like product comparisons, variation swatches, wishlist, and more.',
				'tpc_desc'        => 'Design, save, and revisit your templates anytime with your personal vault on Templates Cloud.',
			],
			'otter-page'       => [
				'heading'      => 'Build innovative layouts with Otter Blocks and Gutenberg',
				'text'         => 'Otter is a lightweight, dynamic collection of page building blocks and templates for the WordPress block editor.',
				'buttons'      => [
					'install_otter_free' => "Install Otter - It's free!",
					'install_now'        => 'Install Now',
					'learn_more'         => 'Learn More',
				],
				'features'     => [
					'advancedTitle' => 'Advanced Features',
					'advancedDesc'  => 'Add features such as Custom CSS, Animations & Visibility Conditions to all blocks.',
					'fastTitle'     => 'Lightweight and Fast',
					'fastDesc'      => 'Otter enhances WordPress site building experience without impacting site speed.',
					'mobileTitle'   => 'Mobile-Friendly',
					'mobileDesc'    => 'Each block can be tweaked to provide a consistent experience across all devices.',
				],
				'details'      => [
					's1Title' => 'A Better Page Building Experience',
					's1Text'  => 'Otter can be used to build everything from a personal blog to an e-commerce site without losing the personal touch. Otter’s ease of use transforms basic blocks into expressive layouts in seconds.',
					's2Title' => 'A New Collection of Patterns',
					's2Text'  => 'A New Patterns Library, containing a range of different elements in a variety of styles to help you build great pages. All of your website’s most important areas are covered: headers, testimonials, pricing tables, sections and more.',
					's3Title' => 'Advanced Blocks',
					's3Text'  => 'Enhance your website’s design with powerful blocks, like the Add to Cart, Business Hours, Review Comparison, and dozens of WooCommerce blocks.',
				],
				'testimonials' => [
					'heading' => 'Trusted by more than 300K website owners',
					'users'   => [
						'user_1' => 'Loved the collection of blocks. If you want to create nice Gutenberg Pages, this plugin will be very handy and useful.',
						'user_2' => 'I am very satisfied with Otter – a fantastic collection of blocks. And the plugin is perfectly integrated with Gutenberg and complete enough for my needs.',
						'user_3' => 'Otter Blocks work really well and I like the customization options. Easy to use and format to fit in with my site theme – and I’ve not encountered any compatibility or speed issues.',
					],
				],
			],
		],
		'float_widget'     => [
			'button' => 'Toggle Help Widget for %s',
			'panel'  => [
				'greeting' => 'Thank you for using %s',
				'title'    => 'How can we help you?',
				'close'    => 'Close Toggle Help Widget',
			],
			'links'  => [
				'documentation'   => 'Documentation',
				'support'         => 'Get Support',
				'wizard'          => 'Run Setup Wizard',
				'upgrade'         => 'Upgrade to Pro',
				'feature_request' => 'Suggest a Feature',
				'rate'            => 'Rate Us',
			],
		],
	];

	/**
	 * Initialize the sdk logic.
	 */
	public static function init() {
		/**
		 * This filter can be used to localize the labels inside each product.
		 */
		self::$labels = apply_filters( 'themeisle_sdk_labels', self::$labels );
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Loader ) ) {
			self::$instance = new Loader();
			$modules        = array_merge( self::$available_modules, apply_filters( 'themeisle_sdk_modules', [] ) );
			foreach ( $modules as $key => $module ) {
				if ( ! class_exists( 'ThemeisleSDK\\Modules\\' . ucwords( $module, '_' ) ) ) {
					unset( $modules[ $key ] );
				}
			}
			self::$available_modules = $modules;
		}
	}

	/**
	 * Get cache token used in API requests.
	 *
	 * @return string Cache token.
	 */
	public static function get_cache_token() {
		$cache_token = get_transient( 'themeisle_sdk_cache_token' );
		if ( false === $cache_token ) {
			$cache_token = wp_generate_password( 6, false );
			set_transient( $cache_token, WEEK_IN_SECONDS );
		}

		return $cache_token;
	}

	/**
	 * Clear cache token.
	 */
	public static function clear_cache_token() {
		delete_transient( 'themeisle_sdk_cache_token' );
	}

	/**
	 * Register product into SDK.
	 *
	 * @param string $base_file The product base file.
	 *
	 * @return Loader The singleton object.
	 */
	public static function add_product( $base_file ) {

		if ( ! is_file( $base_file ) ) {
			return self::$instance;
		}
		$product = new Product( $base_file );

		Module_Factory::attach( $product, self::get_modules() );

		self::$products[ $product->get_slug() ] = $product;

		return self::$instance;
	}

	/**
	 * Get all registered modules by the SDK.
	 *
	 * @return array Modules available.
	 */
	public static function get_modules() {
		return self::$available_modules;
	}

	/**
	 * Get all products using the SDK.
	 *
	 * @return array<Product> Products available.
	 */
	public static function get_products() {
		return self::$products;
	}

	/**
	 * Get the version of the SDK.
	 *
	 * @return string The version.
	 */
	public static function get_version() {
		return self::$version;
	}
}
