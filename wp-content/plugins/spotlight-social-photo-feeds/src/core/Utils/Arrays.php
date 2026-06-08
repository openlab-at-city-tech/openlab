<?php

namespace RebelCode\Spotlight\Instagram\Utils;

/**
 * Utility functions for working with arrays,
 *
 * @since 0.1
 */
class Arrays
{
    /**
     * Functionally equivalent to {@link array_map}, but passes the index or key as a second argument to the callback.
     * For code legibility purposes, this function takes the array as the first argument whereas {@link array_map} takes
     * the array as a second argument. By accepting the function as a second argument, the syntax becomes much cleaner
     * when using anonymous functions.
     *
     * @since 0.1
     *
     * @param array    $array The array to map.
     * @param callable $fn    The function to call for each entry in the array. It will receive the value and the index
     *                        as arguments in that order, and must return the new value.
     *
     * @return array The mapped array.
     */
    public static function map(array $array, callable $fn)
    {
        $newArray = [];

        array_walk($array, function ($val, $key) use ($fn, &$newArray) {
            $newArray[$key] = $fn($val, $key);
        });

        return $newArray;
    }

    /**
     * Similar to {@link Arrays::map()}, but reverses the order of the index and key arguments given to the function.
     *
     * @since 0.1
     *
     * @param array    $array The array to map.
     * @param callable $fn    The function to call for each entry in the array. It will receive the index and the value
     *                        as arguments in that order, and must return the new value.
     *
     * @return array The mapped array.
     */
    public static function mapAssoc(array $array, callable $fn)
    {
        $newArray = [];

        array_walk($array, function ($val, $key) use ($fn, &$newArray) {
            $newArray[$key] = $fn($key, $val);
        });

        return $newArray;
    }

    /**
     * Maps each value in an array to a key->value pair, creating a new associative array.
     *
     * This function uses {@link Arrays::mapPairs()} but omits the first argument (the key) when calling the callback.
     *
     * @since 0.5
     *
     * @param array    $array The array to map.
     * @param callable $fn    The function to call for each pair in the array. It will receive the value as argument
     *                        and must return an array containing two values: the key and value for the new array.
     *
     * @return array The mapped array.
     */
    public static function createMap(array $array, callable $fn)
    {
        return static::mapPairs($array, function ($key, $value) use ($fn) {
            return $fn($value);
        });
    }

    /**
     * Maps each key->value pair in an array, creating a new array.
     *
     * The given function is used to construct a new key->value pair. This distinguishes this function from
     * {@link Arrays::map()} by allowing the caller to create a mapped array with different keys.
     *
     * @since 0.1
     *
     * @param array    $array The array to map.
     * @param callable $fn    The function to call for each pair in the array. It will receive the key, the value and
     *                        the index as arguments in that order, and must return an array containing two values: the
     *                        new key and the new value.
     *
     * @return array The mapped array.
     */
    public static function mapPairs(array $array, callable $fn)
    {
        $newArray = [];
        $idx = 0;

        array_walk($array, function ($val, $key) use ($fn, &$newArray, &$idx) {
            [$newKey, $newVal] = $fn($key, $val, $idx);
            $newArray[$newKey] = $newVal;
            $idx++;
        });

        return $newArray;
    }

    /**
     * Runs a function on each element of the array.
     *
     * @since 0.1
     *
     * @param array    $array The array.
     * @param callable $fn    The function to call for each value. It will receive the value and the key as arguments
     *                        in that order. The return value is ignored.
     */
    public static function each(array $array, callable $fn)
    {
        array_walk($array, function ($val, $key) use ($fn) {
            $fn($val, $key);
        });
    }

    /**
     * Runs every function in the array.
     *
     * @since 0.4.1
     *
     * @param array $array The array of functions.
     * @param array $args  Optional list of arguments to pass to each function.
     */
    public static function callEach(array $array, array $args = [])
    {
        array_map(function ($val) use ($args) {
            if (is_callable($val)) {
                call_user_func_array($val, $args);
            }
        }, $array);
    }

    /**
     * Similar to {@link Arrays::each()}, but reverses the order of the arguments given to the function.
     *
     * @since 0.1
     *
     * @param array    $array The array.
     * @param callable $fn    The function to call for each value. It will receive the key and the value as arguments
     *                        in that order. The return value is ignored.
     */
    public static function eachAssoc(array $array, callable $fn)
    {
        array_walk($array, function ($val, $key) use ($fn) {
            $fn($key, $val);
        });
    }

