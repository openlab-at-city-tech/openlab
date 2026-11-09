<?php
/**
 * This file contains the DLM_Downloads_Path class which handles the download paths.
 *
 * @package DownloadMonitor
 * @since 5.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Downloads_Path class.
 *
 * The main class that handles the download paths.
 *
 * @since 5.0.0
 */
class DLM_Downloads_Path {

	/**
	 * Holds the class object.
	 *
	 * @since 5.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 */
	private function __construct() {
		// Set AJAX hooks.
		add_action( 'wp_ajax_dlm_update_downloads_path', array( $this, 'update_downloads_path' ) );
		add_action( 'wp_ajax_dlm_enable_download_path', array( $this, 'enable_download_path' ) );
		// Set the rest of the hooks.
		$this->set_frontend_hooks();
		$this->set_admin_hooks();
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return object The DLM_Downloads_Path object.
	 * @since 5.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_Downloads_Path ) ) {
			self::$instance = new DLM_Downloads_Path();
		}

		return self::$instance;
	}

	/**
	 * Set required admin hooks for the Download Monitor settings.
	 *
	 * @since 5.0.0
	 */
	private function set_admin_hooks() {
		// Add Approved Download Paths section to the Download Monitor's settings page.
		add_filter( 'dlm_settings', array( $this, 'status_tab' ), 15, 1 );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
	}

	/**
	 * Set required admin hooks for the Download Monitor settings.
	 *
	 * @since 5.0.0
	 */
	private function set_frontend_hooks() {
		if ( is_admin() ) {
			return;
		}
		// We need to set the setting for the frontend as well, as we need the default return value.
		add_action( 'init', array( $this, 'register_setting' ) );
	}

	/**
	 * Register settings for advanced download path.
	 *
	 * @since 5.0.0
	 */
	public function register_setting() {
		// Register the setting for multisite.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$default = array();
		if ( is_multisite() ) {
			$multi_args = array(
				'type'    => 'array',
				'default' => array(
					'dlm_turn_off_file_browser' => '0',
				),
			);
			register_setting( 'dlm_advanced_download_path', 'dlm_network_settings', $multi_args );

			// Get the uploads path and URL.
			$uploads_dir = wp_upload_dir();
			// We need the path.
			$uploads_path = $uploads_dir['basedir'];
			// phpcs:enable
			$uploads = $uploads_path;
			// Set the default value.
			$default[] = array(
				'id'       => 2,
				'path_val' => trailingslashit( $uploads ),
				'enabled'  => true,
			);
			// Backwards compatibility for the uploads path.
			$old_user_path = get_option( 'dlm_downloads_path', '' );

			if ( ! empty( $old_user_path ) ) {
				$default[] = array(
					'id'       => 3,
					'path_val' => trailingslashit( $old_user_path ),
					'enabled'  => true,
				);
			}

			$args = array(
				'type'    => 'array',
				'default' => $default,
			);
		} else {
			// Add the ABSPATH path to the default array.
			$default[] = array(
				'id'       => 1,
				'path_val' => trailingslashit( ABSPATH ),
				'enabled'  => true,
			);
			// Add the WP_CONTENT_DIR path to the default array.
			$default[] = array(
				'id'       => 2,
				'path_val' => trailingslashit( WP_CONTENT_DIR ),
				'enabled'  => true,
			);

			// Backwards compatibility for the uploads path.
			$old_user_path = get_option( 'dlm_downloads_path', '' );

			if ( ! empty( $old_user_path ) ) {
				$default[] = array(
					'id'       => 3,
					'path_val' => trailingslashit( $old_user_path ),
					'enabled'  => true,
				);
			}
			// Register the setting for single site.
			$args = array(
				'type'    => 'array',
				'default' => $default,
			);
		}
		register_setting( 'dlm_advanced_download_path', 'dlm_allowed_paths', $args );
	}

	/**
	 * Add Status tab in the Download Monitor's settings page.
	 *
	 * @param  array $settings  Array of settings.
	 *
	 * @return array Updated array of settings.
	 * @since 5.0.0
	 */
	public function status_tab( $settings ) {

		// Check if Multisite and if the user has the manage_network capability.
		if ( $this->check_access() ) {
			$settings['advanced']['sections']['download_path'] = array(
				'title'         => __( 'Approved Download Paths', 'download-monitor' ),
				'fields'        => array(
					array(
						'name'     => '',
						'type'     => 'download_paths_table',
						'priority' => 10,
					),
				),
				'show_upsells'  => false,
				'contend_class' => 'dlm-content-tab-full',
			);
		}

		return $settings;
	}

	/**
	 * Update downloads path.
	 *
	 * @return void
	 * @since 4.8.0
	 */
	public function update_downloads_path() {
		// Check if the request is valid.
		check_ajax_referer( 'dlm-ajax-nonce', 'security' );
		// Check if the path is provided.
		if ( ! isset( $_POST['path'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No path provided', 'download-monitor' ) ) );
		}
		// Check if the user has permission to update the path.
		if ( ! $this->check_access() ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to update the path', 'download-monitor' ) ) );
		}
		// Save the new path in the Allowed Paths Table.
		DLM_Downloads_Path_Helper::save_unique_path( urldecode( $_POST['path'] ) );
		wp_send_json_success( array( 'message' => __( 'Path updated', 'download-monitor' ) ) );
	}

	/**
	 * Update downloads path.
	 *
	 * @return void
	 * @since 4.8.0
	 */
	public function enable_download_path() {

		// Check if the request is valid.
		check_ajax_referer( 'dlm-ajax-nonce', 'security' );
		// Check if the path is provided.
		if ( ! isset( $_POST['path'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No path provided', 'download-monitor' ) ) );
		}
		// Check if the user has permission to update the path.
		if ( ! $this->check_access() ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to update the path', 'download-monitor' ) ) );
		}

		$path = urldecode( $_POST['path'] );
		// Save the new path in the Allowed Paths Table.
		DLM_Downloads_Path_Helper::enable_download_path( $path );
		wp_send_json_success( array( 'message' => __( 'Path enabled', 'download-monitor' ) ) );
	}

	/**
	 * Check if current user has access to the download paths.
	 * @return bool
	 * @since 5.0.10
	 */
	public function check_access() {
		// Load the load.php file to get the is_multisite() function.
		require_once ABSPATH . 'wp-includes/load.php';
		// Check if it's a multisite installation.
		if ( ! is_multisite() ) {
			return current_user_can( 'manage_options' );
		}

		return current_user_can( 'manage_network' );
	}
}
