<?php

namespace TEC\Common\StellarWP\Uplink;

/**
 * A helper class for registering StellarWP Uplink resources.
 */
class Register {
	/**
	 * Register a plugin resource.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Added oAuth parameter.
	 *
	 * @param string   $slug          Resource slug.
	 * @param string   $name          Resource name.
	 * @param string   $version       Resource version.
	 * @param string   $path          Resource path to bootstrap file.
	 * @param string   $class         Resource class.
	 * @param string   $license_class Resource license class.
	 * @param bool|int $oauth         Whether the plugin uses OAuth (bool) or a set of OAuth options (int).
	 *
	 * @return Resources\Resource
	 */
	public static function plugin( $slug, $name, $version, $path, $class, $license_class = null, $oauth = false ) {
		return Resources\Plugin::register( $slug, $name, $version, $path, $class, $license_class, $oauth );
	}

	/**
	 * Register a service resource.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Added oAuth parameter.
	 *
	 * @param string $slug          Resource slug.
	 * @param string $name          Resource name.
	 * @param string $version       Resource version.
	 * @param string $path          Resource path to bootstrap file.
	 * @param string $class         Resource class.
	 * @param string $license_class Resource license class.
	 * @param bool   $oauth         Whether the plugin uses OAuth (bool) or a set of OAuth options (int).
	 *
	 * @return Resources\Resource
	 */
	public static function service( $slug, $name, $version, $path, $class, $license_class = null, $oauth = false) {
		return Resources\Service::register( $slug, $name, $version, $path, $class, $license_class, $oauth );
	}
}
