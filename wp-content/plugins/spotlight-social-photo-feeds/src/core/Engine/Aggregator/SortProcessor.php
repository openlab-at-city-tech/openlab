<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Aggregator;

use RebelCode\Iris\Aggregator\ItemProcessor;
use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Store\Query;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Utils\Arrays;

class SortProcessor implements ItemProcessor
{
    public function process(array &$items, Feed $feed, Query $query): void
    {
        $fOrder = $feed->get('postOrder');

        switch ($fOrder) {
            // Date sorting
            case 'date_asc':
            case 'date_desc':
            {
                $mult = ($fOrder === 'date_asc') ? 1 : -1;

                usort($items, function (Item $a, Item $b) use ($mult) {
                    $t1 = $a->get(MediaItem::TIMESTAMP);
                    $t2 = $b->get(MediaItem::TIMESTAMP);

                    // If both have dates
                    if ($t1 !== null && $t2 !== null) {
                        return ($t1 <=> $t2) * $mult;
                    }

                    // If m2 has no date, consider it as more recent
                    if ($t1 !== null) {
                        return $mult;
                    }

                    // If m1 has no date, consider it as more recent
                    if ($t2 !== null) {
                        return -$mult;
                    }

                    // Neither have dates
                    return 0;
                });

                break;
            }

            // Popularity sorting
            case 'popularity_asc':
            case 'popularity_desc':
            {
                $mult = ($fOrder === 'popularity_asc') ? 1 : -1;

                usort($items, function (Item $a, Item $b) use ($mult) {
                    $s1 = $a->get(MediaItem::LIKES_COUNT, 0) + $a->get(MediaItem::COMMENTS_COUNT, 0);
                    $s2 = $b->get(MediaItem::LIKES_COUNT, 0) + $b->get(MediaItem::COMMENTS_COUNT, 0);

                    return ($s1 <=> $s2) * $mult;
                });

                break;
            }

            // Random order
            case 'random':
            {
                $items = Arrays::shuffle($items);

                break;
            }
        }
    }
}
