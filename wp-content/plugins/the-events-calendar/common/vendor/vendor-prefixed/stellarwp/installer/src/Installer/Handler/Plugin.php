<?php

namespace TEC\Common\StellarWP\Installer\Handler;

use stdClass;
use TEC\Common\StellarWP\Installer\Button;
use TEC\Common\StellarWP\Installer\Config;
use TEC\Common\StellarWP\Installer\Contracts\Handler;
use TEC\Common\StellarWP\Installer\Installer;
use TEC\Common\StellarWP\Installer\Utils\Array_Utils;
use WP_Error;

class Plugin implements Handler {
	/**
	 * Resource basename.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $basename;

	/**
	 * Button.
	 *
	 * @since 1.0.0
	 *
	 * @var Button|null
	 */
	protected $button;

	/**
	 * Action indicating that a resource has been activated.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected $did_action;

	/**
	 * Download URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected $download_url;

	/**
	 * Whether the resource is activated.
	 *
	 * @var bool|null
	 */
	protected $is_active = null;

	/**
	 * Whether the resource is installed.
	 *
	 * @var bool|null
	 */
	protected $is_installed = null;

	/**
	 * The JS action to be used in the button.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $js_action;

	/**
	 * Resource name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * @inheritDoc
	 */
	public $permission = 'install_plugins';

	/**
	 * Resource slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Type of resource.
	 *
	 * @var string|null
	 */
	public $type = 'plugin';

