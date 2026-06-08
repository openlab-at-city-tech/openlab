<?php

namespace RebelCode\Atlas;

use Throwable;

/** @psalm-immutable */
class Query
{
    /** @var QueryTypeInterface */
    protected $type;

    /** @var array<string,mixed> */
    protected $data;

    /**
     * @param QueryTypeInterface $type The query type.
     * @param array<string,mixed> $data An associative array of query data.
     */
    public function __construct(QueryTypeInterface $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function getType(): QueryTypeInterface
    {
        return $this->type;
    }

    /** @return static */
    public function withType(QueryTypeInterface $type): self
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    /**
     * Retrieve a single entry from the query's data.
     *
     * @param string $key The key of the query data to retrieve.
     * @param mixed $default Optional default value to return if no query data corresponds with the given key.
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /** @return array<string,mixed> */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string,mixed> $data An associative array of query data.
     * @return static
     */
    public function withData(array $data): self
    {
        $clone = clone $this;
        $clone->data = $data;
        return $clone;
    }

    /**
     * @param array<string,mixed> $data An associative array of query data.
     * @return static
     */
    public function withAddedData(array $data): self
    {
        $clone = clone $this;
        $clone->data = array_merge($clone->data, $data);
        return $clone;
    }

    /**
     * @param list<string> $keys A list of data keys to omit.
     * @return static
     */
    public function withoutData(array $keys): self
    {
        $clone = clone $this;
        foreach ($keys as $key) {
            unset($clone->data[$key]);
        }
        return $clone;
    }

    /**
     * Compiles the query into a string.
     *
     * @return string
     */
    public function compile(): string
    {
        return $this->type->compile($this);
    }

    public function __toString(): string
    {
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            try {
                return $this->compile();
            } catch (Throwable $throwable) {
                return '';
            }
        } else {
            return $this->compile();
        }
    }
}
