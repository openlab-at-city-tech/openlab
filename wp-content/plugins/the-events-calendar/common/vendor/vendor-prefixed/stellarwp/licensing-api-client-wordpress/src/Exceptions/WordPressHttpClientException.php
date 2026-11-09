<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClientWordPress\Exceptions;

use TEC\Common\Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

/**
 * Represents a transport failure reported by WordPress HTTP APIs.
 */
final class WordPressHttpClientException extends RuntimeException implements ClientExceptionInterface
{
}
