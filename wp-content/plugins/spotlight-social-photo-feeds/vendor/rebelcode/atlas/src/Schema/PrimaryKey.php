<?php

namespace RebelCode\Atlas\Schema;

/** @psalm-immutable */
class PrimaryKey extends Key
{
    /**
     * Constructor.
     *
     * @param list<string> $columns The columns that make up this key.
     */
    public function __construct(array $columns)
    {
        parent::__construct(true, $columns);
    }
}
