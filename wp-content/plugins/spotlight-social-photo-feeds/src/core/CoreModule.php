<?php

namespace RebelCode\Spotlight\Instagram;

use Dhii\Services\Extension;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;

/**
 * The core module for the plugin that acts as a composite for all the other modules.
 *
 * @since 0.2
 */
class CoreModule extends Module
{
    /**
     * @since 0.2
     *
     * @var string
     */
    protected $pluginFile;

    /**
     * @since 0.2
     *
     * @var ModuleInterface[]
     */
    protected $modules;

    /**
     * @since 0.2
     *
     * @var Factory[]
     */
    protected $factories;

    /**
     * @since 0.2
     *
     * @var Extension[]
     */
    protected $extensions;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string            $pluginFile The path to the plugin file.
     * @param ModuleInterface[] $modules    The plugin's modules.
     */
    public function __construct(string $pluginFile, array $modules)
    {
        $this->pluginFile = $pluginFile;
        $this->modules = $modules;

        $this->compileServices();
    }

    /**
     * Retrieves the modules.
     *
     * @since 0.2
     *
     * @return ModuleInterface[] A list of module instances.
     */
    public function getModules() : array
    {
        return $this->modules;
    }

    /**
     * Retrieves the compiled services.
     *
     * @since 0.2
     *
     * @return array A tuple array with two entries: the factory and the extension maps.
     */
    public function getCompiledServices() : array
    {
        return [$this->factories, $this->extensions];
    }

    /**
     * @since 0.2
     *
     * @return Factory[]
     */
    public function getCoreFactories() : array
    {
        return [
            'plugin/core' => new Value($this),
            'plugin/version' => new Value(SL_INSTA_VERSION),
            'plugin/tier' => new Value(0),
            'plugin/modules' => new Value($this->modules),
            'plugin/file' => new Value($this->pluginFile),
            'plugin/dir' => new Value(dirname($this->pluginFile)),
            'plugin/url' => new Factory(['plugin/file'], function ($file) {
                return rtrim(plugin_dir_url($file), '\\/');
            }),
        ];
    }

    /**
     * @since 0.2
     *
     * @return Extension[]
     */
    public function getCoreExtensions() : array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFactories() : array
    {
        return $this->factories;
    }

    /**
     * @inheritDoc
     */
    public function getExtensions() : array
    {
        return $this->extensions;
    }

    /**
     * @inheritDoc
     */
    public function run(ContainerInterface $c): void
    {
        foreach ($this->modules as $module) {
            $module->run($c);
        }
    }

    /**
     * Compiles all the module services.
     *
     * @since 0.2
     */
    protected function compileServices()
    {
        $this->factories = $this->getCoreFactories();
        $this->extensions = $this->getCoreExtensions();

        foreach ($this->modules as $module) {
            $this->factories = array_merge($this->factories, $module->getFactories());
            $moduleExtensions = $module->getExtensions();

            if (empty($this->extensions)) {
                $this->extensions = $moduleExtensions;
                continue;
            }

            foreach ($moduleExtensions as $key => $extension) {
                if (!array_key_exists($key, $this->extensions)) {
                    $this->extensions[$key] = $extension;
                    continue;
                }

                $prevExtension = $this->extensions[$key];
                $this->extensions[$key] = function (ContainerInterface $c, $prev) use ($prevExtension, $extension) {
                    return $extension($c, $prevExtension($c, $prev));
                };
            }
        }
    }
}
