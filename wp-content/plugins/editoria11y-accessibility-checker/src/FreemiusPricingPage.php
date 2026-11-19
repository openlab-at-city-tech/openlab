<?php
/**
 * Pricing-page UX customizations against the bundled Freemius SDK.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Hide the SDK's "Need more sites?" section on the pricing page and
 * inject an enterprise-checkout banner in its place.
 *
 * Targets the SDK-rendered admin page at `admin.php?page=ed11y-pricing`.
 * Two upstream behaviors this class works around without forking vendor/:
 *
 *   1. The SDK ships a hardcoded "Need more sites, custom implementation
 *      and dedicated support?" section in its React pricing app
 *      (vendor/.../assets/js/pricing/freemius-pricing.js around offset
 *      293483). The section renders a `<section class="fs-section
 *      fs-section--custom-implementation">` with a link to
 *      admin.php?page=ed11y-contact.
 *
 *      That contact submenu is registered conditionally — see
 *      Freemius::add_submenu_items() in vendor/.../class-freemius.php
 *      around line 18911, gated on should_add_submenu_or_action_links()
 *      returning true. That function early-returns false in activation
 *      mode (line 18831), and a `is_premium_only` plugin is in activation
 *      mode on every site that does not yet have an active license. So
 *      the SDK renders a link to a slug it never registered, WP returns
 *      403, and admin-header.php trips a strip_tags(null) deprecation
 *      under PHP 8.1+. We sidestep the broken link by hiding the SDK's
 *      section (CSS, see assets/css/editoria11y-pricing.css) and
 *      injecting our own enterprise CTA in its place (via the
 *      `templates/pricing.php` filter below). Enterprise customers go
 *      through our own checkout, not the SDK's contact form.
 *
 *   2. There is also a real bug in the SDK's React price formatter where
 *      `annual_price / 12` values >= 1000 render as "$1.58" instead of
 *      "$1,666.58" — `parseInt("1,666", 10)` returns 1 because parseInt
 *      stops at the thousands comma. This class does not fix that
 *      (it lives in the minified vendor JS); it is a Freemius upstream
 *      issue. Keeping the largest `annual_price / 12` under $1,000
 *      avoids the bug; the unlimited and 1000-site tiers currently
 *      exceed that threshold. See the parent ticket for details.
 *
 * Scope: BOTH builds. This is a freemium plugin (`has_paid_plans => true`),
 * so the SDK pricing page is reachable in the free build too — a free user who
 * clicks Upgrade lands on `admin.php?page=ed11y-pricing`, where the broken
 * "Need more sites?" link and the missing conversion message matter just as
 * much as in the premium build. This class and `assets/css/editoria11y-pricing.css`
 * are therefore intentionally kept off the `editoria11y.php` premium-only file
 * strip list, and the `apply()` call sits outside the `is__premium_only()`
 * gate. (The `set_pricing_title()` deprecation guard below
 * only fires on the SDK's hidden-registration code path; on a normally
 * registered pricing page it simply never runs, which is harmless.)
 */
final class FreemiusPricingPage {

