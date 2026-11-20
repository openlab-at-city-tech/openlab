<?php
/**
 * YayRecommendedModules module Registry — runtime version arbitration.
 *
 * FROZEN ABI. Additive changes only. Older copies of this file shipped by
 * other consumer plugins may load first; if they do, this copy bails before
 * redeclaring the class. Removing or renaming a public method WILL break
 * sites where an older registry is the one that won the load race.
 *
 * Loaded by recommended-modules/loader.php in each consumer plugin.
 */

declare(strict_types=1);

namespace YayRecommendedModules;

defined('ABSPATH') || exit;

// Wrap the class declaration INSIDE the if-block so PHP treats it as
// conditional. Otherwise PHP can early-bind unconditional top-level
// classes during compile/load — registering the class BEFORE any runtime
// statement (including a guard above the class) executes. With opcache
// enabled in WP, this early-binding can fatal on the second consumer's
// include even if a runtime `if (class_exists()) return;` precedes it.
//
// Putting the class inside `if (!class_exists())` forces late binding:
// PHP only registers the class when the if-body actually runs at runtime,
// so the guard works as expected.
if (!class_exists(__NAMESPACE__ . '\\Registry', false)) {

    defined('YAY_CROSS_MODULES_REGISTRY_VERSION') || define('YAY_CROSS_MODULES_REGISTRY_VERSION', '1.3.0');

    class Registry
    {
        /** @var array<string, array<int, array{version: string, path: string}>> */
        private static $candidates = [];

        /** @var array<string, array{version: string, path: string, error?: string}> */
        private static $loaded = [];

        /** @var bool */
        private static $hooked = false;

        /** @var array<string, array<string, bool>> ad_slug => [consumer_slug => true] */
        private static $ad_consumers = [];

        /**
         * Each consumer's bundled module calls this from its register.php.
         * Multiple candidates per module name are collected; load_winners() arbitrates.
         */
        public static function register(string $name, string $version, string $init_path): void
        {
            // Bail loud if a consumer's loader.php is required after plugins_loaded:0 already fired —
            // load_winners() ran and won't fire again, so this candidate would be silently dropped.
            if (function_exists('did_action') && did_action('plugins_loaded') > 0) {
                if (function_exists('error_log')) {
                    error_log("[YayRecommendedModules] late register() for '$name' — already past plugins_loaded; module will not load");
                }
                return;
            }

            self::$candidates[$name][] = [
                'version' => $version,
                'path'    => $init_path,
            ];

            if (!self::$hooked && function_exists('add_action')) {
                self::$hooked = true;
                // Priority 0 so module init runs before any other plugin's plugins_loaded handlers.
                add_action('plugins_loaded', [self::class, 'load_winners'], 0);
            }
        }

        /**
         * Pick newest version per module and require_once its init.php.
         * Wraps each require in try/catch so a single bad module doesn't block the rest.
         */
        public static function load_winners(): void
        {
            foreach (self::$candidates as $name => $candidates) {
                if (isset(self::$loaded[$name])) {
                    continue;
                }
                usort(
                    $candidates,
                    static function (array $a, array $b): int {
                        return version_compare($b['version'], $a['version']);
                    }
                );
                $winner = $candidates[0];
                try {
                    require_once $winner['path'];
                    self::$loaded[$name] = $winner;
                } catch (\Throwable $e) {
                    self::$loaded[$name] = $winner + ['error' => $e->getMessage()];
                    if (function_exists('error_log')) {
                        error_log("[YayRecommendedModules] failed to load module '{$name}': " . $e->getMessage());
                    }
                }
            }
        }

        public static function active_version(string $name): ?string
        {
            return self::$loaded[$name]['version'] ?? null;
        }

        public static function active_path(string $name): ?string
        {
            return self::$loaded[$name]['path'] ?? null;
        }

        /**
         * Consumer plugin declares it bundles/renders the given ad module (used by the
         * ads-toggle module — see modules/ads-toggle). Call once per (consumer_slug, ad_slug)
         * pair, at file scope AFTER this registry.php has loaded (i.e. after requiring
         * recommended-modules/loader.php) — the class must already exist to call this.
         */
        public static function register_ads_consumer(string $consumer_slug, string $ad_slug): void
        {
            self::$ad_consumers[$ad_slug][$consumer_slug] = true;
        }

        /**
         * @return string[] Consumer slugs that registered themselves against $ad_slug.
         */
        public static function get_ads_consumers(string $ad_slug): array
        {
            return array_keys(self::$ad_consumers[$ad_slug] ?? []);
        }

        /**
         * Is $consumer_slug registered against at least one ad module? Used to validate the
         * AJAX toggle request without needing a specific ad_slug (one toggle now covers every
         * ad that consumer registered).
         */
        public static function is_ads_consumer(string $consumer_slug): bool
        {
            foreach (self::$ad_consumers as $consumers) {
                if (isset($consumers[$consumer_slug])) {
                    return true;
                }
            }
            return false;
        }
    }
}
