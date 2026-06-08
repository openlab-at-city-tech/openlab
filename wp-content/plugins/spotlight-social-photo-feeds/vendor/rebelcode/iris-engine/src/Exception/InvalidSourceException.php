<?php

declare(strict_types=1);

namespace RebelCode\Iris\Exception;

use RebelCode\Iris\Data\Source;
use Throwable;

/** @psalm-immutable */
class InvalidSourceException extends IrisException
{
    /** @var Source */
    public $source;

    public function __construct(string $message, Source $source, Throwable $previous = null)
    {
        parent::__construct($message, $previous);
        $this->source = $source;
    }
}
