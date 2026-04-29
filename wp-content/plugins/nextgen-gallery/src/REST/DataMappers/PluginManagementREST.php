<?php

namespace Imagely\NGG\REST\DataMappers;

// phpcs:disable Squiz.Commenting

class PluginManagementREST {

	public function register_routes() {
		register_rest_route(
			'imagely/v1',
			'/plugins/status',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_plugins_status' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			]
		);

		register_rest_route(
			'imagely/v1',
			'/plugins/install',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'install_plugin' ],
				'permission_callback' => [ $this, 'permissions_check_install' ],
				'args'                => [
					'download_url' => [
						'required'          => true,
						'sanitize_callback' => 'esc_url_raw',
					],
					'basename'     => [
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		register_rest_route(
			'imagely/v1',
			'/plugins/activate',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'activate_plugin' ],
				'permission_callback' => [ $this, 'permissions_check_activate' ],
				'args'                => [
					'basename' => [
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		register_rest_route(
			'imagely/v1',
			'/plugins/deactivate',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'deactivate_plugin' ],
				'permission_callback' => [ $this, 'permissions_check_activate' ],
				'args'                => [
					'basename' => [
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	public function permissions_check() {
		return current_user_can( 'manage_options' );
	}

	public function permissions_check_install() {
		return current_user_can( 'install_plugins' );
	}

	public function permissions_check_activate() {
		return current_user_can( 'activate_plugins' );
	}

	public function get_plugins_status( $request ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();
		$plugin_statuses   = [];

		// List of plugins to check with their basenames
		$plugins_to_check = [
			'optinmonster/optin-monster-wp-api.php',
			'wpforms-lite/wpforms.php',
			'google-analytics-for-wordpress/googleanalytics.php',
			'wp-mail-smtp/wp_mail_smtp.php',
			'all-in-one-seo-pack/all_in_one_seo_pack.php',
			'coming-soon/coming-soon.php',
			'rafflepress/rafflepress.php',
			'pushengage/main.php',
			'instagram-feed/instagram-feed.php',
			'custom-facebook-feed/custom-facebook-feed.php',
			'feeds-for-youtube/youtube-feed.php',
			'custom-twitter-feeds/custom-twitter-feed.php',
			'trustpulse-api/trustpulse.php',
			'stripe/stripe-checkout.php',
			'easy-digital-downloads/easy-digital-downloads.php',
			'sugar-calendar-lite/sugar-calendar-lite.php',
			'charitable/charitable.php',
			'insert-headers-and-footers/ihaf.php',
			'duplicator/duplicator.php',
			'soliloquy-lite/soliloquy-lite.php',
		];

		foreach ( $plugins_to_check as $basename ) {
			if ( isset( $installed_plugins[ $basename ] ) ) {
				$plugin_statuses[ $basename ] = [
					'installed' => true,
					'active'    => is_plugin_active( $basename ),
				];
			} else {
				$plugin_statuses[ $basename ] = [
					'installed' => false,
					'active'    => false,
				];
			}
		}

		return new \WP_REST_Response( $plugin_statuses, 200 );
	}

	public function install_plugin( $request ) {
		try {
			$download_url = $request->get_param( 'download_url' );

			if ( ! $download_url ) {
				return new \WP_Error( 'missing_download_url', 'Download URL is required', [ 'status' => 400 ] );
			}

			// Ensure required WordPress files are loaded
			if ( ! function_exists( 'request_filesystem_credentials' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			// Note: We don't use set_current_screen() in REST context as WP_Screen is admin-only
			$method = '';
			$url    = esc_url_raw( admin_url( 'admin.php?page=imagely-about-us' ) );

			ob_start();
			$creds = request_filesystem_credentials( $url, $method, false, false, null );
			if ( false === $creds ) {
				$form = ob_get_clean();
				return new \WP_Error(
					'filesystem_credentials_required',
					__( 'Filesystem credentials required', 'nggallery' ),
					[
						'status' => 403,
						'form'   => $form,
					]
				);
			}

			if ( ! WP_Filesystem( $creds ) ) {
				ob_start();
				request_filesystem_credentials( $url, $method, true, false, null );
				$form = ob_get_clean();
				return new \WP_Error(
					'filesystem_not_accessible',
					__( 'Filesystem not accessible', 'nggallery' ),
					[
						'status' => 403,
						'form'   => $form,
					]
				);
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			// Check if Installer_Skin class exists
			if ( ! class_exists( 'Imagely\NGG\Util\Installer_Skin' ) ) {
				return new \WP_Error( 'installer_skin_missing', 'Installer_Skin class not found', [ 'status' => 500 ] );
			}

			$skin      = new \Imagely\NGG\Util\Installer_Skin();
			$installer = new \Plugin_Upgrader( $skin );
			$result    = $installer->install( $download_url );

			wp_cache_flush();

			if ( is_wp_error( $result ) ) {
				return new \WP_Error( 'install_failed', $result->get_error_message(), [ 'status' => 500 ] );
			}

			if ( $installer->plugin_info() ) {
				$plugin_basename = $installer->plugin_info();

				// Try to activate the plugin
				$activate = activate_plugin( $plugin_basename, false, false, true );

				if ( is_wp_error( $activate ) ) {
					return new \WP_REST_Response(
						[
							'success'   => true,
							'basename'  => $plugin_basename,
							'activated' => false,
							'message'   => 'Plugin installed but not activated',
						],
						200
					);
				}

				return new \WP_REST_Response(
					[
						'success'   => true,
						'basename'  => $plugin_basename,
						'activated' => true,
					],
					200
				);
			}

			return new \WP_Error( 'install_failed', 'Failed to install plugin', [ 'status' => 500 ] );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'install_exception', $e->getMessage(), [ 'status' => 500 ] );
		} catch ( \Error $e ) {
			return new \WP_Error( 'install_error', $e->getMessage(), [ 'status' => 500 ] );
		}
	}

	public function activate_plugin( $request ) {
		$basename = $request->get_param( 'basename' );

		if ( ! $basename ) {
			return new \WP_Error( 'missing_basename', 'Plugin basename is required', [ 'status' => 400 ] );
		}

		$result = activate_plugin( $basename );

		if ( is_wp_error( $result ) ) {
			return new \WP_Error( 'activate_failed', $result->get_error_message(), [ 'status' => 500 ] );
		}

		return new \WP_REST_Response( [ 'success' => true ], 200 );
	}

	public function deactivate_plugin( $request ) {
		$basename = $request->get_param( 'basename' );

		if ( ! $basename ) {
			return new \WP_Error( 'missing_basename', 'Plugin basename is required', [ 'status' => 400 ] );
		}

		deactivate_plugins( $basename );

		return new \WP_REST_Response( [ 'success' => true ], 200 );
	}
}
