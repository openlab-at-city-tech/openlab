<?php
/**
 * The promotions model class for ThemeIsle SDK
 *
 * Here's how to hook it in your plugin: add_filter( 'menu_icons_load_promotions', function() { return array( 'otter' ); } );
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Loader;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promotions module for ThemeIsle SDK.
 */
class Promotions extends Abstract_Module {

	/**
	 * Holds the promotions.
	 *
	 * @var array
	 */
	public $promotions = array();

	/**
	 * Holds the values of the promotions that are not allowed to be shown.
	 * Can be filtered by each product.
	 *
	 * @var array
	 */
	private $dissallowed_promotions = array();

	/**
	 * Option key for promos.
	 *
	 * @var string
	 */
	private $option_main = 'themeisle_sdk_promotions';

	/**
	 * Option key for otter promos.
	 *
	 * @var string
	 */
	private $option_otter = 'themeisle_sdk_promotions_otter_installed';

	/**
	 * Option key for optimole promos.
	 *
	 * @var string
	 */
	private $option_optimole = 'themeisle_sdk_promotions_optimole_installed';

	/**
	 * Option key for ROP promos.
	 *
	 * @var string
	 */
	private $option_rop = 'themeisle_sdk_promotions_rop_installed';

	/**
	 * Option key for Neve promo.
	 *
	 * @var string
	 */
	private $option_neve = 'themeisle_sdk_promotions_neve_installed';

	/**
	 * Option key for Redirection for CF7.
	 *
	 * @var string
	 */
	private $option_redirection_cf7 = 'themeisle_sdk_promotions_redirection_cf7_installed';

	/**
	 * Option key for Hyve.
	 * 
	 * @var string
	 */
	private $option_hyve = 'themeisle_sdk_promotions_hyve_installed';

	/**
	 * Option key for WP Full Pay.
	 * 
	 * @var string
	 */
	private $option_wp_full_pay = 'themeisle_sdk_promotions_wp_full_pay_installed';

	/**
	 * Option key for Feedzy.
	 *
	 * @var string
	 */
	private $option_feedzy = 'themeisle_sdk_promotions_feedzy_installed';

	/**
	 * Option key for Masteriyo promos.
	 *
	 * @var string
	 */
	private $option_masteriyo = 'themeisle_sdk_promotions_masteriyo_installed';

	/**
	 * Loaded promotion.
	 *
	 * @var string
	 */
	private $loaded_promo;

	/**
	 * Woo promotions.
	 *
	 * @var array
	 */
	private $woo_promos = array();

	/**
	 * Debug mode.
	 *
	 * @var bool
	 */
	private $debug = false;

