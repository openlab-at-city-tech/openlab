<?php

namespace SimpleCalendar\plugin_deps\Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(\SimpleCalendar\plugin_deps\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
