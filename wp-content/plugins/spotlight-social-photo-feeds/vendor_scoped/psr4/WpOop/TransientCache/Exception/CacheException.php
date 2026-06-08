<?php

declare (strict_types=1);
namespace RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\Exception;

use Exception;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheException as CacheExceptionInterface;
/**
 * @inheritDoc
 */
class CacheException extends Exception implements CacheExceptionInterface
{
}