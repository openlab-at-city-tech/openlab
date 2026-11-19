<?php
/**
 * Fallback handler for `admin.php?page=ed11y-contact`.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Registers a hidden `admin.php?page=ed11y-contact` route that renders
 * a "where to get help" page whenever the SDK has chosen not to register
 * the slug itself.
 *
 * The Freemius SDK only registers the `ed11y-contact` submenu when
 * `should_add_submenu_or_action_links()` returns true — and only in the
 * matching admin context (network admin for a network-active plugin,
 * per-site admin for a delegated/non-network install). It nonetheless
 * emits hardcoded links to that slug from several places that the host
 * plugin can't intercept individually:
 *
 *   - The hardcoded "Need more sites, custom implementation and dedicated
 *     support?" section in the minified React pricing app
 *     (vendor/.../assets/js/pricing/freemius-pricing.js).
 *   - `contact_url('bug')` calls in license-cancelled / API-error /
 *     parent-install-error notices (class-freemius.php around lines
 *     17600–21500).
 *   - The deactivation feedback modal's "Anything we should know?" path.
 *   - Various sticky notices that link to the contact form when the SDK
 *     wants the user to escalate.
 *
 * Any of those links followed in a context where the SDK didn't register
 * the page lands on `user_can_access_admin_page()` (wp-admin/includes/
 * plugin.php:2175), which 403s on the missing `$_registered_pages` key
 * with "Sorry, you are not allowed to access this page." The most visible
 * case is per-site admin on a network-activated install:
 * `/sandbox-N/wp-admin/admin.php?page=ed11y-contact&topic=…`.
 *
 * Mechanism:
 *
 *   - Hook `admin_menu` and `network_admin_menu` at PHP_INT_MAX so we run
 *     after the SDK's `_prepare_admin_menu` (registered at
 *     WP_FS__LOWEST_PRIORITY = 999_999_999).
 *   - Register a hidden submenu via `add_submenu_page( null, … )`. With a
 *     null parent the page is added to `$_registered_pages` under the
 *     hookname `admin_page_ed11y-contact` without polluting `$submenu`
 *     or `$admin_page_hooks`. When the SDK *has* registered the contact
 *     page, WP's `get_plugin_page_hook()` resolves a different (SDK-side)
 *     hookname first and dispatches there; our hookname only matches when
 *     the SDK didn't register, so the two registrations coexist without
 *     stealing dispatch from the SDK in its happy path.
 *
 * Scope: free-build-safe. The SDK leaves `ed11y-contact` links in the
 * free build too (the deactivation feedback modal and several error-path
 * notices both call `contact_url()` regardless of plan), so this class
 * is NOT listed under `@fs_premium_only` in `editoria11y.php` and must
 * not call any premium-only seam.
 */
final class FreemiusContactFallback {

	/**
	 * Slug the SDK uses for its contact submenu: `{menu_slug}-contact`,
	 * derived from the unified menu slug exactly the way the SDK derives
	 * it (see FreemiusMenu). Building it from the shared constant keeps
	 * this fallback and the network-admin guard pointing at the same slug
	 * if the menu slug ever changes.
	 */
	const SLUG = FreemiusMenu::MENU_SLUG . '-contact';

	/**
	 * Wire the fallback registration to both per-site and network admin
	 * menu builds. Called once from `editoria11y.php`, outside the
	 * premium-only gate so it ships in the free build too.
	 */
	public static function register(): void {
		// PHP_INT_MAX > WP_FS__LOWEST_PRIORITY (999_999_999), so we
		// run after `_prepare_admin_menu()` has had its chance to
		// register the slug under the SDK's own (non-null) parent.
		add_action( 'admin_menu', array( __CLASS__, 'register_route' ), PHP_INT_MAX );
		add_action( 'network_admin_menu', array( __CLASS__, 'register_route' ), PHP_INT_MAX );
	}

