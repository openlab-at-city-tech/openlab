# ThemeIsle SDK — Agent Reference

> Quick-reference guide for AI agents working on this codebase.

## What This Is

A shared WordPress library bundled into Themeisle plugins and themes. It provides common features (licensing, analytics, notifications, promotions, etc.) so each product doesn't reimplement them. Multiple products may bundle different versions; only the highest version ever loads.

## Directory Map

```
themeisle-sdk-main/
├── load.php            Entry point bundled by each product. Handles version arbitration.
├── start.php           Bootstrap: requires all class files, calls Loader::init().
├── src/
│   ├── Loader.php      Singleton. Owns $products, $available_modules, $labels.
│   ├── Product.php     Model for a registered plugin/theme. Reads file headers.
│   ├── Common/
│   │   ├── Abstract_module.php   Base class every module extends.
│   │   └── Module_factory.php    Instantiates + attaches modules to products.
│   └── Modules/        One file per feature module (18 total).
├── tests/              PHPUnit tests. One file per module.
├── docs/               Integration guides. One file per feature.
└── assets/             Compiled JS/CSS for SDK UI components.
```

## Key Concepts

### How Products Register
Products add their base file to the `themeisle_sdk_products` filter — that is the *only* required step:

```php
add_filter( 'themeisle_sdk_products', function( $products ) {
    $products[] = __FILE__;
    return $products;
} );
```

### Product File Headers
The SDK reads WordPress file headers to configure itself per product:

```
WordPress Available: yes   # yes = on WP.org (free). no = premium only.
Requires License:    yes   # yes = activates the Licenser module.
Pro Slug:            neve-pro  # Slug of the companion pro plugin.
```

### Module Loading Contract
Each module in `src/Modules/` extends `Abstract_Module` and implements:
- `can_load( $product ) : bool` — Should this module run for this product?
- `load( $product ) : self` — Register WordPress hooks.

`Module_Factory::attach()` calls both methods for every registered module/product pair.

### Labels (UI Strings)
All UI strings are in `Loader::$labels` (see [src/Loader.php](src/Loader.php) lines 73–328). Products and plugins override them via:

```php
add_filter( 'themeisle_sdk_labels', function( $labels ) {
    $labels['review']['notice'] = __( 'Custom message', 'text-domain' );
    return $labels;
} );
```

The merge logic ensures the first real translation wins; later callbacks cannot overwrite already-translated values.

## All 18 Modules

| Module | File | Loads when | Doc |
|--------|------|-----------|-----|
| `licenser` | `Licenser.php` | `Requires License: yes` in header | [docs/LICENSER.md](docs/LICENSER.md) |
| `logger` | `Logger.php` | Always (filterable) | [docs/LOGGER.md](docs/LOGGER.md) |
| `notification` | `Notification.php` | Installed >100h, admin user | [docs/NOTIFICATIONS.md](docs/NOTIFICATIONS.md) |
| `review` | `Review.php` | `WordPress Available: yes`, not partner | [docs/REVIEW.md](docs/REVIEW.md) |
| `promotions` | `Promotions.php` | Not partner, not recently dismissed | [docs/PROMOTIONS.md](docs/PROMOTIONS.md) |
| `rollback` | `Rollback.php` | Always | [docs/ROLLBACK.md](docs/ROLLBACK.md) |
| `uninstall_feedback` | `Uninstall_feedback.php` | Always | [docs/UNINSTALL-FEEDBACK.md](docs/UNINSTALL-FEEDBACK.md) |
| `about_us` | `About_us.php` | `{key}_about_us_metadata` filter returns data | [docs/ABOUT-US.md](docs/ABOUT-US.md) |
| `float_widget` | `Float_widget.php` | `{key}_float_widget_metadata` filter returns data | [docs/FLOAT-WIDGET.md](docs/FLOAT-WIDGET.md) |
| `announcements` | `Announcements.php` | Not partner | [docs/ANNOUNCEMENTS.md](docs/ANNOUNCEMENTS.md) |
| `welcome` | `Welcome.php` | `{key}_welcome_metadata` filter returns enabled data | [docs/WELCOME.md](docs/WELCOME.md) |
| `compatibilities` | `Compatibilities.php` | Not partner, admin user | [docs/COMPATIBILITIES.md](docs/COMPATIBILITIES.md) |
| `dashboard_widget` | `Dashboard_widget.php` | Not partner | — |
| `featured_plugins` | `Featured_plugins.php` | Always | — |
| `recommendation` | `Recommendation.php` | Always | — |
| `script_loader` | `Script_loader.php` | Always | [docs/TELEMETRY.md](docs/TELEMETRY.md) |
| `translate` | `Translate.php` | Always | — |
| `translations` | `Translations.php` | Always | — |

