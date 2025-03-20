<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

class WPCACollection implements IteratorAggregate, Countable
{
    /** @var array  */
    private $items;

    /**
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function add($value)
    {
        //backwards compat with $value,$key signature
        $args = func_get_args();
        if (count($args) === 2) {
            list($value2, $key) = $args;
            if (!$this->has($key)) {
                $this->put($key, $value2);
            }
            return $this;
        }

        $this->items[] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function put($key, $value)
    {
        $this->items[$key] = $value;
        return $this;
    }

    public function set($value, $key)
    {
        _deprecated_function(__METHOD__, '2.0');
        $this->put($key, $value);
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove($key)
    {
        unset($this->items[$key]);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->items[$key]);
    }

    /**
     * @param string $key
     * @param mixed|null $default_value
     *
     * @return mixed|null
     */
    public function get($key, $default_value = null)
    {
        return $this->has($key) ? $this->items[$key] : $default_value;
    }

    /**
     * @return  array
     */
    public function all()
    {
        return $this->items;
    }

    public function get_all()
    {
        _deprecated_function(__METHOD__, '2.0');
        return $this->all();
    }

    public function set_all($items)
    {
        _deprecated_function(__METHOD__, '2.0');
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function filter($callback)
    {
        if (!is_callable($callback)) {
            return $this;
        }

        return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return bool
     */
    public function is_empty()
    {
        return empty($this->items);
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
