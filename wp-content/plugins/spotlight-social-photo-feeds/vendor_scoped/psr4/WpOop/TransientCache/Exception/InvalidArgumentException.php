<?php

declare (strict_types=1);
namespace RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\Exception;

use InvalidArgumentException as NativeInvalidArgumentException;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;
/**
 * @inheritDoc
 */
class InvalidArgumentException extends NativeInvalidArgumentException implements PsrInvalidArgumentException
{
}