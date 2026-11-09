# POPE framework (vendored)

This directory is a vendored copy of the **`awesomemotive/pope-framework`** library,
previously pulled in as a Composer dependency (`imagely/pope-framework: v0.19`) from a
private VCS repository.

| | |
|---|---|
| Upstream repo | `git@github.com:awesomemotive/pope-framework.git` (private) |
| Vendored version | `v0.19` |
| Upstream commit | `78e0b0c59b68c44b86e688f447a5a80e78e44b4f` (2023-12-20) |
| Vendored on | 2026-07-29 |

## Why it lives here

POPE is a frozen, internal-only library — the last functional change to `lib/` landed in
2020, and everything since has been PHP 8.x deprecation patches. NextGEN Gallery was its
only consumer (NextGEN Pro/Plus/Starter use the classes but never declared the dependency).

Keeping it as a private-VCS Composer dependency meant:

- every `composer install`/`update` — including the one the build runs — needed SSH access
  to a private GitHub repo;
- the plugin build runs `composer --no-dev update` (not `install`), so the lockfile was
  bypassed at build time;
- shipping a one-line PHP compatibility fix required a commit, tag, and version bump in a
  separate repository.

Vendoring it removes the private dependency from the build, and puts the code where it can
be refactored in place as POPE is progressively removed (see nextgen-gallery#35).

## Relationship to POPE removal

This is **not** the removal of POPE — it is a no-op relocation that makes the removal
tractable. The architecture (component registry, adapters, mixins) is unchanged. Removal
continues under nextgen-gallery#35, gated on `NGG_PRO_API_VERSION`.

## Local modifications

The copy is byte-identical to upstream `v0.19` **except** for `lib/autoload.php`, where the
bare relative `require_once('class.*.php')` calls were made `__DIR__`-relative so loading no
longer depends on `include_path` or the calling file's directory. A provenance docblock was
added to the same file.

Two upstream quirks are preserved deliberately — do not "fix" them without checking callers:

- `POPE_VERSION` is defined as `'0.17'` even at tag `v0.19`.
- Every `lib/*.php` file begins with a `defined('POPE_VERSION') || die('Use autoload.php')`
  guard, so the files must be loaded via `lib/autoload.php` and never individually.

## Loading

Loaded eagerly from `nggallery.php`, immediately after the Composer autoloader — matching
the old behaviour, where Composer's `files` autoload declared these classes on **every**
request. That is load-bearing: code such as
`src/Util/ThirdPartyCompatibility.php` checks `in_array( 'C_Component_Registry', get_declared_classes() )`,
and third-party plugins (EWWW, WP Smush, Imagify, ShortPixel) reference `C_Gallery_Storage`.
Declaring the classes is separate from bootstrapping the registry, which still happens
lazily in `C_NextGEN_Bootstrap::load_pope()`.
