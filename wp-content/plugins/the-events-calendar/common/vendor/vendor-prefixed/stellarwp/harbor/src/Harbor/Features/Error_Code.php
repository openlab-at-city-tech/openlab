<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features;

/**
 * WP_Error codes for the Features system.
 *
 * PHP 7.4 does not support native enums, so string
 * constants serve as the next-best compile-time guard.
 *
 * @since 1.0.0
 */
class Error_Code {

	/**
	 * A requested feature was not found in the catalog.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_NOT_FOUND = 'lw-harbor-feature-not-found';

	/**
	 * A feature check failed due to an unexpected error.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_CHECK_FAILED = 'lw-harbor-feature-check-failed';

	/**
	 * A feature request failed due to an unexpected error.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_REQUEST_FAILED = 'lw-harbor-feature-request-failed';

	/**
	 * The feature catalog response was invalid or could not be parsed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const INVALID_RESPONSE = 'lw-harbor-feature-invalid-response';

	/**
	 * A feature was passed to a strategy that does not support its type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_TYPE_MISMATCH = 'lw-harbor-feature-type-mismatch';

	/**
	 * Plugin deactivation did not take effect.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const DEACTIVATION_FAILED = 'lw-harbor-deactivation-failed';

	/**
	 * A concurrent install is already in progress for the same plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const INSTALL_LOCKED = 'lw-harbor-install-locked';

	/**
	 * The expected plugin file was not found on disk after installation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const PLUGIN_NOT_FOUND_AFTER_INSTALL = 'lw-harbor-plugin-not-found-after-install';

	/**
	 * The WordPress plugins_api() call failed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const PLUGINS_API_FAILED = 'lw-harbor-plugins-api-failed';

	/**
	 * No download link was returned by plugins_api() for the requested plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const DOWNLOAD_LINK_MISSING = 'lw-harbor-download-link-missing';

	/**
	 * The plugin installation failed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const INSTALL_FAILED = 'lw-harbor-install-failed';

	/**
	 * A fatal PHP error (Throwable) occurred during plugin activation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const ACTIVATION_FATAL = 'lw-harbor-activation-fatal';

	/**
	 * Plugin activation failed or did not take effect.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const ACTIVATION_FAILED = 'lw-harbor-activation-failed';

	/**
	 * The server's PHP or WordPress version does not meet the plugin's requirements.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const REQUIREMENTS_NOT_MET = 'lw-harbor-requirements-not-met';

	/**
	 * The active theme cannot be disabled (WordPress always needs an active theme).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const THEME_IS_ACTIVE = 'lw-harbor-theme-is-active';

	/**
	 * A theme feature cannot be deactivated programmatically — the user must delete it manually.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const THEME_DELETE_REQUIRED = 'lw-harbor-theme-delete-required';

	/**
	 * The expected theme was not found on disk after installation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const THEME_NOT_FOUND_AFTER_INSTALL = 'lw-harbor-theme-not-found-after-install';

	/**
	 * The WordPress themes_api() call failed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const THEMES_API_FAILED = 'lw-harbor-themes-api-failed';

	/**
	 * The feature is not currently active (installed and enabled).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_NOT_ACTIVE = 'lw-harbor-feature-not-active';

	/**
	 * The feature type does not support updates.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const UPDATE_NOT_SUPPORTED = 'lw-harbor-update-not-supported';

	/**
	 * No update is available for the feature.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const NO_UPDATE_AVAILABLE = 'lw-harbor-no-update-available';

	/**
	 * The feature update failed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const UPDATE_FAILED = 'lw-harbor-update-failed';

	/**
	 * A feature could not be enabled (strategy threw an exception).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_ENABLE_FAILED = 'lw-harbor-feature-enable-failed';

	/**
	 * A feature could not be disabled (strategy threw an exception).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_DISABLE_FAILED = 'lw-harbor-feature-disable-failed';

	/**
	 * A catalog feature has a type with no registered Feature subclass.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const UNKNOWN_FEATURE_TYPE = 'lw-harbor-unknown-feature-type';

	/**
	 * An attempt was made to enable, disable, or update a feature that does not
	 * support that operation from within WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const FEATURE_NOT_MODIFIABLE = 'lw-harbor-feature-not-modifiable';

	/**
	 * A feature is within the user's licensed tier but has been individually
	 * removed from the license capabilities — it cannot be enabled.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const CAPABILITY_REVOKED = 'lw-harbor-capability-revoked';

	/**
	 * An attempt was made to enable a feature that is covered by neither
	 * the unified license nor an active legacy license.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const NOT_LICENSED = 'lw-harbor-not-licensed';

	/**
	 * Maps an error code to its recommended HTTP status code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $code An Error_Code constant value.
	 *
	 * @return int The HTTP status code (defaults to 422 for unknown codes).
	 */
	public static function http_status( string $code ): int {
		/** @var array<string, int> */
		static $map = [
			// 400 Bad Request — the feature type cannot be handled by the resolved strategy.
			self::FEATURE_TYPE_MISMATCH          => 400,

			// 403 Forbidden — the feature is within the user's tier but its capability
			// has been individually removed from their license, or it is covered by
			// neither the unified license nor an active legacy license.
			self::CAPABILITY_REVOKED             => 403,
			self::NOT_LICENSED                   => 403,

			// 404 Not Found — the requested feature slug does not exist in the catalog.
			self::FEATURE_NOT_FOUND              => 404,

			// 409 Conflict — a concurrent install is in progress, a deactivation
			// was undone by another process, or the active theme cannot be disabled.
			self::INSTALL_LOCKED                 => 409,
			self::THEME_IS_ACTIVE                => 409,
			self::THEME_DELETE_REQUIRED          => 409,
			self::DEACTIVATION_FAILED            => 409,

			// 422 Unprocessable Entity — the request was understood but the operation
			// could not be completed (requirements not met, install/activation failure,
			// missing download link, unexpected package structure, enable/disable/update failure).
			self::FEATURE_NOT_ACTIVE             => 422,
			self::UPDATE_NOT_SUPPORTED           => 422,
			self::NO_UPDATE_AVAILABLE            => 422,
			self::UPDATE_FAILED                  => 422,
			self::FEATURE_ENABLE_FAILED          => 422,
			self::FEATURE_DISABLE_FAILED         => 422,
			self::REQUIREMENTS_NOT_MET           => 422,
			self::INSTALL_FAILED                 => 422,
			self::ACTIVATION_FATAL               => 422,
			self::ACTIVATION_FAILED              => 422,
			self::PLUGIN_NOT_FOUND_AFTER_INSTALL => 422,
			self::THEME_NOT_FOUND_AFTER_INSTALL  => 422,
			self::DOWNLOAD_LINK_MISSING          => 422,
			self::UNKNOWN_FEATURE_TYPE           => 422,
			self::FEATURE_NOT_MODIFIABLE         => 422,

			// 502 Bad Gateway — an upstream service (feature API, plugins_api) returned an error.
			self::INVALID_RESPONSE               => 502,
			self::FEATURE_CHECK_FAILED           => 502,
			self::FEATURE_REQUEST_FAILED         => 502,
			self::PLUGINS_API_FAILED             => 502,
			self::THEMES_API_FAILED              => 502,
		];

		// Default to 422 for unknown codes.
		return $map[ $code ] ?? 422;
	}
}
