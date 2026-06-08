<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Wp\MetaField;
use RebelCode\Spotlight\Instagram\PostTypes\MediaPostType;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Engine\IgPostStore;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Actions\DeleteAllPostsAction;
use RebelCode\Iris\Data\Source;
use Psr\Container\ContainerInterface;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factories\FuncService;
use Dhii\Services\Factories\Constructor;

/**
 * The module that adds the media post type and all related functionality to the plugin.
 *
 * @since 0.1
 */
class MediaModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories() : array
    {
        return [
            //==========================================================================
            // POST TYPE
            //==========================================================================

            // The media CPT
            'cpt' => new Constructor(MediaPostType::class, [
                'cpt/slug',
                'cpt/args',
                'cpt/fields',
                '@engine/store/files',
            ]),

            // The media CPT slug name
            'cpt/slug' => new Value('sl-insta-media'),
            // The media CPT registration args
            'cpt/args' => new Value([
                'labels' => [
                    'name' => 'Spotlight posts',
                    'singular_name' => 'Spotlight post',
                ],
                'public' => false,
                'supports' => ['title', 'custom-fields'],
                'show_in_rest' => false,
            ]),
            // The meta fields for the media CPT
            'cpt/fields' => new Value([
                new MetaField(MediaPostType::MEDIA_ID),
                new MetaField(MediaPostType::USERNAME),
                new MetaField(MediaPostType::TIMESTAMP),
                new MetaField(MediaPostType::CAPTION),
                new MetaField(MediaPostType::TYPE),
                new MetaField(MediaPostType::URL),
                new MetaField(MediaPostType::PERMALINK),
                new MetaField(MediaPostType::THUMBNAIL_URL),
                new MetaField(MediaPostType::LIKES_COUNT),
                new MetaField(MediaPostType::COMMENTS_COUNT),
                new MetaField(MediaPostType::CHILDREN),
                new MetaField(MediaPostType::LAST_REQUESTED),
            ]),

            //==========================================================================
            // ACTIONS
            //==========================================================================

            'actions/delete_all' => new Constructor(DeleteAllPostsAction::class, [
                'cpt/slug',
                '@engine/store/files',
                '@engine/importer/scheduler/cron/hook',
                '@engine/importer/lock',
                '@engine/importer/interrupt',
            ]),

            //==========================================================================
            // MIGRATIONS
            //==========================================================================

            'migrations/0.4.1/generate_thumbnails' => new FuncService(
                ['@media/cpt', '@engine/store'],
                function ($v1, $v2, PostType $mediaCpt, IgPostStore $store) {
                    if (version_compare($v1 ?? '0.0', '0.4.1', '<')) {
                        foreach ($mediaCpt->query() as $post) {
                            // Extend the time limit by 10 seconds
                            set_time_limit(10);

                            // Convert from post to item and generate the thumbnails
                            $item = $store->postToItem($post);
                            $item = $store->getFileStore()->downloadForItem($item);

                            // Update the item
                            $store->insert($item);
                        }
                    }
                }
            ),

            'migrations/0.9/update_sources_meta' => new FuncService(['@wp/db'], function ($oldVer, $newVer, $wpdb) {
                if (version_compare($oldVer ?? '0.0', '0.9', '<')) {
                    $sourceNameQuery = sprintf(
                        "SELECT post_id, meta_value FROM %s WHERE meta_key = '%s'",
                        $wpdb->postmeta,
                        MediaPostType::SOURCE_NAME
                    );

                    $sourceTypeQuery = sprintf(
                        "SELECT post_id, meta_value FROM %s WHERE meta_key = '%s'",
                        $wpdb->postmeta,
                        MediaPostType::SOURCE_TYPE
                    );

                    $sourceQuery = sprintf(
                        'SELECT st.post_id, sn.meta_value as name, st.meta_value as type
                        FROM (%s) as sn
                        JOIN (%s) as st
                        ON sn.post_id = st.post_id',
                        $sourceNameQuery,
                        $sourceTypeQuery
                    );

                    $rows = $wpdb->get_results($sourceQuery);

                    if (is_array($rows)) {
                        foreach ($rows as $row) {
                            $source = new Source($row->name, $row->type);
                            delete_post_meta($row->post_id, MediaPostType::SOURCE_NAME);
                            delete_post_meta($row->post_id, MediaPostType::SOURCE_TYPE);
                            add_post_meta($row->post_id, MediaPostType::SOURCE, (string) $source);
                        }
                    }
                }
            }),
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
                'migrations/0.4.1/generate_thumbnails',
                'migrations/0.9/update_sources_meta',
            ]),
        ];
    }
}
