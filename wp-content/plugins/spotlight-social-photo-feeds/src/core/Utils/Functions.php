<?php

namespace RebelCode\Spotlight\Instagram\Utils;

/**
 * Utility functions for dealing with functions.
 *
 * @since 0.5
 */
class Functions
{
    /**
     * Used during partial application (see {@link Func::apply()}) to denote skipped arguments.
     *
     * @since 0.5
     */
    const SKIP = '__$' . __CLASS__ . '$SKIP_ARG$__';

    /**
     * @since 0.5
     *
     * @var callable|null
     */
    protected $callable;

    /**
     * @since 0.5
     *
     * @var array
     */
    protected $args;

    /**
     * Constructor.
     *
     * @since 0.5
     *
     * @param callable|null $callable The callable, or null for a noop function.
     * @param array         $args     Optional pre-applied arguments.
     */
    protected function __construct(?callable $callable = null, array $args = [])
    {
        $this->callable = $callable;
        $this->args = $args;
    }

    /**
     * Invokes the function.
     *
     * @since 0.5
     *
     * @param mixed ...$args The arguments.
     *
     * @return mixed The return value.
     */
    public function __invoke(...$args)
    {
        return static::call($this->callable, static::mergeArgs($this->args, $args));
    }

    /**
     * Creates a no-operation function.
     *
     * @since 0.5
     *
     * @return callable
     */
    public static function noop() : callable
    {
        return new static();
    }

    /**
     * Creates a function that simply returns one of its args.
     *
     * @since 0.5
     *
     * @param int $arg The argument to return, as a 0-based index.
     *
     * @return callable
     */
    public static function returnArg(int $arg = 0) : callable
    {
        $arg = max($arg, 0);

        return function () use ($arg) {
            return func_get_arg($arg);
        };
    }

    /**
     * Creates a function that returns a given value.
     *
     * @since 0.5
     *
     * @param mixed $value The value to return.
     *
     * @return callable The created function.
     */
    public static function thatReturns($value) : callable
    {
        return function () use ($value) {
            return $value;
        };
    }

    /**
     * Partially applies a function with some arguments.
     *
     * @since 0.5
     *
     * @param callable $fn   The function to partially apply arguments to.
     * @param array    $args The arguments to apply. Include {@link Func::SKIP} to skip arguments positionally.
     *
     * @return callable The partially applied function.
     */
    public static function apply(callable $fn, array $args) : callable
    {
        return ($fn instanceof static && $fn->callable !== null)
            ? new static($fn->callable, static::mergeArgs($fn->args, $args))
            : new static($fn, $args);
    }

    /**
     * Merges the given functions into a single function.
     *
     * When the resulting merged function is invoked, the given functions are invoked in the given order and are passed
     * the full set of invocation arguments.
     *
     * No value is returned from the merged function. For returning values, use {@link Func::pipe()}.
     *
     * @since 0.5
     *
     * @see   Func::pipe()
     *
     * @param iterable<callable> $functions
     *
     * @return callable The merged function.
     */
    public static function merge(iterable $functions) : callable
    {
        return function (...$args) use ($functions) {
            foreach ($functions as $function) {
                if (is_callable($function)) {
                    static::call($function, $args);
                }
            }
        };
    }

    /**
     * Merges the given functions into a single function and returns the value from the last function.
     *
     * When the resulting merged function is invoked, the given functions are invoked in the given order and are passed
     * the full set of invocation arguments, with the exception of the first argument, which will be the return value
     * of the previous function. The first function's first argument will be the first invocation argument.
     *
     * The merged function will return the last function's return value.
     *
     * @since 0.5
     *
     * @param iterable<callable> $functions
     *
     * @return callable The piping function.
     */
    public static function pipe(iterable $functions) : callable
    {
        return function (...$args) use ($functions) {
            foreach ($functions as $function) {
                if (is_callable($function)) {
                    $args[0] = static::call($function, $args);
                }
            }

            return $args[0];
        };
    }

