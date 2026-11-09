<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\Functions;

use TEC\Common\LiquidWeb\Harbor\Admin\Feature_Manager_Page;
use TEC\Common\LiquidWeb\Harbor\API\Functions\Actions\Display_Legacy_License_Page_Notice;
use TEC\Common\LiquidWeb\Harbor\API\Functions\Actions\Register_Submenu;
use TEC\Common\LiquidWeb\Harbor\Config;
use TEC\Common\LiquidWeb\Harbor\Features\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Features\Manager;
use TEC\Common\LiquidWeb\Harbor\Licensing\Repositories\License_Repository;
use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Repository;
use TEC\Common\LiquidWeb\Harbor\Site\Data;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use Throwable;
use WP_Error;

/**
 * Registers this Harbor instance's callbacks into the global function registry.
 *
 * Each vendor-prefixed Harbor instance calls register() during init, storing
 * version-keyed closures via _lw_harbor_global_function_registry(). The closures are defined
 * here (inside the namespaced file) so Strauss-prefixed class references
 * resolve correctly for this specific instance.
 *
 * @since 1.0.0
 */
class Global_Function_Registry {

	use With_Debugging;

	/**
	 * Missing-feature messages already logged on this request.
	 *
	 * @since 1.6.2
	 *
	 * @var array<string, true>
	 */
	private static $logged_missing_features = [];

	/**
	 * Registers this instance's callbacks into the global function registry.
	 *
	 * @since 1.0.0
	 * @since 1.6.2 Feature and catalog callbacks return their default without logging when Harbor is not loaded, and log a feature missing from the catalog once per request.
	 *
	 * @param string $version The version of this Harbor instance.
	 *
	 * @return void
	 */
	public static function register( string $version ): void {
		\_lw_harbor_global_function_registry(
			'lw_harbor_has_unified_license_key',
			$version,
			static function (): bool {
				try {
					return Config::get_container()->get( License_Repository::class )->key_exists();
				} catch ( Throwable $e ) {
					self::debug_log_throwable( $e, 'Error checking unified license key existence' );

					return false;
				}
			}
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_get_unified_license_key',
			$version,
			static function (): ?string {
				try {
					return Config::get_container()->get( License_Repository::class )->get_key();
				} catch ( Throwable $e ) {
					self::debug_log_throwable( $e, 'Error getting unified license key' );

					return null;
				}
			}
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_is_product_license_active',
			$version,
			static function ( string $product ): bool {
				try {
					return Config::get_container()->get( License_Repository::class )->is_product_valid( $product );
				} catch ( Throwable $e ) {
					self::debug_log_throwable( $e, 'Error checking product license' );

					return false;
				}
			}
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_is_feature_enabled',
			$version,
			static function ( string $slug ) {
				if ( ! self::is_loaded() ) {
					return false;
				}

				try {
					$result = Config::get_container()->get( Manager::class )->is_enabled( $slug );

					if ( is_wp_error( $result ) ) {
						self::log_feature_error( $result, 'Error checking feature enabled state' );

						return false;
					}

					return $result;
				} catch ( Throwable $e ) {
					self::debug_log_throwable( $e, 'Error checking feature enabled state' );

					return false;
				}
			}
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_is_feature_available',
			$version,
			static function ( string $slug ) {
				if ( ! self::is_loaded() ) {
					return false;
				}

				try {
					$result = Config::get_container()->get( Manager::class )->is_available( $slug );

					if ( is_wp_error( $result ) ) {
						self::log_feature_error( $result, 'Error checking feature availability' );

						return false;
					}

					return $result;
				} catch ( Throwable $e ) {
					self::debug_log_throwable( $e, 'Error checking feature availability' );

					return false;
				}
			}
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_get_license_page_url',
			$version,
			static function (): string {
				return admin_url( 'options-general.php?page=' . Feature_Manager_Page::PAGE_SLUG );
			}
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_display_legacy_license_page_notice',
			$version,
			new Display_Legacy_License_Page_Notice()
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_register_submenu',
			$version,
			new Register_Submenu()
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_get_licensed_domain',
			$version,
			static function (): string {
				try {
					return Config::get_container()->get( Data::class )->get_domain();
				} catch ( Throwable $e ) {
					self::debug_log_throwable( $e, 'Error getting site domain' );

					return '';
				}
			}
		);

		\_lw_harbor_global_function_registry(
			'lw_harbor_refresh_catalog',
			$version,
			static function (): bool {
				if ( ! self::is_loaded() ) {
					return false;
				}

				try {
					$result = Config::get_container()->get( Catalog_Repository::class )->refresh();

					if ( is_wp_error( $result ) ) {
						self::debug_log_wp_error( $result, 'Error refreshing catalog' );

						return false;
					}

					return true;
				} catch ( Throwable $e ) {
					self::debug_log_throwable( $e, 'Error refreshing catalog' );

					return false;
				}
			}
		);
	}

	/**
	 * Whether Harbor registered its providers on this request.
	 *
	 * Harbor::init() registers these callbacks for every host, but binds the
	 * Features and Portal services only when a premium plugin is present.
	 *
	 * @since 1.6.2
	 *
	 * @return bool
	 */
	private static function is_loaded(): bool {
		return did_action( 'lw_harbor/loaded' ) > 0;
	}

	/**
	 * Logs a feature lookup error.
	 *
	 * A host can ask about the same slug many times in one request. A slug missing
	 * from the catalog is logged the first time only, so a wrong slug or a stale
	 * catalog still shows up in the log without repeating on every check.
	 *
	 * @since 1.6.2
	 *
	 * @param WP_Error $error   The error returned by the feature manager.
	 * @param string   $context Short description of the failed check.
	 *
	 * @return void
	 */
	private static function log_feature_error( WP_Error $error, string $context ): void {
		if ( $error->get_error_code() === Error_Code::FEATURE_NOT_FOUND ) {
			$message = $error->get_error_message();

			if ( isset( self::$logged_missing_features[ $message ] ) ) {
				return;
			}

			self::$logged_missing_features[ $message ] = true;
		}

		self::debug_log_wp_error( $error, $context );
	}
}
