<?php

namespace RebelCode\Atlas\Expression;

use Throwable;

/** @psalm-immutable */
abstract class BaseExpr implements ExprInterface
{
    public function and($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::AND, Term::create($term));
    }

    public function or($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::OR, Term::create($term));
    }

    public function xor($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::XOR, Term::create($term));
    }

    public function equals($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::EQ, Term::create($term));
    }

    public function notEquals($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::NEQ, Term::create($term));
    }

    public function gt($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::GT, Term::create($term));
    }

    public function lt($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::LT, Term::create($term));
    }

    public function gte($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::GTE, Term::create($term));
    }

    public function lte($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::LTE, Term::create($term));
    }

    public function is($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::IS, Term::create($term));
    }

    public function isNot($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::IS_NOT, Term::create($term));
    }

    public function in($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::IN, Term::create($term));
    }

    public function notIn($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::NOT_IN, Term::create($term));
    }

    public function like($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::LIKE, Term::create($term));
    }

    public function notLike($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::NOT_LIKE, Term::create($term));
    }

    public function between($term1, $term2): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::BETWEEN, Term::create([$term1, $term2]));
    }

    public function notBetween($term1, $term2): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::NOT_BETWEEN, Term::create([$term1, $term2]));
    }

    public function regexp($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::REGEXP, Term::create($term));
    }

    public function notRegexp($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::NOT_REGEXP, Term::create($term));
    }

    public function plus($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::PLUS, Term::create($term));
    }

    public function minus($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::MINUS, Term::create($term));
    }

    public function mult($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::MULT, Term::create($term));
    }

    public function div($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::DIV, Term::create($term));
    }

    public function intDiv($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::INT_DIV, Term::create($term));
    }

    public function mod($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::MOD, Term::create($term));
    }

    public function rightShift($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::R_SHIFT, Term::create($term));
    }

    public function leftShift($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::L_SHIFT, Term::create($term));
    }

    public function bitwiseAnd($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::B_AND, Term::create($term));
    }

    public function bitwiseOr($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::B_OR, Term::create($term));
    }

    public function bitwiseXor($term): BinaryExpr
    {
        return new BinaryExpr($this, BinaryExpr::B_XOR, Term::create($term));
    }

    public function bitwiseNeg(): UnaryExpr
    {
        return new UnaryExpr(UnaryExpr::B_NEG, $this);
    }

    public function not(): UnaryExpr
    {
        return new UnaryExpr(UnaryExpr::NOT, $this);
    }

    public function neg(): UnaryExpr
    {
        return new UnaryExpr(UnaryExpr::NEG, $this);
    }

    public function fn(string $fn): UnaryExpr
    {
        return new UnaryExpr($fn, $this);
    }

    public function __toString(): string
    {
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            try {
                return $this->toString();
            } catch (Throwable $throwable) {
                return '';
            }
        } else {
            return $this->toString();
        }
    }
}
