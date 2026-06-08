<?php

declare(strict_types=1);

namespace RebelCode\Iris;

use RebelCode\Iris\Converter\ConversionShortCircuit;
use RebelCode\Iris\Converter\ConversionStrategy;
use RebelCode\Iris\Data\Item;
use RebelCode\Iris\Exception\ConversionException;
use RebelCode\Iris\Exception\StoreException;

class Converter
{
    /** @var Store */
    protected $store;

    /** @var ConversionStrategy */
    protected $strategy;

    /**
     * Constructor.
     */
    public function __construct(Store $store, ConversionStrategy $strategy)
    {
        $this->store = $store;
        $this->strategy = $strategy;
    }

    /**
     * @throws StoreException
     * @throws ConversionException
     */
    public function convert(Item $item): ?Item
    {
        try {
            return $this->doConversion($item, $this->store->get($item->id));
        } catch (ConversionShortCircuit $e) {
            return null;
        }
    }

    /**
     * @param Item[] $items
     *
     * @return Item[]
     *
     * @throws StoreException
     * @throws ConversionException
     */
    public function convertMultiple(array $items): array
    {
        $existingItems = $this->store->getMultiple(
            array_map(
                function (Item $item) {
                    return $item->id;
                },
                $items
            )
        );

        $items = $this->strategy->beforeBatch($items, $existingItems);

        $convertedItems = [];
        foreach ($items as $item) {
            try {
                $item = $this->doConversion($item, $existingItems[$item->id] ?? null);
            } catch (ConversionShortCircuit $e) {
                $item = $e->getItem();
                break;
            } finally {
                if ($item !== null) {
                    $convertedItems[] = $item;
                }
            }
        }

        return $this->strategy->afterBatch($convertedItems);
    }

    /**
     * @throws Exception\ConversionException
     * @throws ConversionShortCircuit
     */
    protected function doConversion(Item $item, ?Item $existing): ?Item
    {
        $item = $this->strategy->convert($item);

        if ($item !== null && $existing !== null) {
            $item = $this->strategy->reconcile($item, $existing);
        }

        if ($item !== null) {
            $item = $this->strategy->finalize($item);
        }

        return $item;
    }
}
