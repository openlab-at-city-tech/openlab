<?php

use ElementsKit_Lite\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Name: ElementsKit Lite
 * Description: The most advanced addons for Elementor with tons of widgets, Header builder, Footer builder, Mega menu builder, layout pack and powerful custom controls.
 * Plugin URI: https://products.wpmet.com/elementskit
 * Author: Wpmet
 * Version: 3.3.2
 * Author URI: https://wpmet.com/
 *
 * Text Domain: elementskit-lite
 * Domain Path: /languages
 *
 * ElementsKit is a powerful addon for Elementor page builder.
 * It includes most comprehensive modules, such as "header footer builder", "mega menu",
 * "layout installer", "quick form builder" etc under the hood.
 * It has a tons of widgets to create any sites with an ease. It has some most unique
 * and powerful custom controls for elementor, such as "image picker", "ajax select", "widget area".
 *
 */


final class ElementsKit_Lite {
	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 * @var string The plugin version.
	 */
	static function version() {
		return '3.3.2';
	}

	/**
	 * Package type
	 *
	 * @since 1.1.0
	 * @var string The plugin purchase type [pro/ free].
	 */
	static function package_type() {
		return apply_filters( 'elementskit/core/package_type', 'free' );
	}


	/**
	 * Package type
	 *
	 * @since 1.1.0
	 * @var string The plugin purchase type [pro/ free].
	 */
	static function license_status() {
		if ( ! class_exists( 'ElementsKit\Libs\Framework\Classes\License' ) ) {
			return 'invalid';
		}
		if ( ElementsKit\Libs\Framework\Classes\License::instance()->status() != 'valid' ) {
			return 'invalid';
		}

		return 'valid';
	}

	public static function license_data() {
		if ( ! class_exists( '\ElementsKit_Lite\Libs\Framework\Classes\Utils' ) ) {
			return array(
				'key'            => '',
				'checksum'       => '',
				'plugin_package' => self::package_type(),
			);
		}

		return array(
			'key'            => \ElementsKit_Lite\Libs\Framework\Classes\Utils::instance()->get_option( 'license_key' ),
			'checksum'       => get_option( '__validate_oppai__' ),
			'plugin_package' => self::package_type(),
		);
	}


	/**
	 * Product ID
	 *
	 * @since 1.2.6
	 * @var string The plugin ID in our server.
	 */
	static function product_id() {
		return '9';
	}

	/**
	 * Author Name
	 *
	 * @since 1.3.1
	 * @var string The plugin author.
	 */
	static function author_name() {
		return 'Wpmet';
	}

	/**
	 * Store Name
	 *
	 * @since 1.3.1
	 * @var string The store name: self site, envato.
	 */
	static function store_name() {
		return 'wordpressorg';
	}

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	static function min_el_version() {
		return '3.0.0';
	}

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	static function min_php_version() {
		return '7.0';
	}

	/**
	 * Plugin file
	 *
	 * @since 1.0.0
	 * @var string plugins's root file.
	 */
	static function plugin_file() {
		return __FILE__;
	}

	/**
	 * Plugin url
	 *
	 * @since 1.0.0
	 * @var string plugins's root url.
	 */
	static function plugin_url() {
		return trailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Plugin dir
	 *
	 * @since 1.0.0
	 * @var string plugins's root directory.
	 */
	static function plugin_dir() {
		return trailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Plugin's widget directory.
	 *
	 * @since 1.0.0
	 * @var string widget's root directory.
	 */
	static function widget_dir() {
		return self::plugin_dir() . 'widgets/';
	}

	/**
	 * Plugin's widget url.
	 *
	 * @since 1.0.0
	 * @var string widget's root url.
	 */
	static function widget_url() {
		return self::plugin_url() . 'widgets/';
	}


	/**
	 * API url
	 *
	 * @since 1.0.0
	 * @var string for license, layout notification related functions.
	 */
	static function api_url() {
		return 'https://api.wpmet.com/public/';
	}

	/**
	 * Account url
	 *
	 * @since 1.2.6
	 * @var string for plugin update notification, user account page.
	 */
	static function account_url() {
		return 'https://account.wpmet.com';
	}

	/**
	 * Plugin's module directory.
	 *
	 * @since 1.0.0
	 * @var string module's root directory.
	 */
	static function module_dir() {
		return self::plugin_dir() . 'modules/';
	}

	/**
	 * Plugin's module url.
	 *
	 * @since 1.0.0
	 * @var string module's root url.
	 */
	static function module_url() {
		return self::plugin_url() . 'modules/';
	}


	/**
	 * Plugin's lib directory.
	 *
	 * @since 1.0.0
	 * @var string lib's root directory.
	 */
	static function lib_dir() {
		return self::plugin_dir() . 'libs/';
	}

	/**
	 * Plugin's lib url.
	 *
	 * @since 1.0.0
	 * @var string lib's root url.
	 */
	static function lib_url() {
		return self::plugin_url() . 'libs/';
	}

	/**
	 * Active plugin's textdomain list
	 */
	static function active_plugins() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$apl           = get_option( 'active_plugins' );
		$plugins       = get_plugins();
		$filter_string = '';
		foreach ( $apl as $p ) {
			if ( isset( $plugins[ $p ] ) && isset( $plugins[ $p ]['TextDomain'] ) ) {
				$filter_string .= ',' . $plugins[ $p ]['TextDomain'];
			}
		}
		return ltrim( $filter_string, ',' );
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		// Load the main static helper class.
		require_once self::plugin_dir() . 'libs/notice/notice.php'; // new notice system
		require_once self::plugin_dir() . 'libs/banner/banner.php'; // new banner system
		require_once self::plugin_dir() . 'libs/stories/stories.php'; // new stories system
		require_once self::plugin_dir() . 'libs/rating/rating.php';
		require_once self::plugin_dir() . 'libs/pro-awareness/pro-awareness.php'; // pro menu class file
		require_once self::plugin_dir() . 'libs/forms/forms.php'; // form menu class file
		require_once self::plugin_dir() . 'libs/our-plugins/our-plugins.php'; // used to display the wpmet other plugins
		require_once self::plugin_dir() . 'libs/emailkit/emailkit.php';
		require_once self::plugin_dir() . 'helpers/utils.php';

		// Load translation
		add_action( 'init', array( $this, 'i18n' ) );
		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ), 100 );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'elementskit-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Initialize the plugin
	 *
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		// Load the Plugin class, it's the core class of ElementsKit_Lite.
		require_once self::plugin_dir() . 'plugin.php';

