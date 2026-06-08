<?php

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

use RebelCode\Spotlight\Instagram\Wp\SubMenu;
use RebelCode\Spotlight\Instagram\Wp\AdminPage;
use RebelCode\Spotlight\Instagram\Module;
use Psr\Container\ContainerInterface;
use Dhii\Services\Factory;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Extension;

/**
 * This module is only used for development purposes.
 *
 * @since   0.1
 *
 * @package dev
 */
class DevModule extends Module
{
    public const DEV_REQUEST_PARAM = 'sli_developer';
    public const DEV_CAPABILITY = 'sli_developer';

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function run(ContainerInterface $c): void
    {
        // Listen for DB reset requests
        add_action('spotlight/instagram/init', $c->get('reset_db'));
        // Listen for DB media delete requests
        add_action('spotlight/instagram/init', $c->get('delete_media'));
        // Listen for DB thumbnail delete requests
        add_action('spotlight/instagram/init', $c->get('delete_thumbnails'));
        // Listen for log clear requests
        add_action('spotlight/instagram/init', $c->get('clear_log'));
        // Listen for error sim toggle requests
        add_action('spotlight/instagram/init', $c->get('sim_error'));

        // Listen for developer capability requests and add/remove the developer capability
        add_action('init', function () {
            $makeDev = $_GET[static::DEV_REQUEST_PARAM] ?? null;
            if ($makeDev === null || !is_admin()) {
                return;
            }

            $user = wp_get_current_user();
            if ($user === null || !$user->has_cap('manage_options')) {
                return;
            }

            if (boolval($makeDev)) {
                $user->add_cap(static::DEV_CAPABILITY);
            } else {
                $user->remove_cap(static::DEV_CAPABILITY);
            }
        });

        add_filter('spotlight/instagram/api/connect_access_token', [DevAccessTokenHandler::class, 'handle'], 10, 2);

        // {
        //     add_action('init', function () {
        //         add_rewrite_rule('^spotlight/?\??(.*)', 'index.php?sli-admin=1&$matches[1]', 'top');
        //     });
        //
        //     add_filter('query_vars', function ($vars) {
        //         $vars[] = 'sli-admin';
        //
        //         return $vars;
        //     });
        //
        //     add_filter('template_include', function ($template) use ($c) {
        //         return get_query_var('sli-admin', false)
        //             ? $c->get('plugin/dir') . '/includes/admin.php'
        //             : $template;
        //     });
        // }
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories(): array
    {
        return [
            //==========================================================================
            // DEV MENU and PAGE
            //==========================================================================

            'menu/item' => new Factory(['page'], function ($page) {
                return SubMenu::page($page, 'sli-dev', 'Dev tools', 'manage_options', PHP_INT_MAX);
            }),

            // The dev page
            'page' => new Factory(['page/render'], function ($renderFn) {
                return new AdminPage('Spotlight Dev Tools', $renderFn);
            }),

            // The render function for the page
            'page/render' => function (ContainerInterface $c) {
                return new DevPage($c->get('plugin/core'), $c);
            },

            //==========================================================================
            // DEV SERVER (Webpack)
            //==========================================================================

            // Whether or not to use the dev server for the front-end
            'dev_server/enabled' => new Factory(['@plugin/dir'], function ($dir) {
                // If constant defined, use its value
                if (defined('SL_INSTA_UI_DEV_SERVER')) {
                    return SL_INSTA_UI_DEV_SERVER;
                }

                // Otherwise, autodetect built files
                return !file_exists($dir . '/ui/dist/runtime.js');
            }),
            // The URL to the front-end dev server
            'dev_server/url' => new Value('https://localhost:7000'),

            //==========================================================================
            // DEV TOOLS
            //==========================================================================

            'dev_catalog' => new Constructor(DevCatalog::class),

            // The DB reset tool
            'reset_db' => new Constructor(DevResetDb::class, ['@media/actions/delete_all']),

            // The DB media delete tool
            'delete_media' => new Constructor(DevDeleteMedia::class, ['@media/actions/delete_all', '@engine/store']),

            // The DB thumbnail delete tool
            'delete_thumbnails' => new Constructor(DevDeleteThumbnails::class, ['@engine/store/files']),

            // The clear log tool
            'clear_log' => new Constructor(DevClearLog::class),

            // The error simulation tool
            'sim_error' => new Constructor(DevSimError::class),
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getExtensions() : array
    {
        return [
            // Add the menu item to Spotlight menu
            'ui/menu/items' => new Extension(['menu/item'], function ($prev, $item) {
                if (static::isDeveloper()) {
                    $prev[] = $item;
                }

                return $prev;
            }),

            // Use the dev server
            'ui/root_url' => new Extension(
                ['dev_server/enabled', 'dev_server/url'],
                function ($url, $enabled, $devServer) {
                    return $enabled ? $devServer : $url;
                }
            ),

            'engine/fetcher/strategy/catalog_map' => new Extension(['dev_catalog'], function ($prev, $catalog) {
                $prev['DEVELOPER'] = $catalog;

                return $prev;
            }),
        ];
    }

    public static function isDeveloper(): bool
    {
        if (defined('SL_INSTA_DEV') && SL_INSTA_DEV) {
            return true;
        }

        if (!function_exists('wp_get_current_user')) {
            return false;
        }

        $user = wp_get_current_user();

        return $user !== null && $user->has_cap(static::DEV_CAPABILITY);
    }
}
