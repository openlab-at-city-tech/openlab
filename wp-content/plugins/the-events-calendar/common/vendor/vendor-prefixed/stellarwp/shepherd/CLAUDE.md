# StellarWP Shepherd - AI Assistant Context

## Project Overview

Shepherd is a lightweight background processing library for WordPress built on top of Action Scheduler. It provides a clean, fluent API for defining and dispatching asynchronous tasks with built-in support for retries, debouncing, and logging.

## Key Features

- **Background Task Processing**: Offload time-consuming operations to background processes
- **Synchronous Task Execution**: Run tasks immediately with lifecycle callbacks via `run()` method (since 0.1.0)
- **Automatic Retries**: Configurable retry mechanism with exponential backoff
- **Task Debouncing**: Prevents tasks from running too frequently with customizable delays
- **Unique Task Enforcement**: Prevents duplicate tasks from being scheduled
- **Database Logging**: Comprehensive lifecycle tracking for all tasks
- **Priority System**: Assign priorities (0-255) to control task execution order
- **Group Management**: Organize tasks into logical groups
- **Built-in Tasks**: Includes pre-packaged tasks like Email for common operations

## Architecture

### Core Components

1. **Regulator** (`src/Regulator.php`): Central task management system
2. **Provider** (`src/Provider.php`): Service provider for dependency injection
3. **Task_Abstract** (`src/Abstracts/Task_Abstract.php`): Base class for all tasks
4. **Database Tables**: Custom tables for task data and logging

### Database Schema

- `shepherd_{prefix}_tasks`: Stores task data and retry information
- `shepherd_{prefix}_task_logs`: Tracks task lifecycle events

### Task Lifecycle States

- `created`: Task has been scheduled
- `started`: Task execution has begun
- `finished`: Task completed successfully
- `failed`: Task execution failed
- `rescheduled`: Task has been rescheduled
- `retrying`: Task is being retried after failure
- `cancelled`: Task has been cancelled

## Installation & Setup

### Installation

```bash
composer require stellarwp/shepherd
```

### Registration

Shepherd requires a DI container implementing `StellarWP\ContainerContract\ContainerInterface`. Register it on the `plugins_loaded` action at the LATEST:

```php
\StellarWP\Shepherd\Config::set_hook_prefix( 'my_app' ); // Needs to be set before the container is registered.
$container = get_my_apps_container(); // Your container instance
$container->singleton( \StellarWP\Shepherd\Provider::class );
$container->get( \StellarWP\Shepherd\Provider::class )->register();
```

## Creating Tasks

Tasks are recommended to extend `Task_Abstract`:

```php
class My_Task extends Task_Abstract {
    // Optional: Override constructor for type hinting
    public function __construct( string $message, int $code = 200 ) {
        parent::__construct( $message, $code ); // Should call parent constructor
    }

    public function process(): void {
        // Access arguments via $this->get_args()
        $message = $this->get_args()[0];
        $code = $this->get_args()[1];

        // Task logic here
        if ( ! $result ) {
            throw new ShepherdTaskException( 'Task failed' );
        }
    }

    public function get_task_prefix(): string {
        return 'my_task_'; // Max 15 characters
    }

    // Optional: Configure retries
    public function get_max_retries(): int {
        return 2; // Will retry 2 times (3 total attempts)
    }

    // Optional: Configure retry delay
    public function get_retry_delay(): int {
        return 30; // 30 seconds between retries
    }
}
```

## Usage Examples

```php
// Dispatch a task immediately
shepherd()->dispatch(new My_Task($arg1, $arg2));

// Dispatch with delay (in seconds)
shepherd()->dispatch(new My_Task($arg1, $arg2), 300); // 5 minutes

// Run tasks synchronously with lifecycle callbacks (since 0.1.0)
shepherd()->run(
    [ new My_Task($arg1, $arg2), new Another_Task() ],
    [
        'before' => function( $task ) { /* called before each task */ },
        'after'  => function( $task ) { /* called after each task */ },
        'always' => function( $tasks ) { /* called after all tasks complete successfully */ },
    ]
);

// Retrieve task logs
use StellarWP\Shepherd\Contracts\Logger;
use StellarWP\Shepherd\Provider;

$logger = Provider::get_container()->get( Logger::class );
$logs = $logger->retrieve_logs( $task_id );
```

## Built-in Tasks

### Email Task

Sends emails asynchronously with automatic retries (up to 5 attempts):

```php
use StellarWP\Shepherd\Tasks\Email;

$email_task = new Email(
    'recipient@example.com',
    'Subject',
    '<h1>HTML Body</h1>',
    ['Content-Type: text/html; charset=UTF-8'],
    ['/path/to/attachment.pdf']
);

shepherd()->dispatch($email_task);
```

## Logging System

### Default Logger Change

Shepherd now uses `ActionScheduler_DB_Logger` as the default logger instead of `DB_Logger`. This change:

- **Reduces database overhead** by reusing Action Scheduler's existing `actionscheduler_logs` table
- **Maintains compatibility** with the existing Logger interface
- **Preserves all functionality** including log retrieval and lifecycle tracking