		\ElementsKit_Lite\Plugin::registrar_autoloader();

		// init notice class
		\Oxaim\Libs\Notice::init();

		// init pro menu class
		\Wpmet\Libs\Pro_Awareness::init();

		// Check if Elementor installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_head', array( $this, 'missing_elementor' ) );
			return;
		}

		// Check for required PHP version.
		if ( version_compare( PHP_VERSION, self::min_php_version(), '<' ) ) {
			add_action( 'admin_head', array( $this, 'failed_php_version' ) );
			return;
		}

		// Register ElementsKit_Lite widget category
		add_action( 'elementor/elements/categories_registered', array( $this, 'elementor_widget_category' ) );

		// initiate elementor custom controls
		new \ElementsKit_Lite\Modules\Controls\Init();

		add_action(
			'elementor/init',
			function() {
				if ( class_exists( 'ElementsKit' ) && ! class_exists( 'ElementsKit_Comp' ) ) {
					return;
				}

				// adding backward classes and methods for older 14 number themes.
				require_once self::plugin_dir() . 'compatibility/backward/plugin-class-backward-compatibility.php';
				require_once self::plugin_dir() . 'compatibility/backward/utils-backward-compablity.php';

				// Run the instance.
				Plugin::instance();

				// adding backward classes and methods for older 14 number themes.
				require_once self::plugin_dir() . 'compatibility/backward/module-list.php';
				require_once self::plugin_dir() . 'compatibility/backward/widget-list.php';
			}
		);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have required Elementor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function missing_elementor() {
		$btn = array(
			'default_class' => 'button',
			'class'         => 'button-primary ', // button-primary button-secondary button-small button-large button-link
		);

		if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
			$btn['text'] = esc_html__( 'Activate Elementor', 'elementskit-lite' );
			$btn['url']  = wp_nonce_url( 'plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1', 'activate-plugin_elementor/elementor.php' );
		} else {
			$btn['text'] = esc_html__( 'Install Elementor', 'elementskit-lite' );
			$btn['url']  = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		}

		\Oxaim\Libs\Notice::instance( 'elementskit-lite', 'unsupported-elementor-version' )
		->set_type( 'error' )
		->set_message( sprintf( '%1$s %2$s+, %3$s', 
				esc_html__( 'ElementsKit requires Elementor version', 'elementskit-lite' ),
				self::min_el_version() ,
				esc_html__( 'which is currently NOT RUNNING.', 'elementskit-lite' ),
			) )
		->set_button( $btn )
		->call();
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function failed_php_version() {
		\Oxaim\Libs\Notice::instance( 'elementskit-lite', 'unsupported-php-version' )
		->set_type( 'error' )
		->set_message( sprintf( '%1$s %2$s+, %3$s',
				esc_html__( 'ElementsKit requires PHP version', 'elementskit-lite' ),
				self::min_php_version(),
				esc_html__( 'which is currently NOT RUNNING on this server.', 'elementskit-lite' )
			))
		->call();
	}

	/**
	 * Rewrite flush.
	 *
	 * @since 1.0.7
	 * @access public
	 */
	public static function install_activation_key() {
		add_option( 'elementskit-lite__plugin_activated', self::plugin_file() );
	}

	/**
	 * Add category.
	 *
	 * Register custom widget category in Elementor's editor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function elementor_widget_category( $widgets_manager ) {
		$widgets_manager->add_category(
			'elementskit',
			array(
				'title' => esc_html__( 'ElementsKit', 'elementskit-lite' ),
				'icon'  => 'fa fa-plug',
			),
			1
		);
		$widgets_manager->add_category(
			'elementskit_headerfooter',
			array(
				'title' => esc_html__( 'ElementsKit Header Footer', 'elementskit-lite' ),
				'icon'  => 'fa fa-plug',
			),
			1
		);
	}
}

new ElementsKit_Lite();

register_activation_hook( __FILE__, 'ElementsKit_Lite::install_activation_key' );
