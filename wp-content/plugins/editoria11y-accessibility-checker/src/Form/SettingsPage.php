<?php
/**
 * Editoria11y Settings page lifecycle.
 *
 * Owns:
 *   - The wp-admin entry points (Settings → Editoria11y, Settings →
 *     Editoria11y License, the cross-page nav-tab row).
 *   - WP Settings API registration: `register_setting()` plus the
 *     `add_settings_section()` / `add_settings_field()` calls that the
 *     `do_settings_sections('ed11y')` call in `render_page()` later
 *     consults.
 *   - The page render shell. The form body is built by the WP Settings
 *     API; all per-field markup lives on `SettingsFields` (callbacks
 *     resolve via `array( SettingsFields::class, 'method_name' )`).
 *
 * The companion classes wired by `Editoria11y\Plugin::admin()`:
 *   - `MigrationPanel` (admin notice + AJAX worker)
 *   - `SettingsValidator` (sanitize callback, registered here)
 *   - `Editoria11y\Admin\Dashboard` and `Editoria11y\Admin\CsvExport`
 *   - `CustomRulesPage` (sibling submenu)
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

use Editoria11y\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the main settings page and the surrounding admin surfaces.
 */
class SettingsPage {

	/**
	 * Wire all admin_menu / admin_init / admin_head hooks the settings
	 * page needs.
	 *
	 * Called from `Editoria11y\Plugin::admin()`.
	 */
	public function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_settings_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_sections_and_fields' ) );
		self::register_freemius_account_filters();
	}

	/**
	 * Make the Freemius-rendered Account / Add-ons / Upgrade / Free
	 * Trial pages render under the same nav-tab row as our own
	 * Settings and Custom Rules pages.
	 *
	 * Two filters do the work:
	 *
	 *   - `hide_account_tabs` suppresses the secondary
	 *     `<h2 class="nav-tab-wrapper">` that account.php would
	 *     otherwise emit (vendor/.../templates/account.php near the
	 *     top of the .wrap.fs-section div). Without this we'd render
	 *     two horizontal tab rows on the Account page.
	 *
	 *   - `templates/account.php` is the wrapping filter Freemius
	 *     added "to allow developers wrapping the template in custom
	 *     HTML (e.g. within a wizard/tabs)" — see
	 *     Freemius::_account_page_render(). We use it to inject our
	 *     <h1> + nav-tab row into the rendered account.php output.
	 *
	 * The Add-ons / Upgrade / Free Trial destinations Freemius would
	 * have shown in its own row are re-emitted as siblings in
	 * `render_nav_tabs()`, so suppressing the Freemius row does not
	 * make any page unreachable from the UI.
	 */
	public static function register_freemius_account_filters() {
		if ( ! function_exists( 'ed11ycsa' ) ) {
			return;
		}
		ed11ycsa()->add_filter( 'hide_account_tabs', '__return_true' );
		ed11ycsa()->add_filter( 'templates/account.php', array( __CLASS__, 'wrap_account_template' ) );
		// Registered in BOTH builds (not gated behind is__premium_only): the
		// free build's connect/opt-in screen is where a free user enters a
		// license key to activate the CSA features, so the "becoming a
		// supporter" pointer belongs there too. render_obtain_license_notice()
		// adapts its copy to the running build via ed11ycsa()->is_premium().
		ed11ycsa()->add_action( 'connect/before', array( __CLASS__, 'render_obtain_license_notice' ) );
	}

	/**
	 * Banner shown on the Freemius connect / activation screen (the opt-in
	 * shown on `options-general.php?page=ed11y`), pointing users at the
	 * supporter / license path.
	 *
	 * Shown in BOTH builds (the registration in
	 * register_freemius_account_filters() is no longer gated behind
	 * is__premium_only), but the copy differs because the two builds offer
	 * different things:
	 *   - Premium (CSA) build: its connect screen DOES expose a license-key
	 *     field, so we keep the "enter your existing license key …" copy and
	 *     also surface a pointer to editoria11y.com/codes (the SDK explains how
	 *     to *enter* a key but offers no path to *obtain* one).
	 *   - Free (WP.org) build: the SDK does NOT expose a license field — license
	 *     activation is gated to the premium build by
	 *     Freemius::_add_license_activation() (has_premium_version && !is_premium
	 *     returns early) and by connect.php's `$require_license_key` (false when
	 *     !is_premium_code && has_release_on_freemius). So the free copy must NOT
	 *     promise license entry; it points only to the supporter path. Activation
	 *     happens later, in the supporter build the user installs.
	 *
	 * Both paragraphs branch on `ed11ycsa()->is_premium()` — a runtime check,
	 * NOT the is__premium_only() strip marker, so the free branch survives the
	 * preprocessor and runs when is_premium is flipped to false in the free
	 * build. The premium copy is left verbatim so its existing 29-locale
	 * translations stay valid; the free build adds two new strings.
	 *
	 * Registered via the `connect/before` Freemius action, which fires just
	 * above `<div id="fs_connect" class="wrap">` — so we wrap our own `.wrap`
	 * for the standard admin gutter.
	 *
	 * Links are injected via printf `%s` placeholders rather than living inside
	 * the translated string so URLs stay in code (not editable by translators)
	 * and translators only see the human-readable copy.
	 */
	public static function render_obtain_license_notice() {
		$codes_link = sprintf(
			'<a href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url( 'https://editoria11y.com/codes/' ),
			esc_html__( 'becoming a supporter', 'editoria11y' )
		);
		?>
		<div class="wrap">
			<div class="notice notice-info" style="margin-left: 0; margin-right: 0;">
				<?php
				if ( ed11ycsa()->is_premium() ) {
					// Premium (CSA) build: its connect screen DOES expose a
					// license-key field (require_license_key can be true), so the
					// "enter your existing license key" path is real here.
					$free_link = sprintf(
						'<a href="%s" target="_blank" rel="noopener">%s</a>',
						esc_url( 'https://wordpress.org/plugins/editoria11y-accessibility-checker/' ),
						esc_html__( 'free version at WordPress.org', 'editoria11y' )
					);
					?>
					<p>
						<?php
						printf(
							/* translators: %s: link reading "free version at WordPress.org". */
							esc_html__( 'This site has the community-supported "CSA" version of Editoria11y installed rather than the %s.', 'editoria11y' ),
							$free_link // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						);
						?>
					</p>
					<p>
						<?php
						printf(
							/* translators: %s: link reading "becoming a supporter". */
							esc_html__( 'To activate the community-supported features and continue receiving updates, either enter your existing license key or view options for %s.', 'editoria11y' ),
							$codes_link // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						);
						?>
					</p>
					<?php
				} else {
					// Free (WP.org) build: the SDK does NOT expose a license-key
					// field here (has_premium_version + !is_premium gates it off —
					// see Freemius::_add_license_activation()). License activation
					// only happens in the supporter build, so do NOT tell the user
					// to "enter a license key"; point only to the supporter path.
					?>
					<p>
						<?php esc_html_e( 'This site is running the free version of Editoria11y from WordPress.org. The community-supported (CSA) features are not active.', 'editoria11y' ); ?>
					</p>
					<p>
						<?php
						printf(
							/* translators: %s: link reading "becoming a supporter". */
							esc_html__( 'Unlock the community-supported features and continued updates by %s.', 'editoria11y' ),
							$codes_link // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						);
						?>
					</p>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Inject our admin chrome into the Freemius account.php output.
	 *
	 * The Freemius template opens with `<div class="wrap fs-section">`
	 * and goes straight into postboxes; we splice an <h1> and the
	 * shared nav-tab row in between so the page reads as the third
	 * tab of our settings UI rather than a standalone Freemius screen.
	 *
	 * @param string $html Rendered account.php output passed in by
	 *                     `fs_templates/account.php_<slug>`.
	 * @return string
	 */
	public static function wrap_account_template( $html ) {
		ob_start();
		?>
		<h1><?php esc_html_e( 'Editoria11y License & Account', 'editoria11y' ); ?></h1>
		<?php self::render_nav_tabs( 'ed11y-account' ); ?>
		<?php
		$injection = ob_get_clean();
		return preg_replace(
			'#(<div class="wrap fs-section">)#',
			'$1' . "\n" . $injection,
			$html,
			1
		);
	}

	/**
	 * Allowed-HTML allowlist for `wp_kses` calls inside the settings page
	 * field-help text.
	 *
	 * @return array<string, array<string, true>> wp_kses-shape allowlist.
	 */
	public static function allowed_html() {
		return array(
			'em'     => array(),
			'strong' => array(),
			'code'   => array(),
			'br'     => array(),
			'p'      => array(),
		);
	}

	/**
	 * Register Settings → Editoria11y Settings.
	 */
	public static function register_settings_menu() {
		$page = add_options_page(
			esc_html__( 'Editoria11y', 'editoria11y' ),
			esc_html__( 'Editoria11y', 'editoria11y' ),
			'manage_options',
			'ed11y',
			array( __CLASS__, 'render_page' )
		);
		if ( ! $page ) {
			return;
		}
		// Provided hook_suffix that's returned to add scripts only on
		// settings page.
		add_action( 'load-' . $page, array( __CLASS__, 'enqueue_styles_scripts' ) );
	}

	/**
	 * Render the nav-tab row shown at the top of the plugin's admin pages.
	 *
	 * The Custom Rules tab only renders when CSA is active — without a
	 * license the page is a permanent upgrade-prompt and showing it as a
	 * sibling tab would imply parity with the always-available Settings /
	 * License pages.
	 *
	 * @param string $current Slug of the active page.
	 */
	public static function render_nav_tabs( $current ) {
		$tabs = array(
			'ed11y' => __( 'Settings', 'editoria11y' ),
		);
		// Free build: the SDK runs in anonymous mode (the strip flips
		// anonymous_mode => true), so an unregistered user has no Account
		// page AND no way to reach one — the reconnect URL clears the
		// stored skip only for dynamic_init to re-simulate it on the next
		// request, so the click just loops back to Settings. Only offer
		// the tab once a real account exists (opted in under a pre-3.0
		// build, or connected through checkout).
		if ( function_exists( 'ed11ycsa' ) && ed11ycsa()->is_registered() ) {
			$tabs['ed11y-account'] = __( 'License & Account', 'editoria11y' );
		}
		// Freemius preprocessor strips this whole if-block from the free
		// build. To keep `else` branches out of the preprocessor's path,
		// the free-build tab layout is set above and this block only
		// widens it for the premium build. The unset/re-add keeps
		// "License & Account" last in the row.

		// On network-activated installs the Freemius "License & Account"
		// destination resolves to a network-admin URL (see tab_url() /
		// Freemius's _get_admin_page_url). A non-super-admin who clicked it
		// would be redirected into network admin and either 403 against the
		// access guards in FreemiusAccessControl or land on a page with no
		// actionable content for them. Drop it from the row entirely so the
		// destination link is never visible. Our own ed11y-* pages stay: they
		// are per-site Settings pages, not network-admin Freemius pages.
		if ( self::is_network_locked_for_user() ) {
			unset( $tabs['ed11y-account'] );
		}
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $slug => $label ) {
			$class = ( $slug === $current ) ? 'nav-tab nav-tab-active' : 'nav-tab';
			printf(
				'<a href="%s" class="%s">%s</a>',
				esc_url( self::tab_url( $slug ) ),
				esc_attr( $class ),
				esc_html( $label )
			);
		}
		self::render_freemius_extra_tabs();
		echo '</h2>';
	}

	/**
	 * Resolve the admin URL for a nav-tab slug.
	 *
	 * With the Freemius menu unified onto our settings page, every nav-tab is
	 * an `ed11y*` page under `options-general.php` — except the Freemius
	 * "License & Account" tab (`ed11y-account`), whose live destination depends
	 * on the opt-in state because the SDK only registers an account page once
	 * the user is connected. We route that one specially; everything else is
	 * one of our own pages and resolves with a plain `admin_url()`.
	 *
	 * @param string $slug Tab slug from {@see render_nav_tabs()}.
	 */
	private static function tab_url( string $slug ): string {
		if ( 'ed11y-account' === $slug && function_exists( 'ed11ycsa' ) ) {
			$freemius = ed11ycsa();
			// Registered: the live Account page (…-account), where the license
			// and account UI render.
			if ( $freemius->is_registered() ) {
				return (string) $freemius->_get_admin_page_url( 'account' );
			}
			// Not yet connected — either the user skipped the opt-in (anonymous)
			// or opted in but hasn't confirmed the email yet (pending
			// activation). Neither state has an Account page, and the bare slug
			// is just the settings page (looping the user back to where they
			// are). Premium build only in practice: render_nav_tabs() hides the
			// tab from unregistered users in the free build, where the SDK's
			// anonymous_mode would re-simulate the skip right after reconnect
			// cleared it. Re-enter the opt-in / license flow via the reconnect URL:
			// it targets the menu slug — now the always-registered settings page
			// (`ed11y`) — and its reset action (handled by the SDK's
			// connect_again() on admin_init, which clears BOTH anonymous and
			// pending) lands on a real page instead of 403ing or looping.
			if ( $freemius->is_anonymous() || $freemius->is_pending_activation() ) {
				return (string) $freemius->get_reconnect_url();
			}
			// Activation mode (fresh install): the SDK overrides the settings
			// page with its opt-in screen; link straight to it.
			return (string) $freemius->_get_admin_page_url( '' );
		}
		return admin_url( 'options-general.php?page=' . $slug );
	}

	/**
	 * Append the Add-ons / Upgrade / Free Trial tabs Freemius would
	 * normally render at the top of its Account page.
	 *
	 * `register_freemius_account_filters()` suppresses that row via
	 * the `hide_account_tabs` filter, so without re-emitting these
	 * destinations here they would only be reachable via deep links
	 * inside the Account body. The visibility gating mirrors
	 * vendor/.../templates/account.php so we surface exactly what
	 * Freemius itself would have surfaced — no more, no less.
	 *
	 * Shown once the user has either connected (registered) OR skipped the
	 * opt-in (anonymous) — both are settled states where the upgrade path is
	 * meaningful and the pricing page is reachable. Skipped only in fresh
	 * activation mode, where the opt-in (not an upgrade pitch) is the focus.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) The trial-tab and
	 *   upgrade-tab visibility branches are extracted to helpers, but
	 *   PHPMD's pdepend analyzer over-counts when the plugin's
	 *   `editoria11y.php` bootstrap file is included in the same
	 *   analysis pass (see scripts/test.sh).
	 */
	private static function render_freemius_extra_tabs() {
		if ( ! function_exists( 'ed11ycsa' ) ) {
			return;
		}
		// Add-Ons / Upgrade / Free Trial all land on network-admin pages
		// when the plugin is network-active; same reasoning as the main
		// nav-tab gate above — keep the destinations out of the row for
		// users who can't act on them.
		if ( self::is_network_locked_for_user() ) {
			return;
		}
		$freemius = ed11ycsa();
		// Shown for users who have connected (registered), skipped the opt-in
		// (anonymous), or opted in but not yet confirmed their email (pending
		// activation). All three are settled states using the free build where
		// the upgrade/pricing page is reachable and surfacing it is the whole
		// point of the freemium funnel. Hidden only in fresh activation mode
		// (none of the three), where the opt-in is the focus — and where the
		// settings page (and this row) isn't rendered anyway.
		if (
			! $freemius->is_registered() &&
			! $freemius->is_anonymous() &&
			! $freemius->is_pending_activation()
		) {
			return;
		}

		$slug         = $freemius->get_slug();
		$show_upgrade = self::should_show_upgrade_tab( $freemius );

		if ( $freemius->has_addons() ) {
			printf(
				'<a href="%s" class="nav-tab">%s</a>',
				esc_url( $freemius->_get_admin_page_url( 'addons' ) ),
				esc_html( fs_text_inline( 'Add-Ons', 'add-ons', $slug ) )
			);
		}

		if ( $show_upgrade ) {
			printf(
				'<a href="%s" class="nav-tab">%s</a>',
				esc_url( $freemius->get_upgrade_url() ),
				esc_html( fs_text_x_inline( 'Upgrade', 'verb', 'upgrade', $slug ) )
			);
			self::maybe_render_free_trial_tab( $freemius, $slug );
		}
	}

	/**
	 * Whether the Upgrade tab applies to the current Freemius state.
	 *
	 * Pulled out of `render_freemius_extra_tabs()` so PHPMD's cyclomatic
	 * counter doesn't trip on the four-clause AND chain inside what was
	 * already a multi-branch method.
	 *
	 * @param object $freemius Freemius instance from `ed11ycsa()`.
	 * @return bool
	 */
	private static function should_show_upgrade_tab( $freemius ): bool {
		$has_paid_plan = $freemius->apply_filters( 'has_paid_plan_account', $freemius->has_paid_plan() );
		return (
			! $freemius->is_whitelabeled() &&
			$has_paid_plan &&
			! $freemius->is_paying() &&
			! $freemius->is_paid_trial()
		);
	}

	/**
	 * Emit the Free Trial tab if the current state allows it.
	 *
	 * Sibling helper to `should_show_upgrade_tab()`; only ever called
	 * inside the `$show_upgrade` branch above. Mirrors the visibility
	 * gating in vendor/.../templates/account.php.
	 *
	 * @param object $freemius Freemius instance.
	 * @param string $slug     Plugin slug used by Freemius's i18n helpers.
	 */
	private static function maybe_render_free_trial_tab( $freemius, string $slug ): void {
		if (
			! $freemius->apply_filters( 'show_trial', true ) ||
			$freemius->is_trial_utilized() ||
			! $freemius->has_trial_plan()
		) {
			return;
		}
		printf(
			'<a href="%s" class="nav-tab">%s</a>',
			esc_url( $freemius->get_trial_url() ),
			esc_html( fs_text_inline( 'Free Trial', 'free-trial', $slug ) )
		);
	}

	/**
	 * Whether the current user is on a network-activated install but
	 * lacks super-admin rights.
	 *
	 * When true, every `ed11ycsa*` destination on the nav-tab row would
	 * route into network admin — a surface that either 403s for the
	 * user (see FreemiusAccessControl::guard_network_admin_pages /
	 * the SDK PR) or has no actionable content for them. Both
	 * render_nav_tabs and render_freemius_extra_tabs gate on this so
	 * the link is never displayed in the first place.
	 *
	 * `is_network_active()` here is the SDK accessor — it returns true
	 * when the plugin is active across the network, regardless of
	 * delegated-connection state. Lives in this class (not
	 * FreemiusAccessControl) because the helper is also exercised by
	 * the free-build nav-tab path; the gate is read-only and survives
	 * the free-build strip.
	 *
	 * @return bool
	 */
	private static function is_network_locked_for_user(): bool {
		return (
			function_exists( 'ed11ycsa' )
			&& is_multisite()
			&& ed11ycsa()->is_network_active()
			&& ! is_super_admin()
		);
	}

	/** Enqueue styles for the plugin's admin pages. */
	public static function enqueue_styles_scripts() {
		wp_enqueue_style(
			'editoria11y-wp-css',
			trailingslashit( ED11Y_ASSETS ) . 'css/editoria11y-wp-admin.css',
			null,
			Plugin::VERSION
		);
	}

	/**
	 * Register `ed11y_plugin_settings` with the WP Settings API.
	 *
	 * Sanitize callback delegates to `SettingsValidator::validate` —
	 * passed as a callable array because the callback is non-trivial
	 * (~300 lines) and has its own test coverage.
	 */
	public static function register_settings() {
		register_setting(
			'ed11y_settings',
			'ed11y_plugin_settings',
			array(
				'sanitize_callback' => array( SettingsValidator::class, 'validate' ),
			)
		);
	}

	/**
	 * Register every section + field via the WP Settings API.
	 *
	 * Section hierarchy mirrors Drupal's `Editoria11ySettings.php`. The
	 * "Getting started" section deliberately groups developer- and
	 * content-area fields together: developers scan a superset of what
	 * content editors see, and presenting them in the same fieldset
	 * exposes the funnel (the developer area constrains what content
	 * editors can even test).
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public static function register_sections_and_fields() {

		add_settings_section(
			'ed11y_getting_started',
			__( 'Getting started', 'editoria11y' ),
			array( SettingsFields::class, 'getting_started_section_intro' ),
			'ed11y'
		);

		// Per-test enable/disable, split into the two top-level sections
		// `TestNames::groupSet()` exposes (content tests / template tests).
		add_settings_section(
			'ed11y_modify_content_tests',
			__( 'Modify content tests', 'editoria11y' ),
			array( SettingsFields::class, 'modify_content_tests_section_intro' ),
			'ed11y'
		);
		add_settings_field(
			'tests_off_content',
			esc_html__( 'Tests by group', 'editoria11y' ),
			array( SettingsFields::class, 'modify_content_tests_field' ),
			'ed11y',
			'ed11y_modify_content_tests'
		);

		add_settings_section(
			'ed11y_modify_template_tests',
			__( 'Modify template tests', 'editoria11y' ),
			array( SettingsFields::class, 'modify_template_tests_section_intro' ),
			'ed11y'
		);
		add_settings_field(
			'tests_off_template',
			esc_html__( 'Tests by group', 'editoria11y' ),
			array( SettingsFields::class, 'modify_template_tests_field' ),
			'ed11y',
			'ed11y_modify_template_tests'
		);

		// Custom rules section is a deep-link surface to the dedicated
		// submenu page that owns the CRUD UI. CSA-only.

		/* --- Getting started: page-area config (the funnel) --- */

		add_settings_field(
			'ed11y_checkRoots',
			esc_html__( 'Page regions with user-editable content', 'editoria11y' ),
			array( SettingsFields::class, 'check_roots_field' ),
			'ed11y',
			'ed11y_getting_started',
			array( 'label_for' => 'ed11y_checkRoots' )
		);

		// The label text differs when CSA is active; the field itself is
		// shared. Build the free-build label first; the premium-only block
		// below replaces it when CSA is active at runtime.
		$ignore_elements_label = esc_html__( 'Do not check for content errors inside these elements', 'editoria11y' );

		add_settings_field(
			'ed11y_ignore_elements',
			$ignore_elements_label,
			array( SettingsFields::class, 'ignore_elements_field' ),
			'ed11y',
			'ed11y_getting_started',
			array( 'label_for' => 'ed11y_ignore_elements' )
		);

		// Stack of collapsibles for fields that used to live in their own
		// top-level <h2> sections (Assertiveness / Theme compatibility /
		// WordPress compatibility). Folded into a single row at the end of
		// "Getting started" so the only remaining <h2>s are Getting started,
		// the two Modify-tests sections, and Custom rules.
		add_settings_field(
			'ed11y_advanced_settings',
			esc_html__( 'Advanced settings', 'editoria11y' ),
			array( SettingsFields::class, 'advanced_settings_field' ),
			'ed11y',
			'ed11y_getting_started'
		);
	}

	/**
	 * Render the Editoria11y Settings page shell.
	 *
	 * The form body is generated by the WP Settings API via
	 * `do_settings_sections('ed11y')`; the migration progress panel and
	 * nav-tab row are rendered above it.
	 */
	public static function render_page() {
		?>

		<div class="wrap">
			<h1><?php esc_html_e( 'Editoria11y Settings', 'editoria11y' ); ?></h1>
			<?php self::render_nav_tabs( 'ed11y' ); ?>
			<?php MigrationPanel::render(); ?>

			<div id="poststuff">
				<div id="post-body" class="editoria11y-settings metabox-holder">
					<div id="post-body-content">

					<div class="announcement-component">
						<!-- stuff above the form -->
					</div>

					<form method="post" action="options.php" autocomplete="off" class="ed11y-form-admin">
						<?php settings_fields( 'ed11y_settings' ); ?>
						<?php do_settings_sections( 'ed11y' ); ?>
						<?php submit_button( esc_html__( 'Save Settings', 'editoria11y' ), 'primary large' ); ?>
					</form>
					<?php ConditionalFields::print_script(); ?>
				</div><!-- .post-body-content -->

				</div><!-- .editoria11y-settings -->
				<br class="clear">
			</div>
		</div>
		<?php
	}
}
