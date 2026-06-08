<?php

declare (strict_types=1);
namespace RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache;

use Exception;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionInterface;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;
/**
 * Wraps a pool that throws wrong exceptions in a 100% PSR-compliant one by hiding exceptions :(
 *
 * Homage to excellent SilentPool gin.
 */
class SilentPool implements CacheInterface
{
    /**
     * @var CacheInterface
     */
    protected $cache;
    /**
     * @param CacheInterface $cache A possibly non-compliant cache pool.
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        try {
            return $this->cache->get($key);
        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentExceptionInterface) {
                throw $e;
            }
            return $default;
        }
    }
    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        try {
            return $this->cache->set($key, $value, $ttl);
        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentExceptionInterface) {
                throw $e;
            }
            return false;
        }
    }
    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        try {
            return $this->cache->delete($key);
        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentExceptionInterface) {
                throw $e;
            }
            return false;
        }
    }
    /**
     * @inheritDoc
     */
    public function clear()
    {
        try {
            return $this->cache->clear();
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        try {
            return $this->cache->getMultiple($keys, $default);
        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentExceptionInterface) {
                throw $e;
            }
            return [];
        }
    }
    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        try {
            return $this->cache->setMultiple($values, $ttl);
        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentExceptionInterface) {
                throw $e;
            }
            return false;
        }
    }
    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        try {
            return $this->cache->deleteMultiple($keys);
        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentExceptionInterface) {
                throw $e;
            }
            return false;
        }
    }
    /**
     * @inheritDoc
     */
    public function has($key)
    {
        try {
            return $this->cache->has($key);
        } catch (Exception $e) {
            if ($e instanceof InvalidArgumentExceptionInterface) {
                throw $e;
            }
            return false;
        }
    }
}