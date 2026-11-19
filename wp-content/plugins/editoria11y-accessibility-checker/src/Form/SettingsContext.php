<?php
/**
 * Settings-page rendering context — a tiny static stack that lets the
 * shared `SettingsFields` renderers serve both the per-site Settings →
 * Editoria11y screen and the multisite Network → Editoria11y screen.
 *
 * Each field renderer needs three things at render time:
 *   - the current stored value for the input it is rendering,
 *   - whether to add `disabled` to the input (locked-at-network state,
 *     only meaningful in `site` context),
 *   - an optional "Managed at network level" note to print near the
 *     input (same gating).
 *
 * Rather than passing a context flag through every renderer's signature
 * (the Settings API doesn't make that easy), we keep the context on a
 * small stack here. The site page pushes nothing (`site` is the
 * default); the network page pushes `network` immediately before
 * `do_settings_sections` and pops immediately after. Tests use the same
 * push/pop API.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Rendering-context indirection for the Editoria11y settings forms.
 */
final class SettingsContext {

	/**
	 * Stack of contexts. Always non-empty; the top is the current
	 * context. Default `'site'` is seeded lazily so a fresh request
	 * never observes an empty stack.
	 *
	 * @var array<int, string>
	 */
	private static $stack = array();

	/**
	 * Push a new context onto the stack.
	 *
	 * @param string $context Either 'site' or 'network'.
	 */
	public static function push( string $context ): void {
		if ( ! in_array( $context, array( 'site', 'network' ), true ) ) {
			$context = 'site';
		}
		self::$stack[] = $context;
	}

	/**
	 * Pop the top context off the stack.
	 *
	 * No-op when the stack is at its implicit `site` baseline; this
	 * makes mismatched push/pop calls safe rather than fataling.
	 */
	public static function pop(): void {
		if ( ! empty( self::$stack ) ) {
			array_pop( self::$stack );
		}
	}

	/**
	 * Clear the stack entirely (test-fixture hook only).
	 */
	public static function reset(): void {
		self::$stack = array();
	}

	/**
	 * Current context — `'site'` or `'network'`.
	 */
	public static function current(): string {
		if ( empty( self::$stack ) ) {
			return 'site';
		}
		return end( self::$stack );
	}

	/** Whether we are rendering the network defaults page. */
	public static function is_network(): bool {
		return 'network' === self::current();
	}

	/**
	 * Raw stored value for a main-option setting, in the current context.
	 *
	 * In `site` context: behaves exactly like `ed11y_get_raw_setting()`.
	 * In `network` context: returns the raw stored network default.
	 *
	 * @param string $key Setting key.
	 */
	public static function get_raw_setting( string $key ): string {
		if ( self::is_network() ) {
			return ed11y_get_network_default_setting( $key );
		}
		return ed11y_get_raw_setting( $key );
	}

	/**
	 * Raw stored value for a CSA-option setting, in the current context.
	 *
	 * @param string $key CSA setting key.
	 */
	public static function get_csa_raw_setting( string $key ): string {
		if ( self::is_network() ) {
			return ed11y_get_network_default_csa_setting( $key );
		}
		return ed11y_get_csa_raw_setting( $key );
	}

