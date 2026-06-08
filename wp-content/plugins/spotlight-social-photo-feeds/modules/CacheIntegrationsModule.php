<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Module;
use function rocket_clean_exclude_file;

class CacheIntegrationsModule extends Module
{
    public function run(ContainerInterface $c): void
    {
        /*------------------------------------------------------------------------------------------------
         * WP ROCKET
         ------------------------------------------------------------------------------------------------*/
        {
            // Exclude JS files from minification and combining
            add_filter('rocket_exclude_js', function ($exclude) use ($c) {
                $exclude[] = rocket_clean_exclude_file($c->get('ui/assets_url') . '/(.*).js');
                $exclude[] = rocket_clean_exclude_file('/wp-includes/js/dist/vendor/react(.*).js');
                $exclude[] = rocket_clean_exclude_file('/wp-includes/js/dist/development/react(.*).js');

                return $exclude;
            });
            // Exclude inline JS (such as localized data) from minification and combining
            add_filter('rocket_excluded_inline_js_content', function ($exclude) use ($c) {
                $exclude[] = $c->get('ui/l10n/common/var');

                return $exclude;
            });
        }

        /*------------------------------------------------------------------------------------------------
         * LITESPEED CACHE
         ------------------------------------------------------------------------------------------------*/
        {
            add_filter('litespeed_optimize_js_excludes', function ($exclude) {
                $exclude[] = 'spotlight-';
                $exclude[] = 'react';

                return $exclude;
            });
        }
    }
}
