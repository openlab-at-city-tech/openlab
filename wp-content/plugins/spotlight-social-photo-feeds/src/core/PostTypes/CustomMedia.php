<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\PostTypes;

use RebelCode\Iris\Data\Item;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaProductType;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaType;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\UserSource;
use RebelCode\Spotlight\Instagram\Utils\Arrays;

class CustomMedia
{
    const ID = 'id';
    const TYPE = 'type';
    const TIMESTAMP = 'timestamp';
    const URL = 'url';
    const PERMALINK = 'permalink';
    const CAPTION = 'caption';
    const SIZE = 'size';
    const CHILDREN = 'children';
    // Child keys
    const CHILD_ID = self::ID;
    const CHILD_TYPE = self::TYPE;
    const CHILD_URL = self::URL;

    /**
     * Gets the custom media for an account.
     *
     * @param int $accountId The account ID.
     *
     * @return Item[] A list of custom media items.
     */
    public static function getForAccount(int $accountId): array
    {
        $username = get_post_meta($accountId, AccountPostType::USERNAME, true);
        $customMedia = get_post_meta($accountId, AccountPostType::CUSTOM_MEDIA, false);

        return Arrays::map($customMedia, function (array $data) use ($username) {
            return static::metaDataToItem($username, $data);
        });
    }

    public static function addCustomMedia(int $accountId, array $data)
    {
        return add_post_meta($accountId, AccountPostType::CUSTOM_MEDIA, static::createMetaData($data));
    }

    public static function updateCustomMedia(int $accountId, string $postId, array $data)
    {
        $metaId = static::getMetaId($accountId, $postId);

        if ($metaId === null) {
            return false;
        }

        /** @noinspection PhpParamsInspection */
        return update_metadata_by_mid('post', $metaId, static::createMetaData($data), AccountPostType::CUSTOM_MEDIA);
    }

    public static function deleteCustomMedia(int $accountId, string $postId)
    {
        $metaId = static::getMetaId($accountId, $postId);

        if ($metaId === null) {
            return false;
        }

        return delete_metadata_by_mid('post', $metaId);
    }

    public static function getMetaId(int $accountId, string $customPostId)
    {
        global $wpdb;
        $rows = $wpdb->get_results(
            sprintf(
                'SELECT * FROM %s WHERE post_id = %d AND meta_key = "%s"',
                $wpdb->postmeta,
                $accountId,
                AccountPostType::CUSTOM_MEDIA
            ),
            'ARRAY_A'
        );

        $rows = Arrays::map($rows, function (array $row) {
            $row['meta_value'] = unserialize($row['meta_value']);
            return $row;
        });

        $existing = Arrays::find($rows, function ($row) use ($customPostId) {
            return ($row['meta_value'][static::ID] ?? "") === $customPostId;
        });

        return ($existing === null) ? null : (int) $existing['meta_id'];
    }

    public static function createMetaData(array $data): array
    {
        $time = time();

        return [
            static::ID => empty($data['id']) ? ("CUSTOM-" . $time) : $data['id'],
            static::TYPE => $data['type'] ?? MediaType::IMAGE,
            static::URL => $data['url'] ?? '',
            static::CAPTION => $data['caption'] ?? '',
            static::SIZE => $data['size'] ?? '',
            static::PERMALINK => $data['permalink'] ?? '',
            static::TIMESTAMP => $data['timestamp'] ?? date(DATE_ISO8601, $time),
            static::CHILDREN => Arrays::map($data['children'] ?? [], function ($child) use ($time) {
                $child[static::CHILD_ID] = empty($child['id']) ? ("CHILD-" . $time) : $child['id'];
                $child[static::CHILD_TYPE] = $child['type'] ?? MediaType::IMAGE;
                $child[static::CHILD_URL] = $child['url'] ?? '';

                return $child;
            }),
        ];
    }

    public static function metaDataToItem(string $username, array $data): Item
    {
        $source = UserSource::create($username, UserSource::TYPE_BUSINESS);

        return new Item($data['id'], $data['id'], [$source], [
            MediaItem::USERNAME => $username,
            MediaItem::MEDIA_ID => $data[static::ID],
            MediaItem::MEDIA_TYPE => $data[static::TYPE],
            MediaItem::MEDIA_URL => $data[static::URL],
            MediaItem::THUMBNAIL_URL => $data[static::URL],
            MediaItem::PERMALINK => $data[static::PERMALINK],
            MediaItem::CAPTION => $data[static::CAPTION],
            MediaItem::MEDIA_SIZE => $data[static::SIZE],
            MediaItem::TIMESTAMP => $data[static::TIMESTAMP],
            MediaItem::LIKES_COUNT => 0,
            MediaItem::COMMENTS_COUNT => 0,
            MediaItem::COMMENTS => [],
            MediaItem::SHORTCODE => '',
            MediaItem::THUMBNAILS => [],
            MediaItem::VIDEO_TITLE => '',
            MediaItem::IS_STORY => false,
            MediaItem::MEDIA_PRODUCT_TYPE => MediaProductType::FEED,
            MediaItem::CHILDREN => Arrays::map($data[static::CHILDREN], function ($child) {
                return [
                    MediaItem::CHILD_ID => $child[static::CHILD_ID],
                    MediaItem::MEDIA_TYPE => $child[static::CHILD_TYPE],
                    MediaItem::MEDIA_URL => $child[static::CHILD_URL],
                    MediaItem::THUMBNAIL_URL => $child[static::CHILD_URL],
                    MediaItem::MEDIA_SIZE => $child[static::SIZE],
                ];
            }),
        ]);
    }
}
