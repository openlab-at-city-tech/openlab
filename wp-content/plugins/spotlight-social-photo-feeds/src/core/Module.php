<?php

namespace RebelCode\Spotlight\Instagram;

use Psr\Container\ContainerInterface;

/**
 * Padding layer for modules.
 */
abstract class Module implements ModuleInterface
{
    public function run(ContainerInterface $c): void { }

    public function getExtensions(): array { return []; }

    public function getFactories(): array { return []; }
}
