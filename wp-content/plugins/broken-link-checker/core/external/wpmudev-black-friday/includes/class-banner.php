<?php
/**
 * WPMUDEV Black Friday Banner class
 *
 * Handles the display and rendering of Black Friday promotional banners.
 *
 * @since   2.0.0
 * @author  WPMUDEV
 * @package WPMUDEV\Modules\BlackFriday
 */

namespace WPMUDEV\Modules\BlackFriday;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Banner
 *
 * Manages Black Friday banner display and interactions.
 *
 * @since 2.0.0
 */
class Banner {

	/**
	 * Banner version.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $version = '2.1.0';

	/**
	 * Campaign URL.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $campaign_url = '';

	/**
	 * Campaign UTM link.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $utm_campaign = 'BF-Plugins-2026-banner';

	/**
	 * Option name to store data.
	 *
	 * @since 1.0
	 * @var string $option_name
	 */
	protected $option_name = 'wpmudev_black_friday_2026_flag';

	/**
	 * DOM element ID for the banner.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $dom_element_id = '';

	/**
	 * Script version.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $script_version;

	/**
	 * Nonce.
	 *
	 * @since 1.0
	 * @var string $nonce
	 */
	protected $nonce = 'wpmudev-bf-common';

	/**
	 * UTM content for the banner.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $utm_content = 'BF-Plugins-2026';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. Banner configuration arguments.
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'campaign_url' => 'https://wpmudev.com/black-friday/',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->campaign_url   = esc_url( $args['campaign_url'] );
		$this->script_version = ! empty( Utils::script_data( 'banner', 'version' ) ) ? Utils::script_data( 'banner', 'version' ) : $this->version;
		$this->dom_element_id = 'wpmudev-bf-banner-' . $this->script_version;

		$this->set_actions();
	}

	/**
	 * Set the wp hooks-actions for module Banner (The admin notice).
	 *
	 * @return void
	 */
	protected function set_actions() {
		// Enqueue scripts and styles for the banner.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// Render the banner in admin notices.
		add_action( 'admin_notices', array( $this, 'render' ) );
		add_action( 'network_admin_notices', array( $this, 'render' ) );

		// Ajax request to dismiss.
		add_action( 'wp_ajax_wpmudev_bf_dismiss', array( $this, 'dismiss_banner' ) );
	}

	/**
	 * Enqueue assets for banner module of the BF sub-module.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! $this->can_display() ) {
			return;
		}

		$this->enqueue_styles();
		$this->enqueue_scripts();
	}

	/**
	 * Enqueues scripts and styles for the banner.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'wpmudev-bf-common',
			WPMUDEV_MODULE_BLACK_FRIDAY_ASSETS_URL . '/banner.min.css',
			array(),
			$this->script_version
		);

		// Hide the banner while the plugin shows a full screen view, eg Smush's onboarding wizard.
		// The plugin adds the body class in PHP, so the banner is hidden from the first paint, and shows again if the class is removed.
		$hidden_body_class = sanitize_html_class( Utils::get_current_page_plugin()['banner_hidden_body_class'] ?? '' );

		if ( ! empty( $hidden_body_class ) ) {
			wp_add_inline_style(
				'wpmudev-bf-common',
				sprintf( 'body.%s #wpbody-content .wpmudev-bf-banner { display: none !important; }', $hidden_body_class )
			);
		}
	}

	/**
	 * Enqueues js scripts for the banner.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$dependencies = ! empty( Utils::script_data( 'banner', 'dependencies' ) )
			? Utils::script_data( 'banner', 'dependencies' )
			: array(
				'react',
				'wp-element',
				'wp-i18n',
				'wp-is-shallow-equal',
				'wp-polyfill',
				'wp-dom-ready',
			);

		$plugin_data  = Utils::get_current_page_plugin();
		$utm_source   = $plugin_data['utm_source'] ?? '';
		$campaign_url = add_query_arg(
			array(
				'utm_source'   => $utm_source,
				'utm_medium'   => 'plugin',
				'utm_campaign' => $utm_source . '-' . $this->utm_campaign,
				'utm_content'  => $this->utm_content,
			),
			Utils::get_campaign_url( $plugin_data, $this->campaign_url )
		);

		wp_enqueue_script(
			'wpmudev-bf-common',
			WPMUDEV_MODULE_BLACK_FRIDAY_ASSETS_URL . '/banner.min.js',
			$dependencies,
			$this->script_version,
			true
		);

		$data = array(
			'nonce'             => wp_create_nonce( $this->nonce ),
			'campaign_url'      => $campaign_url,
			'dom_element_id'    => $this->dom_element_id,
			'container'         => Utils::get_current_page_plugin()['banner_container'] ?? '',
			'translation_texts' => $this->translation_texts(),
			'root_url'          => esc_url( WPMUDEV_MODULE_BLACK_FRIDAY_URL ),
		);

		/**
		 * Filters the banner data, eg its texts or campaign URL.
		 *
		 * @since 2.1.0
		 *
		 * @param array $data The banner script data.
		 */
		$data = (array) apply_filters( 'wpmudev_blackfriday_banner_data', $data );

