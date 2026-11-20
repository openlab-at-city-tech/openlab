<?php
/**
 * The "Connect your AI agent" module for ThemeIsle SDK.
 *
 * Offers the Easy MCP connector to products that expose WordPress abilities:
 * a plugin-row link, a dismissable notice and one modal that enables the
 * connector and hands the user the deep links of their AI agent.
 *
 * Here's how to hook it in your product:
 *
 * add_filter( '<product_key>_ai_connect_metadata', 'add_ai_connect_meta' );
 *
 * function add_ai_connect_meta( $data ) {
 *  return [
 *       'name'         => <nice name>, // optional, defaults to the product's friendly name
 *       'notice_cases' => [ 'optimize new uploads', 'purge cached images', 'offload originals' ], // 2-3 short use cases
 *       'prompts'      => [ 'Show me my delivery settings ...', ... ], // ready-to-copy prompts
 *       'ability_prefix' => 'optimole', // optional, every registered "optimole/..." ability is switched on in Easy MCP on Enable, except the ones registered with meta 'ai_connect' => false
 *       'abilities'    => [ 'optimole/get-delivery-settings', ... ], // optional, explicit ability names to switch on as well
 *       'internal_slug' => 'mlo', // optional, the slug the product passes to themeisle_internal_page when it is not its install slug
 *  ]
 * }
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2026, Themeisle
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.3.62
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
 * AI Connect module for ThemeIsle SDK.
 */
class Ai_Connect extends Abstract_Module {
	const CONNECTOR_SLUG   = 'easy-mcp-ai';
	const CONNECTOR_FILE   = 'easy-mcp-ai/easy-mcp-ai.php';
	const CONNECTOR_ROUTE  = 'easy-mcp-ai/v1/mcp';
	const INTEGRATIONS_URL = 'https://easymcpai.com/integrations';
	const ABILITIES_OPTION = 'easy_mcp_ai_enabled_abilities';

	/**
	 * Easy MCP arms this one-shot transient on activation and its admin page
	 * redirects to the setup wizard once it is found (Setup_State::REDIRECT_TRANSIENT).
	 */
	const CONNECTOR_REDIRECT_TRANSIENT = 'easy_mcp_ai_setup_redirect';

	const AJAX_ENABLE   = 'themeisle_sdk_ai_connect_enable';
	const AJAX_DISMISS  = 'themeisle_sdk_ai_connect_dismiss';
	const NONCE         = 'themeisle_sdk_ai_connect';
	const DISMISSED_KEY = 'themeisle_sdk_ai_connect_dismissed';

	/**
	 * After a dismissal nothing is shown for this long; then the next product
	 * the user has not dismissed gets its turn.
	 */
	const REMIND_AFTER = 30 * DAY_IN_SECONDS;

	/**
	 * A user is asked this many times at most, however many products opted in.
	 */
	const MAX_DISMISSALS = 3;

	/**
	 * The notice waits this long after the product was installed.
	 */
	const MINIMUM_INSTALL_AGE = DAY_IN_SECONDS;

	/**
	 * And this long after the module first ran on the site, so an update does
	 * not greet a long-time user with it.
	 */
	const MINIMUM_SINCE = 2 * DAY_IN_SECONDS;

	/**
	 * When the module first ran on this site.
	 */
	const SINCE_OPTION = 'themeisle_sdk_ai_connect_since';


	/**
	 * Every product that opted in, keyed by product key. Shared by all the
	 * instances so one screen renders ONE notice and ONE modal, however many
	 * Themeisle products the site runs.
	 *
	 * @var array<string, array{product: Product, data: array}>
	 */
	private static $registered = array();

	/**
	 * Whether the shared hooks were added already.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * Slug of the product whose internal page is being displayed, if any.
	 *
	 * @var string
	 */
	private static $internal_product = '';

	/**
	 * Whether this request is the Customizer controls screen.
	 *
	 * @var bool
	 */
	private static $in_customizer = false;

	/**
	 * Whether this request is the Site Editor (block themes edit their site
	 * there, never in the Customizer).
	 *
	 * @var bool
	 */
	private static $in_site_editor = false;

	/**
	 * Whether the assets were enqueued on this request.
	 *
	 * @var bool
	 */
	private static $enqueued = false;

	/**
	 * The product picked for this screen.
	 *
	 * @var array|null
	 */
	private static $current = null;

