<?php

namespace Imagely\NGG\REST\DataMappers;

use Imagely\NGG\Util\LicenseHelper;

// phpcs:disable Squiz.Commenting

class LicenseREST {

	public function register_routes() {
		register_rest_route(
			'imagely/v1',
			'/license/activate',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'activate_license' ],
				'permission_callback' => [ $this, 'permissions_check' ],
				'args'                => [
					'license_key' => [
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	public function permissions_check() {
		return current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' );
	}

	/**
	 * Activate license and install the appropriate Pro version
	 *
	 * @param \WP_REST_Request $request The REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function activate_license( $request ) {
		$license_key = $request->get_param( 'license_key' );

		if ( empty( $license_key ) ) {
			return new \WP_Error( 'license_key_required', 'License key is required', [ 'status' => 400 ] );
		}

		// Verify license with external server
		$verification_result = LicenseHelper::verify_license_with_server( $license_key );

		if ( is_wp_error( $verification_result ) ) {
			return $verification_result;
		}

		// Extract product level from verification
		$product = $verification_result['level'];
		$status  = $verification_result['status'];

		// Check if the product is already active
		$current_level = LicenseHelper::get_license_type();
		if ( $current_level === $product ) {
			return new \WP_REST_Response(
				[
					'success' => true,
					'message' => 'Congratulations! This site is now receiving automatic updates.',
					'data'    => [
						'product'           => $product,
						'already_installed' => true,
						'already_activated' => true,
					],
				],
				200
			);
		}

		// Check if the product is installed but not activated
		$plugin_basenames = [
			'pro'     => 'nextgen-gallery-pro/nggallery-pro.php',
			'plus'    => 'nextgen-gallery-plus/nggallery-plus.php',
			'starter' => 'nextgen-gallery-starter/nggallery-starter.php',
		];

		if ( LicenseHelper::is_product_installed( $product ) ) {
			// Plugin is installed but not active - just activate it
			$plugin_basename = $plugin_basenames[ $product ];
			$activate        = activate_plugin( $plugin_basename, false, false, true );

			if ( is_wp_error( $activate ) ) {
				return $activate;
			}

			return new \WP_REST_Response(
				[
					'success' => true,
					'message' => 'Congratulations! This site is now receiving automatic updates.',
					'data'    => [
						'product'   => $product,
						'basename'  => $plugin_basename,
						'activated' => true,
						'installed' => false, // Was already installed
					],
				],
				200
			);
		}

		// Download the Pro plugin
		$download_url = LicenseHelper::get_download_url( $license_key, $product );

		if ( is_wp_error( $download_url ) ) {
			return $download_url;
		}

		// Install the plugin (silent mode for REST)
		$install_result = LicenseHelper::install_plugin( $download_url, true, true );

		if ( is_wp_error( $install_result ) ) {
			return $install_result;
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'message' => 'Congratulations! This site is now receiving automatic updates.',
				'data'    => [
					'product'   => $product,
					'basename'  => $install_result['basename'],
					'activated' => $install_result['activated'],
				],
			],
			200
		);
	}
}
