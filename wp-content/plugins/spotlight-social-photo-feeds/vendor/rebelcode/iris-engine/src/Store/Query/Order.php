<?php

declare(strict_types=1);

namespace RebelCode\Iris\Store\Query;

/** @psalm-immutable */
class Order
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     * @var string
     * @psalm-var Order::ASC|Order::DESC
     */
    public $type;

    /** @var string */
    public $field;

    /**
     * Constructor.
     *
     * @param string $type Either {@link Order::ASC} for ascending, or {@link Order::DESC} for descending.
     * @param string $field The key of the {@link Item::data} field to order by.
     *
     * @psalm-param Order::ASC|Order::DESC $type
     */
    public function __construct(string $type, string $field)
    {
        $this->type = $type;
        $this->field = $field;
    }
}
