<?php

namespace RebelCode\Spotlight\Instagram\Engine\Data\Source;

use RebelCode\Iris\Data\Source;

/**
 * Source for story posts from a user.
 */
class StorySource
{
    const TYPE = 'USER_STORY';

    /**
     * Creates a source for a user's story.
     *
     * @param string $username The user name.
     *
     * @return Source The created source instance.
     */
    public static function create(string $username): Source
    {
        return new Source($username, static::TYPE);
    }
}
