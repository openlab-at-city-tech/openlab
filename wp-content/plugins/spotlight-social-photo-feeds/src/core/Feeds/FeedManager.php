<?php

namespace RebelCode\Spotlight\Instagram\Feeds;

use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Data\Source;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\HashtagSource;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\TaggedUserSource;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\UserSource;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\PostTypes\FeedPostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Post;

/**
 * A manager for feeds (not WordPress Posts, but {@link Feed} instances).
 */
class FeedManager
{
    /**
     * @var PostType
     */
    public $feeds;

    /**
     * @var PostType
     */
    public $accounts;

    /**
     * Constructor.
     *
     * @param PostType $feeds
     * @param PostType $accounts
     */
    public function __construct(PostType $feeds, PostType $accounts)
    {
        $this->feeds = $feeds;
        $this->accounts = $accounts;
    }

    /**
     * Gets the item feed for a post by ID.
     *
     * @param string|int $id The ID.
     *
     * @return Feed|null The item feed, or null if the ID does not correspond to a post.
     */
    public function get($id): ?Feed
    {
        return $this->wpPostToFeed($this->feeds->get($id));
    }

    /**
     * Queries the feeds.
     *
     * @param array $query The WP_Query args.
     * @param int|null $num The number of feeds to retrieve.
     * @param int $page The result page number.
     *
     * @return Feed[] A list of feeds.
     */
    public function query($query = [], $num = null, $page = 1): array
    {
        return Arrays::map($this->feeds->query($query, $num, $page), [$this, 'wpPostToFeed']);
    }

    /**
     * Retrieves the sources to use for a given set of feed options.
     *
     * @param array $options The feed options.
     *
     * @return Source[] A list of item sources.
     */
    public function getSources(array $options): array
    {
        $sources = [];

        foreach ($options['accounts'] ?? [] as $id) {
            $post = $this->accounts->get($id);

            if ($post !== null) {
                $sources[] = UserSource::create($post->{AccountPostType::USERNAME}, $post->{AccountPostType::TYPE});
            }
        }

        foreach ($options['tagged'] ?? [] as $id) {
            $post = $this->accounts->get($id);

            if ($post !== null) {
                $sources[] = TaggedUserSource::create($post->{AccountPostType::USERNAME});
            }
        }

        foreach ($options['hashtags'] ?? [] as $hashtag) {
            $tag = $hashtag['tag'] ?? '';

            if (!empty($tag)) {
                $sources[] = HashtagSource::create($tag, $hashtag['sort'] ?? HashtagSource::TYPE_POPULAR);
            }
        }

        return $sources;
    }

    /**
     * Creates an {@link Feed} from a set of feed options.
     *
     * @param array $options The feed options.
     *
     * @return Feed The created feed.
     */
    public function createFeed(array $options): Feed
    {
        return new Feed(null, $this->getSources($options), $options);
    }

    /**
     * Converts a WordPress post into an {@link Feed}.
     *
     * @param WP_Post|null $post The WordPress post.
     *
     * @return Feed|null The created feed or null if the post is null.
     */
    public function wpPostToFeed(?WP_Post $post): ?Feed
    {
        if ($post === null) {
            return null;
        } else {
            return $this->createFeed($post->{FeedPostType::OPTIONS});
        }
    }
}
