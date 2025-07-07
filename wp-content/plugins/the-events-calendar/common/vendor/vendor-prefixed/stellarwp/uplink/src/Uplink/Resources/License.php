<?php

namespace TEC\Common\StellarWP\Uplink\Resources;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Site\Data;
use TEC\Common\StellarWP\Uplink\Utils;

class License {
	/**
	 * How often to check for updates (in hours).
	 *
	 * @var int
	 */
	protected $check_period = 12;

	/**
	 * Container instance.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * License key.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected $key;

	/**
	 * License origin.
	 *
	 *     network_option
	 *     site_option
	 *     file
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $key_origin;

	/**
	 * License origin code.
	 *
	 *     m = manual
	 *     e = embedded
	 *     o = original
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $key_origin_code;

	/**
	 * Option prefix for the key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_option_prefix = 'stellarwp_uplink_license_key_';

	/**
	 * Option prefix for the key status.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_status_option_prefix = 'stellarwp_uplink_license_key_status_';

	/**
	 * Resource instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Resource
	 */
	protected $resource;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Resource $resource The resource instance.
	 * @param ContainerInterface|null $container Container instance.
	 */
	public function __construct( Resource $resource, $container = null ) {
		$this->resource  = $resource;
		$this->container = $container ?: Config::get_container();
	}

	/**
	 * Deletes the key in site options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of key (network, local).
	 *
	 * @return bool
	 */
	public function delete_key( string $type = 'local' ): bool {
		if ( 'network' === $type && is_multisite() ) {
			return delete_network_option( 0, $this->get_key_option_name() );
		}

		return delete_option( $this->get_key_option_name() );
	}

	/**
	 * Get the license key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The type of key to get (any, network, local, default).
	 *
	 * @return string
	 */
	public function get_key( $type = 'any' ) {
		if ( empty( $this->key ) && ( 'any' === $type || 'network' === $type ) ) {
			$this->key = $this->get_key_from_network_option();

			if ( ! empty( $this->key ) ) {
				$this->key_origin = 'network_option';
			}
		}

		if ( empty( $this->key ) && ( 'any' === $type || 'local' === $type ) ) {
			$this->key = $this->get_key_from_option();

			if ( ! empty( $this->key ) ) {
				$this->key_origin = 'site_option';
			}
		}

		if ( empty( $this->key ) && ( 'any' === $type || 'default' === $type ) ) {
			$this->key = $this->get_key_from_license_file();

			if ( ! empty( $this->key ) ) {
				$this->key_origin = 'file';
			}
		}

		/**
		 * Filter the license key.
		 *
		 * @since 1.0.0
		 *
		 * @param string|null $key The license key.
		 * @param Resource $resource The resource instance.
		 */
		$key = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/license_get_key', $this->key, $this->resource );

		/**
		 * Filter the license key.
		 *
		 * Accepts the resource's slug dynamically.
		 *
		 * @since 2.0.0
		 *
		 * @param string|null $key The license key.
		 * @param Resource $resource The resource instance.
		 */
		$key = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/' . $this->resource->get_slug() . '/license_get_key', $key, $this->resource );

		return $key ?: '';
	}

