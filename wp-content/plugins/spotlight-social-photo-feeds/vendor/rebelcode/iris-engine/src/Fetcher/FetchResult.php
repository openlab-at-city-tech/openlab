<?php

declare(strict_types=1);

namespace RebelCode\Iris\Fetcher;

use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Data\Source;

/** @psalm-immutable */
class FetchResult
{
    /** @var Item[] */
    public $items;

    /** @var Source */
    public $source;

    /** @var int|null */
    public $catalogSize;

    /** @var string|null */
    public $nextCursor;

    /** @var string|null */
    public $prevCursor;

    /** @var string[] */
    public $errors;

    /**
     * Constructor.
     *
     * @param Item[] $items The fetched items.
     * @param Source $source The source from which items where fetched.
     * @param int|null $catalogSize The total number of items in the source's catalog.
     * @param string|null $nextCursor The cursor for the next batch of items.
     * @param string|null $prevCursor The cursor for the previous batch of items.
     * @param string[] $errors A list of error or warning messages.
     */
    public function __construct(
        array $items,
        Source $source,
        ?int $catalogSize = null,
        ?string $nextCursor = null,
        ?string $prevCursor = null,
        array $errors = []
    ) {
        $this->items = $items;
        $this->source = $source;
        $this->catalogSize = $catalogSize;
        $this->nextCursor = $nextCursor;
        $this->prevCursor = $prevCursor;
        $this->errors = $errors;
    }
}
