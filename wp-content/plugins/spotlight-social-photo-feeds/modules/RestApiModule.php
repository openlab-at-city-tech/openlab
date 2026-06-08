<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Extension;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\ServiceList;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Iris\Engine;
use RebelCode\Spotlight\Instagram\Di\EndPointService;
use RebelCode\Spotlight\Instagram\Feeds\FeedManager;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Notifications\NotificationProvider;
use RebelCode\Spotlight\Instagram\RestApi\Auth\AuthUserCapability;
use RebelCode\Spotlight\Instagram\RestApi\Auth\AuthVerifyToken;
use RebelCode\Spotlight\Instagram\RestApi\AuthGuardInterface;
use RebelCode\Spotlight\Instagram\RestApi\EndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPointManager;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\ConnectAccountEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts\AddCustomMediaEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts\DeleteCustomMediaEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts\GetCustomMediaEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts\UpdateCustomMediaEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\DeleteAccountEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\DeleteAccountMediaEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\GetAccessTokenEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\GetAccountsEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\UpdateAccountEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\ErrorLog\ClearErrorLogEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\ErrorLog\DownloadErrorLogEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\ErrorLog\GetErrorLogEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Feeds\DeleteFeedsEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Feeds\GetFeedsEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Feeds\SaveFeedsEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Media\GetMediaEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Media\ImportMediaEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Media\RegenerateFilesEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Notifications\GetNotificationsEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Promotion\GetPostNiceUrlEndPoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Promotion\SearchPostsEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Settings\GetSettingsEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Settings\SaveSettingsEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Tools\CleanUpMediaEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Tools\ClearCacheEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Tools\ClearCacheFeedEndpoint;
use RebelCode\Spotlight\Instagram\RestApi\Transformers\AccountTransformer;
use RebelCode\Spotlight\Instagram\RestApi\Transformers\FeedsTransformer;
use RebelCode\Spotlight\Instagram\RestApi\Transformers\MediaTransformer;
use RebelCode\Spotlight\Instagram\Utils\Strings;
use RebelCode\Spotlight\Instagram\Wp\CronJob;

/**
 * The module that adds the REST API to the plugin.
 *
 * @since 0.1
 */
class RestApiModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories(): array
    {
        return [
            // The namespace for the REST API
            'namespace' => new Value('sl-insta'),

            // The REST API base URL
            'base_url' => new Factory(['namespace'], function ($ns) {
                return rest_url() . $ns;
            }),

            // The REST API endpoint manager
            'manager' => new Factory(['namespace', 'endpoints'], function ($ns, $endpoints) {
                return new EndPointManager($ns, $endpoints);
            }),

            // The REST API endpoints under the `namespace`
            'endpoints' => new ServiceList([
                'endpoints/feeds/get',
                'endpoints/feeds/save',
                'endpoints/feeds/delete',
                'endpoints/accounts/get',
                'endpoints/accounts/delete',
                'endpoints/accounts/connect',
                'endpoints/accounts/update',
                'endpoints/accounts/delete_media',
                'endpoints/accounts/get_access_token',
                'endpoints/accounts/custom_media/get',
                'endpoints/accounts/custom_media/add',
                'endpoints/accounts/custom_media/update',
                'endpoints/accounts/custom_media/delete',
                'endpoints/media/get',
                'endpoints/media/feed',
                'endpoints/media/import',
                'endpoints/media/regen_files',
                'endpoints/promotion/search_posts',
                'endpoints/promotion/nice_url',
                'endpoints/settings/get',
                'endpoints/settings/patch',
                'endpoints/notifications/get',
                'endpoints/clean_up_media',
                'endpoints/clear_cache',
                'endpoints/clear_cache_feed',
                'endpoints/error_log/get',
                'endpoints/error_log/download',
                'endpoints/error_log/clear',
            ]),

            //==========================================================================
            // USER AUTH
            //==========================================================================

            // The user capability required to access the REST API endpoints that manage entities
            'auth/user/capability' => new Value('edit_pages'),

            // The auth guard to use to authorize logged in users
            'auth/user' => new Constructor(AuthUserCapability::class, ['auth/user/capability']),

            //==========================================================================
            // PUBLIC AUTH
            //==========================================================================

            // The HTTP header where the public REST API token should be included for authorized requests
            'auth/public/nonce_header' => new Value('X-Sli-Auth-Token'),
            // The name of the DB option where the public token is stored
            'auth/public/token_option' => new Value('sli_api_auth_token'),
            // The token to use for public REST API requests.
            // This factory should detect when the site URL changes and
            'auth/public/token' => new Factory(['auth/public/token_option'], function ($optionName) {
                $token = get_option($optionName, null);

                if (empty($token)) {
                    $token = sha1(Strings::generateRandom(32));
                    update_option($optionName, $token);
                }

                return $token;
            }),

            // The auth guard to use for REST API endpoints to authorize requests against the token
            'auth/public' => new Constructor(AuthVerifyToken::class, [
                'auth/public/nonce_header',
                'auth/public/token',
            ]),

            //==========================================================================
            // FEEDS
            //==========================================================================

            // The transformer for formatting feeds into REST API responses
            'feeds/transformer' => new Constructor(FeedsTransformer::class, [
                '@wp/db',
            ]),

            // The REST API endpoint for retrieving feeds
            'endpoints/feeds/get' => new Factory(
                ['@feeds/cpt', 'feeds/transformer', 'auth/user'],
                function ($cpt, $t9r, $auth) {
                    return new EndPoint(
                        '/feeds(?:/(?P<id>\d+))?',
                        ['GET'],
                        new GetFeedsEndpoint($cpt, $t9r),
                        $auth
                    );
                }
            ),

            // The REST API endpoint for saving feeds
            'endpoints/feeds/save' => new Factory(
                ['@feeds/cpt', 'feeds/transformer', 'auth/user'],
                function ($cpt, $t9r, $auth) {
                    return new EndPoint(
                        '/feeds(?:/(?P<id>\d+))?',
                        ['POST'],
                        new SaveFeedsEndpoint($cpt, $t9r),
                        $auth
                    );
                }
            ),
            'endpoints/feeds/delete' => new Factory(
                ['@feeds/cpt', 'feeds/transformer', 'auth/user'],
                function ($cpt, $t9r, $auth) {
                    return new EndPoint(
                        '/feeds/delete/(?P<id>\d+)',
                        ['POST'],
                        new DeleteFeedsEndpoint($cpt, $t9r),
                        $auth
                    );
                }
            ),

            //==========================================================================
            // ACCOUNTS
            //==========================================================================

            // The transformer for formatting accounts into REST API responses
            'accounts/transformer' => new Constructor(AccountTransformer::class, [
                '@feeds/cpt',
            ]),

            // The GET endpoint for accounts
            'endpoints/accounts/get' => new Factory(
                ['@accounts/cpt', 'accounts/transformer', 'auth/public'],
                function ($cpt, $t9r, $auth) {
                    return new EndPoint(
                        '/accounts(?:/(?P<id>\d+))?',
                        ['GET'],
                        new GetAccountsEndPoint($cpt, $t9r),
                        $auth
                    );
                }
            ),
            // The DELETE endpoint for accounts
            'endpoints/accounts/delete' => new Factory(
                ['@accounts/cpt', '@media/cpt', 'accounts/transformer', 'auth/user'],
                function ($accountsCpt, $mediaCpt, $t9r, $auth) {
                    return new EndPoint(
                        '/accounts/delete/(?P<id>\d+)',
                        ['POST'],
                        new DeleteAccountEndPoint($accountsCpt, $mediaCpt, $t9r),
                        $auth
                    );
                }
            ),
            // The endpoint to manually connect an account by access token
            'endpoints/accounts/connect' => new Factory(
                ['@ig/api/client', '@accounts/cpt', 'auth/user'],
                function ($client, $cpt, $auth) {
                    return new EndPoint(
                        '/connect',
                        ['POST'],
                        new ConnectAccountEndPoint($client, $cpt),
                        $auth
                    );
                }
            ),
            // The endpoint for updating account information
            'endpoints/accounts/update' => new Factory(
                ['@accounts/cpt', 'auth/user'],
                function ($cpt, $auth) {
                    return new EndPoint(
                        '/accounts',
                        ['POST'],
                        new UpdateAccountEndPoint($cpt),
                        $auth
                    );
                }
            ),
            // The endpoint that deletes media for an account
            'endpoints/accounts/delete_media' => new Factory(
                ['@accounts/cpt', '@engine/store', 'auth/user'],
                function ($accountsCpt, $store, $auth) {
                    return new EndPoint(
                        '/account_media/delete/(?P<id>\d+)',
                        ['POST'],
                        new DeleteAccountMediaEndpoint($accountsCpt, $store),
                        $auth
                    );
                }
            ),
            // The endpoint to provides access tokens
            'endpoints/accounts/get_access_token' => new Factory(
                ['@accounts/cpt', 'auth/user'],
                function ($cpt, $auth) {
                    return new EndPoint(
                        '/access_token/(?P<id>\d+)',
                        ['GET'],
                        new GetAccessTokenEndPoint($cpt),
                        $auth
                    );
                }
            ),

            // The endpoint for getting the custom media posts for an account
            'endpoints/accounts/custom_media/get' => new Factory(
                ['@server/instance', '@accounts/cpt', 'auth/user'],
                function ($server, $accountsCpt, $auth) {
                    return new EndPoint(
                        '/accounts/(?P<id>\d+)/custom_media',
                        ['GET'],
                        new GetCustomMediaEndPoint($server, $accountsCpt),
                        $auth
                    );
                }
            ),

            // The endpoint for adding a custom media post to an account
            'endpoints/accounts/custom_media/add' => new Factory(
                ['auth/user'],
                function ($auth) {
                    return new EndPoint(
                        '/accounts/(?P<id>\d+)/custom_media',
                        ['POST'],
                        new AddCustomMediaEndPoint(),
                        $auth
                    );
                }
            ),

            // The endpoint for updating a custom media post for an account
            'endpoints/accounts/custom_media/update' => new Factory(
                ['auth/user'],
                function ($auth) {
                    return new EndPoint(
                        '/accounts/(?P<account_id>\d+)/custom_media/(?P<media_id>[a-zA-Z0-9-_]+)',
                        ['POST'],
                        new UpdateCustomMediaEndPoint(),
                        $auth
                    );
                }
            ),

            // The endpoint for deleting a custom media post from an account
            'endpoints/accounts/custom_media/delete' => new Factory(
                ['auth/user'],
                function ($auth) {
                    return new EndPoint(
                        '/accounts/(?P<account_id>\d+)/custom_media/(?P<media_id>[a-zA-Z0-9-_]+)/delete',
                        ['POST'],
                        new DeleteCustomMediaEndPoint(),
                        $auth
                    );
                }
            ),

            //==========================================================================
            // MEDIA
            //==========================================================================

            // The transformer that transforms IG media instances into REST API response format
            'media/transformer' => new Constructor(MediaTransformer::class, []),

            // The GET endpoint for retrieving media
            'endpoints/media/get' => new Factory(
                ['@server/instance', 'auth/public'],
                function ($server, $auth) {
                    return new EndPoint(
                        '/media',
                        ['GET'],
                        new GetMediaEndPoint($server),
                        $auth
                    );
                }
            ),
            // Equivalent to the above service, but POST
            'endpoints/media/feed' => new Factory(
                ['@server/instance', 'auth/public'],
                function ($server, $auth) {
                    return new EndPoint(
                        '/media/feed',
                        ['POST'],
                        new GetMediaEndPoint($server),
                        $auth
                    );
                }
            ),

            // The endpoint for importing media posts from IG
            'endpoints/media/import' => new Factory(
                ['@server/instance', 'auth/public'],
                function ($server, $auth) {
                    return new EndPoint(
                        '/media/import',
                        ['POST'],
                        new ImportMediaEndPoint($server),
                        $auth
                    );
                }
            ),

            // The endpoint for regenerating thumbnails and video posts
            'endpoints/media/regen_files' => new Factory(
                ['@engine/store', 'auth/user'],
                function ($store, $auth) {
                    return new EndPoint(
                        '/media/files/regen',
                        ['POST'],
                        new RegenerateFilesEndPoint($store),
                        $auth
                    );
                }
            ),

            //==========================================================================
            // PROMOTION
            //==========================================================================

            // The endpoint for searching for posts from the "Promote" feature
            'endpoints/promotion/search_posts' => new Factory(['auth/user'], function ($auth) {
                return new EndPoint(
                    '/search_posts',
                    ['GET'],
                    new SearchPostsEndpoint(),
                    $auth
                );
            }),

            // The endpoint for getting the nice URLs for posts
            'endpoints/promotion/nice_url' => new EndPointService(
                '/nice_url',
                ['GET'],
                GetPostNiceUrlEndPoint::class,
                [],
                'auth/user'
            ),

            //==========================================================================
            // SETTINGS
            //==========================================================================

            // The endpoint for retrieving settings
            'endpoints/settings/get' => new Factory(['@config/set', 'auth/user'], function ($config, $auth) {
                return new EndPoint(
                    '/settings',
                    ['GET'],
                    new GetSettingsEndpoint($config),
                    $auth
                );
            }),

            // The endpoint for changing settings
            'endpoints/settings/patch' => new Factory(['@config/set', 'auth/user'], function ($config, $auth) {
                return new EndPoint(
                    '/settings',
                    ['POST', 'PUT', 'PATCH'],
                    new SaveSettingsEndpoint($config),
                    $auth
                );
            }),

            //==========================================================================
            // NOTIFICATIONS
            //==========================================================================

            // The endpoint for notifications
            'endpoints/notifications/get' => new Factory(
                ['@notifications/store', 'auth/user'],
                function (NotificationProvider $store, AuthGuardInterface $authGuard) {
                    return new EndPoint(
                        '/notifications',
                        ['GET'],
                        new GetNotificationsEndPoint($store),
                        $authGuard
                    );
                }
            ),

            //==========================================================================
            // TOOLS
            //==========================================================================

            // The endpoint for running the media clean up optimization
            'endpoints/clean_up_media' => new Factory(
                ['@cleaner/action', 'auth/user'],
                function ($action, AuthGuardInterface $authGuard) {
                    return new EndPoint(
                        '/clean_up_media',
                        ['POST'],
                        new CleanUpMediaEndpoint($action),
                        $authGuard
                    );
                }
            ),

            // The endpoint for clearing the API cache
            'endpoints/clear_cache' => new EndPointService(
                '/clear_cache',
                ['POST'],
                ClearCacheEndpoint::class,
                ['@ig/cache/pool', '@media/actions/delete_all'],
                'auth/user'
            ),

            // The endpoint for clearing the cache for a specific feed
            'endpoints/clear_cache_feed' => new Factory(
                ['@engine/instance', '@feeds/manager', 'auth/user'],
                function (Engine $engine, FeedManager $feedManager, AuthGuardInterface $authGuard) {
                    return new EndPoint(
                        '/clear_cache/feed',
                        ['POST'],
                        new ClearCacheFeedEndpoint($engine, $feedManager),
                        $authGuard
                    );
                }
            ),

            'endpoints/error_log/get' => new EndPointService(
                '/error_log',
                ['GET'],
                GetErrorLogEndPoint::class,
                [],
                'auth/user'
            ),

            'endpoints/error_log/download' => new EndPointService(
                '/error_log/download',
                ['GET'],
                DownloadErrorLogEndPoint::class,
                [],
                'auth/user'
            ),

            'endpoints/error_log/clear' => new EndPointService(
                '/error_log/clear',
                ['POST'],
                ClearErrorLogEndPoint::class,
                [],
                'auth/user'
            ),

            // The value to use for the "Expires" HTTP header in media endpoints
            'headers/media/expiry' => new Factory(['@updater/main/job'], function (CronJob $job) {
                $event = CronJob::getScheduledEvent($job);

                return is_object($event)
                    ? ($event->timestamp ?? 0)
                    : 0;
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
            // Add the REST API's URL to the localization data for the common bundle
            'ui/l10n/common' => new Extension(['base_url'], function ($config, $baseUrl) {
                $config['restApi']['baseUrl'] = $baseUrl;

                return $config;
            }),
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function run(ContainerInterface $c): void
    {
        add_action('rest_api_init', function () use ($c) {
            /* @var $manager EndPointManager */
            $manager = $c->get('manager');
            $manager->register();
        });

        // Whitelist Spotlight's REST API endpoints with the "JWT Auth" plugin
        // https://wordpress.org/plugins/jwt-auth/
        add_filter('jwt_auth_whitelist', function ($whitelist) use ($c) {
            $whitelist[] = '/' . rest_get_url_prefix() . '/' . $c->get('namespace') . '/*';

            return $whitelist;
        });

        // Add the public nonce header to the allowed CORS header list
        add_filter('rest_allowed_cors_headers', function ($headers) use ($c) {
            $headers[] = $c->get('auth/public/nonce_header');

            return $headers;
        });
    }
}
