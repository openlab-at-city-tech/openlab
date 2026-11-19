<?php
/**
 * Sanitize callback for the multisite Network Settings page.
 *
 * Dispatches per-key sanitize through {@see FieldSanitizer} and per-test
 * routing through {@see TestStateNormalizer} / {@see RoleNormalizer} so
 * the network and per-site forms can never drift on what's a valid value
 * for a given field.
 *
 * Targets the two network-level option storage blobs:
 *
 *   - `ed11y_network_default_settings`     (main / free settings)
 *   - `ed11y_network_default_csa_settings` (CSA settings)
 *
 * Both share the same shape:
 *
 *   array(
 *     'values' => array<string,mixed>,
 *     'modes'  => array<string,string>,  // 'new' | 'all' | 'lock'
 *   )
 *
 * Mode semantics:
 *   - 'new'  → seed into new sites only (no propagation to existing sites).
 *   - 'all'  → seed into new sites AND backfill into existing sites whose
 *              stored value is either absent, equal to the previous network
 *              value, or equal to the hardcoded default (see
 *              {@see NetworkDefaultsWorker}).
 *   - 'lock' → enforced at read time; site cannot override. The bundle lock
 *              {@see SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES} is a
 *              synthetic lock with no matching value.
 *
 * Form input shape after {@see NetworkSettingsPage::rewrite_field_names()}
 * has run over the buffered field markup:
 *
 *   free_values[KEY]               (per-key main-option value)
 *   csa_values[KEY]                (per-key CSA-option value)
 *   free_modes[KEY]   = "new|all|lock"
 *   csa_modes[KEY]    = "new|all|lock"
 *   network_tests_enabled[KEY]     (free-mode test selection)
 *   network_tests_state[KEY]       (CSA-mode 3-way test routing)
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Per-form sanitize for the network defaults form POSTs.
 */
class NetworkSettingsValidator {

	/**
	 * Sanitize the main-option half of the network defaults form.
	 *
	 * @param array $posted Raw POST.
	 * @return array{values: array<string,mixed>, modes: array<string,string>}
	 */
	public static function validate_free( array $posted ): array {
		$raw_values = isset( $posted['free_values'] ) && is_array( $posted['free_values'] )
			? $posted['free_values']
			: array();
		$raw_modes  = isset( $posted['free_modes'] ) && is_array( $posted['free_modes'] )
			? $posted['free_modes']
			: array();

		$values = self::sanitize_posted_values( $raw_values, FieldSanitizer::main_keys(), 'main' );

		// Free-mode test selection → tests_off default.
		$enabled_post = isset( $posted['network_tests_enabled'] ) && is_array( $posted['network_tests_enabled'] )
			? $posted['network_tests_enabled']
			: null;
		if ( null !== $enabled_post ) {
			// No "existing tests_off" to preserve at the network level — the
			// network blob is authored from scratch each save, not merged.
			$values['tests_off'] = TestStateNormalizer::from_free_post( $enabled_post, '' );
		}

		$modes = self::filter_modes( $raw_modes, $values, array() );

		return array(
			'values' => $values,
			'modes'  => $modes,
		);
	}

