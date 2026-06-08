<?php

namespace RebelCode\Spotlight\Instagram\Config;

/**
 * A config entry implementation that does nothing.
 *
 * @see ConfigService
 */
class NullConfigEntry implements ConfigEntry
{
    /** @inheritDoc */
    public function getValue()
    {
        return null;
    }

    /** @inheritDoc */
    public function setValue($value)
    {
    }
}
