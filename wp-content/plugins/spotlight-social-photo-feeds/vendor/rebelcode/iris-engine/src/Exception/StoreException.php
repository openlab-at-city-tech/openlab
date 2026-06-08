<?php

declare(strict_types=1);

namespace RebelCode\Iris\Exception;

use RebelCode\Iris\Store;
use Throwable;

/** @psalm-immutable */
class StoreException extends IrisException
{
    /** @var Store */
    public $store;

    public function __construct(string $message, Store $store, Throwable $previous = null)
    {
        parent::__construct($message, $previous);
        $this->store = $store;
    }
}
