<?php
/**
 * The Support Hub module for ThemeIsle SDK.
 *
 * One floating Help button on the product's admin screens that opens straight
 * into a short contact form, nudges at the moments users need help most
 * (unfinished setup) and a simpler deactivation window.
 *
 * Here's how to hook it in your product:
 *
 * add_filter( '<product_key>_support_hub_metadata', 'add_support_hub_meta' );
 *
 * function add_support_hub_meta( $data ) {
 *  return [
 *       'pages'          => [ 'my-product-' ],                // admin screen ids, exact or prefix (trailing - or _)
 *       'is_pro'         => My_Product::has_pro(),
 *       'links'          => [                                    // footer links: label, URL, Dashicons slug
 *           [ 'icon' => 'book-alt', 'text' => 'Docs', 'url' => 'https://docs.themeisle.com/...' ],
 *           [ 'icon' => 'groups', 'text' => 'Forum', 'url' => 'https://wordpress.org/support/plugin/...' ],
 *       ],
 *       'setup_complete' => function () { return My_Product::is_configured(); }, // optional, enables the setup nudge
 *       'error_selectors'=> [ '.my-product-error-box' ],       // optional, extra error containers
 *       'support_email'  => 'support@example.com',             // optional, mailto fallback
 *       'primary_color'  => '#7b61ff',                         // optional, accent colour (hex)
 *       'internal_slug'  => 'mlo',                             // optional, the slug passed to themeisle_internal_page when it is not the install slug
 *  ];
 * }
 *
 * Products that fire `themeisle_internal_page` on their screens need no `pages`:
 * every screen declared that way is a product screen.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2026, Themeisle
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.4.0
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
 * Support Hub module for ThemeIsle SDK.
 *
 * @phpstan-type Metadata array{pages: list<string>, is_pro: bool, links: list<array{icon: string, text: string, url: string}>, setup_complete: (callable(): bool)|null, error_selectors: list<string>, support_email: string, primary_color: string, internal_slug: string}
 * @phpstan-type RegisteredProduct array{product: Product, data: Metadata}
 * @phpstan-type CrashSummary array{fingerprint: string, type: int, event_type: 'fatal_error'|'uncaught_exception', message: string, file: string, line: int, trace: list<array{file: string, line: int, function: string}>, count: int, first_seen: int, last_seen: int, product_version: string}
 * @phpstan-type SiteInfo array{product: string, product_name: string, product_version: string, plan: string, license_status: string, wp_version: string, php_version: string, locale: string, multisite: bool, theme: array{name: string|false, version: string|false}, active_plugins: list<array{name: string, version: string}>, crashes: list<CrashSummary>, sdk_version: string}
 * @phpstan-type Payload array{product: string, product_name: string, version: string, plan: string, source: string, message: string, email: string, site: string, context: array<array-key, scalar>, site_info: SiteInfo|null, sdk_version: string}
 * @phpstan-type UserState array{setup?: array<string, int>}
 * @phpstan-type DeliveryResult array{sent: bool, dry_run: bool, payload?: Payload}
 */
class Support_Hub extends Abstract_Module {
	const AJAX_SEND = 'themeisle_sdk_support_hub_send';
	const AJAX_MARK = 'themeisle_sdk_support_hub_mark';
	const NONCE     = 'themeisle_sdk_support_hub';
	const USER_META = 'themeisle_sdk_support_hub';

	/**
	 * Where the mailto: fallback goes when the API cannot be reached.
	 */
	const DEFAULT_EMAIL = 'friends@themeisle.com';

	/**
	 * The setup nudge waits this long after the product was installed.
	 */
	const SETUP_MIN_AGE = 3 * DAY_IN_SECONDS;

	/**
	 * And repeats at most this often.
	 */
	const SETUP_COOLDOWN = WEEK_IN_SECONDS;

	/**
	 * One send per user per this many seconds.
	 */
	const SEND_THROTTLE = 30;

	/**
	 * Longest message accepted.
	 */
	const MAX_MESSAGE_LENGTH = 5000;

	/**
	 * Production URL of the tracking API, used unless the endpoint filter
	 * supplies another one.
	 */
	const ENDPOINT = 'https://api.themeisle.com/tracking/support';

	/**
	 * Query parameter that forces a nudge when `themeisle_sdk_support_hub_debug` is on.
	 */
	const DEBUG_QUERY = 'ti_support_nudge';

	/**
	 * Sources a message can carry.
	 */
	const SOURCES = [ 'widget', 'error', 'setup', 'deactivation', 'custom' ];

