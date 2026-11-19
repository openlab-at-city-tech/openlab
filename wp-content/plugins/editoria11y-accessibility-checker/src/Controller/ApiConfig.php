<?php
/**
 * REST endpoint for the static, browser-cacheable configuration payload.
 *
 * Returns the site-wide configuration that does not vary per request: the
 * passthrough settings, the test-name catalog, and (in subsequent commits)
 * the global "okAll" dismissals plus the CSA additions. The browser caches
 * the response for 30 days, and cache-busting is driven by a `?v=<n>` query
 * parameter — `n` is the `ed11y_config_version` option.
 *
 * Mirrors `Drupal\editoria11y\Controller\Ed11yConfigController`.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Controller;

use Editoria11y\CustomRules;
use Editoria11y\NetworkCustomRules;
use Editoria11y\TestNames;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

/**
 * REST controller for `GET /ed11y/v1/config`.
 */
class ApiConfig {

	/** Route path used for filter scoping (matches WP_REST_Request::get_route()). */
	const ROUTE = '/ed11y/v1/config';

	/**
	 * Set during set_cache_headers() when our route is in flight, then read
	 * (and reset) by the rest_send_nocache_headers filter below. Without
	 * this two-step coordination, our 30-day Cache-Control is emitted by
	 * send_headers() and then immediately overridden by WP_REST_Server.
	 *
	 * @var bool
	 */
	private static $suppress_nocache = false;

	/**
	 * Wires the route registration and the cache-header filters.
	 *
	 * The two filters work together: `rest_post_dispatch` sets our header
	 * AND signals to the second filter that this request is the one we
	 * want to keep cacheable. `rest_send_nocache_headers` fires later in
	 * `WP_REST_Server::serve_request()`, after `send_headers()` has
	 * already emitted our header — without the second filter, WP loops
	 * the nocache headers via `send_header()` (which calls PHP's
	 * `header()` with replace=true) and clobbers our Cache-Control.
	 *
	 * Both filters are global; both scope by route internally so other
	 * REST endpoints keep their default no-cache behavior.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_filter( 'rest_post_dispatch', array( __CLASS__, 'set_cache_headers' ), 10, 3 );
		add_filter( 'rest_send_nocache_headers', array( __CLASS__, 'suppress_nocache_headers' ), 10, 1 );
	}

	/**
	 * Register the GET /ed11y/v1/config route.
	 */
	public function register_routes() {
		register_rest_route(
			'ed11y/v1',
			'/config',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_config' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
	}

	/**
	 * Same gate the `ed11y_load_scripts()` enqueue uses: any user the plugin
	 * would offer the in-page checker to should also be able to read the
	 * config blob the checker needs to initialize.
	 *
	 * `current_user_can( 'edit_posts' )` covers administrators, editors,
	 * authors, and contributors out of the box, plus any custom role with
	 * that capability. Anonymous users get `false`, which WP turns into a 401.
	 *
	 * @return bool
	 */
	public function permissions_check() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Build the static config payload.
	 *
	 * Composed from up to four layers, all site-wide (no per-request data —
	 * that stays inline in the per-page `<script id="editoria11y-init">`
	 * blob):
	 *
	 *   1. Static settings — derived from `ed11y_plugin_settings` via the
	 *      shared `ed11y_get_static_settings()` helper, which is also what
	 *      the per-page payload uses, so the two views can never disagree.
	 *   2. `testNames` — the full UPPER_SNAKE → translated label catalog from
	 *      `Editoria11y\TestNames::core_names()`.
	 *   3. `globalDismissals` — the `okAll` branch of the dismissal table.
	 *      Page-scoped (`ok` / `hide`) dismissals stay in the per-page
	 *      payload because they vary per request + user. The JS shim merges
	 *      both halves into a single dictionary before constructing Ed11y.
	 *   4. CSA-only additions — emitted when `ed11y_is_csa_active()` is true.
	 *      Mirrors Drupal's `editoria11y_csa_editoria11y_alter_global_config`
	 *      hook payload: developer-mode panel behavior, the per-test routing
	 *      CSVs (so the bundled JS knows which alerts to surface for the
	 *      developer profile), and the contrast-exemption selectors.
	 *      The per-user `profile` field remains dynamic.
	 *
	 * Subsequent commits add `ignore_tests` (derived from a future
	 * `tests_off` CSV-to-array projection) and `custom_rules` (admin-defined
	 * tests).
	 *
	 * @param WP_REST_Request $request The incoming request (the `?v=` query
	 *                                 param is the browser's cachebust
	 *                                 signal, not a server-side dispatch
	 *                                 dimension).
	 * @return WP_REST_Response
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function get_config( WP_REST_Request $request ): WP_REST_Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$payload                     = ed11y_get_static_settings();
		$payload['testNames']        = TestNames::core_names();
		$payload['globalDismissals'] = ed11y_get_global_dismissals();

		// CSA payload additions wrapped in the preprocessor gate so the
		// free build never references the CustomRules class (stripped via
		// @fs_premium_only) and never emits CSA-only keys the free JS
		// has no consumer for. The runtime `ed11y_is_csa_active()` check
		// gates trial/license state inside the premium build.

		return new WP_REST_Response( $payload, 200 );
	}

