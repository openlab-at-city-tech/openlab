<?php

namespace TEC\Common\StellarWP\AdminNotices\ValueObjects;

use InvalidArgumentException;

class ScreenCondition
{
    /**
     * @var string|array a string compared against the url, a regex for the url, or an array of conditions used against WP_Screen
     *
     * @see https://developer.wordpress.org/reference/classes/wp_screen/
     */
    private $condition;

    /**
     * @var bool
     */
    private $isRegex = true;

    public function __construct($condition)
    {
        $this->validateCondition($condition);

        $this->condition = $condition;

        // check if condition is a string with a regex using ~ as the delimiter
        $this->isRegex = is_string($condition) && preg_match('/^~.+~[a-z]*$/', $condition) === 1;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function isRegex(): bool
    {
        return $this->isRegex;
    }

    private function validateCondition($condition)
    {
        // check if condition is a string or an array
        if (!(is_string($condition) || is_array($condition))) {
            throw new InvalidArgumentException('Screen condition must be a string or an array');
        }

        // check if array is an associative array with WP_Screen properties
        static $wpScreenProperties = null;

        if ($wpScreenProperties === null) {
            $wpScreenProperties = get_class_vars('WP_Screen');
        }

        if (is_array($condition) && array_diff_key($condition, $wpScreenProperties)) {
            throw new InvalidArgumentException(
                'Screen condition must be an associative array with WP_Screen properties'
            );
        }
    }
}