	/**
	 * This product's metadata, received from the filter.
	 *
	 * @var array
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
		if ( ! is_admin() ) {
			return false;
		}
		// With the connector already running there is nothing left to offer: no
		// notice, no plugin-row link, no modal.
		if ( $this->is_connector_active() ) {
			return false;
		}

		$this->data = self::sanitize_metadata( apply_filters( $product->get_key() . '_ai_connect_metadata', array() ), $product );

		return ! empty( $this->data );
	}

	/**
	 * Keep only what the UI can use. A product that sends no prompt and no use
	 * case has nothing to say, and is treated as not opted in.
	 *
	 * @param mixed   $data Raw filter result.
	 * @param Product $product Product object.
	 *
	 * @return array
	 */
	public static function sanitize_metadata( $data, $product ) {
		if ( ! is_array( $data ) ) {
			return array();
		}
		$strings = function ( $list, $max ) {
			$list = is_array( $list ) ? $list : array();
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

		$cases   = $strings( isset( $data['notice_cases'] ) ? $data['notice_cases'] : array(), 3 );
		$prompts = $strings( isset( $data['prompts'] ) ? $data['prompts'] : array(), 5 );
		if ( empty( $cases ) || empty( $prompts ) ) {
			return array();
		}

		$abilities = array();
		foreach ( $strings( isset( $data['abilities'] ) ? $data['abilities'] : array(), 100 ) as $ability ) {
			if ( preg_match( '#^[a-z0-9\-]+/[a-z0-9\-/]+$#', $ability ) ) {
				$abilities[] = $ability;
			}
		}

		$prefixes = array();
		$raw      = isset( $data['ability_prefix'] ) ? $data['ability_prefix'] : array();
		foreach ( $strings( is_string( $raw ) ? array( $raw ) : $raw, 5 ) as $prefix ) {
			if ( preg_match( '#^[a-z0-9\-]+$#', $prefix ) ) {
				$prefixes[] = $prefix;
			}
		}

		$name = isset( $data['name'] ) && is_string( $data['name'] ) && '' !== trim( $data['name'] ) ? trim( wp_strip_all_tags( $data['name'] ) ) : $product->get_friendly_name();

		$internal_slug = isset( $data['internal_slug'] ) && is_string( $data['internal_slug'] ) ? sanitize_key( $data['internal_slug'] ) : '';

		return array(
			'name'          => $name,
			'notice_cases'  => $cases,
			'prompts'       => $prompts,
			'abilities'     => $abilities,
			'prefixes'      => $prefixes,
			'internal_slug' => '' !== $internal_slug ? $internal_slug : $product->get_slug(),
		);
	}

	/**
	 * Registers the hooks.
	 *
	 * @param Product $product Product to load.
	 *
	 * @return Ai_Connect
	 */
	public function load( $product ) {
		$this->product = $product;

		self::$registered[ $product->get_key() ] = array(
			'product' => $product,
			'data'    => $this->data,
		);

		if ( $product->is_plugin() ) {
			add_filter( 'plugin_row_meta', array( $this, 'add_row_meta' ), 10, 2 );
		}

		if ( self::$hooked ) {
			return $this;
		}
		self::$hooked = true;

		if ( ! get_option( self::SINCE_OPTION ) ) {
			add_option( self::SINCE_OPTION, time(), '', false );
		}

		add_action( 'themeisle_internal_page', array( __CLASS__, 'mark_internal_page' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'render_notice' ) );
		add_action( 'in_admin_header', array( $this, 'ensure_notice_hook' ), PHP_INT_MAX );
		// Late: after the products had their chance to fire themeisle_internal_page.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 999 );
		// admin_footer runs BEFORE admin_print_footer_scripts, so the modal is in
		// the DOM when the script initialises.
		add_action( 'admin_footer', array( $this, 'render_modal' ) );
		// Themes: the Customizer has no admin notices, so the same invitation
		// goes into its notification area (see ai-connect.js).
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer' ), 999 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'render_modal' ), 1 );
		add_action( 'wp_ajax_' . self::AJAX_ENABLE, array( $this, 'ajax_enable' ) );
		add_action( 'wp_ajax_' . self::AJAX_DISMISS, array( $this, 'ajax_dismiss' ) );

		return $this;
	}

	/**
	 * Remember which product's internal page this is.
	 *
	 * @param string $product_slug Product slug.
	 * @param string $page_slug Page slug.
	 *
	 * @return void
	 */
	public static function mark_internal_page( $product_slug, $page_slug = '' ) {
		if ( ! is_string( $product_slug ) || '' !== self::$internal_product ) {
			return;
		}
		// Some products pass their plugin basename ("dir/file.php") instead of
		// the slug; the directory is the slug either way.
		if ( false !== strpos( $product_slug, '/' ) ) {
			$product_slug = dirname( $product_slug );
		}
		self::$internal_product = $product_slug;
	}

	/**
	 * Some products clear every admin_notices callback on their own screens
	 * (remove_all_actions on load-{$hook}), which takes ours with it. Put it
	 * back just before the notices are printed.
	 *
	 * @return void
	 */
	public function ensure_notice_hook() {
		if ( false === has_action( 'admin_notices', array( $this, 'render_notice' ) ) ) {
			add_action( 'admin_notices', array( $this, 'render_notice' ) );
		}
		// Before anything prints: the review/opt-in queue picks its notice at
		// admin_notices and reads this timestamp, so it yields to us today.
		if ( $this->should_show_notice() ) {
			self::mark_seen();
		}
	}

	/**
	 * Did any product on this site opt in? The Promotions module asks, so the
	 * site gets one invitation to the connector and not two.
	 *
	 * @return bool
	 */
	public static function has_products() {
		return ! empty( self::$registered );
	}

	/**
	 * Was the SDK's own Easy MCP promotion dismissed on this site? It is the
	 * same invitation, so the answer counts here too.
	 *
	 * @return bool
	 */
	public static function is_promotion_dismissed() {
		$stored = json_decode( (string) get_option( 'themeisle_sdk_promotions', '{}' ), true );
		if ( ! is_array( $stored ) ) {
			return false;
		}

		return ! empty( $stored['easy-mcp-plugins-install'] ) || ! empty( $stored['easy-mcp-profile'] );
	}

	/**
	 * Only users who may install AND activate plugins can do what the modal
	 * offers, so nobody else sees any of it.
	 *
	 * @return bool
	 */
	public static function user_can_connect() {
		return current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' );
	}

	/**
	 * Whether this is the plugins list screen.
	 *
	 * @return bool
	 */
	private static function is_plugins_screen() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		return $screen && 'plugins' === $screen->id;
	}

