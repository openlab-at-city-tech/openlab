<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Exception;

use RebelCode\WordPress\Http\Middleware\HttpErrorsToExceptions;

/**
 * An exception that is thrown by the {@link HttpErrorsToExceptions} middleware for 5xx responses.
 */
class ServerErrorException extends BadResponseException
{
}
