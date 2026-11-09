<?php

/**
 * Migration contract.
 *
 * @package \TEC\Common\StellarWP\Migrations\Contracts
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Contracts;

use JsonSerializable;
use TEC\Common\StellarWP\Migrations\Enums\Operation;
use TEC\Common\StellarWP\Migrations\Enums\Status;
use TEC\Common\StellarWP\Migrations\Models\Execution;
/**
 * Interface for migrations.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Contracts
 */
interface Migration extends JsonSerializable
{
    /**
     * Get the migration ID.
     *
     * @since 0.0.1
     *
     * @return string The migration ID.
     */
    public function get_id(): string;
    /**
     * Get the migration label.
     *
     * @since 0.0.1
     *
     * @return string
     */
    public function get_label(): string;
    /**
     * Get the migration description.
     *
     * @since 0.0.1
     *
     * @return string
     */
    public function get_description(): string;
    /**
     * Get the total number of items to process.
     *
     * @since 0.0.1
     *
     * @param Operation|null $operation The operation to get the total items for. On null, `Operation::UP()` is assumed.
     *
     * @return int
     */
    public function get_total_items(?Operation $operation = null): int;
    /**
     * Get the number of retries per batch.
     *
     * @since 0.0.1
     *
     * @return int
     */
    public function get_number_of_retries_per_batch(): int;
    /**
     * Get the default number of items to process per batch by default.
     * It can be overridden in the runtime.
     *
     * @since 0.0.1
     *
     * @return int
     */
    public function get_default_batch_size(): int;
    /**
     * Get the migration tags.
     *
     * @since 0.0.1
     *
     * @return array<string>
     */
    public function get_tags(): array;
    /**
     * Whether the migration is applicable to the current site.
     *
     * This is something that should not change by whether the migration has been run or not.
     *
     * @since 0.0.1
     *
     * @return bool
     */
    public function is_applicable(): bool;
    /**
     * Whether the migration can run.
     *
     * @since 0.0.1
     *
     * @return bool
     */
    public function can_run(): bool;
    /**
     * Whether the migration has been completed.
     *
     * @since 0.0.1
     *
     * @return bool
     */
    public function is_up_done(): bool;
    /**
     * Whether the migration has been rolled back.
     *
     * @since 0.0.1
     *
     * @return bool
     */
    public function is_down_done(): bool;
    /**
     * Runs the migration.
     *
     * @since 0.0.1
     *
     * @param int $batch      The batch number.
     * @param int $batch_size The batch size.
     *
     * @return void
     */
    public function up(int $batch, int $batch_size): void;
    /**
     * Reverts the migration.
     *
     * @since 0.0.1
     *
     * @param int $batch      The batch number.
     * @param int $batch_size The batch size.
     *
     * @return void
     */
    public function down(int $batch, int $batch_size): void;
    /**
     * Runs before each batch of the rollback.
     *
     * @since 0.0.1
     *
     * @param int $batch      The batch number.
     * @param int $batch_size The batch size.
     *
     * @return void
     */
    public function before_down(int $batch, int $batch_size): void;
    /**
     * Runs before each batch of the migration.
     *
     * @since 0.0.1
     *
     * @param int $batch The batch number.
     * @param int $batch_size The batch size.
     *
     * @return void
     */
    public function before_up(int $batch, int $batch_size): void;
    /**
     * Runs after each batch of the migration.
     *
     * @since 0.0.1
     *
     * @param int  $batch        The batch number.
     * @param int  $batch_size   The batch size.
     * @param bool $is_completed Whether the migration has been completed.
     *
     * @return void
     */
    public function after_up(int $batch, int $batch_size, bool $is_completed): void;
    /**
     * Runs after each batch of the rollback.
     *
     * @since 0.0.1
     *
     * @param int  $batch        The batch number.
     * @param int  $batch_size   The batch size.
     * @param bool $is_completed Whether there are more batches to run.
     *
     * @return void
     */
    public function after_down(int $batch, int $batch_size, bool $is_completed): void;
    /**
     * Get extra arguments to be passed to the `up()` method for a specific batch.
     *
     * @since 0.0.1
     *
     * @param int $batch The batch number.
     * @param int $batch_size The batch size.
     *
     * @return array<mixed>
     */
    public function get_up_extra_args_for_batch(int $batch, int $batch_size): array;
    /**
     * Get the total number of batches.
     *
     * @since 0.0.1
     *
     * @param int            $batch_size The batch size.
     * @param Operation|null $operation  The operation to get the total batches for. On null, `Operation::UP()` is assumed.
     *
     * @return int
     */
    public function get_total_batches(int $batch_size, ?Operation $operation): int;
    /**
     * Get extra arguments to be passed to the `down()` method for a specific batch.
     *
     * @since 0.0.1
     *
     * @param int $batch The batch number.
     * @param int $batch_size The batch size.
     *
     * @return array<mixed>
     */
    public function get_down_extra_args_for_batch(int $batch, int $batch_size): array;
    /**
     * Convert the migration to an array.
     *
     * @since 0.0.1
     *
     * @phpstan-return array{
     *     label: string,
     *     description: string,
     *     tags: array<string>,
     *     total_batches: int,
     *     can_run: bool,
     *     is_applicable: bool,
     *     status: Status,
     * }
     *
     * @return array<string, mixed>
     */
    public function to_array(): array;
    /**
     * Get the migration status.
     *
     * @since 0.0.1
     *
     * @return Status
     */
    public function get_status(): Status;
    /**
     * Get the latest execution for this migration.
     *
     * @since 0.0.1
     *
     * @return Execution|null The execution model or null if not found.
     */
    public function get_latest_execution(): ?Execution;
}