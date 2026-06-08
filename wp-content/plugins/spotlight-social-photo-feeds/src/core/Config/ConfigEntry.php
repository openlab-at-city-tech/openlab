<?php

namespace RebelCode\Spotlight\Instagram\Config;

/**
 * Represents a single config entry.
 *
 * A config entry is an entity that controls where and how a certain config value is stored or read from. This is
 * used in conjunction with {@link ConfigSet} to provide configuration that is customizable at a per-entry level.
 *
 * @since 0.1
 * @see   ConfigSet
 */
interface ConfigEntry
{
    /**
     * Retrieves the value for this config entry.
     *
     * @since 0.1
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Sets the value for this config entry.
     *
     * @since 0.1
     *
     * @param mixed $value The value to set.
     */
    public function setValue($value);
}
