<?php

declare(strict_types=1);

namespace RebelCode\Iris\Exception;

use RebelCode\Iris\Data\Item;
use Throwable;

/** @psalm-immutable */
class ConversionException extends IrisException
{
    /** @var Item */
    public $item;

    public function __construct(string $message, Item $item, Throwable $previous = null)
    {
        parent::__construct($message, $previous);
        $this->item = $item;
    }
}
