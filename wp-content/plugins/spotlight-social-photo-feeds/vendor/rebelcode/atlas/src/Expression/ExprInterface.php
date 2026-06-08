<?php

namespace RebelCode\Atlas\Expression;

/** @psalm-immutable */
interface ExprInterface
{
    public function and($term): BinaryExpr;

    public function or($term): BinaryExpr;

    public function xor($term): BinaryExpr;

    public function equals($term): BinaryExpr;

    public function notEquals($term): BinaryExpr;

    public function gt($term): BinaryExpr;

    public function lt($term): BinaryExpr;

    public function gte($term): BinaryExpr;

    public function lte($term): BinaryExpr;

    public function is($term): BinaryExpr;

    public function isNot($term): BinaryExpr;

    public function in($term): BinaryExpr;

    public function notIn($term): BinaryExpr;

    public function like($term): BinaryExpr;

    public function notLike($term): BinaryExpr;

    public function between($term1, $term2): BinaryExpr;

    public function notBetween($term1, $term2): BinaryExpr;

    public function regexp($term): BinaryExpr;

    public function notRegexp($term): BinaryExpr;

    public function plus($term): BinaryExpr;

    public function minus($term): BinaryExpr;

    public function mult($term): BinaryExpr;

    public function div($term): BinaryExpr;

    public function intDiv($term): BinaryExpr;

    public function mod($term): BinaryExpr;

    public function rightShift($term): BinaryExpr;

    public function leftShift($term): BinaryExpr;

    public function bitwiseAnd($term): BinaryExpr;

    public function bitwiseOr($term): BinaryExpr;

    public function bitwiseXor($term): BinaryExpr;

    public function bitwiseNeg(): UnaryExpr;

    public function not(): UnaryExpr;

    public function neg(): UnaryExpr;

    public function toString(): string;

    public function __toString(): string;
}
