<?php

namespace RebelCode\Atlas\Exception;

use RebelCode\Atlas\Query;
use RuntimeException;
use Throwable;

class QueryCompileException extends RuntimeException
{
    /** @var Query */
    protected $query;

    public function __construct(string $message, Query $query, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->query = $query;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }
}