		$data['campaign_url'] = esc_url_raw( $data['campaign_url'] ?? '' );

		wp_localize_script( 'wpmudev-bf-common', 'wpmudevBFBanner', $data );
	}

	/**
	 * Render the banner HTML.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render() {
		if ( ! $this->can_display() ) {
			return;
		}

		static $printed = false;

		if ( $printed ) {
			return;
		}
		$printed = true;

		?>
		<div id="<?php echo esc_attr( $this->dom_element_id ); ?>" class="wpmudev-bf-banner notice"></div>
		<?php
	}

	/**
	 * Check if banner can be displayed.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if banner can be displayed, false otherwise.
	 */
	private function can_display(): bool {
		static $can_display = null;

		if ( ! is_null( $can_display ) ) {
			return $can_display;
		}

		// Check user capabilities, if banner/notice has been dismissed (closed) and if current page is one of our plugins admin pages.
		if ( ! Utils::current_user_can_view() || $this->is_dismissed() || ! $this->is_allowed_page() ) {
			$can_display = false;
		} else {
			$can_display = true;
		}

		return $can_display;
	}

	/**
	 * Check if current page is one of our plugins admin pages.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if current page is allowed, false otherwise.
	 */
	private function is_allowed_page(): bool {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		if ( empty( $screen ) || empty( $screen->id ) ) {
			return false;
		}

		$allowed_slugs = $this->get_plugin_screens();
		$current_page  = $screen->id;

		foreach ( $allowed_slugs as $slug ) {
			if ( is_string( $slug ) && str_contains( $current_page, $slug ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get's the admin screens of each WPMU DEV plugin.
	 *
	 * @return array
	 */
	protected function get_plugin_screens(): array {
		return Utils::get_plugin_screens();
	}

	/**
	 * Get's all plugin data from plugins_list file.
	 *
	 * @return array
	 */
	protected function get_plugins_list(): array {
		return Utils::get_plugins_list();
	}

	/**
	 * Ajax request handler for banner dismissal.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function dismiss_banner() {
		// Verify nonce.
		check_ajax_referer( $this->nonce, 'nonce' );

		// Anyone who can see the banner can dismiss it, for everyone.
		if ( ! Utils::current_user_can_view() ) {
			wp_send_json_error( array( 'success' => false ), 403 );
		}

		// Dismiss notice.
		update_site_option( $this->option_name, true );

		wp_send_json_success( array( 'success' => true ) );
	}

	/**
	 * Check if the banner has been dismissed.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if dismissed, false otherwise.
	 */
	private function is_dismissed() {
		return (bool) get_site_option( $this->option_name, false );
	}

	/**
	 * Get the DOM element ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string The DOM element ID.
	 */
	public function get_dom_element_id() {
		return $this->dom_element_id;
	}

	/**
	 * Get UTM link.
	 *
	 * @since 2.0.0
	 *
	 * @return string The UTM link.
	 */
	public function get_utm_campaign() {
		return $this->utm_campaign;
	}

	/**
	 * Get banner version.
	 *
	 * @since 2.0.0
	 *
	 * @return string The version number.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Gets assets data for given key.
	 *
	 * @param string $key The requested portion of data, an array key, usually version or dependencies.
	 *
	 * @return string|array
	 */
	protected function script_data( string $key = '' ) {
		return Utils::script_data( 'banner', $key );
	}

	/**
	 * Gets the script data from assets php file.
	 *
	 * @return array
	 */
	protected function raw_script_data(): array {
		return Utils::raw_script_data( 'banner' );
	}

	/**
	 * Get translation texts for the banner.
	 *
	 * @since 2.0.0
	 *
	 * @return array The translation texts.
	 */
	protected function translation_texts() {
		/**
		 * Filters the deal price shown in the banner subtext.
		 *
		 * @since 2.1.0
		 *
		 * @param string $price The deal price, eg $3.33/m.
		 */
		$price = apply_filters( 'wpmudev_blackfriday_deal_price', '$3.33/m' );

		return array(
			'dismiss_button'       => __( 'Dismiss', 'wpmudev-black-friday' ),
			'black_friday_special' => __( 'Black Friday Special', 'wpmudev-black-friday' ),
			'subtext'              => sprintf(
				/* translators: %s: The deal price, eg $3.33/m. */
				__( '%s for unlimited sites', 'wpmudev-black-friday' ),
				$price
			),
			'button_text'          => __( 'See the deal', 'wpmudev-black-friday' ),
		);
	}
}