	/**
	 * Called once at SDK bootstrap from `editoria11y.php`, next to
	 * `FreemiusAccessControl::apply()` and `FreemiusOverrides::apply()`.
	 *
	 * The `$ed11ycsa->add_filter(...)` calls route through Freemius's
	 * slug-aware filter wrapper, so the filter tag is namespaced to this
	 * plugin's instance — other Freemius-using plugins on the same site
	 * are unaffected.
	 *
	 * @param \Freemius $ed11ycsa Live SDK instance from fs_dynamic_init().
	 */
	public static function apply( \Freemius $ed11ycsa ): void {
		// 1. CSS path filter. The SDK's pricing.php enqueues whatever
		// absolute filesystem path this filter returns
		// (vendor/.../templates/pricing.php around line 71), converting
		// it to a URL via fs_asset_url(). We hand it a small stylesheet
		// that hides `.fs-section--custom-implementation`. The filter
		// only fires on the pricing-page render, so this CSS never
		// loads on any other admin screen.
		$ed11ycsa->add_filter(
			'pricing/css_path',
			array( __CLASS__, 'pricing_css_path' )
		);

		// 2. Template filter. The SDK applies this filter to the full
		// rendered output of `templates/pricing.php` (see
		// vendor/.../class-freemius.php around line 23470). We PREPEND our
		// enterprise banner before that output — the banner is a
		// self-contained `.ed11ycsa-enterprise-banner` block styled by
		// assets/css/editoria11y-pricing.css, so it does not depend on
		// living inside the SDK's `#fs_pricing` wrap. (If it should ever
		// inherit that wrap's max-width/margins, splice it after the
		// `#fs_pricing` opener instead — a deliberate change, not the
		// current behavior. See finding B3.)
		$ed11ycsa->add_filter(
			'templates/pricing.php',
			array( __CLASS__, 'inject_enterprise_banner' ),
			10
		);

		// 3. PHP 8.1+ `strip_tags(null)` deprecation guard (defensive).
		//
		// With the menu unified onto our settings page, the SDK registers the
		// pricing page as a visible submenu of Settings — hookname
		// `settings_page_ed11y-pricing` — and `get_admin_page_title()` finds it
		// in `$submenu`, so `$title` is set and there's no null to strip. We
		// still prime `$title` from the page's `load-` action as belt-and-
		// suspenders: it's harmless when the title is already set, and covers
		// the SDK's hidden-registration edge (`parent_slug = ''`, which only
		// happens for `is_only_premium` modules — not this freemium build, but
		// cheap insurance if that ever changes). `load-{hookname}` fires from
		// admin.php right before admin-header.php is included, so the
		// assignment lands in time for the `strip_tags( $title )` call.
		add_action( 'load-settings_page_ed11y-pricing', array( __CLASS__, 'set_pricing_title' ) );

		// 4. Network-admin upgrade URL (free build only). In network admin the
		// SDK builds the pricing/upgrade URL from the menu's per-site parent —
		// `network/options-general.php?page=ed11y-pricing` — but
		// options-general.php has no network-admin equivalent (it's settings.php
		// there), so that URL 404s. The premium build routes this to its own
		// NetworkLicensePage under settings.php; that page is @fs_premium_only,
		// so the free build has no in-WP network pricing surface. Send network
		// super-admins to the supporter page to purchase instead (they then
		// install the premium build network-wide). Per-site admin is untouched —
		// the in-WP pricing page works there. The is_premium() gate keeps this a
		// free-build-only fallback: in the premium build NetworkLicensePage owns
		// the redirect, so this no-ops.
		$ed11ycsa->add_filter(
			'pricing_url',
			array( __CLASS__, 'network_upgrade_url' )
		);
	}

	/**
	 * `pricing_url` filter: in the FREE build's network admin, replace the SDK's
	 * 404ing `network/options-general.php?page=ed11y-pricing` upgrade URL with
	 * the supporter page. See item 4 in {@see apply()} for the rationale.
	 *
	 * @param string $url SDK-computed pricing/upgrade URL.
	 * @return string The supporter URL in free-build network admin; $url otherwise.
	 */
	public static function network_upgrade_url( $url ) {
		if (
			function_exists( 'fs_is_network_admin' ) &&
			fs_is_network_admin() &&
			function_exists( 'ed11ycsa' ) &&
			! ed11ycsa()->is_premium()
		) {
			return 'https://editoria11y.com/codes?src=wpm';
		}
		return $url;
	}

	/**
	 * `load-settings_page_ed11y-pricing` callback: prime the global `$title`
	 * so admin-header.php's `strip_tags( $title )` never sees null. See the
	 * rationale comment in {@see apply()}.
	 */
	public static function set_pricing_title(): void {
		global $title;
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Intentional: primes the core $title global for a hidden-registered (parent_slug='') SDK page so admin-header.php's strip_tags($title) doesn't hit null. No public API sets a hidden page's title.
		$title = __( 'Editoria11y CSA pricing', 'editoria11y' );
	}

