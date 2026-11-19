<?php
/**
 * Sanitize / validate callback registered with the WP Settings API for
 * the per-site `ed11y_plugin_settings` option.
 *
 * The actual per-key sanitize rules live on {@see FieldSanitizer} so the
 * network defaults form dispatches through the same registry. This file
 * owns the cross-key logic: test-state routing, the side-effect CSA
 * option write, and server-side enforcement of network locks.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

use Editoria11y\TestNames;

defined( 'ABSPATH' ) || exit;

/**
 * Static sanitize callback for `ed11y_plugin_settings`.
 *
 * Registered via `register_setting()` in
 * {@see SettingsPage::register_settings()}.
 */
class SettingsValidator {

	/**
	 * Synthetic lock key in the CSA network-defaults storage. When set,
	 * coerces a site's `tests_off` / `tests_content` / `tests_dev` and
	 * `roles` to the network values as a unit. Defined here so both the
	 * network form and the enforcement pass agree on the key name.
	 */
	const BUNDLE_LOCK_TESTS_AND_ROLES = 'tests_assignment_bundle';

	/**
	 * Per-site CSA keys coerced by {@see BUNDLE_LOCK_TESTS_AND_ROLES}.
	 */
	const BUNDLE_LOCK_TESTS_AND_ROLES_KEYS = array(
		'tests_off',
		'tests_content',
		'tests_dev',
		'roles',
	);

	/**
	 * Parent → child(ren) lock subordinations in the CSA blob.
	 *
	 * When a parent key is properly locked (lock flag + non-empty value),
	 * each subordinated child key is treated as locked too — even if its
	 * own value is empty. The pair below covers the
	 * `csa_dev_check_root_field` UI: one "Lock this default" checkbox on
	 * the parent radio covers the conditional `specify_root` textarea
	 * underneath it.
	 *
	 * Consulted by both {@see ed11y_effective_network_csa_lock()} (read
	 * pipeline) and {@see enforce_network_csa_locks()} (per-site save).
	 *
	 * @var array<string,array<int,string>>
	 */
	const CSA_LOCK_SUBORDINATIONS = array(
		'dev_check_root' => array( 'specify_root' ),
	);

