<?php

namespace RebelCode\Spotlight\Instagram\Feeds;

/**
 * Represents a feed saved in the database.
 *
 * @since 0.1
 */
class Feed
{
    /**
     * @since 0.1
     *
     * @var int|null
     */
    protected $id;

    /**
     * @since 0.1
     *
     * @var string
     */
    protected $name;

    /**
     * @since 0.1
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param int|null $id      The ID of the feed in the database.
     * @param string   $name    The user-given name for the feed.
     * @param array    $options The options for the feed, which control rendering.
     */
    public function __construct(?int $id, string $name = '', array $options = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->options = $options;
    }

    /**
     * Retrieves the ID of the feed.
     *
     * @since 0.1
     *
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Retrieves the name of the feed.
     *
     * @since 0.1
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Retrieves the options for this feed.
     *
     * @since 0.1
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * Retrieves a single option, optionally defaulting to a specific value.
     *
     * @since 0.1
     *
     * @param string $key     The key of the option to retrieve.
     * @param mixed  $default Optional value to return if no option is found for the given $key.
     *
     * @return mixed|null The value for the option that corresponds to the given $key, or $default if not found.
     */
    public function getOption(string $key, $default = null)
    {
        return array_key_exists($key, $this->options)
            ? $this->options[$key]
            : $default;
    }

    /**
     * Creates a feed instance from an array.
     *
     * @since 0.1
     *
     * @param array $data The array from which to create the feed.
     *
     * @return Feed The created feed instance.
     */
    public static function fromArray(array $data)
    {
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? '';
        $options = $data['options'] ?? [];

        return new Feed($id, $name, $options);
    }
}
