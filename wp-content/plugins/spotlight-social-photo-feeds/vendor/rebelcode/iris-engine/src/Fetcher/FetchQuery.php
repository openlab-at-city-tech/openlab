<?php

declare(strict_types=1);

namespace RebelCode\Iris\Fetcher;

use RebelCode\Iris\Data\Source;

/** @psalm-immutable */
class FetchQuery
{
    /** @var Source */
    public $source;

    /** @var string|null */
    public $cursor;

    /** @var int|null */
    public $count;

    /** @var int */
    public $accrual;

    public function __construct(
        Source $source,
        ?string $cursor = null,
        ?int $count = null,
        int $accrual = 0
    ) {
        $this->source = $source;
        $this->cursor = $cursor;
        $this->count = $count;
        $this->accrual = $accrual;
    }

    public function forNextBatch(FetchResult $result): ?self
    {
        if ($result->nextCursor === null) {
            return null;
        }

        return new self($this->source, $result->nextCursor, $this->count, $this->accrual + count($result->items));
    }
}
