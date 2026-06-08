<?php

declare(strict_types=1);

namespace RebelCode\Iris\Store\Query;

/** @psalm-immutable */
class Expression implements Criterion
{
    public const EQUAL_TO = '=';
    public const NOT_EQUAL_TO = '!=';
    public const GREATER_THAN = '>';
    public const LESS_THAN = '<';
    public const GREATER_OR_EQUAL_TO = '>=';
    public const LESS_OR_EQUAL_TO = '<=';
    public const LIKE = 'LIKE';
    public const NOT = 'NOT LIKE';
    public const REGEX = 'REGEX';
    public const IN = 'IN';
    public const NOT_IN = 'NOT_IN';
    public const BETWEEN = 'BETWEEN';
    public const NOT_BETWEEN = 'BETWEEN';
    public const EXISTS = 'EXISTS';
    public const NOT_EXISTS = 'EXISTS';

    /** @var string */
    public $field;

    /** @var mixed|null */
    public $value;

    /**
     * @var string
     *
     * @psalm-var Expression::*
     */
    public $operator;

    /**
     * Constructor.
     *
     * @param string $field The field that the criterion is based on.
     * @param string $operator The criterion operator. See the class constants in {@link Expression}.
     * @param mixed|null $value Optional value to use in the criterion. Can be null for {@link Expression::EXISTS} and
     *                          {@link Expression::NOT_EXISTS} criterion.
     *
     * @psalm-param Expression::* $operator
     */
    public function __construct(string $field, string $operator, $value = null)
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }
}