	/**
	 * Plugin deactivation reasons, in display order. `feedback_id` is the id
	 * sent to the existing uninstall feedback endpoint so Metabase keeps its
	 * data; 11 is new ("Missing something I need"), the others reuse the old
	 * survey ids.
	 *
	 * @var array<string, array{feedback_id: int, message: bool}>
	 */
	const REASONS = [
		'not_working'    => [
			'feedback_id' => 4,
			'message'     => true,
		],
		'missing'        => [
			'feedback_id' => 11,
			'message'     => true,
		],
		'no_longer_need' => [
			'feedback_id' => 5,
			'message'     => false,
		],
		'switching'      => [
			'feedback_id' => 3,
			'message'     => false,
		],
		'temporary'      => [
			'feedback_id' => 6,
			'message'     => false,
		],
		'other'          => [
			'feedback_id' => 999,
			'message'     => true,
		],
	];

	/**
	 * Theme switch reasons, in display order, on the old theme survey ids.
	 *
	 * @var array<string, array{feedback_id: int, message: bool}>
	 */
	const REASONS_THEME = [
		'not_like_demo'   => [
			'feedback_id' => 7,
			'message'     => true,
		],
		'missing'         => [
			'feedback_id' => 8,
			'message'     => true,
		],
		'plugin_conflict' => [
			'feedback_id' => 9,
			'message'     => true,
		],
		'new_design'      => [
			'feedback_id' => 10,
			'message'     => false,
		],
		'other'           => [
			'feedback_id' => 999,
			'message'     => true,
		],
	];

	/**
	 * Every product that opted in, keyed by product key. Shared by all the
	 * instances so one screen renders ONE launcher.
	 *
	 * @var array<string, RegisteredProduct>
	 */
	private static $registered = array();

	/**
	 * Whether the shared hooks were added already.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * Product slugs that fired `themeisle_internal_page` on this request.
	 *
	 * @var array<string, bool>
	 */
	private static $internal_pages = array();

	/**
	 * Key of the product that owns the launcher on this screen.
	 *
	 * @var string|null
	 */
	private static $owner = null;

	/**
	 * Whether the assets were enqueued on this request.
	 *
	 * @var bool
	 */
	private static $enqueued = false;

	/**
	 * Whether the markup was printed on this request.
	 *
	 * @var bool
	 */
	private static $rendered = false;

	/**
	 * This product's metadata, received from the filter.
	 *
	 * @var Metadata|array{}
	 */
	private $data = array();

	/**
	 * Should we load this module.
	 *
	 * @param Product $product Product object.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}
		if ( apply_filters( 'themeisle_sdk_hide_support_hub', false ) || apply_filters( $product->get_key() . '_hide_support_hub', false ) ) {
			return false;
		}

		$this->data = self::sanitize_metadata( apply_filters( $product->get_key() . '_support_hub_metadata', array() ), $product );

		return ! empty( $this->data );
	}

	/**
	 * Keep only what the module can use, with universal defaults.
	 *
	 * @param mixed   $data    Raw filter result.
	 * @param Product $product Product object.
	 *
	 * @return Metadata|array{} Empty when the product did not opt in.
	 */
	public static function sanitize_metadata( $data, $product ) {
		if ( ! is_array( $data ) || empty( $data ) ) {
			return array();
		}
		$strings = function ( $list, $max = 20 ) {
			$list = is_array( $list ) ? $list : ( is_string( $list ) && '' !== $list ? array( $list ) : array() );
			$list = array_filter(
				array_map(
					function ( $item ) {
						return is_string( $item ) ? trim( wp_strip_all_tags( $item ) ) : '';
					},
					$list
				)
			);
			return array_slice( array_values( $list ), 0, $max );
		};
		$url     = function ( $value ) {
			return is_string( $value ) ? esc_url_raw( $value ) : '';
		};

		$email         = isset( $data['support_email'] ) && is_string( $data['support_email'] ) ? sanitize_email( $data['support_email'] ) : '';
		$color         = isset( $data['primary_color'] ) && is_string( $data['primary_color'] ) ? sanitize_hex_color( trim( $data['primary_color'] ) ) : '';
		$internal_slug = isset( $data['internal_slug'] ) && is_string( $data['internal_slug'] ) ? sanitize_key( $data['internal_slug'] ) : '';

		// Footer links: each needs a text and a URL; the icon is a Dashicons slug (without the `dashicons-` prefix).
		$links = array();
		foreach ( ( isset( $data['links'] ) && is_array( $data['links'] ) ) ? $data['links'] : array() as $link ) {
			if ( ! is_array( $link ) || empty( $link['text'] ) || empty( $link['url'] ) || ! is_string( $link['text'] ) || ! is_string( $link['url'] ) ) {
				continue;
			}
			$icon = isset( $link['icon'] ) && is_string( $link['icon'] ) ? strtolower( trim( $link['icon'] ) ) : '';
			$icon = preg_replace( '/^dashicons-/', '', $icon );
			if ( ! preg_match( '/^[a-z0-9-]+$/', $icon ) ) {
				$icon = '';
			}
			$links[] = array(
				'icon' => $icon,
				'text' => trim( wp_strip_all_tags( $link['text'] ) ),
				'url'  => $url( $link['url'] ),
			);
			if ( 6 === count( $links ) ) {
				break;
			}
		}

		return array(
			'pages'           => $strings( isset( $data['pages'] ) ? $data['pages'] : array(), 50 ),
			'is_pro'          => ! empty( $data['is_pro'] ),
			'links'           => $links,
			'setup_complete'  => isset( $data['setup_complete'] ) && is_callable( $data['setup_complete'] ) ? $data['setup_complete'] : null,
			// Every selector the product lists gets its link, so no cap here.
			'error_selectors' => $strings( isset( $data['error_selectors'] ) ? $data['error_selectors'] : array(), PHP_INT_MAX ),
			'support_email'   => '' !== $email ? $email : self::DEFAULT_EMAIL,
			'primary_color'   => is_string( $color ) ? $color : '',
			'internal_slug'   => '' !== $internal_slug ? $internal_slug : $product->get_slug(),
		);
	}

