<?php

namespace Imagely\NGG\Util;

// phpcs:disable Squiz.Commenting

/**
 * License Helper - Shared utilities for license verification and plugin installation
 */
class LicenseHelper {

	/**
	 * Verify license key with external licensing server
	 *
	 * @param string $license_key The license key to verify.
	 * @return array{level: string, status: string}|\WP_Error
	 */
	public static function verify_license_with_server( $license_key ) {
		$url = 'https://members.photocrati.com/wp-json/licensing/v1/register_site';

		$query_args = [
			'license_key' => $license_key,
			'site_url'    => site_url(),
		];

		$args = [
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'body'        => $query_args,
			'user-agent'  => 'ImagelyUpdates/' . NGG_PLUGIN_VERSION . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
		];

		$response = wp_safe_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$http_code = wp_remote_retrieve_response_code( $response );
		$result    = json_decode( wp_remote_retrieve_body( $response ) );

		// Check for API errors
		if ( isset( $result->error ) && '' !== $result->error ) {
			return new \WP_Error(
				'license_error',
				self::get_error_message( $result->error )
			);
		}

		$valid = in_array( $result->status ?? '', [ 'active', 'inactive', 'disabled' ], true );

		// Check if status is valid
		if ( 200 !== $http_code || ! $valid ) {
			if ( 'expired' === ( $result->status ?? '' ) ) {
				return new \WP_Error(
					'license_expired',
					self::get_error_message( 'license_expired' )
				);
			}

			return new \WP_Error(
				'license_invalid',
				self::get_error_message( null )
			);
		}

		$product = $result->level ?? false;

		if ( ! $product ) {
			return new \WP_Error(
				'product_not_found',
				'Product not found.'
			);
		}

		// Check if limit is reached
		if ( '' === ( $result->is_at_limit ?? '' ) ) {
			return new \WP_Error(
				'license_limit_reached',
				'Sorry, you have reached the limit of sites for this license key.'
			);
		}

		return [
			'level'  => $product,
			'status' => $result->status,
		];
	}

	/**
	 * Get download URL for the Pro plugin
	 *
	 * @param string $license_key The license key.
	 * @param string $product The product name (pro, plus, or starter).
	 * @return string|\WP_Error
	 */
	public static function get_download_url( $license_key, $product ) {
		$url  = 'https://members.photocrati.com/wp-json/licensing/v1/get_update?product=nextgen-gallery-' . $product . '&license_key=' . $license_key . '&site_url=' . site_url();
		$args = [
			'method'      => 'GET',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'ImagelyUpdates/' . NGG_PLUGIN_VERSION . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
		];

		$response = wp_safe_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$http_code = wp_remote_retrieve_response_code( $response );
		$body      = wp_remote_retrieve_body( $response );

		if ( 200 !== $http_code ) {
			return new \WP_Error(
				'download_failed',
				'Failed to get download URL'
			);
		}

		$download_url = json_decode( $body )->download_url ?? '';

		if ( empty( $download_url ) ) {
			return new \WP_Error(
				'download_url_missing',
				'Download URL not found in response'
			);
		}

		return $download_url;
	}

