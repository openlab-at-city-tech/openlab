<?php
/**
 * WPMUDEV Black Friday Action Links class
 *
 * Handles the addition of Black Friday action links in the plugins table.
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
 * Class Action_Links
 *
 * Manages Black Friday action links in the plugins table.
 *
 * @since 2.0.0
 */
class Action_Links {
	/**
	 * Action Links version.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $version = '2.0.0';

	/**
	 * Campaign URL.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $campaign_url = '';

	/**
	 * Link text.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $link_text = '';

	/**
	 * UTM content for the menu.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $utm_content = 'BF-Plugins-2025';

	/**
	 * UTM campaign for the menu.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $utm_campaign = 'BF-Plugins-2025-plugins-list';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. Action links configuration arguments.
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'campaign_url' => 'https://wpmudev.com/blackfriday',
			'link_text'    => __( 'Get Black Friday Deal', 'wpmudev-black-friday' ),
		);

		$args = wp_parse_args( $args, $defaults );

		$this->campaign_url = esc_url( $args['campaign_url'] );
		$this->link_text    = sanitize_text_field( $args['link_text'] );

		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Get all WPMU DEV plugins.
		$plugins_list = Utils::get_plugins_list();

		// Add action link filter for each plugin.
		foreach ( $plugins_list as $plugin_data ) {
			if ( empty( $plugin_data['slug'] ) ) {
				continue;
			}

			$action_links_hook = ( is_multisite() && is_network_admin() ) ? 'network_admin_plugin_action_links_' : 'plugin_action_links_';
			$plugin_path       = $plugin_data['path'];
			$plugin_utm_source = $plugin_data['utm_source'];

			add_filter(
				$action_links_hook . $plugin_path,
				function ( $actions ) use ( $plugin_utm_source ) {
					// Modify the campaign URL to include the correct UTM source.
					$campaign_url = add_query_arg(
						array(
							'utm_source'   => $plugin_utm_source,
							'utm_campaign' => $plugin_utm_source . '-' . $this->utm_campaign,
							'utm_medium'   => 'plugin',
							'utm_content'  => $this->utm_content,
						),
						$this->campaign_url
					);

					// Create Black Friday link.
					$black_friday_link = sprintf(
						'<a href="%s" target="_blank" rel="noopener noreferrer" class="wpmudev-bf-action-link">%s</a>',
						esc_url( $campaign_url ),
						esc_html( $this->link_text )
					);

					// Add Black Friday link.
					$actions['wpmudev_black_friday'] = $black_friday_link;

					return $this->handle_action_links( $actions );
				},
				998,
				1
			);
		}

		// Enqueue styles for action links.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Handle action links to ensure proper ordering.
	 *
	 * Ensures that 'deactivate' is the last item and 'wpmudev_black_friday' is second to last. Also removes other offers links.
	 *
	 * @since 2.0.0
	 *
	 * @param array $actions An array of plugin action links.
	 *
	 * @return array Modified array of plugin action links.
	 */
	public function handle_action_links( $actions ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $actions;
		}

		// Store the deactivate link if it exists.
		$deactivate_link = null;
		if ( isset( $actions['deactivate'] ) ) {
			$deactivate_link = $actions['deactivate'];
			unset( $actions['deactivate'] );
		}

		// Add deactivate link back as the last item.
		if ( ! is_null( $deactivate_link ) ) {
			$actions['deactivate'] = $deactivate_link;
		}

		if ( isset( $actions['upgrade'] ) ) {
			unset( $actions['upgrade'] );
		}

		if ( isset( $actions['smush_upgrade'] ) ) {
			unset( $actions['smush_upgrade'] );
		}

		// Forminator uses renew.
		if ( isset( $actions['renew'] ) ) {
			unset( $actions['renew'] );
		}

		foreach ( $actions as $key => $action ) {
			if ( strpos( $action, 'smartcrawl_pluginlist_upgrade' ) !== false ||
				strpos( $action, 'hummingbird_pluginlist_upgrade' ) !== false ) {
				unset( $actions[ $key ] );
			}
		}

		return $actions;
	}

	/**
	 * Enqueue custom styles for the Black Friday action links.
	 *
	 * @since 2.0.0
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {
		// Only load on plugins page.
		if ( 'plugins.php' !== $hook_suffix ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$custom_css = '
			.wpmudev-bf-action-link {
				color: #B1FF65 !important;
				background: #000 !important;
				line-height: 16px !important;
				font-weight: 500 !important;
				padding: 3px 5px !important;
				transition: background 0.5s ease !important;
			}
			.wpmudev-bf-action-link:hover,
			.wpmudev-bf-action-link:focus {
				background: #B1FF65 !important;
				color: #000 !important;
			}
		';

		wp_register_style( 'wpmudev-bf-action-links', false, array(), $this->version );
		wp_enqueue_style( 'wpmudev-bf-action-links' );
		wp_add_inline_style( 'wpmudev-bf-action-links', $custom_css );
	}

	/**
	 * Get campaign URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string The campaign URL.
	 */
	public function get_campaign_url() {
		return $this->campaign_url;
	}

	/**
	 * Get link text.
	 *
	 * @since 2.0.0
	 *
	 * @return string The link text.
	 */
	public function get_link_text() {
		return $this->link_text;
	}
}
