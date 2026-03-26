# PHP-DI Fork Rationale

## Overview

This fork of `php-di/php-di` 6.4.0 is **embedded directly into smashballoon/framework** to fix PHP 8.4 deprecation warnings while maintaining PHP 7.4+ support.

**Location:** `sb-common/Packages/php-di/`

## Problem Statement

PHP 8.4 deprecated implicit nullable parameter declarations:

```php
// Deprecated in PHP 8.4 - causes E_DEPRECATED warnings
function setClassName(string $className = null)

// Correct - explicit nullable type
function setClassName(?string $className = null)
```

**php-di/php-di 6.x** contains 26 such patterns, causing deprecation warnings on PHP 8.4+.

### Why Not Upgrade to PHP-DI 7.x?

| Version | PHP Requirement |
|---------|-----------------|
| php-di/php-di 6.x | PHP >= 7.2 |
| php-di/php-di 7.x | PHP >= 8.0 |

We need to support PHP 7.4+, so upgrading to PHP-DI 7.x is not an option.

---

## Solutions Evaluated

### 1. PHP-Scoper Patcher (Rejected)

**Approach:** Add a patcher in `scoper.inc.php` to fix nullable types at build time.

```php
$contents = preg_replace(
    '/([(,]\s*|^\s+)([A-Za-z\\\\]+)(\s+\$\w+\s*=\s*null)/m',
    '$1?$2$3',
    $contents
);
```

**Why rejected:**
- PHP-Scoper's purpose is namespace prefixing, not code transformation
- Fragile: `composer update` could introduce new patterns the regex doesn't catch
- Hidden magic that future developers won't understand
- Violates single responsibility principle

### 2. Container-Only Fork in sb-common (Failed)

**Approach:** Fork only the Container class into `smashballoon/framework`:

```
sb-common/Packages/Container/DI/Container.php
```

**Why it failed:**

PHP-Scoper created namespace conflicts:

```php
// Original php-di in vendor/ became:
SmashBalloon\Reviews\Vendor\DI\Container

// Fork in sb-common became:
SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Packages\Container\DI\Container
```

Two different Container classes with different namespaces caused:
- Type hint mismatches
- "Class not found" errors
- Autoloader conflicts

### 3. Full Package Fork (Implemented)

**Approach:** Fork the entire `php-di/php-di` package as `smashballoon/php-di`.

**Why it works:**

```json
// Before
{ "require": { "php-di/php-di": "^6.1.0" } }

// After
{ "require": { "smashballoon/php-di": "*" } }
```

- **Same namespace:** Fork uses `DI\` namespace (identical to original)
- **Drop-in replacement:** No code changes needed in consuming projects
- **No conflicts:** Only ONE php-di package exists after installation
- **PHP-Scoper compatible:** Prefixes normally to `SmashBalloon\Reviews\Vendor\DI\`
- **Stable:** `composer update` works without breaking anything

---

## Implementation Details

### Fork Location

The fork is **embedded directly into smashballoon/framework** (not as a separate package):

```
sb-common/
├── composer.json        # smashballoon/framework with DI\ autoload
└── Packages/
    └── php-di/
        ├── src/
        │   ├── Container.php
        │   ├── ContainerBuilder.php
        │   ├── functions.php
        │   └── ...
        ├── README.md
        └── FORK_RATIONALE.md
```

### Composer Configuration (smashballoon/framework)

The fork is integrated via PSR-4 autoloading in `sb-common/composer.json`:

```json
{
  "name": "smashballoon/framework",
  "autoload": {
    "psr-4": {
      "Smashballoon\\Framework\\": "",
      "DI\\": "Packages/php-di/src/"
    },
    "files": [
      "Utilities/functions.php",
      "Packages/php-di/src/functions.php"
    ]
  },
  "replace": {
    "php-di/php-di": "6.4.0"
  },
  "require": {
    "php": ">=7.4",
    "psr/container": "^1.0",
    "php-di/invoker": "^2.0",
    "php-di/phpdoc-reader": "^2.0.1",
    "laravel/serializable-closure": "^1.0"
  }
}
```

### Key Design Decisions

1. **Embedded, not separate package:** Avoids nested path repository resolution issues
2. **`DI\` namespace preserved:** Same namespace as original php-di for drop-in compatibility
3. **`replace` directive:** Satisfies transitive dependencies from other packages (e.g., customizer)
4. **Dependencies moved to framework:** php-di's dependencies are now direct framework dependencies

### Changes from Upstream

Based on `php-di/php-di` version 6.4.0:

1. **Embedded in framework:** No longer a separate package, now part of `smashballoon/framework`
2. **PHP requirement:** `>=7.4.0` (required by laravel/serializable-closure)
3. **26+ nullable fixes:** All implicit nullable parameters made explicit (`?Type` syntax)
4. **Parameter order fix:** `ObjectCreator::setPrivatePropertyValue()` - nullable parameter moved after required
5. **Removed:** tests, docs, website directories (not needed for runtime)

### Files Modified

| File | Fixes |
|------|-------|
| `src/functions.php` | 2 |
| `src/Container.php` | 3 |
| `src/ContainerBuilder.php` | 1 |
| `src/Proxy/ProxyFactory.php` | 1 |
| `src/Compiler/ObjectCreationCompiler.php` | 2 |
| `src/Definition/ObjectDefinition.php` | 3 |
| `src/Definition/Resolver/ParameterResolver.php` | 2 |
| (+ 12 more files) | 12 |
| **Total** | **26** |

---

## Maintenance

### Expected Maintenance: Minimal

PHP-DI 6.x is no longer actively developed. The maintainers focus on PHP-DI 7.x.

### If Upstream Releases a Fix

If `php-di/php-di` 6.x ever gets a PHP 8.4 compatibility release:
1. Compare changes with our fork
2. Either merge upstream changes or switch back to official package

### Future: Dropping PHP 7.x Support

When PHP 7.x support is no longer needed:
1. Remove the fork
2. Switch to `php-di/php-di: ^7.0`
3. Delete `packages/php-di/` directory

---

## Comparison Summary

| Criteria | Patcher | Container Fork | Full Fork |
|----------|---------|----------------|-----------|
| Separation of concerns | ❌ | ✅ | ✅ |
| No namespace conflicts | ✅ | ❌ | ✅ |
| `composer update` safe | ❌ | ✅ | ✅ |
| Drop-in replacement | N/A | ❌ | ✅ |
| Maintenance burden | Medium | High | Low |
| **Recommended** | No | No | **Yes** |

---

## References

- [PHP 8.4 Implicit Nullable Deprecation](https://php.watch/versions/8.4/implicitly-marking-parameter-type-nullable-deprecated)
- [PHP-DI GitHub](https://github.com/PHP-DI/PHP-DI)
- **sb-common PR #17:** Fork moved to smashballoon/framework
- **sb-customizer PR #67:** Customizer updated to use framework's fork
- **sb-reviews PR #401:** Plugin updated to use framework's fork
- PR #399: Patcher approach (superseded)
- sb-common PR #15: Revert of Container-only fork (previous failed approach)
