<?php

namespace RebelCode\Spotlight\Instagram\Engine\Data\Item;

use DateTime;
use Exception;
use RebelCode\Iris\Data\Item;
use RebelCode\Spotlight\Instagram\IgApi\IgMedia;

class MediaItem
{
    // -----------------------
    // INSTAGRAM API FIELDS
    // -----------------------
    const MEDIA_ID = 'media_id';
    const CAPTION = 'caption';
    const USERNAME = 'username';
    const TIMESTAMP = 'timestamp';
    const MEDIA_TYPE = 'media_type';
    const MEDIA_URL = 'media_url';
    const MEDIA_PRODUCT_TYPE = 'media_product_type';
    const PERMALINK = 'permalink';
    const SHORTCODE = 'shortcode';
    const VIDEO_TITLE = 'video_title';
    const THUMBNAIL_URL = 'thumbnail_url';
    const LIKES_COUNT = 'like_count';
    const COMMENTS_COUNT = 'comments_count';
    const COMMENTS = 'comments';
    const CHILDREN = 'children';
    // -----------------------
    // CHILD MEDIA FIELDS
    // -----------------------
    const CHILD_ID = 'id';
    // -----------------------
    // CUSTOM FIELDS
    // -----------------------
    const POST = 'post';
    const IS_STORY = 'is_story';
    const LAST_REQUESTED = 'last_requested';
    const THUMBNAILS = 'thumbnails';
    const MEDIA_SIZE = 'media_size';
    const SOURCE_TYPE = 'source_type';
    const SOURCE_NAME = 'source_name';

    /** Checks if an item is a story post, regardless of whether or not it's expired. */
    public static function isStory(Item $item): bool
    {
        return $item->get(static::IS_STORY, false) ||
               $item->get(static::MEDIA_PRODUCT_TYPE) === MediaProductType::STORY;
    }

    /** Checks if an item is a valid (non-expired) story. */
    public static function isValidStory(Item $item): bool
    {
        return static::isStory($item) && static::isTimestampValidForStory($item);
    }

    /** Checks if an item is an expired story. */
    public static function isExpiredStory(Item $item): bool
    {
        return static::isStory($item) && !static::isTimestampValidForStory($item);
    }

    /** Checks if an item's timestamp is valid for a story. Does not check if the item actually IS a story. */
    protected static function isTimestampValidForStory(Item $item): bool
    {
        try {
            $datetime = new DateTime($item->get(static::TIMESTAMP));
            $diff = time() - $datetime->getTimestamp();

            return $diff < IgMedia::STORY_MAX_LIFE;
        } catch (Exception $exception) {
            return false;
        }
    }
}
