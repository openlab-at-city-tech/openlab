# Nexcess Banners

[![CI](https://github.com/stellarwp/banners/workflows/CI/badge.svg)](https://github.com/stellarwp/banners/actions?query=branch%3Amain)

Marketing banners for WordPress admin pages, printed where you call them and dismissed per user across
every plugin that bundles the package.

## Table of contents

* [Installation](#installation)
* [Notes on examples](#notes-on-examples)
* [Registering banners](#registering-banners)
* [Rendering a banner](#rendering-a-banner)
* [Fields](#fields)
* [Versions](#versions)
* [Replacing the template or stylesheet](#replacing-the-template-or-stylesheet)
* [When a banner renders](#when-a-banner-renders)
* [Dismissal](#dismissal)
* [Shared state](#shared-state)
* [Development](#development)

## Installation

Install the package from Packagist:

```bash
composer require stellarwp/banners
```

Then prefix it with [Strauss](https://github.com/BrianHenryIE/strauss). This is required, not a
suggestion: WordPress plugins share one PHP namespace, so two plugins shipping unprefixed copies of
this package would collide on whichever autoloader registered first.

See the [Strauss setup docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

The package is built for this. Everything it shares between plugins is reached through a string key,
which Strauss leaves alone while it rewrites namespaces and class names, so prefixed copies still
agree on one registry, one dismissal and one render per banner. See [Shared state](#shared-state).

## Notes on examples

Because the package is prefixed, all examples use `StraussGeneratedNamespace` to stand in for
whatever prefix you configure.

## Registering banners

Every plugin registers its banners on `init`, before anything renders:

```php
use StraussGeneratedNamespace\Nexcess\Banners\Banner;
use StraussGeneratedNamespace\Nexcess\Banners\BannerFields;
use StraussGeneratedNamespace\Nexcess\Banners\Registry;

add_action(
	'init',
	static function (): void {
		$fields           = new BannerFields();
		$fields->heading  = __( 'Black Friday: 40% off Pro.', 'my-plugin' );
		$fields->body     = __( 'Unlock every block and priority support.', 'my-plugin' );
		$fields->ctaLabel = __( 'Shop the sale', 'my-plugin' );
		$fields->ctaUrl   = 'https://example.com/black-friday?utm_source=my-plugin';

		( new Registry() )->register(
			new Banner(
				'nexcess-black-friday-2026',
				'1.0.0',
				$fields,
				plugins_url( 'vendor-prefixed/stellarwp/banners/', MY_PLUGIN_FILE ),
				new DateTimeImmutable( '2026-11-27 00:00', new DateTimeZone( 'America/New_York' ) ),
				new DateTimeImmutable( '2026-12-01 00:00', new DateTimeZone( 'America/New_York' ) )
			)
		);
	}
);
```

Register on `init`. Earlier, `__()` loads translations too soon. Later, another plugin may already
have rendered an older version of the banner.

The fourth argument is the URL your plugin serves its copy of the package from, with or without a
trailing slash. The banner loads its stylesheet from there, so build it with `plugins_url()` against
wherever Strauss put the package in your plugin.

## Rendering a banner

Fetch a banner by ID and render it wherever it should appear:

```php
add_action(
	'admin_notices',
	static function (): void {
		( new Registry() )->get( 'nexcess-black-friday-2026' )->render( static fn(): bool => my_plugin_is_admin_page() );
	}
);
```

`render()` prints immediately: the link to the banner's stylesheet, then the banner. Call it in your
admin page template, or on a hook such as `admin_notices`. The callable decides whether the banner
shows here. Without it, the banner renders unconditionally.

Render the IDs your plugin registers: once `register()` has run, `get()` cannot throw for them. For
an ID only other plugins register, check `has()` first, as shown below.

`get()` returns a copy of the newest registered version, so you can change its [fields](#fields) before
rendering, such as to add your own UTM parameters to the CTA. The change stays with your copy:

```php
$banner                   = ( new Registry() )->get( 'nexcess-black-friday-2026' );
$banner->fields()->ctaUrl = add_query_arg( 'utm_source', 'my-plugin', $banner->fields()->ctaUrl );
$banner->render();
```

**Do not type-hint the banner `get()` returns.** Another plugin may have registered the newest version,
so it can be that plugin's prefixed `Banner`: the same methods under a different class name. A
parameter declared as your `Banner` would throw a `TypeError` on it. Call its methods and set its
fields' properties, and it works whichever copy it came from.

`get()` throws an `OutOfBoundsException` when no banner has the ID, such as after a typo, before
`register()` ran, or when no active plugin registers that ID any more. Uncaught, that is a fatal
error on the admin page, so check `has()` first or catch it:

```php
$registry = new Registry();

if ( $registry->has( 'nexcess-black-friday-2026' ) ) {
	$registry->get( 'nexcess-black-friday-2026' )->render();
}
```

It is PHP's own class, which Strauss does not prefix, so catch `\OutOfBoundsException`.

## Fields

`BannerFields` holds what a banner says and shows, as public string properties:

| Property | Default | Notes |
| --- | --- | --- |
| `heading` | `''` | The message, in white. |
| `body` | `''` | Follows the heading in blue, on a line of its own where the banner is narrow. |
| `ctaLabel` | `''` | The button renders only when both `ctaLabel` and `ctaUrl` are set. |
| `ctaUrl` | `''` | Add your own UTM parameters. Opens in a new tab. |
| `dismissLabel` | `'Dismiss this banner.'` | Screen-reader label of the dismiss control. |

The schedule is not a field. `Banner`'s fifth and sixth arguments, `?DateTimeImmutable $start` and
`?DateTimeImmutable $end`, bound it. Either may be null, for no bound. The banner shows from `start`
and stops at `end`, compared as moments in time, so their time zones do not matter.

The markup lives in `src/templates/banner.php`, which escapes every value at output, and its styles in
`src/templates/banner.css`. The banner sizes itself to the space it is given, not the screen: wide at
the top of a page, stacked in a narrow spot such as a settings sidebar.

## Replacing the template or stylesheet

Pass `render()` your own template file, stylesheet URL, or both:

```php
$banner->render(
	static fn(): bool => my_plugin_is_admin_page(),
	__DIR__ . '/templates/pro-upsell-banner.php',
	plugins_url( 'assets/pro-upsell-banner.css', MY_PLUGIN_FILE )
);
```

Pass `null` to keep the bundled one. Either applies only to this call, so it never changes how
another plugin renders the same banner.

A template path that is not a file falls back to the bundled template. The template gets `$fields`,
`$dismiss_url` and `$inline`. Start from the bundled one: it escapes every value, adds the `inline`
class when `$inline` is true, and links the dismiss control to `$dismiss_url`. Read `$fields` only
through its properties, never by type, since it may be another copy's `BannerFields`.

A stylesheet you pass replaces the bundled one, so a template of your own still gets the bundled
styles unless you pass one too.

## Versions

Every banner has a version, `Banner`'s second argument. When several plugins register the same ID,
the registry keeps the newest by `version_compare()`, in whatever order they register. On a tie, the
first registered stays. Every plugin then renders that version, with the code, template and
stylesheet of the plugin that registered it. That is why the package URL is given to the banner and
not to `render()`.

So when product A registers version 1.0.0 of `nexcess-black-friday-2026` and product B registers 1.0.1,
both render 1.0.1. Bump the version whenever a banner's copy or schedule changes.

Dismissal is keyed by ID, not version: a user who dismissed 1.0.0 does not see 1.0.1.

## When a banner renders

`render()` prints the banner unless one of these holds, checked in this order:

1. The request is not for wp-admin: `is_admin()` is false.
2. Another copy of the package already rendered the same banner ID on this request.
3. The current time is before `start` or at or after `end`, unless the schedule filter says otherwise.
4. The current user dismissed it.
5. The callable was passed and returned false.

The callable runs last and only when it can still matter, so an expensive check costs nothing on a
banner that would not show anyway.

Filter `nx_banners_scheduled:{id}` to decide step 3 yourself, such as to see a banner in QA before it
starts, without moving the site's clock:

```php
add_filter( 'nx_banners_scheduled:nexcess-black-friday-2026', '__return_true' );
```

It receives whether the banner is inside its schedule. Return false to hide a banner inside it. The
filter applies only when the plugin that registered the banner bundles version 1.1.0 or later.

When two plugins render the same banner ID on one request, the first to call `render()` prints it and
the other prints nothing. Different IDs each render.

Rendering prints the stylesheet's link just before the banner, not in the page head: `render()` runs
after wp-admin printed the head's styles, and a style enqueued that late prints in the footer, which
would leave the banner unstyled until the page finishes loading. WordPress prints each handle once.

The banner is a core `.notice`. Rendered on a notice hook, `admin_notices`, `all_admin_notices`,
`network_admin_notices` or `user_admin_notices`, it is left to wp-admin's `common.js`, which moves it
under the page heading like every other notice. Rendered anywhere else, it carries `inline`, which
`common.js` leaves alone, so it stays where you called `render()`.

## Dismissal

Dismissals are per user, stored in user meta as the time of the dismissal, keyed by banner ID.
Dismissing a banner hides it for that user in every plugin that renders the same ID, and leaves every
other banner alone. Treat a published ID as permanent: renaming it shows the banner again to everyone
who dismissed it.

The dismiss control is a nonce-protected link back to the current page. The banner's own `render()`
stores the dismissal when that page loads again, since a client calling `render()` mid-page leaves no
earlier hook to handle it on. Nothing redirects, so the dismiss arguments stay in the address bar
until the next navigation. Repeating the request is harmless and keeps the first dismissal time. A
link with a bad nonce, or from a logged-out visitor, is ignored and the banner renders as usual.

No script ships. The notice does not carry `is-dismissible`, so core's `makeNoticesDismissible()`
adds no second, non-persisting button.

## Shared state

Each plugin prefixes its own copy, which rewrites namespaces and class names but not string literals.
Everything shared between plugins is therefore reached through a string key:

| Key | Purpose |
| --- | --- |
| `nx_banners` | `$GLOBALS` key of the registry, as `[ id => banner ]`. A banner there may be any copy's `Banner`. |
| `nx_banners_dismissed` | User meta: when the user dismissed each banner, as `[ id => Unix timestamp ]`. |
| `nx-banners-dismiss` | Dismiss query argument carrying the banner ID. The nonce action is `nx-banners-dismiss:{id}`. |
| `nx-banners-nonce` | Nonce query argument, named so it cannot replace a `_wpnonce` already in the page URL. |
| `nx_banners_rendered:{id}` | Action fired when a banner renders; later copies check `did_action()` and stand down. |
| `nx_banners_scheduled:{id}` | Filter: whether the banner is inside its schedule, as a bool. |
| `nx-banners-{id}` | Handle of the banner's stylesheet in WordPress's style registry. |

A registered banner is called only through its methods, never checked by class: `version()` while
registering, and `fields()` and `render( ?callable $shouldDisplay, ?string $template, ?string $stylesheet )`
by whoever fetches it, along with the `BannerFields` property names.

These keys and methods are a cross-version compatibility contract. Do not rename them or change their
shape: another plugin may still be running an older prefixed copy.

## Development

```bash
composer install
composer check
```

| Command | What it runs |
| --- | --- |
| `composer phpcs` | Nexcess coding standards over `src` and `tests` |
| `composer phpstan` | Level 8, `src` only |
| `composer test` | PHPUnit |
| `composer check` | All three, in that order |

The suite uses [Brain\Monkey](https://github.com/Brain-WP/BrainMonkey) to stub the WordPress
functions the package calls, so it runs with nothing but Composer installed.

Bump `Package::VERSION` with every release. The bundled stylesheet's URL carries it, so browsers
fetch the new file instead of a cached one.

Requires PHP 7.4+ and WordPress 6.6+.
