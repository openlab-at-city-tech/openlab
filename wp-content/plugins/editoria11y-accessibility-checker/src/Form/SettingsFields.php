<?php
/**
 * Per-field renderers and section intros for the Editoria11y Settings page.
 *
 * Every method here is registered as an `add_settings_field()` /
 * `add_settings_section()` callback by `SettingsPage::register_sections_and_fields()`.
 * Splitting this off the page-lifecycle class keeps each file focused: the
 * lifecycle reads as one short class, the markup grouped by topic reads
 * as a long one.
 *
 * Methods are organized in section order to match the rendered page:
 *
 *   1. Section intros (Getting started / Modify content / Modify
 *      template / Custom rules — the only remaining top-level <h2>s).
 *   2. Top-level settings fields (theme, alert mode, livecheck, scan
 *      area, ignore lists, etc.).
 *   3. CSA-only settings fields (`csa_*_field` methods).
 *   4. Per-test renderers (`render_test_groups_for_set` and the two
 *      thin entry points `modify_*_tests_field`).
 *   5. Advanced-settings stack (`advanced_settings_field`) — folds the
 *      former Assertiveness / Theme compat / WP compat sections into
 *      one TD of stacked <details> at the end of Getting started.
 *   6. Theme-compat nested-details renderers (positioning / dynamic /
 *      sync / headings) — called from `advanced_settings_field`.
 *   7. Shared helpers (`compat_textarea`, `compat_checkbox`,
 *      `render_group_refinements`).
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

use Editoria11y\Controller\ApiConfig;
use Editoria11y\Form\SettingsContext;
use Editoria11y\NetworkCustomRules;
use Editoria11y\Form\SettingsValidator;
use Editoria11y\TestNames;

defined( 'ABSPATH' ) || exit;

/**
 * Static field-renderer methods for the WP Settings API callbacks.
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class SettingsFields {

	/* === Section intros === */

	/**
	 * "Getting started" section intro.
	 *
	 * Frames the developer-vs-content area distinction. When CSA is
	 * active, the developer-mode fields appear above content_root /
	 * ignore_elements to show that developers scan the superset. When
	 * CSA is inactive, the dev-area fields are absent (registered
	 * conditionally) but the intro surface still mentions the gate so
	 * admins see what's behind it.
	 */
	public static function getting_started_section_intro() {
		// @todo: update Configuration guide URL when Ed11y site guide is ready.

		?>
		<p class="description">
			<?php
			esc_html_e(
				'Define the parts of each page Editoria11y checks. Activating CSA unlocks an additional "developer area" superset that runs more checks and routes alerts to the roles you choose.',
				'editoria11y'
			);
			?>
		</p>
		<?php
	}

	/** "Modify content tests" section intro. */
	public static function modify_content_tests_section_intro() {
		?>
		<p>
			<?php
			esc_html_e(
				'These tests detect issues content editors can fix. Checks can be individually turned off; sites with CSA licenses can split tests between roles.',
				'editoria11y'
			);
			?>
		</p>
		<?php
		self::print_bundle_lock_note();
		// One bundle entry covers the per-test selection on both this
		// section AND "Modify template tests" — they share the same
		// storage CSVs — plus the developer-roles assignment. Rendered
		// here (the first occurrence) so admins see it before the tables.
		// The dropdown is three-way: seed new sites only, propagate to all
		// (with mid-flight cancellation support), or enforce as a lock.
		SettingsContext::print_bundle_mode_dropdown(
			SettingsValidator::BUNDLE_LOCK_TESTS_AND_ROLES,
			__( 'Apply all the tests and role assignments below to', 'editoria11y' )
		);
	}

	/** "Modify template tests" section intro. */
	public static function modify_template_tests_section_intro() {
		?>
		<p>
			<?php
			esc_html_e(
				'These tests detect issues that usually need theme or plugin changes to fix, and are part of the CSA license for site administrators and developers.',
				'editoria11y'
			);
			?>
		</p>
		<?php if ( SettingsContext::is_network() ) : ?>
			<p class="description">
				<em>
				<?php
				esc_html_e(
					'The "tests + roles assignment" propagation setting under "Modify content tests" above also governs the template-test selection — both are stored together.',
					'editoria11y'
				);
				?>
				</em>
			</p>
			<?php
		endif;
		self::print_bundle_lock_note();
	}

	/**
	 * Print the "Managed at the network level" note above a test-group
	 * section when the bundle lock is in effect. Site context only — on
	 * the network defaults page the super-admin is authoring the lock and
	 * the widgets stay editable.
	 */
	private static function print_bundle_lock_note(): void {
		if ( SettingsContext::is_network() ) {
			return;
		}
		if ( ! ed11y_is_bundle_locked() ) {
			return;
		}
		?>
		<p class="ed11y-locked-by-network">
			<strong>🔒 <?php esc_html_e( 'Managed at the network level.', 'editoria11y' ); ?></strong>
		</p>
		<?php
	}

	/**
	 * "Custom rules (CSA)" section intro.
	 *
	 * Brief description when CSA is active (with a "Manage / Add" pair
	 * of buttons that deep-link to the dedicated submenu page); upgrade
	 * stub when inactive so the section header isn't a dead surface.
	 */
	public static function custom_rules_section_intro() {
		// Network defaults page: link out to the dedicated network custom
		// rules submenu (super-admin only). Mirrors the per-site button
		// pair but operates on `NetworkCustomRules` storage.
		if ( SettingsContext::is_network() ) {

			return;
		}
		// Entire CSA-active branch is wrapped in the preprocessor gate
		// because it references the CustomRules class, which is stripped
		// from the free build via the @fs_premium_only header in
		// editoria11y.php. Section registration is similarly gated in
		// SettingsPage::register_sections_and_fields().

		?>
		<p class="description">
			<?php
			esc_html_e(
				'Custom rules let CSA admins register additional accessibility tests with their own selectors, labels, and tip content. Activate CSA to manage them here.',
				'editoria11y'
			);
			?>
		</p>
		<?php
	}

	/* === Top-level settings fields === */

	/** Theme select. */
	public static function theme_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_theme' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_theme' ); ?>
		<select name="ed11y_plugin_settings[ed11y_theme]" id="ed11y-theme" class="form-select"<?php echo SettingsContext::field_disabled_attr( 'ed11y_theme' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<option <?php echo 'sleekTheme' === $settings ? 'selected="true"' : ''; ?>value="sleekTheme">Sleek</option>
			<option <?php echo 'lightTheme' === $settings ? 'selected="true"' : ''; ?>value="lightTheme">Classic</option>
			<option <?php echo 'darkTheme' === $settings ? 'selected="true"' : ''; ?>value="darkTheme">Dark</option>
		</select>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_theme' ); ?>
		<?php
	}

	/** Alert mode select (`alert_mode`). */
	public static function alert_mode_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_alert_mode' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_alert_mode' ); ?>
		<select name="ed11y_plugin_settings[ed11y_alert_mode]" id="ed11y-alert_mode" class="form-select" aria-describedby="alert_mode_description"<?php echo SettingsContext::field_disabled_attr( 'ed11y_alert_mode' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<option <?php echo 'polite' === $settings ? 'selected="true"' : ''; ?>value="polite">Start open if there are any alerts</option>
			<option <?php echo 'assertive' === $settings ? 'selected="true"' : ''; ?>value="assertive">Start open if there are new alerts</option>
			<option <?php echo 'active' === $settings ? 'selected="true"' : ''; ?>value="active">Always start open</option>
			<option <?php echo 'minimized' === $settings ? 'selected="true"' : ''; ?>value="minimized">Start minimized</option>
		</select>
		<p id="alert_mode_description" class="description">
			<?php esc_html_e( 'Choose when the control panel should open and show inline tips. "Start open if there are any alerts" is recommended, as it helps tips get noticed over time.', 'editoria11y' ); ?>
		</p>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_alert_mode' ); ?>
		<?php
	}

	/** Livecheck select. */
	public static function livecheck_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_livecheck' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_livecheck' ); ?>
		<select name="ed11y_plugin_settings[ed11y_livecheck]" id="ed11y-livecheck" class="form-select" aria-describedby="livecheck_description"<?php echo SettingsContext::field_disabled_attr( 'ed11y_livecheck' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<option <?php echo 'all' === $settings ? 'selected="true"' : ''; ?>value="all">Always start open</option>
			<option <?php echo 'minimized' === $settings ? 'selected="true"' : ''; ?>value="minimized">Always start minimized</option>
			<option <?php echo 'errors' === $settings ? 'selected="true"' : ''; ?>value="errors">Remember my preference</option>
			<option <?php echo 'none' === $settings ? 'selected="true"' : ''; ?>value="none">Hide checker while editing</option>
		</select>
		<p id="livecheck_description" class="description">
			<?php esc_html_e( 'Choose whether to show inline tips while editing post and page content. The checker always hides while editing templates and layouts.', 'editoria11y' ); ?>
		</p>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_livecheck' ); ?>
		<?php
	}

	/** Content-area selectors field (`ed11y_checkRoots`). */
	public static function check_roots_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_checkRoots' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_checkRoots' ); ?>
		<textarea autocomplete="off"
			class="code"
			name="ed11y_plugin_settings[ed11y_checkRoots]"
			rows="3" cols="45"
			id="ed11y_checkRoots"
			aria-describedby="target_description"
			<?php echo SettingsContext::field_disabled_attr( 'ed11y_checkRoots' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea( $settings ); ?></textarea>
		<p id="target_description" class="description">
			<?php
				echo wp_kses(
					__(
						'Provide CSS selectors. Content authors will only see the alerts within these parts of the page. Do not provide selectors that nest within each other, or the inner content will be checked twice.',
						'editoria11y'
					),
					SettingsPage::allowed_html()
				);
			?>
		</p>
		<p class="description">
			<?php
				echo wp_kses( __( 'The default is <code>main</code> or <code>body</code>, depending on theme.', 'editoria11y' ), SettingsPage::allowed_html() );
			?>
		</p>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_checkRoots' ); ?>

		<?php
	}

	/** Ignore-elements field (`ed11y_ignore_elements`). */
	public static function ignore_elements_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_ignore_elements' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_ignore_elements' ); ?>
		<textarea autocomplete="off"
		class="code" id="ed11y_ignore_elements"
		aria-describedby="exclusions_description"
		name="ed11y_plugin_settings[ed11y_ignore_elements]"
		rows="3" cols="45"
		<?php echo SettingsContext::field_disabled_attr( 'ed11y_ignore_elements' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_attr( $settings ); ?></textarea>
		<p id="exclusions_description" class="description">
			<?php
				echo wp_kses(
					__(
						'If Editoria11y is flagging things editors cannot fix, e.g., theme-generated "read more" links or social media widgets,
				provide CSS selectors for elements you would like it to ignore. Be specific, e.g. <code>.read-more a, .wp-block-post-excerpt__more-link, #comments h3</code>',
						'editoria11y'
					),
					SettingsPage::allowed_html()
				);
			?>
		</p>
		<p class="description">
			<?php
				esc_html_e(
					'If you are new at this, start by opening your browser\'s developer tools, inspecting the element you do not want flagged, and looking for unique-looking CSS selectors.',
					'editoria11y'
				);
			?>
		</p>
		<p class="description">
			<?php
				echo wp_kses(
					sprintf(
						/* translators: %s is the built-in list of always-ignored CSS selectors. */
						__( 'Selectors you add are appended to the built-in list, which is always ignored: %s', 'editoria11y' ),
						'<code>' . esc_html( ed11y_container_ignore_baseline() ) . '</code>'
					),
					SettingsPage::allowed_html()
				);
			?>
		</p>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_ignore_elements' ); ?>

		<?php
	}

	/** Checkvisibility select. */
	public static function checkvisibility_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_checkvisibility' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_checkvisibility' ); ?>
		<select name="ed11y_plugin_settings[ed11y_checkvisibility]" id="ed11y-checkvisibility" class="form-select" aria-describedby="checkvisibility_description"<?php echo SettingsContext::field_disabled_attr( 'ed11y_checkvisibility' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<option <?php echo '' === $settings ? 'selected="true"' : ''; ?>value="">Theme default</option>
			<option <?php echo 'true' === $settings ? 'selected="true"' : ''; ?>value="true">Check for visibility</option>
			<option <?php echo 'false' === $settings ? 'selected="true"' : ''; ?>value="false">Disable visibility checking</option>
		</select>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_checkvisibility' ); ?>

		<p id="checkvisibility-description">Set if your theme throws "this element may be hidden" alerts
			when using the next/previous buttons on the main panel.
			See the main library documentation for <a href="https://editoria11y.princeton.edu/configuration/#js-events">JS events</a> and <a href="https://editoria11y.princeton.edu/configuration/#hidden-content">developer tips for revealing hidden content on demand</a>.</p>
			<p><em>And please tell us if this happens with a common theme so we can add it to the defaults!</em></p>
		<?php
	}

	/** Custom-tests numeric field. */
	public static function custom_tests_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_custom_tests' );
		$default  = ed11y_get_default_options( 'ed11y_custom_tests' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_custom_tests' ); ?>
		<input autocomplete="off"
		class="code" id="ed11y_custom_tests"
		aria-describedby="ed11y_custom_tests_description"
		type="number" min="0" max="99" name="ed11y_plugin_settings[ed11y_custom_tests]"
		placeholder="<?php echo esc_attr( $default ); ?>"
		value="<?php echo esc_attr( $settings ); ?>" pattern="[^<>\\\x27;|@&]+"
		<?php echo SettingsContext::field_disabled_attr( 'ed11y_custom_tests' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>/>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_custom_tests' ); ?>
		<p id="ed11y_custom_tests_description">
			<?php
				echo wp_kses( __( 'Set to the number of other themes or plugins that will be injecting custom result JS events. Editoria11y will wait until it receives this number of <code>ed11yResume</code> notifications before showing results.', 'editoria11y' ), SettingsPage::allowed_html() );
			?>
		</p>
		<?php
	}

	/** No-run selector field. */
	public static function no_run_field() {
		$settings = SettingsContext::get_raw_setting( 'ed11y_no_run' );
		$default  = ed11y_get_default_options( 'ed11y_no_run' );
		?>
		<?php SettingsContext::print_lock_note( 'ed11y_no_run' ); ?>
		<textarea
			id="ed11y_no_run"
			name="ed11y_plugin_settings[ed11y_no_run]"
			rows="1" cols="45"
			class="code"
			placeholder="<?php echo esc_attr( $default ); ?>"
			aria-describedby="ed11y_no_run_description"
			<?php echo SettingsContext::field_disabled_attr( 'ed11y_no_run' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		><?php echo esc_textarea( $settings ); ?></textarea>
		<p id="ed11y_no_run_description" class="description">
			<?php
				echo wp_kses( __( 'Used to block checking on site sections or admin pages.', 'editoria11y' ), SettingsPage::allowed_html() );
			?>
		</p>
		<?php SettingsContext::print_mode_dropdown( 'ed11y_no_run' ); ?>
		<?php
	}

	/* === CSA-only settings fields === */

	/**
	 * Render the dev_check_root radio group, plus its conditional
	 * "specify regions" textarea inline within the same cell.
	 *
	 * The textarea was historically a separate `add_settings_field()`
	 * row whose visibility depended on the radio's value. Folding it
	 * into the parent's <td> communicates the dependency visually
	 * (shared cell) and removes the orphaned <th> for what was really
	 * a sub-control of "specify".
	 */
	public static function csa_dev_check_root_field() {
		// Method body wrapped so the free build keeps an empty method
		// shell — any leftover settings-API callback reference still
		// resolves but renders nothing.
	}

	/** Render the always_ignore textarea. */
	public static function csa_always_ignore_field() {
	}

	/**
	 * Render the role-checkbox group.
	 *
	 * Reads available roles from `ed11y_get_developer_role_options()` so
	 * any role with `edit_posts` (built-in or third-party-registered)
	 * shows up. Saved value is a CSV; each checkbox checks if its slug
	 * appears in the CSV's slug list.
	 */
	public static function csa_roles_field() {
	}

	/**
	 * Render the dev_assertiveness select.
	 *
	 * @todo implement, test, update wording upstream in Drupal.
	 */
	public static function csa_dev_assertiveness_field() {
	}

	/* === Per-test renderers === */

	/** Modify content tests umbrella field. */
	public static function modify_content_tests_field() {
		self::render_test_groups_for_set( 'content_tests' );
	}

	/** Modify template tests umbrella field. */
	public static function modify_template_tests_field() {
		self::render_test_groups_for_set( 'template_tests' );
	}

	/**
	 * Walks `TestNames::group_labels()`, filters by the given set, and
	 * emits a `<details>` collapsible per matched group with the per-test
	 * widget inside.
	 *
	 * Per-test widget shape depends on `ed11y_is_csa_active()`:
	 *
	 *   - CSA inactive: checkbox (inverted polarity — checked means
	 *     enabled, unchecked goes into `tests_off`). Tests not in
	 *     `TestNames::content_tests()` (i.e. developer-only tests) render
	 *     as a disabled checkbox so admins can see what's behind the
	 *     gate. Existing dev-test entries in `tests_off` from a prior
	 *     CSA-active session are preserved.
	 *
	 *   - CSA active: 3-way `<select>` (Off / Developers only / Everyone).
	 *     Default current value is computed in priority order from the
	 *     four CSVs (`tests_dev` > `tests_content` > `tests_off` >
	 *     content-aware default) so a test that never got an explicit
	 *     setting still renders sanely.
	 *
	 * @param string $set Either 'content_tests' or 'template_tests'.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public static function render_test_groups_for_set( string $set ) {
		$is_csa       = ed11y_is_csa_active();
		$labels       = TestNames::core_names();
		$content_set  = array_flip( TestNames::content_tests() );
		$group_labels = TestNames::group_labels();

		// Preload state once per render — avoids per-row option reads.
		// The free-mode preload always runs (its values are ignored when
		// the runtime $is_csa branch is taken); the CSA preload sits
		// inside the preprocessor gate so it's stripped from the free
		// build alongside the CSA-only setting helpers it depends on.
		$off_csv         = SettingsContext::get_raw_setting( 'tests_off' );
		$off_set         = '' === $off_csv ? array() : array_flip( explode( ',', $off_csv ) );
		$csa_off_set     = array();
		$csa_dev_set     = array();
		$csa_content_set = array();

		// Bundle lock applies to the test-routing widgets as a unit. In
		// site context, disable the inputs and surface the "Managed at
		// the network level" note so the per-site admin sees that the
		// widgets are read-only. The network defaults page must remain
		// editable so the super-admin can author the locked value, so
		// gate on `is_network()`.
		$tests_locked  = ! SettingsContext::is_network() && ed11y_is_bundle_locked();
		$disabled_attr = $tests_locked ? ' disabled' : '';

		// Free-mode form marker. Unchecked checkboxes vanish from a POST, so
		// an all-unchecked save is otherwise indistinguishable from input
		// that never came from this form — and the validator only re-derives
		// `tests_off` from checkbox state for real form saves (see
		// SettingsValidator::validate()). The sentinel guarantees the
		// `tests_enabled` array is always present on a form POST. Suppressed
		// when the bundle lock disables the checkboxes: disabled inputs
		// don't post, and the absent array is how the validator knows to
		// preserve the stored value instead of deriving "everything off".
		// CSA mode needs no marker — its `<select>`s always post.
		if ( ! $is_csa && ! $tests_locked ) {
			echo '<input type="hidden" name="ed11y_plugin_settings[tests_enabled][__form]" value="1" />';
		}

		foreach ( $group_labels as $group_id => $group_label ) {
			if ( TestNames::group_set( $group_id ) !== $set ) {
				continue;
			}

			// Collect tests in this group, sorted by translated label for
			// stable rendering across locales.
			$tests_in_group = array();
			foreach ( $labels as $key => $label ) {
				if ( TestNames::group_for_key( $key ) === $group_id ) {
					$tests_in_group[ $key ] = $label;
				}
			}
			asort( $tests_in_group );

			if ( empty( $tests_in_group ) ) {
				continue;
			}
			?>
			<details class="ed11y-test-group" style="margin: 0.5em 0; padding: 0.5em 0.75em; border: 1px solid #ccd0d4; border-radius: 4px;">
				<summary style="cursor: pointer; font-weight: 600;">
					<?php echo esc_html( $group_label ); ?>
					<span style="color:#646970; font-weight: normal;">
						(<?php echo count( $tests_in_group ); ?>)
					</span>
				</summary>
				<?php self::render_group_refinements( $group_id ); ?>
				<table class="widefat striped" style="margin-top: 0.5em;">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Rule', 'editoria11y' ); ?></th>
							<th>
							<?php
							if ( $is_csa ) {
								esc_html_e( 'Show to', 'editoria11y' );
							} else {
								esc_html_e( 'Active', 'editoria11y' ); }
							?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $tests_in_group as $key => $label ) : ?>
						<?php
						$is_content = isset( $content_set[ $key ] );
						if ( $is_csa ) {
							if ( isset( $csa_dev_set[ $key ] ) ) {
								$current = 'developers';
							} elseif ( isset( $csa_content_set[ $key ] ) ) {
								$current = 'everyone';
							} elseif ( isset( $csa_off_set[ $key ] ) ) {
								$current = 'nobody';
							} else {
								// Default routing: content tests visible to
								// all, dev tests visible to developers only.
								$current = $is_content ? 'everyone' : 'developers';
							}
						} else {
							$checked = $is_content && ! isset( $off_set[ $key ] );
						}
						$field_id = 'ed11y_test_' . sanitize_html_class( $key );
						?>
						<tr>
							<td>

								<label for="<?php echo esc_attr( $field_id ); ?>">
									<p><?php echo esc_html( $label ); ?></p>
									<p><code><?php echo esc_html( $key ); ?></code></p>
								</label>
							</td>
							<td>
								<?php if ( $is_csa ) : ?>
									<select id="<?php echo esc_attr( $field_id ); ?>"
										name="ed11y_plugin_settings[tests_state][<?php echo esc_attr( $key ); ?>]"
										<?php echo $disabled_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal " disabled" / empty string. ?>>
										<option value="everyone"   <?php selected( 'everyone', $current ); ?>><?php esc_html_e( 'Everyone', 'editoria11y' ); ?></option>
										<option value="developers" <?php selected( 'developers', $current ); ?>><?php esc_html_e( 'Developers only', 'editoria11y' ); ?></option>
										<option value="nobody"     <?php selected( 'nobody', $current ); ?>><?php esc_html_e( 'Off', 'editoria11y' ); ?></option>
									</select>
								<?php elseif ( $is_content ) : ?>
									<input type="checkbox"
										id="<?php echo esc_attr( $field_id ); ?>"
										name="ed11y_plugin_settings[tests_enabled][<?php echo esc_attr( $key ); ?>]"
										value="1"
										<?php checked( $checked, true ); ?>
										<?php echo $disabled_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal " disabled" / empty string. ?>
									/>
								<?php else : ?>
									<!--<input type="checkbox"
										id="<?php echo esc_attr( $field_id ); ?>"
										disabled
										aria-describedby="ed11y_test_<?php echo esc_attr( sanitize_html_class( $key ) ); ?>_desc"
									/>-->
									<span id="ed11y_test_<?php echo esc_attr( sanitize_html_class( $key ) ); ?>_desc" style="color:#646970; font-size: 0.85em;">
										<?php esc_html_e( '(Part of CSA suite)', 'editoria11y' ); ?>
									</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</details>
			<?php
		}
	}

	/**
	 * Render group-level refinement fields injected above a test group's
	 * per-test widget table.
	 *
	 * Drupal's settings form puts a handful of related fields inside their
	 * matching test group rather than scattered across the form. We mirror
	 * that here so the relationship between (e.g.) the contrast tests and
	 * `contrast_ignore` is obvious — they're in the same `<details>`.
	 *
	 * Mapping (matching Drupal's exact order within each group):
	 *   - links_content: ed11y_documentContent / link_ignore_selector /
	 *     ignore_link_strings (== ed11y_link_ignore_strings) /
	 *     link_strings_new_windows
	 *   - embeds: embedded_content_warning
	 *   - contrast: contrast_ignore (CSA-only)
	 *
	 * @param string $group_id Group identifier from
	 *                         `TestNames::group_for_key()`.
	 */
	public static function render_group_refinements( string $group_id ) {
		if ( 'links_content' === $group_id ) {
			// May be Drupal "ed11y_documentContent".
			self::compat_textarea(
				'ed11y_documentContent',
				__( 'Remind the editor that these linked documents need a manual check', 'editoria11y' ),
				__( 'Add or remove filetypes. Set to "false" to disable the test altogether. Providing any value will override the default.', 'editoria11y' )
			);
			self::compat_textarea(
				'link_ignore_selector',
				__( 'Remove elements that match these selectors before testing link text', 'editoria11y' ),
				__( 'Provide a CSS selector for elements your themes or plugins programmatically add to links (usually external or open-in-new-window markers), so they can be ignored when the link text is checked for the "link has no text" and "link text is not meaningful" tests.', 'editoria11y' )
			);
			self::compat_textarea(
				'ed11y_link_ignore_strings',
				__( 'Remove these strings before testing link text', 'editoria11y' ),
				__( 'Provide a pipe-separated ("|") list of phrases your themes or plugins programmatically add to links to hint a purpose (external, mail, phone, open-in-new-window), so they can be ignored when the link text is checked for the "link has no text" and "link text is not meaningful" tests; e.g.: (link is external)|(link sends email).', 'editoria11y' )
			);
			self::compat_textarea(
				'link_strings_new_windows',
				__( 'Strings in links that indicate new tabs', 'editoria11y' ),
				__( 'Provide a pipe-separated list of phrases your site uses to warn users a link opens in a new tab; e.g.: new tab|new window|external.', 'editoria11y' )
			);
			echo '<hr>';
			return;
		}
		if ( 'embeds' === $group_id ) {
			self::compat_textarea(
				'ed11y_datavizContent',
				__( 'Embeds flagged as needing manual review', 'editoria11y' ),
				__( 'Added to the built-in detection list (Looker/Data Studio, Tableau, Power BI, Qlik). Provide a comma-separated list of domains or URL fragments. To stop flagging visualizations entirely, disable that test above.', 'editoria11y' ),
				'dashboards.example.edu'
			);
			self::compat_textarea(
				'ed11y_videoContent',
				__( 'Videos flagged as needing a manual check for captions', 'editoria11y' ),
				__( 'Added to the built-in detection list (YouTube, Vimeo, Panopto, Wistia, Dailymotion, Brightcove, Vidyard and generic video URLs). Provide a comma-separated list of domains or URL fragments, e.g. yuja.com. To stop flagging videos entirely, disable that test above.', 'editoria11y' ),
				'yuja.com, mediaspace.example.edu'
			);
			self::compat_textarea(
				'ed11y_audioContent',
				__( 'Audio flagged as needing a manual check for transcripts', 'editoria11y' ),
				__( 'Added to the built-in detection list (SoundCloud, Simplecast, Podbean, Buzzsprout, Spotify, Apple Podcasts and other major hosts). Provide a comma-separated list of domains or URL fragments. To stop flagging audio entirely, disable that test above.', 'editoria11y' ),
				'podcasts.example.edu'
			);
			self::compat_textarea(
				'embedded_content_warning',
				__( 'Remind editor that content in these embeds needs manual review', 'editoria11y' ),
				__( 'Provide a comma-separated list of selectors you wish to flag for the editor, e.g.: .my-embedded-feed, #my-social-link-block.', 'editoria11y' )
			);
			echo '<hr>';
			return;
		}
	}

	/* === Advanced-settings stack === */

	/**
	 * Render the "Advanced settings" cell at the end of Getting started.
	 *
	 * Stack of six sibling `<details>` collapsibles in the same TD, in the
	 * order requested by the page redesign:
	 *
	 *   1. Assertiveness — content / dev / editor checker modes.
	 *   2. WordPress compatibility — theme + no_run + custom_tests +
	 *      checkvisibility + report_restrict + hide_report_link
	 *      (i.e. everything that previously lived in the separate
	 *      "Theme compatibility" and "WordPress compatibility" sections).
	 *   3. Positioning, 4. Dynamic / shadow content, 5. Sync, 6. Headings —
	 *      delegated to the existing `theme_compat_*_field` renderers,
	 *      each of which already emits its own `<details>`.
	 */
	public static function advanced_settings_field() {
		?>
		<details class="ed11y-compat-group" style="margin: 0.5em 0; padding: 0.5em 0.75em; border: 1px solid #ccd0d4; border-radius: 4px;">
			<summary style="cursor: pointer; font-weight: 600;"><?php esc_html_e( 'Assertiveness', 'editoria11y' ); ?></summary>
			<p>
				<label for="ed11y-alert_mode"><strong><?php esc_html_e( 'Checker mode for content roles', 'editoria11y' ); ?></strong></label>
			</p>
			<?php self::alert_mode_field(); ?>

			<p>
				<label for="ed11y-livecheck"><strong><?php esc_html_e( 'Checker mode inside editor', 'editoria11y' ); ?></strong></label>
			</p>
			<?php self::livecheck_field(); ?>
		</details>

		<details class="ed11y-compat-group" style="margin: 0.5em 0; padding: 0.5em 0.75em; border: 1px solid #ccd0d4; border-radius: 4px;">
			<summary style="cursor: pointer; font-weight: 600;"><?php esc_html_e( 'Theme and plugin compatibility', 'editoria11y' ); ?></summary>
			<p>
				<label for="ed11y-theme"><strong><?php esc_html_e( 'Editoria11y theme', 'editoria11y' ); ?></strong></label>
			</p>
			<?php self::theme_field(); ?>
			<p>
				<label for="ed11y_field_panel_pin">
					<strong><?php esc_html_e( 'Pin panel to', 'editoria11y' ); ?></strong>
				</label>
			</p>
			<?php SettingsContext::print_lock_note( 'panel_pin' ); ?>
			<select id="ed11y_field_panel_pin" name="ed11y_plugin_settings[panel_pin]"<?php echo SettingsContext::field_disabled_attr( 'panel_pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php $current = SettingsContext::get_raw_setting( 'panel_pin' ); ?>
				<option value="right" <?php selected( $current, 'right' ); ?>><?php esc_html_e( 'Right', 'editoria11y' ); ?></option>
				<option value="left"  <?php selected( $current, 'left' ); ?>><?php esc_html_e( 'Left', 'editoria11y' ); ?></option>
			</select>
			<?php SettingsContext::print_mode_dropdown( 'panel_pin' ); ?>
			<?php
			self::compat_textarea(
				'panel_no_cover',
				__( 'Shift panel position if it overlaps these elements', 'editoria11y' ),
				__( 'Comma-separated selectors for competing widgets, e.g. .interface-interface-skeleton__sidebar.', 'editoria11y' )
			);
			?>

			<p>
				<label for="ed11y_no_run"><strong><?php esc_html_e( 'Do not check on pages with these elements', 'editoria11y' ); ?></strong></label>
			</p>
			<?php self::no_run_field(); ?>
			<p>
				<label for="ed11y_custom_tests"><strong><?php esc_html_e( 'JS injected custom rules', 'editoria11y' ); ?></strong></label>
			</p>
			<?php self::custom_tests_field(); ?>
			<?php
			self::compat_textarea(
				'hide_edit_links',
				__( 'Don\'t show edit links on tips in these containers', 'editoria11y' ),
				__( 'Provide a comma-separated list of page sections where edit links should not show. To hide the links everywhere, set this field to an asterisk (*).', 'editoria11y' )
			);
			?>

		</details>

		<?php
		self::theme_compat_positioning_field();
		self::theme_compat_dynamic_field();
		self::theme_compat_sync_field();
		self::theme_compat_headings_field();
	}

	/* === Theme-compat nested-details renderers === */

	/** Render the Positioning nested-details group. */
	public static function theme_compat_positioning_field() {
		?>
		<details class="ed11y-compat-group" style="margin: 0.5em 0; padding: 0.5em 0.75em; border: 1px solid #ccd0d4; border-radius: 4px;">
			<summary style="cursor: pointer; font-weight: 600;"><?php esc_html_e( 'Handling tips on hidden content', 'editoria11y' ); ?></summary>
			<p>
				<label for="ed11y-checkvisibility"><strong><?php esc_html_e( 'Warn users if elements might not be visible when jumping to tips', 'editoria11y' ); ?></strong></label>
			</p>
			<?php self::checkvisibility_field(); ?>

			<?php
			self::compat_textarea(
				'element_hides_overflow',
				__( 'Elements with overflow hidden', 'editoria11y' ),
				__( 'Sometimes buttons get drawn and visually truncated outside the bounds of a positioned element. Provide a selector list.', 'editoria11y' )
			);

			self::compat_textarea(
				'hidden_handlers',
				__( 'Theme JS will handle revealing hidden tooltips inside these containers', 'editoria11y' ),
				__( 'Editoria11y detects hidden tooltips and warns the user when they try to jump to them from the panel. For elements on this list, Editoria11y will dispatch a JS event instead of a warning, so custom JS in your theme can first reveal the hidden tip (e.g., open an accordion or tab panel).', 'editoria11y' )
			);
			?>
		</details>
		<?php
	}

	/** Render the Dynamic content nested-details group. */
	public static function theme_compat_dynamic_field() {
		?>
		<details class="ed11y-compat-group" style="margin: 0.5em 0; padding: 0.5em 0.75em; border: 1px solid #ccd0d4; border-radius: 4px;">
			<summary style="cursor: pointer; font-weight: 600;"><?php esc_html_e( 'Detecting dynamic and shadow content', 'editoria11y' ); ?></summary>
			<p>
				<label for="ed11y_field_watch_for_changes">
					<strong><?php esc_html_e( 'Dynamically refresh if new content appears', 'editoria11y' ); ?></strong>
				</label>
			</p>
			<?php SettingsContext::print_lock_note( 'watch_for_changes' ); ?>
			<select id="ed11y_field_watch_for_changes" name="ed11y_plugin_settings[watch_for_changes]"<?php echo SettingsContext::field_disabled_attr( 'watch_for_changes' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php $current = SettingsContext::get_raw_setting( 'watch_for_changes' ); ?>
				<option value="true"        <?php selected( $current, 'true' ); ?>><?php esc_html_e( 'Watch for changes anywhere on the page', 'editoria11y' ); ?></option>
				<option value="checkRoots"  <?php selected( '' === $current || 'checkRoots' === $current, true ); ?>><?php esc_html_e( 'Only watch for changes to content containers present on load', 'editoria11y' ); ?></option>
				<option value="false"       <?php selected( $current, 'false' ); ?>><?php esc_html_e( 'Do not watch for changes', 'editoria11y' ); ?></option>
			</select>
			<?php SettingsContext::print_mode_dropdown( 'watch_for_changes' ); ?>
			<p class="description"><?php esc_html_e( 'Set to "anywhere" if changes are being missed; set to "do not watch" if you notice performance issues.', 'editoria11y' ); ?></p>
			<?php
			self::compat_textarea(
				'shadow_components',
				__( 'Check inside these specific Web components', 'editoria11y' ),
				__( 'Provide selectors for elements with shadow DOM you want tested. E.g.: my-fancy-accordion-widget, my-magical-slideshow.', 'editoria11y' )
			);
			self::compat_checkbox(
				'detect_shadow',
				__( 'Auto-detect any Web components', 'editoria11y' ),
				__( 'This is easier to configure than specifying components, but may slow test runs on very complicated pages.', 'editoria11y' )
			);
			?>
		</details>
		<?php
	}

	/** Render the Sync nested-details group. */
	public static function theme_compat_sync_field() {
		?>
		<details class="ed11y-compat-group" style="margin: 0.5em 0; padding: 0.5em 0.75em; border: 1px solid #ccd0d4; border-radius: 4px;">
			<summary style="cursor: pointer; font-weight: 600;"><?php esc_html_e( 'Syncing results to reports', 'editoria11y' ); ?></summary>

			<?php
			$report_restrict  = SettingsContext::get_raw_setting( 'ed11y_report_restrict' );
			$hide_report_link = SettingsContext::get_raw_setting( 'ed11y_hide_report_link' );
			?>
			<?php SettingsContext::print_lock_note( 'ed11y_report_restrict' ); ?>
			<p>
				<label for="ed11y_report_restrict_field">
					<input type="checkbox"
						id="ed11y_report_restrict_field"
						name="ed11y_plugin_settings[ed11y_report_restrict]"
						value="1"
						aria-describedby="ed11y_report_restrict_description"
						<?php checked( '1', $report_restrict ); ?>
						<?php echo SettingsContext::field_disabled_attr( 'ed11y_report_restrict' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					/>
					<strong><?php esc_html_e( 'Only admins can view reports', 'editoria11y' ); ?></strong>
				</label>
			</p>
			<?php SettingsContext::print_mode_dropdown( 'ed11y_report_restrict' ); ?>
			<p id="ed11y_report_restrict_description" class="description">
				<?php esc_html_e( 'By default both admins and editors can view reports.', 'editoria11y' ); ?>
			</p>
			<?php SettingsContext::print_lock_note( 'ed11y_hide_report_link' ); ?>
			<p>
				<label for="ed11y_hide_report_link_field">
					<input type="checkbox"
						id="ed11y_hide_report_link_field"
						name="ed11y_plugin_settings[ed11y_hide_report_link]"
						value="1"
						aria-describedby="ed11y_hide_report_link_description"
						<?php checked( '1', $hide_report_link ); ?>
						<?php echo SettingsContext::field_disabled_attr( 'ed11y_hide_report_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					/>
					<strong><?php esc_html_e( 'Hide reports shortcut on toggle', 'editoria11y' ); ?></strong>
				</label>
			</p>
			<?php SettingsContext::print_mode_dropdown( 'ed11y_hide_report_link' ); ?>
			<p id="ed11y_hide_report_link_description" class="description">
				<?php
				echo wp_kses(
					__( 'Reports will still be available on the WordPress admin dashboard.', 'editoria11y' ),
					SettingsPage::allowed_html()
				);
				?>
			</p>
			<?php
			$current_redundant = SettingsContext::get_raw_setting( 'redundant_prefix' );
			?>
			<p>
				<label for="ed11y_field_redundant_prefix">
					<strong><?php esc_html_e( 'Remove redundant base url from URLs', 'editoria11y' ); ?></strong>
				</label>
			</p>
			<?php SettingsContext::print_lock_note( 'redundant_prefix' ); ?>
			<input type="text"
				id="ed11y_field_redundant_prefix"
				name="ed11y_plugin_settings[redundant_prefix]"
				value="<?php echo esc_attr( $current_redundant ); ?>"
				class="code"
				<?php echo SettingsContext::field_disabled_attr( 'redundant_prefix' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			/>
			<?php SettingsContext::print_mode_dropdown( 'redundant_prefix' ); ?>
			<p class="description"><?php esc_html_e( 'Provide base URL ("/mysite") if your site is installed in a subdirectory. Subdirectories tend to get duplicated (/mysite/mysite/mypage) and throw errors from the API.', 'editoria11y' ); ?></p>

			<?php
			self::compat_textarea(
				'preserve_params',
				__( 'Preserve query parameters', 'editoria11y' ),
				__( 'The dashboard ignores most parameters: results for both /news?f=1 and /news?f=2 will show up as just /news. Provide a comma-separated list of parameters that are meaningful and should appear as separate pages in results.', 'editoria11y' )
			);
			self::compat_checkbox(
				'disable_sync',
				__( 'Disable sync altogether', 'editoria11y' ),
				__( 'Syncing test results is required for the issue dashboard, dismissal dashboard, and "mark OK" buttons.', 'editoria11y' )
			);
			?>
		</details>
		<?php
	}

	/** Render the Headings nested-details group. */
	public static function theme_compat_headings_field() {
		?>
		<details class="ed11y-compat-group" style="margin: 0.5em 0; padding: 0.5em 0.75em; border: 1px solid #ccd0d4; border-radius: 4px;">
			<summary style="cursor: pointer; font-weight: 600;"><?php esc_html_e( 'Heading outline position of editable content', 'editoria11y' ); ?></summary>
			<p class="description">
				<?php esc_html_e( 'To check headings while editing, Editoria11y needs to know what the first heading level should be in each editable field. Body fields should generally be at the H2 level.', 'editoria11y' ); ?>
			</p>
			<?php
			self::compat_textarea(
				'live_h2',
				__( 'H2 level content (body content)', 'editoria11y' ),
				__( 'Body fields are preceded by an H1 (the post or page title), so their highest heading level should be H2.', 'editoria11y' )
			);
			self::compat_textarea(
				'live_h3',
				__( 'H3 level content (blocks or widgets with separate titles)', 'editoria11y' ),
				__( 'The highest heading level should be H3.', 'editoria11y' )
			);
			self::compat_textarea(
				'live_h4',
				__( 'H4 level content (deeply nested blocks or widgets)', 'editoria11y' ),
				__( 'The highest heading level should be H4.', 'editoria11y' )
			);
			?>
		</details>
		<?php
	}

	/* === Shared helpers === */

	/**
	 * Render a labeled textarea for one of the new Drupal-parity fields.
	 *
	 * The Theme-compatibility nested groups use a few near-identical
	 * textarea fields (one row, large-text, code-monospace). Factored so
	 * each group's renderer stays readable.
	 *
	 * @param string $key         Storage key (under `ed11y_plugin_settings`).
	 * @param string $label       Translated field label.
	 * @param string $description Translated description / help text.
	 * @param string $placeholder Placeholder override. Defaults to the key's
	 *                            hardcoded default — pass an example value
	 *                            for additive keys whose default is empty.
	 */
	public static function compat_textarea( string $key, string $label, string $description = '', string $placeholder = '' ) {
		$value    = SettingsContext::get_raw_setting( $key );
		$default  = '' !== $placeholder ? $placeholder : (string) ed11y_get_default_options( $key );
		$field_id = 'ed11y_field_' . sanitize_html_class( $key );
		?>
		<p>
			<label for="<?php echo esc_attr( $field_id ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		</p>
		<?php SettingsContext::print_lock_note( $key ); ?>
		<textarea
			id="<?php echo esc_attr( $field_id ); ?>"
			name="ed11y_plugin_settings[<?php echo esc_attr( $key ); ?>]"
			rows="1" cols="45"
			class="code"
			placeholder="<?php echo esc_attr( $default ); ?>"
			<?php echo SettingsContext::field_disabled_attr( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		><?php echo esc_textarea( $value ); ?></textarea>
		<?php SettingsContext::print_mode_dropdown( $key ); ?>
		<?php if ( '' !== $description ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render a checkbox for a Drupal-parity field.
	 *
	 * @param string $key         Storage key (under `ed11y_plugin_settings`).
	 * @param string $label       Translated field label.
	 * @param string $description Translated description / help text.
	 */
	public static function compat_checkbox( string $key, string $label, string $description = '' ) {
		$value    = SettingsContext::get_raw_setting( $key );
		$field_id = 'ed11y_field_' . sanitize_html_class( $key );
		?>
		<?php SettingsContext::print_lock_note( $key ); ?>
		<p>
			<label for="<?php echo esc_attr( $field_id ); ?>">
				<input type="checkbox"
					id="<?php echo esc_attr( $field_id ); ?>"
					name="ed11y_plugin_settings[<?php echo esc_attr( $key ); ?>]"
					value="1"
					<?php checked( '1', $value ); ?>
					<?php echo SettingsContext::field_disabled_attr( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				/>
				<?php echo esc_html( $label ); ?>
			</label>
		</p>
		<?php SettingsContext::print_mode_dropdown( $key ); ?>
		<?php if ( '' !== $description ) : ?>
			<p class="description" id="<?php echo esc_attr( $field_id ); ?>_desc"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php
	}
}