	/**
	 * Should we load this module.
	 *
	 * @param Product $product Product object.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		if ( apply_filters( 'themeisle_sdk_ran_promos', false ) === true ) {
			return false;
		}
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}

		$this->debug        = apply_filters( 'themeisle_sdk_promo_debug', $this->debug );
		$promotions_to_load = apply_filters( $product->get_key() . '_load_promotions', array() );

		$promotions_to_load[] = 'optimole';
		$promotions_to_load[] = 'rop';
		$promotions_to_load[] = 'woo_plugins';
		$promotions_to_load[] = 'neve';
		$promotions_to_load[] = 'redirection-cf7';
		$promotions_to_load[] = 'hyve';
		$promotions_to_load[] = 'wp_full_pay';
		$promotions_to_load[] = 'feedzy_import';
		$promotions_to_load[] = 'learning-management-system';

		if ( defined( 'NEVE_VERSION' ) || defined( 'WPMM_PATH' ) || defined( 'OTTER_BLOCKS_VERSION' ) || defined( 'OBFX_URL' ) ) {
			$promotions_to_load[] = 'feedzy_embed';
		}
		$promotions_to_load = array_unique( $promotions_to_load );

		$this->promotions = $this->get_promotions();

		$this->dissallowed_promotions = apply_filters( $product->get_key() . '_dissallowed_promotions', array() );

		foreach ( $this->promotions as $slug => $data ) {
			if ( ! in_array( $slug, $promotions_to_load, true ) ) {
				unset( $this->promotions[ $slug ] );
			}
		}
		add_action( 'init', array( $this, 'register_settings' ), 99 );
		add_action( 'admin_init', array( $this, 'register_reference' ), 99 );

		return ! empty( $this->promotions );
	}

	/**
	 * Registers the hooks.
	 *
	 * @param Product $product Product to load.
	 */
	public function load( $product ) {
		if ( ! $this->is_writeable() || ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$last_dismiss_time = $this->get_last_dismiss_time();

		if ( ! $this->debug && is_int( $last_dismiss_time ) && ( time() - $last_dismiss_time ) < ( 3 * WEEK_IN_SECONDS ) ) {
			return;
		}

		$this->product = $product;

		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_field' ), 10, 2 );
		add_action( 'current_screen', [ $this, 'load_available' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_ajax_tisdk_update_option', array( $this, 'dismiss_promotion' ) );
		add_filter( 'themeisle_sdk_ran_promos', '__return_true' );

		if ( get_option( $this->option_neve, false ) !== true ) {
			add_action( 'wp_ajax_themeisle_sdk_dismiss_notice', 'ThemeisleSDK\Modules\Notification::regular_dismiss' );
		}
	}

	/**
	 * Load available promotions.
	 */
	public function load_available() {
		$this->promotions = $this->filter_by_screen_and_merge();
		if ( empty( $this->promotions ) ) {
			return;
		}

		$this->load_promotion( $this->promotions[ array_rand( $this->promotions ) ] );
	}


	/**
	 * Register plugin reference.
	 *
	 * @return void
	 */
	public function register_reference() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( ! isset( $_GET['plugin'] ) || ! isset( $_GET['_wpnonce'] ) ) {
			return;
		}

		$plugin = rawurldecode( $_GET['plugin'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( wp_verify_nonce( $_GET['_wpnonce'], 'activate-plugin_' . $plugin ) === false ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		if ( isset( $_GET['reference_key'] ) ) {
			update_option( 'otter_reference_key', sanitize_key( $_GET['reference_key'] ) );
		}

		if ( isset( $_GET['optimole_reference_key'] ) ) {
			update_option( 'optimole_reference_key', sanitize_key( $_GET['optimole_reference_key'] ) );
		}

		if ( isset( $_GET['rop_reference_key'] ) ) {
			update_option( 'rop_reference_key', sanitize_key( $_GET['rop_reference_key'] ) );
		}

		if ( isset( $_GET['neve_reference_key'] ) ) {
			update_option( 'neve_reference_key', sanitize_key( $_GET['neve_reference_key'] ) );
		}

		if ( isset( $_GET['hyve_reference_key'] ) ) {
			update_option( 'hyve_reference_key', sanitize_key( $_GET['hyve_reference_key'] ) );
		}

		if ( isset( $_GET['wp_full_pay_reference_key'] ) ) {
			update_option( 'wp_full_pay_reference_key', sanitize_key( $_GET['wp_full_pay_reference_key'] ) );
		}

		if ( isset( $_GET['feedzy_reference_key'] ) || ( isset( $_GET['from'], $_GET['plugin'] ) && $_GET['from'] === 'import' && str_starts_with( sanitize_key( $_GET['plugin'] ), 'feedzy' ) ) ) {
			update_option( 'feedzy_reference_key', sanitize_key( $_GET['feedzy_reference_key'] ?? 'i-' . $this->product->get_key() ) );
			update_option( $this->option_feedzy, 1 );
		}
	}

	/**
	 * Register Settings
	 */
	public function register_settings() {
		$default = get_option( 'themeisle_sdk_promotions_otter', '{}' );

		register_setting(
			'themeisle_sdk_settings',
			$this->option_main,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => $default,
			)
		);

		register_setting(
			'themeisle_sdk_settings',
			$this->option_otter,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
		register_setting(
			'themeisle_sdk_settings',
			$this->option_optimole,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
		register_setting(
			'themeisle_sdk_settings',
			$this->option_rop,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
		register_setting(
			'themeisle_sdk_settings',
			$this->option_neve,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
		register_setting(
			'themeisle_sdk_settings',
			$this->option_redirection_cf7,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
		register_setting(
			'themeisle_sdk_settings',
			$this->option_hyve,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
		register_setting(
			'themeisle_sdk_settings',
			$this->option_wp_full_pay,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
		register_setting(
			'themeisle_sdk_settings',
			$this->option_masteriyo,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);
	}

	/**
	 * Check if the path is writable.
	 *
	 * @return boolean
	 * @access  public
	 */
	public function is_writeable() {

		include_once ABSPATH . 'wp-admin/includes/file.php';
		$filesystem_method = get_filesystem_method();

		if ( 'direct' === $filesystem_method ) {
			return true;
		}

		return false;
	}

	/**
	 * Third-party compatibility.
	 *
	 * @return boolean
	 */
	private function has_conflicts() {
		global $pagenow;

		// Editor notices aren't compatible with Enfold theme.
		if ( defined( 'AV_FRAMEWORK_VERSION' ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get promotions.
	 *
	 * @return array
	 */
	private function get_promotions() {
		$has_otter                 = defined( 'OTTER_BLOCKS_VERSION' ) || $this->is_plugin_installed( 'otter-blocks' );
		$had_otter_from_promo      = get_option( $this->option_otter, false );
		$has_optimole              = defined( 'OPTIMOLE_VERSION' ) || $this->is_plugin_installed( 'optimole-wp' );
		$had_optimole_from_promo   = get_option( $this->option_optimole, false );
		$has_rop                   = defined( 'ROP_LITE_VERSION' ) || $this->is_plugin_installed( 'tweet-old-post' );
		$had_rop_from_promo        = get_option( $this->option_rop, false );
		$has_woocommerce           = class_exists( 'WooCommerce' );
		$has_sparks                = defined( 'SPARKS_WC_VERSION' ) || $this->is_plugin_installed( 'sparks-for-woocommerce' );
		$has_ppom                  = defined( 'PPOM_VERSION' ) || $this->is_plugin_installed( 'woocommerce-product-addon' );
		$has_redirection_cf7       = defined( 'WPCF7_PRO_REDIRECT_PLUGIN_VERSION' ) || $this->is_plugin_installed( 'wpcf7-redirect' );
		$had_redirection_cf7_promo = get_option( $this->option_redirection_cf7, false );
		$has_hyve                  = defined( 'HYVE_LITE_VERSION' ) || $this->is_plugin_installed( 'hyve' ) || $this->is_plugin_installed( 'hyve-lite' );
		$had_hyve_from_promo       = get_option( $this->option_hyve, false );
		$has_hyve_conditions       = version_compare( get_bloginfo( 'version' ), '6.2', '>=' ) && $this->has_support_page();
		$has_wfp_full_pay          = defined( 'WP_FULL_STRIPE_BASENAME' ) || $this->is_plugin_installed( 'wp-full-stripe-free' );
		$had_wfp_from_promo        = get_option( $this->option_wp_full_pay, false );
		$has_wfp_conditions        = $this->has_donate_page();
		$is_min_req_v              = version_compare( get_bloginfo( 'version' ), '5.8', '>=' );
		$current_theme             = wp_get_theme();
		$has_neve                  = $current_theme->template === 'neve' || $current_theme->parent() === 'neve';
		$has_neve_from_promo       = get_option( $this->option_neve, false );
		$has_enough_attachments    = $this->has_min_media_attachments();
		$has_enough_old_posts      = $this->has_old_posts();
		$is_min_php_7_4            = version_compare( PHP_VERSION, '7.4', '>=' );
		$has_feedzy                = defined( 'FEEDZY_BASEFILE' ) || $this->is_plugin_installed( 'feedzy-rss-feedss' );
		$had_feedzy_from_promo     = get_option( $this->option_feedzy, false );
		$has_masteriyo             = defined( 'MASTERIYO_VERSION' ) || $this->is_plugin_installed( 'learning-management-system' );
		$had_masteriyo_from_promo  = get_option( $this->option_masteriyo, false );
		$has_masteriyo_conditions  = $this->has_lms_tagline();
		$is_min_php_7_2            = version_compare( PHP_VERSION, '7.2', '>=' );

		$all = [
			'optimole'                   => [
				'om-editor'      => [
					'env'     => ! $has_optimole && $is_min_req_v && ! $had_optimole_from_promo,
					'screen'  => 'editor',
					'delayed' => true,
				],
				'om-image-block' => [
					'env'     => ! $has_optimole && $is_min_req_v && ! $had_optimole_from_promo,
					'screen'  => 'editor',
					'delayed' => true,
				],
				'om-attachment'  => [
					'env'    => ! $has_optimole && ! $had_optimole_from_promo,
					'screen' => 'media-editor',
				],
				'om-media'       => [
					'env'    => ! $has_optimole && ! $had_optimole_from_promo && $has_enough_attachments,
					'screen' => 'media',
				],
				'om-elementor'   => [
					'env'     => ! $has_optimole && ! $had_optimole_from_promo && defined( 'ELEMENTOR_VERSION' ),
					'screen'  => 'elementor',
					'delayed' => true,
				],
			],
			'feedzy_import'              => [
				'feedzy-import' => [
					'env'    => true,
					'screen' => 'import',
					'always' => true,
				],
			],
			'feedzy_embed'               => [
				'feedzy-editor' => [
					'env'    => ! $has_feedzy && is_main_site() && ! $had_feedzy_from_promo,
					'screen' => 'editor',
				],
			],
			'otter'                      => [
				'blocks-css'        => [
					'env'     => ! $has_otter && $is_min_req_v && ! $had_otter_from_promo,
					'screen'  => 'editor',
					'delayed' => true,
				],
				'blocks-animation'  => [
					'env'     => ! $has_otter && $is_min_req_v && ! $had_otter_from_promo,
					'screen'  => 'editor',
					'delayed' => true,
				],
				'blocks-conditions' => [
					'env'     => ! $has_otter && $is_min_req_v && ! $had_otter_from_promo,
					'screen'  => 'editor',
					'delayed' => true,
				],
			],
			'rop'                        => [
				'rop-posts' => [
					'env'     => ! $has_rop && ! $had_rop_from_promo && $has_enough_old_posts,
					'screen'  => 'edit-post',
					'delayed' => true,
				],
			],
			'woo_plugins'                => [
				'ppom'                  => [
					'env'    => ! $has_ppom && $has_woocommerce,
					'screen' => 'edit-product',
				],
				'sparks-wishlist'       => [
					'env'    => ! $has_sparks && $has_woocommerce,
					'screen' => 'edit-product',
				],
				'sparks-announcement'   => [
					'env'    => ! $has_sparks && $has_woocommerce,
					'screen' => 'edit-product',
				],
				'sparks-product-review' => [
					'env'    => ! $has_sparks && $has_woocommerce,
					'screen' => 'edit-product',
				],
			],
			'neve'                       => [
				'neve-themes-popular' => [
					'env'    => ! $has_neve && ! $has_neve_from_promo,
					'screen' => 'themes-install-popular',
				],
			],
			'redirection-cf7'            => [
				'wpcf7' => [
					'env'     => ! $has_redirection_cf7 && ! $had_redirection_cf7_promo,
					'screen'  => 'wpcf7',
					'delayed' => true,
				],
			],
			'hyve'                       => [
				'hyve-plugins-install' => [
					'env'    => $is_min_php_7_4 && ! $has_hyve && ! $had_hyve_from_promo && $has_hyve_conditions,
					'screen' => 'plugin-install',
				],
			],
			'wp_full_pay'                => [
				'wp-full-pay-plugins-install' => [
					'env'    => ! $has_wfp_full_pay && ! $had_wfp_from_promo && $has_wfp_conditions,
					'screen' => 'plugin-install',
				],
			],
			'learning-management-system' => [
				'masteriyo-plugins-install' => [
					'env'    => $is_min_php_7_2 && ! $has_masteriyo && ! $had_masteriyo_from_promo && $has_masteriyo_conditions,
					'screen' => 'plugin-install',
				],
			],
		];

		foreach ( $all as $slug => $data ) {
			foreach ( $data as $key => $conditions ) {
				if ( ! $conditions['env'] || $this->has_conflicts() ) {
					unset( $all[ $slug ][ $key ] );

					continue;
				}

				if ( $this->get_upsells_dismiss_time( $key ) ) {
					unset( $all[ $slug ][ $key ] );
				}
			}

			if ( empty( $all[ $slug ] ) ) {
				unset( $all[ $slug ] );
			}
		}
		return $all;
	}

	/**
	 * Get the upsell dismiss time.
	 *
	 * @param string $key The upsell key. If empty will return all dismiss times.
	 *
	 * @return false | string | array
	 */
	private function get_upsells_dismiss_time( $key = '' ) {
		$old  = get_option( 'themeisle_sdk_promotions_otter', '{}' );
		$data = get_option( $this->option_main, $old );

		$data = json_decode( $data, true );

		if ( empty( $key ) ) {
			return $data;
		}

		return isset( $data[ $key ] ) ? $data[ $key ] : false;
	}

	/**
	 * Get the last dismiss time of a promotion.
	 *
	 * @return int | false The timestamp of last dismiss or false.
	 */
	private function get_last_dismiss_time() {
		$dismissed = $this->get_upsells_dismiss_time();

		return empty( $dismissed ) ? false : max( array_values( $dismissed ) );
	}

	/**
	 * Filter by screen & merge into single array of keys.
	 *
	 * @return array
	 */
	private function filter_by_screen_and_merge() {
		$current_screen = get_current_screen();

		$is_elementor      = isset( $_GET['action'] ) && $_GET['action'] === 'elementor';
		$is_media          = isset( $current_screen->id ) && $current_screen->id === 'upload';
		$is_posts          = isset( $current_screen->id ) && $current_screen->id === 'edit-post';
		$is_editor         = method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor();
		$is_theme_install  = isset( $current_screen->id ) && ( $current_screen->id === 'theme-install' );
		$is_plugin_install = isset( $current_screen->id ) && ( $current_screen->id === 'plugin-install' );
		$is_product        = isset( $current_screen->id ) && $current_screen->id === 'product';
		$is_import         = isset( $current_screen->id ) && $current_screen->id === 'import';
		$is_cf7_install    = isset( $current_screen->id ) && function_exists( 'str_contains' ) ? str_contains( $current_screen->id, 'page_wpcf7' ) : false;

		$return               = [];
		$product_install_time = (int) $this->product->get_install_time();
		$is_older             = time() > ( $product_install_time + ( 3 * DAY_IN_SECONDS ) );
		$is_newer             = time() < ( $product_install_time + ( 6 * HOUR_IN_SECONDS ) );
		foreach ( $this->promotions as $slug => $promos ) {
			foreach ( $promos as $key => $data ) {

				$data = wp_parse_args(
					$data,
					[
						'delayed' => false,
						'always'  => false,
					] 
				);

				if (
					! $this->debug &&
					(
						( $data['delayed'] === true && ! $is_older ) || // Skip promotions that are delayed for 3 days.
						$is_newer // Skip promotions for the first 6 hours after install.
					)
					&& ! $data['always']
				) {
					unset( $this->promotions[ $slug ][ $key ] );

					continue;
				}
				switch ( $data['screen'] ) {
					case 'media-editor':
						if ( ! $is_media && ! $is_editor ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'media':
						if ( ! $is_media ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'editor':
						if ( ! $is_editor || $is_elementor ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'import':
						if ( ! $is_import ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'elementor':
						if ( ! $is_elementor ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'edit-post':
						if ( ! $is_posts ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'edit-product':
						if ( ! $is_product ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'themes-install-popular':
						if ( ! $is_theme_install ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'wpcf7':
						if ( ! $is_cf7_install ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
					case 'plugin-install':
						if ( ! $is_plugin_install ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
				}
			}

			$return = array_merge( $return, $this->promotions[ $slug ] );
		}

		$return = array_filter(
			$return,
			function ( $value, $key ) {
				return ! in_array( $key, $this->dissallowed_promotions, true );
			},
			ARRAY_FILTER_USE_BOTH
		);

		return array_keys( $return );
	}

	/**
	 * Load single promotion.
	 *
	 * @param string $slug slug of the promotion.
	 */
	private function load_promotion( $slug ) {
		$this->loaded_promo = $slug;

		if ( $this->debug ) {
			add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
			if ( $this->get_upsells_dismiss_time( 'om-media' ) === false ) {
				add_action( 'admin_notices', [ $this, 'render_optimole_dash_notice' ] );
			}
			if ( $this->get_upsells_dismiss_time( 'rop-posts' ) === false ) {
				add_action( 'admin_notices', [ $this, 'render_rop_dash_notice' ] );
			}
			if ( $this->get_upsells_dismiss_time( 'neve-themes-popular' ) === false ) {
				add_action( 'admin_notices', [ $this, 'render_neve_themes_notice' ] );
			}
			if ( $this->get_upsells_dismiss_time( 'redirection-cf7' ) === false ) {
				add_action( 'admin_notices', [ $this, 'render_redirection_cf7_notice' ] );
			}
			if ( $this->get_upsells_dismiss_time( 'hyve-plugins-install' ) === false ) {
				add_action( 'admin_notices', [ $this, 'render_hyve_notice' ] );
			}

			if ( $this->get_upsells_dismiss_time( 'wp-full-pay-plugins-install' ) === false ) {
				add_action( 'admin_notices', [ $this, 'render_wp_full_pay_notice' ] );
			}

			if ( $this->get_upsells_dismiss_time( 'masteriyo-plugins-install' ) === false ) {
				add_action( 'admin_notices', [ $this, 'render_masteriyo_notice' ] );
			}

			add_action( 'load-import.php', [ $this, 'add_import' ] );
			$this->load_woo_promos();

			return;
		}
		switch ( $slug ) {
			case 'om-editor':
			case 'om-image-block':
			case 'blocks-css':
			case 'blocks-animation':
			case 'blocks-conditions':
				add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue' ] );
				break;
			case 'om-attachment':
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				break;
			case 'om-media':
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'admin_notices', [ $this, 'render_optimole_dash_notice' ] );
				break;
			case 'rop-posts':
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'admin_notices', [ $this, 'render_rop_dash_notice' ] );
				break;
			case 'feedzy-import':
				add_action( 'load-import.php', [ $this, 'add_import' ] );

				break;
			case 'feedzy-editor':
				add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue' ] );
				break;
			case 'ppom':
			case 'sparks-wishlist':
			case 'sparks-announcement':
			case 'sparks-product-reviews':
				$this->load_woo_promos();
				break;
			case 'neve-themes-popular':
				// Remove any other notifications if Neve promotion is showing
				remove_action( 'admin_notices', array( 'ThemeisleSDK\Modules\Notification', 'show_notification' ) );
				remove_action(
					'wp_ajax_themeisle_sdk_dismiss_notice',
					array(
						'ThemeisleSDK\Modules\Notification',
						'dismiss',
					)
				);
				remove_action( 'admin_head', array( 'ThemeisleSDK\Modules\Notification', 'dismiss_get' ) );
				remove_action( 'admin_head', array( 'ThemeisleSDK\Modules\Notification', 'setup_notifications' ) );
				// Add required actions to display this notification
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'admin_notices', [ $this, 'render_neve_themes_notice' ] );
				break;
			case 'wpcf7':
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'admin_notices', [ $this, 'render_redirection_cf7_notice' ] );
				break;
			case 'hyve-plugins-install':
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'admin_notices', [ $this, 'render_hyve_notice' ] );
				break;
			case 'wp-full-pay-plugins-install':
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'admin_notices', [ $this, 'render_wp_full_pay_notice' ] );
				break;
			case 'masteriyo-plugins-install':
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'admin_notices', [ $this, 'render_masteriyo_notice' ] );
				break;
		}
	}

	/**
	 * Add import row.
	 *
	 * @return void
	 */
	public function add_import() {
		global $wp_importers;
		if ( isset( $wp_importers['feedzy-rss-feeds'] ) ) {
			return;
		}
		$wp_importers['feedzy-rss-feeds'] = array( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			'Feedzy',
			sprintf( Loader::$labels['promotions']['feedzy']['import_desc'], '<span style="float: left; font-style: italic;margin-top:0.4em;">', $this->product->get_friendly_name(), '</span>' ),
			'install' => 'feedzy-rss-feeds',
		);
		if ( defined( 'FEEDZY_BASEFILE' ) ) {
			unset( $wp_importers['feedzy-rss-feeds']['install'] );
		}
	}
	/**
	 * Render dashboard notice.
	 */
	public function render_optimole_dash_notice() {
		$screen = get_current_screen();

		if ( ! isset( $screen->id ) || $screen->id !== 'upload' ) {
			return;
		}

		echo '<div id="ti-optml-notice" class="notice notice-info ti-sdk-om-notice"></div>';
	}

	/**
	 * Enqueue the assets.
	 */
	public function enqueue() {
		global $themeisle_sdk_max_path;
		$handle            = 'ti-sdk-promo';
		$saved             = $this->get_upsells_dismiss_time();
		$themeisle_sdk_src = $this->get_sdk_uri();
		$user              = wp_get_current_user();
		$asset_file        = require $themeisle_sdk_max_path . '/assets/js/build/promos/index.asset.php';
		$deps              = array_merge( $asset_file['dependencies'], [ 'updates' ] );

		$themes                                = wp_get_themes();
		$neve_action                           = isset( $themes['neve'] ) ? 'activate' : 'install';
		$labels                                = Loader::$labels['promotions'];
		$labels['feedzy']['editor_recommends'] = sprintf(
			$labels['feedzy']['editor_recommends'],
			$this->product->get_friendly_name(),
			'<a target="_blank" href="' . add_query_arg(
				array(
					'tab'                  => 'plugin-information',
					'plugin'               => 'feedzy-rss-feeds',
					'_wpnonce'             => wp_create_nonce( 'activate-plugin_feedzy-rss-feeds' ),
					'feedzy_reference_key' => 'e-' . $this->product->get_key(),
				),
				network_admin_url( 'plugin-install.php' )
			) . '">',
			'</a>' 
		);
		wp_register_script( $handle, $themeisle_sdk_src . 'assets/js/build/promos/index.js', $deps, $asset_file['version'], true );
		wp_localize_script(
			$handle,
			'themeisleSDKPromotions',
			[
				'debug'                  => $this->debug,
				'labels'                 => $labels,
				'email'                  => $user->user_email,
				'showPromotion'          => $this->loaded_promo,
				'optionKey'              => $this->option_main,
				'product'                => $this->product->get_name(),
				'option'                 => empty( $saved ) ? new \stdClass() : $saved,
				'nonce'                  => wp_create_nonce( 'wp_rest' ),
				'assets'                 => $themeisle_sdk_src . 'assets/images/',
				'optimoleApi'            => esc_url( rest_url( 'optml/v1/register_service' ) ),
				'optimoleActivationUrl'  => $this->get_plugin_activation_link( 'optimole-wp' ),
				'otterActivationUrl'     => $this->get_plugin_activation_link( 'otter-blocks' ),
				'ropActivationUrl'       => $this->get_plugin_activation_link( 'tweet-old-post' ),
				'optimoleDash'           => esc_url( add_query_arg( [ 'page' => 'optimole' ], admin_url( 'upload.php' ) ) ),
				'ropDash'                => esc_url( add_query_arg( [ 'page' => 'TweetOldPost' ], admin_url( 'admin.php' ) ) ),
				// translators: %s is the product name.
				'title'                  => esc_html( sprintf( Loader::$labels['promotions']['recommended'], $this->product->get_name() ) ),
				'redirectionCF7MoreUrl'  => tsdk_utmify( 'https://docs.themeisle.com/collection/2014-redirection-for-contact-form-7', 'redirection-for-contact-form-7', 'plugin-install' ),
				'rfCF7ActivationUrl'     => $this->get_plugin_activation_link( 'wpcf7-redirect' ),
				'cf7Dash'                => esc_url( add_query_arg( [ 'page' => 'wpcf7-new' ], admin_url( 'admin.php' ) ) ),
				'hyveActivationUrl'      => $this->get_plugin_activation_link( 'hyve-lite' ),
				'hyveDash'               => esc_url( add_query_arg( [ 'page' => 'wpfs-settings-stripe' ], admin_url( 'admin.php' ) ) ),
				'wpFullPayActivationUrl' => $this->get_plugin_activation_link( 'wp-full-stripe-free' ),
				'wpFullPayDash'          => esc_url( add_query_arg( [ 'page' => 'wpfs-settings-stripe' ], admin_url( 'admin.php' ) ) ),
				'masteriyoActivationUrl' => $this->get_plugin_activation_link( 'masteriyo' ),
				'masteriyoDash'          => esc_url( add_query_arg( [ 'page' => 'masteriyo-onboard' ], admin_url( 'index.php' ) ) ),
				'nevePreviewURL'         => esc_url( add_query_arg( [ 'theme' => 'neve' ], admin_url( 'theme-install.php' ) ) ),
				'neveAction'             => $neve_action,
				'activateNeveURL'        => esc_url(
					add_query_arg(
						[
							'action'     => 'activate',
							'stylesheet' => 'neve',
							'_wpnonce'   => wp_create_nonce( 'switch-theme_neve' ),
						],
						admin_url( 'themes.php' )
					)
				),
			]
		);
		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle, $themeisle_sdk_src . 'assets/js/build/promos/style-index.css', [ 'wp-components' ], $asset_file['version'] );
	}

	/**
	 * Render rop notice.
	 */
	public function render_rop_dash_notice() {
		$screen = get_current_screen();

		if ( ! isset( $screen->id ) || $screen->id !== 'edit-post' ) {
			return;
		}

		echo '<div id="ti-rop-notice" class="notice notice-info ti-sdk-rop-notice"></div>';
	}

	/**
	 * Render Neve Themes notice.
	 */
	public function render_neve_themes_notice() {
		echo '<div id="ti-neve-notice" class="notice notice-info ti-sdk-om-notice"></div>';
	}

	/**
	 * Render Hyve notice.
	 */
	public function render_hyve_notice() {
		echo '<div id="ti-hyve-notice" class="notice notice-info ti-sdk-om-notice"></div>';
	}

	/**
	 * Render WP Full Pay notice.
	 */
	public function render_wp_full_pay_notice() {
		echo '<div id="ti-wp-full-pay-notice" class="notice notice-info ti-sdk-om-notice"></div>';
	}

	/**
	 * Render Redirection for CF7 notice.
	 */
	public function render_redirection_cf7_notice() {
		echo '<div id="ti-redirection-cf7-notice" class="notice notice-info ti-sdk-om-notice"></div>';
	}

	/**
	 * Render Masteriyo notice.
	 */
	public function render_masteriyo_notice() {
		echo '<div id="ti-masteriyo-notice" class="notice notice-info ti-sdk-om-notice"></div>';
	}

	/**
	 * Add promo to attachment modal.
	 *
	 * @param array    $fields Fields array.
	 * @param \WP_Post $post Post object.
	 *
	 * @return array
	 */
	public function add_attachment_field( $fields, $post ) {
		if ( $post->post_type !== 'attachment' ) {
			return $fields;
		}

		if ( ! isset( $post->post_mime_type ) || strpos( $post->post_mime_type, 'image' ) === false ) {
			return $fields;
		}

		$meta = wp_get_attachment_metadata( $post->ID );

		if ( isset( $meta['filesize'] ) && $meta['filesize'] < 100000 ) {
			return $fields;
		}

		$fields['optimole'] = array(
			'input' => 'html',
			'html'  => '<div id="ti-optml-notice-helper"></div>',
			'label' => '',
		);

		if ( count( $fields ) < 2 ) {
			add_filter( 'wp_required_field_message', '__return_empty_string' );
		}

		return $fields;
	}

	/**
	 * Check if has 50 image media items.
	 *
	 * @return bool
	 */
	private function has_min_media_attachments() {
		if ( $this->debug ) {
			return true;
		}
		$attachment_count = get_transient( 'tsk_attachment_count' );
		if ( false === $attachment_count ) {
			$args = array(
				'post_type'      => 'attachment',
				'posts_per_page' => 51,
				'fields'         => 'ids',
				'post_status'    => 'inherit',
				'no_found_rows'  => true,
			);

			$query            = new \WP_Query( $args );
			$attachment_count = $query->post_count;


			set_transient( 'tsk_attachment_count', $attachment_count, DAY_IN_SECONDS );
		}

		return $attachment_count > 50;
	}

	/**
	 * Check if the website has more than 100 posts and over 10 are over a year old.
	 *
	 * @return bool
	 */
	private function has_old_posts() {
		if ( $this->debug ) {
			return true;
		}

		$posts_count = get_transient( 'tsk_posts_count' );

		// Create a new WP_Query object to get all posts
		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => 101, //phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'fields'         => 'ids',
			'no_found_rows'  => true,
		);

		if ( false === $posts_count ) {
			$query       = new \WP_Query( $args );
			$total_posts = $query->post_count;
			wp_reset_postdata();

			// Count the number of posts older than 1 year
			$one_year_ago       = gmdate( 'Y-m-d H:i:s', strtotime( '-1 year' ) );
			$args['date_query'] = array(
				array(
					'before'    => $one_year_ago,
					'inclusive' => true,
				),
			);

			$query     = new \WP_Query( $args );
			$old_posts = $query->post_count;
			wp_reset_postdata();

			$posts_count = array(
				'total_posts' => $total_posts,
				'old_posts'   => $old_posts,
			);

			set_transient( 'tsk_posts_count', $posts_count, DAY_IN_SECONDS );
		}

		// Check if there are more than 100 posts and more than 10 old posts
		return $posts_count['total_posts'] > 100 && $posts_count['old_posts'] > 10;
	}

	/**
	 * Check if should load Woo promos.
	 *
	 * @return bool
	 */
	private function load_woo_promos() {
		$this->woo_promos = array(
			'ppom'                  => array(
				'title'       => Loader::$labels['promotions']['woo']['ppom_title'],
				'description' => Loader::$labels['promotions']['woo']['ppom_desc'],
				'icon'        => '<svg width="25" height="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1800 1800"><path d="M241.023,324.818c0.252,0,0.505,0.035,0.758,0.035h465.68c17.266,0,31.256-13.99,31.256-31.252 c0-17.262-13.99-31.247-31.256-31.247H351.021h-109.24c-17.258,0-31.252,13.985-31.252,31.247 C210.529,310.605,224.121,324.412,241.023,324.818z"/><path d="M210.529,450.306c0,17.257,13.994,31.252,31.252,31.252h769.451c17.262,0,31.256-13.995,31.256-31.252 c0-17.266-13.994-31.252-31.256-31.252H241.781C224.523,419.054,210.529,433.04,210.529,450.306z"/><path d="M1011.232,575.751H241.781c-8.149,0-15.549,3.147-21.116,8.261c-6.213,5.712-10.136,13.879-10.136,22.987 c0,17.262,13.994,31.26,31.252,31.26h769.451c17.262,0,31.256-13.999,31.256-31.26c0-9.108-3.923-17.275-10.141-22.987 C1026.781,578.898,1019.386,575.751,1011.232,575.751z"/><path d="M1011.232,732.461H241.781c-17.258,0-31.252,13.99-31.252,31.247c0,17.262,13.994,31.257,31.252,31.257 h769.451c17.262,0,31.256-13.995,31.256-31.257C1042.488,746.451,1028.494,732.461,1011.232,732.461z"/><path d="M1011.232,889.157H241.781c-8.149,0-15.549,3.147-21.116,8.261c-6.213,5.713-10.136,13.879-10.136,22.987 c0,17.257,13.994,31.261,31.252,31.261h769.451c17.262,0,31.256-14.004,31.256-31.261c0-9.108-3.923-17.274-10.141-22.987 C1026.781,892.305,1019.386,889.157,1011.232,889.157z"/><path d="M1011.232,1045.867H241.781c-17.258,0-31.252,13.99-31.252,31.243c0,17.271,13.994,31.265,31.252,31.265 h769.451c17.262,0,31.256-13.994,31.256-31.265C1042.488,1059.857,1028.494,1045.867,1011.232,1045.867z"/><path d="M1011.232,1202.576H241.781c-17.258,0-31.252,13.995-31.252,31.252c0,17.258,13.994,31.252,31.252,31.252 h769.451c17.262,0,31.256-13.994,31.256-31.252C1042.488,1216.571,1028.494,1202.576,1011.232,1202.576z"/><path d="M1011.232,1359.273H241.781c-8.149,0-15.549,3.151-21.116,8.265c-6.213,5.713-10.136,13.875-10.136,22.987 c0,17.258,13.994,31.261,31.252,31.261h769.451c17.262,0,31.256-14.003,31.256-31.261c0-9.112-3.923-17.274-10.141-22.987 C1026.781,1362.425,1019.386,1359.273,1011.232,1359.273z"/><path d="M1233.542,251.228l-49.851-45.109L1052.136,87.076l-59.185-53.554c-5.293-4.792-11.947-7.421-18.786-7.836 h-3.49H83.676c-45.688,0-82.858,37.375-82.858,83.316v1583.612c0,45.94,37.17,83.316,82.858,83.316h1078.562 c45.68,0,82.845-37.376,82.845-83.316V277.08v-3.182C1244.646,264.73,1240.261,256.589,1233.542,251.228z M1003.117,125.864 l131.119,118.657h-131.119V125.864z M1183.691,1692.613c0,12.094-9.622,21.926-21.454,21.926H83.676 c-11.836,0-21.467-9.832-21.467-21.926V109.001c0-12.089,9.631-21.925,21.467-21.925h857.857V275.38 c0,17.052,13.785,30.862,30.786,30.862h211.372V1692.613z"/><path d="M1798.578,180.737c-7.049-88.305-81.114-158.02-171.205-158.02c-0.004,0-0.004,0-0.004,0 c-45.889,0-89.033,17.874-121.479,50.32c-29.18,29.175-46.519,67.005-49.73,107.699h-0.586v13.609c0,0.06-0.005,0.115-0.005,0.175 c0,0.026,0.005,0.056,0.005,0.082l-0.005,1369.26h0.197c0.557,5.404,2.522,10.731,6.047,15.373l141.135,185.91 c5.803,7.648,14.851,12.136,24.447,12.136c9.601-0.004,18.646-4.496,24.447-12.14l141.093-185.897 c3.528-4.65,5.494-9.982,6.051-15.391h0.197V180.737H1798.578z M1549.299,116.448c20.854-20.855,48.578-32.339,78.07-32.339h0.004 c50.24,0,92.746,33.723,106.076,79.718h-212.19C1526.358,146.098,1535.896,129.852,1549.299,116.448z M1595.372,1502.468 l-78.413,0.005l0.005-1260.345h220.828v1260.336h-81.103l0.009-1016.486l-61.335,0.004L1595.372,1502.468z M1627.382,1695.821 l-100.171-131.963l200.338-0.004L1627.382,1695.821z"/></svg>',
				'has_install' => true,
				'link'        => wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => 'woocommerce-product-addon',
						),
						admin_url( 'update.php' )
					),
					'install-plugin_woocommerce-product-addon'
				),
			),
			'sparks-wishlist'       => array(
				'title'       => Loader::$labels['promotions']['woo']['spark_title1'],
				'description' => Loader::$labels['promotions']['woo']['spark_desc1'],
				'icon'        => '<svg width="25" height="25" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/SVG" aria-hidden="true"><path d="M60 21.25H57.5V40.125H60V21.25Z"></path><path d="M2.5 40H0V8.75C0 6.625 1.625 5 3.75 5H25V7.5H3.75C3 7.5 2.5 8 2.5 8.75V40Z"></path><path d="M56.25 51.25H3.75C1.625 51.25 0 49.625 0 47.5V42.5H25V45H2.5V47.5C2.5 48.25 3 48.75 3.75 48.75H56.25C57 48.75 57.5 48.25 57.5 47.5V43.75H60V47.5C60 49.625 58.375 51.25 56.25 51.25Z"></path><path d="M23.75 58.75H21.25V57.25L22.5 51.125V50H25V51.5L23.75 57.625V58.75Z"></path><path d="M38.75 58.75H36.25V57.625L35 51.25V50H37.5V51.125L38.75 57.5V58.75Z"></path><path d="M41.25 57.5H18.75V60H41.25V57.5Z"></path><path d="M56.25 32.5H43.75C41.625 32.5 40 30.875 40 28.75V3.75C40 1.625 41.625 0 43.75 0H56.25C58.375 0 60 1.625 60 3.75V28.75C60 30.875 58.375 32.5 56.25 32.5ZM43.75 2.5C43 2.5 42.5 3 42.5 3.75V28.75C42.5 29.5 43 30 43.75 30H56.25C57 30 57.5 29.5 57.5 28.75V3.75C57.5 3 57 2.5 56.25 2.5H43.75Z"></path><path d="M50 27.5C50.6904 27.5 51.25 26.9404 51.25 26.25C51.25 25.5596 50.6904 25 50 25C49.3096 25 48.75 25.5596 48.75 26.25C48.75 26.9404 49.3096 27.5 50 27.5Z"></path><path d="M51.25 45H31.25C29.125 45 27.5 43.375 27.5 41.25V8.75C27.5 6.625 29.125 5 31.25 5H37.5V7.5H31.25C30.5 7.5 30 8 30 8.75V41.25C30 42 30.5 42.5 31.25 42.5H51.25C52 42.5 52.5 42 52.5 41.25V35H55V41.25C55 43.375 53.375 45 51.25 45Z"></path><path d="M41.25 40C41.9404 40 42.5 39.4404 42.5 38.75C42.5 38.0596 41.9404 37.5 41.25 37.5C40.5596 37.5 40 38.0596 40 38.75C40 39.4404 40.5596 40 41.25 40Z"></path><path d="M21.75 40H18.25L13.25 35H11.75L6.75 40H0V37.5H5.75L10.75 32.5H14.25L19.25 37.5H20.75L52.875 5.375L54.625 7.125L21.75 40Z"></path><path d="M55 11.25H52.5V7.5H48.75V5H55V11.25Z"></path><defs><clip-path id="clip0"><rect width="60" height="60" fill="white"></rect></clip-path></defs></svg>',
				'link'        => tsdk_utmify( 'https://themeisle.com/plugins/sparks-for-woocommerce/', 'promo', 'products-tabs' ),
			),
			'sparks-announcement'   => array(
				'title'       => Loader::$labels['promotions']['woo']['spark_title2'],
				'description' => Loader::$labels['promotions']['woo']['spark_desc2'],
				'icon'        => '<svg width="25" height="25" viewBox="0 0 60 61" fill="none" xmlns="http://www.w3.org/2000/SVG" aria-hidden="true"><path d="M30 8.89282C29.6685 8.89282 29.3505 8.76113 29.1161 8.52671C28.8817 8.29228 28.75 7.97434 28.75 7.64282V1.39282C28.75 1.0613 28.8817 0.743359 29.1161 0.508939C29.3505 0.274518 29.6685 0.142822 30 0.142822C30.3315 0.142822 30.6495 0.274518 30.8839 0.508939C31.1183 0.743359 31.25 1.0613 31.25 1.39282V7.64282C31.25 7.97434 31.1183 8.29228 30.8839 8.52671C30.6495 8.76113 30.3315 8.89282 30 8.89282Z"></path><path d="M30 21.9105L26.25 18.1605V7.82598L26.9409 7.47992C27.8914 7.00723 28.9385 6.76123 30 6.76123C31.0615 6.76123 32.1086 7.00723 33.0591 7.47992L33.75 7.82598V18.1605L30 21.9105ZM28.75 17.1253L30 18.3753L31.25 17.1253V9.44219C30.4344 9.19928 29.5656 9.19928 28.75 9.44219V17.1253Z"></path><path d="M60 60.1428H0V22.6428H17.5C17.8315 22.6428 18.1495 22.7745 18.3839 23.0089C18.6183 23.2434 18.75 23.5613 18.75 23.8928C18.75 24.2243 18.6183 24.5423 18.3839 24.7767C18.1495 25.0111 17.8315 25.1428 17.5 25.1428H2.5V57.6428H57.5V25.1428H42.5C42.1685 25.1428 41.8505 25.0111 41.6161 24.7767C41.3817 24.5423 41.25 24.2243 41.25 23.8928C41.25 23.5613 41.3817 23.2434 41.6161 23.0089C41.8505 22.7745 42.1685 22.6428 42.5 22.6428H60V60.1428Z"></path><path d="M11.2493 53.8933C11.0421 53.8929 10.8383 53.841 10.6561 53.7424C10.474 53.6438 10.3191 53.5015 10.2055 53.3283C10.0919 53.1551 10.0231 52.9564 10.0052 52.75C9.98727 52.5436 10.0209 52.336 10.103 52.1458L26.353 14.6459C26.4182 14.4953 26.5125 14.359 26.6304 14.2448C26.7483 14.1306 26.8876 14.0408 27.0402 13.9804C27.1928 13.9201 27.3559 13.8903 27.52 13.893C27.6841 13.8956 27.8461 13.9306 27.9967 13.9958C28.1473 14.0611 28.2836 14.1553 28.3978 14.2732C28.5119 14.3912 28.6018 14.5304 28.6621 14.683C28.7225 14.8357 28.7522 14.9987 28.7496 15.1628C28.7469 15.3269 28.712 15.4889 28.6467 15.6395L12.3967 53.1395C12.2999 53.3634 12.1397 53.5541 11.9358 53.6881C11.7319 53.822 11.4932 53.8934 11.2493 53.8933Z"></path><path d="M48.7505 53.8935C48.5065 53.8935 48.2679 53.8222 48.064 53.6883C47.8601 53.5543 47.6999 53.3637 47.603 53.1398L31.353 15.6398C31.2212 15.3356 31.2157 14.9915 31.3376 14.6833C31.4595 14.375 31.6989 14.1278 32.003 13.9961C32.3072 13.8643 32.6513 13.8588 32.9595 13.9807C33.2678 14.1026 33.515 14.3419 33.6467 14.6461L49.8967 52.1461C49.9789 52.3363 50.0125 52.5439 49.9946 52.7503C49.9767 52.9566 49.9078 53.1553 49.7942 53.3285C49.6806 53.5018 49.5258 53.6441 49.3436 53.7427C49.1614 53.8413 48.9576 53.8932 48.7505 53.8936V53.8935Z"></path><path d="M30 33.8928C29.6685 33.8928 29.3505 33.7611 29.1161 33.5267C28.8817 33.2923 28.75 32.9743 28.75 32.6428V25.1428C28.75 24.8113 28.8817 24.4934 29.1161 24.2589C29.3505 24.0245 29.6685 23.8928 30 23.8928C30.3315 23.8928 30.6495 24.0245 30.8839 24.2589C31.1183 24.4934 31.25 24.8113 31.25 25.1428V32.6428C31.25 32.9743 31.1183 33.2923 30.8839 33.5267C30.6495 33.7611 30.3315 33.8928 30 33.8928Z"></path><path d="M45 30.1428H15C14.6685 30.1428 14.3505 30.0111 14.1161 29.7767C13.8817 29.5423 13.75 29.2243 13.75 28.8928C13.75 28.5613 13.8817 28.2434 14.1161 28.0089C14.3505 27.7745 14.6685 27.6428 15 27.6428H45C45.3315 27.6428 45.6495 27.7745 45.8839 28.0089C46.1183 28.2434 46.25 28.5613 46.25 28.8928C46.25 29.2243 46.1183 29.5423 45.8839 29.7767C45.6495 30.0111 45.3315 30.1428 45 30.1428Z"></path><defs><clip-path id="clip0"><rect width="60" height="60" fill="white" transform="translate(0 0.142822)"></rect></clip-path></defs></svg>',
				'link'        => tsdk_utmify( 'https://themeisle.com/plugins/sparks-for-woocommerce/', 'promo', 'products-tabs' ),
			),
			'sparks-product-review' => array(
				'title'       => Loader::$labels['promotions']['woo']['spark_title3'],
				'description' => Loader::$labels['promotions']['woo']['spark_desc3'],
				'icon'        => '<svg width="25" height="25" viewBox="0 0 60 61" fill="none" xmlns="http://www.w3.org/2000/SVG" aria-hidden="true"><path d="M58.75 54.1797H1.25C1.08584 54.1797 0.923271 54.1474 0.771595 54.0846C0.619919 54.0218 0.482103 53.9297 0.366021 53.8137C0.24994 53.6976 0.157867 53.5598 0.0950637 53.4081C0.0322604 53.2564 -4.26571e-05 53.0939 4.22759e-08 52.9297V6.67969C-4.26571e-05 6.51552 0.0322604 6.35296 0.0950637 6.20128C0.157867 6.04961 0.24994 5.91179 0.366021 5.79571C0.482103 5.67963 0.619919 5.58755 0.771595 5.52475C0.923271 5.46195 1.08584 5.42964 1.25 5.42969H58.75C58.9142 5.42964 59.0767 5.46195 59.2284 5.52475C59.3801 5.58755 59.5179 5.67963 59.634 5.79571C59.7501 5.91179 59.8421 6.04961 59.9049 6.20128C59.9677 6.35296 60 6.51552 60 6.67969V52.9297C60 53.0939 59.9677 53.2564 59.9049 53.4081C59.8421 53.5598 59.7501 53.6976 59.634 53.8137C59.5179 53.9297 59.3801 54.0218 59.2284 54.0846C59.0767 54.1474 58.9142 54.1797 58.75 54.1797ZM2.5 51.6797H57.5V7.92969H2.5V51.6797Z"></path><path d="M6.25 15.4297C6.94036 15.4297 7.5 14.87 7.5 14.1797C7.5 13.4893 6.94036 12.9297 6.25 12.9297C5.55964 12.9297 5 13.4893 5 14.1797C5 14.87 5.55964 15.4297 6.25 15.4297Z"></path><path d="M10 15.4297C10.6904 15.4297 11.25 14.87 11.25 14.1797C11.25 13.4893 10.6904 12.9297 10 12.9297C9.30964 12.9297 8.75 13.4893 8.75 14.1797C8.75 14.87 9.30964 15.4297 10 15.4297Z"></path><path d="M13.75 15.4297C14.4404 15.4297 15 14.87 15 14.1797C15 13.4893 14.4404 12.9297 13.75 12.9297C13.0596 12.9297 12.5 13.4893 12.5 14.1797C12.5 14.87 13.0596 15.4297 13.75 15.4297Z"></path><path d="M58.75 15.4297H18.75C18.4185 15.4297 18.1005 15.298 17.8661 15.0636C17.6317 14.8292 17.5 14.5112 17.5 14.1797C17.5 13.8482 17.6317 13.5302 17.8661 13.2958C18.1005 13.0614 18.4185 12.9297 18.75 12.9297H58.75C59.0815 12.9297 59.3995 13.0614 59.6339 13.2958C59.8683 13.5302 60 13.8482 60 14.1797C60 14.5112 59.8683 14.8292 59.6339 15.0636C59.3995 15.298 59.0815 15.4297 58.75 15.4297Z"></path><path d="M28.7502 37.9297C28.4187 37.9295 28.1009 37.7978 27.8664 37.5634L24.4289 34.1259C24.198 33.8908 24.0693 33.574 24.0708 33.2445C24.0723 32.915 24.2039 32.5994 24.4369 32.3664C24.6699 32.1334 24.9855 32.0018 25.315 32.0003C25.6445 31.9988 25.9613 32.1275 26.1964 32.3584L28.6977 34.8597L38.8588 23.4522C38.968 23.3296 39.1002 23.2298 39.2479 23.1583C39.3957 23.0869 39.5561 23.0452 39.7199 23.0358C39.8838 23.0263 40.0479 23.0492 40.2029 23.1032C40.3579 23.1571 40.5008 23.2411 40.6233 23.3503C40.7459 23.4594 40.8457 23.5917 40.9172 23.7394C40.9886 23.8872 41.0303 24.0476 41.0397 24.2114C41.0492 24.3753 41.0263 24.5394 40.9723 24.6944C40.9184 24.8494 40.8344 24.9922 40.7253 25.1148L29.6834 37.511C29.5702 37.6382 29.4322 37.7409 29.2779 37.8129C29.1237 37.8849 28.9563 37.9247 28.7862 37.9298L28.7502 37.9297Z"></path><path d="M29.977 44.1812C28.3217 44.1775 26.6876 43.8085 25.1912 43.1007C23.6948 42.3928 22.3731 41.3635 21.3203 40.0861C20.2675 38.8087 19.5095 37.3148 19.1004 35.7108C18.6913 34.1068 18.6413 32.4322 18.9537 30.8067C19.2662 29.1811 19.9335 27.6445 20.9081 26.3065C21.8827 24.9684 23.1406 23.862 24.592 23.0659C26.0433 22.2699 27.6525 21.804 29.3046 21.7013C30.9568 21.5987 32.6113 21.8619 34.15 22.4722C34.4579 22.5949 34.7044 22.8349 34.8354 23.1393C34.9663 23.4438 34.9709 23.7878 34.8482 24.0957C34.7255 24.4036 34.4856 24.6501 34.1811 24.7811C33.8766 24.912 33.5326 24.9166 33.2247 24.7939C31.44 24.0862 29.472 23.985 27.6241 24.5059C25.7762 25.0269 24.1508 26.141 22.9985 27.6767C21.8462 29.2124 21.2308 31.0844 21.2473 33.0043C21.2637 34.9242 21.9111 36.7854 23.0895 38.3011C24.268 39.8168 25.9122 40.903 27.7688 41.3922C29.6254 41.8813 31.5913 41.7464 33.3637 41.0082C35.136 40.27 36.6164 38.9694 37.5768 37.3069C38.5372 35.6444 38.9242 33.7122 38.6782 31.8081C38.6568 31.6451 38.6678 31.4795 38.7104 31.3208C38.7531 31.1621 38.8267 31.0133 38.927 30.8831C39.0272 30.7528 39.1522 30.6436 39.2947 30.5617C39.4373 30.4798 39.5945 30.4268 39.7575 30.4058C39.9206 30.3848 40.0861 30.3961 40.2448 30.4391C40.4034 30.4822 40.552 30.5561 40.682 30.6566C40.8121 30.7572 40.921 30.8824 41.0026 31.0251C41.0842 31.1678 41.1368 31.3252 41.1574 31.4883C41.3469 32.9535 41.2459 34.4417 40.8602 35.8679C40.4745 37.294 39.8116 38.6303 38.9094 39.8002C38.0072 40.9702 36.8834 41.9509 35.6021 42.6865C34.3208 43.4221 32.9071 43.898 31.4419 44.0872C30.9561 44.1494 30.4668 44.1807 29.977 44.1812Z"></path><defs><clip-path id="clip0"><rect width="60" height="60" fill="white" transform="translate(0 0.429688)"></rect></clip-path></defs></svg>',
				'link'        => tsdk_utmify( 'https://themeisle.com/plugins/sparks-for-woocommerce/', 'promo', 'products-tabs' ),
			),
		);

		// Check if $this-promotions isn't empty and has one of the items to load.
		$can_load = ! empty( $this->promotions ) && count( array_intersect( $this->promotions, array_keys( $this->woo_promos ) ) ) > 0;

		if ( ! $can_load && ! $this->debug ) {
			return;
		}

		add_action(
			'woocommerce_product_data_tabs',
			function ( $tabs ) {
				$tabs['tisdk-suggestions'] = array(
					'label'    => Loader::$labels['promotions']['woo']['title'],
					'target'   => 'tisdk_suggestions',
					'class'    => array(),
					'priority' => 1000,
				);

				return $tabs;
			}
		);

		add_action( 'woocommerce_product_data_panels', array( $this, 'woocommerce_tab_content' ) );
	}

	/**
	 * WooCommerce Tab Content.
	 */
	public function woocommerce_tab_content() {
		// Filter content based on if the key exists in $this->promotions array.
		$content = array_filter(
			$this->woo_promos,
			function ( $key ) {
				return in_array( $key, $this->promotions, true );
			},
			ARRAY_FILTER_USE_KEY
		);

		// Display CSS
		self::render_woo_tabs_css();

		self::render_notice_dismiss_ajax();
		?>

		<div id="tisdk_suggestions" class="panel woocommerce_options_panel hidden">
			<div class="tisdk-suggestions-header">
				<h4><?php echo esc_html( Loader::$labels['promotions']['woo']['title2'] ); ?></h4>
			</div>
			<div class="tisdk-suggestions-content">
				<?php foreach ( $content as $key => $item ) : ?>
					<div class="tisdk-suggestion" id="<?php echo esc_attr( $key ); ?>">
						<?php if ( isset( $item['icon'] ) ) : ?>
							<div class="tisdk-suggestion-icon">
								<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>
						<div class="tisdk-suggestion-content">
							<h4><?php echo esc_html( $item['title'] ); ?></h4>
							<p><?php echo esc_html( $item['description'] ); ?></p>
						</div>
						<div class="tisdk-suggestion-cta">
							<a href="<?php echo esc_url( $item['link'] ); ?>" target="blank" class="button">
								<?php echo( ( isset( $item['has_install'] ) && $item['has_install'] ) ? esc_html( Loader::$labels['promotions']['woo']['cta_install'] ) : esc_html( Loader::$labels['promotions']['woo']['learn_more'] ) ); ?>
							</a>
							<a class="suggestion-dismiss"
							   title="<?php echo esc_attr( Loader::$labels['promotions']['woo']['dismiss'] ); ?>"
							   href="#"></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * CSS for WooCommerce Tabs
	 */
	public static function render_woo_tabs_css() {
		$icon = 'M208 88.286c0-10 6.286-21.714 17.715-21.714 11.142 0 17.714 11.714 17.714 21.714 0 10.285-6.572 21.714-17.714 21.714C214.286 110 208 98.571 208 88.286zm304 160c0 36.001-11.429 102.286-36.286 129.714-22.858 24.858-87.428 61.143-120.857 70.572l-1.143.286v32.571c0 16.286-12.572 30.571-29.143 30.571-10 0-19.429-5.714-24.572-14.286-5.427 8.572-14.856 14.286-24.856 14.286-10 0-19.429-5.714-24.858-14.286-5.142 8.572-14.571 14.286-24.57 14.286-10.286 0-19.429-5.714-24.858-14.286-5.143 8.572-14.571 14.286-24.571 14.286-18.857 0-29.429-15.714-29.429-32.857-16.286 12.285-35.715 19.428-56.571 19.428-22 0-43.429-8.285-60.286-22.857 10.285-.286 20.571-2.286 30.285-5.714-20.857-5.714-39.428-18.857-52-36.286 21.37 4.645 46.209 1.673 67.143-11.143-22-22-56.571-58.857-68.572-87.428C1.143 321.714 0 303.714 0 289.429c0-49.714 20.286-160 86.286-160 10.571 0 18.857 4.858 23.143 14.857a158.792 158.792 0 0 1 12-15.428c2-2.572 5.714-5.429 7.143-8.286 7.999-12.571 11.714-21.142 21.714-34C182.571 45.428 232 17.143 285.143 17.143c6 0 12 .285 17.714 1.143C313.714 6.571 328.857 0 344.572 0c14.571 0 29.714 6 40 16.286.857.858 1.428 2.286 1.428 3.428 0 3.714-10.285 13.429-12.857 16.286 4.286 1.429 15.714 6.858 15.714 12 0 2.857-2.857 5.143-4.571 7.143 31.429 27.714 49.429 67.143 56.286 108 4.286-5.143 10.285-8.572 17.143-8.572 10.571 0 20.857 7.144 28.571 14.001C507.143 187.143 512 221.714 512 248.286zM188 89.428c0 18.286 12.571 37.143 32.286 37.143 19.714 0 32.285-18.857 32.285-37.143 0-18-12.571-36.857-32.285-36.857-19.715 0-32.286 18.858-32.286 36.857zM237.714 194c0-19.714 3.714-39.143 8.571-58.286-52.039 79.534-13.531 184.571 68.858 184.571 21.428 0 42.571-7.714 60-20 2-7.429 3.714-14.857 3.714-22.572 0-14.286-6.286-21.428-20.572-21.428-4.571 0-9.143.857-13.429 1.714-63.343 12.668-107.142 3.669-107.142-63.999zm-41.142 254.858c0-11.143-8.858-20.857-20.286-20.857-11.429 0-20 9.715-20 20.857v32.571c0 11.143 8.571 21.142 20 21.142 11.428 0 20.286-9.715 20.286-21.142v-32.571zm49.143 0c0-11.143-8.572-20.857-20-20.857-11.429 0-20.286 9.715-20.286 20.857v32.571c0 11.143 8.857 21.142 20.286 21.142 11.428 0 20-10 20-21.142v-32.571zm49.713 0c0-11.143-8.857-20.857-20.285-20.857-11.429 0-20.286 9.715-20.286 20.857v32.571c0 11.143 8.857 21.142 20.286 21.142 11.428 0 20.285-9.715 20.285-21.142v-32.571zm49.715 0c0-11.143-8.857-20.857-20.286-20.857-11.428 0-20.286 9.715-20.286 20.857v32.571c0 11.143 8.858 21.142 20.286 21.142 11.429 0 20.286-10 20.286-21.142v-32.571zM421.714 286c-30.857 59.142-90.285 102.572-158.571 102.572-96.571 0-160.571-84.572-160.571-176.572 0-16.857 2-33.429 6-49.714-20 33.715-29.714 72.572-29.714 111.429 0 60.286 24.857 121.715 71.429 160.857 5.143-9.714 14.857-16.286 26-16.286 10 0 19.428 5.714 24.571 14.286 5.429-8.571 14.571-14.286 24.858-14.286 10 0 19.428 5.714 24.571 14.286 5.429-8.571 14.857-14.286 24.858-14.286 10 0 19.428 5.714 24.857 14.286 5.143-8.571 14.571-14.286 24.572-14.286 10.857 0 20.857 6.572 25.714 16 43.427-36.286 68.569-92 71.426-148.286zm10.572-99.714c0-53.714-34.571-105.714-92.572-105.714-30.285 0-58.571 15.143-78.857 36.857C240.862 183.812 233.41 254 302.286 254c28.805 0 97.357-28.538 84.286 36.857 28.857-26 45.714-65.714 45.714-104.571z';

		?>
		<style>
			.tisdk-suggestions_options a {
				display: flex !important;
				align-items: center;
			}

			.tisdk-suggestions_options a::before {
				content: url("data:image/svg+xml,%3Csvg fill='%23135e96' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath d='<?php echo esc_attr( $icon ); ?>'/%3E%3C/svg%3E") !important;
				min-width: 13px;
				max-width: 13px;
			}

			.tisdk-suggestions_options.active a::before {
				content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath d='<?php echo esc_attr( $icon ); ?>'/%3E%3C/svg%3E") !important;
			}

			.tisdk-suggestions-header {
				padding: 1em 1.5em;
				border-bottom: 1px solid #eee;
			}

			.tisdk-suggestions-header h4 {
				font-size: 1.1em;
				margin: 0;
			}

			.tisdk-suggestion {
				display: flex;
				align-items: center;
				flex-direction: row;
				padding: 1em 1.5em;
			}

			.tisdk-suggestion-icon {
				height: 40px;
				margin: 0;
				margin-right: 0px;
				margin-right: 1.5em;
				flex: 0 0 40px;
				background: #966ccf;
				display: flex;
				justify-content: center;
				border-radius: 100%;
				align-items: center;
				padding: 5px;
			}

			.tisdk-suggestion-icon svg {
				fill: #fff;
			}

			.tisdk-suggestion-content {
				flex: 1 1 60%;
			}

			.tisdk-suggestion-content h4 {
				margin: 0;
			}

			.tisdk-suggestion-content p {
				margin: 0;
				margin-top: 4px;
				padding: 0;
				line-height: 1.5;
			}

			.tisdk-suggestion-cta {
				flex: 1 1 30%;
				min-width: 160px;
				text-align: right;
			}

			.tisdk-suggestion-cta .button {
				display: inline-block;
				min-width: 120px;
				text-align: center;
				margin: 0;
			}

			.tisdk-suggestion-cta .suggestion-dismiss {
				position: relative;
				top: 5px;
				right: auto;
				margin-left: 1em;
				text-decoration: none;
			}
		</style>
		<?php
	}

	/**
	 * JS for Dismissing Notice
	 */
	public static function render_notice_dismiss_ajax() {
		?>
		<script>
			jQuery(document).ready(function ($) {
				// AJAX request to update the option value
				$('.tisdk-suggestion .suggestion-dismiss').click(function (e) {
					e.preventDefault();
					var suggestion = $(this).closest('.tisdk-suggestion');
					var value = suggestion.attr('id');

					var nonce = '<?php echo esc_attr( wp_create_nonce( 'tisdk_update_option' ) ); ?>';

					$.ajax({
						url: window.ajaxurl,
						type: 'POST',
						data: {
							action: 'tisdk_update_option',
							value,
							nonce
						},
						complete() {
							suggestion.remove();

							// If element with .tisdk-suggestions-content has no children, hide the whole panel. Skip if the selector doesn't exist.
							if ($('.tisdk-suggestions-content').length && !$('.tisdk-suggestions-content').children().length) {
								$('.tisdk-suggestions_options').remove();
								$('#tisdk_suggestions').remove();
								$('.general_options').addClass('active');
								$('#general_product_data').css('display', 'block');
							}
						}
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Update the option value using AJAX
	 */
	public function dismiss_promotion() {
		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['value'] ) ) {
			$response = array(
				'success' => false,
				'message' => 'Missing nonce or value.',
			);
			wp_send_json( $response );
			wp_die();
		}

		$nonce = sanitize_text_field( $_POST['nonce'] );
		$value = sanitize_text_field( $_POST['value'] );

		if ( ! wp_verify_nonce( $nonce, 'tisdk_update_option' ) ) {
			$response = array(
				'success' => false,
				'message' => 'Invalid nonce.',
			);
			wp_send_json( $response );
			wp_die();
		}

		$options = get_option( $this->option_main );
		$options = json_decode( $options, true );

		$options[ $value ] = time();

		update_option( $this->option_main, wp_json_encode( $options ) );

		$response = array(
			'success' => true,
		);

		wp_send_json( $response );
		wp_die();
	}

	/**
	 * Check if the user has a support page.
	 */
	public function has_support_page() {
		$transient_name = 'tisdk_has_support_page';
		$has_support    = get_transient( $transient_name );

		if ( false === $has_support ) {
			global $wpdb;

			// We use %i escape identifier that was added in WP 6.2.0, hence need to ignore PHPCS warning.
			// We only show this notice to users on higher version as that is the minimum for Hyve as well.
			$query = $wpdb->get_var( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare( // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
					'SELECT ID FROM %i WHERE post_type = %s AND post_status = %s AND post_title LIKE %s LIMIT 1', // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder
					$wpdb->posts,
					'page',
					'publish',
					'%support%'
				)
			);

			$has_support = $query ? 'yes' : 'no';

			set_transient( $transient_name, $has_support, 7 * DAY_IN_SECONDS );
		}

		return 'yes' === $has_support;
	}

	/**
	 * Check if the user has a donate page.
	 */
	public function has_donate_page() {
		$transient_name = 'tisdk_has_donate_page';
		$has_donate     = get_transient( $transient_name );

		if ( false === $has_donate ) {
			global $wpdb;

			$query = $wpdb->get_var( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = %s AND post_status = %s AND post_title LIKE %s LIMIT 1',
					'page',
					'publish',
					'%donate%'
				)
			);

			$has_donate = $query ? 'yes' : 'no';

			set_transient( $transient_name, $has_donate, 7 * DAY_IN_SECONDS );
		}

		return 'yes' === $has_donate;
	}

	/**
	 * Check if the tagline contains LMS related keywords.
	 *
	 * @return bool True if the tagline contains LMS-related keywords, false otherwise.
	 */
	public function has_lms_tagline() {
		$tagline      = strtolower( get_bloginfo( 'description' ) );
		$lms_keywords = array( 'learning', 'courses' );

		foreach ( $lms_keywords as $keyword ) {
			if ( strpos( $tagline, $keyword ) !== false ) {
				return true;
			}
		}

		return false;
	}
}
