<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Strategy;

use TEC\Common\LiquidWeb\Harbor\Features\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Plugin;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use WP_Error;
use Throwable;
use WP_Ajax_Upgrader_Skin;
use Plugin_Upgrader;

use function activate_plugin;
use function deactivate_plugins;
use function is_plugin_active;
use function is_plugin_active_for_network;
use function plugins_api;
use function get_site_transient;
use function rest_convert_error_to_response;
use function sanitize_key;
use function wp_json_encode;

/**
 * Plugin Strategy — installs, activates, and deactivates WordPress plugins as
 * "features" using ZIP file downloads.
 *
 * The shared enable/disable/update/is_active/ensure_installed flow is templated
 * by Installable_Strategy. This class provides the WP-specific hooks:
 * - do_install()     → plugins_api() + Plugin_Upgrader
 * - do_activate()    → activate_plugin() with fatal error protection
 * - do_deactivate()  → deactivate_plugins() + verification
 * - do_update()      → Plugin_Upgrader::upgrade()
 *
 * A plugin feature is active when WordPress reports the plugin as active.
 * A plugin feature is disabled if the plugin is deactivated or uninstalled.
 *
 * @since 1.0.0
 */
class Plugin_Strategy extends Installable_Strategy {

	/**
	 * @var Plugin
	 */
	protected Feature $feature;

