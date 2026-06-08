<?php

namespace RebelCode\Atlas\Expression;

/** @psalm-immutable */
class UnaryExpr extends BaseExpr
{
    public const NOT = '!';
    public const NEG = '-';
    public const B_NEG = '~';

    /** @var string */
    protected $operator;

    /** @var ExprInterface */
    protected $operand;

    /** Constructor */
    public function __construct(string $operator, ExprInterface $term)
    {
        $this->operator = $operator;
        $this->operand = $term;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getOperand(): ExprInterface
    {
        return $this->operand;
    }

    public function toString(): string
    {
        $term = $this->operand->toString();

        $isColumn = ($this->operand instanceof Term && $this->operand->getType() === Term::COLUMN);
        $isDistinct = $isColumn && $this->operand->isDistinct();
        $distinct = $isDistinct ? 'DISTINCT ' : '';

        return "$this->operator($distinct$term)";
    }
}
