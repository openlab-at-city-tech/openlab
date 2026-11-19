<?php
/**
 * Limit the Freemius trial promotion ("If you like Editoria11y …, consider
 * supporting its development; start with a 30-day free trial …") to one
 * display per site per plugin version, on the plugin's settings and
 * report screens.
 *
 * The SDK adds that promotion (`Freemius::_add_trial_notice()`) as a
 * STICKY notice with id `trial_promotion`: once added it renders on
 * every admin page until an admin clicks its dismiss X, and the SDK
 * re-adds it every 30 days after that. While the sticky exists,
 * `is_in_trial_promotion()` also badges the plugin's menu item on every
 * admin page and relabels "Upgrade" as "Start Trial". The premium build
 * never reaches it (`is_premium()` short-circuits the method outside
 * WP_FS__DEV_MODE), so in practice this governs the free build — the
 * class is registered in both builds and must not be premium-gated.
 *
 * Mechanism:
 *
 *   - `show_first_trial_after_n_sec` / `reshow_trial_after_every_n_sec`
 *     (read only inside `_add_trial_notice()` on admin_init) return 0
 *     when the promotion may be added — on a plugin screen, not yet
 *     shown for the running Plugin::VERSION — so it appears immediately
 *     instead of 24 hours / 30 days later. Otherwise they return
 *     PHP_INT_MAX, which fails both SDK timer checks and skips the add.
 *     The gate deliberately does NOT use `show_trial`: the SDK account
 *     page and Form\SettingsPage read that filter to decide whether to
 *     show their Free Trial links, which must stay visible.
 *   - `show_admin_notice` renders the promotion only on a plugin screen,
 *     then records the version and removes the sticky. Recording at
 *     render time (not at add time) means an admin request that never
 *     prints notices — a redirect, a screen that suppresses them —
 *     doesn't consume the one showing. Removal happens mid-render, which
 *     is safe: the notice manager iterates a copy of its notice array.
 *     Because add and render are gated on the same screens, the sticky
 *     is normally added and removed within one request, so the menu
 *     badge never lingers.
 *
 * Per-site option, matching the SDK's per-site sticky storage; on a
 * network-active install each delegated subsite shows it once.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Once-per-version, plugin-screens-only gate for the Freemius trial promotion.
 */
final class FreemiusTrialNotice {

	/**
	 * SDK notice id for the trial promotion (see `_add_trial_notice()`).
	 */
	const NOTICE_ID = 'trial_promotion';

	/**
	 * Option holding the Plugin::VERSION the promotion was last shown for.
	 */
	const OPTION = 'ed11y_trial_notice_version';

	/**
	 * The SDK instance, kept for `remove_sticky()` at render time.
	 *
	 * @var \Freemius|null
	 */
	private static $sdk = null;

	/**
	 * Wire the SDK filters against the live SDK instance.
	 *
	 * @param \Freemius $ed11ycsa Live SDK instance returned by fs_dynamic_init().
	 */
	public static function apply( \Freemius $ed11ycsa ): void {
		self::$sdk = $ed11ycsa;
		$ed11ycsa->add_filter( 'show_first_trial_after_n_sec', array( __CLASS__, 'filter_trial_delay' ) );
		$ed11ycsa->add_filter( 'reshow_trial_after_every_n_sec', array( __CLASS__, 'filter_trial_delay' ) );
		$ed11ycsa->add_filter( 'show_admin_notice', array( __CLASS__, 'filter_show_admin_notice' ), 10, 2 );
	}

	/**
	 * Admin page slugs (`?page=`) the promotion may appear on: the
	 * settings page and the accessibility report dashboard.
	 *
	 * Only screens that exist in the free build are listed — the premium
	 * build never reaches the promotion. The Custom Rules / Crawler pages
	 * are premium-only, and referencing their classes (or
	 * FreemiusMenu::MENU_SLUG) here would fatal in the free build, where
	 * the strip removes those files.
	 *
	 * @return string[]
	 */
	private static function plugin_page_slugs(): array {
		return array(
			'ed11y',        // Form\SettingsPage.
			'editoria11y',  // Admin\Dashboard.
		);
	}

	/**
	 * Freemius `show_first_trial_after_n_sec` and
	 * `reshow_trial_after_every_n_sec` filter.
	 *
	 * @param int $seconds The SDK's default delay (ignored).
	 * @return int 0 to let the SDK add the promotion now; PHP_INT_MAX to skip it.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter) Filter signature; the gate replaces the SDK's timers outright.
	 */
	public static function filter_trial_delay( $seconds ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- filter signature; the gate replaces the SDK's timers outright.
		return ( self::is_plugin_screen() && ! self::shown_for_current_version() ) ? 0 : PHP_INT_MAX;
	}

	/**
	 * Freemius `show_admin_notice` filter.
	 *
	 * Passes every other notice through unchanged. The trial promotion
	 * is held back off the plugin's screens; on them it renders, then
	 * the version is marked as shown and the sticky removed so later
	 * page loads don't repeat it.
	 *
	 * @param bool                $show Whether the SDK would otherwise render this notice.
	 * @param array<string,mixed> $msg  Notice descriptor (see FS_Admin_Notice_Manager).
	 * @return bool
	 */
	public static function filter_show_admin_notice( $show, $msg ) {
		if ( true !== $show || self::NOTICE_ID !== ( $msg['id'] ?? '' ) ) {
			return $show;
		}

		if ( ! self::is_plugin_screen() ) {
			return false;
		}

		update_option( self::OPTION, Plugin::VERSION );

		if ( null !== self::$sdk ) {
			self::$sdk->remove_sticky( self::NOTICE_ID );
		}

		return $show;
	}

	/**
	 * Whether the current admin request is one of the plugin's screens.
	 *
	 * Reads `?page=` rather than get_current_screen() because the add
	 * runs on admin_init, before the screen is set up.
	 */
	private static function is_plugin_screen(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only check on a navigational query var; no state change here.
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		return '' !== $page && in_array( $page, self::plugin_page_slugs(), true );
	}

	/**
	 * Whether this site already showed the promotion for Plugin::VERSION.
	 */
	private static function shown_for_current_version(): bool {
		return Plugin::VERSION === get_option( self::OPTION, '' );
	}
}
