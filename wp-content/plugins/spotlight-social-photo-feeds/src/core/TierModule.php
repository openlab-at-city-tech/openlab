<?php

namespace RebelCode\Spotlight\Instagram;

use Dhii\Services\Factories\Value;

class TierModule extends Module
{
    /** @var int */
    protected $tier;

    /** Constructor. */
    public function __construct(int $tier)
    {
        $this->tier = $tier;
    }

    /** @inheritDoc */
    public function getExtensions(): array
    {
        return [
            'plugin/tier' => new Value($this->tier),
        ];
    }
}
