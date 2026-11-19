<?php
/**
 * Single source of truth for how the bundled Freemius SDK structures its
 * admin menu around this plugin: the unified menu slug, the sub-page slugs
 * the SDK derives from it, and the parent file it registers them under.
 *
 * Why this exists (findings A4/A5): this knowledge previously lived in two
 * independent copies — the guarded-slug list in {@see FreemiusAccessControl}
 * and the parent-slug selection in {@see FreemiusContactFallback}. A future
 * SDK build that changes slug derivation or menu parenting would have to be
 * found and fixed in both places, and the permission guard would silently
 * stop matching (falling back to the SDK's per-site `manage_options` check —
 * the exact hole it exists to close). Centralizing here means one edit, and
 * the drift test in tests/class-test-freemius-sdk-slugs.php pins the SDK
 * side of the contract so a vendored-SDK bump that breaks it fails CI.
 *
 * SDK contract encoded here (validated against the version pinned in
 * FreemiusAccessControl::SDK_KNOWN_GOOD_MAX):
 *
 *   - Sub-page slugs are `{menu_slug}-{page}` — see
 *     `FS_Admin_Menu_Manager::get_slug( $page )` in
 *     vendor/freemius/wordpress-sdk/includes/managers/class-fs-admin-menu-manager.php
 *     (`$this->_menu_slug . '-' . $page`).
 *   - The SDK registers exactly five sub-pages, via `add_submenu_item()` in
 *     vendor/.../class-freemius.php: account, contact, pricing, addons,
 *     affiliation.
 *   - With the menu unified onto our settings page, the SDK parents its
 *     entries under the per-site Settings screen (`options-general.php`) and
 *     the network Settings screen (`settings.php`) in network admin.
 *
 * Build scope: free-build-safe. {@see FreemiusContactFallback} (which ships
 * in the free build) consumes this class, so it must NOT be listed under
 * `@fs_premium_only` in editoria11y.php and must not call premium-only seams.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Freemius SDK menu-structure constants + derivation helpers.
 */
final class FreemiusMenu {

	/**
	 * The unified menu slug passed to fs_dynamic_init() (`menu.slug`).
	 *
	 * Must equal the slug of the settings page the plugin itself registers
	 * (see Form\SettingsPage::register_settings_menu) — the SDK's redirects
	 * target this slug, and pointing it at a page that only exists in
	 * activation mode is the historical source of the 403s. The invariant
	 * is pinned by tests/class-test-freemius-menu.php and
	 * tests/class-test-freemius-sdk-slugs.php.
	 */
	const MENU_SLUG = 'ed11y';

	/**
	 * The page suffixes the SDK appends to the menu slug for its sub-pages.
	 * Order mirrors the registration order in class-freemius.php.
	 */
	const SDK_SUBPAGE_SUFFIXES = array(
		'account',
		'contact',
		'pricing',
		'addons',
		'affiliation',
	);

	/**
	 * The full `?page=` slugs of the SDK's sub-pages, derived exactly the
	 * way the SDK derives them (`{menu_slug}-{page}`).
	 *
	 * @return array<int,string>
	 */
	public static function sdk_subpage_slugs(): array {
		return array_map(
			static function ( string $suffix ): string {
				return self::MENU_SLUG . '-' . $suffix;
			},
			self::SDK_SUBPAGE_SUFFIXES
		);
	}

	/**
	 * The parent file the SDK registers its submenu entries under in the
	 * given admin context. Callers typically pass `fs_is_network_admin()`.
	 *
	 * @param bool $is_network_admin Whether the current context is network admin.
	 */
	public static function sdk_parent_slug( bool $is_network_admin ): string {
		return $is_network_admin ? 'settings.php' : 'options-general.php';
	}

	/**
	 * The `?page=` slug of the CSA build's network License page.
	 *
	 * Duplicates Form\NetworkLicensePage::SLUG because that class is
	 * `@fs_premium_only`-stripped from the free build, and free-build-safe
	 * code ({@see FreemiusNetworkUrls}) needs the slug without referencing
	 * the class. Drift is pinned by
	 * tests/class-test-freemius-menu.php::test_network_license_slug_matches_license_page.
	 */
	const NETWORK_LICENSE_SLUG = 'ed11y-network-license';
}
