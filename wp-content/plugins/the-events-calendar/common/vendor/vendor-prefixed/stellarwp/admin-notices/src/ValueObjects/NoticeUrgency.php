<?php

namespace TEC\Common\StellarWP\AdminNotices\ValueObjects;

use InvalidArgumentException;

/**
 * A value object representing the urgency of a notice.
 *
 * @since 1.0.0
 */
class NoticeUrgency
{
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';
    const SUCCESS = 'success';

    /**
     * @var string
     */
    private $urgency;

    /**
     * @since 1.0.0
     */
    public static function info(): self
    {
        return new self(self::INFO);
    }

    /**
     * @since 1.0.0
     */
    public static function warning(): self
    {
        return new self(self::WARNING);
    }

    /**
     * @since 1.0.0
     */
    public static function error(): self
    {
        return new self(self::ERROR);
    }

    /**
     * @since 1.0.0
     */
    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    /**
     * @since 1.0.0
     */
    public function __construct(string $urgency)
    {
        if (!in_array($urgency, [self::INFO, self::WARNING, self::ERROR, self::SUCCESS])) {
            throw new InvalidArgumentException('Invalid urgency');
        }

        $this->urgency = $urgency;
    }

    /**
     * @since 1.0.0
     */
    public function __toString(): string
    {
        return $this->urgency;
    }
}
