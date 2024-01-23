<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Installer;

class Installer {
	/**
	 * The version number for the library.
	 *
	 * @since 1.1.0
	 */
	public const VERSION = '1.1.1';

	/**
	 * Asset object.
	 *
	 * @since 1.1.0
	 *
	 * @var Assets|null
	 */
	protected $assets;

	/**
	 * @var ?string
	 */
	protected static $hook_prefix;

	/**
	 * Is this libary initialized?
	 *
	 * @since 1.0.0
	 *
	 * @var Installer|null
	 */
	public static $instance;

	/**
	 * Registered plugins.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $plugins = [];

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 *
	 * @return Installer
	 */
	public static function init(): Installer {
		if ( static::$instance ) {
			return static::$instance;
		}

		static::$instance = new self();

		return static::$instance;
	}

	/**
	 * Gets the asset object.
	 *
	 * @since 1.1.0
	 *
	 * @return Assets
	 */
	public function assets(): Assets {
		if ( empty( $this->assets ) ) {
			$this->assets = new Assets();
		}

		return $this->assets;
	}

	/**
	 * Helper function for ::init().
	 *
	 * @since 1.0.0
	 *
	 * @return Installer
	 */
	public static function get(): Installer {
		return static::init();
	}

	/**
	 * Resets the object back to its origin state.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function reset(): void {
		static::$instance = null;
	}

	/**
	 * Deregisters a plugin for installation / activation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_slug The slug of the plugin.
	 *
	 * @return bool Whether the plugin was deregistered or not.
	 */
	public function deregister_plugin( string $plugin_slug ): bool {
		if ( ! isset( $this->plugins[ $plugin_slug ] ) ) {
			return false;
		}

		$hook_prefix = Config::get_hook_prefix();

		remove_action( "wp_ajax_stellarwp_installer_{$hook_prefix}_install_plugin_{$plugin_slug}", [ $this->plugins[ $plugin_slug ], 'handle_request' ] );
		remove_action( 'deactivated_plugin', [ $this->plugins[ $plugin_slug ], 'clear_install_and_activation_cache' ] );

		unset( $this->plugins[ $plugin_slug ] );

		/**
		 * Fires when a plugin is deregistered.
		 *
		 * @since 1.0.0
		 *
		 * @param string $plugin_slug The slug of the plugin.
		 */
		do_action( "stellarwp/installer/{$hook_prefix}/deregister_plugin", $plugin_slug );

		return true;
	}

	/**
	 * Gets the busy class.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_busy_class() {
		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filters the busy class.
		 *
		 * @since 1.1.0
		 *
		 * @param string $busy_class The busy class.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/busy_class", 'is-busy' );
	}

	/**
	 * Gives the JS object name used for handling JS behaviors.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_js_object_key(): string {
		return sanitize_key( Config::get_hook_prefix() );
	}

	/**
	 * Gives the JS selectors indexed by slug.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_js_selectors(): array {
		$plugins   = $this->get_registered_plugins();

		$selectors = [];

		foreach ( $plugins as $plugin_slug => $plugin ) {
			$selectors[ $plugin_slug ] = '.' . $plugin->get_button()->get_selector();
		}

		return $selectors;
	}

	/**
	 * Generates a nonce for the installer.
	 *
	 * @since 1.0.0
	 *
	 * @return false|string
	 */
	public function get_nonce() {
		return wp_create_nonce( $this->get_nonce_name() );
	}

	/**
	 * Gets the nonce name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_nonce_name(): string {
		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filters the nonce name.
		 *
		 * @since 1.0.0
		 *
		 * @param string $nonce_name The nonce name.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/nonce_name", 'stellarwp_installer_resource_install' );
	}

	/**
	 * Gets a plugin button.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $slug Plugin slug.
	 * @param string      $action Action to perform. (install|activate)
	 * @param string|null $button_label Button label.
	 * @param string|null $redirect_url Redirect URL.
	 *
	 * @return string|null
	 */
	public function get_plugin_button( string $slug, string $action, ?string $button_label = null, ?string $redirect_url = null  ): ?string {
		ob_start();
		$this->render_plugin_button( $slug, $action, $button_label, $redirect_url );
		return ob_get_clean();
	}

