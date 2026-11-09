# Shepherd

Shepherd is a lightweight and powerful background processing library for WordPress, built on top of Action Scheduler. It provides a simple, fluent API for defining and dispatching asynchronous tasks, with built-in support for retries, debouncing, and logging.

## Features

- **Simple, Fluent API**: A straightforward way to define and dispatch background tasks.
- **Action Scheduler Integration**: Leverages the reliability of Action Scheduler for task processing.
- **Automatic Retries**: Configurable automatic retries for failed tasks.
- **Debouncing**: Prevent tasks from running too frequently.
- **Logging**: Built-in database logging for task lifecycle events.
- **Included Tasks**: Comes with ready-to-use tasks including `Email` (with multi-recipient support), `HTTP_Request`, and `Herding` tasks.

## Getting Started

For a guide on how to install Shepherd and get started with creating and dispatching your first task, please see our [Getting Started guide](./docs/getting-started.md).

## Advanced Usage

For more detailed information on advanced features like task retries, debouncing, unique tasks, and logging, please refer to our [Advanced Usage guide](./docs/advanced-usage.md).

## Built-in Tasks

Shepherd comes with a set of pre-packaged tasks to handle common background operations. For more information, please see our [Tasks guide](./docs/tasks.md).

## Contributing

We welcome contributions! Please see our contributing guidelines for more information.