	/**
	 * `(network_)admin_menu` callback: register the hidden fallback.
	 *
	 * Registration is unconditional. When the SDK has already registered
	 * `ed11y-contact` under its top-level menu, WP's
	 * `get_plugin_page_hook()` resolves the SDK's hookname first and our
	 * `admin_page_ed11y-contact` hook never fires for that request.
	 * When the SDK didn't register (activation mode, or a context where
	 * `should_add_submenu_or_action_links()` returned false), our
	 * hookname is the only match and our render callback runs.
	 *
	 * Capability mirrors the SDK's own (`manage_options`); the page
	 * contains no privileged data — only public support pointers — so
	 * a stricter cap would lock out exactly the users most likely to
	 * follow a stray contact link.
	 */
	public static function register_route(): void {
		// Now that the SDK menu is unified onto our settings page, the SDK
		// builds its contact links as a submenu of our parent — Settings
		// (options-general.php) per-site, settings.php in network admin. Match
		// that host file so we catch the stray `?page=ed11y-contact` links, and
		// register ONLY when the SDK has not already registered the page there
		// (otherwise both render callbacks fire on one request). In the free
		// build the SDK exposes "Contact Us" only as an external link, never a
		// ?page= route, so this fallback is what catches its stray links.
		// Parent selection is shared with the network-admin guard via
		// FreemiusMenu (finding A5) so an SDK menu-structure change is a
		// one-place edit.
		$parent = FreemiusMenu::sdk_parent_slug( fs_is_network_admin() );
		if ( isset( $GLOBALS['_registered_pages'][ get_plugin_page_hookname( self::SLUG, $parent ) ] ) ) {
			return;
		}

		// phpcs:ignore WordPress.WP.CapabilityChecks.RoleFound -- mirrors the SDK's own contact-page capability so the fallback never refuses where the real page would have allowed access.
		$hook = add_submenu_page(
			$parent,
			__( 'Contact Editoria11y', 'editoria11y' ),
			__( 'Contact Editoria11y', 'editoria11y' ),
			'manage_options',
			self::SLUG,
			array( __CLASS__, 'render_page' )
		);

		// Enqueue our admin stylesheet on this page only. `set_title()` primes
		// the global `$title` defensively against the PHP 8.1+
		// `strip_tags( null )` deprecation in admin-header.php — it's belt-and-
		// suspenders now that the page is a real (CSS-hidden) submenu of
		// Settings rather than a `parent_slug = ''` hidden page, but harmless,
		// and still covers any context where the SDK leaves the title unset.
		if ( false !== $hook && '' !== (string) $hook ) {
			add_action( "load-{$hook}", array( __CLASS__, 'set_title' ) );
			add_action( "load-{$hook}", array( __CLASS__, 'enqueue_assets' ) );
		}
	}

	/**
	 * `load-{hookname}` callback: prime the global `$title` so
	 * admin-header.php's `strip_tags( $title )` doesn't see null.
	 *
	 * @see register_route() — defensive title prime; harmless on the normal
	 *      submenu registration, covers any context that leaves it unset.
	 */
	public static function set_title(): void {
		global $title;
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Intentional: primes the core $title global for a hidden-registered (parent_slug='') SDK page so admin-header.php's strip_tags($title) doesn't hit null. No public API sets a hidden page's title.
		$title = __( 'Contact Editoria11y', 'editoria11y' );
	}

	/**
	 * `load-{hookname}` callback: attach the shared admin stylesheet.
	 *
	 * Mirrors the enqueue used by the main settings page (see
	 * `Form\SettingsPage::enqueue_styles_scripts()`) so this fallback
	 * page inherits the same typography, spacing, and link treatments
	 * as the rest of the plugin's admin UI. Queued from `load-` rather
	 * than `admin_enqueue_scripts` so it only loads on this hidden page,
	 * and uses the same handle so a second registration is a no-op if
	 * SettingsPage's enqueue ever fires alongside us.
	 */
	public static function enqueue_assets(): void {
		wp_enqueue_style(
			'editoria11y-wp-css',
			trailingslashit( ED11Y_ASSETS ) . 'css/editoria11y-wp-admin.css',
			null,
			Plugin::VERSION
		);
	}

