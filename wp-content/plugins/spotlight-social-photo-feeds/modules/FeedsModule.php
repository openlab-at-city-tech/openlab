<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\Value;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Feeds\FeedManager;
use RebelCode\Spotlight\Instagram\Feeds\FeedTemplate;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\PostTypes\FeedPostType;
use RebelCode\Spotlight\Instagram\Wp\MetaField;
use RebelCode\Spotlight\Instagram\Wp\PostType;

/**
 * The module that adds the feeds post type and related functionality to the plugin.
 *
 * @since 0.1
 */
class FeedsModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories() : array
    {
        return [
            // The feeds CPT
            'cpt' => new Constructor(PostType::class, [
                'cpt/slug',
                'cpt/args',
                'cpt/fields',
            ]),
            // The slug name for the feeds CPT
            'cpt/slug' => new Value('sl-insta-feed'),
            // The args for the feeds CPT
            'cpt/args' => new Value([
                'labels' => [
                    'name' => 'Spotlight feeds',
                    'singular_name' => 'Spotlight feed',
                ],
                'public' => false,
                'supports' => ['title', 'custom-fields'],
                'show_in_rest' => false,
            ]),
            // The meta fields for the feeds CPT
            'cpt/fields' => new Value([
                new MetaField(FeedPostType::OPTIONS),
            ]),

            // The template that renders feeds
            'template' => new Constructor(FeedTemplate::class, []),

            // The item feed manager
            'manager' => new Constructor(FeedManager::class, ['cpt', '@accounts/cpt']),
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
        ];
    }
}
