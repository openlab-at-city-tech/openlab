<?php

declare (strict_types=1);
namespace RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache;

use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;
/**
 * Creates cache pools that throw only PSR-legal exceptions.
 */
class SilentPoolFactory implements CachePoolFactoryInterface
{
    /**
     * @var CachePoolFactoryInterface
     */
    protected $factory;
    /**
     * @param CachePoolFactoryInterface $factory A factory of possibly non-compliant cache pools.
     */
    public function __construct(CachePoolFactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    /**
     * @inheritDoc
     */
    public function createCachePool(string $poolName): CacheInterface
    {
        return new SilentPool($this->factory->createCachePool($poolName));
    }
}