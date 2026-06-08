<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

use DateTime;

/**
 * Represents an Instagram media object as retrieved from the Graph API.
 *
 * @since 0.1
 */
class IgMedia
{
    /**
     * The maximum lifetime for story media, in seconds.
     *
     * @since 0.6
     */
    const STORY_MAX_LIFE = 86400; // = 24hrs

    /**
     * @since 0.1
     *
     * @var string
     */
    public $id;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $username;

    /**
     * @since 0.1
     *
     * @var DateTime|null
     */
    public $timestamp;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $caption;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $type;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $url;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $permalink;

    /**
     * @since 0.7
     *
     * @var string
     */
    public $shortcode;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $thumbnail;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $likesCount;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $commentsCount;

    /**
     * @since 0.1
     *
     * @var IgComment[]
     */
    public $comments;

    /**
     * @since 0.1
     *
     * @var array
     */
    public $children;

    /**
     * Creates a media object from the given Instagram API data.
     *
     * @since 0.1
     *
     * @param array $data The media data.
     *
     * @return static The created media object.
     */
    public static function create(array $data)
    {
        $data = array_merge(static::getDefaults(), $data);

        $timestamp = empty($data['timestamp'])
            ? null
            : DateTime::createFromFormat(DATE_ISO8601, $data['timestamp']);

        $children = isset($data['children']['data'])
            ? $data['children']['data']
            : $data['children'];

        foreach ($children as $idx => $child) {
            if (is_array($child)) {
                $children[$idx] = static::create($child);
            }
        }

        $comments = isset($data['comments']['data'])
            ? $data['comments']['data']
            : $data['comments'];

        foreach ($comments as $idx => $comment) {
            if (is_array($comment)) {
                $comments[$idx] = IgComment::create($comment);
            }
        }

        $media = new static();
        $media->id = $data['id'];
        $media->username = $data['username'];
        $media->timestamp = $timestamp;
        $media->caption = $data['caption'];
        $media->type = $data['media_type'] ?? $data['type'];
        $media->url = $data['media_url'] ?? $data['url'];
        $media->permalink = $data['permalink'];
        $media->shortcode = $data['shortcode'];
        $media->thumbnail = $data['thumbnail_url'];
        $media->likesCount = $data['like_count'] ?? $data['likesCount'];
        $media->commentsCount = $data['comments_count'] ?? $data['commentsCount'];
        $media->comments = $comments;
        $media->children = $children;

        return $media;
    }

    /**
     * Retrieves the default creation data.
     *
     * @since 0.1
     *
     * @return array
     */
    public static function getDefaults()
    {
        return [
            'id' => '',
            'username' => '',
            'timestamp' => '',
            'caption' => '',
            'media_type' => '',
            'media_url' => '',
            'permalink' => '',
            'shortcode' => '',
            'thumbnail_url' => '',
            'like_count' => 0,
            'comments_count' => 0,
            'comments' => [],
            'children' => [],
        ];
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @since 0.1
     *
     * @return DateTime|null
     */
    public function getTimestamp() : ?DateTime
    {
        return $this->timestamp;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getCaption() : string
    {
        return $this->caption;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getPermalink() : string
    {
        return $this->permalink;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getThumbnailUrl() : string
    {
        return $this->thumbnail;
    }

    /**
     * @since 0.1
     *
     * @return int
     */
    public function getLikesCount() : int
    {
        return $this->likesCount;
    }

    /**
     * @since 0.1
     *
     * @return int
     */
    public function getCommentsCount() : int
    {
        return $this->commentsCount;
    }

    /**
     * @since 0.1
     *
     * @return array
     */
    public function getComments() : array
    {
        return $this->comments;
    }

    /**
     * @since 0.1
     *
     * @return IgMedia[]
     */
    public function getChildren() : array
    {
        return $this->children;
    }

    /**
     * Creates a copy with the given list of comments.
     *
     * @since 0.1
     *
     * @param IgComment[] $comments The comments.
     *
     * @return IgMedia The copied instance with the comments.
     */
    public function withComments(array $comments)
    {
        $instance = clone $this;
        $instance->comments = $comments;

        return $instance;
    }
}
