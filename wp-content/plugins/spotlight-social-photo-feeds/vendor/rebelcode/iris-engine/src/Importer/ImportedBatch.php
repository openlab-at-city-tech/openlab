<?php

declare(strict_types=1);

namespace RebelCode\Iris\Importer;

use RebelCode\Iris\Data\Item;

/** @psalm-immutable */
class ImportedBatch
{
    /** @var Item[] */
    public $items;

    /** @var string[] */
    public $errors;

    /** @var bool */
    public $hasNext;

    /**
     * Constructor.
     *
     * @param Item[] $items The imported items.
     * @param string[] $errors The errors encountered during importing.
     * @param bool $hasNext Whether another batch can be fetched.
     */
    public function __construct(array $items, array $errors, bool $hasNext)
    {
        $this->items = $items;
        $this->errors = $errors;
        $this->hasNext = $hasNext;
    }

    /**
     * Merges the batch with another.
     *
     * @param ImportedBatch $batch The batch to merge with.
     *
     * @return ImportedBatch A batch that contains all the items and errors from both batches.
     */
    public function mergeWith(ImportedBatch $batch): ImportedBatch
    {
        return new self(
            array_merge($this->items, $batch->items),
            array_merge($this->errors, $batch->errors),
            $this->hasNext || $batch->hasNext
        );
    }
}