    /**
     * Decorates a function to handle thrown exceptions using another function.
     *
     * @since 0.5
     *
     * @param callable      $fn      The function that is expected to throw exceptions.
     * @param string[]      $catch   The fully qualified name of the exception class to catch.
     * @param callable|null $handler The function to call when an exception is thrown by $fn. Receives the thrown
     *                               exception as the first argument, together with all the arguments that were
     *                               originally passed to $fn.
     *
     * @return callable The decorated function.
     */
    public static function catch(callable $fn, array $catch = [Exception::class], ?callable $handler = null) : callable
    {
        return function (...$args) use ($fn, $catch, $handler) {
            try {
                return static::call($fn, $args);
            } catch (Exception $excInstance) {
                if (in_array(get_class($excInstance), $catch)) {
                    return static::call($handler, array_merge([$excInstance], $args));
                }

                throw $excInstance;
            }
        };
    }

    /**
     * Memoizes a function's results such that subsequent calls with the same arguments skip invocation altogether.
     *
     * @since 0.5
     *
     * @param callable $fn The function to memoize.
     *
     * @return callable The memoizing function.
     */
    public static function memoize(callable $fn) : callable
    {
        return function (...$args) use ($fn) {
            static $cache = [];

            $key = static::hashArgs($args);

            if (!array_key_exists($key, $cache)) {
                $cache[$key] = static::call($fn, $args);
            }

            return $cache[$key];
        };
    }

    /**
     * Decorates a function such that its arguments are mapped to new values prior to invocation.
     *
     * @since 0.5
     *
     * @param callable $fn    The function to decorate.
     * @param callable $mapFn The mapping function, which should accept a value as argument and return the new value.
     *
     * @return callable The decorated function.
     */
    public static function mapArgs(callable $fn, callable $mapFn) : callable
    {
        return function (...$args) use ($fn, $mapFn) {
            return static::call($fn, array_map($mapFn, $args));
        };
    }

    /**
     * Reorders a function's arguments.
     *
     * Important: the given $order is processed sequentially in the order they are given. When one ordering is
     * processed, the arguments list is re-indexed. Therefore, each reordering should take previous positional
     * changes into account.
     *
     * **For Example**:
     *
     * Consider arguments [d, a, c, b].
     *
     * An ordering of [0 => 2, 2 => 1] will move `d` after `c` and then move `d` (now at position 2) after `a`, yielding
     * [a, d, c, d].
     *
     * To obtain [a, b, c, d], and ordering of [0 => 2, 3 => 1] can be used.
     *
     * @since 0.5
     *
     * @param callable $fn         The function whose arguments to reorder.
     * @param array    $reordering The reordering, as a mapping of argument indices (starting at zero) to the desired
     *                             new position.
     *
     * @return callable The function with reordered arguments.
     */
    public static function reorderArgs(callable $fn, array $reordering) : callable
    {
        return function (...$args) use ($fn, $reordering) {
            $numArgs = count($args);
            foreach ($reordering as $oldIdx => $newIdx) {
                $oldIdx = max(min($numArgs, $oldIdx), 0);
                $newIdx = max(min($numArgs, $newIdx), 0);

                if (!array_key_exists($oldIdx, $args)) {
                    continue;
                }

                $arg = array_splice($args, $oldIdx, 1);
                array_splice($args, $newIdx, 0, $arg);
                $args = array_values($args);
            }

            return static::call($fn, $args);
        };
    }

    /**
     * Captures the output of a function to return it instead.
     *
     * @since 0.5
     *
     * @param callable $fn The function whose output should be captured.
     *
     * @return callable A function that returns a string containing the original function's output.
     */
    public static function capture(callable $fn) : callable
    {
        return function (...$args) use ($fn) {
            ob_start();

            static::call($fn, $args);

            return ob_get_clean();
        };
    }

