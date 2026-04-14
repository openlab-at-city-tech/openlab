<?php


namespace InstagramFeed\Vendor\DI;

use InstagramFeed\Vendor\Psr\Container\ContainerExceptionInterface;
/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
