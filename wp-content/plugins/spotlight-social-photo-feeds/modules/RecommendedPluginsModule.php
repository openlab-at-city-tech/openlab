<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Extension;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\EndPointService;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\RecommendedPlugins\RecommendedPlugins;
use RebelCode\Spotlight\Instagram\RecommendedPlugins\RecommendedPluginsBanner;
use RebelCode\Spotlight\Instagram\RecommendedPlugins\RecommendedPluginsPage;
use RebelCode\Spotlight\Instagram\RestApi\Auth\AuthUserCapability;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\RecommendedPlugins\DismissBannerEndPoint;
use RebelCode\Spotlight\Instagram\Wp\AdminPage;
use RebelCode\Spotlight\Instagram\Wp\SubMenu;

/**
 * Recommends other RebelCode plugins: a "Recommended plugins" page under the Spotlight menu, and a dismissible banner
 * on the Feeds screen. The list is bundled; nothing is fetched from a remote server.
 */
class RecommendedPluginsModule extends Module
{
    public function run(ContainerInterface $c): void
    {
        add_action('admin_enqueue_scripts', function () use ($c) {
            if ($this->isOnPage($c)) {
                add_thickbox();
                wp_enqueue_script('plugin-install');
                wp_enqueue_script('updates');
            }
        });

        add_action('admin_footer', function () use ($c) {
            if ($this->isOnPage($c)) {
                wp_print_request_filesystem_credentials_modal();
            }
        });
    }

    public function getFactories(): array
    {
        return [
            // Built lazily so that the strings are translated after "init"
            'list' => new Factory(['@ui/images_url'], function (string $imagesUrl) {
                return [
                    [
                        'slug' => 'vireo-analytics',
                        'name' => 'Vireo Analytics',
                        'description' => __(
                            'Cookieless analytics for WordPress, with a report that shows how visitors use your Instagram feeds.',
                            'sl-insta'
                        ),
                        'icon' => $imagesUrl . '/recommended/vireo-analytics.png',
                        'banner' => [
                            'message' => __(
                                'See how visitors use your Instagram feeds: which posts they open, the links they follow and who presses Follow. Vireo Analytics is a free, cookieless analytics plugin from the Spotlight team, with a report built for Spotlight.',
                                'sl-insta'
                            ),
                            'install' => __('Install Vireo Analytics', 'sl-insta'),
                            'activate' => __('Activate Vireo Analytics', 'sl-insta'),
                        ],
                    ],
                    [
                        'slug' => 'wp-rss-aggregator',
                        'name' => 'WP RSS Aggregator',
                        'description' => __(
                            'Import RSS feeds and show them on your site, or turn feed items into posts.',
                            'sl-insta'
                        ),
                        'icon' => $imagesUrl . '/recommended/wp-rss-aggregator.png',
                    ],
                ];
            }),

            'plugins' => new Constructor(RecommendedPlugins::class, ['list']),

            'page/slug' => new Value('spotlight-recommended-plugins'),
            'page/capability' => new Value('install_plugins'),

            'page' => new Factory(['plugins'], function (RecommendedPlugins $plugins) {
                return new AdminPage(
                    __('Recommended plugins', 'sl-insta'),
                    new RecommendedPluginsPage($plugins)
                );
            }),

            'menu/item' => new Factory(
                ['page', 'page/slug', 'page/capability'],
                function (AdminPage $page, string $slug, string $capability) {
                    return SubMenu::page(
                        $page,
                        $slug,
                        __('Recommended plugins', 'sl-insta'),
                        $capability
                    );
                }
            ),

            'banner/min_age' => new Value(7 * DAY_IN_SECONDS),
            'banner/meta_key' => new Value('sli_recommended_banner_dismissed'),

            // Decides whether the Feeds screen banner shows, and remembers its dismissal
            'banner' => new Constructor(RecommendedPluginsBanner::class, [
                'plugins',
                'page/capability',
                'banner/meta_key',
                'banner/min_age',
                '@user/config/date_started',
            ]),

            // Same capability as the banner, so only users who can see it can dismiss it
            'endpoint/auth' => new Constructor(AuthUserCapability::class, ['page/capability']),

            'endpoint' => new EndPointService(
                '/recommended_plugins/dismiss_banner',
                ['POST'],
                DismissBannerEndPoint::class,
                ['banner'],
                'endpoint/auth'
            ),
        ];
    }

    public function getExtensions(): array
    {
        return [
            'ui/menu/items' => new Extension(['menu/item'], function ($items, SubMenu $item) {
                $items[] = $item;

                return $items;
            }),
            'ui/l10n/admin-common' => new Extension(
                ['banner', 'page/slug', 'page/capability'],
                function ($l10n, RecommendedPluginsBanner $banner, string $slug, string $capability) {
                    $l10n['recommendedPluginsUrl'] = current_user_can($capability)
                        ? admin_url('admin.php?page=' . $slug)
                        : null;
                    // The banner only shows in the admin, so skip the plugin lookups on front-end pages
                    $l10n['recommendedBanner'] = is_admin() ? $banner->getData() : null;

                    return $l10n;
                }
            ),
            'rest_api/endpoints' => new ArrayExtension(['endpoint']),
        ];
    }

    protected function isOnPage(ContainerInterface $c): bool
    {
        global $plugin_page;

        return $plugin_page === $c->get('page/slug');
    }
}