	/**
	 * Gets registered plugins.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_registered_plugins(): array {
		return $this->plugins;
	}

	/**
	 * Gets registered plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Handler\Plugin|null
	 */
	public function get_registered_plugin( string $slug ): ?Handler\Plugin {
		if ( ! isset( $this->plugins[ $slug ] ) ) {
			return null;
		}

		return $this->plugins[ $slug ];
	}

	/**
	 * Returns whether or not a plugin is active.
	 *
	 * @param string $slug Resource slug.
	 *
	 * @return bool
	 */
	public function is_active( string $slug ): bool {
		if ( isset( $this->plugins[ $slug ] ) ) {
			return $this->plugins[ $slug ]->is_active();
		}

		if ( is_plugin_active( $slug ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns whether or not a plugin is installed.
	 *
	 * @param string $slug Resource slug.
	 *
	 * @return bool
	 */
	public function is_installed( string $slug ): bool {
		if ( $this->is_plugin_installed( $slug ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns whether or not a plugin is installed.
	 *
	 * @param string $slug Resource slug.
	 *
	 * @return bool
	 */
	public function is_plugin_installed( string $slug ): bool {
		if ( isset( $this->plugins[ $slug ] ) ) {
			return $this->plugins[ $slug ]->is_installed();
		}

		return false;
	}

	/**
	 * Returns whether or not a plugin is registered.
	 *
	 * @param string $slug Resource slug.
	 *
	 * @return bool
	 */
	public function is_registered( string $slug ): bool {
		return isset( $this->plugins[ $slug ] );
	}

	/**
	 * Registers a plugin for installation / activation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_slug The slug of the plugin.
	 * @param string $plugin_name The non-translated name of the plugin.
	 * @param string|null $download_url The download URL of the plugin.
	 * @param string|null $did_action The action that indicates that a plugin is active.
	 *
	 * @return void
	 */
	public function register_plugin( string $plugin_slug, string $plugin_name, ?string $download_url = null, ?string $did_action = null ): void {
		// If the plugin is already registered, deregister it first so we don't have duplicate actions.
		if ( isset( $this->plugins[ $plugin_slug ] ) ) {
			$this->deregister_plugin( $plugin_slug );
		}

		$hook_prefix = Config::get_hook_prefix();
		$js_action   = "stellarwp_installer_{$hook_prefix}_install_plugin_{$plugin_slug}";

		$handler     = new Handler\Plugin(
			$plugin_name,
			$plugin_slug,
			$download_url,
			$did_action,
			$js_action
		);

		$this->plugins[ $plugin_slug ] = $handler;

		add_action( "wp_ajax_{$js_action}", [ $handler, 'handle_request' ] );
		add_action( 'deactivated_plugin', [ $handler, 'clear_install_and_activation_cache' ] );

		/**
		 * Fires when a plugin is registered.
		 *
		 * @since 1.0.0
		 *
		 * @param string $plugin_slug The slug of the plugin.
		 * @param string $plugin_name The name of the plugin.
		 * @param string|null $download_url The download URL of the plugin.
		 */
		do_action( "stellarwp/installer/{$hook_prefix}/register_plugin", $plugin_slug, $plugin_name, $download_url );
	}

	/**
	 * Renders a plugin button.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $slug Plugin slug.
	 * @param string      $action Action to perform. (install|activate)
	 * @param string|null $button_label Button label.
	 * @param string|null $redirect_url Redirect URL.
	 *
	 * @return void
	 */
	public function render_plugin_button( string $slug, string $action, ?string $button_label = null, ?string $redirect_url = null  ): void {
		if ( ! isset( $this->plugins[ $slug ] ) ) {
			return;
		}

		$this->plugins[ $slug ]->get_button()->render( $action, $button_label, $redirect_url );
	}
}
