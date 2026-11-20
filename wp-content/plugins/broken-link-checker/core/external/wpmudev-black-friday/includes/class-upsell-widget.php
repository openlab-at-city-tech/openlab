<?php
/**
 * WPMUDEV Black Friday Upsell Widget class
 *
 * Restyles the plugins' upsell widgets for the Black Friday campaign.
 *
 * @since   2.1.0
 * @author  WPMUDEV
 * @package WPMUDEV\Modules\BlackFriday
 */

namespace WPMUDEV\Modules\BlackFriday;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Upsell_Widget
 *
 * Plugins don't provide hooks for their upsell widgets, so the widgets are found and changed with js.
 * Each plugin sets its widget selectors in plugins-list.php under the `upsell_widget` key.
 *
 * @since 2.1.0
 */
class Upsell_Widget {

	/**
	 * Script version.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	private $version = '2.1.0';

	/**
	 * Campaign URL.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	private $campaign_url = '';

	/**
	 * UTM campaign for the widget.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	private $utm_campaign = 'BF-Plugins-2026-widget';

	/**
	 * UTM content for the widget.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	private $utm_content = 'BF-Plugins-2026';

	/**
	 * Constructor.
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Optional. Widget configuration arguments.
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'campaign_url' => 'https://wpmudev.com/black-friday/',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->campaign_url = esc_url( $args['campaign_url'] );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue widget assets on the screens of plugins that have an upsell widget.
	 *
	 * @since 2.1.0
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! Utils::current_user_can_view() ) {
			return;
		}

		$widgets = $this->get_current_screen_widgets();

		if ( empty( $widgets ) ) {
			return;
		}

		// Read the asset file directly. Utils::script_data() returns the whole array when a key is empty, eg no dependencies.
		$asset          = Utils::raw_script_data( 'widget' );
		$script_version = ! empty( $asset['version'] ) ? $asset['version'] : $this->version;
		$dependencies   = $asset['dependencies'] ?? array();

		wp_enqueue_style(
			'wpmudev-bf-widget',
			WPMUDEV_MODULE_BLACK_FRIDAY_ASSETS_URL . '/widget.min.css',
			array(),
			$script_version
		);

		wp_enqueue_script(
			'wpmudev-bf-widget',
			WPMUDEV_MODULE_BLACK_FRIDAY_ASSETS_URL . '/widget.min.js',
			$dependencies,
			$script_version,
			false // In the head, so the widget is restyled while it's parsed. See src/widget.js.
		);

		/**
		 * Filters the upsell widget data, eg its texts or campaign URLs.
		 *
		 * @since 2.1.0
		 *
		 * @param array $data The widget script data.
		 */
		$data = (array) apply_filters(
			'wpmudev_blackfriday_widget_data',
			array(
				'widgets'           => $widgets,
				'translation_texts' => $this->translation_texts(),
			)
		);

		foreach ( (array) ( $data['widgets'] ?? array() ) as $key => $widget ) {
			$data['widgets'][ $key ]['campaign_url'] = esc_url_raw( $widget['campaign_url'] ?? '' );
		}

		wp_localize_script( 'wpmudev-bf-widget', 'wpmudevBFWidget', $data );
	}

	/**
	 * Get the upsell widgets settings for the current screen.
	 *
	 * @since 2.1.0
	 *
	 * @return array The widgets settings, each with its campaign url.
	 */
	private function get_current_screen_widgets(): array {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return array();
		}

		$screen = get_current_screen();

		if ( empty( $screen ) || empty( $screen->id ) ) {
			return array();
		}

		$widgets = array();

		foreach ( Utils::get_plugins_list() as $plugin_key => $plugin_data ) {
			$widget = $plugin_data['upsell_widget'] ?? array();

			if ( empty( $widget['screens'] ) || empty( $widget['button'] ) || ! in_array( $screen->id, (array) $widget['screens'], true ) ) {
				continue;
			}

			$utm_source = $plugin_data['utm_source'] ?? '';

			$widgets[] = array(
				'plugin'       => sanitize_html_class( $plugin_key ),
				'button'       => $widget['button'],
				'container'    => $widget['container'] ?? '',
				'title'        => $widget['title'] ?? '',
				'hide'         => $widget['hide'] ?? '',
				'campaign_url' => add_query_arg(
					array(
						'utm_source'   => $utm_source,
						'utm_medium'   => 'plugin',
						'utm_campaign' => $utm_source . '-' . $this->utm_campaign,
						'utm_content'  => $this->utm_content,
					),
					Utils::get_campaign_url( $plugin_data, $this->campaign_url )
				),
			);
		}

		return $widgets;
	}

	/**
	 * Get translation texts for the widget.
	 *
	 * @since 2.1.0
	 *
	 * @return array The translation texts.
	 */
	protected function translation_texts() {
		return array(
			'black_friday_special' => __( 'Black Friday Special', 'wpmudev-black-friday' ),
			'button_text'          => __( 'See the deal', 'wpmudev-black-friday' ),
		);
	}
}
