<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Converter;

use RebelCode\Iris\Converter\ConversionStrategy;
use RebelCode\Iris\Data\Item;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Utils\Functions;

class IgConversionStrategy implements ConversionStrategy
{
    public function beforeBatch(array $incoming, array $existing): array
    {
        return $incoming;
    }

    public function convert(Item $item): ?Item
    {
        $comments = $item->data[MediaItem::COMMENTS]['data'] ?? [];
        $children = $item->data[MediaItem::CHILDREN]['data'] ?? [];
        $shortcode = $item->data[MediaItem::SHORTCODE] ?? '';
        $permalink = $item->data[MediaItem::PERMALINK] ?? '';

        if (empty($shortcode) || empty($permalink)) {
            if (preg_match('/instagram\.com\/p\/([^\/]+)/i', $permalink, $matches)) {
                if (isset($matches[1])) {
                    $shortcode = $matches[1];
                }
            }
        }

        return $item->withChanges([
            MediaItem::SHORTCODE => $shortcode,
            MediaItem::COMMENTS => $comments,
            MediaItem::CHILDREN => $children,
        ]);
    }

    public function reconcile(Item $incoming, Item $existing): ?Item
    {
        $newItem = $existing
            ->withAddedSources($incoming->sources)
            ->withChanges([
                MediaItem::MEDIA_URL => $incoming->get(MediaItem::MEDIA_URL),
                MediaItem::CAPTION => $incoming->get(MediaItem::CAPTION),
                MediaItem::LIKES_COUNT => $incoming->get(MediaItem::LIKES_COUNT),
                MediaItem::COMMENTS_COUNT => $incoming->get(MediaItem::COMMENTS_COUNT),
                MediaItem::COMMENTS => $incoming->get(MediaItem::COMMENTS_COUNT),
            ]);

        // Update the URL for video posts
        $type = $newItem->get(MediaItem::MEDIA_TYPE);
        if ($type === 'VIDEO') {
            $newItem = $newItem->with(MediaItem::MEDIA_URL, $incoming->get(MediaItem::MEDIA_URL));
        }

        // Update the URL for video posts in albums
        $children = $existing->get(MediaItem::CHILDREN, []);
        $newChildren = $incoming->get(MediaItem::CHILDREN, []);
        foreach ($children as $idx => $child) {
            if ($child[MediaItem::MEDIA_TYPE] === 'VIDEO' && !empty($newChildren[$idx][MediaItem::MEDIA_URL])) {
                $child[MediaItem::MEDIA_URL] = $newChildren[$idx][MediaItem::MEDIA_URL];
            }
            $children[$idx] = $child;
        }
        $newItem = $newItem->with(MediaItem::CHILDREN, $children);

        // If the reconciled item is the same as the existing one, disregard it
        if ($this->itemsEqual($newItem, $existing)) {
            return null;
        }

        return $newItem;
    }

    public function finalize(Item $item): ?Item
    {
        return $item;
    }

    protected function itemsEqual(Item $a, Item $b): bool
    {
        $sources1 = Arrays::map($a->sources, Functions::method('__toString'));
        $sources2 = Arrays::map($b->sources, Functions::method('__toString'));
        sort($sources1);
        sort($sources2);
        $sources1 = Arrays::join($sources1, ',');
        $sources2 = Arrays::join($sources2, ',');

        return $a->id === $b->id &&
               $a->data == $b->data &&
               $sources1 === $sources2;
    }

    public function afterBatch(array $items): array
    {
        return $items;
    }
}
