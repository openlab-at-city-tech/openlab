<?php
/**
 * Convert the settings-form test-routing POST shapes into the canonical
 * CSV strings the storage uses.
 *
 * Per-site and network-defaults forms both render the same per-test
 * widgets (free: checkbox, CSA: 3-way select) and both need to translate
 * the resulting POST into:
 *
 *   - `tests_off` CSV (main option) — content tests routed off.
 *   - `tests_off` CSV (CSA option)  — every test routed to nobody.
 *   - `tests_content` CSV            — tests visible to all roles.
 *   - `tests_dev` CSV                — tests visible only to developer roles.
 *
 * Keeping the conversion here means {@see SettingsValidator} and
 * {@see NetworkSettingsValidator} can't drift; one set of routing
 * rules, two callers.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

use Editoria11y\TestNames;

defined( 'ABSPATH' ) || exit;

/**
 * Stateless converter for the per-test routing POST shapes.
 */
final class TestStateNormalizer {

	/**
	 * Convert a CSA-mode `tests_state` POST into the four CSV strings.
	 *
	 * Form input is `tests_state[KEY] = 'nobody' | 'developers' | 'everyone'`.
	 * Missing or unknown values are not classified (treated as unset).
	 *
	 * @param array<string,string> $tests_state Raw `tests_state` POST sub-array.
	 * @return array{main_off:string, csa_off:string, csa_content:string, csa_dev:string}
	 */
	public static function from_csa_post( array $tests_state ): array {
		$content_tests = TestNames::content_tests();
		$csa_off       = array();
		$csa_content   = array();
		$csa_dev       = array();
		$main_off      = array();

		foreach ( array_keys( TestNames::core_names() ) as $key ) {
			$state = isset( $tests_state[ $key ] ) ? (string) $tests_state[ $key ] : '';
			if ( 'developers' === $state ) {
				$csa_dev[] = $key;
			} elseif ( 'everyone' === $state ) {
				$csa_content[] = $key;
			} elseif ( 'nobody' === $state ) {
				$csa_off[] = $key;
				if ( in_array( $key, $content_tests, true ) ) {
					// Mirror "off" content tests into the main option so a
					// CSA-expired site preserves the admin's intent.
					$main_off[] = $key;
				}
			}
		}

		return array(
			'main_off'    => implode( ',', $main_off ),
			'csa_off'     => implode( ',', $csa_off ),
			'csa_content' => implode( ',', $csa_content ),
			'csa_dev'     => implode( ',', $csa_dev ),
		);
	}

	/**
	 * The four CSV strings an UNTOUCHED CSA-mode form posts.
	 *
	 * The 3-way selects have no blank option, so a never-authored form
	 * still posts a state for every test: the renderer's default routing
	 * (content tests → everyone, everything else → developers). Callers
	 * that need to tell "the admin routed tests" apart from "the form
	 * posted its defaults" — the network orphan gate in
	 * {@see NetworkDefaultsWorker::detect_orphan_changed_keys()} — compare
	 * against this instead of the stored defaults, which are all empty.
	 *
	 * @return array{main_off:string, csa_off:string, csa_content:string, csa_dev:string}
	 */
	public static function default_csa_routing(): array {
		$content_tests = TestNames::content_tests();
		$state         = array();
		foreach ( array_keys( TestNames::core_names() ) as $key ) {
			$state[ $key ] = in_array( $key, $content_tests, true ) ? 'everyone' : 'developers';
		}
		return self::from_csa_post( $state );
	}

	/**
	 * Convert a free-mode `tests_enabled` POST into the main `tests_off` CSV.
	 *
	 * Inverted-polarity semantics: checked = enabled, unchecked = in
	 * `tests_off`. Dev-test entries in the pre-existing CSV pass through
	 * unchanged so a trial-expired site doesn't silently re-enable every
	 * developer test on the next free-mode save.
	 *
	 * @param array<string,mixed> $tests_enabled    Raw `tests_enabled` POST.
	 * @param string              $existing_off_csv Current `tests_off` value.
	 * @return string New `tests_off` CSV.
	 */
	public static function from_free_post( array $tests_enabled, string $existing_off_csv ): string {
		$content_tests = TestNames::content_tests();
		$enabled_keys  = array_keys( $tests_enabled );
		$existing_arr  = '' === $existing_off_csv ? array() : explode( ',', $existing_off_csv );

		// Preserve dev-test entries (non-content) and any other pass-through keys.
		$preserved = array_filter(
			$existing_arr,
			static function ( $key ) use ( $content_tests ) {
				return ! in_array( $key, $content_tests, true );
			}
		);

		// Add content tests whose checkbox arrived unchecked.
		foreach ( $content_tests as $key ) {
			if ( ! in_array( $key, $enabled_keys, true ) ) {
				$preserved[] = $key;
			}
		}

		return implode( ',', array_values( array_unique( $preserved ) ) );
	}
}
