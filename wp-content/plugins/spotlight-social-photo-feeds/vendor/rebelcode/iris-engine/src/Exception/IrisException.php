<?php

declare(strict_types=1);

namespace RebelCode\Iris\Exception;

use RuntimeException;
use Throwable;

/**
 * @psalm-immutable
 * @psalm-suppress MutableDependency
 */
class IrisException extends RuntimeException
{
    /**
     * Constructor.
     *
     * @param string $message The exception message.
     * @param Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