	/**
	 * Install a plugin from a download URL
	 *
	 * @param string $download_url The download URL for the plugin.
	 * @param bool   $silent Whether to suppress output (for REST) or use set_current_screen (for AJAX).
	 * @param bool   $activate Whether to activate the plugin after installation.
	 * @return array{basename: string, activated: bool}|\WP_Error
	 */
	public static function install_plugin( $download_url, $silent = true, $activate = true ) {
		if ( empty( $download_url ) ) {
			return new \WP_Error(
				'download_url_required',
				'Download URL is required'
			);
		}

		// Ensure required WordPress files are loaded
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Set current screen for AJAX context (non-silent mode)
		if ( ! $silent ) {
			set_current_screen();
		}

		$method = '';
		$url    = esc_url_raw( admin_url( 'admin.php?page=imagely-settings' ) );

		ob_start();
		$creds = request_filesystem_credentials( $url, $method, false, false, null );
		if ( false === $creds ) {
			$form = ob_get_clean();
			return new \WP_Error(
				'filesystem_credentials_required',
				'Filesystem credentials required',
				[ 'form' => $form ]
			);
		}

		if ( ! WP_Filesystem( $creds ) ) {
			ob_start();
			request_filesystem_credentials( $url, $method, true, false, null );
			$form = ob_get_clean();
			return new \WP_Error(
				'filesystem_not_accessible',
				'Filesystem not accessible',
				[ 'form' => $form ]
			);
		}
		ob_end_clean();

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		// Check if Installer_Skin class exists
		if ( ! class_exists( 'Imagely\NGG\Util\Installer_Skin' ) ) {
			return new \WP_Error(
				'installer_skin_missing',
				'Installer_Skin class not found'
			);
		}

		$skin      = new \Imagely\NGG\Util\Installer_Skin();
		$installer = new \Plugin_Upgrader( $skin );
		$result    = $installer->install( $download_url );

		wp_cache_flush();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();

			$activated = false;

			// Try to activate the plugin if requested
			if ( $activate ) {
				$activation_result = activate_plugin( $plugin_basename, false, false, true );
				$activated         = ! is_wp_error( $activation_result );
			}

			return [
				'basename'  => $plugin_basename,
				'activated' => $activated,
			];
		}

		return new \WP_Error(
			'install_failed',
			'Failed to install plugin'
		);
	}

	/**
	 * Get the current license type/level installed
	 *
	 * @return string
	 */
	public static function get_license_type() {
		if ( defined( 'NGG_PRO_PLUGIN_BASENAME' ) ) {
			return 'pro';
		} elseif ( defined( 'NGG_PLUS_PLUGIN_BASENAME' ) ) {
			return 'plus';
		} elseif ( defined( 'NGG_STARTER_PLUGIN_BASENAME' ) ) {
			return 'starter';
		}

		return 'lite';
	}

	/**
	 * Check if a specific license level is already installed (active or inactive)
	 *
	 * @param string $product The product level to check (pro, plus, or starter).
	 * @return bool True if installed (active or not), false otherwise.
	 */
	public static function is_product_installed( $product ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_basenames = [
			'pro'     => 'nextgen-gallery-pro/nggallery-pro.php',
			'plus'    => 'nextgen-gallery-plus/nggallery-plus.php',
			'starter' => 'nextgen-gallery-starter/nggallery-starter.php',
		];

		if ( ! isset( $plugin_basenames[ $product ] ) ) {
			return false;
		}

		$all_plugins = get_plugins();
		return isset( $all_plugins[ $plugin_basenames[ $product ] ] );
	}

	/**
	 * Get error message for license errors
	 *
	 * @param string|null $error_code The error code.
	 * @return string
	 */
	public static function get_error_message( $error_code ) {
		if ( ! isset( $error_code ) ) {
			return 'Something went wrong, please try again later.';
		}

		$messages = [
			'empty_site_url'              => __( 'The site URL is missing. Please provide a valid URL.', 'nggallery' ),
			'license_not_found'           => __( 'The license key was not found. Please verify and try again.', 'nggallery' ),
			'license_status_expired'      => __( 'The license key has expired. Please renew your license.', 'nggallery' ),
			'license_expired'             => __( 'The license key has expired. Please renew your license.', 'nggallery' ),
			'license_status_disabled'     => __( 'The license key has not been activated yet. Please contact support.', 'nggallery' ),
			'license_disabled'            => __( 'The license key has not been activated yet. Please contact support.', 'nggallery' ),
			'license_status_revoked'      => __( 'The license key has been revoked. Please contact support.', 'nggallery' ),
			'license_revoked'             => __( 'The license key has been revoked. Please contact support.', 'nggallery' ),
			'license_limit_installations' => __( 'The license key has reached the maximum number of installations.', 'nggallery' ),
		];

		return $messages[ $error_code ] ?? __( 'An unknown error occurred. Please try again.', 'nggallery' );
	}
}
