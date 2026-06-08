<?php

declare(strict_types=1);

namespace RebelCode\Iris\Utils;

/**
 * A marker implementation that does nothing.
 */
class NullMarker implements Marker
{
    /** @var bool */
    protected $isSet;

    /**
     * Constructor.
     *
     * @param bool $isSet Whether the marker reports as being set or not.
     */
    public function __construct(bool $isSet = false)
    {
        $this->isSet = $isSet;
    }

    public function create(): void
    {
    }

    public function isSet(): bool
    {
        return $this->isSet;
    }

    public function delete(): void
    {
    }
}
