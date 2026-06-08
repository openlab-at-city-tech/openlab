<?php

namespace RebelCode\Atlas\Exception;

use RebelCode\Atlas\Table;
use RuntimeException;
use Throwable;

class NoTableSchemaException extends RuntimeException
{
    /** @var Table|null */
    protected $table;

    public function __construct(string $message = "", ?Table $table = null, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->table = $table;
    }

    public function getTable(): ?Table
    {
        return $this->table;
    }
}