    /**
     * Outputs a function's return value.
     *
     * @since 0.5
     *
     * @param callable $fn The function whose return value should be outputted.
     *
     * @return callable A function that outputs the original function's return value.
     */
    public static function output(callable $fn) : callable
    {
        return function (...$args) use ($fn) {
            echo static::call($fn, $args);
        };
    }

    /**
     * Negates a function's return values.
     *
     * @since 0.6
     *
     * @param callable $fn The function whose return values will be negated.
     *
     * @return callable A function that calls the given $fn and returns the negation of its returned values.
     */
    public static function not(callable $fn): callable
    {
        return function (...$args) use ($fn) {
            return !static::call($fn, $args);
        };
    }

    /**
     * Creates a function that calls a method on the first argument, while passing any remaining arguments.
     *
     * @since 0.5
     *
     * @param string $method The name of the method to call on the first argument.
     *
     * @return callable The created function.
     */
    public static function method(string $method) : callable
    {
        return function (...$args) use ($method) {
            if (count($args) === 0) {
                throw new BadFunctionCallException('At least one argument is required');
            }

            $object = array_shift($args);

            return static::call([$object, $method], $args);
        };
    }

    /**
     * Creates a function that returns the value of a specific property from its object argument.
     *
     * @param string $property The name of the property whose value to return.
     *
     * @return callable The created function.
     */
    public static function property(string $property): callable
    {
        return function ($arg) use ($property) {
            return $arg->{$property};
        };
    }

    /**
     * Creates a function that returns the value of a specific index from its array argument.
     *
     * @param string $idx The index whose value to return.
     *
     * @return callable The created function.
     */
    public static function index(string $idx): callable
    {
        return function ($arg) use ($idx) {
            return $arg[$idx];
        };
    }

    /**
     * Merges two lists of arguments.
     *
     * This function will attempt to insert the arguments from the second list into skipped arguments in the first
     * list. Any arguments from the second list that are inserted this way will be appended to the end of the argument
     * list.
     *
     * @since 0.5
     *
     * @param array $args1 The first list of arguments.
     * @param array $args2 The second list of arguments.
     *
     * @return array The merged list of arguments.
     */
    protected static function mergeArgs(array $args1, array $args2)
    {
        $replaceSkipped = function ($arg) use (&$args2, &$args1) {
            if ($arg === static::SKIP && !empty($args2)) {
                return array_shift($args2);
            }

            return $arg;
        };

        $replacedArgs = array_map($replaceSkipped, $args1);

        return !empty($args2)
            ? array_merge($replacedArgs, $args2)
            : $replacedArgs;
    }

    /**
     * Calls a function with a given list of arguments.
     *
     * This function will make sure to strip out any skipped arguments, as well as spread the list of arguments
     * appropriately based on argument index.
     *
     * @since 0.5
     *
     * @param callable|null $callable The callable to call.
     * @param array         $args     Optional list of arguments.
     *
     * @return mixed The return value.
     */
    protected static function call(?callable $callable, array $args = [])
    {
        if (!is_callable($callable)) {
            return null;
        }

        $filteredArgs = array_filter($args, function ($arg) {
            return $arg !== static::SKIP;
        });

        return ($callable)(...$filteredArgs);
    }

    /**
     * Hashes a given list of arguments.
     *
     * @since 0.5
     *
     * @param array $args The arguments to hash.
     *
     * @return string A hash string that uniquely identifies the argument values (but not the argument references).
     */
    protected static function hashArgs(array $args)
    {
        return sha1(print_r(array_map([__CLASS__, 'prepareArgForHash'], $args), true));
    }

    /**
     * Prepares an argument value for hashing.
     *
     * @since 0.5
     * @see   Func::hashArgs()
     *
     * @param mixed $arg The argument value.
     *
     * @return string A string that uniquely represents the **value** (not the reference) of the argument.
     */
    protected static function prepareArgForHash($arg) : string
    {
        if (is_array($arg)) {
            return static::prepareArgForHash($arg);
        }

        if (is_object($arg)) {
            return get_class($arg) . '_' . spl_object_hash($arg);
        }

        return (string) $arg;
    }
}
