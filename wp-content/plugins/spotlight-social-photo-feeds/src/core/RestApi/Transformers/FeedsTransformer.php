<?php

namespace RebelCode\Spotlight\Instagram\RestApi\Transformers;

use Dhii\Transformer\TransformerInterface;
use RebelCode\Spotlight\Instagram\Feeds\Feed;
use RebelCode\Spotlight\Instagram\PostTypes\FeedPostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use WP_Post;
use wpdb;

/**
 * Transforms {@link Feed} instances, or {@link WP_Post} instances that represents feeds, into REST API response format.
 *
 * @since 0.1
 */
class FeedsTransformer implements TransformerInterface
{
    /**
     * @since 0.1
     *
     * @var wpdb
     */
    protected $wpdb;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param wpdb $wpdb The WordPress database driver.
     */
    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function transform($source)
    {
        if ($source instanceof Feed) {
            $feed = $source;
        } elseif ($source instanceof WP_Post) {
            $feed = FeedPostType::fromWpPost($source);
        } else {
            return $source;
        }

        if (\apply_filters('sl-insta/rest_api/skip_feed_usages', false, $feed) === false) {
            $shortcodeUsages = FeedPostType::getShortcodeUsages($feed, $this->wpdb);
            $wpBlockUsages = FeedPostType::getWpBlockUsages($feed, $this->wpdb);
            $elementorUsages = FeedPostType::getElementorWidgetUsages($feed, $this->wpdb);
        } else {
            $shortcodeUsages = [];
            $wpBlockUsages = [];
            $elementorUsages = [];
        }

        // Shortcodes and blocks can exist in the same page
        $contentUsages = Arrays::mergeUnique($shortcodeUsages, $wpBlockUsages, function ($a, $b) {
            return $a['id'] === $b['id'];
        });
        // Elementor widgets are not part of post_content, so we don't need to merge only uniques
        $usages = array_merge($contentUsages, $elementorUsages);

        return array_merge([
            'id' => $feed->getId(),
            'name' => $feed->getName(),
            'usages' => $usages,
            'options' => $feed->getOptions(),
        ]);
    }
}
