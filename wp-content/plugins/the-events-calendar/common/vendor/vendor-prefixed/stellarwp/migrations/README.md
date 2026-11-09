# Migrations

A batched database migrations library for WordPress plugins powered by [Shepherd](https://github.com/stellarwp/shepherd).

[![PHP Compatibility](https://github.com/stellarwp/migrations/actions/workflows/compatibility.yml/badge.svg)](https://github.com/stellarwp/migrations/actions/workflows/compatibility.yml)
[![PHP Code Standards](https://github.com/stellarwp/migrations/actions/workflows/phpcs.yml/badge.svg)](https://github.com/stellarwp/migrations/actions/workflows/phpcs.yml)
[![PHPStan](https://github.com/stellarwp/migrations/actions/workflows/phpstan.yml/badge.svg)](https://github.com/stellarwp/migrations/actions/workflows/phpstan.yml)
[![General Code Standards](https://github.com/stellarwp/migrations/actions/workflows/standards.yml/badge.svg)](https://github.com/stellarwp/migrations/actions/workflows/standards.yml)
[![PHP Tests](https://github.com/stellarwp/migrations/actions/workflows/tests-php.yml/badge.svg)](https://github.com/stellarwp/migrations/actions/workflows/tests-php.yml)

## Features

- **Batched execution** - Process large datasets incrementally without timeouts.
- **Automatic rollback** - Failed migrations trigger automatic rollback via `down()` (status: `FAILED`).
- **Manual rollback** - Completed migrations can be manually rolled back (status: `REVERTED`).
- **Activity logging** - All migration activity is logged to a database table.
- **Automatic log cleanup** - Old logs are automatically cleaned up after a configurable retention period.
- **Extensible hooks** - Actions and filters for custom behavior at each stage.
- **WP-CLI integration** - Full CLI support for running, rolling back, and monitoring migrations.

## Quick Start

```php
use StellarWP\Migrations\Config;
use StellarWP\Migrations\Provider;
use StellarWP\Migrations\Registry;
use StellarWP\Migrations\Abstracts\Migration_Abstract;
use StellarWP\Migrations\Enums\Operation;

// Configure the library.
Config::set_container( $container );
Config::set_hook_prefix( 'my_plugin' );

// Register the provider.
$container->get( Provider::class )->register();

// Create a migration.
class Rename_Meta_Key extends Migration_Abstract {
    public function get_label(): string {
        return 'Rename Meta Key';
    }

    public function get_description(): string {
        return 'Renames old_key to new_key in post meta.';
    }

    public function is_applicable(): bool {
        return true;
    }

    public function is_up_done(): bool {
        global $wpdb;
        return ! (int) $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(*) FROM %i WHERE meta_key = %s',
                $wpdb->postmeta,
                'old_key'
            )
        );
    }

    public function is_down_done(): bool {
        global $wpdb;
        return ! (int) $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(*) FROM %i WHERE meta_key = %s',
                $wpdb->postmeta,
                'new_key'
            )
        );
    }

    public function get_total_items( ?Operation $operation = null ): int {
        global $wpdb;
        $key = ( $operation ?? Operation::UP() )->equals( Operation::DOWN() ) ? 'new_key' : 'old_key';
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(*) FROM %i WHERE meta_key = %s',
                $wpdb->postmeta,
                $key
            )
        );
    }

    public function get_default_batch_size(): int {
        return 100;
    }

    public function up( int $batch, int $batch_size ): void {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                'UPDATE %i SET meta_key = %s WHERE meta_key = %s LIMIT %d',
                $wpdb->postmeta,
                'new_key',
                'old_key',
                $batch_size
            )
        );
    }

    public function down( int $batch, int $batch_size ): void {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                'UPDATE %i SET meta_key = %s WHERE meta_key = %s LIMIT %d',
                $wpdb->postmeta,
                'old_key',
                'new_key',
                $batch_size
            )
        );
    }
}

// Register the migration with a unique ID.
$registry = Config::get_container()->get( Registry::class );
$registry->register( 'my_plugin_rename_meta_key', Rename_Meta_Key::class );
```

## Logging

Add custom logs to track migration progress and debug issues:

```php
public function up( int $batch, int $batch_size ): void {
    $logger = Config::get_container()->get( Logger::class );

    $logger->info( 'Starting batch processing.', [ 'batch' => $batch ] );

    // ... do migration work ...

    if ( $some_warning_condition ) {
        $logger->warning( 'Found invalid data.', [ 'record_id' => $id ] );
    }
}
```

Available log levels: `info()`, `warning()`, `error()`, `debug()`

**Log Cleanup:**

Old migration logs are automatically cleaned up after 180 days (configurable via the `stellarwp_migrations_{prefix}_log_retention_days` filter). See [Getting Started](./docs/getting-started.md#log-cleanup) for details.

## WP-CLI

Manage migrations from the command line:

```bash
# List all registered migrations
wp my-plugin migrations list

# Run a specific migration
wp my-plugin migrations run my_migration_id

# Rollback a migration
wp my-plugin migrations rollback my_migration_id

# View execution history
wp my-plugin migrations executions my_migration_id

# View logs for an execution
wp my-plugin migrations logs 123 --type=error
```

The command prefix (`my-plugin` above) is derived from your configured hook prefix.

See [CLI Reference](./docs/cli.md) for full command documentation.

## Documentation

- [Getting Started](./docs/getting-started.md) - Installation and basic usage
- [Migration Contract](./docs/migration-contract.md) - Full API reference
- [Admin UI Reference](./docs/admin-ui.md) - Admin interface for managing migrations
- [CLI Reference](./docs/cli.md) - WP-CLI commands and usage
- [REST API Reference](./docs/rest-api.md) - REST API endpoints for programmatic access
- [Programmatic Scheduling](./docs/programmatic-scheduling.md) - How to programmatically schedule migrations
- [Hooks Reference](./docs/hooks.md) - Available actions and filters
- [Tests](./docs/test.md) - Test setup instructions

## Contributing

We welcome contributions. Please see our contributing guidelines for more information.
