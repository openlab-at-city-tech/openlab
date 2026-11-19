<?php
/**
 * Shared per-key sanitize registry for the Editoria11y settings forms.
 *
 * Both `SettingsValidator` (per-site form) and
 * `NetworkSettingsValidator` (network defaults form) dispatch through
 * here, so a key only ever has one sanitize rule no matter which form
 * posted it. Adding a new field means adding one entry to
 * {@see main_map()} or {@see csa_map()} — both forms pick it up
 * automatically and a field-coverage drift test asserts the maps stay
 * exhaustive.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Per-key sanitize dispatcher for the main + CSA option blobs.
 *
 * The `sanitize_*` helpers are invoked through `self::{$sanitizer}(...)`
 * dynamic dispatch driven by the key→sanitizer maps below. PHPMD's
 * static-analyzer cannot follow that pattern and flags the methods as
 * unused — they aren't.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
final class FieldSanitizer {

	/**
	 * Sanitize one main-option value by key.
	 *
	 * Unknown keys pass through untouched — callers that want strict
	 * filtering check membership in {@see main_keys()} themselves.
	 *
	 * @param string $key   Main-option setting key.
	 * @param mixed  $value Raw posted value.
	 * @return mixed Sanitized value (always a scalar for known keys).
	 */
	public static function sanitize_main( string $key, $value ) {
		$map = self::main_map();
		if ( ! isset( $map[ $key ] ) ) {
			return $value;
		}
		$sanitizer = $map[ $key ];
		return self::{$sanitizer}( $value );
	}

	/**
	 * Sanitize one CSA-option value by key.
	 *
	 * @param string $key   CSA-option setting key.
	 * @param mixed  $value Raw posted value.
	 * @return mixed Sanitized value.
	 */
	public static function sanitize_csa( string $key, $value ) {
		$map = self::csa_map();
		if ( ! isset( $map[ $key ] ) ) {
			return $value;
		}
		$sanitizer = $map[ $key ];
		return self::{$sanitizer}( $value );
	}

	/**
	 * Keys handled by {@see sanitize_main()}.
	 *
	 * @return array<int,string>
	 */
	public static function main_keys(): array {
		return array_keys( self::main_map() );
	}

	/**
	 * Keys handled by {@see sanitize_csa()}.
	 *
	 * @return array<int,string>
	 */
	public static function csa_keys(): array {
		return array_keys( self::csa_map() );
	}

	/**
	 * Main-option key → sanitizer-method name.
	 *
	 * @return array<string,string>
	 */
	public static function main_map(): array {
		return array(
			// Selects / single-line text.
			'ed11y_theme'               => 'sanitize_theme',
			'ed11y_alert_mode'          => 'sanitize_alert_mode',
			'ed11y_livecheck'           => 'sanitize_livecheck',
			'ed11y_checkvisibility'     => 'sanitize_checkvisibility',
			'panel_pin'                 => 'sanitize_panel_pin',
			'watch_for_changes'         => 'sanitize_watch_for_changes',

			// Textareas / selector lists.
			'ed11y_checkRoots'          => 'sanitize_text_strip_target',
			'ed11y_ignore_elements'     => 'sanitize_text_strip_basic',
			'ed11y_link_ignore_strings' => 'sanitize_text_strip_basic',
			// String storage (not int): kept as text-strip so existing live
			// values stay shape-compatible. Form input is `type="number"`.
			'ed11y_custom_tests'        => 'sanitize_text_strip_target',
			'ed11y_no_run'              => 'sanitize_text_strip_target',
			'ed11y_videoContent'        => 'sanitize_enum_chars',
			'ed11y_audioContent'        => 'sanitize_enum_chars',
			// Enum-char filter; the trailing sanitize_textarea_field in the
			// per-site validator was a no-op after this strict allowlist.
			'ed11y_documentContent'     => 'sanitize_enum_chars',
			'ed11y_datavizContent'      => 'sanitize_enum_chars',

			// Drupal-parity textareas.
			'hide_edit_links'           => 'sanitize_textarea',
			'panel_no_cover'            => 'sanitize_textarea',
			'element_hides_overflow'    => 'sanitize_textarea',
			'hidden_handlers'           => 'sanitize_textarea',
			'shadow_components'         => 'sanitize_textarea',
			'preserve_params'           => 'sanitize_textarea',
			'live_h2'                   => 'sanitize_textarea',
			'live_h3'                   => 'sanitize_textarea',
			'live_h4'                   => 'sanitize_textarea',
			'embedded_content_warning'  => 'sanitize_textarea',
			'link_ignore_selector'      => 'sanitize_textarea',
			'link_strings_new_windows'  => 'sanitize_textarea',
			'redundant_prefix'          => 'sanitize_text_field_wrap',

			// Booleans (storage = '1' / '').
			'ed11y_report_restrict'     => 'sanitize_bool',
			'ed11y_hide_report_link'    => 'sanitize_bool',
			'detect_shadow'             => 'sanitize_bool',
			'disable_sync'              => 'sanitize_bool',
		);
	}

	/**
	 * CSA-option key → sanitizer-method name.
	 *
	 * @return array<string,string>
	 */
	public static function csa_map(): array {
		return array(
			'tests_off'         => 'sanitize_csv_keys',
			'tests_content'     => 'sanitize_csv_keys',
			'tests_dev'         => 'sanitize_csv_keys',
			'dev_check_root'    => 'sanitize_dev_check_root',
			'specify_root'      => 'sanitize_textarea',
			'always_ignore'     => 'sanitize_textarea',
			'roles'             => 'sanitize_csv_keys',
			'dev_assertiveness' => 'sanitize_dev_assertiveness',
			'contrast_ignore'   => 'sanitize_textarea',
		);
	}

	/* === per-key sanitize primitives === */

	/**
	 * Strip script/HTML and `>` from a single-line text field.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_text_strip_target( $value ): string {
		$remove = array(
			'&lt;'     => '',
			'&apos;'   => '',
			'&amp;'    => '',
			'&percnt;' => '',
			'&#96;'    => '',
			'`'        => '',
			'&gt;'     => '',
			'>'        => '',
		);
		return strtr( sanitize_text_field( (string) $value ), $remove );
	}

	/**
	 * Like {@see sanitize_text_strip_target()} but keeps `>` (CSS combinators).
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_text_strip_basic( $value ): string {
		$remove = array(
			'&lt;'     => '',
			'&apos;'   => '',
			'&amp;'    => '',
			'&percnt;' => '',
			'&#96;'    => '',
			'`'        => '',
		);
		return strtr( sanitize_text_field( (string) $value ), $remove );
	}

	/**
	 * Restrict to `, . : <space> [A-Za-z0-9]` (enum-char allowlist).
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_enum_chars( $value ): string {
		$special_chars = '/[^.,:a-zA-Z0-9 ]/';
		return preg_replace( $special_chars, '', sanitize_text_field( (string) $value ) );
	}

	/**
	 * Multi-line textarea sanitize (preserves line breaks).
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_textarea( $value ): string {
		return sanitize_textarea_field( (string) $value );
	}

	/**
	 * Single-line text-field sanitize.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_text_field_wrap( $value ): string {
		return sanitize_text_field( (string) $value );
	}

	/**
	 * Checkbox storage shape: '1' / ''.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_bool( $value ): string {
		return ! empty( $value ) ? '1' : '';
	}

	/**
	 * Enum '', 'true', 'false' for `ed11y_checkvisibility`.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_checkvisibility( $value ): string {
		$choices = array( '', 'true', 'false' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : '';
	}

	/**
	 * Enum for the `ed11y_theme` select. Invalid values (only reachable by
	 * a forged/scripted POST — the form is a fixed-choice select) store ''
	 * so the read overlay falls back to the hardcoded default, matching
	 * the other fixed-choice enums instead of persisting arbitrary
	 * alphanumeric strings into the JS payload.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_theme( $value ): string {
		$choices = array( 'sleekTheme', 'lightTheme', 'darkTheme' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : '';
	}

	/**
	 * Enum for the `ed11y_alert_mode` select (library alertMode vocabulary).
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_alert_mode( $value ): string {
		$choices = array( 'polite', 'assertive', 'active', 'minimized' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : '';
	}

	/**
	 * Enum for the `ed11y_livecheck` select.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_livecheck( $value ): string {
		$choices = array( 'all', 'minimized', 'errors', 'none' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : '';
	}

	/**
	 * Enum 'right' / 'left' for the panel-pin radio.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_panel_pin( $value ): string {
		$choices = array( 'right', 'left' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : 'right';
	}

	/**
	 * Enum 'true' / 'checkRoots' / 'false' for `watch_for_changes`.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_watch_for_changes( $value ): string {
		$choices = array( 'true', 'checkRoots', 'false' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : 'checkRoots';
	}

	/**
	 * Enum 'automatic' / 'match' / 'specify' for CSA `dev_check_root`.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_dev_check_root( $value ): string {
		$choices = array( 'automatic', 'match', 'specify' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : 'automatic';
	}

	/**
	 * Enum for CSA `dev_assertiveness` — the developer-profile panel
	 * behavior, expressed in the library's alertMode vocabulary. Must match
	 * the `<option>` set the SettingsFields renderer emits; the old
	 * whitelist listed a `smart` value the form never offered while
	 * rejecting two values it did (`active`, `minimized`), silently
	 * coercing those choices to 'assertive' on save.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_dev_assertiveness( $value ): string {
		$choices = array( 'assertive', 'polite', 'active', 'minimized' );
		return in_array( (string) $value, $choices, true ) ? (string) $value : 'assertive';
	}

	/**
	 * Normalize a CSV of identifier-ish slugs (test keys, role slugs).
	 *
	 * Accepts either a CSV string or an array form (some inputs arrive
	 * as `roles[admin]=1` checkbox groups). Strips empties and
	 * non-identifier characters from each segment.
	 *
	 * @param mixed $value Posted value.
	 */
	private static function sanitize_csv_keys( $value ): string {
		if ( is_array( $value ) ) {
			$parts = array_keys( $value );
		} else {
			$parts = explode( ',', (string) $value );
		}
		$cleaned = array();
		foreach ( $parts as $part ) {
			$part = preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $part );
			if ( '' !== $part ) {
				$cleaned[] = $part;
			}
		}
		return implode( ',', array_values( array_unique( $cleaned ) ) );
	}
}
