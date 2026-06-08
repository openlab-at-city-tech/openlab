<?php

declare(strict_types=1);

namespace RebelCode\Iris\Data;

/** @psalm-immutable */
class ImmutableDataObject
{
    /** @var array<string, mixed> */
    public $data = [];

    /**
     * Constructor.
     *
     * @param array<string, mixed> $data The data map.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Retrieves a data entry.
     *
     * @param string $key The key of the data entry to retrieve.
     * @param mixed|null $default The value to return if no data entry exists for the given key.
     *
     * @return mixed|null The value of the data entry for the given key.
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Creates a copy instance with a different data key.
     *
     * @param string $key The key of the data entry to change.
     * @param mixed $value The new value of the data entry with the given key.
     *
     * @return static The mutated instance.
     */
    public function with(string $key, $value): self
    {
        if (array_key_exists($key, $this->data) && $this->data[$key] === $value) {
            return $this;
        }

        $clone = clone $this;
        $clone->data[$key] = $value;

        return $clone;
    }

    /**
     * Creates a copy instance with a data changes.
     *
     * @param array<string, mixed> $changes The data changes to apply.
     *
     * @return static The mutated instance.
     */
    public function withChanges(array $changes): self
    {
        if (empty($changes)) {
            return $this;
        }

        $newData = array_merge($this->data, $changes);

        if ($newData === $this->data) {
            return $this;
        }

        $clone = clone $this;
        $clone->data = $newData;

        return $clone;
    }
}
