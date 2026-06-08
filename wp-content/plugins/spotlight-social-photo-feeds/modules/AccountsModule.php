<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\FuncService;
use Dhii\Services\Factories\Value;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\Wp\MetaField;
use RebelCode\Spotlight\Instagram\Wp\PostType;

/**
 * The module that adds the accounts post type to the plugin.
 *
 * @since 0.1
 */
class AccountsModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories() : array
    {
        return [
            // The accounts CPT
            'cpt' => new Constructor(AccountPostType::class, [
                'cpt/slug',
                'cpt/args',
                'cpt/fields',
                '@engine/store'
            ]),

            // The accounts CPT slug name
            'cpt/slug' => new Value('sl-insta-account'),
            // The accounts CPT registration args
            'cpt/args' => new Value([
                'labels' => [
                    'name' => 'Spotlight accounts',
                    'singular_name' => 'Spotlight account',
                ],
                'public' => false,
                'supports' => ['title', 'custom-fields'],
                'show_in_rest' => false,
            ]),
            // The meta fields for the accounts CPT
            'cpt/fields' => new Value([
                new MetaField(AccountPostType::USER_ID),
                new MetaField(AccountPostType::USERNAME),
                new MetaField(AccountPostType::TYPE),
                new MetaField(AccountPostType::MEDIA_COUNT),
                new MetaField(AccountPostType::BIO),
                new MetaField(AccountPostType::CUSTOM_BIO),
                new MetaField(AccountPostType::PROFILE_PIC_URL),
                new MetaField(AccountPostType::CUSTOM_PROFILE_PIC),
                new MetaField(AccountPostType::ACCESS_TOKEN),
                new MetaField(AccountPostType::ACCESS_IV),
                new MetaField(AccountPostType::ACCESS_EXPIRY),
            ]),

            //==========================================================================
            // MIGRATIONS
            //==========================================================================

            // This migration procedure encrypts all access tokens in the database
            'migrations/0.9.6/encrypt_access_tokens' => new FuncService(
                ['cpt'],
                function ($oldVer, $newVer, PostType $cpt) {
                    if (!version_compare($oldVer, '0.9.6', '<') || !extension_loaded('openssl')) {
                        return;
                    }

                    foreach ($cpt->query() as $post) {
                        // Extend the time limit by 10 seconds
                        set_time_limit(10);

                        $token = get_post_meta($post->ID, AccountPostType::ACCESS_TOKEN, true);
                        $iv = get_post_meta($post->ID, AccountPostType::ACCESS_IV, true);

                        // Only encrypt if the account has no IV (i.e. not already encrypted
                        if (empty($iv)) {
                            $iv = AccountPostType::generateEncryptionIv();
                            $token = AccountPostType::encryptAccessToken($token, $iv);

                            update_post_meta($post->ID, AccountPostType::ACCESS_TOKEN, $token);
                            update_post_meta($post->ID, AccountPostType::ACCESS_IV, $iv);
                        }
                    }
                }
            ),
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
            // Add the post type to WordPress
            'wp/post_types' => new ArrayExtension(['cpt']),
            // Add the migrations
            'migrator/migrations' => new ArrayExtension([
                'migrations/0.9.6/encrypt_access_tokens',
            ]),
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function run(ContainerInterface $c): void
    {
    }
}
