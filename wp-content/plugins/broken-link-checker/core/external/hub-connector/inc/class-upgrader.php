<?php
/**
 * The upgrader class to perform install/update etc.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

use WP_Error;
use Theme_Upgrader;
use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;

/**
 * Class Upgrader
 */
class Upgrader {

	use Singleton;

	/**
	 * Stores the errors during current action.
	 *
	 * @since  1.0.0
	 * @var WP_Error
	 * @accces protected
	 */
	protected $error;

	/**
	 * Options for current action.
	 *
	 * @since  1.0.0
	 *
	 * @var array
	 * @access protected
	 */
	protected $options = array();

	/**
	 * Initialize the upgrader.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		// New error instance.
		$this->reset_errors();

		// Load dependencies.
		$this->load_dependencies();
	}

	/**
	 * Install a new plugin or theme.
	 *
	 * Optionally plugin can be activated after installation.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function install( $item, $type = 'plugin', $options = array() ) {
		$this->reset_errors();
		$this->options = $options;

		// Setup temp filters.
		$this->setup_filters();

		// If the string is already a URL.
		if ( filter_var( $item, FILTER_VALIDATE_URL ) ) {
			$link = esc_url_raw( $item );
		} else {
			// Get download link.
			$link = 'plugin' === $type ? $this->get_plugin_link( $item ) : $this->get_theme_link( $item );
		}

		// No download link found.
		if ( ! $link ) {
			$this->remove_filters();

			return false;
		}

		$skin = new WP_Ajax_Upgrader_Skin();

		if ( 'plugin' === $type ) {
			$upgrader = new Plugin_Upgrader( $skin );
			$success  = $upgrader->install( $link );
			if ( true === $success && $this->get_option( 'activate' ) ) {
				// Activate the plugin.
				$activated = activate_plugin(
					$upgrader->plugin_info(),
					false,
					$this->get_option( 'network_wide', is_multisite() ),
					$this->get_option( 'silent', true )
				);

				// If error in activation.
				if ( is_wp_error( $activated ) ) {
					$this->add_error( 'INS.10', $activated->get_error_message() );

					$success = false;
				}
			}
		} else {
			$upgrader = new Theme_Upgrader( $skin );
			$success  = $upgrader->install( $link );
		}

		// Remove temporary filters.
		$this->remove_filters();

		return $success;
	}

	/**
	 * Get download link from plugin id, slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $plugin Slug or ID.
	 *
	 * @return bool|string
	 */
	protected function get_plugin_link( $plugin ) {
		if ( is_numeric( $plugin ) ) {
			// Build download URL.
			return API::get()->rest_url_auth( 'install/' . intval( $plugin ) );
		} elseif ( is_string( $plugin ) ) {
			// File name.
			$slug = false !== strpos( $plugin, '/' ) ? dirname( $plugin ) : $plugin;

			// Save on a bit of bandwidth.
			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => sanitize_key( $slug ),
					'fields' => array( 'sections' => false ),
				)
			);

			// Unable to get plugin data.
			if ( is_wp_error( $api ) ) {
				// Add error.
				$this->add_error( 'INS.03', $api->get_error_message() );

				return false;
			}

			// Get download link.
			return $api->download_link;
		}

		// Invalid slug.
		$this->add_error( 'INS.02', __( 'Invalid or empty slug.', 'wpmudev' ) );

		return false;
	}

	/**
	 * Get download link from theme slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $theme Slug.
	 *
	 * @return bool
	 */
	public function get_theme_link( $theme ) {
		// Save on a bit of bandwidth.
		$api = themes_api(
			'theme_information',
			array(
				'slug'   => sanitize_key( $theme ),
				'fields' => array( 'sections' => false ),
			)
		);

		// Unable to get plugin data.
		if ( is_wp_error( $api ) ) {
			// Add error.
			$this->add_error( 'INS.03', $api->get_error_message() );

			return false;
		}

		// Get download link.
		return $api->download_link;
	}

	/**
	 * Setup temporary filters to alter the process.
	 *
	 * These filters will be removed after the action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setup_filters() {
		// Overwrite existing package.
		if ( $this->get_option( 'overwrite' ) ) {
			add_filter( 'upgrader_package_options', array( $this, 'filter_overwrite' ) );
		}
	}

	/**
	 * Set flag to overwrite plugin/theme if folder already exists.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Installation options.
	 *
	 * @return array
	 */
	public function filter_overwrite( $options ) {
		// Make sure we are overwriting existing plugin.
		$options['abort_if_destination_exists'] = false;

		return $options;
	}

	/**
	 * Remove filters we added for the action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function remove_filters() {
		// Remove overwrite filter.
		remove_filter( 'upgrader_package_options', array( $this, 'filter_overwrite' ) );
	}

	/**
	 * Get a specific option value for current action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = false ) {
		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : $default;
	}

	/**
	 * Get the error holder instance.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Error
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Get a specific option value for current action.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $code    Error code.
	 * @param string     $message Error message.
	 *
	 * @return void
	 */
	public function add_error( $code, $message ) {
		$this->error->add( $code, $message );
	}

	/**
	 * Reset error class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function reset_errors() {
		$this->error = new WP_Error();
	}

	/**
	 * Load all required files for the upgrader.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function load_dependencies() {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/theme-install.php';
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		include_once ABSPATH . 'wp-admin/includes/theme.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';
	}
}
