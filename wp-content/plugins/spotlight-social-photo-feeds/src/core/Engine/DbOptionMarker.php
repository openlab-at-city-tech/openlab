<?php

namespace RebelCode\Spotlight\Instagram\Engine;

use RebelCode\Iris\Utils\Marker;

class DbOptionMarker implements Marker
{
    /** @var string */
    protected $option;

    /** @var bool */
    protected $autoload;

    /**
     * @param string $option
     * @param bool $autoload
     */
    public function __construct(string $option, bool $autoload = false)
    {
        $this->option = $option;
        $this->autoload = $autoload;
    }

    /** @inheritDoc */
    public function create(): void
    {
        update_option($this->option, '1', $this->autoload);
    }

    /** @inheritDoc */
    public function isSet(): bool
    {
        return filter_var(get_option($this->option, false), FILTER_VALIDATE_BOOLEAN);
    }

    /** @inheritDoc */
    public function delete(): void
    {
        delete_option($this->option);
    }
}