	/**
	 * Sanitize the CSA-option half of the network defaults form.
	 *
	 * The CSA blob allows one synthetic bundle lock
	 * ({@see SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES}) that has no
	 * matching `values[]` entry — the bundle locks
	 * tests_off/content/dev + roles together as a unit. All other lock
	 * entries still require a matching non-empty value (per the per-key
	 * "lock without value is inert" rule).
	 *
	 * @param array $posted Raw POST.
	 * @return array{values: array<string,mixed>, modes: array<string,string>}
	 */
	public static function validate_csa( array $posted ): array {
		$raw_values = isset( $posted['csa_values'] ) && is_array( $posted['csa_values'] )
			? $posted['csa_values']
			: array();
		$raw_modes  = isset( $posted['csa_modes'] ) && is_array( $posted['csa_modes'] )
			? $posted['csa_modes']
			: array();

		$values = self::sanitize_posted_values( $raw_values, FieldSanitizer::csa_keys(), 'csa' );

		// Roles needs role-existence validation, not just CSV cleanup.
		// Route through {@see RoleNormalizer} when the form actually posted
		// the field — otherwise the FieldSanitizer sanitize_csv_keys result
		// (already in $values) stands.
		if ( array_key_exists( 'roles', $raw_values ) ) {
			$values['roles'] = RoleNormalizer::normalize( $raw_values['roles'] );
		}

		// CSA-mode 3-way state → tests_off/content/dev defaults.
		$state_post = isset( $posted['network_tests_state'] ) && is_array( $posted['network_tests_state'] )
			? $posted['network_tests_state']
			: null;
		if ( null !== $state_post ) {
			$routed                  = TestStateNormalizer::from_csa_post( $state_post );
			$values['tests_off']     = $routed['csa_off'];
			$values['tests_content'] = $routed['csa_content'];
			$values['tests_dev']     = $routed['csa_dev'];
		}

		$modes = self::filter_modes(
			$raw_modes,
			$values,
			array( SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES )
		);

		return array(
			'values' => $values,
			'modes'  => $modes,
		);
	}

	/**
	 * Mirror a CSA-mode save's "nobody" content-test routing into the MAIN
	 * blob's `tests_off` (finding F4).
	 *
	 * The per-site validator does this inside apply_csa_routing()
	 * (`routed['main_off']`), but the network form in CSA mode renders
	 * 3-way selects — never the free-mode checkboxes — so validate_free()
	 * never authors the main blob's `tests_off`. Three consumers read
	 * exactly that key: the bundle-lock read overlay in
	 * ed11y_get_settings(), the bundle arm of
	 * SettingsValidator::enforce_network_locks(), and the worker's bundle
	 * seeding. Without the mirror, network "Off" routing never reaches
	 * free-mode/expired sites.
	 *
	 * No-op when the POST carries no `network_tests_state` (free-mode
	 * form: validate_free()'s own derivation stands).
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $free_blob Output of validate_free().
	 * @param array<string,mixed>                                             $posted    Raw POST.
	 * @return array{values: array<string,mixed>, modes: array<string,string>}
	 */
	public static function mirror_main_tests_off( array $free_blob, array $posted ): array {
		if ( ! self::is_csa_mode_post( $posted ) ) {
			return $free_blob;
		}
		$free_blob['values']['tests_off'] = TestStateNormalizer::from_csa_post( $posted['network_tests_state'] )['main_off'];
		return $free_blob;
	}

	/**
	 * Whether the POST came from the CSA-mode rendering of the network form.
	 *
	 * The CSA-only fields (developer scan area, roles, developer
	 * assertiveness, contrast ignore) and the 3-way test selects are all
	 * registered behind the same `ed11y_is_csa_active()` gate, so the
	 * `network_tests_state` array is present exactly when the CSA fields
	 * were on the page. The free build never renders any of them.
	 *
	 * @param array<string,mixed> $posted Raw POST.
	 */
	public static function is_csa_mode_post( array $posted ): bool {
		return isset( $posted['network_tests_state'] ) && is_array( $posted['network_tests_state'] );
	}

	/**
	 * Keep the stored CSA blob when the form that was saved never rendered
	 * the CSA fields.
	 *
	 * The network blobs are authored from scratch on every save, which is
	 * right for a form that showed every field — but a free-mode render
	 * (free build, or a CSA build whose license is not active on the
	 * network admin's blog) shows none of the CSA fields, so an
	 * unconditional write would wipe every CSA default and per-key lock
	 * the super-admin authored while licensed. The one CSA entry that
	 * free-mode form DOES render is the tests + roles bundle dropdown, so
	 * its mode is taken from the new blob.
	 *
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $old_csa  Previous CSA storage.
	 * @param array{values: array<string,mixed>, modes: array<string,string>} $csa_blob Output of validate_csa().
	 * @param array<string,mixed>                                             $posted   Raw POST.
	 * @return array{values: array<string,mixed>, modes: array<string,string>}
	 */
	public static function keep_csa_storage_for_free_form( array $old_csa, array $csa_blob, array $posted ): array {
		if ( self::is_csa_mode_post( $posted ) ) {
			return $csa_blob;
		}
		$bundle_key = SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES;
		$modes      = $old_csa['modes'];
		unset( $modes[ $bundle_key ] );
		if ( isset( $csa_blob['modes'][ $bundle_key ] ) ) {
			$modes[ $bundle_key ] = $csa_blob['modes'][ $bundle_key ];
		}
		return array(
			'values' => $old_csa['values'],
			'modes'  => $modes,
		);
	}