	/**
	 * Merge per-site and network-defined custom rules into the single
	 * list shipped to the browser.
	 *
	 * Resolution rules:
	 *
	 *   - Locked network rule wins outright over any per-site rule with
	 *     the same id; site cannot disable it.
	 *   - Unlocked network rule wins UNLESS the site has explicitly
	 *     disabled its id (via {@see NetworkCustomRules::set_site_disabled()})
	 *     OR has a per-site rule with the same id (then the per-site
	 *     rule overrides the network one).
	 *   - Per-site rules with no network counterpart pass through unchanged.
	 *
	 * The `_locked` flag is consumed here and stripped before the rules
	 * reach the browser via {@see transform_custom_rule()}.
	 *
	 * @return array<int, array<string, mixed>>
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.ShortVariable)
	 */
	public static function merge_site_and_network_rules(): array {
		// Sentinel-variable wrap so the free-build preprocessor can strip
		// the inner block (which references CustomRules + NetworkCustomRules,
		// both removed via @fs_premium_only) without leaving the negated
		// form the preprocessor does not understand. See the freemius skill.
		$merged = array();

		return array_values( $merged );
	}

	/**
	 * Translate WP-storage-shape custom rules into the camelCase shape the
	 * bundled library's `prepareCustomRuleset()` expects.
	 *
	 * Mirrors Drupal's `editoria11y_csa_editoria11y_alter_global_config()`
	 * transform: snake_case → camelCase, `element_set` CSV → array,
	 * `case_sensitive` → bool, `include_text` / `exclude_text` already
	 * arrays in WP storage so we pass them through.
	 *
	 * @param array<int, array<string, mixed>> $rules Validated WP-storage-shape rules.
	 * @return array<int, array<string, mixed>>
	 */
	public static function transform_custom_rules( array $rules ): array {
		return array_map( array( __CLASS__, 'transform_custom_rule' ), $rules );
	}

	/**
	 * Transform a single custom-rule record. Pulled out of the looping
	 * helper above so PHPMD's cyclomatic-complexity counter sees one
	 * straight-line key lookup per field rather than eleven nested
	 * ternaries inside a `foreach`.
	 *
	 * @param array<string, mixed> $rule Validated WP-storage-shape rule.
	 * @return array<string, mixed>
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod) Called via array_map's
	 *   callable-array form, which PHPMD's flow analyzer doesn't trace.
	 */
	private static function transform_custom_rule( array $rule ): array {
		$str = static function ( $rule, string $key, string $fallback = '' ): string {
			return isset( $rule[ $key ] ) ? (string) $rule[ $key ] : $fallback;
		};
		$arr = static function ( $rule, string $key ): array {
			return isset( $rule[ $key ] ) && is_array( $rule[ $key ] )
				? array_values( array_map( 'strval', $rule[ $key ] ) )
				: array();
		};

		$element_set_csv = $str( $rule, 'element_set' );
		$element_set     = array_values( array_filter( array_map( 'trim', explode( ',', $element_set_csv ) ) ) );
		if ( empty( $element_set ) ) {
			// An empty stored set means "no element restriction", but the
			// library's checkCustomRuleset() iterates elementSet — an empty
			// array silently never executes the rule at all. Map the
			// unrestricted state to the 'Everything' preset so selector-only
			// and include-text-only rules actually run.
			$element_set = array( 'Everything' );
		}

		return array(
			'testKey'        => $str( $rule, 'test_key' ),
			'testName'       => $str( $rule, 'test_name' ),
			'tipContent'     => $str( $rule, 'tip_content' ),
			'type'           => $str( $rule, 'type', 'error' ),
			'dismissKey'     => $str( $rule, 'dismiss_key', 'text' ),
			'elementSet'     => $element_set,
			'filterSelector' => $str( $rule, 'filter_selector' ),
			'includeText'    => $arr( $rule, 'include_text' ),
			'excludeText'    => $arr( $rule, 'exclude_text' ),
			'caseSensitive'  => ! empty( $rule['case_sensitive'] ),
		);
	}

