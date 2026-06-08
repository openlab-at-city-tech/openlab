<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram;

use Exception;
use RebelCode\Iris\Aggregator\AggregateResult;
use RebelCode\Iris\Data\Feed;
use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Engine;
use RebelCode\Iris\Importer;
use RebelCode\Iris\Importer\ImportedBatch;
use RebelCode\Iris\Utils\Marker;
use RebelCode\Spotlight\Instagram\Engine\Data\Feed\StoryFeed;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaChild;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaComment;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaProductType;
use RebelCode\Spotlight\Instagram\Engine\IgPostStore;
use RebelCode\Spotlight\Instagram\Feeds\FeedManager;
use RebelCode\Spotlight\Instagram\Utils\Arrays;

class Server
{
    /** @var Engine */
    protected $engine;

    /** @var Importer */
    protected $importer;

    /** @var FeedManager */
    protected $feedManager;

    /** @var Marker */
    protected $importerLock;

    /** Constructor. */
    public function __construct(Engine $engine, Importer $importer, FeedManager $feedManager, Marker $importerLock)
    {
        $this->engine = $engine;
        $this->importer = $importer;
        $this->feedManager = $feedManager;
        $this->importerLock = $importerLock;
    }

    public function getFeedMedia(array $options = [], ?int $from = 0, ?int $num = null): array
    {
        if (is_array($options['numPosts'] ?? null)) {
            $num = max(
                $options['numPosts']['desktop'] ?? 9,
                $options['numPosts']['tablet'] ?? 9,
                $options['numPosts']['phone'] ?? 9,
            );
        } else {
            $num = intval($options['numPosts']) ?? 9;
        }

        // Get media and total
        $feed = $this->feedManager->createFeed($options);
        $mainResult = $this->engine->getAggregator()->aggregate($feed, $num, $from);
        $needImport = ($mainResult->storeTotal === 0);

        if (!$needImport) {
            // Check each feed source whether an import is required
            foreach ($feed->sources as $source) {
                try {
                    $sourceItems = $this->engine->getStore()->getForSources([$source], 1);
                    $needImport = $needImport || count($sourceItems) === 0;
                } catch (Exception $e) {
                    // Fail silently
                    continue;
                }
            }
        }

        IgPostStore::updateLastRequestedTime($mainResult->items);

        // Get stories
        $storyFeed = StoryFeed::createFromFeed($feed);
        $storiesResult = ($storyFeed !== null)
            ? $this->engine->getAggregator()->aggregate($storyFeed)
            : new AggregateResult([], 0, 0, 0);

        $items = Arrays::map($mainResult->items, [$this, 'transform']);
        $stories = Arrays::map($storiesResult->items, [$this, 'transform']);

        return [
            'media' => $items,
            'stories' => $stories,
            'total' => $mainResult->preTotal,
            'needImport' => $needImport,
            'errors' => [],
        ];
    }

    public function getSourceMedia(Source $source, int $from = 0, int $num = null, bool $asc = false): array
    {
        $feed = new Feed(null, [$source], [
            'postOrder' => 'date_' . ($asc ? 'asc' : 'desc'),
            'mediaType' => 'all',
        ]);

        $result = $this->engine->getAggregator()->aggregate($feed, $num, $from);
        $items = Arrays::map($result->items, [$this, 'transform']);

        return [
            'media' => $items,
            'total' => $result->preTotal,
            'errors' => [],
        ];
    }

    public function import(array $options): array
    {
        $feed = $this->feedManager->createFeed($options);

        try {
            $batch = $this->importer->importForSources($feed->sources);
        } catch (\Throwable $e) {
            ErrorLog::exception($e);
            $batch = new ImportedBatch([], [$e->getMessage()], false);
        }

        return [
            'success' => true,
            'items' => $batch->items,
            'isLocked' => count($batch->items) === 0 && $this->importerLock->isSet(),
            'batching' => $batch->hasNext,
            'errors' => $batch->errors,
        ];
    }

    public function transform(Item $item): array
    {
        $children = $item->data['children'] ?? [];
        foreach ($children as $idx => $child) {
            $children[$idx] = [
                'id' => $child[MediaChild::MEDIA_ID],
                'type' => $child[MediaChild::MEDIA_TYPE],
                'url' => $child[MediaChild::MEDIA_URL],
                'permalink' => $child[MediaChild::PERMALINK] ?? '',
                'shortcode' => $child[MediaChild::SHORTCODE] ?? '',
                'size' => $child[MediaItem::MEDIA_SIZE] ?? null,
                'thumbnail' => $child[MediaItem::THUMBNAIL_URL] ?? '',
                'thumbnails' => $child[MediaItem::THUMBNAILS] ?? [],
            ];
        }

        $comments = $item->data['comments'] ?? [];
        foreach ($comments as $idx => $comment) {
            $comments[$idx] = [
                'id' => $comment[MediaComment::ID],
                'username' => $comment[MediaComment::USERNAME],
                'text' => $comment[MediaComment::TEXT],
                'timestamp' => $comment[MediaComment::TIMESTAMP],
                'likeCount' => $comment[MediaComment::LIKES_COUNT],
            ];
        }

        $result = [
            'id' => $item->data[MediaItem::MEDIA_ID],
            'username' => $item->data[MediaItem::USERNAME],
            'caption' => $item->data[MediaItem::CAPTION],
            'timestamp' => $item->data[MediaItem::TIMESTAMP],
            'type' => $item->data[MediaItem::MEDIA_TYPE],
            'url' => $item->data[MediaItem::MEDIA_URL],
            'size' => $item->data[MediaItem::MEDIA_SIZE],
            'permalink' => $item->data[MediaItem::PERMALINK],
            'shortcode' => $item->data[MediaItem::SHORTCODE] ?? '',
            'videoTitle' => $item->data[MediaItem::VIDEO_TITLE] ?? '',
            'productType' => $item->data[MediaItem::MEDIA_PRODUCT_TYPE] ?? MediaProductType::FEED,
            'thumbnail' => $item->data[MediaItem::THUMBNAIL_URL],
            'thumbnails' => $item->data[MediaItem::THUMBNAILS],
            'likesCount' => $item->data[MediaItem::LIKES_COUNT],
            'commentsCount' => $item->data[MediaItem::COMMENTS_COUNT],
            'comments' => $comments,
            'children' => $children,
            'sources' => $item->sources,
        ];

        return apply_filters('spotlight/instagram/server/transform_item', $result);
    }
}
