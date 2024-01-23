<?php

declare (strict_types=1);
namespace SimpleCalendar\plugin_deps\GuzzleHttp\Promise;

/**
 * Exception that is set as the reason for a promise that has been cancelled.
 * @internal
 */
class CancellationException extends RejectionException
{
}
