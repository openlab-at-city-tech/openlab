<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Exceptions;

use RuntimeException;

/**
 * Thrown when a response body cannot be decoded into the expected JSON structure.
 */
final class DecodingException extends RuntimeException
{
}
