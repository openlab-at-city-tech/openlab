<?php

namespace RebelCode\Spotlight\Instagram\Engine\Data\Source;

use RebelCode\Iris\Data\Source;

/**
 * Source for posts from a hashtag.
 */
class HashtagSource
{
    const TYPE_RECENT = 'RECENT_HASHTAG';
    const TYPE_POPULAR = 'POPULAR_HASHTAG';

    /**
     * Creates a source for a hashtag.
     *
     * @param string $tag  The hashtag.
     * @param string $type The hashtag media type.
     *
     * @return Source The created source instance.
     */
    public static function create(string $tag, string $type) : Source
    {
        $srcType = stripos($type, 'recent') === false
            ? static::TYPE_POPULAR
            : static::TYPE_RECENT;

        return new Source($tag, $srcType);
    }

    /**
     * Checks if a given source type is a hashtag source type.
     *
     * @param string $type The type to check.
     *
     * @return bool True if the argument is a hashtag source type, false if not.
     */
    public static function isHashtagSource(string $type): bool {
        return $type === static::TYPE_RECENT || $type === static::TYPE_POPULAR;
    }
}
