<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Strategy;

use TEC\Common\LiquidWeb\Harbor\Features\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Theme;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use WP_Error;
use WP_Ajax_Upgrader_Skin;
use Theme_Upgrader;

use function get_site_transient;
use function sanitize_key;
use function themes_api;
use function wp_get_theme;

/**
 * Theme Strategy — installs WordPress themes as "features".
 *
 * The shared enable/disable/update/is_active/ensure_installed flow is templated
 * by Installable_Strategy. This class provides the WP-specific hooks:
 * - do_install()     → themes_api() + Theme_Upgrader
 * - do_activate()    → no-op (theme is already installed)
 * - do_deactivate()  → returns error (theme files are never deleted)
 * - do_update()      → Theme_Upgrader::upgrade()
 *
 * A theme feature is active when the theme is installed on disk.
 * No DB option is stored — disk presence is the sole source of truth.
 * A theme feature is disabled if the theme is uninstalled (deleted from disk).
 *
 * @since 1.0.0
 */
class Theme_Strategy extends Installable_Strategy {

	/**
	 * @var Theme
	 */
	protected Feature $feature;

	// ── Abstract method implementations ─────────────────────────────────

	/**
	 * Check whether the theme is "active" — for themes, this means installed on disk.
	 *
	 * Unlike plugins where "active" means currently running, for themes "active"
	 * means the theme is installed and available for the user to activate through
	 * the WordPress UI.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check_active(): bool {
		return $this->check_installed();
	}

	/**
	 * Check whether the theme is installed on disk.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check_installed(): bool {
		return wp_get_theme( $this->feature->get_slug() )->exists();
	}

	/**
	 * Install the theme via themes_api() and Theme_Upgrader.
	 *
	 * Resolves the download link through themes_api(), which is expected to
	 * be filtered by the Features Provider to return catalog data for known
	 * feature slugs.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error
	 */
	protected function do_install() {
		static::debug_log(
			sprintf(
				'Fetching theme info for "%s" via themes_api().',
				$this->feature->get_slug()
			)
		);

		$theme_info = themes_api(
			'theme_information',
			[
				'slug'   => sanitize_key( $this->feature->get_slug() ),
				'fields' => [ 'sections' => false ],
			]
		);

		if ( is_wp_error( $theme_info ) ) {
			return new WP_Error(
				Error_Code::THEMES_API_FAILED,
				sprintf(
					/* translators: %1$s: feature name, %2$s: error message */
					__( 'Could not retrieve download information for "%1$s": %2$s', 'tribe-common' ),
					$this->feature->get_name(),
					$theme_info->get_error_message()
				)
			);
		}

		if ( empty( $theme_info->download_link ) ) {
			return new WP_Error(
				Error_Code::DOWNLOAD_LINK_MISSING,
				sprintf(
					/* translators: %s: feature name */
					__( 'No download link is available for "%s".', 'tribe-common' ),
					$this->feature->get_name()
				)
			);
		}

		$skin          = new WP_Ajax_Upgrader_Skin();
		$upgrader      = new Theme_Upgrader( $skin );
		$download_link = Cast::to_string( $theme_info->download_link );

		static::debug_log(
			sprintf(
				'Installing theme "%s" from %s.',
				$this->feature->get_slug(),
				$download_link
			)
		);

		return $this->run_upgrader(
			static function () use ( $upgrader, $download_link ) {
				return $upgrader->install( $download_link );
			},
			$skin,
			Error_Code::INSTALL_FAILED,
			false
		);
	}

	/**
	 * Activate the theme feature.
	 *
	 * Unlike plugins, themes are not activated through the feature system.
	 * The theme is installed and ready for the user to activate through
	 * WordPress's Appearance → Themes UI. No further action needed.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error
	 */
	protected function do_activate() {
		return true;
	}

	/**
	 * Deactivate the theme feature.
	 *
	 * If the theme is not on disk, it is already "disabled" — return success.
	 * If the theme IS on disk, we cannot programmatically delete it; the user
	 * must remove it themselves via Appearance → Themes.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error
	 */
	protected function do_deactivate() {
		if ( ! $this->check_installed() ) {
			static::debug_log(
				sprintf( 'Theme "%s" not installed, already disabled.', $this->feature->get_slug() )
			);

			return true;
		}

		static::debug_log(
			sprintf(
				'Theme "%s" is on disk, cannot deactivate programmatically.',
				$this->feature->get_slug()
			)
		);

		return new WP_Error(
			Error_Code::THEME_DELETE_REQUIRED,
			sprintf(
				/* translators: %s: theme name */
				__( 'The theme "%s" is installed on disk and cannot be deactivated programmatically. Please delete it manually via Appearance → Themes in the WordPress admin.', 'tribe-common' ),
				$this->feature->get_name()
			)
		);
	}

	/**
	 * Run the theme upgrade.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error
	 */
	protected function do_update() {
		static::debug_log(
			sprintf( 'Running theme upgrade for "%s".', $this->feature->get_slug() )
		);

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );
		$slug     = $this->feature->get_slug();

		return $this->run_upgrader(
			static function () use ( $upgrader, $slug ) {
				return $upgrader->upgrade( $slug );
			},
			$skin,
			Error_Code::UPDATE_FAILED,
			true
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function get_not_found_after_install_error_code(): string {
		return Error_Code::THEME_NOT_FOUND_AFTER_INSTALL;
	}

	/**
	 * Check whether an update is available for this theme.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check_update_available(): bool {
		$update_themes = get_site_transient( 'update_themes' );

		if ( ! is_object( $update_themes ) || empty( $update_themes->response ) || ! is_array( $update_themes->response ) ) {
			return false;
		}

		return isset( $update_themes->response[ $this->feature->get_slug() ] );
	}

	/**
	 * Load WordPress admin includes required for theme management.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function load_wp_admin_includes(): void {
		if ( ! function_exists( 'themes_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/theme.php';
		}

		if ( ! class_exists( 'Theme_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		if ( ! class_exists( 'WP_Ajax_Upgrader_Skin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		}

		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
	}
}