## Common Filter Reference

| Filter | Purpose |
|--------|---------|
| `themeisle_sdk_products` | Register a product base file |
| `themeisle_sdk_labels` | Override any UI string |
| `themeisle_sdk_modules` | Add custom module names |
| `themeisle_sdk_required_files` | Add custom module PHP files |
| `themeisle_sdk_enable_telemetry` | Enable JS telemetry (return `true`) |
| `themeisle_sdk_disable_telemetry` | Disable all telemetry (return `true`) |
| `themeisle_sdk_hide_notifications` | Suppress all admin notices |
| `themeisle_sdk_is_black_friday_sale` | Force Black Friday banner on/off |
| `themeisle_sdk_promo_debug` | Force promotions to show (dev only) |
| `themeisle_sdk_welcome_debug` | Force welcome notice to show (dev only) |
| `themeisle_sdk_current_date` | Override current date (useful in tests) |
| `{product_key}_about_us_metadata` | Configure About Us page |
| `{product_key}_float_widget_metadata` | Configure floating help widget |
| `{product_key}_welcome_metadata` | Configure welcome/upgrade notice |
| `{product_key}_load_promotions` | Add promotion slugs for this product |
| `{product_key}_dissallowed_promotions` | Block specific promotion slugs |
| `{product_slug}_sdk_enable_logger` | Enable/disable logger for product |
| `{product_slug}_sdk_should_review` | Enable/disable review prompt |
| `{product_key}_enable_licenser` | Enable/disable licenser module |
| `{product_key}_hide_license_field` | Hide license field on settings page |
| `{product_key}_hide_license_notices` | Suppress license admin notices |
| `themeisle_sdk_compatibilities/{slug}` | Declare version compatibility requirements |
| `themesle_sdk_namespace_{md5(basefile)}` | Set product namespace for license filters |
| `themeisle_sdk_license_process_{ns}` | Trigger license activate/deactivate |
| `product_{ns}_license_status` | Read license status |
| `product_{ns}_license_key` | Read license key |
| `product_{ns}_license_plan` | Read license plan/price ID |
| `tsdk_utmify_{content}` | Override UTM params for a URL |
| `tsdk_utmify_url_{content}` | Override final UTM-ified URL |

## Global Helper Functions

Defined in `load.php`, available everywhere after `init`:

```php
tsdk_utmify( $url, $area, $location )   // Append UTM params
tsdk_lstatus( $file )                   // License status string
tsdk_lis_valid( $file )                 // bool — is license valid?
tsdk_lplan( $file )                     // int — license price_id
tsdk_lkey( $file )                      // string — license key
tsdk_translate_link( $url, $type, $langs ) // Localize a URL
tsdk_support_link( $file )              // Pre-filled support URL or false
```

## Options Written by the SDK

All options use `{product_key}` where key = slug with hyphens replaced by underscores.

| Option | Content |
|--------|---------|
| `{key}_install` | Unix timestamp of first activation |
| `{key}_version` | Last known product version |
| `{key}_license` | Raw license key (free products) |
| `{key}_license_data` | JSON object from license API |
| `{key}_license_status` | `valid` \| `not_active` \| `active_expired` |
| `{key}_logger_flag` | `yes` \| `no` |
| `themeisle_sdk_notifications` | Notification queue metadata |
| `themeisle_sdk_promotions` | Promotion dismiss timestamps |
| `themeisle_sdk_promotions_{promo}_installed` | Whether a promoted plugin was installed |

## API Endpoints

```
https://api.themeisle.com/license/check/{product}/{key}/{url}/{token}
https://api.themeisle.com/license/activate/{product}/{key}
https://api.themeisle.com/license/deactivate/{product}/{key}
https://api.themeisle.com/license/version/{product}/{key}/{version}/{url}
https://api.themeisle.com/license/versions/{product}/{key}/{url}/{version}
https://api.themeisle.com/tracking/log
https://api.themeisle.com/tracking/events
https://api.themeisle.com/tracking/uninstall
```

## Tests

```bash
composer install
./vendor/bin/phpunit
```

Test files mirror module names: `tests/licenser-test.php`, `tests/loader-test.php`, etc.
Sample products used in tests live in `tests/sample_products/`.

## Adding a New Module

1. Create `src/Modules/My_Feature.php` extending `Abstract_Module`
2. Implement `can_load()` and `load()`
3. Add `'my_feature'` to `$available_modules` in `Loader.php`
4. Add the file path to the `$files_to_load` array in `start.php`
5. Add a test in `tests/my-feature-test.php`
6. Add a doc in `docs/MY-FEATURE.md`
