<?php

declare (strict_types=1);
namespace RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache;

use DateInterval;
use DateTimeImmutable;
use Exception;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;
use RangeException;
use RuntimeException;
use wpdb;
use RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\Exception\CacheException;
use RebelCode\Spotlight\Instagram\Vendor\WpOop\TransientCache\Exception\InvalidArgumentException;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionInterface;
use function is_int;
use function is_iterable;
use function is_null;
use function sprintf;
use function strlen;
use function strpos;
use function str_split;
use function substr;
use function delete_transient;
use function get_option;
use function get_transient;
use function set_transient;
use function array_map;
/**
 * {@inheritDoc}
 *
 * Uses WordPress transients as storage medium.
 */
class CachePool implements CacheInterface
{
    public const RESERVED_KEY_SYMBOLS = '{}()/\@:';
    public const NAMESPACE_SEPARATOR = '/';
    protected const TABLE_NAME_OPTIONS = 'options';
    protected const FIELD_NAME_OPTION_NAME = 'option_name';
    protected const OPTION_NAME_PREFIX_TRANSIENT = '_transient_';
    protected const OPTION_NAME_PREFIX_TIMEOUT = 'timeout_';
    protected const OPTION_NAME_MAX_LENGTH = 191;
    /**
     * @var wpdb
     */
    protected $wpdb;
    /**
     * @var string
     */
    protected $poolName;
    /**
     * @var mixed
     */
    protected $defaultValue;
    /**
     * @var int|DateInterval
     */
    protected $defaultTtl;
    /**
     * @param wpdb   $wpdb         The WP database object.
     * @param string $poolName     The name of this cache pool. Must be unique to this instance.
     * @param mixed $defaultValue  A random value. Used for false-negative detection. The more chaotic - the better.
     * @param int|DateInterval $defaultTtl Default TTL to use when caching new entries.
     */
    public function __construct(wpdb $wpdb, string $poolName, $defaultValue, $defaultTtl = 0)
    {
        if ($poolName === static::OPTION_NAME_PREFIX_TIMEOUT) {
            throw new RangeException(sprintf('Pool name cannot be "%1$s"', static::OPTION_NAME_PREFIX_TIMEOUT));
        }
        $this->wpdb = $wpdb;
        $this->poolName = $poolName;
        $this->defaultValue = $defaultValue;
        $this->defaultTtl = $defaultTtl;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If problem retrieving.
     */
    public function get($key, $default = null)
    {
        $this->validateKey($key);
        $transientKey = $this->prepareKey($key);
        try {
            $value = $this->getTransient($transientKey);
        } catch (RangeException $e) {
            return $default;
        } catch (RuntimeException $e) {
            $message = sprintf('Could not retrieve cache for key "%1$s": %2$s', $key, $e->getMessage());
            throw new CacheException($message, 0, $e);
        }
        return $value;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If TTL cannot be normalized to a number of seconds.
     * @throws InvalidArgumentException If TTL is invalid.
     */
    public function set($key, $value, $ttl = null)
    {
        $this->validateKey($key);
        $origKey = $key;
        $key = $this->prepareKey($key);
        $ttl = is_null($ttl) ? $this->defaultTtl : $ttl;
        try {
            $ttl = $ttl instanceof DateInterval ? $this->getIntervalDuration($ttl) : $ttl;
        } catch (Exception $e) {
            throw new CacheException(sprintf('Could not normalize cache TTL: %s', $e->getMessage()));
        }
        if (!is_int($ttl)) {
            throw new InvalidArgumentException('The specified cache TTL is invalid');
        }
        try {
            $this->setTransient($key, $value, $ttl);
        } catch (RuntimeException $e) {
            $message = sprintf('Could not write value for key "%1$s" to cache: %2$s', $origKey, $e->getMessage());
            throw new CacheException($message, 0, $e);
        }
        return true;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If problem deleting.
     */
    public function delete($key)
    {
        $this->validateKey($key);
        $origKey = $key;
        $key = $this->prepareKey($key);
        try {
            $this->deleteTransient($key);
        } catch (Exception $e) {
            $message = sprintf('Failed to delete cache for key "%1$s": %2$s', $origKey, $e->getMessage());
            throw new CacheException($message, 0, $e);
        }
        return true;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If problem clearing.
     */
    public function clear()
    {
        try {
            $keys = $this->getAllKeys();
            $this->deleteMultiple($keys);
        } catch (Exception|InvalidArgumentExceptionInterface $e) {
            throw new CacheException(sprintf('Failed to clear cache: %s', $e->getMessage()), 0, $e);
        }
        return true;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If problem retrieving.
     */
    public function getMultiple($keys, $default = null)
    {
        if (!is_iterable($keys)) {
            throw new InvalidArgumentException('List of keys is not an iterable value');
        }
        $entries = [];
        foreach ($keys as $key) {
            $value = $this->get($key, $default);
            $entries[$key] = $value;
        }
        return $entries;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If problem persisting.
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_iterable($values)) {
            throw new InvalidArgumentException('List of keys is not an iterable value');
        }
        try {
            $ttl = $ttl instanceof DateInterval ? $this->getIntervalDuration($ttl) : $ttl;
        } catch (Exception $e) {
            throw new CacheException(sprintf('Could not normalize cache TTL: %s', $e->getMessage()));
        }
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If problem deleting.
     */
    public function deleteMultiple($keys)
    {
        if (!is_iterable($keys)) {
            throw new InvalidArgumentException('List of keys is not an iterable value');
        }
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }
    /**
     * @inheritDoc
     *
     * @throws CacheException If problem determining.
     */
    public function has($key)
    {
        $default = $this->defaultValue;
        $value = $this->get($key, $default);
        return $value !== $default;
    }
    /**
     * Retrieves a transient value, by key.
     *
     * @param string $key The transient key.
     *
     * @return mixed The transient value.
     *
     * @throws RangeException If transient for key not found.
     * @throws RuntimeException If problem retrieving.
     */
    protected function getTransient(string $key)
    {
        $value = $this->getTransientOriginal($key);
        if ($value !== false) {
            return $value;
        }
        $prefix = static::OPTION_NAME_PREFIX_TRANSIENT;
        $optionKey = "{$prefix}{$key}";
        try {
            $this->getOption($optionKey);
        } catch (RangeException $e) {
            throw new RangeException(sprintf('Transient for key "%1$s" does not exist', $key), 0, $e);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf('Could not verify existence of transient "%1$s"', $key), 0, $e);
        }
        return $value;
    }
    /**
     * Retrieves a transient value as is.
     *
     * @param string $key The transient key.
     *
     * @return mixed The transient value.
     */
    protected function getTransientOriginal(string $key)
    {
        $value = get_transient($key);
        return $value;
    }
    /**
     * Assigns a transient value, by key.
     *
     * @param string $key   The transient key.
     * @param mixed  $value The transient value. Any serializable object.
     * @param int    $ttl   The amount of seconds after which the transient will expire.
     *
     * @throws RangeException If key invalid.
     * @throws RuntimeException If problem setting.
     */
    protected function setTransient(string $key, $value, int $ttl): void
    {
        $this->validateTransientKey($key);
        if (!set_transient($key, $value, $ttl)) {
            throw new RuntimeException(sprintf('set_transient() failed with key "%1$s" with TTL %2$ss', $key, $ttl));
        }
    }
    /**
     * Retrieves an option value by name.
     *
     * @param string $key     The option name.
     *
     * @return mixed The option value.
     *
     * @throws RangeException If option value does not exist.
     * @throws RuntimeException If problem retrieving option.
     */
    protected function getOption(string $key)
    {
        $errorValue = $this->defaultValue;
        $value = $this->getOptionOriginal($key, $errorValue);
        if ($value === $errorValue) {
            throw new RangeException(sprintf('Option for key "%1$s" does not exist', $key));
        }
        return $value;
    }
    /**
     * Retrieves an option value by name.
     *
     * @param string $key     The option key.
     * @param null   $default The value to return if option not found.
     *
     * @return mixed The option value.
     */
    protected function getOptionOriginal(string $key, $default = null)
    {
        return get_option($key, $default);
    }
    /**
     * Deletes a transient with the specified key.
     *
     * @param string $key The key to delete a transient for.
     *
     * @throws RuntimeException If problem deleting.
     */
    protected function deleteTransient(string $key): void
    {
        if (!delete_transient($key)) {
            throw new RuntimeException(sprintf('delete_transient() failed for key "%1$s"', $key));
        }
    }
    /**
     * Validates a cache key.
     *
     * @param string $key The key to validate.
     *
     * @throws InvalidArgumentException If key is invalid.
     */
    protected function validateKey(string $key)
    {
        $prefix = $this->getTimeoutOptionNamePrefix();
        if (strlen("{$prefix}{$key}") > static::OPTION_NAME_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('Given the %1$d char length of this cache pool\'s name, the key length must not exceed %2$d chars', strlen($this->poolName), static::OPTION_NAME_MAX_LENGTH - strlen($prefix)));
        }
        $reservedSymbols = str_split(static::RESERVED_KEY_SYMBOLS, 1);
        foreach ($reservedSymbols as $symbol) {
            if (strpos($key, $symbol) !== false) {
                throw new InvalidArgumentException(sprintf('Cache key "%1$s" is invalid', $key));
            }
        }
    }
    /**
     * Validates a transient key.
     *
     * @param string $key The key to validate.
     *
     * @throws RangeException If key is invalid.
     */
    protected function validateTransientKey(string $key): void
    {
        $maxLength = $this->getTransientKeyMaxLength();
        $keyLength = strlen($key);
        if ($keyLength > $maxLength) {
            throw new RangeException(sprintf('Transient key "%1$s" length is %2$d chars, which exceeds max length of %3$d chars', $key, $keyLength, $maxLength));
        }
    }
    /**
     * Retrieves the amount of characters at most allowed in a transient key.
     *
     * @return int The amount of characters.
     */
    protected function getTransientKeyMaxLength(): int
    {
        $longestPrefix = $this->getTransientTimeoutOptionNamePrefix();
        $keyMaxLength = static::OPTION_NAME_MAX_LENGTH - strlen($longestPrefix);
        return $keyMaxLength;
    }
    /**
     * Prepares a cache key, giving it a namespace.
     *
     * @param string $key The key to prepare.
     *
     * @return string The prepared key.
     */
    protected function prepareKey(string $key): string
    {
        $namespace = $this->poolName;
        $separator = static::NAMESPACE_SEPARATOR;
        return "{$namespace}{$separator}{$key}";
    }
    /**
     * Retrieves all keys that correspond to this cache pool.
     *
     * @throws Exception If problem retrieving.
     *
     * @return iterable A list of keys.
     */
    protected function getAllKeys(): iterable
    {
        $tableName = $this->getTableName(static::TABLE_NAME_OPTIONS);
        $fieldName = static::FIELD_NAME_OPTION_NAME;
        $prefix = $this->getOptionNamePrefix();
        $query = "SELECT `{$fieldName}` FROM `{$tableName}` WHERE `{$fieldName}` LIKE '{$prefix}%'";
        $results = $this->selectColumn($query, $fieldName);
        $keys = $this->getCacheKeysFromOptionNames($results);
        return $keys;
    }
    /**
     * Runs a SELECT query, and retrieves a list of values for a field with the specified name.
     *
     * @param string $query      The SELECT query.
     * @param string $columnName The name of the field to retrieve.
     * @param array  $args       Query parameters.
     *
     * @return iterable The list of values for the specified field.
     */
    protected function selectColumn(string $query, string $columnName, array $args = []): iterable
    {
        $query = $this->prepareQuery($query, $args);
        $results = $this->wpdb->get_results($query, ARRAY_A);
        return array_map(function ($row) use ($columnName) {
            return $row[$columnName] ?? null;
        }, $results);
    }
    /**
     * Retrieve the name of a DB table by its identifier.
     *
     * @param string $identifier The table identifier.
     *
     * @return string The table name in the DB.
     */
    protected function getTableName(string $identifier): string
    {
        $prefix = $this->wpdb->prefix;
        $tableName = "{$prefix}{$identifier}";
        return $tableName;
    }
    /**
     * Prepares a parameterized query.
     *
     * @param string $query  The query to prepare. May include placeholders.
     * @param array  $params The parameters that will replace corresponding placeholders in the query.
     *
     * @return string The prepared query. Parameters will be interpolated.
     */
    protected function prepareQuery(string $query, array $params = []): string
    {
        if (empty($params)) {
            return $query;
        }
        $prepared = $this->wpdb->prepare($query, ...$params);
        return $prepared;
    }
    /**
     * Retrieves all cache keys that correspond to the given list of option names
     *
     * @param iterable $optionNames
     *
     * @throws Exception If problem retrieving.
     *
     * @return iterable A list of cache keys.
     */
    protected function getCacheKeysFromOptionNames(iterable $optionNames): iterable
    {
        $keys = [];
        foreach ($optionNames as $name) {
            $key = $this->getCacheKeyFromOptionName($name);
            $keys[] = $key;
        }
        return $keys;
    }
    /**
     * Retrieves the prefix of option names that represent transients of this cache pool.
     *
     * @return string The prefix.
     */
    protected function getOptionNamePrefix(): string
    {
        $transientPrefix = static::OPTION_NAME_PREFIX_TRANSIENT;
        $separator = static::NAMESPACE_SEPARATOR;
        $namespace = $this->poolName;
        $prefix = "{$transientPrefix}{$namespace}{$separator}";
        return $prefix;
    }
    /**
     * Retrieves the prefix of option names that represent transient timeouts of this cache pool.
     *
     * @return string The prefix.
     */
    protected function getTimeoutOptionNamePrefix(): string
    {
        $transientPrefix = $this->getTransientTimeoutOptionNamePrefix();
        $separator = static::NAMESPACE_SEPARATOR;
        $namespace = $this->poolName;
        $prefix = "{$transientPrefix}{$namespace}{$separator}";
        return $prefix;
    }
    /**
     * Retrieves the prefix of an option name that represents a transient timeout.
     *
     * This is the longest prefix of transient options.
     *
     * @return string The prefix.
     */
    protected function getTransientTimeoutOptionNamePrefix(): string
    {
        return static::OPTION_NAME_PREFIX_TRANSIENT . static::OPTION_NAME_PREFIX_TIMEOUT;
    }
    /**
     * Retrieves the cache key that corresponds to the specified option name.
     *
     * @param string $name The option name.
     *
     * @return string The cache key.
     *
     * @throws Exception If problem determining key.
     */
    protected function getCacheKeyFromOptionName(string $name): string
    {
        $prefix = $this->getOptionNamePrefix();
        if (strpos($name, $prefix) !== 0) {
            throw new RangeException(sprintf('Option name "%1$s" is not formed according to this cache pool', $name));
        }
        $key = substr($name, strlen($prefix));
        return $key;
    }
    /**
     * Retrieves the total duration from an interval.
     *
     * @param DateInterval $interval The interval.
     *
     * @throws Exception If problem retrieving.
     *
     * @return int The duration in seconds.
     */
    protected function getIntervalDuration(DateInterval $interval): int
    {
        $reference = new DateTimeImmutable();
        $endTime = $reference->add($interval);
        return $endTime->getTimestamp() - $reference->getTimestamp();
    }
}