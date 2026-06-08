<?php

namespace RebelCode\Spotlight\Instagram\MediaStore;

use DateTime;
use RebelCode\Spotlight\Instagram\Engine\Store\MediaFileStore;
use RebelCode\Spotlight\Instagram\IgApi\IgMedia;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Utils\Functions;
use WP_Post;

/** @deprecated */
class IgCachedMedia extends IgMedia
{
    /**
     * @since 0.1
     *
     * @var WP_Post|null
     */
    public $post;

    /**
     * @since 0.4.1
     *
     * @var string[]
     */
    public $thumbnails;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $lastRequested;

    /**
     * @since 0.1
     *
     * @deprecated Use {@link sources} instead.
     *
     * @var MediaSource
     */
    public $source;

    /** @var MediaSource[] */
    public $sources;

    /**
     * @inheritDoc
     *
     * @since 0.1
     *
     * @return IgCachedMedia
     */
    public static function create(array $data)
    {
        $instance = parent::create($data);

        $instance->post = $data['post'] ?? null;
        $instance->lastRequested = empty($data['last_requested']) ? time() : $data['last_requested'];
        $instance->source = MediaSource::create($data['source'] ?? []);
        $instance->thumbnails = $data['thumbnails'] ?? [];

        return $instance;
    }

    /**
     * Creates an instance from a non-cached instance.
     *
     * @since 0.1
     *
     * @param IgMedia $media
     * @param array   $extra
     *
     * @return IgCachedMedia
     */
    public static function from(IgMedia $media, array $extra = []) : IgCachedMedia
    {
        if ($media instanceof static) {
            return $media;
        }

        $post = $extra['post'] ?? null;
        $lastRequested = empty($extra['last_requested']) ? time() : $extra['last_requested'];
        $source = MediaSource::create($extra['source'] ?? []);

        /** @var MediaFileStore $fileStore */
        $fileStore = spotlightInsta()->get('engine/store/files');
        $thumbnailFiles = $fileStore->getThumbnailsFor($media->id);
        $thumbnailUrls = Arrays::map($thumbnailFiles, Functions::property('url'));

        return static::create([
            'post' => $post,
            'id' => $media->id,
            'username' => $media->username,
            'timestamp' => $media->timestamp ? $media->timestamp->format(DateTime::ISO8601) : null,
            'caption' => $media->caption,
            'media_type' => $media->type,
            'media_url' => $media->url,
            'permalink' => $media->permalink,
            'thumbnail_url' => $media->thumbnail,
            'thumbnails' => $thumbnailUrls,
            'like_count' => $media->likesCount,
            'comments_count' => $media->commentsCount,
            'comments' => $media->comments,
            'children' => $media->children,
            'last_requested' => $lastRequested,
            'source' => $source,
        ]);
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public static function getDefaults()
    {
        $defaults = parent::getDefaults();
        $defaults['last_requested'] = '';
        $defaults['source'] = MediaSource::create([]);

        return $defaults;
    }

    /**
     * @since 0.1
     *
     * @return WP_Post|null
     */
    public function getPost() : ?WP_Post
    {
        return $this->post;
    }

    /**
     * @since 0.1
     *
     * @return int
     */
    public function getLastRequested() : int
    {
        return $this->lastRequested;
    }

    /**
     * @since 0.1
     *
     * @return MediaSource
     */
    public function getSource() : MediaSource
    {
        return $this->source;
    }

    /**
     * Gets a {@link DateTime} instance that represents the current date and time.
     *
     * @since 0.1
     *
     * @return DateTime
     */
    public static function now()
    {
        return DateTime::createFromFormat(DateTime::ISO8601, date(DateTime::ISO8601));
    }
}