	/**
	 * Render callback for the fallback page.
	 *
	 * Mirrors the "Contacts" section from editoria11y.com (stripped of
	 * Bootstrap utility classes and icon-link SVGs). Output is hand-built
	 * with `esc_*()` / `wp_kses()` wrappers rather than a template partial
	 * — the page is intentionally tiny and free-build-safe (no dependency
	 * on the CSA-only Form/ stack).
	 *
	 * The SDK's `topic` query param (`pre_sale_question`, `bug`, etc.)
	 * is ignored; the link list below covers every actionable case.
	 */
	public static function render_page(): void {
		$allowed = array(
			'a' => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
			),
		);

		// Slack / @itmaybejj links built up here so the sprintf
		// translator string only contains numbered placeholders, not
		// inline HTML — keeps the .po entry readable for translators.
		$itmaybejj      = '<a href="https://github.com/itmaybejj" target="_blank" rel="noopener noreferrer">@itmaybejj</a>';
		$drupal_slack   = '<a href="https://www.drupal.org/join-slack" target="_blank" rel="noopener noreferrer">#Drupal</a>';
		$wp_slack       = '<a href="https://make.wordpress.org/chat/" target="_blank" rel="noopener noreferrer">#WordPress</a>';
		$wpcampus_slack = '<a href="https://wpcampus.org/community-3/slack/" target="_blank" rel="noopener noreferrer">#WPCampus</a>';
		$digicol_slack  = '<a href="https://membership.digicol.org/join-slack/" target="_blank" rel="noopener noreferrer">#DigiCol</a>';

		$forums_link = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( 'https://github.com/itmaybejj/editoria11y/discussions' ),
			esc_html__( 'Editoria11y library forums', 'editoria11y' )
		);

		echo '<div class="wrap ed11y-banner">';
		echo '<h1>' . esc_html__( 'Contact Editoria11y', 'editoria11y' ) . '</h1>';

		echo '<p>' . wp_kses(
			sprintf(
				/* translators: 1: @itmaybejj GitHub profile link, 2: #Drupal Slack invite link, 3: #WordPress Slack chat link, 4: #WPCampus Slack invite link, 5: #DigiCol Slack invite link. */
				__( 'Lead maintainer %1$s can usually be found on Slack for informal support, configuration tips and debugging any time the sun is up in the western hemisphere in %2$s, %3$s, %4$s and %5$s.', 'editoria11y' ),
				$itmaybejj,
				$drupal_slack,
				$wp_slack,
				$wpcampus_slack,
				$digicol_slack
			),
			$allowed
		) . '</p>';

		echo '<p>' . wp_kses(
			sprintf(
				/* translators: %s: link to the Editoria11y library GitHub discussions page. */
				__( 'Check the %s for roadmap proposals and discussions.', 'editoria11y' ),
				$forums_link
			),
			$allowed
		) . '</p>';

		echo '<h2><strong>' . esc_html__( 'Formal support:', 'editoria11y' ) . '</strong></h2>';
		echo '<ul>';
		echo '<li><a href="' . esc_url( 'https://github.com/itmaybejj/editoria11y/issues' ) . '" target="_blank" rel="noopener noreferrer">'
			. esc_html__( 'Editoria11y Library issue queue', 'editoria11y' ) . '</a></li>';
		echo '<li><a href="' . esc_url( 'https://github.com/itmaybejj/editoria11y-wp/issues' ) . '" target="_blank" rel="noopener noreferrer">'
			. esc_html__( 'Editoria11y WordPress issue queue', 'editoria11y' ) . '</a></li>';
		echo '<li><a href="' . esc_url( 'https://www.drupal.org/project/issues/editoria11y' ) . '" target="_blank" rel="noopener noreferrer">'
			. esc_html__( 'Editoria11y Drupal issue queue', 'editoria11y' ) . '</a></li>';
		echo '<li><a href="' . esc_url( 'https://form.jotform.com/260627909360158' ) . '" target="_blank" rel="noopener noreferrer">'
			. esc_html__( 'Submit a support request', 'editoria11y' ) . '</a></li>';
		echo '</ul>';

		echo '</div>';
	}
}
