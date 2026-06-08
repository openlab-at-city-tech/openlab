<?php

namespace RebelCode\Spotlight\Instagram\Utils;

/**
 * Utility functions related to strings.
 *
 * @since 0.3
 */
class Strings
{
    /**
     * Transforms a kebab-cased string into camelCase.
     *
     * @since 0.3
     *
     * @param string $kebab The kebab-cased string.
     *
     * @return string The camelCased version of the given $kebab string.
     */
    public static function kebabToCamel(string $kebab)
    {
        $parts = explode('-', $kebab);

        if (count($parts) === 1) {
            return reset($parts);
        }

        $first = array_shift($parts);
        $words = array_map('ucfirst', $parts);

        return $first . implode('', $words);
    }

    /**
     * Interpolates values into placeholders in a string.
     *
     * Example usage:
     * ```
     * Strings::interpolate('Hello %{name}', '%{', '}', ['name' => 'world']);
     * ```
     *
     * @param string $subject The string to be interpolated.
     * @param string $delim1  The delimiter that mark the beginning of a placeholder.
     * @param string $delim2  The delimiter that mark the end of a placeholder.
     * @param array  $values  The values to interpolate into the string.
     *
     * @return string The interpolated string.
     */
    public static function interpolate(string $subject, string $delim1, string $delim2, array $values = [])
    {
        $replace = [];

        foreach ($values as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace[$delim1 . $key . $delim2] = $val;
            }
        }

        return strtr($subject, $replace);
    }

    /**
     * Generates a random string containing alphanumeric characters, without duplicates.
     *
     * @since 0.3
     *
     * @param int $length The length of the generated string.
     *
     * @return string The generated string.
     */
    public static function generateRandom(int $length = 10)
    {
        $charArray = [
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
        ];

        shuffle($charArray);

        return implode('', array_slice($charArray, 0, $length));
    }
}
