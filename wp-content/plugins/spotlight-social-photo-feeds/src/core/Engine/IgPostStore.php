<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine;

use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Exception\StoreException;
use RebelCode\Iris\Store;
use RebelCode\Iris\Store\Query\Condition;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaChild;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaProductType;
use RebelCode\Spotlight\Instagram\Engine\Store\MediaFileStore;
use RebelCode\Spotlight\Instagram\IgApi\IgMedia;
use RebelCode\Spotlight\Instagram\PostTypes\MediaPostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use WP_Post;

class IgPostStore implements Store
{
    /** @var string */
    protected $postType;

    /** @var MediaFileStore */
    protected $files;

    /**
     * Constructor.
     *
     * @param string $postType
     * @param MediaFileStore $thumbnailStore
     */
    public function __construct(string $postType, MediaFileStore $thumbnailStore)
    {
        $this->postType = $postType;
        $this->files = $thumbnailStore;
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

    public function getFileStore(): MediaFileStore
    {
        return $this->files;
    }

    public function regenerateFiles(): void
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $batchNum = 0;
        $batchSize = 100;

        do {
            $items = $this->query(new Store\Query([], null, null, $batchSize, $batchNum * $batchSize));

            if (count($items) === 0) {
                break;
            }

            foreach ($items as $item) {
                $item = $this->files->downloadForItem($item, true);
                $postData = $this->itemToPostData($item);
                wp_update_post($postData, true);
            }

            $batchNum++;
        } while (true);
    }

    public function insert(Item $item): Item
    {
        $item = $this->files->downloadForItem($item);

        $postData = $this->itemToPostData($item);

        if ($item->localId === null) {
            $result = wp_insert_post($postData, true);
        } else {
            $result = wp_update_post($postData, true);
        }

        if (is_wp_error($result)) {
            $message = $result->get_error_message();
            $details = $result->get_error_data();
            if (is_string($details) && !empty($details)) {
                $message .= ' ' . $details;
            }

            throw new StoreException($message, $this);
        }

        $insertedId = (int) $result;

        delete_post_meta($insertedId, MediaPostType::SOURCE);
        foreach ($item->sources as $source) {
            add_post_meta($insertedId, MediaPostType::SOURCE, (string) $source);
        }

        return $item->withLocalId($insertedId);
    }

    public function insertMultiple(array $items, int $mode = self::IGNORE_FAIL): array
    {
        $result = [];
        foreach ($items as $item) {
            set_time_limit(30);
            try {
                $result[] = $this->insert($item);
            } catch (StoreException $ex) {
                if ($mode === Store::THROW_ON_FAIL) {
                    throw $ex;
                } else {
                    error_log("Ignored IgPostStore exception: {$ex->getMessage()}");
                }
            }
        }

        return $result;
    }

    public function get(string $id): ?Item
    {
        $post = get_post($id);

        return ($post instanceof WP_Post && $post->post_type === $this->postType)
            ? $this->postToItem($post)
            : null;
    }

