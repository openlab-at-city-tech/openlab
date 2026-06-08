<?php

declare(strict_types=1);

namespace RebelCode\Iris\Data;

/** @psalm-immutable */
class Item extends ImmutableDataObject
{
    /** @var string */
    public $id;

    /** @var int|string|null */
    public $localId;

    /** @var Source[] */
    public $sources;

    /**
     * Constructor.
     *
     * @param string $id An ID that uniquely identifies the item from other items from the same source.
     * @param int|string|null $localId The ID of the item in local storage.
     * @param Source[] $sources The sources from which the item was fetched.
     * @param array<string, mixed> $data The data for this item.
     */
    public function __construct(string $id, $localId, array $sources, array $data = [])
    {
        parent::__construct($data);
        $this->id = $id;
        $this->localId = $localId;
        $this->sources = array_values($sources);
    }

    /**
     * @param int|string|null $localId
     */
    public function withLocalId($localId): Item
    {
        if ($this->localId === $localId) {
            return $this;
        }

        $clone = clone $this;
        $clone->localId = $localId;

        return $clone;
    }

    /**
     * @param Source[] $sources
     */
    public function withSources(array $sources): Item
    {
        $sources = array_values($sources);

        if ($this->sources === $sources) {
            return $this;
        }

        $clone = clone $this;
        $clone->sources = $sources;

        return $clone;
    }

    /**
     * @param Source[] $sources
     */
    public function withAddedSources(array $sources): Item
    {
        $newSources = [];
        foreach (array_merge($this->sources, $sources) as $source) {
            $newSources[(string) $source] = $source;
        }

        return $this->withSources($newSources);
    }
}
