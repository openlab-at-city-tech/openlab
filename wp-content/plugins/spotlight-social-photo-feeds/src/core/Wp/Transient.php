<?php

namespace RebelCode\Spotlight\Instagram\Wp;

/**
 * Represents a WordPress transient in an immutable struct-like form.
 *
 * @since 0.1
 */
class Transient
{
    /**
     * @since 0.1
     *
     * @var string
     */
    protected $key;

    /**
     * @since 0.1
     *
     * @var int
     */
    protected $expiry;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string $key    The transient's key.
     * @param int    $expiry The timestamp for when the transient expires.
     */
    public function __construct(string $key, int $expiry)
    {
        $this->key = $key;
        $this->expiry = $expiry;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * @since 0.1
     *
     * @return int
     */
    public function getExpiry() : int
    {
        return $this->expiry;
    }

    /**
     * Retrieves the value of a transient.
     *
     * @since 0.1
     *
     * @param Transient $transient The transient instance.
     * @param mixed     $default   The value to return if the transient is not set.
     *
     * @return mixed
     */
    public static function getValue(Transient $transient, $default = null)
    {
        $value = get_transient($transient->key);

        return ($value === false) ? $default : $value;
    }

    /**
     * Sets a value to a transient.
     *
     * @since 0.1
     *
     * @param Transient $transient The transient instance.
     * @param mixed     $value     The value to set to the transient.
     *
     * @return bool True on success, false on failure.
     */
    public static function setValue(Transient $transient, $value)
    {
        return set_transient($transient->key, $value, $transient->expiry);
    }
}
