<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Aggregator;

use RebelCode\Iris\Aggregator\ItemProcessor;
use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Store\Query;
use RebelCode\Spotlight\Instagram\PostTypes\CustomMedia;

class CustomMediaPreProcessor implements ItemProcessor
{
    public function process(array &$items, Feed $feed, Query $query): void
    {
        $accountIds = $feed->get('accounts', []);

        foreach ($accountIds as $accountId) {
            if (is_int($accountId)) {
                foreach (CustomMedia::getForAccount($accountId) as $customMedia) {
                    $items[] = $customMedia;
                }
            }
        }
    }
}
