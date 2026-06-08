<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use WP_Screen;
use RebelCode\Spotlight\Instagram\Wp\SubMenu;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Wp\Menu;
use RebelCode\Spotlight\Instagram\Wp\Asset;
use RebelCode\Spotlight\Instagram\Wp\AdminPage;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Embed\CreatePostEndPoint;
use RebelCode\Spotlight\Instagram\Modules\Dev\DevModule;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Di\EndPointService;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Actions\IgImageProxy;
use Psr\Container\ContainerInterface;
use Dhii\Services\Factory;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factories\StringService;
use Dhii\Services\Factories\FuncService;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\Alias;

/**
 * The module for the UI front-end of the plugin.
 *
 * @since 0.1
 */
class UiModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories(): array
    {
        return [
            //==========================================================================
            // MENU
            //==========================================================================

            // The menu
            'menu' => new Constructor(Menu::class, [
                'main_page',
                'menu/slug',
                'menu/label',
                'menu/capability',
                'menu/icon',
                'menu/position',
                'menu/items',
            ]),

            // Configuration for the menu
            'menu/slug' => new Value('spotlight-instagram'),
            'menu/capability' => new Value('edit_pages'),
            'menu/label' => new Value('Instagram Feeds'),
            'menu/icon' => new Value('dashicons-instagram'),
            'menu/position' => new Value(30),

            // The items that appear under the WP Admin menu entry
            // The UI react app  will mount over the menu with identical-looking components. But we still need to
            // register them in order for the menu to appear while the user is on pages where the app is not loaded.
            'menu/items' => new Factory(['menu/slug', 'menu/capability'], function ($parentSlug, $cap) {
                $parentUrl = admin_url("admin.php?page={$parentSlug}");

                return [
                    SubMenu::url("{$parentUrl}&screen=feeds", 'Feeds', $cap),
                    SubMenu::url("{$parentUrl}&screen=promotions", 'Promotions', $cap),
                    SubMenu::url("{$parentUrl}&screen=analytics", "Analytics", $cap),
                    SubMenu::url("{$parentUrl}&screen=guides", "Guides", $cap),
                    SubMenu::url("{$parentUrl}&screen=settings", 'Settings', $cap),
                ];
            }),

            //==========================================================================
            // MAIN PAGE (Where the app is mounted)
            //==========================================================================

            // The page to show for the menu
            'main_page' => new Constructor(AdminPage::class, ['main_page/title', 'main_page/render_fn']),

            // The title of the page, shown in the browser's tab
            'main_page/title' => new Value('Spotlight'),

            // The render function for the page
            'main_page/render_fn' => new FuncService(['main_page/root_element_id'], function ($id) {
                do_action('spotlight/instagram/enqueue_admin_app');

                return sprintf('<div class="wrap"><div id="%s"></div></div>', $id);
            }),

            // The ID of the root element, onto which React will mount
            'main_page/root_element_id' => new Value('spotlight-instagram-admin'),

            //==========================================================================
            // PATHS
            //==========================================================================

            // The path, relative to the plugin directory, where the UI code is located
            'root_path' => new Value('/ui'),
            // The path, relative to the root URL, from where assets are located
            'assets_path' => new Value('/dist'),
            // The path, relative to the root URL, from where static (non-built) assets are located
            'static_path' => new Value('/static'),
            // The path, relative to assets_path, from where scripts are located
            'scripts_path' => new Value(''),
            // The path, relative to assets_path, from where styles are located
            'styles_path' => new Value('/styles'),
            // The path, relative to assets_path, from where images are located
            'images_path' => new Value('/images'),

            //==========================================================================
            // URLS
            //==========================================================================

            // The URL to where the UI code is located
            'root_url' => new StringService('{0}{1}', ['@plugin/url', 'root_path']),
            // The URL from where assets will be served
            'assets_url' => new StringService('{0}{1}', ['root_url', 'assets_path']),
            // The URL from where static assets will be served
            'static_url' => new StringService('{0}{1}', ['root_url', 'static_path']),
            // The URL from where scripts and styles are served
            'scripts_url' => new StringService('{0}{1}', ['assets_url', 'scripts_path']),
            'styles_url' => new StringService('{0}{1}', ['assets_url', 'styles_path']),
            // The URL from where images will be served
            'images_url' => new StringService('{0}{1}', ['root_url', 'images_path']),

            //==========================================================================
            // ASSETS
            //==========================================================================

            // The version to use for assets (the plugin's current version)
            'assets_ver' => new Alias('plugin/version', new Value('')),

            // The scripts
            'scripts' => new Factory(['scripts_url', 'assets_ver'], function ($url, $ver) {
                return [
                    /* === VENDORS === */
                    'sli-runtime' => Asset::script("{$url}/runtime.js", $ver),
                    'sli-admin-vendors' => Asset::script("{$url}/admin-vendors.js", $ver),
                    'sli-common-vendors' => Asset::script("{$url}/common-vendors.js", $ver, [
                        'sli-runtime',
                        'react',
                        'react-dom',
                    ]),

                    /* === FEED === */

                    'sli-common' => Asset::script("{$url}/common.js", $ver, [
                        'sli-common-vendors',
                    ]),
                    'sli-feed' => Asset::script("{$url}/feed.js", $ver, [
                        'sli-common',
                    ]),

                    /* === ADMIN COMMON === */

                    'sli-admin-common' => Asset::script("{$url}/admin-common.js", $ver, [
                        'sli-admin-vendors',
                        'sli-common',
                        'sli-feed',
                    ]),
                    'sli-editor' => Asset::script("{$url}/feed-editor.js", $ver, [
                        'sli-admin-common',
                        'sli-feed',
                    ]),

                    /* === FRONT APP === */

                    'sli-front' => Asset::script("{$url}/front-app.js", $ver, [
                        'sli-feed',
                    ]),

                    /* === ADMIN APP === */

                    // The main admin app
                    'sli-admin' => Asset::script("{$url}/admin-app.js", $ver, [
                        'sli-editor',
                        'wp-element',
                        'wp-i18n'
                    ]),

                    /* === WP BLOCK APP === */

                    'sli-wp-block' => Asset::style("{$url}/wp-block.js", $ver, [
                        'sli-admin-common',
                        'sli-feed',
                    ]),
                ];
            }),

            // The styles
            'styles' => new Factory(['styles_url', 'static_url', 'assets_ver'], function ($url, $static, $ver) {
                return [
                    'sli-common-vendors' => Asset::style("{$url}/common-vendors.css", $ver),
                    'sli-common' => Asset::style("{$url}/common.css", $ver, [
                        'sli-common-vendors',
                    ]),
                    'sli-feed' => Asset::style("{$url}/feed.css", $ver, [
                        'sli-common',
                    ]),
                    'sli-admin-common' => Asset::style("{$url}/admin-common.css", $ver, [
                        'sli-common',
                        'sli-feed',
                        'dashicons'
                    ]),
                    'sli-admin' => Asset::style("{$url}/admin-app.css", $ver, [
                        'sli-admin-common',
                        'wp-edit-post',
                    ]),
                    'sli-editor' => Asset::style("{$url}/feed-editor.css", $ver, [
                        'sli-admin-common',
                        'sli-feed',
                    ]),
                    'sli-front' => Asset::style("{$url}/front-app.css", $ver, [
                        'sli-common',
                        'sli-feed',
                    ]),
                    'sli-wp-block' => Asset::style("{$url}/wp-block.css", $ver),
                    // Styles to override Freemius CSS
                    'sli-fs-override' => Asset::style("{$static}/fs-override.css", $ver),
                    // Styles to use in WP Admin
                    'sli-wp-admin' => Asset::style("{$static}/sli-wp-admin.css", $ver),
                ];
            }),

            // The function that registers all the scripts and styles
            'register_assets_fn' => new FuncService(['scripts', 'styles'], function ($arg, $scripts, $styles) {
                Arrays::eachAssoc($scripts, [Asset::class, 'register']);
                Arrays::eachAssoc($styles, [Asset::class, 'register']);

                do_action('spotlight/instagram/register_assets');
            }),

            // The scripts to load for the admin app
            'admin_scripts' => new Value([
                'wp_enqueue_media', // see wp_enqueue_media()
                'sli-admin',
            ]),

            // The styles to load for the admin app
            'admin_styles' => new Value([
                'sli-admin',
                'sli-editor',
            ]),

            // The scripts to load for the front app
            'front_scripts' => new Value([
                'sli-front',
            ]),

            // The styles to load for the front app
            'front_styles' => new Value([
                'sli-front',
            ]),

            //==========================================================================
            // LOCALIZATION
            //==========================================================================

            // Localization data for the common bundle
            'l10n/common/var' => new Value('SliCommonL10n'),
            'l10n/common' => new Factory(
                ['@plugin/tier', 'images_url', '@rest_api/auth/public/token'],
                function ($tier, $imagesUrl, $token) {
                    $siteDomain = substr(get_site_url(null, '', 'https'), strlen('https'));
                    return [
                        'tier' => $tier,
                        'siteDomain' => $siteDomain,
                        'restApi' => [
                            'baseUrl' => '',
                            'authToken' => $token,
                        ],
                        'imagesUrl' => $imagesUrl,
                    ];
                }
            ),

            // Localization data for the admin-common bundle
            'l10n/admin-common/var' => new Value('SliAdminL10n'),
            'l10n/admin-common' => new Factory(
                ['@plugin/version', '@ig/api/basic/auth_url', '@ig/api/graph/auth_url', 'onboarding/is_done'],
                function ($version, $basicAuthUrl, $graphAuthUrl, $onboardingDone) {
                    $postTypes = Arrays::map(PostType::getPostTypes('and', ['public' => true]), function ($postType) {
                        return [
                            'slug' => $postType->name,
                            'labels' => [
                                'singularName' => $postType->labels->singular_name,
                                'pluralName' => $postType->label,
                            ],
                        ];
                    });

                    return [
                        'version' => $version,
                        'adminUrl' => admin_url(),
                        'restApi' => [
                            'personalAuthUrl' => $basicAuthUrl,
                            'businessAuthUrl' => $graphAuthUrl,
                            'wpNonce' => wp_create_nonce('wp_rest'),
                        ],
                        'doOnboarding' => !$onboardingDone,
                        'cronSchedules' => Arrays::mapPairs(wp_get_schedules(), function ($key, $val, $idx) {
                            return [$idx, array_merge($val, ['key' => $key])];
                        }),
                        'postTypes' => $postTypes,
                        'hasElementor' => defined('ELEMENTOR_VERSION'),
                        'devModeEnabled' => DevModule::isDeveloper(),
                    ];
                }
            ),

            //==========================================================================
            // ADMIN ONBOARDING
            //==========================================================================

            // The name of option that indicates whether the user went through the onboarding process
            'onboarding/option' => new Value('sli_user_did_onboarding'),
            // Whether or not the user has completed the onboarding process
            'onboarding/is_done' => new Factory(['onboarding/option'], function ($option) {
                return filter_var(get_option($option, false), FILTER_VALIDATE_BOOLEAN);
            }),

            //==========================================================================
            // INSTAGRAM IMAGE PROXY
            //==========================================================================

            'image_proxy' => new Constructor(IgImageProxy::class),

            //==========================================================================
            // REST API ENDPOINTS
            //==========================================================================

            'endpoints/create_post' => new EndPointService(
                '/create_post',
                ['POST'],
                CreatePostEndPoint::class,
                [],
                '@rest_api/auth/user'
            ),

            //==========================================================================
            // CONFIG
            //==========================================================================

            'config/show_premium_features' => new Factory([], function () {
                return new WpOption('sli_show_premium_features', true, false, WpOption::SANITIZE_BOOL);
            }),
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getExtensions(): array
    {
        return [
            'wp/menus' => new ArrayExtension(['menu']),
            'config/entries' => new ArrayExtension([
                'showPremiumFeatures' => 'config/show_premium_features',
            ]),
            'rest_api/endpoints' => new ArrayExtension(['endpoints/create_post'])
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function run(ContainerInterface $c): void
    {
        // Register the image proxy to listen for requests
        add_action('init', $c->get('image_proxy'));

        // Register the assets
        {
            add_action('spotlight/instagram/init', $c->get('register_assets_fn'), 100);
        }

        // Actions for enqueueing assets
        {
            // Action that localizes config for the apps.
            add_action('spotlight/instagram/localize_config', function () use ($c) {
                $common = $c->get('l10n/common');
                wp_localize_script('sli-common', $c->get('l10n/common/var'), $common);

                $adminCommon = $c->get('l10n/admin-common');
                wp_localize_script('sli-admin-common', $c->get('l10n/admin-common/var'), $adminCommon);
            });

            // Action that enqueues the admin app.
            add_action('spotlight/instagram/enqueue_admin_app', function () use ($c) {
                // Enqueue assets
                array_map([$this, 'enqueueScript'], $c->get('admin_scripts'));
                array_map([$this, 'enqueueStyle'], $c->get('admin_styles'));

                // Localize
                do_action('spotlight/instagram/localize_config');
            });

            // Action that enqueues the front app.
            add_action('spotlight/instagram/enqueue_front_app', function () use ($c) {
                // Enqueue assets
                array_map([$this, 'enqueueScript'], $c->get('front_scripts'));
                array_map([$this, 'enqueueStyle'], $c->get('front_styles'));

                // Localize
                do_action('spotlight/instagram/localize_config');
            });
        }

        {
            // Remove WordPress' emoji-to-image conversion
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
        }

        // Handlers for onboarding
        {
            // When a feed is saved, update the onboarding option
            add_action('save_post', function ($postId, $post) use ($c) {
                if ($post->post_type === $c->get('feeds/cpt/slug')) {
                    update_option($c->get('onboarding/option'), true);
                }
            }, 10, 2);
        }

        // Hide the Freemius promo tab
        add_action('admin_enqueue_scripts', function () use ($c) {
            $screen = get_current_screen();

            if ($screen instanceof WP_Screen && stripos($screen->id, 'spotlight-instagram') !== false) {
                echo '<style>#fs_promo_tab { display: none; }</style>';
            }

            // Enqueue our admin styles
            wp_enqueue_style('sli-wp-admin');
            // Enqueue admin styles that override Freemius' styles
            wp_enqueue_style('sli-fs-override');
        });
    }

    /**
     * Enqueues a script by its handle, or via a callback function (useful for enqueueing WP core assets).
     *
     * @param string|callable $script Script handle or callback function.
     */
    protected function enqueueScript($script)
    {
        if (is_callable($script)) {
            $script();
        } else {
            wp_enqueue_script($script);
        }
    }

    /**
     * Enqueues a style by its handle, or via a callback function (useful for enqueueing WP core assets).
     *
     * @param string|callable $style Style handle or callback function.
     */
    protected function enqueueStyle($style)
    {
        if (is_callable($style)) {
            $style();
        } else {
            wp_enqueue_style($style);
        }
    }
}
