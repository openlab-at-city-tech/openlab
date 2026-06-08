<?php

namespace RebelCode\Atlas;

/** @psalm-immutable */
class Order
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /** @var string */
    protected $field;

    /**
     * @var string
     * @psalm-var Order::*
     */
    protected $sort;

    /**
     * Constructor.
     *
     * @psalm-param Order::* $sort
     */
    public function __construct(string $field, string $sort = self::ASC)
    {
        $this->field = $field;
        $this->sort = $sort;
    }

    public function getField(): string
    {
        return $this->field;
    }

    /** @psalm-return Order::* */
    public function getSort(): string
    {
        return $this->sort;
    }

    public function asc(): Order
    {
        return ($this->sort === self::DESC)
            ? new self($this->field, self::ASC)
            : $this;
    }

    public function desc(): Order
    {
        return ($this->sort === self::ASC)
            ? new self($this->field, self::DESC)
            : $this;
    }

    public static function by(string $field): Order
    {
        return new self($field);
    }
}
