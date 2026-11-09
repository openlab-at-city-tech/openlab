<?php

/**
 * Migrations Registry.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Migrations
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations;

use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use ArrayAccess;
use Iterator;
use Countable;
use RuntimeException;
/**
 * Migrations Registry.
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Migrations
 *
 * @implements ArrayAccess<string, class-string<Migration>>
 * @implements Iterator<string, Migration>
 */
class Registry implements ArrayAccess, Iterator, Countable
{
    /**
     * Collection of items.
     *
     * @since 0.0.1
     *
     * @var array<string, class-string<Migration>>
     */
    protected array $migrations = [];
    /**
     * Constructor.
     *
     * @since 0.0.1
     *
     * @param array<string, class-string<Migration>> $migrations An array of Migration IDs to Migration class-strings.
     *
     * @return void
     *
     * @throws RuntimeException If the migration is not a valid migration.
     */
    public function __construct(array $migrations = [])
    {
        foreach ($migrations as $migration_id => $migration) {
            if (!(is_string($migration_id) && is_string($migration))) {
                throw new RuntimeException('You should pass a map of Migration IDs to Migration class-strings to the Registry constructor.');
            }
            $this->register($migration_id, $migration);
        }
    }
    /**
     * Sets a value in the collection.
     *
     * @since 0.0.1
     *
     * @param string                  $migration_id    The migration ID.
     * @param class-string<Migration> $migration_class The migration class to register.
     *
     * @return void
     *
     * @throws RuntimeException If the migration is too late to be registered.
     */
    public function register(string $migration_id, string $migration_class): void
    {
        // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- class-string is not a valid PHP type hint.
        $prefix = Config::get_hook_prefix();
        if (did_action("stellarwp_migrations_{$prefix}_schedule_migrations")) {
            _doing_it_wrong(__FUNCTION__, 'Too late to add a migration to the registry.', '0.0.1');
            return;
        }
        // MySQL index limitations.
        if (strlen($migration_id) > 191) {
            throw new RuntimeException("Migration ID {$migration_id} is too long.");
        }
        $this->migrations[$migration_id] = $migration_class;
    }
    /**
     * @inheritDoc
     *
     * @return Migration|null
     */
    public function current(): ?Migration
    {
        $migration_class = current($this->migrations);
        return $migration_class === false ? null : new $migration_class(key($this->migrations));
    }
    /**
     * @inheritDoc
     */
    public function key(): ?string
    {
        return key($this->migrations);
    }
    /**
     * @inheritDoc
     */
    public function next(): void
    {
        next($this->migrations);
    }
    /**
     * @inheritDoc
     *
     * @param string $migration_id The migration id to check.
     *
     * @return bool
     */
    public function offsetExists($migration_id): bool
    {
        return array_key_exists($migration_id, $this->migrations);
    }
    /**
     * @inheritDoc
     *
     * @param string $offset The offset to get.
     *
     * @return Migration|null
     */
    public function offsetGet($offset): ?Migration
    {
        $migration_class = $this->migrations[$offset] ?? null;
        return $migration_class ? new $migration_class($offset) : null;
    }
    /**
     * @inheritDoc
     *
     * @param string                  $offset The offset to set.
     * @param class-string<Migration> $value  The migration class to set.
     *
     * @return void
     *
     * @throws RuntimeException If the offset is not a string.
     */
    public function offsetSet($offset, $value): void
    {
        if (!(is_string($offset) && is_string($value))) {
            throw new RuntimeException('You should provide a string as the migration ID to set a migration in the registry.');
        }
        $this->register($offset, $value);
    }
    /**
     * @inheritDoc
     *
     * @param string $migration_id The migration id to unset.
     */
    public function offsetUnset($migration_id): void
    {
        unset($this->migrations[$migration_id]);
    }
    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        reset($this->migrations);
    }
    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return key($this->migrations) !== null;
    }
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->migrations);
    }
    /**
     * Get a migration by its ID.
     *
     * @since 0.0.1
     *
     * @param string $migration_id The migration ID.
     *
     * @return Migration|null
     */
    public function get(string $migration_id): ?Migration
    {
        return $this->offsetGet($migration_id);
    }
    /**
     * Flush the registry.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function flush(): void
    {
        $this->migrations = [];
    }
    /**
     * Filter the migrations.
     *
     * @since 0.0.1
     *
     * @param callable( Migration ): bool $callback The callback to filter the migrations.
     *
     * @return self
     */
    public function filter(callable $callback): self
    {
        $migrations = $this->build_migrations();
        $migrations = array_filter($migrations, $callback);
        $clean = [];
        foreach ($migrations as $migration_id => $migration) {
            $clean[$migration_id] = get_class($migration);
        }
        return new self($clean);
    }
    /**
     * Get all the migrations.
     *
     * @since 0.0.1
     *
     * @return array<string,Migration>
     */
    public function all(): array
    {
        return $this->build_migrations();
    }
    /**
     * Build the migrations.
     *
     * @since 0.0.1
     *
     * @return array<string, Migration>
     */
    private function build_migrations(): array
    {
        $migrations = [];
        foreach ($this->migrations as $migration_id => $migration_class) {
            $migrations[$migration_id] = new $migration_class($migration_id);
        }
        return $migrations;
    }
}