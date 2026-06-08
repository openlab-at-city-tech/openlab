<?php

namespace RebelCode\Spotlight\Instagram\Notifications;

/**
 * Represents a single notification.
 *
 * @since 0.2
 */
class Notification
{
    /**
     * @since 0.2
     *
     * @var string|int
     */
    protected $id;

    /**
     * @since 0.2
     *
     * @var string
     */
    protected $title;

    /**
     * @since 0.2
     *
     * @var string
     */
    protected $content;

    /**
     * @since 0.2
     *
     * @var int|null
     */
    protected $date;

    /**
     * Constructor.
     *
     * @since 0.2
     *
     * @param int|string $id
     * @param string     $title
     * @param string     $content
     * @param int|null   $date
     */
    public function __construct($id, string $title, string $content, ?int $date)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
    }

    /**
     * @since 0.2
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @since 0.2
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @since 0.2
     *
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * @since 0.2
     *
     * @return int|null
     */
    public function getDate() : ?int
    {
        return $this->date;
    }
}
