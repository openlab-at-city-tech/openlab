<?php

declare(strict_types=1);

namespace RebelCode\Iris\Store\Query;

/** @psalm-immutable */
class Condition implements Criterion
{
    public const AND = 'AND';
    public const OR = 'OR';

    /**
     * @var string
     *
     * @psalm-var Condition::AND|Condition::OR
     */
    public $relation;

    /** @var Criterion[] */
    public $criteria;

    /**
     * Constructor.
     *
     * @param string $relation The relation between the condition
     * @param Criterion[] $criteria The condition's criteria.
     *
     * @psalm-param Condition::AND|Condition::OR $relation
     */
    public function __construct(string $relation, array $criteria)
    {
        $this->relation = $relation;
        $this->criteria = $criteria;
    }
}
