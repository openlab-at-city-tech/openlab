<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

defined( 'ABSPATH' ) || exit;

class Plugin_Installer {

	private $plugin_file;
	private $plugin_slug;

	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_slug = dirname( $plugin_file );
		$this->initialize_filesystem();
	}

	/**
	 * Install and activate a plugin.
	 */
	public function install_and_activate() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( $this->plugin_file ) ) {
			return;
		}

		if ( $this->is_plugin_installed() ) {
			$ignore_silent_activation = ['popup-builder-block/popup-builder-block.php'];
			$silent = in_array( $this->plugin_file, $ignore_silent_activation ) ? false : true;
			
			$activate = activate_plugin( $this->plugin_file, '', false, $silent );
			if ( ! is_wp_error( $activate ) ) {
				return true;
			}
		} else {
			$this->install_plugin();
		}
	}

	/**
	 * Install the plugin using WP_Upgrader.
	 */
	private function install_plugin() {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$plugin_info = $this->get_plugin_information( $this->plugin_slug );

		if ( is_wp_error( $plugin_info ) || ! $plugin_info || empty( $plugin_info->download_link ) ) {
			return false;
		}

		$skin = new \ElementsKit_Lite\Libs\Framework\Classes\Plugin_Skin();

		$upgrader = new \Plugin_Upgrader( $skin );
		$upgrader->install( $plugin_info->download_link );

		if ( $this->is_plugin_installed() ) {
			$ignore_silent_activation = ['popup-builder-block/popup-builder-block.php'];
			$silent = in_array( $this->plugin_file, $ignore_silent_activation ) ? false : true;

			$activate = activate_plugin( $this->plugin_file, '', false, $silent );
			if ( ! is_wp_error( $activate ) ) {
				return true;
			}
		}
	}

	/**
	 * Get plugin info from WordPress.org.
	 */
	private function get_plugin_information( $plugin_slug ) {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api( 'plugin_information', [
			'slug'   => $plugin_slug,
			'fields' => [
				'download_link' => true,
			],
		] );

		return $api;
	}

	/**
	 * Check if plugin is installed.
	 */
	private function is_plugin_installed() {
		global $wp_filesystem;
		return $wp_filesystem->exists( WP_PLUGIN_DIR . '/' . $this->plugin_file );
	}

	/**
	 * Init WordPress Filesystem API.
	 */
	private function initialize_filesystem() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();
	}

	/**
	 * Static method to bulk install/activate multiple plugins.
	 */
	public static function single_install_and_activate( string $plugin_file ) {
		$installer = new self( $plugin_file );
		$installer->install_and_activate();
	}

	/**
	 * Static method to bulk install/activate multiple plugins.
	 */
	public static function bulk_install_and_activate( array $plugin_files ) {
		foreach ( $plugin_files as $plugin_file ) {
			$installer = new self( $plugin_file );
			$installer->install_and_activate();
		}
	}
}
