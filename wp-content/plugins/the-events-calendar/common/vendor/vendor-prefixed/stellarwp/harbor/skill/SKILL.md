---
name: harbor-integration
description: Integrating LiquidWeb Harbor (unified licensing, updates, feature gating) into a WordPress plugin. Use when touching Harbor::init, Config::set_*, LWSW license keys, lw_harbor_* helper functions, lw-harbor/* or lw_harbor/* hooks, legacy license reporting, license or activation UI, onboarding that mentions a key, the Nexcess Licensing page, or any vendored copy of Harbor (vendor/stellarwp/harbor or a Strauss-prefixed copy under vendor-prefixed/). Also use before changing anything that depends on whether a customer is licensed, paying, or on a paid plan (premium or add-on checks, platform or application fee waivers, feature gating, entitlements), and as soon as you meet a lw_harbor_* call or a LiquidWeb\Harbor class in code you are about to change.
---

# Harbor integration

Harbor is a PHP library bundled by Nexcess plugins for unified licensing, updates,
and feature management. It is vendored per plugin and namespace-prefixed with Strauss,
so several copies coexist on one site and negotiate leadership internally (highest
version wins). Never assume a shared installation.

## Hard boundary: free plugins do not touch licensing

Read this before writing any license, activation, or onboarding code. **Determine first
whether the plugin you are editing is distributed on WordPress.org.** Harbor is bundled
in free .org plugins (Kadence Blocks, Give, TEC free) as well as paid ones, and it is
inert in the free ones on purpose. Several of the things Harbor does — validating a key,
calling our servers, installing a plugin — could be read as running against the
[WordPress.org plugin guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/) if a plugin distributed there did them, and a free
plugin's place in the directory is what is at stake. The points at issue are item 5
(trialware), item 6 (a service existing solely to validate licenses), item 7 (contacting
external servers without consent), and item 8 (executable code from outside the .org
repository).

A plugin on WordPress.org **must not**:

- present a license field that validates a key,
- send a request to the Commerce Portal, the Licensing API, or Herald at runtime, directly
  or through Harbor,
- install or activate anything from an entered key.

The most a free plugin may do: a **static link** to the Portal, and optionally a **local
`LWSW-` format check** — a string comparison, no network call — to steer a user who
pasted a unified key into a legacy field.

Bundling Harbor is not the violation. A free entry plugin still vendors Harbor and calls
`Harbor::init()`; with no premium plugin on the site the gate stays closed and nothing
networked is registered. Do not remove that bootstrap. A free entry plugin may attach the
gate filter on behalf of its premium add-ons, but only with a callback that returns
`true` when one of them is actually active (GiveWP does this). Reading stored state
through the `lw_harbor_*` helpers is fine too: they read local options and make no
request (`lw_harbor_refresh_catalog()` is the exception). What a free plugin must not do
is open the gate unconditionally or drive Harbor's validation, activation, and install
paths.

All new licensing and activation surface belongs in the **premium** plugin, where the
premium-plugin gate has opened and Harbor is active: license fields, validation calls,
activation buttons, install flows. (Pre-existing Uplink license fields in free plugins
are grandfathered; this rule governs new surface.)

**Free-product onboarding is never gated behind a license.** It must complete without a
key, with no exceptions — including a step that can be skipped.

If a ticket asks for validation, an install-from-key flow, or a request to the licensing
services inside a free plugin, it cannot be built as written. Say so and move the work to the premium plugin.

## Never hand-roll what Harbor exposes

Portal URLs, activation URLs, script handles, and license state come from the
`lw_harbor_*` global functions. Do not build a Portal query string or activation URL in a
plugin: each hand-rolled copy duplicates the Portal contract and drifts the moment a
parameter changes, and it bypasses the version negotiation that routes calls to the
highest Harbor copy on the site. If the helper you need does not exist, that is a Harbor
ticket, not a reason to inline it.

## Release discipline

Harbor changes ship on Harbor's release train: PR merged, tagged, released, then
`composer update` in each plugin, then plugin QA. **Never release or QA a plugin whose
`composer.json` pins a Harbor dev branch.** Prototyping against `dev-main` is fine;
shipping is not. Leadership is elected by version, so a dev branch (no bumped version)
loses to an equal released version that lacks your change, and the feature silently does
not run — sometimes without an error, sometimes as a fatal on a missing method.

## Before writing any code

1. **Find the copy that actually runs, and resolve its prefix.** Read the consuming
   plugin's `composer.json` under `extra.strauss`: `namespace_prefix` is the prefix,
   `target_directory` (commonly `vendor-prefixed/` or `vendor/vendor-prefixed/`) is where
   the prefixed, executing copy of Harbor lives. That is the code the site loads.

   `vendor/stellarwp/harbor` is the unprefixed source Strauss copied from. It may be
   gutted (`delete_vendor_files`) or gone entirely (`delete_vendor_packages`) after a
   build, and consuming plugins usually gitignore `vendor/` while committing the prefixed
   tree — so on a fresh clone it may not exist at all. Read whichever tree is present;
   prefer the prefixed one when both are.

   Every class reference below is written unprefixed. Prefix it yourself, and never copy
   a prefix out of documentation examples.

2. **Check the Strauss global-function exclusion.** `src/Harbor/global-functions.php`
   must not be prefixed. Recent Strauss versions prefix global function names, which
   breaks cross-copy negotiation — each plugin gets private helpers and the
   `function_exists()` guards never see each other. The consuming `composer.json` needs:

   ```json
   "exclude_from_prefix": {
       "file_patterns": [
           "/harbor/src/Harbor/global-functions\\.php$"
       ]
   }
   ```

3. **Read the installed source, not memory.** The vendored copy located in step 1 is
   the contract for the version actually installed.
   Global helpers are defined in `src/Harbor/global-functions.php` and dispatched to the
   highest-version copy by `src/Harbor/API/Functions/Global_Function_Registry.php`.
   Bootstrap lives in `src/Harbor/Harbor.php`, `src/Harbor/Config.php`, and
   `src/Harbor/Premium_Plugin_Registry.php`.
4. **PHP 7.4 is the floor.** No union types, `match`, constructor promotion, named
   arguments, enums, or readonly.

## Bootstrap

Order matters and is the most common source of "Harbor does nothing" bugs. `__return_true`
in step 1 is for a premium plugin. A free entry plugin either skips step 1 or attaches a
callback that returns `true` only when one of its premium add-ons is active.

```php
use Prefix\LiquidWeb\Harbor\Config;
use Prefix\LiquidWeb\Harbor\Harbor;

// 1. Open the gate. MUST run before Harbor::init(). Premium plugins only.
add_filter( 'lw_harbor/premium_plugin_exists', '__return_true' );

// 2. Identify the host plugin. Pass a basename constant defined in the main
//    plugin file via plugin_basename( __FILE__ ) — calling plugin_basename()
//    from inside a service class resolves the wrong file.
Config::set_plugin_basename( MY_PLUGIN_BASENAME );

// 3. Hand over the DI container.
Config::set_container( $container );

// 4. Boot.
Harbor::init();
```

`Harbor::init()` registers providers, REST routes, and the admin page **only if** a
callback on `lw_harbor/premium_plugin_exists` returned `true`. If that filter is attached
from a class that Harbor itself loads, it is too late — the gate already evaluated.
The global functions register before the gate, so the `lw_harbor_*` helpers exist on
free-only sites too; see "Global helpers" for what they return there.
Register the Harbor provider after all other providers so the container is complete.

Anything that depends on a booted Harbor hooks `lw_harbor/loaded`, not `plugins_loaded`.

## Two hook prefixes exist

Both are real. Copy them exactly; guessing the separator silently no-ops.

- `lw_harbor/` (underscore) — `premium_plugin_exists`, `loaded`
- `lw-harbor/` (hyphen) — `legacy_licenses`, `hide_menu_item`, `get_domain`, and
  internal events such as `lw-harbor/unified_license_key_changed`,
  `lw-harbor/feature_enabled`

## Bundling a license key

Harbor scans active plugins for `LWSW_KEY.php` in the plugin root. No registration call.

```php
<?php return 'LWSW-xxxx-xxxx-xxxx-xxxx';
```

Gitignore it; inject at build/deploy time. Auto-stored only when the site has no key yet —
an already-stored key always wins. Uplink v2 plugins do not ship this file.

## Reporting legacy licenses

```php
add_filter( 'lw-harbor/legacy_licenses', function ( array $licenses ): array {
    $licenses[] = [
        'key'             => $key,        // required
        'slug'            => $slug,       // required — product/add-on slug
        'name'            => $name,       // required
        'product'         => 'givewp',    // required — brand slug
        'is_active'       => true,        // required — bool
        'page_url'        => admin_url( '...' ), // required
        'use_for_updates' => false,       // optional, default false
        'expires_at'      => '2026-01-01',// optional
    ];

    return $licenses;
} );
```

- Entries missing `key` or `slug` are dropped at intake — no UI, no notices, no downloads.
- `use_for_updates => true` marks the matching catalog feature available and routes
  downloads through Herald's `/legacy/download`. Set it **only** when the key is
  Stellar Licensing v3 compatible. Keys from another backend (SolidWP, some Give legacy
  keys) must leave it `false`, otherwise users see an update badge for a package Herald
  cannot serve.
- `is_active` is taken at face value; Harbor cannot verify it. Herald re-validates
  server-side before serving a ZIP, so an over-reported `true` cannot leak a package.
- Harbor renders consolidated admin notices for inactive legacy licenses. Remove your
  plugin's own license notices to avoid duplicates — the leader instance renders them once.
- On an existing legacy license screen, call
  `lw_harbor_display_legacy_license_page_notice( 'GiveWP' );` directly in the render
  callback. It echoes immediately; no hook needed.

## Global helpers

All delegate to the highest-version Harbor instance on the site, so they are safe under
multiple bundled copies. All are premium-context only in practice. On a free-only site
the gate never opened: feature checks and `lw_harbor_refresh_catalog()` return `false`,
and `lw_harbor_get_license_page_url()` still returns a URL, but the page behind it is not
registered. Check `did_action( 'lw_harbor/loaded' )` before linking to it.

| Function                                       | Signature                           | Purpose                                                                                   |
| ---------------------------------------------- | ----------------------------------- | ----------------------------------------------------------------------------------------- |
| `lw_harbor_is_product_license_active`          | `(string $slug): bool`              | Primary gate for premium behavior / fee waivers.                                          |
| `lw_harbor_has_unified_license_key`            | `(): bool`                          | Local check, no remote call.                                                              |
| `lw_harbor_get_unified_license_key`            | `(): ?string`                       | Stored unified key.                                                                       |
| `lw_harbor_get_licensed_domain`                | `(): string`                        | Host portion of `home_url()`, lowercased.                                                 |
| `lw_harbor_is_feature_enabled`                 | `(string $slug): bool`              | Feature active locally on this site.                                                      |
| `lw_harbor_is_feature_available`               | `(string $slug): bool`              | Feature included in the customer's tier.                                                  |
| `lw_harbor_get_license_page_url`               | `(): string`                        | Nexcess Licensing URL; empty when no Harbor copy is loaded.                               |
| `lw_harbor_register_submenu`                   | `(string $parent_slug): void`       | Appends a Licensing item. No-op before `lw_harbor/loaded`.                                |
| `lw_harbor_display_legacy_license_page_notice` | `(string $product_name = ''): void` | Echoes the migration notice.                                                              |
| `lw_harbor_refresh_catalog`                    | `(): bool`                          | **Synchronous** catalog re-fetch. User-initiated actions only, never a passive page load. |

Verify this list against `src/Harbor/global-functions.php` in the installed copy — the set
grows between releases, and a helper documented elsewhere may not exist in your version.

`is_feature_enabled` (on locally) and `is_feature_available` (entitled by tier) are
different questions. Gating premium code usually wants both, or `available` plus the
user's toggle.

## Naming: "Nexcess Licensing"

Call Harbor's license page **Nexcess Licensing** in UI copy, onboarding text,
and link labels. This is also the title of Harbor's page and its Settings menu entry.

`Feature_Manager_Page` is the internal class name. Do not surface it to users.

## Admin menu

```php
add_action( 'lw_harbor/loaded', function () {
    lw_harbor_register_submenu( 'my-plugin-menu-slug' );
} );

// Optional: drop the standalone Settings → Nexcess Licensing entry.
add_filter( 'lw-harbor/hide_menu_item', '__return_true' );
```

`hide_menu_item` hides both the Settings entry and registered submenu items; the page
itself stays registered so direct URLs still work.

## Constants (wp-config.php, before Harbor loads)

| Constant                       | Purpose                                               |
| ------------------------------ | ----------------------------------------------------- |
| `LW_HARBOR_DISABLE_DEBUG_LOG`  | Silence all Harbor logging while `WP_DEBUG` stays on. |
| `LW_HARBOR_LICENSING_BASE_URL` | Point the licensing API elsewhere (local dev).        |
| `LW_HARBOR_PORTAL_BASE_URL`    | Point the Commerce Portal catalog API elsewhere.      |
| `LW_HARBOR_HERALD_BASE_URL`    | Point the Herald API elsewhere.                       |

Logging is otherwise gated on `WP_DEBUG` **and** `WP_DEBUG_LOG`; messages are prefixed
`Harbor:` in `debug.log`.

## DI container gotcha

Harbor uses DI52. When registering into a container Harbor will read, closures must close
over the container rather than type-hint it:

```php
// Correct
$container->singleton( Service::class, function () use ( $container ) {
    return new Service( $container->get( Dep::class ) );
} );

// Wrong — TypeError under the wrapper-pattern container
$container->singleton( Service::class, static function ( ContainerInterface $c ) { ... } );
```

DI52's `ClosureBuilder::build()` passes the raw `lucatume\DI52\Container`, which implements
PSR-11's `ContainerInterface` but not `StellarWP\ContainerContract\ContainerInterface`.

## Model of the system

- One `LWSW-` key per site, shared by every Nexcess product.
- A product is a brand family (Kadence, Give, The Events Calendar, LearnDash), not a
  plugin. Each has one or more entry plugins that bootstrap Harbor; most are free.
- Features are the resolved join of catalog data (Commerce Portal) and licensing data.
  They are not a third source of truth — do not write feature state directly.
- Entering a key validates it and fetches the catalog. It does not consume a seat,
  activate a product, or unlock anything by itself.
- Data shapes (tier slugs, catalog structure, API response formats) are still in flux.
  Architecture is stable; payloads are not. Verify shapes against the installed source
  before depending on them.

## Review checklist for a Harbor-touching PR

- Does this add licensing surface or a network call to a free .org plugin?
- Does it build a Portal or activation URL by hand instead of calling a helper?
- Does it gate free onboarding on a key?
- Does `composer.json` pin a Harbor `dev-` constraint on a branch headed for release or QA?
- Does user-facing copy say "Nexcess Licensing"?
- Is `global-functions.php` excluded from Strauss prefixing?

Deeper subsystem docs (portal, features, licensing, cron, notices, REST API) live in the
Harbor repository under `docs/`; they are excluded from the Composer dist to keep plugin
packages small. `docs/guides/integration.md` there is the long-form version of this file.
