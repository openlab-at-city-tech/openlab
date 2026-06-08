<?php

namespace RebelCode\Spotlight\Instagram\Engine\Data\Source;

use RebelCode\Iris\Data\Source;

/**
 * Source for posts where a user is tagged.
 *
 * @since 0.5
 */
class TaggedUserSource
{
    const TYPE = 'TAGGED_ACCOUNT';

    /**
     * Creates a source for a tagged user.
     *
     * @since 0.5
     *
     * @param string $username The username of the tagged user.
     *
     * @return Source The created source instance.
     */
    public static function create(string $username) : Source
    {
        return new Source($username, static::TYPE);
    }
}
