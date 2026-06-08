<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

use DateTime;

/**
 * Represents an Instagram media comment as retrieved from the Graph API.
 *
 * @since 0.1
 */
class IgComment
{
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
     * @var string
     */
    public $text;

    /**
     * @since 0.1
     *
     * @var DateTime|null
     */
    public $timestamp;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $likeCount;

    /**
     * Creates an instance using data from the Graph API.
     *
     * @since 0.1
     *
     * @param array $data An associative array containing the comment data from the API.
     *
     * @return static The created instance.
     */
    public static function create(array $data)
    {
        $data = array_merge(static::getDefaults(), $data);

        $timestamp = empty($data['timestamp'])
            ? null
            : DateTime::createFromFormat(DATE_ISO8601, $data['timestamp']);

        $comment = new static();
        $comment->id = $data['id'];
        $comment->username = $data['username'];
        $comment->text = $data['text'];
        $comment->timestamp = $timestamp;
        $comment->likeCount = $data['like_count'];

        return $comment;
    }

    /**
     * Retrieves the default values for a comment's data.
     *
     * @since 0.1
     *
     * @return array An associative array containing the default values for each comment property.
     */
    public static function getDefaults() : array
    {
        return [
            'id' => '',
            'username' => '',
            'text' => '',
            'timestamp' => '',
            'like_count' => 0,
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
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
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
     * @return int
     */
    public function getLikeCount() : int
    {
        return $this->likeCount;
    }
}
