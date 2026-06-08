<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram;

use Psr\Container\ContainerInterface;

interface ModuleInterface
{
    /**
     * Runs the module.
     *
     * @param ContainerInterface $c A services container instance.
     */
    public function run(ContainerInterface $c);

    /**
     * Returns a list of all container entries registered by this module.
     *
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     *
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     *
     * @return callable[]
     */
    public function getFactories();

    /**
     * Returns a list of all container entries extended by this module.
     *
     * - the key is the entry name
     * - the value is a callable that will return the modified entry
     *
     * Callables have the following signature:
     *        function(Psr\Container\ContainerInterface $container, $previous)
     *     or function(Psr\Container\ContainerInterface $container, $previous = null)
     *
     * About factories parameters:
     *
     * - the container (instance of `Psr\Container\ContainerInterface`)
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null`
     * will be passed.
     *
     * @return callable[]
     */
    public function getExtensions();
}
