<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleCalendar\plugin_deps\Monolog\Handler;

use SimpleCalendar\plugin_deps\Monolog\Processor\ProcessorInterface;
/**
 * Interface to describe loggers that have processors
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface ProcessableHandlerInterface
{
    /**
     * Adds a processor in the stack.
     *
     * @psalm-param ProcessorInterface|callable(array): array $callback
     *
     * @param  ProcessorInterface|callable $callback
     * @return HandlerInterface            self
     */
    public function pushProcessor(callable $callback) : \SimpleCalendar\plugin_deps\Monolog\Handler\HandlerInterface;
    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @psalm-return callable(array): array
     *
     * @throws \LogicException In case the processor stack is empty
     * @return callable
     */
    public function popProcessor() : callable;
}
