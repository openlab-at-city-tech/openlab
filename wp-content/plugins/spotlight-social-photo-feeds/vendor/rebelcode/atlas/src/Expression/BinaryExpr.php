<?php

namespace RebelCode\Atlas\Expression;

use DomainException;

/** @psalm-immutable */
class BinaryExpr extends BaseExpr
{
    const EQ = '=';
    const NEQ = '!=';
    const GT = '>';
    const LT = '<';
    const GTE = '>=';
    const LTE = '<=';
    const IS = 'IS';
    const IS_NOT = 'IS NOT';
    const IN = 'IN';
    const NOT_IN = 'NOT IN';
    const LIKE = 'LIKE';
    const NOT_LIKE = 'NOT LIKE';
    const BETWEEN = 'BETWEEN';
    const NOT_BETWEEN = 'NOT BETWEEN';
    const REGEXP = 'REGEXP';
    const NOT_REGEXP = 'NOT REGEXP';
    const PLUS = '+';
    const MINUS = '-';
    const MULT = '*';
    const DIV = '/';
    const INT_DIV = 'DIV';
    const MOD = '%';
    const R_SHIFT = '>>';
    const L_SHIFT = '<<';
    const B_AND = '&';
    const B_OR = '|';
    const B_XOR = '^';
    const AND = 'AND';
    const OR = 'OR';
    const XOR = 'XOR';
    /** @var ExprInterface */
    protected $left;
    /** @var string */
    protected $operator;
    /** @var ExprInterface */
    protected $right;

    /** Constructor */
    public function __construct(ExprInterface $left, string $operator, ExprInterface $right)
    {
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    public function getLeft(): ExprInterface
    {
        return $this->left;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getRight(): ExprInterface
    {
        return $this->right;
    }

    public function toString(): string
    {
        $left = $this->left->toString();

        if ($this->operator === self::BETWEEN || $this->operator === self::NOT_BETWEEN) {
            if (!$this->right instanceof Term || !is_array($value = $this->right->getValue())) {
                throw new DomainException('Right operand of ' . $this->operator . ' expression is not an array term');
            }

            $between1 = $value[0]->toString();
            $between2 = $value[1]->toString();

            return "($left $this->operator $between1 AND $between2)";
        } else {
            $right = $this->right->toString();

            return "($left $this->operator $right)";
        }
    }
}
