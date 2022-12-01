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
	private $promotions = array();

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
	 * Loaded promotion.
	 *
	 * @var string
	 */
	private $loaded_promo;

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

		$this->debug          = apply_filters( 'themeisle_sdk_promo_debug', $this->debug );
		$promotions_to_load   = apply_filters( $product->get_key() . '_load_promotions', array() );
		$promotions_to_load[] = 'optimole';

		$this->promotions = $this->get_promotions();

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

		$this->product = $product;

		$last_dismiss = $this->get_last_dismiss_time();

		if ( ! $this->debug && $last_dismiss && ( time() - $last_dismiss ) < 7 * DAY_IN_SECONDS ) {
			return;
		}

		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_field' ), 10, 2 );
		add_action( 'current_screen', [ $this, 'load_available' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'themeisle_sdk_ran_promos', '__return_true' );
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
		if ( isset( $_GET['reference_key'] ) ) {
			update_option( 'otter_reference_key', sanitize_key( $_GET['reference_key'] ) );
		}

		if ( isset( $_GET['optimole_reference_key'] ) ) {
			update_option( 'optimole_reference_key', sanitize_key( $_GET['optimole_reference_key'] ) );
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
	}

	/**
	 * Get the SDK base url.
	 *
	 * @return string
	 */
	private function get_sdk_uri() {
		global $themeisle_sdk_max_path;

		if ( $this->product->is_plugin() ) {
			return plugins_url( '/', $themeisle_sdk_max_path . '/themeisle-sdk/' );
		};

		return get_template_directory_uri() . '/vendor/codeinwp/themeisle-sdk/';
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
	 * Get the Otter Blocks plugin status.
	 *
	 * @param string $plugin Plugin slug.
	 *
	 * @return bool
	 */
	private function is_plugin_installed( $plugin ) {
		static $allowed_keys = [
			'otter-blocks' => 'otter-blocks/otter-blocks.php',
			'optimole-wp'  => 'optimole-wp/optimole-wp.php',
		];

		if ( ! isset( $allowed_keys[ $plugin ] ) ) {
			return false;
		}

		if ( file_exists( WP_CONTENT_DIR . '/plugins/' . $allowed_keys[ $plugin ] ) ) {
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
		$has_otter               = defined( 'OTTER_BLOCKS_VERSION' ) || $this->is_plugin_installed( 'otter-blocks' );
		$had_otter_from_promo    = get_option( $this->option_otter, false );
		$has_optimole            = defined( 'OPTIMOLE_VERSION' ) || $this->is_plugin_installed( 'optimole-wp' );
		$had_optimole_from_promo = get_option( $this->option_optimole, false );
		$is_min_req_v            = version_compare( get_bloginfo( 'version' ), '5.8', '>=' );
		$has_enough_attachments  = $this->has_min_media_attachments();

		$all = [
			'optimole' => [
				'om-editor'     => [
					'env'    => ! $has_optimole && $is_min_req_v && ! $had_optimole_from_promo,
					'screen' => 'editor',
				],
				'om-attachment' => [
					'env'    => ! $has_optimole && ! $had_optimole_from_promo,
					'screen' => 'media',
				],
				'om-media'      => [
					'env'    => ! $has_optimole && ! $had_optimole_from_promo && $has_enough_attachments,
					'screen' => 'media',
				],
				'om-elementor'  => [
					'env'    => ! $has_optimole && ! $had_optimole_from_promo && defined( 'ELEMENTOR_VERSION' ),
					'screen' => 'elementor',
				],
			],
			'otter'    => [
				'blocks-css'        => [
					'env'    => ! $has_otter && $is_min_req_v && ! $had_otter_from_promo,
					'screen' => 'editor',
				],
				'blocks-animation'  => [
					'env'    => ! $has_otter && $is_min_req_v && ! $had_otter_from_promo,
					'screen' => 'editor',
				],
				'blocks-conditions' => [
					'env'    => ! $has_otter && $is_min_req_v && ! $had_otter_from_promo,
					'screen' => 'editor',
				],
			],
		];

		foreach ( $all as $slug => $data ) {
			foreach ( $data as $key => $conditions ) {
				if ( ! $conditions['env'] ) {
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
	 * @return false | string
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
	 * @return false
	 */
	private function get_last_dismiss_time() {
		$dismissed = $this->get_upsells_dismiss_time();

		if ( empty( $dismissed ) ) {
			return false;
		}

		$last_dismiss = max( array_values( $dismissed ) );

		return $last_dismiss;
	}

	/**
	 * Filter by screen & merge into single array of keys.
	 *
	 * @return array
	 */
	private function filter_by_screen_and_merge() {
		$current_screen = get_current_screen();

		$is_elementor = isset( $_GET['action'] ) && $_GET['action'] === 'elementor';
		$is_media     = isset( $current_screen->id ) && $current_screen->id === 'upload';
		$is_editor    = method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor();

		$return = [];

		foreach ( $this->promotions as $slug => $promos ) {
			foreach ( $promos as $key => $data ) {
				switch ( $data['screen'] ) {
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
					case 'elementor':
						if ( ! $is_elementor ) {
							unset( $this->promotions[ $slug ][ $key ] );
						}
						break;
				}
			}

			$return = array_merge( $return, $this->promotions[ $slug ] );
		}

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

			return;
		}

		switch ( $slug ) {
			case 'om-editor':
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
		$asset_file        = require $themeisle_sdk_max_path . '/assets/js/build/index.asset.php';
		$deps              = array_merge( $asset_file['dependencies'], [ 'updates' ] );

		wp_register_script( $handle, $themeisle_sdk_src . 'assets/js/build/index.js', $deps, $asset_file['version'], true );
		wp_localize_script(
			$handle,
			'themeisleSDKPromotions',
			[
				'debug'                 => $this->debug,
				'email'                 => $user->user_email,
				'showPromotion'         => $this->loaded_promo,
				'optionKey'             => $this->option_main,
				'product'               => $this->product->get_name(),
				'option'                => empty( $saved ) ? new \stdClass() : $saved,
				'nonce'                 => wp_create_nonce( 'wp_rest' ),
				'assets'                => $themeisle_sdk_src . 'assets/images/',
				'optimoleApi'           => esc_url( rest_url( 'optml/v1/register_service' ) ),
				'optimoleActivationUrl' => $this->get_plugin_activation_link( 'optimole-wp' ),
				'otterActivationUrl'    => $this->get_plugin_activation_link( 'otter-blocks' ),
				'optimoleDash'          => esc_url( add_query_arg( [ 'page' => 'optimole' ], admin_url( 'upload.php' ) ) ),
				// translators: %s is the product name.
				'title'                 => esc_html( sprintf( __( 'Recommended by %s', 'neve' ), $this->product->get_name() ) ),
			]
		);
		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle, $themeisle_sdk_src . 'assets/js/build/style-index.css', [ 'wp-components' ], $asset_file['version'] );
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

		if ( isset( $meta['filesize'] ) && $meta['filesize'] < 200000 ) {
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
	 * Get plugin activation link.
	 *
	 * @param string $slug The plugin slug.
	 *
	 * @return string
	 */
	private function get_plugin_activation_link( $slug ) {
		$reference_key = $slug === 'otter-blocks' ? 'reference_key' : 'optimole_reference_key';

		return esc_url(
			add_query_arg(
				array(
					'plugin_status' => 'all',
					'paged'         => '1',
					'action'        => 'activate',
					$reference_key  => $this->product->get_key(),
					'plugin'        => rawurlencode( $slug . '/' . $slug . '.php' ),
					'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $slug . '/' . $slug . '.php' ),
				),
				admin_url( 'plugins.php' )
			)
		);
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

		return array_sum( (array) wp_count_attachments( 'image' ) ) > 50;
	}
}
