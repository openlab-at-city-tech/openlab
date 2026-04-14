# WordPress Plugin Framework

<p>
Supports <code>PHP >= 7.4</code>.
<br>For contribution environment setup, it uses <code>Composer 2.X</code>
</p>

## Installation

Add below code snippet in `composer.json`, then run `composer install`.

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:awesomemotive/sb-common.git"
    }
  ],
  "require": {
    "smashballoon/framework": "dev-master"
  }
}
```

## Included Packages

### PHP-DI Fork (PHP 8.4 Compatible)

This framework includes an embedded fork of `php-di/php-di` 6.4.0 with PHP 8.4 deprecation fixes.

**Why:** PHP-DI 6.x has implicit nullable parameters (`Type $param = null`) which are deprecated in PHP 8.4. PHP-DI 7.x requires PHP 8.0+, but we need PHP 7.4+ support.

**How it works:**
- Fork is embedded at `Packages/php-di/`
- Autoloaded via PSR-4 as `DI\` namespace (same as original)
- `replace` directive in composer.json satisfies transitive dependencies

**See:** [`Packages/php-di/FORK_RATIONALE.md`](Packages/php-di/FORK_RATIONALE.md) for full documentation.
