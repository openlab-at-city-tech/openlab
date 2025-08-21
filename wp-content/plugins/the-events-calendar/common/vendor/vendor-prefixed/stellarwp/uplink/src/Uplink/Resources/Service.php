<?php

namespace TEC\Common\StellarWP\Uplink\Resources;

class Service extends Resource {
	/**
	 * @inheritDoc
	 */
	protected $type = 'service';

	/**
	 * @inheritDoc
	 */
	public static function register( $slug, $name, $version, $path, $class, string $license_class = null, $oauth = false) {
		return parent::register_resource( static::class, $slug, $name, $version, $path, $class, $license_class, $oauth );
	}
}
