<?php

declare(strict_types=1);

namespace RebelCode\Iris\Converter;

use Exception;
use RebelCode\Iris\Data\Item;

/**
 * Used by conversion strategies to short-circuit the conversion process.
 */
class ConversionShortCircuit extends Exception
{
    /** @var Item|null */
    protected $item;

    /**
     * @param Item|null $item Optional item to pass back to the converter to include in the final list of items.
     */
    public function __construct(?Item $item = null)
    {
        parent::__construct();
        $this->item = $item;
    }

    /**
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }
}
