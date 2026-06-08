<?php

declare(strict_types=1);

namespace RebelCode\Iris\Converter;

use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Exception\ConversionException;

interface ConversionStrategy
{
    /**
     * Converts a batch of items after each item in the batch has been individually converted.
     *
     * @param Item[] $incoming The incoming items to be converted.
     * @param array<string, Item> $existing A list of corresponding existing items from the store.
     *
     * @return Item[] The list of items to convert.
     */
    public function beforeBatch(array $incoming, array $existing): array;

    /**
     * Converts an item.
     *
     * @param Item $item The item to convert.
     *
     * @return Item|null The converted item, or null if the item should be rejected.
     *
     * @throws ConversionException If an error occurred.
     * @throws ConversionShortCircuit To stop conversion early and dismiss any remaining items.
     */
    public function convert(Item $item): ?Item;

    /**
     * Reconciles a converted item with a corresponding existing item.
     *
     * @param Item $incoming The item to be converted.
     * @param Item $existing The corresponding existing item.
     *
     * @return Item|null The reconciled item, or null if the item should be rejected.
     *
     * @throws ConversionException If an error occurred.
     * @throws ConversionShortCircuit To stop conversion early and dismiss any remaining items.
     */
    public function reconcile(Item $incoming, Item $existing): ?Item;

    /**
     * Finalizes an item after conversion and possible reconciliation.
     *
     * @param Item $item The item to finalize.
     *
     * @return Item|null The finalized item, or null if the item should be rejected.
     *
     * @throws ConversionException If an error occurred.
     * @throws ConversionShortCircuit To stop conversion early and dismiss any remaining items.
     */
    public function finalize(Item $item): ?Item;

    /**
     * Converts a batch of items after each item in the batch has been individually converted.
     *
     * @param Item[] $items The converted items.
     *
     * @return Item[] The final list of items.
     */
    public function afterBatch(array $items): array;
}
