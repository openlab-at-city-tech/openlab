<?php

/**
 * Abstract Migration class.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Abstracts
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Abstracts;

use TEC\Common\StellarWP\Migrations\Contracts\Migration;
use TEC\Common\StellarWP\Migrations\Enums\Operation;
use TEC\Common\StellarWP\Migrations\Tables\Migration_Executions;
use TEC\Common\StellarWP\Migrations\Enums\Status;
use TEC\Common\StellarWP\Migrations\Models\Execution;
/**
 * Base class for migrations.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Abstracts
 */
abstract class Migration_Abstract implements Migration
{
    /**
     * The migration ID.
     *
     * @since 0.0.1
     *
     * @var string
     */
    private string $migration_id;
    /**
     * Constructor.
     *
     * @since 0.0.1
     *
     * @param string $migration_id The migration ID.
     *
     * @return void
     */
    public function __construct(string $migration_id)
    {
        $this->migration_id = $migration_id;
    }
    /**
     * Get the migration ID.
     *
     * @since 0.0.1
     *
     * @return string The migration ID.
     */
    public function get_id(): string
    {
        return $this->migration_id;
    }
    /**
     * Runs before each batch of the migration.
     *
     * @param int $batch      The batch number.
     * @param int $batch_size The batch size.
     *
     * @return void
     */
    public function before_up(int $batch, int $batch_size): void
    {
    }
    /**
     * Runs after each batch of the migration.
     *
     * @param int  $batch        The batch number.
     * @param int  $batch_size   The batch size.
     * @param bool $is_completed Whether the migration has been completed.
     *
     * @return void
     */
    public function after_up(int $batch, int $batch_size, bool $is_completed): void
    {
    }
    /**
     * Runs before each batch of the rollback.
     *
     * @param int $batch      The batch number.
     * @param int $batch_size The batch size.
     *
     * @return void
     */
    public function before_down(int $batch, int $batch_size): void
    {
    }
    /**
     * Runs after each batch of the rollback.
     *
     * @param int  $batch        The batch number.
     * @param int  $batch_size   The batch size.
     * @param bool $is_completed Whether the roll-back has been completed.
     *
     * @return void
     */
    public function after_down(int $batch, int $batch_size, bool $is_completed): void
    {
    }
    /**
     * Whether the migration can run.
     *
     * @since 0.0.1
     *
     * @return bool Whether the migration can run.
     */
    public function can_run(): bool
    {
        return true;
    }
    /**
     * Get the number of retries per batch.
     *
     * @since 0.0.1
     *
     * @return int The number of retries per batch.
     */
    public function get_number_of_retries_per_batch(): int
    {
        return 0;
    }
    /**
     * Get the total number of batches.
     *
     * @since 0.0.1
     *
     * @param int            $batch_size The batch size.
     * @param Operation|null $operation  The operation to get the total batches for. On null, `Operation::UP()` is assumed.
     *
     * @return int The total number of batches.
     */
    public function get_total_batches(int $batch_size, ?Operation $operation = null): int
    {
        if ($batch_size <= 0) {
            return 0;
        }
        return (int) ceil($this->get_total_items($operation) / $batch_size);
    }
    /**
     * Get the migration tags.
     *
     * @since 0.0.1
     *
     * @return array<string> The tags associated with the migration.
     */
    public function get_tags(): array
    {
        return [];
    }
    /**
     * Get extra arguments to be passed to the `up()` method for a specific batch.
     *
     * @since 0.0.1
     *
     * @param int $batch      The batch number.
     * @param int $batch_size The batch size.
     *
     * @return array<mixed>
     */
    public function get_up_extra_args_for_batch(int $batch, int $batch_size): array
    {
        return [];
    }
    /**
     * Get extra arguments to be passed to the `down()` method for a specific batch.
     *
     * @since 0.0.1
     *
     * @param int $batch      The batch number.
     * @param int $batch_size The batch size.
     *
     * @return array<mixed>
     */
    public function get_down_extra_args_for_batch(int $batch, int $batch_size): array
    {
        return [];
    }
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
    public function to_array(): array
    {
        return ['label' => $this->get_label(), 'description' => $this->get_description(), 'tags' => $this->get_tags(), 'total_batches' => $this->get_total_batches($this->get_default_batch_size()), 'can_run' => $this->can_run(), 'is_applicable' => $this->is_applicable(), 'status' => $this->get_status()];
    }
    /**
     * Convert the migration to a JSON serializable array.
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
    public function jsonSerialize(): array
    {
        return $this->to_array();
    }
    /**
     * Get the migration status.
     *
     * Returns NOT_APPLICABLE for non-applicable migrations.
     * Otherwise queries the database for the latest execution
     * and returns its status. If no executions exist, returns PENDING.
     *
     * @since 0.0.1
     *
     * @return Status The current status of the migration.
     */
    public function get_status(): Status
    {
        if (!$this->is_applicable()) {
            return Status::NOT_APPLICABLE();
        }
        $latest_execution = $this->get_latest_execution();
        if (null === $latest_execution) {
            return Status::PENDING();
        }
        return $latest_execution->get_status();
    }
    /**
     * Get the latest execution for this migration.
     *
     * @since 0.0.1
     *
     * @return Execution|null The execution model or null if not found.
     */
    public function get_latest_execution(): ?Execution
    {
        $executions = Migration_Executions::paginate(['order' => 'DESC', 'orderby' => 'created_at', 'migration_id' => ['column' => 'migration_id', 'value' => $this->get_id(), 'operator' => '=']], 1, 1);
        if (!empty($executions[0]) && $executions[0] instanceof Execution) {
            return $executions[0];
        }
        return null;
    }
}