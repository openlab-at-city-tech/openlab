<?php

namespace RebelCode\Spotlight\Instagram\Engine\Data\Feed;

use RebelCode\Iris\Data\Feed;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\UserSource;

class StoryFeed
{
    const MEDIA_TYPE = 'stories';

    /**
     * Creates a story-only variant of a given feed.
     *
     * @param Feed $feed The feed.
     *
     * @return Feed|null The story feed, or null if the given feed does not have a business account source.
     */
    public static function createFromFeed(Feed $feed): ?Feed
    {
        if (empty($feed->sources)) {
            return null;
        }

        foreach ($feed->sources as $source) {
            if ($source->type === UserSource::TYPE_BUSINESS) {
                return $feed->with('mediaType', static::MEDIA_TYPE);
            }
        }

        return null;
    }
}