	/**
	 * Get the license key from a class that holds the license key.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	protected function get_key_from_license_file() {
		$license_class = $this->resource->get_license_class();
		$key           = null;

		if ( empty( $license_class ) ) {
			return null;
		}

		if ( defined( $license_class . '::KEY' ) ) {
			$key = $license_class::KEY;
		} elseif ( defined( $license_class . '::DATA' ) ) {
			$key = $license_class::DATA;
		}

		return $key;
	}

	/**
	 * Get the license key from a network option.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	protected function get_key_from_network_option() {
		if ( ! is_multisite() ) {
			return null;
		}

		if ( ! $this->resource->is_network_activated() ) {
			return null;
		}

		/** @var string|null */
		return get_network_option( 0, $this->get_key_option_name(), null );
	}

	/**
	 * Get the license key from an option.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	protected function get_key_from_option() {
		/** @var string|null */
		return get_option( $this->get_key_option_name(), null );
	}

	/**
	 * Get the option name for the license key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_key_option_name(): string {
		return static::$key_option_prefix . $this->resource->get_slug();
	}

	/**
	 * Get the license key origin code.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_key_origin_code(): string {
		if ( ! empty( $this->key_origin_code ) ) {
			return $this->key_origin_code;
		}

		$key         = $this->get_key();
		$default_key = $this->get_key( 'default' );

		if ( $key === $default_key ) {
			$this->key_origin_code = 'o';
		} elseif ( 'file' === $this->key_origin ) {
			$this->key_origin_code = 'e';
		} else {
			$this->key_origin_code = 'm';
		}

		return $this->key_origin_code;
	}

	/**
	 * Get the license key status from an option.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	protected function get_key_status() {
		/** @var string|null */
		$status = get_option( $this->get_key_status_option_name(), 'invalid' );

		if ( null === $status && $this->get_key() ) {
			$this->resource->validate_license( $this->get_key(), $this->resource->is_network_licensed() );
			$status = get_option( $this->get_key_status_option_name(), 'invalid' );
		}

		return $status;
	}

	/**
	 * Get the option name for the license key status.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_key_status_option_name(): string {
		/** @var Data */
		$data = $this->container->get( Data::class );

		return static::$key_status_option_prefix . $this->resource->get_slug() . '_'. $data->get_site_domain();
	}

	/**
	 * Whether the plugin is network activated and licensed or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_network_licensed() {
		$is_network_licensed = false;

		if ( ! is_network_admin() && $this->resource->is_network_activated() ) {
			$network_key = $this->get_key( 'network' );
			$local_key   = $this->get_key( 'local' );

			// Check whether the network is licensed and NOT overridden by local license
			if ( $network_key && ( empty( $local_key ) || $local_key === $network_key ) ) {
				$is_network_licensed = true;
			}
		}

		return $is_network_licensed;
	}

	/**
	 * Whether the plugin is validly licensed or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {
		return 'valid' === $this->get_key_status();
	}

	/**
	 * Whether the plugin license is expired or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_expired(): bool {
		return 'expired' === $this->get_key_status();
	}

	/**
	 * Whether the validation has expired.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_validation_expired() {
		$option_expiration = get_option( $this->get_key_status_option_name() . '_timeout', null );
		return is_null( $option_expiration ) || ( time() > $option_expiration );
	}

	/**
	 * Sets the key in site options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key License key.
	 * @param string $type Type of key (network, local).
	 *
	 * @return bool
	 */
	public function set_key( string $key, string $type = 'local' ): bool {
		$key = Utils\Sanitize::key( $key );

		$this->key = $key;

		if ( 'network' === $type && is_multisite() ) {
			// WordPress would otherwise return false if the keys already match.
			if ( $this->get_key_from_network_option() === $key ) {
				return true;
			}

			return update_network_option( 0, $this->get_key_option_name(), $key );
		}

		// WordPress would otherwise return false if the keys already match.
		if ( $this->get_key_from_option() === $key ) {
			return true;
		}

		return update_option( $this->get_key_option_name(), $key );
	}

	/**
	 * Sets the key status based on the key validation check results.
	 *
	 * @since 2.0.0
	 *
	 * @param int $valid 0 for invalid, 1 or 2 for valid.
	 *
	 * @return void
	 */
	public function set_key_status( $valid ) {
		$status = Utils\Checks::is_truthy( $valid ) ? 'valid' : 'invalid';
		update_option( $this->get_key_status_option_name(), $status );
		update_option( $this->get_key_status_option_name() . '_timeout', $this->check_period * HOUR_IN_SECONDS );
	}

	/**
	 * Get the key as a string.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->get_key() ?: '';
	}
}