	/**
	 * Resolve the absolute filesystem path of our pricing CSS file.
	 *
	 * `fs_asset_url()` (called by pricing.php after this filter
	 * returns) expects an absolute path on disk and converts it to a
	 * URL with the appropriate `content_url()` / `plugins_url()`
	 * prefix. `__DIR__` here is `…/editoria11y-wp-csa/src`, so
	 * `dirname(__DIR__)` is the plugin root.
	 *
	 * @return string Absolute path to editoria11y-pricing.css.
	 */
	public static function pricing_css_path(): string {
		return dirname( __DIR__ ) . '/assets/css/editoria11y-pricing.css';
	}

	/**
	 * Prepend an enterprise-checkout banner to the rendered pricing
	 * page.
	 *
	 * @param string $html Rendered output of vendor/.../templates/pricing.php.
	 * @return string Modified HTML with the banner prepended before the SDK output.
	 */
	public static function inject_enterprise_banner( $html ) {
		$banner = self::render_enterprise_banner();
		if ( '' === $banner ) {
			return $html;
		}
		return $banner . $html;
	}

	/**
	 * Return the enterprise-banner HTML, or empty string to no-op.
	 *
	 * Output is inserted into the pricing page as-is. If you ever
	 * interpolate user-controlled or option-stored content into the
	 * banner, route it through wp_kses_post() (or stricter) inside
	 * `render_enterprise_banner_html()` — this method's return value
	 * is treated as trusted HTML by `inject_enterprise_banner()`.
	 *
	 * Return `''` to skip injection entirely (e.g. when toggling
	 * the banner off behind a future feature flag) — the CSS hide of
	 * the SDK section is unaffected and the user simply sees the
	 * pricing grid with no replacement block underneath.
	 */
	private static function render_enterprise_banner(): string {
		return self::render_enterprise_banner_html();
	}

	/**
	 *
	 * Kept as a separate method (rather than inlined into
	 * render_enterprise_banner()) so the wrapper above stays a
	 * stable seam if you later want to gate the banner behind a
	 * filter, option, or capability check — that logic goes in the
	 * caller, and this method stays a dumb HTML returner.
	 */
	private static function render_enterprise_banner_html(): string {
		$inline_strong = array( 'strong' => array() );

		$heading = esc_html__( 'Support the project', 'editoria11y' );

		$p_free = wp_kses(
			__( 'Editoria11y promotes accessibility in a unique way. Its tools are highly effective at helping non-technical authors prepare content that can be enjoyed equally by disabled Web users. We consider this a public good, so <strong>Editoria11y will always be free to use</strong>.', 'editoria11y' ),
			$inline_strong
		);

		$p_not_free = esc_html__( 'Editoria11y is not, however, free to develop or support.', 'editoria11y' );

		$p_csa = esc_html__( 'The Community Supported Add-ons (CSA) project fills the gap: project members support the development of the Editoria11y library, its CMS plugins, and the CSA suite: a rapidly growing set of quality assurance tools that provide similar functionality to commercial products, open-source and on-prem. In return, they get access to premium features and support.', 'editoria11y' );

		$supporters_link = sprintf(
			'<a href="%1$s"><strong>%2$s</strong></a>',
			esc_url( 'https://editoria11y.com/license/' ),
			esc_html__( 'lower (or higher!) support levels', 'editoria11y' )
		);

		$p_contribute = wp_kses(
			sprintf(
				/* translators: %s: link to the CSA supporters page and drop the "farmshare" word in languages without the concept. */
				__( 'This is a <strong>contribute what you can</strong> "farmshare" model. The Editoria11y LLC site has options for %s, options to contribute as a coder or tester instead, and options for cross-platform and unlimited-site licenses.', 'editoria11y' ),
				$supporters_link
			),
			array(
				'strong' => array(),
				'a'      => array( 'href' => array() ),
			)
		);

		return sprintf(
			'<div class="ed11ycsa-enterprise-banner"><h1>%1$s</h1><p>%2$s</p><p>%3$s</p><p>%4$s</p><p>%5$s</p></div>',
			$heading,
			$p_free,
			$p_not_free,
			$p_csa,
			$p_contribute
		);
	}
}
