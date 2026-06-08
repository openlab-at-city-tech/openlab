<?php

namespace RebelCode\Spotlight\Instagram\PostTypes;

use RebelCode\Spotlight\Instagram\Engine\Store\MediaFileStore;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Post;

/**
 * The post type for media.
 *
 * This class extends the {@link PostType} class only as a formality. The primary purpose of this class is to house
 * the meta key constants and functionality for dealing with posts of the media custom post type.
 *
 * @since 0.1
 */
class MediaPostType extends PostType
{
    const MEDIA_ID = '_sli_media_id';
    const USERNAME = '_sli_media_username';
    const TIMESTAMP = '_sli_timestamp';
    const CAPTION = '_sli_caption';
    const TYPE = '_sli_media_type';
    const URL = '_sli_media_url';
    const PRODUCT_TYPE = '_sli_product_type';
    const PERMALINK = '_sli_permalink';
    const SHORTCODE = '_sli_shortcode';
    const VIDEO_TITLE = '_sli_video_title';
    const THUMBNAIL_URL = '_sli_thumbnail_url';
    const THUMBNAILS = '_sli_thumbnails';
    const SIZE = '_sli_media_size';
    const LIKES_COUNT = '_sli_likes_count';
    const COMMENTS_COUNT = '_sli_comments_count';
    const COMMENTS = '_sli_comments';
    const CHILDREN = '_sli_children';
    const LAST_REQUESTED = '_sli_last_requested';
    const IS_STORY = '_sli_is_story';
    const SOURCE = '_sli_source';
    /** @deprecated Use {@link SOURCE} instead. */
    const SOURCE_NAME = '_sli_source_name';
    /** @deprecated Use {@link SOURCE} instead. */
    const SOURCE_TYPE = '_sli_source_type';

    /** @var MediaFileStore */
    protected $fileStore;

    /** Constructor */
    public function __construct(string $slug, array $args, array $fields, MediaFileStore $fileStore)
    {
        parent::__construct($slug, $args, $fields);
        $this->fileStore = $fileStore;
    }

    /** @inheritDoc */
    public function deleteAll()
    {
        $result = parent::deleteAll();

        if ($result !== false) {
            $this->fileStore->deleteAll();
        }

        return $result;
    }

    /**
     * Finds a media post with a specific Instagram ID.
     *
     * @param PostType $cpt The post type instance.
     * @param string   $id  The Instagram ID.
     *
     * @return WP_Post|null The found media post with the given Instagram ID, or null if no matching post was found.
     */
    public static function getByInstagramId(PostType $cpt, string $id): ?WP_Post
    {
        $posts = $cpt->query([
            'meta_query' => [
                [
                    'key' => static::MEDIA_ID,
                    'value' => $id,
                ],
            ],
        ]);

        return count($posts) > 0
            ? $posts[0]
            : null;
    }
}
