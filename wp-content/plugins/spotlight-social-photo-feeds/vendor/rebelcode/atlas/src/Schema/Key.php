<?php

namespace RebelCode\Atlas\Schema;

/** @psalm-immutable */
class Key
{
    /** @var bool */
    protected $isPrimary;

    /** @var list<string> */
    protected $columns;

    /**
     * Constructor.
     *
     * @param bool $isPrimary Whether the key is a primary key.
     * @param list<string> $columns The columns that make up this key.
     */
    public function __construct(bool $isPrimary, array $columns)
    {
        $this->isPrimary = $isPrimary;
        $this->columns = $columns;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    /** @return list<string> */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
