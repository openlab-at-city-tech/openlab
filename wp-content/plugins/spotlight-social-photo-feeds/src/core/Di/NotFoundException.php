<?php

namespace RebelCode\Spotlight\Instagram\Di;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Implementation of an exception when a service is not found in a DI container.
 *
 * @since 0.1
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
