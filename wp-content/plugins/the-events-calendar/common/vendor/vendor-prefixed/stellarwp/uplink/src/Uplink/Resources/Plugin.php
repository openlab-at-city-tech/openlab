<?php

namespace TEC\Common\StellarWP\Uplink\Resources;

use TEC\Common\StellarWP\Uplink\Admin\Notice;
use TEC\Common\StellarWP\Uplink\API\Validation_Response;

class Plugin extends Resource {
	/**
	 * Plugin update status.
	 *
	 * @since 1.0.0
	 *
	 * @var \stdClass|null
	 */
	protected $update_status;

	/**
	 * @inheritDoc
	 */
	protected $type = 'plugin';

	/**
	 * Update status for the resource.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $update_status_option_prefix = 'stellarwp_uplink_update_status_';

	/**
	 * Check for plugin updates.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $transient The pre-saved value of the `update_plugins` site transient.
	 * @param bool $force_fetch Force fetching the update status.
	 *
	 * @return mixed
	 */
	public function check_for_updates( $transient, $force_fetch = false ) {
		if ( ! is_object( $transient ) ) {
			return $transient;
		}

		// Allow .org plugins to opt out of update checks.
		if ( apply_filters( 'stellarwp/uplink/' . $this->get_slug() . '/prevent_update_check', false ) ) {
			return $transient;
		}

		$status                  = $this->get_update_status( $force_fetch );
		$status->last_check      = time();
		$status->checked_version = $this->get_installed_version();

		// Save before actually doing the checking just in case something goes wrong. We don't want to continually recheck.
		$this->set_update_status( $status );

		$results        = $this->validate_license();
		$status->update = $results->get_raw_response();

		// Prevent an empty class from being saved in the $transient.
		if ( isset( $status->update->version ) ) {
			$product_path = $this->get_path();

			if ( version_compare( $this->get_version_from_response( $results ), $this->get_installed_version(), '>' ) ) {
				/** @var \stdClass $transient */
				if ( ! isset( $transient->response ) ) {
					$transient->response = [];
				}

				$transient->response[ $product_path ] = $results->get_update_details();

				// Clear the no_update property if it exists.
				if ( isset( $transient->no_update[ $product_path ] ) ) {
					unset( $transient->no_update[ $product_path ] );
				}

				if ( 'expired' === $results->get_result() ) {
					$this->container->get( Notice::class )->add_notice( Notice::EXPIRED_KEY, $this->get_slug() );
				}
			} else {
				// Clean up any stale update info.
				if ( isset( $transient->response[ $product_path ] ) ) {
					unset( $transient->response[ $product_path ] );
				}

				/**
				 * If the plugin is up to date, we need to add it to the `no_update` property so that enable auto updates can appear correctly in the UI.
				 *
				 * See this post for more information:
				 * @link https://make.wordpress.org/core/2020/07/30/recommended-usage-of-the-updates-api-to-support-the-auto-updates-ui-for-plugins-and-themes-in-wordpress-5-5/
				 */
				/** @var \stdClass $transient */
				if ( ! isset( $transient->no_update ) ) {
					$transient->no_update = [];
				}

				$transient->no_update[ $product_path ] = $results->get_update_details();
			}

			// In order to show relevant issues on plugins page parse response data and add it to transient
			if ( version_compare( $this->get_version_from_response( $results ), $this->get_installed_version(), '>' ) && in_array( $results->get_result(), [ 'expired', 'invalid' ] ) ) {
				/** @var \stdClass $transient */
				if ( ! isset( $transient->response ) ) {
					$transient->response = [];
				}
				$transient->response[ $product_path ] = $results->handle_api_errors();
			}

		}

		$this->set_update_status( $status );

		return $transient;
	}

	/**
	 * Retrieve version from response
	 *
	 * @param Validation_Response $response
	 *
	 * @return string
	 */
	protected function get_version_from_response( $response ): string {
		if ( ! isset( $response->get_raw_response()->version ) ) {
			return '';
		}

		return $response->get_raw_response()->version;
	}

	/**
	 * Get the update status of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force_fetch Force fetching the update status.
	 *
	 * @return mixed
	 */
	public function get_update_status( $force_fetch = false ) {
		if ( ! $force_fetch ) {
			$this->update_status = get_option( $this->get_update_status_option_name(), null );
		}

		if ( ! is_object( $this->update_status ) ) {
			$this->update_status = (object) [
				'last_check'      => 0,
				'checked_version' => '',
				'update'          => null,
			];
		}

		return $this->update_status;
	}

	/**
	 * Gets the update status option name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_update_status_option_name(): string {
		return static::$update_status_option_prefix . $this->get_slug();
	}

	/**
	 * @inheritDoc
	 */
	public static function register( $slug, $name, $version, $path, $class, string $license_class = null, $oauth = false ) {
		return parent::register_resource( static::class, $slug, $name, $version, $path, $class, $license_class, $oauth );
	}

	/**
	 * Updates the update status value in options.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $status
	 *
	 * @return void
	 */
	protected function set_update_status( $status ) {
		update_option( $this->get_update_status_option_name(), $status );
	}

}