    public function getMultiple(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $posts = get_posts([
            'post_type' => $this->postType,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => MediaPostType::MEDIA_ID,
                    'value' => $ids,
                    'compare' => 'IN',
                ],
            ],
        ]);

        return Arrays::createMap($posts, function (WP_Post $post) {
            $item = $this->postToItem($post);

            return [$item->id, $item];
        });
    }

    public function getForSources(array $sources, ?int $count = null, int $offset = 0): array
    {
        return $this->query(
            new Store\Query($sources, null, null, $count, $offset)
        );
    }

    public function query(Store\Query $query): array
    {
        $args = $this->queryToWpQueryArgs($query);
        $posts = get_posts($args);

        return Arrays::map($posts, function (WP_Post $post) {
            return $this->postToItem($post);
        });
    }

    public function delete(string $id): bool
    {
        return $this->deleteItem($this->get($id));
    }

    public function deleteMultiple(array $ids): int
    {
        $count = 0;
        foreach ($ids as $id) {
            if ($this->delete((string) $id)) {
                $count++;
            }
        }

        return $count;
    }

    public function deleteForSources(array $sources): int
    {
        $items = $this->getForSources($sources);

        $count = 0;
        foreach ($items as $item) {
            if ($this->deleteItem($item)) {
                $count++;
            }
        }

        return $count;
    }

    protected function deleteItem(?Item $item): bool
    {
        if ($item !== null && $item->localId !== null) {
            $result = wp_delete_post($item->localId);

            if (!empty($result)) {
                $this->files->deleteFor($item->data);

                return true;
            }
        }

        return false;
    }

    protected function itemToPostData(Item $item): array
    {
        $data = [
            'post_type' => $this->postType,
            'post_status' => 'publish',
            'post_title' => 'Instagram post #' . $item->id,
            'meta_input' => [
                MediaPostType::MEDIA_ID => $item->id,
                MediaPostType::USERNAME => $item->data[MediaItem::USERNAME] ?? '',
                MediaPostType::TIMESTAMP => $item->data[MediaItem::TIMESTAMP] ?? null,
                MediaPostType::CAPTION => $item->data[MediaItem::CAPTION] ?? '',
                MediaPostType::TYPE => $item->data[MediaItem::MEDIA_TYPE] ?? '',
                MediaPostType::URL => $item->data[MediaItem::MEDIA_URL] ?? '',
                MediaPostType::SIZE => $item->data[MediaItem::MEDIA_SIZE] ?? '',
                MediaPostType::PERMALINK => $item->data[MediaItem::PERMALINK] ?? '',
                MediaPostType::SHORTCODE => $item->data[MediaItem::SHORTCODE] ?? '',
                MediaPostType::VIDEO_TITLE => $item->data[MediaItem::VIDEO_TITLE] ?? '',
                MediaPostType::PRODUCT_TYPE => $item->data[MediaItem::MEDIA_PRODUCT_TYPE] ?? '',
                MediaPostType::THUMBNAIL_URL => $item->data[MediaItem::THUMBNAIL_URL] ?? '',
                MediaPostType::THUMBNAILS => $item->data[MediaItem::THUMBNAILS] ?? [],
                MediaPostType::LIKES_COUNT => $item->data[MediaItem::LIKES_COUNT] ?? 0,
                MediaPostType::COMMENTS_COUNT => $item->data[MediaItem::COMMENTS_COUNT] ?? 0,
                MediaPostType::COMMENTS => $item->data[MediaItem::COMMENTS] ?? [],
                MediaPostType::CHILDREN => $item->data[MediaItem::CHILDREN] ?? [],
                MediaPostType::IS_STORY => $item->data[MediaItem::IS_STORY] ?? false,
                MediaPostType::LAST_REQUESTED => time(),
            ],
        ];

        if (!empty($item->localId)) {
            $data['ID'] = $item->localId;
        }

        return $data;
    }

    public function postToItem(WP_Post $post): Item
    {
        $id = $post->{MediaPostType::MEDIA_ID};

        $sources = get_post_meta($post->ID, MediaPostType::SOURCE, false);
        // If the item has no sources
        if (empty($sources)) {
            // Check the deprecated meta keys
            $sources = [
                new Source(
                    $post->{MediaPostType::SOURCE_NAME},
                    $post->{MediaPostType::SOURCE_TYPE}
                ),
            ];
        } else {
            $sources = Arrays::map($sources, [Source::class, 'fromString']);
        }

        $children = $post->{MediaPostType::CHILDREN};
        $children = is_array($children) ? $children : [];
        $children = Arrays::map($children, function ($child) {
            return ($child instanceof IgMedia)
                ? [
                    MediaChild::MEDIA_ID => $child->id,
                    MediaChild::MEDIA_TYPE => $child->type,
                    MediaChild::PERMALINK => $child->permalink,
                    MediaChild::SHORTCODE => $child->shortcode,
                    MediaChild::MEDIA_URL => $child->url,
                ]
                : (array) $child;
        });

        $thumbnails = $post->{MediaPostType::THUMBNAILS};
        $thumbnails = is_array($thumbnails) ? $thumbnails : [];
        $thumbnails = Arrays::map($thumbnails, function ($url) {
            return (is_ssl() && is_string($url))
                ? preg_replace('#^http://#', 'https://', $url, 1)
                : $url;
        });

        $comments = $post->{MediaPostType::COMMENTS};
        $comments = is_array($comments) ? $comments : [];

        $likesCount = intval($post->{MediaPostType::LIKES_COUNT});
        $commentsCount = intval($post->{MediaPostType::COMMENTS_COUNT});
        $size = is_array($post->{MediaPostType::SIZE})
            ? $post->{MediaPostType::SIZE}
            : null;

        $timestamp = $post->{MediaPostType::TIMESTAMP};
        $timestamp = is_numeric($timestamp)
            ? date(DATE_ISO8601, intval($timestamp))
            : $timestamp;

        $data = [
            MediaItem::MEDIA_ID => $id,
            MediaItem::CAPTION => $post->{MediaPostType::CAPTION},
            MediaItem::USERNAME => $post->{MediaPostType::USERNAME},
            MediaItem::TIMESTAMP => $timestamp,
            MediaItem::MEDIA_TYPE => $post->{MediaPostType::TYPE},
            MediaItem::MEDIA_URL => $post->{MediaPostType::URL},
            MediaItem::MEDIA_PRODUCT_TYPE => $post->{MediaPostType::PRODUCT_TYPE} ?? MediaProductType::FEED,
            MediaItem::MEDIA_SIZE => $size,
            MediaItem::PERMALINK => $post->{MediaPostType::PERMALINK},
            MediaItem::SHORTCODE => $post->{MediaPostType::SHORTCODE} ?? '',
            MediaItem::VIDEO_TITLE => $post->{MediaPostType::VIDEO_TITLE} ?? '',
            MediaItem::THUMBNAIL_URL => $post->{MediaPostType::THUMBNAIL_URL},
            MediaItem::THUMBNAILS => $thumbnails,
            MediaItem::LIKES_COUNT => $likesCount,
            MediaItem::COMMENTS_COUNT => $commentsCount,
            MediaItem::COMMENTS => $comments,
            MediaItem::CHILDREN => $children,
            MediaItem::IS_STORY => boolval($post->{MediaPostType::IS_STORY}),
            MediaItem::LAST_REQUESTED => $post->{MediaPostType::LAST_REQUESTED},
            MediaItem::POST => $post->ID,
        ];

        return new Item($id, $post->ID, $sources, $data);
    }

    protected function queryToWpQueryArgs(Store\Query $query): array
    {
        $queryArgs = [
            'post_type' => $this->postType,
            'posts_per_page' => $query->count ?? ($query->offset > 0 ? PHP_INT_MAX : -1),
            'offset' => $query->offset,
        ];

        $order = $query->order ?? new Store\Query\Order(Store\Query\Order::DESC, MediaPostType::TIMESTAMP);
        $queryArgs['order'] = $order->type;
        $queryArgs['meta_key'] = $order->field;
        $queryArgs['orderby'] = 'meta_value';

        $metaQuery = [];

        if (count($query->sources) > 0) {
            $sourceQuery = Arrays::map($query->sources, function ($source) {
                return [
                    'key' => MediaPostType::SOURCE,
                    'value' => (string) $source,
                    'compare' => '=',
                ];
            });

            $sourceQuery['relation'] = 'OR';
            $metaQuery[] = $sourceQuery;
        }

        if ($query->condition) {
            $metaQuery[] = $this->conditionToMetaQuery($query->condition);
        }

        if (!empty($metaQuery)) {
            $metaQuery['relation'] = 'AND';
            $queryArgs['meta_query'] = $metaQuery;
        }

        return $queryArgs;
    }

    protected function conditionToMetaQuery(Condition $condition): array
    {
        $result = ['relation' => $condition->relation];

        foreach ($condition->criteria as $criterion) {
            if ($criterion instanceof Store\Query\Expression) {
                $result[] = [
                    'key' => $criterion->field,
                    'compare' => $criterion->operator,
                    'value' => $criterion->value,
                ];
            } elseif ($criterion instanceof Condition) {
                $result[] = $this->conditionToMetaQuery($criterion);
            }
        }

        return $result;
    }

    /**
     * Updates the last requested time for a list of items.
     *
     * @param Item[] $items The list of items.
     */
    public static function updateLastRequestedTime(array $items)
    {
        if (count($items) === 0) {
            return;
        }

        global $wpdb;

        $postIds = Arrays::join($items, ',', function (Item $item) {
            return '\'' . $item->localId . '\'';
        });

        $table = $wpdb->prefix . 'postmeta';
        $query = sprintf(
            "UPDATE %s SET meta_value = '%s' WHERE meta_key = '%s' AND post_id IN (%s)",
            $table,
            time(),
            MediaPostType::LAST_REQUESTED,
            $postIds
        );

        $wpdb->query($query);
    }
}
