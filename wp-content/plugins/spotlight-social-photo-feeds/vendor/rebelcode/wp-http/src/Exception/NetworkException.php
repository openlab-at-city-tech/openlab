<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Exception;

use Psr\Http\Client\NetworkExceptionInterface;

/**
 * An exception that is thrown when a network connection cannot be established.
 */
class NetworkException extends RequestException implements NetworkExceptionInterface
{
}
