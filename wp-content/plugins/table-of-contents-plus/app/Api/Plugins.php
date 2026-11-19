<?php
namespace AIOSEO\TableOfContents\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin install/deinstall.
 *
 * @since 1.0.0
 */
class Plugins {
	/**
	 * Installs plugins.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function installPlugins( $request ) {
		$body    = $request->get_json_params();
		$plugins = ! empty( $body['plugins'] ) ? $body['plugins'] : [];
		$network = ! empty( $body['network'] ) ? $body['network'] : false;
		$error   = esc_html__( 'Installation failed. Please check permissions and try again.', 'table-of-contents-plus' );

		if ( ! is_array( $plugins ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => $error
			], 400 );
		}

		if ( ! aioseoTableOfContents()->helpers->canInstall() ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => $error
			], 400 );
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$failed    = [];
		$completed = [];
		foreach ( $plugins as $plugin ) {
			if ( empty( $plugin['plugin'] ) ) {
				return new \WP_REST_Response( [
					'success' => false,
					'message' => $error
				], 400 );
			}

			$result = aioseoTableOfContents()->helpers->installAddon( $plugin['plugin'], $network );
			if ( ! $result ) {
				$failed[] = $plugin['plugin'];
			} else {
				$completed[ $plugin['plugin'] ] = $result;
			}
		}

		return new \WP_REST_Response( [
			'success'   => true,
			'completed' => $completed,
			'failed'    => $failed
		], 200 );
	}

	/**
	 * Deactivates plugins.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function deactivatePlugins( $request ) {
		$body    = $request->get_json_params();
		$plugins = ! empty( $body['plugins'] ) ? $body['plugins'] : [];
		$network = ! empty( $body['network'] ) ? $body['network'] : false;
		$error   = esc_html__( 'Deactivation failed. Please check permissions and try again.', 'table-of-contents-plus' );

		if ( ! is_array( $plugins ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => $error
			], 400 );
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => $error
			], 400 );
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$failed    = [];
		$completed = [];
		foreach ( $plugins as $plugin ) {
			if ( empty( $plugin['plugin'] ) ) {
				return new \WP_REST_Response( [
					'success' => false,
					'message' => $error
				], 400 );
			}

			$deactivated = deactivate_plugins( $plugin['plugin'], false, $network );
			if ( is_wp_error( $deactivated ) ) {
				$failed[] = $plugin['plugin'];
			}

			$completed[] = $plugin['plugin'];
		}

		return new \WP_REST_Response( [
			'success'   => true,
			'completed' => $completed,
			'failed'    => $failed
		], 200 );
	}
}