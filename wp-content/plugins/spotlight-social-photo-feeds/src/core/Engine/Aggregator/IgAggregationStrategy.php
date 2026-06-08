<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Aggregator;

use RebelCode\Iris\Aggregator\AggregationStrategy;
use RebelCode\Iris\Aggregator\ItemProcessor;
use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Store\Query;
use RebelCode\Spotlight\Instagram\Engine\Data\Feed\StoryFeed;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaProductType;
use RebelCode\Spotlight\Instagram\PostTypes\MediaPostType;

class IgAggregationStrategy implements AggregationStrategy
{
    /** @var ItemProcessor[] */
    protected $preProcessors;

    /** @var ItemProcessor[] */
    protected $postProcessors;

    /**
     * Constructor.
     *
     * @param ItemProcessor[] $preProcessors
     * @param ItemProcessor[] $postProcessors
     */
    public function __construct(array $preProcessors = [], array $postProcessors = [])
    {
        $this->preProcessors = $preProcessors;
        $this->postProcessors = $postProcessors;
    }

    public function doManualPagination(Feed $feed, Query $query): bool
    {
        return true;
    }

    public function getFeedQuery(Feed $feed, ?int $count = null, int $offset = 0): ?Query
    {
        if ($count === null) {
            $numPosts = $feed->get('numPosts');

            if (is_array($numPosts)) {
                $desktop = intval($numPosts['desktop'] ?? 9);
                $tablet = intval($numPosts['tablet'] ?? 0);
                $phone = intval($numPosts['phone'] ?? 0);

                $count = (int) max($desktop, $tablet, $phone);
            } else {
                $count = (int) $numPosts;
            }
        }

        if ($feed->get('mediaType') === StoryFeed::MEDIA_TYPE) {
            $condition = new Query\Condition(Query\Condition::AND, [
                new Query\Expression('_sli_non_existent_field', '=', 'no value'),
            ]);
        } else {
            $condition = new Query\Condition(Query\Condition::AND, [
                new Query\Expression(MediaPostType::PRODUCT_TYPE, '!=', MediaProductType::STORY),
                new Query\Expression(MediaPostType::IS_STORY, '!=', '1'),
            ]);
        }

        return new Query($feed->sources, null, $condition, $count, $offset);
    }

    public function getPreProcessors(Feed $feed, Query $query): array
    {
        return $this->preProcessors;
    }

    public function getPostProcessors(Feed $feed, Query $query): array
    {
        return $this->postProcessors;
    }
}
