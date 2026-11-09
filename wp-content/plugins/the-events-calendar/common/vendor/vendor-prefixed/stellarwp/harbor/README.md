# Harbor

Bundled library that handles licensing, updates, and feature gating for WordPress plugins and themes.

[![PHP Compatibility](https://github.com/stellarwp/harbor/actions/workflows/compatibility.yml/badge.svg)](https://github.com/stellarwp/harbor/actions/workflows/compatibility.yml)
[![PHP Tests](https://github.com/stellarwp/harbor/actions/workflows/tests-php.yml/badge.svg)](https://github.com/stellarwp/harbor/actions/workflows/tests-php.yml)
[![PHPStan](https://github.com/stellarwp/harbor/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/stellarwp/harbor/actions/workflows/static-analysis.yml)
[![E2E Tests](https://github.com/stellarwp/harbor/actions/workflows/tests-e2e.yml/badge.svg)](https://github.com/stellarwp/harbor/actions/workflows/tests-e2e.yml)

## Installation

It's recommended that you install Harbor as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/harbor
```

> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

### Strauss must not prefix the global functions

Harbor's global functions (`src/Harbor/global-functions.php`) are deliberately non-namespaced. They are how the Harbor copies on a site find each other and route every call to the highest-version copy. Recent Strauss versions prefix global function names as well as namespaces, which breaks that negotiation — each plugin gets a privately-named copy of the helpers and the `function_exists()` guards never see one another.

Exclude the file in your Strauss config:

```json
"exclude_from_prefix": {
    "file_patterns": [
        "/harbor/src/Harbor/global-functions\\.php$"
    ]
}
```

## Agent skill

Harbor ships `skill/SKILL.md`: the rules a consuming plugin has to follow, written for the AI coding agent that will edit your plugin. It covers the free-vs-premium WordPress.org boundary, consuming the `lw_harbor_*` API instead of hand-rolling Portal URLs, Harbor's release train, and how to find the Strauss-prefixed copy that actually runs.

Agents only read it if your plugin tells them to. Paste this into your plugin's `AGENTS.md` (or `CLAUDE.md`):

```markdown
## Harbor (licensing)

Licensing, license keys, premium or add-on checks, platform fees, and anything calling `lw_harbor_*` go through Harbor, the library vendored at `vendor/vendor-prefixed/stellarwp/harbor/`.

Hard rules, no exceptions:

- A plugin distributed on WordPress.org must not validate a license key, send a request to the Commerce Portal, the Licensing API, or Herald, or install or activate anything from an entered key. That work belongs in the premium plugin.
- Onboarding for a free plugin never requires a license key, not even as a step that can be skipped.
- Never build a Portal URL, activation URL, or licensing request by hand. Use the `lw_harbor_*` helpers. A missing helper is a Harbor ticket.
- Never release or QA with `composer.json` pinned to a Harbor `dev-` branch.
- Strauss must not prefix `src/Harbor/global-functions.php`.
- Call the license page "Nexcess Licensing" in anything a user sees.

Before changing any of that code, read `vendor/vendor-prefixed/stellarwp/harbor/skill/SKILL.md` for the API, hooks, and bootstrap order of the installed version. If the file is missing, run `composer install` first.
```

Replace `vendor/vendor-prefixed` with your Strauss `target_directory` if it differs. Nothing else needs changing.

The hard rules sit in your own file so every agent and review bot sees them on a fresh clone, before `composer install`. The API detail stays in the vendored file so it always matches the Harbor version you have installed.

**If you are an agent reading this from `vendor/`** and the plugin's `AGENTS.md` has no Harbor section, read `skill/SKILL.md` next to this file before writing licensing code, and tell the developer to add the section.

### Harbor and WordPress.org

Harbor is bundled in free WordPress.org plugins as well as paid ones, and stays inert in the free ones by design. Several of the things it does — validating a license key, calling our servers, installing a plugin — could be read as running against the [WordPress.org plugin guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/) if a plugin distributed there did them.

A plugin on WordPress.org must not present a license field that validates a key, send a request to the Commerce Portal / the Licensing API / Herald at runtime (directly or through Harbor), or install or activate anything from an entered key. All new licensing and activation surface belongs in the premium plugin, behind the premium-plugin gate. The skill and the [Integration Guide](/docs/guides/integration.md#before-you-build-the-free-vs-premium-boundary) carry the full rule.

## Initialize the library

Initializing the Harbor library should be done within the `plugins_loaded` action, preferably at priority `0`.

Harbor only boots its providers when at least one premium plugin announces itself via the `lw_harbor/premium_plugin_exists` filter. **The filter must be attached before `Harbor::init()` is called**, otherwise the gate inside `Harbor::init()` short-circuits and the providers, REST routes, admin page, and `lw_harbor/loaded` action are never registered. The simplest pattern is to add the filter on the line immediately above the `Harbor::init()` call (as shown below), but anywhere earlier in the request works just as well.

```php
use LiquidWeb\Harbor\Config;
use LiquidWeb\Harbor\Harbor;

add_action( 'plugins_loaded', function() {
 /**
  * Configure the container.
  *
  * The container must be compatible with stellarwp/container-contract.
  * See here: https://github.com/stellarwp/container-contract#usage.
  *
  * If you do not have a container, we recommend https://github.com/lucatume/di52
  * and the corresponding wrapper:
  * https://github.com/stellarwp/container-contract/blob/main/examples/di52/Container.php
  */
 $container = new Container();
 // Use a plugin basename constant defined in your main plugin file,
 // e.g. define( 'MY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) )
 Config::set_plugin_basename( MY_PLUGIN_BASENAME );
 Config::set_container( $container );

 // Announce that this premium plugin should bring Harbor online.
 // Must be added before Harbor::init(). Anywhere earlier in the request works,
 // but the line above the call is the simplest pattern.
 add_filter( 'lw_harbor/premium_plugin_exists', '__return_true' );

 Harbor::init();
}, 0 );
```

## Translation

Package is using `__( 'Invalid request: nonce field is expired. Please try again.', '%TEXTDOMAIN%' )` function for translation. In order to change domain placeholder `'%TEXTDOMAIN%'` to your plugin translation domain run

```bash
./bin/stellar-harbor domain=<your-plugin-domain>
```

or

```bash
./bin/stellar-harbor
```

and prompt the plugin domain
You can also add lines below to your composer file in order to run command automatically

```json
"scripts": {
 "stellar-harbor": [
   "vendor/bin/stellar-harbor domain=<your-plugin-domain>"
 ],
 "post-install-cmd": [
   "@stellar-harbor"
 ],
 "post-update-cmd": [
   "@stellar-harbor"
 ]
  }
```

## Registering a plugin

Harbor discovers your plugin's embedded key automatically by scanning active plugins for a file named `LWSW_KEY.php` in the plugin root. No filter registration is required. See the [Harbor Integration Guide](/docs/guides/integration.md) for more details.

## Changelog

This project uses [@stellarwp/changelogger](https://github.com/stellarwp/changelogger) to manage its changelog. All notable changes are tracked via changelog entry files in the `changelog/` directory.

To add a new changelog entry:

```bash
bunx @stellarwp/changelogger add
```

To compile changelog entries into `changelog.txt`:

```bash
bunx @stellarwp/changelogger write --overwrite-version <version>
```

## Releasing

1. Run the **Release Prep** workflow (`Actions → Release Prep → Run workflow`). Supply the target branch, version (e.g. `1.2.0`), and the release date (e.g. `2026-04-29`). The workflow bumps the `VERSION` constant, compiles the changelog, and opens a PR automatically.
2. Review and merge the PR.
3. Create a GitHub Release with a new tag in the format `vX.X.X` targeting the merge commit.
4. Optionally, once the release is checked, run the **Update Consumers** workflow (`Actions → Update Consumers → Run workflow`) instead of bumping each plugin by hand. It opens a PR in every plugin in [Plugins with Harbor](#plugins-with-harbor) that bumps `stellarwp/harbor` to the new version (The Events Calendar and Event Tickets get theirs through tribe-common). To run it locally, or to target specific repos, run `composer release:update-consumers -- [version] [owner/repo ...]`. Add `--dry-run` to see the change without pushing anything. The consumer list lives in `dev_scripts/update-consumers.sh`.

## Documentation

Start with [Harbor Overview](/docs/harbor.md) for the full architecture.

### Subsystems

- [Licensing](/docs/subsystems/licensing.md) — Key discovery, API responses, validation workflows, caching.
- [Catalog](/docs/subsystems/catalog.md) — Product families, tiers, features, the Commerce Portal API.
- [Features](/docs/subsystems/features.md) — Feature types, resolution, strategies, Manager API.
- [Cron](/docs/subsystems/cron.md) — Scheduled refresh of catalog and licensing data.
- [Frontend](/docs/subsystems/frontend.md) — React app, @wordpress/data store, component hierarchy, CSS scoping.
- [Notices](/docs/subsystems/notices.md) — Admin notices, legacy license warnings, persistent dismissal.

### Architecture

- [Unified License Key](/docs/architecture/unified-license-key-system-design.md) — Key model, seat mechanics, system boundaries.
- [Fat Leader / Thin Instance](/docs/architecture/fat-leader-thin-instance.md) — Leader election, cross-instance hooks.
- [Conventions](/docs/architecture/conventions.md) — Naming conventions for namespaces, packages, identifiers.

### API Reference

- [REST: License](/docs/api/rest/license.md) — License endpoints.
- [REST: Catalog](/docs/api/rest/catalog.md) — Catalog endpoints.
- [REST: Features](/docs/api/rest/features.md) — Feature endpoints.
- [REST: Legacy Licenses](/docs/api/rest/legacy-licenses.md) — Legacy license endpoints.
- [Liquid Web Licensing v1](/docs/api/liquid-web-software-licensing-v1.md) — External licensing API consumed by Harbor.

### Guides

- [Integration Guide](/docs/guides/integration.md) — How to integrate your plugin with Harbor.
- [CLI Commands](/docs/guides/cli.md) — WP-CLI commands for feature management.
- [Testing](/docs/guides/testing.md) — PHP tests with Codeception/`slic`; E2E tests with Playwright/wp-env.

### Plugins with Harbor

| Plugin name             | Repository                                                                                            | Distribution                                                             | Note                                                                                        |
|-------------------------|-------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------|---------------------------------------------------------------------------------------------|
| GiveWP                  | [impress-org/givewp](https://github.com/impress-org/givewp)                                           | [wp.org](https://wordpress.org/plugins/give/)                            |                                                                                             |
| LearnDash               | [stellarwp/learndash-core](https://github.com/stellarwp/learndash-core)                               | [Herald](https://herald.nexcess.com/admin/products/sfwd-lms)             |                                                                                             |
| MemberDash              | [stellarwp/memberdash](https://github.com/stellarwp/memberdash)                                       | [Herald](https://herald.nexcess.com/admin/products/memberdash)           |                                                                                             |
| The Events Calendar     | [the-events-calendar/the-events-calendar](https://github.com/the-events-calendar/the-events-calendar) | [wp.org](https://wordpress.org/plugins/the-events-calendar/)             | [tribe-common](https://github.com/the-events-calendar/tribe-common) should be updated first |
| Event Tickets           | [the-events-calendar/event-tickets](https://github.com/the-events-calendar/event-tickets)             | [wp.org](https://wordpress.org/plugins/event-tickets/)                   | [tribe-common](https://github.com/the-events-calendar/tribe-common) should be updated first |
| Kadence Memberships Pro | [stellarwp/restrict-content-pro](https://github.com/stellarwp/restrict-content-pro)                   | [Herald](https://herald.nexcess.com/admin/products/restrict-content-pro) |                                                                                             |
| Kadence Blocks          | [stellarwp/kadence-blocks](https://github.com/stellarwp/kadence-blocks)                               | [wp.org](https://wordpress.org/plugins/kadence-blocks/)                  |                                                                                             |
| Kadence Shop Kit        | [stellarwp/kadence-shop-kit](https://github.com/stellarwp/kadence-shop-kit)                           | [Herald](https://herald.nexcess.com/admin/products/kadence-shop-kit)     |                                                                                             |
| Kadence Theme Kit Pro   | [stellarwp/kadence-pro](https://github.com/stellarwp/kadence-pro)                                     | [Herald](https://herald.nexcess.com/admin/products/kadence-theme-pro)    |                                                                                             |
