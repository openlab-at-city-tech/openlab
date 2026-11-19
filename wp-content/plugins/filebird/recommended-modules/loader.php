<?php
/**
 * YayRecommendedModules consumer loader.
 *
 * Required by each consumer plugin's main file:
 *   require_once __DIR__ . '/recommended-modules/loader.php';
 *
 * Probes both layouts so the same file works in:
 *   - source repo: modules/<name>/register.php
 *   - synced consumer dir: <name>/register.php (modules/ wrapper flattened)
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/registry.php';

$globs = glob(__DIR__ . '/*/register.php')
    ?: glob(__DIR__ . '/modules/*/register.php')
    ?: [];

foreach ($globs as $register) {
    require_once $register;
}