	/**
	 * WordPress.org data.
	 *
	 * @since 1.0.0
	 *
	 * @var stdClass|WP_Error|null
	 */
	protected $wordpress_org_data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $name, string $slug, ?string $download_url = null, ?string $did_action = null, ?string $js_action = null ) {
		$this->name         = $name;
		$this->slug         = $slug;
		$this->download_url = $download_url;
		$this->did_action   = $did_action;
		$this->js_action    = $js_action;
	}

	/**
	 * @inheritDoc
	 */
	public function activate(): bool {
		if ( ! $this->is_installed() ) {
			return $this->install();
		}

		if ( $this->is_active() ) {
			return true;
		}

		$activate = activate_plugin( $this->get_basename(), '', false, true );

		$this->is_active = ( $activate === null );

		return $this->is_active;
	}

	/**
	 * @inheritDoc
	 */
	public function clear_install_and_activation_cache( $plugin ) {
		if ( $this->get_basename() !== $plugin ) {
			return;
		}

		$this->is_installed = null;
		$this->is_active    = null;
	}

	/**
	 * Gets a plugin's basename.
	 *
	 * @return string|null
	 */
	public function get_basename(): ?string {
		if ( empty( $this->basename ) ) {
			$plugins = get_plugins();
			foreach ( $plugins as $file => $plugin ) {
				if ( $plugin['Name'] === $this->name ) {
					$this->basename = $file;
					break;
				}
			}
		}

		return $this->basename;
	}

	/**
	 * Gets the resource's button.
	 *
	 * @since 1.0.0
	 *
	 * @return Button
	 */
	public function get_button() : Button {
		if ( empty( $this->button ) ) {
			$this->button = new Button( $this );
		}
		return $this->button;
	}

	/**
	 * Gets the download_url.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	protected function get_download_url() : ?string {
		if ( ! $this->download_url ) {
			$api = $this->get_wordpress_org_data();

			if ( ! is_wp_error( $api ) ) {
				$this->download_url = $api->download_link;
			}
		}

		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filters the download URL for the resource.
		 *
		 * @since 1.0.0
		 *
		 * @param string|null $download_url The download URL.
		 * @param string      $slug         The resource slug.
		 * @param Handler     $handler      The handler instance.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/download_url", $this->download_url, $this->slug, $this );
	}

	/**
	 * @inheritDoc
	 */
	public function get_error_message(): ?string {
		$hook_prefix = Config::get_hook_prefix();

		$install_url = wp_nonce_url(
			self_admin_url(
				'update.php?action=install-plugin&plugin=' . $this->slug
			),
			'install-plugin_' . $this->slug
		);

		$message     = sprintf(
			/* Translators: %1$s - opening link tag, %2$s - closing link tag. */
			__( 'There was an error and plugin could not be installed, %1$splease install manually%2$s.', 'tribe-common' ),
			'<a href="' . esc_url( $install_url ) . '">',
			'</a>'
		);

		/**
		 * Filters the error message for a plugin install.
		 *
		 * @since 1.0.0
		 *
		 * @param string $message The error message.
		 * @param string $slug The plugin slug.
		 * @param Plugin $plugin The plugin handler.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/install_error_message", $message, $this->slug, $this );
	}

	/**
	 * @inheritDoc
	 */
	public function get_js_action(): string {
		return $this->js_action;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the resource permission.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_permission(): string {
		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filters the permission for installing the resource.
		 *
		 * @since 1.0.0
		 *
		 * @param string|null $permission The permission.
		 * @param string      $slug       The resource slug.
		 * @param Handler     $handler    The installer object.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/get_permission", $this->permission, $this->slug, $this );
	}

	/**
	 * Tests to see if the requested variable is set either as a post field or as a URL
	 * param and returns the value if so.
	 *
	 * Post data takes priority over fields passed in the URL query. If the field is not
	 * set then $default (null unless a different value is specified) will be returned.
	 *
	 * The variable being tested for can be an array if you wish to find a nested value.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $var
	 * @param mixed        $default
	 *
	 * @return mixed
	 */
	protected function get_request_var( $var, $default = null ) {
		$requests = [];

		// Prevent a slew of warnings every time we call this.
		$requests[] = $_REQUEST;
		$requests[] = $_GET;
		$requests[] = $_POST;

		$unsafe = Array_Utils::get_in_any( $requests, $var, $default );
		return Array_Utils::sanitize_deep( $unsafe );
	}

	/**
	 * @inheritDoc
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * @inheritDoc
	 */
	public function get_wordpress_org_data() {
		if ( $this->wordpress_org_data ) {
			return $this->wordpress_org_data;
		}

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		$hook_prefix = Config::get_hook_prefix();

		$api_results = plugins_api(
			'plugin_information',
			[
				'slug'   => $this->slug,
				'fields' => [
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				],
			]
		);

		/**
		 * Filters the WordPress.org data for a plugin.
		 *
		 * @since 1.0.0
		 *
		 * @param object|WP_Error $api_results The WordPress.org data.
		 * @param string $slug The plugin slug.
		 * @param Plugin $plugin The plugin handler.
		 */
		$api_results = apply_filters( "stellarwp/installer/{$hook_prefix}/wordpress_org_data", $api_results, $this->slug, $this );

		$this->wordpress_org_data = $api_results;

		return $this->wordpress_org_data;
	}

	/**
	 * @inheritDoc
	 */
	public function handle_request() {
		$installer = Installer::get();

		if ( ! check_ajax_referer( $installer->get_nonce_name(), 'nonce', false ) ) {
			$response['message'] = wpautop( __( 'Insecure request.', 'tribe-common' ) );

			wp_send_json_error( $response );
		}

		if ( ! current_user_can( $this->get_permission() ) ) {
			wp_send_json_error(
				[
					'message' => wpautop(
						sprintf(
							__( 'Security Error, Need higher permissions to install %1$s.' , 'tribe-common' ),
							$this->name
						)
					)
				]
			);
		}

		$vars = [
			'request' => $this->get_request_var( 'request' ),
		];

		$success = false;

		if ( 'install' === $vars['request'] ) {
			$success = $this->install();
		} elseif ( 'activate' === $vars['request'] ) {
			$success = $this->activate();
		}

		if ( false === $success ) {
			wp_send_json_error( [ 'message' => wpautop( $this->get_error_message() ) ] );
		} else {
			wp_send_json_success( [ 'message' => __( 'Success.', 'tribe-common' ) ] );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function install(): bool {
		if ( ! class_exists( 'WP_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$url = $this->get_download_url();

		if ( ! is_wp_error( $this->wordpress_org_data ) ) {
			$upgrader  = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
			$installed = $upgrader->install( $url );

			if ( $installed ) {
				$activate = activate_plugin( $this->get_basename(), '', false, true );
				$success  = ! is_wp_error( $activate );
			} else {
				$success = false;
			}
		} else {
			$success = false;
		}

		return $success;
	}

	/**
	 * @inheritDoc
	 */
	public function is_active(): bool {
		if ( $this->is_active === null ) {
			$did_action = false;

			if ( isset( $this->did_action ) ) {
				$did_action = did_action( $this->did_action );
			}

			$this->is_active = is_plugin_active( $this->get_basename() ) || is_plugin_active_for_network( $this->get_basename() ) || $did_action;
		}

		return $this->is_active;
	}

	/**
	 * Checks if the plugin is installed.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean True if active
	 */
	public function is_installed(): bool {
		if ( $this->is_installed === null ) {
			$this->is_installed = false;
			$installed_plugins  = get_plugins();

			foreach ( $installed_plugins as $file => $plugin ) {
				if ( $plugin['Name'] === $this->name ) {
					$this->basename     = $file;
					$this->is_installed = true;
					break;
				}
			}
		}

		return $this->is_installed;
	}
}
