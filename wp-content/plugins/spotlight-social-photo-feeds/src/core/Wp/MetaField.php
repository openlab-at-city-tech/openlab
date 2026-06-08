<?php

namespace RebelCode\Spotlight\Instagram\Wp;

/**
 * Represents configuration for a WordPress post meta field in an immutable struct-like form.
 *
 * @since 0.1
 */
class MetaField
{
    /**
     * The default meta field registration arguments.
     *
     * @since 0.1
     */
    const DEFAULT_ARGS = [
        'single' => true,
        'show_in_rest' => true,
    ];

    /**
     * @since 0.1
     *
     * @var string
     */
    protected $key;

    /**
     * @since 0.1
     *
     * @var array
     */
    protected $args;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string $key  The key of the meta field.
     * @param array  $args Optional addition arguments. See {@link register_meta()}.
     */
    public function __construct(string $key, array $args = self::DEFAULT_ARGS)
    {
        $this->key = $key;
        $this->args = $args;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * @since 0.1
     *
     * @return array
     */
    public function getArgs() : array
    {
        return $this->args;
    }

    /**
     * Registers a meta field for a specific WordPress post type.
     *
     * @since 0.1
     *
     * @param MetaField $field    The meta field to register.
     * @param string    $postType The slug of the post type to register for.
     */
    public static function registerFor(MetaField $field, string $postType)
    {
        register_post_meta($postType, $field->key, $field->args);
    }
}