	/**
	 * WordPress error codes that indicate PHP or WP version requirements are not met.
	 *
	 * Install path: emitted by Plugin_Upgrader::check_package() and captured by the skin.
	 * Activation path: returned directly by activate_plugin() via validate_plugin_requirements().
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	private const REQUIREMENTS_ERROR_CODES = [
		'incompatible_php_required_version',
		'incompatible_wp_required_version',
		'plugin_php_incompatible',
		'plugin_wp_incompatible',
		'plugin_wp_php_incompatible',
	];

	// ── Abstract method implementations ─────────────────────────────────

	/**
	 * Check whether the plugin is currently active in WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check_active(): bool {
		$plugin_file = $this->feature->get_plugin_file();

		return is_plugin_active( $plugin_file )
			|| is_plugin_active_for_network( $plugin_file );
	}

	/**
	 * Check whether the plugin is installed on disk.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check_installed(): bool {
		return file_exists( WP_PLUGIN_DIR . '/' . $this->feature->get_plugin_file() );
	}

	/**
	 * Install the plugin via plugins_api() and Plugin_Upgrader.
	 *
	 * Resolves the download link through plugins_api(), which is expected to
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
				'Fetching plugin info for "%s" via plugins_api().',
				$this->feature->get_slug()
			)
		);

		$plugin_info = plugins_api(
			'plugin_information',
			[
				'slug'   => sanitize_key( $this->feature->get_slug() ),
				'fields' => [ 'sections' => false ],
			]
		);

		if ( is_wp_error( $plugin_info ) ) {
			return new WP_Error(
				Error_Code::PLUGINS_API_FAILED,
				sprintf(
					/* translators: %1$s: feature name, %2$s: error message */
					__( 'Could not retrieve download information for "%1$s": %2$s', 'tribe-common' ),
					$this->feature->get_name(),
					$plugin_info->get_error_message()
				)
			);
		}

		if ( empty( $plugin_info->download_link ) ) {
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
		$upgrader      = new Plugin_Upgrader( $skin );
		$download_link = Cast::to_string( $plugin_info->download_link );

		static::debug_log(
			sprintf(
				'Installing plugin "%s" from %s.',
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
	 * Activate the plugin with fatal error protection.
	 *
	 * Uses try/catch Throwable to catch PHP Error subclasses
	 * (ParseError, TypeError, etc.) and a shutdown function with output
	 * buffering to handle die()/exit() calls during plugin activation.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error
	 */
	protected function do_activate() {
		static::debug_log(
			sprintf(
				'Activating plugin "%s" (%s).',
				$this->feature->get_slug(),
				$this->feature->get_plugin_file()
			)
		);

		$plugin_file = $this->feature->get_plugin_file();
		$completed   = false;
		$die_output  = '';

		// Register a shutdown function to handle die()/exit() during activation.
		// PHP runs shutdown functions after die(), so we can capture the output
		// and send a proper JSON error response instead of raw text.
		register_shutdown_function(
			static function () use ( $plugin_file, &$completed, &$die_output ) {
				if ( $completed ) { // @phpstan-ignore-line booleanNot.alwaysFalse -- completed is set to true in the try block.
					return;
				}

				// Pick up anything from buffers that wp_ob_end_flush_all didn't reach.
				while ( ob_get_level() > 0 ) {
					$die_output .= ob_get_clean() ?: '';
				}

				$error = new WP_Error(
					Error_Code::ACTIVATION_FATAL,
					sprintf(
						/* translators: %s: plugin file path */
						__( 'The plugin "%s" called exit/die during activation and terminated the process.', 'tribe-common' ),
						$plugin_file
					),
					[ 'status' => 422 ]
				);

				if ( $die_output !== '' ) {
					$error->add(
						Error_Code::ACTIVATION_FATAL,
						substr( $die_output, 0, 500 )
					);
				}

				$response = rest_convert_error_to_response( $error );

				if ( ! headers_sent() ) {
					http_response_code( $response->get_status() );
					header( 'Content-Type: application/json; charset=UTF-8' );
				}

				echo wp_json_encode( $response->get_data() );
			}
		);

		// Start output buffering with a callback to intercept die() output.
		// WordPress registers wp_ob_end_flush_all as a shutdown function
		// (before ours), which flushes all buffer levels. Without a callback,
		// the die() output would reach the client before our shutdown function
		// runs. The callback intercepts the flush, captures the output, and
		// returns '' to suppress it.
		$ob_level_before = ob_get_level();

		ob_start(
			static function ( $buffer ) use ( &$completed, &$die_output ) {
				if ( $completed ) { // @phpstan-ignore-line booleanNot.alwaysFalse -- completed is set to true in the try block.
					return $buffer;
				}

				$die_output .= Cast::to_string( $buffer );

				return '';
			}
		);

		try {
			$result = activate_plugin( $plugin_file );
		} catch ( Throwable $e ) {
			$completed = true;

			// Clean any buffers added during activation (e.g. WordPress's own
			// ob_start inside activate_plugin()) plus our own buffer.
			while ( ob_get_level() > $ob_level_before ) {
				ob_end_clean();
			}

			static::debug_log(
				sprintf(
					'Fatal error activating plugin "%s": %s %s:%s',
					$this->feature->get_slug(),
					$e->getMessage(),
					$e->getFile(),
					$e->getLine()
				)
			);

			return new WP_Error(
				Error_Code::ACTIVATION_FATAL,
				sprintf(
					/* translators: %s: feature name */
					__( 'A fatal error occurred while activating "%s".', 'tribe-common' ),
					$this->feature->get_name()
				)
			);
		}

		$completed = true;

		// Clean any buffers added during activation plus our own buffer.
		while ( ob_get_level() > $ob_level_before ) {
			ob_end_clean();
		}

		if ( is_wp_error( $result ) ) {
			$error_code = in_array( $result->get_error_code(), self::REQUIREMENTS_ERROR_CODES, true )
				? Error_Code::REQUIREMENTS_NOT_MET
				: Error_Code::ACTIVATION_FAILED;

			return new WP_Error(
				$error_code,
				sprintf(
					/* translators: %1$s: feature name, %2$s: error message */
					__( 'Activation of "%1$s" failed: %2$s', 'tribe-common' ),
					$this->feature->get_name(),
					wp_strip_all_tags( $result->get_error_message() )
				)
			);
		}

		if ( ! $this->check_active() ) {
			return new WP_Error(
				Error_Code::ACTIVATION_FAILED,
				sprintf(
					/* translators: %s: feature name */
					__( '"%s" did not activate successfully. Please try again.', 'tribe-common' ),
					$this->feature->get_name()
				)
			);
		}

		return true;
	}

	/**
	 * Deactivate the plugin.
	 *
	 * Never deletes plugin files — deactivation is safe and reversible.
	 * Idempotent: returns true if the plugin is already inactive.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error
	 */
	protected function do_deactivate() {
		$plugin_file = $this->feature->get_plugin_file();

		// Idempotent: if already inactive, bail.
		if ( ! $this->check_active() ) {
			static::debug_log(
				sprintf( 'Plugin "%s" already inactive.', $this->feature->get_slug() )
			);

			return true;
		}

		static::debug_log(
			sprintf(
				'Deactivating plugin "%s" (%s).',
				$this->feature->get_slug(),
				$plugin_file
			)
		);

		// deactivate_plugins() returns void — it never errors. We verify the
		// actual state afterward to confirm deactivation succeeded.
		deactivate_plugins( $plugin_file, false, is_plugin_active_for_network( $plugin_file ) );

		// Verify the plugin is actually inactive now. This catches edge cases
		// where a deactivation hook re-activates the plugin or WordPress's
		// plugin state is otherwise inconsistent.
		// @phpstan-ignore-next-line if.alwaysTrue -- (deactivate_plugins() changes active state via DB side effects invisible to static analysis).
		if ( $this->check_active() ) {
			return new WP_Error(
				Error_Code::DEACTIVATION_FAILED,
				sprintf(
					/* translators: %s: feature name */
					__( '"%s" could not be deactivated. The plugin may have been reactivated by another process.', 'tribe-common' ),
					$this->feature->get_name()
				)
			);
		}

		return true; // @phpstan-ignore deadCode.unreachable (The check above is a double check)
	}

	/**
	 * Run the plugin upgrade.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error
	 */
	protected function do_update() {
		static::debug_log(
			sprintf( 'Running plugin upgrade for "%s".', $this->feature->get_slug() )
		);

		$skin        = new WP_Ajax_Upgrader_Skin();
		$upgrader    = new Plugin_Upgrader( $skin );
		$plugin_file = $this->feature->get_plugin_file();

		return $this->run_upgrader(
			static function () use ( $upgrader, $plugin_file ) {
				return $upgrader->upgrade( $plugin_file );
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
		return Error_Code::PLUGIN_NOT_FOUND_AFTER_INSTALL;
	}

	/**
	 * @inheritDoc
	 */
	protected function get_requirements_error_codes(): array {
		return self::REQUIREMENTS_ERROR_CODES;
	}

	/**
	 * Check whether an update is available for this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check_update_available(): bool {
		$update_plugins = get_site_transient( 'update_plugins' );

		if ( ! is_object( $update_plugins ) || empty( $update_plugins->response ) || ! is_array( $update_plugins->response ) ) {
			return false;
		}

		return isset( $update_plugins->response[ $this->feature->get_plugin_file() ] );
	}

	/**
	 * Load WordPress admin includes required for plugin management.
	 *
	 * These files may not be loaded in REST API or AJAX contexts, but are
	 * needed for is_plugin_active(), activate_plugin(), deactivate_plugins(),
	 * and Plugin_Upgrader.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function load_wp_admin_includes(): void {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		if ( ! class_exists( 'Plugin_Upgrader' ) ) {
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
