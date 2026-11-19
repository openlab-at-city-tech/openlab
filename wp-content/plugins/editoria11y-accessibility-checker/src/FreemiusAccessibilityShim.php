<?php
/**
 * Accessibility shim for Freemius SDK admin chrome.
 *
 * The bundled Freemius SDK renders admin notices and modal dialogs
 * with markup that fails several WCAG criteria — most visibly:
 *
 *   - The `.fs-close` dismiss control on sticky admin notices is a
 *     `<div>` with no `role`, no `tabindex`, and no keyboard
 *     activation, leaving it inaccessible to keyboard and AT users
 *     ([admin-notice.php:99](vendor/freemius/wordpress-sdk/templates/admin-notice.php#L99)).
 *   - The same `.fs-close` selector in modal forms (license activation,
 *     opt-out, email update, etc.) is rendered as `<a href="!#">`,
 *     which IS focusable but uses an invalid URL and lacks button
 *     semantics.
 *   - `templates/forms/resend-key.php:80` uses `tabindex="3"` — a
 *     positive value that breaks natural tab order (WCAG 2.4.3).
 *   - Default styling sets the icon color to `#aaa` (~2.5:1 against
 *     white — fails WCAG 1.4.11).
 *   - No `:focus-visible` styling at all.
 *   - The decorative `<i class="dashicons">` icon child has no
 *     `aria-hidden`, so AT announces "Dashicon dashicons-no" first.
 *
 * Freemius's filter surface for admin notices only covers the
 * message and title content (`sticky_message_{id}` /
 * `sticky_title_{id}` hooks) — the surrounding chrome is template
 * code outside any filter. That leaves three options: fork the
 * vendor templates (wiped on `composer update`), file an upstream
 * PR (long-tail), or ship a JS+CSS shim that runs site-wide on
 * admin pages. We do the shim immediately and the upstream PR in
 * parallel.
 *
 * The shim's enqueue scope is every admin page because Freemius
 * renders its own notices independently of the active screen
 * (license-expired sticky, trial promotion banner, opt-in dialog).
 * Asset weight is small (<2 KB combined) and there are no network
 * calls, so the cost of shipping it everywhere is negligible.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the JS+CSS pair that normalizes Freemius admin chrome
 * into a WCAG-conformant baseline.
 */
final class FreemiusAccessibilityShim {

	/**
	 * Stable enqueue handles. Constants because the test suite reads
	 * them and downstream code may want to add a dependency.
	 */
	const HANDLE_CSS = 'editoria11y-admin-pages';
	const HANDLE_JS  = 'editoria11y-admin-pages';

	/**
	 * Hook the enqueue callback onto every admin page load.
	 *
	 * Called from `Plugin::admin()` so this class is dormant on the
	 * frontend and doesn't run when the plugin is loaded for non-admin
	 * contexts (REST, cron, etc.).
	 */
	public static function register(): void {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	/**
	 * Enqueue the shim CSS and JS.
	 *
	 * No `$hook_suffix` filtering — Freemius can inject notices on any
	 * admin screen, so the shim has to be present everywhere. Both
	 * assets are static files served from the plugin's `assets/`
	 * directory; no inline data, no AJAX dependency.
	 */
	public static function enqueue(): void {
		wp_enqueue_style(
			self::HANDLE_CSS,
			trailingslashit( ED11Y_ASSETS ) . 'css/editoria11y-admin-pages.css',
			array(),
			Plugin::VERSION
		);
		wp_enqueue_script(
			self::HANDLE_JS,
			trailingslashit( ED11Y_ASSETS ) . 'js/editoria11y-admin-pages.js',
			array(),
			Plugin::VERSION,
			true
		);
	}
}
