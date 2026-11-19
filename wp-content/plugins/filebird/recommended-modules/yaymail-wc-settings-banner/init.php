<?php

declare(strict_types=1);

namespace YayRecommendedModules\YaymailWCSettingsBanner;

defined( 'ABSPATH' ) || exit;

class Plugin
{
    public static function boot(): void
    {
        require __DIR__ . '/main.php';
    }
}

if (function_exists('add_action')) {
    add_action('plugins_loaded', [Plugin::class, 'boot']);
}
