<?php
/**
 * Rewrite SDK-built URLs that target the phantom network-admin parent.
 *
 * The SDK builds its page URLs from the fs_dynamic_init() menu parent
 * (`options-general.php`) verbatim: `_get_admin_page_url()` hands that
 * parent file to `network_admin_url()` in network context, producing
 * `wp-admin/network/options-general.php?page=ed11y` — a parent FILE that
 * does not exist in network admin (its Settings parent is `settings.php`),
 * so the browser lands on a bare server "File not found." Observed live
 * (WP 6.x / SDK 2.13.4): network-activating the plugin redirected there
 * via `_redirect_on_activation_hook()` → `get_activation_url()`.
 *
 * A parent-file rewrite to `settings.php?page=ed11y` is not enough: with
 * the connection delegated (FreemiusAccessControl::force_network_delegation)
 * the SDK never registers a network-admin connect page, so that URL 403s
 * ("Sorry, you are not allowed…" — verified live). The correct landing is
 * the plugin's own network page: the License page in the CSA build (the
 * next step of the network activation workflow) or the network Defaults
 * page in the free build.
 *
 * The interception seam is the SDK's `connect_url` filter — the documented
 * hook applied in `get_activation_url()`, which is the only builder of the
 * broken URL we have observed. The callback no-ops on anything that does
 * not match the phantom-parent shape, so per-site connect links (sticky
 * notice "Complete activation" links etc.) pass through untouched.
 *
 * Build scope: free-build-safe — network activation of the free build hits
 * the same phantom parent. Must NOT be listed under `@fs_premium_only` and
 * must not reference stripped classes; the License slug therefore comes
 * from {@see FreemiusMenu::NETWORK_LICENSE_SLUG}, not
 * NetworkLicensePage::SLUG (drift between the two is pinned by
 * tests/class-test-freemius-menu.php).
 *
 * @package Editoria11y
 */

namespace Editoria11y;

use Editoria11y\Form\NetworkSettingsPage;

defined( 'ABSPATH' ) || exit;

/**
 * Redirects SDK network-admin links away from the nonexistent
 * `network/options-general.php` parent.
 */
final class FreemiusNetworkUrls {

	/**
	 * Register the `connect_url` filter against the live SDK instance.
	 *
	 * @param \Freemius $ed11ycsa Live SDK instance returned by fs_dynamic_init().
	 */
	public static function apply( \Freemius $ed11ycsa ): void {
		$ed11ycsa->add_filter( 'connect_url', array( __CLASS__, 'fix_network_connect_url' ) );
	}

	/**
	 * `connect_url` filter callback.
	 *
	 * Leaves every URL untouched except the phantom-parent shape, which is
	 * replaced with the plugin's own network page URL.
	 *
	 * @param mixed $url URL computed by the SDK (string in practice).
	 * @return mixed
	 */
	public static function fix_network_connect_url( $url ) {
		if ( ! is_string( $url ) || false === strpos( $url, '/network/options-general.php' ) ) {
			return $url;
		}

		return network_admin_url( 'settings.php?page=' . self::network_landing_slug() );
	}

	/**
	 * The `?page=` slug of the network page an SDK network redirect should
	 * land on: License in the CSA build, network Defaults in the free build
	 * (where the License page does not exist).
	 */
	private static function network_landing_slug(): string {
		$is_premium = function_exists( 'ed11ycsa' ) && \ed11ycsa()->is_premium();

		return $is_premium ? FreemiusMenu::NETWORK_LICENSE_SLUG : NetworkSettingsPage::SLUG;
	}
}
