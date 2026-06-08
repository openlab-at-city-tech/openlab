<?php

declare (strict_types=1);
namespace RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache;

use DateInterval;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;
use wpdb;
use function uniqid;
/**
 * @inheritDoc
 */
class CachePoolFactory implements CachePoolFactoryInterface
{
    /**
     * @var wpdb
     */
    protected $wpdb;
    /**
     * @var int|DateInterval
     */
    protected $defaultTtl;
    /**
     * @param wpdb $wpdb       The WP database adapter.
     * @param int  $defaultTtl The TTL to use if no TTL is supplied at consumption time.
     */
    public function __construct(wpdb $wpdb, $defaultTtl = 0)
    {
        $this->wpdb = $wpdb;
        $this->defaultTtl = $defaultTtl;
    }
    /**
     * @inheritDoc
     */
    public function createCachePool(string $poolName): CacheInterface
    {
        $default = uniqid('default');
        $pool = new CachePool($this->wpdb, $poolName, $default, $this->defaultTtl);
        return $pool;
    }
}