	/**
	 * Registers the hooks.
	 *
	 * @param Product $product Product to load.
	 *
	 * @return Support_Hub
	 */
	public function load( $product ) {
		$this->product = $product;

		self::$registered[ $product->get_key() ] = array(
			'product' => $product,
			'data'    => $this->data,
		);

		if ( self::$hooked ) {
			return $this;
		}
		self::$hooked = true;

		add_action( 'themeisle_internal_page', array( __CLASS__, 'mark_internal_page' ), 10, 2 );
		// Late: after the products had their chance to fire themeisle_internal_page.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 999 );
		// admin_footer runs BEFORE admin_print_footer_scripts, so the markup is
		// in the DOM when the script initialises.
		add_action( 'admin_footer', array( $this, 'render' ) );
		add_action( 'wp_ajax_' . self::AJAX_SEND, array( $this, 'ajax_send' ) );
		add_action( 'wp_ajax_' . self::AJAX_MARK, array( $this, 'ajax_mark' ) );

		return $this;
	}

	/**
	 * Remember which product's internal page this is.
	 *
	 * @param string $product_slug Product slug.
	 * @param string $page_slug    Page slug.
	 *
	 * @return void
	 */
	public static function mark_internal_page( $product_slug, $page_slug = '' ) {
		if ( ! is_string( $product_slug ) ) {
			return;
		}
		if ( false !== strpos( $product_slug, '/' ) ) {
			$product_slug = dirname( $product_slug );
		}
		self::$internal_pages[ $product_slug ] = true;
	}

	/**
	 * Only administrators can contact support through the hub.
	 *
	 * @return bool
	 */
	public static function user_can_use() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Did this product opt in?
	 *
	 * @param Product $product Product.
	 *
	 * @return bool
	 */
	public static function handles( $product ) {
		return isset( self::$registered[ $product->get_key() ] );
	}

	/**
	 * Does the hub own the deactivation flow of this product? Plugins get the
	 * window on the Plugins screen, themes get feedback on Add Themes.
	 *
	 * @param Product $product Product.
	 *
	 * @return bool
	 */
	public static function handles_deactivation( $product ) {
		return ( $product->is_plugin() || $product->is_theme() ) && self::handles( $product ) && self::user_can_use() && ! apply_filters( $product->get_key() . '_hide_uninstall_feedback', false );
	}

