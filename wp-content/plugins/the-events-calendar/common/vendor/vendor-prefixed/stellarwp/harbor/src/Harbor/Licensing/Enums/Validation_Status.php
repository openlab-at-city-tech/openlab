<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing\Enums;

use TEC\Common\LiquidWeb\Harbor\Licensing\Error_Code;

/**
 * Validation status constants mirroring the Liquid Web v1 licensing API.
 *
 * @since 1.0.0
 *
 * @see \StellarWP\Licensing\V4\Domain\Enums\Validation_Status (licensing service)
 */
final class Validation_Status {

	/**
	 * The license is valid and the product is activated on this domain.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const VALID = 'valid';

	/**
	 * The subscription has expired.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const EXPIRED = 'expired';

	/**
	 * The subscription is suspended.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const SUSPENDED = 'suspended';

	/**
	 * The subscription is cancelled.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const CANCELLED = 'cancelled';

	/**
	 * The license itself is suspended (all products affected).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const LICENSE_SUSPENDED = 'license_suspended';

	/**
	 * The license is banned (all products affected).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const LICENSE_BANNED = 'license_banned';

	/**
	 * No entitlement exists for this product under the license.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const NO_ENTITLEMENT = 'no_entitlement';

	/**
	 * The product is not activated on this domain.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const NOT_ACTIVATED = 'not_activated';

	/**
	 * All available activation seats are consumed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const OUT_OF_ACTIVATIONS = 'out_of_activations';

	/**
	 * The license key is not recognized.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const INVALID_KEY = 'invalid_key';

	/**
	 * The product has not been activated and requires activation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const ACTIVATION_REQUIRED = 'activation_required';

	/**
	 * The license covers multiple tiers and a tier selection is required.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const TIER_SELECTION_REQUIRED = 'tier_selection_required';

	/**
	 * Returns all valid status values.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public static function all(): array {
		return [
			self::VALID,
			self::EXPIRED,
			self::SUSPENDED,
			self::CANCELLED,
			self::LICENSE_SUSPENDED,
			self::LICENSE_BANNED,
			self::NO_ENTITLEMENT,
			self::NOT_ACTIVATED,
			self::OUT_OF_ACTIVATIONS,
			self::INVALID_KEY,
			self::ACTIVATION_REQUIRED,
			self::TIER_SELECTION_REQUIRED,
		];
	}

	/**
	 * Returns whether the given value is a valid status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The status value to check.
	 *
	 * @return bool
	 */
	public static function is_valid( string $value ): bool {
		return in_array( $value, self::all(), true );
	}

	/**
	 * Returns a human-readable error message for a non-valid status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The status value.
	 *
	 * @return string
	 */
	public static function message( string $value ): string {
		$messages = [
			self::EXPIRED                 => __( 'The entitlement has expired.', 'tribe-common' ),
			self::SUSPENDED               => __( 'The entitlement is suspended.', 'tribe-common' ),
			self::CANCELLED               => __( 'The entitlement is cancelled.', 'tribe-common' ),
			self::LICENSE_SUSPENDED       => __( 'The license is suspended.', 'tribe-common' ),
			self::LICENSE_BANNED          => __( 'The license is banned.', 'tribe-common' ),
			self::NO_ENTITLEMENT          => __( 'No entitlement exists for this product.', 'tribe-common' ),
			self::NOT_ACTIVATED           => __( 'The product is not activated on this domain.', 'tribe-common' ),
			self::OUT_OF_ACTIVATIONS      => __( 'All activation seats are in use.', 'tribe-common' ),
			self::INVALID_KEY             => __( 'The license key is not recognized.', 'tribe-common' ),
			self::ACTIVATION_REQUIRED     => __( 'The product requires activation.', 'tribe-common' ),
			self::TIER_SELECTION_REQUIRED => __( 'A tier selection is required for this product.', 'tribe-common' ),
		];

		return $messages[ $value ] ?? __( 'The license validation failed.', 'tribe-common' );
	}

	/**
	 * Maps a validation status to its corresponding Error_Code constant.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The validation status value.
	 *
	 * @return string An Error_Code constant value.
	 */
	public static function error_code( string $value ): string {
		$map = [
			self::EXPIRED                 => Error_Code::EXPIRED,
			self::SUSPENDED               => Error_Code::SUSPENDED,
			self::CANCELLED               => Error_Code::CANCELLED,
			self::LICENSE_SUSPENDED       => Error_Code::LICENSE_SUSPENDED,
			self::LICENSE_BANNED          => Error_Code::LICENSE_BANNED,
			self::NO_ENTITLEMENT          => Error_Code::NO_ENTITLEMENT,
			self::OUT_OF_ACTIVATIONS      => Error_Code::OUT_OF_ACTIVATIONS,
			self::INVALID_KEY             => Error_Code::INVALID_KEY,
			self::ACTIVATION_REQUIRED     => Error_Code::ACTIVATION_REQUIRED,
			self::TIER_SELECTION_REQUIRED => Error_Code::TIER_SELECTION_REQUIRED,
		];

		return $map[ $value ] ?? Error_Code::UNKNOWN_ERROR;
	}
}
