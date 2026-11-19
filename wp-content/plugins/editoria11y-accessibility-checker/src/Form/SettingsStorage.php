<?php
/**
 * Canonical settings-storage writer.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

/**
 * The single seam for writing already-canonical settings blobs.
 *
 * Two writers exist for `ed11y_plugin_settings`-shaped options:
 *
 *  1. The per-site form, via the WP Settings API — options.php runs the
 *     POST through {@see SettingsValidator::validate()} (hooked onto
 *     `sanitize_option_*` by {@see SettingsPage::register_settings()}).
 *     That is the desired path for form-shaped input.
 *  2. Programmatic writers (the network defaults seeder/backfill in
 *     {@see NetworkDefaultsWorker}, the install-time normalization and
 *     seeding in {@see \Editoria11y\Installer}) whose values are already
 *     canonical. These MUST NOT pass through the form validator: it
 *     expects form-shaped input (`tests_enabled` / `tests_state` UI
 *     sub-arrays) and re-derives `tests_off` from a possibly-absent
 *     `tests_enabled` — for a canonical blob that silently rewrites
 *     `tests_off` to "every content test is off"
 *     ({@see TestStateNormalizer::from_free_post()} with an empty post).
 *
 * The validator is attached whenever `admin_init` has run — which covers
 * more than form saves: site creation inside a network-admin request
 * (Network → Sites → Add new) and `Installer::check_tables()` triggered
 * from an admin page render both write settings after `admin_init`.
 * Every programmatic writer must therefore funnel through
 * {@see write_canonical()} instead of calling `update_option()` directly.
 */
final class SettingsStorage {

	/**
	 * `update_option()` that detaches the form's `sanitize_option_*`
	 * callback for the duration of the write, then restores it.
	 *
	 * Only the known {@see SettingsValidator::validate()} callback is
	 * detached — third-party filters on the option are left in place.
	 * No-op detach when the validator isn't attached (e.g. cron, WP-CLI,
	 * front-end requests, or options the form never registers).
	 *
	 * @param string              $option_name Option name on the current blog.
	 * @param array<string,mixed> $value       Already-canonical value to store.
	 */
	public static function write_canonical( string $option_name, array $value ): void {
		$filter_name = "sanitize_option_$option_name";
		$callback    = array( SettingsValidator::class, 'validate' );
		$priority    = has_filter( $filter_name, $callback );
		if ( false !== $priority ) {
			remove_filter( $filter_name, $callback, (int) $priority );
		}
		try {
			update_option( $option_name, $value );
		} finally {
			if ( false !== $priority ) {
				add_filter( $filter_name, $callback, (int) $priority );
			}
		}
	}
}
