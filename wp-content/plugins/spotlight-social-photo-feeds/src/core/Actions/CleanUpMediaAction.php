<?php

namespace RebelCode\Spotlight\Instagram\Actions;

use RebelCode\Iris\Engine;
use RebelCode\Spotlight\Instagram\Config\ConfigSet;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaProductType;
use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\PostTypes\MediaPostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Utils\Functions;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use Throwable;

/**
 * The action that cleans up old media.
 *
 * @since 0.1
 */
class CleanUpMediaAction
{
    /**
     * Config key for the age limit.
     *
     * @since 0.1
     */
    const CFG_AGE_LIMIT = 'cleanerAgeLimit';

    /** @var Engine */
    protected $engine;

    /** @var PostType */
    protected $cpt;

    /** @var ConfigSet */
    protected $config;

    /** Constructor */
    public function __construct(Engine $engine, PostType $cpt, ConfigSet $config)
    {
        $this->engine = $engine;
        $this->cpt = $cpt;
        $this->config = $config;
    }

    /**
     * @since 0.1
     *
     * @param string|null $ageLimit Optional age limit override, to ignore the saved config value.
     *
     * @return int The number of deleted posts.
     */
    public function __invoke(?string $ageLimit = null)
    {
        try {
            set_time_limit(3600);

            $count = 0;

            // Delete media according to the age limit
            {
                $ageLimit = $ageLimit ?? $this->config->get(static::CFG_AGE_LIMIT)->getValue();
                $ageTime = strtotime($ageLimit . ' ago');

                global $wpdb;

                $query = sprintf(
                    "DELETE post, meta
                FROM {$wpdb->posts} post
                JOIN {$wpdb->postmeta} meta on post.ID = meta.post_id
                WHERE post.post_type = '%s' AND post.ID IN (
                    SELECT * FROM (
                        SELECT post.ID
                        FROM {$wpdb->posts} as post
                        JOIN {$wpdb->postmeta} as meta on post.ID = meta.post_id
                        WHERE meta.meta_key = '%s' AND meta.meta_value < %d
                    ) as t
                )",
                    $this->cpt->getSlug(),
                    MediaPostType::LAST_REQUESTED,
                    $ageTime
                );

                $result = $wpdb->query($query);

                if (is_numeric($result)) {
                    $count += intval($result);
                }
            }

            // Delete expired stories
            {
                $storyPosts = $this->cpt->query([
                    'meta_query' => [
                        [
                            'key' => MediaPostType::PRODUCT_TYPE,
                            'compare' => '==',
                            'value' => MediaProductType::STORY,
                        ],
                    ],
                ]);

                $ids = Arrays::map($storyPosts, Functions::property('ID'));
                $storyItems = $this->engine->getStore()->getMultiple($ids);

                foreach ($storyItems as $item) {
                    if (MediaItem::isExpiredStory($item)) {
                        $this->engine->getStore()->delete($item->id);
                        $count++;
                    }
                }
            }

            return $count;
        } catch (Throwable $exception) {
            ErrorLog::exception($exception);
            return 0;
        }
    }
}