	/**
	 * Whether this is the WordPress Dashboard (index.php).
	 *
	 * @return bool
	 */
	private static function is_dashboard_screen() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		return $screen && 'dashboard' === $screen->id;
	}

	/**
	 * Is this one of the theme surfaces: the themes list, the Customizer or
	 * the Site Editor?
	 *
	 * @return bool
	 */
	private static function is_theme_surface() {
		if ( self::$in_customizer || self::is_site_editor() ) {
			return true;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		return $screen && 'themes' === $screen->id;
	}

	/**
	 * Whether this is the Site Editor (site-editor.php). Its admin notices are
	 * printed behind the full-screen editor, so the invitation goes through the
	 * editor's own notices store instead (see ai-connect.js).
	 *
	 * @return bool
	 */
	private static function is_site_editor() {
		if ( self::$in_site_editor ) {
			return true;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'site-editor' === $screen->id ) {
			self::$in_site_editor = true;
		}

		return self::$in_site_editor;
	}

	/**
	 * The product this screen talks about: the owner of the internal page, on
	 * the plugins list the opted-in plugin that was installed first, on the
	 * themes list and in the Customizer the opted-in theme.
	 *
	 * @return array|null { product, data }
	 */
	private static function current() {
		// Not cached: products fire themeisle_internal_page from their own
		// admin_enqueue_scripts callbacks, so an early answer would be a wrong one.
		self::$current = null;

		if ( '' !== self::$internal_product ) {
			$editor = self::$in_customizer || self::is_site_editor();
			foreach ( self::$registered as $entry ) {
				if ( $entry['product']->get_slug() !== self::$internal_product && $entry['data']['internal_slug'] !== self::$internal_product ) {
					continue;
				}
				// A plugin that counts the editor as its own page (Otter fires
				// themeisle_internal_page from the block editor) must not take
				// the Customizer or the Site Editor away from the theme.
				if ( $editor && ! $entry['product']->is_theme() ) {
					break;
				}
				self::$current = $entry;
				return self::$current;
			}
			if ( ! $editor ) {
				return self::$current;
			}
		}

		$theme_surface = self::is_theme_surface();
		if ( ! $theme_surface && ! self::is_plugins_screen() ) {
			return null;
		}

		$oldest = PHP_INT_MAX;
		foreach ( self::$registered as $entry ) {
			if ( $entry['product']->is_theme() !== $theme_surface || self::is_product_dismissed( $entry['product'] ) ) {
				continue;
			}
			$installed = (int) $entry['product']->get_install_time();
			if ( $installed < $oldest ) {
				$oldest        = $installed;
				self::$current = $entry;
			}
		}

		return self::$current;
	}

	/**
	 * Is the connector plugin active?
	 *
	 * @return bool
	 */
	public function is_connector_active() {
		return $this->is_plugin_active( self::CONNECTOR_SLUG );
	}

	/**
	 * Is the notice resting for this user? True for REMIND_AFTER after a
	 * dismissal, and for good after MAX_DISMISSALS. Per user, not per site:
	 * another administrator has not seen it yet.
	 *
	 * @return bool
	 */
	public static function is_dismissed() {
		$dismissals = self::dismissals();
		if ( empty( $dismissals ) ) {
			return false;
		}

		return count( $dismissals ) >= self::MAX_DISMISSALS || ( time() - max( $dismissals ) ) < self::REMIND_AFTER;
	}

	/**
	 * What this user dismissed: product key => time. Per user, not per site.
	 *
	 * @return array<string, int>
	 */
	public static function dismissals() {
		$stored = get_user_meta( get_current_user_id(), self::DISMISSED_KEY, true );
		if ( empty( $stored ) ) {
			return array();
		}
		if ( ! is_array( $stored ) ) {
			// A bare time, from before dismissals were kept per product.
			return array( '_' => (int) $stored );
		}

		return array_map( 'intval', $stored );
	}

	/**
	 * Has this user dismissed this product's notice?
	 *
	 * @param Product $product Product.
	 *
	 * @return bool
	 */
	private static function is_product_dismissed( $product ) {
		return array_key_exists( $product->get_key(), self::dismissals() );
	}

	/**
	 * Whether the notice shows on this request.
	 *
	 * @return bool
	 */
	public function should_show_notice() {
		if ( ! self::user_can_connect() || self::is_dismissed() || self::is_promotion_dismissed() ) {
			return false;
		}
		$current = self::current();
		// A product's own page only ever talks about that product.
		if ( null === $current || self::is_product_dismissed( $current['product'] ) ) {
			return false;
		}
		if ( ( time() - (int) get_option( self::SINCE_OPTION, time() ) ) < self::MINIMUM_SINCE ) {
			return false;
		}
		// The sale is the one SDK notice that goes first; every other one waits for us.
		if ( apply_filters( 'themeisle_sdk_is_black_friday_sale', false ) ) {
			return false;
		}
		return ( time() - (int) $current['product']->get_install_time() ) >= self::MINIMUM_INSTALL_AGE;
	}

	/**
	 * Tell the other SDK notices we were here, so they keep their distance:
	 * the review/opt-in queue waits its usual days after us, and the promotions
	 * their usual weeks after a dismissal. Ours goes first: it is marked before
	 * the notices print, so on the same page view the queue already yields.
	 *
	 * @param bool $dismissed Whether the user dismissed us, or just saw us.
	 *
	 * @return void
	 */
	public static function mark_seen( $dismissed = false ) {
		$notifications = get_option( 'themeisle_sdk_notifications', array() );
		$notifications = is_array( $notifications ) ? $notifications : array();
		$last_active   = isset( $notifications['last_notification_active'] ) ? (int) $notifications['last_notification_active'] : 0;
		// Once a day is enough: this runs on every page view that shows the notice.
		if ( $dismissed || ( time() - $last_active ) > DAY_IN_SECONDS ) {
			$notifications['last_notification_active'] = time();
			update_option( 'themeisle_sdk_notifications', $notifications );
		}
		if ( $dismissed ) {
			$promotions               = json_decode( (string) get_option( 'themeisle_sdk_promotions', '{}' ), true );
			$promotions               = is_array( $promotions ) ? $promotions : array();
			$promotions['ai-connect'] = time();
			update_option( 'themeisle_sdk_promotions', wp_json_encode( $promotions ) );
		}
	}

	/**
	 * Plugin-row link, next to "View details".
	 *
	 * @param string[] $links Row meta links.
	 * @param string   $file Plugin base file.
	 *
	 * @return string[]
	 */
	public function add_row_meta( $links, $file ) {
		if ( plugin_basename( $this->product->get_basefile() ) !== $file || ! self::user_can_connect() ) {
			return $links;
		}

		$links[] = '<a href="#" class="ti-ai-connect-open" data-ti-ai-connect="' . esc_attr( $this->product->get_key() ) . '">'
			. self::sparkle()
			. esc_html( Loader::$labels['ai_connect']['row_link'] ) . '</a>';

		return $links;
	}

	/**
	 * The notice.
	 *
	 * @return void
	 */
	public function render_notice() {
		// The Site Editor prints admin notices behind its full-screen app; the
		// invitation reaches it through the editor's notices store instead.
		if ( self::is_site_editor() || ! $this->should_show_notice() ) {
			return;
		}
		// Some products only fire themeisle_internal_page while their scripts
		// print, after admin_enqueue_scripts: the page was unknown when assets
		// were decided. Both are footer-safe, so enqueue them now.
		$this->enqueue();

		$current = self::current();
		$labels  = Loader::$labels['ai_connect'];
		$do      = self::join_cases( $current['data']['notice_cases'] );
		?>
		<?php if ( '' !== self::$internal_product && ! self::is_dashboard_screen() ) : ?>
			<?php // The product's own page: every other notice steps aside while ours shows. Not on the WP Dashboard, which some themes claim as their page but which belongs to everyone. ?>
			<style>.notice:not(.ti-ai-notice):not(.themeisle-sdk-license-notice), .updated:not(.ti-ai-notice), .update-nag { display: none !important; }</style>
		<?php else : ?>
			<?php // Shared screens: one SDK voice at a time. Only the SDK's own review, opt-in, promotion and sale notices step aside. ?>
			<style>.themeisle-sdk-notice, .ti-sdk-om-notice, .ti-sdk-rop-notice, .themeisle-sale { display: none !important; }</style>
		<?php endif; ?>
		<div class="notice notice-info is-dismissible ti-ai-notice" data-ti-ai-notice>
			<div class="ti-ai-notice-inner">
				<p>
					<strong><?php echo esc_html( sprintf( $labels['notice_title'], $current['data']['name'] ) ); ?></strong>
					<?php echo esc_html( sprintf( $labels['notice_text'], $do ) ); ?>
				</p>
				<a href="#" class="button ti-ai-connect-open" data-ti-ai-connect="<?php echo esc_attr( $current['product']->get_key() ); ?>"><?php echo self::sparkle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><?php echo esc_html( $labels['notice_button'] ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * "a, b or c".
	 *
	 * @param string[] $cases Use cases.
	 *
	 * @return string
	 */
	private static function join_cases( $cases ) {
		$last = array_pop( $cases );

		return empty( $cases ) ? (string) $last : sprintf( Loader::$labels['ai_connect']['cases_join'], implode( ', ', $cases ), $last );
	}

	/**
	 * Whether anything of ours is on this screen.
	 *
	 * @return bool
	 */
	private function is_relevant_screen() {
		return self::user_can_connect() && ( ( ! self::$in_customizer && self::is_plugins_screen() ) || $this->should_show_notice() );
	}

	/**
	 * Customizer controls: same assets and modal, when the notice is due.
	 *
	 * @return void
	 */
	public function enqueue_customizer() {
		self::$in_customizer = true;
		$this->enqueue();
	}

	/**
	 * Assets, only where the link or the notice can be.
	 *
	 * @return void
	 */
	public function enqueue() {
		// The Customizer fires admin_enqueue_scripts too (WP_Customize_Widgets
		// does, from inside customize_controls_enqueue_scripts), so this can run
		// before enqueue_customizer(): recognise the screen either way.
		if ( did_action( 'customize_controls_init' ) ) {
			self::$in_customizer = true;
		}
		if ( self::$enqueued || ! $this->is_relevant_screen() ) {
			return;
		}
		self::$enqueued = true;

		$handle = 'themeisle-sdk-ai-connect';
		$base   = $this->get_sdk_uri() . 'assets/js/build/ai_connect/';
		$asset  = dirname( dirname( __DIR__ ) ) . '/assets/js/build/ai_connect/ai-connect.asset.php';
		$meta   = is_readable( $asset ) ? require $asset : array(
			'dependencies' => array(),
			'version'      => Loader::get_version(),
		);

		wp_enqueue_style( $handle, $base . 'ai-connect.css', array(), $meta['version'] );
		wp_enqueue_script( $handle, $base . 'ai-connect.js', $meta['dependencies'], $meta['version'], true );

		$endpoint = rest_url( self::CONNECTOR_ROUTE );
		$products = array();
		foreach ( self::$registered as $key => $entry ) {
			$products[ $key ] = array(
				'name'    => $entry['data']['name'],
				'prompts' => $entry['data']['prompts'],
			);
		}
		$current = self::current();
		$labels  = Loader::$labels['ai_connect'];
		// The button says what will happen: install and activate, or just activate.
		$labels['enable']   = $this->enable_label();
		$labels['enabling'] = $this->is_plugin_installed( self::CONNECTOR_SLUG ) ? $labels['activating'] : $labels['enabling'];
		$pitch              = array();
		$surface            = '';
		// The Customizer and the Site Editor have no usable admin notices: the
		// script delivers the invitation through their own notification APIs.
		if ( self::$in_customizer ) {
			$surface = 'customizer';
		} elseif ( self::is_site_editor() ) {
			$surface = 'site_editor';
		}
		if ( '' !== $surface && null !== $current && $this->should_show_notice() ) {
			self::mark_seen();
			$pitch = array(
				'title' => sprintf( $labels['notice_title'], $current['data']['name'] ),
				'text'  => sprintf( $labels['notice_text'], self::join_cases( $current['data']['notice_cases'] ) ),
				'icon'  => self::sparkle(),
			);
		}

		wp_localize_script(
			$handle,
			'themeisleSDKAiConnect',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::NONCE ),
				'actions'  => array(
					'enable'  => self::AJAX_ENABLE,
					'dismiss' => self::AJAX_DISMISS,
				),
				'endpoint' => $endpoint,
				'links'    => self::connect_links( $endpoint ),
				'products' => $products,
				'current'  => $current ? $current['product']->get_key() : ( empty( $products ) ? '' : key( $products ) ),
				'labels'   => $labels,
				'pitch'    => $pitch,
				'surface'  => $surface,
			)
		);
	}

	/**
	 * One-click deep links. They mirror Easy MCP's own
	 * Admin_Page::build_connect_url() so they can be offered before it runs.
	 * ChatGPT has no link to its form: its link opens the Plugins page, where
	 * the user adds the server and pastes the URL. Cursor's link wants
	 * its `config` as Base64 JSON; the script appends it (window.btoa), because
	 * PHP's Base64 function is refused by the WordPress.org plugin check in
	 * every product that bundles the SDK, whatever the reason for the call.
	 *
	 * @param string $endpoint The site's MCP URL.
	 *
	 * @return array<string, string>
	 */
	public static function connect_links( $endpoint ) {
		// get_bloginfo() returns the name escaped for display; a URL needs the real one.
		$title = trim( wp_specialchars_decode( (string) get_bloginfo( 'name' ), ENT_QUOTES ) );
		$name  = '' !== $title ? $title . ' WordPress' : 'WordPress';

		return array(
			'claude'  => 'https://claude.ai/customize/connectors?modal=add-custom-connector&connectorName=' . rawurlencode( $name ) . '&connectorUrl=' . rawurlencode( $endpoint ),
			'chatgpt' => 'https://chatgpt.com/plugins',
			'cursor'  => 'cursor://anysphere.cursor-deeplink/mcp/install?name=' . rawurlencode( $name ),
		);
	}

	/**
	 * The modal. Printed once, whatever the number of opted-in products.
	 *
	 * @return void
	 */
	public function render_modal() {
		if ( ! $this->is_relevant_screen() ) {
			return;
		}
		$labels = Loader::$labels['ai_connect'];
		?>
		<div id="ti-ai-connect" class="ti-ai-modal" hidden>
			<div class="ti-ai-backdrop" data-close></div>
			<div class="ti-ai-dialog" role="dialog" aria-modal="true" aria-labelledby="ti-ai-title" data-enabled="0" tabindex="-1">
				<button type="button" class="ti-ai-close" data-close aria-label="<?php echo esc_attr( $labels['close'] ); ?>"><span class="dashicons dashicons-no-alt"></span></button>

				<p class="ti-ai-eyebrow"><?php echo self::sparkle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><?php echo esc_html( $labels['eyebrow'] ); ?></p>
				<h2 id="ti-ai-title" data-title></h2>
				<p class="ti-ai-lead" data-lead></p>

				<div class="ti-ai-enable">
					<button type="button" class="button button-primary button-hero" data-enable><?php echo esc_html( $this->enable_label() ); ?></button>
					<p class="ti-ai-hint ti-ai-when-off" data-note></p>
					<span class="ti-ai-enabled-badge"><span class="dashicons dashicons-yes-alt"></span><?php echo esc_html( $labels['enabled'] ); ?></span>
					<p class="ti-ai-error" data-error role="alert" hidden></p>
				</div>

				<label class="ti-ai-label" for="ti-ai-endpoint"><?php echo esc_html( $labels['url_label'] ); ?></label>
				<div class="ti-ai-copyrow">
					<input id="ti-ai-endpoint" type="text" class="regular-text code" readonly data-endpoint value="">
					<button type="button" class="button" data-copy="#ti-ai-endpoint"><?php echo esc_html( $labels['copy'] ); ?></button>
				</div>

				<h3><?php echo esc_html( $labels['connect_heading'] ); ?></h3>
				<div class="ti-ai-clients">
					<a class="button ti-ai-client" data-client="claude" target="_blank" rel="noopener noreferrer" href="#"><?php echo esc_html( $labels['connect_claude'] ); ?></a>
					<a class="button ti-ai-client" data-client="chatgpt" target="_blank" rel="noopener noreferrer" href="#"><?php echo esc_html( $labels['connect_chatgpt'] ); ?></a>
					<a class="button ti-ai-client" data-client="cursor" href="#"><?php echo esc_html( $labels['connect_cursor'] ); ?></a>
				</div>
				<p class="ti-ai-hint ti-ai-when-off"><?php echo esc_html( $labels['hint_off'] ); ?> <?php echo esc_html( $labels['more_agents'] ); ?> <a class="ti-ai-more" href="<?php echo esc_url( self::INTEGRATIONS_URL ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $labels['more_agents_link'] ); ?></a></p>
				<p class="ti-ai-hint ti-ai-when-on"><?php echo esc_html( $labels['hint_on'] ); ?> <?php echo esc_html( $labels['more_agents'] ); ?> <a class="ti-ai-more" href="<?php echo esc_url( self::INTEGRATIONS_URL ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $labels['more_agents_link'] ); ?></a></p>

				<h3 class="ti-ai-when-off"><?php echo esc_html( $labels['prompts_off'] ); ?></h3>
				<h3 class="ti-ai-when-on"><?php echo esc_html( $labels['prompts_on'] ); ?></h3>
				<ul class="ti-ai-prompts" data-prompts></ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Refuse an AJAX call that is not ours to serve.
	 *
	 * @return void
	 */
	private function guard_ajax() {
		check_ajax_referer( self::NONCE, 'nonce' );
		if ( ! self::user_can_connect() ) {
			wp_send_json_error( array( 'message' => Loader::$labels['ai_connect']['error_permission'] ), 403 );
		}
	}

	/**
	 * Dismiss the notice for this user.
	 *
	 * @return void
	 */
	public function ajax_dismiss() {
		$this->guard_ajax();
		$key = isset( $_POST['product'] ) ? sanitize_key( wp_unslash( $_POST['product'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in guard_ajax().
		if ( ! isset( self::$registered[ $key ] ) ) {
			$key = '_';
		}
		$dismissals         = self::dismissals();
		$dismissals[ $key ] = time();
		update_user_meta( get_current_user_id(), self::DISMISSED_KEY, $dismissals );
		self::mark_seen( true );
		wp_send_json_success();
	}

	/**
	 * Install (when missing) and activate the connector, then switch the
	 * product's abilities on in it.
	 *
	 * @return void
	 */
	public function ajax_enable() {
		$this->guard_ajax();

		$key   = isset( $_POST['product'] ) ? sanitize_key( wp_unslash( $_POST['product'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in guard_ajax().
		$entry = isset( self::$registered[ $key ] ) ? self::$registered[ $key ] : null;

		$result = $this->install_and_activate();
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 500 );
		}

		if ( null !== $entry ) {
			self::enable_abilities( array_merge( $entry['data']['abilities'], self::abilities_with_prefix( $entry['data']['prefixes'] ) ) );
		}

		$endpoint = rest_url( self::CONNECTOR_ROUTE );
		wp_send_json_success(
			array(
				'endpoint' => $endpoint,
				'links'    => self::connect_links( $endpoint ),
			)
		);
	}

	/**
	 * Install from WordPress.org when missing, then activate. Both steps are
	 * skipped when already done, so calling it twice is harmless.
	 *
	 * @return true|\WP_Error
	 */
	public function install_and_activate() {
		if ( $this->is_connector_active() ) {
			return true;
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! $this->is_plugin_installed( self::CONNECTOR_SLUG ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => self::CONNECTOR_SLUG,
					'fields' => array( 'sections' => false ),
				)
			);
			if ( is_wp_error( $api ) ) {
				return $api;
			}
			if ( ! is_object( $api ) || empty( $api->download_link ) ) {
				return new \WP_Error( 'themeisle_ai_connect_install_failed', Loader::$labels['ai_connect']['error_install'] );
			}

			$upgrader  = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
			$installed = $upgrader->install( $api->download_link );
			if ( is_wp_error( $installed ) ) {
				return $installed;
			}
			if ( true !== $installed ) {
				$errors = $upgrader->skin->get_errors();
				return is_wp_error( $errors ) && $errors->has_errors() ? $errors : new \WP_Error( 'themeisle_ai_connect_install_failed', Loader::$labels['ai_connect']['error_install'] );
			}
			wp_clean_plugins_cache();
		}

		$activated = activate_plugin( self::CONNECTOR_FILE );
		if ( is_wp_error( $activated ) ) {
			return $activated;
		}
		self::disarm_connector_setup_redirect();

		return true;
	}

	/**
	 * The person is in the modal, about to copy the MCP URL and connect an
	 * agent: Easy MCP's setup-wizard redirect, armed by its activation hook for
	 * the next admin page load, would take them away from that. Disarm it; the
	 * wizard is still offered on Easy MCP's own page.
	 *
	 * @return void
	 */
	public static function disarm_connector_setup_redirect() {
		delete_transient( self::CONNECTOR_REDIRECT_TRANSIENT );
	}

	/**
	 * Switch the product's abilities on in the connector, keeping whatever the
	 * site owner enabled before. Only names that are really registered are
	 * added, and nothing is ever removed.
	 *
	 * @param string[] $abilities Ability names declared by the product.
	 *
	 * @return string[] The names that were added.
	 */
	public static function enable_abilities( $abilities ) {
		if ( empty( $abilities ) || ! current_user_can( 'manage_options' ) ) {
			return array();
		}
		// A name can come from both the explicit list and the prefix.
		$abilities = array_values( array_unique( array_filter( $abilities, 'is_string' ) ) );
		if ( function_exists( 'wp_get_abilities' ) ) {
			$abilities = array_values( array_intersect( $abilities, array_keys( (array) wp_get_abilities() ) ) );
		}

		$enabled = get_option( self::ABILITIES_OPTION, array() );
		$enabled = is_array( $enabled ) ? $enabled : array();
		$added   = array_values( array_diff( $abilities, $enabled ) );
		if ( ! empty( $added ) ) {
			update_option( self::ABILITIES_OPTION, array_values( array_merge( $enabled, $added ) ) );
		}

		return $added;
	}

	/**
	 * The registered abilities under the given name prefixes, minus the ones
	 * the product registered with meta 'ai_connect' => false.
	 *
	 * @param string[] $prefixes Ability name prefixes, without the slash.
	 *
	 * @return string[]
	 */
	public static function abilities_with_prefix( $prefixes ) {
		if ( empty( $prefixes ) || ! function_exists( 'wp_get_abilities' ) ) {
			return array();
		}
		$names = array();
		foreach ( (array) wp_get_abilities() as $name => $ability ) {
			if ( ! in_array( strtok( (string) $name, '/' ), $prefixes, true ) ) {
				continue;
			}
			if ( is_object( $ability ) && method_exists( $ability, 'get_meta_item' ) && false === $ability->get_meta_item( 'ai_connect', true ) ) {
				continue;
			}
			$names[] = (string) $name;
		}
		return $names;
	}

	/**
	 * The Enable button's label: the plugin is installed and only needs
	 * activating, or it is fetched from WordPress.org first.
	 *
	 * @return string
	 */
	private function enable_label() {
		$labels = Loader::$labels['ai_connect'];

		return $this->is_plugin_installed( self::CONNECTOR_SLUG ) ? $labels['enable_installed'] : $labels['enable'];
	}

	/**
	 * The sparkle icon. Dashicons has no AI icon.
	 *
	 * @return string
	 */
	public static function sparkle() {
		return '<svg class="ti-ai-sparkle" width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" focusable="false">'
			. '<path d="M10 2l1.6 4.4L16 8l-4.4 1.6L10 14l-1.6-4.4L4 8l4.4-1.6L10 2z"/><path d="M16 12l.8 2.2L19 15l-2.2.8L16 18l-.8-2.2L13 15l2.2-.8L16 12z"/>'
			. '<path d="M4 12l.6 1.6L6.2 14.2l-1.6.6L4 16.4l-.6-1.6-1.6-.6 1.6-.6L4 12z"/></svg>';
	}

	/**
	 * Forget everything. Tests only.
	 *
	 * @return void
	 */
	public static function reset() {
		self::$registered       = array();
		self::$hooked           = false;
		self::$internal_product = '';
		self::$in_customizer    = false;
		self::$in_site_editor   = false;
		delete_option( self::SINCE_OPTION );
		self::$current  = null;
		self::$enqueued = false;
	}
}
