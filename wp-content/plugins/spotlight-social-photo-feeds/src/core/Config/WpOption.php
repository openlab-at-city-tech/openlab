<?php

namespace RebelCode\Spotlight\Instagram\Config;

/**
 * A config entry implementation that uses WordPress Options for storage.
 *
 * @since 0.1
 * @see   ConfigService
 */
class WpOption implements ConfigEntry
{
    const SANITIZE_BOOL = 1;
    const SANITIZE_INT = 2;

    /**
     * @since 0.1
     *
     * @var string
     */
    protected $key;

    /**
     * @since 0.1
     *
     * @var mixed
     */
    protected $default;

    /**
     * @var bool
     */
    protected $autoload;

    /** @var int */
    protected $sanitize;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string $key The option key.
     * @param mixed $default The default value for this option.
     * @param bool $autoload Whether to autoload the option or not.
     * @param int $sanitize Optional value sanitization type.
     */
    public function __construct(string $key, $default = null, bool $autoload = false, int $sanitize = 0)
    {
        $this->key = $key;
        $this->default = $default;
        $this->autoload = $autoload;
        $this->sanitize = $sanitize;
    }

    /**
     * Retrieves the key of the option.
     *
     * @since 0.1
     *
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * Retrieves the default value of the option.
     *
     * @since 0.1
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getValue()
    {
        $value = get_option($this->key, $this->default);

        switch ($this->sanitize) {
            case static::SANITIZE_BOOL:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case static::SANITIZE_INT:
                return (int) $value;
        }

        return $value;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function setValue($value)
    {
        switch ($this->sanitize) {
            case static::SANITIZE_BOOL:
                $value = (int) filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case static::SANITIZE_INT:
                $value = (int) $value;
                break;
        }

        update_option($this->key, $value, $this->autoload);
    }
}
