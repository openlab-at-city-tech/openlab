<?php

namespace SimpleCalendar\plugin_deps\GuzzleHttp\Promise;

final class Is
{
    /**
     * Returns true if a promise is pending.
     *
     * @return bool
     */
    public static function pending(\SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled or rejected.
     *
     * @return bool
     */
    public static function settled(\SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() !== \SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled.
     *
     * @return bool
     */
    public static function fulfilled(\SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface::FULFILLED;
    }
    /**
     * Returns true if a promise is rejected.
     *
     * @return bool
     */
    public static function rejected(\SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \SimpleCalendar\plugin_deps\GuzzleHttp\Promise\PromiseInterface::REJECTED;
    }
}
