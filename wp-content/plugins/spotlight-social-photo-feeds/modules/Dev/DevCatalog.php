<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher\Catalog;
use RebelCode\Iris\Fetcher\FetchResult;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;

class DevCatalog implements Catalog
{
    public const DEFAULT_SIZE = 100;

    public function query(Source $source, ?string $cursor = null, ?int $count = null): FetchResult
    {
        $parts = explode('_', $source->id);

        $catalogSize = is_numeric($parts[1] ?? false)
            ? (int) $parts[1]
            : static::DEFAULT_SIZE;

        $count = $count ?? 10;
        $start = intval($cursor);
        $end = min($catalogSize, $start + $count);

        if (!is_int($start)) {
            $start = 0;
        }

        $items = [];
        for ($i = $start; $i < $end; ++$i) {
            $num = $catalogSize - $i;
            $id = sprintf('dev-%d', $num);

            $items[] = new Item($id, null, [$source], [
                MediaItem::MEDIA_ID => $id,
                MediaItem::CAPTION => sprintf('Dev post #%d', $num),
                MediaItem::USERNAME => 'spotlight_dev',
                MediaItem::TIMESTAMP => date(DATE_ISO8601, time() - ($i * DAY_IN_SECONDS)),
                MediaItem::MEDIA_TYPE => 'IMAGE',
                MediaItem::MEDIA_URL => 'https://www.instagram.com/p/CHsEQO7hX3H/media/?size=m',
                MediaItem::MEDIA_PRODUCT_TYPE => 'media_product_type',
                MediaItem::PERMALINK => 'https://www.instagram.com/p/CHsEQO7hX3H/',
                MediaItem::SHORTCODE => 'CHsEQO7hX3H',
                MediaItem::VIDEO_TITLE => '',
                MediaItem::THUMBNAIL_URL => 'https://www.instagram.com/p/CHsEQO7hX3H/media/?size=t',
                MediaItem::LIKES_COUNT => '0',
                MediaItem::COMMENTS_COUNT => '0',
                MediaItem::COMMENTS => [],
                MediaItem::CHILDREN => [],
            ]);
        }

        $nextStart = $end;
        $prevStart = $start - 1;
        $nextCursor = ($nextStart < $catalogSize) ? (string) $nextStart : null;
        $prevCursor = ($prevStart > 0) ? (string) $prevStart : null;

        return new FetchResult($items, $source, $catalogSize, $nextCursor, $prevCursor);
    }
}

/*
 * $parts = explode('_', $source->id);
        if (count($parts) < 2 || !is_numeric($parts[1] ?? '')) {
            return new FetchResult([], $source);
        }

        $catalogSize = (int) $parts[1];
 */