	/**
	 * Sanitize the posted settings array; returns the value WordPress
	 * will write to `ed11y_plugin_settings`.
	 *
	 * Side effect: in CSA mode, also writes to `ed11y_csa_plugin_settings`
	 * — Drupal's `submitForm()` saves both configs as a unit, and we
	 * mirror that here.
	 *
	 * @param array $settings Raw settings as posted from the form.
	 * @return array Sanitized settings.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public static function validate( $settings ) {

		// Per-key sanitize via the shared registry — same rules whether
		// the POST came from the per-site form or the network defaults
		// form's per-site save replay.
		foreach ( FieldSanitizer::main_keys() as $key ) {
			if ( ! array_key_exists( $key, $settings ) ) {
				continue;
			}
			$settings[ $key ] = FieldSanitizer::sanitize_main( $key, $settings[ $key ] );
		}

		// Per-test enable/disable routing.
		//
		// Two branches picked by the CSA gate:
		//
		// - Free mode:
		// `tests_enabled[KEY]='1'` checkboxes → main `tests_off` CSV.
		// Developer-test entries already in `tests_off` (set during a
		// prior CSA-active session) pass through unchanged so a
		// trial-expired site doesn't silently re-enable every dev test.
		//
		// - CSA mode:
		// `tests_state[KEY]='nobody'|'developers'|'everyone'` → four CSVs
		// (main `tests_off` + CSA `tests_off` / `tests_content` /
		// `tests_dev`). See {@see TestStateNormalizer}.
		//
		// Production callers gate via `ed11y_is_csa_active()` (not Freemius
		// directly) so tests can simulate CSA via the
		// `ed11y_is_csa_active` filter.
		$handled_csa = false;

		// CSA-mode branch wrapped in the preprocessor gate so it strips
		// from the free build. The `$handled_csa` flag, rather than an
		// `if/else`, gates the free branch — Freemius's preprocessor only
		// removes the `is__premium_only()` block, so a sibling `else`
		// would be orphaned and parse-fail.

		if ( ! $handled_csa ) {
			// Only re-derive `tests_off` from checkbox state when the input is
			// actually form-shaped. Every real free-mode form POST carries the
			// `tests_enabled` array — even with all boxes unchecked — because
			// the renderer emits a hidden `__form` sentinel alongside the
			// checkbox group (checkboxes alone vanish from the POST when
			// unchecked). Input WITHOUT the array is not a form save: it is
			// this validator's own output (WP re-runs sanitize when
			// `update_option` falls through to `add_option`), a third-party
			// `update_option()` call, or a bundle-locked form whose disabled
			// checkboxes (and suppressed sentinel) never posted. Re-deriving
			// from an absent array used to rewrite `tests_off` to "every
			// content test off" — preserve the incoming/stored value instead.
			if ( isset( $settings['tests_enabled'] ) && is_array( $settings['tests_enabled'] ) ) {
				$existing_off          = ed11y_get_raw_setting( 'tests_off' );
				$settings['tests_off'] = TestStateNormalizer::from_free_post( $settings['tests_enabled'], $existing_off );
			} elseif ( ! array_key_exists( 'tests_off', $settings ) ) {
				$settings['tests_off'] = ed11y_get_raw_setting( 'tests_off' );
			}
		}

		// `tests_enabled`, `tests_state`, `csa_settings`, and
		// `csa_custom_rules` are UI artifacts for posting form state; do
		// not persist them into the main option row.
		unset(
			$settings['tests_enabled'],
			$settings['tests_state'],
			$settings['csa_settings'],
			$settings['csa_custom_rules']
		);

		// Server-side lock enforcement. The per-site form renders locked
		// fields as `disabled` (see {@see SettingsContext::field_disabled_attr()})
		// but that is a UX hint only — a hostile or scripted POST can
		// still include the locked key. Override any locked-key value
		// with the network-default value so the storage shape always
		// reflects the network admin's decision regardless of client
		// behavior.
		$settings = self::enforce_network_locks( $settings );

		// Reset cache.
		delete_transient( 'editoria11y_settings' );

		return $settings;
	}

	/**
	 * Coerce any locked-at-network keys back to their network-default value.
	 *
	 * Operates on the main-option array (the value WordPress is about to
	 * write to `ed11y_plugin_settings`). CSA option locks are applied
	 * separately in {@see enforce_network_csa_locks__premium_only()} so
	 * the two option lifecycles stay independent.
	 *
	 * @param array $settings Post-sanitize main settings.
	 * @return array Same array with locked keys overwritten.
	 */
	private static function enforce_network_locks( array $settings ): array {
		$network = ed11y_get_network_default_settings_storage();
		foreach ( $network['modes'] ?? array() as $key => $mode ) {
			if ( 'lock' !== $mode ) {
				continue;
			}
			if ( ! isset( $network['values'][ $key ] ) || empty( $network['values'][ $key ] ) ) {
				// Lock without value is inert — see {@see ed11y_is_setting_locked()}.
				continue;
			}
			$settings[ $key ] = $network['values'][ $key ];
		}

		// The bundle lock lives in the CSA blob's modes but covers the MAIN
		// blob's `tests_off` too — mirror {@see ed11y_is_setting_locked()},
		// which reports `tests_off` locked whenever the bundle is. Without
		// this, a forged or disabled-checkbox POST under a bundle lock could
		// leave stored `tests_off` diverged from the network decision, and
		// the divergence resurfaced the moment the bundle was unlocked.
		// Checked outside the loop above because the main blob's own modes
		// can be empty while the bundle is locked.
		if ( ed11y_is_bundle_locked() ) {
			$settings['tests_off'] = $network['values']['tests_off'] ?? '';
		}
		return $settings;
	}
}
