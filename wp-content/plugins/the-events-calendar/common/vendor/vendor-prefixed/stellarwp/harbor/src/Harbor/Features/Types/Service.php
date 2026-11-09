<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Types;

/**
 * A Feature delivered as an externally managed service (e.g. Promoter).
 *
 * Service features have no WordPress-side installation, version, or activation.
 * They are enabled or disabled exclusively through the Commerce Portal.
 *
 * @since 1.0.0
 */
final class Service extends Feature {

	/**
	 * Constructor for a Feature delivered as a service.
	 *
	 * Stores only the base attributes common to all features; version,
	 * plugin_file, and other installable fields do not apply to services.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $attributes The feature attributes.
	 *
	 * @return void
	 */
	public function __construct( array $attributes ) {
		$base               = self::base_attributes( $attributes );
		$base['type']       = self::TYPE_SERVICE;
		$base['is_enabled'] = $base['is_available'];

		parent::__construct( $base );
	}

	/**
	 * Creates a Service instance from an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The feature data from the API response.
	 *
	 * @return static
	 */
	public static function from_array( array $data ) {
		return new self( $data );
	}
}
