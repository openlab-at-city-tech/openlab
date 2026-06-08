<?php

namespace RebelCode\Spotlight\Instagram\Di;

use Dhii\Services\Extension;
use Dhii\Services\Factory;
use LogicException;
use Psr\Container\ContainerInterface;

/**
 * The container implementation for the services provided by the plugin's modules.
 *
 * @since 0.1
 */
class Container implements ContainerInterface
{
    /**
     * @since 0.1
     *
     * @var FactoryInterface[]
     */
    protected $factories;

    /**
     * @since 0.1
     *
     * @var ExtensionInterface[]
     */
    protected $extensions;

    /**
     * @since 0.1
     *
     * @var array
     */
    protected $cache;

    /**
     * @since 0.1
     *
     * @var string[]
     */
    protected $filterPrefixes;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string[]    $filterPrefixes The prefixes to use for WordPress filters when services are created.
     * @param Factory[]   $factories      The service factories.
     * @param Extension[] $extensions     The service extensions.
     */
    public function __construct(array $filterPrefixes, array $factories, array $extensions)
    {
        $this->factories = $factories;
        $this->extensions = $extensions;
        $this->cache = [];
        $this->filterPrefixes = $filterPrefixes;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function get($id)
    {
        static $fetching = [];

        // Circular dependency detection
        if (isset($fetching[$id])) {
            $i = 0;
            $trace = array_map(function ($id) use (&$i) {
                return '#' . $i++ . ' ' . $id;
            }, array_reverse(array_keys($fetching)));

            $trace = implode("\n", $trace);

            throw new LogicException("Circular dependency detected for \"{$id}\", required by:\n{$trace}");
        }

        $fetching[$id] = true;

        try {
            return $this->actualGet($id);
        } finally {
            unset($fetching[$id]);
        }
    }

    /**
     * Internal method that performs the actual service resolution.
     *
     * @since 0.1
     *
     * @param string $key The service key.
     *
     * @return mixed The created service value.
     * @throws NotFoundException If no service with the given key was found.
     * @throws ContainerException If failed to create the service using the corresponding factory and extensions.
     */
    protected function actualGet($key)
    {
        if (!$this->has($key)) {
            throw new NotFoundException("Service \"$key\" does not exist");
        }

        if (!array_key_exists($key, $this->cache)) {
            try {
                $service = ($this->factories[$key])($this);

                if (array_key_exists($key, $this->extensions)) {
                    $service = ($this->extensions[$key])($this, $service);
                }
            } catch (ContainerException $exception) {
                throw new ContainerException(
                    "Failed to create service \"{$key}\". Reason: " . $exception->getMessage(),
                    null,
                    $exception
                );
            }

            foreach ($this->filterPrefixes as $prefix) {
                $this->cache[$key] = apply_filters($prefix . $key, $service);
            }
        }

        return $this->cache[$key];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->factories);
    }
}