	/**
	 * Is the current screen one of the product's screens?
	 *
	 * @param Product  $product Product.
	 * @param Metadata $data Product metadata.
	 *
	 * @return bool
	 */
	public static function is_product_screen( $product, $data ) {
		if ( ! empty( self::$internal_pages[ $product->get_slug() ] ) || ! empty( self::$internal_pages[ $data['internal_slug'] ] ) ) {
			return true;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || empty( $screen->id ) ) {
			return false;
		}
		foreach ( $data['pages'] as $page ) {
			if ( $page === $screen->id ) {
				return true;
			}
			$last = substr( $page, - 1 );
			if ( ( '-' === $last || '_' === $last ) && 0 === strpos( $screen->id, $page ) ) {
				return true;
			}
			// A `page=` slug, like the old float widget accepted.
			if ( false !== strpos( $screen->id, '_page_' . $page ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * The product that owns the launcher on this screen: the first one whose
	 * screen this is. Not cached before the footer: products fire
	 * themeisle_internal_page from their own admin_enqueue_scripts callbacks.
	 *
	 * @return RegisteredProduct|null
	 */
	public static function current() {
		if ( null !== self::$owner && isset( self::$registered[ self::$owner ] ) ) {
			return self::$registered[ self::$owner ];
		}
		foreach ( self::$registered as $key => $entry ) {
			if ( self::is_product_screen( $entry['product'], $entry['data'] ) ) {
				self::$owner = $key;
				return $entry;
			}
		}

		return null;
	}

	/**
	 * The products whose deactivation window is rendered on this screen: every
	 * hub plugin on the Plugins screen, the active hub theme on the Add Themes
	 * screen (the same trigger as the legacy theme drawer, which the window
	 * replaces), unless its feedback was sent in the last 100 days.
	 *
	 * @return array<string, RegisteredProduct>
	 */
	private static function deactivation_products() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$id     = $screen && ! empty( $screen->id ) ? $screen->id : '';
		if ( ! in_array( $id, array( 'plugins', 'theme-install' ), true ) ) {
			return array();
		}
		$list = array();
		foreach ( self::$registered as $key => $entry ) {
			$product = $entry['product'];
			if ( ! self::handles_deactivation( $product ) ) {
				continue;
			}
			if ( $product->is_plugin() && 'plugins' === $id ) {
				$list[ $key ] = $entry;
			} elseif ( $product->is_theme() && 'theme-install' === $id && in_array( $product->get_slug(), array( get_stylesheet(), get_template() ), true ) && false === get_transient( 'ti_sdk_pause_' . $key ) ) {
				$list[ $key ] = $entry;
			}
		}

		return $list;
	}

	/**
	 * Assets, only where the launcher or the deactivation window can be.
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( self::$enqueued || ! self::user_can_use() ) {
			return;
		}
		$current      = self::current();
		$deactivation = self::deactivation_products();
		if ( null === $current && empty( $deactivation ) ) {
			return;
		}
		self::$enqueued = true;

		$handle = 'themeisle-sdk-support-hub';
		$base   = $this->get_sdk_uri() . 'assets/js/build/support_hub/';
		$asset  = dirname( dirname( __DIR__ ) ) . '/assets/js/build/support_hub/support-hub.asset.php';
		$meta   = is_readable( $asset ) ? require $asset : array(
			'dependencies' => array(),
			'version'      => Loader::get_version(),
		);

		wp_enqueue_style( $handle, $base . 'support-hub.css', array( 'dashicons' ), $meta['version'] );
		wp_enqueue_script( $handle, $base . 'support-hub.js', $meta['dependencies'], $meta['version'], true );
		// Not wp_localize_script(): before WordPress 6.9 it leaves `<` unescaped,
		// so a `<!--<script>` in any string would swallow the rest of the page.
		wp_add_inline_script(
			$handle,
			'var themeisleSDKSupportHub = ' . wp_json_encode( $this->localize( $current, $deactivation ), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) . ';',
			'before'
		);
	}

	/**
	 * Everything the script needs.
	 *
	 * @param RegisteredProduct|null           $current The product owning the launcher.
	 * @param array<string, RegisteredProduct> $deactivation Products with a deactivation window on this screen.
	 *
	 * @return array
	 */
	private function localize( $current, $deactivation ) {
		$user   = wp_get_current_user();
		$labels = Loader::$labels['support_hub'];

		$config = array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( self::NONCE ),
			'actions'      => array(
				'send' => self::AJAX_SEND,
				'mark' => self::AJAX_MARK,
			),
			'user'         => array(
				'email' => $user ? $user->user_email : '',
			),
			'labels'       => $labels,
			'product'      => null,
			'nudges'       => array(),
			'deactivation' => array(),
			'maxLength'    => self::MAX_MESSAGE_LENGTH,
		);

		if ( null !== $current ) {
			$product = $current['product'];
			$data    = $current['data'];

			$config['product'] = array(
				'key'            => $product->get_key(),
				'slug'           => $product->get_slug(),
				'name'           => $product->get_friendly_name(),
				'isPro'          => $data['is_pro'],
				'links'          => $data['links'],
				'errorSelectors' => $data['error_selectors'],
				'supportEmail'   => $data['support_email'],
				'primaryColor'   => $data['primary_color'],
			);
			$config['nudges']  = $this->nudges( $product, $data );
			$config['labels']  = self::fill_labels( $labels, $product->get_friendly_name() );
		}

		foreach ( $deactivation as $key => $entry ) {
			$product = $entry['product'];
			$theme   = $product->is_theme();
			$texts   = $labels[ $theme ? 'reasons_theme' : 'reasons' ];
			$reasons = array();
			foreach ( $theme ? self::REASONS_THEME : self::REASONS as $id => $reason ) {
				$reasons[] = array(
					'id'          => $id,
					'label'       => $texts[ $id ]['label'],
					'placeholder' => $reason['message'] ? $texts[ $id ]['placeholder'] : '',
					'message'     => $reason['message'],
					'feedbackId'  => $reason['feedback_id'],
				);
			}
			$config['deactivation'][ $product->get_slug() ] = array(
				'key'            => $key,
				'type'           => $product->get_type(),
				'name'           => $product->get_friendly_name(),
				'supportEmail'   => $entry['data']['support_email'],
				'primaryColor'   => $entry['data']['primary_color'],
				'feedbackAction' => $key . '_uninstall_feedback',
				'feedbackNonce'  => wp_create_nonce( 'ThemeisleSDK\Modules\Uninstall_Feedback' ),
				'reasons'        => $reasons,
				// Themes: the window opens by itself, like the drawer it replaces.
				'autoOpen'       => $theme ? Uninstall_Feedback::AUTO_TRIGGER_DEACTIVATE_WINDOW_SECONDS : 0,
			);
		}

		return $config;
	}

	/**
	 * Product name into the labels that have a placeholder for it.
	 *
	 * @param array  $labels Labels.
	 * @param string $name   Product name.
	 *
	 * @return array
	 */
	private static function fill_labels( $labels, $name ) {
		foreach ( array( 'subtitle', 'setup_title', 'mailto_subject' ) as $key ) {
			if ( isset( $labels[ $key ] ) ) {
				$labels[ $key ] = sprintf( $labels[ $key ], $name );
			}
		}

		return $labels;
	}

	/**
	 * Which nudges the script may show on this page: the unfinished setup.
	 *
	 * @param Product  $product Product.
	 * @param Metadata $data Product metadata.
	 *
	 * @return array{setup: bool, forced?: 'setup'}
	 */
	public function nudges( $product, $data ) {
		$state  = self::user_state();
		$nudges = array( 'setup' => false );

		$forced = self::forced_nudge();
		if ( '' !== $forced ) {
			$nudges['forced']  = $forced;
			$nudges[ $forced ] = true;

			return $nudges;
		}

		if ( null !== $data['setup_complete'] ) {
			$old_enough = ( time() - (int) $product->get_install_time() ) >= self::SETUP_MIN_AGE;
			$rested     = self::rested( $state, 'setup', $product, self::SETUP_COOLDOWN );
			if ( $old_enough && $rested && ! call_user_func( $data['setup_complete'] ) ) {
				$nudges['setup'] = true;
			}
		}

		return $nudges;
	}

	/**
	 * The nudge an administrator forces for testing, or ''.
	 *
	 * Needs `themeisle_sdk_support_hub_debug` to return true and
	 * `?ti_support_nudge=setup` on the page. A forced nudge ignores the
	 * cooldown and the script never marks it as acted on.
	 *
	 * @return ''|'setup'
	 */
	public static function forced_nudge() {
		if ( ! apply_filters( 'themeisle_sdk_support_hub_debug', false ) || ! self::user_can_use() ) {
			return '';
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only debug switch, gated by the filter and the capability.
		$source = isset( $_GET[ self::DEBUG_QUERY ] ) ? sanitize_key( wp_unslash( $_GET[ self::DEBUG_QUERY ] ) ) : '';

		return 'setup' === $source ? 'setup' : '';
	}

	/**
	 * Whether this user last acted on a product's nudge long enough ago. The
	 * clock starts when the user dismisses the nudge or clicks "Get help",
	 * not when it appears, so an ignored nudge comes back.
	 *
	 * @param UserState $state    The user's state.
	 * @param string    $source   `setup`.
	 * @param Product   $product  Product.
	 * @param int       $cooldown Seconds to wait after an interaction.
	 *
	 * @return bool
	 */
	private static function rested( $state, $source, $product, $cooldown ) {
		$key = $product->get_key();
		if ( empty( $state[ $source ][ $key ] ) ) {
			return true;
		}

		return ( time() - (int) $state[ $source ][ $key ] ) >= $cooldown;
	}

	/**
	 * What this user already acted on: setup timestamps per product. Per
	 * user, not per site.
	 *
	 * @return UserState
	 */
	public static function user_state() {
		$state = get_user_meta( get_current_user_id(), self::USER_META, true );

		return is_array( $state ) ? $state : array();
	}

	/**
	 * Record that this user acted on a nudge: dismissed it or clicked "Get
	 * help". The cooldown is per product.
	 *
	 * @param 'setup' $source  Nudge source.
	 * @param Product $product The product whose nudge it was.
	 *
	 * @return void
	 */
	public static function mark( $source, $product ) {
		$state = self::user_state();
		if ( ! isset( $state[ $source ] ) || ! is_array( $state[ $source ] ) ) {
			$state[ $source ] = array();
		}
		$state[ $source ][ $product->get_key() ] = time();
		update_user_meta( get_current_user_id(), self::USER_META, $state );
	}

	/**
	 * The launcher root, and the deactivation windows on the Plugins and Add Themes screens.
	 *
	 * @return void
	 */
	public function render() {
		if ( self::$rendered || ! self::user_can_use() ) {
			return;
		}
		// Some products only fire themeisle_internal_page while their scripts
		// print, after admin_enqueue_scripts: the page was unknown when assets
		// were decided. Both are footer-safe, so enqueue them now.
		$this->enqueue();

		$current      = self::current();
		$deactivation = self::deactivation_products();
		if ( null === $current && empty( $deactivation ) ) {
			return;
		}
		self::$rendered = true;

		if ( null !== $current ) {
			echo '<div id="ti-support-hub" class="ti-support-hub"></div>';
		}
		foreach ( $deactivation as $entry ) {
			echo '<div class="ti-support-deactivate-root" data-ti-support-deactivate="' . esc_attr( $entry['product']->get_slug() ) . '" data-ti-support-type="' . esc_attr( $entry['product']->get_type() ) . '"></div>';
		}
	}

	/**
	 * Site information sent along with a message. Never keys, tokens or
	 * payment data.
	 *
	 * @param Product  $product Product.
	 * @param Metadata $data Product metadata.
	 *
	 * @return SiteInfo
	 */
	public static function site_info( $product, $data ) {
		global $wp_version;

		$theme   = wp_get_theme();
		$plugins = array();
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		foreach ( (array) get_option( 'active_plugins', array() ) as $file ) {
			$path = WP_PLUGIN_DIR . '/' . $file;
			if ( ! is_string( $file ) || ! is_readable( $path ) ) {
				continue;
			}
			$info      = get_plugin_data( $path, false, false );
			$plugins[] = array(
				'name'    => isset( $info['Name'] ) && '' !== $info['Name'] ? $info['Name'] : dirname( $file ),
				'version' => isset( $info['Version'] ) ? $info['Version'] : '',
			);
		}

		return array(
			'product'         => $product->get_slug(),
			'product_name'    => $product->get_friendly_name(),
			'product_version' => $product->get_version(),
			'plan'            => self::plan( $product, $data ),
			'license_status'  => self::license_status( $product ),
			'wp_version'      => (string) $wp_version,
			'php_version'     => PHP_VERSION,
			'locale'          => get_locale(),
			'multisite'       => is_multisite(),
			'theme'           => array(
				'name'    => $theme->get( 'Name' ),
				'version' => $theme->get( 'Version' ),
			),
			'active_plugins'  => $plugins,
			// The same compact crash summary the uninstall feedback carries:
			// the pending Crash_Reporter reports for this product, if any.
			'crashes'         => class_exists( 'ThemeisleSDK\Modules\Crash_Reporter' ) ? Crash_Reporter::get_uninstall_summary( $product ) : array(),
			'sdk_version'     => Loader::get_version(),
		);
	}

	/**
	 * The plan a message is tagged with: free, or pro with the price id when
	 * the Licenser knows it.
	 *
	 * The license is read from the product itself, or from the pro addon it
	 * names in its `Pro Slug` header when that addon is registered with the
	 * SDK (see licensed_product()).
	 *
	 * @param Product  $product Product.
	 * @param Metadata $data Product metadata.
	 *
	 * @return string
	 */
	public static function plan( $product, $data ) {
		if ( empty( $data['is_pro'] ) ) {
			return 'free';
		}
		$price_id = - 1;
		if ( class_exists( 'ThemeisleSDK\Modules\Licenser' ) ) {
			$price_id = Licenser::plan( self::licensed_product( $product )->get_basefile() );
		}

		return $price_id > 0 ? 'pro-' . $price_id : 'pro';
	}

	/**
	 * The license status for the site info, from the same product plan()
	 * reads. Empty when it has no Licenser.
	 *
	 * @param Product $product Product.
	 *
	 * @return string
	 */
	public static function license_status( $product ) {
		return (string) apply_filters( self::licensed_product( $product )->get_key() . '_license_status', '' );
	}

	/**
	 * The product whose Licenser holds the license: the product itself when
	 * it requires one, otherwise the registered product its `Pro Slug` header
	 * names (a free plugin whose pro addon owns the license). The product
	 * itself when neither applies.
	 *
	 * @param Product $product Product.
	 *
	 * @return Product
	 */
	public static function licensed_product( $product ) {
		if ( $product->requires_license() ) {
			return $product;
		}
		$pro_slug = $product->get_pro_slug();
		$products = Loader::get_products();
		if ( '' !== $pro_slug && isset( $products[ $pro_slug ] ) && $products[ $pro_slug ]->requires_license() ) {
			return $products[ $pro_slug ];
		}

		return $product;
	}

	/**
	 * Build the message payload.
	 *
	 * @param Product                  $product          Product.
	 * @param Metadata                 $data Product metadata.
	 * @param string                   $source           One of SOURCES.
	 * @param string                   $message          The user's message.
	 * @param string                   $email            Reply-to address.
	 * @param bool                     $include_site_info Whether to attach the site info.
	 * @param array<array-key, scalar> $context Technical context (error, reason, ...).
	 *
	 * @return Payload
	 */
	public static function build_payload( $product, $data, $source, $message, $email, $include_site_info, $context = array() ) {
		$payload = array(
			'product'      => $product->get_slug(),
			'product_name' => $product->get_friendly_name(),
			'version'      => $product->get_version(),
			'plan'         => self::plan( $product, $data ),
			'source'       => in_array( $source, self::SOURCES, true ) ? $source : 'custom',
			'message'      => $message,
			'email'        => $email,
			'site'         => get_site_url(),
			'context'      => $context,
			'site_info'    => $include_site_info ? self::site_info( $product, $data ) : null,
			'sdk_version'  => Loader::get_version(),
		);

		return apply_filters( 'themeisle_sdk_support_hub_payload', $payload, $product );
	}

	/**
	 * Deliver to the tracking API. An endpoint filtered to an empty value
	 * (QA sites, tests) makes it a dry run: the payload is logged and
	 * returned to the browser.
	 *
	 * @param Payload $payload Message payload.
	 *
	 * @return DeliveryResult|\WP_Error
	 */
	public static function deliver( $payload ) {
		$endpoint = apply_filters( 'themeisle_sdk_support_hub_endpoint', self::ENDPOINT );
		if ( empty( $endpoint ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[TISDK_SUPPORT] ' . wp_json_encode( $payload ) ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}

			return array(
				'sent'    => false,
				'dry_run' => true,
				'payload' => $payload,
			);
		}

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 10, //phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( $payload ),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return new \WP_Error( 'themeisle_support_hub_failed', Loader::$labels['support_hub']['error_send'], array( 'status' => $code ) );
		}

		return array(
			'sent'    => true,
			'dry_run' => false,
		);
	}

	/**
	 * Validate and send one message for a product.
	 *
	 * @param Product                  $product          Product.
	 * @param Metadata                 $data Product metadata.
	 * @param string                   $source           Source.
	 * @param string                   $message          Message.
	 * @param string                   $email            Reply-to.
	 * @param bool                     $include_site_info Whether to attach the site info.
	 * @param array<array-key, scalar> $context Technical context.
	 *
	 * @return DeliveryResult|\WP_Error
	 */
	public static function send( $product, $data, $source, $message, $email, $include_site_info, $context = array() ) {
		$labels  = Loader::$labels['support_hub'];
		$message = trim( (string) $message );
		if ( '' === $message ) {
			return new \WP_Error( 'themeisle_support_hub_empty', $labels['error_empty'] );
		}
		// Characters, not bytes: a multibyte message must not end in a broken character.
		if ( mb_strlen( $message, 'UTF-8' ) > self::MAX_MESSAGE_LENGTH ) {
			$message = mb_substr( $message, 0, self::MAX_MESSAGE_LENGTH, 'UTF-8' );
		}
		$email = trim( (string) $email );
		if ( '' === $email ) {
			$user  = wp_get_current_user();
			$email = $user ? $user->user_email : '';
		}
		// Validated, not sanitized: a sanitizer would rewrite a mistyped address instead of rejecting it.
		if ( ! is_email( $email ) ) {
			return new \WP_Error( 'themeisle_support_hub_email', $labels['error_email'] );
		}

		if ( ! self::claim_send_slot() ) {
			return new \WP_Error( 'themeisle_support_hub_throttled', $labels['error_throttled'] );
		}

		$payload = self::build_payload( $product, $data, $source, $message, $email, (bool) $include_site_info, $context );

		return self::deliver( $payload );
	}

	/**
	 * Claim a per-user send slot, including when an expired slot is contested.
	 *
	 * @return bool
	 */
	private static function claim_send_slot() {
		global $wpdb;

		$key     = 'ti_sdk_support_hub_' . get_current_user_id();
		$now     = time();
		$expires = $now + self::SEND_THROTTLE;

		// add_option() uses an upsert; the unique option name must reject a second insert.
		$claimed = 1 === $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- An atomic claim requires a unique insert.
			$wpdb->prepare(
				"INSERT IGNORE INTO $wpdb->options (option_name, option_value, autoload) VALUES (%s, %s, 'no')",
				$key,
				(string) $expires
			)
		);
		if ( ! $claimed ) {
			$claimed = 1 === $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Expiry must be checked in the same write that claims the slot.
				$wpdb->prepare(
					"UPDATE $wpdb->options SET option_value = %s WHERE option_name = %s AND CAST(option_value AS UNSIGNED) <= %d",
					(string) $expires,
					$key,
					$now
				)
			);
		}
		if ( $claimed ) {
			wp_cache_delete( $key, 'options' );
			wp_cache_delete( 'notoptions', 'options' );
		}

		return $claimed;
	}

	/**
	 * Refuse an AJAX call that is not ours to serve.
	 *
	 * @return void
	 */
	private function guard_ajax() {
		check_ajax_referer( self::NONCE, 'nonce' );
		if ( ! self::user_can_use() ) {
			wp_send_json_error( array( 'message' => Loader::$labels['support_hub']['error_permission'] ), 403 );
		}
	}

	/**
	 * The registered product a request talks about.
	 *
	 * @return RegisteredProduct|null
	 */
	private static function requested_product() {
		$key = isset( $_POST['product'] ) ? sanitize_key( wp_unslash( $_POST['product'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in guard_ajax().

		return isset( self::$registered[ $key ] ) ? self::$registered[ $key ] : null;
	}

	/**
	 * Send a message from the widget or the deactivation window.
	 *
	 * @return void
	 */
	public function ajax_send() {
		$this->guard_ajax();
		$entry = self::requested_product();
		if ( null === $entry ) {
			wp_send_json_error( array( 'message' => Loader::$labels['support_hub']['error_product'] ), 400 );
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Verified in guard_ajax().
		$source  = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'widget';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		// Kept as typed: sanitize_email() and sanitize_text_field() both turn a
		// mistyped address into a different valid one (john,doe@ → johndoe@),
		// which support would then reply to. send() rejects it with is_email().
		$email   = isset( $_POST['email'] ) ? trim( (string) wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated with is_email() in send() before any use.
		$include = ! isset( $_POST['include_site_info'] ) || in_array( (string) wp_unslash( $_POST['include_site_info'] ), array( '1', 'true', 'yes' ), true );
		$context = array();
		if ( isset( $_POST['context'] ) && is_string( $_POST['context'] ) ) {
			$decoded = json_decode( wp_unslash( $_POST['context'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Every decoded key and value goes through sanitize_context().
			if ( is_array( $decoded ) ) {
				$context = self::sanitize_context( $decoded );
			}
		}
		// phpcs:enable

		$result = self::send( $entry['product'], $entry['data'], $source, $message, $email, $include, $context );
		if ( is_wp_error( $result ) ) {
			$status = 'themeisle_support_hub_throttled' === $result->get_error_code() ? 429 : 500;
			if ( in_array( $result->get_error_code(), array( 'themeisle_support_hub_empty', 'themeisle_support_hub_email' ), true ) ) {
				$status = 400;
			}
			wp_send_json_error(
				array(
					'code'    => $result->get_error_code(),
					'message' => $result->get_error_message(),
				),
				$status
			);
		}

		wp_send_json_success( $result );
	}

	/**
	 * Keep the technical context flat, short and free of anything that is not
	 * a string or number.
	 *
	 * @param array<array-key, mixed> $context Raw context.
	 *
	 * @return array<array-key, scalar>
	 */
	public static function sanitize_context( $context ) {
		$clean = array();
		$count = 0;
		foreach ( $context as $key => $value ) {
			if ( ++ $count > 20 ) {
				break;
			}
			$key = sanitize_key( (string) $key );
			if ( '' === $key ) {
				continue;
			}
			if ( is_numeric( $value ) || is_bool( $value ) ) {
				$clean[ $key ] = $value;
			} elseif ( is_string( $value ) ) {
				$clean[ $key ] = mb_substr( sanitize_textarea_field( $value ), 0, 2000, 'UTF-8' );
			}
		}

		return $clean;
	}

	/**
	 * Remember that this user acted on a nudge: dismissed it or clicked Get help.
	 *
	 * @return void
	 */
	public function ajax_mark() {
		$this->guard_ajax();
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Verified in guard_ajax().
		$source = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : '';
		// phpcs:enable
		if ( 'setup' !== $source ) {
			wp_send_json_error( array( 'message' => 'Unknown source.' ), 400 );
		}
		$entry = self::requested_product();
		if ( null === $entry ) {
			wp_send_json_error( array( 'message' => Loader::$labels['support_hub']['error_product'] ), 400 );
		}
		self::mark( 'setup', $entry['product'] );
		wp_send_json_success();
	}

	/**
	 * Forget everything. Tests only.
	 *
	 * @return void
	 */
	public static function reset() {
		self::$registered     = array();
		self::$hooked         = false;
		self::$internal_pages = array();
		self::$owner          = null;
		self::$enqueued       = false;
		self::$rendered       = false;
	}
}