	/**
	 * Pre-selection value for a CSA "choice" field — a radio group or
	 * `<select>` that always needs exactly one option marked selected, so it
	 * cannot render a meaningful "blank" state the way a text field can.
	 *
	 * In `network` context: the raw stored network default, falling back to
	 * the hardcoded module default when the key has never been authored. This
	 * is what makes a never-configured choice field pre-select the module
	 * default — mirroring the per-site page — while still reading the
	 * *network* blob once a default has been saved. (Reading
	 * {@see ed11y_get_csa_setting()} here would instead surface the network
	 * admin's *current blog* value, so a saved network default would be
	 * invisible in the UI and re-saving would silently seed the wrong value.)
	 *
	 * In `site` context: the effective per-site value, exactly as
	 * {@see ed11y_get_csa_setting()} returns it (including the network-lock
	 * overlay), so a locked field still shows the enforced network value.
	 *
	 * Text / textarea fields do NOT need this — they read
	 * {@see get_csa_raw_setting()} and let an empty value reveal the
	 * placeholder default. Use this only for radios / selects.
	 *
	 * @param string $key CSA setting key.
	 * @return mixed Stored/effective value used to pre-select an option.
	 */
	public static function get_csa_choice_setting( string $key ) {
		if ( self::is_network() ) {
			$raw = ed11y_get_network_default_csa_setting( $key );
			return '' !== $raw ? $raw : ed11y_get_csa_default_options( $key );
		}
		return ed11y_get_csa_setting( $key );
	}

	/**
	 * Attribute string to splice into an `<input>` / `<select>` /
	 * `<textarea>` opening tag: ` disabled` when the key is locked at
	 * network level AND we're in `site` context, otherwise empty.
	 *
	 * Leading space is intentional so callers can safely interpolate
	 * the value mid-tag without managing whitespace themselves.
	 *
	 * @param string $key Setting key.
	 */
	public static function field_disabled_attr( string $key ): string {
		if ( self::is_network() ) {
			return '';
		}
		return ed11y_is_setting_locked( $key ) ? ' disabled' : '';
	}

	/**
	 * Attribute helper for CSA fields. See {@see field_disabled_attr()}.
	 *
	 * @param string $key CSA setting key.
	 */
	public static function csa_field_disabled_attr( string $key ): string {
		if ( self::is_network() ) {
			return '';
		}
		return ed11y_is_csa_setting_locked( $key ) ? ' disabled' : '';
	}

	/**
	 * Echo a small "Managed at network level" note when the named key
	 * is locked at network level and we're rendering for a site admin.
	 *
	 * @param string $key Setting key.
	 */
	public static function print_lock_note( string $key ): void {
		if ( self::is_network() ) {
			return;
		}
		if ( ! ed11y_is_setting_locked( $key ) ) {
			return;
		}
		self::echo_lock_note();
	}

	/**
	 * CSA-side counterpart to {@see print_lock_note()}.
	 *
	 * @param string $key CSA setting key.
	 */
	public static function print_csa_lock_note( string $key ): void {
		if ( self::is_network() ) {
			return;
		}
		if ( ! ed11y_is_csa_setting_locked( $key ) ) {
			return;
		}
		self::echo_lock_note();
	}

	/**
	 * Shared note markup so the wording can evolve in one place.
	 */
	private static function echo_lock_note(): void {
		?>
		<p class="ed11y-locked-by-network">
			<strong>🔒  <?php esc_html_e( 'Managed at the network level', 'editoria11y' ); ?></strong>
		</p>
		<?php
	}

	/**
	 * Emit the per-field "Default on…" mode dropdown shown on the
	 * network defaults page; no-op in site context.
	 *
	 * The select name is `free_modes[<key>]` so the network form
	 * handler's POST shape is symmetric with `free_values[<key>]`.
	 *
	 * @param string $key Main-option setting key.
	 */
	public static function print_mode_dropdown( string $key ): void {
		if ( ! self::is_network() ) {
			return;
		}
		$storage = ed11y_get_network_default_settings_storage();
		$current = is_string( $storage['modes'][ $key ] ?? null ) ? $storage['modes'][ $key ] : '';
		self::echo_mode_dropdown( 'free_modes', $key, $current );
	}

	/**
	 * CSA-side counterpart to {@see print_mode_dropdown()}.
	 *
	 * @param string $key CSA-option setting key.
	 */
	public static function print_csa_mode_dropdown( string $key ): void {
		if ( ! self::is_network() ) {
			return;
		}
		$storage = ed11y_get_network_default_csa_settings_storage();
		$current = is_string( $storage['modes'][ $key ] ?? null ) ? $storage['modes'][ $key ] : '';
		self::echo_mode_dropdown( 'csa_modes', $key, $current );
	}

