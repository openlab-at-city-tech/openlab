<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Utils;

/**
 * Utilities for unified license key validation.
 *
 * Unified keys are issued by StellarWP and always begin with the
 * LWSW- prefix. Any key that does not match this format should be
 * rejected before storage or use.
 *
 * @since 1.0.0
 */
final class License_Key {

	/**
	 * The expected prefix for all unified license keys.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const PREFIX = 'LWSW-';

	/**
	 * Whether the given value is a structurally valid unified license key.
	 *
	 * Checks that the key is a non-empty string beginning with LWSW-.
	 * This is an initial format check only — it does not contact the
	 * licensing server to verify the key is active or correct.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The key to validate.
	 *
	 * @return bool
	 */
	public static function is_valid_format( string $key ): bool {
		return stripos( $key, self::PREFIX ) === 0;
	}
}