	/**
	 * Override the default REST `Cache-Control: no-cache` header for this
	 * route, on success only.
	 *
	 * `WP_REST_Server::serve_request()` calls `nocache_headers()` before
	 * dispatch. That call writes short-lived cache headers via PHP's
	 * `header()`. Setting `Cache-Control` on the response object causes
	 * `send_headers()` to emit our value via `header()` later in the request
	 * lifecycle, and PHP's `header()` replaces same-named headers by default
	 * — so our 30-day directive wins.
	 *
	 * Three guards keep the filter narrow:
	 *   - Only `WP_REST_Response` instances (not raw arrays / WP_Errors that
	 *     the filter occasionally sees during pre-dispatch error paths).
	 *   - Only the exact `/ed11y/v1/config` route. Other REST routes keep
	 *     WordPress's default no-cache behavior.
	 *   - Only HTTP 200. A 401 / 403 from `permissions_check()` must keep
	 *     the short cache so revoked permissions take effect promptly; if
	 *     we cached 403s for 30 days, granting a user new access wouldn't
	 *     be visible until their browser cache expired.
	 *
	 * The 30-day max-age (2_628_000 seconds) and the use of `private` /
	 * `immutable` mirror Drupal's `ConfigCacheControlSubscriber`:
	 *   - `private`   — per-user payload (CSA includes a per-user `profile`
	 *                   in a later commit); shared / CDN caches must not
	 *                   serve one user's response to another.
	 *   - `immutable` — browser skips revalidation entirely; cachebust is
	 *                   driven by changing the `?v=<n>` query param, not by
	 *                   the cache header itself.
	 *
	 * @param mixed           $response Result from dispatch (usually
	 *                                  WP_REST_Response, occasionally
	 *                                  WP_Error during error paths).
	 * @param WP_REST_Server  $server   The REST server instance.
	 * @param WP_REST_Request $request  The dispatched request.
	 *
	 * @return mixed The response, possibly with an updated Cache-Control header.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function set_cache_headers( $response, $server, $request ) {
		if ( ! ( $response instanceof WP_REST_Response ) ) {
			return $response;
		}
		if ( self::ROUTE !== $request->get_route() ) {
			return $response;
		}
		if ( 200 !== $response->get_status() ) {
			return $response;
		}
		// Signal the companion `rest_send_nocache_headers` filter to skip
		// the no-cache loop for this request only. Without this signal
		// WP_REST_Server's nocache pass overwrites the header we just set.
		self::$suppress_nocache = true;
		$response->header( 'Cache-Control', 'private, max-age=2628000, immutable' );
		return $response;
	}

	/**
	 * Suppress the WP-default no-cache loop.
	 *
	 * `WP_REST_Server::serve_request()` calls
	 * `send_headers( $response->get_headers() )` (which emits our
	 * `Cache-Control: private, max-age=2628000, immutable`) and THEN —
	 * for logged-in users — applies the `rest_send_nocache_headers`
	 * filter, which triggers a loop over `wp_get_nocache_headers()`.
	 * Its `send_header()` uses PHP's `header()` with default replace=true,
	 * so its no-cache `Cache-Control` overwrites ours.
	 *
	 * Returning `false` here skips that whole loop. Using a per-request
	 * static flag (rather than `false` unconditionally) means we don't
	 * change WP's no-cache behavior on any other REST route — important
	 * because plugins / themes may rely on the default.
	 *
	 * @param bool $send_no_cache Default value WP would have used.
	 * @return bool
	 */
	public static function suppress_nocache_headers( $send_no_cache ) {
		if ( self::$suppress_nocache ) {
			self::$suppress_nocache = false;
			return false;
		}
		return $send_no_cache;
	}
}
