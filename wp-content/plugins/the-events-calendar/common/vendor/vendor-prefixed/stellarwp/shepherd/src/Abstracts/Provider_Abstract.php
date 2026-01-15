<?php

/**
 * The base service provider class.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Abstracts;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface as Container;
// phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
/**
 * Class Provider_Abstract
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts
 */
abstract class Provider_Abstract
{
    /**
     * Whether the service provider will be a deferred one or not.
     *
     * @var bool
     */
    protected $deferred = false;
    /**
     * @var Container
     */
    protected $container;
    /**
     * ServiceProvider constructor.
     *
     * @param Container $container The container instance.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    /**
     * Whether the service provider will be a deferred one or not.
     *
     * @return bool
     */
    public function isDeferred()
    {
        return $this->deferred;
    }
    /**
     * Returns an array of the class or interfaces bound and provided by the service provider.
     *
     * @return array<string> A list of fully-qualified implementations provided by the service provider.
     */
    public function provides()
    {
        return [];
    }
    /**
     * Binds and sets up implementations at boot time.
     *
     * @return void The method will not return any value.
     */
    public function boot()
    {
    }
    /**
     * Registers the service provider bindings.
     *
     * @return void The method does not return any value.
     */
    abstract public function register();
}