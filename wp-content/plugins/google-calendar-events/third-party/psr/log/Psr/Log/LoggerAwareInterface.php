<?php

namespace SimpleCalendar\plugin_deps\Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(\SimpleCalendar\plugin_deps\Psr\Log\LoggerInterface $logger);
}
