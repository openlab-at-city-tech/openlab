<?php

namespace RebelCode\Atlas\Exception;

use Throwable;

class MissingQueryTypeException extends \RuntimeException
{
    /** @var string */
    protected $queryType;

    public function __construct(string $message = "", string $queryType = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->queryType = $queryType;
    }

    public function getQueryType(): string
    {
        return $this->queryType;
    }
}