	/**
	 * Apply per-key sanitizers from the shared registry; drop unknown keys.
	 *
	 * @param array<string,mixed> $raw          Raw posted values.
	 * @param array<int,string>   $allowed_keys Keys accepted for this scope.
	 * @param string              $scope        'main' or 'csa'.
	 * @return array<string,mixed>
	 */
	private static function sanitize_posted_values( array $raw, array $allowed_keys, string $scope ): array {
		$out      = array();
		$dispatch = 'csa' === $scope ? 'sanitize_csa' : 'sanitize_main';
		foreach ( $allowed_keys as $key ) {
			if ( ! array_key_exists( $key, $raw ) ) {
				continue;
			}
			$out[ $key ] = FieldSanitizer::{$dispatch}( $key, $raw[ $key ] );
		}
		return $out;
	}

	/**
	 * Filter the raw `*_modes` POST map down to the modes we'll store.
	 *
	 * Per-key rules:
	 *   - Mode value must be one of `'new'`, `'all'`, `'lock'`. Anything else
	 *     drops the entry (the missing entry tells the seeder / backfill to
	 *     ignore that key).
	 *   - For non-bundle keys, the corresponding `$values[$key]` must be
	 *     present and non-empty. A mode with no matching value is meaningless
	 *     for `'new'` / `'all'` (nothing to propagate) and inert for `'lock'`
	 *     (see {@see ed11y_is_setting_locked()}), so we drop the entry to
	 *     keep the storage clean.
	 *   - Bundle keys (synthetic keys covering multiple real keys, e.g.
	 *     {@see SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES}) bypass the
	 *     value-check and accept all three modes — the bundle's own value
	 *     is synthetic, but the underlying keys' values live in `$values`
	 *     and are propagated/locked together via the bundle.
	 *   - The four CSA keys governed by the tests-and-roles bundle
	 *     ({@see SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS}) are
	 *     explicitly rejected at the per-key level — propagation for those
	 *     four is owned by the bundle entry, and accepting a per-key mode
	 *     would create two competing sources of truth.
	 *
	 * @param array<string,mixed> $raw_modes        Raw modes map from POST.
	 * @param array<string,mixed> $values           Sanitized values.
	 * @param array<int,string>   $bundle_lock_keys Synthetic bundle keys allowed independent of values.
	 * @return array<string,string>
	 */
	private static function filter_modes( array $raw_modes, array $values, array $bundle_lock_keys ): array {
		$allowed = array( 'new', 'all', 'lock' );
		$out     = array();
		foreach ( $raw_modes as $key => $mode ) {
			if ( ! is_string( $mode ) || ! in_array( $mode, $allowed, true ) ) {
				continue;
			}
			if ( in_array( $key, $bundle_lock_keys, true ) ) {
				$out[ $key ] = $mode;
				continue;
			}
			// CSA keys governed by the bundle are not allowed to carry a
			// per-key mode — the bundle entry above is the single source of
			// truth for their propagation.
			if ( in_array( $key, SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES_KEYS, true ) ) {
				continue;
			}
			if ( ! array_key_exists( $key, $values ) ) {
				continue;
			}
			if ( '' === $values[ $key ] || null === $values[ $key ] ) {
				continue;
			}
			$out[ $key ] = $mode;
		}
		return $out;
	}
}
