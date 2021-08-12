<?php

namespace SimpleCalendar\plugin_deps\Psr\Cache;

/**
 * Exception interface for invalid cache arguments.
 *
 * Any time an invalid argument is passed into a method it must throw an
 * exception class which implements Psr\Cache\InvalidArgumentException.
 */
interface InvalidArgumentException extends \SimpleCalendar\plugin_deps\Psr\Cache\CacheException
{
}
