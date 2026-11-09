<?php

/**
 * The Execution model.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Models
 */
namespace TEC\Common\StellarWP\Migrations\Models;

use TEC\Common\StellarWP\Migrations\Enums\Status;
use DateTimeInterface;
/**
 * Execution model.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Models
 */
class Execution
{
    /**
     * The execution ID.
     *
     * @since 0.0.1
     *
     * @var int
     */
    private int $id;
    /**
     * The migration ID.
     *
     * @since 0.0.1
     *
     * @var string
     */
    private string $migration_id;
    /**
     * The start date.
     *
     * @since 0.0.1
     *
     * @var ?DateTimeInterface
     */
    private ?DateTimeInterface $start_date;
    /**
     * The end date.
     *
     * @since 0.0.1
     *
     * @var DateTimeInterface
     */
    private ?DateTimeInterface $end_date;
    /**
     * The status.
     *
     * @since 0.0.1
     *
     * @var Status
     */
    private Status $status;
    /**
     * The items total.
     *
     * @since 0.0.1
     *
     * @var int
     */
    private int $items_total;
    /**
     * The items processed.
     *
     * @since 0.0.1
     *
     * @var int
     */
    private int $items_processed;
    /**
     * The parent execution ID (set when this execution is an automatic rollback after a failure).
     *
     * @since 0.0.1
     *
     * @var int|null
     */
    private ?int $parent_execution_id;
    /**
     * The created at.
     *
     * @since 0.0.1
     *
     * @var DateTimeInterface
     */
    private DateTimeInterface $created_at;
    /**
     * Constructor.
     *
     * @since 0.0.1
     *
     * @param array<string, mixed> $attributes The attributes.
     *
     * @phpstan-param array{
     *     id: int,
     *     migration_id: string,
     *     start_date_gmt: DateTimeInterface|null,
     *     end_date_gmt: DateTimeInterface|null,
     *     status: string,
     *     items_total: int,
     *     items_processed: int,
     *     parent_execution_id: int|null,
     *     created_at: DateTimeInterface
     * } $attributes
     */
    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'];
        $this->migration_id = $attributes['migration_id'];
        $this->start_date = $attributes['start_date_gmt'];
        $this->end_date = $attributes['end_date_gmt'];
        $this->status = Status::from($attributes['status']);
        $this->items_total = $attributes['items_total'];
        $this->items_processed = $attributes['items_processed'];
        $this->parent_execution_id = $attributes['parent_execution_id'] ?? null;
        $this->created_at = $attributes['created_at'];
    }
    /**
     * Get the execution ID.
     *
     * @since 0.0.1
     *
     * @return int
     */
    public function get_id(): int
    {
        return $this->id;
    }
    /**
     * Get the migration ID.
     *
     * @since 0.0.1
     *
     * @return string
     */
    public function get_migration_id(): string
    {
        return $this->migration_id;
    }
    /**
     * Get the start date.
     *
     * @since 0.0.1
     *
     * @return ?DateTimeInterface
     */
    public function get_start_date(): ?DateTimeInterface
    {
        return $this->start_date;
    }
    /**
     * Get the end date.
     *
     * @since 0.0.1
     *
     * @return ?DateTimeInterface
     */
    public function get_end_date(): ?DateTimeInterface
    {
        return $this->end_date;
    }
    /**
     * Get the status.
     *
     * @since 0.0.1
     *
     * @return Status
     */
    public function get_status(): Status
    {
        return $this->status;
    }
    /**
     * Get the items total.
     *
     * @since 0.0.1
     *
     * @return int
     */
    public function get_items_total(): int
    {
        return $this->items_total;
    }
    /**
     * Get the items processed.
     *
     * @since 0.0.1
     *
     * @return int
     */
    public function get_items_processed(): int
    {
        return $this->items_processed;
    }
    /**
     * Get the parent execution ID (set when this execution is an automatic rollback).
     *
     * @since 0.0.1
     *
     * @return int|null
     */
    public function get_parent_execution_id(): ?int
    {
        return $this->parent_execution_id;
    }
    /**
     * Get the created at.
     *
     * @since 0.0.1
     *
     * @return DateTimeInterface
     */
    public function get_created_at(): DateTimeInterface
    {
        return $this->created_at;
    }
    /**
     * Convert the execution to an array.
     *
     * @since 0.0.1
     *
     * @return array<string, mixed>
     */
    public function to_array(): array
    {
        return ['id' => $this->get_id(), 'migration_id' => $this->get_migration_id(), 'start_date' => $this->get_start_date(), 'end_date' => $this->get_end_date(), 'status' => $this->get_status(), 'items_total' => $this->get_items_total(), 'items_processed' => $this->get_items_processed(), 'parent_execution_id' => $this->get_parent_execution_id(), 'created_at' => $this->get_created_at()];
    }
}