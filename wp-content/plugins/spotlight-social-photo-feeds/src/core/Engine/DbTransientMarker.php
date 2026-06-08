<?php

namespace RebelCode\Spotlight\Instagram\Engine;

use RebelCode\Iris\Utils\Marker;

class DbTransientMarker implements Marker
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $expiry;

    /**
     * @param string $name The name of the transient.
     * @param int $expiry The number of seconds before the transient expires.
     */
    public function __construct(string $name, int $expiry = 0)
    {
        $this->name = $name;
        $this->expiry = $expiry;
    }

    /** @inheritDoc */
    public function create(): void
    {
        set_transient($this->name, '1', $this->expiry);
    }

    /** @inheritDoc */
    public function isSet(): bool
    {
        return filter_var(get_transient($this->name), FILTER_VALIDATE_BOOLEAN);
    }

    /** @inheritDoc */
    public function delete(): void
    {
        delete_transient($this->name);
    }
}
