<?php

declare(strict_types=1);

namespace RebelCode\Iris\Data;

/** @psalm-immutable */
class Feed extends ImmutableDataObject
{
    /** @var int|string */
    public $id;

    /** @var Source[] */
    public $sources;

    /**
     * @inheritDoc
     *
     * @param int|string $id
     * @param Source[] $sources
     */
    public function __construct($id, array $sources, array $data)
    {
        parent::__construct($data);
        $this->id = $id;
        $this->sources = $sources;
    }
}