### Logger Options

```php
use StellarWP\Shepherd\Config;
use StellarWP\Shepherd\Loggers\ActionScheduler_DB_Logger;
use StellarWP\Shepherd\Loggers\DB_Logger;

// Default: Use Action Scheduler's logs table
Config::set_logger( new ActionScheduler_DB_Logger() );

// Alternative: Use Shepherd's dedicated logs table
Config::set_logger( new DB_Logger() );
```

### Log Storage Format

When using `ActionScheduler_DB_Logger`, logs are stored in a special format within the `message` column:

```
shepherd_{hook_prefix}||{task_id}||{type}||{level}||{json_entry}
```

This format allows Shepherd to store its metadata while maintaining compatibility with Action Scheduler's table structure.

## Development Commands

### Testing

```bash
# You need to have slic installed and configured to use shepherd.

# Then you can run each suite like:
slic run wpunit
slic run integration
```

### Code Quality

```bash
# Run static analysis
composer test:analysis

# Check PHP compatibility
composer compatibility

# Run coding standards check
vendor/bin/phpcs
```

### Common Tasks

```bash
# Install dependencies, ignoring uopz extension which is met inside of the slic container.
composer install --ignore-platform-req=ext-uopz

```

## Important Files and Locations

- **Main entry point**: `shepherd.php`
- **Core logic**: `src/Regulator.php`
- **Task base class**: `src/Abstracts/Task_Abstract.php`
- **Database schemas**: `src/Tables/`
- **Built-in tasks**: `src/Tasks/`
- **Tests**: `tests/`
- **Documentation**: `docs/`

## Testing Approach

- Uses Codeception via [slic](https://github.com/stellarwp/slic) for testing
- Test configuration in `codeception.dist.yml` and `codeception.slic.yml` with environmental variables defined in `.env.testing.slic`
- Integration tests for full workflow testing
- Snapshot testing for complex data structures

## Coding Standards

- Follows WordPress coding standards and more Specifically StellarWP's coding standards.
- Uses PHPStan for static analysis (level defined in `phpstan.neon.dist`)
- PHP 7.4+ compatibility required
- PSR-4 autoloading under `StellarWP\Shepherd` namespace

## Dependencies

- **stellarwp/db**: Database abstraction layer
- **stellarwp/schema**: Database schema management
- **woocommerce/action-scheduler**: Task queue backend
- **psr/log**: PSR-3 logger interface
- **stellarwp/container-contract**: A DI container that implements [StellarWP's container contract](https://github.com/stellarwp/container-contract)

## Common Development Patterns

### Adding a New Task

1. Create a new class extending `Task_Abstract` in `src/Tasks/`
2. Implement required methods (`process()`, `get_task_prefix()`)
3. Optionally override retry configuration methods
4. If implemented withing Shepherd, add tests in `tests/unit/Tasks/`

### Modifying Database Schema

1. Update table's column definitions in `src/Tables/`
2. Update the table's schema version.
3. Update any affected repository classes.

### Adding New Features

1. Follow existing patterns in the codebase
2. Add appropriate logging using the logger trait
3. Include comprehensive tests
4. Update documentation as needed

## Troubleshooting

### Common Issues

1. **uopz extension missing**: Use `--ignore-platform-req=ext-uopz` with composer

### Custom Logger Implementation

You can implement a custom logger by implementing the `Logger` interface:

```php
use StellarWP\Shepherd\Contracts\Logger;

class My_Custom_Logger implements Logger {
    // Implement required methods
}

// Set before Provider::register()
\StellarWP\Shepherd\Config::set_logger( new My_Custom_Logger() );
```

## Task Behavior Details

### Unique Tasks

- Tasks are unique based on class name and arguments
- Dispatching a duplicate task will be ignored (no-op)

### Retry Logic

- Tasks fail when they throw an `Exception` in their `process()` method.
- Retry count is the number of additional attempts (not total attempts)
- Each retry can have a configurable delay
- Failed tasks are logged

## Additional Notes

- This is a WordPress plugin/library, not a standalone application
- Requires WordPress environment for full functionality
- Action Scheduler must be available (included as dependency)
- All database operations use StellarWP's [DB](https://github.com/stellarwp/db) library

## Contributing Guidelines

**IMPORTANT**: Before making any commits or opening PRs, always check:

- `.github/CONTRIBUTING.md` - Complete commit and PR guidelines
- Pre-commit checklist:
  - Run `composer test:analysis`
  - Run `composer compatibility`
  - Run `vendor/bin/phpcs`
  - Run `slic run wpunit && slic run integration`
  - Update documentation if needed
  - Follow conventional commit format

## Documentation

For more detailed information, refer to the documentation files:

- `docs/getting-started.md` - Installation and basic usage guide
- `docs/advanced-usage.md` - Advanced features like retries, debouncing, and logging
- `docs/tasks.md` - Information about built-in tasks
- `docs/tasks/email.md` - Detailed documentation for the Email task
- `docs/api-reference.md` - Complete API documentation
- `docs/configuration.md` - Configuration guide