    /**
     * Concatenates the elements of an array using a glue string, optionally mapping each value before concatenation.
     *
     * @since 0.1
     *
     * @param array         $array The array.
     * @param string        $glue  The string to use to concatenate values.
     * @param callable|null $fn    Optional function for mapping values before concatenation. The {@link Arrays::map()}
     *                             function will be used for this purpose. If null, no mapping is performed.
     *
     * @return string A string containing the concatenated values, each separated by the $glue string.
     */
    public static function join(array $array, string $glue, ?callable $fn = null): string
    {
        if ($fn === null) {
            return implode($glue, $array);
        }

        return implode($glue, static::map($array, $fn));
    }

    /**
     * Returns the first element from an array that satisfies a condition.
     *
     * @param array $array The array to search.
     * @param callable|null $fn The condition function. The value and the key of the element are passed to the function
     *                          as arguments, in that order. The function should return true if the element satisfies
     *                          the condition, and false if it doesn't.
     *
     * @return mixed|null The element that satisfied the condition, or null if no element satisfied the condition.
     */
    public static function find(array $array, ?callable $fn = null)
    {
        if ($fn === null) {
            return null;
        }

        foreach ($array as $key => $value) {
            if ($fn($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Assigns a deep key in an array to a specific value.
     *
     * A path array is used to find the deep key. Each element in the path is checked for in the array. If it does not
     * exist, an empty array will be created at that key and the algorithm will repeat the process for that new array.
     *
     * @since 0.1
     *
     * @param array $array The array to change.
     * @param array $path  A list of strings, representing the path to the deep key.
     * @param mixed $value The value to set.
     */
    public static function setPath(array &$array, array $path, $value)
    {
        if (empty($path)) {
            return;
        }

        $cursor = &$array;
        while (count($path) > 1) {
            $head = array_shift($path);

            if (!array_key_exists($head, $cursor)) {
                $cursor[$head] = [];
            }

            $cursor = &$cursor[$head];
        }

        $last = reset($path);
        $cursor[$last] = $value;
    }

    /**
     * Retrieves the last element of an array.
     *
     * Internally, this uses {@link end()}. However, this function does not take the argument by reference, allowing
     * it to be used with intermediate values.
     *
     * @since 0.1
     *
     * @param array $array The array.
     *
     * @return mixed The value of the last element of the array, of false if the array is empty.
     */
    public static function last(array $array)
    {
        return end($array);
    }

    /**
     * Performs a deep array merge.
     *
     * This function behaves similarly to {@link array_merge()}, making values in the second argument override those
     * in the first. If a value is an array in both arguments, the function will recurse. This is unlike the
     * {@link array_merge_recursive()} function, which combines values into new arrays.
     *
     * @since 0.1
     *
     * @param array $array1 The array to merge into.
     * @param array $array2 The array whose values to merge into $array1.
     *
     * @return array The merged array.
     */
    static function deepMerge(array $array1, array $array2)
    {
        $result = $array1;

        foreach ($array2 as $key => $value) {
            // Both arrays have sub-arrays for this key. Recurse
            if (isset($array1[$key]) && is_array($value) && is_array($array1[$key])) {
                $result[$key] = static::deepMerge($array1[$key], $value);
                continue;
            }

            // No conflict. Append if numeric key, override if string key
            if (is_numeric($key)) {
                $result[] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Merges two arrays ensuring that only unique elements are present in the final array.
     *
     * The merging behavior is similar to {@link array_merge()}; elements with string keys in the second array will
     * overwrite elements in $array1 with the same key, while elements with numeric keys will be appended to the end
     * of the resulting list.
     *
     * @since 0.3
     *
     * @param array    $array1    The first array.
     * @param array    $array2    The second array.
     * @param callable $compareFn A function that takes two elements as arguments and returns true if they are equal,
     *                            or false if they are not. Elements from $array2 for which this function returns
     *                            true will be omitted from the resulting array.
     *
     * @return array The resulting array, containing all elements from $array1 (some of which may have been overwritten
     *               by corresponding elements from $array2), and non-intersecting elements from $array2 that are not
     *               considered to be equal to any element in $array1.
     */
    static function mergeUnique(array $array1, array $array2, callable $compareFn)
    {
        $result = $array1;

        foreach ($array2 as $key => $value2) {
            $equalItems = array_filter($array1, function ($value1) use ($compareFn, $value2) {
                return $compareFn($value1, $value2);
            });

            if (count($equalItems) === 0) {
                if (is_numeric($key)) {
                    $result[] = $value2;
                } else {
                    $result[$key] = $value2;
                }
            }
        }

        return $result;
    }

    /**
     * Creates a copy of an array containing only unique values.
     *
     * @since 0.5
     *
     * @param array    $array    The array.
     * @param callable $uniqueFn The function that determines uniqueness for each element. Receives an array element as
     *                           argument and is expected to return a unique string or integer key for that element.
     *
     * @return array The resulting array.
     */
    static function unique(array $array, callable $uniqueFn)
    {
        $unique = [];

        foreach ($array as $value) {
            $unique[$uniqueFn($value)] = $value;
        }

        return array_values($unique);
    }

    /**
     * Breaks path keys into sub-arrays, creating a tree from a flat list.
     *
     * Example:
     * ```
     * Arrays::breakPaths([
     *      'test/one' => 1,
     *      'test/two' => 2,
     *      'very/deep/key' => 'hello',
     *      'foo' => 'bar'
     * ])
     *
     * // Gives:
     *
     * [
     *      'test' => [
     *          'one' => 1,
     *          'two' => 2,
     *      ],
     *      'very' => [
     *          'deep' => [
     *              'key' => 'hello'
     *          ]
     *      ],
     *      'foo' => 'bar'
     * ]
     * ```
     *
     * @since 0.1
     *
     * @param array  $array     The array to break
     * @param string $delimiter The path delimiter.
     *
     * @return array
     */
    static function breakPaths(array $array, string $delimiter)
    {
        $result = [];

        static::each($array, function ($value, $key) use (&$result, $delimiter) {
            static::setPath($result, explode($delimiter, $key), $value);
        });

        return $result;
    }

    /**
     * Flattens an array tree into a flat list with path keys.
     *
     * Example:
     * ```
     * Arrays::flattenPaths([
     *      'test' => [
     *          'one' => 1,
     *          'two' => 2,
     *      ],
     *      'very' => [
     *          'deep' => [
     *              'key' => 'hello'
     *          ]
     *      ],
     *      'foo' => 'bar'
     * ])
     *
     * // Gives:
     *
     * [
     *      'test/one' => 1,
     *      'test/two' => 2,
     *      'very/deep/key' => 'hello',
     *      'foo' => 'bar'
     * ]
     * ```
     *
     * @since 0.1
     *
     * @param array  $array     The array to flatten
     * @param string $delimiter The path delimiter.
     *
     * @return array
     */
    static function flattenPaths(array $array, string $delimiter, array $path = [])
    {
        $result = [];

        static::each($array, function ($value, $key) use (&$result, $delimiter, &$path) {
            array_push($path, $key);
            $pathStr = implode('/', $path);

            $newValues = is_array($value)
                ? static::flattenPaths($value, $delimiter, $path)
                : [$pathStr => $value];

            $result = array_merge($result, $newValues);

            array_pop($path);
        });

        return $result;
    }

    /**
     * Paginates an array.
     *
     * @since 0.1
     *
     * @param array $array The array to paginate.
     * @param int   $page The page to return.
     * @param int   $perPage The number of items per page.
     *
     * @return array An array containing the following:
     *               1. The resulting paginated array
     *               2. The total number of items in the array prior to paginating
     *               3. The total number of available pages
     */
    static function paginate(array $array, int $page, int $perPage)
    {
        $total = count($array);

        if ($total === 0) {
            return [[], 0, 0];
        }

        $perPage = max(0, min($total, $perPage));

        $numPages = ceil($total / $perPage);
        $page = max(1, min($page, $numPages));

        return [
            array_slice($array, ($page - 1) * $perPage, $perPage),
            $total,
            $numPages,
        ];
    }

    /**
     * Shuffles an array into random order.
     *
     * @since 0.5
     *
     * @param array $array The array to shuffle.
     *
     * @return array A shuffled version of the array.
     */
    static function shuffle(array $array)
    {
        $count = count($array);
        // If empty or only 1 element, do nothing
        if ($count < 2) {
            return $array;
        }

        // Iterate backwards
        $currIdx = $count - 1;
        while ($currIdx !== 0) {
            // Pick a random element
            $randIdx = rand(0, $currIdx - 1);

            // Swap with current
            $temp = $array[$currIdx];
            $array[$currIdx] = $array[$randIdx];
            $array[$randIdx] = $temp;

            // Move to previous element in the list
            $currIdx--;
        }

        return $array;
    }

    /**
     * Picks a subset of elements from an array, by keys.
     *
     * @since 0.6
     *
     * @param array $array The array.
     * @param array $keys  The keys to pick.
     *
     * @return array An array containing all elements from the original $array whose keys are present in the given
     *               $keys param.
     */
    static function pickKeys(array $array, array $keys)
    {
        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $result[$key] = $array[$key];
            }
        }

        return $result;
    }
}