	/**
	 * Emit the bundle propagation-mode dropdown covering the per-test
	 * selection (`tests_off` / `tests_content` / `tests_dev`) + `roles`
	 * as a unit. CSA-side only — these four keys are stored in the CSA
	 * option in CSA mode.
	 *
	 * The dropdown matches the three-way shape of per-key dropdowns:
	 *
	 *   - `'new'`  → seed all four into new sites at creation.
	 *   - `'all'`  → seed at creation AND backfill into existing sites
	 *                whose stored values are still tracking the network
	 *                (see {@see NetworkDefaultsWorker}).
	 *   - `'lock'` → enforce all four at read time; sites cannot override.
	 *   - `''`     → "no network default" — the four values are stored
	 *                but never propagate. Authoring path for "remove the
	 *                bundle default entirely."
	 *
	 * The bundle entry is the single source of truth for these four
	 * keys' propagation; per-key modes for them are rejected by the
	 * network validator ({@see NetworkSettingsValidator::filter_modes()}).
	 *
	 * @param string $bundle_key Synthetic bundle key
	 *                           ({@see SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES}).
	 * @param string $label      Translated label rendered above the dropdown.
	 */
	public static function print_bundle_mode_dropdown( string $bundle_key, string $label ): void {
		if ( ! self::is_network() ) {
			return;
		}
		$storage = ed11y_get_network_default_csa_settings_storage();
		$current = is_string( $storage['modes'][ $bundle_key ] ?? null ) ? $storage['modes'][ $bundle_key ] : '';
		$options = array(
			''     => __( '— No network default —', 'editoria11y' ),
			'new'  => __( 'Default for new sites', 'editoria11y' ),
			'all'  => __( 'Default for all sites (skip custom values)', 'editoria11y' ),
			'lock' => __( 'Override for all sites (delete custom values)', 'editoria11y' ),
		);
		?>
		<p class="ed11y-network-mode ed11y-bundle-mode">
			<label>
				<strong><?php echo esc_html( $label ); ?>:</strong>
				<select name="csa_modes[<?php echo esc_attr( $bundle_key ); ?>]">
					<?php foreach ( $options as $value => $option_label ) : ?>
						<option
							value="<?php echo esc_attr( $value ); ?>"
							<?php selected( $current, $value ); ?>
						><?php echo esc_html( $option_label ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<?php
	}

	/**
	 * Shared mode-dropdown markup so the wording stays in one place.
	 *
	 * An empty `$current` selects the implicit "no network default" option
	 * (no entry in `modes[]`), which the validator drops; that is the
	 * authoring path for "remove this network default entirely."
	 *
	 * @param string $name_prefix `free_modes` or `csa_modes`.
	 * @param string $key         Setting key.
	 * @param string $current     Currently stored mode (`''`, `'new'`, `'all'`, `'lock'`).
	 */
	private static function echo_mode_dropdown( string $name_prefix, string $key, string $current ): void {
		$options = array(
			''     => __( '— No network default —', 'editoria11y' ),
			'new'  => __( 'Default for new sites', 'editoria11y' ),
			'all'  => __( 'Default for all sites (skip custom values)', 'editoria11y' ),
			'lock' => __( 'Override for all sites (delete custom values)', 'editoria11y' ),
		);
		?>
		<p class="ed11y-network-mode">
			<label>
				<?php esc_html_e( 'Propagation', 'editoria11y' ); ?>
				<select name="<?php echo esc_attr( $name_prefix ); ?>[<?php echo esc_attr( $key ); ?>]">
					<?php foreach ( $options as $value => $label ) : ?>
						<option
							value="<?php echo esc_attr( $value ); ?>"
							<?php selected( $current, $value ); ?>
						><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<?php
	}
}
