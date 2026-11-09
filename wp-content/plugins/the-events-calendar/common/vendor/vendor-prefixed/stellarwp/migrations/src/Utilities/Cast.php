<?php

/**
 * Casting Utilities.
 *
 * A helper class to provide easier ways to cast values.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Utilities
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Utilities;

/**
 * A helper class to provide easier ways to cast values.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Utilities
 */
class Cast
{
    /**
     * Casts a value to a string if possible or returns an empty string.
     *
     * @since 0.0.1
     *
     * @param mixed $value The value to cast.
     *
     * @return string
     */
    public static function to_string($value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (!is_scalar($value)) {
            return '';
        }
        return strval($value);
    }
    /**
     * Casts a value to a int if possible or returns an empty string.
     *
     * @since 0.0.1
     *
     * @param mixed $value The value to cast.
     *
     * @return int
     */
    public static function to_int($value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (!is_scalar($value)) {
            return 0;
        }
        return intval($value);
    }
    /**
     * Casts a value to a float if possible or returns an empty string.
     *
     * @since 0.0.1
     *
     * @param mixed $value The value to cast.
     *
     * @return float
     */
    public static function to_float($value): float
    {
        if (is_float($value)) {
            return $value;
        }
        if (!is_scalar($value)) {
            return 0.0;
        }
        return floatval($value);
    }
    /**
     * Casts a value to a bool if possible or returns an empty string.
     *
     * @since 0.0.1
     *
     * @param mixed $value The value to cast.
     *
     * @return bool
     */
    public static function to_bool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (!is_scalar($value)) {
            return false;
        }
        return boolval($value);
    }
}